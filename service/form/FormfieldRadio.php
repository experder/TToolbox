<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\form;

use tt\service\Error;

class FormfieldRadio extends Formfield {

	/**
	 * @var FormfieldRadioOption[] $options
	 */
	private $options = false;

	/**
	 * @param string                 $name
	 * @param FormfieldRadioOption[] $options
	 * @param string                 $value
	 * @param bool                   $val_from_request
	 * @param array                  $more_params
	 */
	public function __construct($name, $options, $value = null, $val_from_request = true, $more_params = array()) {
		parent::__construct($name, null, $value, $val_from_request, $more_params);
		if (is_array($options)) {
			if (!(reset($options) instanceof FormfieldRadioOption)) {
				$array = array();
				foreach ($options as $key => $value) {
					$array[] = new FormfieldRadioOption($key, $value);
				}
				$options = $array;
			}
			$this->options = $options;
		}
		if ($this->options === false) {
			new Error("Options must be an array of FormfieldRadioOption!");
		}
		foreach ($options as $option) {
			$option->name = $name . " = " . $option->getValue();
		}
		$this->outer_class = "radio";
	}

	protected function toHtml() {
		return "<div" . $this->getParams_outer() . ">"
			. $this->options_html()
			. "</div>";
	}

	private function options_html() {
		$options_html = array();
		foreach ($this->options as $option) {
			$options_html[] = $option->toFormHtml($this->name, $this->value) . "\n";
		}
		return implode("\n", $options_html);
	}

	public function inner_html() {
		return "???";//(never used)
	}

}
