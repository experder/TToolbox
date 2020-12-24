<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

class DebugTools {

	/**
	 * Examples:
	 * $backtrace_plain = implode("\n", DebugTools::backtrace());
	 * $backtrace_html = implode("<br>", DebugTools::backtrace());
	 * @param int $cut_from_the_start Number of steps after the error occured.
	 * @return string[]
	 */
	public static function backtrace($cut_from_the_start = 0) {
		$caller = array();
		$backtrace = debug_backtrace();
		if (!$backtrace || !is_array($backtrace)) {
			if (Error::$recursion_protection)
				new Error("TODO");//Is there any reason for this?
			return array("unknown caller");
		}
		if ($cut_from_the_start) {
			if (isset($backtrace[$cut_from_the_start])) {
				$backtrace = array_slice($backtrace, $cut_from_the_start);
			} else {
				if (Error::$recursion_protection)
					new Error("Fix your code.31");
				return array("unset depth $cut_from_the_start");
			}
		}
		foreach ($backtrace as $row) {
			if (!isset($row["file"]) || !isset($row["line"])) continue;
			$caller[] = $row["file"] . ':' . $row["line"];
		}
		if (!$caller) {
			if (Error::$recursion_protection)
				new Error("TODO");//Is there any reason for this?
			return array("unknown_caller");
		}

		return $caller;
	}

}