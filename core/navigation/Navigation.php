<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\navigation;

use tt\coremodule\dbmodell\core_pages;

class Navigation {

	private static $singleton = null;

	/**
	 * @var core_pages[]
	 */
	private $entries;

	/**
	 * @var core_pages[] $breadcrumbs
	 */
	private $breadcrumbs = null;

	private function __construct($entries) {
		$this->entries = $entries;
	}

	public static function getInstance() {
		if (self::$singleton === null) {
			$entries = core_pages::sql_select();
			self::$singleton = new Navigation($entries);
		}
		return self::$singleton;
	}

	/**
	 * @param string $id
	 * @return core_pages|false
	 */
	public function getEntryById($id) {
		if (!isset($this->entries[$id])) {
			return false;
		}
		return $this->entries[$id];
	}

	public function getTitleRaw($id) {
		$entry = $this->getEntryById($id);
		if ($entry === false) {
			return $id;
		}
		return $entry->getTitle();
	}

	/**
	 * @return core_pages[]
	 */
	public function getHierarchy() {
		$root = array();
		foreach ($this->entries as $entry){
			$parent = $entry->getParentEntry();
			if($parent===false){
				$root[] = $entry;
			}else{
				$parent->addChildEntry($entry);
			}
		}
		return $root;
	}

	/**
	 * @param string $highlighted_id
	 * @return core_pages[]
	 */
	public function getBreadcrumbs($highlighted_id){
		if($this->breadcrumbs===null){
			$this->breadcrumbs=$this->evaluateBreadcrumbs($highlighted_id);
		}
		return $this->breadcrumbs;
	}
	private function evaluateBreadcrumbs($highlighted_id){
		$entry = $this->getEntryById($highlighted_id);
		if($entry===false)return false;
		$breadcrumbs = $entry->getBreadcrumbs();
		return $breadcrumbs;
	}

	public function getHtml($highlighted_id) {
		$html = array();

		$root = array();
		foreach ($this->getHierarchy() as $entry) {

			$next = $entry->getHtml($highlighted_id);
			if($next!==false)$root[]=$next;

		}
		$html[] = "<ul>".implode("\n", $root)."</ul>";

//		foreach ($this->getBreadcrumbs($highlighted_id) as $item) {
//			$html[] = "--->".$item->getTitle();
//		}

		return implode("", $html);
	}

}