<?php
/***************************************************************
Klassenbibliotheken für die Werwaltung von Orten (Personen/
Lagermöglichkeiten)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

/** =================================================================
                            Lagerorte
================================================================= **/
interface iLOrt {
    public function __construct($nr);
    public static function getLOrtList();   // listet alle Orte in einem Array
    public function getLOrt();              // liefert den Ort zur id
    public function add($st);
    public function edit($t);
    public function del();
    public function view();                 // listet alle Artikel für diesn Ort
}

/***** interne routinen **********************************
      get()         // --dito-- schreibt dies aber ins Objekt
      upd()         // eigentl. editroutine
      is_linked     // prüft auf eine Verknüpfung
**********************************************************/
class LOrt implements iLOrt {
    const
        SQL_get = 'SELECT lagerort FROM i_lagerort WHERE id = ?;',
        SQL_islinked = 'SELECT id FROM i_main WHERE lagerort = ?;';
    protected
        $id     = null,
        $lort   = null;

    public function __construct($nr = null) {
        if (isset($nr) AND is_numeric($nr)) self::get((int)$nr);
    }

    protected function get($nr) {
        global $db;
        $data = $db->extended->getOne(self::SQL_get, null, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) fehler(4);        // kein Datensatz vorhanden

        $this->id = (int)$nr;
        $this->lort = $data;
    }

    final public function getLOrt() {
    /****************************************************************
    *  Aufgabe: liefert den Texteintrag zum Lagerort
    *   Return: string
    ****************************************************************/
        return $this->lort;
    }

    final public static function getLOrtList() {
    /****************************************************************
    *  Aufgabe: liefert die Liste mit den möglichen Lagerorten
    *   Return: string
    ****************************************************************/
        global $db;
        $list = $db->extended->getAll(
            'SELECT * FROM i_lagerort', array('integer', 'text'));
        IsDbError($list);
        $data = array();
        foreach($list as $wert) $data[$wert['id']] = $wert['lagerort'];
        natcasesort($data);
        // $data[0] = getString(xxx); <-- keine gute Idee, das hebelt die Verpflichtung
        //                                zur Eingabe eines Lagerorts aus..
        return $data;
    }

    public function add($st) {
        if ($st) :

        else :
            $lort = array();
            if($_POST['lagerort'] !== "") $lort['lagerort'] = $_POST['lagerort']; else fehler(107);
// Test Unique
            $data = $db->extended->autoExecute('p_alias', $lort,
                        MDB2_AUTOQUERY_INSERT, null, array('text','text'));
            IsDbError($data);
        endif;
    }

    public function edit($st) {
        if ($st) :
            $dialog[0][2] = 'Lagerort&nbsp;bearbeiten';
            $dialog[2][1] = $ali->name;
            $dialog[6][1] = $_POST['lort']?'edLort':'addLort';
            $smarty->assign('dialog', $dialog);
            $smarty->display('adm_dialog.tpl');
        else :

        endif;
    }

    protected function upd($st) {
        if ($st) :

        else :
            $data = $db->extended->autoExecute('p_alias', $ali,
                MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($ali->id, 'integer'), array('integer','text','text'));
            IsDbError($data);
        endif;
    }

    final protected function is_linked() {
        global $db;
        $list = $db->extended->getCol(
            self::SQL_islinked, 'integer', $this->id, 'integer');
        IsDbError($list);
        return $list;
    }

    public function del() {
        global $myauth, $db;
        if(!isBit($myauth->getAuthData('rechte'), ARCHIV)) return 2;

        if ($this->is_linked()) Fehler(10006); else {
            // löschen in Tabelle
            IsDbError($db->extended->autoExecute('i_lagerort', null,
                MDB2_AUTOQUERY_DELETE, 'id = '.$db->quote($this->id, 'integer')));
            erfolg();
        }
    }

    public function view() {            // Zeigt den Inhalt des Lagerorts
    }
}


/** =================================================================
                            Orte
================================================================= **/
/**********************************************************
func: __construct($)
    ::getOrt($!)    // holt die Ortsdaten aus der Ortstabelle -> array
      get()         // --dito-- schreibt dies aber ins Objekt
      neu()
      edit()
      set()         // schreibt objekt -> db
      del()         // löscht einen Ort
    ::getOrtList()  // listet alle Orte in einem Array

Anm.:
    Die Liste mit den Staaten und Ländern wird händisch geflegt
    Überarbeitung dieser Klasse zwingend erforderlich!

**********************************************************/
class Ort {
    protected
        $oid     = null,
        $lid    =    1,             // Landeskennung
        $ort    = null,
        $land   = null,
        $bland  = null;

