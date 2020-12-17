<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\html\form;

class FormfieldCheckbox extends Formfield {

	public function __construct($name, $title = null, $value = null, $val_from_request = true, $more_params = array()) {
		parent::__construct($name, $title, $value, $val_from_request, $more_params);
		$this->outer_class = "checkbox";
	}

	protected function toHtml() {
		return "<div" . $this->getParams_outer() .$this->get_title(). ">"
			. "<label>"
			. $this->inner_html()
				.$this->get_label()
			."</label>"
			. "</div>";
	}

	public function inner_html() {
		return "<input type='checkbox' " . ($this->value ? " checked" : "") . $this->getParams_inner() . "/>";
	}

}
