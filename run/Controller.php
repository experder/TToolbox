<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run;

use tt\core\Autoloader;
use tt\core\Config;
use tt\core\page\Page;
use tt\service\Error;
use tt\service\ServiceEnv;
use tt\service\ServiceStrings;

class Controller {

	/**
	 * @return string HTML
	 */
	public function runWeb() {
		new Error("runWeb is not defined in " . get_class());
		return "";
	}

	/**
	 * @return string plaintext
	 */
	public function runCli() {
		new Error("runCli is not defined in " . "");
		return "";
	}

	/**
	 * @return array JSON
	 */
	public function runAjax() {
		ServiceEnv::$response_is_expected_to_be_json = true;
		new Error("runAjax is not defined in " . "");
		return null;
	}

	public static function getWebUrl($controllerClass) {
		//TODO define alias/rewrite in server_init and use here
		$HTTP_RUN = Config::get(Config::HTTP_RUN);
		return $HTTP_RUN . '/?c=' . $controllerClass;
	}

	public static function getWebLink($controllerClass, $linkTitle = null) {
		if ($linkTitle === null) $linkTitle = $controllerClass;
		return "<a href='" . self::getWebUrl($controllerClass) . "'>" . $linkTitle . "</a>";
	}

	public static function run($controllerClass) {
		$controllerClass = ServiceStrings::classnameSafe($controllerClass);
		if (!$controllerClass) new Error("No qualified controller classname given!");

		$file = Autoloader::classnameMatchesProjectNamespace($controllerClass);

		if ($file === false) {
			$PROJ_NAMESPACE_ROOT = Config::get(Config::PROJ_NAMESPACE_ROOT);
			new Error("No class definition found for '$controllerClass'!"
				. " (must start with '$PROJ_NAMESPACE_ROOT')"
			);
		}

		if (!file_exists($file)) {
			new Error("File not found: '$file'");
		}

		$class = new $controllerClass();

		if (!$class instanceof Controller) {
			new Error("Controller class '$controllerClass' does not extend 'tt\\run\\Controller'!");
		}

		$response = $class->runWeb();

		Page::getInstance()->add($response);
		Page::getInstance()->deliver();
	}

}