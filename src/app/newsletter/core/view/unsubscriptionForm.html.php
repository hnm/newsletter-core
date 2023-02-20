<?php 
	use newsletter\core\model\UnsubscriptionForm;
	use n2n\impl\web\ui\view\html\HtmlView;
	
	$view = HtmlView::view($this);
	$html = HtmlView::html($view);
	$formHtml = HtmlView::formHtml($view);
	$ariaFormHtml = HtmlView::ariaFormHtml($view);
	
	$unsubscriptionForm = $view->getParam('unsubscriptionForm'); 
	$view->assert($unsubscriptionForm instanceof UnsubscriptionForm);
	
	$html->meta()->addMeta(array('name' => 'robots', 'content' => 'noindex'));
	
	$view->useTemplate($view->getParam('templateViewId'), 
			array('title' => $view->getL10nText('newsletter_unsubscription_title')));
?>

<div class="newsletter-box">
	<p><?php $html->text('newsletter_unsubscription_text') ?></p>
	
	<?php $formHtml->open($unsubscriptionForm, null, null, array('class' => 'newsletter-unsubscription-form')); ?>
		<?php $ariaFormHtml->input('email', true, array('placeholder' => $view->getL10nText('newsletter_form_email'))) ?>
		<?php $formHtml->buttonSubmit('unsubscribe', 
				$view->getL10nText('newsletter_unsubscription_form_action')) ?>
		<br />
		<?php $ariaFormHtml->message('email') ?>
	<?php $formHtml->close() ?>
</div>