<?php 
	use newsletter\core\model\SubscriptionForm;
	use n2n\impl\web\ui\view\html\HtmlView;
	use newsletter\core\model\NewsletterState;
	
	$view = HtmlView::view($this);
	$html = HtmlView::html($view);
	$formHtml = HtmlView::formHtml($view);
	$ariaFormHtml = HtmlView::ariaFormHtml($view);
	
	$subscriptionForm = $view->getParam('subscriptionForm'); 
	$view->assert($subscriptionForm instanceof SubscriptionForm);
	
	$newsletterState = $view->lookup('newsletter\core\model\NewsletterState');
	$view->assert($newsletterState instanceof NewsletterState);
	
	$message = $view->getParam('message', false);
	$inActivation = $view->getParam('inActivation', false, false);
	
	$view->useTemplate($view->getParam('templateViewId', 
			array('title' => $view->getL10nText(($inActivation) ? 'newsletter_activation_title' : 'newsletter_subscription_title'))));
?>

<div class="newsletter-box">
	<p>
		<?php if ($subscriptionForm->isShowEmail()): ?>
			<?php $html->text('newsletter_subscription_text') ?>
		<?php else: ?>
			<?php $html->out($message) ?>
			<?php $html->text('newsletter_subscription_completion_text') ?>
		<?php endif ?>
	</p>
	
	<?php $formHtml->open($subscriptionForm, null, null, array('class' => 'newsletter-subscription-form')) ?>
		<?php if (!$subscriptionForm->hasCategories()) : ?>
			<?php $ariaFormHtml->label('categoryIds') ?>
			<?php $ariaFormHtml->getSelect('categoryIds', $subscriptionForm->getCategoryIdOptions(), false, null, true) ?>
			<?php $ariaFormHtml->message('categoryIds') ?>
		<?php endif ?>
		<?php if ($subscriptionForm->isShowEmail()): ?>
			<?php $ariaFormHtml->label('email') ?>
			<?php $ariaFormHtml->input('email') ?>
			<?php $ariaFormHtml->message('email') ?>
		<?php endif ?>
		
		<?php $ariaFormHtml->label('firstName', true) ?>
		<?php $ariaFormHtml->input('firstName', true) ?>
		<?php $ariaFormHtml->message('firstName') ?>
		
		<?php $ariaFormHtml->label('lastName', true) ?>
		<?php $ariaFormHtml->input('lastName', true) ?>
		<?php $ariaFormHtml->message('lastName') ?>
		
		<?php $ariaFormHtml->label('gender', true) ?>
		<?php $ariaFormHtml->select('gender', $subscriptionForm->getGenderOptions(), true) ?>
		<?php $ariaFormHtml->message('gender') ?>
		
		<?php $ariaFormHtml->label('saluteWith', true) ?>
		<?php $ariaFormHtml->select('saluteWith', $subscriptionForm->getSalutationOptions(), true) ?>
		<?php $ariaFormHtml->message('saluteWith') ?>
		
		<?php $formHtml->buttonSubmit('subscribe', $view->getL10nText($subscriptionForm->isShowEmail()
				? 'newsletter_subscription_form_subscribe' : 'newsletter_subscription_form_subscribe_complete')) ?>
	<?php $formHtml->close() ?>
	
	<?php $html->link($newsletterState->buildUnsubscriptionUrl($subscriptionForm->getEmail()), 
			$view->getL10nText('newsletter_unsubscription_title')) ?>
</div>
