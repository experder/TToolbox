<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

use tt\core\Config;
use tt\install\Installer;

class CoreDatabase extends UpdateDatabase {

	protected function getModuleName() {
		return Config::MODULE_CORE;
	}

	public static function init() {
		$cd = new CoreDatabase();
		$cd->doUpdate();
	}

	protected function doUpdate() {

		/** @see Installer::initDatabaseDo() */

#		$this->q(2, "");

	}

}