<?php
/********************************************************************

    Klassenbibliothek für Gegenstände

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. ************************************************/

interface iItem {
    const
        SQL_isVal   = 'SELECT isvalid FROM i_main WHERE id = ?;',
        SQL_search1 =
            'SELECT id FROM i_main WHERE (bezeichner ILIKE ?) AND (del != TRUE);';
    public function edit($stat);
    public function del();
    public static function is_Del($nr);
    public function isVal();
    public static function search($s);
}

interface iPlanar extends iItem {
    public function add($stat);
    public function set();
    public function view();
}

/** ==========================================================================
                               ITEM CLASS
========================================================================== **/
abstract class Item implements iItem {
/*      interne Methoden:
            __construct($nr = null)
            ea_struct($set)         // Vorgaben der einzelnen Felder
                                    // für Ein-/Ausgabe
    int     ifDouble()
    void    get(int $nr)
            isDel()
            isLinked()
*/

    protected                       // entspricht Reihenfolge in der DB
        $id             = null,
        $notiz          = null,
        $eigner         = 2,        // -> DIAF
        $leihbar        = false,
        $x              = null,     // in mm (max 32757)
        $y              = null,
        $kollo          = 1,
        $akt_ort        = null, // aktueller Aufenthaltsort des Gegenstandes....
        $a_wert         = 10,       // Anschaffungswert zur Zeit des Zugangs
        $oldsig         = null,     // alte Signatur
        $herkunft       = 1,        // -> DEFA Studio für Trickfilme
        $in_date        = '1993-11-16', // Zugangsdatum...
        $descr          = null,     // Beschreibung des Gegenstandes
        $rest_report    = null,     // Restaurierungsbericht
        $bild_id        = '{}',
        $obj_typ        = 1,
        $del            = false,
        $lagerort       = null,     // -> lagerort
        $bezeichner     = null,
        $editfrom       = null,
        $editdate       = null,
        $zu_film        = null,     // Verweis auf bibl./filmogr. Daten
        $isvalid        = false;    // Bearbeitung abgeschlossen


    protected
        $types      = array(
            'integer',  // id
            'text',     // notiz
            'integer',  // eigner
            'boolean',  // verleihfähig
            'integer',  // x
            'integer',  // y
            'integer',  // kollo
            'text',     // akt_ort
            'integer',  // a_wert
            'text',     // oldsig
            'integer',  // herkunft
            'date',     // in_date
            'text',     // descr
            'text',     // rest_report
            'text',     // images[]     Korrektur nötig
            'integer',  // obj_typ
            'boolean',  // del          true wenn gelöscht
            'integer',  // lagerort
            'text',     // bezeichner
            'integer',  // editfrom
            'date',     // editdate
            'integer',  // zu_film
            'boolean'   // isvalid
        );

