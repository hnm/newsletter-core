<?php
namespace newsletter\core\bo;

use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoInheritance;
use n2n\persistence\orm\InheritanceType;
use n2n\impl\web\ui\view\html\HtmlView;
use newsletter\core\model\ui\TextView;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\core\N2N;
use n2n\mail\MailEncoder;

abstract class NewsletterCi extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoInheritance(InheritanceType::JOINED));
		$ai->p('historyLinks', new AnnoOneToMany(HistoryLink::getClass(), 'newsletterCi'));
	}
	
	private $id;
	private $orderIndex;
	private $historyLinks;
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getOrderIndex() {
		return $this->orderIndex;
	}

	public function setOrderIndex($orderIndex) {
		$this->orderIndex = $orderIndex;
	}
	
	public function getHistoryLinks() {
		return $this->historyLinks;
	}

	public function setHistoryLinks($historyLinks) {
		$this->historyLinks = $historyLinks;
	}

	public abstract function createHtmlUiComponent(HistoryEntry $historyEntry, HtmlView $view);	
	public abstract function createTextUiComponent(HistoryEntry $historyEntry, TextView $view, string $endOfLine);
	
	/**
	 * converts html into text
	 * @param string $html
	 * @param string $eol
	 */
	public static function htmlToText(string $html = null, string $eol = "\n") {
		if ($html === null) return null;
		return html_entity_decode(strip_tags(MailEncoder::htmlToText($html, $eol)), null, N2N::CHARSET);
	}
}