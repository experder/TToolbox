<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\form;

class FormfieldHeader extends Formfield {

	public function __construct($value, $title="", $more_params = array()) {
		parent::__construct("header", "", $value, false, $more_params);
		$this->setTitle($title);
	}

	public function setTitle($title = null){
		$this->title = $title;
	}

	protected function toHtml() {
		return "<div" . $this->getParams_outer() . ">\n"
			. ($this->title===null?"":"\t<label>$this->title</label>\n")
			. "\t".$this->inner_html()."\n"
			. "</div>";
	}

	public function inner_html() {
		return $this->value;
	}

}
