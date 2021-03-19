<?php 
	use newsletter\core\bo\HistoryEntry;
	use n2n\impl\web\ui\view\html\HtmlView;
	use n2n\web\ui\Raw;
	use newsletter\core\rocket\command\model\StatsModel;
	
	/**
	 * @var \n2n\web\ui\view\View $view
	 */
	$view = HtmlView::view($view);
	$html = HtmlView::html($view);

	$statsModel = $view->getParam('statsModel');
	$view->assert($statsModel instanceof StatsModel);
	
	$view->useTemplate('\rocket\core\view\template.html',
			array('title' => $html->getL10nText('newsletter_stats_txt')));
	
	$numNewsletterLoaded = 0;
	$totalNewslettersSent = 0; 
	
	$newsletterDao = $statsModel->getNewsletterDao();
	$newsletter = $statsModel->getNewsletter();

?>

<h2><?php $html->text('newsletter_stats_txt') ?></h2>
<table class="table table-hover rocket-table">
	<thead>
		<tr>
			<th><?php $html->text('status_txt') ?></th>
			<th><?php $html->text('num_txt') ?></th>
			<th><?php $html->text('recipients_txt') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach (HistoryEntry::getPossibleStatus() as $status) : ?>
            <?php $num = $newsletterDao->getNumHistoryEntriesForNewsletter($newsletter, $status) ?>
			<?php $totalNewslettersSent += $num ?>
			<?php $numNewsletterLoaded += ($status === HistoryEntry::STATUS_READ) ? $num : 0 ?>
			<tr>
				<td><?php $html->text('status_' . $status . '_txt') ?></td>
				<td><?php $html->out($num) ?></td>
				<td>
					<?php $html->linkToController(array('detail', $newsletter->getId(), $status), 
							$view->getL10nText('num_recipients_txt', array('num' => $num))) ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<th><?php $html->text('total_txt') ?></th>
			<th><?php $html->out($totalNewslettersSent) ?></th>
			<th><?php $html->text('percentage_loaded_info', 
					array('percentage' => sprintf("%01.2f", ($totalNewslettersSent > 0) ? 
							$numNewsletterLoaded / $totalNewslettersSent * 100 : 0))) ?></th>
		</tr>
	</tfoot>
</table>

<h2><?php $html->text('link_stats_txt') ?></h2>
<table class="table table-hover rocket-table">
	<thead>
		<tr>
			<th><?php $html->text('link_txt') ?></th>
			<th><?php $html->text('num_txt') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $numClicks = 0; ?>
		<?php foreach ($statsModel->getGroupedNewsletterLinks() as $label => $num): ?>
			<?php $numClicks += $num ?>
			<tr>
				<td>
					<?php $html->out($label) ?>
				</td>
				<td><?php $html->out($num) ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<th><?php $html->text('total_txt') ?></th>
			<th><?php $html->out($numClicks) ?></th>
		</tr>
	</tfoot>
</table>

<p><?php $html->text('avg_clicks_per_newsletter_info',
		array('avg' => sprintf("%01.2f", ($numNewsletterLoaded > 0) ? $numClicks / $numNewsletterLoaded : 0))) ?></p>

<div class="rocket-zone-commands">
	<div>
		<?php $html->link($statsModel->buildDetailUrl(), 
			new Raw('<i class="fa fa-times-circle"></i><span>' . $view->getL10nText('common_cancel_label') . '</span>'),
					array('class' => 'btn btn-secondary')) ?>
	</div>
</div>
