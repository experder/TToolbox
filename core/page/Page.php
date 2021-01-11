<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\page;

use tt\core\Config;
use tt\service\thirdparty\Jquery;
use tt\service\thirdparty\LoadJs;

class Page {

	/**
	 * @var Page|null $instance
	 */
	private static $instance = null;

	/**
	 * @var string $id unique page id
	 */
	private $id = null;

	/**
	 * @var Message[] $messages
	 */
	private static $messages = array();

	private $html_nodes = array();

	private $jsScripts = array();

	private function __construct() {
	}

	/**
	 * @return Page
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new Page();
			$j = new Jquery();
			self::$instance->addJs($j->getScriptReference(), "jQuery");
			self::$instance->addJs(Config::get(Config::HTTP_TTROOT) . '/service/js/core.js', "coreJs");
		}
		return self::$instance;
	}

	public static function init($pid) {
		$page = self::getInstance();
		if ($pid === null) return $page;
		$page->id = $pid;

		//TODO: Navigation
		//TODO: Breadcrumbs

		return $page;
	}

	public function addMessage(Message $message) {
		self::$messages[] = $message;
	}

	/**
	 * @param string $type Message::TYPE_
	 * @param string $message
	 */
	public static function addMessageText($type, $message) {
		self::$messages[] = new Message($type, $message);
	}

	/**
	 * @param mixed $node must be of a type described in \t2\core\Node::check_type
	 * @see \tt\core\page\Node::check_type
	 * @return Page $this
	 */
	public function add($node) {

		Node::check_type($node, 1);

		if (is_array($node)) {
			foreach ($node as $n) {
				$this->add($n);
			}
			return $this;
		}

		$this->html_nodes[] = $node;

		return $this;
	}

	public function getHtml() {
		$head = $this->getMainCss();
		$head .= "\n" . $this->getJsHtml();
//TODO:
$head .= "<title>".$this->id."</title>";
		$head = "\n<head>\n$head\n</head>";

		$messages = $this->messagesToHtml();
		$messages = "<div class='messages' id='tt_pg_messages'>" . ($messages ? "\n$messages\n" : "") . "</div>";

		$body = $this->getBodyHtml();
		$body = "\n<div class='inner_body'>\n$body\n</div>";
		$body = $messages . $body;
		$body .= $this->waitSpinner();
$body="<h1>$this->id</h1>".$body;
		$body = "\n<body onunload='t2_spinner_stop();'>\n$body\n</body>\n";

		$html = $head . $body;
		$html = "<!DOCTYPE html><html>$html</html>";
		return $html;
	}

	private function waitSpinner() {
		$waitSpinner = "<div id=\"uploadSpinner\" style='display:none;'><div class=\"spinnerContent\"><div>Please wait...</div></div></div>";
		return $waitSpinner;
	}

	public function getBodyHtml() {
		$html = "";
		foreach ($this->html_nodes as $node) {
			$html .= $node;
		}
		return $html;
	}

	public function getMessages() {
		return self::$messages;
	}

	public function messagesToHtml() {
		$html = array();
		foreach (self::getMessages() as $message) {
			$html[] = $message->toHtml();
		}
		$result = implode("\n", $html);
		return $result;
	}

	public function deliver() {
		echo $this->getHtml();
		exit;
	}

	public function getMainCss() {
		$css = array();
		$HTTP_SKIN = Config::get(Config::HTTP_SKIN);
		$css[] = "<link href=\"" . $HTTP_SKIN . "/main.css\" rel=\"stylesheet\" type=\"text/css\" />";
		return implode("\n", $css);
	}

	public function addJs($scriptUrl, $key = null) {
		$ok = true;
		if ($key === null) {
			$this->jsScripts[] = $scriptUrl;
		} else {
			if (isset($this->jsScripts[$key])) $ok = false;
			$this->jsScripts[$key] = $scriptUrl;
		}
		return $ok;
	}

	public function getJsScripts() {
		return $this->jsScripts;
	}

	public function getJsHtml() {
		$html = array();
		foreach ($this->getJsScripts() as $script) {
			$html[] = LoadJs::htmlScript($script);
		}
		return implode("\n", $html);
	}

	public static function echoAndQuit($html = "") {
		Page::getInstance()
			->add($html)
			->deliver();
	}

}