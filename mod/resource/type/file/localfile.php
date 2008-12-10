<?php    // $Id$

    require('../../../../config.php');
    require('../../lib.php');

    $choose = required_param('choose', PARAM_FILE);

    require_login();

    if (!$CFG->resource_allowlocalfiles) {
        error('You cannot access this script');
    }

    print_header(get_string('localfilechoose', 'resource'));

    print_simple_box(get_string('localfileinfo', 'resource'), 'center');

    ?>
    <script type="text/javascript">
    //<![CDATA[
    function set_value(txt) {
        if (txt.indexOf('/') > -1) {
            path = txt.substring(txt.indexOf('/'),txt.length);
        } else if (txt.indexOf('\\') > -1) {
            path = txt.substring(txt.indexOf('\\'),txt.length);
        } else {
            window.close();
        }
        opener.document.getElementById('<?php echo $choose ?>').value = '<?php p(RESOURCE_LOCALPATH) ?>'+path;
        window.close();
    }
    //]]>
    </script>
    
    <br />
    <div class="form mdl-align">
    <form id="myform">
    <fieldset class="invisiblefieldset">
    <input type="file" size="60" name="myfile" /><br />
    <input type="button" value="<?php print_string('localfileselect','resource') ?>" 
           onClick="return set_value(getElementById('myform').myfile.value)">
    <input type="button" value="<?php print_string('cancel') ?>" 
           onClick="window.close()">
    </fieldset>
    </form>
    </div>

<?php
    print_footer('empty');
?>
