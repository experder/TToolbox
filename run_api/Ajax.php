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
use tt\service\ServiceEnv;

class Ajax extends Controller {

	protected $cmd;

	public function runAjax() {
		if (!isset($this->data["cmd"])){
			new Error(get_class($this).": No cmd sent!");
		}

		$this->cmd=$this->data["cmd"];
		unset($this->data["cmd"]);

		$response = $this->runCmd();
		if($response===null){
			new Error(get_class($this).": Unknown command '$this->cmd'!");
		}
		return $response;
	}

	protected function runCmd() {
		switch ($this->cmd){
			case "test1":
				return array(
					"ok" => true,
					"html" => "You have sent:<pre>" . print_r($this->data, 1) . "</pre>",
					"msg_type" => isset($this->data["msg_type"]) ? $this->data["msg_type"] : "info",
				);
				break;
			default:
				return null;
				break;
		}
	}

	protected function requiredFieldsFromData($fieldlist) {
		$fields = array();
		foreach ($fieldlist as $key){
			if(!isset($this->data[$key])){
				new Error(get_class($this)." (cmd:$this->cmd): Required data not received: '$key'");
			}
			$fields[$key]=$this->data[$key];
		}
		return $fields;
	}

}
