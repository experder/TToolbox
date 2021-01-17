<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\classes\moduleapi;

use tt\core\Modules;

interface Module {

	/**
	 * @return string Module name, [a-z_0-9], 40 chars max
	 * @see Modules::register()
	 * @see Modules::MODULE_ID_MAXLENGTH
	 */
	public function getModuleId();

	/**
	 * @return UpdateDatabase
	 */
	public function getUpdateDatabase();

}