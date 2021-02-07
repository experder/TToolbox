<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\form;

use tt\core\Config;
use tt\service\Html;
use tt\service\ServiceEnv;
use tt\service\ServiceStrings;

/**
 * Class Formfield
 * Generic class representing all formfields.
 */
abstract class Formfield {

	//Formfield:
	protected $name;
	protected $value;
	protected $id = null;
	/**
	 * @var array All params except "name", "value" and "id".
	 */
	protected $more_params;
	protected $disabled = false;

	//Label:
	protected $title;
	protected $tooltip = "";

	//Surrounding div:
	protected $outer_id = null;
	protected $outer_class = null;
	protected $outer_more_params = array();

	protected $cssClasses = array();

	/**
	 * Formfield constructor.
	 * @param             $name
	 * @param string|null $title
	 *                    If set to null, the fieldname is used as label.
	 * @param string|null $value
	 * @param bool        $val_from_request
	 *                    If set to true, the default value ($value) can be overwritten by the request.
	 *                    Example: .../myform.php?myvalue=Foo
	 * @param array       $more_params
	 *                    All params except "name", "value" and "id".
	 */
	public function __construct($name, $title = null, $value = null, $val_from_request = true, $more_params = array()) {
		$this->name = $name;

		//Title: If set to null, the fieldname is used as label.
		$this->title = ($title === null ? $name : $title);

		$this->value = $val_from_request ? ServiceEnv::requestValue($name, $value) : $value;

		$this->more_params = $more_params;
	}

	/**
	 * Generic function is overwritten with the respective HTML by the children.
	 * @return string
	 */
	abstract protected function inner_html();

	public function __toString() {
		return $this->toHtml();
	}

	protected function toHtml() {
		return "<div" . $this->getParams_outer() . ">"
			. "<label" . $this->getTitle() . ">" . $this->getLabel() . "</label>"
			. $this->inner_html()
			. "</div>";
	}

	public function setOuterId($id) {
		$this->outer_id = $id;
	}

	public function setTooltip($tooltip) {
		$this->tooltip = $tooltip;
	}

	public function setDisbled(){
		$this->disabled=true;
	}

	public function setParam($key, $value, $overwrite = true) {
		if (isset($this->more_params[$key]) && !$overwrite) return false;
		$this->more_params[$key] = $value;
		return true;
	}

	protected function getValue() {
		return $this->value;
	}

	protected function getTitle() {
		$tooltip = $this->tooltip;

		//Developers see the fieldname
		if (Config::get(Config::DEVMODE, false)) {
			$tooltip .= " [" . $this->name . "]";
		}

		$title = $tooltip ? " title='" . ServiceStrings::escape_value_html($tooltip) . "'" : "";

		if ($this->tooltip) {
			$title .= " class='tooltipped'";
		}

		return $title;
	}

	protected function getLabel() {
		$label = $this->title;
		return $label;
	}

	public function getName() {
		return $this->name;
	}

	/**
	 * Every formfield has a name, a value, an id and possibly a list of some other parameters ($more_params).
	 * This function creates the corresponding HTML-snippet.
	 * @param bool $value If set to FALSE, the parameter "value" is skipped.
	 * @param bool $name If set to FALSE, the parameter "name" is skipped.
	 * @return string String to insert into the HTML code.
	 */
	protected function getParams_inner($value = true, $name = true) {
		$params = $this->more_params;

		if ($name && $this->name) {
			$params["name"] = $this->name;
		}
		if ($value && $this->value !== null) {
			$params["value"] = $this->value;
		}
		if ($this->id) {
			$params["id"] = $this->id;
		}
		if ($this->cssClasses) {
			if (!isset($params["class"])) $params["class"] = "";
			foreach ($this->cssClasses as $class) {
				$params["class"] .= " " . $class;
			}
		}

		$disabledHtml = $this->disabled?" disabled":"";

		return Html::tag_keyValues($params).$disabledHtml;
	}

	/**
	 * For documentation see getParams_inner.
	 * @see getParams_inner
	 */
	protected function getParams_outer() {
		$params = $this->outer_more_params;

		if ($this->outer_id) $params["id"] = $this->outer_id;
		$params["class"] = "form_field ff_" . $this->name . ($this->outer_class ? " " . $this->outer_class : "");

		return Html::tag_keyValues($params);
	}

}
