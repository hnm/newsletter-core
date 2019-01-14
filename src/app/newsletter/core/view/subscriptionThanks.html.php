<?php 
	use newsletter\core\bo\Recipient;
	use n2n\impl\web\ui\view\html\HtmlView;
	use newsletter\core\model\NewsletterState;
	
	$view = HtmlView::view($this);
	$html = HtmlView::html($view);
	
	$recipient = $view->getParam('recipient');
	$view->assert($recipient instanceof Recipient);
	
	$newsletterState = $view->lookup('newsletter\core\model\NewsletterState');
	$view->assert($newsletterState instanceof NewsletterState);
	
	$view->useTemplate($view->getParam('templateViewId'), 
			array('title' => $view->getL10nText('newsletter_subscription_title')));
?>
<div class="newsletter-box">
	<p><?php $html->text('newsletter_subscription_thanks_' . $recipient->getStatus()) ?></p>
	<?php if ($recipient->getStatus() === Recipient::STATUS_ACTIVE) : ?>
		<?php $html->link($newsletterState->buildUnsubscriptionUrl($recipient->getEmail()), 
				$view->getL10nText('newsletter_unsubscription_title'))?>
	<?php endif ?>
</div>