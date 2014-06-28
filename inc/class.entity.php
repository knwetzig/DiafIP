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
    static function IsInDB($nr, $bereich); // Test ob id/bereich in der DB existiert
    static function getBereich($nr); // Holt zur ID die Bereichskennung
    static function search($s); // liefert die ID's des Suchmusters
    function setValid();        // Set/Unset Flag
    function setDel();          // -- dito--
    static function display($data, $vorlage);
}

abstract class Entity implements iEntity {
    /**********************************************************
    * Interne Methoden:
    *       get($nr)
    *       setSignum()
    **********************************************************/
    const
        TYPEENTITY = 'integer,text,text,text,text,boolean,boolean,integer,text',
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


    function __construct($nr = null) {
        if(isset($nr) AND is_numeric($nr)) self::get(intval($nr));
    }

    protected function get($nr) {
    // Diese Funktion initialisiert das Objekt

        $db =& MDB2::singleton();
        $data = $db->extended->getRow(self::GETDATA,list2array(self::TYPEENTITY),$nr,'integer');
        IsDbError($data);
        if($data) :
            foreach($data as $key => $val) $this->content[$key] = $val;
            if($this->content['bilder']) :
                $this->content['bilder'] = list2array($this->content['bilder']);
                // Achtung Elemente liegen als Text vor! (nicht integer)
            endif;
        else :
            feedback(4,'error');
            exit(4);
        endif;
    }

    abstract function add($stat = null);
    abstract function edit($stat = null);
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

    static function IsInDB($nr, $bereich) {
    /**********************************************************
    Aufg.:  Test ob Nr. als id mit diesem Bereichbuchstaben in der DB existiert
    Input: $nr als Dezimal-Wert / $bereich = Großbuchstabe
    **********************************************************/
        $db =& MDB2::singleton();

        if(is_numeric($nr) AND is_string($bereich) AND (strlen($bereich) == 1)) :
            $data = $db->extended->getRow(
                'SELECT COUNT(*) FROM entity WHERE id = ? and bereich = ?;',
                null, array($nr,$bereich));
            IsDbError($data);

            if($data['count']) :
                return true;
            endif;
        endif;
    }

    static function getBereich($nr) {
    // Holt zur ID die Bereichskennung
        $db =& MDB2::singleton();

        if($nr AND is_numeric($nr)) :
            $data = $db->extended->getRow('SELECT bereich FROM entity WHERE id = ?;', null, $nr);
            IsDbError($data);
            if(!empty($data)) :
                return $data['bereich'];
            endif;
        endif;
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

    protected function view() {
        $data = array(
        // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id', $this->content['id'], VIEW),
            new d_feld('bereich',$this->content['bereich'], VIEW),
            new d_feld('descr',  changetext($this->content['descr']), VIEW,513), // Beschreibung
            new d_feld('bilder', $this->content['bilder'], VIEW),
            new d_feld('notiz',  changetext($this->content['notiz']), IVIEW, 514),
            new d_feld('isvalid', $this->content['isvalid'], IVIEW),
            new d_feld('chdatum', $this->content['editdate'], EDIT),
            new d_feld('chname', $this->getBearbeiter(), EDIT),
            new d_feld('edit', null, EDIT, null, 4013), // edit-Button
            new d_feld('del', null, DELE, null, 4020), // Lösch-Button
        );
        return $data;
    }

    static function display($data, $vorlage) {
        global $smarty;
        $smarty->assign('dialog', a_display($data), 'nocache');
        $smarty->display($vorlage);
    }
}
?>

<!--
$zeit = microtime(true);
$zeit=microtime(true)-$zeit;feedback('Dauer: '.sprintf('%1.6f',$zeit), 'warng');
-->
