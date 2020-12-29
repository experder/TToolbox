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

	const RUN_TYPE_WEB = 1;
	const RUN_TYPE_CLI = 2;
	const RUN_TYPE_API = 3;

	protected $data = array();

	/**
	 * @param array $data
	 */
	public function __construct(array $data) {
		$this->data = $data;
	}

	/**
	 * @return string HTML
	 */
	public function runWeb() {
		new Error("runWeb is not defined in " . get_class($this));
		return "";
	}

	/**
	 * @return string plaintext
	 */
	public function runCli() {
		new Error("runCli is not defined in " . get_class($this));
		return "";
	}

	/**
	 * @return array JSON
	 */
	public function runAjax() {
		ServiceEnv::$response_is_expected_to_be_json = true;
		new Error("runAjax is not defined in " . get_class($this));
		return null;
	}

	public static function getWebUrl($controllerClass) {
		$controllerClass = str_replace('\\', '/', $controllerClass);
		return Config::get(Config::RUN_ALIAS) . $controllerClass;
	}

	public static function getWebLink($controllerClass, $linkTitle = null) {
		if ($linkTitle === null) $linkTitle = $controllerClass;
		return "<a href='" . self::getWebUrl($controllerClass) . "'>" . $linkTitle . "</a>";
	}

	public static function runA() {

		$input = file_get_contents("php://input");
//		$input = json_encode(array(
//			"class"=>"tt\\run_api\\Ajax",
//			"cmd"=>"test1",
//		));

		$input_data = json_decode($input, true);

		//TODO: Kann wieder weg:
		if($input_data===null){$input_data = $_POST;}

		$class = isset($input_data["class"])?$input_data["class"]:"tt\\run_api\\Ajax";
		unset($input_data["class"]);

		self::run($class, self::RUN_TYPE_API, $input_data);
	}

	public static function runC() {

		if (!isset($_REQUEST["c"])) {
			new Error("No controller given! [ /?c= ]");
		}

		unset($_REQUEST["c"]);

		self::run($_REQUEST["c"], self::RUN_TYPE_WEB, $_REQUEST);
	}

	private static function run($controllerClass, $run_type, $data) {
		$controllerClass = str_replace('/', '\\', $controllerClass);
		$controllerClass = ServiceStrings::classnameSafe($controllerClass);
		if (!$controllerClass) new Error("No qualified controller classname given!");

		$file = Autoloader::classnameMatchesProjectNamespace($controllerClass);

		if ($file === false) {
			$file = Autoloader::classnameMatchesTtNamespace($controllerClass);
		}

		if ($file === false) {
			new Error("No class definition found for '$controllerClass'!");
		}

		if (!file_exists($file)) {
			new Error("File not found: '$file'");
		}

		$class = new $controllerClass($data);

		if (!$class instanceof Controller) {
			new Error("Controller class '$controllerClass' does not extend 'tt\\run\\Controller'!");
		}

		switch ($run_type){
			case self::RUN_TYPE_WEB:

				$response = $class->runWeb();
				Page::getInstance()->add($response);
				Page::getInstance()->deliver();

				break;
			case self::RUN_TYPE_CLI:

				$response = $class->runCli();
				echo $response;
				exit;

				break;
			case self::RUN_TYPE_API:

				$response = $class->runAjax();
				$json = json_encode($response);
				echo $json;
				exit;

				break;
			default:
				new Error("Unknown run type!");
				break;
		}

	}

}