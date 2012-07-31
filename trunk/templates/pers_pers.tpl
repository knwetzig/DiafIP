<div class='bereich'>{$dialog['bereich'][1]}</div>
  <table>
    <tr>
      <td>
      <form method='post'>
        <input
          type='text'
          name='sstring'
          value="{$dialog['sstring'][1]}"
          onfocus="if(this.value=='{$dialog['sstring'][1]}'){literal}{this.value='';}{/literal}"
        />
        <input
          type='hidden'
          name='sektion'
          value='person'
        />
        <input
          type='hidden'
          name='aktion'
          value='search'
        />
      </form>
    </td>

{if isset($dialog['add'])}
    <td class="re">
        <form  method='post'>
          <button
            class='small'
            name='aktion'
            value='add'
            onmouseover="return overlib( {literal}'{/literal}{$dialog['add'][3]}{literal}'{/literal}, DELAY, 1000);"
            onmouseout="return nd();"
          ><img src='images/add.png' /></button>
          <input
            type='hidden'
            name='form'
            value='true'
          />
        <input
          type='hidden'
          name='sektion'
          value='person'
        />
      </form>
  </td>
{/if}
  </tr></table>
<hr />
