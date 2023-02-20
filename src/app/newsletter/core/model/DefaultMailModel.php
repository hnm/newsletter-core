<?php
namespace newsletter\core\model;

use newsletter\core\bo\Recipient;
use n2n\mail\MailUtils;
use n2n\core\N2N;
use n2n\context\RequestScoped;
use n2n\web\http\HttpContext;
use n2n\core\config\N2nLocaleConfig;
use n2n\core\config\MailConfig;

class DefaultMailModel implements RequestScoped {
	private $httpContext;
	private $n2nLocaleConfig;
	private $mailConfig;
	private $newsletterState;

	private $controllerContext;
	
	private function _init(HttpContext $httpContext, 
			N2nLocaleConfig $n2nLocaleConfig, MailConfig $mailConfig, NewsletterState $newsletterState) {
		$this->httpContext = $httpContext;
		$this->n2nLocaleConfig = $n2nLocaleConfig;
		$this->mailConfig = $mailConfig;
		$this->newsletterState = $newsletterState;
	}
	
	public function sendActivationMail(Recipient $recipient, $simple = false) {
		if ($recipient->getStatus() != Recipient::STATUS_UNCONFIRMED) return;
		
		$dtc = $this->newsletterState->getDtc();
		
		$dtc->assignN2nLocale($recipient->getN2nLocale(), true);
		$pageName = $this->getPageName();
		
		$subject = $dtc->t('mail_activation_subject', array('page_name' => $pageName));

		$saluteWith = $recipient->getSaluteWith();
		if (null === $saluteWith) {
			$saluteWith = 'default';
		}
		
		$gender = $recipient->getGender();
		if (null !== $gender) {
			$gender = '_' . $gender;
		}
		
		$message = $dtc->t('newsletter_text_salutation_' . $saluteWith . $gender, 
				array('first_name' => $recipient->getFirstName(), 'last_name' => $recipient->getLastName())) . "\n\n";
		
		$activationUrl = $this->newsletterState->getActivationUrl()->extR(
				array(NewsletterDao::encodeEmail($recipient->getEmail()), $recipient->getConfirmationCode(), ($simple) ? true : null));
		if ($activationUrl->isRelative()) {
			$activationUrl = $this->httpContext->getRequest()->getHostUrl()->ext($activationUrl);
		}
				
		$message .= $dtc->t('mail_activation_message', array('link' => (string) $activationUrl, 'pageName' => $pageName));
		MailUtils::sendNotificationMail($subject, $message, $recipient->getEmail());
	}
	
	public function sendDeactivationMail(Recipient $recipient, string $mailRecipient = null) {
		if (!($this->newsletterState->getNewsletterControllerConfig()->isNotifyOnUnsubscription())) return; 
		
		$dtc = $this->newsletterState->getDtc();
		$dtc->assignN2nLocale($this->n2nLocaleConfig->getAdminN2nLocale(), true);
		
		$subject = $dtc->t('mail_deactivation_subject', array('page_name' => $this->getPageName()));
		$message = $dtc->t('mail_deactivation_message', array('email' => $recipient->getEmail()));
		
		MailUtils::sendNotificationMail($subject, $message, $mailRecipient ?? $this->mailConfig->getDefaultAddresser());
	}
	
	private function getPageName() {
		return N2N::getAppConfig()->general()->getPageName();
	}
}