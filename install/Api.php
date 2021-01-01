<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\install;

use tt\run_api\Ajax;

class Api extends Ajax {

	protected function runCmd() {
		switch ($this->cmd) {
			case "cmdGetExternalFile":
				$data = $this->requiredFieldsFromData(array("url", "to_file"));
				$checksum = (isset($this->data["checksum"])&&$this->data["checksum"]!=='false')?$this->data["checksum"]:false;
				return Installer::doGetExternalFile($data["url"], $data["to_file"], $checksum);
				break;
			default:
				return null;
				break;
		}
	}

}