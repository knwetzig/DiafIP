<?php
/**************************************************************

    Personen-Klasse V2

$Rev$
$Author$
$Date$
$URL$

ToDo:
    - name.add() vervollständigen. (Baustelle)
    - $types in set()/add() und edit anpassen
    - add und edit bearbeiten und mit Template abgleichen
***********************************************************/

/**===========================================================
                                NAMEN
=========================================================== **/
interface iPName extends iEntity {
    function getName();
    static function getNameList();	// Listet alle Aliasname (nicht Personen)
    function add($stat = null);
    function edit($stat = null);
    function del();
    function view();                // Liefert die Daten für die Ausgabe
}

class PName extends Entity implements iPName {
    const
        TYPENAME = 'text,text',
        GETDATA = 'SELECT vname, nname FROM p_namen WHERE id = ?;',
        GETNAMLI =
            'SELECT id, vname, nname FROM ONLY p_namen
             ORDER BY nname, vname ASC;',
        SEARCH =
            'SELECT id,bereich FROM p_namen
             WHERE (nname ILIKE ?) OR (vname ILIKE ?)
             ORDER BY nname,vname,id LIMIT ? OFFSET ?;';

    function __construct($nr = null) {
        parent::__construct($nr);
        $this->content['vname'] = '-';
        $this->content['nname'] = '';
        if(isset($nr) AND is_numeric($nr)) self::get(intval($nr));
    }

    protected function get($nr) {
    // Diese Funktion initialisiert das Objekt
        $db =& MDB2::singleton();

        $data = $db->extended->getRow(self::GETDATA, null, $nr, 'integer');
        IsDbError($data);
        if($data) :
            $this->content['vname'] = $data['vname'];
            $this->content['nname'] = $data['nname'];
        else :
            feedback(4,'error');
            exit(4);
        endif;
    }

    function add($stat = null) {
    /**********************************************************
    Aufgabe: Neuanlage eines Namens
    Aufruf: false   für Erstaufruf
            true    Verarbeitung nach Formular
    **********************************************************
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db =& MDB2::singleton();

        if (empty($status)) :
            // begin TRANSACTION anlage name
            $db->beginTransaction('newName'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('entity_id_seq');");
            IsDbError($data);
            $this->content['id'] = $data;
            $this->content['bereich'] = 'N';	// Namen
            $this->edit();
        else :
            $this->edit(true);
// kurze Denkpause, damits dann schneller geht
// Ziel: EIN array für den DB-Export, dazu ein type-array

            foreach($this as $key => $wert) $data[$key] = $wert;

            $erg = $db->extended->autoExecute('p_namen',$data,MDB2_AUTOQUERY_INSERT,
                null, list2array(parent.TYPEENTITY.parent::TYPENAME.self::TYPEPERSON));
            IsDbError($erg);
            $db->commit('newName'); IsDbError($db);
            // ende TRANSACTION
        endif;
*/
    }

    function edit($stat = null) {
    /**********************************************************
    Aufgabe:    Obj ändern
    Aufruf:     false   Formularanforderung
                true    Auswertung
    Return:     Fehlercode
    Anm.:       Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehler-
                haften Eingaben.
    **********************************************************
        global $myauth, $smarty;

        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if(empty($status)) :
            // Daten einsammeln und für Dialog bereitstellen :-)
            $data = array(
                // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('id',    $this->content['id']),
                new d_feld('vname', $this->content['vname'], EDIT, 516),
                new d_feld('nname', $this->content['nname'], EDIT, 517),
                new d_feld('notiz', $this->content['notiz'], EDIT, 514),
                new d_feld('kopf', null, VIEW, 4013));
            $smarty->assign('dialog', a_display($data));
            $smarty->display('person_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        else :	    // Status
            // Reinitialisierung muss vom aufrufenden Programm erledigt werden
            // Formular auswerten
	    try {
            if(isset($_POST['vname']))
                if (empty($_POST['vname'])) $this->content['vname'] = '-';
                else $this->content['vname'] = $_POST['vname'];

            if(isset($_POST['nname'])) {
                if(!empty($_POST['nname'])) $this->content['nname'] = $_POST['nname'];
                else throw new Exception(null, 107);
            }

            if(isset($_POST['notiz'])) $this->content['notiz'] = $_POST['notiz'];
	    }

	    catch (Exception $e) {
		feedback($e->getcode(), 'error');
	    }
            $this->setSignum();		// Bearbeiter und Zeit setzen
        endif;
*/
    }

