<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\api_default;

use tt\service\form\Form;
use tt\service\form\FormfieldPassword;
use tt\service\form\FormfieldText;
use tt\service\Html;

class Session {

	public static function getLoginHtml() {
		$html = "";

		$form = new Form();
		$form->addField(new FormfieldText("name", "User"));
		$form->addField(new FormfieldPassword("pass", "Password"));

		$html .= Html::H1("Login");
		$html .= $form->toHtml();

		return $html;
	}

}