<?php
/**
 *      Lose Sammlung diverser Funktionen
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   Deutsches Institut für Animationsfilm e.V.
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
 * @version     $ Id: $
 * @requirement PHP Version >= 5.4
 */

/** Erzeugt ein Anmeldeformular und wertet es aus
 *
 * @param string $username Der zuletzt übergebene Benutzername
 * @param bool $status     Der Authorisationszustand
 * @param object $auth     Das Auth-Objekt
 */
function loginFunction($username = null, $status = null, &$auth = null) {
    echo <<<FORM
    <form action={$_SERVER['PHP_SELF']} method='post' accept-charset=utf-8
        style='position:absolute; top:150px; left:150px; padding:20px; text-align:center;
        border:1px solid #0080ff; border-radius: 1em; z-index:1000;'>
    <table><tr><td style="vertical-align:middle; padding-left:20px;">
    <input type='text' name='username' value='gast'
        style='width:120px; text-align:center; margin-bottom:5px;'
        onfocus="if (this.value=='gast'){this.value='';}" /><br />
    <input  type='password' name='password' value='gast'
        style='width:120px; text-align:center'
        onfocus="if (this.value=='gast'){this.value='';}" /><br />
    <input style='margin-top:10px; width:120px' type='submit' name='submit' value='einloggen' />
    </td><td>
        <img src="images/password.png" alt="Password" style="padding-left:20px" />
    </td></tr></table></form>
FORM;
}


/** Übergabe Datenbankobjekt
 *
 * @param object $obj ein DB-Objekt
 * @throws <Ausgabe Error-Report und Rollback der Transaktion mit anschließendem Exit>
 * @todo Für den Produktivbereich muß die Ausgabe über den Errorhandler erfolgen
 */
function IsDbError($obj) {
    if (MDB2::isError($obj)) :
        $db = MDB2::singleton();
        if ($db->inTransaction()) $db->rollback();
        echo "<fieldset class='error'><legend>DBMS-Fehler:</legend>";
        print_r($obj->getUserInfo());
        print_r($obj->getMessage());
        echo "</fieldset>\n";
        exit();
    endif;
}

/**
 *  Setzt ein Bit an der n-ten Stelle auf 1
 *
 * @param int $bitFeld
 * @param int $n
 * @return bool
 */
function setBit(&$bitFeld, $n) {
    // Ueberprueft, ob der Wert zwischen 0-31 liegt
    // $n ist die Position (0 beginnend)
    if (($n < 0) or ($n > 31)) return false;

    // Bit Shifting - Hier wird nun der Binaerwert fuer
    // die aktuelle Position gesetzt.
    // | ist nicht das logische ODER sondern das BIT-oder
    $bitFeld |= (0x01 << ($n));
    return true;
}

/**
 * Setzt ein Bit an der n-ten Stelle auf 0
 *
 * @param int $bitFeld
 * @param int $n
 * @return bool
 */
function clearBit(&$bitFeld, $n) {
    // Loescht ein Bit oder ein Bitfeld
    // & ist nicht das logische UND sondern das BIT-and
    $bitFeld &= ~(0x01 << ($n));
    return true;
}

/**
 * Ist die x-te Stelle eine 1?
 *
 * @param int $bitFeld
 * @param int $n
 * @return bool
 */
function isBit($bitFeld, $n) {
    return (bool)($bitFeld & (0x01 << ($n)));
}

/**
 * konvertiert ein ....
 * @param int $wert
 * @return array
 */
function bit2array($wert) {
    $a = [];
    for ($i = 0; $i < 32; $i++) :
        if (isBit($wert, $i)) $a[] = $i;
    endfor;
    return $a;
}

/**
 * konvertiert ein ....
 * @param $wert
 * @param $arr
 * @return mixed
 */
function array2wert($wert, $arr) {
    foreach ($arr as $k) setBit($wert, $k);
    return $wert;
}

/** Testet ob der Ausdruck $val mit $muster validierbar ist
 *
 * @param mixed $val
 * @param string $muster
 * @return int
 * @todo Die Funktion ist wohl besser in der Klasse Wort aufgehoben
 */
function isValid($val, $muster) {
    // Prüfung auf korrekte syntax - keine semantikprüfung!
    $muster = '/' . $muster . '/';
    return preg_match($muster, $val);
}

/**
 * Testet, ob das Datum existiert
 *
 * @param string $date
 * @param string $format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * Wandelt einen Dezimalwert in einen Hexadezimalwert um
 *
 * @param int $wert
 * @return string Hexstring
 */
function dez2hex($wert) {
    return sprintf('%x', $wert);
}

/**
 * Wandelt einen Hexstring in eine Dezimalzahl
 *
 * @param string $wert
 * @return int
 */
function hex2dez($wert) {
    return intval($wert, 16);
}

