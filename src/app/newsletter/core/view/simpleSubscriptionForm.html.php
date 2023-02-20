<?php 
	use newsletter\core\model\SimpleSubscriptionForm;
	use n2n\impl\web\ui\view\html\HtmlView;
	use newsletter\core\model\NewsletterState;
	
	$view = HtmlView::view($this);
	$html = HtmlView::html($view);
	$formHtml = HtmlView::formHtml($view);
	$ariaFormHtml = HtmlView::ariaFormHtml($view);
	
	$simpleSubscriptionForm = $view->getParam('simpleSubscriptionForm'); 
	$view->assert($simpleSubscriptionForm instanceof SimpleSubscriptionForm);
	
	$newsletterState = $view->lookup('newsletter\core\model\NewsletterState');
	$view->assert($newsletterState instanceof NewsletterState);
	
	$view->useTemplate($view->getParam('templateViewId'), 
			array('title' => $view->getL10nText('newsletter_subscription_title')));
?>

<div class="newsletter-box">
	<p><?php $html->text('newsletter_subscription_text') ?></p>
	
	<?php $formHtml->open($simpleSubscriptionForm, null, null, array('class' => 'newsletter-simple-subscription-form')) ?>
		<?php $ariaFormHtml->label('email', true) ?>
		<?php $ariaFormHtml->input('email', true) ?> 
		<?php $ariaFormHtml->message('email') ?>
		 
		<?php $formHtml->buttonSubmit('subscribe', $view->getL10nText('newsletter_subscription_form_subscribe'))?>
	<?php $formHtml->close() ?>
	
	<br /><?php $html->link($newsletterState->buildUnsubscriptionUrl($simpleSubscriptionForm->email), 
			$view->getL10nText('newsletter_unsubscription_title')) ?>
</div>
