<?php    // $Id$

    require('../../../../config.php');
    require('../../lib.php');

    $pathname = optional_param('pathname', '');

    if ($pathname) {
        if (confirm_sesskey()) {
            set_user_preference('resource_localpath', $pathname);
        }
        echo $PAGE->requires->js_function_call('window.close')->asap();
        exit;
    }

    print_header(get_string('localfilechoose', 'resource'));

    print_simple_box(get_string('localfilepath', 'resource', $CFG->wwwroot.'/user/edit.php?course='.SITEID), 'center');
    $PAGE->requires->js('mod/resource/type/file/file.js');

    ?>
    <br />
    <div class="mdl-align form">
    <form id="myform" action="localpath.php" method="post">
    <fieldset class="invisiblefieldset">
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
    <input type="hidden" name="pathname" value="" />
    <input type="file" size="60" name="myfile" /><br />
    <input type="button" value="<?php print_string('localfileselect','resource') ?>" 
           onClick="return local_path_set_value(document.getElementById('myform').myfile.value)">
    <input type="button" value="<?php print_string('cancel') ?>" 
           onClick="window.close()">
    </fieldset>
    </form>
    </div>
<?php
    print_footer('empty');
?>
