<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\config;

use tt\debug\Error;
use tt\service\ServiceFiles;

class Config {

	public static $init_server = true;
	public static $init_server_file = "../../TTconfig/init_server.php";

	public static function initWeb(){

		require_once dirname(__DIR__).'/autoload/Autoloader.php';
		\tt\autoload\Autoloader::init();

		if (self::$init_server) Config::initServer();

	}

	public static function initServer(){
		$cfg_file = __DIR__.'/'.self::$init_server_file;
		$cfg_file = ServiceFiles::cleanupRelativePath($cfg_file);

		if (!file_exists($cfg_file)){
			new Error("Server specific config file not found. $cfg_file");
		}

		require_once $cfg_file;

		return true;
	}

}