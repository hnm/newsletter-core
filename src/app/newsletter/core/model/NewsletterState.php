<?php
namespace newsletter\core\model;

use n2n\context\RequestScoped;
use n2n\util\ex\IllegalStateException;
use n2n\util\uri\Url;
use n2n\l10n\DynamicTextCollection;
use newsletter\core\config\TemplateConfig;
use newsletter\core\config\NewsletterControllerConfig;
use n2n\core\config\MailConfig;
use newsletter\core\bo\Newsletter;
use newsletter\core\bo\HistoryEntry;
use newsletter\core\controller\NewsletterController;

class NewsletterState implements RequestScoped {
	private $templateUrl;
	
	private $newsletterUrl;
	private $thanksUrl;
	private $simpleUrl;
	private $unsubscriptionUrl;
	private $activationUrl;
	
	private $templateStyleCollection;
	private $dtc;
	private $templateConfig;
	private $newsletterControllerConfig;
	private $senderEmail;
	private $senderName;
	private $smtpConfig;
	private $recipientCategories;
	private $replyToEmail;
	private $replyToName;
	
	private function _init(DynamicTextCollection $dtc, MailConfig $mailConfig) {
		$this->templateStyleCollection = new SampleStyleCollection();
		$this->templateConfig = new TemplateConfig();
		$this->newsletterControllerConfig = new NewsletterControllerConfig();
		$this->dtc = $dtc;
		$this->senderEmail = $mailConfig->getDefaultAddresser();
	}
	
	public function setTemplateUrl(Url $templateUrl) {
		$this->templateUrl = $templateUrl;
	}
	
	public function hasNewsletterUrl() {
		return null !== $this->newsletterUrl;
	}
	
	public function setNewsletterUrl(Url $newsletterUrl) {
		$this->newsletterUrl = $newsletterUrl;
	}
	
	public function getTemplateUrl() {
		IllegalStateException::assertTrue(null !== $this->templateUrl);
		
		return $this->templateUrl;
	}
	
	public function getNewsletterUrl() {
		IllegalStateException::assertTrue(null !== $this->newsletterUrl);
		
		return $this->newsletterUrl;
	}
	
	public function setThanksUrl(Url $thanksUrl = null) {
		$this->thanksUrl = $thanksUrl;
	}
	
	public function getThanksUrl() {
		IllegalStateException::assertTrue(null !== $this->thanksUrl || $this->hasNewsletterUrl());
		if (null !== $this->thanksUrl) return $this->thanksUrl;
		
		return $this->newsletterUrl->pathExt(NewsletterController::ACTION_THANKS);
	}
	
	public function setSimpleUrl(Url $simpleUrl = null) {
		$this->simpleUrl = $simpleUrl;
	}
	
	public function getSimpleUrl() {
		IllegalStateException::assertTrue(null !== $this->simpleUrl || $this->hasNewsletterUrl());
		if (null !== $this->simpleUrl) return $this->simpleUrl;
		
		return $this->newsletterUrl->pathExt(NewsletterController::ACTION_SIMPLE);
	}
	
	public function setUnsubscriptionUrl(Url $unsubscriptionUrl) {
		$this->unsubscriptionUrl = $unsubscriptionUrl;
	}
	
	public function getUnsubscriptionUrl() {
		IllegalStateException::assertTrue(null !== $this->unsubscriptionUrl || $this->hasNewsletterUrl());
		
		if (null !== $this->unsubscriptionUrl) return $this->unsubscriptionUrl;
		
		return $this->newsletterUrl->pathExt(NewsletterController::ACTION_UNSUBSCRIBE);
	}
	
	public function getActivationUrl() {
		IllegalStateException::assertTrue(null !== $this->activationUrl || $this->hasNewsletterUrl());
		if (null !== $this->activationUrl) return $this->activationUrl;
		
		return $this->newsletterUrl->pathExt(NewsletterController::ACTION_ACTIVATE);
	}

	public function setActivationUrl(Url $activationUrl) {
		$this->activationUrl = $activationUrl;
	}

	public function setTemplateStyleCollection(TemplateStyleCollection $templateStyleCollection) {
		$this->templateStyleCollection = $templateStyleCollection;
	}
	
	/**
	 * @return \newsletter\core\model\TemplateStyleCollection
	 */
	public function getTemplateStyleCollection() {
		return $this->templateStyleCollection;
	}
	
	public function getDtc() {
		return $this->dtc;
	}

	public function setDtc(DynamicTextCollection $dtc) {
		$this->dtc = $dtc;
	}

	public function getTemplateConfig() {
		return $this->templateConfig;
	}

	public function setTemplateConfig(TemplateConfig $templateConfig) {
		$this->templateConfig = $templateConfig;
	}

	public function getNewsletterControllerConfig() {
		return $this->newsletterControllerConfig;
	}

	public function setNewsletterControllerConfig(NewsletterControllerConfig $newsletterControllerConfig) {
		$this->newsletterControllerConfig = $newsletterControllerConfig;
	}
	
	public function getSenderEmail() {
		return $this->senderEmail;
	}
	
	public function setSenderEmail(string $senderEmail) {
		$this->senderEmail = $senderEmail;
	}
	
	public function getSenderName() {
		return $this->senderName;
	}

	public function setSenderName(string $senderName) {
		$this->senderName = $senderName;
	}
	
	public function getReplyToEmail() {
		return $this->replyToEmail;
	}
	
	public function setReplyToEmail(string $replyToEmail) {
		$this->replyToEmail = $replyToEmail;
	}
	
	public function getReplyToName() {
		return $this->replyToName;
	}
	
	public function setReplyToName($replyToName) {
		$this->replyToName = $replyToName;
	}
	
	public function buildUnsubscriptionUrl(string $email = null) {
		$query = [];
		if (null !== $email) {
			$query['email'] = $email; 
		}
		
		return $this->getUnsubscriptionUrl()->extR(null, $query);
	}
	
	public function buildThanksUrl(string $email) {
		return $this->getThanksUrl()->extR(null, array('email' => $email));
	}
	
	public function buildWebTemplateUrl(Newsletter $newsletter, HistoryEntry $historyEntry) {
		return $this->templateUrl->extR(array($newsletter->getId(), $historyEntry->getCode()));
	}
}