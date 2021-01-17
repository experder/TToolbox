<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\classes\moduleapi;

use tt\core\Config;
use tt\core\database\core_model\core_config;
use tt\core\database\Database;

abstract class UpdateDatabase {

	/**
	 * @var Module
	 */
	protected $module;

	abstract protected function doUpdate();

	private $ver_old = null;
	private $ver = null;

	public function __construct(Module $module) {
		$this->module = $module;
	}

	/**
	 * @return int
	 */
	public function getVer() {
		return $this->ver;
	}

	public static function updateAll(){
		$responses = array();
		foreach (\tt\core\Modules::getAllModules() as $module){
			$responses[] = $module->getUpdateDatabase()->startUpdate();
		}
		return $responses;
	}

	public function startUpdate(){
		$this->ver_old = $this->getVersion();
		$this->ver = $this->ver_old;
		$this->doUpdate();
		if($this->ver_old==$this->ver){
			return "Module '".$this->module->getModuleId()."': Nothing new. Current version: ".$this->ver;
		}
		return "Module '".$this->module->getModuleId()."' updated from version $this->ver_old to $this->ver.";
	}

	protected function q($ver, $query) {
		if($this->ver+1 == $ver){
			Database::getPrimary()->_query($query);
			$ver_new = $this->ver+1;
			$this->setVersion($ver_new);
			$this->ver = $this->getVersion();
		}
	}

	private function setVersion($ver){
		Config::setValue($ver, Config::DBCFG_DB_VERSION, $this->module->getModuleId());
	}
	private function getVersion(){
		$ver = Config::getValue(Config::DBCFG_DB_VERSION, $this->module->getModuleId(), null, 0);
		return $ver;
	}

}