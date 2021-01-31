<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\run;

class ApiResponseHtml implements ApiResponse {

	/**
	 * @var bool $ok
	 */
	protected $ok;
	/**
	 * @var string $html
	 */
	protected $html;
	/**
	 * @var string $msg_type For PostToMessages (optional) Message::TYPE_
	 */
	protected $msg_type;
	//TODO: stats übermitteln

	protected $params = array();

	/**
	 * @param bool   $ok
	 * @param string $html
	 * @param array  $params
	 * @param string $msg_type
	 */
	public function __construct($ok = null, $html = null, array $params = array(), $msg_type = null) {
		$this->ok = $ok;
		$this->html = $html;
		$this->msg_type = $msg_type;
		if ($ok !== null) {
			$params["ok"] = $ok;
		}
		if ($html !== null) {
			$params["html"] = $html;
		}
		if ($msg_type !== null) {
			$params["msg_type"] = $msg_type;
		}
		$this->params = $params;
	}

	public static function createResponse($ok = null, $html = null, array $params = array(), $msg_type = null) {
		$arh = new ApiResponseHtml($ok, $html, $params, $msg_type);
		return $arh->getResponseArray();
	}

	public function getResponseArray() {
		return $this->params;
	}

}