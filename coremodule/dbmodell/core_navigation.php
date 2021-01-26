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
use tt\run\Run;

class core_navigation extends DbModell {

	private static $table_name = null;

	/**
	 * @var string $pageid
	 */
	protected $pageid;
	const ROW_pageid = "pageid";
	/**
	 * @var string $title
	 */
	protected $title;
	const ROW_title = "title";
	/**
	 * @var bool $external
	 */
	protected $external;
	const ROW_external = "external";
	/**
	 * @var string $route
	 */
	protected $route;
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
			. " `id` INT(11) NOT NULL AUTO_INCREMENT,"
			. " `" . self::ROW_pageid . "` varchar(200) NOT NULL,"
			. " `" . self::ROW_title . "` varchar(80) DEFAULT NULL,"
			. " `" . self::ROW_external . "` tinyint(1) NOT NULL,"
			. " `" . self::ROW_route . "` varchar(200) DEFAULT NULL,"
			. " PRIMARY KEY (`id`),"
			. " UNIQUE KEY `pageid` (`" . self::ROW_pageid . "`)"
			. ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	}

	/**
	 * TODO: Generic!
	 * @return string
	 */
	public function sql_insert() {
		return "INSERT INTO " . self::getTableName() . " ("
			. "`" . self::ROW_pageid . "` ,"
			. "`" . self::ROW_title . "` ,"
			. "`" . self::ROW_external . "` ,"
			. "`" . self::ROW_route . "`"
			. ") VALUES ("
			. DB::quote($this->pageid) . ","
			. DB::quote($this->title) . ","
			. DB::quote($this->external) . ","
			. DB::quote($this->route)
			. ");";
	}

	public static function sql_select($where = ""){
		$data = DB::select("SELECT * FROM " . self::getTableName() . " ". $where);
		$navi = array();
		foreach ($data as $row){
			$navi[$row[self::ROW_pageid]] = new core_navigation($row);
		}
		return $navi;
	}

	/**
	 * @deprecated TODO
	 */
	public static function toSql($pageid, $title, $route=null){
		return self::toSql_insert($pageid, $title, $route);
	}
	/**
	 * @param string $pageid
	 * @param string $title
	 * @param string $route
	 * @param bool $external
	 * @return string SQL
	 */
	public static function toSql_insert($pageid, $title, $route=null, $external=false){
		$naviEntry = new core_navigation(array(
			"pageid"=>$pageid,
			"title"=>$title,
			"external"=>$external,
			"route"=>$route,
		));
		return $naviEntry->sql_insert();
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getRoute() {
		return $this->route;
	}

	/**
	 * @return string
	 */
	public function getPageId() {
		return $this->pageid;
	}

	public function getHtml($highlighted_id) {

		$title = htmlentities($this->title);
		if($this->title===null){
			$title=$this->pageid;
		}

		if($this->pageid && $this->pageid==$highlighted_id){
			$title = "<b>$title</b>";
		}

		if($this->external){
			$url = $this->route;
		}else{
			$url = Run::getWebUrl($this->pageid);
		}

		return " [<a href='$url'>$title</a>]";

	}

}