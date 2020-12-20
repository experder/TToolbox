<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\thirdparty;

use tt\core\Config;
use tt\debug\Error;

class LoadJs {

	protected $scriptRef = null;

	public function __construct() {
		if(!defined('HTTP_3RDPARTY')){
			new Error("Please define HTTP_3RDPARTY!");
		}
	}

	public function getScriptRef(){
		return $this->scriptRef;
	}

	public function getScriptReferenceHtml(){
		if($this->getScriptRef()===null){
			new Error("No script reference given for ".get_class()."!\nprotected \$scriptRef = 'jquery/jquery.min.js';");
		}
		$HTTP_3RDPARTY = Config::get(Config::HTTP_3RDPARTY);
		return "<script src=\"".$HTTP_3RDPARTY."/".$this->getScriptRef()."\"></script>";
	}

}