<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule;

use tt\core\database\Database;
use tt\core\Modules;
use tt\coremodule\dbmodell\core_config;
use tt\coremodule\dbmodell\core_navigation;
use tt\coremodule\pages\Admin;
use tt\install\Api;
use tt\moduleapi\UpdateDatabase;
use tt\install\Installer;

class CoreDatabase extends UpdateDatabase {

	public static function init($host, $dbname, $user, $password) {

		$db = Database::init($host, $dbname, $user, $password);

		$db->_query(core_config::sql_001_create());

		$updater = Modules::getInstance()->getModule(CoreModule::MODULE_ID)->getUpdateDatabase();

		$msg = $updater->startUpdate();

		return $msg;
	}

	protected function doUpdate() {

		/** @see Installer::initDatabaseDo() */

		$this->q(1, core_navigation::sql_001_create());
		$this->q(2, core_navigation::toSql_insert(Admin::ROUTE, Admin::TITLE, Admin::getClass()));
		//TODO: Make invisible!
		$this->q(3, core_navigation::toSql_insert(Api::ROUTE, Api::TITLE, Api::getClass()));

	}

}