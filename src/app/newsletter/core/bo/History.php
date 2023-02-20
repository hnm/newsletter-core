<?php
namespace newsletter\core\bo;

use n2n\persistence\orm\CascadeType;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoTable;
use n2n\persistence\orm\annotation\AnnoTransient;
use n2n\util\uri\Url;
use n2n\util\ex\IllegalStateException;
use newsletter\core\model\NewsletterState;
use newsletter\core\model\NewsletterDao;
use newsletter\core\controller\TemplateController;
use newsletter\core\model\Template;
use newsletter\core\bo\Newsletter;
use newsletter\core\bo\HistoryEntry;
use newsletter\core\bo\HistoryLink;

class History extends ObjectAdapter {
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoTable('newsletter_history'));
		$ai->p('tmpNewsletterHtml', new AnnoTransient());
		$ai->p('newsletter', new AnnoManyToOne(Newsletter::getClass()));
		$ai->p('historyEntries', new AnnoOneToMany(HistoryEntry::getClass(), 'history', CascadeType::ALL));
		$ai->p('historyLinks', new AnnoOneToMany(HistoryLink::getClass(), 'history', CascadeType::ALL, null, true));
	}

	const HTML_ATTR_NAME_CI = 'data-ci';

	private $tmpNewsletterHtml;
	private $id;
	private $newsletter;
	private $preparedDate;
	private $historyEntries;
	private $newsletterHtml;
	private $newsletterText;
	private $historyLinks;

	private function _postLoad() {
		$this->tmpNewsletterHtml = $this->newsletterHtml;
	}

	private function _prePersist(NewsletterState $newsletterState, NewsletterDao $newsletterDao) {
		//$this->checkNewsletterHtml($newsletterState, $newsletterDao);
	}

	private function _preUpdate(NewsletterState $newsletterState, NewsletterDao $newsletterDao) {
		//$this->checkNewsletterHtml($newsletterState, $newsletterDao);
	}

	public function checkNewsletterHtml(NewsletterState $newsletterState, NewsletterDao $newsletterDao) {
		if ($this->tmpNewsletterHtml === $this->newsletterHtml) return;
		
		$this->historyLinks = new \ArrayObject();
		$dom = new \DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($this->newsletterHtml);
		libxml_clear_errors();
		
		$history = $this;
		
		foreach ($dom->getElementsByTagName('a') as $link) {
			if (!$link instanceof \DOMElement) continue;
			if (!$link->hasAttribute('href')) return;
			
			$url = Url::create($link->getAttribute('href'));
			if ($url->isRelative()) {
				throw new IllegalStateException();
			}
			
			$historyLink = new HistoryLink();
			$historyLink->setLink((string) $url);
			if ($link->hasAttribute(self::HTML_ATTR_NAME_CI)) {
				if (null !== ($newsletterCi = $newsletterDao->getNewsletterCiById(
						$link->getAttribute(self::HTML_ATTR_NAME_CI)))) {
					$historyLink->setNewsletterCi($newsletterCi);
				}
				
				$link->removeAttribute(self::HTML_ATTR_NAME_CI);
			}
			$newsletterDao->persistHistoryLink($historyLink);
			$historyLink->setHistory($history);
			
			$link->setAttribute('href', (string) $newsletterState->getTemplateUrl()
					->extR(array(TemplateController::ACTION_LINK, $historyLink->getId()), array('c' => Template::PLACEHOLDER_HISTORY_ENTRY_CODE)));
			$this->historyLinks->append($historyLink);
		}
		
		$this->newsletterHtml = $dom->saveHTML();
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return Newsletter
	 */
	public function getNewsletter() {
		return $this->newsletter;
	}

	/**
	 * @param Newsletter $newsletter
	 */
	public function setNewsletter(Newsletter $newsletter) {
		$this->newsletter = $newsletter;
	}

	/**
	 * @return \DateTime
	 */
	public function getPreparedDate() {
		return $this->preparedDate;
	}

	/**
	 * @param \DateTime $preparedDate
	 */
	public function setPreparedDate(\DateTime $preparedDate = null) {
		$this->preparedDate = $preparedDate;
	}

	/**
	 * @return HistoryEntry []
	 */
	public function getHistoryEntries() {
		return $this->historyEntries;
	}

	public function setHistoryEntries($historyEntries) {
		$this->historyEntries = $historyEntries;
	}

	public function getNewsletterHtml() {
		return $this->newsletterHtml;
	}

	public function setNewsletterHtml($newsletterHtml) {
		$this->newsletterHtml = $newsletterHtml;
	}

	public function getNewsletterText() {
		return $this->newsletterText;
	}

	public function setNewsletterText($newsletterText) {
		$this->newsletterText = $newsletterText;
	}

	/**
	 * @return HistoryLink []
	 */
	public function getHistoryLinks() {
		return $this->historyLinks;
	}

	public function setHistoryLinks($historyLinks) {
		$this->historyLinks = $historyLinks;
	}
}