<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\page;

class Page {

	/**
	 * @var Page|null $instance
	 */
	private static $instance = null;

	/**
	 * @var Message[] $messages
	 */
	private static $messages = array();

	private $html_nodes = array();

	private function __construct() {
	}

	/**
	 * @return Page
	 */
	public static function getInstance(){
		if(self::$instance===null){
			self::$instance = new Page();
		}
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

	/**
	 * @param mixed $node must be of a type described in \t2\core\Node::check_type
	 * @see \tt\page\Node::check_type
	 * @return Page $this
	 */
	public function add($node) {

		Node::check_type($node);

		if (is_array($node)) {
			foreach ($node as $n) {
				$this->add($n);
			}
			return $this;
		}

		$this->html_nodes[] = $node;

		return $this;
	}

	public function getHtml(){
		$head = $this->getMainCss();
		$head = "<head>$head</head>";

		$messages = $this->messagesToHtml();
		$messages = $messages?"<div class='messages'>$messages</div>":"";

		$body = $this->getBodyHtml();
		$body = "<div class='inner_body'>$body</div>";
		$body = "<body>$body</body>";
		$body = $messages.$body;

		$html = $head.$body;
		$html = "<!DOCTYPE html><html>$html</html>";
		return $html;
	}

	public function getBodyHtml(){
		$html="";
		foreach ($this->html_nodes as $node) {
			$html .= $node;
		}
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
		echo $this->getHtml();
		exit;
	}

	public function getMainCss(){
		if(defined('HTTP_SKIN')){
			return "<link href=\"".HTTP_SKIN."/main.css\" rel=\"stylesheet\" type=\"text/css\" />";
		}
		return "";
	}

	public static function echoAndQuit($html=""){
		Page::getInstance()
			->add($html)
			->deliver();
	}

}