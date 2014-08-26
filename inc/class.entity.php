<?php
/**
 * BASIS-KLASSE
 *
 * $Rev$
 * $Author$
 * $Date$
 * $URL$
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   Deutsches Institut für Animationsfilm e.V.
 * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
 * @requirement PHP Version >= 5.4
 *
 * ToDo: Bildauswertung
 */

/** ===========================================================
 * ENTITY
 * =========================================================== **/
interface iEntity {
    static function IsInDB($nr, $bereich); // ermittelt Realnamen des Bearbeiters

    static function getBereich($nr); // Test ob id/bereich in der DB existiert

    static function getTrash(); // Holt zur ID die Bereichskennung

    function getBearbeiter(); // Set/Unset Flag

    function setValid(); // Validierungstest

    function isValid(); // Schaltet Löschflag in DB um

    function del(); // Test auf Löschflag

    function isDel(); // schnüffelt im Papierkorb

    function display($vorlage);
}

abstract class Entity implements iEntity {
    /**
     * Interne Methoden:
     *       get($nr)
     *       setSignum()
     */
    const
        TYPEENTITY = 'integer,text,text,text,text,boolean,boolean,integer,timestamp,',
        GETDATA    = 'SELECT * FROM entity WHERE id = ?;',
        GETMUELL   = 'SELECT id, bereich FROM entity WHERE del = TRUE;';

    protected
        $content = [
        'id'       => null,
        'bereich'  => '', // Enthält die Kennung zu welchem Bereich die
        // Entität gehört....
        'descr'    => '', // Beschreibung bzw. Biografie bei Personen
        'bilder'   => '',
        'notiz'    => '', // selbsterklärend ;-)
        'isvalid'  => false, // Flag zur Kennzeichnung, das dieser Datensatz
        // abschließend bearbeitet wurde
        'del'      => false, // Löschflag
        'editfrom' => null, // uid des Bearbeiters
        'editdate' => null // timestamp width Timezone
    ];

    function __construct($nr = null) {
        if (isset($nr) AND is_numeric($nr)) self::get(intval($nr));
    }

    protected function get($nr) {
        // Diese Funktion initialisiert das Objekt
        $db   = MDB2::singleton();
        $data = $db->extended->getRow(self::GETDATA, list2array(self::TYPEENTITY), $nr, 'integer');
        IsDbError($data);
        if ($data) :
            foreach ($data as $key => $val) $this->content[$key] = $val;
        else :
            feedback(4, 'error');
            exit(4);
        endif;
    }

    static function IsInDB($nr, $bereich) {
        /**
         * Test ob Nr. als id mit diesem Bereichsbuchstaben in der DB existiert
         *
         * @param $nr = int, $bereich = Großbuchstabe
         * @return int | null
         */
        $db   = MDB2::singleton();
        $data = null;

        if (is_numeric($nr) AND is_string($bereich) AND (strlen($bereich) == 1)) :
            $data = $db->extended->getRow(
                'SELECT COUNT(*) FROM entity WHERE id = ? AND bereich = ?;', null, [$nr, $bereich]);
            IsDbError($data);
        endif;
        return $data['count'];
    }

    static function getBereich($nr) { // Holt zur ID die Bereichskennung
        $db   = MDB2::singleton();
        $data = null;

        if ($nr AND is_numeric($nr)) :
            $data = $db->extended->getOne('SELECT bereich FROM entity WHERE id = ?;', null, $nr);
            IsDbError($data);
        endif;
        return $data;
    }

    public static function getTrash() {
        /**
         * Bringt alles zum Vorschein was ein Löschbit trägt
         *
         * @return array(Id, Bereich)
         * @access public
         */
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), DELE)) return 2;

        $db   = MDB2::singleton();
        $data = $db->extended->getAll(self::GETMUELL, ['integer', 'text']);
        IsDbError($data);
        return $data;
    }

    abstract function add($status = null);

    abstract function edit($status = null);

    abstract function search($s);

    public function setValid() {
        /**
         * Bearbeitungsflag setzen (Kippschalter)
         *
         * @return null
         * @access public
         */
        if ($this->content['isValid']) $this->content['isValid'] = false;
        else $this->content['isValid'] = true;
    }

    public function isValid() {
        if ($this->content['isValid']) return true; else return false;
    }

    public function del() {
        /**
         * Schaltet das Löschflag um und schreibt das gesamte Objekt in die DB Alternativ kann man diese Funktion nutzen
         * um das Element wieder aus dem Papierkorb zu holen.
         */
        global $myauth;
        if (!isBit($myauth->getAuthData('rechte'), DELE)) return 2;

        if (!$this->content['id']) return 4; // Abbruch weil leerer Datensatz

        // Aufgabe: Löschflag setzen (Kippschalter)
        if ($this->content['del']) $this->content['del'] = false;
        else $this->content['del'] = true;
        $this->save();
    }

    abstract function save();

    public function isDel() {
        if ($this->content['del']) return true; else return false;
    }

    function display($vorlage) {
        global $marty;
        $marty->assign('dialog', a_display($this->view()));
        $marty->display($vorlage);
    }

    protected function view() {
        $data = [
            // name,inhalt optional-> $rechte,$label,$tooltip,valString
            new d_feld('id', $this->content['id'], VIEW),
            new d_feld('bereich', $this->content['bereich'], VIEW),
            new d_feld('descr', changetext($this->content['descr']), VIEW, 513), // Beschreibung
            new d_feld('bilder', $this->content['bilder'], VIEW),
            new d_feld('notiz', changetext($this->content['notiz']), IVIEW, 514),
            new d_feld('isVal', $this->content['isvalid'], IVIEW, 10009),
            new d_feld('chdatum', $this->content['editdate'], EDIT),
            new d_feld('chname', $this->getBearbeiter(), EDIT),
            new d_feld('edit', null, EDIT, null, 4013), // edit-Button
            new d_feld('del', null, DELE, null, 4020), // Lösch-Button
        ];
        return $data;
    }

    final function getBearbeiter() {
        /**
         * Ermittelt den Realnamen des Bearbeiters
         *
         * @return string | null
         */
        $db = MDB2::singleton();
        if (empty($this->content['editfrom'])) exit; // kein Bearbeiter im Objekt
        $bearbeiter = $db->extended->getOne(
            'SELECT realname FROM s_auth WHERE uid = ' . $this->content['editfrom'] . ';');
        IsDbError($bearbeiter);
        return $bearbeiter;
    }

    final protected function setSignum() {
        /**
         * Setzt Bearbeiter und Uhrzeit der Bearbeitung
         *
         * @param void
         * @return void
         * @access public
         **/
        global $myauth;
        $this->content['editfrom'] = $myauth->getAuthData('uid');
        $this->content['editdate'] = date('c', $_SERVER['REQUEST_TIME']);
    }
}

