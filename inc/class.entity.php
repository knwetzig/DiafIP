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
        $content = array(
            'id'         => null,
            'bereich'    => '',      // Enthält die Kennung zu welchem Bereich die
                                     // Entität gehört....
            'descr'      => '',      // Beschreibung bzw. Biografie bei Personen
            'bilder'     => array(),
            'notiz'      => '',      // selbsterklärend ;-)
            'isvalid'    => false,   // Flag zur Kennzeichnung, das dieser Datensatz
                                     // abschließend bearbeitet wurde
            'del'        => false,   // Löschflag
            'editfrom'   => null,    // uid des Bearbeiters
            'editdate'   => null     // timestamp width Timezone
        );

    private
        $types = array(
        'integer',
        'text',     // bereich
        'text',     // descr
        'text',     // bilder (eigtl ein array)
        'text',     // notiz
        'boolean',  // isvalid
        'boolean',  // del
        'integer',  // uid
        'date'      // editdate (soll nicht konvertiert werden!)
    );

    function __construct($nr = null) {
            if(isset($nr) AND is_numeric($nr)) self::get(intval($nr));
    }

    protected function get($nr) {
    // Diese Funktion initialisiert das Objekt

        $db =& MDB2::singleton();
        $data = $db->extended->getRow(self::GETDATA,$this->types,$nr,'integer');
        IsDbError($data);
        if($data) :
            foreach($data as $key => $val) $this->content[$key] = $val;
            if($this->content['bilder']) :
                $this->content['bilder'] = preg_split("/[,{}]/", $this->content['bilder'], null, PREG_SPLIT_NO_EMPTY);
                // Achtung Elemente liegen als Text vor! (nicht integer)
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
        if(!empty($this->content['editfrom'])) :
            $bearbeiter = $db->extended->getCol(
                'SELECT realname FROM s_auth WHERE uid = '.$this->content['editfrom'].';');
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
            $this->content['editfrom'] = $myauth->getAuthData('uid');
            $this->content['editdate'] = $_SERVER['REQUEST_TIME'];
    }

    function setValid() {
    /**********************************************************
     * Aufgabe: Bearbeitungsflag setzen (Kippschalter)
     *  Return: none
     **********************************************************/
        if($this->content['isValid']) $this->content['isValid'] = false; else $this->content['isValid'] = true;
    }

    function setDel() {
    /**********************************************************
     * Aufgabe: Löschflag setzen (Kipschalter)
     *  Return: none
     **********************************************************/
        if($this->content['del']) $this->content['del'] = false; else $this->content['del'] = true;
    }

    function lview() {
        $data = array(
        // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id', dez2hex($this->content['id']), VIEW),
            new d_feld('bereich',$this->content['bereich'], VIEW),
            new d_feld('bilder', $this->content['bilder'], VIEW),
            new d_feld('edit', null, EDIT, null, 4013), // edit-Button
            new d_feld('del', null, DELE, null, 4020), // Lösch-Button
        );
        return $data;
    }

    function view() {
        $data = array(
        // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('descr',  changetext($this->content['descr']), VIEW,513), // Beschreibung
            new d_feld('notiz',  changetext($this->content['notiz']), IVIEW, 514),
            // Bearbeitungssymbole und -ausgaben

            new d_feld('chdatum', $this->content['editdate'], EDIT),
            new d_feld('chname', $this->getBearbeiter(), EDIT),
            new d_feld('isvalid', $this->content['isvalid'], IVIEW)
        );
        return array_merge(self::lview(), $data);
    }
}
?>
