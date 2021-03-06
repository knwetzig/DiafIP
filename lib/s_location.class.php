<?php namespace DiafIP;
use MDB2;
/**
 *  Klassenbibliotheken für die Verwaltung von Orten (Personen/ Lagermöglichkeiten)
 * @todo Revision dringend erforderlich
 */


/**
 * Interface Lagerorte
 */
interface iLOrt {
    public function __construct($nr);
    public static function getLOrtList(); // listet alle Orte in einem Array
    public function getLOrt(); // liefert den Ort zur id
    public function add($st);
    public function edit($t);
    public function del();
    public function view(); // listet alle Artikel für diesn Ort
}

/**
 * Klasse Lagerorte
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   Deutsches Institut für Animationsfilm e.V.
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
 * @version     SVN: $ Id: $
 * @since       r?? Neueinführung
 * @requirement PHP Version >= 5.4
 */
class LOrt implements iLOrt {
    const
        SQL_get      = 'SELECT lagerort FROM i_lagerort WHERE nr = ?;',
        SQL_islinked = 'SELECT id FROM i_main WHERE lagerort = ?;';
    protected
        $nr = null,
        $lort = null;

    public function __construct($nr = null) {
        if (isset($nr) AND is_numeric($nr)) self::get((int)$nr);
    }

    protected function get($nr) {
        $db   = MDB2::singleton();
        $data = $db->extended->getOne(self::SQL_get, null, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) feedback(4, 'hinw'); // kein Datensatz vorhanden

        $this->nr   = (int)$nr;
        $this->lort = $data;
    }

    final public function getLOrt() {
        /**
         *  Aufgabe: liefert den Texteintrag zum Lagerort
         *   Return: string
         **/
        return $this->lort;
    }

    final public static function getLOrtList() {
        /**
         *  Aufgabe: liefert die Liste mit den möglichen Lagerorten
         *  @return string
         **/
        $db   = MDB2::singleton();
        $list = $db->extended->getAll(
            'SELECT * FROM i_lagerort', ['integer', 'text']);
        IsDbError($list);
        $data = [];
        foreach ($list as $wert) $data[$wert['nr']] = $wert['lagerort'];
        natcasesort($data);
        // $data[0] = getString(xxx); <-- keine gute Idee, das hebelt die Verpflichtung
        //                                zur Eingabe eines Lagerorts aus..
        return $data;
    }

    public function add($name) {
        $this->lort = $name;
        if (empty($name)) feedback(107, 'warng');
        elseif ($this->exist()) feedback('Objekt exisitiert bereits', 'error');

        $db  = MDB2::singleton();
        $val = ['lagerort' => $this->lort];
        IsDbError($db->extended->autoExecute('i_lagerort', $val,
                                             MDB2_AUTOQUERY_INSERT, null, 'text'));
    }

    /**
     * @param bool $st
     * @todo __________ BAUSTELLE _____________________
     */
    public function edit($st) {
        global $marty, $db;

        if ($st) :
            $marty->assign('dialog', [
                0 => ['lort', $this->nr, 'Lagerort&nbsp;bearbeiten'],
                2 => ['lagerort', $this->lort, null],
                5 => ['aktion', 'delLOrt', 'löschen'],
                6 => ['aktion', 'edLOrt']] //absendebutton
            );
            $marty->display('adm_dialog.tpl');
        else :

            IsDbError($db->extended->autoExecute('i_lagerort', $this->lort,
                                                 MDB2_AUTOQUERY_UPDATE, 'nr = ' . $db->quote($this->nr, 'integer'),
                                                 'text'));
        endif;
    }

    final protected function is_linked() {
        $db   = MDB2::singleton();
        $list = $db->extended->getCol(
            self::SQL_islinked, 'integer', $this->nr, 'integer');
        IsDbError($list);
        return $list;
    }

    final protected function exist() {
        $db   = MDB2::singleton();
        $sql  = 'SELECT COUNT(*) FROM i_lagerort
                WHERE i_lagerort.lagerort ILIKE ?;';
        $data = $db->extended->getRow($sql, null, [$this->lort]);
        IsDbError($data);
        return $data['count'];
    }

    public function del() {
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), RE_ARCHIV)) return 2;

        if (!$this->is_linked()) :
            // löschen in Tabelle
            $db = MDB2::singleton();
            IsDbError($db->extended->autoExecute('i_lagerort', null, MDB2_AUTOQUERY_DELETE,
                                                 'nr = ' . $db->quote($this->nr, 'integer')));
            feedback(3, 'erfolg');
            return null;
        endif;

        feedback(10006, 'error');
        return 1;
    }

    public function view() { // Zeigt den Inhalt des Lagerorts
    }
}


