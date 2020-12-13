<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\autoload;

use tt\config\Config;
use tt\debug\DebugTools;
use tt\debug\Error;
use tt\service\ServiceEnv;

class Autoloader {

	private static $initialized = false;
	private static $abort_on_error = true;

	public static function init(){
		if(self::$initialized)return;

		$autloloader = new Autoloader();
		$autloloader->register();

		self::$initialized = true;
	}

	public function multipleAutoloader(){
		self::$abort_on_error = false;
	}

	private function register(){
		spl_autoload_register(function ($class_name) {

			$matches_tt = preg_match("/^tt\\\\(.*)/", $class_name, $matches);
			if (!$matches_tt){
				return Autoloader::notFound($class_name . " (doesn't match ^tt\\)");
			}
			$name = $matches[1];

			/*
			 * Special case: API
			 * You can override any class defined in tt/api.
			 * Just place a file with the same name in the project's config/api folder.
			 */
			if (preg_match("/^tt\\\\api\\\\(.*)\$/", $class_name, $matches)){
				require_once dirname(__DIR__).'/service/ServiceFiles.php';
				require_once dirname(__DIR__).'/config/Config.php';
				$name_api = $matches[1];
				$file_api = Config::getServerDir().'/api/'.$name_api.".php";
				if (file_exists($file_api)){
					require_once dirname(__DIR__) . '/service/ServiceEnv.php';
					require_once dirname(__DIR__) . '/debug/Error.php';

					require_once $file_api;

					//instanceof without instanziation:
					if (!ServiceEnv::reflectionInstanceof($class_name, "tt\\api_default\\$name_api")) {
						new Error("TT API class '$class_name' ($file_api) does not extend '\\tt\\api_default\\$name_api'!");
					}

					return true;
				}
			}

			/*
			 * Default case
			 */
			$file = dirname(__DIR__) . '/' . str_replace('\\', '/', $name) . '.php';
			if (!file_exists($file)) {
				return Autoloader::notFound($class_name, 1);
			} else {
				require_once $file;
			}

			return true;
		});

	}

	private static function notFound($class, $backtrace_hint=false) {
		if(!Autoloader::$abort_on_error)return false;
		require_once dirname(__DIR__) . '/debug/Error.php';
		new Error("Can't autoload \"$class\""
			.($backtrace_hint===false?"!":" in ".DebugTools::backtraceLine($backtrace_hint+1))
		);
		return null;
	}

}