<?php
namespace newsletter\core\model\ui;

use n2n\core\N2N;
use n2n\web\ui\view\View;

class TextView extends View {
	public function getContentType() {
		return 'text/plain; charset=' . N2N::CHARSET;
	}
}
