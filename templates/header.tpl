{config_load file="mc.conf" section="setup"}<!DOCTYPE HTML>
<head>
    <title>{#title#}</title>
    <meta charset='utf-8'/>
	<link rel='stylesheet' href="profile/{$dlg['profil']}/style.css">
    <link rel='shortcut icon' href='favicon.ico' />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- LOCAL EINBINDEN! script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="js/jquery.selectbox.min.js"></script>
	<script src="js/functions.js" type="text/javascript"></script -->
	<script src="js/overlib/overlib.js" ></script>
</head>
<body>
    <div id='overDiv'></div>

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
			{if !empty($dlg['impr'])}
				<button  class='noBG' name="sektion" value="impr">{$dlg['impr']}</button>{/if}
	    </form>
    </div>