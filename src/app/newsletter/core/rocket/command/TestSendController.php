<?php
namespace newsletter\core\rocket\command;

use n2n\l10n\DynamicTextCollection;
use n2n\web\http\controller\ControllerAdapter;
use n2n\l10n\MessageContainer;
use n2n\web\http\Request;
use newsletter\core\model\NewsletterState;
use rocket\core\model\RocketState;
use rocket\ei\util\Eiu;
use rocket\ei\util\EiuCtrl;

class TestSendController extends ControllerAdapter {
	/**
	 * @var EiuCtrl
	 */
	private $eiuCtrl;
	/**
	 * @var Eiu $eiu
	 */
	private $eiu;
	/**
	 * @var DynamicTextCollection $dtc
	 */
	private $dtc;

	/**
	 * @param Eiu $eiu
	 * @param RocketState $rocketState
	 * @param Request $request
	 */
	public function prepare(Request $request) {
		$this->eiuCtrl = EiuCtrl::from($this->cu());
		$this->eiu = $this->eiuCtrl->eiu();
		$this->dtc = $this->eiu->dtc('report');
	}

	function index($newsletterId) {
		$newsletter = $this->eiuCtrl->lookupObject($newsletterId);

		$this->eiuCtrl->pushOverviewBreadcrumb()
			->pushDetailBreadcrumb($newsletter)
			->pushCurrentAsSirefBreadcrumb($this->dtc->t('script_cmd_run_newsletter_breadcrumb'));

		$this->eiuCtrl->forwardUrlIframeZone($this->getUrlToController(['src', $newsletterId]),
				$this->dtc->t('test_send_title'));
	}

	public function doSrc(MessageContainer $mc, NewsletterState $newsletterState, $newsletterIdRep) {
		$newsletter = $this->eiuCtrl->lookupEntry($newsletterIdRep)->getEntityObj();

		$testSendForm = new TestSendForm($newsletter);
		if ($this->dispatch($testSendForm, 'send')) {
			$this->refresh();
			return;
		}

		$view = $this->createView('view\testSend.html', array('testSendForm' => $testSendForm));
		$view->setDynamicTextCollection($newsletterState->getDtc());
		$this->forwardView($view);
	}
}
