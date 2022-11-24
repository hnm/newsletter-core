<?php
namespace newsletter\core\model;

use newsletter\core\bo\Newsletter;
use n2n\context\RequestScoped;
use newsletter\core\bo\HistoryEntry;
use newsletter\core\bo\Recipient;
use n2n\core\N2N;
use newsletter\core\bo\Blacklisted;
use newsletter\core\bo\RecipientCategory;
use rocket\core\model\Rocket;
use n2n\persistence\Pdo;
use n2n\core\container\N2nContext;
use n2n\persistence\orm\EntityManager;
use n2n\core\container\TransactionManager;
use n2n\l10n\N2nLocale;
use newsletter\core\bo\NewsletterCi;
use newsletter\core\bo\HistoryLink;
use newsletter\core\bo\HistoryLinkClick;
use n2n\core\config\N2nLocaleConfig;
use n2n\util\type\ArgUtils;

class NewsletterDao implements RequestScoped {
	const HISTORY_ENTRY_CODE_LENGTH = 32;
	const RECIPIENT_CONFIRMATION_CODE_LENGTH = 32;
	const UNCONFIRMED_STATUS_INTERVAL_SPEC = 'P2D';
	
	private $em;
	private $n2nContext;
	private $rocket;
	private $tm;
	private $dtcs;
	private $n2nLocaleConfig;
	private $newsletterState;
	private $template;
	
	private function _init(EntityManager $em, Rocket $rocket, N2nContext $n2nContext, TransactionManager $tm, 
			N2nLocaleConfig $n2nLocaleConfig, NewsletterState $newsletterState, Template $template) {
		$this->em = $em;
		$this->n2nContext = $n2nContext;
		$this->dtcs = array();
		$this->rocket = $rocket;
		$this->tm = $tm;
		$this->n2nLocaleConfig = $n2nLocaleConfig;
		$this->newsletterState = $newsletterState;
		$this->template = $template;
	}
	
	/**
	 * @param int $id
	 * @return \newsletter\core\bo\Newsletter
	 */
	public function getNewsletterById($id) {
		return $this->em->find(Newsletter::getClass(), $id);
	}
	
	/**
	 * @param string $email
	 * @return \newsletter\core\bo\Recipient
	 */
	public function getRecipientByEmailAndLocale($email, N2nLocale $n2nLocale) {
		$tx = $this->tm->createTransaction(true);
		$recipient = $this->em->createSimpleCriteria(Recipient::getClass(), 
				array('email' => $email, 'n2nLocale' => $n2nLocale))->toQuery()->fetchSingle();
		$tx->commit();
		return $recipient;
	}
	
	public function isBlacklisted(string $email) {
		return null !== $this->em->find(Blacklisted::getClass(), $email);
	}
	
	/**
	 * @param \newsletter\core\bo\Newsletter $newsletter
	 * @return \newsletter\core\bo\Recipient[]
	 */
/* 	public function getRecipientsForNewsletter(Newsletter $newsletter) {
		$criteria = $this->em->createCriteria(Recipient::getClass(), 'r');
		$criteria->where(array('r.status' => Recipient::STATUS_ACTIVE, 
				'r.n2nLocale' => $newsletter->getLocale()));
		
		$categoryGroup = $criteria->where()->andGroup();
		foreach ($newsletter->getRecipientCategories() as $recipientCategory) {
			$categoryGroup->orMatch('r.categories.id', '=', $recipientCategory->getId());
		}
		$criteria->group('r.email');
		$criteria->join(HistoryEntry::getClass(), 'he', JoinType::LEFT)->match('r.email', '=', 'he.email');
		$criteria->join(History::getClass(), 'h')->match('he.history', '=', 'h')
				->andMatch('h.newsletter', '=', new CriteriaConstant($newsletter));
		$criteria->having()->match('COUNT(he.id)', '=', 0);
		return $criteria->fetchArray();
	} */
	
