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
use tt\html\form\Form;
use tt\html\form\FormfieldRadio;
use tt\html\form\FormfieldRadioOption;
use tt\html\form\FormfieldText;
use tt\html\Html;
use tt\service\Request;
use tt\service\Templates;

class Installer {

	public static function requireWebPointer(){
		$file = dirname(__DIR__).'/init_web_pointer.php';

		if(!file_exists($file)){
			require_once dirname(__DIR__).'/core/Autoloader.php';
			Autoloader::init();

			if(!Request::cmd('createWebPointer')) {
				$form = new Form("createWebPointer", "", "Create init_web_pointer.php");
				$suggest = "dirname(__DIR__).'/TTconfig/init_web.php'";
				$form->addField(new FormfieldText("val_webpath", "Path to init_web.php", $suggest));
				self::startWizard(
					($m=new Message(Message::TYPE_INFO, "
The file <b>$file</b> (excluded from the repo) points to <b>init_web.php</b> (located in <a href='https://github.com/experder/TToolbox/blob/main/docs/folders.md'>CFG_DIR</a>).
						"))->toHtml()
					. $form
				);
			}

			Templates::create_file($file, __DIR__.'/templates/init_web_pointer.php', array(
				"#INIT_WEB_PATH"=>"require_once ".$_REQUEST["val_webpath"].";",
			));
		}

		require_once $file;
	}

	public static function requireServerInit(){
		$file = Config::get(Config::CFG_SERVER_INIT_FILE);

		if(!file_exists($file)){
			if(!Request::cmd('createInitServer')) {
				$form = new Form("createInitServer", "", "Create init_server.php");

				$form->addField(new FormfieldText("SERVERNAME", "Servername", "mydevserver"));

				$suggest = dirname($_SERVER['SCRIPT_NAME']);
				if(($p=strpos($suggest, '/TToolbox'))!==false){
					$suggest=substr($suggest,0,$p);
				}
				$form->addField(new FormfieldText("HTTP_ROOT", "Web root path (<a href='https://github.com/experder/TToolbox/blob/main/docs/folders.md'>HTTP_ROOT</a>)", $suggest));

				$form->addField(new FormfieldText("DB_HOST", "DB host", "localhost"));
				$form->addField(new FormfieldText("DB_NAME", "DB name", "mytt"));
				$form->addField(new FormfieldText("DB_USER", "DB user", "root"));
				$form->addField(new FormfieldText("DB_PASS", "DB pass"));

				$form->addField(new FormfieldRadio("DEVMODE", array(
					new FormfieldRadioOption("on", "Development"),
					new FormfieldRadioOption("off", "Production"),
				),"off"));

				$platform = "PLATFORM_UNKNOWN";
				if(PHP_OS=='WINNT')$platform = "PLATFORM_WINDOWS";
				if(PHP_OS=='Linux')$platform = "PLATFORM_LINUX";
				$form->addField(new FormfieldRadio("PLATFORM", array(
					new FormfieldRadioOption("PLATFORM_UNKNOWN", "Platform unknown"),
					new FormfieldRadioOption("PLATFORM_WINDOWS", "Windows"),
					new FormfieldRadioOption("PLATFORM_LINUX", "Linux"),
				),$platform));

				self::startWizard(
					($m = new Message(Message::TYPE_INFO, "
The file <a href='https://github.com/experder/TToolbox/blob/main/docs/folders.md'>CFG_SERVER_INIT_FILE</a> (<b>$file</b>) contains server specific settings.
						"))->toHtml()
					. $form
				);
			}

			Templates::create_file($file, __DIR__.'/templates/init_server.php', array(
				"#HTTP_ROOT"=>$_REQUEST["HTTP_ROOT"],
				"#SERVERNAME"=>$_REQUEST["SERVERNAME"],
				"#DB_HOST"=>$_REQUEST["DB_HOST"],
				"#DB_NAME"=>$_REQUEST["DB_NAME"],
				"#DB_USER"=>$_REQUEST["DB_USER"],
				"#DB_PASS"=>$_REQUEST["DB_PASS"],
				"'#DEVMODE'"=>$_REQUEST["DEVMODE"]=='on'?'true':'false',
				"'#PLATFORM'"=>'\tt\core\Config::'.$_REQUEST["PLATFORM"],
			));
		}

		require_once $file;
	}

	public static function startWizard($html){
		echo self::wizardHtml($html);
		exit;
	}

	public static function wizardHtml($body){
		$css = file_get_contents(__DIR__."/wizard.css");
		$head = "<style>$css</style>";
		$head = "<head>$head</head>";

		$html = "";
		$html.=Html::H1("Install wizard");
		$html.=$body;

		$html = $head.$html;
		$html = "<!DOCTYPE html><html>$html</html>";

		return $html;
	}

}