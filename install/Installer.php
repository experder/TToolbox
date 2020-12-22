<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

use tt\core\Autoloader;
use tt\core\page\Message;
use tt\debug\Error;
use tt\html\form\Form;
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
					($m=new Message(Message::TYPE_INFO, "The file <b>$file</b> (excluded from the repo) should point to <b>init_web.php</b> (located in <a href='https://github.com/experder/TToolbox/blob/main/docs/folders.md'>CFG_DIR</a>)."))->toHtml()
					. $form
				);
			}

			Templates::create_file($file, __DIR__.'/templates/init_web_pointer.php', array(
				"#INIT_WEB_PATH"=>"require_once ".$_REQUEST["val_webpath"].";",
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