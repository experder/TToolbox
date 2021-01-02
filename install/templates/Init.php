<?php
/**TPLDOCSTART
 * Template for the file CFG_DIR."/Init.php".
 * @see \tt\install\Installer::requireInit()
 */
if (true) exit;/*
 * TPLDOCEND*/

//TPL:namespace tt\config;

require_once '#TToolbox' . '/core/Config.php';

class Init {

	public static function loadConfig() {

		\tt\core\Config::set(\tt\core\Config::PROJ_NAMESPACE_ROOT, '#PROJ_NAMESPACE_ROOT');
		\tt\core\Config::set(\tt\core\Config::CFG_DIR, __DIR__);
		/*
		\tt\core\Config::set(\tt\core\Config::CFG_PROJECT_DIR, dirname(__DIR__));
		\tt\core\Config::set(\tt\core\Config::CFG_SERVER_INIT_FILE, __DIR__.'/init_server.php');
		\tt\core\Config::set(\tt\core\Config::DIR_3RDPARTY, dirname(__DIR__).'/thirdparty');
		/**/

		//Enable multi Autoloader (Autoloader doesn't terminate on error):
		#require_once '#TToolbox'.'/core/Autoloader.php';
		#\tt\core\Autoloader::multipleAutoloader();

	}

}
