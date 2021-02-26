<?php
namespace newsletter\core\rocket;

use n2n\util\type\CastUtils;
use newsletter\core\bo\Newsletter;
use newsletter\core\model\NewsletterState;
use rocket\ei\manage\preview\controller\PreviewControllerAdapter;

class NewsletterPreviewController extends PreviewControllerAdapter {
	
	public function index(NewsletterState $newsletterState, array $params = null) {
		$newsletter = $this->eiu()->object()->getEntityObj();
		CastUtils::assertTrue($newsletter instanceof Newsletter);
	
		$this->redirect($newsletterState->getTemplateUrl()->extR(array($newsletter->getId())));
	}
	
}