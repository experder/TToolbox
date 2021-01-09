<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\core;

use tt\config\Init;
use tt\core\page\Page;
use tt\install\Installer;
use tt\service\Error;

class Config {

	private static $settings = array();

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


	public static function set($cfgId, $value) {
		self::$settings[$cfgId] = $value;
	}

	public static function getIfSet($cfgId, $else) {

		if (isset(self::$settings[$cfgId])) {
			return self::$settings[$cfgId];
		}

		if (defined($cfgId)) {
			return constant($cfgId);
		}

		return $else;
	}

	public static function get($cfgId) {

		$value = self::getIfSet($cfgId, self::DEFAULT_VALUE_NOT_FOUND);
		if ($value !== self::DEFAULT_VALUE_NOT_FOUND) return $value;

		$default = self::getDefaultValue($cfgId);
		if ($default === self::DEFAULT_VALUE_NOT_FOUND) {
			new Error("No default defined for $cfgId!", 1);
		}

		return $default;
	}

	public static function getDefaultValue($cfgId) {
		switch ($cfgId) {

			case self::DEVMODE:
				return false;
			case self::CFG_PLATFORM:
				return self::PLATFORM_UNKNOWN;

			/*
			 * Init.php (project specific config's)
			 */
			case self::CFG_PROJECT_DIR:
				return dirname(dirname(__DIR__));
			case self::CFG_SERVER_INIT_FILE:
				return self::get(self::CFG_DIR) . '/init_server.php';
			case self::CFG_API_DIR:
				return self::get(self::CFG_DIR) . '/api';
			case self::DIR_3RDPARTY:
				return self::get(self::CFG_PROJECT_DIR) . '/thirdparty';

			/*
			 * CFG_SERVER_INIT_FILE (init_server.php)
			 */
			case self::HTTP_TTROOT:
				return self::get(self::HTTP_ROOT) . '/' . basename(dirname(__DIR__));
			case self::RUN_ALIAS:
				return self::get(self::HTTP_TTROOT) . '/run/?c=';
			case self::RUN_ALIAS_API:
				return self::get(self::HTTP_TTROOT) . '/run_api/';
			case self::HTTP_SKIN:
				return self::get(self::HTTP_ROOT) . '/TTconfig/skins/skin1';
			case self::HTTP_3RDPARTY:
				return self::get(self::HTTP_ROOT) . "/thirdparty";

			default:
				return self::DEFAULT_VALUE_NOT_FOUND;
		}
	}

	/**
	 * Configurations, Autoloader, Database, Session
	 */
	public static function startAjax() {

		//Configurations, Autoloader, Database
		self::startCli();

		//Session
		User::initSession();
	}

	/**
	 * Configurations, Autoloader, Database
	 */
	public static function startCli() {

		//Project specific configuration
		Init::loadConfig();

		//Autoloader
		require_once dirname(__DIR__) . '/core/Autoloader.php';
		Autoloader::init();

		//Server specific configuration, including database settings
		Installer::requireServerInit();
	}

	/**
	 * @deprecated
	 * ttdemo check
	 * core check
	 */
	public static function startWeb() {
		return self::startWeb2(null);
	}

	/**
	 * @param string $pid unique page id
	 * @return Page
	 */
	public static function startWeb2($pid) {

		//Configurations, Autoloader, Database, Session
		self::startAjax();

		//Navigation, Breadcrumbs, HTML
		$page = Page::init($pid);

		return $page;
	}

}