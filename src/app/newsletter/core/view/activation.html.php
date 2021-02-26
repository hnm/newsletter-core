<?php 
	use n2n\impl\web\ui\view\html\HtmlView;

	$view = HtmlView::view($this);
	$html = HtmlView::html($view);
	
	$message = $view->getParam('message');
	
	$view->useTemplate($view->getParam('templateViewId'), 
			array('title' => $view->getL10nText('newsletter_activation_title')));
?>
<div class="newsletter-box">
	<p><?php $html->out($message) ?></p>
</div>
