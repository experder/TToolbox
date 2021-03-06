<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

require_once dirname(__DIR__) . '/run/Runner.php';

use tt\core\Autoloader;
use tt\core\Config;
use tt\core\page\Message;
use tt\coremodule\CoreDatabase;
use tt\run\ApiResponseHtml;
use tt\run\Runner;
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
use tt\service\ServiceStrings;
use tt\service\Templates;
use tt\service\thirdparty\LoadJs;

/**
 * Installer handles:
 * - Creation of "init_pointer.php"
 * - Creation of "Init_project.php"
 * - Creation of "init_server.php"
 * - Create API classes
 * - Creation of database (name specified in init_server.php)
 * - Download of third party packages
 */
class Installer extends Runner {

	const PAGEID = "core/installer";

	const DIVID_download_status_div = 'download_status_div';

	const AJAXDATA_warning = "warning";

	const CMD_GetExternalFile = 'cmdGetExternalFile';

	public static $additionalWizardHead = "";

	public static function getClass() {
		return \tt\service\polyfill\Php5::get_class();
	}

	public static function requireInitPointer() {
		$file = dirname(__DIR__) . '/init_pointer.php';

		if (!file_exists($file)) {
			self::promptInitPointer($file);
		}

		require_once $file;
	}

	private static function promptInitPointer($file) {
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		if (!ServiceEnv::requestCmd('createWebPointer')) {
			$form = new Form("createWebPointer", "", "Create init_pointer.php");
			$suggest = "dirname(__DIR__).'/TTconfig/Init_project.php'";
			$form->addField(new FormfieldText("val_webpath", "Path to Init_project.php", $suggest, true, array("id" => "focus")));
			$m = new Message(Message::TYPE_INFO,
				"The file <b>$file</b> (excluded from the repo) points to <b>Init_project.php</b>."
			);
			self::startWizard(
				$m->toHtml()
				. $form
			);
		}

		Templates::create_file($file, __DIR__ . '/templates/init_pointer.php', array(
			"'#INIT_WEB_PATH'" => $_REQUEST["val_webpath"],
		));
	}

