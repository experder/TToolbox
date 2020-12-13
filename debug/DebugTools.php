<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\debug;

class DebugTools {

	/**
	 * Examples:
	 * $backtrace_plain = implode("\n", DebugTools::backtrace());
	 * $backtrace_html = implode("<br>", DebugTools::backtrace());
	 * @return string[]
	 */
	public static function backtrace() {
		$caller = array();
		$backtrace = debug_backtrace();
		if (!$backtrace || !is_array($backtrace)) {
			return array("unknown_caller");
		}
		foreach ($backtrace as $row) {
			if (!isset($row["file"]) || !isset($row["line"])) continue;
			$caller[] = $row["file"] . ':' . $row["line"];
		}
		if (!$caller) {
			$caller[] = "unknown_caller";
		}

		return $caller;
	}

	public static function backtraceLine($steps=0) {
		if(!is_int($steps))return false;
		$steps+=1;
		$backtrace = self::backtrace();
		if(!isset($backtrace[$steps]))return false;
		return $backtrace[$steps];
	}

}