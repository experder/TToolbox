<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule\dbmodell;

use tt\core\database\DbModell;
use tt\service\Error;

class core_config extends DbModell {

	private static $table_name = null;

	const ROW_id = "id";
	const ROW_idstring = "idstring";
	const ROW_module = "module";
	const ROW_userid = "userid";
	const ROW_content = "content";

	/**
	 * @return string
	 */
	public static function getTableName() {
		if(self::$table_name===null){
			new Error("Table name not set!");
		}
		return self::$table_name;
	}

	/**
	 * @param string $table_name
	 */
	public static function setTableName($table_name) {
		self::$table_name = $table_name;
	}

}