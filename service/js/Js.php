<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\js;

use tt\core\Config;

class Js {

	const JSID_CORE = "coreJs";//Config::HTTP_TTROOT . '/service/js/core.js'

	/**
	 * @param string       $cmd
	 * @param string       $controller
	 * @param array|string $postData
	 * @param string       $callbackFunction
	 * @return string
	 */
	public static function ajaxPost($cmd, $controller, $postData = array(), $callbackFunction = "") {
		if (is_array($postData)) {
			$postData["class"] = $controller;
			$postData["cmd"] = $cmd;
			$dataObj = json_encode($postData);
		} else {
			$dataObj = $postData;
		}
		$api = Config::get(Config::RUN_ALIAS_API);
		return "tt_tools.ajaxPost('$api',$dataObj,function(data){{$callbackFunction}});";
	}

	public static function ajaxPostToId($id, $cmd, $controller, $postData = array(), $responseBody = "html", $callbackFunction = "") {
		$callbackFunction = "$('#$id').html(data.$responseBody);" . $callbackFunction;
		return self::ajaxPost($cmd, $controller, $postData, $callbackFunction);
	}

	public static function ajaxPostToMessages($cmd, $controller, $postData = array(), $responseBody = "html", $callbackFunction = "") {
		$callbackFunction = "
		
			let classname = 'info';
			if(data.msg_type){
				classname = data.msg_type;
			}
			tt_tools.error(data.$responseBody,classname);
		
		" . $callbackFunction;
		return self::ajaxPost($cmd, $controller, $postData, $callbackFunction);
	}

}