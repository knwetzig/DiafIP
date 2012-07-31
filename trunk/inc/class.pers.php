<?php
/**************************************************************
Enthält alle Klassenbibliotheken zu Personendaten

Autor:      Knut Wetzig     (knut@knutwetzig.de)
Copyright:  DIAF e.V.       (kontakt@diaf.de)
Date:       20120717

ToDo:
**************************************************************/


/** =================================================================
                                PERSON ALIAS
================================================================= **/
class Alias {
    public
        $id = null,
        $name = null,
        $notiz = null;

    function __construct($nr = null) {
            if(isset($nr)) self::getAlias($nr);
    }

    function getAlias($nr) {
        global $db;
        $this->id = $nr;
        $sql = 'SELECT name,notiz FROM ONLY p_alias WHERE id = ?;';
        $data = $db->extended->getRow($sql, null, $this->id, 'integer');
        IsDbError($data);
        $this->name = $data['name'];
        $this->notiz = $data['notiz'];
        return $data['name'];
    }

    function getAliasList() {
        global $db;
        $sql = 'SELECT id,name FROM ONLY p_alias;';
        $data = $db->extended->getAll($sql, array('integer','text'));
        IsDbError($data);

        $alist=array('-- ohne --');
        foreach($data as $val) {
            $alist[$val['id']] = $val['name'];
        }
        return $alist;
    }
}


