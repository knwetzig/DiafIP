<?php
/**************************************************************

    Personen-Klasse V2

$Rev$
$Author$
$Date$
$URL$

ToDo:
    Altlastenbefreiung
**************************************************************/

/** ===========================================================
                                NAMEN
=========================================================== **/
interface iName {
    static function getNameList();	// Listet alle Aliasname (nicht Personen)
    function add($status = null);
    function edit($status = null);
    static function search($s);
    function lview();
    function view();
}

class Name extends entity implements iName {
    const
	GETDATA = 'SELECT * FROM ONLY p_namen WHERE id = ?;',
	SEARCH =  'SELECT id FROM ONLY p_namen
	    WHERE (nname ILIKE ?) OR (vname ILIKE ?) ORDER BY nname ASC, vname ASC;';

    protected
	$vname	= '-',
        $nname  = '';

    function __construct($nr = null) {
            if(isset($nr) AND is_int($nr)) self::get($nr);
    }

    protected function get($nr) {
    // Diese Funktion initilisiert das Objekt
        $db =& MDB2::singleton();
        parent::get($nr);
        $data = $db->extended->getRow(self::GETDATA, null, $nr, 'integer');
        IsDbError($data);
        $this->vname = $data['vname'];
        $this->nname = $data['nname'];
    }

