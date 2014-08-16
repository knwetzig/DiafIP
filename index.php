<?php
/**************************************************************
    PHP version: >= 5.4

    DIAFIP - HAUPTPROGRAMM

    $Rev$
    $Author$
    $Date$
    $URL$

    Author: Knut Wetzig <knwetzig@gmail.com>

**************************************************************/

$laufzeit = -gettimeofday(true);
require_once	'configs/config.php';

$_POST = normtext($_POST);              // Filter für htmlentities
$_GET = normtext($_GET);

// --- Anbindung an Datenkern ---
$options = array(
//    'debug'             => 5,
//    'ssl'               => true,
    'use_transactions'  => true,
    'persistent'        => true,
);
$db = MDB2::singleton($dsn, $options);
isDbError($db);
$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
$db->loadModule('Extended');

// --- Authentifizierung ---
$params = array('dsn'           => $dsn,
                'table'         => 's_auth',
                'db_fields'     => 'rechte,lang,uid,realname,notiz,profil');
$myauth = new Auth("MDB2", $params, "loginFunction");
$myauth->start();
if (!$myauth->checkAuth()) exit();        // erfolglose Anmeldung

// Abfangen von Aktionen die nicht durch nachfolgende Eventhandler bedient werden
if (isset($_GET['aktion'])) switch ($_GET['aktion']) :
    case 'logout' :
        $db->disconnect();
        $myauth->logout();
        $myauth->start();
        exit;
    case 'de' :
    case 'en' :
    case 'fr' :
        $myauth->setAuthData('lang', $_GET['aktion']);
        if ($myauth->getAuthData('uid') != 4) :
            IsDbError($db->extended->autoExecute('s_auth',
                array('lang' => $_GET['aktion']), MDB2_AUTOQUERY_UPDATE,
                'uid = '.$db->quote($myauth->getAuthData('uid'), 'integer'), 'text'));
        endif;
endswitch;

switch ($myauth->getAuthData('lang')) :         // locale der DB einstellen
    case 'de' :
        $db->query("SET datestyle TO German");
        break;
    case 'us' :
        $db->query("SET datestyle TO US");
        break;
    case 'en' :
    case 'fr' :
        $db->query("SET datestyle TO European");
        break;
    default :
        $db->query("SET datestyle TO ISO");
endswitch;

require_once 'class.view.php';
require_once 'class.s_location.php';
require_once 'class.entity.php';        // Basisklasse V2
require_once 'class.person.php';        // Personenklasse V2
require_once 'class.figd2.php';         // Biblio-/Filmogr. Daten V2
require_once 'class.media.php';
require_once 'class.item.php';
require_once 'class.statistik.php';

$str = new String($myauth->getAuthData('lang'));

// --- Laden Menübereich ---
$menue = array(
    'F'         => $str->getStr(4008),
    'Y'         => $str->getStr(4028),
    'P'         => $str->getStr(4003),
    'stat'      => $str->getStr(4009),
    'Z'         => $str->getStr(4032),
    'K'         => $str->getStr(4038),
//    'login'     => $str->getStr(4004),
    'logout'    => $str->getStr(4005),
    'realname'  => $myauth->getAuthData('realname'),
    'userid'    => $myauth->getAuthData('uid'),
    'lang'      => $myauth->getAuthData('lang'),
    'profil'    => $myauth->getAuthData('profil'),
    'phpself'   => $_SERVER['PHP_SELF']);

if ($myauth->getAuthData('uid') != 4) {
    $menue['messg'] = $str->getStr(4037);
    $menue['pref']  = $str->getStr(4006);
}
$smarty->assign('dlg', $menue);
$smarty->display('header.tpl');

include 'main.php';

// --- Laden Statistikanzeige ---
$stat = new db_stat();
$smarty->assign('stat', $stat->view());
$smarty->display('statistik.tpl')
?></body></html>