<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

class Files {

	/**
	 * Saves a string to a file.
	 * @param string $filename
	 * @param string $content
	 * @param bool   $append
	 * @param bool   $halt_on_error
	 * @return bool|int the number of bytes written, or <b>FALSE</b> on error.
	 */
	public static function save($filename, $content, $append = false, $halt_on_error = true, $create_dir=true) {
		$dirname = dirname($filename);
		if (!is_dir($dirname) && $create_dir) {
			mkdir($dirname, 0755, true);
		}
		$file = @fopen($filename, $append ? "a" : "w");
		$success = false;
		if ($file !== false) {
			$success = fwrite($file, $content);
			fclose($file);
		}
		if ($success === false && $halt_on_error) {
			new Error("Failure_on_storing_file", "Failure on storing file \"$filename\"!", null, 1);
		}
		return $success;
	}

	public static function relative_path($from, $to) {

		//Insipred by: https://stackoverflow.com/a/2638272
		//Windows:
		$from = str_replace('\\', '/', $from);
		$to = str_replace('\\', '/', $to);
		$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
		$to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;

		$from = explode('/', $from);
		$to = explode('/', $to);
		$relPath = $to;

		foreach ($from as $depth => $dir) {
			// find first non-matching dir
			if ($dir === $to[$depth]) {
				// ignore this directory
				array_shift($relPath);
			} else {
				// get number of remaining dirs to $from
				$remaining = count($from) - $depth;
				if ($remaining > 1) {
					// add traversals up to first matching dir
					$padLength = (count($relPath) + $remaining - 1) * -1;
					$relPath = array_pad($relPath, $padLength, '..');
					break;
				} else {
					$relPath[0] = './' . $relPath[0];
				}
			}
		}
		$rel = implode('/', $relPath);
		$rel = rtrim($rel, '\/');
		$rel = self::cleanup_relative_path($rel);
		return $rel;
	}

	public static function cleanup_relative_path($path) {
		if (!$path) {
			return false;
		}
		$old = "";
		$new = $path;
		while ($old !== $new) {
			$old = $new;
			$new = preg_replace("/\\/[^\\/]*?\\/\\.\\.\\//", "/", $old);
		}
		return $new;
	}

	public static function get_contents($file, $depth = 0) {
		if (!file_exists($file)) {
			new Error("T2_FILE_NOT_FOUND", "File not found!", "File: $file", $depth + 1);
		}
		return file_get_contents($file);
	}

}