    function add($status = null) {
    /**********************************************************
    Aufgabe: Neuanlage eines Namens
    Aufruf: false   für Erstaufruf
            true    Verarbeitung nach Formular
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db =& MDB2::singleton();
        $types  = array(	// Reihenfolge einhalten!
	  'text',         // vname
	  'text',         // name
	  'integer',      // id
	  'text',	// bereich
	  'text',         // Beschreibung
	  'array',	// bild
	  'text',         // notiz
	  'boolean',	// isvalid
	  'boolean',	// delete
	  'integer',	// Zeitstempel
	  'integer'       // uid des bearbeiters
        );

        if (empty($status)) :
            // begin TRANSACTION anlage name
            $db->beginTransaction('newName'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('entity_id_seq');");
            IsDbError($data);
            $this->id = $data;
            $this->bereich = 'N';	// Namen
            $this->edit();
        else :
            $this->edit(true);
            foreach($this as $key => $wert) $data[$key] = $wert;
            $erg = $db->extended->autoExecute('p_namen', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);
            $db->commit('newName'); IsDbError($db);
            // ende TRANSACTION
        endif;
    }

    function edit($status = null) {
    /**********************************************************
    Aufgabe:    Obj ändern
    Aufruf:     false   Formularanforderung
                true    Auswertung
    Return:     Fehlercode
    Anm.:       Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehler-
                haften Eingaben.
    **********************************************************/
        global $myauth, $smarty;

        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if(empty($status)) :
            // Daten einsammeln und für Dialog bereitstellen :-)
            $data = array(
                // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('id',    $this->id),
                new d_feld('vname', $this->vname, EDIT,   516),
                new d_feld('nname', $this->nname, EDIT,   517),
                new d_feld('notiz', $this->notiz, EDIT,   514),
                new d_feld('kopf',  null,      	  VIEW,   4013));
            $smarty->assign('dialog', a_display($data));
            $smarty->display('person_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        else :	    // Status
            // Reinitialisierung muss vom aufrufenden Programm erledigt werden
            // Formular auswerten
	    try {

		if(isset($_POST['vname']))
		    if (empty($_POST['vname'])) $this->vname = '-';
			else $this->vname = $_POST['vname'];

		if(isset($_POST['nname'])) {
		    if(!empty($_POST['mname'])) $this->nname = $_POST['nname'];
		    else throw new Exception(null, 107);
		}

		if(isset($_POST['notiz'])) $this->notiz = $_POST['notiz'];
	    }

	    catch (Exception $e) {
		feedback($e->getcode(), 'error');
	    }
            $this->setSignum();		// Bearbeiter und Zeit setzen
        endif;
    }

    protected function fiVname() {
    /**********************************************************
    * Aufgabe: Ausfiltern des default-Wertes von Vorname
    *  Return: string (null | vname)
    **********************************************************/
        if ($this->vname === '-') return; else return $this->vname.' ';
    }

    static function search($s) {
    /**********************************************************
    Aufgabe:    Sucht in Vor- und Nachnamen (nicht Literal)
    Aufruf:     $s = Suchmuster
    Return:     array(id) oder null
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $erg = array();
        $s = "%".$s."%";        // Suche nach Teilstring
        $db =& MDB2::singleton();

        $data =&$db->extended->getAll(self::SEARCH, 'integer', array($s,$s));
        IsDbError($data);
        foreach($data as $wert) $erg[] = $wert['id'];

        if ($erg) return array_unique($erg);     // id's der gefundenen Namen
        else return 1;
    }

    public function getName() {
    /**********************************************************
    *  Aufgabe: Liefert den zusammngesetzten Namen zurück
    *   Return: bool
    **********************************************************/
        if(empty($this->id)) return;
        $data = $this->fiVname().$this->nname;
        return '<a href="index.php?aktion=view&id='.$this->id.'">'.$data.'</a>';
    }

    static function getNameList() {
    /**********************************************************
    Aufgabe:    Liefert die Namensliste für Drop-Down-Menü
    Return:     array(id, vname+name)
    **********************************************************/
        $db =& MDB2::singleton();
        $sql = 'SELECT id, vname, nname FROM p_namen ORDER BY nname, vname ASC;';
        $data = $db->extended->getAll($sql, array('integer','text','text'));
        IsDbError($data);

        $alist = array(d_feld::getString(0));		// kein Eintrag
        foreach($data as $val) :
	    if ($val['vname'] === '-') :
		$alist[$val['id']] = $val['nname'];
	    else :
		$alist[$val['id']] = $val['vname'].' '.$val['nname'];
	    endif;
        endforeach;
        return $alist;
    }

    function lview() {
	$data = parent::lview();
	$data[] = new d_feld('vname',	$this->vname,	VIEW);
	$data[] = new d_feld('nname',	$this->nname,	VIEW);
	return $data;
    }

    function view() {
	$data = parent::view();
	$data[] = new d_feld('vname',	$this->vname,	VIEW);
	$data[] = new d_feld('nname',	$this->nname,	VIEW);
	return $data;
    }
}

/** ===========================================================
                                PERSONEN
=========================================================== **/
interface iPerson {
    public function edit($stat);
    public function add($stat);
    public function lview();
    public function view();
}

class Person extends Name implements iPerson {
/**************************************************************
func: __construct($)
      get($!)     // holt db-felder -> this
      refresh
    getCastLi()
      set()       // schreibt objekt.person -> db
    ::search($!)  // gibt array der ID's zurück

- Variablennamen, die sich auf die db-Tabelle beziehen müssen identisch
  mit den Spaltennamen sein, damit die Iteration gelingen kann.

**************************************************************/
    protected
        $gtag   = null,       // Geburtstag
        $gort   = null,       // Geburtsstadt
        $ttag   = null,       // Todestag
        $tort   = null,       // Sterbeort
        $strasse = null,      // Strasse + HNr. und Adresszusätze
        $plz    = null,       // PLZ des Wohnortes
        $wort   = null,       // Wohnort (Ort, land))
        $tel    = null,       // Telefonnummer
        $mail   = null,       // mailadresse;
        $aliases = array();

    const
      GETDATA	= 'SELECT * FROM p_person WHERE id = ?;',
      ISLINK    = 'SELECT COUNT(*) FROM f_cast WHERE pid = ?;',
      // Casting-Liste
      GETCALI   = 'SELECT fid, tid FROM f_cast WHERE pid= ? ORDER BY fid;',
      IFDOUBLE  = 'SELECT id FROM p_person_V2
			  WHERE gtag = ? AND vname = ? AND name = ?;';

