<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run;

use tt\api\Navigation;
use tt\core\Autoloader;
use tt\alias\CFG;
use tt\core\Config;
use tt\core\page\Page;
use tt\service\debug\Stats;
use tt\service\Error;
use tt\service\ServiceEnv;
use tt\service\ServiceStrings;

class Run {

	public static function run() {

		require_once dirname(__DIR__) . '/service/ServiceEnv.php';

		if (ServiceEnv::isSapiCLI()) {
			Config::init_cli();
			self::doRunCli();
		}

		if (ServiceEnv::isSapiAPI()) {
			Config::init_api();
			self::doRunApi();
		}

		Config::init_web(null);
		self::doRunWeb();

	}

	public static function getWebUrl($pageId) {
		return Config::get(Config::RUN_ALIAS) . $pageId;
	}

	public static function getWebLink($pageId, $linkTitle = null) {
		if ($linkTitle === null) $linkTitle = $pageId;
		return "<a href='" . self::getWebUrl($pageId) . "'>" . $linkTitle . "</a>";
	}

	private static function doRunWeb() {

		if (!isset($_GET["c"])) {
			new Error("TT API: No Runner specified! [ /?c= ]");
		}

		$class = self::loadRunner($_GET["c"]);

		Page::init($_GET["c"], null);

		$response = $class->runWeb();

		Page::getInstance()->add($response);

		Page::getInstance()->deliver();

	}

	private static function doRunApi() {

		$input = file_get_contents("php://input");

		$input_data = json_decode($input, true);

		if ($input_data === null) {
			$input_data = $_POST;
		}

		if (!isset($input_data["class"])) {
			new Error("TT API: No Runner specified! [ POST: 'class' ]");
		}

		$class = self::loadRunner($input_data["class"]);
		unset($input_data["class"]);

		$cmd = isset($input_data["cmd"]) ? $input_data["cmd"] : null;
		unset($input_data["cmd"]);

		$response = $class->runApi($cmd, $input_data);

		if ($response === false) {
			new Error(get_class($class) . ": Unknown command " . ($cmd === null ? "(null)" : "'$cmd'") . "!");
		}

		if (!($response instanceof ApiResponseHtml)) {
			new Error(get_class($class) . ": runApi('$cmd'): Invalid return value!");
		}

		$json = json_encode($response->getResponseArray());
		echo $json;
		exit;

	}

	private static function doRunCli() {

		global $argv;

		$data = $argv;

		if (!is_array($data) || count($data) < 2) {
			new Error("No arguments passed!");
		}

		//Remoce first element (script itself)
		array_shift($data);

		//First argument: Controller
		$controller = array_shift($data);

		$class = self::loadRunner($controller, false);

		$response = $class->runCli($data);
		echo $response;
		exit;

	}

	private static function checkRunner($controllerClass) {

		if (!ServiceStrings::classnameSafeCheck($controllerClass)) {
			new Error("No qualified controller classname given!");
		}

		$file = Autoloader::classnameMatchesAnyNamespaceRoot($controllerClass);

		if ($file === false || !file_exists($file)) {
			new Error("Controller not found: '$controllerClass'");
		}

		return true;
	}

	/**
	 * @param string $controllerClass
	 * @param bool   $isAlias
	 * @return Runner
	 */
	private static function loadRunner($controllerClass, $isAlias = true) {
		if ($isAlias) {
			$naviEntry = Navigation::getInstance()->getEntryById($controllerClass);
			if ($naviEntry !== false) {
				$controllerClass = $naviEntry->getLink();
			} else {
				new Error("Route not found: '$controllerClass'");
				$controllerClass = str_replace('/', '\\', $controllerClass);
			}
		}

		if (CFG::DEVMODE()) {
			Stats::$apiClass = $controllerClass;
		}

		self::checkRunner($controllerClass);

		$class = new $controllerClass();

		if (!($class instanceof Runner)) {
			new Error("Controller class '$controllerClass' does not extend 'tt\\run\\Runner'!");
		}

		return $class;
	}

}