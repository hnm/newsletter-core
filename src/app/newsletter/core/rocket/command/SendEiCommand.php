<?php
namespace newsletter\core\rocket\command;

use n2n\util\type\CastUtils;
use n2n\web\http\controller\Controller;
use newsletter\core\bo\Newsletter;
use newsletter\core\model\NewsletterDao;
use newsletter\core\model\NewsletterState;
use rocket\ei\util\Eiu;
use rocket\impl\ei\component\command\IndependentEiCommandAdapter;
use n2n\impl\web\ui\view\html\HtmlView;
use rocket\ei\manage\control\ControlButton;
use rocket\ei\manage\control\IconType;

class SendEiCommand extends IndependentEiCommandAdapter {
	const CONTROL_KEY = 'send';

	protected function prepare() {
	}


	function createEntryControls(Eiu $eiu, HtmlView $view): array {
		$eiuEntry = $eiu->entry();

		if ($eiuEntry->isNew()) {
			return array();
		}

 		$newsletterDao = $eiu->lookup(NewsletterDao::class);
 		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);

 		$newsletter = $eiu->entry()->getEntityObj();
 		CastUtils::assertTrue($newsletter instanceof Newsletter);
 		$numRecipients = $newsletterDao->getNumRecipientsForNewsletter($newsletter);
 		if ($numRecipients == 0) {
 			return [];
 		}

 		$newsletterState = $eiu->lookup(NewsletterState::class);
 		CastUtils::assertTrue($newsletterState instanceof NewsletterState);

		$dtc = $newsletterState->getDtc();
 		$dtc->assignModule('rocket');
 		
 		$controlButton = new ControlButton($dtc->t('send_txt'),
 				null, true, ControlButton::TYPE_SECONDARY, IconType::ICON_ENVELOPE_OPEN);
 		
 		$controlButton->setConfirmMessage($dtc->t('send_confirm_msg',
 					['subject' => $newsletter->getSubject(), 'num_recipients' => $numRecipients]));
 		$controlButton->setConfirmOkButtonLabel($dtc->t('common_yes_label'));
 		$controlButton->setConfirmCancelButtonLabel($dtc->t('common_no_label'));

 		$eiuControlFactory = $eiu->frame()->controlFactory($this);
 		
 		return [self::CONTROL_KEY => $eiuControlFactory->createJhtml($controlButton, $eiu->entry()->getPid())];
	}

	public function lookupController(Eiu $eiu): Controller {
		return $eiu->lookup(SendController::class);
	}
}
