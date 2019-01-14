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
use rocket\impl\ei\component\command\IndependentEiCommandAdapter;
use rocket\ei\manage\control\ControlButton;
use rocket\ei\manage\control\IconType;
use rocket\ei\util\Eiu;
use rocket\ei\component\command\control\EntryControlComponent;

class TestSendEiCommand extends IndependentEiCommandAdapter implements EntryControlComponent {
	const CONTROL_KEY = 'testSend';

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\manage\control\EntryControlComponent::getEntryControlOptions()
	 */
	public function getEntryControlOptions(N2nContext $n2nContext, N2nLocale $n2nLocale): array {
		$dtc = new DynamicTextCollection('newsletter', $n2nLocale);
		return array(self::CONTROL_KEY => $dtc->t('test_send_txt'));
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\manage\control\EntryControlComponent::createEntryControls()
	 */
	public function createEntryControls(Eiu $eiu, HtmlView $view): array {
		$cf = $eiu->frame()->controlFactory($this);
		
		$newsletterState = $view->lookup(NewsletterState::class);
		CastUtils::assertTrue($newsletterState instanceof NewsletterState);
		
		$dtc = $newsletterState->getDtc();
		
		$controlButton = new ControlButton($dtc->t('test_send_txt'));
		$controlButton->setIconType(IconType::ICON_CHECK);
		
		$urlExt = (new Path(array($eiu->entry()->getPid())))
				->toUrl(array('refPath' => (string) $eiu->frame()->getCurrentUrl()));
		
		return array(self::CONTROL_KEY => $cf->createJhtml($controlButton, $urlExt));
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\command\EiCommand::lookupController()
	 */
	public function lookupController(Eiu $eiu): Controller {
		return $eiu->lookup(TestSendController::class);
		
	}
}