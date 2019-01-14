<?php
namespace newsletter\core\config;

class NewsletterControllerConfig {
	const DEFAULT_TEMPLATE_VIEW_ID = '~\view\sampleTemplate.html';
	const DEFAULT_SUBSCRIPTION_FORM_VIEW_ID = '~\view\subscriptionForm.html';
	const DEFAULT_SIMPLE_SUBSCRIPTION_FORM_VIEW_ID = '~\view\simpleSubscriptionForm.html';
	const DEFAULT_SUBSCRIPTION_THANKS_VIEW_ID = '~\view\subscriptionThanks.html';
	const DEFAULT_ACTIVATION_VIEW_ID = '~\view\activation.html';
	const DEFAULT_ACTIVATION_COMPLETE_VIEW_ID = '~\view\activationComplete.html';
	const DEFAULT_UNSUBSCRIPTIION_FORM_VIEW_ID = '~\view\unsubscriptionForm.html';
	const DEFAULT_UNSUBSCRIPTIION_CONFIRMATION_VIEW_ID = '~\view\unsubscriptionConfirmation.html';

	private $notifyOnUnsubscription = false;
	private $unsubscriptionMailRecipient;
	private $subscriptionAllowed = true;
	
	private $templateViewId = self::DEFAULT_TEMPLATE_VIEW_ID;
	private $subscriptionFormViewId = self::DEFAULT_SUBSCRIPTION_FORM_VIEW_ID;
	private $simpleSubscriptionFormViewId = self::DEFAULT_SIMPLE_SUBSCRIPTION_FORM_VIEW_ID;
	private $subscriptionThanksViewId = self::DEFAULT_SUBSCRIPTION_THANKS_VIEW_ID;
	private $activationViewId = self::DEFAULT_ACTIVATION_VIEW_ID;
	private $activationCompleteViewId = self::DEFAULT_ACTIVATION_COMPLETE_VIEW_ID;
	private $unsubscriptionFormViewId = self::DEFAULT_UNSUBSCRIPTIION_FORM_VIEW_ID;
	private $unsubscriptionConfirmationViewId = self::DEFAULT_UNSUBSCRIPTIION_CONFIRMATION_VIEW_ID;

	public function isNotifyOnUnsubscription() {
		return $this->notifyOnUnsubscription;
	}
	
	public function setNotifyOnUnsubscription(bool $notifyOnUnsubscription) {
		$this->notifyOnUnsubscription = $notifyOnUnsubscription;
		
		return $this;
	}
	
	public function getUnsubscriptionMailRecipient() {
		return $this->unsubscriptionMailRecipient;
	}
	
	public function setUnsubscriptionMailRecipient(string $unsubscriptionMailRecipient = null) {
		$this->unsubscriptionMailRecipient = $unsubscriptionMailRecipient;
		
		return $this;
	}
	
	public function getTemplateViewId() {
		return $this->templateViewId;
	}
	
	public function setTemplateViewId($templateViewId) {
		$this->templateViewId = $templateViewId;
		
		return $this;
	}
	
	public function getSubscriptionFormViewId() {
		return $this->subscriptionFormViewId;
	}
	
	public function setSubscriptionFormViewId($subscriptionFormViewId) {
		$this->subscriptionFormViewId = $subscriptionFormViewId;
		
		return $this;
	}
	
	public function getSimpleSubscriptionFormViewId() {
		return $this->simpleSubscriptionFormViewId;
	}
	
	public function setSimpleSubscriptionFormViewId($simpleSubscriptionFormViewId) {
		$this->simpleSubscriptionFormViewId = $simpleSubscriptionFormViewId;
		
		return $this;
	}
	
	public function getSubscriptionThanksViewId() {
		return $this->subscriptionThanksViewId;
	}
	
	public function setSubscriptionThanksViewId($subscriptionThanksViewId) {
		$this->subscriptionThanksViewId = $subscriptionThanksViewId;
		
		return $this;
	}
	
	public function getActivationViewId() {
		return $this->activationViewId;
	}
	
	public function setActivationViewId($activationViewId) {
		$this->activationViewId = $activationViewId;
		
		return $this;
	}
	
	public function getActivationCompleteViewId() {
		return $this->activationCompleteViewId;
	}
	
	public function setActivationCompleteViewId($activationCompleteViewId) {
		$this->activationCompleteViewId = $activationCompleteViewId;
		
		return $this;
	}
	
	public function getUnsubscriptionFormViewId() {
		return $this->unsubscriptionFormViewId;
	}
	
	public function setUnsubscriptionFormViewId($unsubscriptionFormViewId) {
		$this->unsubscriptionFormViewId = $unsubscriptionFormViewId;
		
		return $this;
	}
	
	public function getUnsubscriptionConfirmationViewId() {
		return $this->unsubscriptionConfirmationViewId;
	}
	
	public function setUnsubscriptionConfirmationViewId($unsubscriptionConfirmationViewId) {
		$this->unsubscriptionConfirmationViewId = $unsubscriptionConfirmationViewId;
		
		return $this;
	}
	public function isSubscriptionAllowed() {
		return $this->subscriptionAllowed;
	}

	public function setSubscriptionAllowed(bool $subscriptionAllowed) {
		$this->subscriptionAllowed = $subscriptionAllowed;
		
		return $this;
	}
}