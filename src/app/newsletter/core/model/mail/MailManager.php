<?php
namespace newsletter\core\model\mail;

use n2n\context\RequestScoped;
use n2n\util\ex\IllegalStateException;
use n2n\mail\smtp\SmtpConfig;

class MailManager implements RequestScoped {

	private $mailer;
	private $smtpConfig;

	public function setup(Mailer $mailer, SmtpConfig $smtpConfig) {
		$mailer->setup($smtpConfig);
		$this->mailer = $mailer;
		$this->smtpConfig = $smtpConfig;
	}
	
	public function isSetup() {
		return null !== $this->mailer && null !== $this->smtpConfig;
	}
	
	public function send(Mail $mail) {
		IllegalStateException::assertTrue($this->isSetup());
		
		$this->mailer->send($mail);
	}
}