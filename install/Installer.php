<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

use tt\core\Autoloader;
use tt\core\Config;
use tt\core\page\Message;
use tt\service\Error;
use tt\service\form\Form;
use tt\service\form\FormfieldPassword;
use tt\service\form\FormfieldRadio;
use tt\service\form\FormfieldRadioOption;
use tt\service\form\FormfieldText;
use tt\service\Html;
use tt\service\js\Js;
use tt\service\ServiceEnv;
use tt\service\ServiceFiles;
use tt\service\Templates;
use tt\service\thirdparty\LoadJs;

class Installer {

	public static $additionalWizardHead = "";

	public static function requireWebPointer($ajax = false) {
		if ($ajax) {
			require_once dirname(__DIR__) . '/service/ServiceEnv.php';
			ServiceEnv::$response_is_expected_to_be_json = true;
		}
		$file = dirname(__DIR__) . '/init_web_pointer.php';

		if (!file_exists($file)) {
			self::promptWebPointer($file);
		}

		require_once $file;
	}

	private static function promptWebPointer($file) {
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		if (!ServiceEnv::requestCmd('createWebPointer')) {
			$form = new Form("createWebPointer", "", "Create init_web_pointer.php");
			$suggest = "dirname(__DIR__).'/TTconfig/init_web.php'";
			$form->addField(new FormfieldText("val_webpath", "Path to init_web.php", $suggest));
			$m = new Message(Message::TYPE_INFO,
				"The file <b>$file</b> (excluded from the repo) points to <b>init_web.php</b>"
				. " (located in <a href='https://github.com/experder/TToolbox/blob/main/docs/folders.md'>CFG_DIR</a>)."
			);
			self::startWizard(
				$m->toHtml()
				. $form
			);
		}

		Templates::create_file($file, __DIR__ . '/templates/init_web_pointer.php', array(
			"'#INIT_WEB_PATH'" => $_REQUEST["val_webpath"],
		));
	}

