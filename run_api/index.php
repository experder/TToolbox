<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run;

use tt\service\ServiceEnv;

require_once dirname(__DIR__) . '/service/ServiceEnv.php';
ServiceEnv::$response_is_expected_to_be_json = true;

require_once dirname(__DIR__) . '/init_pointer.php';

require_once dirname(__DIR__). '/run/Run.php';
Run::run();
