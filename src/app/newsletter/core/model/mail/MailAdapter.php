<?php
namespace newsletter\core\model\mail;

abstract class MailAdapter implements Mail {
	
	public function getAttachments() {
		return null;
	}
}
