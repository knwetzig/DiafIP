<?php
/**************************************************************
Enthält alle Klassenbibliotheken zu Personendaten

$Rev$
$Author$
$Date$
$URL$

ToDo: Das Löschen von Personen ist noch auf die Papierkorb-Mechanik umzustellen.
        Siehe dazu auch f_main.class. Das Löschprivileg in der DB ist nach
        Fertigstellung zu entziehen.
        Die Person-Class ist mit einem Interface zu versehen.

***** (c) DIAF e.V. *******************************************/

interface iPerson {
    const
        SQL_getPersLi   = 'SELECT id, vname, name FROM p_person
                           ORDER BY name ASC;';

    public function getName();
    public static function getPersonLi();
    public function edit($stat);
    public function add($stat);
    public function set();
    public function del();
    public function search($s);
    public function sview();
    public function view();
}


/** =================================================================
                                ALIAS
================================================================= **/
class Alias {
    public
        $name   = null,
        $notiz  = null,
        $id     = null;     // ! Böser Trick - id muss immer zuletzt stehen

    function __construct($nr = null) {
            if(isset($nr)) self::get($nr);
    }

    protected function get($nr) {
    // Diese Funktion initilisiert das Objekt
        global $db;
        $this->id = $nr;
        $sql = 'SELECT name,notiz FROM ONLY p_alias WHERE id = ?;';
        $data = $db->extended->getRow($sql, null, $this->id, 'integer');
        IsDbError($data);
        $this->name = $data['name'];
        $this->notiz = $data['notiz'];
    }

    static function getAlias($nr) {
    // Diese Funktion gibt den Namen zurück
        global $db;
        if (empty($nr)) return null;
        $sql = 'SELECT name FROM ONLY p_alias WHERE id = ?;';
        $data = $db->extended->getRow($sql, null, $nr, 'integer');
        IsDbError($data);
        return $data['name'];
    }

