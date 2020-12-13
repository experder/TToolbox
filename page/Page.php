<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\page;

use tt\debug\Error;

class Page {

	/**
	 * @var Page|null $instance
	 */
	private static $instance = null;

	/**
	 * @var Message[] $messages
	 */
	private static $messages = array();

	private function __construct() {
	}

	/**
	 * @return Page|null
	 */
	public static function getInstance(){
		return self::$instance;
	}

	public static function init(){
		if(self::$instance!==null){
			new Error("Page has been initialized already.");
		}
		self::$instance = new Page();
		return self::$instance;
	}

	public static function addMessage(Message $message){
		self::$messages[] = $message;
	}

	/**
	 * @param string $type Message::TYPE_
	 * @param string $message
	 */
	public static function addMessageText($type, $message){
		self::$messages[] = new Message($type, $message);
	}

	public function getHtml(){
		$html = "";
		$html.=$this->messagesToHtml();
		$html.="PAGECONTENT";
		return $html;
	}

	public function messagesToHtml(){
		$html = array();
		foreach (self::$messages as $message){
			$html[] = $message->toHtml();
		}
		return implode("\n", $html);
	}

	public function deliver(){
		new Error("deliverfailure");
		echo $this->getHtml();
		exit;
	}

}