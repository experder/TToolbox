<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule;

use tt\moduleapi\Module;
use tt\moduleapi\UpdateDatabase;

class CoreModule implements Module {

	const MODULE_ID = 'core';
	private $updateDatabase = null;

	/**
	 * @inheritdoc
	 */
	public function getModuleId() {
		return self::MODULE_ID;
	}

	/**
	 * @inheritdoc
	 */
	public function getUpdateDatabase() {
		if($this->updateDatabase===null){
			$this->updateDatabase = new CoreDatabase($this);
		}
		return $this->updateDatabase;
	}

}