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

	public static $apiClass = null;

	/**
	 * @var Stats $singleton
	 */
	private static $singleton = null;

	private function __construct() {
	}

	public static function getSingleton() {
		if (self::$singleton === null) {
			self::$singleton = new Stats();
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

	public static function getStatsQueries() {
		$queries = array();
		foreach (self::getSingleton()->getQueries() as $query) {
			array_unshift($queries, $query->toHtml());
		}
		$title = "<b>" . count($queries) . "</b> queries";
		return new StatsElement($title, implode("", $queries), "statsQueries divList");
	}

	public static function getStatsRuntime() {
		$duration = round((microtime(true) - Config::$startTimestamp) * 1000);
		$title = "<b>$duration</b> millis";
		return new StatsElement($title, null, "statsRuntime");
	}

	public static function getStatsPostdata() {
		$title = "<b>POST</b>";
		$stats = array();
		foreach ($_POST as $key => $value) {
			if (is_array($value)) {
				$value = "[ " . implode(", ", $value) . " ]";
			}
			$stats[] = "<div>[" . htmlentities($key) . "] => " . htmlentities($value) . "</div>";
		}
		return new StatsElement($title, implode("", $stats), "statsPostdata divList");
	}

	public static function getStatsAjax() {
		$title = "<b>AJAX</b>";
		$stats = array();

		if(self::$apiClass!==null){
			$stats[] = "<div>CLASS: ".self::$apiClass."</div>";
		}else{
			$stats[] = "<div>URI: ".$_SERVER['REQUEST_URI']."</div>";
		}

		$stats[] = "POST:";
		foreach ($_POST as $key => $value) {
			if (is_array($value)) {
				$value = "[ " . implode(", ", $value) . " ]";
			}
			$stats[] = "<div class='postrow'>" . htmlentities($key) . "=" . htmlentities($value) . "</div>";
		}

		return new StatsElement($title, implode("", $stats), "statsAjax");
	}

	/**
	 * @return StatsElement[]
	 */
	public static function getAllStats() {
		$all = array(
			self::getStatsQueries(),
			self::getStatsRuntime(),
		);
		if (isset($_POST) && $_POST) {
			$all[] = self::getStatsPostdata();
		}
		return $all;
	}

	/**
	 * @return StatsElement[]
	 */
	public static function getAllStats2() {
		$all = array(
			self::getStatsAjax(),
			self::getStatsQueries(),
			self::getStatsRuntime(),
		);
		return $all;
	}

	public static function getAllStatsHtml() {
		$html = array();
		foreach (self::getAllStats() as $element) {
			$html[] = $element->toHtml();
		}
		$html = "<div class='tt_stats'>" . implode("\n", $html) . "</div>";
		return $html;
	}

	public static function getAllStatsJson() {
		$json = array();
		foreach (self::getAllStats2() as $element) {
			$json[] = $element->toJson();
		}
		return $json;
	}

}