    function del() {}

    protected function fiVname() {
    /**********************************************************
    * Aufgabe: Ausfiltern des default-Wertes von Vorname
    *  Return: string (null | vname)
    **********************************************************/
        if ($this->content['vname'] === '-') return; else return $this->content['vname'].'&nbsp';
    }

    static function search($s) {
    /**********************************************************
    Aufgabe:    Sucht in Vor- und Nachnamen (nicht Literal)
    Aufruf:     $s = Suchmuster
    Return:     array(id) oder null (Namen und Personen)
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;
        $db =& MDB2::singleton();
        $max = $db->extended->getOne('SELECT COUNT(*) FROM p_namen WHERE del = false;','integer');
        IsDbError($max);
        $limit = null;
        $offset = null;

       // Suche nach Teilstring
        $erg = array();
        $s = "%".$s."%";

        $data =&$db->extended->getAll(
            self::SEARCH, array('integer','text'), array($s,$s,$limit,$offset));
        IsDbError($data);

        if ($data) return $data; else return 102;
    }

    function getName() {
    /**********************************************************
    Aufgabe: Liefert den zusammngesetzten und verlinkten Namen zurück
    Return: string
    **********************************************************/
        if(empty($this->content['id'])) return;
        $data = self::fiVname().$this->content['nname'];
        return '<a href="index.php?'.$this->content['bereich'].'='.$this->content['id'].'">'.$data.'</a>';
    }

    static function getNameList() {
    /**********************************************************
    Aufgabe:    Liefert die Namensliste für Drop-Down-Menü
    Return:     array(id, vname+name)
    **********************************************************/
        $db =& MDB2::singleton();
        $data = $db->extended->getAll(
            self::GETNAMLI, array('integer','text','text'));
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

    function view() {
        $data = parent::view();
        $data[] = new d_feld('pname', self::getName(), VIEW);
        return $data;
    }
}

/** ===========================================================
                                PERSONEN
=========================================================== **/
interface iPerson extends iPName {
    static function getPersList();  // Listet alle Personen (ohne Aliasnamen)
    function getAliases();          // gibt ein Array der Namen zurück
}

class Person extends PName implements iPerson {
/**************************************************************
- Variablennamen, die sich auf die db-Tabelle beziehen müssen identisch
  mit den Spaltennamen sein, damit die Iteration gelingen kann.
**************************************************************/
    const
        TYPEPERSON = 'date,integer,date,integer,text,text,integer,text,text,text',
        GETDATA	=
            'SELECT gtag, gort, ttag, tort, strasse, plz, wort, tel, mail, aliases
             FROM p_person2 WHERE id = ?;',
        GETPERLI =
            'SELECT id, vname, nname FROM ONLY p_person2
             ORDER BY nname, vname ASC;',
        // Casting-Liste
        GETCALI   =
            'SELECT fid, tid FROM f_cast WHERE pid= ? ORDER BY fid;',
        IFDOUBLE  =
            'SELECT id FROM p_person2
			 WHERE gtag = ? AND vname = ? AND nname = ?;';

    function __construct($nr = null) {
        parent::__construct($nr);
if(!empty($nr) AND $nr > 1000) :
  feedback('Datensatz #'.$nr.' wird initialisiert');
  _v($this);
  return;
endif;
        $this->content['gtag'] = '0001-01-01'; // Geburtstag
        $this->content['gort'] = null;      // Geburtsort
        $this->content['ttag'] = null;      // Todestag
        $this->content['tort'] = null;      // Sterbeort
        $this->content['strasse'] = null;   // + HNr. und Adresszusätze
        $this->content['plz'] = null;       // PLZ des Wohnortes
        $this->content['wort'] = null;      // Wohnort (Ort, land))
        $this->content['tel'] = null;       // Telefonnummer
        $this->content['mail'] = null;      // mailadresse
        $this->content['aliases'] = null;
        if (isset($nr) AND is_numeric($nr)) self::get(intval($nr));
    }

    protected function get($nr) {
    /**********************************************************
    * Aufgabe: Datensatz holen, in @self schreiben
    *  Aufruf: nr  ID des Personendatensatzes (NOT STATIC)
    *  Return: none
    **********************************************************/
        $db =& MDB2::singleton();

        $data = $db->extended->getRow(self::GETDATA,null, $nr);
        IsDbError($data);
        // Ergebnis -> Objekt schreiben
        if($data) :
            foreach($data as $key => $val) :
                $this->content[$key] = $val;
            endforeach;
            if($this->content['aliases']) :
                $this->content['aliases'] = list2array($this->content['aliases']);
            endif;
        else :
            feedback(4,'error');
            exit(4);
        endif;
    }

