<?php
/**************************************************************
    PHP Version >= 5.4

    Klassenbibliothek für Filmogr.-/Bibliografische Daten

    $Rev$
    $Author$
    $Date$
    $URL$

    Author: Knut Wetzig <knwetzig@gmail.com>

**************************************************************/

interface iFibikern {
    public static function getSTitelList();
    public static function getTitelList();
    public function getTitel();
    public function addCast($p, $t);
    public function delCast($p, $t);
    public function search($s);
}

interface iFilm extends iFibikern {
    public function add($status = null);
    public function edit($status = null);
    public function save();
}

interface iBiblio extends iFibikern {
    public function add($status = null);
    public function edit($status = null);
    public function save();
}

/** ==========================================================================
                               FIBIKERN CLASS
========================================================================== **/
abstract class Fibikern extends Entity implements iFibikern {

    const
        TYPEFIBI = 'text,text,text,integer,integer,text,text,text,text,',
        GETDATA  = 'SELECT titel, atitel, utitel, sid, sfolge, prod_jahr,
                            anmerk, quellen, thema
                    FROM f_main2 WHERE id = ?;',
        GETSTITEL= 'SELECT titel, descr FROM f_stitel WHERE sertitel_id = ?;',
        IFDOUBLE = 'SELECT id FROM f_main2 WHERE (del = false) AND (titel = ?);',
        GETTAETIG= 'SELECT * FROM f_taetig;',
        EXCAST   = 'SELECT COUNT(*) FROM f_cast WHERE fid = ? AND pid = ? AND tid = ?;',
        GETCALI  = 'SELECT f_cast.tid, f_cast.pid
                    FROM f_cast WHERE f_cast.fid = ? ORDER BY tid;',
        ISLINK   = 'SELECT COUNT(*) FROM f_cast WHERE fid = ?',
        GETSTILI = 'SELECT sertitel_id, titel FROM f_stitel ORDER BY titel ASC;',
        GETTILI  = 'SELECT f_main2.id, f_main2.titel FROM public.f_main2
                    WHERE f_main2.del != TRUE ORDER BY f_main2.titel ASC;',
        SEARCH   = 'SELECT DISTINCT id FROM f_main2, f_stitel
                    WHERE (f_main2.del = false) AND
                          (f_main2.titel ILIKE ? OR f_main2.atitel ILIKE ? OR
                           f_main2.utitel ILIKE ? OR
                          (f_stitel.titel ILIKE ? AND f_stitel.sertitel_id = f_main2.sid));';

    protected
        $stitel = null,             // Serientitel -> diafip.f_stitel.titel
        $sdescr = null;             // Beschreibung Serie

    public function __construct($nr = NULL) {
        parent::__construct($nr);
        $this->content['titel']     = null;   // Originaltitel
        $this->content['atitel']    = null;   // Arbeitstitel
        $this->content['utitel']    = null;   // Untertitel
        $this->content['sid']       = null;   // Serien - ID
        $this->content['sfolge']    = null;   // Serienfolge
        $this->content['prod_jahr'] = null;
        $this->content['anmerk']    = null;
        $this->content['quellen']   = null;
        $this->content['thema']     = null;     // Schlagwortverzeichnis
        if ((isset($nr)) AND is_numeric($nr)) self::get(intval($nr));
    }

    protected function get($nr) {
    /****************************************************************
    Aufgabe: Initialisiert das Objekt (auch gelöschte)
     Return: void
    ****************************************************************/
        $db = MDB2::singleton();
        $data = $db->extended->getRow(self::GETDATA,list2array(self::TYPEFIBI),$nr,'integer');
        IsDbError($data);
        if ($data) :
        foreach ($data as $key => $val) $this->content[$key] = $val;
        else:
            feedback(4, 'error');        // kein Datensatz vorhanden
            exit(4);
        endif;

        // Serientitel holen, soweit vorhanden
        if ($this->content['sid']) :
            $data = $db->extended->getRow(self::GETSTITEL, null, $this->content['sid']);
            IsDbError($data);
            $this->stitel = $data['titel'];
            $this->sdescr = $data['descr'];
        endif;
    }

