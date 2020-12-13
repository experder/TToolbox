<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\usermgmt;

use tt\api\Session;

class User {

	public static function initSession(){
		#echo "...init session...";
		echo Session::getLoginHtml();
		echo "<hr>";
	}

}