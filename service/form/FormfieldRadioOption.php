<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\form;

class FormfieldRadioOption extends Formfield {

	/**
	 * Formfield_radio_option constructor.
	 * @param string $value
	 * @param string $title
	 * @param string $ttoltip
	 */
	public function __construct($value, $title, $ttoltip = "") {
		parent::__construct(null, $title, $value);
		$this->tooltip = $ttoltip;
	}

	public function toFormHtml($name, $checked_val = null) {
		$checked = ($this->value == $checked_val ? "checked" : "");
		$label = "<input type='radio' $checked name='$name' value='$this->value'/>" . $this->getLabel();
		return "<label " . $this->getTitle() . ">" . $label . "</label>";
	}

	protected function inner_html() {
		/*NOT IN USE*/
	}
}
