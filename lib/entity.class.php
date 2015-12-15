<?php namespace DiafIP {
    use MDB2;
    /**
     * Diese Klasse stellt Grundlegende Eigenschaften und Methoden für ihre
     * Kindklassen bereit.
     */

    /**
     * Abstrakte Elternklasse aller Objekte
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Entity
     * @version     $Id$
     * @since       r52
     * @requirement PHP Version >= 5.4
     */
    abstract class Entity implements iEntity {

        /**
         * Objektkonstante
         *
         * @var array TYPE_ENTITY <Typenliste für content>
         * @var string SQL_GET_DATA <SQL-Statement für Initialisierung>
         * @var string SQL_GET_MUELL <SQL-Statement für Indizierung Papierkorb>
         */
        const
            SQL_GET_DATA    = 'SELECT *
                               FROM entity
                               WHERE id = ?;',

            SQL_GET_MUELL   = 'SELECT id, bereich
                               FROM entity
                               WHERE del = TRUE;',

            TYPE_ENTITY     = 'integer,text,text,text,text,boolean,boolean,integer,text,';

        /**
         * @var array Der Container für die Daten
         */
        protected
            $content = ['id'       => null,
                        'bereich'  => '',    // Enthält die Bereichskennung
                        'descr'    => '',    // Beschreibung bzw. Biografie bei Personen
                        'bilder'   => '',
                        'notiz'    => '',    // selbsterklärend ;-)
                        'isvalid'  => false, // Flag zur Kennzeichnung, das dieser Datensatz
                                             // abschließend bearbeitet wurde
                        'del'      => false, // Löschflag
                        'editfrom' => null,  // uid des Bearbeiters
                        'editdate' => null]; // timestamp width Timezone

        /**
         * Der Konstruktor initiiert ein leeres Objekt oder via get() ein definiertes
         *
         * @param int $nr
         */
        function __construct($nr = null) {
            global $counter; $counter++;        // Profiler
            if (self::existId($nr)) :
                $db   = MDB2::singleton();
                $result = $db->extended->getRow(self::SQL_GET_DATA, list2array(self::TYPE_ENTITY), $nr, 'integer');
                IsDbError($result);
                self::WertZuwCont($result);
            endif;
        }

        protected function WertZuwCont($data){
            // Ergebnis -> Objekt schreiben
            if (!empty($data)) :
                foreach ($data as $key => $val) $this->content[$key] = $val;
            else :
                // Siehe Eintrag "App" in struktur.md" zum Fehler #4
                feedback("Fehler bei der Initialisierung im Objekt \"Entity\"", 'error');
                exit(4);
            endif;
        }

        /**
         * @return integer
         */
        public function getId() {
            return $this->content['id'];
        }

        /**
         * Testet ob es einen Datensatz mit dieser Nummer gibt
         * @param $nr
         * @return bool
         */
        static function existId($nr) {
            $db = MDB2::singleton();
            $anzahl = false;
            if ($nr AND is_numeric($nr)) :
                $anzahl = $db->extended->getOne('SELECT COUNT(*) FROM entity WHERE id = ?;', 'integer', $nr, 'integer');
                IsDbError($anzahl);
            endif;
            return (bool)$anzahl;
        }

        /**
         * Test ob Nr. als id mit diesem Bereichsbuchstaben in der DB existiert
         *
         * @param int $nr
         * @param string $bereich Großbuchstabe
         * @return int
         */
        public static function IsInDB($nr, $bereich) {
            $db   = MDB2::singleton();
            $data = null;

            if (is_numeric($nr) AND is_string($bereich) AND (strlen($bereich) == 1)) :
                $data = $db->extended->getOne(
                    'SELECT COUNT(*) FROM entity WHERE id = ? AND bereich = ?;', 'integer', [$nr, $bereich]);
                IsDbError($data);
            endif;
            return $data;
        }

        /**
         * Holt die passende Bereichskennung zur angegebenen Id
         *
         * @param int $nr
         * @return string Bereichskennung
         */
        public static function getBereich($nr) {
            $db   = MDB2::singleton();
            $data = null;

            if ($nr AND is_numeric($nr)) :
                $data = $db->extended->getOne('SELECT bereich FROM entity WHERE id = ?;', 'text', $nr, 'integer');
                IsDbError($data);
            endif;
            return $data;
        }

        /**
         * Bringt alles zum Vorschein was ein Löschbit trägt
         *
         * @param void
         * @return array(mixed) Id und Bereich
         */
        public static function getTrash() {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_DELE)) return 2;

            $db   = MDB2::singleton();
            $data = $db->extended->getAll(self::SQL_GET_MUELL, ['integer', 'text']);
            IsDbError($data);
            return $data;
        }

        /**
         * Neuanlage eines Objekts
         *
         * @param bool $status
         * @return mixed
         */
        abstract function add($status = null);

        /**
         * Bearbeiten des Objekts
         *
         * @param bool $status
         * @return mixed
         */
        abstract function edit($status = null);

        /**
         * Bearbeitungsflag setzen (Kippschalter)
         *
         * @param void
         * @return void
         */
        public function setValid() {
            if ($this->content['isValid']) $this->content['isValid'] = false;
            else $this->content['isValid'] = true;
        }

        /**
         * Test Validierungsflag
         *
         * @return bool
         */
        public function isValid() {
            if ($this->content['isValid']) return true; else return false;
        }

        /**
         * Schaltet das Löschflag um und schreibt das gesamte Objekt in die DB alternativ kann man diese Funktion nutzen
         * um das Element wieder aus dem Papierkorb zu holen.
         *
         * @return int
         */
        public function del() {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_DELE)) return 2;
            if (!$this->content['id']) return 4; // Abbruch weil leerer Datensatz

            // Aufgabe: Löschflag setzen (Kippschalter)
            if ($this->content['del']) $this->content['del'] = false;
            else $this->content['del'] = true;
            $this->save();
            return null;
        }

        /**
         * Test Löschflag
         *
         * @return bool
         */
        public function isDel() {
            if ($this->content['del']) return true; else return false;
        }

        /**
         * Speichert das Objekt in der DB
         *
         * @return void
         */
        abstract function save();

        /**
         * Übergibt die Datenkollektion an Smarty und startet die Ausgabe
         *
         * @param string $vorlage Der Templatename (ohne Pfad)
         * @return void
         */
        public function display($vorlage) {
            global $marty;
            $marty->assign('dialog', a_display($this->view()));
            $marty->display($vorlage);
        }

        /**
         * Bereitstellung der Ausgabedaten für Filter
         *
         * @return array
         */
        protected function view() {
            $data = [
                // name,inhalt optional-> $rechte,$label,$tooltip,valString
                new d_feld('id', $this->content['id'], RE_VIEW),
                new d_feld('bereich', $this->content['bereich'], RE_VIEW),
                new d_feld('bilder', $this->content['bilder'], RE_VIEW),
                new d_feld('notiz', changetext($this->content['notiz']), RE_IVIEW, 514),
                new d_feld('isVal', $this->content['isvalid'], RE_IVIEW, 10009),
                new d_feld('chdatum', $this->content['editdate'], RE_EDIT),
                new d_feld('chname', $this->getBearbeiter(), RE_EDIT),
                new d_feld('edit', null, RE_EDIT, null, 4013), // edit-Button
                new d_feld('del', null, RE_DELE, null, 4020), // Lösch-Button
            ];
            return $data;
        }

        /**
         * Ermittelt den Realnamen des Bearbeiters
         *
         * @return string
         * @throws <Abbruch wenn keine Bearbeiter-Id gefunden wurde>
         */
        final public function getBearbeiter() {
            $db = MDB2::singleton();
            if (empty($this->content['editfrom'])) exit; // kein Bearbeiter im Objekt
            $bearbeiter = $db->extended->getOne(
                "SELECT realname FROM s_auth WHERE uid = {$this->content['editfrom']};");
            IsDbError($bearbeiter);
            return $bearbeiter;
        }

        /**
         * Setzt Bearbeiter und Uhrzeit der Bearbeitung
         *
         * @param void
         * @return void
         */
        final protected function setSignum() {
            global $myauth;
            $this->content['editfrom'] = $myauth->getAuthData('uid');
            $this->content['editdate'] = date('c', $_SERVER['REQUEST_TIME']);
        }
    }
}

