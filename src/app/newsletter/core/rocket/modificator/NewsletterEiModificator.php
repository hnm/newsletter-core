<?php
namespace newsletter\core\rocket\modificator;

use n2n\util\type\CastUtils;
use newsletter\core\model\NewsletterState;
use rocket\impl\ei\component\modificator\adapter\IndependentEiModificatorAdapter;
use rocket\ei\util\Eiu;

class NewsletterEiModificator extends IndependentEiModificatorAdapter {
	public function setupEiEntry(Eiu $eiu) {
		if (!$eiu->entry()->isNew()) return;

		$newsletterState = $eiu->lookup(NewsletterState::class);
		CastUtils::assertTrue($newsletterState instanceof NewsletterState);
		
		$eiu->entry()->setValue('previewText', $newsletterState->getDtc()->t('newsletter_preview_text_default'));
	}
}