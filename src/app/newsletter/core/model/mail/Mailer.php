<?php
namespace newsletter\core\model\mail;

use n2n\mail\smtp\SmtpConfig;

interface Mailer {
	public function send(Mail $mail);
	public function setup(SmtpConfig $smtpConfig);
}