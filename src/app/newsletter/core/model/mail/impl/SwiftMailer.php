<?php
namespace newsletter\core\model\mail\impl;

use newsletter\core\model\mail\Mailer;
use newsletter\core\model\mail\Mail;
use n2n\util\ex\IllegalStateException;
use n2n\io\managed\File;
use newsletter\core\model\mail\MailNotSendException;
use n2n\mail\smtp\SmtpConfig;

/**
 * @author thomasgunther
 * @deprecated
 */
class SwiftMailer implements Mailer {
	
	private $mailer;
	
	public function setup(SmtpConfig $smtpConfig) {
		$transport = new \Swift_SmtpTransport($smtpConfig->getHost(), $smtpConfig->getPort());
		
		if ($smtpConfig->getSecurityMode()) {
			$transport->setEncryption($smtpConfig->getSecurityMode());
		}
		
		if ($smtpConfig->doAuthenticate()) {
			$transport->setUsername($smtpConfig->getUser())
					->setPassword($smtpConfig->getPassword());
		}
		
		$this->mailer = new \Swift_Mailer($transport);
	}
	
	public function send(Mail $mail) {
		IllegalStateException::assertTrue(null !== $this->mailer, 'Swift mailer not set up properly');
		//$logger = new \Swift_Plugins_Loggers_EchoLogger();
		//$this->mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
		try {
			$swiftMessage = (new \Swift_Message($mail->getSubject()))->setFrom(array($mail->getSenderEmail() => $mail->getSenderName()))
					->setTo($mail->getRecipient())->setBody($mail->getContentHtml(), 'text/html')
					->addPart($mail->getContentTxt(), 'text/plain');
			
			if (null !== ($replyToEmail = $mail->getReplyToEmail())) {
				$swiftMessage->setReplyTo($replyToEmail, $mail->getReplyToName());
			}
			
			if (is_array($attachments = $mail->getAttachments())) {
				foreach ($attachments as $attachment) {
					$swAttach = null;
					if ($attachment instanceof File) {
						$swAttach = \Swift_Attachment::fromPath((string) $attachment->getFileSource()->getFsPath());
						$swAttach->setFilename($attachment->getOriginalName());
					} else {
						$swAttach = \Swift_Attachment::fromPath((string) $attachment);
					}
					
					$swiftMessage->attach($swAttach);
				}
			}
			
			$this->mailer->send($swiftMessage);
		} catch (\Swift_RfcComplianceException $e) {
			throw new MailNotSendException('Sending newsletter to ' . $mail->getRecipient() . 'failed: ' . $e->getMessage(), 0, $e);
		} catch (\Swift_SwiftException $e) {
			throw new MailNotSendException('Sending newsletter to ' . $mail->getRecipient() . 'failed: ' . $e->getMessage(), 0, $e);
		}
	}
}