    function __construct($nr = NULL) {
        if (isset($nr) AND ($nr>0)) {
            $this->oid = $nr;
            $this->get();
        }
        else $this->neu(false);
    }

    protected function get() {    // die dynamische Version
        global $db;
        $sql = 'SELECT * FROM orte WHERE oid = ?;';
        $data = $db->extended->getRow($sql, null, array($this->oid));
        IsDbError($data);
        /* ACHTUNG: Die Kombination mit einem statischen Aufruf führt zum
        Überschreiben von Speicherinhalten!!! deswegen gibt es 2 Versionen */
        foreach($this as $key => &$wert) $wert = $data[$key];
        unset($wert);
    }

    function getOrt($nr) {  // die statische Version
        global $db;
        $sql = 'SELECT * FROM orte WHERE oid = ?;';
        $data = $db->extended->getRow($sql, null, array($nr));
        IsDbError($data);
        return  $data;
    }

    function neu($status) {
    /****************************************************************
    Aufgabe: Neuanlage einer Location
    Aufruf: false   für Erstaufruf
            true    Verarbeitung nach Formular
    ****************************************************************/
        global $db;

        if($status == false) {
            $this->edit(false);
        } else {
            $this->edit(true);
            $data = $db->extended->autoExecute('s_orte', array(
                    'ort' => $this->ort,
                    'land' => $this->lid),
                MDB2_AUTOQUERY_INSERT, null, array(
                    'text',
                    'integer'));
            IsDbError($data);
            return 0;
        }
    }

    function edit($status) {
        global $db, $smarty;
        if($status == false) {
            $smarty->assign('llist', self::getLandList());
            $data = a_display(array(
                // name, inhalt optional-> rechte, label, tooltip, valString
                new d_feld('ort',$this->ort, SEDIT),
                new d_feld('lid',$this->lid)));
            $smarty->assign('dialog', $data);
            $smarty->display('adm_ortedialog.tpl');
        } else {
            // Formular auswerten und in Obj speichern
            if(isset($_POST['ort'])) :
                if(isValid($_POST['ort'], NAMEN))
                    $this->ort = $_POST['ort'];
                else {
                    fehler(107);
                    exit;
                }
            endif;
            $this->lid = (int)$_POST['land'];
        }
    }

    function set() {
    /*************************************************************
    Aufgabe: schreibt das Obj. via Update in die DB zurück
    Return: 0  alles ok
            1  leerer Datensatz
    **************************************************************/
        global $db;
        if (!$this->oid) return 1;   // Abbruch weil leerer Datensatz
        $types = array('integer','text');
        $werte = array('land' => $this->lid, 'ort' => $this->ort);
        $data = $db->extended->autoExecute('s_orte', $werte, MDB2_AUTOQUERY_UPDATE,
            'id = '.$db->quote($this->oid, 'integer'), $types);
        IsDbError($data);
        return 0;
    }

    function del() {
        global $db;
        // Abfrage, ob Ort mit einer Person verküpft ist
        $sql = "SELECT p_person.id FROM public.p_person
                WHERE p_person.tort = ? OR p_person.gort = ? OR p_person.wort = ?;";
        $data = $db->extended->getCol(
                $sql, null,
                array($this->oid, $this->oid, $this->oid),
                array('integer', 'integer', 'integer'));
        IsDbError($data);
        // $data enthält das array mit den konfliktdatensätzen
        if(!$data) {
            erfolg("Lösche Ort: ".$this->ort);
            $res = $db->extended->autoExecute('s_orte', null,
                        MDB2_AUTOQUERY_DELETE, 'id = '.$db->quote($this->oid, 'integer'));
        } else fehler(6);
    }

    function getOrtList() {
    // listet alle Orte in einem Array
        global $db;
        $sql = 'SELECT * FROM orte;';
        $data = $db->extended->getAll($sql);
        IsDbError($data);
        $orte=array('-- unbekannt --');
        foreach($data as $val) { // val ist das Städtearray
            $orte[$val['oid']] = $val['ort'];
        }
        return $orte;
    }

    function getLandList() {
        global $db;
        $sql = 'SELECT * FROM s_land ORDER BY s_land.land ASC, s_land.bland ASC;';
        $data = $db->extended->getAll($sql);
        IsDbError($data);
        $laend = array();
        foreach($data as $val) {
            $laend[$val['id']] = (empty($val['bland'])?$val['land']:$val['bland']."&nbsp;-&nbsp;".$val['land']);
        }
        return $laend;
    }
}   //endclass;
?>