    public function getTitel() {
    /****************************************************************
    *  Aufgabe: Ausgabe des Titels
    *   Return: String / Fehlercode
    ****************************************************************/
        return '<a href="index.php?'.$this->bereich.'='.$this->id.'">'.$this->titel.'</a>';
    }

    final protected function ifDouble() {
    /**********************************************************
    Aufgabe: Ermitteln gleichlautender Titel
     Return: int (ID des letzten Datensatzes | null )
    **********************************************************/
        $db = MDB2::singleton();
        $data = $db->extended->getOne(self::IFDOUBLE, null, $this->content['titel']);
        IsDbError($data);
        return $data;
    }

    final protected static function getTaetigList() {
    /**********************************************************
    Aufgabe: gibt ein Array(num, text) der Taetigkeiten zurück
     Return:
    **********************************************************/
        $db = MDB2::singleton();
        $list = $db->extended->getCol(self::GETTAETIG, 'integer');
        IsDbError($list);
        $data = [];
        foreach ($list as $wert) $data[$wert] = $str->getStr($wert);
        asort($data);
        return $data;
    }

    final public static function getSTitelList() {
    /**********************************************************
    Aufgabe: Ausgabe der Serientitelliste
     Return: array | Fehlercode
    **********************************************************/
        $db = MDB2::singleton();
        $ergebnis = [];
        $erg =& $db->query(self::GETSTILI);
        IsDbError($erg);
        while ($row =$erg->fetchRow()) :
            $ergebnis[$row['sertitel_id']] = $row['titel'];
        endwhile;
        if ($ergebnis) return $ergebnis; else return 1;
    }

    final public static function getTitelList() {
    /**********************************************************
    Aufgabe: Ausgabe der Titelliste (Filme/Bücher)
     Return: array | Fehlercode
    **********************************************************/
        $db = MDB2::singleton();
        $list = $db->extended->getAll(self::GETTILI);
        IsDbError($list);

        $data = [0 => null];
        foreach ($list as $wert) $data[$wert['id']] = $wert['titel'];
        return $data;
    }

    final private function existCast($p, $t) {
    /**********************************************************
    Aufgabe: Testet ob ein Castingeintrag für fid vorhanden ist
     Aufruf: int ($pid), int ($taetigkeit)
     Return: int (Anzahl)
    **********************************************************/
        $db = MDB2::singleton();
        $data = $db->extended->getOne(
            self::EXCAST, integer, [$this->content['id'], $p, $t],
            ['integer', 'integer', 'integer']);
        IsDbError($data);
        return $data;
    }

    final public function addCast($p, $t) {
    /**********************************************************
    Aufgabe: fügt einen Castingdatensatz ein
     Aufruf: int ($pid), int ($taetigkeit)
     Return: Fehlercode
    **********************************************************/
        $db = MDB2::singleton();
        // testen, das nix doppelt eingetragen wird!
        if (self::existCast($p, $t)) return 8;

        IsDbError($db->extended->autoExecute(
            'f_cast', ['fid' => $this->content['id'], 'pid' => $p, 'tid' => $t],
            MDB2_AUTOQUERY_INSERT, null, ['integer', 'integer', 'integer']));
    }

    final public function delCast($p, $t) {
    /**********************************************************
    Aufgabe: löscht einen Castingsatz für diesen Eintrag
     Aufruf: int ($pid), int ($taetigkeit)
     Return: Fehlercode
    **********************************************************/
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db = MDB2::singleton();
        IsDbError($db->extended->autoExecute(
            'f_cast', null, MDB2_AUTOQUERY_DELETE,
            'fid = '.$this->content['id'].' AND pid = '.$p.' AND tid = '.$t
            ));
    }

