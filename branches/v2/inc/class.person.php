<?php
/**************************************************************

    Personen-Klasse V2

$Rev$
$Author$
$Date$
$URL$

ToDo:
    - add und edit bearbeiten und mit Template abgleichen


<codesnippet>
            if($this->content['aliases']) :
                $this->content['aliases'] = list2array($this->content['aliases']);
            endif;
</codesnippet>
***********************************************************/

/**===========================================================
                                NAMEN
=========================================================== **/
interface iPName extends iEntity {
    function getPerson();           // Ermittelt die Person zum Aliasnamen
    function getName();
    static function getNameList();	// Listet alle unbenutzten Aliasnamen (nicht Personen)
    static function search($s);     // liefert die ID's+Bereich des Suchmusters
    function add($status = null);
    function edit($status = null);
    function save();
    function view();                // Liefert die Daten für die Ausgabe
}

class PName extends Entity implements iPName {
    const
        TYPENAME = 'text,text,',
        GETDATA = 'SELECT vname, nname FROM p_namen WHERE id = ?;',
        GETPERSON = 'SELECT id FROM p_person2 WHERE ? = ANY(aliases);',
        GETALIAS =
            'SELECT DISTINCT p_namen.id, p_namen.vname, p_namen.nname
             FROM ONLY p_namen,p_person2
             WHERE (p_namen.del = false) AND (p_namen.id = ANY(p_person2.aliases))
             ORDER BY p_namen.nname, p_namen.vname;',
        GETALLNAMES =
            'SELECT id,vname,nname FROM ONLY p_namen
             WHERE del = false
             ORDER BY nname,vname;',
        SEARCH =
            'SELECT id,bereich FROM p_namen
             WHERE (del = false) AND ((nname ILIKE ?) OR (vname ILIKE ?))
             ORDER BY nname,vname,id LIMIT ? OFFSET ?;';

    protected
        $alias = null;              // Verweis auf die Person die den Alias verwendet

    function __construct($nr = null) {
        parent::__construct($nr);
        $this->content['bereich'] = 'N';
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
            $this->alias = self::getPerson();
        else :
            feedback(4,'error');
            exit(4);
        endif;
    }

    function getPerson() {
    /**********************************************************
    Aufgabe: Ermittelt die Person zum Aliasnamen
    Return:  null : Es existiert keine Person, Datensatz frei zum löschen
             id     Zum Benutzer des Alias
    **********************************************************/
        $db =& MDB2::singleton();
        if($this->content['bereich'] === 'N') :
            $p = $db->extended->getOne(self::GETPERSON, 'integer', $this->content['id'], 'integer');
            IsDbError($p);
            return $p;
        endif;
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
        $types = list2array(self::TYPEENTITY.self::TYPENAME);

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
            IsDbError($db->extended->autoExecute(
                'p_namen',$this->content,MDB2_AUTOQUERY_INSERT, null, $types));
            $db->commit('newName'); IsDbError($db);
            // ende TRANSACTION
        endif;
    }

    public function edit($status = null) {
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
                new d_feld('kopf', null, VIEW, 4013),
                new d_feld('id',    $this->content['id']),
                new d_feld('vname', $this->content['vname'], EDIT, 516),
                new d_feld('nname', $this->content['nname'], EDIT, 517),
                new d_feld('notiz', $this->content['notiz'], EDIT, 514));
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

                if(isset($_POST['nname'])) :
                    if(!empty($_POST['nname'])) $this->content['nname'] = $_POST['nname'];
                    else throw new Exception(null, 107);
                endif;