    function __construct($nr = NULL) {
        if (isset($nr) AND is_int($nr)) self::get($nr);
    }

    protected function get($nr) {
    /**********************************************************
    * Aufgabe: Datensatz holen, in @self schreiben
    *  Aufruf: nr  ID des Personendatensatzes (NOT STATIC)
    *  Return: none
    **********************************************************/
        $db =& MDB2::singleton();
        parent::get($nr);
        $data = $db->extended->getRow(self::GETDATA, null, $nr);
        IsDbError($data);
        // Ergebnis -> Objekt schreiben
        foreach($this as $key => &$wert) $wert = $data[$key];
        unset($wert);
    }

    private function fiGtag() {
    /**********************************************************
    * Aufgabe: Geburtstagsfilter
    *  Return: (int Geburtstag | null)
    **********************************************************/
        if (($this->gtag === '0001-01-01') OR ($this->gtag === '01.01.0001'))
            return ; else return $this->gtag;
    }

    protected function ifDouble() {
    /**********************************************************
    * Aufgabe:
    *  Return:
    **********************************************************/
        $db =& MDB2::singleton();
        $data = $db->extended->getRow(
            self::SQL_ifDouble, null, array($this->gtag, $this->vname, $this->name));
        return $data['id'];
    }

    final protected function getCastList() {
    /**********************************************************
    *  Aufgabe: gibt die Besetzungsliste für diesen Eintrag aus
    *   Return: array(vname, name, tid, pid, job)
    **********************************************************/
        $db =& MDB2::singleton();
        if (empty($this->id)) return;
        $data = $db->extended->getALL(
            self::GETCALI, null, $this->id, 'integer');
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
    /**********************************************************
    * Aufgabe: Prüft ob der Datensatz verknüpft ist
    *  Return: 0 = frei / Nr = Anzahl
    **********************************************************/
        $db =& MDB2::singleton();
        // Prüfkandidaten: f_cast.pid / ...?
        $data = $db->extended->getRow(self::ISLINK, null, $this->id);
        IsDbError($data);
        return $data['count'];
    }

