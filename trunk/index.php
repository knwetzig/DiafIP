<?php
/**************************************************************

    DIAFIP - HAUPTPROGRAMM

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/
$laufzeit = -gettimeofday(true);
require_once	'configs/config.php';
$_POST = normtext($_POST);              // Filter für htmlentities
$_GET = normtext($_GET);

//_v($_GET, 'GET'); _v($_POST, 'POST');
// Anbindung an Datenkern
$options = array(
    'debug'             => 5,
    'use_transactions'  => true,
    'persistent'        => true,
);
$db = &MDB2::singleton($dsn, $options); isDbError($db);
$db->setFetchMode(MDB2_FETCHMODE_ASSOC); isDbError($db);
$db->loadModule('Extended'); isDbError($db);

// Authentifizierung
$params = array(
    "dsn"           => $dsn,
    "table"         => "s_auth",
    "sessionName"   => "diafip",
    "db_fields"     => "rechte, lang, uid, realname, notiz"
);
$myauth = new Auth("MDB2", $params, "loginFunction");
$myauth->start();
if (!$myauth->checkAuth()) exit;        // erfolglose Anmeldung

// Abfangen von Aktionen die nicht durch Eventhandler bedient werden
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
        if ($myauth->getAuthData('rechte') >= EDIT) :
            IsDbError($db->extended->autoExecute(
                's_auth',
                array('lang' => $_GET['aktion']),
                MDB2_AUTOQUERY_UPDATE,
                'uid = '.$db->quote($myauth->getAuthData('uid'),
                'integer'), 'text'));
        endif;
endswitch;

$lang = $myauth->getAuthData('lang');
$smarty->assign('lang', $lang);

switch($lang) :                         // Datumsformat der DB einstellen
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
require_once 'class.pers.php';
require_once 'class.figd.php';
require_once 'class.media.php';
require_once 'class.item.php';
require_once 'class.statistik.php';

// laden Menübereich
$data = getStringList(array(0,4008,4028,4003,0,4005,4006,4009,4032));
$data[9] = $myauth->getAuthData('realname');
$data[10] = $_SERVER['PHP_SELF'];
$smarty->assign('dlg', $data);
$smarty->display('menue.tpl');

include 'main.php';

// laden Statistikanzeige
$stat = new db_stat();
$smarty->assign('stat', $stat->view());
$smarty->display('statistik.tpl')
?></body></html>