<?php
namespace newsletter\core\model;

use n2n\context\RequestScoped;
use n2n\core\container\TransactionManager;
use newsletter\core\bo\History;
use newsletter\core\bo\HistoryEntry;

class HistoryEntryGenerator implements RequestScoped {
	private $newsletterDao;
	private $newsletterState;
	private $tm;
	
	private function _init(NewsletterDao $newsletterDao, NewsletterState $newsletterState, TransactionManager $tm) {
		$this->newsletterDao = $newsletterDao;
		$this->newsletterState = $newsletterState;
		$this->tm = $tm;
	}
	
	/**
	 * Ensure the Newsletterstate is setup properly before calling this method
	 */
	public function buildHistoryEntriesForFirstUnpreparedHistory() {
		$tx = $this->tm->createTransaction();
		$history = $this->newsletterDao->getFirstUnpreparedHistory();
		if (null !== $history) {
			$this->buildHistoryEntries($history);
		}
		$tx->commit();
	}
	
	private function buildHistoryEntries(History $history) {
		$newsletter = $history->getNewsletter();
		$salutationNeeded = preg_match('/' . preg_quote(Template::PLACEHOLDER_SALUTATION) . '/', $history->getNewsletterHtml());
		
		foreach ($this->newsletterDao->getRecipientEmailAddressesForNewsletter($newsletter) as $email) {
			$recipient = $this->newsletterDao->getRecipientByEmailAndLocale($email, $newsletter->getN2nLocale());
			$historyEntry = new HistoryEntry();
			$historyEntry->setEmail($recipient->getEmail());
			$historyEntry->setCode($this->newsletterDao->generateHistoryEntryCode($newsletter, $recipient));
			$historyEntry->setHistory($history);
			$historyEntry->setStatus(HistoryEntry::STATUS_PREPARED);
			if ($salutationNeeded) {
				$historyEntry->setSalutation($recipient->buildSalutation($this->newsletterState->getDtc()));
			}
			$this->newsletterDao->persistHistoryEntry($historyEntry);
		}
		
		if (!$newsletter->isSent()) {
			$newsletter->setSent(true);
			$this->newsletterDao->persistNewsletter($history->getNewsletter());
		}
		
		$history->setPreparedDate(new \DateTime());
		$this->newsletterDao->persistHistory($history);
	}
}
