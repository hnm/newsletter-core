<?php
namespace newsletter\core\rocket\command;

use n2n\web\http\controller\ControllerAdapter;
use n2n\l10n\MessageContainer;
use newsletter\core\model\NewsletterState;
use newsletter\core\model\NewsletterDao;
use n2n\web\http\PageNotFoundException;
use n2n\util\type\CastUtils;
use newsletter\core\bo\Newsletter;
use rocket\ei\util\EiuCtrl;

class SendController extends ControllerAdapter {
	
	public function index(MessageContainer $mc, EiuCtrl $eiuCtrl, NewsletterState $newsletterState, 
			NewsletterDao $newsletterDao, $newsletterIdRep) {
		$entry = $eiuCtrl->lookupEntry($newsletterIdRep);
		$newsletter = $entry->getEntityObj();
		
		CastUtils::assertTrue($newsletter instanceof Newsletter);
		$numRecipients = $newsletterDao->getNumRecipientsForNewsletter($newsletter);
		if ($numRecipients == 0) {
			throw new PageNotFoundException('No recipients for newsletter "' . $newsletter->getSubject() . '" available.');
		}
		
		$newsletterDao->createHistoryForNewsletter($newsletter);
		$dtc = $newsletterState->getDtc();
		$mc->addInfo($dtc->t('send_success', array('numRecipients' => $numRecipients, 
				'subject' => $newsletter->getSubject())));
		$this->redirect($eiuCtrl->buildRedirectUrl($entry));
	}
}