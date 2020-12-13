<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\controller;

use tt\debug\Error;

abstract class Controller {

	/**
	 * @return string HTML
	 */
	public function runWeb(){
		new Error("runWeb is not defined in "."");
		return "";
	}

	/**
	 * @return string plaintext
	 */
	public function runCli(){
		new Error("runCli is not defined in "."");
		return "";
	}

	/**
	 * @return array JSON
	 */
	public function runAjax(){
		new Error("runAjax is not defined in "."");
		return null;
	}

}