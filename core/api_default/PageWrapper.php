<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\api_default;

class PageWrapper {

	public static function getHead($pageId){
		return "<div class='outer_body'>";
	}
	public static function getFoot($pageId){
		return "</div>";
	}

}