<?php
namespace newsletter\core\ui;

use n2n\impl\web\ui\view\html\HtmlView;
use newsletter\core\bo\NewsletterCi;
use n2n\web\ui\UiComponent;
use n2n\web\ui\SimpleBuildContext;
use n2n\web\ui\Raw;
use newsletter\core\bo\History;

class NewsletterCiHtmlBuilder {
	private $view;
	
	public function __construct(HtmlView $view) {
		$this->view = $view;
	}
	
	public function getCi(NewsletterCi $newsletterCi) {
		$uiComponent = $newsletterCi->createHtmlUiComponent($this->view);
		
		if ($uiComponent instanceof UiComponent) {
			$uiComponent = $uiComponent->build(new SimpleBuildContext($this->view));
		}
		
		$dom = new \DOMDocument();
		if (false == $dom->loadHTML($uiComponent)) return new Raw($uiComponent);
		
 		foreach ($dom->getElementsByTagName('a') as $link) {
 			if (!$link instanceof \DOMElement) continue;
			$link->setAttribute(History::HTML_ATTR_NAME_CI, $newsletterCi->getId());
 		}
 		
 		return new Raw($dom->saveHTML());
	}
	
	public function ci(NewsletterCi $newsletterCi) {
		$this->view->out($newsletterCi);
	}
}