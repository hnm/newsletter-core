<?php 
	use newsletter\core\bo\Newsletter;
	use newsletter\core\bo\HistoryEntry;
	use newsletter\core\model\NewsletterDao;
	use newsletter\core\model\ui\TextView;
	use newsletter\core\model\NewsletterState;
	
	$view = TextView::view($this);
	$request = TextView::request($view);
	
	$newsletter = $view->getParam('newsletter');
	$view->assert($newsletter instanceof Newsletter);
	
	$newsletterDao = $view->lookup('\newsletter\core\model\NewsletterDao');
	$view->assert($newsletterDao instanceof NewsletterDao);
	
	$newsletterState = $view->lookup(NewsletterState::class);
	$view->assert($newsletterState instanceof NewsletterState);
	
	$historyEntry = $view->getParam('historyEntry', false, null);
	$view->assert($historyEntry instanceof HistoryEntry);

	$eol = "\n";
?>

<?php foreach ($newsletter->getNewsletterCis() as $newsletterCi) : ?>
	<?php echo $newsletterCi->createTextUiComponent($historyEntry, $view, $eol) . $eol ?>
<?php endforeach ?> 

<?php echo $view->getL10nText('newsletter_template_imprint') . $eol . $eol ?>
<?php echo $view->getL10nText('newsletter_template_disclaimer', 
		array('sender' => $newsletterState->getSenderName())) . $eol . $eol ?>
 
<?php echo $view->getL10nText('homepage_txt') . ':' 
			. $request->getHostUrl()->ext($request->getContextPath())  . $eol ?> 
<?php echo $view->getL10nText('unsubscribe_txt') . ':' 
			. $newsletterState->buildUnsubscriptionUrl($historyEntry->getEmail()) ?>
