<?php
/***************************************************************
Diese Datei nicht bearbeiten - Do not edit this File

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

require_once    'config.local.php';
require_once	'service.php';
require_once	'Auth.php';
require_once	'MDB2.php';
require_once	'Smarty.class.php';

const
    // RegExpressions
    NAMEN     = '^[^`*+!-\':-@[-^{-~]+$',   // Keine Interpunktion, nur runde Klammern
    ANZAHL    = '([1-9]+[\d]*){1,1}',
    // Dezimalzahl -1,2e-3
    DZAHL     = '^[-+]?[\d]*[.,]?[\d]+([eE][-+]?[\d]+)?',
    PLZ       = '^[\d]{4,7}$',
    DAUER     = '^(([\d]+[hH])?([\d]+[mM])?([\d]*(([.][\d]+)?|[sS]))?|([\d]+[:][\d]+[:][\d]+)+([.][\d]+)?)$',
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
    'admin' => 'configs/admin.php',
    'N'     => 'inc/ev_name.php',
    'P'     => 'inc/ev_person.php',         // Personenverzeichnis
    'F'     => "inc/ev_figd.php",           // filmografische Daten
    'Y'     => 'inc/ev_item_planar.php',    // Eventhandler Plangegenstände
    'Z'     => 'inc/ev_item_3dobj.php',
    'K'     => 'inc/ev_item_fkop.php',
    'news'  => 'inc/news.php'
);

// Admin-Array
$adm_site = array(
    'self'  => 'adm_self.php',
    'string'=> 'adm_strings.php',
    'orte'  => 'adm_orte.php',
    'lort'  => 'adm_lort.php',
    'user'  => 'adm_user.php'
);
// ___ Initialisierung abgeschlossen / Programmstart

$smarty = new Smarty;
//    $smarty->template_dir   = 'templates';
$smarty->compile_dir    = $smartyConf['compile_dir'];
$smarty->config_dir     = $smartyConf['config_dir'];
$smarty->cache_dir      = $smartyConf['cache_dir'];
$smarty->force_compile  = $smartyConf['force_compile'];
$smarty->debugging      = $smartyConf['debugging'];
$smarty->display('header.tpl'); // Schreibt den Header und damit html
?>