<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run;

use tt\debug\Error;
use tt\install\Installer;

require_once dirname(__DIR__).'/install/Installer.php';
Installer::requireWebPointer();

if(!isset($_REQUEST["c"])){
	new Error("No controller given! [ /?c= ]");
}

Controller::run($_REQUEST["c"]);
