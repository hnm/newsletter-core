<?php
namespace newsletter\core\rocket\command;

use n2n\web\dispatch\Dispatchable;
use newsletter\core\bo\Newsletter;
use n2n\impl\web\dispatch\map\val\ValEmail;
use newsletter\core\bo\Recipient;
use newsletter\core\model\mail\MailManager;
use newsletter\core\model\mail\NewsletterMail;
use newsletter\core\model\NewsletterDao;
use n2n\l10n\MessageContainer;
use n2n\l10n\Message;
use n2n\web\dispatch\map\bind\BindingDefinition;
use newsletter\core\model\NewsletterState;
use newsletter\core\bo\HistoryEntry;
use newsletter\core\bo\History;
use newsletter\core\model\Template;

class TestSendForm implements Dispatchable {
	/**
	 * @var \newsletter\core\bo\Newsletter
	 */
	private $newsletter;
	
	public $firstName;
	public $lastName;
	public $email;
	public $gender;
	public $saluteWith;
	
	public function __construct(Newsletter $newsletter) {
		$this->newsletter = $newsletter;
	}
	
	public function getNewsletter() {
		return $this->newsletter;
	}
	
	
	private function _validation(BindingDefinition $bd) {
		$bd->val('email', new ValEmail());
// 		$bd->val('saluteWith', new ValEnum(Recipient::getSalutations(), false));
// 		$bd->val('gender', new ValEnum(Recipient::getGenders(), false));
	}
	
	public function send(MailManager $mailManager, NewsletterDao $newsletterDao, 
			MessageContainer $mc, NewsletterState $newsletterState, Template $template) {
		$recipient = new Recipient();
		$recipient->setFirstName($this->firstName);
		$recipient->setLastName($this->lastName);
		$recipient->setEmail($this->email);
		$recipient->setGender($this->gender);
		$recipient->setSaluteWith($this->saluteWith);
		$recipient->setN2nLocale($this->newsletter->getN2nLocale());
		
		$historyEntry = new HistoryEntry();
		$historyEntry->setEmail($recipient->getEmail());
		$historyEntry->setSalutation($recipient->buildSalutation($newsletterState->getDtc()));
		
		$template->setup($this->newsletter);
		
		$history = new History();
		$history->setNewsletter($this->newsletter);
		$history->setNewsletterHtml($template->getHtml());
		$history->setNewsletterText($template->getText());
		
		$historyEntry->setHistory($history);
		
		$newsletterMail = new NewsletterMail($newsletterState->getSenderEmail(),
				$historyEntry, $this->newsletter->getSubject());
		$newsletterMail->setSenderName($newsletterState->getSenderName());

		$newsletterMail->setReplyToEmail($newsletterState->getReplyToEmail());
		$newsletterMail->setReplyToName($newsletterState->getReplyToName());
		$dtc = $newsletterState->getDtc();
		
		try {
			$mailManager->send($newsletterMail);
			$mc->add(Message::create($dtc->translate('test_send_success', 
					array('email' => $this->email)), Message::SEVERITY_INFO));
		} catch (\Exception $e){
			$mc->add(Message::create($dtc->translate('test_send_error', 
					array('email' => $this->email, 'message' => $e->getMessage())), Message::SEVERITY_ERROR));
			return false;
		}
	}

	
}
