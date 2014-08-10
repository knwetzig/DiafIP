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
}

interface iPlanar extends iItem { // wird auch von 3D-Obj genutzt
    public function add($stat);
    public function set();
    public static function search($s);
    public function sview();
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
        $akt_ort        = null,     // aktueller Aufenthaltsort des Gegenstandes
        $a_wert         = 10,       // Anschaffungswert zur Zeit des Zugangs
        $oldsig         = null,     // alte Signatur
        $herkunft       = 1,        // -> DEFA Studio für Trickfilme
        $in_date        = '1993-11-16', // Zugangsdatum...
        $descr          = null,     // Beschreibung des Gegenstandes
        $rest_report    = null,     // Restaurierungsbericht
        $bild_id        = '{}',
        $del            = false,
        $lagerort       = 67,       // -> lagerort unbestimmt
        $bezeichner     = null,
        $editfrom       = null,
        $editdate       = null,
        $zu_film        = null,     // Verweis auf bibl./filmogr. Daten
        $isvalid        = false,    // Bearbeitung abgeschlossen

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
        $db = MDB2::singleton();

        switch ($set) :
          case 'edit' :
            $data = array(
                new d_feld('persLi', Person::getPersonLi()),    // Personenliste
                new d_feld('lortLi', LOrt::getLOrtList()),        // Liste Lagerorte
                new d_feld('filmLi', Film::getTitelList()),     // Liste filmogr.
                new d_feld('bezeichner', $this->bezeichner, EDIT, 4029),
                new d_feld('x',         $this->x,           EDIT,  469, null, ANZAHL),
                new d_feld('y',         $this->y,           EDIT,  470, null, ANZAHL),
                new d_feld('kollo',     $this->kollo,       EDIT,  475, null, ANZAHL),
                new d_feld('lagerort',  $this->lagerort,  ARCHIV,  472, null, ANZAHL),
                new d_feld('akt_ort',   $this->akt_ort,     EDIT,  476),
                new d_feld('zu_film',   $this->zu_film,     EDIT,    5, null, ANZAHL),
                new d_feld('eigner',    $this->eigner,     IEDIT,  473, null, ANZAHL),
                new d_feld('herkunft',  $this->herkunft,   IEDIT,  480, null, ANZAHL),
                new d_feld('in_date',   $this->in_date,    IEDIT,  481, null, DATUM),
                new d_feld('leihbar',   $this->leihbar,   ARCHIV,  474, null, BOOL),
                new d_feld('a_wert',    $this->a_wert,     IEDIT, 4031, null, DZAHL),
                new d_feld('rest_report',$this->rest_report, EDIT, 482),
                new d_feld('descr',     $this->descr,       EDIT,  506),
                new d_feld('notiz',     $this->notiz,       EDIT,  514),
                new d_feld('isvalid', null /* $this->isvalid*/,   ARCHIV,10010)
            );
            if (empty($this->oldsig))
                        $data[] = new d_feld('oldsig', null, EDIT, 479, NAMEN);
            break;

          case 'view' :
            if (!empty($this->editfrom)) :
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
                new d_feld('leihbar',   $this->leihbar,     IVIEW, 474),
                new d_feld('x',         $this->x,           VIEW, 469),
                new d_feld('y',         $this->y,           VIEW, 470),
                new d_feld('kollo',     $this->kollo,       IVIEW, 475),
                new d_feld('zu_film',   Film::getTitel($this->zu_film), VIEW, 5),
                new d_feld('akt_ort',   $this->akt_ort,     IVIEW, 476),
                new d_feld('vers_wert', $this->VWert(),     IVIEW, 477),
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
        $db = MDB2::singleton();
        $data = $db->extended->getRow(self::SQL_get, $this->types, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) feedback(4, 'hinw');    // kein Datensatz vorhanden

        // Ergebnis -> Objekt schreiben
        foreach ($data as $key => $wert) $this->$key = $wert;
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
            if (!empty($_POST['x'])) $this->x =  intval($_POST['x']);
            if (!empty($_POST['y'])) $this->y = intval($_POST['y']);
            if (!empty($_POST['lagerort'])) $this->lagerort = intval($_POST['lagerort']);

            // Überschreiben prüfen
            if (!empty($_POST['akt_ort']))
                $this->akt_ort = $_POST['akt_ort']; else $this->akt_ort = null;

