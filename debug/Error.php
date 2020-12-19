<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\debug;

use tt\page\Message;
use tt\page\Page;
use tt\service\ServiceEnv;

class Error {

	private static $recursion_protection = true;

	private $message;

	/**
	 * @param string $message Errormessage
	 */
	public function __construct($message, $cutBacktrace=0) {
		$this->message = $message;

		if(!self::$recursion_protection){
			$this->message = "(ERROR IN ERROR HANDLING!) ".$this->message;
			echo $this->withNoDependencies();
			exit;
		}
		self::$recursion_protection = false;

		$text_html = $this->getTextHtml($cutBacktrace+1);

		Page::addMessageText(Message::TYPE_ERROR, $text_html);

		//TODO:Handle response types
		Page::getInstance()->deliver();
	}

	public static function fromException(\Exception $e) {
		return new Error($e->getMessage());
	}

	private function withNoDependencies(){
		require_once dirname(__DIR__).'/service/ServiceEnv.php';
		require_once dirname(__DIR__).'/debug/DebugTools.php';
		require_once dirname(__DIR__).'/page/Message.php';
		require_once dirname(__DIR__).'/page/Page.php';

		$last_msg = null;
		if(($messages = Page::getInstance()->getMessages()) && is_array($messages) && ($c=count($messages))>0){
			$last_msg = array_pop($messages);
		}

		$is_commandline_call = ServiceEnv::isSapiCLI();
		$is_json_response = ServiceEnv::$response_is_expected_to_be_json;
		$response_sent = ServiceEnv::responseSent();

		if($is_commandline_call){
			return $this->getTextPlain();
		}

		if($is_json_response) {
			return $this->getJson();
		}

		//HTML-Response:
		$msg = new Message(Message::TYPE_ERROR, $this->getTextHtml());
		$css = $response_sent?"":Page::getInstance()->getMainCss();
		$lastMsgHtml = $last_msg?$last_msg->toHtml():"";
		return $css.$lastMsgHtml.$msg->toHtml();
	}

	private function getTextPlain(){
		return $this->message
			."\n" .implode("\n",DebugTools::backtrace());
	}

	private function getJson($pretty_print=true){
		return json_encode(array(
			"ok" => "false",
			"error_msg" => $this->message,
			"backtrace" => DebugTools::backtrace(),
		),
			$pretty_print ? JSON_PRETTY_PRINT : 0
		);
	}

	private function getTextHtml($cutBacktrace=0){
		return "<pre class='errormessage'>"
			."<div class='errormessage_message'>".htmlentities($this->message)."</div>"
			."<hr>"
			."<div class='errormessage_backtrace'>".implode("<br>",DebugTools::backtrace($cutBacktrace+1))."</div>"
			."</pre>";

	}

}