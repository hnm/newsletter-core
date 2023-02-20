<?php
namespace newsletter\core\model;

use newsletter\core\bo\Newsletter;
use n2n\web\ui\ViewFactory;
use newsletter\core\bo\HistoryEntry;
use n2n\core\container\N2nContext;
use n2n\context\RequestScoped;
use n2n\impl\web\ui\view\html\HtmlView;

class Template implements RequestScoped {
	
	const PLACEHOLDER_HISTORY_ENTRY_CODE = '{historyEntryCode}';
	const PLACEHOLDER_SALUTATION = '{salutation}';
	const PLACEHOLDER_EMAIL = '{email}';
	
	private $newsletter;
	private $response;
	private $n2nContext;
	private $viewFactory;
	private $newsletterState;
	
	private function _init(N2nContext $n2nContext, NewsletterState $newsletterState, ViewFactory $viewFacotory) {
		$this->n2nContext = $n2nContext;
		$this->viewFactory = $viewFacotory;
		$this->newsletterState = $newsletterState;
	}
	
	public function setup(Newsletter $newsletter) {
		$this->newsletter = $newsletter;
	}

	/**
	 * @param HistoryEntry $historyEntry
	 * @return HtmlView
	 */
	public function getHtmlView(HistoryEntry $historyEntry = null) {
		$this->n2nContext->setN2nLocale($this->newsletter->getN2nLocale());
		$view = $this->viewFactory->create($this->newsletterState->getTemplateConfig()->getTemplateHtmlViewId(), 
				array('historyEntry' => $this->detemineHistoryEntry($historyEntry), 'newsletter' => $this->newsletter, 
						'fileLogo' => $this->newsletterState->getTemplateConfig()->getFileLogo()));
		$view->setDynamicTextCollection($this->newsletterState->getDtc());
		
		return $view;
	}
	
	public function getHtml(HistoryEntry $historyEntry = null) {
		$view = $this->getHtmlView();
		$view->initialize();
		return $view->getContents();
	}
	
	public function getText(HistoryEntry $historyEntry = null) {
		$view = $this->viewFactory->create($this->newsletterState->getTemplateConfig()->getTemplateTextViewId(), 
				array('newsletter' => $this->newsletter, 'historyEntry' => $this->detemineHistoryEntry($historyEntry)));
		$view->initialize();
		return $view->getContents();
	}
	
	private function detemineHistoryEntry(HistoryEntry $historyEntry = null) {
		if (null !== $historyEntry) return $historyEntry;
		
		return self::buildDummyHistoryEntry();
	}
	
	public static function buildDummyHistoryEntry() {
		$historyEntry = new HistoryEntry();
		$historyEntry->setCode(Template::PLACEHOLDER_HISTORY_ENTRY_CODE);
		$historyEntry->setEmail(Template::PLACEHOLDER_EMAIL);
		
		return $historyEntry;
	}
	
	public static function finalizeTemplate(string $content, HistoryEntry $historyEntry) {
		return str_replace(
				array(self::PLACEHOLDER_EMAIL, urlencode(self::PLACEHOLDER_EMAIL),
						self::PLACEHOLDER_HISTORY_ENTRY_CODE, urlencode(self::PLACEHOLDER_HISTORY_ENTRY_CODE),
						self::PLACEHOLDER_SALUTATION),
				array($historyEntry->getEmail(), $historyEntry->getEmail(),
						$historyEntry->getCode(), $historyEntry->getCode(),
						$historyEntry->getSalutation()),
				$content);
	}
}