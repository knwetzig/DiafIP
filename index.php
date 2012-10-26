<?php
/**************************************************************

$Rev$
$Author$
$Date: 2012-08-09 19:41:46 +0200 (#$
$URL$

***** (c) DIAF e.V. *******************************************/

require_once	'configs/config.php';
$_POST = normtext($_POST); // Filter für htmlentities

$myauth = new Auth("MDB2", $params, "loginFunction");
$myauth->start();
if (!$myauth->checkAuth()) exit;     // erfolglose Anmeldung

if (isset($_POST['aktion']) AND ($_POST['aktion'] === "logout")) :
    $myauth->logout();
    $myauth->start();
    exit;
endif;
// $lang muß über die Benutzerobefläche aquiriert werden - noch nicht implementiert
$lang = $myauth->getAuthData('lang');
$smarty->assign('lang', $lang);

// Datenbankanbindung
$db = &MDB2::factory(DSN);
isDbError($db);
$db->setFetchMode(MDB2_FETCHMODE_ASSOC); isDbError($db);
$db->loadModule('Extended'); isDbError($db);
switch($lang) :
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
// ab hier steht die Verbindung zur DB

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
$data = getStringList(array(4008,4001,4000,4003,4007,4005,4006,4009));
$data[] = $myauth->getAuthData('realname');
$smarty->assign('dlg', $data);
$smarty->display('menue.tpl');

include 'main.php';

$db->disconnect();
?>
</body>
</html>
