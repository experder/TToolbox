<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run;

use tt\service\Error;

class Runner {

	/**
	 * @param array $args
	 * @return string plaintext
	 */
	public function runCli(array $args = array()) {
		new Error("runCli is not defined in " . get_class($this)
			. ' / ' . count($args));
		return false;
	}

	/**
	 * @param string $cmd
	 * @param array  $data
	 * @return ApiResponseHtml
	 */
	public function runApi($cmd = null, array $data = array()) {
		new Error("runApi is not defined in " . get_class($this)
			. " / " . $cmd
			. " / " . count($data));
		return null;
	}

	/**
	 * @return string|array HTML
	 */
	public function runWeb() {
		new Error("runWeb is not defined in " . get_class($this));
		return false;
	}

	protected function requiredFieldsFromData($data, $fieldlist, $return_associative = true) {
		$fields = array();
		foreach ($fieldlist as $key) {
			if (!isset($data[$key])) {
				new Error(get_class($this) . ": Required data not received: '$key'", 1);
			}
			if ($return_associative) {
				$fields[$key] = $data[$key];
			} else {
				$fields[] = $data[$key];
			}
		}
		return $fields;
	}

}