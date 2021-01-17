<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule;

use tt\core\Config;
use tt\core\database\Database;
use tt\core\Modules;
use tt\coremodule\dbmodell\core_config;
use tt\moduleapi\UpdateDatabase;
use tt\install\Installer;

class CoreDatabase extends UpdateDatabase {

	public static function init($host, $dbname, $user, $password) {

		$db = Database::init($host, $dbname, $user, $password);

		$db->_query(
			"CREATE TABLE " . core_config::getTableName() . " ("
			. " `" . core_config::ROW_id . "` INT(11) NOT NULL AUTO_INCREMENT,"
			. " `" . core_config::ROW_idstring . "` VARCHAR(40) COLLATE utf8_bin NOT NULL,"
			. " `" . core_config::ROW_module . "` VARCHAR(" . Modules::MODULE_ID_MAXLENGTH . ") COLLATE utf8_bin NOT NULL,"
			. " `" . core_config::ROW_userid . "` INT(11) DEFAULT NULL,"
			. " `" . core_config::ROW_content . "` TEXT COLLATE utf8_bin NOT NULL,"
			. " PRIMARY KEY (`" . core_config::ROW_id . "`)"
			. ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
		);

		Config::setValue("0", Config::DBCFG_DB_VERSION, CoreModule::MODULE_ID);

		$updater = Modules::getInstance()->getModule(CoreModule::MODULE_ID)->getUpdateDatabase();
		$msg = $updater->startUpdate();
		return $msg;
	}

	protected function doUpdate() {

		/** @see Installer::initDatabaseDo() */

#		$this->q(1, "");

	}

}