<?php
namespace newsletter\core\rocket\command;

use n2n\web\http\controller\ControllerAdapter;
use n2n\l10n\MessageContainer;
use newsletter\core\model\NewsletterState;
use n2n\impl\web\ui\view\jhtml\JhtmlExec;
use n2n\web\http\controller\ParamQuery;
use rocket\ei\util\EiuCtrl;

class TestSendController extends ControllerAdapter {
	
	public function index(MessageContainer $mc, EiuCtrl$eiuCtrl, NewsletterState $newsletterState, 
			$newsletterIdRep, ParamQuery $refPath) {
		
		$newsletter = $eiuCtrl->lookupEntry($newsletterIdRep)->getEntityObj();
		$dtc = $newsletterState->getDtc();
		$eiuCtrl->applyCommonBreadcrumbs($newsletter, $dtc->t('test_send_txt'));
		
		$testSendForm = new TestSendForm($newsletter);
		if ($this->dispatch($testSendForm, 'send')) {
			$redirectUrl = $eiuCtrl->parseRefUrl($refPath);
			$eiuCtrl->redirect($redirectUrl, null, new JhtmlExec(true));
			return;
		}
		
		$view = $this->createView('view\testSend.html', array('testSendForm' => $testSendForm));
		$view->setDynamicTextCollection($newsletterState->getDtc());
		
		$this->forwardView($view);
	}
}