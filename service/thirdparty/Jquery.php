<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\thirdparty;

use tt\install\Installer;

class Jquery extends LoadJs {

	const JS_NAME = 'jQuery';

	protected $scriptRef = 'jquery341/jquery.min.js';

	protected $externalResource = 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js';
	protected $checksum = "220afd743d9e9643852e31a135a9f3ae";

	protected function downloadPackage() {
		Installer::$additionalWizardHead .= LoadJs::htmlScript($this->externalResource);
		parent::downloadPackage();
	}

}