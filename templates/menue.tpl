{**************************************************************
    Enthält die statische Menüstruktur im linken Bereich
    der Seite

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<div id='menue'>
    <form action='index.php' method='get'>
        <button class='noBG'><img src='images/diaf.png' alt='DIAF' /></button>
        <br /><br />
        <button name='sektion' value='P'>{$dlg['pers']}</button>
        <button name='sektion' value='F'>{$dlg['fgraf']}</button>
        <button name='sektion' value='Y'>{$dlg['i2d']}</button>
        <button name='sektion' value='Z'>{$dlg['i3d']}</button>
        <button name='sektion' value='K'>{$dlg['fkop']}</button>
{if !empty($dlg['messg'])}<button name='sektion' value='news'>{$dlg['messg']}</button>{/if}
{if !empty($dlg['pref'])}<button name='sektion' value='admin'>{$dlg['pref']}</button>{/if}

        <br /><button class='flag' name='aktion' value='de'>
            <img src='images/flag-german.png' alt='de' />
        </button>
        <button class='flag' name='aktion' value='en'>
            <img src='images/flag-english.png' alt='en' />
        </button>
        <button class='flag' name='aktion' value='fr'>
            <img src='images/flag-french.png' alt='fr' />
        </button><br />
        <span class='note' style='padding-top:15px;'><br />{$dlg['realname']}<br /></span>
{if !empty($dlg['logout'])}<button name='aktion' value='logout'>{$dlg['logout']}</button>{/if}
{if !empty($dlg['login'])}<button name='aktion' value='login'>{$dlg['login']}</button>{/if}
    </form>
</div>