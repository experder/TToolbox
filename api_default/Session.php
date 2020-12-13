<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\api_default;

use t2\core\form\Formfield_text;
use tt\html\form\Form;
use tt\html\form\FormfieldPassword;
use tt\html\form\FormfieldText;
use tt\html\Html;
use tt\service\ServiceStrings;

class Session {

	public static function getLoginHtml(){
		$html="";

		$form = new Form();
		$form->add_field(new FormfieldText("name", "User"));
		$form->add_field(new FormfieldPassword("pass", "Password"));

		$html .= Html::H1("Login");
		$html .= $form->toHtml();

		return $html;
	}

}