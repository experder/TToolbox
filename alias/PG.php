<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\alias;

use tt\core\page\Message;
use tt\core\page\Page;

class PG {

	public static function addMessage(Message $message) {
		Page::getInstance()->addMessage($message);
	}

	public static function deliver() {
		Page::getInstance()->deliver();
	}

	/**
	 * @param mixed $node must be of a type described in Node::check_type
	 * @see \tt\core\page\Node::check_type
	 * @return Page $this
	 */
	public static function add($node) {
		return Page::getInstance()->add($node);
	}

	public static function getId(){
		return Page::getInstance()->getId();
	}

}