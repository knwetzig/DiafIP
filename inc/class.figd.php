<?php
/**************************************************************

    Klassenbibliothek für Filmogr.-/Bibliografische Daten

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/


/** ==========================================================================
                               TITEL KLASSE
========================================================================== **/
abstract class Titel {
/**********************************************************
func: __construct($)
      newTitel()
      editTitel()
      getTitel($!)    // holt db-felder ins Objekt
      setTitel()      // schreibt objekt.titel -> db
      delTitel()
      protected::isLinked //prüft die Verknüpfung mit anderen Tabellen
    ::searchTitel($!) // gibt array der ID's zurück
      view()          // wird überladen von film

- Variablennamen, die sich auf die db-Tabelle beziehen müssen identisch
  mit den Spaltennamen sein, damit die Iteration gelingen kann.
**********************************************************/
    protected
        $id     = null,
        $titel  = null,   // Originaltitel
        $atitel = null,   // Arbeitstitel
        $utitel = null,   // Untertitel
        $sid    = null,   // Serien - ID
        $sfolge = null,   // Serienfolge
        $stitel = null,   // Serientitel ->    diafip.f_stitel.titel
        $sdescr = null;   // Beschreibung Serie

    const
        SQL_isLink  = 'SELECT COUNT(*) FROM public.f_film
                           WHERE f_film.titel_id = ?;',
        SQL_get     = 'SELECT * FROM f_titel WHERE id = ?;',
        SQL_getST   = 'SELECT titel, descr FROM f_stitel WHERE sertitel_id = ?;',
        SQL_search  = 'SELECT id FROM f_titel
                            WHERE (titel ILIKE ?) OR   (utitel ILIKE ?)
                            OR (atitel ILIKE ?) ORDER BY titel ASC;',
        SQL_search2 = 'SELECT sertitel_id FROM f_stitel WHERE (titel ILIKE ?);',
        SQL_search3 = 'SELECT id FROM f_titel WHERE sid = ?;';


    abstract function addTitel($stat);

