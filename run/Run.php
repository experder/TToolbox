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

class Run {

	public static function run() {

		require_once dirname(__DIR__) . '/service/ServiceEnv.php';

		if (ServiceEnv::isSapiCLI()) {
			Config::startCli();
			self::doRunCli();
		}

		if (ServiceEnv::isSapiAPI()) {
			Config::startApi();
			self::doRunApi();
		}

		Config::startWeb(null);
		self::doRunWeb();

	}

	public static function getWebUrl($controllerClass) {
		$controllerClass = str_replace('\\', '/', $controllerClass);
		return Config::get(Config::RUN_ALIAS) . $controllerClass;
	}

	public static function getWebLink($controllerClass, $linkTitle = null) {
		if ($linkTitle === null) $linkTitle = $controllerClass;
		return "<a href='" . self::getWebUrl($controllerClass) . "'>" . $linkTitle . "</a>";
	}

	private static function doRunWeb() {

		if (!isset($_GET["c"])) {
			new Error("TT API: No Runner specified! [ /?c= ]");
		}

		$controller = str_replace('/', '\\', $_GET["c"]);

		$class = self::loadRunner($controller);

		Page::init($controller);

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

		$json = json_encode($response);
		echo $json;
		exit;

	}

	/**
	 * Example:
	 *
	 * php TToolbox/run/cli.php myproject\\Myrunner foo bar
	 *
	 * class Myrunner extends Runner {
	 *   public function runCli(array $data = array()) {
	 *     return "You said: " . print_r($data, 1);
	 *   }
	 * }
	 */
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

		$class = self::loadRunner($controller);

		$response = $class->runCli($data);
		echo $response;
		exit;

	}

	private static function checkRunner($controllerClass) {

		if (!$controllerClass) {
			new Error("No qualified controller classname given!");
		}

		$file = Autoloader::classnameMatchesAnyNamespaceRoot($controllerClass);

		if ($file === false) {
			new Error("No class definition found for '$controllerClass'!");
		}

		if (!file_exists($file)) {
			new Error("File not found: '$file'");
		}

		return true;
	}

	/**
	 * @param string $controllerClass
	 * @return Runner
	 */
	private static function loadRunner($controllerClass) {
		$controllerClass = ServiceStrings::classnameSafe($controllerClass);

		self::checkRunner($controllerClass);

		$class = new $controllerClass();

		if (!$class instanceof Runner) {
			new Error("Controller class '$controllerClass' does not extend 'tt\\run\\Runner'!");
		}

		return $class;
	}

}