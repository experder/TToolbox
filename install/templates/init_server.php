<?php
/**TPLDOCSTART
 * Template for the file CFG_SERVER_INIT_FILE (e.g. "init_server.php").
 * @see \tt\install\Installer
 */
if (true) exit;/*
 * TPLDOCEND*/
/*
 * Server specific settings
 *
 * Server: SERVERNAME
 *
 */

\tt\core\Database::init('localhost', 'mytt', 'root', 'veryverysafe');

\tt\core\Config::set(\tt\core\Config::HTTP_ROOT, '/ttDemo');

/*
\tt\core\Config::set(\tt\core\Config::HTTP_RUN, \tt\core\Config::get(\tt\core\Config::HTTP_ROOT).'/TToolbox/run');
\tt\core\Config::set(\tt\core\Config::HTTP_SKIN, \tt\core\Config::get(\tt\core\Config::HTTP_ROOT).'/TTconfig/skins/skin1');
\tt\core\Config::set(\tt\core\Config::HTTP_3RDPARTY, \tt\core\Config::get(\tt\core\Config::HTTP_ROOT).'/thirdparty');
\tt\core\Config::set(\tt\core\Config::CFG_PLATFORM, \tt\core\Config::PLATFORM_UNKNOWN);
\tt\core\Config::set(\tt\core\Config::DEVMODE, false);
/**/
