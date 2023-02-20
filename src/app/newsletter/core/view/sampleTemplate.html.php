<?php
	use n2n\impl\web\ui\view\html\HtmlView;
	
	$view = HtmlView::view($view);
	$html = HtmlView::html($view);
	$request = HtmlView::request($view);
?>
<!doctype html>
<html class="no-js" lang="<?php $html->out($view->getN2nLocale()->getLanguageId()) ?>">
	<?php $html->headStart() ?>
		<!-- internet page created by hnm.ch -->
	<?php $html->headEnd() ?>
	<?php $html->bodyStart() ?>
		<?php $view->importContentView() ?>
	<?php $html->bodyEnd() ?>
</html>