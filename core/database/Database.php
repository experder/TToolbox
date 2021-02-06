<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

use tt\core\CFG;
use tt\core\Config;
use tt\install\Installer;
use tt\service\debug\DebugQuery;
use tt\service\debug\DebugTools;
use tt\service\debug\Stats;
use tt\service\Error;
use tt\service\ServiceArrays;

class Database {

	/**
	 * Returns id value of the INSERTed set of data.
	 */
	const RETURN_LASTINSERTID = 1;
	/**
	 * Returns result of the SELECT query as an associative array.
	 */
	const RETURN_ASSOC = 2;
	/**
	 * Returns the number of rows affected by the last query (UPDATE or DELETE).
	 */
	const RETURN_ROWCOUNT = 3;

	/**
	 * @var \PDO $pdo
	 */
	private $pdo;

	/**
	 * @var string $dbname
	 */
	private $dbname;
	private $host;

	/** @var Database $primary */
	static private $primary = null;

	public static function isPrimarySet() {
		return self::$primary !== null;
	}

	public static function getPrimary() {
		if (self::$primary === null) {
			new Error("ERROR_DB_NOT_INITIALIZED");
		}
		return self::$primary;
	}

	/**
	 * @param string $host
	 * @param string $dbname
	 * @param string $user
	 * @param string $password
	 */
	public function __construct($host, $dbname, $user, $password) {
		$this->host = $host;
		$this->dbname = $dbname;
		$this->initPdo($user, $password);
	}

	private function initPdo($user, $password) {
		try {
			$this->pdo = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $user, $password);
			$this->pdo->query('SET NAMES utf8;');
		} catch (\Exception $e) {
			if ($e instanceof \PDOException) {
				if ($e->getCode() === 2002/*php_network_getaddresses: getaddrinfo failed*/) {
					new Error("Host unknown! $this->host");
				} else if ($e->getCode() === 1045/*Access denied*/) {
					new Error("Access denied! $user@$this->host");
				} else if ($e->getCode() === 1044/*Access denied for user to database*/) {
					new Error("Access denied to database '$this->dbname'!");
				} else if ($e->getCode() === 1049/*Unknown database*/) {
					Installer::initDatabaseGui($this->dbname, $this->host, $user, $password);
				}
			}
			Error::fromException($e);
		}
	}

	public static function init($host, $dbname, $user, $password) {

		if (self::$primary !== null) {
			new Error("Database is already initialized!");
		}

		self::$primary = new Database($host, $dbname, $user, $password);

		return self::$primary;
	}

	public function insert($query, $substitutions = null) {
		return $this->_query($query, $substitutions, self::RETURN_LASTINSERTID);
	}

	public function select($query, $substitutions = null) {
		return $this->_query($query, $substitutions, self::RETURN_ASSOC);
	}

	public function insertAssoc($table, $data_set) {

		$keys_array = array_keys($data_set);
		$keys_prefixed = ServiceArrays::prefixValues(':', $keys_array);

		$substitutions = array();
		foreach ($data_set as $key => $value) {
			$substitutions[':' . $key] = $value;
		}

		$keys = implode(",", $keys_array);
		$values = implode(",", $keys_prefixed);
		return $this->insert("INSERT INTO $table ($keys) VALUES ($values);", $substitutions);
	}

	/**
	 * @param string $query
	 * @param array  $substitutions
	 * @param int    $return_type Database::RETURN_...
	 * @param int    $cutBacktrace
	 * @return string|array|int|null
	 */
	public function _query($query, $substitutions = null, $return_type = 0, $cutBacktrace=0) {
		$statement = $this->pdo->prepare($query);
		$ok = @$statement->execute($substitutions);
		$compiledQuery = $this->debuginfo($statement, $query);
		if (!$ok) {
			$this->error_handling($statement, $cutBacktrace+1, $compiledQuery);
			return null;
		}
		switch ($return_type) {
			case self::RETURN_LASTINSERTID:
				return $this->pdo->lastInsertId();
				break;
			case self::RETURN_ASSOC:
				return $statement->fetchAll(\PDO::FETCH_ASSOC);
				break;
			case self::RETURN_ROWCOUNT:
				return $statement->rowCount();
				break;
			default:
				return null;/*No return type specified*/
				break;
		}
	}

	private function debuginfo(\PDOStatement $statement, $query) {
		if (!CFG::DEVMODE()) {
			return null;
		}
		ob_flush();
		ob_start();
		$statement->debugDumpParams();
		$debugDump = ob_get_clean();
		$compiled_query = DebugTools::getCompiledQueryFromDebugDump($debugDump);
		if (!$compiled_query) {
			$compiled_query = ($debugDump ?: $query);
		}

		Stats::getSingleton()->addQuery(new DebugQuery($compiled_query));
		return $compiled_query;
		if (Config::$DEVMODE) {
			$backtrace = debug_backtrace();


			$caller = (isset($backtrace[$backtrace_depth + 1]['function']) ? $backtrace[$backtrace_depth + 1]['function'] . " " : "")
				. "( " . Debug::backtrace($backtrace_depth + 1, "\n", false) . " )";

			$core_query_class = "";
			if ($caller2 = in_array(str_replace('\\', '/', $caller), Debug::get_core_queries())) {
				$core_query_class = " core_query_class";
				Debug::$queries_corequeries_count++;
				Debug::mark_core_query_checked($caller2);
			}

			$query_html = (new Html("span", $caller, array("class" => "detail_functionSource$core_query_class")))
				. "\n" . (new Html("span", htmlentities($compiled_query), array("class" => "detail_sqlDump$core_query_class")));
			Debug::$queries[] = $query_html;
		}
	}

	private function error_handling(\PDOStatement $statement, $cut_backtrace = 0, $compiledQuery) {
		$eInfo = $statement->errorInfo();
		$errorCode = $eInfo[0];
		$errorInfo = "[$errorCode] " . $eInfo[2];

		if ($errorCode == "HY093") {
			new Error("Invalid parameter number: parameter was not defined", $cut_backtrace + 1);
		}

		new Error($errorInfo."\n-----------\n".$compiledQuery, $cut_backtrace + 1);
	}

	public function getPdo() {
		return $this->pdo;
	}

}