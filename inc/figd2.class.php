<?php namespace DiafIP {
    use Exception;
    use MDB2;
    /**
     * Klassenbibliotheken für Filmogr.-/Bibliografische Daten
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP
     * @version     $Id$
     * @since       r52
     * @requirement PHP Version >= 5.4
     */

    final class Film extends Fibikern implements iFilm {

        protected
            $gattung = null,
            $prodtechnik = null,
            $laenge = null,
            $fsk = null,
            $praedikat = 0,
            $mediaspezi = 0,
            $bildformat = 0,
            $urauffuehr = null;

        const
            SQL_get      = 'SELECT gattung, prodtechnik, laenge, fsk, praedikat,
                            bildformat, mediaspezi, urauffuehr
                       FROM f_film WHERE id = ?;',
            SQL_getPraed = 'SELECT * FROM f_praed ORDER BY praed ASC;',
            SQL_getGenre = 'SELECT * FROM f_genre;',
            SQL_getBfLi  = 'SELECT * FROM f_bformat;',
            SQL_getBF    = 'SELECT format FROM f_bformat WHERE id = ?;',
            SQL_getMS    = 'SELECT * FROM f_mediaspezi;',
            SQL_getPT    = 'SELECT * FROM f_prodtechnik;';

        public function __construct($nr = null) {
            parent::__construct($nr);
            if (isset($nr)) self::get($nr);
        }

        protected function get($nr) {
            /**
             *  Aufgabe:
             *   Aufruf:
             *   Return: void
             */
            $types = [
                'integer', // gattung
                'integer', // prodtechnik
                'time', // laenge   (Sonderformat)
                'integer', // fsk      (Altersempfehlung)
                'integer', // praedikat
                'integer', // bildformat
                'integer', // mediaspezi
                'date' // urauffuehr
            ];

            $db   = MDB2::singleton();
            $data = $db->extended->getRow(self::SQL_get, $types, $nr, 'integer');
            IsDbError($data);
            if (empty($data)) feedback(4, 'error'); // kein Datensatz vorhanden
            // Ergebnis -> Objekt schreiben
            foreach ($data as $key => $wert) $this->$key = $wert;
        }

        protected static function getListGattung() {
            /**
             *  Aufgabe: gibt ein  der Gattungen zurück
             *   Return: Array(num, text)
             */
            $db = MDB2::singleton();
            global $str;
            $list = $db->extended->getCol(self::SQL_getGenre, 'integer');
            $data = [];
            IsDbError($list);
            foreach ($list as $wert) :
                $data[$wert] = $str->getStr($wert);
            endforeach;
            asort($data);
            return $data;
        }

        protected static function getListPraedikat() {
            /**
             *  Aufgabe: gibt eine Liste der Praedikate aus
             *   Return: array(int, string)
             */
            $db = MDB2::singleton();
            global $str;
            $list = $db->extended->getCol(self::SQL_getPraed, 'integer');
            IsDbError($list);
            $data = [];
            // ToDo: Ueberdenken den Einsatz von getStringList !
            foreach ($list as $wert) $data[$wert] = $str->getStr($wert);
            return $data;
        }

        protected static function getListBildformat() {
            /**
             *  Aufgabe: gibt eine Liste der Filmformate zurück
             *   Return: array(int,string)
             */
            $db   = MDB2::singleton();
            $list = $db->extended->getAll(self::SQL_getBfLi);
            IsDbError($list);
            $data = [];
            foreach ($list as $wert) $data[$wert['id']] = $wert['format'];
            return $data;
        }

        protected function getBildformat() {
            // gibt den string mit dem Bildformat zurück
            $db = MDB2::singleton();
            if (empty($this->bildformat)) return null;
            $data = $db->extended->getOne(
                self::SQL_getBF, null, $this->bildformat);
            IsDbError($data);
            return $data;
        }

        protected static function getListMediaSpez() {
            /**
             *  Aufgabe: gibt eine Liste der Mediaspezifikationen zurück
             *   Return: array(int)
             */
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_getMS, 'integer');
            IsDbError($data);
            $data = getStringList($data);
            return $data;
        }

        protected function getThisMediaSpez() {
            /**
             *  Aufgabe: gibt die Liste der verwendeten Produktionstechniken zurück
             *   Return: array (string)
             */
            $list = self::getListMediaSpez();
            $data = [];
            foreach ($list as $key => $wert) :
                if (isbit($this->mediaspezi, $key)) $data[] = $wert;
            endforeach;
            return $data;
        }

        protected static function getListProdTech() {
            /**
             *  Aufgabe: gibt eine Liste der Produktionstechniken zurück
             *   Return: array (string)
             */
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_getPT, 'integer');
            IsDbError($data);
            $data = getStringList($data);
            return $data;
        }

        protected function getThisProdTech() {
            /**
             *  Aufgabe: gibt eine Liste der verwendeten Produktionstechniken zurück
             *   Return: array (string)
             */
            $list = self::getListProdTech();
            $data = [];
            foreach ($list as $key => $wert) :
                if (isbit($this->prodtechnik, $key)) $data[] = $wert;
            endforeach;
            return $data;
        }

        protected function getProdLand() {
            /**
             *  Aufgabe: Prüft, ob für diesen filmogr. Datensatz ein Hersteller
             *           angelegt ist und gibt im Erfolgsfall, das aus den Personen-
             *           daten ermittelte Land zurück.
             *   Return: array | NULL
             */
            $db       = MDB2::singleton();
            $ProdLand = $db->extended->getCol(
                'SELECT s_land.land
                 FROM
                   public.f_cast, public.p_person, public.s_land, public.s_orte
                 WHERE
                   f_cast.pid = p_person.id AND p_person.wort = s_orte.id AND
                   s_orte.land = s_land.id AND f_cast.fid = ? AND f_cast.tid = ?;',
                null, [$this->id, 1480], ['integer', 'integer']
            );
            IsDbError($ProdLand);
            return $ProdLand;
        }

        public function add($status = null) {
            /**
             *   Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
             *   Aufruf:  Status
             *   Return:  Fehlercode
             */
            global $myauth, $marty;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

            $db = MDB2::singleton();
            if ($status == false) :
                $db->beginTransaction('newFilm');
                IsDbError($db);
                // neue id besorgen
                $data = $db->extended->getOne("SELECT nextval('id_seq');");
                IsDbError($data);
                $this->id = $data;
                $this->edit(false);
            else :
                // Objekt wurde vom Eventhandler initiiert
                $types = [
                    // ACHTUNG! Reihenfolge beachten !!!
                    'integer', // gattung
                    'integer', // prodtechnik
                    'time', // laenge (Sonderformat))
                    'integer', // fsk
                    'integer', // praedikat
                    'integer', // mediaspezi
                    'integer', // bildformat
                    'text', // urauffuehr
                    'integer', // id
                    'boolean', // del
                    'integer', // editfrom
                    'date', // editdate
                    'boolean', // isvalid
                    'integer', // bild_id
                    'text', // prod_jahr
                    'text', // thema
                    'text', // quellen
                    'text', // inhalt
                    'text', // notiz
                    'text', // anmerk
                    'text', // titel
                    'text', // atitel
                    'text', // utitel
                    'integer', // sid
                    'integer']; // sfolge

                $this->edit(true);
                foreach ($this as $key => $wert) $data[$key] = $wert;
                unset($data['stitel'], $data['sdescr']);
                $erg = $db->extended->autoExecute('f_film', $data,
                                                  MDB2_AUTOQUERY_INSERT, null, $types);
                IsDbError($erg);
                $db->commit('newFilm');
                IsDbError($db);
                // ende Transaktion
            endif;
            return null;
        }

        public function edit($status = null) {
            /**
             *   Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
             *   Aufruf: array, welches die zu ändernden Felder enthält
             *   Return: none
             **/
            global $myauth, $marty;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
            if ($status == false) : // Formular anzeigen
                $data = [
                    // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                    new d_feld('serTitel', parent::getSTitelList()),
                    new d_feld('gattLi', self::getListGattung()),
                    new d_feld('praedLi', self::getListPraedikat()),
                    new d_feld('taetigLi', parent::getTaetigList()),
                    new d_feld('prodTecLi', self::getListProdTech()),
                    new d_feld('bildFormLi', self::getListBildformat()),
                    new d_feld('mediaSpezLi', self::getListMediaSpez()),
                    new d_feld('persLi', Person::getPersonLi()),
                    new d_feld('bereich', null, null, 4027),
                    new d_feld('id', $this->id),
                    new d_feld('titel', $this->titel, EDIT, 500),
                    new d_feld('atitel', $this->atitel, EDIT, 503),
                    new d_feld('utitel', $this->utitel, EDIT, 501),
                    new d_feld('stitel', $this->stitel, EDIT, 504),
                    new d_feld('sfolge', $this->sfolge, EDIT, 505),
                    new d_feld('sid', $this->sid),
                    new d_feld('bild_id', 'bilddaten array()', EDIT),
                    new d_feld('prod_jahr', $this->prod_jahr, EDIT, 576),
                    new d_feld('thema', $this->thema, EDIT, 577), // Schlagwortliste
                    new d_feld('quellen', $this->quellen, EDIT, 578),
                    new d_feld('inhalt', $this->inhalt, EDIT, 506),
                    new d_feld('notiz', $this->notiz, EDIT, 514),
                    new d_feld('anmerk', $this->anmerk, EDIT, 572),
                    new d_feld('gattung', $this->gattung, EDIT, 579),
                    new d_feld('prodtech', bit2array($this->prodtechnik), EDIT, 571),
                    new d_feld('laenge', $this->laenge, EDIT, 580, 10007),
                    new d_feld('fsk', $this->fsk, EDIT, 581),
                    new d_feld('praedikat', $this->praedikat, EDIT, 582),
                    new d_feld('bildformat', $this->bildformat, EDIT, 608),
                    new d_feld('mediaspezi', bit2array($this->mediaspezi), EDIT, 583),
                    new d_feld('urauff', $this->urauffuehr, EDIT, 584),
                    new d_feld('isvalid', $this->isvalid, IEDIT, 10009),
                ];
                // CastListe nur beim bearbeiten und nicht bei Neuanlage zeigen.
                if ($this->titel) $data[] = new d_feld('cast', $this->getCastList(), EDIT);

                $marty->assign('dialog', a_display($data));
                $marty->display('figd_dialog.tpl');
                $myauth->setAuthData('obj', serialize($this));

            else :
                /* Formular auswerten - Obj zurückspeichern wird im
                 aufrufenden Teil erledigt */

                try {
                    if (empty($this->titel) AND empty($_POST['titel']))
                        throw new Exception(null, 100);
                    else if ($_POST['titel']) $this->titel = $_POST['titel'];

                    if (isset($_POST['atitel'])) :
                        if ($_POST['atitel']) $this->atitel = $_POST['atitel'];
                        else $this->atitel = null;
                    endif;

                    if (isset($_POST['sid']))
                        $this->sid = intval($_POST['sid']); else $this->sid = null;
                    if ($this->sid AND isset($_POST['sfolge']))
                        $this->sfolge = intval($_POST['sfolge']);
                    else $this->sfolge = null;

                    if (isset($_POST['utitel'])) :
                        if ($_POST['utitel']) $this->utitel = $_POST['utitel'];
                        else $this->utitel = null;
                    endif;

                    if (isset($_POST['inhalt'])) :
                        if ($_POST['inhalt']) $this->inhalt = $_POST['inhalt'];
                        else $this->inhalt = null;
                    endif;

                    if (isset($_POST['quellen'])) :
                        if ($_POST['quellen']) $this->quellen = $_POST['quellen'];
                        else $this->quellen = null;
                    endif;

                    if (isset($_POST['anmerk'])) :
                        if ($_POST['anmerk']) $this->anmerk = $_POST['anmerk'];
                        else $this->anmerk = null;
                    endif;

                    if (isset($_POST['prod_jahr'])) :
                        if ($_POST['prod_jahr']) {
                            if (isvalid($_POST['prod_jahr'], '[\d]{1,4}'))
                                $this->prod_jahr = intval($_POST['prod_jahr']);
                            else feedback(103, 'warng');
                        } else $this->prod_jahr = null;
                    endif;

                    if (isset($_POST['thema'])) :
                        if ($_POST['thema']) $this->thema = $_POST['thema'];
                        else $this->thema = null;
                    endif;

                    if (isset($_POST['gattung'])) :
                        if ($_POST['gattung']) {
                            if (isvalid($_POST['gattung'], ANZAHL))
                                $this->gattung = intval($_POST['gattung']);
                            else throw new Exception(null, 4);
                        } else $this->gattung = null;
                    endif;

                    if (isset($_POST['prodtech']))
                        $this->prodtechnik = array2wert(0, $_POST['prodtech']);
                    else $this->prodtechnik = null;

                    if (!empty($_POST['laenge']))
                        if (isValid($_POST['laenge'], DAUER)) $this->laenge = $_POST['laenge']; else feedback(4, warng);
                    else $this->laenge = null;

                    if (isset($_POST['fsk'])) :
                        if (!empty($_POST['fsk'])) {
                            if (isvalid($_POST['fsk'], ANZAHL))
                                $this->fsk = intval($_POST['fsk']);
                            else throw new Exception(null, 4);
                        } else $this->fsk = null;
                    endif;

                    if (isset($_POST['praedikat'])) :
                        if ($_POST['praedikat']) {
                            if (isvalid($_POST['praedikat'], ANZAHL))
                                $this->praedikat = intval($_POST['praedikat']);
                            else throw new Exception(null, 4);
                        } else $this->praedikat = null;
                    endif;

                    if (isset($_POST['urauff'])) :
                        if ($_POST['urauff']) {
                            if (isvalid($_POST['urauff'], DATUM))
                                $this->urauffuehr = $_POST['urauff'];
                            else feedback(103, 'warng');
                        } else $this->urauffuehr = null;
                    endif;

                    if (isset($_POST['bildformat']))
                        if ($_POST['bildformat']) $this->bildformat = intval($_POST['bildformat']);

                    if (isset($_POST['mediaspezi']))
                        $this->mediaspezi = array2wert(0, $_POST['mediaspezi']);
                    else $this->mediaspezi = null;

                    if (isset($_POST['notiz'])) :
                        if ($_POST['notiz']) $this->notiz = $_POST['notiz'];
                        else $this->notiz = null;
                    endif;

                    $this->isvalid = false;
                    if (isset($_POST['isvalid'])) :
                        if ($_POST['isvalid']) $this->isvalid = true;
                    endif;

                    $this->editfrom = $myauth->getAuthData('uid');
                    $this->editdate = date('c', $_SERVER['REQUEST_TIME']);

                    // doppelten Datensatz abfangen
                    $number = self::ifDouble();
                    if (!empty($number) AND $number != $this->id) feedback(10008, 'warng');
                } // end try

                catch (Exception $e) {
                    $fehler[] = $e->getMessage();
                }

                foreach ($fehler as $error) $errmsg .= $error . '<br />';
                if ($errmsg) {
                    feedback(substr($errmsg, 0, -6), 'error');
                    exit;
                }

            endif; // form anzeigen/auswerten
            return null;
        }

        public function save() {
            /**
             *   Aufgabe: schreibt die Daten in die Tabelle 'f_film' zurück (UPDATE)
             *    Return: Fehlercode
             */
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
            if (!$this->id) return 4; // Abbruch: leerer Datensatz

            $types = [
                // ACHTUNG: Reihenfolge beachten!
                'integer', // gattung
                'integer', // prodtechnik
                'text', // laenge (Sonderformat))
                'integer', // fsk
                'integer', // praedikat
                'integer', // mediaspezi
                'integer', // bildformat
                'text', // urauffuehr
                'boolean', // del
                'integer', // editfrom
                'timestamp', // editdate
                'boolean', // isvalid
                'integer', // bild_id
                'text', // prod_jahr
                'text', // thema
                'text', // quellen
                'text', // inhalt
                'text', // notiz
                'text', // anmerk
                'text', // titel
                'text', // atitel
                'text', // utitel
                'integer', // sid
                'integer' // sfolge
            ];

            foreach ($this as $key => $wert) $data[$key] = $wert;
            unset($data['stitel'], $data['sdescr'], $data['id']);

            $db  = MDB2::singleton();
            $erg = $db->extended->autoExecute('f_film', $data,
                                              MDB2_AUTOQUERY_UPDATE, 'id = ' . $db->quote($this->id, 'integer'), $types);
            IsDbError($erg);
            return  null;

            /* im Moment manuell ändern
            // Serientitel schreiben
            if ($this->sid) :
                $sql = "UPDATE ONLY f_stitel SET titel = '" . $this->stitel . "', descr = '" . $this->sdescr . "'WHERE sertitel_id = " . $this->sid . ";";
                $erg =& $db->exec($sql);
                IsDbError($erg);
            endif;
             */

        }

        public function view() {
            /**
             *   Aufgabe: Ausgabe des Filmdatensatzes (an smarty) in der Detailansicht
             *    Return: none
             */
            global $myauth, $str;
            if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

            $data = parent::view();
            // name, inhalt, opt -> rechte, label,tooltip
            $data[] = new d_feld('prod_land', self::getProdLand(), VIEW, 698);
            $data[] = new d_feld('gattung', $str->getStr($this->gattung), VIEW, 579);
            $data[] = new d_feld('prodtech', self::getThisProdTech(), VIEW, 571);
            $data[] = new d_feld('laenge', $this->laenge, VIEW, 580);
            $data[] = new d_feld('fsk', $this->fsk, VIEW, 581);
            $data[] = new d_feld('praedikat', $str->getStr($this->praedikat), VIEW, 582);
            $data[] = new d_feld('bildformat', self::getBildformat(), VIEW, 608);
            $data[] = new d_feld('mediaspezi', self::getThisMediaSpez(), VIEW, 583);
            $data[] = new d_feld('urrauff', $this->urauffuehr, VIEW, 584);

            return $data;
        }
    }
}