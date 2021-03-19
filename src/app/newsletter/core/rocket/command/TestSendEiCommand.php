<?php
namespace newsletter\core\rocket\command;

use n2n\l10n\N2nLocale;
use n2n\l10n\DynamicTextCollection;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\web\http\controller\Controller;
use newsletter\core\model\NewsletterState;
use n2n\util\type\CastUtils;
use n2n\core\container\N2nContext;
use n2n\util\uri\Path;
use rocket\impl\ei\component\command\adapter\IndependentEiCommandAdapter;
use rocket\ei\util\Eiu;
use rocket\si\control\SiButton;
use rocket\si\control\SiIconType;

class TestSendEiCommand extends IndependentEiCommandAdapter {
	const CONTROL_KEY = 'testSend';
	
	protected function prepare() {
	}

 	public function getEntryControlOptions(N2nContext $n2nContext, N2nLocale $n2nLocale): array {
 		$dtc = new DynamicTextCollection('newsletter', $n2nLocale);
 		return array(self::CONTROL_KEY => $dtc->t('test_send_txt'));
 	}

	function createEntryGuiControls(Eiu $eiu): array {
		$eiuEntry = $eiu->entry();

		if ($eiuEntry->isNew() || $eiu->frame()->isExecutedBy($this)) {
			return array();
		}

		$newsletterState = $eiu->lookup(NewsletterState::class);
 		CastUtils::assertTrue($newsletterState instanceof NewsletterState);

 		$dtc = $newsletterState->getDtc();
		$siButton = SiButton::secondary($dtc->t('test_send_txt'), SiIconType::ICON_CHECK)
			->setTooltip($dtc->t('test_send_tooltip', array('entry' => $eiu->frame()->getGenericLabel())))
			->setImportant(true);

		$eiuControlFactory = $eiu->factory()->controls();

		return [$eiuControlFactory->newCmdRef(self::CONTROL_KEY, $siButton, new Path([$eiu->entry()->getPid()]))];
	}

 	/**
 	 * {@inheritDoc}
 	 * @see \rocket\spec\ei\component\command\EiCommand::lookupController()
 	 */
 	public function lookupController(Eiu $eiu): Controller {
 		return $eiu->lookup(TestSendController::class);
		
 	}
}
