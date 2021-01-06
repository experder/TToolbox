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
use tt\run_api\Ajax;
use tt\run_cli\RunApi;
use tt\service\Error;
use tt\service\ServiceStrings;

abstract class Controller {

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
	abstract protected function runWeb();

	public static function getWebUrl($controllerClass) {
		$controllerClass = str_replace('\\', '/', $controllerClass);
		return Config::get(Config::RUN_ALIAS) . $controllerClass;
	}

	public static function getWebLink($controllerClass, $linkTitle = null) {
		if ($linkTitle === null) $linkTitle = $controllerClass;
		return "<a href='" . self::getWebUrl($controllerClass) . "'>" . $linkTitle . "</a>";
	}

	public static function run() {

		if (!isset($_REQUEST["c"])) {
			new Error("No controller given! [ /?c= ]");
		}

		$c = $_REQUEST["c"];
		unset($_REQUEST["c"]);

		self::runController($c, self::RUN_TYPE_WEB, $_REQUEST);
	}

	/**
	 * @param string $controllerClass
	 * @param string $run_type Controller::RUN_TYPE_
	 * @param array  $data
	 */
	public static function runController($controllerClass, $run_type, $data) {
		$controllerClass = str_replace('/', '\\', $controllerClass);
		$controllerClass = ServiceStrings::classnameSafe($controllerClass);
		if (!$controllerClass) new Error("No qualified controller classname given!");

		$file = Autoloader::classnameMatchesAnyNamespaceRoot($controllerClass);

		if ($file === false) {
			new Error("No class definition found for '$controllerClass'!");
		}

		if (!file_exists($file)) {
			new Error("File not found: '$file'");
		}

		switch ($run_type) {
			case self::RUN_TYPE_WEB:

				$class = new $controllerClass($data);

				if (!$class instanceof Controller) {
					new Error("Controller class '$controllerClass' does not extend 'tt\\run\\Controller'!");
				}

				$response = $class->runWeb();
				Page::getInstance()->add($response);
				Page::getInstance()->deliver();

				break;
			case self::RUN_TYPE_CLI:

				$class = new $controllerClass($data);

				if (!$class instanceof RunApi) {
					new Error("Controller class '$controllerClass' does not extend 'tt\\run_cli\\RunApi'!");
				}

				$response = $class->runCli();
				echo $response;
				exit;

				break;
			case self::RUN_TYPE_API:

				$class = new $controllerClass($data);

				if (!$class instanceof Ajax) {
					new Error("Controller class '$controllerClass' does not extend 'tt\\run_api\\Ajax'!");
				}

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