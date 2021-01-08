<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\page;

class PG {

	public static function addMessage(Message $message) {
		Page::getInstance()->addMessage($message);
	}

	/**
	 * @deprecated
	 * core check
	 * demo check
	 */
	public static function echoAndQuit($html = "") {
		Page::echoAndQuit($html);
	}

	public static function deliver() {
		Page::getInstance()->deliver();
	}

	public static function add($node) {
		return Page::getInstance()->add($node);
	}

}