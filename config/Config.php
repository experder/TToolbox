<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\config;

use tt\autoload\Autoloader;
use tt\page\Page;
use tt\service\ServiceEnv;
use tt\usermgmt\User;

class Config {

	/**
	 * @var Config $instance
	 */
	private static $instance = null;

	private function __construct() {
	}

	public static function getInstance(){
		if(self::$instance===null){
			self::$instance=new Config();
		}
		return self::$instance;
	}

	private $init_server_dir = null;
	private $init_server_file = null;
	private $init_project_dir = null;
	private $init_project_file = null;

	public static $DEVMODE = false;

	const PLATFORM_UNKNOWN = 0;
	const PLATFORM_WINDOWS = 1;
	const PLATFORM_LINUX = 2;

	private static $platform = Config::PLATFORM_UNKNOWN;

	/**
	 * @return int Config::PLATFORM_
	 */
	public static function getPlatform() {
		return self::$platform;
	}

	/**
	 * @param int $platform Config::PLATFORM_
	 */
	public static function setPlatform($platform) {
		self::$platform = $platform;
	}

	public static function isPlatformWindows() {
		return self::$platform = self::PLATFORM_WINDOWS;
	}
	public static function isPlatformLinux() {
		return self::$platform = self::PLATFORM_LINUX;
	}
	public static function isPlatformUnknown() {
		return self::$platform = self::PLATFORM_UNKNOWN;
	}

	public function getServerDir(){

		//DEFAULT:
		if($this->init_server_dir===null)$this->init_server_dir=dirname(dirname(__DIR__)).'/TTconfig';

		return $this->init_server_dir;
	}
	public function getServerFile(){

		//DEFAULT:
		if($this->init_server_file===null)
			$this->init_server_file=dirname(dirname(__DIR__)).'/TTconfig/init_server.php';

		return $this->init_server_file;
	}
	public function getProjectDir(){

		//DEFAULT:
		if($this->init_project_dir===null)$this->init_project_dir=dirname(dirname(__DIR__));

		return $this->init_project_dir;
	}
	public function getProjectFile(){

		//DEFAULT:
		if($this->init_project_file===null)
			$this->init_project_file=dirname(dirname(__DIR__)).'/TTconfig/init_project.php';

		return $this->init_project_file;
	}
	public function setServerDir($dir){
		$this->init_server_dir=$dir;
	}
	public function setServerFile($file){
		$this->init_server_file=$file;
	}
	public function setProjectDir($dir){
		$this->init_project_dir=$dir;
	}
	public function setProjectFile($file){
		$this->init_project_file=$file;
	}

	public function startWeb(){

		require_once dirname(__DIR__).'/autoload/Autoloader.php';
		Autoloader::init();

		$this->initServerCfg();

		$this->initProjectCfg();//TODO: Deprecated

		$page = Page::getInstance();

		User::initSession();

		return $page;
	}

	public function initServerCfg(){
		ServiceEnv::requireFile($this->getServerFile(), "Server specific config file not found.");
	}

	public function initProjectCfg(){
		ServiceEnv::requireFile($this->getProjectFile(), "Project specific config file not found.");
	}

}