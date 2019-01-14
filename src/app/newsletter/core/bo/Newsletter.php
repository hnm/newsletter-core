<?php
namespace newsletter\core\bo;

use n2n\persistence\orm\CascadeType;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\persistence\orm\annotation\AnnoOrderBy;
use n2n\persistence\orm\annotation\AnnoManyToMany;
use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\l10n\N2nLocale;
use newsletter\core\bo\NewsletterCi;
use newsletter\core\bo\RecipientCategory;
use newsletter\core\bo\History;

class Newsletter extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->p('newsletterCis', new AnnoOneToMany(NewsletterCi::getClass(), 
				null, CascadeType::ALL, null, true), new AnnoOrderBy(array('orderIndex' => 'ASC')));
		$ai->p('recipientCategories', new AnnoManyToMany(RecipientCategory::getClass(), null, CascadeType::PERSIST|CascadeType::MERGE));
		$ai->p('histories', new AnnoOneToMany(History::getClass(), 'newsletter', CascadeType::ALL));
	}

	private $id;
	private $subject;
	private $previewText;
	private $n2nLocale;
	private $sent = false;
	private $created;
	private $createdBy;
	private $lastMod;
	private $lastModBy;
	private $newsletterCis;
	private $recipientCategories;
	private $histories;

	public function __construct() {
		$this->created = new \DateTime();
		$this->newsletterCis = new \ArrayObject();
	}

	private function _preUpdate() {
		$this->lastMod = new \DateTime();
	}
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getSubject() {
		return $this->subject;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	public function getPreviewText() {
		return $this->previewText;
	}

	public function setPreviewText($previewText) {
		$this->previewText = $previewText;
	}

	/**
	 * @return N2nLocale
	 */
	public function getN2nLocale() {
		return $this->n2nLocale;
	}

	/**
	 * @param N2nLocale $n2nLocale
	 */
	public function setN2nLocale(N2nLocale $n2nLocale) {
		$this->n2nLocale = $n2nLocale;
	}

	public function isSent() {
		return $this->sent;
	}

	public function setSent($sent) {
		$this->sent = (bool) $sent;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 */
	public function setCreated(\DateTime $created) {
		$this->created = $created;
	}

	public function getCreatedBy() {
		return $this->createdBy;
	}

	public function setCreatedBy($createdBy) {
		$this->createdBy = $createdBy;
	}

	/**
	 * @return \DateTime
	 */
	public function getLastMod() {
		return $this->lastMod;
	}

	/**
	 * @param \DateTime $lastMod
	 */
	public function setLastMod(\DateTime $lastMod) {
		$this->lastMod = $lastMod;
	}

	public function getLastModBy() {
		return $this->lastModBy;
	}

	public function setLastModBy($lastModBy) {
		$this->lastModBy = $lastModBy;
	}

	/**
	 * @return NewsletterCi []
	 */
	public function getNewsletterCis() {
		return $this->newsletterCis;
	}

	/**
	 * @param \ArrayObject $newsletterCis
	 */
	public function setNewsletterCis(\ArrayObject $newsletterCis) {
		$this->newsletterCis = $newsletterCis;
	}

	/**
	 * @return RecipientCategory []
	 */
	public function getRecipientCategories() {
		return $this->recipientCategories;
	}

	public function setRecipientCategories($recipientCategories) {
		$this->recipientCategories = $recipientCategories;
	}

	/**
	 * @return History []
	 */
	public function getHistories() {
		return $this->histories;
	}

	public function setHistories($histories) {
		$this->histories = $histories;
	}

}