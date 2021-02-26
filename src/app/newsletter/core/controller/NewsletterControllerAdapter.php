<?php

namespace newsletter\core\controller;

use n2n\web\http\controller\ControllerAdapter;
use newsletter\core\model\NewsletterDao;
use newsletter\core\model\NewsletterState;
use n2n\web\http\PageNotFoundException;
use n2n\util\type\ArgUtils;
use n2n\l10n\N2nLocale;
use newsletter\core\bo\RecipientCategory;

class NewsletterControllerAdapter extends ControllerAdapter {
	protected $newsletterDao;
	protected $newsletterState;
	protected $config;
	protected $recipientCategories;
	
	private function _init(NewsletterDao $newsletterDao, NewsletterState $newsletterState) {
		$this->newsletterDao = $newsletterDao;
		$this->newsletterState = $newsletterState;
	}
	
	/**
	 * @return \newsletter\core\bo\RecipientCategory []
	 */
	public function getRecipientCategories() {
		return $this->recipientCategories;
	}
	
	public function setRecipientCategories(array $recipientCategories = null) {
		ArgUtils::valArray($recipientCategories, RecipientCategory::class, true);
		$this->recipientCategories = $recipientCategories;
	}
	
	public function prepare() {
		$this->config = $this->newsletterState->getNewsletterControllerConfig();
	}
	
	protected function forwardWithDtc(string $viewNameExpression, array $params = null, string $moduleNamespace = null) {
		$params = ArgUtils::toArray($params);
		$params['templateViewId'] = $this->config->getTemplateViewId();
		$view = $this->createView($viewNameExpression, $params, $moduleNamespace);
		$view->setDynamicTextCollection($this->newsletterState->getDtc());
		$this->forwardView($view);
	}
	
	protected function checkSubscriptionAllowed() {
		if ($this->config->isSubscriptionAllowed()) return;
		
		throw new PageNotFoundException('subscription is not allowed');
	}
	
	protected function checkRecipient(string $email, N2nLocale $n2nLocale = null) {
		if (null !== ($recipient =
				$this->newsletterDao->getRecipientByEmailAndLocale($email,
						$n2nLocale ?? $this->getRequest()->getN2nLocale()))) {
			return $recipient;
		}
		
		throw new PageNotFoundException('recipient not found');
	}
}