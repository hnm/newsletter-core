<?php 
	use newsletter\core\model\ui\NewsletterHtmlBuilder;
	use n2n\l10n\DynamicTextCollection;
	use n2n\impl\web\ui\view\html\HtmlView;
	use newsletter\core\rocket\command\TestSendForm;
	use n2n\web\ui\Raw;
	
	$view = HtmlView::view($this);
	$html = HtmlView::html($view);
	$formHtml = HtmlView::formHtml($view);

	$testSendForm = $view->getParam('testSendForm');
	$view->assert($testSendForm instanceof TestSendForm);
	
	$newsletter = $testSendForm->getNewsletter();

    $view->useTemplate('\rocket\si\content\impl\iframe\view\iframeTemplate.html');

    $newsletterHtml = new NewsletterHtmlBuilder($view);
	
	$rocketDtc = new DynamicTextCollection('rocket', $view->getN2nLocale());
	//$eiHtml = new EiuHtmlbuilder($view);
?>

<?php $html->messageList() ?>

<?php $formHtml->open($testSendForm, null, 'post', array('class' => 'rocket-impl-form')) ?>
	<div class="rocket-entry-form">
		<div class="rocket-group rocket-simple-group">
			<label>Empfänger</label>
            <div class="rocket-structure-content">
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
		</div>

        <?php $formHtml->buttonSubmit('send',
                new Raw('<i class="fa fas fa-envelope-open"></i><span>'
                        . $view->getL10nText('rocket_script_cmd_test_send_label') . '</span>'),
                    array('class' => 'btn btn-primary')) ?>
	</div>
<?php $formHtml->close() ?>
