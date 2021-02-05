<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule\dbmodell;

use tt\core\CFG;
use tt\core\Config;
use tt\core\database\DB;
use tt\core\database\DbModell;
use tt\core\navigation\Navigation;
use tt\run\Run;
use tt\service\Error;
use tt\service\ServiceStrings;

class core_pages extends DbModell {

	private static $table_name = null;

	const TYPE_web = 'web';
	const TYPE_api = 'api';
	const TYPE_ext = 'ext';
	const TYPE_int = 'int';
	const TYPE_sup = 'sup';

	/**
	 * @var string $pageid
	 */
	protected $pageid;
	const ROW_pageid = "pageid";
	/**
	 * @var string|null $title
	 */
	protected $title;
	const ROW_title = "title";
	/**
	 * @var string|null $parent
	 */
	protected $parent;
	const ROW_parent = "parent";
	/**
	 * @var string $type core_pages::TYPE_ ['web','api','ext','int','sup']
	 */
	protected $type;
	const ROW_type = "type";
	/**
	 * @var string|null $link
	 */
	protected $link;
	const ROW_link = "link";

	/**
	 * @var core_pages $parentEntry
	 */
	private $parentEntry = null;
	/**
	 * @var core_pages[] $childEntries
	 */
	private $childEntries = array();

	/**
	 * @return string
	 */
	public function getTableName2() {
		if (self::$table_name === null) {
			self::$table_name = Config::get(Config::DB_CORE_PREFIX) . '_pages';
		}
		return self::$table_name;
	}

	public static function sql_001_create() {
		return "CREATE TABLE " . self::getSingleton0()->getTableName2() . " ("
			. " `id` INT(11) NOT NULL AUTO_INCREMENT,"
			. " `" . self::ROW_pageid . "` VARCHAR(200) NOT NULL,"
			. " `" . self::ROW_title . "` VARCHAR(80) DEFAULT NULL,"
//			. " `" . self::ROW_parent . "` INT(11) DEFAULT NULL,"
			. " `" . self::ROW_parent . "` varchar(200) DEFAULT NULL,"
			. " `" . self::ROW_type . "` enum('web','api','ext','int','sup') NOT NULL,"
			. " `" . self::ROW_link . "` VARCHAR(200) DEFAULT NULL,"
			. " PRIMARY KEY (`id`),"
			. " UNIQUE KEY `pageid` (`" . self::ROW_pageid . "`),"
			. " KEY `parent` (`parent`)"
			. ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	}

//	public static function sql_002_constraint1() {
//		return "ALTER TABLE " . self::getTableName() . " ADD CONSTRAINT `core_pages_ibfk_1` FOREIGN KEY (`parent`) REFERENCES " . self::getTableName() . " (`id`);";
//	}

	public static function sql_002_constraint1() {
		return "ALTER TABLE " . self::getSingleton0()->getTableName2() . " ADD CONSTRAINT `core_pages_ibfk_1` FOREIGN KEY (`parent`) REFERENCES " . self::getSingleton0()->getTableName2() . " (`pageid`);";
	}

	/**
	 * TODO: Generic!
	 * @return string
	 */
	public function sql_insert() {
		return "INSERT INTO " . self::getSingleton0()->getTableName2() . " ("
			. "`" . self::ROW_pageid . "` ,"
			. "`" . self::ROW_title . "` ,"
			. "`" . self::ROW_parent . "` ,"
			. "`" . self::ROW_type . "` ,"
			. "`" . self::ROW_link . "`"
			. ") VALUES ("
			. DB::quote($this->pageid) . ","
			. DB::quote($this->title) . ","
			. DB::quote($this->parent) . ","
			. DB::quote($this->type) . ","
			. DB::quote($this->link)
			. ");";
	}

