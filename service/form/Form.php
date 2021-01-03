<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\form;

use tt\service\Html;

class Form {

	/**
	 * @var string|false $action
	 */
	private $action;
	/**
	 * @var string $method
	 */
	private $method;
	private $buttons = array();
	private $fields = array();

	private $params;

	public $onSubmit = "t2_spinner_start();";

	/**
	 * @param string|false $action An URL that is called on form submission.
	 *                             Can be left empty (same page is called).
	 *                             Set to FALSE to disable html native send functionality.
	 *                             (Which will still send form via get)
	 * @param string|false $submit_text Label of the submit button. FALSE to turn off submit button.
	 * @param string|null  $CMD_ If set, a hidden key "cmd" is sent on submission.
	 * @param string|null  $method ["get"|"post"] Form submission method. The submission method is "post" by default.
	 * @param array        $params Associative array width additional params for the HTML form tag.
	 */
	public function __construct($CMD_ = null, $action = "", $submit_text = "Submit", $method = "post", $params = array()) {

		$this->params = $params;

		$this->action = $action;

		$this->method = $method;

		if ($CMD_) {
			$this->fields[] = new FormfieldHidden("cmd", $CMD_);
		}

		if ($submit_text !== false) {
			$this->buttons[] = "<input type='submit' value='$submit_text'>";
		}

	}

	public function addField(Formfield $formfield) {
		$this->fields[] = $formfield;
	}

	public function __toString() {
		return $this->toHtml();
	}

	public function addButton($button) {
		$this->buttons[] = $button;
	}

	public function toHtml() {
		$buttons = "";
		if ($this->buttons) {
			$buttons = "\n" . new Html("div", "", array(
					"class" => "buttons"
				), $this->buttons);
		}
		$fields_html = implode("\n", $this->fields);
		$action = ($this->action === false ? "" : (" action=\"$this->action\" method='$this->method'"));
		$params = $this->params;
		if($this->onSubmit){
			$params["onsubmit"] = $this->onSubmit;
		}
		return "<form$action " . Html::tag_keyValues($params) . ">\n$fields_html$buttons\n</form>";
	}

}