            if (!empty($_POST['kollo'])) $this->kollo = intval($_POST['kollo']);
            if (!empty($_POST['a_wert'])) $this->a_wert = intval($_POST['a_wert']);
            if (!empty($_POST['eigner'])) $this->eigner = intval($_POST['eigner']);
            if (!empty($_POST['herkunft'])) $this->herkunft = intval($_POST['herkunft']);

            if (!empty($_POST['in_date']))
                if (isvalid($_POST['in_date'], DATUM))
                    $this->in_date = $_POST['in_date'];
                else throw new Exception(null, 103);

            if (!empty($_POST['leihbar'])) $this->leihbar = true;
                else $this->leihbar = false;

            // Zuordnung zu Film
            if (!empty($_POST['zu_film'])) $this->zu_film = intval($_POST['zu_film']);

            if (!empty($_POST['oldsig']) AND !$this->oldsig)
                $this->oldsig = $_POST['oldsig'];
            // Alte Signatur sperren
            if (empty($this->oldsig)) $this->oldsig = 'NIL';

            $this->isvalid = false;
            if (isset($_POST['isvalid'])) :
                if ($_POST['isvalid']) $this->isvalid = true;
            endif;

            if (!empty($_POST['descr'])) $this->descr = $_POST['descr'];
            if (!empty($_POST['rest_report'])) $this->rest_report = $_POST['rest_report'];
            if (!empty($_POST['notiz'])) $this->notiz = $_POST['notiz'];
            $this->editfrom = $myauth->getAuthData('uid');
            $this->editdate = date('c', $_SERVER['REQUEST_TIME']);

