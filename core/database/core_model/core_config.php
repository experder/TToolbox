<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database\core_model;

use tt\core\Config;
use tt\core\database\DbModell;

class core_config extends DbModell {

	const content = "content";
	const idstring = "idstring";
	const module = "module";

	/**
	 * @return string
	 */
	public function getTableName() {
		return Config::get(Config::DB_TBL_CFG);
	}
}