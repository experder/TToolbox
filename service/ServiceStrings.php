<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

class ServiceStrings {

	/**
	 * Escapes quotes with htmlentities.
	 * Escapes single quotes, double quotes and the ampersand.
	 * Examples:
	 *      "<tag value = '".escape_value_html($value)."' />"
	 *      "<tag value = \"".escape_value_html($value)."\" />"
	 *      '<tag value = "'.escape_value_html($value).'" />'
	 * @param string $value
	 * @return string
	 */
	public static function escape_value_html($value) {
		return self::replace_byArray($value, array(
			"&" => "&amp;",
			"\"" => "&quot;",
			"'" => "&apos;",
		));
	}

	/**
	 * Other syntax for the str_replace function.
	 *
	 * !!! WARNING! Keys that are a subset of other keys must be defined AFTER the other:
	 * Example 1:
	 * ServiceStrings::replace_byArray("dev dev1", array("dev1"=>"B","dev"=>"A"));
	 * => results in "A B"
	 * Example 2:
	 * ServiceStrings::replace_byArray("dev dev1", array("dev"=>"A","dev1"=>"B"));
	 * => results in "A A1"
	 *
	 * @param array  $substitutions An associative array containing the substitutions.
	 * @param string $string
	 * @return string
	 */
	public static function replace_byArray($string, $substitutions) {
		return str_replace(array_keys($substitutions), array_values($substitutions), $string);
	}

	public static function classnameSafe($string){
		return $string;//preg_replace("/[^a-z0-9\\\\_]i/", "", $string);
	}

}