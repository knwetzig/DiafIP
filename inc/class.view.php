<?php
/**************************************************************
    Stellt Klassen und Funktionen für die
    Ein-/Ausgabefunktionalität bereit.

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/


/** =================================================================
                                VIEW
================================================================= **/
class d_feld {
/**********************************************************
Repräsentiert ein Ein-/Ausgabeelement

  getString(int)  STATIC  Holt String aus Tabelle s_strings
  isValid()               Validierung + Variable setzen
  display()       DYNA    Gibt ein Array für Anzeige aus
**********************************************************/
    protected
        $name       = null, // Feldname aus Objekt
        $inhalt     = null, // Wert des Objektes
        $valStr     = null, // Regulärer Ausdruck zur Validierung des Inhalts
        $label      = null, // Beschriftungstext
        $tooltip    = null,
        $rights     = null; // erforderliche Rechte (pos des Bits, 0 beginnend)

    function __construct($name, $wert, $rechte = null, $label = null, $tipp = null, $valStr = null) {
        $this->name     = $name;
        $this->inhalt   = $wert;
        if (!empty($rechte) AND is_int($rechte)) $this->rights = $rechte;
        $this->valStr   = $valStr;
        if (!empty($label) AND is_int($label)) $this->label = self::getString($label);
        if (!empty($tipp) AND is_int($tipp)) $this->tooltip = self::getString($tipp);
    }

    static function getString($nr) {
        global $lang;
        if(empty($nr) OR !is_numeric($nr)) return null;

        $db = MDB2::singleton();
        $data = $db->extended->getRow(
            'SELECT de, en, fr FROM s_strings WHERE id = ?;', null, $nr);
        if(!empty($data[$lang])) $st = $data[$lang]; else $st = $data['de'];
        return $st;
    }

    protected function isValid() {
        // Püfung auf korrekte syntax - keine semantikprüfung!
        if(isset($valStr)) {
            if(preg_match('/'.$this->valStr.'/', $this->inhalt))
                return true; else return false;
        }
        return 4;   // kein Validierungsstring vorhanden
    }

    function display() {
        global $myauth;
        /* Test auf Berechtigung, stellt sicher, das auch nur das ausgegeben
        wird, wozu der Nutzer eine Berechtigung hat */
        if(is_int($this->rights) AND !isBit($myauth->getAuthData('rechte'),
            $this->rights)) return false;
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
