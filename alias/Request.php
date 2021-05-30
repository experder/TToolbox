<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\alias;

use tt\service\ServiceEnv;

/**
 * @deprecated
 */
class Request {

	/**
	 * @deprecated ServiceEnv::requestValue
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	public static function value($key, $default = null) {
		return ServiceEnv::requestValue($key, $default);
	}

}