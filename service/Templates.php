<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

use tt\debug\Error;

class Templates {

	const RETURN_OK = 1;
	const RETURN_FILE_EXISTS = -1;

	/**
	 * Loads a template file, fills in the values and returns the content as a string.
	 * Comments marked as follows will be removed:
	 * &#47;&#42;&#42;TPLDOCSTART This comment will be removed TPLDOCEND&#42;&#47;
	 * @param string $file
	 * @param array  $replacements
	 * @return string
	 */
	public static function load($file, $replacements) {
		if (!file_exists($file)) {
			new Error("Template file \"$file\" not found!");
		}

		//Read template file:
		$content = file_get_contents($file);

		if ($content === false) {
			new Error("Template file \"$file\" could not be loaded.");
		}

		//Replacements:
		$content = ServiceStrings::replace_byArray($content, $replacements);

		//Remove TPLDOC:
		/** https://github.com/experder/T2/blob/master/help/dev_regex.md */
		$content = preg_replace("/\\/\\*\\*TPLDOCSTART.*?TPLDOCEND\\*\\/\\R?/s", "", $content);

		return $content;
	}

	/**
	 * @param string  $target_file
	 * @param string  $template_file
	 * @param array[] $keyVals
	 * @param bool    $override
	 * @param bool    $report_error
	 * @return int Errornumber
	 */
	public static function create_file($target_file, $template_file, $keyVals, $override = false, $report_error = true) {
		if (!$override && file_exists($target_file)) {
			if ($report_error) {
				new Error("Couldn't store file \"$target_file\". File already exists!");
			}
			return self::RETURN_FILE_EXISTS;
		}
		$content = self::load($template_file, $keyVals);
		$success = ServiceFiles::save($target_file, $content);
		if ($success === false) {
			new Error("Couldn't store file \"$target_file\". Please check rights."
				."\nTry this:\nsudo chmod 777 '" . dirname($target_file) . "' -R");
		}
		return self::RETURN_OK;
	}

}