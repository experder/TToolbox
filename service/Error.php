<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

use tt\core\page\Message;
use tt\core\page\Page;
use tt\run\ApiResponseHtml;

class Error {

	public static $recursion_protection = true;

	private $message;

	/**
	 * @param string $message Errormessage
	 * @param int    $cutBacktrace
	 */
	public function __construct($message, $cutBacktrace = 0) {
		$this->message = $message;

		if (!self::$recursion_protection) {
			self::withNoDependencies("(ERROR IN ERROR HANDLING!) " .$this->message);
		}
		self::$recursion_protection = false;

		$text_html = self::getTextHtml($this->message, $cutBacktrace + 1);

		Page::addMessageText(Message::TYPE_ERROR, $text_html);

		/*
		 * Output depending on the response type
		 */

		if (ServiceEnv::isSapiCLI()) {
			echo self::getTextPlain($this->message, $cutBacktrace + 1);
			exit;
		}

		if (ServiceEnv::isSapiAPI()) {
			echo self::getJson($this->message, true, $cutBacktrace + 1);
			exit;
		}

		Page::getInstance()->deliver();
	}

	public static function fromException(\Exception $e, $cutBacktrace = 0) {
		return new Error($e->getMessage(), $cutBacktrace + 1);
	}

	public static function withNoDependencies($message) {

		//Autoloader:
		require_once dirname(__DIR__) . '/service/ServiceEnv.php';
		require_once dirname(__DIR__) . '/service/DebugTools.php';
		require_once dirname(__DIR__) . '/core/page/Message.php';
		require_once dirname(__DIR__) . '/core/page/Page.php';

		/** @see DebugTools::backtrace: */
		self::$recursion_protection = false;


		$last_msg = null;
		if (($messages = Page::getInstance()->getMessages()) && is_array($messages) && ($c = count($messages)) > 0) {
			$last_msg = array_pop($messages);
		}

		if (ServiceEnv::isSapiCLI()) {
			echo self::getTextPlain($message);
			exit;
		}

		if (ServiceEnv::isSapiAPI()) {
			require_once dirname(__DIR__) . '/run/ApiResponse.php';
			require_once dirname(__DIR__) . '/run/ApiResponseHtml.php';
			echo self::getJson($message);
			exit;
		}

		//HTML-Response:
		$msg = new Message(Message::TYPE_ERROR, self::getTextHtml($message));
		$lastMsgHtml = $last_msg ? $last_msg->toHtml() : "";
		echo $lastMsgHtml . $msg->toHtml();
		exit;
	}

	private static function getTextPlain($message, $cutBacktrace = 0) {
		return "\n*** ERROR ***\n" . $message
			. "\n-------------\n" . implode("\n", DebugTools::backtrace($cutBacktrace + 1)) . "\n";
	}

	private static function getJson($message, $pretty_print = true, $cutBacktrace = 0) {
		$response = new ApiResponseHtml(false, null, array(
			"error_msg" => $message,
			"backtrace" => DebugTools::backtrace($cutBacktrace + 1),
		));
		return json_encode($response->getResponseArray(), $pretty_print ? JSON_PRETTY_PRINT : 0);
	}

	private static function getTextHtml($message, $cutBacktrace = 0) {
		return "<pre class='errormessage'>"
			. "<div class='errormessage_message'>" . htmlentities($message) . "</div>"
			. "<hr>"
			. "<div class='errormessage_backtrace'>" . implode("<br>", DebugTools::backtrace($cutBacktrace + 1)) . "</div>"
			. "</pre>";

	}

}