<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core;

use tt\classes\moduleapi\Module;
use tt\coremodule\CoreModule;
use tt\service\Error;

class Modules {

	const MODULE_ID_MAXLENGTH = 40;//chars

	/**
	 * @var Modules $instance
	 */
	private static $instance = null;

	/**
	 * @var Module[] $modules
	 */
	private $modules = null;

	private function __construct() {
	}

	public static function getInstance(){
		if(self::$instance===null){
			self::$instance = new Modules();
		}
		return self::$instance;
	}

	public static function getAllModules(){
		$modules = self::getInstance()->modules;
		if($modules===null){
			new Error("Modules not initialized!");
		}
		return $modules;
	}

	public function register(Module $module){
		$module_id = $module->getModuleId();

		if(preg_replace("/[^a-z_0-9]/","",$module_id)!==$module_id || strlen($module_id)>Modules::MODULE_ID_MAXLENGTH){
			new Error("Invalid module ID: '$module_id'");
		}

		$this->modules[$module_id] = $module;
	}

	public function getModule($module_id){
		if(!isset($this->modules[$module_id])){
			new Error("Module not registered: '$module_id'");
		}
		$module = $this->modules[$module_id];
		return $module;
	}

	public static function init(){

		$m = Modules::getInstance();

		$m->modules=array();

		$m->register(new CoreModule());

	}

}