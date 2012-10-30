<?php
/**************************************************************

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

require_once	'configs/config.php';
$_POST = normtext($_POST);              // Filter für htmlentities

// Datenbankanbindung
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

if (isset($_POST['aktion']) AND ($_POST['aktion'] === "logout")) :
    $db->disconnect();
    $myauth->logout();
    $myauth->start();
    exit;
endif;

// $lang muß über die Benutzerobefläche aquiriert werden - noch nicht implementiert
$lang = $myauth->getAuthData('lang');
$smarty->assign('lang', $lang);

switch($lang) :                         // Datumsformat der DB einstellen
    case 'de' :
        $db->query("SET datestyle TO German");
        break;
    case 'us' :
        $db->query("SET datestyle TO US");
        break;
    case 'eu' :
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
require_once 'class.db_statistik.php';

// laden Statistikanzeige
$stat = new db_stat();
$smarty->assign('stat', $stat->view());

// laden Menübereich
$data = getStringList(array(4008,4001,4028,4003,4007,4005,4006,4009));
$data[] = $myauth->getAuthData('realname');
$smarty->assign('dlg', $data);
$smarty->display('menue.tpl');

include 'main.php';
?></body></html>