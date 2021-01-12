<?php
namespace newsletter\core\rocket\command;

use rocket\impl\ei\component\command\adapter\IndependentEiCommandAdapter;

class StatsEiCommand extends IndependentEiCommandAdapter {
	const CONTROL_KEY = 'stats';
	
	protected function prepare() {
	}

// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\spec\ei\manage\control\EntryControlComponent::getEntryControlOptions()
// 	 */
// 	public function getEntryControlOptions(N2nContext $n2nContext, N2nLocale $n2nLocale): array {
// 		$dtc = new DynamicTextCollection('newsletter', $n2nLocale);
// 		return array(self::CONTROL_KEY => $dtc->t('stats_txt'));
		
// 	}

// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\spec\ei\manage\control\EntryControlComponent::createEntryControls()
// 	 */
// 	public function createEntryControls(Eiu $eiu, HtmlView $view): array {
// 		$newsletterDao = $view->lookup(NewsletterDao::class);
// 		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);
		
// 		$newsletter = $eiu->entry()->getEntityObj();
// 		CastUtils::assertTrue($newsletter instanceof Newsletter);
		
// 		$numHistoryEntries = $newsletterDao->getNumHistoryEntriesForNewsletter($newsletter);
// 		if ($numHistoryEntries == 0) return [];
		
// 		$newsletterState = $view->lookup(NewsletterState::class);
// 		$view->assert($newsletterState instanceof NewsletterState);
		
// 		$dtc = $newsletterState->getDtc();
		
// 		$controlButton = new ControlButton($dtc->t('stats_txt'));
// 		$controlButton->setIconType(IconType::ICON_DASHBOARD);
		
// 		return array(self::CONTROL_KEY => $eiu->frame()->controlFactory($this)->createJhtml($controlButton, [$eiu->entry()->getPid()]));
// 	}

// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\spec\ei\component\command\EiCommand::lookupController()
// 	 */
// 	public function lookupController(Eiu $eiu): Controller {
// 		return $eiu->lookup(StatsController::class);
// 	}
}

