<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule\dbmodell;

use tt\core\Config;
use tt\core\database\DB;
use tt\core\database\DbModell;
use tt\core\navigation\NaviEntry;

class core_navigation extends DbModell {

	private static $table_name = null;

	const ROW_id = "id";
	const ROW_pageid = "pageid";
	const ROW_title = "title";
	const ROW_route = "route";

	/**
	 * @return string
	 */
	public static function getTableName() {
		if(self::$table_name===null){
			self::$table_name = Config::get(Config::DB_CORE_PREFIX) . '_navigation';
		}
		return self::$table_name;
	}

	public static function sql_001_create(){
		return "CREATE TABLE " . self::getTableName() . " ("
			. " `" . self::ROW_id . "` INT(11) NOT NULL AUTO_INCREMENT,"
			. " `" . self::ROW_pageid . "` varchar(200) NOT NULL,"
			. " `" . self::ROW_title . "` varchar(80) DEFAULT NULL,"
			. " `" . self::ROW_route . "` varchar(200) DEFAULT NULL,"
			. " PRIMARY KEY (`" . self::ROW_id . "`),"
			. " UNIQUE KEY `pageid` (`" . self::ROW_pageid . "`)"
			. ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	}

	public static function sql_select($where = ""){
		$data = DB::select("SELECT * FROM " . self::getTableName() . " ". $where);
		$navi = array();
		foreach ($data as $row){
			$navi[$row[self::ROW_pageid]] = new NaviEntry($row);
		}
		return $navi;
	}

}