            /* Manchmal gibt es einfach keine Maße, wie bei Filmen, die aber
               diese Routine nutzen. deswegen ausgeklammert
            if (empty($this->x) OR empty($this->y)) throw new Exception(null, 111);
            */
        }

        catch (Exception $e) {
            feedback($e->getCode(), 'error');
        }
    }

    final public function getOSig() {
    /****************************************************************
    *  Aufgabe: liefert die alte Signatur zurück, wenn nicht 'NIL' gesetzt ist
    *   Return: string
    ****************************************************************/
        if ($this->oldsig !== 'NIL' OR !empty($this->oldsig))
            return $this->oldsig;
    }

    final protected function ifDouble() {
    /****************************************************************
    *  Aufgabe: Ermitteln gleich
    *   Return: int (ID des letzten Datensatzes | null )
    ****************************************************************/
/**
        $db = MDB2::singleton();
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
        global $myauth;
        $db = MDB2::singleton();

        if (!isBit($myauth->getAuthData('rechte'), DELE)) return 2;

        IsDbError($db->extended->autoExecute(
            'i_main', array('del' => true), MDB2_AUTOQUERY_UPDATE,
            'id = '.$db->quote($this->id, 'integer'), 'boolean'));
    }

    final protected function isDel() {
    /****************************************************************
    *  Aufgabe: Testet ob die Löschflagge gesetzt ist
    *   Return: bool
    ****************************************************************/
        $db = MDB2::singleton();
        if (empty($this->id)) return;
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
        $db = MDB2::singleton();
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
        $db = MDB2::singleton();
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
        $db = MDB2::singleton();
        $data = $db->extended->getOne(
            self::SQL_isVal, 'boolean', $this->id, 'integer');
        IsDbError($data);
        return $data;
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
        $jahre = intval($interval->format('%y'));

        $VWert = $this->a_wert * pow((WERT_QUOT+1), $jahre);
        return round($VWert,-1);
    }

    protected function view() {
    /****************************************************************
    *  Aufgabe: Prototyp der Ausgabefunktion
    *   Return: array mit Daten
    ****************************************************************/
        $data = a_display(self::ea_struct('view'));

        if (!isset($data['leihbar'])) return $data;

        // Umwandlung des boolschen Wertes in einen String - wenn sichtbar
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
        $db = MDB2::singleton();

        $data = $db->extended->getOne(self::SQL_get, 'integer', $nr, 'integer');
        IsDbError($data);
        if (empty($data)) feedback(4 ,'hinw');   // kein Datensatz vorhanden

        // Ergebnis -> Objekt schreiben
        $this->art = $data;
    }

    protected static function getArtLi() {
    /****************************************************************
    *  Aufgabe: Liefert eine Array der Arten (dok/plakat etc)
    *   Return: array()
    ****************************************************************/
        $db = MDB2::singleton();
        $list = $db->extended->getCol('SELECT id FROM i_planar_art;', 'integer');
        IsDbError($list);
        $data = array();
        foreach ($list as $wert) :
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
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db = MDB2::singleton();
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
            foreach ($this as $key => $wert) $data[$key] = $wert;
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
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if ($stat == false) :        // Formular anzeigen
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
            $this->art = intval($_POST['art']);

            // doppelten Datensatz abfangen
            $number = self::ifDouble();
            if (!empty($number) AND $number != $this->id) feedback(10008, 'warng');
        endif;
    }

    public function set() {
    /****************************************************************
    *   Aufgabe: schreibt die Daten in die Tabelle zurück (UPDATE)
    *    Return: Fehlercode
    ****************************************************************/
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->id) return 4;         // Abbruch: leerer Datensatz

        // Uhrzeit und User setzen

        foreach ($this as $key => $wert) $data[$key] = $wert;
        unset($data['types']);
        $types = $this->types;
        array_unshift($types, 'integer');

        $db = MDB2::singleton();
        $erg = $db->extended->autoExecute('i_planar', $data,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
        IsDbError($erg);
    }

    public static function search($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in .......
    *   Param:  string
    *   Return: Array der gefunden ID's | Fehlercode
    ****************************************************************/
        global $myauth;
        $db = MDB2::singleton();
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        $s = "%".$s."%";

        // Suche in Bezeichner (rudimentär)
        $data = $db->extended->getCol(
            'SELECT id FROM ONLY i_planar
            WHERE (bezeichner ILIKE ?) AND (del != TRUE);', null, $s);
        IsDbError($data);
        $erg = $data;
        if ($erg) return array_unique($erg); else return 1;
    }

    public function sview() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes (Listenansicht)
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if ($this->isDel()) return;          // nichts ausgeben, da gelöscht
        $data = parent::view();
        $a = new d_feld('art',   d_feld::getString($this->art),   VIEW, 483);
        $data['art'] = $a->display();
        $a = new d_feld('masze',   $this->x.'x'.$this->y,   VIEW, 484);
        $data['masze'] = $a->display();
        $smarty->assign('dialog', $data);
        $smarty->display('item_planar_ldat.tpl');
    }

    public function view() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes (an smarty)
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if ($this->isDel()) return;          // nichts ausgeben, da gelöscht
        $data = parent::view();
        $a = new d_feld('art',   d_feld::getString($this->art),   VIEW, 483);
        $data['art'] = $a->display();

        $smarty->assign('dialog', $data);
        $smarty->display('item_planar_dat.tpl');
    }
}



/** ==========================================================================
                                3D-Objekt CLASS
========================================================================== **/
final class Obj3d extends Item implements iPlanar {

    protected
        $art    = 585,      // Puppen, Requisiten, Preise
                            // Vorgabe: 585 = Animationspuppe
        $z      = null;     // Oh Wunder, da taucht die 3. Dimension auf ;-))

    const
        SQL_get = 'SELECT art, z FROM ONLY i_3dobj WHERE id = ?;';

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
        $db = MDB2::singleton();
        $data = $db->extended->getRow(self::SQL_get, 'integer', $nr, 'integer');
        IsDbError($data);
        if (empty($data)) feedback(4 ,'hinw');   // kein Datensatz vorhanden

