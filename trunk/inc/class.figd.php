<?php
/**************************************************************

    Klassenbibliothek für Filmogr.-/Bibliografische Daten

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/


/** =================================================================
                            TITEL KLASSE
================================================================= **/
class Titel {
/**********************************************************
func: __construct($)
      newTitel()
      editTitel()
      getTitel($!)    // holt db-felder ins Objekt
      setTitel()      // schreibt objekt.titel -> db
      delTitel()
      protected::isLinked //prüft die Verknüpfung mit anderen Tabellen
    ::searchTitel($!) // gibt array der ID's zurück
      view()          // wird überladen von film

- Variablennamen, die sich auf die db-Tabelle beziehen müssen identisch
  mit den Spaltennamen sein, damit die Iteration gelingen kann.
**********************************************************/
public
$titel  = null,   // Originaltitel
$atitel = null,   // Arbeitstitel
$sid    = null,   // Serien - ID
$sfolge = null,   // Serienfolge
$utitel = null,   // Untertitel
$id     = null,   // Titel-ID ->       diafip.titel.id
$stitel = null,   // Serientitel ->    diafip.f_stitel.titel
$sdescr = null;   // Beschreibung Serie

function __construct($nr = NULL) {
    if (isset($nr)) $this->getTitel($nr);
    else {
        // keinen neuen DB-Satz anlegen. Nur ein leeres Obj
    }
}

function addTitel($stat) {
/****************************************************************
Aufgabe: neuen Datensatz anlegen
        id-> Sequenz holen und anschliessend @self->edit aufrufen und
        die noch leeren Felder auffüllen anschl. mit @self->set schreiben
Aufruf: 0   für Erstaufruf
        1   Parameterverarbeitung nach Formular
Anm.:   Datensätze der Serientitel-DB sind manuell anzulegen
****************************************************************/
    global $db, $myauth;
    $types = array(
        'text',         // titel
        'text',         // atitel
        'integer',      // sid
        'integer',      // sfolge
        'text',         // utitel
        'integer',      // id aus Sequenz
        'integer',      // uid des Erstellers
    );

    if ($stat == false) {
        // Siehe Eintrag class.pers.php::newPerson
        $this->editTitel(false);
    } else {
        // Auswertung-Verifizierung
        $this->editTitel(true);
        $n = $db->extended->getRow("SELECT nextval('f_titel_id_seq');");
        IsDbError($n);
        $this->id = $n['nextval'];
        // Daten in DB schreiben
        $data = array();
        foreach($this as $key => $wert) $data[$key] = $wert;
        $data = array_slice($data, 0, 6);   // liefert die ersten 6 Einträge
        $data['editfrom'] = $myauth->getAuthData('uid');

        IsDbError($db->extended->autoExecute(
            'f_titel', $data, MDB2_AUTOQUERY_INSERT, null, $types));
    }
}

function editTitel($stat) {
/****************************************************************
Aufruf: 0   Formularaufruf
        1   Auswertung
****************************************************************/
    global $db, $smarty, $myauth;
    if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

    if($stat == false) {        // Formular anzeigen
        // Menüpkt für Dialog
        $data = a_display(array(
            // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id', $this->id),
            new d_feld('titel', $this->titel, EDIT, 500),
            new d_feld('atitel', $this->atitel, EDIT, 503),
            new d_feld('utitel', $this->utitel, EDIT, 501),
            new d_feld('stitel', $this->stitel, EDIT, 504),
            new d_feld('sfolge', $this->sfolge, EDIT, 505),
            new d_feld('sid', $this->sid),
            new d_feld('bereich', null, VIEW, 4025)
        ));
        $smarty->assign('dialog', $data);
        // Array der Serientitel laden
        $smarty->assign('serTitel', self::getSTitelList());
        $smarty->display('figd_titel_dialog.tpl');
        $myauth->setAuthData('obj', serialize($this));
    } else {
    // Formular auswerten
        // Obj zurückspeichern wird im aufrufenden Teil erledigt
        if (empty($this->titel) AND empty($_POST['titel'])) {
            fehler(100);
            die();
        }
        if ($_POST['titel']) $this->titel = normtext($_POST['titel']);
        if ($_POST['atitel']) $this->atitel = normtext($_POST['atitel']);
        if(is_numeric($_POST['sid'])) $this->sid = (int)($_POST['sid']);
        if ($this->sid)  $this->sfolge = normzahl($_POST['sfolge']);
        if ($_POST['utitel']) $this->utitel = normtext($_POST['utitel']);
    } // Formularbereich
}


function getTitel($nr) {
/****************************************************************
*  Aufgabe: Holt Daten aus den db-Tabellen
*           wenn Serie -> Serientitel und Folgenummern
*   Aufruf: $nr = f_titel.id
*   Return: 0   alles ok
*           1   kein Datensatz vorhanden
****************************************************************/
    global $db;

    $sql='SELECT * FROM f_titel WHERE id = ?;';
    $data = $db->extended->getRow($sql,null,$nr);
    IsDbError($data);
    if (!$data) return(1);       // kein Datensatz vorhanden

    // Ergebnis -> Objekt schreiben
    foreach($data as $key => $val) {
        $this->$key = $val;
    }

    // ermitteln Serientitel, soweit vorhanden
    if ($this->sid) {
        $sql = 'SELECT titel, descr FROM f_stitel WHERE sertitel_id = ?;';
        $data = $db->extended->getRow($sql, null, $this->sid);
        IsDbError($data);
        $this->stitel = $data['titel'];
        $this->sdescr = $data['descr'];
    }
}

function setTitel() {
/****************************************************************
*  Aufgabe: schreibt geänderte Werte in die db zurück
*   Return: 0   alles ok
*           1   es gibt keine id
****************************************************************/
    global $db;

    if (!$this->id) return 1;   // Abbruch weil leerer Datensatz

    // abgespeckte Kopie von $this erstellen
    $data = array();
    foreach($this as $value) $data[] = $value;
    $data = array_slice($data, 0, 6);   // liefert die ersten 7 Einträge

    $quest =& $db->prepare('UPDATE ONLY f_titel SET
        titel = ?, atitel = ?, sid = ?, sfolge = ?, utitel = ?
        WHERE id = ?;',
        array('text', 'text','integer', 'integer', 'text', 'integer'),
        MDB2_PREPARE_MANIP);
    IsDbError($quest);
    $erg =& $quest->execute($data);
    IsDbError($erg);

    /** im Moment manuell ändern
    // Serientitel schreiben
    if ($this->sid) {
        $sql =("UPDATE ONLY f_stitel
                SET titel = '".$this->stitel."',
                    descr = '".$this->sdescr."'
                WHERE sertitel_id = ".$this->sid.";");
        $erg =& $db->exec($sql);
        IsDbError($erg);
    }
    **/
}

function delTitel() {
/****************************************************************
Unglaublich, hier wird der Titel gelöscht :)
Aufruf: ID des Titels
Return: O -> ok
        1 -> Fehler
****************************************************************/
    global $db;
    if($this->isLinked()) :
        fehler(10006);
        return;
    endif;
    IsDbError($db->extended->autoExecute(
        'f_titel', null, MDB2_AUTOQUERY_DELETE, 'id ='.$db->quote($this->id, 'integer')));
    unset($this);
}

function searchTitel($s) {
/****************************************************************
*  Aufgabe: Suchfunktion in allen Titelspalten (außer Serientiteln)
*   Aufruf: ::string
*   Return: 0   gibt ein Array der gefunden Titel-ID's zurück
*           1   nichts gefunden
*     Anm.: statisch
****************************************************************/
    global $db;
    $s = "%".$s."%";
    $sql = 'SELECT id FROM f_titel WHERE
                (titel ILIKE ?) OR
                (utitel ILIKE ?) OR
                (atitel ILIKE ?)
            ORDER BY titel ASC;
        ';
    $data = $db->extended->getCol($sql,null,array($s,$s,$s));
    IsDbError($data);
    $erg = $data;
    //Weiter suche in Serientiteln
    $sql = 'SELECT sertitel_id FROM f_stitel WHERE (titel ILIKE ?);';
    $stit = $db->extended->getCol($sql,null,$s);
    IsDbError($stit);
    $sql = 'SELECT id FROM f_titel WHERE sid = ?;';
    foreach($stit as $wert) {
        $data = $db->extended->getCol($sql,null,array($wert));
        IsDbError($data);
        $erg = array_merge($erg,$data);
    }
    if ($erg) {
        return array_unique($erg);		// id's der gefundenen Titel
    } else return 1;
}

protected function isLinked() {
// Gibt die Anzahl der verknüpften Datensätze zurück
    global $db;
    $data = $db->extended->getRow('SELECT COUNT(*) FROM public.f_film
        WHERE f_film.titel_id = ?;', null, $this->id);
    IsDbError($data);
    return $data['count'];
}

function getSTitelList() {
/****************************************************************
*  Aufgabe: Ausgabe der Serientitelliste
*   Aufruf: statisch
*   Return: array, alles iO
*           1   Fehler
****************************************************************/
    global $db;
    $ergebnis = array();
    $sql = 'SELECT sertitel_id, titel FROM f_stitel ORDER BY titel ASC;';
    $erg =& $db->query($sql);
    IsDbError($erg);
    while ($row =$erg->fetchRow()) {
        $ergebnis[$row['sertitel_id']] = $row['titel'];
    }
    if ($ergebnis) {
        return $ergebnis;     // Liste der Serientitel
    } else return 1;
}

function view() {
/****************************************************************
*  Aufgabe: Ausgabe der Titeldaten an smarty
****************************************************************/
    global $smarty;
    $data = a_display(array(
        // name, inhalt, opt -> rechte, label, tooltip, valstring
        new d_feld('id', $this->id, VIEW),      // tid
        new d_feld('titel', $this->titel , VIEW, 500),
        new d_feld('atitel', $this->atitel , VIEW, 503),
        new d_feld('utitel', $this->utitel , VIEW, 501),
        new d_feld('stitel', $this->stitel , VIEW, 504),
        new d_feld('sfolge', $this->sfolge , VIEW),
        new d_feld('sdescr', $this->sdescr , VIEW), // Beschreibung der Serie
        new d_feld('edit', null, EDIT, null, 4022),
        new d_feld('del', null, DELE, null, 4021),
        new d_feld('addfilm', null, EDIT, null, 4024),  // Neuanlage filmogr. Datensatz
        new d_feld('addbibl', null, EDIT, null, 4026)   // Neuanlage bibliogr. Datensatz
    ));
    if($this->isLinked()) unset($data['del']);
    $smarty->assign('dialog', $data);
    $smarty->display('figd_titel_dat.tpl');
}
}// Ende Titelclass


/** =================================================================
                                MAIN CLASS
================================================================= **/
abstract class Main {
/********************************************************************
    __construct(?int)
    get(int)            protected
    set()               protected
    add(bool)           public
    edit(bool)          public
    isLinked()          protected
    view()              public
********************************************************************/
    protected
        $id         = null,
        $del        = false,
        $isvalid    = false,
        $titel_id   = null,
        $bild_id    = null,
        $prod_jahr  = null,
        $thema      = null,
        $quellen    = null,
        $inhalt     = null,
        $notiz      = null,
        $editfrom   = null,
        $editdate   = null;

