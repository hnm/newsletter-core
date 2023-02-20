<?php
namespace newsletter\core\model\mail\impl;

use newsletter\core\model\mail\Mailer;
use newsletter\core\model\mail\Mail;
use n2n\util\ex\IllegalStateException;
use n2n\io\managed\File;
use newsletter\core\model\mail\MailNotSendException;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use n2n\mail\smtp\SmtpConfig;

class SymphonyMailer implements Mailer {
	
	private $mailer;
	
	public function setup(SmtpConfig $smtpConfig) {
		IllegalStateException::assertTrue(null !== $smtpConfig->getHost());
		IllegalStateException::assertTrue(null !== $smtpConfig->getUser());
		IllegalStateException::assertTrue(null !== $smtpConfig->getPassword());
		IllegalStateException::assertTrue(null !== $smtpConfig->getPort());
		
		$user = $smtpConfig->getUser();
		$password = $smtpConfig->getPassword();
		$host = $smtpConfig->getHost();
		$port = $smtpConfig->getPort();
		
		$this->mailer = new \Symfony\Component\Mailer\Mailer(Transport::fromDsn('smtp://' . urlencode($user) . ':' . urlencode($password)
				. '@' . $host . ':' . $port));
		
	}
	
	public function send(Mail $mail) {
		IllegalStateException::assertTrue(null !== $this->mailer, 'Swift mailer not set up properly');
		//$logger = new \Swift_Plugins_Loggers_EchoLogger();
		//$this->mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
		try {
			$symphonyEmail = (new Email())->subject($mail->getSubject())->from(new Address($mail->getSenderEmail(), $mail->getSenderName()))
					->to($mail->getRecipient())->text($mail->getContentTxt())->html($mail->getContentHtml());

			
			if (null !== ($replyToEmail = $mail->getReplyToEmail())) {
				$symphonyEmail->replyTo(new Address($replyToEmail, $mail->getReplyToName()));
			}
			
			if (is_array($attachments = $mail->getAttachments())) {
				foreach ($attachments as $attachment) {
					if ($attachment instanceof File) {
						$symphonyEmail->attachFromPath((string) $attachment->getFileSource()->getFsPath(), $attachment->getOriginalName());
						continue;
					} 
					
					$symphonyEmail->attachFromPath((string) $attachment);
				}
			}
			
			$this->mailer->send($symphonyEmail);
		} catch (\Swift_RfcComplianceException $e) {
			throw new MailNotSendException('Sending newsletter to ' . $mail->getRecipient() . 'failed: ' . $e->getMessage(), 0, $e);
		} catch (\Swift_SwiftException $e) {
			throw new MailNotSendException('Sending newsletter to ' . $mail->getRecipient() . 'failed: ' . $e->getMessage(), 0, $e);
		}
	}
}