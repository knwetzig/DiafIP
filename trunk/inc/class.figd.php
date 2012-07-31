<?php
/***************************************************************
Klassen bibliotheken für filmografische Angaben

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:
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
    ::searchTitel($!) // gibt array der ID's zurück
      view()          // wird überladen von film

- Variablennamen, die sich auf die db-Tabelle beziehen müssen identisch
  mit den Spaltennamen sein, damit die Iteration gelingen kann.
- schreibende Ausgaben in die DB erfolgen grundsätzlich als prepaired-
  Statements. Grund ist die integrierte Quoting/Escape Funktionalität
- Ausgabefunktionen sollten mittelfristig umgestellt werden
**********************************************************/
public
$titel  = null,   // Originaltitel
$atitel = null,   // Arbeitstitel
$inhalt = null,   // Synopse
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
    Return: 0   alles ok
            1
    var:
    Anm.: Datensätze der Serientitel-DB sind manuell anzulegen
****************************************************************/
    global $db;

    if ($stat == false) {
        // begin TRANSACTION anlage titel
        // neue id besorgen
        $data = $db->extended->getRow("SELECT nextval('f_titel_id_seq');","integer");
        IsDbError($data);
        $this->id = $data['nextval']; // Zuweisung einer gültigen id

            // Formularausgabe
        $this->editTitel(false);
    } else {
        // Auswertung-Verifizierung
        $this->editTitel(true);
        // Daten in DB schreiben
        $data = array();
        foreach($this as $value) $data[] = $value;
        $data = array_slice($data, 0, 7);   // liefert die ersten 7 Einträge

        $quest =& $db->prepare('INSERT INTO f_titel (
            titel, atitel, inhalt, sid, sfolge, utitel, id)
            VALUES (?,?,?,?,?,?,?);',
            array('text','text','text','integer','integer','text','integer'),
            MDB2_PREPARE_MANIP);
        IsDbError($quest);
        $erg =& $quest->execute($data);
        IsDbError($erg);

        // offene TRANSACTION abschliessen
    }
}

function editTitel($stat) {
/****************************************************************
Aufruf: 0   Formularaufruf
        1   Auswertung
****************************************************************/
    global $db, $smarty, $myauth;
    if($stat == false) {
        // Formular anzeigen

        // Array der Serientitel laden
        $smarty->assign('serTitel', Titel::getSTitelList());
        // Menüpkt für Dialog
        $smarty->assign('dialog', getStringList(array(500,501,503,504,505,506,507)));
        $smarty->assign('obj', $this);
        $smarty->display('figd_titel_dialog.tpl');
    } else {
    // Formular auswerten
        // Obj zurückspeichern wird im aufrufenden Teil erledigt
        if (!$_POST['titel'] AND !$this->titel) {
            _e(getString(100));
            die();
        }
        if ($_POST['titel']) $this->titel = normtext($_POST['titel']);
        if ($_POST['atitel']) $this->atitel = normtext($_POST['atitel']);
        if ($_POST['inhalt']) $this->inhalt = normtext($_POST['inhalt']);
        $this->sid = normzahl($_POST['sid']);
        if ($this->sid)  $this->sfolge = normzahl($_POST['sfolge']);
        else $this->sfolge = null;
        if ($_POST['utitel']) $this->utitel = normtext($_POST['utitel']);
    } // ende Formularbereich
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
    $data = array_slice($data, 0, 7);   // liefert die ersten 7 Einträge

    $quest =& $db->prepare('UPDATE ONLY f_titel SET
        titel = ?, atitel = ?, inhalt = ?, sid = ?, sfolge = ?, utitel = ?
        WHERE id = ?;',
        array('text', 'text','text', 'integer', 'integer', 'text', 'integer'),
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
    $sql = '
        SELECT id
        FROM f_titel
        WHERE
            (titel ILIKE ?) OR
            (utitel ILIKE ?) OR
            (atitel ILIKE ?);
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
    $smarty->assign('dialog',getStringList(array(501,503,504,506)));
    $smarty->assign('titel', $this);
    $smarty->display('figd_titel_dat.tpl');
}
}// Ende Titelclass




/** =================================================================
                                FILM CLASS
================================================================= **/
class Film extends Titel {
    public
    	$id = null,
        $ezul = null,	// Erstzulassung
        $gatt = null,	// Binärwert der die Gattung beschreibt
        $memo = null;

