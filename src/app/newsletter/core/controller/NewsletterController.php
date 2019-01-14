<?php
namespace newsletter\core\controller;

use newsletter\core\model\SubscriptionForm;
use newsletter\core\model\SimpleSubscriptionForm;
use n2n\web\http\controller\ParamGet;

class NewsletterController extends NewsletterControllerAdapter {
	const ACTION_ACTIVATE = 'activate';
	const ACTION_SIMPLE = 'simple';
	const ACTION_THANKS = 'thanks';
	const ACTION_UNSUBSCRIBE = 'unsubscribe';
	
	public function index() {
		if (!$this->config->isSubscriptionAllowed()) {
			$this->redirect($this->newsletterState->buildUnsubscriptionUrl());
			return;
		}
		
		$this->beginTransaction();
		
		$subscriptionForm = new SubscriptionForm($this->newsletterState->getDtc(), $this->getRequest()->getN2nLocale(),
				$this->recipientCategories);
		
		if ($this->dispatch($subscriptionForm, 'subscribe')) {
			$this->commit();
			$this->redirect($this->newsletterState->buildThanksUrl($subscriptionForm->getEmail()));
			return;
		}
		
		$this->commit();
		
		$this->forwardWithDtc($this->config->getSubscriptionFormViewId(), 
				array('subscriptionForm' => $subscriptionForm));
	}
	
	public function doSimple() {
		if (!$this->config->isSubscriptionAllowed()) {
			$this->redirect($this->newsletterState->buildUnsubscriptionUrl());
			return;
		}
		
		$this->beginTransaction();
		$simpleSubscriptionForm = new SimpleSubscriptionForm($this->newsletterState->getDtc(), $this->recipientCategories);
		if ($this->dispatch($simpleSubscriptionForm, 'subscribe')) {
			$this->commit();
			$this->redirect($this->newsletterState->buildThanksUrl($simpleSubscriptionForm->email));
			return;
		}
		
		$this->commit();
		
		$this->forwardWithDtc($this->config->getSimpleSubscriptionFormViewId(), 
				array('simpleSubscriptionForm' => $simpleSubscriptionForm));
	}
	
	public function doThanks(ParamGet $email) {
		$email = (string) $email;
		$this->checkSubscriptionAllowed();
		$this->forwardWithDtc($this->config->getSubscriptionThanksViewId(),
				array('recipient' => $this->checkRecipient($email)));
	}
	
	public function doActivate(ActivationController $activationController, array $delegateParams = null) {
		$activationController->setRecipientCategories($this->recipientCategories);
		$this->delegate($activationController);
	}
	
	public function doUnsubscribe(UnsubscriptionController $unsubscriptionController, array $delegateParams = null) {
		$this->delegate($unsubscriptionController);
	}
}