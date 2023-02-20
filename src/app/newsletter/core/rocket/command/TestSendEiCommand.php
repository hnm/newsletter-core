<?php
namespace newsletter\core\rocket\command;

use n2n\l10n\N2nLocale;
use n2n\l10n\DynamicTextCollection;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\web\http\controller\Controller;
use newsletter\core\model\NewsletterState;
use n2n\util\type\CastUtils;
use n2n\core\container\N2nContext;
use rocket\ei\util\Eiu;
use rocket\impl\ei\component\command\IndependentEiCommandAdapter;
use rocket\ei\manage\control\ControlButton;
use rocket\ei\manage\control\IconType;

class TestSendEiCommand extends IndependentEiCommandAdapter {
	const CONTROL_KEY = 'testSend';
	
	protected function prepare() {
	}

 	public function getEntryControlOptions(N2nContext $n2nContext, N2nLocale $n2nLocale): array {
 		$dtc = new DynamicTextCollection('newsletter', $n2nLocale);
 		return array(self::CONTROL_KEY => $dtc->t('test_send_txt'));
 	}

 	function createEntryControls(Eiu $eiu, HtmlView $view): array {
		$eiuEntry = $eiu->entry();

		if ($eiuEntry->isNew() || $eiu->frame()->isExecutedBy($this)) {
			return array();
		}

		$newsletterState = $eiu->lookup(NewsletterState::class);
 		CastUtils::assertTrue($newsletterState instanceof NewsletterState);

 		$dtc = $newsletterState->getDtc();
 		
 		$controlButton = new ControlButton($dtc->t('test_send_txt'),
 				$dtc->t('test_send_tooltip', array('entry' => $eiu->frame()->getGenericLabel())), 
 				true, ControlButton::TYPE_SECONDARY, IconType::ICON_CHECK);
 		
 		$eiuControlFactory = $eiu->frame()->controlFactory($this);
 		
 		return [self::CONTROL_KEY => $eiuControlFactory->createJhtml($controlButton, $eiu->entry()->getPid())];
	}

 	public function lookupController(Eiu $eiu): Controller {
 		return $eiu->lookup(TestSendController::class);
		
 	}
}
