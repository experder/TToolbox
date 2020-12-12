<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\autoload;

use tt\debug\Error;

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
			$file = dirname(__DIR__) . '/' . str_replace('\\', '/', $name) . '.php';
			if (!file_exists($file)) {
				return Autoloader::notFound($class_name);
			} else {
				require_once $file;
			}

			return true;
		});

	}

	private static function notFound($class) {
		if(!Autoloader::$abort_on_error)return false;
		require_once __DIR__ . '/../debug/Error.php';
		new Error("Can't autoload \"$class\"!");
		return null;
	}

}