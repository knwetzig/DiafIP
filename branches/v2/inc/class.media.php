<?php
/**************************************************************

    Klassenbibliothek für Bildverwaltung / -bearbeitung

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/
// DB->BLOB's http://pear.php.net/manual/en/package.database.mdb2.intro-fetch.php

interface image {
    public function add();
    public function del();
    public function view();
}

/** ==========================================================================
                               BILD CLASS
========================================================================== **/
class bild implements image {
    protected
        $id     = null,
        $img    = null,     // ressource oder was?
        $thumb  = null,     // dito
        $titel  = null,
        $descr  = null,     // Beschreibung
        $img_x  = null,
        $img_y  = null;

    public function __construct($nr = null) {
        if (isset($nr) AND is_numeric($nr)) self::get($nr);
    }

    protected function get($nr) {
    /****************************************************************
    *  Aufgabe:
    *   Aufruf:
    *   Return: void
    ****************************************************************/
    }

    public function add() {
    /****************************************************************
    *  Aufgabe:
    *   Aufruf:
    *   Return:
    ****************************************************************/
        function testValidUpload($code) {
            if ($code == UPLOAD_ERR_OK) return;

            switch ($code) :
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE: $msg = d_feld::getString(450); break;
                case UPLOAD_ERR_PARTIAL: $msg = d_feld::getString(451); break;
                case UPLOAD_ERR_NO_FILE: $msg = d_feld::getString(452); break;
                case UPLOAD_ERR_NO_TMP_DIR: $msg = d_feld::getString(453); break;
                case UPLOAD_ERR_CANT_WRITE: $msg = d_feld::getString(454); break;
                case UPLOAD_ERR_EXTENSION: $msg = d_feld::getString(455); break;
                default: $msg = d_feld::getString(456);
            endswitch;
            throw new Exception($msg);
        }

        global $myauth, $smarty;
        if (!isBit($myauth->getAuthData('rechte'), EDIT)) return 2;

        $db = MDB2::singleton();
        $fehler = array();
        $errmsg = '';
        $types = array(
            // ACHTUNG! Reihenfolge beachten !!!
            'integer',  // id
            'blob',     // img
            'blob',     // thumb
            'text',     // titel
            'text',     // descr
            'integer',  // img_x
            'integer'); // img_y

        try {
            if (!array_key_exists('bild', $_FILES))
                throw new Exception(d_feld::getString(457));

            $image = $_FILES['bild'];

            // Prüfen, das die Datei ordnungsgemäß hochgeladen wurde
            testValidUpload($image['error']);

            if (!is_uploaded_file($image['tmp_name']))
                throw new Exception(d_feld::getString(458));
            $info = getImageSize($image['tmp_name']);
            if (!$info) throw new Exception(d_feld::getString(459));
        }

        catch (Exception $ex) {
            $fehler[] = $ex->getMessage();
        }

        // wenn keine fehler dann einfügen
        if (count($fehler) == 0) {
            $this->img      = file_get_contents($image['tmp_name']);
            $this->titel    = $_POST['titel'];
            $this->descr    = $_POST['descr'];

            $db->beginTransaction('newImage'); IsDbError($db);
            // neue id besorgen
            $data = $db->extended->getOne("SELECT nextval('m_bild_id_seq');");
            IsDbError($data);
            $this->id = (int) $data;

            $data = array(
                'id'    => $this->id,
                'img'   => pg_escape_bytea($this->img),
                'thumb' => '',
                'titel' => $this->titel,
                'descr' => $this->descr,
                'img_x' => $this->img_x,
                'img_y' => $this->img_y
            );

            $erg = $db->extended->autoExecute('m_bild', $data,
                        MDB2_AUTOQUERY_INSERT, null, $types);
            IsDbError($erg);
            $db->commit('newImage'); IsDbError($db);
            // ende Transaktion
        }

        foreach ($fehler as $error) $errmsg .= $error.'<br />';
        if ($errmsg) feedback(substr($errmsg, 0, -6), 'error');
    }   // end add

    public function del() {
    /****************************************************************
    *  Aufgabe:
    *   Aufruf:
    *   Return:
    ****************************************************************/
    }

    public function view() {
    /****************************************************************
    *  Aufgabe:
    *   Aufruf:
    *   Return:
    ****************************************************************/
        header('Content-type: image/png');
        // header('Content-length: '.$image['file_size']);

        echo pg_unescape_bytea($this->img);
    }
}