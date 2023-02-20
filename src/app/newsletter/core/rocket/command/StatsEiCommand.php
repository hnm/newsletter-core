<?php
namespace newsletter\core\rocket\command;

use n2n\util\type\CastUtils;
use n2n\web\http\controller\Controller;
use newsletter\core\bo\Newsletter;
use newsletter\core\model\NewsletterDao;
use newsletter\core\model\NewsletterState;
use rocket\ei\util\Eiu;
use rocket\ei\manage\control\ControlButton;
use rocket\ei\manage\control\IconType;
use rocket\impl\ei\component\command\IndependentEiCommandAdapter;
use n2n\impl\web\ui\view\html\HtmlView;

class StatsEiCommand extends IndependentEiCommandAdapter {
	const CONTROL_KEY = 'stats';

	protected function prepare() {
	}

	function createEntryControls(Eiu $eiu, HtmlView $view): array {
 		$newsletterDao = $eiu->lookup(NewsletterDao::class);
 		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);

 		$newsletter = $eiu->entry()->getEntityObj();
 		CastUtils::assertTrue($newsletter instanceof Newsletter);

 		$numHistoryEntries = $newsletterDao->getNumHistoryEntriesForNewsletter($newsletter);
 		if ($numHistoryEntries == 0) return [];

 		$newsletterState = $eiu->lookup(NewsletterState::class);

 		$dtc = $newsletterState->getDtc();

 		$controlButton = new ControlButton($dtc->t('stats_txt'),
 				null, true, ControlButton::TYPE_SECONDARY, IconType::ICON_TACHOMETER_ALT);
 		
 		$eiuControlFactory = $eiu->frame()->controlFactory($this);
 		
 		return [self::CONTROL_KEY => $eiuControlFactory->createJhtml($controlButton, $eiu->entry()->getPid())];
 	}

 	public function lookupController(Eiu $eiu): Controller {
 		return $eiu->lookup(StatsController::class);
 	}
}