    protected
        $art= array(
            'Animationspuppen',
            'Handpuppen',
            'Zeichentrick',
            'Flachfiguren',
            'Silhouetten',
            'Materialanimation',
            'Realfilm'
        );

    function __construct($nr = NULL) {
        if (isset($nr)) $this->getFilm($nr);
        //else $this->addFilm();
    }

    function __destruct() {
        // Aufgabe: Schreiben Daten -> DB?
    }

    function getFilm($nr) {
    /****************************************************************
    *  Aufgabe: Holt Daten aus den Tabellen
    *   Aufruf:
    *   Return: 0   alles ok
    *           1
    *      var: quest   Anfrageformulierung Filmdaten(prepare)
    *           erg     Ergebnis Filmdaten  (execute)
    *     Anm.: id wird durch titel-id überschrieben! ändern?
    ****************************************************************/
        global $db;
        $sql = "SELECT * FROM f_film WHERE $nr;";
        $erg =& $db->exec($sql);
        IsDbError($erg);
/*        $quest =& $db->prepare('SELECT * FROM f_film WHERE fid = ?;');
        IsDbError($quest);
        $erg =& $quest->execute($nr);
        IsDbError($erg);
*/        $row = $erg->fetchInto();

        $this->tid  = $row['titel'];    // titel AS titelid
        $this->ezul = $row['ezul'];
        $this->gatt = $row['gattung'];
        $this->memo	= $row['notiz'];
        parent::getTitel($this->tid);	// Abfrage Titeldaten
    }

    function searchByText($SText) {
    /****************************************************************
    *  Aufgabe: einfache Suchfunktion
    *           1. Titel durchsuchen (einschl. Notizen)
    *           2. In Filmnotizen suchen
    *   Aufruf: string
    *   Return: (array) der f_Titel.id's
    *           1  nichts gefunden
    *      var: $ergebnis   Liste der ID's
    *     Anm.:
    ****************************************************************/
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

    function setFilm() {
    /****************************************************************
    *  Aufgabe: schreibt die Daten in die Tabelle 'f_film' zurück (UPDATE)
    *   Aufruf:
    *   Return: 0   alles ok
    *           1
    *      var: parent->Titel
    *     Anm.:
    ****************************************************************/
        global $db;
        $data = array(
            $this->tid,
            $this->ezul,
            $this->gatt,
            $this->memo,
            $this->fid
        );

        $quest =& $db->prepare('
            UPDATE ONLY film
            SET titel = ?, ezul = ?, gattung = ?, notiz = ? WHERE id = ?;
        ');
        IsDbError($quest);
        $erg =& $quest->execute($data);
        IsDbError($erg);
    }

    function newFilm() {
    /****************************************************************
    *  Aufgabe: Legt neuen (leeren) Datensatz an
    *   Aufruf:
    *   Return: 0   alles ok
    *           1
    *      var: parent->Titel
    *     Anm.:
    ****************************************************************/
        global $db;
        $data = array(
            $this->fid,
            $this->tid,
            $this->ezul,
            $this->gatt,
            $this->memo
        );

        $erg =& $db->query("SELECT nextval('film_id_seq');"); // neue id besorgen
        $row = $erg->fetchRow();
        $this->fid = (int) $row['nextval'];	// Zuweisung einer gültigen id

        $quest =& $db->prepare('
            INSERT INTO film (id, titel, ezul, gattung, notiz)
            VALUES (?,?,?,?,?);
        ');
        IsDbError($quest);
        $erg =& $quest->execute($data);
        IsDbError($erg);
    }

    function editFilm($fidat) {
    /****************************************************************
    *  Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
    *   Aufruf: array, welches die zu ändernden Felder enthält
    *   Return: none
    *      var:
    *     Anm.:
    ****************************************************************/
// !!Variable        $this->tid  = $fidat['titid'];
        $this->ezul = $fidat['date'];
        $this->gatt = $fidat['art'];
        $this->memo = $fidat['notiz'];
    }

    function view() {
    /****************************************************************
    *  Aufgabe: Ausgabe des Filmdatensatzes (an smarty)
    *   Aufruf:
    *   Return: none
    *      var:
    *     Anm.:
    ****************************************************************/
        global $smarty;
        $smarty->assign('film', $this);
        $Gattung = null;
        foreach($this->art as $pos => $wert) {
            if (isBit($this->gatt,$pos)) $Gattung .= $wert.' ';
        }
        $smarty->assign('art', $Gattung);
        $smarty->display('figd_dat.tpl');
    }
}// ende FILM KLASSE

?>
