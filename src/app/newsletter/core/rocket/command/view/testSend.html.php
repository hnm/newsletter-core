<?php 
	use newsletter\core\model\ui\NewsletterHtmlBuilder;
	use n2n\l10n\DynamicTextCollection;
	use n2n\impl\web\ui\view\html\HtmlView;
	use newsletter\core\rocket\command\TestSendForm;
	use n2n\web\ui\Raw;
	use rocket\ei\manage\control\IconType;
	use rocket\ei\util\gui\EiuHtmlBuilder;
	
	$view = HtmlView::view($this);
	$html = HtmlView::html($view);
	$formHtml = HtmlView::formHtml($view);

	$testSendForm = $view->getParam('testSendForm');
	$view->assert($testSendForm instanceof TestSendForm);
	
	$newsletter = $testSendForm->getNewsletter();
	
	$view->useTemplate('\rocket\core\view\template.html',
			array('title' => $html->getL10nText('test_send_title', array('subject' => $newsletter->getSubject()))));
	
	$newsletterHtml = new NewsletterHtmlBuilder($view);
	
	$rocketDtc = new DynamicTextCollection('rocket', $view->getN2nLocale());
	$eiHtml = new EiuHtmlBuilder($view);
?>
<?php $formHtml->open($testSendForm, null, 'post', array('class' => 'rocket-impl-form')) ?>
	<div class="rocket-entry-form">
		<div class="rocket-group">
			<label>Empfänger</label>
			<div class="rocket-control">
				<div class="rocket-editable rocket-item rocket-required">
					<label>E-Mail<?php //$eiHtml->fieldLabel('email') ?></label>
					<div class="rocket-control">
						<?php $formHtml->input('email', array('class' => 'form-control')) ?>
					</div>
				</div>
				<div class="rocket-block rocket-editable rocket-item">
					<label>Vorname<?php //$eiHtml->fieldLabel('firstName') ?></label>
					<div class="rocket-control">
						<div><?php $formHtml->input('firstName', array('class' => 'form-control')) ?></div>
					</div>
				</div>
				<div class="rocket-editable rocket-item">
					<label>Nachname<?php //$eiHtml->fieldLabel('lastName') ?></label>
					<div class="rocket-control">
						<div><?php $formHtml->input('lastName', array('class' => 'form-control')) ?></div>
					</div>
				</div>
				<div class="rocket-editable rocket-item">
					<label>Geschlecht<?php //$eiHtml->fieldLabel('gender') ?></label>
					<div class="rocket-control">
						<?php $newsletterHtml->genderRadio('gender') ?>
					</div>
				</div>
				<div class="rocket-editable rocket-item">
					<label>Ansprechen mit<?php //$eiHtml->fieldLabel(null, 'saluteWith') ?></label>
					<div class="rocket-control">
						<?php $newsletterHtml->saluationRadio('saluteWith') ?>
					</div>
				</div>
			</div>
		</div>
		<div class="rocket-zone-commands">
			<div>
				<?php $formHtml->buttonSubmit('send', 
						new Raw('<i class="' . IconType::ICON_ENVELOPE_O . '"></i><span>' . $view->getL10nText('rocket_script_cmd_test_send_label') . '</span>'),
						array('class' => 'btn btn-primary')) ?>
				<?php $html->link($html->meta()->getContextUrl($newsletter->getId()), 
					new Raw('<i class="fa fa-times-circle"></i><span>' . $rocketDtc->translate('common_cancel_label') . '</span>'),
							array('class' => 'btn btn-secondary')) ?>
			</div>
		</div>
	</div>
<?php $formHtml->close() ?>