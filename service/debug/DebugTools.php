<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\debug;

use tt\service\Error;

class DebugTools {

	/**
	 * @var DebugQuery[] $queries
	 */
	private $queries = array();

	/**
	 * @var DebugTools $singleton
	 */
	private static $singleton = null;

	private function __construct() {
	}

	public static function getSingleton(){
		if(self::$singleton===null){
			self::$singleton=new DebugTools();
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

	/**
	 * Examples:
	 * $backtrace_plain = implode("\n", DebugTools::backtrace());
	 * $backtrace_html = implode("<br>", DebugTools::backtrace());
	 * @param int $cut_from_the_start Number of steps after the error occured.
	 * @return string[]
	 */
	public static function backtrace($cut_from_the_start = 0) {
		$caller = array();
		$backtrace = debug_backtrace();
		if (!$backtrace || !is_array($backtrace)) {
			if (Error::$recursion_protection)
				new Error("TODO");//Is there any reason for this?
			return array("unknown caller");
		}
		if ($cut_from_the_start) {
			if (isset($backtrace[$cut_from_the_start])) {
				$backtrace = array_slice($backtrace, $cut_from_the_start);
			} else {
				if (Error::$recursion_protection)
					new Error("Fix your code.31");
				return array("unset depth $cut_from_the_start");
			}
		}
		foreach ($backtrace as $row) {
			if (!isset($row["file"]) || !isset($row["line"])) continue;
			$caller[] = $row["file"] . ':' . $row["line"];
		}
		if (!$caller) {
			if (Error::$recursion_protection)
				new Error("TODO");//Is there any reason for this?
			return array("unknown_caller");
		}

		return $caller;
	}

	/**
	 * @param string $dump
	 * @return string|false
	 */
	public static function getCompiledQueryFromDebugDump($dump) {
		$compiled_query = false;
		if (preg_match("/^SQL: \\[[0-9]*?\\] (.*?)\nParams:  0$/", $dump, $matches)) {
			$compiled_query = $matches[1];
		} else {
			preg_match("/\\nSent SQL: \\[([0-9]*?)\\] /", $dump, $matches);
			if (isset($matches[1])) {
				$count = $matches[1];
				preg_match("/\\nSent SQL: \\[$count\\] (.{{$count}})\nParams:/s", $dump, $matches);
				if (isset($matches[1])) {
					$compiled_query = $matches[1];
				}
			}
		}
		return $compiled_query;
	}

}