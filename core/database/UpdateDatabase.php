<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

use tt\core\Config;

abstract class UpdateDatabase {

	abstract protected function getModuleName();

	abstract protected function doUpdate();

	protected function q($ver, $query) {
		#Database::getPrimary()->_query($query);
		$ver = $this->getVersion();
		$ver++;
		$this->setVersion($ver);
	}

	private function setVersion($ver){
		//TODO
	}
	private function getVersion(){
		$data = Database::getPrimary()->_query("SELECT content FROM `" . Config::get(Config::DB_TBL_CFG) . "` WHERE idstring='DB_VERSION' AND module='".Config::MODULE_CORE."' LIMIT 1;", null, Database::RETURN_ASSOC);//TODO: GetConfigVal
		$ver = $data[0]['content'];
		return $ver;
	}

}