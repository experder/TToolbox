<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

use tt\core\Config;

class ServiceFiles {

	/**
	 * Resolves paths like "/var/www/project/../p2/file.php" (=> "/var/www/p2/file.php")
	 * @param string $path Original path
	 * @return string Resolved path
	 */
	public static function cleanupRelativePath($path) {

		//Windows:
		$path = ServiceFiles::windowsPath($path);

		$loop = true;
		while ($loop) {
			$path_before = $path;
			$path = preg_replace("/\\/[a-z0-9_]+\\/\\.\\.\\//i", "/", $path);
			$loop = $path != $path_before;
		}

		return $path;
	}

	public static function windowsPath($path) {
		$path = preg_replace("/\\\\/", "/", $path);
		return $path;
	}

	/**
	 * Saves a string to a file.
	 * @param string $filename
	 * @param string $content
	 * @param bool   $append
	 * @return bool|int the number of bytes written, or <b>FALSE</b> on error.
	 */
	public static function save($filename, $content, $append = false) {
		$dirname = dirname($filename);
		if (!is_dir($dirname)) {
			@mkdir($dirname, 0755, true);
		}

		if (!is_dir($dirname)) {
			$platform = Config::get(Config::CFG_PLATFORM);
			new Error("Couldn't create directory \"$dirname\". Please check rights."
				. (($platform == Config::PLATFORM_UNKNOWN
					|| $platform == Config::PLATFORM_LINUX)
					? "\nTry this:\nsudo chmod 777 '" . dirname($dirname) . "/' -R"
					: "")
			);
		}

		$success = false;

		if (is_resource($content)) {
			$success = @file_put_contents($filename, $content, $append ? FILE_APPEND : 0);
		} else {
			$file = @fopen($filename, $append ? "a" : "w");
			if ($file !== false) {
				$success = fwrite($file, $content);
				fclose($file);
			}
		}
		if ($success === false) {
			$platform = Config::get(Config::CFG_PLATFORM);
#			echo "error!";exit;
			//TODO: message is not shown for init_pointer.php
			new Error("Couldn't store file \"$filename\". Please check rights."
				. (($platform == Config::PLATFORM_UNKNOWN
					|| $platform == Config::PLATFORM_LINUX)
					? "\nTry this:\nsudo chmod 777 '" . dirname($filename) . "' -R"
					: ""
				)
			);
		}
		return $success;
	}

	//TODO:CamelCase
	public static function get_contents($file) {
		//TODO: Option: Restriction to external resources (URLs)
		if (!file_exists($file)) {
			new Error("File does not exist!\nFile: $file", 1);
		}
		return file_get_contents($file);
	}

	public static function dirList($path, $absolute=false, $utf8_out = true, $exclude_assoc = array()) {
		if (!file_exists($path)) return false;
		$dir = opendir($path);
		if (!$dir) return false;
		$files = array();
		while (false !== ($file = readdir($dir))) {
			if ($file == '.' || $file == '..' || isset($exclude_assoc[$file])){
				continue;
			}
			$entry = ($utf8_out ? utf8_encode($file) : $file);
			if($absolute){
				$entry = $path."/".$entry;
			}
			$files[] = $entry;
		}
		closedir($dir);
		return $files;
	}

}