/**
 * parst die DB-Liste in ein PHP-Array  {12,34,56} --> array(12,34,56)
 *
 * @param string $list
 * @return array|int
 */
function list2array($list) {
    if (!is_string($list)) return 1;
    return preg_split("/[,{}]/", $list, null, PREG_SPLIT_NO_EMPTY);
}

/**
 * konvertiert ein Array in eine DB-Liste
 *
 * @param array $arr
 * @return string
 */
function array2list($arr) {
    // wandelt ein PHP-Array in eine DB-Liste um
    /** _____ ACHTUNG! Baustelle _____
     * _v(count($arr));
     * _v($arr,'array');
     * $list = '{';
     * foreach ($arr as $val) $list .= $val.',';
     * $list[strlen($list)-1] = '}';
     * return $list; **/
}

/**
 * Anzeige a la var_dump in einem Bereich
 *
 * @param mixed $text
 * @param string|null $titel
 */
function _v($text, $titel = null) {
    if ($text) {
        echo "<fieldset class='visor'>";
        if ($titel) echo "<legend>&nbsp;" . $titel . "&nbsp;</legend>";
        print_r($text);
        echo "</fieldset>\n";
    }
}

/**
 * Anzeige a la var_dump in einem Popup-Fenster (additiv)
 *
 * @param mixed $text
 * @param string|null $titel
 */
function _vp($text, $titel = null) {
// wie _v aber in einem seperaten Popup-Fenster
    if ($text) :
        $inh  = <<<'VIS'
<html><head><style>body {font-family:monospace;white-space: pre;color:#004000;background-color: #eeffee;padding: 5px;}h3 {border:1px dotted #004000; padding:5px}</style></head><body><h3>
VIS;
        $text = str_replace("\n", '<br />', print_r($text, true));
        echo "<script type=\"text/javascript\">
                mywindow = window.open(\"\", \"visor\", \"width=800px, height=600px, scrollbars=yes, resizable=yes\");
                mywindow.document.write(\"$inh\");
                mywindow.document.write(\"$titel\");
                mywindow.document.write(\"</h3>\");
                mywindow.document.writeln(\"$text\");
                mywindow.document.write(\"</body></html>\");
        </script>";
    endif;
}

/**
 * Hinweistext für User
 *
 * @param string $msg
 * @param string|null $form der Name der CSS-Regel
 */
function feedback($msg, $form = null) {
    global $str;
    if (is_numeric($msg))
        echo "<div class=$form>" . $str->getStr((int)$msg) . "</div>";
    else echo "<div class=$form>" . $msg . '</div>';
}


/**
 * Normalisierung von Text der als Resource übergeben wird
 *
 * @param string $var
 * @return array|string
 */
function normtext($var) {
    if (!is_array($var)) :
        // max drei White-Spaces erlaubt
        $var = preg_replace('/(\s{3})\s+/', '\1', $var);
        // wandelt Zeichen in Umschreibung ('>' --> '&gt;' usw. )
        return trim(htmlspecialchars($var, ENT_NOQUOTES, 'UTF-8'));
    else :
        return array_map('normtext', $var);
    endif;
}

/**
 * Wandelt den übergeben BB-Code in valides HTML um.
 * erlaubt sind b, i, u, pre, url, img
 *
 * @param string $str
 * @return mixed
 */
function changetext($str) {
    /* Falls eine 60 Zeichen lange Nicht-Whitespace-Zeichenkette gefunden wird (\S{60}) wird diese Zeichenkette '\0' um ein Leerzeichen ' ' erweitert. Der Browser kann dann an dieser Stelle den Text umzubrechen. */
    $str = preg_replace('/\S{60}/', '\0 ', $str);

    $str = preg_replace('=\[b\](.*)\[/b\]=Uis', '<span style="font-weight:bold;">\1</span>', $str);
    $str = preg_replace('=\[i\](.*)\[/i\]=Uis', '<span style="font-style:italic;">\1</span>', $str);
    $str = preg_replace('=\[u\](.*)\[/u\]=Uis', '<span style="text-decoration:underline;">\1</span>', $str);
    $str = preg_replace('=\[pre\](.*)\[/pre\]=Uis', '<pre>\1</pre>', $str);
    $str = preg_replace('=\[url\](.*)\[/url\]=Uis', '<a href="\1" target="_blank">\1</a>', $str);
    $str = preg_replace('#\[url=(.*)\](.*)\[/url\]#Uis', '<a href="\1" target="_blank">\2</a>', $str);
    $str = preg_replace('=\[img\](.*)\[/img\]=Uis', '<img src="\1" />', $str);
    $str = preg_replace('#(^|[^"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm',
                        '\1<a href="\2\3">\2\3</a>\4', $str);
    return $str;
}
