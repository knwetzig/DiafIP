<?php
/**
 *
 * Klassenbibliothek für Bildverwaltung / -bearbeitung
 *
 * $Rev: 98 $
 * $Author: knwetzig $
 * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
 * $URL: https://diafip.googlecode.com/svn/trunk/inc/media.class.php $
 */
// DB->BLOB's http://pear.php.net/manual/en/package.database.mdb2.intro-fetch.php

interface image {
    public function add();
    public function del();
    public function view();
}

/**
                               BILD CLASS
*/
class bild implements image {
    protected
        $id = null,
        $img = null, // ressource oder was?
        $thumb = null, // dito
        $titel = null,
        $descr = null, // Beschreibung
        $img_x = null,
        $img_y = null;

    public function __construct($nr = null) {
        if (isset($nr) AND is_numeric($nr)) self::get($nr);
    }

    protected function get($nr) {

    }

    public function add() {
         function testValidUpload($code) {
            if ($code == UPLOAD_ERR_OK) return;
            global $str;

            switch ($code) :
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $msg = $str->getStr(450);
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $msg = $str->getStr(451);
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $msg = $str->getStr(452);
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $msg = $str->getStr(453);
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $msg = $str->getStr(454);
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $msg = $str->getStr(455);
                    break;
                default:
                    $msg = $str->getStr(456);
            endswitch;
            throw new Exception($msg);
        }

        global $myauth, $marty, $str;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db     = MDB2::singleton();
        $fehler = [];
        $errmsg = '';
        $types  = [ // ACHTUNG! Reihenfolge beachten !!!
                    'integer', // id
                    'blob', // img
                    'blob', // thumb
                    'text', // titel
                    'text', // descr
                    'integer', // img_x
                    'integer']; // img_y

        try {
            if (!array_key_exists('bild', $_FILES))
                throw new Exception($str->getStr(457));

            $image = $_FILES['bild'];

            // Prüfen, das die Datei ordnungsgemäß hochgeladen wurde
            testValidUpload($image['error']);

            if (!is_uploaded_file($image['tmp_name']))
                throw new Exception($str->getStr(458));
            $info = getImageSize($image['tmp_name']);
            if (!$info) throw new Exception($str->getStr(459));
        } catch (Exception $ex) {
            $fehler[] = $ex->getMessage();
        }

        // wenn keine fehler dann einfügen
        if (count($fehler) == 0) {
            $this->img   = file_get_contents($image['tmp_name']);
            $this->titel = $_POST['titel'];
            $this->descr = $_POST['descr'];

            $db->beginTransaction('newImage');
            IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('m_bild_id_seq');");
            IsDbError($data);
            $this->id = (int)$data;

            $data = ['id'    => $this->id,
                     'img'   => pg_escape_bytea($this->img),
                     'thumb' => '',
                     'titel' => $this->titel,
                     'descr' => $this->descr,
                     'img_x' => $this->img_x,
                     'img_y' => $this->img_y];

            $erg = $db->extended->autoExecute('m_bild', $data, MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);
            $db->commit('newImage');
            IsDbError($db);
            // ende Transaktion
        }

        foreach ($fehler as $error) $errmsg .= $error . '<br />';
        if ($errmsg) feedback(substr($errmsg, 0, -6), 'error');
    } // end add

    public function del() {
     }

    public function view() {
        header('Content-type: image/png');
        // header('Content-length: '.$image['file_size']);

        echo pg_unescape_bytea($this->img);
    }
}