<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core;

use tt\config\Init;
use tt\core\auth\Token;
use tt\coremodule\dbmodell\core_config;
use tt\core\database\Database;
use tt\core\page\Page;
use tt\install\Installer;
use tt\service\Error;

class Config {

	private static $config_cache = array();

	private static $settings = array();

	const DBCFG_DB_VERSION = "DB_VERSION";

	const DEFAULT_VALUE_NOT_FOUND = "!TTDEFVALNOTFOUND!";

	const PLATFORM_UNKNOWN = 0;
	const PLATFORM_WINDOWS = 1;
	const PLATFORM_LINUX = 2;

	const CFG_PROJECT_DIR = 'CFG_PROJECT_DIR';
	const PROJ_NAMESPACE_ROOT = 'PROJ_NAMESPACE_ROOT';
	const CFG_DIR = 'CFG_DIR';
	const CFG_API_DIR = 'CFG_API_DIR';
	const CFG_SERVER_INIT_FILE = 'CFG_SERVER_INIT_FILE';
	const DIR_3RDPARTY = 'DIR_3RDPARTY';
	const HTTP_TTROOT = 'HTTP_TTROOT';
	const HTTP_SKIN = 'HTTP_SKIN';
	const HTTP_3RDPARTY = 'HTTP_3RDPARTY';
	const CFG_PLATFORM = 'CFG_PLATFORM';
	const DEVMODE = 'DEVMODE';
	const HTTP_ROOT = 'HTTP_ROOT';
	const RUN_ALIAS = 'RUN_ALIAS';
	const RUN_ALIAS_API = 'RUN_ALIAS_API';
	const DB_CORE_PREFIX = 'DB_CORE_PREFIX';

	public static function set($cfgId, $value) {
		self::$settings[$cfgId] = $value;
	}

	public static function get2($cfgId, $else=null) {

		if (isset(self::$settings[$cfgId])) {
			return self::$settings[$cfgId];
		}

		return $else;
	}

	public static function getValue($id, $module, $user = null, $default_value = null) {
		$value = self::recallVal($module, $id, $user);
		if ($value !== false) {
			return $value;
		}

		$database = Database::getPrimary();

		$data = $database->_query("SELECT ".core_config::ROW_content." FROM ".core_config::getTableName()." WHERE ".core_config::ROW_idstring."=:ID AND ".core_config::ROW_module."=:MOD AND ".core_config::ROW_userid."<=>:USR LIMIT 1;", array(
			":ID"=>$id,
			":USR"=>$user,
			":MOD"=>$module,
		), Database::RETURN_ASSOC);

		if(!$data || !is_array($data) || count($data)<1){
			return $default_value;
		}

		if(count($data)>1){
			new Error(
				"Config database corrupt! Multiple entries found for \"$id\" (module '$module'".($user?", user #$user":"").")."
			);
		}

		$value = $data[0][core_config::ROW_content];

		self::storeVal($value, $module, $id, $user);

		return $value;
	}

	private static function recallVal($module, $key, $user = null) {
		$user_index = 'u' . ($user?:0);
		if (!isset(self::$config_cache[$module][$user_index][$key])) {
			return false;
		}
		return self::$config_cache[$module][$user_index][$key];
	}

	private static function storeVal($value, $module, $key, $user=null) {
		$user_index = 'u' . ($user?:0);
		self::$config_cache[$module][$user_index][$key] = $value;
	}


	public static function setValue($value, $key, $module, $user = null) {
		$database = Database::getPrimary();

		$response = $database->_query("UPDATE ".core_config::getTableName()." SET ".core_config::ROW_content."=:VAL WHERE ".core_config::ROW_idstring."=:ID AND ".core_config::ROW_module."=:MOD AND ".core_config::ROW_userid."<=>:USR LIMIT 1;", array(
			":VAL"=>$value,
			":ID"=>$key,
			":MOD"=>$module,
			":USR"=>$user,
		), Database::RETURN_ROWCOUNT);

		if($response===0){
			//Update failed: Insert!

			//TODO: Database:INSERT_ASSOC
			$database->_query("INSERT INTO ".core_config::getTableName()." (
`".core_config::ROW_idstring."` ,
`".core_config::ROW_module."` ,
`".core_config::ROW_userid."` ,
`".core_config::ROW_content."`
)
VALUES (
 :ID, :MOD, :USR, :VAL
);", array(
				":VAL"=>$value,
				":ID"=>$key,
				":MOD"=>$module,
				":USR"=>$user,
			));
		}


		self::storeVal($value, $module, $key, $user);

	}

	public static function startCli() {

		//Configurations, Autoloader, Modules, Database
		self::init1();

	}

	public static function startApi() {

		//Configurations, Autoloader, Modules, Database
		self::init1();

		//Session, Authentication
		self::init2();

	}

	/**
	 * @param string $pid unique page id
	 * @return Page
	 */
	public static function startWeb($pid) {

		//Configurations, Autoloader, Modules, Database
		self::init1();

		//Session, Authentication
		$token = self::init2();

		//Navigation, Breadcrumbs, HTML
		$page = self::init3($pid, $token);

		return $page;
	}

	/**
	 * Configurations, Autoloader, Modules, Database
	 */
	public static function init1() {

		//Project specific configuration
		Init::loadConfig();

		//Autoloader
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		//Modules
		Modules::init();

		//Server specific configuration, including database settings
		Installer::requireServerInit();

	}

	/**
	 * Session, Authentication
	 * @return Token
	 */
	public static function init2() {

		//Session, Authentication
		$token = User::initSession();

		return $token;
	}

	/**
	 * Navigation, Breadcrumbs, HTML
	 *
	 * @param string $pid unique page id
	 * @param Token  $token
	 * @return Page
	 */
	public static function init3($pid, Token $token) {

		//Navigation, Breadcrumbs, HTML
		$page = Page::init($pid, $token);

		return $page;

	}

}