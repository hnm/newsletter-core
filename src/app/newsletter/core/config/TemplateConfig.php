<?php
namespace newsletter\core\config;

use n2n\io\managed\File;

class TemplateConfig {
	const DEFAULT_TEMPLATE_HTML_VIEW_ID = 'newsletter\core\view\template\template.html';
	const DEFAULT_TEMPLATE_TEXT_VIEW_ID = 'newsletter\core\view\template\template.txt';
	
	private $fileLogo;
	private $templateHtmlViewId = self::DEFAULT_TEMPLATE_HTML_VIEW_ID;
	private $templateTextViewId = self::DEFAULT_TEMPLATE_TEXT_VIEW_ID;
	
	public function getFileLogo() {
		return $this->fileLogo;
	}

	public function setFileLogo(File $fileLogo = null) {
		$this->fileLogo = $fileLogo;
		
		return $this;
	}

	public function getTemplateHtmlViewId() {
		return $this->templateHtmlViewId;
	}

	public function setTemplateHtmlViewId($templateHtmlViewId) {
		$this->templateHtmlViewId = $templateHtmlViewId;
		
		return $this;
	}

	public function getTemplateTextViewId() {
		return $this->templateTextViewId;
	}

	public function setTemplateTextViewId($templateTextViewId) {
		$this->templateTextViewId = $templateTextViewId;
		
		return $this;
	}
}