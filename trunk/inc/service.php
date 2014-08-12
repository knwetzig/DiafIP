<?php
/**************************************************************
Lose Sammlung diverser Funktionen

$Rev$
$Author$
$Date$
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/

function loginFunction($username = null, $status = null, &$auth = null) {
//  Erwartet drei Argumente: der zuletzt übergebene Benutzername,
//    den Authorisations-Zustand und das Auth-Objekt

echo <<<FORM
    <form action={$_SERVER['PHP_SELF']} method='post'
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

/** **** Datenbank Tools *************************************/

function IsDbError($obj) { // Übergabe Datenbankobjekt
    if (MDB2::isError($obj)) :
        $db = MDB2::singleton();
        if ($db->inTransaction()) $db->rollback();
        echo "<fieldset class='error'><legend>DBMS-Fehler:</legend>";
            print_r($obj->getUserInfo());
            print_r($obj->getMessage());
        echo "</fieldset>\n";
        exit();
    endif;
    return;
}

/** **** Bitfeldfunktionen / Checkboxen **********************/
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

function clearBit(&$bitFeld, $n) {
	// Loescht ein Bit oder ein Bitfeld
	// & ist nicht das logische UND sondern das BIT-and
	$bitFeld &= ~(0x01 << ($n));
	return true;
}

function isBit($bitFeld, $n) {
	// Ist die x-te Stelle eine 1?
	return (bool)($bitFeld & (0x01 << ($n)));
}

function bit2array($wert) {
    $a = array();
    for ($i = 0; $i < 32; $i++) :
        if (isBit($wert, $i)) $a[] = $i;
    endfor;
    return $a;
}

function array2wert($wert, $arr) {
    foreach ($arr as $k) setBit($wert,$k);
    return $wert;
}

/** **** ALLGEMEINE FUNKTIONEN *********************************************/

function getStringList($sl) {
    if (!is_array($sl)) return 1;
    // Die Liste spricht immer die eingestellte Sprache
    $nl = array();
    foreach ($sl as $value) {
       $nl[] = d_feld::getString($value);
    }
    return $nl;
}

function isValid($val, $muster) {
    // Prüfung auf korrekte syntax - keine semantikprüfung!
    $muster = '/'.$muster.'/';
    return preg_match($muster, $val);
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function dez2hex($wert) {
    return sprintf('%x', $wert);
}

function hex2dez($wert) {
    return intval($wert, 16);
}

function list2array($list) {
//  parst die DB-Liste in ein PHP-Array  {12,34,56} --> array(12,34,56)
    if (!is_string($list)) return 1;
    return preg_split("/[,{}]/", $list, null, PREG_SPLIT_NO_EMPTY);
}

function array2list($arr) {
    // wandelt ein PHP-Array in eine DB-Liste um
/** _____ ACHTUNG! Baustelle _____
_v(count($arr));
_v($arr,'array');
    $list = '{';
    foreach ($arr as $val) $list .= $val.',';
    $list[strlen($list)-1] = '}';
    return $list; **/
}

function _v($text, $titel = null) {
    if ($text) {
        echo "<fieldset class='visor'>";
        if ($titel) echo "<legend>&nbsp;".$titel."&nbsp;</legend>";
        print_r($text);
        echo "</fieldset>\n";
    }
}

function _vp($text, $titel = null) {
// wie _v aber in einem seperaten Popup-Fenster
    if ($text) :
        $inh = <<<'VIS'
<html><head><style>body {font-family:monospace;white-space: pre;color:#004000;background-color: #eeffee;padding: 5px;}h3 {border:1px dotted #004000; padding:5px}</style></head><body><h3>
VIS;
$text = str_replace( "\n", '<br />', print_r($text, true));
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

function feedback($msg, $form = null) {
    if (is_numeric($msg))
        echo "<div class=$form>".d_feld::getString((int)$msg)."</div>";
    else echo "<div class=$form>".$msg.'</div>';
}

/** **** TEXTBEARBEITUNG ****************************************************/
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

function changetext($str) {
    /* Falls eine 60 Zeichen lange Nicht-Whitespace-Zeichenkette gefunden wird (\S{60}) wird diese Zeichenkette '\0' um ein Leerzeichen ' ' erweitert. Der Browser kann dann an dieser Stelle den Text umzubrechen. */
    $str = preg_replace('/\S{60}/', '\0 ', $str);
    /* BB-Code Umwandlungen:
        erlabt sind b, i, u, pre, url, img  */
    $str = preg_replace('=\[b\](.*)\[/b\]=Uis', '<span style="font-weight:bold;">\1</span>', $str);
    $str = preg_replace('=\[i\](.*)\[/i\]=Uis', '<span style="font-style:italic;">\1</span>', $str);
    $str = preg_replace('=\[u\](.*)\[/u\]=Uis', '<span style="text-decoration:underline;">\1</span>', $str);
    $str = preg_replace('=\[pre\](.*)\[/pre\]=Uis', '<pre>\1</pre>', $str);
    $str = preg_replace('=\[url\](.*)\[/url\]=Uis', '<a href="\1" target="_blank">\1</a>', $str);
    $str = preg_replace('#\[url=(.*)\](.*)\[/url\]#Uis', '<a href="\1" target="_blank">\2</a>', $str);
    $str = preg_replace('=\[img\](.*)\[/img\]=Uis', '<img src="\1" />', $str);
    $str = preg_replace('#(^|[^"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm', '\1<a href="\2\3">\2\3</a>\4', $str);

    return $str;
}
?>