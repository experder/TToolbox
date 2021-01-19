<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\debug;

use tt\core\Config;

class Stats {

	/**
	 * @var DebugQuery[] $queries
	 */
	private $queries = array();

	/**
	 * @var Stats $singleton
	 */
	private static $singleton = null;

	private function __construct() {
	}

	public static function getSingleton(){
		if(self::$singleton===null){
			self::$singleton=new Stats();
		}
		return self::$singleton;
	}

	/**
	 * @return DebugQuery[]
	 */
	public function getQueries() {
		return $this->queries;
	}

	/**
	 * @param DebugQuery $query
	 */
	public function addQuery($query) {
		$this->queries[] = $query;
	}

	public static function getStatsQueries(){
		$queries = array();
		foreach (self::getSingleton()->getQueries() as $query){
			$queries[] = $query->toHtml();
		}
		return new StatsElement(implode("",$queries),"statsQueries");
	}

	public static function getStatsRuntime(){
		return new StatsElement("".Config::$startTimestamp, "statsRuntime");
	}

	/**
	 * @return StatsElement[]
	 */
	public static function getAllStats(){
		return array(
			Stats::getStatsQueries(),
			Stats::getStatsRuntime(),
		);
	}

	public static function getAllStatsHtml(){
		$html = array();
		foreach (self::getAllStats() as $element){
			$html[] = $element->toHtml();
		}
		$html = "<div class='tt_stats'>".implode("\n",$html)."</div>";
		return $html;
	}

}