<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\table;

class Table {

	public static $empty_message_ = "No data";
	private $empty_message = true;

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

	public function getEmptyMessage() {
		if($this->empty_message===true){
			$this->empty_message = self::$empty_message_;
		}
		return $this->empty_message;
	}

	/**
	 * @param array $head
	 */
	public function setHead($head) {
		$this->head = $head;
	}

	private function setDefaultHead() {
		$this->head = array();
		if (!$this->data) return;
		$row1 = $this->data[0];
		foreach ($row1 as $key=>$dummy){
			$this->head[$key] = $key;
		}
	}

	public function __toString() {
		return $this->toHtml();
	}

	private function getHead(){
		if($this->head===null){
			$this->setDefaultHead();
		}
		return $this->head;
	}

	public function toHtml() {
		if(!$this->data)return $this->getEmptyMessage();

		$head = $this->getHead();

		$html_head = "<tr><th>".implode("</th><th>",$head)."</th></tr>";

		$html_body = $this->getBody($head);

		$html_table = "<table>$html_head\n$html_body</table>";

		return $html_table;
	}

	private function getBody($header) {

		$rows = array();

		foreach ($this->data as $row_data){

			$row_html = array();

			foreach ($header as $key=>$dummy){

				$row_html[] = isset($row_data[$key])?$row_data[$key]:"";

			}

			$rows[] = "<tr><td>".implode("</td><td>",$row_html)."</td></tr>";

		}

		return implode("\n", $rows);
	}

}