    function editTitel($stat) {
    /****************************************************************
    Aufruf: 0   Formularaufruf
            1   Auswertung
    ****************************************************************/
        global $db, $smarty, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if($stat == false) {        // Formular anzeigen
            // Menüpkt für Dialog
            $data = a_display(array(
                // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('id', $this->id),
                new d_feld('titel', $this->titel, EDIT, 500),
                new d_feld('atitel', $this->atitel, EDIT, 503),
                new d_feld('utitel', $this->utitel, EDIT, 501),
                new d_feld('stitel', $this->stitel, EDIT, 504),
                new d_feld('sfolge', $this->sfolge, EDIT, 505),
                new d_feld('sid', $this->sid),
                new d_feld('bereich', null, VIEW, 4025)
            ));
            $smarty->assign('dialog', $data);
            // Array der Serientitel laden
            $smarty->assign('serTitel', self::getSTitelList());
            $smarty->display('figd_titel_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        } else {
        // Formular auswerten
            // Obj zurückspeichern wird im aufrufenden Teil erledigt
            if (empty($this->titel) AND empty($_POST['titel'])) {
                fehler(100);
                die();
            }
            if ($_POST['titel']) $this->titel = normtext($_POST['titel']);
            if ($_POST['atitel']) $this->atitel = normtext($_POST['atitel']);
            if(is_numeric($_POST['sid'])) $this->sid = (int)($_POST['sid']);
            if ($this->sid)  $this->sfolge = normzahl($_POST['sfolge']);
            if ($_POST['utitel']) $this->utitel = normtext($_POST['utitel']);
        } // Formularbereich
    }


    protected function getTitel($nr) {
    /****************************************************************
      Aufgabe: Holt Daten aus den db-Tabellen
               wenn Serie -> Serientitel und Folgenummern
       Aufruf: $nr = f_titel.id
       Return: Fehlercode
    ****************************************************************/
        global $db;
        $data = $db->extended->getRow(self::SQL_get, null, $nr);
        IsDbError($data);
        if (!$data) return 4;       // kein Datensatz vorhanden

        // Ergebnis -> Objekt schreiben
        foreach($data as $key => $val) {
            $this->$key = $val;
        }

        // ermitteln Serientitel, soweit vorhanden
        if ($this->sid) {
            $data = $db->extended->getRow(self::SQL_getST, null, $this->sid);
            IsDbError($data);
            $this->stitel = $data['titel'];
            $this->sdescr = $data['descr'];
        }
    }

    function setTitel() {
    /****************************************************************
       Aufgabe: schreibt geänderte Werte in die db zurück
        Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->id) return 4;   // Abbruch weil leerer Datensatz

        // abgespeckte Kopie von $this erstellen
        $data = array();
        foreach($this as $value) $data[] = $value;
        $data = array_slice($data, 0, 6);   // liefert die ersten 7 Einträge

        $quest =& $db->prepare('UPDATE ONLY f_titel SET
            titel = ?, atitel = ?, sid = ?, sfolge = ?, utitel = ?
            WHERE id = ?;',
            array('text', 'text','integer', 'integer', 'text', 'integer'),
            MDB2_PREPARE_MANIP);
        IsDbError($quest);
        $erg =& $quest->execute($data);
        IsDbError($erg);

        /** im Moment manuell ändern
        // Serientitel schreiben
        if ($this->sid) {
            $sql =("UPDATE ONLY f_stitel
                    SET titel = '".$this->stitel."',
                        descr = '".$this->sdescr."'
                    WHERE sertitel_id = ".$this->sid.";");
            $erg =& $db->exec($sql);
            IsDbError($erg);
        }
        **/
    }

    function delTitel() {
    /****************************************************************
    Unglaublich, hier wird der Titel gelöscht :) Löschung erfolgt
    sofort ohne Papierkorbfunktion

    Aufruf: ID des Titels
    Return: O -> ok
            1 -> Fehler
    ****************************************************************/
        global $db;
        if(!isBit($myauth->getAuthData('rechte'), DELE)) return 2;
        if($this->isLinked()) :
            fehler(10006);
            return;
        endif;

        IsDbError($db->extended->autoExecute(
            'f_titel', null, MDB2_AUTOQUERY_DELETE,
            'id ='.$db->quote($this->id, 'integer')));
        unset($this);
    }

    static function searchTitel($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in allen Titelspalten (außer Serientiteln)
    *   Aufruf: ::string
    *   Return: Array der gefunden Titel-ID's zurück
    *           Fehlercode
    *     Anm.: statisch
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $s = "%".$s."%";
        $data = $db->extended->getCol(self::SQL_search, null, array($s,$s,$s));
        IsDbError($data);
        $erg = $data;
        //Weiter suche in Serientiteln
        $stit = $db->extended->getCol(self::SQL_search2, null, $s);
        IsDbError($stit);
        foreach($stit as $wert) {
            $data = $db->extended->getCol(self::SQL_search3, null, array($wert));
            IsDbError($data);
            $erg = array_merge($erg,$data);
        }
        if ($erg) {
            return array_unique($erg);		// id's der gefundenen Titel
        } else return 1;
    }

    protected function isLinked() {
    // Gibt die Anzahl der verknüpften Datensätze zurück
        global $db;
        $data = $db->extended->getRow(self::SQL_isLink, null, $this->id);
        IsDbError($data);
        return $data['count'];
    }

    static function getSTitelList() {
    /****************************************************************
    *  Aufgabe: Ausgabe der Serientitelliste
    *   Return: array, alles iO
    *           Fehlercode
    ****************************************************************/
        global $db;
        $ergebnis = array();
        $erg =& $db->query(self::SQL_getSTLi);
        IsDbError($erg);
        while ($row =$erg->fetchRow()) {
            $ergebnis[$row['sertitel_id']] = $row['titel'];
        }
        if ($ergebnis) {
            return $ergebnis;     // Liste der Serientitel
        } else return 1;
    }

    function view() {
    /****************************************************************
    *  Aufgabe: Ausgabe der Titeldaten an smarty
    ****************************************************************/
        global $smarty, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        $data = a_display(array(
            // name, inhalt, opt -> rechte, label, tooltip, valstring
            new d_feld('id', $this->id, VIEW),      // tid
            new d_feld('titel', $this->titel , VIEW, 500),
            new d_feld('atitel', $this->atitel , VIEW, 503),
            new d_feld('utitel', $this->utitel , VIEW, 501),
            new d_feld('stitel', $this->stitel , VIEW, 504),
            new d_feld('sfolge', $this->sfolge , VIEW),
            new d_feld('sdescr', $this->sdescr , VIEW), // Serienbeschreibung
            new d_feld('edit', null, EDIT, null, 4022),
            new d_feld('del', null, DELE, null, 4021),
            new d_feld('addfilm', null, EDIT, null, 4024), // Neuanlage filmogr
            new d_feld('addbibl', null, EDIT, null, 4026)  // Neuanlage biblio
        ));
        if($this->isLinked()) unset($data['del']);
        $smarty->assign('dialog', $data);
        $smarty->display('figd_titel_dat.tpl');
    }

}// Ende Titelclass


/** ==========================================================================
                                MAIN CLASS
========================================================================== **/
abstract class Main {
/********************************************************************

    __construct(?int)
    get(int $nr)        protected
    add(bool $stat)     abstract public
    edit(bool $stat)    abstract public
    set()               abstract protected
    del()               public
    existCast()         protected
    addCast()           public
    delCast()           public
    getCastList()       protected
    viewCastList()      public
    isDel()             protected
    is_Del()            static public
    isLinked()          protected
    isVal()             protected
    view()              abstract public

********************************************************************/

