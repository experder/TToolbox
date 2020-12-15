<?php
/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

namespace tt\config;

use tt\autoload\Autoloader;
use tt\debug\Error;
use tt\page\Page;
use tt\service\ServiceEnv;
use tt\service\ServiceFiles;
use tt\usermgmt\User;

class Config {

	/**
	 * @var Config $instance
	 */
	private static $instance = null;

	private function __construct() {
	}

	public static function getInstance(){
		return self::$instance;
	}

	public static function init(){
		if(self::$instance!==null)return false;
		self::$instance = new Config();
		return self::$instance;
	}

	private $init_server_dir = null;
	private $init_server_file = null;
	private $init_project_dir = null;
	private $init_project_file = null;

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

	public static $DEVMODE = false;

	public function startWeb(){

		$this->initAutoloader();

		$this->initServerCfg();

		$this->initProjectCfg();

		$page = Page::init();

		User::initSession();

		return $page;
	}

	public function initAutoloader(){
		require_once dirname(__DIR__).'/autoload/Autoloader.php';
		Autoloader::init();
	}

	public function initServerCfg(){
		ServiceEnv::requireFile($this->getServerFile(), "Server specific config file not found.");
	}

	public function initProjectCfg(){
		ServiceEnv::requireFile($this->getProjectFile(), "Project specific config file not found.");
	}

}