<?php
namespace newsletter\core\model;

use n2n\web\dispatch\Dispatchable;
use n2n\context\RequestScoped;
use n2n\impl\web\dispatch\map\val\ValEmail;
use n2n\impl\web\dispatch\map\val\ValEnum;
use newsletter\core\bo\Recipient;
use newsletter\core\bo\RecipientCategory;
use n2n\l10n\Message;
use n2n\l10n\N2nLocale;
use n2n\web\dispatch\map\bind\BindingDefinition;
use n2n\impl\web\dispatch\map\val\ValNotEmpty;
use n2n\l10n\DynamicTextCollection;
use n2n\web\dispatch\map\bind\MappingDefinition;
use n2n\util\type\ArgUtils;

class SubscriptionForm implements Dispatchable, RequestScoped {

	private $recipientCategories;
	private $recipient;
	private $categoryIdOptions = array();
	private $locale;
	private $dtc;
	private $fixedEmail;
	
	protected $categoryIds = array();
	protected $firstName;
	protected $lastName;
	protected $email;
	protected $gender;
	protected $saluteWith;

	public function __construct(DynamicTextCollection $dtc, N2nLocale $locale, array $recipientCategories = null) {
		ArgUtils::valArray($recipientCategories, RecipientCategory::class, true);
		$this->recipientCategories = $recipientCategories;
		$this->dtc = $dtc;
		$this->locale = $locale;
	}
	
	private function _mapping(MappingDefinition $md, NewsletterDao $newsletterDao) {
		$md->getMappingResult()->setLabels(array(
				'categoryIds' => $this->dtc->t('newsletter_form_recipient_category'),
				'email' => $this->dtc->t('newsletter_form_email'),
				'firstName' => $this->dtc->t('newsletter_subscription_form_first_name'),
				'lastName' => $this->dtc->t('newsletter_subscription_form_last_name'),
				'gender' => $this->dtc->t('newsletter_subscription_form_gender'),
				'saluteWith' => $this->dtc->t('newsletter_subscription_form_salute_with')
		));
		
		$recipientCategories = empty($this->recipientCategories) ? $newsletterDao->getRecipientCategories($this->locale) : $this->recipientCategories;
		foreach ($recipientCategories as $recipientCategory)  {
			$this->categoryIdOptions[$recipientCategory->getId()] = $recipientCategory->getName();
		}
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	public function getEmail() {
		if (null !== $this->fixedEmail) {
			return $this->fixedEmail;
		}
		
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getGender() {
		return $this->gender;
	}

	public function setGender($gender) {
		$this->gender = $gender;
	}

	public function getSaluteWith() {
		return $this->saluteWith;
	}

	public function setSaluteWith($saluteWith) {
		$this->saluteWith = $saluteWith;
	}
	
	public function getCategoryIds() {
		return $this->categoryIds;
	}

	public function setCategoryIds(array $categoryIds = null) {
		$this->categoryIds = $categoryIds;
	}

	public function getRecipient() {
		return $this->recipient;
	}
	
	public function hasCategories() {
		return count($this->categoryIdOptions) > 1;
	}
	
	public function isShowEmail() {
		return null === $this->fixedEmail;
	}
	
	public function setFixedEmail($fixedEmail) {
		$this->fixedEmail = $fixedEmail;
	}

	private function _validation(BindingDefinition $bc) {
		if ($this->isShowEmail()) {
			$bc->val('email', new ValNotEmpty(Message::create($this->dtc->t('newsletter_err_email_invalid'))));
			$bc->val('email', new ValEmail(Message::create($this->dtc->t('newsletter_err_email_invalid'))));
		}
		
		$bc->val('firstName', new ValNotEmpty(Message::create($this->dtc->t('newsletter_subscription_err_first_name'))));
		$bc->val('lastName', new ValNotEmpty(Message::create($this->dtc->t('newsletter_subscription_err_last_name'))));
		$bc->val('saluteWith', new ValNotEmpty(Message::create($this->dtc->t('newsletter_err_salute_with_invalid'))));
		$bc->val('saluteWith', new ValEnum(Recipient::getSalutations(), Message::create($this->dtc->t('newsletter_err_salute_with_invalid'))));
		$bc->val('gender', new ValNotEmpty(Message::create($this->dtc->t('newsletter_err_gender_invalid'))));
		$bc->val('gender', new ValEnum(Recipient::getGenders(), Message::create($this->dtc->t('newsletter_err_gender_invalid'))));
		
		if (count($this->categoryIdOptions) > 1) {
			$bc->val('categoryIds', new ValEnum(array_keys($this->getCategoryIdOptions()), true));
		}
	}

	public function subscribe(NewsletterDao $newsletterDao, DefaultMailModel $mailModel) {
		$categories = array();
		if (count($this->categoryIdOptions) > 0) {
			if (count($this->categoryIdOptions) === 1) {
				$categories[] = $newsletterDao->getRecipientCategoryById(key($this->categoryIdOptions));
			} else {
				foreach ($this->categoryIds as $catgoryId) {
					$categories[] = $newsletterDao->getRecipientCategoryById($catgoryId);
				}
			}
		}
		
		$this->recipient = $newsletterDao->getOrCreateRecipient($this->getFirstName(), 
				$this->getLastname(), $this->getEmail(), $this->getGender(), $this->getSaluteWith(), 
				$this->locale, $categories);
		
		if (null === $this->fixedEmail) {
			$mailModel->sendActivationMail($this->recipient);
		}
	}
	
	public function getCategoryIdOptions() {
		return $this->categoryIdOptions;
	}
	
	public function getGenderOptions() {
		$genderOptions = array();
		foreach (Recipient::getGenders() as $gender) {
			$genderOptions[$gender] = $this->dtc->translate('newsletter_gender_' . $gender);
		}
		return $genderOptions;
	}
	
	public function getSalutationOptions() {
		$salutationOptions = array();
		foreach (Recipient::getSalutations() as $salutation) {
			$salutationOptions[$salutation] = $this->dtc->translate('newsletter_salutation_' . $salutation);
		}
		return $salutationOptions;
	}
}