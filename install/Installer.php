<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

class Installer {

	public static function requireWebPointer(){
		$file = dirname(__DIR__).'/init_web_pointer.php';
		if(file_exists($file)){
			require_once $file;
			return;
		}

		if(!isset($_REQUEST['createWebPointer'])){
			self::startWizard("File not found. $file","<a href='?createWebPointer'>Create file</a>");

		}else{
			//...
		}

	}

	public static function startWizard($hook, $wizard){
		echo "=== INSTALL WIZARD ===<br>$hook<br>$wizard";
		exit;
	}

}