    final protected function getCastList() {
    /**********************************************************
    Aufgabe: gibt die Besetzungsliste für diesen Datensatz aus
     Return: array(name, tid, pid, job)
        Die Sortierreihenfolge ist durch die ID in der Stringtabelle
        fest vorgegeben. Bei Änderung bitte den Eintrag in der Tabelle
        f_taetig korrigieren.
    **********************************************************/
        $db = MDB2::singleton();
        if (empty($this->content['id'])) return;
        $data = $db->extended->getALL(
            self::GETCALI, null, $this->content['id'], 'integer');
        IsDbError($data);

        // Übersetzung für die Tätigkeit und Namen holen
        foreach ($data as &$wert) :
           $wert['job'] = $str->getStr($wert['tid']);
           $p = new Person($wert['pid']);
           $wert['name'] = $p->getName();
        endforeach;
        unset($wert);
        return $data;
    }

    final protected function isLinked() {
    /**********************************************************
    Aufgabe: Prüft ob der Datensatz verknüpft ist
     Return: int $Anzahl
    **********************************************************/
        $db = MDB2::singleton();
        // Prüfkandidaten: f_cast.fid / ...?
        $data = $db->extended->getOne(self::ISLINK, null, $this->content['id']);
        IsDbError($data);
        return $data;
    }

    public function search($s) {
    /**********************************************************
    Aufgabe: Suchfunktion in allen Titelspalten incl. Serientiteln
      Param:  string
     Return: Array der gefunden ID's | Fehlercode
    **********************************************************/
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $s = "%".$s."%";
        $db = MDB2::singleton();

        // Suche in titel, atitel, utitel
        $data = $db->extended->getCol(self::SEARCH, ['integer'], [$s,$s,$s.$s]);
        IsDbError($data);
        if ($data) :
            return $data;
        else :
            return 1;
        endif;
    }

    public function view() {
    /****************************************************************
    Aufgabe: Zusammenstellung der Daten eines Datensatzes, Einstellen der Rechteparameter
            Auflösen von Listen und holen der Strings aus der Tabelle Zuweisungen und
            ausgabe via display()
    Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $data = parent::view();
        $data[] = new d_feld('titel',$this->content['titel'], VIEW, 500); // Originaltitel
        $data[] = new d_feld('atitel',$this->content['atitel'], VIEW, 503); // Arbeitstitel
        $data[] = new d_feld('utitel',$this->content['utitel'], VIEW, 501); // Untertitel
        $data[] = new d_feld('stitel',$this->stitel, VIEW, 504);            // Serientitel
        $data[] = new d_feld('sfolge',$this->content['sfolge'],VIEW);       // Serienfolge
        $data[] = new d_feld('sdescr',$this->sdescr);                   // Beschreibung Serie
        $data[] = new d_feld('prod_jahr',$this->content['prod_jahr'],VIEW,576);
        $data[] = new d_feld('anmerk',changetext($this->content['anmerk']),VIEW,572);
        $data[] = new d_feld('quellen',$this->content['quellen'],VIEW, 578);
        $data[] = new d_feld('thema',$this->content['thema'],VIEW,577); // Schlagwortliste
//        new d_feld('isVal',     $this->isvalid,             VIEW, 10009);
//        new d_feld('cast',      $this->getCastList(),       VIEW);
        return $data;
    }
}       // ende Main KLASSE

/** ==========================================================================
                                FILM CLASS
========================================================================== **/
final class Film extends Fibikern implements iFilm {

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
        SQL_get      = 'SELECT gattung, prodtechnik, laenge, fsk, praedikat,
                            bildformat, mediaspezi, urauffuehr
                       FROM f_film WHERE id = ?;',
        SQL_getPraed = 'SELECT * FROM f_praed ORDER BY praed ASC;',
        SQL_getGenre = 'SELECT * FROM f_genre;',
        SQL_getBfLi  = 'SELECT * FROM f_bformat;',
        SQL_getBF    = 'SELECT format FROM f_bformat WHERE id = ?;',
        SQL_getMS    = 'SELECT * FROM f_mediaspezi;',
        SQL_getPT    = 'SELECT * FROM f_prodtechnik;';

    public function __construct($nr = NULL) {
        parent::__construct($nr);
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe:
    *   Aufruf:
    *   Return: void
    ****************************************************************/
        $types      = [
            'integer',  // gattung
            'integer',  // prodtechnik
            'time',     // laenge   (Sonderformat)
            'integer',  // fsk      (Altersempfehlung)
            'integer',  // praedikat
            'integer',  // bildformat
            'integer',  // mediaspezi
            'date'      // urauffuehr
        ];

        $db = MDB2::singleton();
        $data = $db->extended->getRow(self::SQL_get, $types, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) feedback(4, 'error');        // kein Datensatz vorhanden
        // Ergebnis -> Objekt schreiben
        foreach ($data as $key => $wert) $this->$key = $wert;
    }

    protected static function getListGattung() {
    /****************************************************************
    *  Aufgabe: gibt ein  der Gattungen zurück
    *   Return: Array(num, text)
    ****************************************************************/
        $db = MDB2::singleton();
        $list = $db->extended->getCol(self::SQL_getGenre, 'integer');
        $data = [];
        IsDbError($list);
        foreach ($list as $wert) :
            $data[$wert] = $str->getStr($wert);
        endforeach;
        asort($data);
        return $data;
    }

    protected static function getListPraedikat() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der Praedikate aus
    *   Return: array(int, string)
    ****************************************************************/
        $db = MDB2::singleton();
        $list = $db->extended->getCol(self::SQL_getPraed, 'integer');
        IsDbError($list);
        $data = [];
        // TODO: Ueberdenken den Einsatz von getStringList !
        foreach ($list as $wert) $data[$wert] = $str->getStr($wert);
        return $data;
    }

    protected static function getListBildformat() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der Filmformate zurück
    *   Return: array(int,string)
    ****************************************************************/
        $db = MDB2::singleton();
        $list = $db->extended->getAll(self::SQL_getBfLi);
        IsDbError($list);
        $data = [];
        foreach ($list as $wert) $data[$wert['id']] = $wert['format'];
        return $data;
    }

