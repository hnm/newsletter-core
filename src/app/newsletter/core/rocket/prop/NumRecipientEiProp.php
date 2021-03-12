<?php
namespace newsletter\core\rocket\prop;

use newsletter\core\model\NewsletterState;
use n2n\util\type\CastUtils;
use newsletter\core\model\NewsletterDao;
use rocket\impl\ei\component\prop\adapter\DisplayableEiPropAdapter;
use rocket\ei\util\Eiu;
use rocket\si\content\impl\meta\SiCrumb;
use rocket\si\content\impl\SiFields;
use rocket\ei\util\factory\EifGuiField;

class NumRecipientEiProp extends DisplayableEiPropAdapter {

	
	protected function prepare() {
	}
	
	protected function createOutEifGuiField(Eiu $eiu): EifGuiField {
		$newsletterState = $eiu->lookup(NewsletterState::class);
		CastUtils::assertTrue($newsletterState instanceof NewsletterState);
		
		$newsletterDao = $eiu->lookup(NewsletterDao::class);
		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);
		
		$num = $newsletterDao->getNumRecipientsForNewsletter($eiu->entry()->getEntityObj());
		return $eiu->factory()->newGuiField(SiFields::crumbOut(SiCrumb::createLabel(
				$newsletterState->getDtc()->t('num_recipients_receive_newsletter_txt', array('num' => $num)))));
	}
	
// 	/**
// 	 * {@inheritDoc}
// 	 * @see \rocket\impl\ei\component\prop\adapter\StatelessDisplayable::createUiComponent()
// 	 */
// 	public function createUiComponent(HtmlView $view, Eiu $eiu) {
// 		$newsletterState = $eiu->lookup(NewsletterState::class);
// 		CastUtils::assertTrue($newsletterState instanceof NewsletterState);
		
// 		$newsletterDao = $eiu->lookup(NewsletterDao::class);
// 		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);
		
// 		$num = $newsletterDao->getNumRecipientsForNewsletter($eiu->entry()->getEntityObj());
// 		return $newsletterState->getDtc()->t('num_recipients_receive_newsletter_txt', array('num' => $num));
// 	}
}