	public static function sql_select($where = "") {
		$data = DB::select("SELECT * FROM " . self::getSingleton0()->getTableName2() . " " . $where);
		$navi = array();
		foreach ($data as $row) {
			$navi[$row[self::ROW_pageid]] = new core_pages($row);
		}
		return $navi;
	}

	/**
	 * @param string      $pageid
	 * @param string      $type ['web','api','ext','int','sup']
	 * @param null|string $title
	 * @param null|string $link
	 * @param null|string $parent
	 * @return string SQL
	 */
	public static function toSql_insert($pageid, $type, $title=null, $link=null, $parent=null) {
		$naviEntry = new core_pages(array(
			"pageid" => $pageid,
			"title" => $title,
			"type" => $type,
			"link" => $link,
			"parent" => $parent,
		));
		return $naviEntry->sql_insert();
	}

	/**
	 * @return string|null
	 */
	public function getTitle() {
		return $this->title;
	}

	public function getBreadcrumbs(){
		$parent = $this->getParentEntry();
		if($parent!==false){
			$breads = $parent->getBreadcrumbs();
		}else{
			$breads = array();
		}
		$breads[] = $this;
		return $breads;
	}

	/**
	 * @return string|null
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @return string
	 */
	public function getPageId() {
		return $this->pageid;
	}

	/**
	 * @return string|null
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @return core_pages|false
	 */
	public function getParentEntry() {
		if($this->parentEntry===null){
			$this->parentEntry = Navigation::getInstance()->getEntryById($this->parent);
		}
		return $this->parentEntry;
	}

	/**
	 * @param core_pages $child
	 */
	public function addChildEntry($child) {
		$this->childEntries[] = $child;
	}

	/**
	 * @return core_pages[]
	 */
	public function getChildEntries() {
		return $this->childEntries;
	}

private static $temp_counter=0;
	/**
	 * @param bool $title
	 * @return string|null
	 */
	public function getHtmlInner($title) {
		if($this->title===null)return null;

		if ($this->type=='web'){
			$url = Run::getWebUrl($this->pageid);
		}else if ($this->type=='api'){
			$url = Config::get(Config::RUN_ALIAS_API) . $this->link;
		}else if ($this->type=='ext'){
			$url = $this->link;
		}else if ($this->type=='int'){
			$url = $this->link;
		}else if ($this->type=='sup'){
			$url = false;
		}else{
			new Error("Unknown page type of '$this->pageid'!");
			$url=null;
		}

		$title_ = htmlentities($this->title);

		$titleTag = ($title?$title_:"");
		if(CFG::DEVMODE())$titleTag.=" [id_".ServiceStrings::cssClassSafe($this->pageid)."]";

		$titleVal = $titleTag?"title='$titleTag'":"";

		if($url!==false){
			$link = "<a href='".htmlentities($url)."' $titleVal>$title_</a>";
		}else{
			$link = "<span class='pseudo_a' $titleVal>$title_</span>";
		}

		return $link;
	}

	public function getHtml($highlighted_id) {
		if(self::$temp_counter++>100)return "RECURSION!!!";//TODO:Recursion in navigation
		if($this->title===null && !$this->childEntries)return false;

		//Link:
		$inner = $this->getHtmlInner($this->parent===null);

		//Children:
		$html=array($inner);
		if($this->childEntries){
			$childsHtml = array();

			foreach ($this->childEntries as $entry){
				$childHtml = $entry->getHtml($highlighted_id);
				if($childHtml===false && $this->title===false)return false;
				$childsHtml[] = $childHtml;
			}

			$html[] = "<ul>".implode("\n", $childsHtml)."</ul>";
		}

		//Highlighting:
		$crumbs = Navigation::getInstance()->getBreadcrumbs($highlighted_id);
		$high = ($crumbs && in_array($this, $crumbs));
		$highclass = $high?" high":"";

		//Ouput:
		$cssId = "id_".ServiceStrings::cssClassSafe($this->pageid);
		return "<li class='$cssId$highclass'>".implode("\n", $html)."</li>";
	}

}