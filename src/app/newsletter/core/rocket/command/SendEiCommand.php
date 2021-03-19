<?php
namespace newsletter\core\rocket\command;

use n2n\l10n\Message;
use n2n\util\type\CastUtils;
use n2n\util\uri\Path;
use n2n\web\http\controller\Controller;
use n2n\web\http\PageNotFoundException;
use newsletter\core\bo\Newsletter;
use newsletter\core\model\NewsletterDao;
use newsletter\core\model\NewsletterState;
use rocket\ei\util\Eiu;
use rocket\impl\ei\component\command\adapter\IndependentEiCommandAdapter;
use rocket\si\control\SiButton;
use rocket\si\control\SiConfirm;
use rocket\si\control\SiIconType;

class SendEiCommand extends IndependentEiCommandAdapter {
	const CONTROL_KEY = 'send';

	protected function prepare() {
	}


	function createEntryGuiControls(Eiu $eiu): array {
		$eiuEntry = $eiu->entry();

		if ($eiuEntry->isNew()) {
			return array();
		}

 		$newsletterDao = $eiu->lookup(NewsletterDao::class);
 		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);

 		$newsletter = $eiu->entry()->getEntityObj();
 		CastUtils::assertTrue($newsletter instanceof Newsletter);
 		$numRecipients = $newsletterDao->getNumRecipientsForNewsletter($newsletter);
 		if ($numRecipients == 0) {
 			return [];
 		}

 		$newsletterState = $eiu->lookup(NewsletterState::class);
 		CastUtils::assertTrue($newsletterState instanceof NewsletterState);

		$dtc = $newsletterState->getDtc();
 		$dtc->assignModule('rocket');

 		$siButton = SiButton::secondary($dtc->t('send_txt'))
				->setIconType(SiIconType::ICON_ENVELOPE_OPEN)
				->setImportant(true)
				->setConfirm(new SiConfirm($dtc->t('send_confirm_msg',
						['subject' => $newsletter->getSubject(), 'num_recipients' => $numRecipients]),
						$dtc->t('common_yes_label'),
						$dtc->t('common_no_label')));

		$eiuControlFactory = $eiu->factory()->controls();
		return [$eiuControlFactory->newCallback(self::CONTROL_KEY, $siButton, function(Eiu $eiu) use ($numRecipients) {
			return $this->send($eiu, $numRecipients);
		})];
	}

	private function send(Eiu $eiu, int $numRecipients) {
		$entry = $eiu->object();
		$newsletter = $entry->getEntityObj();
		CastUtils::assertTrue($newsletter instanceof Newsletter);
		$newsletterDao = $eiu->lookup(NewsletterDao::class);
// 		$numRecipients = $newsletterDao->getNumRecipientsForNewsletter($newsletter);
// 		if ($numRecipients == 0) {
// 			throw new PageNotFoundException('No recipients for newsletter "' . $newsletter->getSubject()
// 					. '" available.');
// 		}
		$newsletterDao->createHistoryForNewsletter($newsletter);
		$newsletterState = $eiu->lookup(NewsletterState::class);
		$dtc = $newsletterState->getDtc();

		$message = Message::create(
				$dtc->t('send_success',
						array('numRecipients' => $numRecipients, 'subject' => $newsletter->getSubject())),
			Message::SEVERITY_INFO);
		return $eiu->factory()->newControlResponse()->message($message);
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\command\EiCommand::lookupController()
	 */
	public function lookupController(Eiu $eiu): Controller {
		return $eiu->lookup(SendController::class);
	}
}
