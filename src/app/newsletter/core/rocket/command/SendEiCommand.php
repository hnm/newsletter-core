<?php
namespace newsletter\core\rocket\command;

use rocket\impl\ei\component\command\adapter\IndependentEiCommandAdapter;

class SendEiCommand extends IndependentEiCommandAdapter {
	const CONTROL_KEY = 'send';
	
	protected function prepare() {
	}

	
// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\spec\ei\manage\control\EntryControlComponent::getEntryControlOptions()
// 	 */
// 	public function getEntryControlOptions(N2nContext $n2nContext, N2nLocale $n2nLocale): array {
// 		$dtc = new DynamicTextCollection('newsletter', $n2nLocale);
		
// 		return array(self::CONTROL_KEY => $dtc->t('send_txt'));
// 	}
	
// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\spec\ei\manage\control\EntryControlComponent::createEntryControls()
// 	 */
// 	public function createEntryControls(Eiu $eiu, HtmlView $view): array {
// 		if (!$eiu->gui()->isBulky()) return [];
		
// 		$newsletterDao = $view->lookup(NewsletterDao::class);
// 		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);
		
// 		$newsletter = $eiu->entry()->getEntityObj();
// 		CastUtils::assertTrue($newsletter instanceof Newsletter);
		
// 		$numRecipients = $newsletterDao->getNumRecipientsForNewsletter($newsletter);
// 		if ($numRecipients == 0) return [];
		
// 		$newsletterState = $view->lookup(NewsletterState::class);
// 		CastUtils::assertTrue($newsletterState instanceof NewsletterState);
		
// 		$dtc = $newsletterState->getDtc();
// 		$dtc->assignModule('rocket');
		
// 		$controlButton = new ControlButton($dtc->t('send_txt'));
// 		$controlButton->setIconType(IconType::ICON_ENVELOPE_O);
// 		$controlButton->setImportant(true);
// 		$controlButton->setConfirmMessage($dtc->translate('send_confirm_msg',
// 				array('subject' => $newsletter->getSubject(), 'num_recipients' => $numRecipients)));
// 		$controlButton->setConfirmOkButtonLabel($dtc->t('common_yes_label'));
// 		$controlButton->setConfirmCancelButtonLabel($dtc->t('common_no_label'));
		
// 		return array(self::CONTROL_KEY => 
// 				$eiu->frame()->controlFactory($this)->createJhtml($controlButton, [$eiu->entry()->getPid()]));
// 	}
	
// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\spec\ei\component\command\EiCommand::lookupController()
// 	 */
// 	public function lookupController(Eiu $eiu): Controller {
// 		return $eiu->lookup(SendController::class);
		
// 	}
}