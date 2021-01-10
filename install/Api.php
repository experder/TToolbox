<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

use tt\run\Runner;

class Api extends Runner {

	public function runApi($cmd = null, array $data = array()) {
		switch ($cmd) {
			case "cmdGetExternalFile":
				list($url, $toFile) = $this->requiredFieldsFromData($data, array("url", "to_file"), false);
				$checksum = (isset($data["checksum"]) && $data["checksum"] !== 'false') ? $data["checksum"] : false;
				return Installer::doGetExternalFile($url, $toFile, $checksum);
				break;
			default:
				return null;
				break;
		}
	}

}