/** Orte */
/*
 * func: __construct($)
 * ::getOrt($!)    // holt die Ortsdaten aus der Ortstabelle -> array
 * get()         // --dito-- schreibt dies aber ins Objekt
 * neu()
 * edit()
 * set()         // schreibt objekt -> db
 * del()         // löscht einen Ort
 * ::getOrtList()  // listet alle Orte in einem Array
 *
 * @todo Die Liste mit den Staaten und Ländern wird händisch geflegt Überarbeitung dieser Klasse zwingend erforderlich!
 */
class Ort {
    protected
        $oid = null,
        $lid = 1, // Landeskennung
        $ort = null,
        $land = null,
        $bland = null;

    function __construct($nr = null) {
        if (isset($nr) AND ($nr > 0)) {
            $this->oid = $nr;
            $this->get();
        } else $this->neu(false);
    }

    protected function get() { // die dynamische Version
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM orte WHERE oid = ?;';
        $data = $db->extended->getRow($sql, null, [$this->oid]);
        IsDbError($data);
        /* ACHTUNG: Die Kombination mit einem statischen Aufruf führt zum
        Überschreiben von Speicherinhalten!!! deswegen gibt es 2 Versionen */
        foreach ($this as $key => &$wert) $wert = $data[$key];
        unset($wert);
    }

    public static function getOrt($nr) { // die statische Version
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM orte WHERE oid = ?;';
        $data = $db->extended->getRow($sql, null, [$nr]);
        IsDbError($data);
        return $data;
    }

    function neu($status) {
        /**
         * Aufgabe: Neuanlage einer Location
         * Aufruf: false   für Erstaufruf
         * true    Verarbeitung nach Formular
         */
        $db = MDB2::singleton();

        if ($status == false) $this->edit(false);
        else {
            $this->edit(true);
            $data = $db->extended->autoExecute(
                's_orte', ['ort'  => $this->ort, 'land' => $this->lid], MDB2_AUTOQUERY_INSERT, null, ['text','integer']);
            IsDbError($data);
        }
    }

    /**
     * @param $status
     */
    function edit($status) {
        global $marty;
        if ($status == false) {
            $marty->assign('llist', self::getLandList());
            $data = a_display([
                                  // name, inhalt optional-> rechte, label, tooltip, valString
                                  new d_feld('ort', $this->ort, RE_SEDIT),
                                  new d_feld('lid', $this->lid)]);
            $marty->assign('dialog', $data);
            $marty->display('adm_ortedialog.tpl');
        } else {
            // Formular auswerten und in Obj speichern
            if (isset($_POST['ort'])) :
                if (isValid($_POST['ort'], REG_NAMEN))
                    $this->ort = $_POST['ort'];
                else feedback(107, 'warng');
            endif;
            $this->lid = intval($_POST['land']);
        }
    }

    function set() {
        /**
         * Schreibt das Obj. via Update in die DB zurück
         * @return int|null  nuu = alles ok 1 =leerer Datensatz
         */
        $db = MDB2::singleton();
        if (!$this->oid) return 1; // Abbruch weil leerer Datensatz
        $types = ['integer', 'text'];
        $werte = ['land' => $this->lid, 'ort' => $this->ort];
        $data  = $db->extended->autoExecute('s_orte', $werte, MDB2_AUTOQUERY_UPDATE,
                                            'id = ' . $db->quote($this->oid, 'integer'), $types);
        IsDbError($data);
        return 0;
    }

    function del() {
        $db = MDB2::singleton();
        // Abfrage, ob Ort mit einer Person verküpft ist
        $sql  = "SELECT p_person2.id FROM public.p_person2
                WHERE p_person2.tort = ? OR p_person2.gort = ? OR p_person2.wort = ?;";
        $data = $db->extended->getCol(
            $sql, null,
            [$this->oid, $this->oid, $this->oid],
            ['integer', 'integer', 'integer']);
        IsDbError($data);
        // $data enthält das array mit den konfliktdatensätzen
        if (!$data) {
            feedback("Lösche Ort: " . $this->ort, 'erfolg');
            $res = $db->extended->autoExecute('s_orte', null,
                                              MDB2_AUTOQUERY_DELETE, 'id = ' . $db->quote($this->oid, 'integer'));
            IsDbError($res);
        } else feedback(6, 'error');
    }

    public static function getOrtList() {
        // listet alle Orte in einem Array
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM orte;';
        $data = $db->extended->getAll($sql);
        IsDbError($data);
        $orte = ['-- unbekannt --'];
        foreach ($data as $val) {
            $st                = $val['ort'] . '&nbsp;-&nbsp;' . $val['land'];
            $orte[$val['oid']] = $st;
        }
        return $orte;
    }

    function getLandList() {
        $db   = MDB2::singleton();
        $sql  = 'SELECT * FROM s_land ORDER BY s_land.land ASC, s_land.bland ASC;';
        $data = $db->extended->getAll($sql);
        IsDbError($data);
        $laend = [];
        foreach ($data as $val) {
            $laend[$val['id']] = (empty($val['bland']) ? $val['land'] : $val['bland'] . "&nbsp;-&nbsp;" . $val['land']);
        }
        return $laend;
    }
} //endclass;