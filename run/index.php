<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run;

use tt\install\Installer;

require_once dirname(__DIR__) . '/install/Installer.php';
Installer::requireInitPointer();

require_once __DIR__ . '/Run.php';
Run::run();
