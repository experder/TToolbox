<?php
/**TPLDOCSTART
 * Template for the file CFG_DIR."/Init.php".
 * @see \tt\install\Installer::requireInit()
 */
if (true) exit;/*
 * TPLDOCEND*/

//TPL:namespace tt\config;

use tt\core\Config;

require_once '#TToolbox' . '/core/Config.php';

class Init {

	public static function loadConfig() {

		Config::set(Config::PROJ_NAMESPACE_ROOT, '#PROJ_NAMESPACE_ROOT');
		Config::set(Config::CFG_DIR, __DIR__);
		/*
		Config::set(Config::CFG_PROJECT_DIR, dirname(__DIR__));
		Config::set(Config::CFG_SERVER_INIT_FILE, __DIR__.'/init_server.php');
		Config::set(Config::DIR_3RDPARTY, dirname(__DIR__).'/thirdparty');
		Config::set(Config::CFG_API_DIR, Config::get(Config::CFG_DIR) . '/api');
		/**/

		//Enable multi Autoloader (Autoloader doesn't terminate on error):
		#require_once '#TToolbox'.'/core/Autoloader.php';
		#\tt\core\Autoloader::multipleAutoloader();

	}

	/**
	 * @param string $pid unique page id
	 * @return \tt\core\page\Page
	 */
	public static function initWeb($pid) {
		return Config::startWeb($pid);
	}

}