    public function edit($stat) {
    /****************************************************************
    Aufgabe:    Obj ändern
    Aufruf:     false   Formularanforderung
                true    Auswertung
    Return:     Fehlercode
    Anm.:       Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehler-
                haften Eingaben.
    ****************************************************************
        global $myauth, $smarty;
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
                new d_feld('gtag', $this->gtag,    EDIT,   502, 10000),
                new d_feld('gort', $this->gort,    EDIT,   4014),
                new d_feld('ttag', $this->ttag,    EDIT,   509, 10000),
                new d_feld('tort', $this->tort,    EDIT,   4014/*,10005*),
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


/** ====== Neue Fehlerbehandlung ==== **


            if(isset($_POST['vname']))
                if (empty($_POST['vname'])) $this->vname = '-';
                    else $this->vname = $_POST['vname'];

            if(isset($_POST['name'])) {
                if(!empty($_POST['name'])) $this->name = $_POST['name'];
                else feedback(107, 'error');
            }

            if (isset($_POST['aliases'])) $this->aliases = $_POST['aliases'];

            if(isset($_POST['gtag'])) {
                if($_POST['gtag']) {
                    if(isValid($_POST['gtag'], DATUM)) $this->gtag = $_POST['gtag'];
                    else feedback(103, 'warng');
                } else $this->gtag = '0001-01-01';
            }

            if(isset($_POST['gort'])) {
                if($_POST['gort'] == 0) $this->gort = null; else $this->gort = $_POST['gort'];
            }

            if(isset($_POST['ttag'])) {
                if($_POST['ttag']) {
                    if(isValid($_POST['ttag'], DATUM)) $this->ttag = $_POST['ttag'];
                    else feedback(103, 'warng');
                } else $this->ttag = null;
            }

            if(isset($_POST['tort'])) {
                if($_POST['tort'] == 0) $this->tort = null; else $this->tort = $_POST['tort'];
            }

/* Testweise ISSUE #4
            if($this->tort OR $this->ttag) {
                // Tote haben keine Postanschrift
                $this->strasse = null;
                $this->wort = null;
                $this->plz = null;
                $this->mail = null;
                $this->tel = null;
            } else {
*
            if(!empty($_POST['strasse']))
                if (isValid($_POST['strasse'], NAMEN)) $this->strasse = $_POST['strasse']; else feedback(109, 'warng');

            if(isset($_POST['wort']))
                if($_POST['wort'] == 0) $this->wort = null; else $this->wort = intval($_POST['wort']);

            if(!empty($_POST['plz']))
                if(isValid($_POST['plz'], PLZ)) $this->plz = $_POST['plz'];
                else feedback(104, 'warng');
            else $this->plz = null;

            if(!empty($_POST['tel']))
                if(isValid($_POST['tel'], TELNR)) $this->tel = $_POST['tel'];
                else feedback(105, 'warng');
            else $this->tel = null;

            if(!empty($_POST['mail']))
                if(isValid($_POST['mail'], EMAIL)) $this->mail = $_POST['mail'];
                else feedback(106, 'warng');
            else $this->mail = null;

            if(isset($_POST['biogr'])) $this->biogr = $_POST['biogr'];
            if(isset($_POST['notiz'])) $this->notiz = $_POST['notiz'];
            $this->editfrom = $myauth->getAuthData('uid');
            $this->editdate = date('c', $_SERVER['REQUEST_TIME']);

            // doppelten Datensatz abfangen
            $number = self::ifDouble();
            if (!empty($number) AND $number != $this->id) feedback(128, 'error');
        }
*/
    }

    public function add($stat) {
    /**********************************************************
    Aufgabe: Neuanlage einer Person
    Aufruf: false   für Erstaufruf
            true    Verarbeitung nach Formular
    **********************************************************/
        global $myauth;
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
                'text',         // name (geerbt von Entity)
                'text',         // notiz (geerbt von Entity)
                'text'          // id
        );
        $db =& MDB2::singleton();

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

    private function set(){
    /**********************************************************
    Aufgabe: schreibt das Obj. via Update in die DB zurück
    Return: 0  alles ok
            4  leerer Datensatz
    **********************************************************/
        global $myauth;
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
        $db =& MDB2::singleton();
        foreach($this as $key => $wert) $data[$key] = $wert;

        $erg = $db->extended->autoExecute('p_person', $data,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->id, 'integer'), $types);
        IsDbError($erg);
        return 0;
    }

    function del() {
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), DELE)) return 2;
        /* Es exisitiert an dieser Stelle noch keine Abfrage, ob der Datensatz ver-
        knüpft ist oder problemlos gelöscht werden kann *

        if(self::isLinked()) feedback(10006, 'warng');
        $db =& MDB2::singleton();

        IsDbError($db->extended->autoExecute('p_person', null,
            MDB2_AUTOQUERY_DELETE, 'id = '.$db->quote($this->id, 'integer')));
        feedback(3, 'erfolg'); return 0;
    }

    function sview() {
    /****************************************************************
    * Aufgabe: Anzeige eines Datensatzes (Listenansicht
    ****************************************************************
        global $myauth, $smarty;
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
    ****************************************************************
        global $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $db =& MDB2::singleton();
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
            new d_feld('chdatum',   $this->editdate,        EDIT),
            new d_feld('chname',    $bearbeiter[0],         EDIT),
        ));

        $smarty->assign('dialog', $data, 'nocache');
        $smarty->display('pers_dat.tpl');
    }
**/
}   // end Personen-klasse
?>
