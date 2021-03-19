<?php
namespace newsletter\core\rocket\command;

use n2n\l10n\DynamicTextCollection;
use n2n\web\http\controller\ControllerAdapter;
use n2n\l10n\MessageContainer;
use n2n\web\http\Request;
use newsletter\core\model\NewsletterState;
use newsletter\core\model\NewsletterDao;
use n2n\web\http\PageNotFoundException;
use n2n\util\type\CastUtils;
use newsletter\core\bo\Newsletter;
use rocket\core\model\RocketState;
use rocket\ei\util\Eiu;
use rocket\ei\util\EiuCtrl;

class SendController extends ControllerAdapter {
	/**
	 * @var EiuCtrl
	 */
	private $eiuCtrl;
	/**§
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

	function index($newsletterId, NewsletterDao $newsletterDao, NewsletterState $newsletterState, MessageContainer $mc) {
//		$newsletter = $this->eiuCtrl->lookupObject($newsletterId);

//		$this->eiuCtrl->pushOverviewBreadcrumb()
//			->pushDetailBreadcrumb($newsletter)
//			->pushCurrentAsSirefBreadcrumb($this->dtc->t('script_cmd_run_newsletter_breadcrumb'));

		$entry = $this->eiuCtrl->lookupObject($newsletterId);
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

		$this->refresh();
//		$this->eiuCtrl->forwardUrlIframeZone($this->getUrlToController(['src', $newsletterId]),
//			$this->dtc->t('test_send_title'));
	}

	public function doSrc($newsletterIdRep, NewsletterState $newsletterState,
			NewsletterDao $newsletterDao, MessageContainer $mc) {

	}
}