    const
        // für protected Funktionen
//        SQL_ifDouble = 'SELECT id FROM i_main WHERE oldsig = ? AND del = false;',
        SQL_get     = 'SELECT * FROM i_main WHERE id = ?;',
        SQL_isDel   = 'SELECT del FROM i_main WHERE id = ?;';
//        SQL_isLink  = 'SELECT COUNT(*) FROM ____ WHERE id = ?';

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    final protected function ea_struct($set) {
    /****************************************************************
    *  Aufgabe: gibt den Ein-/Ausgaberecord zurück
    *   Return: array
    ****************************************************************/
        global $db;

        switch ($set) :
          case 'edit' :
            $data = array(
                new d_feld('persLi', Person::getPersonLi()),    // Personenliste
                new d_feld('lortLi', LOrt::getLOrtList()),        // Liste Lagerorte
                new d_feld('filmLi', Film::getTitelList()),     // Liste filmogr.
                new d_feld('bezeichner', $this->bezeichner, EDIT, 4029),
                new d_feld('x',         $this->x,           EDIT,  469, ANZAHL),
                new d_feld('y',         $this->y,           EDIT,  470, ANZAHL),
                new d_feld('kollo',     $this->kollo,       EDIT,  475, ANZAHL),
                new d_feld('lagerort',  $this->lagerort,  ARCHIV,  472, ANZAHL),
                new d_feld('akt_ort',   $this->akt_ort,     EDIT,  476),
                new d_feld('zu_film',   $this->zu_film,     EDIT,    5, ANZAHL),
                new d_feld('eigner',    $this->eigner,     IEDIT,  473, ANZAHL),
                new d_feld('herkunft',  $this->herkunft,   IEDIT,  480, ANZAHL),
                new d_feld('in_date',   $this->in_date,    IEDIT,  481, DATUM),
                new d_feld('leihbar',   true,             ARCHIV,  474, BOOL),
                new d_feld('a_wert',    $this->a_wert,     IEDIT, 4031, DZAHL),
                new d_feld('rest_report',$this->rest_report, EDIT, 482),
                new d_feld('descr',     $this->descr,       EDIT,  506),
                new d_feld('notiz',     $this->notiz,       EDIT,  514),
                new d_feld('isvalid',   $this->isvalid,   ARCHIV,10010)
            );
            if(empty($this->oldsig))
                        $data[] = new d_feld('oldsig', null, EDIT, 479, NAMEN);
            break;

          case 'view' :
            if(!empty($this->editfrom)) :
                $bearbeiter = $db->extended->getOne(
                    'SELECT realname FROM s_auth WHERE uid = '.$this->editfrom.';');
                IsDbError($bearbeiter);
            else : $bearbeiter = null; endif;

            $besitzer = new Person($this->eigner);
            $vbesitz  = new Person($this->herkunft);
            $lagerort = new LOrt($this->lagerort);
            $data = array( // name, inhalt, opt -> rechte, label,tooltip
                new d_feld('id',        $this->id),
    //          new d_feld('bild_id',   $this->bild_id),
                new d_feld('notiz',     changetext($this->notiz),   EDIT, 514),
                new d_feld('edit',      null, EDIT, null, 4013), // edit-Button
                new d_feld('del',       null, DELE, null, 4020), // Lösch-Button
                new d_feld('chdatum',   $this->editdate,   IVIEW),
                new d_feld('chname',    $bearbeiter,       IVIEW),
                new d_feld('isvalid',   $this->isvalid,    IVIEW, 10010),
                new d_feld('bezeichner', $this->bezeichner, VIEW),
                new d_feld('lagerort',  $lagerort->getLOrt(), IVIEW, 472),
                new d_feld('eigner',    $besitzer->getName(), IVIEW, 473),
                new d_feld('leihbar',   $this->leihbar,     VIEW, 474),
                new d_feld('x',         $this->x,           VIEW, 469),
                new d_feld('y',         $this->y,           VIEW, 470),
                new d_feld('kollo',     $this->kollo,       IVIEW, 475),
                new d_feld('zu_film',   Film::getTitel($this->zu_film), VIEW, 5),
                new d_feld('akt_ort',   $this->akt_ort,     IVIEW, 476),
                new d_feld('vers_wert', $this->VWert(),     VIEW, 477),
                new d_feld('oldsig',    $this->getOSig(),   IVIEW, 479),
                new d_feld('herkunft',  $vbesitz->getName(),IVIEW, 480),
                new d_feld('in_date',   $this->in_date,     IVIEW, 481),
                new d_feld('descr',     changetext($this->descr), VIEW, 506),
                new d_feld('rest_report', changetext($this->rest_report), IVIEW, 482),
            );

        endswitch;
        return $data;
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe: Initialisiert das Objekt (auch gelöschte)
    *   Return: void
    ****************************************************************/
        global $db;
        $data = $db->extended->getRow(self::SQL_get, $this->types, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) fehler(4);    // kein Datensatz vorhanden

        // Ergebnis -> Objekt schreiben
        foreach($data as $key => $wert) $this->$key = $wert;
    }

