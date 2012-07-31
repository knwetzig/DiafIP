{**************************************************************

class: s_location_class.php
proc:

**************************************************************}
<form method='post'>
    <fieldset>
        <legend>&nbsp;Ort&nbsp;ausw&auml;hlen&nbsp;</legend>
        {html_options name=oid options=$olist selected=$seloid}
        <button
            style='font-size:1.5em; width:30px'
            class='small'
            type='submit'
            name='submit'
            value='selekt'
        >&crarr;</button>
        <input type='hidden' name='sektion' value='admin' />
        <input type='hidden' name='site' value='orte' />
    </fieldset>
</form>