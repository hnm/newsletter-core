<?php
namespace newsletter\core\model;

use n2n\context\RequestScoped;
use n2n\persistence\orm\EntityManager;
use newsletter\core\bo\HistoryEntry;
use n2n\persistence\orm\criteria\Criteria;
use n2n\persistence\orm\criteria\item\CriteriaFunction;
use n2n\persistence\orm\criteria\item\CrIt;
use n2n\persistence\orm\criteria\compare\CriteriaComparator;
use newsletter\core\model\mail\NewsletterMail;
use newsletter\core\model\mail\MailManager;
use newsletter\core\model\mail\MailNotSendException;
use n2n\core\N2N;
use n2n\util\type\ArgUtils;
use n2n\core\container\TransactionManager;
use n2n\util\ex\IllegalStateException;

class SendBatchDao implements RequestScoped {
	const MAX_MAILS_NOT_IN_BATCH = 20;
	
	private $em;
	private $mailManager;
	private $newsletterState;
	private $tm;
	
	private function _init(EntityManager $em, MailManager $mailManager, 
			NewsletterState $newsletterState, TransactionManager $tm) {
		$this->em = $em;
		$this->mailManager = $mailManager;
		$this->newsletterState = $newsletterState;
		$this->tm = $tm;
	}

	/**
	 * @param int $limit
	 * @return HistoryEntry []
	 */
	public function getPreparedHistoryEntries(int $limit = null) {
		return $this->em->createSimpleCriteria(HistoryEntry::getClass(), array('status' =>
				HistoryEntry::STATUS_PREPARED), array('id' => Criteria::ORDER_DIRECTION_ASC), $limit)->toQuery()->fetchArray();
	}
	
	public function updateHistoryEntryStatus(HistoryEntry $historyEntry, $status) {
		$historyEntry->setStatus($status);
		$this->em->merge($historyEntry);
	}

	public function getNumSentEmailsForLastHour() {
		$dateTime = new \DateTime();
		// 5 minutes tolerance
		$dateTime->modify('-1 hour -5 minutes');
		$criteria = $this->em->createCriteria();
		$criteria->from(HistoryEntry::getClass(), 'e');
		$criteria->select(CrIt::f(CriteriaFunction::COUNT, CrIt::c(1)));
		$criteria->where()->andMatch('e.sentDate', CriteriaComparator::OPERATOR_LARGER_THAN, $dateTime);
		return $criteria->toQuery()->fetchSingle();
	}

	public function getNumSentEmailsForLastDay() {
		$dateTime = new \DateTime();
		// 5 minutes tolerance
		$dateTime->modify('-1 day -5 minutes');
		$criteria = $this->em->createCriteria();
		$criteria->from(HistoryEntry::getClass(), 'e');
		$criteria->select(CrIt::f(CriteriaFunction::COUNT, CrIt::c(1)));
		$criteria->where()->andMatch('e.sentDate', CriteriaComparator::OPERATOR_LARGER_THAN, $dateTime);
		return $criteria->toQuery()->fetchSingle();
	}
	
	public function sendMails(bool $cronjobAvailable = true, int $numMailsPerHour = 1000, 
			int $numMailsPerRequest = 1000, int $numMailsPerDay = 1000) {
		ArgUtils::assertTrue($numMailsPerHour > 0);
		ArgUtils::assertTrue($numMailsPerRequest > 0);
		
		$numMailsToSend = 0;
		$numSentEmailsForLastHour = $this->getNumSentEmailsForLastHour();
		
		// check hourly limit
		$numMailsToSend = $numMailsPerHour - $numSentEmailsForLastHour;
		if ($numMailsToSend <= 0) return;
		
		$numSentEmailsForLastDay = $this->getNumSentEmailsForLastDay();
		if ($numMailsPerDay <= $numSentEmailsForLastDay) return;
		
		if (($numMailsPerDay - $numSentEmailsForLastDay) < $numMailsToSend) {
			$numMailsToSend = $numMailsPerDay - $numSentEmailsForLastDay;
		}
		if ($numMailsToSend <= 0) return;
		
		if ($cronjobAvailable) {
			set_time_limit(0);
		} else if ($numMailsPerRequest > self::MAX_MAILS_NOT_IN_BATCH) {
			throw new \InvalidArgumentException('Num mails per request if not in batch should not exceed ' . self::MAX_MAILS_NOT_IN_BATCH 
					. '. ' . $numMailsPerRequest . ' given.');
		}

		// check max mails per request limit
		if ($numMailsToSend > $numMailsPerRequest) {
			$numMailsToSend = $numMailsPerRequest;
		}
		
		$this->sendNewsletterMails($numMailsToSend);
	}
	
	private function sendNewsletterMails($num) {
		IllegalStateException::assertTrue(!$this->tm->hasOpenTransaction());
		
		foreach ($this->getPreparedHistoryEntries($num) as $historyEntry) {
			$tx = $this->tm->createTransaction();
			try {
				$historyEntry = $this->em->find(HistoryEntry::getClass(), $historyEntry->getId());
				//Check if historyentry is (still) ready to send
				if (!$this->isSendable($historyEntry)) {
					$tx->commit();
					continue;
				}
				
				// send newsletter mails to system manager when in development mode!
				$alternativeRecipient = N2N::isDevelopmentModeOn() ? N2N::getAppConfig()->mail()->getSystemManagerAddress() : null;
				
				$newsletterMail = new NewsletterMail($this->newsletterState->getSenderEmail(),
						$historyEntry, $historyEntry->getHistory()->getNewsletter()->getSubject(), $alternativeRecipient);
				$newsletterMail->setSenderName($this->newsletterState->getSenderName());
				
				$newsletterMail->setReplyToEmail($this->newsletterState->getReplyToEmail());
				$newsletterMail->setReplyToName($this->newsletterState->getReplyToName());
				
				$this->updateHistoryEntryStatus($historyEntry, HistoryEntry::STATUS_IN_PROGRESS);
				$this->mailManager->send($newsletterMail);
				$historyEntry->setSentDate(new \DateTime());
				$this->updateHistoryEntryStatus($historyEntry, HistoryEntry::STATUS_SENT);
			} catch (MailNotSendException $e) {
				//Mail rejected by Host e.g. the inbox is not on the given server
				$historyEntry->setStatusMessage('Sending Failed: ' . $e->getMessage());
				$this->updateHistoryEntryStatus($historyEntry, HistoryEntry::STATUS_ERROR);
			}
			$tx->commit();
			$this->em->clear();
		}
	}
	
	public function isSendable(HistoryEntry $historyEntry) {
		$this->em->refresh($historyEntry);
		return $historyEntry->getStatus() === HistoryEntry::STATUS_PREPARED;
	}
}
