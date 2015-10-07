<?php namespace DiafIP {
    /***************************************************************
     * Der locale Teil der Konfigurationsdatei für Pfade / DSN-Ort
     *
     *
     * $Rev: 46 $
     * $Author: knwetzig $
     * $Date: 2014-02-02 17:31:55 +0100 (Sun, 02. Feb 2014) $
     * $URL: https://diafip.googlecode.com/svn/branches/v2/configs/config.local.php $
     ***** (c) DIAF e.V. *******************************************/

// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    set_include_path('/pear/lib:/smarty/libs:inc');
    date_default_timezone_set('Europe/Berlin');

    /* Die DSN steht in einer separaten Datei ausserhalb DocumentRoot */
    require_once '../../conf/dsn';

    $smartyConf = [
        'compile_dir'   => '/tmp',          // '/tmp';
        'config_dir'    => 'configs',       // Verzeichnis der Kongurationsdateien
        'cache_dir'     => '/tmp',
        'force_compile' => false,
        'debugging'     => false           // (true | false)
    ];
}