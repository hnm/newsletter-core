<?php
namespace newsletter\core\rocket\prop;

use n2n\impl\web\ui\view\html\HtmlView;
use newsletter\core\model\NewsletterState;
use n2n\util\type\CastUtils;
use newsletter\core\model\NewsletterDao;
use rocket\impl\ei\component\prop\adapter\DisplayableEiPropAdapter;
use rocket\ei\util\Eiu;

class NumRecipientEiProp extends DisplayableEiPropAdapter {

	/**
	 * {@inheritDoc}
	 * @see \rocket\impl\ei\component\prop\adapter\StatelessDisplayable::createUiComponent()
	 */
	public function createUiComponent(HtmlView $view, Eiu $eiu) {
		$newsletterState = $eiu->lookup(NewsletterState::class);
		CastUtils::assertTrue($newsletterState instanceof NewsletterState);
		
		$newsletterDao = $eiu->lookup(NewsletterDao::class);
		CastUtils::assertTrue($newsletterDao instanceof NewsletterDao);
		
		$num = $newsletterDao->getNumRecipientsForNewsletter($eiu->entry()->getEntityObj());
		return $newsletterState->getDtc()->t('num_recipients_receive_newsletter_txt', array('num' => $num));
	}
}