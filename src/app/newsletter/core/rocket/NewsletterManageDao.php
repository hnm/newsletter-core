<?php
namespace newsletter\core\rocket;

use n2n\context\RequestScoped;
use newsletter\core\bo\HistoryEntry;
use n2n\persistence\orm\EntityManager;
use newsletter\core\bo\Recipient;
use newsletter\core\bo\Blacklisted;
use n2n\l10n\N2nLocale;
use newsletter\core\bo\Newsletter;
use rocket\ei\manage\ManageState;

class NewsletterManageDao implements RequestScoped {
	/**
	 * @var EntityManager
	 */
	private $em;
	
	private function _init(ManageState $manageState) {
		$this->em = $manageState->getEntityManager();
	}
	
	public function removeFailedHistoryEntries(Newsletter $newsletter) {
		foreach ($this->getHistoryEntries($newsletter, HistoryEntry::STATUS_ERROR) as $historyEntry) {
			$this->em->remove($historyEntry);
		}
	}
	
	public function resetHistoryEntriesInProgress(Newsletter $newsletter) {
		foreach ($this->getHistoryEntries($newsletter, HistoryEntry::STATUS_IN_PROGRESS) as $historyEntry) {
			$historyEntry->setStatus(HistoryEntry::STATUS_PREPARED);
			$this->em->persist($historyEntry);
		}
	}
	/**
	 * @param string $status
	 * 
	 * @return HistoryEntry []
	 */
	public function getHistoryEntries(Newsletter $newsletter, $status = null) {
		$criteria = $this->em->createCriteria();
		$criteria->from(HistoryEntry::getClass(), 'he');
		$criteria->select('he');
		$criteria->where(array('he.history.newsletter' => $newsletter));
		if (null !== $status) {
			$criteria->where(array('he.status' => $status));
		}
		
		return $criteria->toQuery()->fetchArray();
	}
	
	public function deleteRecipientsForFailedHistoryEntries(Newsletter $newsletter) {
		foreach ($this->getHistoryEntries($newsletter, HistoryEntry::STATUS_ERROR) as $hisoryEntry) {
			if (null === ($recipient = $this->getRecipientByEmailAndN2nLocale($hisoryEntry->getEmail(), 
					$hisoryEntry->getHistory()->getNewsletter()->getN2nLocale()))) continue;
			$this->em->remove($recipient);
		}
	}
	
	public function getRecipientByEmailAndN2nLocale($email, N2nLocale $n2nLocale) {
		return $this->em->createSimpleCriteria(Recipient::getClass(), array('email' => $email, 
				'n2nLocale' => $n2nLocale))->toQuery()->fetchSingle();
	}
	
	public function isBlacklisted($email) {
		return null !== $this->em->createSimpleCriteria(Blacklisted::getClass(), 
				array('email' => $email))->toQuery()->fetchSingle();
	}
}