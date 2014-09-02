<?php namespace DiafIP {
    use ErrorException;
    use MDB2;
    /**
     * Klassenbibliotheken für Personen und Aliasnamen
     *
     * Dazu gehören eine Klasse für Namen und eine Klasse für Personen inclusive der Interfaces
     */

    /**
     * Class Person - Personen sind natürliche und juristische Personen.
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Person
     * @version     $Id$
     * @since       r52
     * @requirement PHP Version >= 5.4
     */
    class Person extends PName implements iPerson {

        const
            TYPEPERSON = 'date,integer,date,integer,text,text,integer,text,text,text',
            GETDATA    =
            'SELECT gtag, gort, ttag, tort, strasse, plz, wort, tel, mail, aliases
             FROM p_person2 WHERE id = ?;',
            GETPERLI   =
            'SELECT id, vname, nname FROM ONLY p_person2 WHERE del = FALSE
             ORDER BY nname, vname ASC;',
            // Casting-Liste
            GETCALI    = 'SELECT fid, tid FROM f_cast WHERE pid= ? ORDER BY fid;',
            IFDOUBLE   =
            'SELECT id FROM p_person2 WHERE gtag = ? AND vname = ? AND nname = ?;';

        /**
         * Initialisiert das Personenobjekt
         *
         * @param int|null $nr
         * @todo Die Funktion get() in den Konstruktor integrieren
         */
        function __construct($nr = null) {
            parent::__construct($nr);
            $this->content['bereich'] = 'P';
            $this->content['gtag']    = '0001-01-01'; // Geburtstag
            $this->content['gort']    = null; // Geburtsort
            $this->content['ttag']    = null; // Todestag
            $this->content['tort']    = null; // Sterbeort
            $this->content['strasse'] = null; // + HNr. und Adresszusätze
            $this->content['plz']     = null; // PLZ des Wohnortes
            $this->content['wort']    = null; // Wohnort (Ort, land))
            $this->content['tel']     = null; // Telefonnummer
            $this->content['mail']    = null; // mailadresse
            $this->content['aliases'] = null;
            if (isset($nr) AND is_numeric($nr)) self::get(intval($nr));
        }

        /**
         * Datensatz holen und in $this schreiben
         *
         * @param  int $nr ID des Personendatensatzes (NOT STATIC)
         * @return void
         * @throws <Meldung Parameterfehler und Abbruch>
         */
        protected function get($nr) {
            $db = MDB2::singleton();

            $data = $db->extended->getRow(self::GETDATA, list2array(self::TYPEPERSON), $nr);
            IsDbError($data);
            // Ergebnis -> Objekt schreiben
            if ($data) :
                foreach ($data as $key => $val) :
                    $this->content[$key] = $val;
                endforeach;
            else :
                feedback(4, 'error');
                exit(4);
            endif;
        }

        /**
         * Liefert die Namensliste für Drop-Down-Menü
         *
         * @return array id, vname+name
         */
        static function getPersList() {
            $db = MDB2::singleton();
            global $str;
            $data = $db->extended->getAll(
                self::GETPERLI, ['integer', 'text', 'text']);
            IsDbError($data);

            $alist = [$str->getStr(0)]; // kein Eintrag
            foreach ($data as $val) :
                if ($val['vname'] === '-') :
                    $alist[$val['id']] = $val['nname'];
                else :
                    $alist[$val['id']] = $val['vname'] . '&nbsp;' . $val['nname'];
                endif;
            endforeach;
            return $alist;
        }

        /**
         * Neuanlage einer Person
         *
         * @param  bool $status false Erstaufruf | true Verarbeitung nach Formular
         * @return int
         */
        public function add($status = null) {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

            $db    = MDB2::singleton();
            $types = list2array(self::TYPEENTITY . self::TYPENAME . self::TYPEPERSON);

            if ($status == false) :
                // begin TRANSACTION anlage person
                $db->beginTransaction('newPerson');
                IsDbError($db);
                // neue id besorgen
                $data = $db->extended->getOne("SELECT nextval('entity_id_seq');");
                IsDbError($data);
                $this->content['id']      = $data;
                $this->content['bereich'] = 'P'; // Namen
                $this->edit(false);
            else :
                $this->edit(true);
                IsDbError($db->extended->autoExecute('p_person2', $this->content, MDB2_AUTOQUERY_INSERT, null, $types));
                $db->commit('newPerson');
                IsDbError($db);
                // ende TRANSACTION
            endif;
            return null;
        }

        /**
         * Objekt bearbeiten
         * Speichert in jedem Fall das Objekt. Verwirft allerdings alle fehlerhaften Eingaben.
         *
         * @param      bool $status (false Formularanforderung | true Auswertung
         * @return     null|int Fehlercode
         */
        public function edit($status = null) {
            global $myauth, $marty;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

            if ($status == false) :
                // Liste mit Alias erstellen und smarty übergeben
                if (self::IsInDB($this->content['id'], $this->content['bereich'])) :
                    $marty->assign('alist', parent::getNameList());
                endif;
                $marty->assign('ortlist', Ort::getOrtList());

                // Daten einsammeln und für Dialog bereitstellen :-)
                $data = [
                    // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                    new d_feld('kopf', null, VIEW, 4013),
                    new d_feld('id', $this->content['id']),
                    new d_feld('vname', $this->content['vname'], EDIT, 516),
                    new d_feld('nname', $this->content['nname'], EDIT, 517),
                    new d_feld('aliases', $this->getAliases(), VIEW),
                    new d_feld('addalias', null, EDIT, 515),
                    new d_feld('notiz', $this->content['notiz'], EDIT, 514),
                    new d_feld('isvalid', $this->content['isvalid'], SEDIT, 10009),
                    new d_feld('gtag', $this->content['gtag'], EDIT, 502, 10000),
                    new d_feld('gort', $this->content['gort'], EDIT, 4014),
                    new d_feld('ttag', $this->content['ttag'], EDIT, 509, 10000),
                    new d_feld('tort', $this->content['tort'], EDIT, 4014),
                    new d_feld('strasse', $this->content['strasse'], IEDIT, 510),
                    new d_feld('wort', $this->content['wort'], IEDIT),
                    new d_feld('plz', $this->content['plz'], IEDIT),
                    new d_feld('tel', $this->content['tel'], IEDIT, 511, 10002),
                    new d_feld('mail', $this->content['mail'], IEDIT, 512),
                    new d_feld('descr', $this->content['descr'], EDIT, 513)];
                $marty->assign('dialog', a_display($data));
                $marty->display('person_dialog.tpl');
                $myauth->setAuthData('obj', serialize($this));
            else : // Formular auswerten
                // Reinitialisierung muss vom aufrufenden Programm erledigt werden

                try {
                    if (isset($_POST['vname'])) :
                        if (empty($_POST['vname'])) $this->content['vname'] = '-';
                        else $this->content['vname'] = $_POST['vname'];
                    endif;

                    if (isset($_POST['nname'])) :
                        if (!empty($_POST['nname'])) : $this->content['nname'] = $_POST['nname'];
                        else :  throw new ErrorException(null, 107, E_ERROR); endif;
                    endif;

                    if (isset($_POST['addalias'])) :
                        if (!empty($_POST['addalias']))
                            $this->addAlias(intval($_POST['addalias']));
                    endif;

                    if (isset($_POST['gtag'])) :
                        if ($_POST['gtag']) :
                            if (isValid($_POST['gtag'], DATUM)) //prüft nur den String !Kalender
                                $this->content['gtag'] = $_POST['gtag'];
                            else throw new ErrorException(null, 103, E_WARNING);
                        else : $this->content['gtag'] = '0001-01-01'; endif;
                    endif;

                    if (isset($_POST['gort'])) :
                        if ($_POST['gort'] == 0) $this->content['gort'] = null;
                        else $this->content['gort'] = $_POST['gort'];
                    endif;

                    if (isset($_POST['ttag'])) :
                        if ($_POST['ttag']) :
                            if (isValid($_POST['ttag'], DATUM))
                                $this->content['ttag'] = $_POST['ttag'];
                            else throw new ErrorException(null, 103, E_WARNING);
                        else : $this->content['ttag'] = null; endif;
                    endif;

                    if (isset($_POST['tort'])) :
                        if ($_POST['tort'] == 0) $this->content['tort'] = null;
                        else $this->content['tort'] = $_POST['tort'];
                    endif;

                    if (!empty($_POST['strasse'])) :
                        if (isValid($_POST['strasse'], NAMEN))
                            $this->content['strasse'] = $_POST['strasse'];
                        else throw new ErrorException(null, 109, E_WARNING);
                    endif;

                    if (isset($_POST['wort'])) :
                        if ($_POST['wort'] == 0) $this->content['wort'] = null;
                        else $this->content['wort'] = intval($_POST['wort']);
                    endif;

                    if (!empty($_POST['plz'])) :
                        if (isValid($_POST['plz'], PLZ)) $this->content['plz'] = $_POST['plz'];
                        else throw new ErrorException(null, 104, E_WARNING);
                    else : $this->content['plz'] = null; endif;

                    if (!empty($_POST['tel'])) :
                        if (isValid($_POST['tel'], TELNR)) $this->content['tel'] = $_POST['tel'];
                        else throw new ErrorException(null, 105, E_WARNING);
                    else : $this->content['tel'] = null; endif;

                    if (!empty($_POST['mail'])) :
                        if (isValid($_POST['mail'], EMAIL))
                            $this->content['mail'] = $_POST['mail'];
                        else throw new ErrorException(null, 106, E_WARNING);
                    else : $this->content['mail'] = null; endif;

                    if (isset($_POST['descr'])) $this->content['descr'] = $_POST['descr'];
                    if (isset($_POST['notiz'])) $this->content['notiz'] = $_POST['notiz'];

                    // doppelten Datensatz abfangen
                    $number = self::ifDouble();
                    if (!empty($number) AND $number != $this->content['id'])
                        throw new ErrorException(null, 128, E_ERROR);

                    $this->content['isvalid'] = false;
                    if (isset($_POST['isvalid'])) :
                        if ($_POST['isvalid']) $this->content['isvalid'] = true;
                    endif;

                    $this->setsignum();
                } catch (ErrorException $e) {
                    switch ($e->getSeverity()) :
                        case E_WARNING :
                            feedback($e->getcode(), 'warng');
                            break;

                        case E_ERROR :
                            feedback($e->getcode(), 'error');
                            exit;
                    endswitch;
                }
            endif; // Status
            return null;
        }

        /**
         *  Ermitteln der/des Aliasnamen
         *
         * @return array|null Liste der Namen.
         */
        public function getAliases() {
            if ($this->content['aliases']) :
                $data = [];
                foreach (list2array($this->content['aliases']) as $val) :
                    $e      = new PName(intval($val));
                    $data[] = $e->fiVname() . $e->content['nname'];
                endforeach;
                return $data;
            endif;
            return null;
        }

        /**
         * Fügt die ID eines PName-Objekts der Aliases-Liste hinzu
         *
         * @param int $nr
         * @return void
         */
        private function addAlias($nr) {
            if (!is_int($nr)) return;
            if (empty($this->content['aliases'])) :
                $this->content['aliases'] = '{' . $nr . '}';
            else :
                $this->content['aliases'] =
                    substr_replace($this->content['aliases'], ',' . $nr . '}', -1, 1);
            endif;
        }

        /**
         *  Ermitteln ob gleiche Person schon existiert
         *
         * @return bool
         */
        private function ifDouble() {
            $db   = MDB2::singleton();
            $data = $db->extended->getOne(self::IFDOUBLE, ['boolean'], [
                $this->content['gtag'],
                $this->content['vname'],
                $this->content['nname']]);
            IsDbError($data);
            return $data;
        }

        /**
         * Schreibt das Obj. via Update in die DB zurück wird von edit/del verwendet
         *
         * @return null|int
         */
        public function save() {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
            if (!$this->content['id']) return 4; // Abbruch weil leerer Datensatz

            $db    = MDB2::singleton();
            $types = list2array(self::TYPEENTITY . self::TYPENAME . self::TYPEPERSON);

            IsDbError($db->extended->autoExecute(
                          'p_person2', $this->content,
                                                 MDB2_AUTOQUERY_UPDATE,
                                                 'id = ' . $db->quote($this->content['id'], 'integer'), $types));
            return null;
        }

        /**
         * Bereitstellung der Daten für Ausgabe durch display()
         *
         * Anzeige eines Datensatzes, Einstellen der Rechteparameter Auflösen von Listen und holen der Strings aus der Tabelle
         * Zuweisungen und ausgabe an pers_dat.tpl
         * Anm.:   Zentrales Objekt zur Handhabung der Ausgabe
         *
         * @return array
         **/
        public function view() {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

            $data   = parent::view();
            $data[] = new d_feld('aliases', $this->getAliases(), VIEW, 515);
            $data[] = new d_feld('gtag', $this->fiGtag(), VIEW, 502);
            $data[] = new d_feld('gort', Ort::getOrt($this->content['gort']), VIEW, 4014);
            $data[] = new d_feld('ttag', $this->content['ttag'], VIEW, 509);
            $data[] = new d_feld('tort', Ort::getOrt($this->content['tort']), VIEW, 4014);
            $data[] = new d_feld('strasse', $this->content['strasse'], IVIEW, 510);
            $data[] = new d_feld('wort', Ort::getOrt($this->content['wort']), IVIEW);
            $data[] = new d_feld('plz', $this->content['plz'], IVIEW);
            $data[] = new d_feld('tel', $this->content['tel'], IVIEW, 511);
            $data[] = new d_feld('mail', $this->content['mail'], IVIEW, 512);
            $data[] = new d_feld('castLi', $this->getCastList(), VIEW); // Verw. Film
            return $data;
        }

        /**
         * Geburtstagsfilter
         *
         * @return int|null
         */
        private function fiGtag() {
            if (($this->content['gtag'] === '0001-01-01') OR ($this->content['gtag'] === '01.01.0001'))
                return null; else return $this->content['gtag'];
        }

        /**
         *  Aufgabe: gibt die Besetzungsliste für diese Person aus
         *
         * @return array [vname, name, tid, pid, job]
         */
        final protected function getCastList() {
            global $str;
            $db = MDB2::singleton();
            if (empty($this->content['id'])) return null;

            // Zusammenstellen der Castingliste für diese Person
            $data = $db->extended->getALL(
                self::GETCALI, ['integer', 'integer', 'integer'], $this->content['id'], 'integer');
            IsDbError($data);

            // Übersetzung für die Tätigkeit und Namen holen
            $f = [];
            foreach ($data as $wert) :
                $film = new Film($wert['fid']);
                if (!$film->isDel()) :
                    $g           = [];
                    $g['ftitel'] = $film->getTitel();
                    $g['job']    = $str->getStr($wert['tid']);
                    $f[]         = $g;
                endif;
            endforeach;
            return $f;
        }
    }
} // end Personen-Klasse