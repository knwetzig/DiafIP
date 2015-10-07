<?php namespace DiafIP;
        use MDB2;
/**
 * Klassenbibliotheken für Filmogr.-/Bibliografische Daten
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
 * @package     DiafIP\Film
 * @version     $Id$
 * @since       r99 Aufteilung Klasse
 * @requirement PHP Version >= 5.4
 */

/** FIBIMAIN CLASS */
abstract class FibiMain extends Entity implements iFibiMain {

    const
        TYPEFIBI  = 'text,text,text,integer,integer,text,text,text,text,',
        GETDATA   = 'SELECT titel, atitel, utitel, sid, sfolge, prod_jahr, anmerk, quellen, thema FROM f_main2 WHERE id = ?;',
        GETSTITEL = 'SELECT titel, descr FROM f_stitel WHERE sertitel_id = ?;',
        IFDOUBLE  = 'SELECT id FROM f_main2 WHERE (del = FALSE) AND (titel = ?);',
        GETTAETIG = 'SELECT * FROM f_taetig;',
        EXCAST    = 'SELECT COUNT(*) FROM f_cast WHERE fid = ? AND pid = ? AND tid = ?;',
        GETCALI   = 'SELECT f_cast.tid, f_cast.pid FROM f_cast WHERE f_cast.fid = ? ORDER BY tid;',
        ISLINK    = 'SELECT COUNT(*) FROM f_cast WHERE fid = ?',
        GETSTILI  = 'SELECT sertitel_id, titel FROM f_stitel ORDER BY titel ASC;',
        GETTILI   = 'SELECT f_main2.id, f_main2.titel FROM public.f_main2 WHERE f_main2.del != TRUE
                     ORDER BY f_main2.titel ASC;',
        SEARCH    = 'SELECT DISTINCT id FROM f_main2, f_stitel
                     WHERE (f_main2.del = FALSE) AND (f_main2.titel ILIKE ? OR
                        f_main2.atitel ILIKE ? OR
                        f_main2.utitel ILIKE ? OR
                        (f_stitel.titel ILIKE ? AND f_stitel.sertitel_id = f_main2.sid));';

    protected
        $stitel = null, // Serientitel -> diafip.f_stitel.titel
        $sdescr = null; // Beschreibung Serie

    /**
     * Initialisiert das Objekt (auch gelöschte)
     * @param null $nr
     */
    public function __construct($nr = null) {
        parent::__construct($nr);
        $this->content['titel']     = null; // Originaltitel
        $this->content['atitel']    = null; // Arbeitstitel
        $this->content['utitel']    = null; // Untertitel
        $this->content['sid']       = null; // Serien - ID
        $this->content['sfolge']    = null; // Serienfolge
        $this->content['prod_jahr'] = null;
        $this->content['anmerk']    = null;
        $this->content['quellen']   = null;
        $this->content['thema']     = []; // Schlagwortverzeichnis
        if ((isset($nr)) AND is_numeric($nr)) :
            $db   = MDB2::singleton();
            $data = $db->extended->getRow(self::GETDATA, list2array(self::TYPEFIBI), $nr, 'integer');
            if(!empty($data['thema'])) $data['thema'] = list2array($data['thema']);
            self::WertZuwCont($data);

            // Serientitel holen, soweit vorhanden
            if ($this->content['sid']) :
                $data = $db->extended->getRow(self::GETSTITEL, null, $this->content['sid'], 'integer');
                IsDbError($data);
                $this->stitel = $data['titel'];
                $this->sdescr = $data['descr'];
            endif;
        endif;
    }

    /**
     * Ausgabe des Titels
     * @return string
     */
    public function getTitel() {
        return '<a href="index.php?' . $this->content['bereich'] . '=' . $this->content['id'] . '">' . $this->content['titel'] .
        '</a>';
    }

    /**
     * Ermitteln gleichlautender Titel
     * @return mixed
     */
    final protected function ifDouble() {
        $db   = MDB2::singleton();
        $data = $db->extended->getOne(self::IFDOUBLE, null, $this->content['titel']);
        IsDbError($data);
        return $data;
    }

    /**
     * @param $titel
     * @param $descr
     * @return int|null
     */
    public static function addSerTitel($titel, $descr) {
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        $db = MDB2::singleton();

        $erg = $db->extended->autoExecute('f_stitel', [$titel,$descr], MDB2_AUTOQUERY_INSERT, null,['text','text']);
        IsDbError($erg);
        return null;
    }

    public static function editSerTitel($nr, $status = null) {}
    public static function delSerTitel($nr) {}

    /**
     * gibt ein Array(num, text) der Tätigkeiten zurück
     * @return array
     */
    final protected static function getTaetigList() {
        $db = MDB2::singleton();
        global $str;
        $list = $db->extended->getCol(self::GETTAETIG, 'integer');
        IsDbError($list);
        $data = [];
        foreach ($list as $wert) $data[$wert] = $str->getStr($wert);
        asort($data);
        return $data;
    }

    /**
     * Ausgabe der Serientitelliste
     * @return array|int
     */
    final public static function getSTitelList() {
        $db       = MDB2::singleton();
        $ergebnis = [];
        $erg      = $db->query(self::GETSTILI);
        IsDbError($erg);
        while ($row = $erg->fetchRow()) :
            $ergebnis[$row['sertitel_id']] = $row['titel'];
        endwhile;
        if ($ergebnis) return $ergebnis; else return 1;
    }

