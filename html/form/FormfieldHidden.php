<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\html\form;

use tt\debug\Error;

class FormfieldHidden extends Formfield {

	public function __construct($name, $value) {
		parent::__construct($name, null, $value, false);
	}

	public function toHtml() {
		return "<input type='hidden'" . $this->getParams_inner() . " />";
	}

	/** Not in use. */
	protected function inner_html() {
		new Error("Should never be called");
		return "NOT IN USE";
	}
}
