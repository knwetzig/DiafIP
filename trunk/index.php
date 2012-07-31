<?php
/**************************************************************
$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$
***** (c) DIAF e.V. *******************************************/
require_once	'configs/config.php';

$myauth = new Auth("MDB2", $params, "loginFunction");
$myauth->start();
if ($myauth->checkAuth()) {     // erfolgreiche Anmeldung
    if (isset($_POST['aktion']) AND ($_POST['aktion'] === "logout")) {
        $myauth->logout();
        $myauth->start();
        die();
    }
    // $lang muß über die Benutzerobefläche aquiriert werden
    // im Moment noch nicht implementiert
    $lang = $myauth->getAuthData('lang');
    $smarty->assign('lang', $lang);

    $db = &MDB2::factory(DSN);
    isDbError($db);
    $db->setFetchMode(MDB2_FETCHMODE_ASSOC);
    switch($lang) {
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
    }
    isDbError($db);
    $db->loadModule('Extended'); isDbError($db);

    // ab hier steht die Verbindung zur DB
    require_once 'class.view.php';
    require_once 'class.s_location.php';
    require_once 'class.pers.php';
    require_once 'class.figd.php';
//    require_once 'class.item.php';
    require_once 'class.db_statistik.php';

//_________________________________________________________________________
//_v($_POST, 'Zentraler POST-eingang');
//_________________________________________________________________________

    $stat = new db_stat();      // laden Statistikanzeige
    $smarty->assign('stat', $stat->view());
    $data = getStringList(array(4008,4001,4000,4003,4007,4005,4006,4009));
    $data[] = $myauth->getAuthData('realname');
    $smarty->assign('dlg', $data);
    $smarty->display('menue.tpl');

    include 'main.php';

    $db->disconnect();
}   // ende nutzbereich
echo  "\n\t</body> \n </html>";
?>
