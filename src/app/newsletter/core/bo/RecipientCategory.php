<?php
namespace newsletter\core\bo;

use n2n\persistence\orm\annotation\AnnoTable;
use n2n\persistence\orm\annotation\AnnoManyToMany;
use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;

class RecipientCategory extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('newsletter_recipient_category'));
		$ai->p('recipients', new AnnoManyToMany(Recipient::getClass(), 'categories'));
	}
	
	private $id;
	private $name;
	private $lft;
	private $rgt;
	private $lastMod;
	private $lastModBy;
	private $recipients;
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}

	public function getLft() {
		return $this->lft;
	}

	public function setLft($lft) {
		$this->lft = $lft;
	}

	public function getRgt() {
		return $this->rgt;
	}

	public function setRgt($rgt) {
		$this->rgt = $rgt;
	}

	/**
	 * @return \newsletter\core\bo\Recipient[]
	 */
	public function getRecipients() {
		return $this->recipients;
	}

	public function setRecipients($recipients) {
		$this->recipients = $recipients;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getLastMod() {
		return $this->lastMod;
	}
	
	public function setLastMod(\DateTime $lastMod = null) {
		$this->lastMod = $lastMod;
	}
	
	public function getLastModBy() {
		return $this->lastModBy;
	}
	
	public function setLastModBy($lastModBy) {
		$this->lastModBy = $lastModBy;
	}
	
	public function equals($obj) {
		return $obj instanceof RecipientCategory && $obj->getId() == $this->getId();
	}
}