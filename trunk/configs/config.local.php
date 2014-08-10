<?php
/***************************************************************
Der locale Teil der Konfigurationsdatei für Pfade / DSN-Ort


$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
set_include_path('/pear/lib:/smarty/libs:inc');
date_default_timezone_set('Europe/Berlin');

/* Die DSN steht in einer separaten Datei ausserhalb DocumentRoot */
require_once    '/conf/dsn';

$smartyConf = array(
    'compile_dir'     => '/tmp',          // '/tmp';
    'config_dir'      => 'configs',       // Verzeichnis der Kongurationsdateien
    'cache_dir'       => '/tmp',
    'force_compile'   =>  true,
    'debugging'       =>  false           // (true | false)
);
?>