<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\alias;

use tt\core\database\Database;
use tt\coremodule\dbmodell\core_pages;

class DB {

	public static function insertAssoc($table, $data_set) {
		return Database::getPrimary()->insertAssoc($table, $data_set);
	}

	public static function select($query, $substitutions = null) {
		return Database::getPrimary()->select($query, $substitutions);
	}

	public static function select_single($query, $substitutions = null) {
		return Database::getPrimary()->select_single($query, $substitutions);
	}

	public static function insert($query, $substitutions = null) {
		return Database::getPrimary()->insert($query, $substitutions);
	}

	public static function quote($string) {
		return Database::getPrimary()->quote($string);
	}

	public static function getTableName_core_pages() {
		return core_pages::getSingleton()->getTableName2();
	}

}