<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core;

use tt\install\Installer;
use tt\service\Error;
use tt\service\ServiceEnv;
use tt\service\ServiceStrings;

class Autoloader {

	private static $initialized = false;
	private static $abort_on_error = true;

	private static $all_namespace_roots = null;

	public static function init() {
		if (self::$initialized) return;

		$autloloader = new Autoloader();
		$autloloader->register();

		self::$initialized = true;
	}

	public static function multipleAutoloader() {
		self::$abort_on_error = false;
	}

	private function register() {
		spl_autoload_register(function ($class_name) {
			require_once dirname(__DIR__) . '/service/ServiceStrings.php';
			require_once dirname(__DIR__) . '/core/Config.php';
			$class_name = ServiceStrings::classnameSafe($class_name);

			//Namespace: TT\API
			if (Autoloader::loadApiClass($class_name)) return true;

			foreach (Autoloader::getAllNamespaceRoots() as $namespace => $folder) {
				if (Autoloader::requireFileInNamespace($class_name, $namespace, $folder)) return true;
			}

			return Autoloader::notFound($class_name, 2);
		});

	}

	private static function loadApiClass($class_name) {
		if (!preg_match("/^tt\\\\api\\\\(.*)\$/", $class_name, $matches)) return false;
		require_once dirname(__DIR__) . '/service/ServiceEnv.php';

		$name_api = $matches[1];

		$file_api = Config::getChecked(Config::CFG_API_DIR) . '/' . $name_api . ".php";
		if (!file_exists($file_api)) {
			//Installer: Create API file stubs
			Installer::initApiClass($name_api, $file_api);
		}

		require_once $file_api;

		if (!ServiceEnv::reflectionInstanceof($class_name, "tt\\core\\api_default\\$name_api")) {
			require_once dirname(__DIR__) . '/service/Error.php';
			new Error(
				"TT API class '$class_name' ($file_api) does not extend '\\tt\\core\\api_default\\$name_api'!"
			);
		}

		return true;
	}

	private static function requireFileInNamespace($classname, $namespace_root, $folder) {
		$file = self::classnameMatchesNamespaceRoot($classname, $namespace_root, $folder);

		if (!$file) return false;

		if (!file_exists($file)) return false;

		require_once $file;

		return true;
	}

	public static function classnameMatchesAnyNamespaceRoot($classname) {
		foreach (self::getAllNamespaceRoots() as $namespace => $folder) {
			if (($file = self::classnameMatchesNamespaceRoot($classname, $namespace, $folder)) !== false) {
				return $file;
			}
		}
		return false;
	}

	private static function getAllNamespaceRoots() {
		if (self::$all_namespace_roots === null) {

			self::$all_namespace_roots = array(
				"tt" => dirname(__DIR__),
			);

			if (($r=Config::get(Config::PROJ_NAMESPACE_ROOT, false)) !== false) {
				self::$all_namespace_roots[$r] = Config::get(Config::CFG_PROJECT_DIR);
			}

		}
		return self::$all_namespace_roots;
	}

	private static function classnameMatchesNamespaceRoot($classname, $namespace_root, $folder) {
		if (!preg_match("/^$namespace_root\\\\(.*)/", $classname, $matches)) return false;

		$name = $matches[1];

		$file = str_replace('\\', '/', $folder . '/' . $name . '.php');

		return $file;
	}

	private static function notFound($class, $cutBacktrace = 0) {
		if (!Autoloader::$abort_on_error) return false;
		require_once dirname(__DIR__) . '/service/Error.php';
		new Error("Can't autoload \"$class\"!", $cutBacktrace + 1);
		return null;
	}

}