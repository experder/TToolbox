<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

class Request {

	public static function value($key, $default = null) {
		if (isset($_REQUEST[$key])) {
			return $_REQUEST[$key];
		}
		return $default;
	}

	public static function value_unset($key, $default = null) {
		$val = self::value($key, $default);
		unset($_REQUEST[$key]);
		return $val;
	}

}