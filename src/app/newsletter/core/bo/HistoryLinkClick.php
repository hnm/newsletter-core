<?php
namespace newsletter\core\bo;

use n2n\persistence\orm\annotation\AnnoTable;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\FetchType;

class HistoryLinkClick extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('newsletter_history_link_click'));
		$ai->p('historyEntry', new AnnoManyToOne(HistoryEntry::getClass()));
		$ai->p('historyLink', new AnnoManyToOne(HistoryLink::getClass(), null, FetchType::EAGER));
		$ai->p('recipient', new AnnoManyToOne(Recipient::getClass()));
	}
	
	private $id;
	private $historyEntry;
	private $historyLink;
	private $recipient;
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getHistoryEntry() {
		return $this->historyEntry;
	}

	public function setHistoryEntry(HistoryEntry $historyEntry) {
		$this->historyEntry = $historyEntry;
	}

	public function getHistoryLink() {
		return $this->historyLink;
	}

	public function setHistoryLink(HistoryLink $historyLink) {
		$this->historyLink = $historyLink;
	}
	
	public function getRecipient() {
		return $this->recipient;
	}

	public function setRecipient(Recipient $recipient = null) {
		$this->recipient = $recipient;
	}
}