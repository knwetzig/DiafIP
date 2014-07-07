{**************************************************************

    Template für den Eingabedialog-Namen

call:   class.person.php
class:  PName/Person
proc:   edit
param:  array([0] = Datenfeldname, [1] = inhalt, [2] = label, [3] = tooltip)

$Rev$
$Author$
$Date$
$URL$

**************************************************************}

<form action='{$dlg['phpself']}' method='post'>
  <fieldset>
    <legend>{$dialog['kopf'][2]}&nbsp;#{$dialog['id'][1]}</legend>
      <table><colgroup><col><col><col><col></colgroup>

<!-- Vorname/Name -->
        <tr>
{if isset($dialog['vname'])}
            <td>{$dialog['vname'][2]}</td>
            <td><input
                type='text'
                name="{$dialog['vname'][0]}"
                value="{$dialog['vname'][1]}"
                onfocus="if(this.value=='{$dialog['vname'][1]}'){literal}{this.value='';}{/literal}"
            /></td>
{/if}
{if isset($dialog['nname'])}
          <td>{$dialog['nname'][2]}</td>
          <td><input type='text' name="{$dialog['nname'][0]}" value="{$dialog['nname'][1]}" /></td>
{/if}
        </tr>

<!-- Aliasnamen anzeigen -->
{if isset($dialog['aliases'])}
          <td>{$dialog['aliases'][2]}</td>
          <td>{$dialog['aliases'][1]}</td>
{/if}

{if isset($alist)}
        <tr>
          <td>{$dialog['addalias'][2]}</td>
          <td>
            {html_options name={$dialog['addalias'][0]} options=$alist}
          </td>
        </tr>
{/if}

<!-- Geb.-tag/-Ort -->
{if isset($dialog['gtag'])}
        <tr>
          <td>{$dialog['gtag'][2]}</td>
          <td>
            <input
              type='text'
              name="{$dialog['gtag'][0]}"
              value="{$dialog['gtag'][1]}"
              onmouseover="return overlib({literal}'{/literal}{$dialog['gtag'][3]} {literal}'{/literal},DELAY,1000);"
              onmouseout="return nd();"
            />
          </td>

          <td>{$dialog['gort'][2]}</td>
          <td>{html_options name=$dialog['gort'][0] options=$ortlist selected=$dialog['gort'][1]}</td>
        </tr>
{/if}

<!-- Tod.-tag/-Ort -->
{if isset($dialog['ttag'])}
        <tr>
          <td>{$dialog['ttag'][2]}</td>
          <td><input
            type='text'
            name="{$dialog['ttag'][0]}"
            value="{$dialog['ttag'][1]}"
            />
          </td>

          <td>{$dialog['tort'][2]}</td>
          <td>{html_options name=$dialog['tort'][0] options=$ortlist selected=$dialog['tort'][1]}</td>
        </tr>
{/if}

<!-- Anschrift -->
{if isset($dialog['strasse'])}
        <tr class="darkBG">
          <td>{$dialog['strasse'][2]}</td>
          <td colspan=3>
            <input
              type='text'
              name="{$dialog['strasse'][0]}"
              value="{$dialog['strasse'][1]}"
            />
          </td>
          </tr>
{/if}

<!-- PLZ/Ort -->
{if isset($dialog['plz'])}
        <tr class="darkBG">
          <td>&nbsp;</td>
          <td colspan=3>
            <input
              style='width:100px'
              type='text'
              name="{$dialog['plz'][0]}"
              value="{$dialog['plz'][1]}"
            />
            {html_options name={$dialog['wort'][0]} options=$ortlist selected=$dialog['wort'][1]}
          </td>
        </tr>
{/if}

<!-- email -->
{if isset($dialog['mail'])}
        <tr class="darkBG">
          <td>{$dialog['mail'][2]}</td>
          <td colspan=3>
            <input
              type='text'
              name="{$dialog['mail'][0]}"
              value="{$dialog['mail'][1]}"
            />
          </td>
        </tr>
{/if}

<!-- tel -->
{if isset($dialog['tel'])}
        <tr class="darkBG">
          <td>{$dialog['tel'][2]}</td>
          <td colspan=3>
            <input
              type='text'
              name="{$dialog['tel'][0]}"
              value="{$dialog['tel'][1]}"
              onmouseover="return overlib({literal}'{/literal}{$dialog['tel'][3]} {literal}'{/literal},DELAY,1000);"
              onmouseout="return nd();"
            />
          </td>
        </tr>
{/if}

<!-- Biografie -->
{if isset($dialog['descr'])}
        <tr>
          <td class="top">{$dialog['descr'][2]}</td>
          <td colspan=3>
            <textarea
              name="{$dialog['descr'][0]}"
              cols='60'
              rows='15'
              wrap='soft'
            >{$dialog['descr'][1]}</textarea>
          </td>
        </tr>
{/if}

<!-- Notiz -->
{if isset($dialog['notiz'])}
        <tr class="darkBG">
          <td class="top">{$dialog['notiz'][2]}</td>
          <td colspan=3>
            <textarea
              name="{$dialog['notiz'][0]}"
              cols='60'
              rows='10'
              wrap='soft'
            >{$dialog['notiz'][1]}</textarea>
          </td>
        </tr>
{/if}

        <tr>
          <td></td>
          <td><input type='submit' name='submit' /></td>
        </tr>
      </table>
    </fieldset>

    <input
      type='hidden'
      name='sektion'
      value='{$sektion}'
    />
    <input
      type='hidden'
      name='aktion'
      value='{$aktion}'
    />
</form>