	public static function getExternalFile($url, $toFile, $onSuccessJs = "") {
		if (!ServiceEnv::requestCmd('getExternalFile')) {

			$msg = "Downloading <b>$url</b>...";
			$m = new Message(Message::TYPE_INFO, $msg);
			$msg = $m->toHtml();
			$msg = "<div id='download_status_div'>$msg</div>";

			self::startWizard(
				$msg
				. "<script>" . Js::ajaxPostToId("download_status_div", "getExternalFile", "tt\\install\\Api", array(
					"url" => $url,
					"to_file" => $toFile,
				), "html", "
				
					if(data.ok){
						$onSuccessJs
					}
				
				") . "</script>"
			);
		}

		return true;
	}

	public static function doGetExternalFile($url, $toFile) {
		$stream = fopen($url, 'r');
		if($stream===false){
			new Error("Could not open URL '$url'!");
		}
		$bytesWritten = ServiceFiles::save($toFile, $stream);

		$filename = basename($toFile);

		$msg = "Successfully stored file '$filename'.";
		$msg = Message::messageToHtml(Message::TYPE_CONFIRM, $msg);

		return array(
			"ok" => true,
			"bytes_written" => $bytesWritten,
			"html" => $msg,
		);
	}

	public static function requireServerInit() {
		$file = Config::get(Config::CFG_SERVER_INIT_FILE);

		if (!file_exists($file)) {
			self::promptServerInit($file);
		}

		require_once $file;
	}

	private static function promptServerInit($file) {
		if (!ServiceEnv::requestCmd('createInitServer')) {
			$form = new Form("createInitServer", "", "Create init_server.php");

			$form->addField(new FormfieldText("SERVERNAME", "Servername", "mydevserver"));

			$suggest = dirname($_SERVER['SCRIPT_NAME']);
			if (($p = strpos($suggest, '/TToolbox')) !== false) {
				$suggest = substr($suggest, 0, $p);
			}
			$form->addField(new FormfieldText("HTTP_ROOT", "Web root path (<a href='https://github.com/experder/TToolbox/blob/main/docs/folders.md'>HTTP_ROOT</a>)", $suggest));

			$form->addField(new FormfieldText("HTTP_TTROOT", "TT root path (HTTP_TTROOT)", "\\tt\\core\\Config::get(\\tt\\core\\Config::HTTP_ROOT).'/TToolbox'"));

			$form->addField(new FormfieldText("DB_HOST", "DB host", "localhost"));
			$form->addField(new FormfieldText("DB_NAME", "DB name", "mytt"));
			$form->addField(new FormfieldText("DB_USER", "DB user", "root"));
			$form->addField(new FormfieldPassword("DB_PASS", "DB pass"));

			$form->addField(new FormfieldRadio("DEVMODE", array(
				new FormfieldRadioOption("on", "Development"),
				new FormfieldRadioOption("off", "Production"),
			), "off"));

			$platform = "PLATFORM_UNKNOWN";
			if (PHP_OS == 'WINNT') $platform = "PLATFORM_WINDOWS";
			if (PHP_OS == 'Linux') $platform = "PLATFORM_LINUX";
			$form->addField(new FormfieldRadio("PLATFORM", array(
				new FormfieldRadioOption("PLATFORM_UNKNOWN", "Platform unknown"),
				new FormfieldRadioOption("PLATFORM_WINDOWS", "Windows"),
				new FormfieldRadioOption("PLATFORM_LINUX", "Linux"),
			), $platform));

			$suggest = "\\tt\\core\\Config::get(\\tt\\core\\Config::HTTP_TTROOT) . '/run/?c='";
			$form->addField(new FormfieldText("RUNALIAS", "run alias", $suggest));

			self::startWizard(
				Message::messageToHtml(Message::TYPE_INFO,
					"The file <a href='https://github.com/experder/TToolbox/blob/main/docs/folders.md'>CFG_SERVER_INIT_FILE</a> (<b>$file</b>) contains server specific settings."
				)
				. $form
			);
		}

		Templates::create_file($file, __DIR__ . '/templates/init_server.php', array(
			"#HTTP_ROOT" => $_REQUEST["HTTP_ROOT"],
			"'#HTTP_TTROOT'" => $_REQUEST["HTTP_TTROOT"],
			"#SERVERNAME" => $_REQUEST["SERVERNAME"],
			"#DB_HOST" => $_REQUEST["DB_HOST"],
			"#DB_NAME" => $_REQUEST["DB_NAME"],
			"#DB_USER" => $_REQUEST["DB_USER"],
			"#DB_PASS" => $_REQUEST["DB_PASS"],
			"'#DEVMODE'" => $_REQUEST["DEVMODE"] == 'on' ? 'true' : 'false',
			"'#PLATFORM'" => '\tt\core\Config::' . $_REQUEST["PLATFORM"],
			"'#RUNALIAS'" => $_REQUEST["RUNALIAS"],
		));
	}

	public static function requireInitWeb($file) {
		if (!file_exists($file)) {
			self::promptInitWeb($file);
		}

		require_once $file;
	}

	private static function promptInitWeb($file) {
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		if (!ServiceEnv::requestCmd('createInitWeb')) {
			$form = new Form("createInitWeb", "", "Create init_web.php");

			$suggest = "dirname(__DIR__) . '/" . basename(dirname(__DIR__)) . "'";
			$form->addField(new FormfieldText("TToolbox", "Relative path to TToolbox", $suggest));

			$suggest = strtolower(basename(dirname(dirname(__DIR__))));
			$form->addField(new FormfieldText("PROJ_NAMESPACE_ROOT", "Project's root namespace", $suggest));

			self::startWizard(
				Message::messageToHtml(Message::TYPE_INFO,
					"The file <b>$file</b> contains project specific settings."
				)
				. $form
			);
		}

		Templates::create_file($file, __DIR__ . '/templates/init_web.php', array(
			"'#TToolbox'" => $_REQUEST["TToolbox"],
			"#PROJ_NAMESPACE_ROOT" => $_REQUEST["PROJ_NAMESPACE_ROOT"],
		));
	}

	public static function startWizard($html) {

		if (ServiceEnv::$response_is_expected_to_be_json) {
			new Error("Start wizard: Not possible!"
			#.implode("<br>",DebugTools::backtrace())
			);
		}

		echo self::wizardHtml($html);
		exit;
	}

	public static function wizardHtml($body) {
		$css = file_get_contents(__DIR__ . "/wizard.css");
		$head = "<style>$css</style>";
		$head .= self::$additionalWizardHead;

		if (Config::getIfSet(Config::HTTP_ROOT, false))
			$head .= LoadJs::htmlScript(Config::get(Config::HTTP_TTROOT) . '/service/js/core.js');

		$head = "<head>$head</head>";

		$html = Html::H1("Install wizard");
		$html .= "<div id='tt_pg_messages'></div>";
		$html .= $body;

		$html = $head . $html;
		$html = "<!DOCTYPE html><html>$html</html>";

		return $html;
	}

}