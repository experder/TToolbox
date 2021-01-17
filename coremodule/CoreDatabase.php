<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule;

use tt\core\Modules;
use tt\moduleapi\UpdateDatabase;
use tt\install\Installer;

class CoreDatabase extends UpdateDatabase {

	public static function init() {
		$updater = Modules::getInstance()->getModule(CoreModule::MODULE_ID)->getUpdateDatabase();
		$msg = $updater->startUpdate();
		return $msg;
	}

	protected function doUpdate() {

		/** @see Installer::initDatabaseDo() */

#		$this->q(1, "");

	}

}