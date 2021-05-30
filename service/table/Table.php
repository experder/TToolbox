<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\table;

class Table {

	/**
	 * @var array[] $data
	 */
	private $data;

	/**
	 * @var array $head
	 */
	private $head = null;

	public function __construct(array $data) {
		$this->data = $data;
	}

	/**
	 * @param array $head
	 */
	public function setHead($head) {
		$this->head = $head;
	}

	public function __toString() {
		return $this->toHtml();
	}

	public function toHtml() {
		//TODO
		return "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
	}

}