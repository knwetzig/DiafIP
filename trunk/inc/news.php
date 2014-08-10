<?php
/**************************************************************
   Enthält ein internes Board zum Austauschen von Nachrichten.

$Rev::                              $
$Author::                           $
$Date::                             $
$URL$

***** (c) DIAF e.V. *******************************************/

if (!$myauth->checkAuth()) {
    feedback(108, 'error');
    exit();
}

if ($myauth->getAuthData('rechte') < 2) {
    feedback(2, 'error');
    exit();
}

echo '<div class="bereich">Nachrichten</div>';
if (!isset($_POST['aktion'])) {
    // einfügen -> ist Benutzer berechtigt neue Artike zu erstellen
    echo "<form method='post'>
            <input type='submit' name='aktion' value='neu' />
            <input type='hidden' name='sektion' value='news' />
        </form>";
}

switch(isset($_POST['aktion'])?$_POST['aktion']:'') :

    case "neu":
       if (!isset($_POST['submit'])) {
            // Formular anzeigen

?>
            <form method='post'>
                <fieldset><legend> Beitrag erstellen </legend>
                    <input style='width:500px' type='text' name='titel' value='Titel eingeben' onfocus="if (this.value=='Titel eingeben'){this.value='';}" /><br />
                    <textarea name='text' style='width:500px;height:300px'></textarea><br />
                    <input type='hidden' name='sektion' value='news' />
                    <input type='hidden' name='aktion' value='neu' />
                    <input type='submit' name='submit' />
                </fieldset>
            </form>
<?php
        } else {
            //  Auswertung evt. Eingaben
            if (!preg_match('/[!-ÿ]/',$_POST['titel'])) {
                feedback(100, 'warng');
                break;
            }
            $a = array(
                'titel'     => $_POST['titel'],
                'inhalt'    => $_POST['text'],
                'editfrom'  => $myauth->getAuthData('uid')
            );
            $data = $db->extended->autoExecute('s_news', $a,
                MDB2_AUTOQUERY_INSERT, null, array('text','text','integer'));
            IsDbError($data);
            unset($_POST['aktion']);
        }
        break; // Ende --neu--

    case "edit" :
        if (!isset($_POST['submit'])) {
            $sql = "SELECT * FROM s_news WHERE id = {$_POST['news']};";
            $data= &$db->extended->getRow($sql);
            IsDbError($data);
            // Formular anzeigen
            echo <<<EDITFORM
<form method='post'>
    <fieldset>
        <legend> Beitrag bearbeiten </legend>
        <input type='text' name='titel' value='{$data['titel']}' style='width:500px' /><br />
        <textarea name='text' style='width:500px;height:300px'>{$data['inhalt']}</textarea><br />
        <input type='hidden' name='sektion' value='news' />
        <input type='hidden' name='aktion' value='edit' />
        <input type='hidden' name='news' value='{$data['id']}' />
        <input type='submit' name='submit' />
    </fieldset>
</form>
EDITFORM;
        } else {
            // Auswertung evt. Eingaben
            if (!preg_match('/[!-ÿ]/',$_POST['titel'])) {
                feedback(100, 'warng');
            }
            $a = array(
                'titel'  => $_POST['titel'],
                'inhalt' => $_POST['text']
            );
            $data = $db->extended->autoExecute('s_news', $a,
                MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($_POST['news'],'integer'), array('text', 'text'));
            IsDbError($data);
            unset($_POST['aktion']);
        }
    break;

    case "del" : // Löschen ohne Formular
        if (isset($_POST['news']))  {
            $data = $db->extended->autoExecute('s_news', null,
                MDB2_AUTOQUERY_DELETE, 'id = '.$db->quote($_POST['news'],'integer'));
            IsDbError($data);
        } else;

        unset($_POST['aktion']);
endswitch;


// Anzeige von die Scheiß
$sql = "SELECT
            s_news.id AS nid,
            s_news.titel,
            s_news.inhalt,
            s_news.editdate AS chdatum,
            s_news.editfrom AS autor,
            s_auth.realname
        FROM
            public.s_news,
            public.s_auth
        WHERE
            s_news.editfrom = s_auth.uid
        ORDER BY
            s_news.editdate DESC;";
$data = $db->extended->getAll($sql, array('integer','text','text','date','text'));
IsDbError($data);

foreach ($data as $wert) :
    echo "<hr /><form method='post'><span style='float:right' class='note'>\n"
        .$wert['chdatum']."&nbsp;|&nbsp;".$wert['realname']."&nbsp;\n";
    /* Nutzer berechtigt zu editieren? */
    if ($myauth->getAuthdata('uid') === $wert['autor'] OR
            isbit($myauth->getAuthData('rechte'), SU)) {
        echo "<button class='small' name='aktion' value='edit'><img src='images/edit.png' /></button>\n";
        echo "<button class='small' name='aktion' value='del'><img src='images/del.png' /></button>\n";
    }
    echo "<input type='hidden' name='sektion' value='news' />
        <input type='hidden' name='news' value='{$wert['nid']}' /></span>\n";

    // Der eigtl. Inhalt
    echo "<div style='font-weight:bold'>".$wert['titel']."</div>\n";
    echo "<p style='white-space:normal'>".nl2br(changetext($wert['inhalt']))."</p>\n";
    echo "</form>\n";
endforeach;
// Ende Anzeige
?>