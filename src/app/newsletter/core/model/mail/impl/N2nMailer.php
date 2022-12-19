<?php
namespace newsletter\core\model\mail\impl;

use newsletter\core\model\mail\Mailer;
use newsletter\core\model\mail\Mail;
use n2n\core\config\SmtpConfig;
use n2n\mail\Transport;

class N2nMailer implements Mailer {
	public function send(Mail $mail) {
		$n2nMail = new \n2n\mail\Mail($mail->getSenderEmail(), $mail->getSubject(), $mail->getContentTxt(), $mail->getRecipient());
		
		Transport::send($n2nMail);
	}

	/**
	 * {@inheritDoc}
	 * @see \newsletter\core\model\mail\Mailer::setup()
	 */
	public function setup(SmtpConfig $smtpConfig) {
		
	}
}