<?php namespace DiafIP {
    use MDB2;
    /**
     * Klassenbibliotheken für Personen und Aliasnamen
     *
     * Dazu gehören eine Klasse für Namen und eine Klasse für Personen inclusive der Interfaces
     */


    /**
     * Class PName
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Person
     * @version     $Id$
     * @since       r99 Klassentrennung
     * @requirement PHP Version >= 5.4
     */
    class PName extends Entity implements iPName {
        const
            SQL_GET_DATA =     'SELECT vname, nname
                                FROM p_namen
                                WHERE id = ?;',

            SQL_GET_PERSON =   'SELECT id
                                FROM p_person2
                                WHERE ? = ANY(aliases);',

            SQL_GET_ALIAS  =   'SELECT DISTINCT p_namen.id, p_namen.vname, p_namen.nname
                                FROM ONLY p_namen,p_person2
                                WHERE (p_namen.del = FALSE) AND (p_namen.id = ANY(p_person2.aliases))
                                ORDER BY p_namen.nname, p_namen.vname;',

            SQL_GET_CAST_LI    = 'SELECT fid, tid FROM f_cast WHERE pid= ? ORDER BY fid;',      // Casting-Liste

            SQL_GET_ALL_NAMES ='SELECT id,vname,nname
                                FROM p_namen
                                WHERE del = FALSE
                                ORDER BY nname,vname;',

            SQL_GET_NAMES =    'SELECT id,vname,nname
                                FROM ONLY p_namen
                                WHERE del = FALSE
                                ORDER BY nname,vname;',

            SQL_GET_ID_FROM_NAME = 'SELECT id
                                FROM p_namen
                                WHERE (vname = ?) AND (nname = ?)',

            SQL_SEARCH_NAME  = 'SELECT id,bereich
                                FROM p_namen
                                WHERE (del = FALSE) AND ((nname ILIKE ?) OR (vname ILIKE ?))
                                ORDER BY nname,vname;',

            TYPENAME         = 'text,text,';

        /**
         * Verweis auf die Person die den Alias verwendet
         *
         * @var null $alias
         */
        protected
            $alias = null;

        /**
         * Initialisiert das Objekt
         *
         * @param int|null $nr
         */
        function __construct($nr = null) {
            parent::__construct($nr);
            $this->content['vname']   = '-';
            $this->content['nname']   = '';
            if (isset($nr) AND is_numeric($nr)) :
                $db = MDB2::singleton();
                $data = $db->extended->getRow(self::SQL_GET_DATA, list2array(self::TYPENAME), $nr, 'integer');
                IsDbError($data);
                if ($data) :
                    $this->content['vname'] = $data['vname'];
                    $this->content['nname'] = $data['nname'];
                    $this->alias            = self::getPerson();
                else :
                    feedback("Fehler bei der Initialisierung im Objekt \'PName\'", 'error'); // #4
                    exit(4);
                endif;
            endif;
        }

        /**
         * Ermittelt die Person zum Aliasnamen
         *
         * @return  int|null null : Es existiert keine Person, Datensatz frei zum löschen
         *          int :  Id zum Benutzer des Alias
         */
        function getPerson() {
            $db = MDB2::singleton();
            $p  = null;
            if ($this->content['bereich'] === 'N') :
                $p = $db->extended->getOne(self::SQL_GET_PERSON, 'integer', $this->content['id'], 'integer');
                IsDbError($p);
            endif;
            return $p;
        }

        /**
         * Prüft, ob sich ein Namenseintrag in der DB finden lässt und liefert die Id's
         *
         * @param $vname
         * @param $nname
         * @return array | null
         */
        final static function getIdFromName($nname, $vname = null) {
            if(empty($vname)) $vname = '-';
            $db = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_GET_ID_FROM_NAME, 'integer', [$vname, $nname]);
            IsDbError($data);
            return $data;
        }


        /**
         * Stellt die Liste mit den Id's und Namen für das Formular zusammen
         *
         * @param $arr
         * @return array
         */
        protected function arrpack($arr) {
            $erg = [];
            foreach ($arr as $val) :
                if ($val['vname'] === '-') :
                    $erg[$val['id']] = $val['nname'];
                else :
                    $erg[$val['id']] = $val['vname'] . '&nbsp;' . $val['nname'];
                endif;
            endforeach;
            return $erg;
        }

        /**
         * Liefert die Namensliste für Drop-Down-Menü "Aliasnamen" im Personendialog
         * Listet nur die unbenutzten Aliasnamen
         *
         * @return  array   [id, vname+name]
         *
         *                  Anm.:   Vielleicht findet sich ja mal ein Held der die Datenbankabfrage optimiert
         *                          und dieses recht komplizierte Konstrukt auflöst ;-)
         */
        static function getUnusedAliasNameList() {
            global $str;

            $db   = MDB2::singleton();
            $erg  = [];
            $data = $db->extended->getAll(self::SQL_GET_ALIAS, ['integer', 'text', 'text']);
            IsDbError($data);
            $data = self::arrpack($data);
            $all  = $db->extended->getAll(self::SQL_GET_NAMES, ['integer', 'text', 'text']);
            IsDbError($all);
            $all    = self::arrpack($all);
//            $erg[0] = $str->getStr(0); // kein Eintrag
            $erg += array_diff($all, $data);
            return $erg;
        }

        /**
         * Liefert eine Liste aller Namen und Personen zur Listenansicht (Formular)
         * @todo Eliminierung der unbenutzten Aliasnamen
         * @return mixed
         */
        static function getNameList() {
            $db   = MDB2::singleton();
            $all  = $db->extended->getAll(self::SQL_GET_ALL_NAMES, ['integer', 'text', 'text']);
            IsDbError($all);
            return self::arrpack($all);
        }

        /**
         * Neuanlage eines Namens
         *
         * @param   bool $status false Erstaufruf | true Verarbeitung nach Formular
         * @return int|null
         */
        function add($status = null) {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;

            $db    = MDB2::singleton();
            $types = list2array(self::TYPE_ENTITY . self::TYPENAME);

            if (empty($status)) :
                // begin TRANSACTION anlage name
                $db->beginTransaction('newName');
                IsDbError($db);
                // neue id besorgen
                $data = $db->extended->getOne("SELECT nextval('entity_id_seq');");
                IsDbError($data);
                $this->content['id']      = $data;
                $this->content['bereich'] = 'N'; // Namen
                $this->edit();
            else :
                $this->edit(true);
                IsDbError($db->extended->autoExecute(
                              'p_namen', $this->content, MDB2_AUTOQUERY_INSERT, null, $types));
                $db->commit('newName');
                IsDbError($db);
                // ende TRANSACTION
            endif;
            return null;
        }

        /**
         * Objekt bearbeiten
         *
         * @param   bool $status false Erstaufruf | true Verarbeitung nach Formular
         * @return  int|null Fehlercode
         *                       Anm.:       Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehler-
         *                       haften Eingaben.
         */
        public function edit($status = null) {
            global $myauth, $marty;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;

            if (empty($status)) :
                // Daten einsammeln und für Dialog bereitstellen :-)
                $data = [
                    // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                    new d_feld('kopf', null, RE_VIEW, 4013),
                    new d_feld('id', $this->content['id']),
                    new d_feld('vname', $this->content['vname'], RE_EDIT, 516),
                    new d_feld('nname', $this->content['nname'], RE_EDIT, 517),
                    new d_feld('notiz', $this->content['notiz'], RE_EDIT, 514)];
                $marty->assign('dialog', a_display($data));
                $marty->display('pers_dialog.tpl');
                $myauth->setAuthData('obj', serialize($this));
            else : // Status
                // Reinitialisierung muss vom aufrufenden Programm erledigt werden
                // Formular auswerten
                try {
                    if (isset($_POST['vname']))
                        if (empty($_POST['vname'])) $this->content['vname'] = '-';
                        else $this->content['vname'] = $_POST['vname'];

                    if (isset($_POST['nname'])) :
                        if (!empty($_POST['nname'])) $this->content['nname'] = $_POST['nname'];
                        else throw new \Exception(null, 107);
                    endif;

                    if (isset($_POST['notiz'])) $this->content['notiz'] = $_POST['notiz'];
                } catch (\Exception $e) {
                    feedback($e->getcode(), 'error');
                    exit;
                }

                $this->setSignum(); // Bearbeiter und Zeit setzen
            endif;
            return null;
        }

        /**
         * Schreibt das Obj. via Update in die DB zurück wird durch add/edit/del gebraucht
         *
         * @return int|null 4 = leerer Datensatz
         */
        public function save() {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;
            if (!$this->content['id']) return 4; // Abbruch weil leerer Datensatz

            $db    = MDB2::singleton();
            $types = list2array(self::TYPE_ENTITY . self::TYPENAME);

            IsDbError($db->extended->autoExecute(
                'p_namen', $this->content, MDB2_AUTOQUERY_UPDATE,
                'id = ' . $db->quote($this->content['id'], 'integer'), $types));
            return null;
        }

        /**
         * Sucht in Vor- und Nachnamen (nicht Literal)
         *
         * @param string $s Suchmuster
         * @return array|null  Id's oder null (Namen und Personen)
         */
        static public function search($s) {
            $db  = MDB2::singleton();
            /* Ermittelt die Anzahl der gültigen Aliase und Personen
            $max = $db->extended->getOne('SELECT COUNT(*) FROM p_namen WHERE del = FALSE;', 'integer');
            IsDbError($max); */

            $s = "%" . $s . "%";                // Suche nach Teilstring
            $data = $db->extended->getAll(self::SQL_SEARCH_NAME, ['integer', 'text'], [$s, $s]);
            IsDbError($data);
            // [id] wird schlüssel
            $list = [];
            foreach($data as $val) : $list[intval($val['id'])] = $val['bereich']; endforeach;
            $data = array_diff_key($list, self::getUnusedAliasNameList());
            if ($data) return $data; else return 102;
        }

        /**
         * Liefert den zusammen gesetzten und verlinkten Namen zurück
         *
         * @return string
         */
        public function getName() {
            if (empty($this->content['id'])) return null;

            $a = null;
            $data = self::fiVname() . $this->content['nname'];
            $i    = $this->content['id'];
            if (!empty($this->alias)) :
                $i = $this->alias;
                $a = '*';
            endif;
            return '<a href="index.php?P='.$i.'">'.$data."</a>$a";
        }

        /**
         * Aufgabe: Ausfiltern des default-Wertes von Vorname
         *
         * @return string|null
         */
        protected function fiVname() {
            if ($this->content['vname'] === '-') return null;
            else return $this->content['vname'] . '&nbsp;';
        }

        /**
         *  Aufgabe: gibt die Besetzungsliste für diese Person aus
         *
         * @return array [ftitel, job]
         */
        final protected function getCastList() {
            if (empty($this->content['id'])) return null;

            global $str;
            $db = MDB2::singleton();
            $castLi = [];

            // Zusammenstellen der Castingliste für diese Person
            $data = $db->extended->getALL(
                self::SQL_GET_CAST_LI,
                ['integer', 'integer', 'integer'],
                $this->content['id'],
                'integer'
            );
            IsDbError($data);

            // Übersetzung für die Tätigkeit und Namen holen
            foreach ($data as $wert) :
                $film = new Film($wert['fid']);
                if (!$film->isDel()) :
                    $g           = [];
                    $g['ftitel'] = $film->getTitel();
                    $g['job']    = $str->getStr($wert['tid']);
                    $castLi[]         = $g;
                endif;
                unset($film);
            endforeach;
            return $castLi;
        }

        /**
         * Bereitstellung des Namens
         *
         * @return array
         */
        public function view() {
            $data   = parent::view();
            $data[] = new d_feld('pname', self::getName(), RE_VIEW);
            return $data;
        }
    }
}