                if(isset($_POST['notiz'])) $this->content['notiz'] = $_POST['notiz'];
            }

            catch (Exception $e) {
                feedback($e->getcode(), 'error');
                exit;
            }

            $this->setSignum();		// Bearbeiter und Zeit setzen
        endif;
    }

    public function save() {
    /**********************************************************
    Aufgabe: schreibt das Obj. via Update in die DB zurück
             wird bei add/edit/del gebraucht
    Return: 4 = leerer Datensatz
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->content['id']) return 4;   // Abbruch weil leerer Datensatz

        $db =& MDB2::singleton();
        $types = list2array(self::TYPEENTITY.self::TYPENAME);

        IsDbError($db->extended->autoExecute('p_namen', $this->content,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->content['id'], 'integer'), $types));
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

    protected function fiVname() {
    /**********************************************************
    * Aufgabe: Ausfiltern des default-Wertes von Vorname
    *  Return: string (null | vname)
    **********************************************************/
        if ($this->content['vname'] === '-') return;
            else return $this->content['vname'].'&nbsp;';
    }

    public function getName() {
    /**********************************************************
    Aufgabe: Liefert den zusammngesetzten und verlinkten Namen zurück
    Return: string
    **********************************************************/
        if(empty($this->content['id'])) return;
        $data = self::fiVname().$this->content['nname'];
        $i = $this->content['id'];
        if(!empty($this->alias)) $i = $this->alias;
        return '<a href="index.php?'.$this->content['bereich'].'='.$i.'">'.$data.'</a>';
    }

    static function getNameList() {
    /**********************************************************
    Aufgabe:    Liefert die Namensliste für Drop-Down-Menü
    Return:     array(id, vname+name)
    Anm.:       Vielleicht findet sich ja mal ein Held der die
                Datenbankabfrage optimiert und dieses recht
                komplizierte Konstrukt auflöst ;-)
    **********************************************************/
        function arrpack($arr) {
            $erg = array();
            foreach($arr as $val) :
                if ($val['vname'] === '-') :
                    $erg[$val['id']] = $val['nname'];
                else :
                    $erg[$val['id']] = $val['vname'].'&nbsp;'.$val['nname'];
                endif;
            endforeach;
            return $erg;
        }

        $db =& MDB2::singleton();
        $data = $db->extended->getAll(
            self::GETALIAS, array('integer','text','text'));
        IsDbError($data);
        $data = arrpack($data);
        $all = $db->extended->getAll(
            self::GETALLNAMES, array('integer','text','text'));
        IsDbError($all);
        $all = arrpack($all);
        $erg[0] = d_feld::getString(0);        // kein Eintrag
        $erg += array_diff($all,$data);
        return $erg;
    }

    public function view() {
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
            'SELECT id, vname, nname FROM ONLY p_person2 WHERE del = false
             ORDER BY nname, vname ASC;',
        // Casting-Liste
        GETCALI = 'SELECT fid, tid FROM f_cast WHERE pid= ? ORDER BY fid;',
        IFDOUBLE =
            'SELECT id FROM p_person2 WHERE gtag = ? AND vname = ? AND nname = ?;';

    function __construct($nr = null) {
        parent::__construct($nr);
        $this->content['bereich'] = 'P';
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
        $data = $db->extended->getOne(self::IFDOUBLE, null, array(
            $this->content['gtag'],
            $this->content['vname'],
            $this->content['nname']));
        IsDbError($data);
        return $data;
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

    public function getAliases() {
    /**********************************************************
    Aufgabe: Ermitteln der/des Aliasnamen
    Rückgabe: Liste der Namen.
    Return: array(string)
    **********************************************************/
        if($this->content['aliases']) :
            $data = array();
            foreach(list2array($this->content['aliases']) as $val) :
                $e = new PName(intval($val));
                $data[] = $e->fiVname().$e->content['nname'];
            endforeach;
            return $data;
        endif;
    }

    private function addAlias($nr) {
    /**********************************************************
    Aufgabe:    Fügt die ID eines PName-Objekts der Aliases-Liste hinzu
    Return:     void
    **********************************************************/
        if(!is_int($nr)) return;
        if(empty($this->content['aliases'])) :
            $this->content['aliases'] = '{'.$nr.'}';
        else :
            $this->content['aliases'] =
                substr_replace($this->content['aliases'], ','.$nr.'}',-1,1);
        endif;
    }

    final protected function getCastList() {
    /**********************************************************
    *  Aufgabe: gibt die Besetzungsliste für diesen Eintrag aus
    *   Return: array(vname, name, tid, pid, job)
    **********************************************************/
        $db =& MDB2::singleton();
        if (empty($this->content['id'])) return;
        $data = $db->extended->getALL(
            self::GETCALI, null, $this->content['id'], 'integer');
        IsDbError($data);

        // Übersetzung für die Tätigkeit und Namen holen
        $f=array();
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

    public function add($status = null) {
    /**********************************************************
    Aufgabe: Neuanlage einer Person
    Aufruf: false   für Erstaufruf
            true    Verarbeitung nach Formular
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db =& MDB2::singleton();
        $types = list2array(self::TYPEENTITY.self::TYPENAME.self::TYPEPERSON);

        if ($status == false) :
            // begin TRANSACTION anlage person
            $db->beginTransaction('newPerson'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('entity_id_seq');");
            IsDbError($data);
            $this->content['id'] = $data;
            $this->content['bereich'] = 'P';    // Namen
            $this->edit(false);
        else :
            $this->edit(true);
            IsDbError($db->extended->autoExecute('p_person2', $this->content,
                        MDB2_AUTOQUERY_INSERT, null, $types));
            $db->commit('newPerson'); IsDbError($db);
            // ende TRANSACTION
        endif;
    }

    public function edit($status = null) {
    /****************************************************************
    Aufgabe:    Obj ändern
    Aufruf:     false   Formularanforderung
                true    Auswertung
    Return:     Fehlercode
    Anm.:       Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehler-
                haften Eingaben.
    ****************************************************************/
        global $myauth, $smarty;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        if($status == false) :
            // Liste mit Alias erstellen und smarty übergeben
            if(self::IsInDB($this->content['id'], $this->content['bereich'])) :
                $smarty->assign('alist', parent::getNameList());
            endif;
            $smarty->assign('ortlist', Ort::getOrtList());

            // Daten einsammeln und für Dialog bereitstellen :-)
            $data = array(
                // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('kopf', null, VIEW, 4013),
                new d_feld('id',    $this->content['id']),
                new d_feld('vname', $this->content['vname'],EDIT,516),
                new d_feld('nname', $this->content['nname'],EDIT,517),
                new d_feld('aliases',$this->getAliases(),VIEW),
                new d_feld('addalias', null,EDIT,515),
                new d_feld('notiz', $this->content['notiz'],EDIT,514),
                new d_feld('isvalid', $this->content['isvalid'],SEDIT, 10009),
                new d_feld('gtag', $this->content['gtag'],EDIT,502, 10000),
                new d_feld('gort', $this->content['gort'],EDIT,4014),
                new d_feld('ttag', $this->content['ttag'],EDIT,509,10000),
                new d_feld('tort', $this->content['tort'],EDIT,4014),
                new d_feld('strasse',$this->content['strasse'],IEDIT,510),
                new d_feld('wort', $this->content['wort'],IEDIT),
                new d_feld('plz',  $this->content['plz'],IEDIT),
                new d_feld('tel',  $this->content['tel'],IEDIT,511,10002),
                new d_feld('mail', $this->content['mail'],IEDIT,512),
                new d_feld('biogr',$this->content['descr'],EDIT,513));
_vp($this->content);
            $smarty->assign('dialog', a_display($data));
            $smarty->display('person_dialog.tpl');
            $myauth->setAuthData('obj', serialize($this));
        else :    // Formular auswerten
            // Reinitialisierung muss vom aufrufenden Programm erledigt werden

            try {
                if(isset($_POST['vname'])) :
                    if (empty($_POST['vname'])) $this->content['vname'] = '-';
                    else $this->content['vname'] = $_POST['vname'];
                endif;

                if(isset($_POST['nname'])) :
                    if(!empty($_POST['nname'])) : $this->content['nname'] = $_POST['nname'];
                    else :  throw new ErrorException(null,107,E_ERROR); endif;
                endif;

                if(isset($_POST['addalias'])) :
                    if(!empty($_POST['addalias']))
                        $this->addAlias(intval($_POST['addalias']));
                endif;

                if(isset($_POST['gtag'])) :
                    if($_POST['gtag']) :
                        if(isValid($_POST['gtag'],DATUM)) //prüft nur den String !Kalender
                            $this->content['gtag'] = $_POST['gtag'];
                        else throw new ErrorException(null,103,E_WARNING);
                    else : $this->content['gtag'] = '0001-01-01'; endif;
                endif;

                if(isset($_POST['gort'])) :
                    if($_POST['gort'] == 0) $this->content['gort'] = null;
                    else $this->content['gort'] = $_POST['gort'];
                endif;

                if(isset($_POST['ttag'])) :
                    if($_POST['ttag']) :
                        if(isValid($_POST['ttag'], DATUM))
                            $this->content['ttag'] = $_POST['ttag'];
                        else throw new ErrorException(null,103,E_WARNING);
                    else : $this->content['ttag'] = null; endif;
                endif;

                if(isset($_POST['tort'])) :
                    if($_POST['tort'] == 0) $this->content['tort'] = null;
                    else $this->content['tort'] = $_POST['tort'];
                endif;

                if(!empty($_POST['strasse'])) :
                    if (isValid($_POST['strasse'], NAMEN))
                        $this->content['strasse'] = $_POST['strasse'];
                    else throw new ErrorException(null,109,E_WARNING);
                endif;

                if(isset($_POST['wort'])) :
                    if($_POST['wort'] == 0) $this->content['wort'] = null;
                    else $this->content['wort'] = intval($_POST['wort']);
                endif;

                if(!empty($_POST['plz'])) :
                    if(isValid($_POST['plz'], PLZ)) $this->content['plz'] = $_POST['plz'];
                    else throw new ErrorException(null,104,E_WARNING);
                else : $this->content['plz'] = null; endif;

                if(!empty($_POST['tel'])) :
                    if(isValid($_POST['tel'], TELNR)) $this->content['tel'] = $_POST['tel'];
                    else throw new ErrorException(null,105,E_WARNING);
                else : $this->content['tel'] = null; endif;

                if(!empty($_POST['mail'])) :
                    if(isValid($_POST['mail'], EMAIL))
                        $this->content['mail'] = $_POST['mail'];
                    else throw new ErrorException(null,106,E_WARNING);
                else : $this->content['mail'] = null; endif;

                if(isset($_POST['biogr'])) $this->content['descr'] = $_POST['biogr'];
                if(isset($_POST['notiz'])) $this->content['notiz'] = $_POST['notiz'];

                // doppelten Datensatz abfangen
                $number = self::ifDouble();
                if (!empty($number) AND $number != $this->content['id'])
                    throw new ErrorException(null,128,E_ERROR);

                $this->content['isvalid'] = false;
                if(isset($_POST['isvalid'])) :
                    if ($_POST['isvalid']) $this->content['isvalid'] = true;
                endif;

                $this->setsignum();
            }

            catch (Exception $e) {
                switch($e->getSeverity()) :
                    case E_WARNING :
                        feedback($e->getcode(),'warng');
                        break;

                    case E_ERROR :
                        feedback($e->getcode(),'error');
                        exit;
                endswitch;
            }
        endif; // Status
    }

    public function save() {
    /**********************************************************
    Aufgabe: schreibt das Obj. via Update in die DB zurück
             wird bei edit/del gebraucht
    Return: 4 = leerer Datensatz
    **********************************************************/
        global $myauth;
        if(!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
        if (!$this->content['id']) return 4;   // Abbruch weil leerer Datensatz

        $db =& MDB2::singleton();
        $types = list2array(self::TYPEENTITY.self::TYPENAME.self::TYPEPERSON);

        IsDbError($db->extended->autoExecute('p_person2', $this->content,
            MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($this->content['id'], 'integer'), $types));
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
