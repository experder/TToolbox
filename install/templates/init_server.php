<?php
/**TPLDOCSTART
 * Template for the file CFG_SERVER_INIT_FILE (e.g. "init_server.php").
 * @see \tt\install\Installer::requireServerInit()
 */
if (true) exit;/*
 * TPLDOCEND*/

/*
 * Server specific settings
 *
 * Server: #SERVERNAME
 *
 */

use tt\core\database\core_model\core_config;
use tt\core\database\Database;
use tt\core\Config;

Config::set(Config::DEVMODE, '#DEVMODE');

Config::set(Config::DB_CORE_PREFIX, 'core');

core_config::setTableName(Config::get(Config::DB_CORE_PREFIX) . '_config');

Database::init('#DB_HOST', '#DB_NAME', '#DB_USER', '#DB_PASS');

Config::set(Config::HTTP_ROOT, '#HTTP_ROOT');

Config::set(Config::HTTP_TTROOT, '#HTTP_TTROOT');

Config::set(Config::HTTP_SKIN, Config::get(Config::HTTP_ROOT) .'/'. basename(Config::get(Config::CFG_DIR)) . '/skins/skin1');

Config::set(Config::HTTP_3RDPARTY, Config::get(Config::HTTP_ROOT) . '/thirdparty');

Config::set(Config::RUN_ALIAS_API, Config::get(Config::HTTP_TTROOT) . '/run_api/');

Config::set(Config::RUN_ALIAS, Config::get(Config::HTTP_TTROOT) . '/run/?c=');

Config::set(Config::CFG_PLATFORM, '#PLATFORM');
