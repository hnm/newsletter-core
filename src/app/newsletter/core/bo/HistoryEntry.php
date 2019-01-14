<?php
namespace newsletter\core\bo;

use n2n\persistence\orm\CascadeType;
use n2n\persistence\orm\annotation\AnnoTable;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\util\type\ArgUtils;

class HistoryEntry extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('newsletter_history_entry'));
		$ai->p('history', new AnnoManyToOne(History::getClass(), CascadeType::PERSIST|CascadeType::MERGE));
		$ai->p('historyLinkClicks', new AnnoOneToMany(HistoryLinkClick::getClass(), 'historyEntry', CascadeType::ALL));
	}
	
	const STATUS_PREPARED = 'prepared';
	const STATUS_IN_PROGRESS = 'in-progress';
	const STATUS_SENT = 'sent';
	const STATUS_READ = 'read';
	const STATUS_ERROR = 'error';
	
	private $id;
	private $email;
	private $salutation;
	private $status;
	private $code;
	private $statusMessage;
	private $sentDate;
	private $history;
	private $historyLinkClicks;
	
	public function __construct(string $email = null, string $code = null) {
		$this->email = $email;
		$this->code = $code;
	}
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
	}
	
	public function getSalutation() {
		return $this->salutation;
	}
	
	public function setSalutation(string $salutation = null) {
		$this->salutation = $salutation;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		ArgUtils::valEnum($status, self::getPossibleStatus());
		$this->status = $status;
	}
	
	public function getCode() {
		return $this->code;
	}

	public function setCode($code) {
		$this->code = $code;
	}
	/**
	 * @return \DateTime
	 */
	public function getSentDate() {
		return $this->sentDate;
	}
	/**
	 * @param DateTime $sentDate
	 */
	public function setSentDate(\DateTime $sentDate) {
		$this->sentDate = $sentDate;
	}
	
	public function getHistoryLinkClicks() {
		return $this->historyLinkClicks;
	}

	public function setHistoryLinkClicks($historyLinkClicks) {
		$this->historyLinkClicks = $historyLinkClicks;
	}

	/**
	 * @return \newsletter\core\bo\History
	 */
	public function getHistory() {
		return $this->history;
	}

	public function setHistory(History $history) {
		$this->history = $history;
	}
	
	public function getStatusMessage() {
		return $this->statusMessage;
	}
	
	public function setStatusMessage($statusMessage) {
		$this->statusMessage = $statusMessage;
	}
	
	public static function getPossibleStatus() {
		return array(self::STATUS_PREPARED, self::STATUS_IN_PROGRESS, self::STATUS_ERROR, 
				self::STATUS_SENT, self::STATUS_READ);
	}
	
	public function hasHistory() {
		return null !== $this->history;
	}
}