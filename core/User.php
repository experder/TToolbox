<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core;

use tt\api\Session;
use tt\core\auth\Token;
use tt\core\page\Page;
use tt\service\ServiceEnv;

class User {

	public static function initSession() {
		return new Token();//disabled for the moment
		if (!ServiceEnv::isSapiCLI() && !ServiceEnv::$response_is_expected_to_be_json) {
			Page::echoAndQuit(Session::getLoginHtml());
		}
		return new Token();
	}

}