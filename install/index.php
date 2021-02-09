<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

use tt\config\Init_project;
use tt\core\page\PG;

require_once __DIR__ . '/Installer.php';
Installer::requireInitPointer();

Init_project::initWeb(Installer::PAGEID);

PG::add("Ready.");

PG::deliver();
