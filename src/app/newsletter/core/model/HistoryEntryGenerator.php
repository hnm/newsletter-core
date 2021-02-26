<?php
namespace newsletter\core\model;

use n2n\core\ShutdownListener;
use newsletter\core\bo\Newsletter;
use newsletter\core\bo\History;
use newsletter\core\bo\HistoryEntry;
use n2n\persistence\orm\EntityManager;
use n2n\core\container\TransactionManager;

class HistoryEntryGenerator implements ShutdownListener {
	private $em;
	private $newsletter;
	private $newsletterDao;
	private $tm;
	private $template;
	private $newsletterState;
	
	public function __construct(Template $template, Newsletter $newsletter, EntityManager $em,
			NewsletterDao $newsletterDao, TransactionManager $tm, NewsletterState $newsletterState) {
		$this->em = $em;
		$this->template = $template;
		$template->setup($newsletter);
		$this->newsletter = $newsletter;
		$this->newsletterDao = $newsletterDao;
		$this->tm = $tm;
		$this->newsletterState = $newsletterState;
	}
	/* (non-PHPdoc)
	 * @see \n2n\core\ShutdownListener::onShutdown()
	 */
	public function onShutdown() {
		set_time_limit(0);
		$em = $this->em;
		
		$tx = $this->tm->createTransaction();
		$newsletter = $this->newsletterDao->getNewsletterById($this->newsletter->getId());
		//todo: get recipients directly
		$emailAddresses = $this->newsletterDao->getRecipientEmailAddressesForNewsletter($newsletter);
		if (count($emailAddresses) === 0) {
			$tx->commit();
			return;
		}
		
		$history = new History();
		$history->setNewsletter($newsletter);
		$history->setNewsletterHtml($this->template->getHtml());	
		$history->setNewsletterText($this->template->getText());
		$history->setPreparedDate(new \DateTime());
		
		$newsletter->setSent(true);
		$em->persist($history);
		$history->checkNewsletterHtml($this->newsletterState, $this->newsletterDao);
		$em->persist($newsletter);
		$tx->commit();
		
		$i = 0;
		$salutationNeeded = preg_match('/' . preg_quote(Template::PLACEHOLDER_SALUTATION) . '/', $history->getNewsletterHtml());
		
		$tx = $this->tm->createTransaction();
		foreach ($emailAddresses as $email) {
			$recipient = $this->newsletterDao->getRecipientByEmailAndLocale($email, $this->newsletter->getN2nLocale());
			$historyEntry = new HistoryEntry();
			$historyEntry->setEmail($recipient->getEmail());
			$historyEntry->setCode($this->newsletterDao->generateHistoryEntryCode($newsletter, $recipient));
			$historyEntry->setHistory($history);
			$historyEntry->setStatus(HistoryEntry::STATUS_PREPARED);
			if ($salutationNeeded) {
				$historyEntry->setSalutation($recipient->buildSalutation($this->newsletterState->getDtc()));
			}
			$em->persist($historyEntry);
		}
		$tx->commit();
		
	}
}