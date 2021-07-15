<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core\api_default;

use tt\coremodule\dbmodell\core_pages;

class Navigation {

	private static $singleton = null;

	/**
	 * @var core_pages[]
	 */
	protected $entries = null;

	/**
	 * @var core_pages[] $breadcrumbs
	 */
	private $breadcrumbs = null;

	protected function __construct() {
		$this->load();
	}

	protected function load() {
		$this->entries = core_pages::sql_select();
	}

	public static function getInstance() {
		if (self::$singleton === null) {
			self::$singleton = new \tt\api\Navigation();
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
		foreach ($this->entries as $entry) {
			$parent = $entry->getParentEntry();
			if ($parent === false) {
				$root[] = $entry;
			} else {
				$parent->addChildEntry($entry);
			}
		}
		return $root;
	}

	/**
	 * @param string $highlighted_id
	 * @return core_pages[]
	 */
	public function getBreadcrumbs($highlighted_id) {
		if ($this->breadcrumbs === null) {
			$this->breadcrumbs = $this->evaluateBreadcrumbs($highlighted_id);
		}
		return $this->breadcrumbs;
	}

	private function evaluateBreadcrumbs($highlighted_id) {
		$entry = $this->getEntryById($highlighted_id);
		if ($entry === false) return false;
		$breadcrumbs = $entry->getBreadcrumbs();
		return $breadcrumbs;
	}

	public function getBreadcrumbsHtml() {
		$breadcrumbs = $this->getBreadcrumbs(null);
		if (!$breadcrumbs || count($breadcrumbs) < 2) return "";
		$html = array();
		foreach ($breadcrumbs as $item) {
			$bc = $item->getHtmlInner(false);
			if ($bc) $html[] = $bc;
		}
		$string = implode("\n<span class='breadcrumb_next'>&gt;</span> ", $html);
		return "<nav class='breadcrumbs'>$string</nav>";
	}

	public function getHtml($highlighted_id) {
		$html = array();

		$root = array();
		foreach ($this->getHierarchy() as $entry) {

			$next = $entry->getHtml($highlighted_id);
			if ($next !== false) $root[] = $next;

		}
		$html[] = "<ul>" . implode("\n", $root) . "</ul>";

		return implode("", $html);
	}

}