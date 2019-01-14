<?php
namespace newsletter\core\model;

interface TemplateStyleCollection {
	/**
	 * Returns the generell Text Color
	 * @return string
	 */
	public function getTextColor();
	/**
	 * Returns the Text Color for the Footer
	 * @return string
	 */
	public function getFooterTextColor();
	/**
	 * Returns the generell BackgroundColor
	 * @return string
	 */
	public function getBodyBackgroundColor();
	/**
	 * Returns the generell BackgroundColor For Text Blocks
	 * @return string
	 */
	public function getTextBackgroundColor();
	/**
	 * Returns the Header BackgroundColor
	 * @return string
	 */
	public function getHeaderBackgroundColor();
	/**
	 * Returns the Logo BackgroundColor
	 * @return string
	 */
	public function getLogoBackgroundColor();
	/**
	 * Returns the Footer BackgroundColor
	 * @return string
	 */
	public function getFooterBackgroundColor();
	/**
	 * Returns the Primary color (commonly used as link color)
	 * @return string
	 */
	public function getPrimaryColor();
	/**
	 * Returns the Base Font Family 
	 * @return string
	 */
	public function getBaseFontFamily();
	/**
	 * Returns the Size of the Base Font in Pixel
	 * @return int
	 */
	public function getBaseFontPixelSize();
	/**
	 * Returns the Line Height in Pixel
	 * return int
	 */
	public function getBasePixelLineHeight();
	/**
	 * Returns the Headings Font Family 
	 * @return string
	 */
	public function getHeadingsFontFamily();
	/**
	 * Return the Size of the Headings Font in Pixel
	 * @return int
	 */
	public function getHeadingsFontPixelSize();
	/**
	 * Returns the Hedings Line Height in Pixel
	 * @return int
	 */
	public function getHeadingsPixelLineHeight();
	/**
	 * Returns the Headings Font Weight
	 * @return string
	 */
	public function getHeadingsFontWeight();
	/**
	 * returns the button color
	 * @return string
	 */
	public function getButtonColor();
	/**
	 * Returns the button hover color
	 * @return string
	 */
	public function getButtonHoverColor();
	/**
	 * Returns the Size of the Button Text Font
	 * @return int
	 */
	public function getButtonFontPixelSize();
	/**
	 * Returns the line height for buttons
	 * @return float
	 */
	public function getButtonLineHeight();
	/**
	 * Returns a light gray
	 * @return string
	 */
	public function getLightGray();
	/**
	 * Returns a medium gray
	 * @return string
	 */
	public function getMediumGray();
	/**
	 * Returns a dark gray
	 * @return string
	 */
	public function getDarkGray();
	
	/**
	 * Returns the margin after a p tag
	 * @return string
	 */
	public function getPMargin();
}