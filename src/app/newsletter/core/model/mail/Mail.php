<?php
namespace newsletter\core\model\mail;

interface Mail {
	public function getRecipient();
	public function getSenderEmail();
	public function getSenderName();
	public function getSubject();
	public function getContentHtml();
	public function getContentTxt();
	public function getAttachments();
	public function getReplyToEmail();
	public function getReplyToName();
}
