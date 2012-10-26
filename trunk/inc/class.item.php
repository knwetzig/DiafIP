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
        SQL_isdel   = 'SELECT del FROM i_item WHERE id = ?;';

    public function del();
    public static function is_Del($nr);
    public static function search($s);
}

interface iexTyp extends iItem {
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
            __construct(?int)
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
        $lagerort       = null,
        $bezeichn       = null,
        $eigner         = null, // -> Person
        $leihbar        = false,
        $x              = null, // in mm
        $y              = null,
        $kollo          = 1,
        $akt_ort        = null, // aktueller Aufenthaltsort des Gegenstandes....
        $wert_idx       = null, // Index * Konst * Zeit = Versicherungswert
        $oldsig         = null, // alte Signatur
        $herkunft       = null, // -> Person
        $in_date        = null, // Zugangsdatum...
        $descr          = null, // Beschreibung des Gegenstandes
        $rest_report    = null, // Restaurierungsbericht
        $obj_typ        = 1;

    const
        // für protected Funktionen
//        SQL_ifDouble = 'SELECT id FROM f_main WHERE titel = ? AND del = false;',
        SQL_get     = 'SELECT * FROM i_item WHERE id = ?;';
//        SQL_isLink  = 'SELECT COUNT(*) FROM f_cast WHERE fid = ?';

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe: Initialisiert das Objekt (auch gelöschte)
    *   Return: void
    ****************************************************************/
        global $db;
/**
        $types      = array(
            'integer',  // id
            'boolean',  // del          true wenn gelöscht
            'integer',  // editfrom
            'date',     // editdate
            'integer',  // bild_id
            'text',     // notiz        (intern)
        );
**/
        $data = $db->extended->getRow(self::SQL_get, null/*$types*/, $nr, 'integer');
        IsDbError($data);
        if (empty($data)) :   // kein Datensatz vorhanden
            fehler(4);
            exit;
        endif;
_v($data);
/**
        // Ergebnis -> Objekt schreiben
        foreach($data as $key => $wert) :
            $this->$key = $wert;
        endforeach;
**/
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
            'i_objekt', array('del' => true), MDB2_AUTOQUERY_UPDATE,
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
    *  Aufgabe: Testet ob Löschflag für Eintrag $nr gesetzt ist
    *   Aufruf: int $nr
    *   Return: bool
    ****************************************************************/
        global $db;
        $data = $db->extended->getRow(
            self::SQL_isDel, 'boolean', $nr, 'integer');
        IsDbError($data);
        return $data['del'];
    }

    final protected function isLinked() {
    /****************************************************************
    *  Aufgabe: Prüft ob der Datensatz verknüpft ist
    *   Return: int $Anzahl
    ****************************************************************/
        global $db;
        // Prüfkandidaten:  ...?
        $data = $db->extended->getRow(self::SQL_isLink, null, $this->id);
        IsDbError($data);
        return $data['count'];
    }

    public static function search($s) {
    /****************************************************************
    *  Aufgabe: Suchfunktion in allen Titelspalten
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
}// ende Obj KLASSE



/** ==========================================================================
                                ?FIGUREN CLASS
========================================================================== **/
final class exItem extends Item implements iexTyp {

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
            new d_feld('bild_id',   $this->bild_id),
            new d_feld('notiz',     changetext($this->notiz),   EDIT, 514),
            new d_feld('edit',      null, EDIT, null, 4013), // edit-Button
            new d_feld('del',       null, DELE, null, 4020), // Lösch-Button
            new d_feld('chdatum',   $this->editdate),
            new d_feld('chname',    $bearbeiter[0]),
        ));
        $smarty->assign('dialog', $data);
    }
} // endclass


?>