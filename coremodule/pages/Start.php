<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule\pages;

use tt\run\Runner;

class Start extends Runner {

	const PAGEID = "ttdemo_index";

	public static function getClass() {
		return \tt\service\polyfill\Php5::get_class();
	}

	/**
	 * @inheritdoc
	 */
	public function runWeb() {
		$html = array();

		$html[] = "Welcome!";

		return $html;
	}

}