<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

/**
 * @deprecated
 */
class DB {

	/**
	 * @deprecated
	 */
	public static function insertAssoc($table, $data_set) {
		return Database::getPrimary()->insertAssoc($table, $data_set);
	}

	/**
	 * @deprecated
	 */
	public static function select($query, $substitutions = null) {
		return Database::getPrimary()->select($query, $substitutions);
	}

	/**
	 * @deprecated
	 */
	public static function insert($query, $substitutions = null) {
		return Database::getPrimary()->insert($query, $substitutions);
	}

	/**
	 * @deprecated
	 */
	public static function quote($string) {
		if ($string === null) return "NULL";
		if (is_numeric($string)) return $string;
		if (is_bool($string)) return $string ? "TRUE" : "FALSE";
		return Database::getPrimary()->getPdo()->quote($string);
	}

}