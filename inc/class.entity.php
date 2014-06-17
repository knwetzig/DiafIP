<?php
/**************************************************************

    Basisbibliothek

$Rev$
$Author$
$Date$
$URL$

ToDo:


**************************************************************/

/** ===========================================================
                                ENTITY
=========================================================== **/
interface iEntity {
    function getBearbeiter();   // ermittelt Realnamen des Bearbeiters
    static function search($s); // liefert die ID's des Suchmusters
    function setValid();        // Set/Unset Flag
    function setDel();          // -- dito--
    function lview();           // array mit Objekten Listenansicht
    function view();            // dito Detailansicht
}

abstract class entity implements iEntity {
    /**********************************************************
    * Interne Methoden:
    *       get($nr)
    *       setSignum()
    **********************************************************/
    const
        GETDATA = 'SELECT * FROM entity WHERE id = ?;';

    protected
        $id         = null,
        $bereich    = '',       // Enthält die Kennung zu welchem Bereich die
                                // Entität gehört....
        $descr      = '',       // Beschreibung bzw. Biografie bei Personen
        $bilder     = array(),
        $notiz      = '',       // selbsterklärend ;-)
        $isvalid    = false,    // Flag zur Kennzeichnung, das dieser Datensatz
                                // abschließend bearbeitet wurde
        $del        = false,    // Löschflag
        $editfrom   = null,     // uid des Bearbeiters
        $editdate   = null;        // bigint,

    function __construct($nr = null) {
            if(isset($nr) AND is_int($nr)) self::get($nr);
    }

    protected function get($nr) {
    // Diese Funktion initialisiert das Objekt
        $db =& MDB2::singleton();
        $data = $db->extended->getRow(self::GETDATA,null,$nr,'integer');
        IsDbError($data);
        if($data) :
            foreach($data as $key => $val) $this->$key = $val;
            if($this->bilder) :
                $this->bilder = preg_split("/[,{}]/", $this-> bilder, null, PREG_SPLIT_NO_EMPTY);
            endif;
        else :
            feedback(4,'warng');
            exit(4);
        endif;
    }

    abstract protected function add($status);
    abstract protected function edit($status);
    abstract static function search($s);

    final function getBearbeiter() {
    /**********************************************************
     * Aufgabe: Ermittelt den Realnamen des Bearbeiters
     *  Return: string | none
     **********************************************************/
        $db =& MDB2::singleton();
        $bearbeiter = null;
        if(!empty($this->editfrom)) :
            $bearbeiter = $db->extended->getCol(
                'SELECT realname FROM s_auth WHERE uid = '.$this->editfrom.';');
            IsDbError($bearbeiter);
        endif;
        if($bearbeiter) return $bearbeiter[0];
    }

    final protected function setSignum() {
    /**********************************************************
     * Aufgabe: Setzt Bearbeiter und Uhrzeit der Bearbeitung
     *  Return: none
     **********************************************************/
        global $myauth;
            $this->editfrom = $myauth->getAuthData('uid');
            $this->editdate = $_SERVER['REQUEST_TIME'];
    }

    function setValid() {
    /**********************************************************
     * Aufgabe: Bearbeitungsflag setzen (Kippschalter)
     *  Return: none
     **********************************************************/
        if($this->isValid) $this->isValid = false; else $this->isValid = true;
    }

    function setDel() {
    /**********************************************************
     * Aufgabe: Löschflag setzen (Kipschalter)
     *  Return: none
     **********************************************************/
        if($this->del) $this->del = false; else $this->del = true;
    }

    function lview() {
        $data = array(
        // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id',     $this->id,       VIEW),
            new d_feld('bereich', $this->bereich, VIEW),
            new d_feld('bilder', $this->bilder, VIEW),
            new d_feld('edit',   null,          EDIT, null, 4013), // edit-Button
            new d_feld('del',    null,          DELE, null, 4020), // Lösch-Button
        );
        return $data;
    }

    function view() {
        $data = array(
        // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('descr',  changetext($this->descr),  VIEW,   513),   // Beschreibung
            new d_feld('notiz',  changetext($this->notiz),  IVIEW,  514),   // Notiz
            // Bearbeitungssymbole und -ausgaben
            new d_feld('chdatum', date('ymd',$this->editdate), EDIT),
            new d_feld('chname', $this->getBearbeiter(),    EDIT)
        );
        return array_merge(self::lview(), $data);
    }
}
?>