    protected
        $id         = null,
        $del        = false,
        $editfrom   = null,
        $editdate   = null,
        $isvalid    = false,
        $bild_id    = null,
        $prod_jahr  = null,
        $thema      = null,
        $quellen    = null,
        $inhalt     = null,
        $notiz      = null,
        $anmerk     = null,
        $titel      = null,   // Originaltitel
        $atitel     = null,   // Arbeitstitel
        $utitel     = null,   // Untertitel
        $sid        = null,   // Serien - ID
        $sfolge     = null,   // Serienfolge
        $stitel     = null,   // Serientitel ->    diafip.f_stitel.titel
        $sdescr     = null;   // Beschreibung Serie

    const
        SQL_getTaetigk = 'SELECT * FROM f_taetig;',
        SQL_getST   = 'SELECT titel, descr FROM f_stitel WHERE sertitel_id = ?;',
        SQL_getSTLi = 'SELECT sertitel_id, titel FROM f_stitel ORDER BY titel ASC;',
        SQL_get     = 'SELECT * FROM f_main WHERE id = ?;',
        SQL_exCast  = 'SELECT COUNT(*) FROM F_cast
                       WHERE fid = ? AND pid = ? AND tid = ?;',
        SQL_getCaLi = 'SELECT
                         p_person.vname, p_person.name,
                         f_cast.tid, f_cast.pid
                       FROM
                         public.f_cast, public.p_person
                       WHERE
                         f_cast.pid = p_person.id AND f_cast.fid = ?
                       ORDER BY
                         p_person."name" ASC, p_person.vname ASC;',
        SQL_isDel   = 'SELECT del FROM f_main WHERE id = ?;',
        SQL_isLink  = 'SELECT COUNT(*) FROM f_cast WHERE fid = ?',
        SQL_isVal   = 'SELECT isvalid FROM f_main WHERE id = ?;';

    abstract function add($stat);
    abstract function edit($stat);
    abstract function set();
    abstract function view();

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected static function getTaetigList() {
    // gibt ein Array(num, text) der Taetigkeiten zurück
        global $db;
        $list = $db->extended->getCol(self::SQL_getTaetigk, 'integer');
        $data = array();
        IsDbError($list);
        foreach($list as $wert) :
            $data[$wert] = d_feld::getString($wert);
        endforeach;
        asort($data);
        return $data;
    }

    static function getSTitelList() {
    /****************************************************************
    *  Aufgabe: Ausgabe der Serientitelliste
    *   Return: array, alles iO
    *           Fehlercode
    ****************************************************************/
        global $db;
        $ergebnis = array();
        $erg =& $db->query(self::SQL_getSTLi);
        IsDbError($erg);
        while ($row =$erg->fetchRow()) {
            $ergebnis[$row['sertitel_id']] = $row['titel'];
        }
        if ($ergebnis) {
            return $ergebnis;     // Liste der Serientitel
        } else return 1;
    }

