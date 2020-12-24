<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core;

use tt\service\Error;
use tt\service\ServiceEnv;
use tt\service\ServiceStrings;

class Autoloader {

	private static $initialized = false;
	private static $abort_on_error = true;

	public static function init(){
		if(self::$initialized)return;

		$autloloader = new Autoloader();
		$autloloader->register();

		self::$initialized = true;
	}

	public static function multipleAutoloader(){
		self::$abort_on_error = false;
	}

	private function register(){
		spl_autoload_register(function ($class_name) {
			require_once dirname(__DIR__).'/service/ServiceStrings.php';
			require_once dirname(__DIR__).'/core/Config.php';
			$class_name = ServiceStrings::classnameSafe($class_name);

			// Case API
			// You can override any class defined in tt/api.
			// Just place a file with the same name in the project's config/api folder (CFG_DIR.'/api').
			if (Autoloader::loadApiClass($class_name)) return true;

			// Case TT
			if (Autoloader::loadTtNamespace($class_name)) return true;

			// Case PROJECT
			if (Autoloader::loadProjectNamespace($class_name)) return true;

			return Autoloader::notFound($class_name, 2);
		});

	}

	private static function loadApiClass($class_name) {
		if (!preg_match("/^tt\\\\api\\\\(.*)\$/", $class_name, $matches))return false;

		$name_api = $matches[1];

		$file_api = Config::get(Config::CFG_DIR).'/api/'.$name_api.".php";
		if (!file_exists($file_api))return false;

		require_once $file_api;

		require_once dirname(__DIR__) . '/service/ServiceEnv.php';
		if (!ServiceEnv::reflectionInstanceof($class_name, "tt\\api_default\\$name_api")) {
			require_once dirname(__DIR__) . '/debug/Error.php';
			new Error("TT API class '$class_name' ($file_api) does not extend '\\tt\\api_default\\$name_api'!");
		}

		return true;
	}

	private static function loadTtNamespace($class_name) {
		if (!preg_match("/^tt\\\\(.*)/", $class_name, $matches))return false;

		$name = $matches[1];

		$file = dirname(__DIR__) . '/' . str_replace('\\', '/', $name) . '.php';

		if (!file_exists($file))return false;

		require_once $file;

		return true;
	}

	private static function loadProjectNamespace($class_name) {
		if (Config::getIfSet(Config::PROJ_NAMESPACE_ROOT, false) === false) return false;

		$file = Autoloader::classnameMatchesProjectNamespace($class_name);

		if (!$file) return false;

		if (!file_exists($file))return false;

		require_once $file;

		return true;
	}

	public static function classnameMatchesProjectNamespace($classname){

		$PROJ_NAMESPACE_ROOT = Config::get(Config::PROJ_NAMESPACE_ROOT);

		if (!preg_match("/^$PROJ_NAMESPACE_ROOT\\\\(.*)\$/", $classname, $matches))return false;

		$name = $matches[1];

		$file = str_replace('\\', '/', Config::get(Config::CFG_PROJECT_DIR) . '/' . $name . '.php');

		return $file;
	}

	private static function notFound($class, $cutBacktrace=0) {
		if(!Autoloader::$abort_on_error)return false;
		require_once dirname(__DIR__) . '/debug/Error.php';
		new Error("Can't autoload \"$class\"!",$cutBacktrace+1);
		return null;
	}

}