    /**
     * Ausgabe der Titelliste (Filme/Bücher)
     * @return array
     */
    final public static function getTitelList() {
        $db   = MDB2::singleton();
        $list = $db->extended->getAll(self::GETTILI);
        IsDbError($list);

        $data = [0 => null];
        foreach ($list as $wert) $data[$wert['id']] = $wert['titel'];
        return $data;
    }

    /**
     * Testet ob ein Castingeintrag für fid vorhanden ist
     * @param $p
     * @param $t
     * @return mixed
     */
    final private function existCast($p, $t) {
        $db   = MDB2::singleton();
        $data = $db->extended->getOne(
            self::EXCAST, 'integer', [$this->content['id'], $p, $t],
            ['integer', 'integer', 'integer']);
        IsDbError($data);
        return $data;
    }

    /**
     * Fügt einen Castingdatensatz ein
     * @param $p
     * @param $t
     * @return int|null
     */
    final public function addCast($p, $t) {
        $db = MDB2::singleton();
        if (self::existCast($p, $t)) return 8;      // testen, das nix doppelt eingetragen wird!

        IsDbError($db->extended->autoExecute(
                      'f_cast', ['fid' => $this->content['id'], 'pid' => $p, 'tid' => $t],
                      MDB2_AUTOQUERY_INSERT, null, ['integer', 'integer', 'integer']));
        return null;
    }

    /**
     * löscht einen Castingsatz für diesen Eintrag
     * @param $p
     * @param $t
     * @return int|null
     */
    final public function delCast($p, $t) {
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db = MDB2::singleton();
        IsDbError($db->extended->autoExecute(
                      'f_cast', null, MDB2_AUTOQUERY_DELETE,
                      'fid = ' . $this->content['id'] . ' AND pid = ' . $p . ' AND tid = ' . $t
                  ));
        return null;
    }

    /**
     * Gibt die Besetzungsliste für diesen Datensatz aus
     * Anm.: Die Sortierreihenfolge ist durch die ID in der Stringtabelle
     *  fest vorgegeben. Bei Änderung bitte den Eintrag in der Tabelle
     *  f_taetig korrigieren.
     * @return array (name, tid, pid, job)
     */
    final protected function getCastList() {
        $db = MDB2::singleton();
        global $str;
        if (empty($this->content['id'])) return null;
        $data = $db->extended->getALL(
            self::GETCALI, null, $this->content['id'], 'integer');
        IsDbError($data);

        // Übersetzung für die Tätigkeit und Namen holen
        foreach ($data as &$wert) :
            $wert['job']  = $str->getStr($wert['tid']);
            $p            = new Person($wert['pid']);
            $wert['name'] = $p->getName();
        endforeach;
        unset($wert);
        return $data;
    }

    /**
     * Prüft ob der Datensatz verknüpft ist
     * @return mixed
     */
    final protected function isLinked() {
        $db = MDB2::singleton();
        // Prüfkandidaten: f_cast.fid / ...?
        $data = $db->extended->getOne(self::ISLINK, null, $this->content['id']);
        IsDbError($data);
        return $data;
    }

    /**
     * Suchfunktion in allen Titelspalten incl. Serientiteln
     * @param $s
     * @return array | int
     */
    static public function search($s) {
        $s  = "%" . $s . "%";
        $db = MDB2::singleton();

        // Suche in titel, atitel, utitel
        $data = $db->extended->getCol(self::SEARCH, ['integer'], [$s, $s, $s, $s]);
        IsDbError($data);
        if ($data) :
            return $data;
        else :
            return 1;
        endif;
    }

    /**
     * Zusammenstellung der Daten eines Datensatzes, Einstellen der Rechteparameter
     * Auflösen von Listen und holen der Strings aus der Tabelle Zuweisungen und
     * ausgabe via display()
     * Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
     * @return array|int
     */
    public function view() {
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $data   = parent::view();

        $data[] = new d_feld('descr', changetext($this->content['descr']), VIEW, 506); // Beschreibung
        $data[] = new d_feld('titel', self::getTitel(), VIEW, 500); // Originaltitel
        $data[] = new d_feld('atitel', $this->content['atitel'], VIEW, 503); // Arbeitstitel
        $data[] = new d_feld('utitel', $this->content['utitel'], VIEW, 501); // Untertitel
        $data[] = new d_feld('stitel', $this->stitel, VIEW, 504); // Serientitel
        $data[] = new d_feld('sfolge', $this->content['sfolge'], VIEW); // Serienfolge
        $data[] = new d_feld('sdescr', $this->sdescr); // Beschreibung Serie
        $data[] = new d_feld('prod_jahr', $this->content['prod_jahr'], VIEW, 576);
        $data[] = new d_feld('anmerk', changetext($this->content['anmerk']), VIEW, 572);
        $data[] = new d_feld('quellen', $this->content['quellen'], VIEW, 578);
        $data[] = new d_feld('thema', $this->content['thema'], VIEW, 577); // Schlagwortliste
        $data[] = new d_feld('isVal', $this->content['isvalid'], VIEW, 10009);
        $data[] = new d_feld('cast',  $this->getCastList(), VIEW);
        return $data;
    }
}
