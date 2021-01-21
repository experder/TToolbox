<?php
/**TPLDOCSTART
 * Template for the file "init_pointer.php" (ignored in git).
 * @see \tt\install\Installer::requireInitPointer()
 */
if (true) exit;/*
 * TPLDOCEND*/

#require_once dirname(__DIR__).'/TTconfig/Init_project.php';
#require_once '#INIT_WEB_PATH';

require_once __DIR__ . '/install/Installer.php';
\tt\install\Installer::requireInitProject('#INIT_WEB_PATH');
