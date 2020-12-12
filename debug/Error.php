<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\debug;

class Error {

	/**
	 * @param string $message Errormessage
	 */
	public function __construct($message) {
		$report = $message;
		$report .= implode("\n",DebugTools::backtrace());
		$report = "<pre>$report</pre>";
		echo $report;
		exit;
	}

	public static function fromException(\Exception $e) {
		return new Error($e->getMessage());
	}

}