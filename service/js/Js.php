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

	/**
	 * Example:
	 * $dataArray = array(
	 *     "cmd"=>"test1",
	 * );
	 * $funktion = "alert(data.response1);";
	 * PG::add("<span onclick=\"".htmlentities(Js::ajaxPost(null,null,$dataArray,$funktion))."\" style='cursor:
	 * pointer'>Ajax test</span>");
	 *
	 * @param string       $cmd
	 * @param string       $controller
	 * @param array|string $postData
	 * @param string       $callbackFunction
	 * @return string
	 */
	public static function ajaxPost($cmd=null, $controller=null, $postData=array(), $callbackFunction=""){
		if($controller!==null){
			$postData["controller"]=$controller;
		}
		if($cmd!==null){
			$postData["cmd"]=$cmd;
		}
		if(is_array($postData)){
			$dataObj = json_encode($postData);
		}else{
			$dataObj = $postData;
		}
		$api = Config::get(Config::HTTP_TTROOT)."/run_api/";
		return "tt_ajax_post('$api',$dataObj,function(data){{$callbackFunction}});";
	}

	public static function ajaxPostToId($id, $responseBody="html", $cmd=null, $controller=null, $postData=array(), $callbackFunction=""){
		$callbackFunction = "$('#$id').html(data.$responseBody);".$callbackFunction;
		return self::ajaxPost($cmd, $controller, $postData, $callbackFunction);
	}

	public static function ajaxPostToMessages($responseBody="html", $cmd=null, $controller=null, $postData=array(), $callbackFunction=""){
		$callbackFunction = "
		
			let msg = $('<div>',{'class': 'message info'}).html(data.$responseBody);
			$('#tt_pg_messages').append(msg);
		
		".$callbackFunction;
		return self::ajaxPost($cmd, $controller, $postData, $callbackFunction);
	}

}