    abstract protected function isLinked();
    abstract protected function set();

    function __construct($nr = NULL) {
        if (isset($nr)) self::get($nr);
        //else $this->addFilm();
    }

    protected function get($nr) {
    // Initialisiert das Objekt
        global $db;
        $this->id = $nr;
        $sql = "SELECT * FROM ONLY f_main WHERE $nr;";
/** _____ ACHTUNG! BAUSTELLE _____ **/
    }

    function add($stat) {
    /****************************************************************
        Aufgabe: Legt neuen (leeren) Datensatz an
        Aufruf:
        Return: Fehlercode
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }

    function edit($stat) {
    /****************************************************************
        Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
        Aufruf: array, welches die zu ändernden Felder enthält
        Return: none
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
    }


    function view() {
    /****************************************************************
        Aufgabe: Ausgabe des Filmdatensatzes (an smarty)
        Aufruf:
        Return: none
            var:
            Anm.:
    ****************************************************************/
        global $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
    }
}// ende Main KLASSE

/** =================================================================
                                FILM CLASS
================================================================= **/
class Film extends Main {
/********************************************************************
    __construct(?int)
    get(int)            protected
    set()               protected
    add(bool)           public
    edit(bool)          public
    isLinked()          protected
    view()              public
********************************************************************/
    protected
        $gattung    = null,
        $prodtechnik = null,
        $laenge     = null,
        $fsk        = null,
        $praedikat  = null,
        $mediaspezi = 0,
        $urauffuehr = '1900-01-01';

