<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

use tt\install\Installer;
use tt\service\Error;

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

		//TODO:self::$primary->checkVersion();

		return self::$primary;
	}

	private function checkVersion() {

		echo "97!";

	}

	/**
	 * @param string $query
	 * @param array  $substitutions
	 * @param int    $return_type Database::RETURN_...
	 * @return string|array|int|null
	 */
	public function _query($query, $substitutions=null, $return_type=0) {
		$statement = $this->pdo->prepare($query);
		$ok = @$statement->execute($substitutions);
		$this->debuginfo($statement, $query);
		if (!$ok) {
			$this->error_handling($statement, $query);
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
		return;
		if (Config::$DEVMODE) {
			$backtrace = debug_backtrace();

			ob_flush();
			ob_start();
			$statement->debugDumpParams();
			$debugDump = ob_get_clean();
			$compiled_query = self::get_compiled_query_from_debugDump($debugDump);
			if (!$compiled_query) {
				$compiled_query = ($debugDump ?: $query);
			}

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

	private function /*TODO:*/error_handling(\PDOStatement $statement, $query) {
		new Error(print_r($statement->errorInfo(),1));
		return;
		$eInfo = $statement->errorInfo();
		$errorCode = $eInfo[0];
		$errorInfo = "[$errorCode] " . $eInfo[2];
		$errorType = Error::TYPE_SQL;
		if (!$eInfo[2]) {
			if ($errorCode === 'HY093'/*Invalid parameter number: parameter was not defined*/) {
				$errorInfo = "Invalid parameter number: parameter was not defined";
			}
		}
		if ($errorCode === "42S02"/*Unknown table*/) {
			$errorType = Error::TYPE_TABLE_NOT_FOUND;
		}
		ob_flush();
		ob_start();
		$statement->debugDumpParams();
		$debugDump = ob_get_clean();
		$compiled_query = self::get_compiled_query_from_debugDump($debugDump);
		if (!$compiled_query) {
			$compiled_query = ($debugDump ?: $query);
		}

		$this->error = new Error($errorType, $errorInfo, $compiled_query, $backtrace_depth + 1, $halt_on_error);
	}

}