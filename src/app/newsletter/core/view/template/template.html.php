<?php 
	use newsletter\core\bo\Newsletter;
	use newsletter\core\controller\TemplateController;
	use newsletter\core\model\NewsletterDao;
	use n2n\impl\web\ui\view\html\HtmlView;
	use n2n\io\managed\img\ImageFile;
	use newsletter\core\bo\HistoryEntry;
	use n2n\io\managed\File;
	use newsletter\core\model\NewsletterState;
	
	$view = HtmlView::view($this);
	$request = HtmlView::request($view);
	$html = HtmlView::html($view);
	$httpContext = HtmlView::httpContext($view);
	
	$newsletterState = $view->lookup('newsletter\core\model\NewsletterState');
	$view->assert($newsletterState instanceof NewsletterState);
	
	$newsletter = $view->getParam('newsletter');
	$view->assert($newsletter instanceof Newsletter);
	
	$fileLogo = $view->getParam('fileLogo', false);
	$view->assert(null === $fileLogo || $fileLogo instanceof File);
	
	$historyEntry = $view->getParam('historyEntry');
	$view->assert($historyEntry instanceof HistoryEntry);
	
	$newsletterDao = $view->lookup('\newsletter\core\model\NewsletterDao');
	$view->assert($newsletterDao instanceof NewsletterDao);
	
	$styleCollection = $newsletterState->getTemplateStyleCollection();
	
	$imageLogo = null;
	if ($fileLogo !== null && $fileLogo->isValid()) {
		$imageLogo = new ImageFile($fileLogo);
	}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="x-apple-disable-message-reformatting">
	<title><?php $html->out($newsletter->getSubject()) ?></title>

	<!--[if mso]>
		<style>
			* {
				font-family: <?php $styleCollection->getBaseFontFamily() ?> !important;
			}
		</style>
	<![endif]-->

	<style>
		html,
		body {
			margin: 0 auto !important;
			padding: 0 !important;
			height: 100% !important;
			width: 100% !important;
		}

		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		div[style*="margin: 16px 0"] {
			margin:0 !important;
		}

		table,
		td {
			mso-table-lspace: 0pt !important;
			mso-table-rspace: 0pt !important;
		}

		table {
			border-spacing: 0 !important;
			border-collapse: collapse !important;
			table-layout: fixed !important;
			margin: 0 auto !important;
		}
		table table table {
			table-layout: auto;
		}

		img {
			-ms-interpolation-mode:bicubic;
		}

		*[x-apple-data-detectors],	/* iOS */
		.x-gmail-data-detectors, 	/* Gmail */
		.x-gmail-data-detectors *,
		.aBn {
			border-bottom: 0 !important;
			cursor: default !important;
			color: inherit !important;
			text-decoration: none !important;
			font-size: inherit !important;
			font-family: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
		}

		.a6S {
			display: none !important;
			opacity: 0.01 !important;
		}
		img.g-img + div {
			display:none !important;
		   }

		.button-link {
			text-decoration: none !important;
		}

		@media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */
			.email-container {
				min-width: 375px !important;
			}
		}

		.button-td,
		.button-a {
			transition: all 100ms ease-in;
		}
		.button-td:hover,
		.button-a:hover {
			background: <?php $html->out($styleCollection->getButtonHoverColor()) ?> !important;
			border-color: <?php $html->out($styleCollection->getButtonHoverColor()) ?> !important;
		}

		@media screen and (max-width: 480px) {

			.fluid {
				width: 100% !important;
				max-width: 100% !important;
				height: auto !important;
				margin-left: auto !important;
				margin-right: auto !important;
			}

			.stack-column,
			.stack-column-center {
				display: block !important;
				width: 100% !important;
				max-width: 100% !important;
				direction: ltr !important;
			}
			.stack-column-center {
				text-align: center !important;
			}

			.center-on-narrow {
				text-align: center !important;
				display: block !important;
				margin-left: auto !important;
				margin-right: auto !important;
				float: none !important;
			}
			table.center-on-narrow {
				display: inline-block !important;
			}

			/* Adjust typography on small screens to improve readability */
			.email-container p {
				font-size: 17px !important;
				line-height: 22px !important;
			}
		}

	</style>

	<!--[if gte mso 9]>
	<xml>
	  <o:OfficeDocumentSettings>
		<o:AllowPNG/>
		<o:PixelsPerInch>96</o:PixelsPerInch>
	 </o:OfficeDocumentSettings>
	</xml>
	<![endif]-->
	
</head>
<body width="100%" style="margin: 0; mso-line-height-rule: exactly;" bgcolor="#ebebeb">
	<center style="width: 100%; background: #ebebeb; text-align: left;">
		
		<div style="max-width: 680px; margin: auto;" class="email-container">
			<!--  [if mso]>
			<table  width="680" role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center">
				<tr>
					<td>
			<![endif] -->

			<!-- Email Header -->
			<table style="max-width: 680px;" role="presentation" aria-hidden="true" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
				<tr>
					<td style="padding: 20px 0; text-align: center">
						<img src="<?php $html->out($newsletterState->getTemplateUrl()->extR(array(TemplateController::ACTION_LOGO, 
							$newsletter->getId(), $historyEntry->getCode()))) ?>" aria-hidden="true" alt="" style="font-family: Arial, sans-serif; font-size: 15px; line-height: 20px; color: #333333; height: auto; background: #ebebeb" align="middle" width="123" height="92" border="0">
					</td>
				</tr>
			</table>
			
			<!-- Email Body -->
			<table style="max-width: 680px;" class="email-container" role="presentation" aria-hidden="true" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
				<?php foreach ($newsletter->getNewsletterCis() as $newsletterCi) : ?>
					<?php $html->out($newsletterCi->createHtmlUiComponent($historyEntry, $this)) ?>
				<?php endforeach ?>
			</table>

			<!-- Email Footer : BEGIN -->
			<table style="max-width: 680px;" role="presentation" aria-hidden="true" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
				<tr style="font-size: 13px; font-family: Arial, sans-serif; line-height: 20px; color: #333333">
					<td class="x-gmail-data-detectors" style="padding: 40px 10px; width: 100%; text-align: center; font-family: Arial, sans-serif; font-size: 15px; line-height: 20px; color: #333333">
						<webversion style="color: <?php $html->out($styleCollection->getTextColor())?>; text-decoration:underline; font-weight: bold;">
							<?php $html->link($newsletterState->buildWebTemplateUrl($newsletter, $historyEntry), 
									$view->getL10nText('newsletter_template_link_alternate'), 
									array('style' => 'font-size: ' . ($styleCollection->getBaseFontPixelSize() - 2) . 'px; color: ' . $styleCollection->getDarkGray() . ';')) ?>
						</webversion>
						<br><br>
						Firmenname<br>Musterstrasse 65, CH-8400 Winterthur<br>+41 (0) 123 45 67
						<br><br>
						<unsubscribe style="color: <?php $html->out($styleCollection->getTextColor())?>; text-decoration:underline;">
							<?php $html->link($newsletterState->buildUnsubscriptionUrl($historyEntry->getEmail()), 
									$view->getL10nText('unsubscribe_txt'), 
									array('style' => 'color: ' . $styleCollection->getTextColor() . ';')) ?>
						</unsubscribe>
					</td>
				</tr>
			</table>
			<!-- Email Footer : END -->

			<!--[if mso]>
					</td>
				</tr>
			</table>
			<![endif]-->
		</div>

	</center>
</body>
</html>