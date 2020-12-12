<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\debug;

use tt\service\ServiceEnv;

class Error {

	private static $recursion_protection = true;

	/**
	 * @param string $message Errormessage
	 */
	public function __construct($message) {

		if(!!self::$recursion_protection)self::withNoDependencies($message);
		self::$recursion_protection = false;

		$report = $message;
		$report .= "\n".implode("\n",DebugTools::backtrace());
		$report = "<pre>$report</pre>";

		echo $report;
		exit;
	}

	public static function fromException(\Exception $e) {
		return new Error($e->getMessage());
	}

	/**
	 * Classes used in errorhandling could cause an error and thus an endless loop.
	 * In this case this quick exit is used.
	 * @param $message
	 */
	private function withNoDependencies($message){

		require_once dirname(__DIR__).'/service/ServiceEnv.php';
		$is_commandline_call = ServiceEnv::isSapiCLI();
		$is_json_response = ServiceEnv::$response_is_expected_to_be_json;
		$response_sent = ServiceEnv::responseSent();

		require_once dirname(__DIR__).'/debug/DebugTools.php';
		$backtrace = DebugTools::backtrace();

		if($is_commandline_call){
			echo $message
				."\n" .implode("\n",$backtrace);
			exit;
		}

		if($is_json_response) {
			echo json_encode(array(
				"ok" => "false",
				"error_msg" => $message,
				"backtrace" => $backtrace,
			), JSON_PRETTY_PRINT);
			exit;
		}

		//HTML-Response:
		if(!$response_sent){
			$css = defined('HTTP_SKIN')?
				"<link href=\"".HTTP_SKIN."/main.css\" rel=\"stylesheet\" type=\"text/css\" />"
				:"";
			echo $css;
		}
		echo "<pre class='emergency_errormessage'>"
			."<div class='emergency_errormessage_message'>".htmlentities($message)."</div>"
			."<hr>"
			."<div class='emergency_errormessage_backtrace'>".implode("<br>",$backtrace)."</div>"
			."</pre>";
		exit;
	}

}