<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\debug;

class StatsElement {

	private $content;
	private $class;

	/**
	 * @param string $content HTML
	 * @param string $class CSS class
	 */
	public function __construct($content, $class=null) {
		$this->content = $content;
		$this->class = $class;
	}

	public function toHtml(){
		$class = $this->class?"class='$this->class'":"";
		return "<div $class>$this->content</div>";
	}

}