	public static function getExternalFile($url, $toFile, $onSuccessJs = "", $checksum = false) {
		if (!ServiceEnv::requestCmd(self::CMD_GetExternalFile)) {

			$msg = "Downloading <b>$url</b>...";
			$m = new Message(Message::TYPE_INFO, $msg);
			$msg = $m->toHtml();
			$msg = "<div id='" . self::DIVID_download_status_div . "'>$msg</div>";

			self::startWizard(
				$msg
				. "<script>" . Js::ajaxPostToId(self::DIVID_download_status_div, self::CMD_GetExternalFile, self::PAGEID, array(
					"url" => $url,
					"to_file" => $toFile,
					"checksum" => $checksum,
				), "html", "
				
					if(data.ok){
						$onSuccessJs
					}
				
				") . "</script>"
			);
		}

		return true;
	}

	public static function doGetExternalFile($url, $toFile, $checksum = false) {
		$stream = fopen($url, 'r');
		if ($stream === false) {
			new Error("Could not open URL '$url'!");
		}
		ServiceFiles::save($toFile, $stream);

		$filename = basename($toFile);

		$msg = "Successfully stored file '$filename'.";
		$msg = Message::messageToHtml(Message::TYPE_CONFIRM, $msg);
		$form = new Form(null, "", false);
		$form->addButton(
			"<input type='submit' id='focus' value='OK'>"
		);
		$msg .= $form;

		$warning = false;

		if ($checksum !== false) {
			$hash = hash_file("md5", $toFile);
			if ($hash !== $checksum) {
				$warning = "Stored file '$filename', but hash doesn't match!";
				$msg = Message::messageToHtml(Message::TYPE_ERROR, $warning);
			}
		}

		return new ApiResponseHtml(true, $msg, array(
			self::AJAXDATA_warning => $warning,
		));

	}

	public static function requireInitServer() {
		$file = Config::get(Config::CFG_SERVER_INIT_FILE);

		if (!file_exists($file)) {
			self::promptServerInit($file);
		}

		require_once $file;
	}

	private static function promptServerInit($file) {
		if (!ServiceEnv::requestCmd('createInitServer')) {
			$form = new Form("createInitServer", "", "Create init_server.php");

			$suggest = dirname($_SERVER['SCRIPT_NAME']);
			if (($p = strpos($suggest, '/TToolbox')) !== false) {
				$suggest = substr($suggest, 0, $p);
			}
			$form->addField(new FormfieldText("HTTP_ROOT", "Web root path", $suggest, true, array("id" => "focus")));

			$form->addField(new FormfieldText("DB_HOST", "DB host", "localhost"));
			$form->addField(new FormfieldText("DB_NAME", "DB name", "mytt"));
			$form->addField(new FormfieldText("DB_USER", "DB user", "root"));
			$form->addField(new FormfieldPassword("DB_PASS", "DB pass"));

			$form->addField(new FormfieldRadio("DEVMODE", array(
				new FormfieldRadioOption("on", "Development"),
				new FormfieldRadioOption("off", "Production"),
			), "on"));

			self::startWizard(
				Message::messageToHtml(Message::TYPE_INFO,
					"The file <b>$file</b> contains server specific settings."
				)
				. $form
			);
		}

		$platform = "PLATFORM_UNKNOWN";
		if (PHP_OS == 'WINNT') $platform = "PLATFORM_WINDOWS";
		if (PHP_OS == 'Linux') $platform = "PLATFORM_LINUX";

		$servername = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "myserver";

		$ttroot = "Config::get(Config::HTTP_ROOT) . '/" . basename(dirname(__DIR__)) . "'";

		Templates::create_file($file, __DIR__ . '/templates/init_server.php', array(
			"<?php" . PHP_EOL . PHP_EOL . "/*" => "<?php" . PHP_EOL . "/*",

			"#HTTP_ROOT" => $_REQUEST["HTTP_ROOT"],
			"#DB_HOST" => $_REQUEST["DB_HOST"],
			"#DB_NAME" => $_REQUEST["DB_NAME"],
			"#DB_USER" => $_REQUEST["DB_USER"],
			"#DB_PASS" => $_REQUEST["DB_PASS"],
			"'#DEVMODE'" => $_REQUEST["DEVMODE"] == 'on' ? 'true' : 'false',

			"'#HTTP_TTROOT'" => $ttroot,
			"'#PLATFORM'" => 'Config::' . $platform,
			"#SERVERNAME" => $servername,
		));
	}

	public static function requireInitProject($file) {
		if (!file_exists($file)) {
			self::promptInitProject($file);
		}

		require_once $file;
	}

	private static function promptInitProject($file) {
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		if (!ServiceEnv::requestCmd('cmdCreateInit')) {
			$form = new Form("cmdCreateInit", "", "Create " . basename($file));

			$suggest = "dirname(__DIR__) . '/" . basename(dirname(__DIR__)) . "'";
			$form->addField(new FormfieldText("TToolbox", "Path to TToolbox", $suggest, true, array(
				"id" => "focus",
			)));

			$suggest = strtolower(basename(dirname(dirname(__DIR__))));
			$form->addField(new FormfieldText("PROJ_NAMESPACE_ROOT", "Project's root namespace", $suggest));

			self::startWizard(
				Message::messageToHtml(Message::TYPE_INFO,
					"The file <b>$file</b> contains project specific settings."
				)
				. $form
			);
		}

		Templates::create_file($file, __DIR__ . '/templates/Init_project.php', array(
			"//TPL:namespace" => "namespace",
			"'#TToolbox'" => $_REQUEST["TToolbox"],
			"#PROJ_NAMESPACE_ROOT" => $_REQUEST["PROJ_NAMESPACE_ROOT"],
		));

		$form = new Form(null, "", false);
		$form->addButton(
			"<input type='submit' id='focus' value='OK'>"
		);
		self::startWizard(
			Message::messageToHtml(Message::TYPE_CONFIRM,
				"File '<b>$file</b>' has been created."
			)
			. $form
		);

	}

	public static function initDatabaseGui($dbname, $host, $user, $password) {
		if (!ServiceEnv::requestCmd('cmdInitDatabase')) {
			$form = new Form("cmdInitDatabase", "", false);
			$form->addButton(
				"<input type='submit' id='focus' value='"
				. ServiceStrings::escape_value_html("Create database '$dbname'")
				. "'>"
			);

			self::startWizard(
				Message::messageToHtml(Message::TYPE_INFO,
					"Database '<b>$dbname</b>' needs to be created."
				)
				. $form
			);
		}

		$init_response = self::initDatabaseDo($dbname, $host, $user, $password);

		$form2 = new Form(null, "", false);
		$form2->addButton(
			"<input type='submit' id='focus' value='OK'>"
		);
		self::startWizard(
			Message::messageToHtml(Message::TYPE_CONFIRM,
				"Database '<b>$dbname</b>' has been created. $init_response"
			)
			. $form2
		);

	}

	private static function initDatabaseDo($dbname, $host, $user, $password) {
		$dbh = new \PDO("mysql:host=" . $host, $user, $password);
		$dbh->exec(
			"CREATE DATABASE `" . $dbname . "` CHARACTER SET utf8 COLLATE utf8_general_ci;"
		) or die("Error240! " . print_r($dbh->errorInfo(), true));

		$msg = CoreDatabase::init($host, $dbname, $user, $password);

		return $msg;
	}

	public static function initApiClass($classname, $filename) {
		if (!ServiceEnv::requestCmd('cmdInitApiClass')) {
			$form = new Form("cmdInitApiClass", "", false);
			$form->addButton(
				"<input type='submit' id='focus' value=\"Create API class '$classname'\">"
			);

			self::startWizard(
				Message::messageToHtml(Message::TYPE_INFO,
					"API class '<b>$filename</b>' needs to be created."
				)
				. $form
			);
		}

		$api_class_content = "<?php\n\nnamespace tt\\api;\n\nclass $classname extends \\tt\\core\\api_default\\$classname {\n}";

		ServiceFiles::save($filename, $api_class_content);
	}

	public static function startWizard($html) {

		if (ServiceEnv::isSapiAPI() || ServiceEnv::isSapiCLI()) {
			new Error("Start wizard: Not possible! Please call via Browser.");
		}

		echo self::wizardHtml($html);
		exit;
	}

	public static function onloadFocusJs() {
		return "e=document.getElementById('focus');if(e)e.focus();";
	}

	public static function wizardHtml($body) {
		$css = file_get_contents(__DIR__ . "/wizard.css");
		$js = file_get_contents(__DIR__ . "/wizard.js");

		$head = "<style>$css</style>";
		$head .= "<script>$js</script>";
		$head .= self::$additionalWizardHead;

		if ($ttroot = Config::get(Config::HTTP_TTROOT, false))
			$head .= LoadJs::htmlScript($ttroot . '/service/js/core.js');

		$head = "<head>$head</head>";

		$html = Html::H1("Install wizard");
		$html .= "<div id='tt_pg_messages'></div>";
		$html .= $body;

		$html = "<body onload='" . ServiceStrings::escape_value_html(self::onloadFocusJs()) . "'>$html</body>";

		$html = $head . $html;
		$html = "<!DOCTYPE html><html>$html</html>";

		return $html;
	}

	public function runApi($cmd = null, array $data = array()) {
		switch ($cmd) {
			case self::CMD_GetExternalFile:
				list($url, $toFile) = $this->requiredFieldsFromData($data, array("url", "to_file"), false);
				$checksum = (isset($data["checksum"]) && $data["checksum"] !== 'false') ? $data["checksum"] : false;
				return Installer::doGetExternalFile($url, $toFile, $checksum);
				break;
			default:
				return null;
				break;
		}
	}

}