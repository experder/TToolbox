<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\service;

class ServiceFiles {

	/**
	 * Resolves paths like "/var/www/project/../p2/file.php" (=> "/var/www/p2/file.php")
	 * @param string $path Original path
	 * @return string Resolved path
	 */
	public static function cleanupRelativePath($path){

		//Windows:
		$path = ServiceFiles::windowsPath($path);

		$loop = true;
		while ($loop){
			$path_before = $path;
			$path=preg_replace("/\\/[a-z0-9_]+\\/\\.\\.\\//i", "/", $path);
			$loop = $path!=$path_before;
		}

		return $path;
	}

	public static function windowsPath($path){
		$path = preg_replace("/\\\\/", "/", $path);
		return $path;
	}


}