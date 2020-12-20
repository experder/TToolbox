<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\page;

class Message {

	const TYPE_INFO = "info";
	const TYPE_ERROR = "error";
	const TYPE_CONFIRM = "ok";
	const TYPE_QUESTION = "ask";

	private $message;
	private $type;

	public function __construct($type, $message) {
		$this->type = $type;
		$this->message = $message;
	}

	public function toHtml(){
		return "<div class='message $this->type'>".$this->message."</div>";
	}

}