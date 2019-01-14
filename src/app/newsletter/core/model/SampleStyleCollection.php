<?php
namespace newsletter\core\model;

class SampleStyleCollection implements TemplateStyleCollection {
	public function getTextColor() {
		return '#333333';
	}
	
	public function getFooterTextColor() {
		return $this->getTextColor();
	}
	
	public function getBodyBackgroundColor() {
		return '#ebebeb';
	}
	
	public function getTextBackgroundColor() {
		return '#ffffff';
	}
	
	public function getHeaderBackgroundColor() {
		return $this->getBodyBackgroundColor();
	}
	
	public function getLogoBackgroundColor() {
		return $this->getHeaderBackgroundColor();
	}
	
	public function getFooterBackgroundColor() {
		return $this->getBodyBackgroundColor();
	}

	public function getPrimaryColor() {
		return '#FF6701';
	}

	public function getBaseFontFamily() {
		return 'Arial, sans-serif';
	}

	public function getBaseFontPixelSize() {
		return 15;
	}

	public function getBasePixelLineHeight() {
		return 20;
	}
	
	public function getHeadingsFontFamily() {
		return 'Arial, sans-serif';
	}

	public function getHeadingsFontPixelSize() {
		return 18;
	}
	public function getHeadingsFontWeight() {
		return 'bold';
	}

	public function getHeadingsPixelLineHeight() {
		return 24;
	}
	
	public function getButtonColor() {
		return $this->getPrimaryColor();
	}
	
	public function getButtonHoverColor() {
		return '#db5902';
	}
	
	public function getButtonFontPixelSize() {
		return 13;
	}

	public function getButtonLineHeight() {
		return 1.1;
	}
	
	public function getLightGray() {
		return '#f3f3f3';
	}

	public function getMediumGray() {
		return '#ebebeb';
	}

	public function getDarkGray() {
		return '#6b6b6b';
	}
	
	public function getPMargin() {
		return 10;
	}
}