    private function fiGtag() {
    /**********************************************************
    * Aufgabe: Geburtstagsfilter
    *  Return: (int Geburtstag | null)
    **********************************************************/
        if (($this->content['gtag'] === '0001-01-01') OR ($this->content['gtag'] === '01.01.0001'))
            return ; else return $this->content['gtag'];
    }

    private function ifDouble() {
    /**********************************************************
    * Aufgabe: Ermitteln ob gleiche Person schon existiert
    *  Return:
    **********************************************************/
        $db =& MDB2::singleton();
        $data = $db->extended->getRow(
            self::SQL_ifDouble, null, array($this->gtag, $this->vname, $this->nname));
        return $data['id'];
    }

    static function getPersList() {
    /**********************************************************
    Aufgabe:    Liefert die Namensliste für Drop-Down-Menü
    Return:     array(id, vname+name)
    **********************************************************/
        $db =& MDB2::singleton();
        $data = $db->extended->getAll(
            self::GETPERLI, array('integer','text','text'));
        IsDbError($data);

        $alist = array(d_feld::getString(0));       // kein Eintrag
        foreach($data as $val) :
            if ($val['vname'] === '-') :
                $alist[$val['id']] = $val['nname'];
            else :
                $alist[$val['id']] = $val['vname'].'&nbsp;'.$val['nname'];
            endif;
        endforeach;
        return $alist;
    }

    function getAliases() {
    /**********************************************************
    Aufgabe: Ermitteln der/des Aliasnamen
    Rückgabe: Liste der verlinkten Namen. Der Link zeigt dabei auf die Person und nicht
              den Namenseintrag
    Return: array(string)
    **********************************************************/
        if(is_array($this->content['aliases'])) :
            $data = array();
            foreach($this->content['aliases'] as $val) :
                $e = new PName(intval($val));
                $data[] = $e->fiVname().$e->content['nname'];
//                $data[] = '<a href="index.php?'.$this->content['bereich'].'='.$this->content['id'].'">'.$e->fiVname().$e->content['nname'].'</a>';
            endforeach;
            return $data;
        endif;
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

    public function add($stat = null) {
    /**********************************************************
    Aufgabe: Neuanlage einer Person
    Aufruf: false   für Erstaufruf
            true    Verarbeitung nach Formular
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

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

    public function edit($stat = null) {
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

    private function set(){
    /**********************************************************
    Aufgabe: schreibt das Obj. via Update in die DB zurück
             wird bei add/edit/del gebraucht
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
    /**********************************************************
    Aufgabe: Schaltet das Löschflag um und schreibt das gesamte Objekt in die DB
    Anm:     Alternativ kann man diese Funktion nutzen um das Element wieder aus dem
             Papierkorb zu holen.
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), DELE)) return 2;
        self::setDel();
        self::set();
    }

    function view() {
    /****************************************************************
    Aufgabe: Anzeige eines Datensatzes, Einstellen der Rechteparameter
            Auflösen von Listen und holen der Strings aus der Tabelle
            Zuweisungen und ausgabe an pers_dat.tpl
    Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
    ****************************************************************/
        global $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

        $data = parent::view();
        $data[] = new d_feld('aliases', $this->getAliases(), VIEW, 515);
        $data[] = new d_feld('gtag',   $this->fiGtag(), VIEW, 502);
        $data[] = new d_feld('gort',   Ort::getOrt($this->content['gort']),VIEW,4014);
        $data[] = new d_feld('ttag',   $this->content['ttag'], VIEW, 509);
        $data[] = new d_feld('tort',   Ort::getOrt($this->content['tort']),VIEW,4014);
        $data[] = new d_feld('strasse',$this->content['strasse'], IVIEW,510);
        $data[] = new d_feld('wort',   Ort::getOrt($this->content['wort']),IVIEW);
        $data[] = new d_feld('plz',    $this->content['plz'],IVIEW);
        $data[] = new d_feld('tel',    $this->content['tel'],IVIEW,511);
        $data[] = new d_feld('mail',   $this->content['mail'],IVIEW,512);
        $data[] = new d_feld('castLi', $this->getCastList(),VIEW);// Verw. Film
        return $data;
    }

}   // end Personen-klasse
?>
