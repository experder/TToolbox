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

	public function runAjax() {
		ServiceEnv::$response_is_expected_to_be_json = true;

		if (!isset($this->data["cmd"])){
			new Error("No cmd sent!");
		}

		$cmd=$this->data["cmd"];
		unset($this->data["cmd"]);

		return $this->runCmd($cmd);
	}

	protected function runCmd($cmd) {
		switch ($cmd){
			case "test1":
				return array("ok"=>true,"html"=>"You have sent:<pre>".print_r($this->data,1)."</pre>");
				break;
			default:
				new Error("Unknown command '$cmd'!");
				return null;
				break;
		}
	}

}
