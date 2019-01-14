<?php 
	use newsletter\core\bo\Recipient;
	use n2n\impl\web\ui\view\html\HtmlView;
	
	$view = HtmlView::view($view);
	$html = HtmlView::html($view);
	
	$recipient = $view->getParam('recipient');
	$view->assert($recipient instanceof Recipient);
	
	$view->useTemplate($view->getParam('templateViewId'), 
			array('title' => $view->getL10nText('newsletter_activation_complete_title')));
?>
<div class="newsletter-box">
	<p><?php $html->text('newsletter_activation_complete') ?></p>
</div>