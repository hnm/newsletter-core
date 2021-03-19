<?php
namespace newsletter\core\rocket\command\model;

use n2n\context\RequestScoped;
use newsletter\core\model\NewsletterDao;
use newsletter\core\model\NewsletterState;
use newsletter\core\bo\Newsletter;
use newsletter\core\model\Template;
use n2n\web\http\Request;
use newsletter\core\bo\HistoryEntry;
use rocket\ei\util\EiuCtrl;

class StatsModel implements RequestScoped {
	
	private $newsletterDao;
	private $newsletterState;
	private $request;
	
	private $groupedNewsletterLinks;
	private $newsletter;
	private $eiuCtrl;
	
	private function _init(NewsletterDao $newsletterDao, NewsletterState $newsletterState, Request $request) {
		$this->newsletterDao = $newsletterDao;
		$this->newsletterState = $newsletterState;
		$this->request = $request;
	}
	
	public function setup(Newsletter $newsletter, EiuCtrl $eiuCtrl) {
		$dtc = $this->newsletterState->getDtc();
		$historyEntry = Template::buildDummyHistoryEntry();
		
		$this->groupedNewsletterLinks = array();
		foreach ($this->newsletterDao->getHistoryLinkStatsForNewsletter($newsletter) as $linkStat) {
			$label = $this->determineLabel(Template::finalizeTemplate($linkStat['link'], $historyEntry), 
					$historyEntry, $newsletter);
			
			if (isset($this->groupedNewsletterLinks[$label])) {
				$this->groupedNewsletterLinks[$label] += $linkStat['num'];
				continue;
			} 
				
			$this->groupedNewsletterLinks[$label] = $linkStat['num'];
		}
		
		$this->newsletter = $newsletter;
		$this->eiuCtrl = $eiuCtrl;
	}
	
	public function getGroupedNewsletterLinks() {
		return $this->groupedNewsletterLinks;
	}
	
	public function getNewsletterDao() {
		return $this->newsletterDao;
	}
	
	public function getNewsletter() {
		return $this->newsletter;
	}
	
//	public function buildDetailUrl() {
//		return $this->eiuCtrl->buildRedirectUrl($this->eiuCtrl->lookupEntry($this->newsletter->getId()));
//	}
	
	private function determineLabel(string $url, HistoryEntry $historyEntry, Newsletter $newsletter) {
		$dtc = $this->newsletterState->getDtc();
		$label = $url;
		
		if ($label === urldecode((string) $this->newsletterState->buildUnsubscriptionUrl($historyEntry->getEmail()))) {
			return $dtc->t('unsubscribe_txt');
		} 
		
		if ($label === urldecode((string) $this->newsletterState->buildWebTemplateUrl($newsletter, $historyEntry))) {
			return $dtc->t('web_view_txt');
		} 
		
		if ($label === (string) $this->request->getHostUrl()->extR($this->request->getContextPath())) {
			return $dtc->t('homepage_txt');
		}
		
		return $label;
	}
}