    protected function set() {
    /****************************************************************
        Aufgabe: schreibt die Daten in die Tabelle 'f_main' zurück (UPDATE)
        Return: Fehlercode
    ****************************************************************/
        global $db;
    }

    protected function isLinked() {
    // Checkt ob der Datensatz verknüpft ist (0 = frei / Nr = Anzahl)
        global $db;
    }

} // endclass Film
/** ----- Altlasten --- snippet
function searchByText($SText) {
/ ****************************************************************
    Aufgabe: einfache Suchfunktion
            1. Titel durchsuchen (einschl. Notizen)
            2. In Filmnotizen suchen
    Aufruf: string
    Return: (array) der f_Titel.id's
            1  nichts gefunden
        var: $ergebnis   Liste der ID's
        Anm.:
**************************************************************** /
    global $db;
    $ergebnis = array();

    // 1. suche in Titeln und Inhalt
    $tli = Titel::search($STxt);
    foreach($tli as $nr) {
        $erg =& $db->query('
            SELECT DISTINCT fid FROM f_film
            WHERE titel = '.$nr.';
        ');
        IsDbError($erg);
        while ($row = $erg->fetchInto()) $ergebnis[] = (int)$row['fid'];
    }

    // 2. Suche in Notizen
    $STxt = "%".$STxt."%";
    $erg =& $db->query("
        SELECT DISTINCT fid
        FROM f_film
        WHERE film.notiz ILIKE '".$STxt."';");
    IsDbError($erg);

    while ($row = $erg->fetchRow()) $ergebnis[] = (int)$row['fid'];
    if ($ergebnis) {
        return array_unique($ergebnis); // id's der gefundenen Filme
    } else return 1;
}

---- /snippet **/

?>