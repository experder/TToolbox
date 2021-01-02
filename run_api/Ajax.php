<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run_api;

use tt\run\Controller;
use tt\service\Error;
use tt\service\ServiceStrings;

abstract class Ajax {

	protected $cmd;

	protected $data = array();

	/**
	 * @param array $data
	 */
	public function __construct(array $data) {
		$this->data = $data;
	}

	public static function run() {

		$input = file_get_contents("php://input");

		$input_data = json_decode($input, true);

		if ($input_data === null) {
			$input_data = $_POST;
		}

		if(!isset($input_data["class"])){
			new Error("Ajax: No class set!");
		}

		$class = ServiceStrings::classnameSafe($input_data["class"]);
		unset($input_data["class"]);

		Controller::runController($class, Controller::RUN_TYPE_API, $input_data);
	}

	/**
	 * @return array JSON
	 */
	public function runAjax() {
		if (!isset($this->data["cmd"])) {
			new Error(get_class($this) . ": No cmd sent!");
		}

		$this->cmd = $this->data["cmd"];
		unset($this->data["cmd"]);

		$response = $this->runCmd();
		if ($response === null) {
			new Error(get_class($this) . ": Unknown command '$this->cmd'!");
		}
		return $response;
	}

	/**
	 * @return array|null array(
	 * "ok" => true,
	 * "html" => "...",
	 * //Optional:
	 * "msg_type" => "info",//["info","error","ok","ask"]
	 * );
	 */
	abstract protected function runCmd();

	protected function requiredFieldsFromData($fieldlist) {
		$fields = array();
		foreach ($fieldlist as $key) {
			if (!isset($this->data[$key])) {
				new Error(get_class($this) . " (cmd:$this->cmd): Required data not received: '$key'");
			}
			$fields[$key] = $this->data[$key];
		}
		return $fields;
	}

}
