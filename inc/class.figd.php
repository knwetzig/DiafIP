<?php
/**************************************************************

    Klassenbibliothek für Filmogr.-/Bibliografische Daten

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

interface iMain {
    const
        SQL_getSTLi = 'SELECT sertitel_id, titel FROM f_stitel ORDER BY titel ASC;',
        SQL_isVal   = 'SELECT isvalid FROM f_main WHERE id = ?;',
        SQL_search1  = 'SELECT id FROM f_film
                            WHERE (titel ILIKE ?) OR   (utitel ILIKE ?)
                            OR (atitel ILIKE ?) ORDER BY titel ASC;',
        SQL_search2 = 'SELECT sertitel_id FROM f_stitel WHERE (titel ILIKE ?);',
        SQL_search3 = 'SELECT id FROM f_film WHERE sid = ?;';

    public static function getSTitelList();
    public function del();
    public static function is_Del($nr);
    public function addCast($p, $t);
    public function delCast($p, $t);
    public function isVal();
    public static function search($s);
}

interface iTyp extends iMain {
    public function add($stat);
    public function edit($stat);
    public function set();
    public function view();
}

/** ==========================================================================
                               MAIN CLASS
========================================================================== **/
abstract class Main implements iMain {
/********************************************************************

interne Methoden:
            __construct(?int)
    int     ifDouble()
            getTaetigList()     static
    void    get(int $nr)
            isDel()
            existCast()
            getCastList()
            isLinked()

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
        SQL_ifDouble = 'SELECT id FROM f_main WHERE titel = ? AND del = false;',
        SQL_getTaetigk = 'SELECT * FROM f_taetig;',
        SQL_getST   = 'SELECT titel, descr FROM f_stitel WHERE sertitel_id = ?;',
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
        SQL_isLink  = 'SELECT COUNT(*) FROM f_cast WHERE fid = ?';

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    final protected function ifDouble() {
    /****************************************************************
    *  Aufgabe: Ermitteln gleichlautender Titel
    *   Return: int (ID des letzten Datensatzes | null )
    ****************************************************************/
        global $db;
        $data = $db->extended->getRow(self::SQL_ifDouble, null, $this->titel);
        IsDbError($data);
        return $data['id'];
    }

    final protected static function getTaetigList() {
    /****************************************************************
    *  Aufgabe: gibt ein Array(num, text) der Taetigkeiten zurück
    *   Return:
    ****************************************************************/
    //
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

    final public static function getSTitelList() {
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
    /****************************************************************
    *  Aufgabe: Initialisiert das Objekt (auch gelöschte)
    *   Return: void
    ****************************************************************/
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

    final public function del() {
    /****************************************************************
    *  Aufgabe: Setzt "NUR" das Löschflag für den Datensatz in der DB
    *   Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), DELE)) return 2;

        IsDbError($db->extended->autoExecute(
            'f_main', array('del' => true), MDB2_AUTOQUERY_UPDATE,
            'id = '.$db->quote($this->id, 'integer'), 'boolean'));
    }

    final protected function isDel() {
    /****************************************************************
    *  Aufgabe: Testet ob die Löschflagge gesetzt ist
    *   Return: bool
    ****************************************************************/
        global $db;
        if(empty($this->id)) return;
        $data = $db->extended->getRow(
            self::SQL_isDel, 'boolean', $this->id, 'integer');
        IsDbError($data);
        return $data['del'];
    }

    final public static function is_Del($nr) {
    /****************************************************************
    *  Aufgabe: Testet ob Löschflaf für Eintrag $nr gesetzt ist
    *   Aufruf: int $nr
    *   Return: bool
    ****************************************************************/
        global $db;
        $data = $db->extended->getRow(
            self::SQL_isDel, 'boolean', $nr, 'integer');
        IsDbError($data);
        return $data['del'];
    }

    final protected function existCast($p, $t) {
    /****************************************************************
    *  Aufgabe: Testet ob ein Castingeintrag für fid vorhanden ist
    *   Aufruf: int ($pid), int ($taetigkeit)
    *   Return: int (Anzahl)
    ****************************************************************/
    //
        global $db;
        $data = $db->extended->getRow(
            self::SQL_exCast, null, array($this->id, $p, $t),
            array('integer', 'integer', 'integer'));
        IsDbError($data);
        return $data['count'];
    }

    final public function addCast($p, $t) {
    /****************************************************************
    *  Aufgabe: fügt einen Castingdatensatz ein
    *   Aufruf: int ($pid), int ($taetigkeit)
    *   Return: Fehlercode
    ****************************************************************/
        global $db;
        // testen, das nix doppelt eingetragen wird!
        if(self::existCast($p, $t)) return 8;

        IsDbError($db->extended->autoExecute(
            'f_cast', array('fid' => $this->id, 'pid' => $p, 'tid' => $t),
            MDB2_AUTOQUERY_INSERT, null, array('integer', 'integer', 'integer')));
    }

    final public function delCast($p, $t) {
    /****************************************************************
    *  Aufgabe: löscht einen Castingsatz für diesen Eintrag
    *   Aufruf: int ($pid), int ($taetigkeit)
    *   Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        IsDbError($db->extended->autoExecute(
            'f_cast', null, MDB2_AUTOQUERY_DELETE,
            'fid = '.$this->id.' AND pid = '.$p.' AND tid = '.$t
            ));
    }

    final protected function getCastList() {
    /****************************************************************
    *  Aufgabe: gibt die Besetzungsliste für diesen Eintrag aus
    *   Return: array(vname, name, tid, pid, job)
    ****************************************************************/
        global $db;
        if (empty($this->id)) return;
        $data = $db->extended->getALL(
            self::SQL_getCaLi, null, $this->id, 'integer');
        IsDbError($data);

        // Übersetzung für die Tätigkeit holen
        foreach($data as &$wert) :
           $wert['job'] = d_feld::getString($wert['tid']);
           if ($wert['vname'] === '-') $wert['vname'] = null;
        endforeach;
        unset($wert);
        return ($data);
    }

    final protected function isLinked() {
    /****************************************************************
    *  Aufgabe: Prüft ob der Datensatz verknüpft ist
    *   Return: int $Anzahl
    ****************************************************************/
        global $db;
        // Prüfkandidaten: f_cast.fid / ...?
        $data = $db->extended->getRow(self::SQL_isLink, null, $this->id);
        IsDbError($data);
        return $data['count'];
    }

    final public function isVal() {
    /****************************************************************
    *  Aufgabe: Testet ob der Datensatz einer Überarbeitung bedarf (a la Wiki)
    *   Return: bool
    ****************************************************************/
        global $db;
        $data = $db->extended->getRow(
            self::SQL_isVal, 'boolean', $this->id, 'integer');
        IsDbError($data);
        return $data['isvalid'];
    }

    public static function search($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in allen Titelspalten
    *   Param:  string
    *   Return: Array der gefunden ID's | Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        $s = "%".$s."%";

        // Suche in titel, atitel, utitel
        $data = $db->extended->getCol(self::SQL_search1, null, array($s,$s,$s));
        IsDbError($data);
        $erg = $data;

        //Weiter suche in Serientiteln
        $stit = $db->extended->getCol(self::SQL_search2, null, $s);
        IsDbError($stit);
        foreach($stit as $wert) :
            $data = $db->extended->getCol(self::SQL_search3, null, array($wert));
            IsDbError($data);
            $erg = array_merge($erg,$data);
        endforeach;
        if ($erg) :
            // Ausfiltern gelöschter Datensätze
            $erg = array_unique($erg);
            foreach($erg as $key => $wert) :
                if(self::is_Del($wert)) unset($erg[$key]);
            endforeach;
            return $erg;
        else :
            return 1;
        endif;
    }
}// ende Main KLASSE

/** ==========================================================================
                                FILM CLASS
========================================================================== **/
final class Film extends Main implements iTyp {
/********************************************************************

Interne Methoden:
    getListGattung()    protected static -> Array(num, text) der Gattungen
    getListPraedikat()  protected static -> Array(num, text) der Praedikate
    getListBildformat()   protected static -> array(string) der Bildformate
    getBildformat()
    getListMediaSpez()  protected static -> num. Liste der Mediaspezifikationen
    getThisMediaSpez()  protected        -> dito für angewandte Mediaspezifikationen
    getListProdTech()   protected static -> array(txt)der Produktionstechniken
    getThisProdTech()   protected        -> dito für verwendete Produktionstechniken

********************************************************************/

    protected
        $gattung    = null,
        $prodtechnik = null,
        $laenge     = null,
        $fsk        = null,
        $praedikat  = 0,
        $mediaspezi = 0,
        $bildformat = 0,
        $urauffuehr = null;

    const
        SQL_get      = 'SELECT gattung, prodtechnik, laenge, fsk,
                          praedikat, bildformat, mediaspezi, urauffuehr
                       FROM f_film WHERE id = ?;',
        SQL_getPraed = 'SELECT * FROM f_praed ORDER BY praed ASC;',
        SQL_getGenre = 'SELECT * FROM f_genre;',
        SQL_getBfLi  = 'SELECT * FROM f_bformat;',
        SQL_getBF    = 'SELECT format FROM f_bformat WHERE id = ?;',
        SQL_getMS    = 'SELECT * FROM f_mediaspezi;',
        SQL_getPT    = 'SELECT * FROM f_prodtechnik;';

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe:
    *   Aufruf:
    *   Return: void
    ****************************************************************/
        parent::get($nr);
        global $db;
        $types      = array(
            'integer',  // gattung
            'integer',  // prodtechnik
            'text',     // laenge   (Sonderformat)
            'integer',  // fsk      (Altersempfehlung)
            'integer',  // praedikat
            'integer',  // bildformat
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
        foreach($data as $key => $wert) $this->$key = $wert;
    }

    protected static function getListGattung() {
    /****************************************************************
    *  Aufgabe: gibt ein  der Gattungen zurück
    *   Return: Array(num, text)
    ****************************************************************/
        global $db;
        $list = $db->extended->getCol(self::SQL_getGenre, 'integer');
        $data = array();
        IsDbError($list);
        foreach($list as $wert) :
            $data[$wert] = d_feld::getString($wert);
        endforeach;
        asort($data);
        return $data;
    }

    protected static function getListPraedikat() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der Praedikate zurück
    *   Return: array(int, string)
    ****************************************************************/
        global $db;
        $list = $db->extended->getCol(self::SQL_getPraed, 'integer');
        IsDbError($list);
        $data = array();
        foreach($list as $wert) $data[$wert] = d_feld::getString($wert);
        return $data;
    }

    protected static function getListBildformat() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der Filmformate zurück
    *   Return: array(int,string)
    ****************************************************************/
        global $db;
        $list = $db->extended->getAll(self::SQL_getBfLi);
        IsDbError($list);
        $data = array('');
        foreach($list as $wert) $data[$wert['id']] = $wert['format'];
        return $data;
    }

    protected function getBildformat() {
    // gibt den string mit dem Bildformat zurück
        global $db;
        if (empty($this->bildformat)) return;
        $data = $db->extended->getOne(
            self::SQL_getBF, null, $this->bildformat);
        IsDbError($data);
        return $data;
    }

    protected static function getListMediaSpez() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der Mediaspezifikationen zurück
    *   Return: array(int)
    ****************************************************************/
        global $db;
        $data = $db->extended->getCol(self::SQL_getMS, 'integer');
        IsDbError($data);
        $data = getStringList($data);
        return $data;
    }

    protected function getThisMediaSpez() {
    /****************************************************************
    *  Aufgabe: gibt die Liste der verwendeten Produktionstechniken zurück
    *   Return: array (string)
    ****************************************************************/
        $list = self::getListMediaSpez();
        $data = array();
        foreach($list as $key => $wert) :
            if(isbit($this->mediaspezi, $key)) $data[] = $wert;
        endforeach;
        return $data;
    }

    protected static function getListProdTech() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der Produktionstechniken zurück
    *   Return: array (string)
    ****************************************************************/
        global $db;
        $data = $db->extended->getCol(self::SQL_getPT, 'integer');
        IsDbError($data);
        $data = getStringList($data);
        return $data;
    }

    protected function getThisProdTech() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der verwendeten Produktionstechniken zurück
    *   Return: array (string)
    ****************************************************************/
        $list = self::getListProdTech();
        $data = array();
        foreach($list as $key => $wert) :
            if(isbit($this->prodtechnik, $key)) $data[] = $wert;
        endforeach;
        return $data;
    }

    public function add($stat) {
    /****************************************************************
    *   Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
    *   Aufruf:  Status
    *   Return:  Fehlercode
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if ($stat == false) :
            $db->beginTransaction('newFilm'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getRow("SELECT nextval('f_main_id_seq');");
            IsDbError($data);
            $this->id = $data['nextval'];
            $this->edit(false);
            $myauth->setAuthData('obj', serialize($this));
        else :
            // Objekt wurde vom Eventhandler initiiert
            $types = array(
                // ACHTUNG! Reihenfolge beachten !!!
                'integer',      // gattung
                'integer',      // prodtechnik
                'text',         // laenge (Sonderformat))
                'integer',      // fsk
                'integer',      // praedikat
                'integer',      // mediaspezi
                'integer',      // bildformat
                'text',         // urauffuehr
                'integer',      // id
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
                'integer');     // sfolge

            $this->edit(true);
            // Typ wird autom. generiert
            foreach($this as $key => $wert) $data[$key] = $wert;
            unset($data['stitel'], $data['sdescr']);
            $erg = $db->extended->autoExecute('f_film', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);
            $db->commit('newFilm'); IsDbError($db);
            // ende Transaktion
        endif;
    }

    public function edit($stat) {
    /****************************************************************
    *   Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
    *   Aufruf: array, welches die zu ändernden Felder enthält
    *   Return: none
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if($stat == false) :        // Formular anzeigen
            $data = array(
                // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('serTitel',  parent::getSTitelList()),
                new d_feld('gattLi',    self::getListGattung()),
                new d_feld('praedLi',   self::getListPraedikat()),
                new d_feld('taetigLi',  parent::getTaetigList()),
                new d_feld('prodTecLi', self::getListProdTech()),
                new d_feld('bildFormLi',self::getListBildformat()),
                new d_feld('mediaSpezLi',self::getListMediaSpez()),
                new d_feld('persLi',    person::getPersonLi()),
                new d_feld('bereich',   null,               null, 4027),
                new d_feld('id',        $this->id),
                new d_feld('titel',     $this->titel,       EDIT, 500),
                new d_feld('atitel',    $this->atitel,      EDIT, 503),
                new d_feld('utitel',    $this->utitel,      EDIT, 501),
                new d_feld('stitel',    $this->stitel,      EDIT, 504),
                new d_feld('sfolge',    $this->sfolge,      EDIT, 505),
                new d_feld('sid',       $this->sid),
                new d_feld('bild_id',   'bilddaten array()', EDIT),
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
                new d_feld('bildformat',$this->bildformat,  EDIT, 608),
                new d_feld('mediaspezi',bit2array($this->mediaspezi),  EDIT, 583),
                new d_feld('urauff',    $this->urauffuehr,  EDIT, 584),
                new d_feld('isvalid',   $this->isvalid,     IEDIT, 10009),
            );
            // CastListe nur beim bearbeiten und nicht bei Neuanlage zeigen.
            if ($this->titel) $data[] = new d_feld('cast', $this->getCastList(), EDIT);

            $smarty->assign('dialog', a_display($data));
            $smarty->display('figd_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        else :                         // Formular auswerten
            // Obj zurückspeichern wird im aufrufenden Teil erledigt
            if (empty($this->titel) AND empty($_POST['titel'])) {
                fehler(100);
                exit();
            } else if ($_POST['titel']) $this->titel = normtext($_POST['titel']);

            if(isset($_POST['atitel'])) :
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
                $this->prodtechnik = bitArr2wert($_POST['prodtech']);
            else $this->prodtechnik = null;

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

            if(isset($_POST['urauff'])) :
                if ($_POST['urauff']) {
                    if(isvalid($_POST['urauff'], DATUM))
                        $this->urauffuehr = normtext($_POST['urauff']);
                    else fehler(103);
                } else $this->urauffuehr = null;
            endif;

            if(isset($_POST['bildformat']))
                if ($_POST['bildformat'])
                    $this->bildformat = normzahl($_POST['bildformat']);

            if(isset($_POST['mediaspezi']))
                $this->mediaspezi = bitArr2wert($_POST['mediaspezi']);
            else $this->mediaspezi = null;


            if(isset($_POST['notiz'])) :
                if ($_POST['notiz'])
                    $this->notiz = normtext($_POST['notiz']);
                else $this->notiz = null;
            endif;

            $this->isvalid = false;
            if(isset($_POST['isvalid'])) :
                if ($_POST['isvalid']) $this->isvalid = true;
            endif;

            $this->editfrom = $myauth->getAuthData('uid');
            $this->editdate = date('c', $_SERVER['REQUEST_TIME']);

            // doppelten Datensatz abfangen
            $number = self::ifDouble();
            if (!empty($number) AND $number != $this->id) warng('10008');
        endif;  // Formularbereich
    }

    public function set() {
    /****************************************************************
    *   Aufgabe: schreibt die Daten in die Tabelle 'f_film' zurück (UPDATE)
    *    Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if(!$this->id) return 4;         // Abbruch: leerer Datensatz
        $types = array(
        // ACHTUNG: Reihenfolge beachten!
            'integer',      // gattung
            'integer',      // prodtechnik
            'text',         // laenge (Sonderformat))
            'integer',      // fsk
            'integer',      // praedikat
            'integer',      // mediaspezi
            'integer',      // bildformat
            'text',         // urauffuehr
            'boolean',      // del
            'integer',      // editfrom
            'timestamp',    // editdate
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
            'integer',      // typ (1 = film)
        );
        foreach($this as $key => $wert) $data[$key] = $wert;
        unset($data['stitel'], $data['sdescr'], $data['id']);
        $erg = $db->extended->autoExecute('f_film', $data,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
        IsDbError($erg);
        return;

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

    public function view() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Filmdatensatzes (an smarty)
    *    Return: none
    ****************************************************************/
        global $db, $myauth, $smarty;
        if($this->isDel()) return;          // nichts ausgeben, da gelöscht
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if(!empty($this->editfrom)) :
            $bearbeiter = $db->extended->getCol(
                'SELECT realname FROM s_auth WHERE uid = '.$this->editfrom.';');
            IsDbError($bearbeiter);
        else : $bearbeiter = null;
        endif;
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
            new d_feld('inhalt',    changetext($this->inhalt),  VIEW, 506),
            new d_feld('notiz',     changetext($this->notiz),   EDIT, 514),
            new d_feld('anmerk',    changetext($this->anmerk),  VIEW, 572),
            new d_feld('gattung',   d_feld::getString($this->gattung), VIEW, 579),
            new d_feld('prodtech',  self::getThisProdTech(),    VIEW, 571),
            new d_feld('laenge',    $this->laenge,              VIEW, 580),
            new d_feld('fsk',       $this->fsk,                 VIEW, 581),
            new d_feld('praedikat', d_feld::getString($this->praedikat), VIEW, 582),
            new d_feld('bildformat', self::getBildformat(),    VIEW, 608),
            new d_feld('mediaspezi', self::getThisMediaSpez(),  VIEW, 583),
            new d_feld('urrauff',   $this->urauffuehr,          VIEW, 584),
            new d_feld('cast',      $this->getCastList(),       VIEW),
            new d_feld('edit',      null, EDIT, null, 4013), // edit-Button
            new d_feld('del',       null, DELE, null, 4020), // Lösch-Button
            new d_feld('isVal',     $this->isvalid,             VIEW, 10009),
            new d_feld('chdatum',   $this->editdate),
            new d_feld('chname',    $bearbeiter[0]),
        ));
        $smarty->assign('dialog', $data);
        $smarty->display('figd_dat.tpl');
    }
} // endclass Film


/** ==========================================================================
                                BIBLIO CLASS
========================================================================== **/
class Biblio extends Main implements iTyp {
/********************************************************************


********************************************************************/

    protected   // --> Variablen und Konstantendefinition
        $szahl  = null,     //Seitenzahl
        $format = null;     // Bitset für Formate: Paperback, Heft etc..

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe:
    *   Aufruf:
    *   Return: void
    ****************************************************************/
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

?>