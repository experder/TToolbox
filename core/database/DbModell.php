<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

use tt\service\Error;

abstract class DbModell {

	protected $id;

	/**
	 * @param array $data
	 */
	public function __construct($data) {
		if (is_array($data)) {
			$this->setData($data);
		}
	}

	public function setData($data_array) {
		if (!is_array($data_array)) return;
		$all_fields = get_object_vars($this);

		foreach ($data_array as $key => $value) {
			if (!array_key_exists($key, $all_fields)) {
				new Error("Skipped key '$key' when setting data for: " . get_class($this));
			} else {
				$this->$key = $value;
			}
		}

	}

	/**
	 * @return string
	 * TODO: Not static!
	 */
	public static function getTableName() {
		return "UNDEFINED!";
	}

}