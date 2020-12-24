<?php
/**TPLDOCSTART
 * Template for the file "init_web_pointer.php" (ignored in git).
 * @see \tt\install\Installer::requireWebPointer()
 */
if (true) exit;/*
 * TPLDOCEND*/

#require_once dirname(__DIR__).'/TTconfig/init_web.php';
#require_once '#INIT_WEB_PATH';

require_once __DIR__ . '/install/Installer.php';
\tt\install\Installer::requireInitWeb('#INIT_WEB_PATH');
