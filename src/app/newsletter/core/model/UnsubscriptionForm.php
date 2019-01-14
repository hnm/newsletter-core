<?php
namespace newsletter\core\model;

use n2n\impl\web\dispatch\map\val\ValEmail;
use n2n\web\dispatch\Dispatchable;
use n2n\web\dispatch\map\bind\BindingErrors;
use n2n\web\dispatch\map\bind\BindingDefinition;
use n2n\impl\web\dispatch\map\val\ValNotEmpty;
use n2n\util\type\ArgUtils;
use n2n\l10n\DynamicTextCollection;
use n2n\l10n\Message;

class UnsubscriptionForm implements Dispatchable {
	private $dtc;
	private $sendMail;
	private $mailRecipient;
	
	protected $email;
	
	public function __construct(DynamicTextCollection $dtc, bool $sendMail, 
			string $email = null, string $mailRecipient = null) {
		$this->dtc = $dtc;
		$this->sendMail = $sendMail;
		$this->mailRecipient = $mailRecipient;
		
		if (ValEmail::isEMail($email)) {
			$this->setEmail($email);
		}
	}
	
	public function setEmail(string $email = null) {
		ArgUtils::assertTrue($email === null || ValEmail::isEMail($email));
		
		$this->email = $email;
	}

	public function getEmail() {
		return $this->email;
	}
	
	private function _validation(BindingDefinition $bd) { 
		$bd->val('email', new ValNotEmpty(Message::create($this->dtc->t('newsletter_err_email_invalid'))), 
				new ValEmail(Message::create($this->dtc->t('newsletter_err_email_invalid'))));

		$that = $this;
		$bd->closure(function($email, NewsletterDao $newsletterDao, BindingErrors $be) use ($that){
			if (count($newsletterDao->getRecipientsByEmail($email)) === 0) {
				$be->addError('email', Message::create($that->dtc->t('newsletter_err_email_not_available', 
						array('email' => $email))));
			} 
		});
	}

	public function unsubscribe(NewsletterDao $newsletterDao, DefaultMailModel $mailModel) {
		foreach ($newsletterDao->getRecipientsByEmail($this->email) as $recipient) {
			$newsletterDao->moveToBlacklist($recipient);
		}
		if (!$this->sendMail) return;
		
		$mailModel->sendDeactivationMail($recipient, $this->mailRecipient);
	}
	
}