    static function getAliasList() {
        global $db;
        $sql = 'SELECT id, name FROM ONLY p_alias;';
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
class Person extends Alias implements iPerson {
/**********************************************************
func: __construct($)
      get($!)     // holt db-felder -> this
      refresh
    getPersonLi()       // gibt die Liste aller Personen aus
    getCastLi()
      edit()
      add()
      set()       // schreibt objekt.person -> db
    del()       // löscht Personendatensatz
    ::search($!)  // gibt array der ID's zurück
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
        $bild   = null,       // id auf Bilddatenbank
        $editdate = null,
        $editfrom = null;

    const
        SQL_isLink      = 'SELECT COUNT(*) FROM f_cast WHERE pid = ?;',
        SQL_getCaLi     = 'SELECT fid, tid FROM f_cast WHERE pid= ? ORDER BY fid;',
        SQL_ifDouble    = 'SELECT id FROM p_person
                           WHERE gtag = ? AND vname = ? AND name = ?;';
    function __construct($nr = NULL) {
        if (isset($nr)) $this->get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    * Aufgabe: Datensatz holen, in @self schreiben
    *  Aufruf: nr  ID des Personendatensatzes (NOT STATIC)
    *  Return: none
    ****************************************************************/
        global $db;
        $sql = 'SELECT * FROM p_person WHERE id = ?;';
        $data = $db->extended->getRow($sql, null, $nr);
        IsDbError($data);
        // Ergebnis -> Objekt schreiben
        foreach($this as $key => &$wert) $wert = $data[$key];
        unset($wert);
        // -> Bildinitialisierung hinzufügen
    }

    public function getName() {
    /****************************************************************
    *  Aufgabe: Liefert den zusammngesetzten Namen zurück
    *   Return: bool
    ****************************************************************/
        if(empty($this->id)) return;
            $data = $this->fiVname().$this->name;
        return '<a href="index.php?aktion=view&id='.$this->id.'">'.$data.'</a>';
    }


    protected function fiGtag() {
    /****************************************************************
    * Aufgabe: Geburtstagsfilter
    *  Return: (int Geburtstag | null)
    ****************************************************************/
        if (($this->gtag === '0001-01-01') OR ($this->gtag === '01.01.0001'))
            return ; else return $this->gtag;
    }

    protected function fiVname() {
    /****************************************************************
    * Aufgabe: Ausfiltern des default-Wertes von Vorname
    *  Return: string (null | vname)
    ****************************************************************/
        if ($this->vname === '-') return; else return $this->vname.' ';
    }

    protected function ifDouble() {
    /****************************************************************
    * Aufgabe:
    *  Return:
    ****************************************************************/
        global $db;
        $data = $db->extended->getRow(
            self::SQL_ifDouble, null, array($this->gtag, $this->vname, $this->name));
        return $data['id'];
    }

    public static function getPersonLi() {
    /****************************************************************
    * Aufgabe: Personenliste
    *  Return: array([id] => vname+name)
    ****************************************************************/
        global $db;
        $list = $db->extended->getAll(self::SQL_getPersLi);
        IsDbError($list);
        $data = array();
        foreach($list as $wert) :
            if ($wert['vname'] !== '-') $data[$wert['id']] = $wert['vname'].'&nbsp;'.$wert['name']; else $data[$wert['id']] = $wert['name'];
        endforeach;
        return $data;
    }

    final protected function getCastList() {
    /****************************************************************
    *  Aufgabe: gibt die Besetzungsliste für diesen Eintrag aus
    *   Return: array(vname, name, tid, pid, job)
    ****************************************************************/
        global $db;
        if (empty($this->id)) return;
        $data = $db->extended->getALL(
            self::SQL_getCaLi, null, $this->id, 'integer');
        IsDbError($data);
        $f=array();

        // Übersetzung für die Tätigkeit und Namen holen
        foreach($data as $wert) :
            if(!Film::is_Del($wert['fid'])) :
                $g = array();
                $g['ftitel'] = Film::getTitel($wert['fid']);
                $g['job'] = d_feld::getString($wert['tid']);
                $f[] = $g;
            endif;
        endforeach;
        return $f;
    }

    protected function isLinked() {
    /****************************************************************
    * Aufgabe: Prüft ob der Datensatz verknüpft ist
    *  Return: 0 = frei / Nr = Anzahl
    ****************************************************************/
        global $db;
        // Prüfkandidaten: f_cast.pid / ...?
        $data = $db->extended->getRow(self::SQL_isLink, null, $this->id);
        IsDbError($data);
        return $data['count'];
    }

    function edit($stat) {
    /****************************************************************
    Aufgabe:    Obj ändern
    Aufruf:     false   Formularanforderung
                true    Auswertung
    Return:     Fehlercode
    Anm.:       Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehler-
                haften Eingaben.
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
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
                new d_feld('tort', $this->tort,    EDIT,   4014,10005),
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

            if(isset($_POST['vname']))
                if (empty($_POST['vname'])) $this->vname = '-';
                    else $this->vname = $_POST['vname'];

            if(isset($_POST['name'])) {
                if(!empty($_POST['name'])) $this->name = $_POST['name'];
                else fehler(107);
            }

            if (isset($_POST['aliases'])) $this->aliases = $_POST['aliases'];

            if(isset($_POST['gtag'])) {
                if($_POST['gtag']) {
                    if(isValid($_POST['gtag'], DATUM)) $this->gtag = $_POST['gtag'];
                    else warng(103);
                } else $this->gtag = '0001-01-01';
            }

            if(isset($_POST['gort'])) {
                if($_POST['gort'] == 0) $this->gort = null; else $this->gort = $_POST['gort'];
            }

            if(isset($_POST['ttag'])) {
                if($_POST['ttag']) {
                    if(isValid($_POST['ttag'], DATUM)) $this->ttag = $_POST['ttag'];
                    else warng(103);
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
                if(isset($_POST['strasse'])) $this->strasse = $_POST['strasse'];
                if(isset($_POST['wort'])) {
                    if($_POST['wort'] == 0) $this->wort = null; else $this->wort = $_POST['wort'];
                }

                if(isset($_POST['plz'])) {
                    if($_POST['plz']){
                        if(isValid($_POST['plz'], '[\d]{3,5}')) $this->plz = $_POST['plz'];
                        else warng(104);
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
                        else warng(106);
                    } else $this->mail = null;
                }
            }

            if(isset($_POST['biogr'])) $this->biogr = $_POST['biogr'];
            if(isset($_POST['notiz'])) $this->notiz = $_POST['notiz'];
            $this->editfrom = $myauth->getAuthData('uid');
            $this->editdate = date('c', $_SERVER['REQUEST_TIME']);

            // doppelten Datensatz abfangen
            $number = self::ifDouble();
            if (!empty($number) AND $number != $this->id) fehler(128);
        }
    }

    public function add($stat) {
    /****************************************************************
    Aufgabe: Neuanlage einer Person
    Aufruf: false   für Erstaufruf
            true    Verarbeitung nach Formular
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        $types  = array(
                'text',         // vname
                'date',         // gtag
                'integer',      // idx_gort
                'date',         // ttag
                'integer',      // idx_tort
                'text',         // str
                'text',         // plz (string!)
                'integer',      // wort
                'text',         // tel
                'text',         // mail
                'text',         // biogr
                'integer',      // aliases
                'integer',      // bild
                'timestamp',    // Zeitstempel
                'integer',      // uid des bearbeiters
                'text',         // name (geerbt von Alias)
                'text',         // notiz (geerbt von Alias)
                'text'          // id
        );

        if ($stat == false) {
            // begin TRANSACTION anlage person
            $db->beginTransaction('newPerson'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('id_seq');");
            IsDbError($data);
            $this->id = $data;
            $this->edit(false);
        } else {
            $this->edit(true);
            foreach($this as $key => $wert) $data[$key] = $wert;
            $erg = $db->extended->autoExecute('p_person', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);
            $db->commit('newPerson'); IsDbError($db);
            // ende TRANSACTION
        }
    }

    function set(){
    /****************************************************************
    Aufgabe: schreibt das Obj. via Update in die DB zurück
    Return: 0  alles ok
            4  leerer Datensatz
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->id) return 4;   // Abbruch weil leerer Datensatz

        $types = array(
                'text',         // vname
                'date',         // gtag
                'integer',      // gort
                'date',         // ttag
                'integer',      // tort
                'text',         // str
                'text',         // plz
                'integer',      // wort
                'text',         // tel
                'text',         // mail
                'text',         // biogr
                'integer',      // aliases
                'integer',      // bild
                'timestamp',    // Zeitstempel
                'integer',      // uid des bearbeiters
                'text',         // name -> Alias
                'text',         // notiz -> Alias
                'integer',      // id -> Alias
        );

        foreach($this as $key => $wert) $data[$key] = $wert;

        $erg = $db->extended->autoExecute('p_person', $data,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
        IsDbError($erg);
        return 0;
    }

    function del() {
        global $myauth, $db;
        if(!isBit($myauth->getAuthData('rechte'), DELE)) return 2;
        /* Es exisitiert an dieser Stelle noch keine Abfrage, ob der Datensatz ver-
        knüpft ist oder problemlos gelöscht werden kann */

        if(self::isLinked()) fehler(10006);

        IsDbError($db->extended->autoExecute('p_person', null,
            MDB2_AUTOQUERY_DELETE, 'id = '.$db->quote($this->id, 'integer')));
        erfolg(); return 0;
    }

    function search($s) {
    /****************************************************************
    Aufgabe: Simple Suche nach Personen über Namen
    Aufruf: string
    Return: 0   gibt ein Array der gefunden Personen-ID's zurück
            1   nichts gefunden
    Anm.: statisch
    ****************************************************************/
        global $db, $myauth;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $erg = array();
        $s = "%".$s."%";        // Suche nach Teilstring
        $sql ='
            SELECT p_person.id
            FROM p_person
            WHERE
            (p_person.name ILIKE ?) OR
            (p_person.vname ILIKE ?)
            ORDER BY p_person.name ASC, p_person.vname ASC;';
        // DISTINCT wieder entfernt -> array_unique()
        /* Beispiel für die UNION-Klausel
        SELECT verleihe.name FROM verleihe WHERE verleihe.name LIKE 'W%'
        UNION
        SELECT schauspieler.name FROM schauspielerWHERE schauspieler.name LIKE 'W%';
        */
        $data =&$db->extended->getAll($sql, null, array($s,$s));
        IsDbError($data);
        foreach($data as $wert) $erg[] = (int)$wert['id'];

        if ($erg) return array_unique($erg);     // id's der gefundenen Personen
        else return 1;
    }

    function sview() {
    /****************************************************************
    * Aufgabe: Anzeige eines Datensatzes (Listenansicht
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        // Zuweisungen und ausgabe an pers_dat.tpl

        $data = a_display(array(
        // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id',     $this->id,                 VIEW),          // pid
            new d_feld('vname',  $this->fiVname(),          VIEW),          // vname
            new d_feld('name',   $this->name,               VIEW),          // name
            // alias (Liste)
            new d_feld('aliases', parent::getAlias($this->aliases), VIEW, 515),
            new d_feld('gtag',   $this->fiGtag(),           VIEW,   502),   // Geburtstag
            new d_feld('gort',   Ort::getOrt($this->gort),  VIEW,  4014),   // GebOrt
            new d_feld('edit',   null,                      EDIT, null, 4013), // edit-Button
            new d_feld('del',    null,                      DELE, null, 4020), // Lösch-Button
        ));
        $smarty->assign('dialog', $data, 'nocache');
        $smarty->display('pers_ldat.tpl');
    }

    function view() {
    /****************************************************************
    Aufgabe: Anzeige eines Datensatzes, Einstellen der Rechteparameter
            Auflösen von Listen und holen der Strings aus der Tabelle
    Aufruf:  DYNA
    Return:  void
    Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
    ****************************************************************/
        global $db, $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        if(!empty($this->editfrom)) :
            $bearbeiter = $db->extended->getCol(
                'SELECT realname FROM s_auth WHERE uid = '.$this->editfrom.';');
            IsDbError($bearbeiter);
        else : $bearbeiter = null;
        endif;
        // Zuweisungen und ausgabe an pers_dat.tpl

        $data = a_display(array(
        // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id',     $this->id,                 VIEW),          // pid
            new d_feld('vname',  $this->fiVname(),          VIEW),          // vname
            new d_feld('name',   $this->name,               VIEW),          // name
            // alias (Liste)
            new d_feld('aliases', parent::getAlias($this->aliases), VIEW, 515),
            new d_feld('gtag',   $this->fiGtag(),           VIEW,   502),   // Geburtstag
            new d_feld('gort',   Ort::getOrt($this->gort),  VIEW,  4014),   // GebOrt
            new d_feld('ttag',   $this->ttag,               VIEW,   509),   // Todestag
            new d_feld('tort',   Ort::getOrt($this->tort),  VIEW,  4014),   // Sterbeort
            new d_feld('strasse',$this->strasse,            IVIEW,  510),   // Anschrift
            new d_feld('wort',   Ort::getOrt($this->wort),  IVIEW),         // Wohnort
            new d_feld('plz',    $this->plz,                IVIEW),         // PLZ
            new d_feld('tel',    $this->tel,                IVIEW,  511),   // Telefonnr.
            new d_feld('mail',   $this->mail,               IVIEW,  512),   // email
            new d_feld('biogr',  changetext($this->biogr),  VIEW,   513),   // Biografie
            new d_feld('castLi', $this->getCastList(),      VIEW),          // Verw. Film
            new d_feld('notiz',  changetext($this->notiz),  IVIEW,  514),   // Notiz
            new d_feld('bild',   $this->bild,               VIEW),
            new d_feld('edit',   null,                      EDIT, null, 4013), // edit-Button
            new d_feld('del',    null,                      DELE, null, 4020), // Lösch-Button
            new d_feld('chdatum',   $this->editdate),
            new d_feld('chname',    $bearbeiter[0]),
        ));

        $smarty->assign('dialog', $data, 'nocache');
        $smarty->display('pers_dat.tpl');
    }
}   // end Personen-klasse
?>
