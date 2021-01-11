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
			$this->pdo->query('SET NAMES utf8');
		} catch (\Exception $e) {
			if ($e instanceof \PDOException) {
				if ($e->getCode() === 2002/*php_network_getaddresses: getaddrinfo failed*/) {
					new Error("Host unknown! $this->host");
				} else if ($e->getCode() === 1045/*Access denied*/) {
					new Error("Access denied! $user@$this->host");
				} else if ($e->getCode() === 1044/*Access denied for user to database*/) {
					new Error("Access denied to database '$this->dbname'!");
				} else if ($e->getCode() === 1049/*Unknown database*/) {
					Installer::initDatabase($this->dbname, $this->host, $user, $password);
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


}