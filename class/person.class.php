<?php namespace DiafIP {
    use MDB2, ErrorException, DateTime, DateInterval;
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
            SQL_GET_DATA    = 'SELECT gtag, gort, ttag, tort, strasse, plz, wort, tel, mail, aliases
                          FROM p_person2 WHERE id = ?;',
            SQL_IF_DOUBLE   = 'SELECT id FROM p_person2 WHERE gtag = ? AND vname = ? AND nname = ?;';

        /**
         * Initialisiert das Personenobjekt
         *
         * @param int|null $nr
         */
        function __construct($nr = null) {
            parent::__construct($nr);
            if(!empty($nr) AND $this->content['bereich'] !== 'P') return 1;
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
            if (isset($nr) AND is_numeric($nr)) :
                $db = MDB2::singleton();
                $data = $db->extended->getRow(self::SQL_GET_DATA, list2array(self::TYPEPERSON), $nr, 'integer');
                self::WertZuwCont($data);
            endif;
        }

        /**
         * Neuanlage einer Person
         *
         * @param  bool $status false Erstaufruf | true Verarbeitung nach Formular
         * @return int
         */
        public function add($status = null) {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;

            $db    = MDB2::singleton();
            $types = list2array(self::TYPE_ENTITY . self::TYPENAME . self::TYPEPERSON);

            if ($status == false) :
                // begin TRANSACTION anlage person
                $db->beginTransaction('newPerson');
                IsDbError($db);
                // neue id besorgen
                $data = $db->extended->getOne("SELECT nextval('entity_id_seq');");
                IsDbError($data);
                $this->content['id']      = $data;
                $this->content['bereich'] = 'P';
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
            global $myauth, $marty, $str;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;

            if ($status == false) :
                // Liste mit Alias erstellen und smarty übergeben
                if (self::IsInDB($this->content['id'], $this->content['bereich'])) :
                    $ual = parent::getUnusedAliasNameList();
                    if(!empty($ual)) :
                        $ual = [0 => $str->getStr(0)];
                        $ual += parent::getUnusedAliasNameList();
                        $marty->assign('alist', $ual);
                    endif;
                endif;
                $marty->assign('ortlist', Ort::getOrtList());

                // Daten einsammeln und für Dialog bereitstellen :-)
                $data = [
                    // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                    new d_feld('kopf', null, RE_VIEW, 4013),
                    new d_feld('id', $this->content['id']),
                    new d_feld('vname', $this->content['vname'], RE_EDIT, 516),
                    new d_feld('nname', $this->content['nname'], RE_EDIT, 517),
                    new d_feld('aliases', $this->getAliases(), RE_VIEW),
                    new d_feld('addalias', null, RE_EDIT, 515),
                    new d_feld('notiz', $this->content['notiz'], RE_EDIT, 514),
                    new d_feld('isvalid', false, RE_SEDIT, 10009),
                    new d_feld('gtag', $this->content['gtag'], RE_EDIT, 502, 10000),
                    new d_feld('gort', $this->content['gort'], RE_EDIT, 4014),
                    new d_feld('ttag', $this->content['ttag'], RE_EDIT, 509, 10000),
                    new d_feld('tort', $this->content['tort'], RE_EDIT, 4014),
                    new d_feld('strasse', $this->content['strasse'], RE_IEDIT, 510),
                    new d_feld('wort', $this->content['wort'], RE_IEDIT),
                    new d_feld('plz', $this->content['plz'], RE_IEDIT),
                    new d_feld('tel', $this->content['tel'], RE_IEDIT, 511, 10002),
                    new d_feld('mail', $this->content['mail'], RE_IEDIT, 512),
                    new d_feld('descr', $this->content['descr'], RE_EDIT, 513)];
                $marty->assign('dialog', a_display($data));
                $marty->display('pers_dialog.tpl');
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
                            if (isValid($_POST['gtag'], REG_DATUM)) //prüft nur den String !Kalender
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
                            if (isValid($_POST['ttag'], REG_DATUM)) :

                                // Test das Geburt vor Tod liegt ;-)
                                $born = new DateTime($this->content['gtag']);
                                $dead = new DateTime($_POST['ttag']);
                                $erg = $born->diff($dead);
                                if (!$erg->invert) :
                                    $this->content['ttag'] = $_POST['ttag'];
                                else :
                                    throw new ErrorException(null, 112, E_WARNING);
                                endif;
                            else :
                                throw new ErrorException(null, 103, E_WARNING);
                            endif;
                        else : $this->content['ttag'] = null; endif;
                    endif;

                    if (isset($_POST['tort'])) :
                        if ($_POST['tort'] == 0) $this->content['tort'] = null;
                        else $this->content['tort'] = $_POST['tort'];
                    endif;

                    if (!empty($_POST['strasse'])) :
                        if (isValid($_POST['strasse'], REG_NAMEN))
                            $this->content['strasse'] = $_POST['strasse'];
                        else throw new ErrorException(null, 109, E_WARNING);
                    endif;

                    if (isset($_POST['wort'])) :
                        if ($_POST['wort'] == 0) $this->content['wort'] = null;
                        else $this->content['wort'] = intval($_POST['wort']);
                    endif;

                    if (!empty($_POST['plz'])) :
                        if (isValid($_POST['plz'], REG_PLZ)) $this->content['plz'] = $_POST['plz'];
                        else throw new ErrorException(null, 104, E_WARNING);
                    else : $this->content['plz'] = null; endif;

                    if (!empty($_POST['tel'])) :
                        if (isValid($_POST['tel'], REG_TELNR)) $this->content['tel'] = $_POST['tel'];
                        else throw new ErrorException(null, 105, E_WARNING);
                    else : $this->content['tel'] = null; endif;

                    if (!empty($_POST['mail'])) :
                        if (isValid($_POST['mail'], REG_EMAIL))
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
         * @return integer
         */
        private function ifDouble() {
            $db   = MDB2::singleton();
            $data = $db->extended->getOne(self::SQL_IF_DOUBLE, ['integer'], [
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
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;
            if (!$this->content['id']) return 4; // Abbruch weil leerer Datensatz

            $db    = MDB2::singleton();
            $types = list2array(self::TYPE_ENTITY . self::TYPENAME . self::TYPEPERSON);

            IsDbError($db->extended->autoExecute(
                'p_person2', $this->content, MDB2_AUTOQUERY_UPDATE,
                'id = ' . $db->quote($this->content['id'], 'integer'), $types)
            );
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
            if (!isBit($myauth->getAuthData('rechte'), RE_VIEW)) return 2;

            $data   = parent::view();
            $data[] = new d_feld('descr', changetext($this->content['descr']), RE_VIEW, 513); // Biografie
            $data[] = new d_feld('aliases', $this->getAliases(), RE_VIEW, 515);
            $data[] = new d_feld('gtag', $this->fiGtag(), RE_VIEW, 502);
            $data[] = new d_feld('gort', Ort::getOrt($this->content['gort']), RE_VIEW, 4014);
            $data[] = new d_feld('ttag', $this->content['ttag'], RE_VIEW, 509);
            $data[] = new d_feld('tort', Ort::getOrt($this->content['tort']), RE_VIEW, 4014);
            $data[] = new d_feld('strasse', $this->content['strasse'], RE_IVIEW, 510);
            $data[] = new d_feld('wort', Ort::getOrt($this->content['wort']), RE_IVIEW);
            $data[] = new d_feld('plz', $this->content['plz'], RE_IVIEW);
            $data[] = new d_feld('tel', $this->content['tel'], RE_IVIEW, 511);
            $data[] = new d_feld('mail', $this->content['mail'], RE_IVIEW, 512);
            $data[] = new d_feld('castLi', $this->getCastList(), RE_VIEW);
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
    }
} // end Personen-Klasse