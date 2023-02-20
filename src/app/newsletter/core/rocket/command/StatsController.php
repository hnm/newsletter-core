<?php
namespace newsletter\core\rocket\command;

use n2n\web\http\controller\ControllerAdapter;
use n2n\l10n\MessageContainer;
use newsletter\core\model\NewsletterState;
use rocket\core\model\Breadcrumb;
use newsletter\core\bo\HistoryEntry;
use n2n\web\http\PageNotFoundException;
use newsletter\core\rocket\command\model\StatsModel;
use newsletter\core\rocket\NewsletterManageDao;
use rocket\ei\util\EiuCtrl;

class StatsController extends ControllerAdapter {
	
	
	const ACTION_REMOVE_FAILED_HISTORY_ENTRIES = 'rfhe';
	const ACTION_DELETE_RECIPIENTS = 'dr';
	const ACTION_RESET_IN_PROGRESS_HISTORY_ENTRIES = 'riphe';
	
	private $eiuCtrl;
	private $newsletter;
	private $dtc;
	private $newsletterState;
	private $mc;
	
	public function prepare(EiuCtrl $eiuCtrl, NewsletterState $newsletterState, MessageContainer $mc) {
		$this->eiuCtrl = $eiuCtrl;
		$this->newsletterState = $newsletterState;
		$this->mc = $mc;
		$this->dtc = $this->newsletterState->getDtc();
		$this->dtc->assignModule('rocket');
	}
	
	public function index(StatsModel $statsModel, $newsletterIdRep) {
		$this->setup($newsletterIdRep);
		$this->eiuCtrl->applyCommonBreadcrumbs($this->newsletter, $this->dtc->t('stats_txt'));
		
		$statsModel->setup($this->newsletter, $this->eiuCtrl);
		
		$view = $this->createView('view\stats.html', array('statsModel' => $statsModel));
		$view->setDynamicTextCollection($this->dtc);
		
		$this->forwardView($view);
	}
	
	public function doDetail($newsletterIdRep, $status) {
		$this->setup($newsletterIdRep, array(new Breadcrumb($this->getUrlToController($newsletterIdRep), $this->dtc->t('stats_txt')), 
				new Breadcrumb(null, $status)));
		
		if (!in_array($status, HistoryEntry::getPossibleStatus())) {
			throw new PageNotFoundException();
		}
		
		$view = $this->createView('view\statsDetail.html', array('newsletter' => $this->newsletter,
				'status' => $status, 'eiuFrame' => $this->eiuCtrl->frame()));
		$view->setDynamicTextCollection($this->dtc);
		
		$this->forwardView($view);
	}
	
	public function doRfhe(NewsletterManageDao $manageDao, $newsletterIdRep) {
		$this->setup($newsletterIdRep);
		$manageDao->removeFailedHistoryEntries($this->newsletter);
		$this->mc->addInfo($this->dtc->t('remove_failed_history_entries_info'));
		$this->redirectToController($newsletterIdRep);
	}
	
	public function doRiphe(NewsletterManageDao $manageDao, $newsletterIdRep) {
		$this->setup($newsletterIdRep);
		$manageDao->resetHistoryEntriesInProgress($this->newsletter);
		$this->mc->addInfo($this->dtc->t('reset_history_entries_info'));
		
		$this->redirectToController($newsletterIdRep);
	}
	
	public function doDr(NewsletterManageDao $manageDao, $newsletterIdRep) {
		$this->setup($newsletterIdRep);
		$manageDao->deleteRecipientsForFailedHistoryEntries($this->newsletter);
		$this->mc->addInfo($this->dtc->t('delete_recipients_info'));
		$this->redirectToController($newsletterIdRep);
	}
	
	private function setup(string $newsletterIdRep, array $breadcrumbs = null) {
		$this->newsletter = $this->eiuCtrl->lookupEntry($newsletterIdRep)->getEntityObj();
		
		if (null !== $breadcrumbs) {
			$this->eiuCtrl->applyCommonBreadcrumbs($this->newsletter);
			$this->eiuCtrl->applyBreadcrumbs(...$breadcrumbs);
		}
	}
}