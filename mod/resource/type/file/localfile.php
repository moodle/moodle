<?php    // $Id$

    require('../../../../config.php');
    require('../../lib.php');

    $choose = required_param('choose', PARAM_FILE);

    require_login();

    if (!$CFG->resource_allowlocalfiles) {
        print_error('cannotcallscript');
    }

    print_header(get_string('localfilechoose', 'resource'));

    print_simple_box(get_string('localfileinfo', 'resource'), 'center');

    $PAGE->requires->js('mod/resource/type/file/file.js');
    ?>
    <br />
    <div class="mdl-align form">
    <form id="myform">
    <fieldset class="invisiblefieldset">
    <input type="file" size="60" name="myfile" /><br />
    <input type="button" value="<?php print_string('localfileselect','resource') ?>" 
           onClick="return local_file_set_value('<?php echo $choose?>',  '<?php echo p(RESOURCE_LOCALPATH);?>')">
    <input type="button" value="<?php print_string('cancel') ?>" 
           onClick="window.close()">
    </fieldset>
    </form>
    </div>

<?php
    print_footer('empty');
?>
