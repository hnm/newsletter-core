<?php
namespace newsletter\core\ei;

use rocket\impl\ei\component\modificator\adapter\IndependentEiModificatorAdapter;
use rocket\ei\util\Eiu;
use newsletter\core\rocket\NewsletterManageDao;
use rocket\ei\EiPropPath;
use n2n\l10n\Message;

class BlacklistedEmailModificator extends IndependentEiModificatorAdapter {

	function setupEiEntry(Eiu $eiu) {
		$eiu->entry()->onValidate(function () use ($eiu) {
			$this->validate($eiu);
		});
	}
	
	public function validate(Eiu $eiu) {
		$email = $eiu->entry()->getValue('email');
		if (empty($email)) return;
		
		$manageDao = $eiu->lookup(NewsletterManageDao::class);
		if (!$manageDao->isBlacklisted($email)) return;
		
		$eiu->entry()->field(EiPropPath::create('email'))->addError(Message::createCode('recipient_blacklisted_err', null, 'newsletter\core'));
	}
}