    protected function get($nr) {
    // Initialisiert das Objekt (auch gelöschte)
        global $db;
        $types      = array(
            'integer',  // id
            'boolean',  // del          true wenn gelöscht
            'integer',  // editfrom
            'date',     // editdate
            'boolean',  // isvalid
            'integer',  // bild_id
            'text',     // prod_jahr    char(4)
            'text',     // thema        (Schlagwortliste)
            'text',     // quellen      (Freitext)
            'text',     // inhalt
            'text',     // notiz        (intern)
            'text',     // anmerk
            'text',     // Originaltitel
            'text',     // Arbeitstitel
            'text',     // Untertitel
            'integer',  // Serien - ID
            'integer',  // Serienfolge
        );

        $data = $db->extended->getRow(self::SQL_get, $types, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) :   // kein Datensatz vorhanden
            fehler(4);
            exit;
        endif;
        // Ergebnis -> Objekt schreiben
        foreach($data as $key => $wert) :
            $this->$key = $wert;
        endforeach;

        // Serientitel holen, soweit vorhanden
        if ($this->sid) {
            $data = $db->extended->getRow(self::SQL_getST, null, $this->sid);
            IsDbError($data);
            $this->stitel = $data['titel'];
            $this->sdescr = $data['descr'];
        }
    }

    function del() {
    // Setzt "NUR" das Flag für den Datensatz in der DB
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), DELE)) return 2;

        IsDbError($db->extended->autoExecute(
            'f_main', array('del' => true), MDB2_AUTOQUERY_UPDATE,
            'id = '.$db->quote($this->id, 'integer'), 'boolean'));
    }

    protected function existCast($p, $t) {
    // Testet ob ein Castingeintrag vorhanden ist
        global $db;
        $data = $db->extended->getRow(
            self::SQL_exCast, null, array($this->id, $p, $t),
            array('integer', 'integer', 'integer'));
        IsDbError($data);
        return $data['count'];
    }

    function addCast($p, $t) {
    // fügt einen Castingdatensatz ein
    // testen, das nix doppelt eingetragen wird!
        global $db;
        if(self::existCast($p, $t)) return 8;
        IsDbError($db->extended->autoExecute('f_cast',
                        array('fid' => $this->id, 'pid' => $p, 'tid' => $t),
                        MDB2_AUTOQUERY_INSERT, null,
                        array('integer', 'integer', 'integer')));
    }

    function delCast($p, $t) {
    // löscht einen Castingsatz für diesen Eintrag
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        IsDbError($db->extended->autoExecute(
            'f_cast', null, MDB2_AUTOQUERY_DELETE,
            'fid = '.$db->quote($this->id, 'integer') AND
            'pid = '.$db->quote($p, 'integer') AND
            'tid = '.$db->quote($t, 'integer')));
    }

    protected function getCastList() {
    // gibt die Besetzungsliste für diesen Eintrag aus:
    // array(vname, name, tid, pid, job)
        global $db;
        $data = $db->extended->getALL(
            self::SQL_getCaLi, null, $this->id, 'integer');
        IsDbError($data);

        // Übersetzung für die Tätigkeit holen
        foreach($data as &$wert) :
           $wert['job'] = d_feld::getString($wert['tid']);
        endforeach;

        return ($data);
    }

    function viewCastList() {
        global $smarty;
        $data = self::getCastList();
        $smarty->assign('CastList', $data);
        $smarty->display('figd_castlist.tpl');
    }

    protected function isDel() {
    // Testet ob die Löschflagge gesetzt ist
        global $db;
        $data = $db->extended->getRow(
            self::SQL_isDel, 'boolean', $this->id, 'integer');
        IsDbError($data);
        return $data['del'];
    }

    static function is_Del($nr) {
    // Testet ob die Löschflagge gesetzt ist
        global $db;
        $data = $db->extended->getRow(
            self::SQL_isDel, 'boolean', $nr, 'integer');
        IsDbError($data);
        return $data['del'];
    }

    protected function isLinked() {
    // Prüft ob der Datensatz verknüpft ist (0 = frei / Nr = Anzahl)
        global $db;
        // Prüfkandidaten: f_cast.fid / ...?
        $data = $db->extended->getRow(self::SQL_isLink, null, $this->id);
        IsDbError($data);
        return $data['count'];
    }

    function isVal() {
    // Testet ob der Datensatz einer Überarbeitung bedarf (a la Wiki)
        global $db;
        $data = $db->extended->getRow(
            self::SQL_isVal, 'boolean', $this->id, 'integer');
        IsDbError($data);
        return $data['isvalid'];
    }
}// ende Main KLASSE


/** ==========================================================================
                                FILM CLASS
========================================================================== **/
class Film extends Main {
/********************************************************************

    del()               public      (inherit)
    addCast()           public      (inherit)
    delCast()           public      (inherit)
    getCastList()       protected   (inherit)
    viewCastList()      public      (inherit)
    isDel()             protected   (inherit)
    isLinked()          protected   (inherit)
    isVal()             protected   (inherit)
    __construct(?int)               (inherit)
    get(int)            protected   (inherit)
    set()               public
    add(bool)           public
    edit(bool)          public
    view()              public

********************************************************************/

