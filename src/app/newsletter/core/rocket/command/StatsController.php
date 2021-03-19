<?php
namespace newsletter\core\rocket\command;

use gallery\core\model\Breadcrumb;
use n2n\l10n\DynamicTextCollection;
use n2n\web\http\controller\ControllerAdapter;
use n2n\l10n\MessageContainer;
use n2n\web\http\Request;
use newsletter\core\bo\Newsletter;
use newsletter\core\model\NewsletterState;
use newsletter\core\bo\HistoryEntry;
use n2n\web\http\PageNotFoundException;
use newsletter\core\rocket\command\model\StatsModel;
use newsletter\core\rocket\NewsletterManageDao;
use rocket\core\model\RocketState;
use rocket\ei\util\Eiu;
use rocket\ei\util\EiuCtrl;

class StatsController extends ControllerAdapter {
	
	const ACTION_REMOVE_FAILED_HISTORY_ENTRIES = 'rfhe';
	const ACTION_DELETE_RECIPIENTS = 'dr';
	const ACTION_RESET_IN_PROGRESS_HISTORY_ENTRIES = 'riphe';

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
	public function prepare(NewsletterState $newsletterState, Request $request) {
		$this->eiuCtrl = EiuCtrl::from($this->cu());
		$this->eiu = $this->eiuCtrl->eiu();
		$this->dtc = $newsletterState->getDtc();
		$this->dtc->assignModule('rocket');
	}

	public function index($newsletterIdRep, StatsSrcController $srcController) {
		$newsletterEiuObj = $this->eiuCtrl->lookupObject($newsletterIdRep);

		$this->eiuCtrl->pushOverviewBreadcrumb()
				->pushDetailBreadcrumb($newsletterEiuObj)
				->pushCurrentAsSirefBreadcrumb($this->dtc->t('stats_txt'));

		$this->eiuCtrl->forwardUrlIframeZone($this->getUrlToController(['src', $newsletterIdRep]),
				$this->dtc->t('newsletter_stats_txt'));
	}

	function doSrc(StatsSrcController $srcController, array $params = null) {
		$this->delegate($srcController);
	}
}
