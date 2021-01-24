<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\navigation;

use tt\coremodule\dbmodell\core_navigation;

class Navigation {

	/**
	 * @var core_navigation[]
	 */
	private $entries;

	public function __construct($entries) {
		$this->entries = $entries;
	}

	public static function fromDb(){
		$entries = core_navigation::sql_select();
		return new Navigation($entries);
	}

	public function getEntryById($id){
		if(!isset($this->entries[$id])){
			return false;
		}
		return $this->entries[$id];
	}

	public function getTitleRaw($id){
		$entry = $this->getEntryById($id);
		if($entry===false){
			return $id;
		}
		return $entry->getTitle();
	}

	public function getHtml($highlighted_id){
		$html = array();

		foreach ($this->entries as $entry){

			$html[] = $entry->getHtml($highlighted_id);

		}

		return implode("", $html);
	}

}