    protected
        $gattung    = null,
        $prodtechnik = null,
        $laenge     = null,
        $fsk        = null,
        $praedikat  = null,
        $mediaspezi = 0,
        $urauffuehr = null; // '1900-01-01';

    const
        SQL_get     = 'SELECT gattung, prodtechnik, laenge, fsk,
                          praedikat, mediaspezi, urauffuehr
                       FROM ONLY f_film WHERE id = ?;',
        SQL_getPraed = 'SELECT * FROM f_praed;',
        SQL_getGattg = 'SELECT * FROM f_gatt;',
        SQL_getPT   = 'SELECT * FROM f_prodtechnik;',
        SQL_getMS   = 'SELECT * FROM f_mediaspezi;';

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
        parent::get($nr);
        global $db;
        $types      = array(
            'integer',  // gattung
            'integer',  // prodtechnik
            'text',     // laenge   (Sonderformat)
            'integer',  // fsk      (Altersempfehlung)
            'integer',  // praedikat
            'integer',  // mediaspezi
            'date',     // urauffuehr
        );
        $data = $db->extended->getRow(self::SQL_get, $types, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) :   // kein Datensatz vorhanden
            fehler(4);
            exit;
        endif;
        // Ergebnis -> Objekt schreiben
        foreach($data as $key => $wert) :
            $this->$key = $wert;
        endforeach;
    }

    protected static function getListGattung() {
    // gibt ein Array(num, text) der Gattungen zurück
        global $db;
        $list = $db->extended->getCol(self::SQL_getGattg, 'integer');
        $data = array();
        IsDbError($list);
        foreach($list as $wert) :
            $data[$wert] = d_feld::getString($wert);
        endforeach;
        asort($data);
        return $data;
    }

    protected static function getListPraedikat() {
    // gibt ein Array(num, text) der Praedikate zurück
        global $db;
        $list = $db->extended->getCol(self::SQL_getPraed, 'integer');
        IsDbError($list);
        $data = array();
        foreach($list as $wert) :
            $data[$wert] = d_feld::getString($wert);
        endforeach;
        return $data;
    }

    protected static function getListMediaSpez() {
    // gibt eine numerische Liste der Mediaspezifikationen zurück
        global $db;
        $data = $db->extended->getCol(self::SQL_getMS, 'integer');
        IsDbError($data);
        $data = getStringList($data);
        return $data;
    }

    protected function getThisMediaSpez() {
    // gibt ein Text-Array der verwendeten Produktionstechniken zurück
        $list = self::getListMediaSpez();
        $data = array();
        foreach($list as $key => $wert) :
            if(isbit($this->mediaspezi, $key)) $data[] = $wert;
        endforeach;
        $data = getStringList($data);
        return $data;
    }

    protected static function getListProdTech() {
    // gibt ein Text-Array der Produktionstechniken zurück
        global $db;
        $data = $db->extended->getCol(self::SQL_getPT, 'integer');
        IsDbError($data);
        $data = getStringList($data);
        return $data;
    }

    protected function getThisProdTech() {
    // gibt ein Text-Array der verwendeten Produktionstechniken zurück
        $list = self::getListProdTech();
        $data = array();
        foreach($list as $key => $wert) :
            if(isbit($this->prodtechnik, $key)) $data[] = $wert;
        endforeach;
        $data = getStringList($data);
        return $data;
    }

    function add($stat) {
    /****************************************************************
        Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
        Aufruf:  Status
        Return:  Fehlercode
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if ($stat == false) $this->edit(false);
        else {
            $this->edit(true);
            $types = array(
                'integer',      // gattung
                'integer',      // prodtechnik
                'text',         // laenge (Sonderformat))
                'integer',      // fsk
                'integer',      // praedikat
                'integer',      // mediaspezi
                'text',         // urauffuehr
                'boolean',      // del
                'integer',      // editfrom
                'date',         // editdate
                'boolean',      // isvalid
                'integer',      // bild_id
                'text',         // prod_jahr
                'text',         // thema
                'text',         // quellen
                'text',         // inhalt
                'text',         // notiz
                'text',         // anmerk
                'text',         // titel
                'text',         // atitel
                'text',         // utitel
                'integer',      // sid
                'integer',      // sfolge
            );

            // 1. Daten für f_film
            // Typ und ID werden autom. generiert
            foreach($this as $key => $wert) $data[$key] = $wert;
            unset($data['id']);            // id löschen, wird vom DBMS vergeben
            $data['editfrom'] = $myauth->getAuthData('uid');
            $erg = $db->extended->autoExecute('f_film', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);
        }
    }

    function edit($stat) {
    /****************************************************************
        Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
        Aufruf: array, welches die zu ändernden Felder enthält
        Return: none
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if($stat == false) {        // Formular anzeigen
            // Menüpkt für Dialog
            $data = a_display(array(
                // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('serTitel',  parent::getSTitelList(),    VIEW),
                new d_feld('gattLi',    self::getListGattung(),     VIEW),
                new d_feld('praedLi',   self::getListPraedikat(),   VIEW),
                new d_feld('taetigLi',  parent::getTaetigList(),    VIEW),
                new d_feld('prodTecLi', self::getListProdTech(),    VIEW),
                new d_feld('mediaSpezLi',self::getListMediaSpez(),  VIEW),
                new d_feld('persLi',    person::getPersonLi(),      VIEW),
                new d_feld('id',        $this->id),
                new d_feld('titel',     $this->titel,       EDIT, 500),
                new d_feld('atitel',    $this->atitel,      EDIT, 503),
                new d_feld('utitel',    $this->utitel,      EDIT, 501),
                new d_feld('stitel',    $this->stitel,      EDIT, 504),
                new d_feld('sfolge',    $this->sfolge,      EDIT, 505),
                new d_feld('sid',       $this->sid),
                new d_feld('bild_id',   $this->bild_id),
                new d_feld('prod_jahr', $this->prod_jahr,   EDIT, 576),
                new d_feld('thema',     $this->thema,       EDIT, 577), // Schlagwortliste
                new d_feld('quellen',   $this->quellen,     EDIT, 578),
                new d_feld('inhalt',    $this->inhalt,      EDIT, 506),
                new d_feld('notiz',     $this->notiz,       EDIT, 514),
                new d_feld('anmerk',    $this->anmerk,      EDIT, 572),
                new d_feld('gattung',   $this->gattung,     EDIT, 579),
                new d_feld('prodtech',  bit2array($this->prodtechnik), EDIT, 571),
                new d_feld('laenge',    $this->laenge,      EDIT, 580, 10007),
                new d_feld('fsk',       $this->fsk,         EDIT, 581),
                new d_feld('praedikat', $this->praedikat,   EDIT, 582),
                new d_feld('mediaspezi',bit2array($this->mediaspezi),  EDIT, 583),
                new d_feld('urauff',    $this->urauffuehr,  EDIT, 584),
                new d_feld('cast',      $this->getCastList(), EDIT),

            ));
            $smarty->assign('dialog', $data);
            $smarty->display('figd_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        } else {
        // Formular auswerten
            // Obj zurückspeichern wird im aufrufenden Teil erledigt
            if (empty($this->titel) AND empty($_POST['titel'])) {
                fehler(100);
                exit();
            } else if ($_POST['titel']) $this->titel = normtext($_POST['titel']);

            if(!isset($_POST['atitel'])) :
                if ($_POST['atitel']) $this->atitel = normtext($_POST['atitel']);
                else $this->atitel = null;
            endif;

            if(isset($_POST['sid']) AND is_numeric($_POST['sid']))
                $this->sid = (int)($_POST['sid']); else $this->sid = null;
            if ($this->sid AND isset($_POST['sfolge']))
                $this->sfolge = normzahl($_POST['sfolge']);
            else $this->sfolge = null;

            if(isset($_POST['utitel'])) :
                if ($_POST['utitel']) $this->utitel = normtext($_POST['utitel']);
                else $this->utitel = null;
            endif;

            if(isset($_POST['inhalt'])) :
                if ($_POST['inhalt']) $this->inhalt = normtext($_POST['inhalt']);
                else $this->inhalt = null;
            endif;

            if(isset($_POST['quellen'])) :
                if ($_POST['quellen']) $this->quellen = normtext($_POST['quellen']);
                else $this->quellen = null;
            endif;

            if(isset($_POST['anmerk'])) :
                if ($_POST['anmerk']) $this->anmerk = normtext($_POST['anmerk']);
                else $this->anmerk = null;
            endif;

            if(isset($_POST['prod_jahr'])) :
                if ($_POST['prod_jahr']) {
                    if(isvalid($_POST['prod_jahr'], '[\d]{1,4}'))
                        $this->prod_jahr = normzahl($_POST['prod_jahr']);
                    else fehler(103);
                } else $this->prod_jahr = null;
            endif;

            if(isset($_POST['thema'])) :
                if ($_POST['thema']) $this->thema = normtext($_POST['thema']);
                else $this->thema = null;
            endif;

            if(isset($_POST['gattung'])) :
                if ($_POST['gattung'] AND is_numeric($_POST['gattung'])) {
                    if(isvalid($_POST['gattung'], ANZAHL))
                        $this->gattung = normzahl($_POST['gattung']);
                    else fehler(4);
                } else $this->gattung = null;
            endif;

            if(isset($_POST['prodtech']))
                $this->prodtech = bitArr2wert($_POST['prodtech']);

            if(isset($_POST['laenge'])) :
                if ($_POST['laenge'])
                    $this->laenge = normtext($_POST['laenge']);
                else $this->laenge = null;
            endif;


            if(isset($_POST['fsk'])) :
                if ($_POST['fsk']) {
                    if(isvalid($_POST['fsk'], ANZAHL))
                        $this->fsk = normzahl($_POST['fsk']);
                    else fehler(4);
                } else $this->fsk = null;
            endif;

            if(isset($_POST['praedikat'])) :
                if ($_POST['praedikat']) {
                    if(isvalid($_POST['praedikat'], ANZAHL))
                        $this->praedikat = normtext($_POST['praedikat']);
                    else fehler(4);
                } else $this->praedikat = null;
            endif;

            if(isset($_POST['urauffuehr'])) :
                if ($_POST['urauffuehr']) {
                    if(isvalid($_POST['urauffuehr'], DATUM))
                        $this->urauffuehr = normtext($_POST['urauffuehr']);
                    else fehler(103);
                } else $this->urauffuehr = null;
            endif;

            if(isset($_POST['mediaspezi']))
                $this->mediaspezi = bitArr2wert($_POST['mediaspezi']);


            if(isset($_POST['notiz'])) :
                if ($_POST['notiz'])
                    $this->notiz = normtext($_POST['notiz']);
                else $this->notiz = null;
            endif;

_v($this,'Editierte Daten');
        } // Formularbereich

    }

    function set() {
    /****************************************************************
        Aufgabe: schreibt die Daten in die Tabelle 'f_film' zurück (UPDATE)
        Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if(!$this->id) return 4;         // Abbruch: leerer Datensatz
        $types = array(
            'integer',      // gattung
            'integer',      // prodtechnik
            'text',         // laenge (Sonderformat))
            'integer',      // fsk
            'integer',      // praedikat
            'integer',      // mediaspezi
            'text',         // urauffuehr
            'boolean',      // del
            'integer',      // editfrom
            'date',         // editdate
            'boolean',      // isvalid
            'integer',      // bild_id
            'text',         // prod_jahr
            'text',         // thema
            'text',         // quellen
            'text',         // inhalt
            'text',         // notiz
            'text',         // anmerk
            'text',         // titel
            'text',         // atitel
            'text',         // utitel
            'integer',      // sid
            'integer',      // sfolge
        );
        foreach($this as $key => $wert) $data[$key] = $wert;
        // Bearbeitungsoptionen anhängen
        $data['editdate'] = date('c', $_SERVER['REQUEST_TIME']);
        $data['editfrom'] = $myauth->getAuthData('uid');

        $erg = $db->extended->autoExecute('f_film', $data,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
        IsDbError($erg);
        return;
    }

    function view() {
    /****************************************************************
        Aufgabe: Ausgabe des Filmdatensatzes (an smarty)
        Aufruf:
        Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if($this->isDel()) return;          // nichts ausgeben, da gelöscht

/** _____ ACHTUNG! BAUSTELLE _____ **/
        $data = a_display(array( // name, inhalt, opt -> rechte, label,tooltip
            new d_feld('id',        $this->id,          VIEW),   // fid
            new d_feld('titel',     $this->titel,       VIEW, 500), // Originaltitel
            new d_feld('atitel',    $this->atitel,      VIEW, 503), // Arbeitstitel
            new d_feld('utitel',    $this->utitel,      VIEW, 501), // Untertitel
            new d_feld('sfolge',    $this->sfolge,      VIEW),      // Serienfolge
            new d_feld('stitel',    $this->stitel,      VIEW, 504), // Serientitel
            new d_feld('sdescr',    $this->sdescr),   // Beschreibung Serie
            new d_feld('bild_id',   $this->bild_id),
            new d_feld('prod_jahr', $this->prod_jahr,   VIEW, 576),
            new d_feld('thema',     $this->thema,       VIEW, 577), // Schlagwortliste
            new d_feld('quellen',   $this->quellen,     VIEW, 578),
            new d_feld('inhalt',    changetext($this->inhalt), VIEW, 506),
            new d_feld('notiz',     changetext($this->notiz),  EDIT, 514),
            new d_feld('anmerk',    changetext($this->anmerk), VIEW, 572),
            new d_feld('gattung',   d_feld::getString($this->gattung), VIEW, 579),
            new d_feld('prodtech',  self::getThisProdTech(), VIEW, 571),
            new d_feld('laenge',    $this->laenge,      VIEW, 580),
            new d_feld('fsk',       $this->fsk,         VIEW, 581),
            new d_feld('praedikat', d_feld::getString($this->praedikat), VIEW, 582),
            new d_feld('mediaspezi', self::getThisMediaSpez(), VIEW, 583),
            new d_feld('urrauff',   $this->urauffuehr,  VIEW, 584),
            new d_feld('cast',      $this->getCastList(), VIEW),
            new d_feld('edit',   null, EDIT, null, 4013), // edit-Button
            new d_feld('del',    null, DELE, null, 4020)  // Lösch-Button
        ));
        $smarty->assign('dialog', $data);
        $smarty->display('figd_dat.tpl');
    }
} // endclass Film


