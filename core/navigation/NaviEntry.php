<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\navigation;

use tt\core\database\DB;
use tt\coremodule\dbmodell\core_navigation;

class NaviEntry {

	/**
	 * @var string $pageid
	 */
	protected $pageid;
	/**
	 * @var string $title
	 */
	protected $title;
	/**
	 * @var string $route
	 */
	protected $route;

	/**
	 * @param string $pageid
	 * @param string $title
	 * @param string $route
	 */
	private function __construct($pageid, $title, $route) {
		$this->pageid = $pageid;
		$this->title = $title;
		$this->route = $route;
	}

	public function sql_insert() {
		return "INSERT INTO " . core_navigation::getTableName() . " ("
			. "`" . core_navigation::ROW_pageid . "` ,"
			. "`" . core_navigation::ROW_title . "` ,"
			. "`" . core_navigation::ROW_route . "`"
			. ") VALUES ("
			. DB::quote($this->pageid) . ","
			. DB::quote($this->title) . ","
			. DB::quote($this->route)
			. ");";
	}

	/**
	 * @param string $pageid
	 * @param string $title
	 * @param string $route
	 * @return string SQL
	 */
	public static function toSql($pageid, $title, $route=null){
		$naviEntry = new NaviEntry($pageid, $title, $route);
		return $naviEntry->sql_insert();
	}

}