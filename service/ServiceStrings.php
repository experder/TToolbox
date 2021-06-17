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
	 * Example:
	 *      "<tag value = '".escape_value_html($value)."' />"
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
	 * Escapes control characters for JS.
	 * Escapes backslashes, single quotes and double quotes.
	 * @param string $value
	 * @return string
	 */
	public static function escape_value_js($value) {
		return self::replace_byArray($value, array(
			"\\" => "\\\\",
			"'" => "\\'",
			"\"" => "\\\"",
		));
	}

	/**
	 * Other syntax for the str_replace function.
	 *
	 * !!! WARNING! Keys that are a subset of other keys must be defined AFTER the other:
	 * Example 1:
	 * ServiceStrings::replace_byArray("dev dev1", array("dev"=>"A","dev1"=>"B"));
	 * => results in "A A1"
	 * Example 2:
	 * ServiceStrings::replace_byArray("dev dev1", array("dev1"=>"B","dev"=>"A"));
	 * => results in "A B"
	 *
	 * @param array  $substitutions An associative array containing the substitutions.
	 * @param string $string
	 * @return string
	 */
	public static function replace_byArray($string, $substitutions) {
		return str_replace(array_keys($substitutions), array_values($substitutions), $string);
	}

	public static function classnameSafe($string) {
		//https://www.php.net/manual/en/language.oop5.basic.php
		//('\' for namespaces)
		return preg_replace("/[^\\\\a-z0-9_\\x80-\\xff]/i", "", $string);
	}

	public static function classnameSafeCheck($string) {
		$string_checked = self::classnameSafe($string);
		return (($string_checked === $string) && $string);
	}

	public static function cssClassSafe($string) {
		//https://www.w3.org/TR/CSS2/syndata.html#characters
		return preg_replace("/[^a-zA-Z0-9\\-_\\x80-\\xff]/", "", $string);
	}

	public static function startsWith($needle, $haystack) {
		return mb_substr($haystack, 0, mb_strlen($needle)) === $needle;
	}

}