/** ==========================================================================
                                BIBLIO CLASS
========================================================================== **/
class Biblio extends Main {
/********************************************************************

    del()               public      (inherit)
    addCast()           public      (inherit)
    delCast()           public      (inherit)
    getCastList()       protected   (inherit)
    viewCastList()      public      (inherit)
    isDel()             protected   (inherit)
    isLinked()          protected   (inherit)
    isVal()             protected   (inherit)
    __construct(?int)               (inherit)
    get(int)            protected   (inherit)
    set()               public
    add(bool)           public
    edit(bool)          public
    view()              public

********************************************************************/

    protected   // --> Variablen und Konstantendefinition
        $szahl  = null,     //Seitenzahl
        $format = null;     // Bitset für Formate: Paperback, Heft etc..

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
        parent::get($nr);
        // ....
    }

    function add($stat) {
    /****************************************************************
        Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
        Aufruf:
        Return: Fehlercode
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }

    function edit($stat) {
    /****************************************************************
        Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
        Aufruf: array, welches die zu ändernden Felder enthält
        Return: Fehlercode
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }

    function set() {
    /****************************************************************
        Aufgabe: schreibt die Daten in die Tabelle 'f_biblio' zurück (UPDATE)
        Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }

    function view() {
    /****************************************************************
        Aufgabe: Ausgabe des Datensatzes (an smarty)
        Aufruf:
        Return: Fehlercode
    ****************************************************************/
        global $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
    }
} // endclass Biblio



