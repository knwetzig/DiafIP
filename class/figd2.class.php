<?php namespace DiafIP {
    use Exception, MDB2;
    /**
     * Klassenbibliotheken für Filmografische Daten
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP
     * @version     $Id$
     * @since       r52
     * @requirement PHP Version >= 5.4
     */
    final class Film extends FibiMain implements iFilm {
        const
            SQL_get      = 'SELECT gattung, prodtechnik, laenge, fsk, praedikat,
                            bildformat, mediaspezi, urauffuehr FROM f_film2 WHERE id = ?;',
            SQL_getPraed = 'SELECT * FROM f_praed ORDER BY praed ASC;',
            SQL_getGenre = 'SELECT * FROM f_genre;',
            SQL_getBfLi  = 'SELECT * FROM f_bformat;',
            SQL_getBF    = 'SELECT format FROM f_bformat WHERE id = ?;',
            SQL_getMS    = 'SELECT * FROM f_mediaspezi;',
            SQL_getPT    = 'SELECT * FROM f_prodtechnik;';

        public function __construct($nr = null) {
            parent::__construct($nr);
            $this->content['gattung']  = null;
            $this->content['prodtechnik']   = null;
            $this->content['laenge'] = null;
            $this->content['fsk'] = null;
            $this->content['praedikat'] = 0;
            $this->content['mediaspezi'] = 0;
            $this->content['bildformat'] = 0;
            $this->content['urauffuehr'] = null;
            if ((isset($nr)) AND is_numeric($nr)) self::get(intval($nr));
        }

        /**
         * @param int $nr
         */
        protected function get($nr) {
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

            if ($data) :
                foreach ($data as $key => $val) $this->content[$key] = $val;
            else:
                feedback(4, 'error'); // kein Datensatz vorhanden
                exit(4);
            endif;
        }

        public function add($status = null) {
            /**
             *   Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
             *   Aufruf:  Status
             *   Return:  Fehlercode
             */
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

            $db = MDB2::singleton();
            if ($status == false) :
                $db->beginTransaction('newFilm');
                IsDbError($db);
                // neue id besorgen
                $data = $db->extended->getOne("SELECT nextval('id_seq');");
                IsDbError($data);
                $this->content['id'] = $data;
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
                $data[] = null;
                foreach ($this as $key => $wert) $data[$key] = $wert;
                unset($data['stitel'], $data['sdescr']);
                $erg = $db->extended->autoExecute('f_film', $data, MDB2_AUTOQUERY_INSERT, null, $types);
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
                $data = [];
                    // $name,$inhalt optional-> $rechte,$label,$tooltip,valString
                $data[] = new d_feld('serTitel', parent::getSTitelList());
                $data[] = new d_feld('gattLi', self::getListGattung());
                $data[] = new d_feld('praedLi', self::getListPraedikat());
                $data[] = new d_feld('taetigLi', parent::getTaetigList());
                $data[] = new d_feld('prodTecLi', self::getListProdTech());
                $data[] = new d_feld('bildFormLi', self::getListBildformat());
                $data[] = new d_feld('mediaSpezLi', self::getListMediaSpez());
                $data[] = new d_feld('persLi', Person::getPersList());
                $data[] = new d_feld('bereich', null, null, 4027);
                $data[] = new d_feld('id', $this->content['id']);
                $data[] = new d_feld('titel', $this->content['titel'], EDIT, 500);
                $data[] = new d_feld('atitel', $this->content['atitel'], EDIT, 503);
                $data[] = new d_feld('utitel', $this->content['utitel'], EDIT, 501);
                $data[] = new d_feld('stitel', $this->content['stitel'], EDIT, 504);
                $data[] = new d_feld('sfolge', $this->content['sfolge'], EDIT, 505);
                $data[] = new d_feld('sid', $this->content['sid']);
                $data[] = new d_feld('bild_id', 'bilddaten array()', EDIT);
                $data[] = new d_feld('prod_jahr', $this->content['prod_jahr'], EDIT, 576);
                $data[] = new d_feld('thema', $this->content['thema'], EDIT, 577); // Schlagwortliste
                $data[] = new d_feld('quellen', $this->content['quellen'], EDIT, 578);
                $data[] = new d_feld('inhalt', $this->content['inhalt'], EDIT, 506);
                $data[] = new d_feld('notiz', $this->content['notiz'], EDIT, 514);
                $data[] = new d_feld('anmerk', $this->content['anmerk'], EDIT, 572);
                $data[] = new d_feld('gattung', $this->content['gattung'], EDIT, 579);
                $data[] = new d_feld('prodtech', bit2array($this->content['prodtechnik']), EDIT, 571);
                $data[] = new d_feld('laenge', $this->content['laenge'], EDIT, 580, 10007);
                $data[] = new d_feld('fsk', $this->content['fsk'], EDIT, 581);
                $data[] = new d_feld('praedikat', $this->content['praedikat'], EDIT, 582);
                $data[] = new d_feld('bildformat', $this->content['bildformat'], EDIT, 608);
                $data[] = new d_feld('mediaspezi', bit2array($this->content['mediaspezi']), EDIT, 583);
                $data[] = new d_feld('urauff', $this->content['urauffuehr'], EDIT, 584);
                $data[] = new d_feld('isvalid', $this->content['isvalid'], IEDIT, 10009);
                // CastListe nur beim bearbeiten und nicht bei Neuanlage zeigen.
                if ($this->content['titel']) $data[] = new d_feld('cast', $this->getCastList(), EDIT);

                $marty->assign('dialog', a_display($data));
                $marty->display('figd_dialog.tpl');
                $myauth->setAuthData('obj', serialize($this));

            else :
                /* Formular auswerten - Obj zurückspeichern wird im
                 aufrufenden Teil erledigt */

                try {
                    if (empty($this->content['titel']) AND empty($_POST['titel']))
                        throw new Exception(null, 100);
                    else if ($_POST['titel']) $this->content['titel'] = $_POST['titel'];

                    if (isset($_POST['atitel'])) :
                        if ($_POST['atitel']) $this->content['atitel'] = $_POST['atitel'];
                        else $this->content['atitel'] = null;
                    endif;

                    if (isset($_POST['sid']))
                        $this->content['sid'] = intval($_POST['sid']); else $this->content['sid'] = null;
                    if ($this->content['sid'] AND isset($_POST['sfolge']))
                        $this->content['sfolge'] = intval($_POST['sfolge']);
                    else $this->content['sfolge'] = null;

                    if (isset($_POST['utitel'])) :
                        if ($_POST['utitel']) $this->content['utitel'] = $_POST['utitel'];
                        else $this->content['utitel'] = null;
                    endif;

                    if (isset($_POST['inhalt'])) :
                        if ($_POST['inhalt']) $this->content['inhalt'] = $_POST['inhalt'];
                        else $this->content['inhalt'] = null;
                    endif;

                    if (isset($_POST['quellen'])) :
                        if ($_POST['quellen']) $this->content['quellen'] = $_POST['quellen'];
                        else $this->content['quellen'] = null;
                    endif;

                    if (isset($_POST['anmerk'])) :
                        if ($_POST['anmerk']) $this->content['anmerk'] = $_POST['anmerk'];
                        else $this->content['anmerk'] = null;
                    endif;

                    if (isset($_POST['prod_jahr'])) :
                        if ($_POST['prod_jahr']) {
                            if (isvalid($_POST['prod_jahr'], '[\d]{1,4}'))
                                $this->content['prod_jahr'] = intval($_POST['prod_jahr']);
                            else feedback(103, 'warng');
                        } else $this->content['prod_jahr'] = null;
                    endif;

                    if (isset($_POST['thema'])) :
                        if ($_POST['thema']) $this->content['thema'] = $_POST['thema'];
                        else $this->content['thema'] = null;
                    endif;

                    if (isset($_POST['gattung'])) :
                        if ($_POST['gattung']) {
                            if (isvalid($_POST['gattung'], ANZAHL))
                                $this->content['gattung'] = intval($_POST['gattung']);
                            else throw new Exception(null, 4);
                        } else $this->content['gattung'] = null;
                    endif;

                    if (isset($_POST['prodtech']))
                        $this->content['prodtechnik'] = array2wert(0, $_POST['prodtech']);
                    else $this->content['prodtechnik'] = null;

                    if (!empty($_POST['laenge']))
                        if (isValid($_POST['laenge'], DAUER)) $this->content['laenge'] = $_POST['laenge']; else feedback(4, 'warng');
                    else $this->content['laenge'] = null;

                    if (isset($_POST['fsk'])) :
                        if (!empty($_POST['fsk'])) {
                            if (isvalid($_POST['fsk'], ANZAHL))
                                $this->content['fsk'] = intval($_POST['fsk']);
                            else throw new Exception(null, 4);
                        } else $this->content['fsk'] = null;
                    endif;

                    if (isset($_POST['praedikat'])) :
                        if ($_POST['praedikat']) {
                            if (isvalid($_POST['praedikat'], ANZAHL))
                                $this->content['praedikat'] = intval($_POST['praedikat']);
                            else throw new Exception(null, 4);
                        } else $this->content['praedikat'] = null;
                    endif;

                    if (isset($_POST['urauff'])) :
                        if ($_POST['urauff']) {
                            if (isvalid($_POST['urauff'], DATUM))
                                $this->content['urauffuehr'] = $_POST['urauff'];
                            else feedback(103, 'warng');
                        } else $this->content['urauffuehr'] = null;
                    endif;

                    if (isset($_POST['bildformat']))
                        if ($_POST['bildformat']) $this->content['bildformat'] = intval($_POST['bildformat']);

                    if (isset($_POST['mediaspezi']))
                        $this->content['mediaspezi'] = array2wert(0, $_POST['mediaspezi']);
                    else $this->content['mediaspezi'] = null;

                    if (isset($_POST['notiz'])) :
                        if ($_POST['notiz']) $this->content['notiz'] = $_POST['notiz'];
                        else $this->content['notiz'] = null;
                    endif;

                    $this->content['isvalid'] = false;
                    if (isset($_POST['isvalid'])) :
                        if ($_POST['isvalid']) $this->content['isvalid'] = true;
                    endif;

                    $this->content['editfrom'] = $myauth->getAuthData('uid');
                    $this->content['editdate'] = date('c', $_SERVER['REQUEST_TIME']);

                    // doppelten Datensatz abfangen
                    $number = self::ifDouble();
                    if (!empty($number) AND $number != $this->content['id']) feedback(10008, 'warng');
                } // end try

                catch (Exception $e) {
                    $fehler[] = $e->getMessage();
                }

                $errmsg = null;
                $fehler = null;
                foreach ($fehler as $error) $errmsg .= $error . '<br />';
                if ($errmsg) {
                    feedback(substr($errmsg, 0, -6), 'error');
                    exit;
                }

            endif; // form anzeigen/auswerten
            return null;
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
             *  Aufgabe: gibt eine Liste der Prädikate aus
             *   Return: array(int, string)
             */
            $db = MDB2::singleton();
            global $str;
            $list = $db->extended->getCol(self::SQL_getPraed, 'integer');
            IsDbError($list);
            return $str->getStrList($list);
        }

        protected static function getListProdTech() {
            /**
             *  Aufgabe: gibt eine Liste der Produktionstechniken zurück
             *   Return: array (string)
             */
            global $str;
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_getPT, 'integer');
            IsDbError($data);
            return $str->getStrList($data);
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

        protected static function getListMediaSpez() {
            /**
             *  Aufgabe: gibt eine Liste der Mediaspezifikationen zurück
             *   Return: array(int)
             */
            global $str;
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_getMS, 'integer');
            IsDbError($data);
            return $str->getStrList($data);
        }

        public function save() {
            /**
             *   Aufgabe: schreibt die Daten in die Tabelle 'f_film' zurück (UPDATE)
             *    Return: Fehlercode
             */
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;
            if (!$this->content['id']) return 4; // Abbruch: leerer Datensatz

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

            $data[] = null;
            foreach ($this->content as $key => $wert) $data[$key] = $wert;
            unset($data['stitel'], $data['sdescr'], $data['id']);

            $db  = MDB2::singleton();
            $erg = $db->extended->autoExecute('f_film', $data, MDB2_AUTOQUERY_UPDATE, 'id = ' . $db->quote($this->content['id'], 'integer'), $types);
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
             *   Aufgabe: Ausgabe des Filmdatensatzes (an smarty)
             *    Return: array
             */
            global $myauth, $str;
            if (!isBit($myauth->getAuthData('rechte'), VIEW)) return 2;

            $data = parent::view();
            // name, inhalt, opt -> rechte, label,tooltip
            $data[] = new d_feld('prod_land', self::getProdLand(), VIEW, 698);
            $data[] = new d_feld('gattung', $str->getStr($this->content['gattung']), VIEW, 579);
            $data[] = new d_feld('prodtech', self::getThisProdTech(), VIEW, 571);
            $data[] = new d_feld('laenge', $this->content['laenge'], VIEW, 580);
            $data[] = new d_feld('fsk', $this->content['fsk'], VIEW, 581);
            $data[] = new d_feld('praedikat', $str->getStr($this->content['praedikat']), VIEW, 582);
            $data[] = new d_feld('bildformat', self::getBildformat(), VIEW, 608);
            $data[] = new d_feld('mediaspezi', self::getThisMediaSpez(), VIEW, 583);
            $data[] = new d_feld('urrauff', $this->content['urauffuehr'], VIEW, 584);

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
                'SELECT s_land.land FROM public.f_cast, public.p_person2, public.s_land, public.s_orte
                 WHERE
                   f_cast.pid = p_person2.id AND p_person2.wort = s_orte.id AND
                   s_orte.land = s_land.id AND f_cast.fid = ? AND f_cast.tid = ?;',
                null, [$this->content['id'], 1480], ['integer', 'integer']
            );
            IsDbError($ProdLand);
            return $ProdLand;
        }

        protected function getThisProdTech() {
            /**
             *  Aufgabe: gibt eine Liste der verwendeten Produktionstechniken zurück
             *   Return: array (string)
             */
            $list = self::getListProdTech();
            $data = [];
            foreach ($list as $key => $wert) :
                if (isbit($this->content['prodtechnik'], $key)) $data[] = $wert;
            endforeach;
            return $data;
        }

        protected function getBildformat() {
            // gibt den string mit dem Bildformat zurück
            $db = MDB2::singleton();
            if (empty($this->content['bildformat'])) return null;
            $data = $db->extended->getOne(
                self::SQL_getBF, null, $this->content['bildformat']);
            IsDbError($data);
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
                if (isbit($this->content['mediaspezi'], $key)) $data[] = $wert;
            endforeach;
            return $data;
        }
    }
}