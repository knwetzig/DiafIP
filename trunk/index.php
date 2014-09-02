<?php namespace DiafIP {
    use Auth, MDB2;
    /**
     * DIAFIP - HAUPTPROGRAMM
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP
     * @version     $Id: $
     * @since       r1 Erstellung
     * @requirement PHP Version >= 5.4
     */

    $laufzeit = -gettimeofday(true);
    require_once	'configs/config.php';
    $_POST = normtext($_POST);              // Filter für htmlentities
    $_GET = normtext($_GET);

// Authentifizierung
    $params = ['dsn' => $dsn,
               'table' => 's_auth',
               'db_fields' => 'rechte,lang,uid,realname,notiz,profil'];
    $myauth = new Auth("MDB2", $params, "loginFunction");
    $myauth->start();
    if (!$myauth->checkAuth()) exit();

// DB-Initialisierung
    $db = MDB2::singleton($dsn, ['use_transactions' => true, 'persistent' => true]);
    IsDbError($db);
    $db->setFetchMode(MDB2_FETCHMODE_ASSOC);
    $db->loadModule('Extended');

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
                                                     ['lang' => $_GET['aktion']], MDB2_AUTOQUERY_UPDATE,
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

    require_once 'entity.class.php';        // Basisklasse
    require_once 'pname.class.php' ;        // Aliasnamen
    require_once 'person.class.php';        // Personenklasse
    require_once 'fibikern.class.php';      // Basisklasse für Biblio-/Filmogr. Daten
    require_once 'bibl2.class.php';         // Bibliografische Daten
    require_once 'figd2.class.php';         // Filmografische Daten
    require_once 'class.view.php';
    require_once 'class.s_location.php';
    require_once 'class.media.php';
    require_once 'class.item.php';
    require_once 'class.statistik.php';
// Initialisierung String-Objekt
    $str = new Wort($myauth->getAuthData('lang'));

// Laden Menübereich
    $menue = ['F'        => $str->getStr(4008),
              'Y'        => $str->getStr(4028),
              'P'        => $str->getStr(4003),
              'stat'     => $str->getStr(4009),
              'Z'        => $str->getStr(4032),
              'K'        => $str->getStr(4038),
              //    'login'    => $str->getStr(4004),
              'logout'   => $str->getStr(4005),
              'realname' => $myauth->getAuthData('realname'),
              'userid'   => $myauth->getAuthData('uid'),
              'lang'     => $myauth->getAuthData('lang'),
              'profil'   => $myauth->getAuthData('profil'),
              'phpself'  => $_SERVER['PHP_SELF']];

    if ($myauth->getAuthData('uid') != 4) :
        $menue['messg'] = $str->getStr(4037);
        $menue['pref']  = $str->getStr(4006);
    endif;
    $marty->assign('dlg', $menue);
    $marty->display('header.tpl');

    require_once 'inc/main.php';

// Laden Statistikanzeige
    $stat = new db_stat();
    $marty->assign('stat', $stat->view());
    $marty->display('statistik.tpl');
    echo '</body></html>';
}