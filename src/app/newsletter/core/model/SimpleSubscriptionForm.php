<?php
namespace newsletter\core\model;

use n2n\web\dispatch\Dispatchable;
use n2n\impl\web\dispatch\map\val\ValNotEmpty;
use n2n\web\http\Request;
use newsletter\core\bo\RecipientCategory;
use n2n\web\dispatch\map\bind\BindingDefinition;
use n2n\l10n\Message;
use n2n\impl\web\dispatch\map\val\ValEmail;
use n2n\l10n\DynamicTextCollection;
use n2n\web\dispatch\map\bind\MappingDefinition;
use n2n\util\type\ArgUtils;

class SimpleSubscriptionForm implements Dispatchable {
	
	private $dtc;
	private $recipientCategories;
	
	public $email;
	
	public function __construct(DynamicTextCollection $dtc, array $recipientCategories = null) {
		ArgUtils::valArray($recipientCategories, RecipientCategory::class, true);
		$this->recipientCategories = $recipientCategories;
		$this->dtc = $dtc;
	}
	
	private function _mapping(MappingDefinition $md) {
		$md->getMappingResult()->setLabel('email', $this->dtc->t('newsletter_form_email'));
	}
	
	private function _validation(BindingDefinition $bd, Request $request) {
		$bd->val('email', new ValNotEmpty(Message::create($this->dtc->translate('newsletter_err_email_invalid'))));
		$bd->val('email', new ValEmail(Message::create($this->dtc->translate('newsletter_err_email_invalid'))));
	}
	
	public function subscribe(NewsletterDao $newsletterDao, Request $request, DefaultMailModel $mailModel) {
		$categories = array();
		if (null !== $this->recipientCategories) {
			foreach ($this->recipientCategories as $recipientCategory) {
				$categories[] = $newsletterDao->getRecipientCategoryById($recipientCategory->getId());
			}
		}
		
		$recipient = $newsletterDao->getOrCreateRecipientForEmailAndLocale($this->email, 
				$request->getN2nLocale(), $categories);
		$mailModel->sendActivationMail($recipient, true);
	}
}