    protected function getBildformat() {
    // gibt den string mit dem Bildformat zurück
        $db = MDB2::singleton();
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
        $db = MDB2::singleton();
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
        $data = [];
        foreach ($list as $key => $wert) :
            if (isbit($this->mediaspezi, $key)) $data[] = $wert;
        endforeach;
        return $data;
    }

    protected static function getListProdTech() {
    /****************************************************************
    *  Aufgabe: gibt eine Liste der Produktionstechniken zurück
    *   Return: array (string)
    ****************************************************************/
        $db = MDB2::singleton();
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
        $data = [];
        foreach ($list as $key => $wert) :
            if (isbit($this->prodtechnik, $key)) $data[] = $wert;
        endforeach;
        return $data;
    }

    protected function getProdLand() {
    /****************************************************************
    *  Aufgabe: Prüft, ob für diesen filmogr. Datensatz ein Hersteller
    *           angelegt ist und gibt im Erfolgsfall, das aus den Personen-
    *           daten ermittelte Land zurück.
    *   Return: array | NULL
    ****************************************************************/
        $db = MDB2::singleton();
        $ProdLand = $db->extended->getCol(
           'SELECT s_land.land
            FROM
              public.f_cast, public.p_person, public.s_land, public.s_orte
            WHERE
              f_cast.pid = p_person.id AND p_person.wort = s_orte.id AND
              s_orte.land = s_land.id AND f_cast.fid = ? AND f_cast.tid = ?;',
            null, [$this->id, 1480], ['integer', 'integer']
        );
        IsDbError($ProdLand);
        return $ProdLand;
    }

