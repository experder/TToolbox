<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\coremodule\pages;

use tt\core\page\Message;
use tt\moduleapi\UpdateDatabase;
use tt\run\ApiResponseHtml;
use tt\run\Runner;
use tt\service\Error;
use tt\service\form\Form;
use tt\service\js\Js;
use tt\service\ServiceStrings;

class Admin extends Runner {

	const TITLE = "Admin";
	const ROUTE = "core/admin";

	const CMD_updateDb = "updateDb";

	public static function getClass() {
		return \tt\service\polyfill\Php5::get_class();
	}

	/**
	 * @inheritdoc
	 */
	public function runWeb() {
		$html = "";

		$form = new Form(null, "", "Update DB");
		$form->onSubmit .= (Js::ajaxPostToMessages(null, null, "{
			class:'".ServiceStrings::escape_value_js(self::ROUTE)."',
			cmd:'".self::CMD_updateDb."',
		}"))
			. "return false;";

		$html.=$form;

		return $html;
	}

	/**
	 * @inheritdoc
	 */
	public function runApi($cmd = null, array $data = array()) {
		switch ($cmd) {
			case self::CMD_updateDb:

				$responses = UpdateDatabase::updateAll();
				$response = "Updated database.<pre>".print_r($responses,1)."</pre>";

				return new ApiResponseHtml(
					true,
					$response,
					array(),
					Message::TYPE_CONFIRM
				);
				break;
			default:
				new Error(get_class($this) . ": Unknown command" . ($cmd === null ? " (null)" : " '$cmd'") . "!");
				return null;
				break;
		}

	}

}