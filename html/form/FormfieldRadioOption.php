<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\html\form;

use tt\config\Config;

class FormfieldRadioOption {

	private $value;
	private $title;

	/**
	 * Formfield_radio_option constructor.
	 * @param string $value
	 * @param string $title
	 */
	public function __construct($value, $title) {
		$this->value = $value;
		$this->title = $title;
	}

	public function to_form_html($name, $checked_val = null) {
		$checked = ($this->value == $checked_val ? "checked" : "");
		$title = Config::$DEVMODE ? " title='$this->value'" : '';
		return "<label class='radiolabel' $title><div class='ff_radiooption'><input type='radio' $checked name='$name' value='$this->value'/>$this->title</div></label>";
	}

}