    public function add($status = null) {
    /****************************************************************
    *   Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
    *   Aufruf:  Status
    *   Return:  Fehlercode
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db = MDB2::singleton();
        if ($status == false) :
            $db->beginTransaction('newFilm'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('id_seq');");
            IsDbError($data);
            $this->id = $data;
            $this->edit(false);
        else :
            // Objekt wurde vom Eventhandler initiiert
            $types = [
                // ACHTUNG! Reihenfolge beachten !!!
                'integer',      // gattung
                'integer',      // prodtechnik
                'time',         // laenge (Sonderformat))
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
                'integer'];     // sfolge

            $this->edit(true);
            foreach ($this as $key => $wert) $data[$key] = $wert;
            unset($data['stitel'], $data['sdescr']);
            $erg = $db->extended->autoExecute('f_film', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);
            $db->commit('newFilm'); IsDbError($db);
            // ende Transaktion
        endif;
    }

    public function edit($status = null) {
    /****************************************************************
    *   Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
    *   Aufruf: array, welches die zu ändernden Felder enthält
    *   Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if ($status == false) :        // Formular anzeigen
            $data = [
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
            ];
            // CastListe nur beim bearbeiten und nicht bei Neuanlage zeigen.
            if ($this->titel) $data[] = new d_feld('cast', $this->getCastList(), EDIT);

            $smarty->assign('dialog', a_display($data));
            $smarty->display('figd_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));

        else :          // Formular auswerten - Obj zurückspeichern wird im
                        // aufrufenden Teil erledigt

/** ===== Neue Fehlerbehandlung einfügen ===== **/
            try {
                if (empty($this->titel) AND empty($_POST['titel']))
                    throw new Exception(null, 100);
                else if ($_POST['titel']) $this->titel = $_POST['titel'];

                if (isset($_POST['atitel'])) :
                    if ($_POST['atitel']) $this->atitel = $_POST['atitel'];
                    else $this->atitel = null;
                endif;

                if (isset($_POST['sid']))
                    $this->sid = intval($_POST['sid']); else $this->sid = null;
                if ($this->sid AND isset($_POST['sfolge']))
                    $this->sfolge = intval($_POST['sfolge']);
                else $this->sfolge = null;

                if (isset($_POST['utitel'])) :
                    if ($_POST['utitel']) $this->utitel = $_POST['utitel'];
                    else $this->utitel = null;
                endif;

                if (isset($_POST['inhalt'])) :
                    if ($_POST['inhalt']) $this->inhalt = $_POST['inhalt'];
                    else $this->inhalt = null;
                endif;

                if (isset($_POST['quellen'])) :
                    if ($_POST['quellen']) $this->quellen = $_POST['quellen'];
                    else $this->quellen = null;
                endif;

                if (isset($_POST['anmerk'])) :
                    if ($_POST['anmerk']) $this->anmerk = $_POST['anmerk'];
                    else $this->anmerk = null;
                endif;

                if (isset($_POST['prod_jahr'])) :
                    if ($_POST['prod_jahr']) {
                        if (isvalid($_POST['prod_jahr'], '[\d]{1,4}'))
                            $this->prod_jahr = intval($_POST['prod_jahr']);
                        else feedback(103, 'warng');
                    } else $this->prod_jahr = null;
                endif;

                if (isset($_POST['thema'])) :
                    if ($_POST['thema']) $this->thema = $_POST['thema'];
                    else $this->thema = null;
                endif;

                if (isset($_POST['gattung'])) :
                    if ($_POST['gattung']) {
                        if (isvalid($_POST['gattung'], ANZAHL))
                            $this->gattung = intval($_POST['gattung']);
                        else throw new Exception(null, 4);
                    } else $this->gattung = null;
                endif;

                if (isset($_POST['prodtech']))
                $this->prodtechnik = array2wert(0, $_POST['prodtech']);
                else $this->prodtechnik = null;

                if (!empty($_POST['laenge']))
                    if ( isValid($_POST['laenge'], DAUER)) $this->laenge = $_POST['laenge']; else feedback(4, warng);
                else $this->laenge = null;

                if (isset($_POST['fsk'])) :
                    if (!empty($_POST['fsk'])) {
                        if (isvalid($_POST['fsk'], ANZAHL))
                            $this->fsk = intval($_POST['fsk']);
                        else throw new Exception(null, 4);
                    } else $this->fsk = null;
                endif;

                if (isset($_POST['praedikat'])) :
                    if ($_POST['praedikat']) {
                        if (isvalid($_POST['praedikat'], ANZAHL))
                            $this->praedikat = intval($_POST['praedikat']);
                        else throw new Exception(null, 4);
                    } else $this->praedikat = null;
                endif;

                if (isset($_POST['urauff'])) :
                    if ($_POST['urauff']) {
                        if (isvalid($_POST['urauff'], DATUM))
                            $this->urauffuehr = $_POST['urauff'];
                        else feedback(103, 'warng');
                    } else $this->urauffuehr = null;
                endif;

                if (isset($_POST['bildformat']))
                    if ($_POST['bildformat']) $this->bildformat = intval($_POST['bildformat']);

                if (isset($_POST['mediaspezi']))
                    $this->mediaspezi = array2wert(0, $_POST['mediaspezi']);
                else $this->mediaspezi = null;

                if (isset($_POST['notiz'])) :
                    if ($_POST['notiz']) $this->notiz = $_POST['notiz'];
                        else $this->notiz = null;
                endif;

                $this->isvalid = false;
                if (isset($_POST['isvalid'])) :
                    if ($_POST['isvalid']) $this->isvalid = true;
                endif;

                $this->editfrom = $myauth->getAuthData('uid');
                $this->editdate = date('c', $_SERVER['REQUEST_TIME']);

                // doppelten Datensatz abfangen
                $number = self::ifDouble();
                if (!empty($number) AND $number != $this->id) feedback(10008, 'warng');
            }   // end try

            catch (Exception $e) {
                $fehler[] = $e->getMessage();
            }

            foreach ($fehler as $error) $errmsg .= $error.'<br />';
            if ($errmsg) {
                feedback(substr($errmsg, 0, -6), 'error');
                exit;
            }

        endif;      // form anzeigen/auswerten
    }

