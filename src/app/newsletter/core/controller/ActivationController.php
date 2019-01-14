<?php

namespace newsletter\core\controller;

use newsletter\core\model\SubscriptionForm;

class ActivationController extends NewsletterControllerAdapter {
	
	public function index($encodedRecipientEmail, $confirmationCode, $simple = null) {
		$this->checkSubscriptionAllowed();
		
		$this->beginTransaction();
		$messageCode = 'newsletter_activation_recipient_not_found';
		$email = base64_decode($encodedRecipientEmail);
		
		$recipient = $this->newsletterDao->getRecipientByEmailAndConfirmationCode($email, $confirmationCode);
		if (null !== $recipient) {
			$messageCode = 'newsletter_activation_success_' . $recipient->getStatus();
			$this->newsletterDao->activateRecipient($recipient);
		}
		
		$message = $this->newsletterState->getDtc()->t($messageCode);
		
		if (!$simple || null === $recipient) {
			$this->commit();
			$this->forwardWithDtc($this->config->getActivationViewId(),
					array('recipient' => $recipient, 'message' => $message));
			return;
		}
		
		$subscriptionForm = new SubscriptionForm($this->newsletterState->getDtc(),
				$this->getRequest()->getN2nLocale(), $this->recipientCategories);
		$subscriptionForm->setFixedEmail($email);
		
		if ($this->dispatch($subscriptionForm, 'subscribe')) {
			$this->commit();
			$this->redirectToController(array('complete', $email));
			return;
		}
		$this->commit();
		
		$this->forwardWithDtc($this->config->getSubscriptionFormViewId(),
				array('subscriptionForm' => $subscriptionForm, 'message' => $message, 'inActivation' => true));
	}
	
	public function doComplete($email) {
		$this->checkSubscriptionAllowed();
		$this->forwardWithDtc($this->config->getActivationCompleteViewId(), array('recipient' => $this->checkRecipient($email)));
	}
}