	public function getRecipientEmailAddressesForNewsletter(Newsletter $newsletter) {
		$params = array();
		$sql = $this->prepareRecipientsSql($newsletter, $params);
		$stmt = $this->em->getPdo()->prepare($sql);
		$stmt->execute($params);
		$emailAddresses = array();
		foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $result) {
			$emailAddresses[] = $result['email'];
		}
		return $emailAddresses;
	}
	
	public function getNewsletterCiById($id) {
		return $this->em->find(NewsletterCi::getClass(), $id);
	}
	
	/**
	 * @param int $id
	 * @return HistoryLink
	 */
	public function getHistoryLinkById($id) {
		return $this->em->find(HistoryLink::getClass(), $id);
	}
	
	public function persistHistoryLink(HistoryLink $historyLink) {
		$this->em->persist($historyLink);
		// @todo: causes exception. links must be flushed, in order to have id!
		$this->em->flush();
	}
	
	public function getNumRecipientsForNewsletter(Newsletter $newsletter) {
		$params = array();
		$sql = $this->prepareRecipientsSql($newsletter, $params);
		
		$sql = "SELECT COUNT(0) as num FROM (" 
				. $this->prepareRecipientsSql($newsletter, $params) . ") a";
		$stmt = $this->em->getPdo()->prepare($sql);
		$stmt->execute($params);
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);
		return intval($result['num']);
	}
	
	public function prepareRecipientsSql(Newsletter $newsletter, &$params) {
		$params = array('n2nLocale' => (string) $newsletter->getN2nLocale(),
				'status' => Recipient::STATUS_ACTIVE, 'newsletter_id' => $newsletter->getId());
		$sql = "SELECT email
				FROM newsletter_recipient r
				LEFT JOIN newsletter_recipient_recipient_categories nrcc ON nrcc.recipient_id = r.id
				LEFT JOIN newsletter_recipient_category nrc on nrc.id = nrcc.recipient_category_id
				WHERE r.status = :status
					AND r.n2n_locale = :n2nLocale
					AND email not in (
						SELECT email
						FROM newsletter_history_entry he
							JOIN newsletter_history h ON h.id = he.history_id
						WHERE h.newsletter_id = :newsletter_id)";
		if (count($recipientCategories = $newsletter->getRecipientCategories()) > 0) {
			$sql .= " AND (";
			$categorySql = array();
			foreach ($recipientCategories as $key => $recipientCategory) {
				$index = 
				$params['re_lft_' . $key] = $recipientCategory->getLft();
				$params['re_rgt_' . $key] = $recipientCategory->getRgt();
				$categorySql[] = '(nrc.lft >= :re_lft_' . $key . ' AND nrc.rgt <= :re_rgt_' . $key . ')';
			}
			$sql .= implode(' OR ', $categorySql) . ")";
		}
		return $sql . " GROUP BY email";
	}

	public function generateHistoryEntryCode(Newsletter $newsletter, Recipient $recipient) {
		return $this->generateCode(self::HISTORY_ENTRY_CODE_LENGTH, 
				array($newsletter->getId(), $recipient->getEmail()));
	}
	/**
	 * @param string $code
	 * @return \newsletter\core\bo\HistoryEntry
	 */
	public function getHistoryEntryByCode($code) {
		return $this->em->createSimpleCriteria(HistoryEntry::getClass(), array('code' => $code))
				->toQuery()->fetchSingle();
	}
	/**
	 * @param \newsletter\core\bo\Newsletter $newsletter
	 * @param string $status
	 * @return \newsletter\core\bo\HistoryEntry[]
	 */
	public function getHistoryEntriesForNewsletter(Newsletter $newsletter, $status = null) {
		$historyEntries = null;
		$criteria = $this->em->createCriteria();
		$criteria->select('h');
		$criteria->from(HistoryEntry::getClass(), 'h');
		$criteria->where(array('h.history.newsletter' => $newsletter));
		if (null !== $status) {
			$criteria->where(array('h.status' => $status));
		}
		$criteria->group('h.email');
		$historyEntries = $criteria->toQuery()->fetchArray();

		return $historyEntries;
	}

	public function getNumHistoryEntriesForNewsletter(Newsletter $newsletter, $status = null) {
		$params = array('newsletterId' => $newsletter->getId());
		$sql = 'SELECT COUNT(0) AS num FROM (SELECT nhe.id 
				FROM  newsletter_history_entry nhe 
					INNER JOIN  newsletter_history nh ON nhe.history_id = nh.id 
				WHERE nh.newsletter_id = :newsletterId';
		if (null !== $status) {
			$sql .= ' AND nhe.status = :status';
			$params['status'] = $status;
		}
		$sql .= ' GROUP BY nhe.email) a';
		$stmt = $this->em->getPdo()->prepare($sql);
		$stmt->execute($params);
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);
		return intval($result['num']);
	}
	
	public function getHistoryLinkStatsForNewsletter(Newsletter $newsletter) {
		return $this->em->createNqlCriteria('SELECT hl.link AS link, COUNT(hl.historyLinkClicks.id) AS num 
				FROM HistoryLink hl WHERE hl.history.newsletter = :newsletter GROUP BY hl.id', array('newsletter' => $newsletter))->toQuery()->fetchArray();
	}

	/**
	 * @param \newsletter\core\bo\HistoryEntry
	 * @param \newsletter\core\bo\Article $historyLink
	 * @return \newsletter\core\bo\HistoryEntryContentItemClick
	 */
	public function getHistoryLinkClick(HistoryEntry $historyEntry, HistoryLink $historyLink) {
		return $this->em->createSimpleCriteria(HistoryLinkClick::getClass(),
				array('historyEntry' => $historyEntry, 'historyLink' => $historyLink))->toQuery()->fetchSingle();
	}
	/**
	 * @param \newsletter\core\bo\Recipient $recipient
	 * @param \newsletter\core\bo\Newsletter $newsletter
	 * @return \newsletter\core\bo\HistoryEntry
	 */
	public function getHistoryEntryForRecipientAndNewsletter(Recipient $recipient, Newsletter $newsletter) {
		return $this->em->createSimpleCriteria(HistoryEntry::getClass(),
				array('email' => $recipient->getEmail(), 'history.newsletter' => $newsletter))->toQuery()->fetchSingle();
	}
	
	public function updateHistoryEntryStatus(HistoryEntry $historyEntry, $status) {
		$historyEntry->setStatus($status);
		$this->em->persist($historyEntry);
	}
	
	public function createHistoryLinkClick(HistoryEntry $historyEntry, HistoryLink $historyLink) {
		if (null !== $this->getHistoryLinkClick($historyEntry, $historyLink)) return;
		
		$historyLinkClick = new HistoryLinkClick();
		$historyLinkClick->setHistoryLink($historyLink);
		$historyLinkClick->setHistoryEntry($historyEntry);
		$historyLinkClick->setRecipient($this->getRecipientByEmailAndLocale($historyEntry->getEmail(), 
				$historyEntry->getHistory()->getNewsletter()->getN2nLocale()));
		$this->em->persist($historyLinkClick);
	}

	public function createHistoryForNewsletter(Newsletter $newsletter) {
		$this->applyBlacklist();
		N2N::registerShutdownListener(new HistoryEntryGenerator($this->template, $newsletter, $this->em, 
				$this, $this->tm, $this->newsletterState));
	}
	
	public function getOrCreateRecipient(string $firstName, string $lastName, string $email, string $gender, string $saluteWith, 
			N2nLocale $n2nLocale, array $categories = null) {
		ArgUtils::valArray($categories, RecipientCategory::class, true);
		
		$tx = $this->tm->createTransaction();
		$recipient = $this->buildRecipient($email, $n2nLocale, $categories);
		
		$recipient->setFirstName($firstName);
		$recipient->setLastName($lastName);
		$recipient->setGender($gender);
		$recipient->setSaluteWith($saluteWith);
		$this->em->persist($recipient);
		$tx->commit();
		
		return $recipient;
	}
	
	public function getOrCreateRecipientForEmailAndLocale(string $email, N2nLocale $n2nLocale, array $categories = null) {
		$tx = $this->tm->createTransaction();
		$recipient = $this->buildRecipient($email, $n2nLocale, $categories);
		$this->em->persist($recipient);
		$tx->commit();
		
		return $recipient;
	}
	
	private function buildRecipient(string $email, N2nLocale $n2nLocale, array $categories = null) {
		ArgUtils::valArray($categories, RecipientCategory::class, true);
		
		$tmpCategories = array();
		if (null === ($recipient = $this->getRecipientByEmailAndLocale($email, $n2nLocale))) {
			$recipient = new Recipient();
			$recipient->setEmail($email);
			$recipient->setN2nLocale($n2nLocale);
			$recipient->setStatus(Recipient::STATUS_UNCONFIRMED);
		} else {
			if (null !== ($blacklisted = $this->em->find(Blacklisted::getClass(), $recipient->getEmail()))) {
				$this->em->remove($blacklisted);
			}
			
			foreach ($recipient->getCategories() as $category) {
				$tmpCategories[$category->getId()] = $category;
			}
		}
		
		foreach (ArgUtils::toArray($categories) as $category) {
			$tmpCategories[$category->getId()] = $category;
		}
		
		$recipient->setCategories(new \ArrayObject($tmpCategories));
		$recipient->setConfirmationCode($this->generateConfirmationCode($recipient));
		
		return $recipient;
	}
	
	public function moveToBlacklist(Recipient $recipient) {
		$tx = $this->tm->createTransaction();
		$em = $this->em;
		if (null === $em->find(Blacklisted::getClass(), $recipient->getEmail()) ) {
			$blacklisted = new Blacklisted();
			$blacklisted->setEmail($recipient->getEmail());
			$blacklisted->setCreated(new \DateTime());
			$em->persist($blacklisted);
			$em->remove($recipient);
		}
		$tx->commit();
	}
	
	private function generateConfirmationCode(Recipient $recipient) {
		return $this->generateCode(self::RECIPIENT_CONFIRMATION_CODE_LENGTH, array($recipient->getEmail()));
	}
	
	private function generateCode($maxLength, array $additionals) {
		return substr(base64_encode(md5(implode('', $additionals) . rand())), 0,
				$maxLength);
	}
	
	public function applyBlacklist() {
		$em = $this->em;
		$blacklisteds = $em->createSimpleCriteria(Blacklisted::getClass())->toQuery()->fetchArray();
		
		foreach ($blacklisteds as $blackListed) {
			foreach ($this->getRecipientsByEmail($blackListed->getEmail()) as $recipient) {
				$em->remove($recipient);
			}
		}
		
		$this->removeUnconfirmedRecipients();
	}
	
	public function getRecipientsByEmail($email) {
		return $this->em->createSimpleCriteria(Recipient::getClass(), array('email' => $email))->toQuery()->fetchArray();
	}
	/**
	 * @param int $id
	 * @param string $confirmationCode
	 * @return \newsletter\core\bo\Recipient
	 */
	public function getRecipientByEmailAndConfirmationCode($email, $confirmationCode) {
		$tx = $this->tm->createTransaction(true);
		$recipient = $this->em->createSimpleCriteria(Recipient::getClass(), 
				array('email' => $email , 'confirmationCode' => $confirmationCode))->toQuery()->fetchSingle();
		$tx->commit();
		return $recipient;
	}
	
	public function activateRecipient(Recipient $recipient) {
		$tx = $this->tm->createTransaction();
		
		$em = $this->em;
		if (null !== ($blacklisted = $em->find(Blacklisted::getClass(), $recipient->getEmail()))) {
			$em->remove($blacklisted);
		}
		
		if (!$recipient->isActive()) {
			$recipient->setStatus(Recipient::STATUS_ACTIVE);
			$em->persist($recipient);
		}
		
		$tx->commit();
	}
	
	public function getSharedRecipientCategories(Newsletter $newsletter, Recipient $recipient) {
		$recipientCategories = array();
		$recipientAssignedCategories = $recipient->getCategories();
		
		foreach ($newsletter->getRecipientCategories() as $recipientCategory) {
			foreach ($recipientAssignedCategories as $category) {
				if (!$recipientCategory->equals($category)) continue;
				$recipientCategories[] = $category;
			}
		}
		return $recipientCategories;
	}
	/**
	 * @return \newsletter\core\bo\RecipientCategory[] 
	 */
	public function getRecipientCategories(N2nLocale $n2nLocale = null) {
		return $this->em->createSimpleCriteria(RecipientCategory::getClass())->toQuery()->fetchArray();
	}
	
	public function getRecipientCategoryById($id) {
		return $this->em->find(RecipientCategory::getClass(), $id);
	}
	
	public static function encodeEmail($email) {
		return base64_encode($email);
	}
	
	public static function decodeEmail($email) {
		return base64_decode($email);
	}

	/**
	 * @return Blacklisted[]
	 */
	public function getBlacklisteds() {
		return $this->em->createSimpleCriteria(Blacklisted::getClass())->toQuery()->fetchArray();
	}

	private function removeUnconfirmedRecipients() {
		$dateTime = new \DateTime();
		$dateTime->sub(new \DateInterval(self::UNCONFIRMED_STATUS_INTERVAL_SPEC));

		$em = $this->em;
		$criteria = $em->createCriteria();
		$criteria->from(Recipient::getClass(), 'r');
		$criteria->select('r');
		$criteria->where(array('r.status' => Recipient::STATUS_UNCONFIRMED))
				->andMatch('r.created', '<=', $dateTime);
		
		foreach ($criteria->toQuery()->fetchArray() as $recipient) {
			$em->remove($recipient);
		}
	}
}
