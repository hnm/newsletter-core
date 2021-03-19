<?php
namespace newsletter\core\model\ui;

use n2n\impl\web\ui\view\html\HtmlView;
use newsletter\core\bo\Recipient;
use n2n\impl\web\ui\view\html\HtmlElement;

class NewsletterHtmlBuilder {
	
	private $view;
	private $formHtml;
	
	public function __construct(HtmlView $view) {
		$this->view = $view;
		$this->formHtml = $view->getFormHtmlBuilder();
	}
	
	public function getSalutationRadio($propertyExpression, array $attrs = null) {
		$elemContainer = new HtmlElement('div', $attrs);
		foreach (Recipient::getSalutations() as $key => $salutation) {
			$label = new HtmlElement('label', array('class' => 'radio mr-2'),
					$this->formHtml->getInputRadio($propertyExpression, $salutation, array('class' => 'mr-1')));
			$label->appendContent($this->view->getL10nText('newsletter_salutation_' . $salutation));
			$elemContainer->appendContent($label);
		}
		return $elemContainer;
	}
	
	public function saluationRadio($propertyExpression, array $attrs = null) {
		$this->view->out($this->getSalutationRadio($propertyExpression, $attrs));
	}
	
	public function getGenderRadio($propertyExpression, array $attrs = null) {
		$elemContainer = new HtmlElement('div', $attrs);
		foreach (Recipient::getGenders() as $gender) {
			$label = new HtmlElement('label', array('class' => 'radio mr-2'),
					$this->formHtml->getInputRadio($propertyExpression, $gender, array('class' => 'mr-1')));
			$label->appendContent($this->view->getL10nText('newsletter_gender_' . $gender));
			$elemContainer->appendContent($label);
		}
		return $elemContainer;
	}
	
	public function genderRadio($propertyExpression, array $attrs = null) {
		$this->view->out($this->getGenderRadio($propertyExpression, $attrs));
	}
}
