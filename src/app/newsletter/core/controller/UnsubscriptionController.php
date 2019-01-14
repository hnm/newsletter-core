<?php
namespace newsletter\core\controller;

use n2n\web\http\controller\ParamGet;
use newsletter\core\model\UnsubscriptionForm;

class UnsubscriptionController extends NewsletterControllerAdapter {
	public function index(ParamGet $email = null) {
		$this->beginTransaction();
		$unsubscriptionForm = new UnsubscriptionForm($this->newsletterState->getDtc(),
				$this->config->isNotifyOnUnsubscription(),
				$email, $this->config->getUnsubscriptionMailRecipient());
		
		if ($this->dispatch($unsubscriptionForm, 'unsubscribe')) {
			$this->commit();
			$this->redirectToController('confirm');
			return;
		}
		
		$this->commit();
		$this->forwardWithDtc($this->config->getUnsubscriptionFormViewId(),
				array('unsubscriptionForm' => $unsubscriptionForm));
	}
	
	public function doConfirm() {
		$this->checkSubscriptionAllowed();
		$this->forwardWithDtc($this->config->getUnsubscriptionConfirmationViewId());
	}
}