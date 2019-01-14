<?php
namespace newsletter\core\controller;

use n2n\web\http\PageNotFoundException;
use n2n\web\http\controller\ControllerAdapter;
use newsletter\core\model\NewsletterDao;
use newsletter\core\bo\HistoryEntry;
use newsletter\core\model\Template;
use n2n\web\http\controller\ParamGet;
use n2n\core\config\GeneralConfig;
use newsletter\core\model\NewsletterState;
use newsletter\core\bo\Recipient;

class TemplateController extends ControllerAdapter {
	
	const ACTION_LOGO = 'logo';
	const ACTION_LINK = 'link';
	
	private $newsletterDao;
	private $generalConfig;
	private $newsletterState;
	
	private function _init(NewsletterDao $newsletterDao, GeneralConfig $generalConfig, 
			NewsletterState $newsletterState) {
		$this->newsletterDao = $newsletterDao;
		$this->generalConfig = $generalConfig;
		$this->newsletterState = $newsletterState;
	}
	
	public function index(Template $template, $newsletterId, $historyEntryCode = null) {
		$this->beginTransaction();
		// fix for links without id
		if ($newsletterId == self::ACTION_LINK && $_GET['c']) {
			if (null === ($historyEntry = $this->newsletterDao->getHistoryEntryByCode((string) $_GET['c']))) {
				$this->commit();
				throw new PageNotFoundException();
			}
			$newsletterId = $historyEntry->getHistory()->getNewsletter()->getId();
		}
		$newsletter = $this->checkNewsLetter($newsletterId);
		$historyEntry = $this->newsletterDao->getHistoryEntryByCode($historyEntryCode);
		if (null !== $historyEntry) {
			$this->newsletterDao->updateHistoryEntryStatus($historyEntry, HistoryEntry::STATUS_READ);
		} else {
			$historyEntry = new HistoryEntry();
			$historyEntry->setSalutation((new Recipient())->buildSalutation($this->newsletterState->getDtc()));
		}
		$this->commit();
		
		$template->setup($newsletter, $this->newsletterState->getDtc(), $this->newsletterState->getTemplateConfig());
		$this->forwardView($template->getHtmlView($historyEntry));
	}

	public function doLogo($newsletterId, $historyEntryCode = null) {
		$fileLogo = $this->newsletterState->getTemplateConfig()->getFileLogo();
		if (null === $fileLogo) {
			throw new PageNotFoundException('No logo given');
		}
		
		if (null !== $historyEntryCode && $historyEntryCode !== Template::PLACEHOLDER_HISTORY_ENTRY_CODE
				&& null !== ($historyEntry = $this->newsletterDao->getHistoryEntryByCode($historyEntryCode))) {
			$this->beginTransaction();
			$this->newsletterDao->updateHistoryEntryStatus($historyEntry, HistoryEntry::STATUS_READ);
			$this->commit();
		}
		
		$this->sendFile($fileLogo);
	}

	public function doLink($linkId, ParamGet $c = null) {
		$this->beginTransaction();
		$historyLink = $this->newsletterDao->getHistoryLinkById($linkId);
		if (null === $historyLink) {
			$this->commit();
			throw new PageNotFoundException();
		}

		if (null !== $c) {
			if (null === ($historyEntry = $this->newsletterDao->getHistoryEntryByCode((string) $c))) {
				$this->commit();
				throw new PageNotFoundException();
			}
			
			$this->newsletterDao->updateHistoryEntryStatus($historyEntry, HistoryEntry::STATUS_READ);
			$this->newsletterDao->createHistoryLinkClick($historyEntry, $historyLink);
		} else {
			$historyEntry = Template::buildDummyHistoryEntry();
		}
		
		$this->commit();
		
		//append Google Analytics param
		$this->redirect($historyLink->getUrl($historyEntry)->queryExt(array('utm_source' => 'newsletter',
				'utm_medium' => 'email',
				'utm_campaign' => $historyLink->getHistory()->getNewsletter()->getSubject())));
	}
	
	private function checkNewsLetter($newsletterId) {
		if (null !== ($newsletter = $this->newsletterDao->getNewsletterById($newsletterId))) {
			return $newsletter;
		}
		
		throw new PageNotFoundException('Newsletter not found: ' . $newsletterId);
	}
}