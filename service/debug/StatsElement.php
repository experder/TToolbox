<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service\debug;

use tt\core\page\Page;

class StatsElement {

	private $content;
	private $class;
	private $title;

	/**
	 * @param string $title
	 * @param string $content HTML
	 * @param string $class CSS class
	 */
	public function __construct($title, $content, $class=null) {
		$this->title = $title;
		$this->content = $content;
		$this->class = $class;
	}

	public function toHtml(){
		$id = Page::getNextGlobalId();
		if($this->content){
			$btn = "<div class='statsBtn expand' onclick=\"$('#$id').toggle(400);\">$this->title</div>";
			$class = 'statsContent'.($this->class?" ".$this->class:"");
			$content = "<div class='$class'>$this->content</div>";
			$content = "<div class='contentWrapper' id='$id'>$content</div>";
			return $btn.$content;
		}
		return "<div class='statsBtn'>$this->title</div>";
	}

}