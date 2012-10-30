<?php
/********************************************************************

    Klassenbibliothek für Gegenstände

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. ************************************************/

interface iItem {
    public function del();
    public static function is_Del($nr);
    public static function search($s);
}

interface iPlanar extends iItem {
    public function add($stat);
    public function edit($stat);
    public function set();
    public function view();
}

/** ==========================================================================
                               ITEM CLASS
========================================================================== **/
abstract class Item implements iItem {
/*      interne Methoden:
            __construct($nr = null)
    int     ifDouble()
    void    get(int $nr)
            isDel()
            isLinked()
*/

    protected
        $id             = null,
        $del            = false,
        $editfrom       = null,
        $editdate       = null,
        $bild_id        = array(),
        $notiz          = null,
        $lagerort       = null, // -> lagerort
        $bezeichner     = null,
        $eigner         = null, // -> Person
        $leihbar        = false,
        $x              = null, // in mm (max 32757)
        $y              = null,
        $kollo          = 1,
        $akt_ort        = null, // aktueller Aufenthaltsort des Gegenstandes....
        $a_wert         = null, // Index * Konst * Zeit = Versicherungswert
        $oldsig         = null, // alte Signatur
        $herkunft       = null, // -> Person
        $in_date        = null, // Zugangsdatum...
        $descr          = null, // Beschreibung des Gegenstandes
        $rest_report    = null, // Restaurierungsbericht
        $obj_typ        = 1;

    const
        // für protected Funktionen
//        SQL_ifDouble = 'SELECT id FROM i_main WHERE oldsig = ? AND del = false;',
        SQL_get     = 'SELECT * FROM i_main WHERE id = ?;',
        SQL_getLOrt = 'SELECT lagerort FROM i_lagerort WHERE id = ?;',
        SQL_isDel   = 'SELECT del FROM i_main WHERE id = ?;';
//        SQL_isLink  = 'SELECT COUNT(*) FROM ____ WHERE fid = ?';

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe: Initialisiert das Objekt (auch gelöschte)
    *   Return: void
    ****************************************************************/
        global $db;
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

    final protected function getLOrt() {
    /****************************************************************
    *  Aufgabe: liefert den Texteintrag zum Lagerort
    *   Return: string
    ****************************************************************/
        global $db;
        if(empty($this->lagerort)) return;
        $data = $db->extended->getOne(
            self::SQL_getLOrt, 'text', $this->lagerort, 'integer');
        IsDbError($data);
        return $data;
    }

    final protected function ifDouble() {
    /****************************************************************
    *  Aufgabe: Ermitteln gleichlautender Titel
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

    public static function search($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in .......
    *   Param:  string
    *   Return: Array der gefunden ID's | Fehlercode
    ****************************************************************/
        global $db, $myauth;
/**
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
**/
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
    *   Param:  string
    *   Return: Array der gefunden ID's | Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!empty($this->editfrom)) :
            $bearbeiter = $db->extended->getOne(
                'SELECT realname FROM s_auth WHERE uid = '.$this->editfrom.';');
            IsDbError($bearbeiter);
        else : $bearbeiter = null;
        endif;
        $besitzer = new Person($this->eigner);
        $vbesitz  = new Person($this->herkunft);

        $data = a_display(array( // name, inhalt, opt -> rechte, label,tooltip
            new d_feld('id',        $this->id),
//          new d_feld('bild_id',   $this->bild_id),
            new d_feld('notiz',     changetext($this->notiz),   EDIT, 514),
            new d_feld('edit',      null, EDIT, null, 4013), // edit-Button
            new d_feld('del',       null, DELE, null, 4020), // Lösch-Button
            new d_feld('chdatum',   $this->editdate,    VIEW),
            new d_feld('chname',    $bearbeiter,       IVIEW),
            new d_feld('bezeichner', $this->bezeichner, VIEW),
            new d_feld('lagerort',  $this->getLOrt(),  IVIEW, 472),
            new d_feld('eigner',    $besitzer->getName(), IVIEW, 473),
            new d_feld('leihbar',   $this->leihbar,     VIEW, 474),
            new d_feld('x',         $this->x,           VIEW, 469),
            new d_feld('y',         $this->y,           VIEW, 470),
            new d_feld('kollo',     $this->kollo,      IVIEW, 475),
            new d_feld('akt_ort',   $this->akt_ort,    IVIEW, 476),
            new d_feld('vers_wert', $this->VWert(),     VIEW, 477),
            new d_feld('oldsig',    $this->oldsig,     IVIEW, 479),
            new d_feld('herkunft',  $vbesitz->getName(), IVIEW, 480),
            new d_feld('in_date',   $this->in_date,    IVIEW, 481),
            new d_feld('descr',     changetext($this->descr), VIEW, 506),
            new d_feld('rest_report', changetext($this->rest_report), IVIEW, 482),
        ));
        return $data;
    }
}


/** ==========================================================================
                                PLANAR CLASS
========================================================================== **/
final class Planar extends Item implements iPlanar {

    protected
        $art    = null;         // Dokument, Plakat oder was auch immer

    const
        SQL_get = 'SELECT art FROM ONLY i_planar WHERE id = ?;';

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
        $types = array('integer'
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

    public function add($stat) {
    /****************************************************************
    *   Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
    *   Aufruf:  Status
    *   Return:  Fehlercode
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if ($stat == false) :
        else :
            // Objekt wurde vom Eventhandler initiiert
            $types = array(
                // ACHTUNG! Reihenfolge beachten !!!
            );

            $this->edit(true);
            // Typ wird autom. generiert
            //    .....
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
            );

            $myauth->setAuthData('obj', serialize($this));
        else :                         // Formular auswerten
            // Obj zurückspeichern wird im aufrufenden Teil erledigt

            // doppelten Datensatz abfangen
            $number = self::ifDouble();
            if (!empty($number) AND $number != $this->id) warng('10008');
        endif;  // Formularbereich
    }

    public function set() {
    /****************************************************************
    *   Aufgabe: schreibt die Daten in die Tabelle '?????' zurück (UPDATE)
    *    Return: Fehlercode
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if(!$this->id) return 4;         // Abbruch: leerer Datensatz
        $types = array(
        // ACHTUNG: Reihenfolge beachten!
        );
        foreach($this as $key => $wert) $data[$key] = $wert;
    }

    public function view() {
    /****************************************************************
    *   Aufgabe: Ausgabe des Objektdatensatzes (an smarty)
    *    Return: none
    ****************************************************************/
        global $myauth, $smarty;
        if($this->isDel()) return;          // nichts ausgeben, da gelöscht
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        $data = parent::view();
        $a = new d_feld('art',   d_feld::getString($this->art),   VIEW, 483);
        $data['art'] = $a->display();

        $smarty->assign('dialog', $data);
        $smarty->display('item_planar_dat.tpl');
    }
}
?>