/** ----- Altlasten --- snippet
function searchByText($SText) {
/ ****************************************************************
    Aufgabe: einfache Suchfunktion
            1. Titel durchsuchen (einschl. Notizen)
            2. In Filmnotizen suchen
    Aufruf: string
    Return: (array) der f_Titel.id's
            1  nichts gefunden
        var: $ergebnis   Liste der ID's
        Anm.:
**************************************************************** /
    global $db;
    $ergebnis = array();

    // 1. suche in Titeln und Inhalt
    $tli = Titel::search($STxt);
    foreach($tli as $nr) {
        $erg =& $db->query('
            SELECT DISTINCT fid FROM f_film
            WHERE titel = '.$nr.';
        ');
        IsDbError($erg);
        while ($row = $erg->fetchInto()) $ergebnis[] = (int)$row['fid'];
    }

    // 2. Suche in Notizen
    $STxt = "%".$STxt."%";
    $erg =& $db->query("
        SELECT DISTINCT fid
        FROM f_film
        WHERE film.notiz ILIKE '".$STxt."';");
    IsDbError($erg);

    while ($row = $erg->fetchRow()) $ergebnis[] = (int)$row['fid'];
    if ($ergebnis) {
        return array_unique($ergebnis); // id's der gefundenen Filme
    } else return 1;
}

---- /snippet **/
?>