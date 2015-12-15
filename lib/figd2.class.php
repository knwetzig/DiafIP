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
            SQL_GET_FILM        = 'SELECT gattung,prodtechnik,fsk,praedikat,mediaspezi,urauffuehr,laenge,bildformat
                                   FROM f_film2
                                   WHERE id = ?;',

            GET_BILDFORMAT      = 'SELECT format
                                   FROM f_bformat
                                   WHERE id = ?;',

            SQL_GET_BILDFORMAT_LI = 'SELECT *
                                     FROM f_bformat
                                     ORDER BY id ASC;',

            SQL_GET_GENRE       = 'SELECT * FROM f_genre;',

            SQL_GET_MEDIASPEZ   = 'SELECT * FROM f_mediaspezi;',

            SQL_GET_PRAED       = 'SELECT * FROM f_praed ORDER BY praed ASC;',

            SQL_GET_PRODTECHNIK = 'SELECT * FROM f_prodtechnik;',

            TYPE_FILM           = 'integer,integer,integer,integer,integer,date,text,integer';

        private $fehler = [];

        public function __construct($nr = null) {
            parent::__construct($nr);
            $this->content['gattung']  = null;
            $this->content['prodtechnik']   = null;
            $this->content['fsk'] = null;
            $this->content['praedikat'] = 0;
            $this->content['mediaspezi'] = 0;
            $this->content['urauffuehr'] = null;
            $this->content['laenge'] = null;
            $this->content['bildformat'] = 0;
            if ((isset($nr)) AND is_numeric($nr)) :
                $db   = MDB2::singleton();
                $data = $db->extended->getRow(self::SQL_GET_FILM, list2array(self::TYPE_FILM), $nr, 'integer');
                self::WertZuwCont($data);
            endif;
        }

        /**
         * Legt neuen (leeren) Datensatz an (INSERT)
         * @param null $status
         * @return int|null
         */
        public function add($status = null) {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;

            $db = MDB2::singleton();
            if ($status == false) :
                $db->beginTransaction('newFilm');
                IsDbError($db);
                // neue id besorgen
                $data = $db->extended->getOne("SELECT nextval('entity_id_seq');");
                IsDbError($data);
                $this->content['id'] = $data;
                $this->content['bereich'] = 'F';
                $this->edit(false);
            else :
                // Objekt wurde vom Eventhandler initiiert
                $this->edit(true);
                $data = null;
                foreach ($this->content as $key => $wert) $data[$key] = $wert;
                $erg = $db->extended->autoExecute('f_film2', $data, MDB2_AUTOQUERY_INSERT, null,
                    list2array(parent::TYPE_ENTITY . parent::TYPE_FIBI . self::TYPE_FILM));
                IsDbError($erg);

                $db->commit('newFilm');
                IsDbError($db);
                // ende Transaktion
            endif;
            return null;
        }

        /**
         * Ändert die Objekteigenschaften (ohne zu speichern!)
         * @param null $status
         * @return int|null
         */
        public function edit($status = null) {
            global $myauth, $marty;

            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;
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
                $data[] = new d_feld('nameLi', PName::getNameList());
                $data[] = new d_feld('bereich', $this->content['bereich'], RE_VIEW, 4027);
                $data[] = new d_feld('id', $this->content['id']);
                $data[] = new d_feld('titel', $this->content['titel'], RE_EDIT, 500);
                $data[] = new d_feld('atitel', $this->content['atitel'], RE_EDIT, 503);
                $data[] = new d_feld('utitel', $this->content['utitel'], RE_EDIT, 501);
                $data[] = new d_feld('stitel', $this->stitel, RE_EDIT, 504);
                $data[] = new d_feld('sfolge', $this->content['sfolge'], RE_EDIT, 505);
                $data[] = new d_feld('sid', $this->content['sid']);
                $data[] = new d_feld('bild_id', 'bilddaten[]', RE_EDIT);
                $data[] = new d_feld('prod_jahr', $this->content['prod_jahr'], RE_EDIT, 576);
                $data[] = new d_feld('thema', $this->content['thema'], RE_EDIT, 577); // Schlagwortliste - array
                $data[] = new d_feld('quellen', $this->content['quellen'], RE_EDIT, 578);
                $data[] = new d_feld('inhalt', $this->content['descr'], RE_EDIT, 506);
                $data[] = new d_feld('notiz', $this->content['notiz'], RE_EDIT, 514);
                $data[] = new d_feld('anmerk', $this->content['anmerk'], RE_EDIT, 572);
                $data[] = new d_feld('gattung', $this->content['gattung'], RE_EDIT, 579);
                $data[] = new d_feld('prodtech', bit2array($this->content['prodtechnik']), RE_EDIT, 571);
                $data[] = new d_feld('laenge', $this->content['laenge'], RE_EDIT, 580, 10007);
                $data[] = new d_feld('fsk', $this->content['fsk'], RE_EDIT, 581);
                $data[] = new d_feld('praedikat', $this->content['praedikat'], RE_EDIT, 582);
                $data[] = new d_feld('bildformat', $this->content['bildformat'], RE_EDIT, 608);
                $data[] = new d_feld('mediaspezi', bit2array($this->content['mediaspezi']), RE_EDIT, 583);
                $data[] = new d_feld('urauff', $this->content['urauffuehr'], RE_EDIT, 584);
                $data[] = new d_feld('isvalid', false, RE_IEDIT, 10009);
                // CastListe nur beim bearbeiten und nicht bei Neuanlage zeigen.
                if ($this->content['titel']) $data[] = new d_feld('cast', $this->getCastList(), RE_EDIT);

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
                        if ($_POST['inhalt']) $this->content['descr'] = $_POST['inhalt'];
                        else $this->content['descr'] = null;
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
                        if ($_POST['thema']) :
                            $this->content['thema'] = preg_split("/[\s,]+/", $_POST['thema']);
                        else : $this->content['thema'] = null;
                        endif;
                    endif;

                    if (isset($_POST['gattung'])) :
                        if ($_POST['gattung']) {
                            if (isvalid($_POST['gattung'], REG_ANZAHL))
                                $this->content['gattung'] = intval($_POST['gattung']);
                            else throw new Exception(null, 4);
                        } else $this->content['gattung'] = null;
                    endif;

                    if (isset($_POST['prodtech']))
                        $this->content['prodtechnik'] = array2wert(0, $_POST['prodtech']);
                    else $this->content['prodtechnik'] = null;

                    if (!empty($_POST['laenge']))
                        if (isValid($_POST['laenge'], REG_DAUER)) $this->content['laenge'] = $_POST['laenge']; else feedback(4, 'warng');
                    else $this->content['laenge'] = null;

                    if (isset($_POST['fsk'])) :
                        if (!empty($_POST['fsk'])) {
                            if (isvalid($_POST['fsk'], REG_ANZAHL))
                                $this->content['fsk'] = intval($_POST['fsk']);
                            else throw new Exception(null, 4);
                        } else $this->content['fsk'] = null;
                    endif;

                    if (isset($_POST['praedikat'])) :
                        if ($_POST['praedikat']) {
                            if (isvalid($_POST['praedikat'], REG_ANZAHL))
                                $this->content['praedikat'] = intval($_POST['praedikat']);
                            else throw new Exception(null, 4);
                        } else $this->content['praedikat'] = null;
                    endif;

                    if (isset($_POST['urauff'])) :
                        if ($_POST['urauff']) {
                            if (isvalid($_POST['urauff'], REG_DATUM))
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
                    $this->fehler[] = $e->getMessage();
                }

                $errmsg = null;
                foreach ($this->fehler as $error) $errmsg .= $error . '<br />';
                if ($errmsg) {
                    feedback(substr($errmsg, 0, -6), 'error');
                    exit;
                }

            endif; // form anzeigen/auswerten
            return null;
        }

        /**
         * @return array
         */
        static function getListGattung() {
            $db = MDB2::singleton();
            global $str;
            $list = $db->extended->getCol(self::SQL_GET_GENRE, 'integer');
            $data = [];
            IsDbError($list);
            foreach ($list as $wert) :
                $data[$wert] = $str->getStr($wert);
            endforeach;
            asort($data);
            return $data;
        }

        /**
         * Gibt eine Liste der Prädikate aus
         * @return array
         */
        static function getListPraedikat() {
            global $str;
            $db = MDB2::singleton();
            $data = [];

            $list = $db->extended->getCol(self::SQL_GET_PRAED, 'integer');
            IsDbError($list);
            foreach($list as $wert) $data[$wert] = $str->getStr($wert);
            return $data;
        }

        /**
         * Gibt eine Liste der Produktionstechniken zurück
         * @return array (string)
         */
        static function getListProdTech() {
            global $str;
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_GET_PRODTECHNIK, 'integer');
            IsDbError($data);
            return $str->getStrList($data);
        }

        /**
         * Gibt eine Liste der Filmformate zurück
         * @return array(int,string)
         */
        protected static function getListBildformat() {
            $db   = MDB2::singleton();
            $list = $db->extended->getAll(self::SQL_GET_BILDFORMAT_LI);
            IsDbError($list);
            $data = [];
            foreach ($list as $wert) $data[$wert['id']] = $wert['format'];
            return $data;
        }

        /**
         * Gibt eine Liste der Mediaspezifikationen zurück
         * @return array(int)
         */
        protected static function getListMediaSpez() {
            global $str;
            $db   = MDB2::singleton();
            $data = $db->extended->getCol(self::SQL_GET_MEDIASPEZ, 'integer');
            IsDbError($data);
            return $str->getStrList($data);
        }

        /**
         * schreibt die Daten in die Tabelle 'f_film2' zurück (UPDATE)
         * @return int|null
         */
        public function save() {
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;
            if (!$this->content['id']) return 4; // Abbruch: leerer Datensatz
            // Preparierung Thema array -> db-Liste
            if (!empty($this->content['thema'])) $this->content['thema'] = array2list($this->content['thema']);
            $db  = MDB2::singleton();
            IsDbError($db->extended->autoExecute('f_film2', $this->content, MDB2_AUTOQUERY_UPDATE,
                'id = ' . $db->quote($this->content['id'], 'integer'),
                 list2array(parent::TYPE_ENTITY . parent::TYPE_FIBI . self::TYPE_FILM)));
            return  null;
        }

        /**
         * Ausgabe des Filmdatensatzes (an smarty)
         * @return array|int
         */
        public function view() {
            global $myauth, $str;
            if (!isBit($myauth->getAuthData('rechte'), RE_VIEW)) return 2;

            $data = parent::view();
            // name, inhalt, opt -> rechte, label,tooltip
            $data[] = new d_feld('prod_land', self::getProdLand(), RE_VIEW, 698);
            $data[] = new d_feld('gattung', $str->getStr($this->content['gattung']), RE_VIEW, 579);
            $data[] = new d_feld('prodtech', self::getThisProdTech(), RE_VIEW, 571);
            $data[] = new d_feld('laenge', $this->content['laenge'], RE_VIEW, 580);
            $data[] = new d_feld('fsk', $this->content['fsk'], RE_VIEW, 581);
            $data[] = new d_feld('praedikat', $str->getStr($this->content['praedikat']), RE_VIEW, 582);
            $data[] = new d_feld('bildformat', self::getBildformat(), RE_VIEW, 608);
            $data[] = new d_feld('mediaspezi', self::getThisMediaSpez(), RE_VIEW, 583);
            $data[] = new d_feld('urauff', $this->content['urauffuehr'], RE_VIEW, 584);
            $data[] = new d_feld('regie', self::getRegie(), RE_VIEW, 1000);

            return $data;
        }

        /**
         * Ermitttelt die Namen der Regisseure für diesen Film
         * @return array
         */
        protected function getRegie() {
            $db = MDB2::singleton();
            $Regie = $db->extended->getCol(
                'SELECT f_cast.pid FROM public.f_cast WHERE fid = ? AND tid = 1000', 'integer', $this->content['id'],
                'integer');
            IsDbError($Regie);
            $namen = [];
            foreach($Regie as $wert) :
                $pers = new PName($wert);
                $namen[] = $pers->getName();
            endforeach;
            return $namen;
        }

        /**
         * Aufgabe: Prüft, ob für diesen filmogr. Datensatz ein Hersteller
         *          angelegt ist und gibt im Erfolgsfall, das aus den Personen-
         *          daten ermittelte Land zurück.
         * @return array | NULL
         */
        protected function getProdLand() {
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

        /**
         * Gibt eine Liste der verwendeten Produktionstechniken zurück
         * @return array (string)
         */
        protected function getThisProdTech() {
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
                self::GET_BILDFORMAT, null, $this->content['bildformat']);
            IsDbError($data);
            return $data;
        }

        /**
         * Gibt die Liste der verwendeten Produktionstechniken zurück
         * @return array (string)
         */
        protected function getThisMediaSpez() {
            $list = self::getListMediaSpez();
            $data = [];
            foreach ($list as $key => $wert) :
                if (isbit($this->content['mediaspezi'], $key)) $data[] = $wert;
            endforeach;
            return $data;
        }
    }
}