<?php namespace DiafIP {
    use ErrorException, MDB2;
    global $myauth, $marty, $str;
    /**
     * Import von Personen und Filmografischen Daten
     *
     * Date: 04.11.15
     * Time: 11:23
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
     * @requirement PHP Version >= 5.4
     *
     * File dient dem Import von Filmografischen und Personendaten. Das Dateiformat ist
     * eine gültige XML-Datei entsprechend der vorgegeben DTD.
     * Die Datei wird Datensatzweise geparst und in die DB-eingepflegt. ---soweit die Grundidee ---
     */

    if (!$myauth->getAuth()) :
        feedback(108, 'error'); // Fremdaufruf!
        exit();
    endif;

    $marty->assign('dialog', ['bereich' => [1 => $str->getStr(492)]]);
    $marty->display('main_bereich.tpl');

    if (!isBit($myauth->getAuthData('rechte'), RE_IEDIT)) :
        feedback(2, 'error');
        exit(2);
    endif;

    $filme = null;
    if (!file_exists('defa_export.xml')) :
        feedback('Konnte defa_export.xml nicht öffnen');
        exit(1);
    endif;

    libxml_use_internal_errors(true);
    $filme = simplexml_load_file('defa_export.xml');
    if ($filme === false) :
        feedback('Importieren der Daten fehlgeschlagen', 'error');
        echo "<div style='font-family: monospace;font-weight: normal;white-space: pre;'>";
        foreach(libxml_get_errors() as $error) :
            echo "\t", $error->message;
        endforeach;
        echo "</div>";
        exit;
    endif;

    $db = MDB2::singleton();
    $gattg = Film::getListGattung();
    $taetig = Film::getTaetigList();
    function putNotiz($text, &$arr) {
        if(!empty($arr['notiz'])) $arr['notiz'] .= "\n";
        $arr['notiz'] .= $text;
    }

    // ==========  Ende der Vorbereitungsarbeiten ==========

    try {
        $xml = new \SimpleXMLIterator('defa_export.xml', LIBXML_NOBLANKS, true);
        for($xml->rewind(); $xml->valid(); $xml->next()) :              // FILME

            // Start -> TRANSAKTION
            $db->beginTransaction('newFilm');
            IsDbError($db);

            // Anfordern Id;
            $Id = $db->extended->getOne("SELECT nextval('entity_id_seq');");
            IsDbError($Id);

            $inhalt = [];
            $inhalt['id'] = $Id;
            $inhalt['bereich'] = 'F';
            $inhalt['editfrom'] = 2;
            $inhalt['quellen'] = "Übernahme aus dem Datenbestand der DEFA-Stiftung";
            $inhalt['prodtechnik'] = null;
            $inhalt['notiz'] = null;
            $castLi = [];
            $JahrVon = null;
            $JahrBis = null;
            $rolle = null;
            $person = null;

            foreach($xml->getChildren() as $name => $data) :
                switch($name) :
                    case 'DEFA_FILMID': $inhalt['anmerk'] = 'DEFA-FilmId: '.strval($data); break;
                    case 'TITELORIGINAL_ARCHIV': $inhalt['titel'] = strval($data); break;
                    case 'TITELSONSTIGE': $inhalt['atitel'] = strval($data); break;
                    case 'SID': $inhalt['sid']  = intval($data); break;
                    case 'SFOLGE': $inhalt['sfolge'] = intval($data); break;
                    case 'LITERARISCHEVORLAGE': $inhalt['anmerk'] .= "\nLiterarische Vorlage: ".strval($data); break;
                    case 'FILMART': putNotiz('Filmart: '.strval($data), $inhalt); break;
                    case 'TRICKART':
                        $ta = strval($data);
                        $elemente = preg_split('/[\s,-\\/]+/', $ta);
                        foreach($elemente as $art) :
                            switch($art) :
                                case 'Trickfilm':
                                case 'Zeichen' :
                                case 'Zeichentrick' :
                                case 'Zeichnetrickfilm' :
                                    $inhalt['prodtechnik'] += 1;       // [0] Zeichenanimation
                                    break;

                                case 'Puppen':
                                case 'Puppentrick' :
                                case 'Puppentrickfilm' :
                                    $inhalt['prodtechnik'] += 2;       // [1] Figurenanimation
                                    break;

                                case 'Knet':
                                case 'Relieftrickfilm' :
                                case 'Sandanimationsfilm' :
                                    $inhalt['prodtechnik'] += 4;       // [2] Materialanimation
                                    break;

                                case 'Handpuppen':
                                case 'Handpuppenfilm':
                                case 'Marionettem':
                                case 'Marionettenfilm':
                                    $inhalt['prodtechnik'] += 8;       // [3] Puppenspiel
                                    break;

                                case 'Silhouetten':
                                case 'Silhouettenfilm':
                                    $inhalt['prodtechnik'] += 16;      // [4] Silhouettenanimation
                                    break;

                                case 'Flach':
                                case 'Flachfiguren':
                                case 'Flachfigurenfilm' :
                                case 'Flachtrickfilm':
                                    $inhalt['prodtechnik'] += 32;      // [5] Flachfigurenanimation
                                    break;

                                case '':
                                    $inhalt['prodtechnik'] += 64;      // [6] Computeranamiation
                                    break;

                                case 'Real':
                                case 'Realfilm' :
                                case 'Realteil' :
                                    $inhalt['prodtechnik'] += 128;     // [7] Realfilm
                                    break;

                                case 'Realsilhouetten' :
                                    $inhalt['prodtechnik'] += 134;     // [4] + [7]
                            endswitch;
                        endforeach;
                        break;
                    case 'GENRE':
                        $genre = array_search(strval($data),$gattg);
                        if($genre) $inhalt['gattung'] = $genre; else putNotiz('Genre: '.strval($data),$inhalt);
                        break;
                    case 'HERSTELLUNGSJAHRVON': $JahrVon = intval($data); break;
                    case 'HERSTELLUNGSJAHRBIS': $JahrBis = intval($data); break;
                    case 'KURZINHALT': $inhalt['descr'] = strval($data); break;
                    case 'SCHLAGWORTE': putNotiz("Schlagworte: ".strval($data), $inhalt); break;
                    case 'FARBE': if(strval($data) === 'sw') $inhalt['mediaspezi'] = 1; break;
                    case 'NOTIZ': putNotiz(strval($data), $inhalt); break;
                    case 'AUFTRAGGEBER':
                        $castLi[] = ['fid' => $Id,
                                     'pid' => intval(PName::getIdFromName(strval($data->FIRMA))[0]),
                                     'tid' => array_search((strval($data->TYP)), $taetig)];

                        foreach($data as $key => $item ) :
                            if($key === 'FILMPERSON') :
                                if(!empty($item->ROLLE)) $rolle = array_search((strval($item->ROLLE)), $taetig);
                                foreach($item as $fp) :
                                    $person = PName::getIdFromName(trim(strval($fp->NACHNAME)),trim(strval
                                                                                                       ($fp->VORNAME)));
                                    if(!empty($person) AND $rolle)
                                        $castLi[] = ['fid' => $Id, 'pid' => $person[0],'tid' => $rolle];
                                endforeach;
                            endif;
                        endforeach;
                        break;

                endswitch;
            endforeach;
            if (!empty($JahrBis)) $inhalt['prod_jahr'] = $JahrBis; else $inhalt['prod_jahr'] = $JahrVon;

            $erg = $db->extended->autoExecute('f_film2', $inhalt, MDB2_AUTOQUERY_INSERT);
            IsDbError($erg);

            $db->commit('newFilm');
            IsDbError($db);            // ende Transaktion

            echo "Der Film <span class='fett'>{$inhalt['titel']}</span> wurde erfolgreich unter der Id: $Id angelegt.<br>";
            foreach ($castLi as $var) :
                Film::addCast($var);
            endforeach;
            unset($film, $inhalt, $cast);
        endfor;
    }
    /** __________ Auswertung __________ */
    catch (ErrorException $e) {
        switch ($e->getSeverity()) :
            case E_WARNING :
                feedback($e->getMessage(), 'warng');
                break;

            case E_ERROR :
                feedback($e->getMessage(), 'error');
                exit();
        endswitch;
    }
}