<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

use tt\core\Config;
use tt\core\database\core_model\core_config;

abstract class UpdateDatabase {

	abstract protected function getModuleName();

	abstract protected function doUpdate();

	private $ver_old = null;
	private $ver = null;

	/**
	 * @return int
	 */
	public function getVer() {
		return $this->ver;
	}

	public function startUpdate(){
		$this->ver_old = $this->getVersion();
		$this->ver = $this->ver_old;
		$this->doUpdate();
		if($this->ver_old==$this->ver){
			return "Module '".$this->getModuleName()."': Nothing new. Current version: ".$this->ver;
		}
		return "Module '".$this->getModuleName()."' updated from version $this->ver_old to $this->ver.";
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
		Database::getPrimary()->_query("UPDATE `" . Config::get(Config::DB_TBL_CFG) . "` SET `".core_config::content."` = '$ver' WHERE `".core_config::idstring."` ='".Config::DBCFG_DB_VERSION."' AND ".core_config::module."='".$this->getModuleName()."';");//TODO: SetConfigVal
	}
	private function getVersion(){
		$data = Database::getPrimary()->_query("SELECT ".core_config::content." FROM `" . Config::get(Config::DB_TBL_CFG) . "` WHERE ".core_config::idstring."='".Config::DBCFG_DB_VERSION."' AND ".core_config::module."='".$this->getModuleName()."' LIMIT 1;", null, Database::RETURN_ASSOC);//TODO: GetConfigVal
		$ver = $data[0]['content'];
		return $ver;
	}

}