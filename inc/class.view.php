<?php
/**************************************************************
Stellt Klassen und Funktionen für die
Ein-/Ausgabefunktionalität bereit.

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/



/** =================================================================
                                VIEW
================================================================= **/
class d_feld {
/**********************************************************
Beschreibt ein Ein-/Ausgabeelement

  getString(int)  STATIC  Holt String aus Tabelle s_strings
  isValid()               Validierung + Variable setzen
                          bool / 2->keine Validierung möglich
  display()       DYNA    Gibt ein Array für Anzeige aus
**********************************************************/
protected
    $name       = null, // Feldname aus Objekt
    $inhalt     = null, // Wert des Objektes
    $valStr     = null, // Regulärer Ausdruck zur Validierung des Inhalts
    $label      = null, // Beschriftungstext
    $tooltip    = null,
    $rights     = null, // erforderliche Rechte (pos des Bits, 0 beginnend)
    $isvalid    = false; // bool -> isvalid

function __construct($na, $inh, $ri = null, $de = null, $tt = null, $ty= null) {
    // de -> label / tt -> tooltip sind id-nr für stringtabelle
    $this->name     = $na;
    $this->inhalt   = $inh;
    $this->rights   = $ri;
    $this->valStr   = $ty;
    if ($de) $this->label = self::getString($de);
    if ($tt) $this->tooltip = self::getString($tt);
}

function getString($nr) {
    global $db, $lang;
    $str = "";
    if($lang === 'us' OR $lang === 'en') {
        $sql = "SELECT en FROM s_strings WHERE id = ?;";
        $data = $db->extended->getRow($sql, null, $nr);
        IsDbError($data);
        if($data['en']) $str = $data['en'];
    }
    if($str == "") {
        $sql = "SELECT de FROM s_strings WHERE id = ?;";
        $data = $db->extended->getRow($sql, null, $nr);
        IsDbError($data);
        $str = $data['de'];
    }
    return $str;
}

function isValid() {
    if(isset($valStr)) {
        // Püfung auf korrekte syntax - keine semantikprüfung!
        if(preg_match('/'.$this->valStr.'/', $this->inhalt)) {
            $this->isvalid = true;
            return true;
        } else return false;
    }
    return 2;   // kein Validierungsstring vorhanden
}

function display() {
    global $myauth;
    /* Test auf Berechtigung, stellt sicher, das auch nur das ausgegeben
    wird, wozu der Nutzer eine Berechtigung hat */
    if(is_int($this->rights) AND !isBit($myauth->getAuthData('rechte'), $this->rights)) return false;
    // feldname, inhalt, label, tooltip
    $daten = array(
        $this->name,
        $this->inhalt,
        $this->label,
        $this->tooltip);
    return $daten;
}
}


/** =================================================================
                               arrayverarbeitung
================================================================= **/
function a_display($arr) {
    $data = array();
    foreach ($arr as $val) {
        if (is_array($val->display())) {
            $a = $val->display();
            if($a) $data[$a[0]] = $a;
        }
    }
    return $data;
}
?>
