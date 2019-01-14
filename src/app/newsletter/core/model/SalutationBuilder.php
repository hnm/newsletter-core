<?php
namespace newsletter\core\model;

use newsletter\core\bo\Newsletter;
use newsletter\core\bo\Recipient;
use n2n\l10n\DynamicTextCollection;

interface SalutationBuilder {
	public function buildSalutation(DynamicTextCollection $dtc, Newsletter $newsletter, Recipient $recipient);
}