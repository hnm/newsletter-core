<?php
namespace newsletter\core\model\mail;

use newsletter\core\model\Template;
use newsletter\core\bo\HistoryEntry;

class NewsletterMail extends MailAdapter {
	private $senderEmail;
	private $senderName;
	private $subject;
	private $historyEntry;
	private $alternativeRecipient;
	private $replyToEmail;
	private $replyToName;
	
	public function __construct(string $senderEmail, HistoryEntry $historyEntry, 
			string $subject, string $alternativeRecipient = null) {
		$this->senderEmail = $senderEmail;
		$this->historyEntry = $historyEntry;
		$this->alternativeRecipient = $alternativeRecipient;
		$this->subject = $subject;
	}
	
	public function getSenderName() {
		return $this->senderName;
	}
	
	public function setSenderName(string $senderName) {
		$this->senderName = $senderName;
	}
	
	public function getSubject() {
		return $this->subject;
	}
	
	public function getRecipient() {
		if (null !== $this->alternativeRecipient) {
			return $this->alternativeRecipient;
		}
		
		return $this->historyEntry->getEmail();
	}
	
	public function getSenderEmail() {
		return $this->senderEmail;
	}
	
	public function getReplyToEmail() {
		return $this->replyToEmail;
	}
	
	public function setReplyToEmail(string $replyToEmail) {
		$this->replyToEmail = $replyToEmail;
	}
	
	public function getReplyToName() {
		return $this->replyToName;
	}
	
	public function setReplyToName($replyToName) {
		$this->replyToName = $replyToName;
	}
	
	public function getContentHtml() {
		return Template::finalizeTemplate($this->getCheckedContentHtml(), $this->historyEntry);
	}
	
	public function getContentTxt() {
		return Template::finalizeTemplate($this->getCheckedContentTxt(), $this->historyEntry);
	}

	private function getCheckedContentHtml() {
		return $this->historyEntry->getHistory()->getNewsletterHtml();
	}
	
	private function getCheckedContentTxt() {
		return $this->historyEntry->getHistory()->getNewsletterText();
	}
}
