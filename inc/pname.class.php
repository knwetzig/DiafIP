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
            TYPENAME    = 'text,text,',
            GETDATA     = 'SELECT vname, nname FROM p_namen WHERE id = ?;',
            GETPERSON   = 'SELECT id FROM p_person2 WHERE ? = ANY(aliases);',
            GETALIAS    =
            'SELECT DISTINCT p_namen.id, p_namen.vname, p_namen.nname
             FROM ONLY p_namen,p_person2
             WHERE (p_namen.del = FALSE) AND (p_namen.id = ANY(p_person2.aliases))
             ORDER BY p_namen.nname, p_namen.vname;',
            GETALLNAMES =
            'SELECT id,vname,nname FROM ONLY p_namen
             WHERE del = FALSE
             ORDER BY nname,vname;',
            SEARCH      =
            'SELECT id,bereich FROM p_namen
             WHERE (del = FALSE) AND ((nname ILIKE ?) OR (vname ILIKE ?))
             ORDER BY nname,vname,id LIMIT ? OFFSET ?;';

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
            $this->content['bereich'] = 'N';
            $this->content['vname']   = '-';
            $this->content['nname']   = '';
            if (isset($nr) AND is_numeric($nr)) :
                $nr = intval($nr);
                $db = MDB2::singleton();

                $data = $db->extended->getRow(self::GETDATA, list2array(self::TYPENAME), $nr, 'integer');
                IsDbError($data);
                if ($data) :
                    $this->content['vname'] = $data['vname'];
                    $this->content['nname'] = $data['nname'];
                    $this->alias            = self::getPerson();
                else :
                    feedback(4, 'error');
                    exit(4);
                endif;
            endif;
        }

        /**
         * Diese Funktion initialisiert das Objekt
         *
         * @param int $nr
         * @deprecated
         */
        protected function get($nr) {
            $db = MDB2::singleton();

            $data = $db->extended->getRow(self::GETDATA, list2array(self::TYPENAME), $nr, 'integer');
            IsDbError($data);
            if ($data) :
                $this->content['vname'] = $data['vname'];
                $this->content['nname'] = $data['nname'];
                $this->alias            = self::getPerson();
            else :
                feedback(4, 'error');
                exit(4);
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
                $p = $db->extended->getOne(self::GETPERSON, 'integer', $this->content['id'], 'integer');
                IsDbError($p);
            endif;
            return $p;
        }

        /**
         * Liefert die Namensliste für Drop-Down-Menü
         *
         * @return  array   [id, vname+name]
         *
         * Anm.:       Vielleicht findet sich ja mal ein Held der die Datenbankabfrage optimiert
         * und dieses recht komplizierte Konstrukt auflöst ;-)
         */
        static function getNameList() {
            global $str;
            /**
             * Stellt die Liste mit den Id's und Namen zusammen
             *
             * @param $arr
             * @return array
             */
            function arrpack($arr) {
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

            $db   = MDB2::singleton();
            $data = $db->extended->getAll(
                self::GETALIAS, ['integer', 'text', 'text']);
            IsDbError($data);
            $data = arrpack($data);
            $all  = $db->extended->getAll(
                self::GETALLNAMES, ['integer', 'text', 'text']);
            IsDbError($all);
            $all    = arrpack($all);
            $erg[0] = $str->getStr(0); // kein Eintrag
            $erg += array_diff($all, $data);
            return $erg;
        }

        /**
         * Neuanlage eines Namens
         *
         * @param   bool $status false Erstaufruf | true Verarbeitung nach Formular
         * @return int|null
         */
        function add($status = null) {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

            $db    = MDB2::singleton();
            $types = list2array(self::TYPEENTITY . self::TYPENAME);

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
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

            if (empty($status)) :
                // Daten einsammeln und für Dialog bereitstellen :-)
                $data = [
                    // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                    new d_feld('kopf', null, VIEW, 4013),
                    new d_feld('id', $this->content['id']),
                    new d_feld('vname', $this->content['vname'], EDIT, 516),
                    new d_feld('nname', $this->content['nname'], EDIT, 517),
                    new d_feld('notiz', $this->content['notiz'], EDIT, 514)];
                $marty->assign('dialog', a_display($data));
                $marty->display('person_dialog.tpl');
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
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
            if (!$this->content['id']) return 4; // Abbruch weil leerer Datensatz

            $db    = MDB2::singleton();
            $types = list2array(self::TYPEENTITY . self::TYPENAME);

            IsDbError($db->extended->autoExecute('p_namen', $this->content,
                                                 MDB2_AUTOQUERY_UPDATE,
                                                 'id = ' . $db->quote($this->content['id'], 'integer'), $types));
            return null;
        }

        /**
         * Sucht in Vor- und Nachnamen (nicht Literal)
         *
         * @param string $s Suchmuster
         * @return array|null  Id's oder null (Namen und Personen)
         */
        public function search($s) {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

            $db  = MDB2::singleton();
            $max = $db->extended->getOne('SELECT COUNT(*) FROM p_namen WHERE del = FALSE;', 'integer');
            IsDbError($max);
            $limit  = null;
            $offset = null;

            // Suche nach Teilstring
            $s = "%" . $s . "%";

            $data = $db->extended->getAll(
                self::SEARCH, ['integer', 'text'], [$s, $s, $limit, $offset]);
            IsDbError($data);

            if ($data) return $data; else return 102;
        }

        /**
         * Bereitstellung des Namens
         *
         * @return array
         */
        public function view() {
            $data   = parent::view();
            $data[] = new d_feld('pname', self::getName(), VIEW);
            return $data;
        }

        /**
         * Liefert den zusammngesetzten und verlinkten Namen zurück
         *
         * @return string
         */
        public function getName() {
            if (empty($this->content['id'])) return null;
            $data = self::fiVname() . $this->content['nname'];
            $b    = $this->content['bereich'];
            $i    = $this->content['id'];
            if (!empty($this->alias)) :
                $i = $this->alias;
                $b = 'P';
            endif;
            return '<a href="index.php?' . $b . '=' . $i . '">' . $data . '</a>';
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
    }
}