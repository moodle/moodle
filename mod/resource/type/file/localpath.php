<?php    // $Id$

    require('../../../../config.php');
    require('../../lib.php');

    $pathname = optional_param('pathname', '');

    if ($pathname) {
        if (confirm_sesskey()) {
            set_user_preference('resource_localpath', $pathname);
        }
        ?>
        <script type="text/javascript">
        //<![CDATA[
        window.close();
        //]]>
        </script>
        <?php
        exit;
    }

    print_header(get_string('localfilechoose', 'resource'));

    print_simple_box(get_string('localfilepath', 'resource', $CFG->wwwroot.'/user/edit.php?course='.SITEID), 'center');

    ?>
    <script type="text/javascript">
    //<![CDATA[
    function set_value(txt) {
        if (txt.indexOf('/') > -1) {
            txt = txt.substring(0,txt.lastIndexOf('/'));
        } else if (txt.indexOf('\\') > -1) {
            txt = txt.substring(0,txt.lastIndexOf('\\'));
        }
        getElementById('myform').pathname.value = txt;
        getElementById('myform').submit();
    }
    //]]>
    </script>
    
    <br />
    <div class="form mdl-align">
    <form id="myform" action="localpath.php" method="post">
    <fieldset class="invisiblefieldset">
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
    <input type="hidden" name="pathname" value="" />
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
