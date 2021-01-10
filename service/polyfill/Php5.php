<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\polyfill;

/**
 * Features that have been introduced in PHP 5.4, 5.5 and 5.6
 */
class Php5 {

	/**
	 * Determines classname of the calling class.
	 *
	 * Can be used instead of the constant "class" (Since 5.5):
	 * PHP 5.3:
	 *   Myclass::getClass();
	 *   class Myclass {
	 *     public static function getClass() {
	 *       return \tt\service\polyfill\Php5::get_class();
	 *     }
	 *   }
	 * PHP 5.5:
	 *   Myclass::class;
	 *
	 * @return string name of the calling class
	 */
	public static function get_class() {
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
		$class = $backtrace[1]["class"];
		return $class;
	}

}