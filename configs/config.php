<?php
/***************************************************************
Konfigurationsdatei für Konstantendefinition / Pfade / DSN-Ort
Die DSN steht in einer separaten Datei ausserhalb DocumentRoot
Lädt den Header!

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

error_reporting(E_ALL & ~E_DEPRECATED);
set_include_path('/pear/lib:/smarty/libs:inc');
date_default_timezone_set('Europe/Berlin');
require_once	'service.php';
require_once	'Auth.php';
require_once	'MDB2.php';
require_once	'Smarty.class.php';
require_once    '../../conf/dsn';

const
    // RegExpressions
    NAMEN     = '^[^`{}"-,:-@[-^]+$',
    NAME_LEER = '[^`{}"-,:-@[-^]*',
    ANZAHL    = '([1-9]+[\d]*){1,1}',
    DZAHL     = '^[-+]?[\d]*[.,]?[\d]+([eE][-+]?[\d]+)?',
    BOOL      = '(^(true|[1]|false|[0])\b){1,1}',
    TELNR     = '^[+\d][\d\s]*$',              // +49 351 123456
    EMAIL     = '[\w].*[@].*[.][\w]{2,3}',   //xxx@yyy.zzz
    DATUM     =     '[\d]{4,4}[\D\W][\d]{1,2}[\D\W][\d]{1,2}|[\d]{4,4}[0-1][\d][0-3][\d]|[\d]{1,2}[\D\W][\d]{1,2}[\D\W][\d]{2,4}',
    /* Vorsicht nicht Narrensicher! Kann nur der groben Prüfung dienen
        1999-2.31 ISO (Trenner kann alles ausser Buchtabe/Ziffer sein)
        19991231  ISO
        31.12/19 German/Euro/US (Sehr locker)
    */

    // Rechte-Zuweisung
    VIEW    =  0,       // sieht alle allgemein zugängl. Daten
    IVIEW   =  1,       // sieht auch interne Daten
    EDIT    =  2,       // kann allg. Daten editieren
    IEDIT   =  3,       // editieren interner Daten (Personaldaten etc)
    SEDIT   =  4,       // kann Presets bearbeiten
    DELE    =  5,       // Löschberechtigung
    ARCHIV  =  6,       // Depotverwaltung
    /*
    .
    siehe auch configs/adm_user.php */
    ADMIN   = 15,
    SU      = 16,

    WERT_QUOT = 0.05;   // Wertsteigerungsquotient zur Berechnung der Vers.Summe

// Sektion für 'sektion'
$datei = array(
    'admin'     => "configs/admin.php",
    'film'	    => "inc/ev_figd.php",		    // filmografische Daten
    'person'    => "inc/ev_pers.php",           // Personenverzeichnis
    'i_planar'  => 'inc/ev_item_planar.php',    // Eventhandler Plangegenstände
    'i_3d'      => '',
    'i_media'   => '',
    'changes'   => 'changes.txt'
);

// Admin-Array
$adm_site = array(
    'self'  => "adm_self.php",
    'alias' => "adm_aliasname.php",
    'orte'  => "adm_orte.php",
    'user'  => "adm_user.php"
);
// ___ Initialisierung abgeschlossen / Programmstart

$smarty = new Smarty;
//    $smarty->template_dir   = 'templates';
$smarty->compile_dir    = '/tmp';
$smarty->config_dir     = 'configs';
$smarty->cache_dir      = '/tmp';
$smarty->force_compile  = true;
$smarty->debugging      = true;
$smarty->display('header.tpl'); // Schreibt den Header und damit html
?>