<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

class ServiceDateTime {

	public static function timeElapsedInWords($timestamp, $vor = "vor", $now = true) {
		if ($now === true) {
			$now = time();
		}
		$time_midnight = strtotime("noon", $timestamp);
		$now_midnight = strtotime("noon", $now);

		if ($now_midnight > $time_midnight) {
			#$elapsed_days = round($elapsed_seconds/86400);
			$elapsed_days = round(($now_midnight - $time_midnight) / 86400/* 1 day = 24*60*60 */);
			return $vor . " " . $elapsed_days . " " . ($elapsed_days < 2 ? "Tag" : "Tagen");
		}

		$elapsed_seconds = $now - $timestamp;

		if ($elapsed_seconds > 7200/* 2 hours = 2*60*60 */) {
			$elapsed_hours = $elapsed_seconds / 3600;
			return $vor . " " . round($elapsed_hours) . " " . "Stunden";
		}

		if ($elapsed_seconds > 120/* 2 minutes */) {
			$elapsed_minutes = $elapsed_seconds / 60;
			return $vor . " " . round($elapsed_minutes) . " " . "Minuten";
		}

		return $vor . " " . $elapsed_seconds . " " . "Sekunden";
	}

}
