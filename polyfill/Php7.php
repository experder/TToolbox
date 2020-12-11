<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\polyfill;

/**
 * Features that have been introduced in PHP 7
 */
class Php7 {

	/**
	 * Example: $session_id = bin2hex(\tt\polyfill\Php7::random_bytes(10));
	 *
	 * @param int $length
	 * @return string
	 */
	public static function random_bytes($length) {
		$string = "";
		for ($i = 0; $i < $length; $i++) {
			$string .= chr(rand(0, 255));
		}
		return $string;
	}

}