        // Ergebnis -> Objekt schreiben
        $this->art = $data['art'];
        $this->z = $data['z'];
    }

    protected static function getArtLi() {
    /****************************************************************
    *  Aufgabe: Liefert eine Array der Arten
    *   Return: array()
    ****************************************************************/
        $db = MDB2::singleton();
        $list = $db->extended->getCol('SELECT id FROM i_3dobj_art;', 'integer');
        IsDbError($list);
        $data = array();
        foreach ($list as $wert) :
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
        global $myauth;
        $db = MDB2::singleton();
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if ($stat == false) :
            $db->beginTransaction('new3Ditem'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('id_seq');");
            IsDbError($data);
            $this->id = $data;
            $this->edit($stat);
       else :
            // Objekt wurde vom Eventhandler initiiert
            $this->edit(true);
            foreach ($this as $key => $wert) $data[$key] = $wert;
            unset($data['types']);

            $types = $this->types;
            array_unshift($types, 'integer','integer');
            $erg = $db->extended->autoExecute('i_3dobj', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);

            $db->commit('new3Ditem'); IsDbError($db);
            // ende Transaktion
        endif;
    }

    public function edit($stat) {
    /****************************************************************
    *   Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
    *   Return: none
    ****************************************************************/
        global $myauth, $smarty;
        $db = MDB2::singleton();
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if ($stat == false) :        // Formular anzeigen
            $data = a_display(self::ea_struct('edit'));

            $a = new d_feld('artLi', self::getArtLi());
            $data['artLi'] = $a->display();
            $a = new d_feld('art', $this->art, EDIT, 4030);
            $data['art'] = $a->display();

            $a = new d_feld('z', $this->z, EDIT, 471, ANZAHL);
            $data['z'] = $a->display();
            $smarty->assign('dialog', $data);
            $smarty->display('item_3dobj_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        else :                         // Formular auswerten
            // Obj zurückspeichern wird im aufrufenden Teil erledigt
            parent::edit(true);
            $this->art = intval($_POST['art']);
            if (!empty($_POST['z'])) $this->z = intval($_POST['z']);

            // doppelten Datensatz abfangen
            $number = self::ifDouble();
            if (!empty($number) AND $number != $this->id) feedback(10008, 'warng');
        endif;
    }

    public function set() {
    /****************************************************************
    *   Aufgabe: schreibt die Daten in die Tabelle zurück (UPDATE)
    *    Return: Fehlercode
    ****************************************************************/
        global $myauth;
        $db = MDB2::singleton();
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->id) return 4;         // Abbruch: leerer Datensatz

        // Uhrzeit und User setzen

        foreach ($this as $key => $wert) $data[$key] = $wert;
        unset($data['types']);
        $types = $this->types;
        array_unshift($types, 'integer', 'integer');
        $erg = $db->extended->autoExecute('i_3dobj', $data,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
        IsDbError($erg);
    }

    public static function search($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in .......
    *   Param:  string
    *   Return: Array der gefunden ID's | Fehlercode
    ****************************************************************/
        global $myauth;
        $db = MDB2::singleton();
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $s = "%".$s."%";

        // Suche in Bezeichner (rudimentär)
        $data = $db->extended->getCol(
            'SELECT id FROM ONLY i_3dobj
            WHERE (bezeichner ILIKE ?) AND (del != TRUE);', null, $s);
        IsDbError($data);
        $erg = $data;
        if ($erg) return array_unique($erg); else return 1;
    }

    public function sview() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes an smarty
    *            Listenansicht
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if ($this->isDel()) return;          // nichts ausgeben, da gelöscht

        $data = parent::view();
        $a = new d_feld('art',   d_feld::getString($this->art),   VIEW, 483);
        $data['art'] = $a->display();
        $a = new d_feld('masze',   $this->x.'x'.$this->y.'x'.$this->z,   VIEW, 484);
        $data['masze'] = $a->display();
        $smarty->assign('dialog', $data);
        $smarty->display('item_3dobj_ldat.tpl');
    }

    public function view() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes an smarty
    *            Detailansicht
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if ($this->isDel()) return;          // nichts ausgeben, da gelöscht

        $data = parent::view();
        $a = new d_feld('art',   d_feld::getString($this->art),   VIEW, 483);
        $data['art'] = $a->display();
        $a = new d_feld('z',   $this->z,   VIEW, 471);
        $data['z'] = $a->display();
        $smarty->assign('dialog', $data);
        $smarty->display('item_3dobj_dat.tpl');
    }
}


/** ==========================================================================
                                Filmkopie CLASS
========================================================================== **/
final class FilmKopie extends Item implements iPlanar {

    protected
        $medium     = 10,   // Film, DVD etc  DB->i_medium
                            // Vorgabe: 10 = 35mm Film
        $material   = 5,    // Trägermaterial DB->i_material
        $tonart     = 5,    // Tonverfahren DB->i_tonart, Vorgabe LT-Mono
        $fps        = 24,   // Frames/s (Vorführfrequenz)
        $laufzeit   = null; // Laufzeit bei angegebner fps in sekunden

