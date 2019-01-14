<?php
namespace newsletter\core\bo;

use n2n\l10n\N2nLocale;
use n2n\persistence\orm\annotation\AnnoTable;
use n2n\persistence\orm\annotation\AnnoManyToMany;
use n2n\util\type\ArgUtils;
use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\l10n\DynamicTextCollection;
use n2n\persistence\orm\annotation\AnnoJoinTable;

class Recipient extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('newsletter_recipient'));
		$ai->p('categories', new AnnoManyToMany(RecipientCategory::getClass()), 
				new AnnoJoinTable('newsletter_recipient_recipient_categories'));
		$ai->p('historyEntryClicks', new AnnoOneToMany(HistoryLinkClick::getClass()));
	}
	
	const STATUS_UNCONFIRMED = 'unconfirmed';
	const STATUS_ACTIVE = 'active';
	const GENDER_MALE = 'male';
	const GENDER_FEMALE = 'female';
	const SALUTE_WITH_FIRST = 'first';
	const SALUTE_WITH_LAST = 'last';
	
	private $id;
	private $email;
	private $firstName;
	private $lastName;
	private $gender;
	private $status = self::STATUS_ACTIVE;
	private $saluteWith;
	private $confirmationCode;
	private $n2nLocale;
	private $lastMod;
	private $lastModBy;
	private $categories;
	private $created;
	private $historyEntryClicks;
	
	public function __construct($email = null, $firstName = null, $lastName = null) {
		$this->created = new \DateTime();
		$this->email = $email;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
	}
	
	private function _preUpdate() {
		$this->lastMod = new \DateTime();
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setEmail($email) {
		$this->email = $email;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}
	
	public function getFirstName() {
		return $this->firstName;
	}
	
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}
	
	public function getLastName() {
		return $this->lastName;
	}
	
	public function setGender($gender){
		if (!in_array($gender, array(self::GENDER_MALE, self::GENDER_FEMALE))) {
			$gender = null;
		}
		$this->gender = $gender;
	}
	
	public function getGender() {
		return $this->gender;
	}
	
	public function setStatus($status = self::STATUS_UNCONFIRMED) {
		ArgUtils::valEnum($status, array(self::STATUS_ACTIVE, self::STATUS_UNCONFIRMED));
		if (!in_array($status, array(self::STATUS_UNCONFIRMED, self::STATUS_ACTIVE))) {
			$status = self::STATUS_UNCONFIRMED;
		}
		$this->status = $status;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function setSaluteWith($saluteWith = null) {
		if (!in_array($saluteWith, array(self::SALUTE_WITH_FIRST, self::SALUTE_WITH_LAST))) {
			$saluteWith = null;
		}

		$this->saluteWith = $saluteWith;
	}
	
	public function getSaluteWith() {
		return $this->saluteWith;
	}
	
	public function setConfirmationCode($confirmationCode) {
		$this->confirmationCode = $confirmationCode;
	}
	
	public function getConfirmationCode() {
		return $this->confirmationCode;
	}
	
	/**
	 * @return \newsletter\core\bo\RecipientCategory[]
	 */
	public function getCategories() {
		return $this->categories;
	}

	public function setCategories($categories) {
		$this->categories = $categories;
	}

	public function setN2nLocale(N2nLocale $n2nLocale) {
		$this->n2nLocale = $n2nLocale;
	}
	/**
	 * @return \n2n\l10n\Locale
	 */
	public function getN2nLocale() {
		return $this->n2nLocale;
	}

	public function setLastMod(\DateTime $lastmod = null) {
		$this->lastMod = $lastmod;
	}
	
	public function getLastMod() {
		return $this->lastMod;
	}
	
	public function getLastModBy() {
		return $this->lastModBy;
	}

	public function setLastModBy($lastModBy) {
		$this->lastModBy = $lastModBy;
	}
	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	public function setCreated(\DateTime $created = null) {
		$this->created = $created;
	}
	
	/**
	 * @return HistoryEntryClick
	 */
	public function getHistoryEntryClicks() {
		return $this->historyEntryClicks;
	}

	public function setHistoryEntryClicks($historyEntryClicks) {
		$this->historyEntryClicks = $historyEntryClicks;
	}

	public function getFullName() {
		return trim($this->firstName . ' ' . $this->lastName);
	}
	
	public function getFullEmailAddress() {
		$fullname = $this->getFullName();
		if (empty($fullname)) {
			return $this->email;
		} else {
			return $fullname . ' <' . $this->email . '>';
		}
	}
	
	public function isActive() {
		return $this->getStatus() == self::STATUS_ACTIVE;
	}
	
	public static function getSalutations() {
		return array(self::SALUTE_WITH_FIRST, self::SALUTE_WITH_LAST);
	}
	
	public static function getGenders() {
		return array(self::GENDER_MALE, self::GENDER_FEMALE);
	}
	
	public function buildSalutation(DynamicTextCollection $dtc) {
		return $dtc->t('newsletter_text_salutation_' . ($this->saluteWith ?? 'default') 
				. (null !== $this->gender ? '_' . $this->gender : ''), array('first_name' => $this->firstName, 'last_name' => $this->lastName));
	}
}