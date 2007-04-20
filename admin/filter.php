<?php // $Id$
    // filter.php
    // Edit text filter settings

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/tablelib.php');

    $adminroot = admin_get_root();
    admin_externalpage_setup('managefilters', $adminroot);

    // get parameters
    $param = new Object;

    $param->filter = required_param('filter', PARAM_PATH);
    $param->submit = optional_param('submit', 0, PARAM_BOOL);
    $param->reset  = optional_param('reset', 0, PARAM_BOOL);

    $filtername =  substr($param->filter, strpos( $param->filter, '/' )+1 ) ;

    // $CFG->pagepath is used to generate the body and id attributes for the body tag
    // of the page. It is also used to generate the link to the Moodle Docs for this view.
    $CFG->pagepath = 'filter/' . $filtername . '/config';

    // get translated strings for use on page
    $txt = new Object;
    $txt->managefilters = get_string( 'managefilters' );
    $txt->administration = get_string( 'administration' );
    $txt->configuration = get_string( 'configuration' );

    //======================
    // Process Actions
    //======================

    // if reset pressed let filter config page handle it
    $forcereset = false;
    if (!empty($param->reset)) {
        $forcereset = true;
    }
    else
    if ($config = data_submitted()) {

        // check session key
        if (!confirm_sesskey()) {
             error( get_string('confirmsesskeybad', 'error' ) );
        }

        $configpath = $CFG->dirroot.'/filter/'.$filtername.'/filterconfig.php';
        if (file_exists($configpath)) {
            require_once($configpath);
            $functionname = $filtername.'_process_config';
            if (function_exists($functionname)) {
                $functionname($config);
                $saved = true;
            }
        }

        if (empty($saved)) {
            // run through submitted data
            // reject if does not start with filter_
            foreach ($config as $name => $value) {
                set_config($name, stripslashes($value));
            }
        }
        redirect("$CFG->wwwroot/$CFG->admin/filters.php");
        exit;
    }

    //==============================
    // Display logic
    //==============================

    $filtername = ucfirst($filtername);
    admin_externalpage_print_header($adminroot);
    print_heading( $filtername );

    print_simple_box(get_string("configwarning", "admin"), "center", "50%");
    echo "<br />";

    print_simple_box_start("center",'');

    ?>
    <form action="filter.php?filter=<?php echo urlencode($param->filter); ?>" method="post">
    <div style="text-align: center">
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

    <?php include "$CFG->dirroot/$param->filter/filterconfig.html"; ?>

        <input type="submit" name="submit" value="<?php print_string('savechanges'); ?>" />
        <input type="submit" name="reset" value="<?php echo print_string('resettodefaults'); ?>" />
    </div>
    </form>

    <?php
    print_simple_box_end();

    admin_externalpage_print_footer($adminroot);
?>