    const
        SQL_get = 'SELECT medium, material, tonart, fps, laufzeit
                      FROM ONLY i_fkop WHERE id = ?;';

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
        $db = MDB2::singleton();
        $data = $db->extended->getRow(self::SQL_get, 'integer', $nr, 'integer');
        IsDbError($data);
        if (empty($data)) feedback(4 ,'hinw');   // kein Datensatz vorhanden

        // Ergebnis -> Objekt schreiben
        $this->medium     = $data['medium'];
        $this->material   = $data['material'];
        $this->tonart     = $data['tonart'];
        $this->fps        = $data['fps'];
        $this->laufzeit   = $data['laufzeit'];

        // Anpassung Typen
        array_unshift($this->types,
            'integer', 'integer', 'integer', 'integer', 'date');
    }

    protected function getMedium($nr) {
        //DB->i_medium
        $db = MDB2::singleton();
        $data = $db->extended->getOne('
            SELECT medium FROM i_medium WHERE id = ?;', 'text', $nr);
        IsDbError($data);
        return $data;
    }

    protected function getMediumLi() {
        $db = MDB2::singleton();
        $erg = $db->extended->getAll('
            SELECT * FROM i_medium ORDER BY id;');
        IsDbError($erg);

        $data = array();
        foreach ($erg as $arr) $data[$arr['id']] = $arr['medium'];
        return $data;
    }

    protected function getMaterial($nr) {
        $db = MDB2::singleton();
        $data = $db->extended->getOne('
            SELECT material FROM i_material WHERE id = ?;', 'text', $nr);
        IsDbError($data);
        return $data;
    }

    protected function getMaterialLi() {
        $db = MDB2::singleton();
        $erg = $db->extended->getAll('
            SELECT * FROM i_material ORDER BY id;');
        IsDbError($erg);

        $data = array();
        foreach ($erg as $arr) $data[$arr['id']] = $arr['material'];
        return $data;
    }

    protected function getTonart($nr) {
        //DB->i_tonart
        $db = MDB2::singleton();
        $data = $db->extended->getOne('
            SELECT audiotyp FROM i_tonart WHERE id = ?;', 'text', $nr);
        IsDbError($data);
        return $data;
    }

    protected function getTonartLi() {
        $db = MDB2::singleton();
        $erg = $db->extended->getAll('
            SELECT * FROM i_tonart ORDER BY id;');
        IsDbError($erg);

        $data = array();
        foreach ($erg as $arr) $data[$arr['id']] = $arr['audiotyp'];
        return $data;
    }

    public function add($stat) {
    /****************************************************************
    *   Aufgabe: Legt leeren Datensatz an (INSERT)
    *   Aufruf:  Status
    *   Return:  Fehlercode
    ****************************************************************/
        global $myauth;
        $db = MDB2::singleton();
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if ($stat == false) :
            $db->beginTransaction('newFKop'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('id_seq');");
            IsDbError($data);
            $this->id = $data;
            $this->edit($stat);
       else :
            // Objekt wurde vom Eventhandler initiiert
            $this->edit(true);

            foreach ($this as $key => $wert) $data[$key] = $wert;
            unset($data['types']);

            $erg = $db->extended->autoExecute(
                'i_fkop', $data, MDB2_AUTOQUERY_INSERT);
            IsDbError($erg);

            $db->commit('newFKop'); IsDbError($db);
            // ende Transaktion
        endif;
    }

    public function edit($stat) {
    /****************************************************************
    *   Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
    *   Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        $db = MDB2::singleton();

        if ($stat == false) :        // Formular anzeigen
            $data = a_display(self::ea_struct('edit'));

            $a = new d_feld('mediumLi', self::getMediumLi());
            $data['mediumLi'] = $a->display();
            $a = new d_feld('medium', $this->medium, EDIT, 490, ANZAHL);
            $data['medium'] = $a->display();

            $a = new d_feld('materialLi', self::getMaterialLi());
            $data['materialLi'] = $a->display();
            $a = new d_feld('material', $this->material, EDIT, 488, ANZAHL);
            $data['material'] = $a->display();

            $a = new d_feld('tonartLi', self::getTonartLi());
            $data['tonartLi'] = $a->display();
            $a = new d_feld('tonart', $this->tonart, EDIT, 491, ANZAHL);
            $data['tonart'] = $a->display();

            $a = new d_feld('fps', $this->fps, EDIT, 489, ANZAHL);
            $data['fps'] = $a->display();

            $a = new d_feld('lzeit', $this->laufzeit, EDIT, 580, 10007, DAUER);
            $data['lzeit'] = $a->display();

            $smarty->assign('dialog', $data);
            $smarty->display('item_fkop_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        else :
            // Formular auswerten
            // Obj zurückspeichern wird im aufrufenden Teil erledigt
            parent::edit(true);  // Zeile 195

            try {
                $this->medium   = intval($_POST['medium']);
                $this->material = intval($_POST['material']);
                $this->tonart   = intval($_POST['tonart']);

                if (!empty($_POST['fps']))
                    $this->fps = intval($_POST['fps']); else $this->fps = null;

                if (isset($_POST['lzeit'])) :
                    if ($_POST['lzeit']) {
                        if (isvalid($_POST['lzeit'], DAUER))
                            $this->laufzeit = $_POST['lzeit'];
                        else throw new Exception(null, 4);
                    } else $this->laufzeit = null;
                endif;

                // doppelten Datensatz abfangen
                $number = self::ifDouble();
                if (!empty($number) AND $number != $this->id) feedback(10008, 'warng');
            }

            catch (Exception $e) {
                feedback($e->getCode(), 'error');
            }
        endif;
    }

    public function set() {
    /****************************************************************
    *   Aufgabe: schreibt die Daten in die Tabelle zurück (UPDATE)
    *    Return: Fehlercode
    ****************************************************************/
        global $myauth;
        $db = MDB2::singleton();
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->id) return 4;         // Abbruch: leerer Datensatz

        // Uhrzeit und User setzen

        $data = array();
        foreach ($this as $key => $wert) $data[$key] = $wert;
        unset($data['types']);

        $erg = $db->extended->autoExecute(
            'i_fkop', $data, MDB2_AUTOQUERY_UPDATE,
            'id = '.$db->quote($this->id, 'integer'), $this->types);
        IsDbError($erg);
    }

    public static function search($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in .......
    *   Param:  string
    *   Return: Array der gefunden ID's | Fehlercode
    ****************************************************************/
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $s = "%".$s."%";
        $db = MDB2::singleton();
        // Suche in Bezeichner (rudimentär)
        $data = $db->extended->getCol(
            'SELECT id FROM ONLY i_fkop
            WHERE (bezeichner ILIKE ?) AND (del != TRUE);', null, $s);
        IsDbError($data);
        $erg = $data;
        if ($erg) return array_unique($erg); else return 1;
    }

    public function sview() {

    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes an smarty
    *            Detailansicht
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if ($this->isDel()) return;          // nichts ausgeben, da gelöscht

        $data = parent::view();
        $a = new d_feld('medium', self::getMedium($this->medium), VIEW, 490);
        $data['medium'] = $a->display();
        $a = new d_feld('laufzeit', $this->laufzeit, VIEW, 580);
        $data['laufzeit'] = $a->display();

        $smarty->assign('dialog', $data);
        $smarty->display('item_fkop_ldat.tpl');
    }

    public function view() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes an smarty
    *            Detailansicht
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if ($this->isDel()) return;          // nichts ausgeben, da gelöscht

        $data = parent::view();
        // Film, DVD etc  DB->i_medium
        $a = new d_feld('medium', self::getMedium($this->medium), VIEW, 490);
        $data['medium'] = $a->display();
        // Trägermaterial DB->i_material
        $a = new d_feld('material', self::getMaterial($this->material),VIEW, 488);
        $data['material'] = $a->display();
        // Tonverfahren DB->i_tonart, Vorgabe LT-Mono
        $a = new d_feld('tonart', self::getTonart($this->tonart), VIEW, 1320);
        $data['tonart'] = $a->display();
        $a = new d_feld('fps', $this->fps, VIEW, 489);
        $data['fps'] = $a->display();
        // Laufzeit bei angegebner fps in sekunden
        $a = new d_feld('laufzeit', $this->laufzeit, VIEW, 580);
        $data['laufzeit'] = $a->display();

        $smarty->assign('dialog', $data);
        $smarty->display('item_fkop_dat.tpl');
    }
} // --- end class fkop
?>