    public function edit($stat) {
    /****************************************************************
    *  Aufgabe: Validiert und setzt die Eingaben (basics).
    *           Wird nur vom Auswertungszweig benötigt
    *   Return: void
    ****************************************************************/
        global $myauth;

        try {
            if (empty($this->bezeichner) AND empty($_POST['bezeichner']))
                throw new Exception(null, 100);
            if ($_POST['bezeichner']) $this->bezeichner = $_POST['bezeichner'];

            if (!empty($_POST['x']) AND is_numeric($_POST['x']))
                $this->x = (int)$_POST['x'];

            if (!empty($_POST['y']) AND is_numeric($_POST['y']))
                $this->y = (int)$_POST['y'];

            if (!empty($_POST['lagerort'])) $this->lagerort = (int)$_POST['lagerort'];

            // Überschreiben prüfen
            if (!empty($_POST['akt_ort']))
                $this->akt_ort = $_POST['akt_ort']; else $this->akt_ort = null;

            if (!empty($_POST['kollo']) AND is_numeric($_POST['kollo']))
                $this->kollo = (int)$_POST['kollo'];

            if (!empty($_POST['a_wert']) AND is_numeric($_POST['a_wert']))
                $this->a_wert = (int)$_POST['a_wert'];

            if (!empty($_POST['eigner']) AND is_numeric($_POST['eigner']))
                $this->eigner = (int)$_POST['eigner'];

            if (!empty($_POST['herkunft']) AND is_numeric($_POST['herkunft']))
                $this->herkunft = (int)$_POST['herkunft'];

            if (!empty($_POST['in_date']))
                if(isvalid($_POST['in_date'], DATUM))
                    $this->in_date = $_POST['in_date'];
                else throw new Exception(null, 103);

            if (!empty($_POST['leihbar'])) $this->leihbar = true;
            else $this->leihbar = false;

            // Zuordnung zu Film
            if (!empty($_POST['zu_film'])) $this->zu_film = (int)$_POST['zu_film'];

            if (!empty($_POST['oldsig']) AND !$this->oldsig)
                $this->oldsig = $_POST['oldsig'];
            // Alte Signatur sperren
            if(empty($this->oldsig)) $this->oldsig = 'NIL';

            $this->isvalid = false;
            if(isset($_POST['isvalid'])) :
                if ($_POST['isvalid']) $this->isvalid = true;
            endif;

            if (!empty($_POST['descr'])) $this->descr = $_POST['descr'];
            if (!empty($_POST['rest_report'])) $this->rest_report = $_POST['rest_report'];
            if (!empty($_POST['notiz'])) $this->notiz = $_POST['notiz'];
            $this->editfrom = $myauth->getAuthData('uid');
            $this->editdate = date('c', $_SERVER['REQUEST_TIME']);

            if (empty($this->x) OR empty($this->y)) throw new Exception(null, 111);
        }

        catch (Exception $e) {
            fehler($e->getCode());
        }
    }

    final public function getOSig() {
    /****************************************************************
    *  Aufgabe: liefert die alte Signatur zurück, wenn nicht 'NIL' gesetzt ist
    *   Return: string
    ****************************************************************/
        if($this->oldsig !== 'NIL' OR !empty($this->oldsig))
            return $this->oldsig;
    }

    final protected function ifDouble() {
    /****************************************************************
    *  Aufgabe: Ermitteln gleich
    *   Return: int (ID des letzten Datensatzes | null )
    ****************************************************************/
/**
        global $db;
        $data = $db->extended->getRow(self::SQL_ifDouble, null, $this->titel);
        IsDbError($data);
        return $data['id'];
**/
    }

    final public function del() {
    /****************************************************************
    *  Aufgabe: Setzt "NUR" das Löschflag für den Datensatz in der DB
    *   Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), DELE)) return 2;

        IsDbError($db->extended->autoExecute(
            'i_main', array('del' => true), MDB2_AUTOQUERY_UPDATE,
            'id = '.$db->quote($this->id, 'integer'), 'boolean'));
    }

    final protected function isDel() {
    /****************************************************************
    *  Aufgabe: Testet ob die Löschflagge gesetzt ist
    *   Return: bool
    ****************************************************************/
        global $db;
        if(empty($this->id)) return;
        $data = $db->extended->getOne(
            self::SQL_isDel, 'boolean', $this->id, 'integer');
        IsDbError($data);
        return $data;
    }

    final public static function is_Del($nr) {
    /****************************************************************
    *  Aufgabe: Testet ob Löschflag für Eintrag $nr gesetzt ist
    *   Aufruf: int $nr
    *   Return: bool
    ****************************************************************/
        global $db;
        $data = $db->extended->getOne(
            self::SQL_isDel, 'boolean', $nr, 'integer');
        IsDbError($data);
        return $data;
    }

    final protected function isLinked() {
    /****************************************************************
    *  Aufgabe: Prüft ob der Datensatz verknüpft ist
    *   Return: int $Anzahl
    ****************************************************************/
        global $db;
        // Prüfkandidaten:  ...?
        $data = $db->extended->getOne(self::SQL_isLink, null, $this->id);
        IsDbError($data);
        return $data;
    }

    final public function isVal() {
    /****************************************************************
    *  Aufgabe: Testet ob der Datensatz einer Überarbeitung bedarf (a la Wiki)
    *   Return: bool
    ****************************************************************/
        global $db;
        $data = $db->extended->getOne(
            self::SQL_isVal, 'boolean', $this->id, 'integer');
        IsDbError($data);
        return $data;
    }

    public static function search($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in .......
    *   Param:  string
    *   Return: Array der gefunden ID's | Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $s = "%".$s."%";

        // Suche in Bezeichner (rudimentär)
        $data = $db->extended->getCol(self::SQL_search1, null, $s);
        IsDbError($data);
        $erg = $data;
        if ($erg) return array_unique($erg); else return 1;
    }

