<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core;

use tt\core\database\DB;
use tt\coremodule\dbmodell\core_config;
use tt\core\database\Database;
use tt\core\page\Page;
use tt\install\Installer;
use tt\service\Error;
use tt\service\ServiceEnv;

Config::$startTimestamp = microtime(true);

class Config {

	public static $startTimestamp;

	private static $config_cache = array();

	private static $settings = array();

	public static $project_initialized = false;

	const DBCFG_DB_VERSION = "DB_VERSION";

	const DEFAULT_VALUE_NOT_FOUND = "!TTDEFVALNOTFOUND!";

	const PLATFORM_UNKNOWN = 0;
	const PLATFORM_WINDOWS = 1;
	const PLATFORM_LINUX = 2;

	const PROJ_TITLE = 'PROJ_TITLE';
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

	/**
	 * @deprecated TODO
	 */
	public static function get2($cfgId, $else=null) {
		return self::get($cfgId, $else);
	}
	public static function get($cfgId, $else=null) {

		if (isset(self::$settings[$cfgId])) {
			return self::$settings[$cfgId];
		}

		return $else;
	}
	public static function getChecked($cfgId) {

		if (isset(self::$settings[$cfgId])) {
			return self::$settings[$cfgId];
		}

		new Error("Config value not set: '$cfgId'", 1);
		return false;
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

		//Will we have to UPDATE or INSERT?
		$data_exists = DB::select(
			"SELECT ".core_config::ROW_id
			. " FROM " . core_config::getTableName()
			. " WHERE " . core_config::ROW_idstring . "=:ID"
			. " AND " . core_config::ROW_module . "=:MOD"
			. " AND " . core_config::ROW_userid . "<=>:USR",
			array(
				":ID" => $key,
				":MOD" => $module,
				":USR" => $user,
			)
		);

		if($data_exists){
			//UPDATE
			if(count($data_exists)>1){
				new Error("Config database is corrupt! Duplicate entry for '$key'!");
			}
			$id = $data_exists[0][core_config::ROW_id];
			Database::getPrimary()->_query(
				"UPDATE " . core_config::getTableName()
				. " SET " . core_config::ROW_content . "=:VAL"
				. " WHERE " . core_config::ROW_id . "=".$id,
				array(
					":VAL" => $value,
				)
			);
		}else{
			//INSERT
			DB::insertAssoc(core_config::getTableName(), array(
				core_config::ROW_idstring=>$key,
				core_config::ROW_content=>$value,
				core_config::ROW_module=>$module,
				core_config::ROW_userid=>$user,
			));
		}

		self::storeVal($value, $module, $key, $user);
	}

	public static function init_cli() {

		//Autoloader
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		//Init_project called?
		self::checkProjectInitialization();

		//Modules
		Modules::init();

		//Server specific configuration, including database settings
		require_once Config::get(Config::CFG_SERVER_INIT_FILE);

	}

	public static function init_api() {

		//Autoloader
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		//Init_project called?
		self::checkProjectInitialization();

		//API calls return JSON
		ServiceEnv::$response_is_expected_to_be_json = true;

		//Modules
		Modules::init();

		//Server specific configuration, including database settings
		require_once Config::get(Config::CFG_SERVER_INIT_FILE);

		//Session, Authentication
		User::initSession();

	}

	private static function checkProjectInitialization() {
		if(!self::$project_initialized){
			new Error("Please initialize project settings. (require 'Init_project.php')", 2);
		}
	}

	/**
	 * @param string $pid unique page id
	 * @return Page
	 */
	public static function init_web($pid) {

		//Autoloader
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		//Init_project called?
		self::checkProjectInitialization();

		//Modules
		Modules::init();

		//Server specific configuration, including database settings
		Installer::requireInitServer();

		//Session, Authentication
		$token = User::initSession();

		//Breadcrumbs, HTML
		$page = Page::init($pid, $token);

		return $page;
	}

}