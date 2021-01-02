<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run_cli;

use tt\run\Controller;
use tt\service\Error;
use tt\service\ServiceStrings;

abstract class RunApi {

	protected $data = array();

	/**
	 * @param array $data
	 */
	public function __construct(array $data) {
		$this->data = $data;
	}

	public static function run() {
		global $argv;

		$data = $argv;

		if(!is_array($data) || count($data)<2){
			new Error("No arguments passed!");
		}

		#print_r($data);exit;

		//Remoce first element (script itself)
		array_shift($data);

		//First argument: Controller
		$controller = array_shift($data);

		$class = ServiceStrings::classnameSafe($controller);

		Controller::runController($class, Controller::RUN_TYPE_CLI, $data);
	}

	/**
	 * @return string plaintext
	 */
	abstract public function runCli();

}
