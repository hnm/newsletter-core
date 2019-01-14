<?php
namespace newsletter\core\bo;

use n2n\persistence\orm\annotation\AnnoId;
use n2n\persistence\orm\annotation\AnnoTable;
use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;

class Blacklisted extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('newsletter_blacklisted'));
		$ai->p('email', new AnnoId(false));
	}
	
	private $email;
	/**
	 * @var \DateTime
	 */
	private $created;
	
	public function __construct() {
		$this->created = new \DateTime();
	}

	public function setEmail($email) {
		$this->email = $email;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function setCreated(\DateTime $created = null) {
		$this->created = $created;
	}
	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}
}