<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\thirdparty;

use tt\core\Config;
use tt\install\Installer;
use tt\service\Error;

class LoadJs {

	protected $scriptRef = null;
	protected $externalResource = null;
	/**
	 * @var string|false $checksum md5-hash of the resource or false if no check required.
	 */
	protected $checksum = false;
	protected $downloadTo = null;
	protected $unzip = false;

	public function __construct() {
		if (!file_exists(Config::get(Config::DIR_3RDPARTY) . '/' . $this->getScriptRef())) {
			$this->downloadPackage();
		}
	}

	protected function downloadPackage() {
		if (($res = $this->getExternalResource()) === null) {
			new Error("No external resource given for " . get_class($this) . "!"
				. "\nprotected \$externalResource = 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js';");
		}
		$downloadTo = $this->downloadTo;
		if ($downloadTo === null) $downloadTo = $this->scriptRef;
		$downloadTo = Config::get(Config::DIR_3RDPARTY) . '/' . $downloadTo;
		#Installer::getExternalFile($res, $downloadTo, "if(!data.".Installer::AJAXDATA_warning."){setTimeout(function(){location.reload();},2000);}", $this->checksum);
		Installer::getExternalFile($res, $downloadTo, "", $this->checksum);
	}

	public function getExternalResource() {
		return $this->externalResource;
	}

	public function getScriptRef() {
		return $this->scriptRef;
	}

	public function getScriptReference() {
		if ($this->getScriptRef() === null) {
			new Error("No script reference given for " . get_class($this) . "!"
				. "\nprotected \$scriptRef = 'jquery341/jquery.min.js';");
		}
		$HTTP_3RDPARTY = Config::get(Config::HTTP_3RDPARTY);
		return $HTTP_3RDPARTY . "/" . $this->getScriptRef();
	}

	public static function htmlScript($src) {
		return "<script src=\"$src\"></script>";
	}

}