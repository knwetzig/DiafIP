{config_load file="mc.conf" section="setup"}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{#title#}</title>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    <!--    <meta http-equiv='Pragma' content='no-cache' />
    <meta http-equiv='Cache-Control' content='post-check=0, pre-check=0, FALSE' /> -->
	<link rel='stylesheet' type='text/css' href="profile/{$dlg['profil']}/style.css"/>
    <link rel='shortcut icon' href='favicon.ico' />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!-- LOCAL EINBINDEN! script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="js/jquery.selectbox.min.js"></script>
	<script src="js/functions.js" type="text/javascript"></script -->
	<script type="text/javascript" src="js/overlib/overlib.js" ></script>
</head>
<body>
    <div id='overDiv'></div>
{* sofern benötigt
    <script language="javascript" src="inc/overlib/overlib_anchor.js"></script>
    <script language="javascript" src="inc/overlib/overlib_crossframe.js"></script>
    <script language="javascript" src="inc/overlib/overlib_cssstyle.js"></script>
    <script language="javascript" src="inc/overlib/overlib_exclusive.js"></script>
    <script language="javascript" src="inc/overlib/overlib_followscroll.js"></script>
    <script language="javascript" src="inc/overlib/overlib_hideform.js"></script>
    <script language="javascript" src="inc/overlib/overlib_shadow.js"></script>
    <script language="javascript" src="inc/overlib/overlib_centerpopup.js"></script>
*}
    <script type="text/javascript">
	    var ol_width = '250px';
    </script>

    <header id="header">
	    <div class="header-content">
		    <div class="header-content-btn-nav">
			    <div class="navicon-line"></div>
		    </div>
		    <div class="header-content-logo"><a href="/"></a></div>
            <!-- Ooops, hier ist uns wohl die Mehrsprachigkeit abhanden gekommen (title-Tag) ;( -->
		    <div class="header-content-btn-contact"><a href="mailto:kontakt@diaf.de" title="Kontaktieren Sie
		    uns"></a></div>
	    </div>
    </header>

    <div id='menue'>
	    <form action='index.php' method='get'>
		    <button class='noBG'><img src='images/diaf.png' alt='DIAF'/></button>
		    <p>
			    <button name='sektion' value='P'>{$dlg['P']}</button>
				<button name='sektion' value='F'>{$dlg['F']}</button>
				{*	<button name='sektion' value='Y'>{$dlg['Y']}</button>
					<button name='sektion' value='Z'>{$dlg['Z']}</button>
					<button name='sektion' value='K'>{$dlg['K']}</button> *}
			    {if !empty($dlg['messg'])}
				    <button name='sektion' value='news'>{$dlg['messg']}</button>{/if}
			    {if !empty($dlg['pref'])}
				    <button name='sektion' value='admin'>{$dlg['pref']}</button>{/if}
		    </p>
		    <p>
			    <button class='flag' name='aktion' value='de'>
				    <img src='images/flag-german.png' alt='de'/>
			    </button>
			    <button class='flag' name='aktion' value='en'>
				    <img src='images/flag-english.png' alt='en'/>
			    </button>
			    <button class='flag' name='aktion' value='fr'>
				    <img src='images/flag-french.png' alt='fr'/>
			    </button>
		    </p>
		    <span class='note'>{$dlg['realname']}</span>
		    {if !empty($dlg['logout'])}
			    <button name='aktion' value='logout'>{$dlg['logout']}</button>{/if}
		    {if !empty($dlg['login'])}
			    <button name='aktion' value='login'>{$dlg['login']}</button>{/if}
	    </form>
    </div>