    protected function VWert() {
    /****************************************************************
    *  Aufgabe: Errechnet den Versicherungswert eines Gegenstandes
    *           Ausgangswert * (quotient) hoch Jahre = Zeitwert
    *           Anschliessend auf volle 10 € aufrunden.
    *   Return: Integer
    ****************************************************************/
        $begin = new DateTime($this->in_date);
        $interval = $begin->diff(new DateTime("now"));
        $jahre =(int) $interval->format('%y');

        $VWert = $this->a_wert * pow((WERT_QUOT+1), $jahre);
        return round($VWert,-1);
    }

    protected function view() {
    /****************************************************************
    *  Aufgabe: Prototyp der Ausgabefunktion
    *   Return: array mit Daten
    ****************************************************************/
        $data = a_display(self::ea_struct('view'));

        if ($data['leihbar'][1]) $data['leihbar'][1] = d_feld::getString(474);
        else $data['leihbar'][1] = d_feld::getString(485);
        return $data;
    }
}


/** ==========================================================================
                                PLANAR CLASS
========================================================================== **/
final class Planar extends Item implements iPlanar {

    protected
        $art    = 460;      // Dokument, Plakat oder was auch immer
                            // Vorgabe: 460 = Dokument

    const
        SQL_get = 'SELECT art FROM ONLY i_planar WHERE id = ?;';

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe: Aufruf der Elternklasse zur Initialisierung und Ergänzung
    *           um eigene Felder
    *   Return: void
    ****************************************************************/
        parent::get($nr);
        global $db;

        $data = $db->extended->getOne(self::SQL_get, 'integer', $nr, 'integer');
        IsDbError($data);
        if (empty($data)) fehler(4);   // kein Datensatz vorhanden

        // Ergebnis -> Objekt schreiben
        $this->art = $data;
    }

    protected static function getArtLi() {
    /****************************************************************
    *  Aufgabe: Liefert eine Array der Arten (dok/plakat etc)
    *   Return: array()
    ****************************************************************/
        global $db;
        $list = $db->extended->getCol('SELECT id FROM i_planar_art;', 'integer');
        IsDbError($list);
        $data = array();
        foreach($list as $wert) :
            $data[$wert] = d_feld::getString($wert);
        endforeach;
        asort($data);
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
            $db->beginTransaction('new2Ditem'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('id_seq');");
            IsDbError($data);
            $this->id = $data;
            $this->edit($stat);
       else :
            // Objekt wurde vom Eventhandler initiiert
            $this->edit(true);
            // Typ wird autom. generiert
            foreach($this as $key => $wert) $data[$key] = $wert;
            unset($data['types']);

            $types = $this->types;
            array_unshift($types, 'integer');

            $erg = $db->extended->autoExecute('i_planar', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);

            $db->commit('new2Ditem'); IsDbError($db);
            // ende Transaktion

        endif;
    }

    public function edit($stat) {
    /****************************************************************
    *   Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
    *   Return: none
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if($stat == false) :        // Formular anzeigen
            $data = a_display(self::ea_struct('edit'));
            $a = new d_feld('art', $this->art, EDIT, 4030);
            $data['art'] = $a->display();
            $a = new d_feld('artLi', self::getArtLi());
            $data['artLi'] = $a->display();
            $smarty->assign('dialog', $data);
            $smarty->display('item_planar_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        else :                         // Formular auswerten
            // Obj zurückspeichern wird im aufrufenden Teil erledigt
            parent::edit(true);
            $this->art = (int)$_POST['art'];

            // doppelten Datensatz abfangen
            $number = self::ifDouble();
            if (!empty($number) AND $number != $this->id) warng('10008');
        endif;
    }

    public function set() {
    /****************************************************************
    *   Aufgabe: schreibt die Daten in die Tabelle zurück (UPDATE)
    *    Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if(!$this->id) return 4;         // Abbruch: leerer Datensatz

        // Uhrzeit und User setzen

        foreach($this as $key => $wert) $data[$key] = $wert;
        unset($data['types']);
        $types = $this->types;
        array_unshift($types, 'integer');

        $erg = $db->extended->autoExecute('i_planar', $data,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
        IsDbError($erg);
    }

    public function view() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes (an smarty)
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if($this->isDel()) return;          // nichts ausgeben, da gelöscht
        $data = parent::view();
        $a = new d_feld('art',   d_feld::getString($this->art),   VIEW, 483);
        $data['art'] = $a->display();

        $smarty->assign('dialog', $data);
        $smarty->display('item_planar_dat.tpl');
    }
}
?>