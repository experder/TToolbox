<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

use tt\core\Autoloader;
use tt\html\form\Form;
use tt\service\Request;
use tt\service\Templates;

class Installer {

	public static function requireWebPointer(){
		$file = dirname(__DIR__).'/init_web_pointer.php';

		if(!file_exists($file)){
			require_once dirname(__DIR__).'/core/Autoloader.php';
			require_once dirname(__DIR__).'/html/form/Form.php';
			Autoloader::init();

			if(!Request::cmd('createWebPointer')) {
				$form = new Form("createWebPointer", "", "Create file");
				//TODO:...
				self::startWizard("File not found. $file", $form);
			}

			Templates::create_file($file, __DIR__.'/templates/init_web_pointer.phpX', array(
				"#INIT_WEB_PATH"=>"",//TODO:...
			));
		}

		require_once $file;
	}

	public static function startWizard($hook, $wizard){
		echo "=== INSTALL WIZARD ===<br>$hook<br>$wizard";
		exit;
	}

}