/** =================================================================
                                PERSON CLASS
================================================================= **/
class Person extends Alias {
/**********************************************************
func: __construct($)
      newPerson()
      editPerson()
      getPerson($!)     // holt db-felder -> this / gibt ein array zurück
      setPerson()       // schreibt objekt.person -> db
    ::delPerson()       // löscht Personendatensatz
    ::searchPerson($!)  // gibt array der ID's zurück
      view()            // ausgabe

- Variablennamen, die sich auf die db-Tabelle beziehen müssen identisch
  mit den Spaltennamen sein, damit die Iteration gelingen kann.

**********************************************************/
public
    $vname  = null,
    $gtag   = null,       // Geburtstag
    $gort   = null,       // Geburtsstadt
    $ttag   = null,       // Todestag
    $tort   = null,       // Sterbeort
    $strasse = null,      // Strasse + HNr. und Adresszusätze
    $plz    = null,       // PLZ des Wohnortes
    $wort   = null,       // Wohnort (Ort, land))
    $tel    = null,       // Telefonnummer
    $mail   = null,       // mailadresse;
    $biogr  = null,       // Kurzbiografie
    $aliases = null,
    $bild   = null;       // id auf Bilddatenbank


function __construct($nr = NULL) {
    if (isset($nr)) $this->getPerson($nr);
}

function getPerson($nr) {
/****************************************************************
Aufgabe: Datensatz holen, in @self schreiben
 Aufruf: nr  ID des Personendatensatzes (NOT STATIC)
 Return: array   alles ok
         1       Fehler
   Anm.:
****************************************************************/
    global $db;

    $sql = 'SELECT * FROM p_person WHERE id = ?;';
    $data = $db->extended->getRow($sql, null, array($nr));
    // Ergebnis -> Objekt schreiben
    foreach($this as $key => &$wert) $wert = $data[$key];
    unset($wert);

    // -> Bildinitialisierung hinzufügen
}

function editPerson($stat) {
/****************************************************************
Aufgabe:    Obj ändern
Aufruf:     false   Formularanforderung
            true    Auswertung
Return:     0   alles ok
            1
Anm.: Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehler-
    haften Eingaben.
****************************************************************/
    global $db, $myauth, $smarty;
    if($stat == false) {
        // Liste mit Alias erstellen und smarty übergeben
        $smarty->assign('alist', parent::getAliasList());
        $smarty->assign('ortlist', Ort::getOrtList());

        // Daten einsammeln und für Dialog bereitstellen :-)
        $data = a_display(array(
            // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id',   $this->id),
            new d_feld('vname',$this->vname,   EDIT,   516),
            new d_feld('name', $this->name,    EDIT,   517),
            new d_feld('aliases',$this->aliases, EDIT, 515),
            new d_feld('gtag', $this->gtag,    EDIT,   502,10000),
            new d_feld('gort', $this->gort,    EDIT,   4014),
            new d_feld('ttag', $this->ttag,    EDIT,   509,10000),
            new d_feld('tort', $this->tort,    EDIT,   4014),
            new d_feld('strasse',$this->strasse,IEDIT, 510),
            new d_feld('wort', $this->wort,    IEDIT),
            new d_feld('plz',  $this->plz,     IEDIT),
            new d_feld('tel',  $this->tel,     IEDIT,  511,10002),
            new d_feld('mail', $this->mail,    IEDIT,  512),
            new d_feld('biogr',$this->biogr,   EDIT,   513),
            new d_feld('notiz',$this->notiz,   EDIT,   514),
            new d_feld('bereich',   null,      VIEW,   4013)));
        $smarty->assign('dialog', $data);
        $smarty->display('pers_dialog.tpl');
        $myauth->setAuthData('obj', serialize($this));
    } else {    // Status
        // Reinitialisierung muss vom aufrufenden Programm erledigt werden
        // Formular auswerten
        if(isset($_POST['vname'])) $this->vname = normtext($_POST['vname']);
        if(isset($_POST['name'])) {
            if(isValid($_POST['name'], NAMEN)) $this->name = normtext($_POST['name']);
            else {
                fehler(107);
                die();
            }
        }

        if (isset($_POST['aliases'])) $this->aliases = $_POST['aliases'];

        if(isset($_POST['gtag'])) {
            if($_POST['gtag']) {
                if(isValid($_POST['gtag'], DATUM)) $this->gtag = $_POST['gtag'];
                else fehler(103);
            } else $this->gtag = null;
        }

        if(isset($_POST['gort'])) {
            if($_POST['gort'] == 0) $this->gort = null; else $this->gort = $_POST['gort'];
        }

        if(isset($_POST['ttag'])) {
            if($_POST['ttag']) {
                if(isValid($_POST['ttag'], DATUM)) $this->ttag = $_POST['ttag'];
                else fehler(103);
            } else $this->ttag = null;
        }

        if(isset($_POST['tort'])) {
            if($_POST['tort'] == 0) $this->tort = null; else $this->tort = $_POST['tort'];
        }

        if($this->tort OR $this->ttag) {
            // Tote haben keine Postanschrift
            $this->strasse = null;
            $this->wort = null;
            $this->plz = null;
            $this->mail = null;
            $this->tel = null;
        } else {
            if(isset($_POST['strasse'])) $this->strasse = normtext($_POST['strasse']);
            if(isset($_POST['wort'])) {
                if($_POST['wort'] == 0) $this->wort = null; else $this->wort = $_POST['wort'];
            }

            if(isset($_POST['plz'])) {
                if($_POST['plz']){
                    if(isValid($_POST['plz'], '[\d]{3,5}')) $this->plz = $_POST['plz'];
                    else fehler(104);
                } else $this->plz = null;
            }

            if(isset($_POST['tel'])) {
                if($_POST['tel']) {
                    if(isValid($_POST['tel'], TELNR)) $this->tel = $_POST['tel'];
                    else fehler(105);
                } else $this->tel = null;
            }

            if(isset($_POST['mail'])) {
                if($_POST['mail']) {
                    if(isValid($_POST['mail'], EMAIL)) $this->mail = $_POST['mail'];
                    else fehler(106);
                } else $this->mail = null;
            }
        }

        if(isset($_POST['biogr'])) $this->biogr = normtext($_POST['biogr']);
        if(isset($_POST['notiz'])) $this->notiz = normtext($_POST['notiz']);
    }
}

function newPerson($stat) {
/****************************************************************
Aufgabe: Neuanlage einer Person
Aufruf: false   für Erstaufruf
        true    Verarbeitung nach Formular
****************************************************************/
    global $db;
    $types = array('text','date','integer','date','integer'/*tort*/,'text',
            'text','integer'/*wort*/,'text','text','text','integer'/*aliases*/,
            'integer'/*bild*/, /*alias->id*/'integer','text','text');

    if ($stat == false) {
        // begin TRANSACTION anlage person
        $db->beginTransaction('newPerson'); isDBError($db);
        // neue id besorgen
        $data =& $db->extended->getRow("SELECT nextval('p_alias_id_seq');");
        IsDbError($data);
        $this->id = $data['nextval'];
        $this->editPerson(false);
    } else {
        $this->editPerson(true);
        $data = $db->extended->autoExecute('p_person', $this,
                    MDB2_AUTOQUERY_INSERT, null, $types);
        IsDbError($data);
        $db->commit('newPerson'); isDBError($db);
        // ende TRANSACTION
    }
}

function setPerson(){
/****************************************************************
Aufgabe: schreibt das Obj. via Update in die DB zurück
 Return: 0  alles ok
         1  leerer Datensatz
   Anm.:
****************************************************************/
    global $db;
    $types = array('text','date','integer','date','integer'/*tort*/,'text',
            'text','integer'/*wort*/,'text','text','text','integer'/*aliases*/,
            'integer'/*bild*/, /*alias->id*/'integer','text','text');

    if (!$this->id) return 1;   // Abbruch weil leerer Datensatz

    $data = $db->extended->autoExecute('p_person', $this,
        MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
    IsDbError($data);
    return 0;
}

function delPerson($nr) {
    global $db;
    /* Es exisitiert an dieser Stelle noch keine Abfrage, ob der Datensatz ver-
    knüpft ist oder problemlos gelöscht werden kann */
    $data = $db->extended->autoExecute('p_person', null,
        MDB2_AUTOQUERY_DELETE, 'id = '.$db->quote($nr, 'integer'));
    IsDbError($data);
}

function searchPerson($s) {
/****************************************************************
Aufgabe: Simple Suche nach Personen über Namen
 Aufruf: string
 Return: 0   gibt ein Array der gefunden Personen-ID's zurück
         1   nichts gefunden
   Anm.: statisch
****************************************************************/
    global $db;
    $erg = array();
    $s = "%".$s."%";        // Suche nach Teilstring
    $sql ='
        SELECT p_person.id
        FROM p_person
        WHERE
         (p_person.name ILIKE ?) OR
         (p_person.vname ILIKE ?)
        ORDER BY p_person.name ASC;';
    // DISTINCT wieder entfernt -> array_unique()
    /* Beispiel für die UNION-Klausel
    SELECT verleihe.name
    FROM verleihe
    WHERE verleihe.name LIKE 'W%'
    UNION
    SELECT schauspieler.name
    FROM schauspieler
    WHERE schauspieler.name LIKE 'W%';
    */
    $data =&$db->extended->getAll($sql, null, array($s,$s));
    IsDbError($data);
    foreach($data as $wert) $erg[] = (int)$wert['id'];

    if ($erg) return array_unique($erg);     // id's der gefundenen Personen
    else return 1;
}

function view() {
/****************************************************************
Aufgabe: Anzeige eines Datensatzes, Einstellen der Rechteparameter
         Auflösen von Listen und holen der Strings aus der Tabelle
Aufruf:  DYNA
Return:  void
Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
****************************************************************/
    global $smarty;
    // Zuweisungen und ausgabe an pers_dat.tpl
    $data = a_display(array(
    // name,inhalt optional-> $rechte,$label,$tooltip,valString
        new d_feld('id',     $this->id,                 VIEW),          // pid
        new d_feld('vname',  $this->vname,              VIEW),          // vname
        new d_feld('name',   $this->name,               VIEW),          // name
        // alias (Liste)
        new d_feld('aliases',self::getAlias($this->aliases), VIEW, 515),
        new d_feld('gtag',   $this->gtag,               VIEW,   502),   // Geburtstag
        new d_feld('gort',   Ort::getOrt($this->gort),  VIEW,  4014),   // GebOrt
        new d_feld('ttag',   $this->ttag,               VIEW,   509),   // Todestag
        new d_feld('tort',   Ort::getOrt($this->tort),  VIEW,  4014),   // Sterbeort
        new d_feld('strasse',$this->strasse,            IVIEW,  510),   // Anschrift
        new d_feld('wort',   Ort::getOrt($this->wort),  IVIEW),         // Wohnort
        new d_feld('plz',    $this->plz,                IVIEW),         // PLZ
        new d_feld('tel',    $this->tel,                IVIEW,  511),   // Telefonnr.
        new d_feld('mail',   $this->mail,               IVIEW,  512),   // email
        new d_feld('biogr',  changetext($this->biogr),  VIEW,   513),   // Biografie
        new d_feld('notiz',  changetext($this->notiz),  IVIEW,  514),   // Notiz
        new d_feld('bild',   $this->bild,               VIEW),
        new d_feld('edit',   null,                      EDIT),          // edit-Button
        new d_feld('del',    null,                      DELE),          // Lösch-Button
    ));

    $smarty->assign('dialog', $data, 'nocache');
    $smarty->display('pers_dat.tpl');
}
}   // end Personen-klasse


/** SCHNIPSEL
    $sql = 'SELECT ______ FROM ______ WHERE id = ?;';
    $data = $db->extended->getRow($sql, null, array(______), TYP);
    IsDbError($data);
**/
?>
