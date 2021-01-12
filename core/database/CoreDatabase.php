<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\database;

use tt\core\Config;

class CoreDatabase extends UpdateDatabase {

	protected function getModuleName() {
		return Config::MODULE_CORE;
	}

	protected function doUpdate() {

		$this->q(1,
			"CREATE `" . Config::get(Config::DB_TBL_CFG) . "` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idstring` varchar(40) COLLATE utf8_bin NOT NULL,
  `module` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"
		);

	}

}