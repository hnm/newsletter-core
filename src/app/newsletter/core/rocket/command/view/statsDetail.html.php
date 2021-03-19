<?php 
	use newsletter\core\bo\Newsletter;
	use newsletter\core\model\NewsletterDao;
	use newsletter\core\bo\HistoryEntry;
	
	use n2n\impl\web\ui\view\html\HtmlView;
	use n2n\web\ui\Raw;
	use newsletter\core\rocket\command\StatsController;
	use rocket\ei\util\frame\EiuFrame;
	
	/**
	 * @var \n2n\web\ui\view\View $view
	 */
	$view = HtmlView::view($view);
	$html = HtmlView::html($view);
	
	$newsletterDao = $view->lookup(NewsletterDao::class);
	$view->assert($newsletterDao instanceof NewsletterDao);
	
	$newsletter = $view->getParam('newsletter');
	$view->assert($newsletter instanceof Newsletter);
	
	$status = $view->getParam('status');
	$view->assert(in_array($status, HistoryEntry::getPossibleStatus()));
	
	$eiuFrame = $view->getParam('eiuFrame');
	$view->assert($eiuFrame instanceof EiuFrame);
	
	$numEntries = 0;
    $view->useTemplate('\rocket\si\content\impl\iframe\view\iframeTemplate.html');
    $html->meta()->setTitle($view->getL10nText('stats_detail_txt',
            array('status' => $view->getL10nText('status_' . $status . '_txt'))));
//	$view->useTemplate('\rocket\core\view\template.html',
//			array('title' => $view->getL10nText('stats_detail_txt', array('status' => $view->getL10nText('status_' . $status . '_txt')))));
?>

<h3><?php $html->text('common_properties_title') ?></h3>

<table class="table table-hover rocket-list">
	<thead>
		<tr>
			<th>
				<?php $html->text('email_txt')?>
			</th>
			<th>
				<?php $html->text('status_txt')?>
			</th>
			<?php if ($status === HistoryEntry::STATUS_ERROR): ?>
				<th>
					<?php $html->text('message_txt')?>
				</th>
			<?php endif ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($newsletterDao->getHistoryEntriesForNewsletter($newsletter, $status) as $historyEntry): $numEntries++?>
			<tr>
				<td>
					<?php $html->out($historyEntry->getEmail())?>
				</td>
				<td>
					<?php $html->out($historyEntry->getStatus()) ?>
				</td>
				<?php if ($status === HistoryEntry::STATUS_ERROR): ?>
					<td>
						<?php $html->out($historyEntry->getStatusMessage()) ?>
					</td>
				<?php endif ?>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
	
<div class="rocket-zone-commands">
	<div>
		<?php $html->linkToController($newsletter->getId(), 
				new Raw('<i class="fa fa-times-circle"></i><span>' . $view->getL10nText('common_cancel_label') . '</span>'),
						array('class' => 'btn btn-secondary')) ?>
		<?php if ($numEntries > 0): ?>
			<?php if ($status === HistoryEntry::STATUS_ERROR): ?>
				<?php $html->linkToController(array(StatsController::ACTION_REMOVE_FAILED_HISTORY_ENTRIES, $newsletter->getId()),  
						new Raw('<i class="fa fa-times-circle"></i><span>' . $view->getL10nText('reset_txt') . '</span>'), 
						array('class' => 'btn btn-secondary')) ?>
				<?php $html->linkToController(array(StatsController::ACTION_DELETE_RECIPIENTS, $newsletter->getId()), 
						new Raw('<i class="fa fa-times-circle"></i><span>' . $view->getL10nText('delete_recipients_txt') . '</span>'),
						array('class' => 'btn btn-danger')) ?>
			<?php elseif ($status === HistoryEntry::STATUS_IN_PROGRESS): ?>
				<?php $html->linkToController(array(StatsController::ACTION_RESET_IN_PROGRESS_HISTORY_ENTRIES, $newsletter->getId()), 
						new Raw('<i class="fa fa-times-circle"></i><span>' . $view->getL10nText('reset_status_txt') . '</span>'),
						array('class' => 'btn btn-secondary')) ?>
			<?php endif ?>
		<?php endif ?>
	</div>
</div>
