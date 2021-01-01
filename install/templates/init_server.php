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

\tt\core\Database::init('#DB_HOST', '#DB_NAME', '#DB_USER', '#DB_PASS');

\tt\core\Config::set(\tt\core\Config::HTTP_ROOT, '#HTTP_ROOT');
\tt\core\Config::set(\tt\core\Config::HTTP_TTROOT, '#HTTP_TTROOT');
/*
\tt\core\Config::set(\tt\core\Config::HTTP_RUN, \tt\core\Config::get(\tt\core\Config::HTTP_TTROOT).'/run');
\tt\core\Config::set(\tt\core\Config::HTTP_SKIN, \tt\core\Config::get(\tt\core\Config::HTTP_ROOT).'/TTconfig/skins/skin1');
\tt\core\Config::set(\tt\core\Config::HTTP_3RDPARTY, \tt\core\Config::get(\tt\core\Config::HTTP_ROOT).'/thirdparty');
/**/

#\tt\core\Config::set(\tt\core\Config::RUN_ALIAS, \tt\core\Config::get(\tt\core\Config::HTTP_RUN) . '/?c=');
\tt\core\Config::set(\tt\core\Config::RUN_ALIAS, '#RUNALIAS');

\tt\core\Config::set(\tt\core\Config::CFG_PLATFORM, '#PLATFORM');

\tt\core\Config::set(\tt\core\Config::DEVMODE, '#DEVMODE');