    public function save() {
    /****************************************************************
    *   Aufgabe: schreibt die Daten in die Tabelle 'f_film' zurück (UPDATE)
    *    Return: Fehlercode
    ****************************************************************/
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->id) return 4;         // Abbruch: leerer Datensatz

        $types = [
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
            'integer'       // sfolge
        ];

        foreach ($this as $key => $wert) $data[$key] = $wert;
        unset($data['stitel'], $data['sdescr'], $data['id']);

        $db = MDB2::singleton();
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
    *   Aufgabe: Ausgabe des Filmdatensatzes (an smarty) in der Detailansicht
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $data = parent::view();
            // name, inhalt, opt -> rechte, label,tooltip
        $data[] = new d_feld('prod_land', self::getProdLand(),VIEW,698);
        $data[] = new d_feld('gattung', $str->getStr($this->gattung),VIEW,579);
        $data[] = new d_feld('prodtech',  self::getThisProdTech(),VIEW,571);
        $data[] = new d_feld('laenge',    $this->laenge,VIEW,580);
        $data[] = new d_feld('fsk',       $this->fsk,VIEW,581);
        $data[] = new d_feld('praedikat', $str->getStr($this->praedikat),VIEW, 582);
        $data[] = new d_feld('bildformat', self::getBildformat(),VIEW,608);
        $data[] = new d_feld('mediaspezi', self::getThisMediaSpez(),VIEW,583);
        $data[] = new d_feld('urrauff',   $this->urauffuehr,VIEW,584);
    }
} // endclass Film


/** ==========================================================================
                                BIBLIO CLASS
========================================================================== **/
class Biblio extends Fibikern implements iBiblio {

    protected   // --> Variablen und Konstantendefinition
        $szahl  = null,     //Seitenzahl
        $format = null;     // Bitset für Formate: Paperback, Heft etc..

    public function __construct($nr = NULL) {
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

    public function add($status = null) {
    /****************************************************************
        Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
        Aufruf:
        Return: Fehlercode
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }

    public function edit($status = null) {
    /****************************************************************
        Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
        Aufruf: array, welches die zu ändernden Felder enthält
        Return: Fehlercode
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }

    public function save() {
    /****************************************************************
        Aufgabe: schreibt die Daten in die Tabelle 'f_biblio' zurück (UPDATE)
        Return: Fehlercode
    ****************************************************************/
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }

    public function view() {
    /****************************************************************
        Aufgabe: prepare to display
        Aufruf:
        Return: Fehlercode
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
    }
} // endclass Biblio

?>