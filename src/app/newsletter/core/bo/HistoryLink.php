<?php
namespace newsletter\core\bo;

use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\persistence\orm\CascadeType;
use n2n\persistence\orm\FetchType;
use n2n\persistence\orm\annotation\AnnoTable;
use n2n\util\uri\Url;
use newsletter\core\model\Template;

class HistoryLink extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('newsletter_history_link'));
		$ai->p('history', new AnnoManyToOne(History::getClass()));
		$ai->p('historyLinkClicks', new AnnoOneToMany(HistoryLinkClick::getClass(), 
				'historyLink', CascadeType::ALL, null, true));
		$ai->p('newsletterCi', new AnnoManyToOne(NewsletterCi::getClass(), null, FetchType::EAGER));
	}
	
	private $id;
	private $history;
	private $link;
	private $historyLinkClicks;
	private $newsletterCi;
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getHistory() {
		return $this->history;
	}

	public function setHistory(History $history) {
		$this->history = $history;
	}

	public function getLink() {
		return $this->link;
	}

	public function setLink(string $link) {
		$this->link = $link;
	}

	/**
	 * @return HistoryLinkClick
	 */
	public function getHistoryLinkClicks() {
		return $this->historyLinkClicks;
	}

	public function setHistoryLinkClicks(\ArrayObject $historyLinkClicks) {
		$this->historyLinkClicks = $historyLinkClicks;
	}

	public function getNewsletterCi() {
		return $this->newsletterCi;
	}

	public function setNewsletterCi(NewsletterCi $newsletterCi = null) {
		$this->newsletterCi = $newsletterCi;
	}
	
	public function getUrl(HistoryEntry $historyEntry) {
		return Url::create(Template::finalizeTemplate($this->link, $historyEntry));
	}
}