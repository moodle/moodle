<?php // $Id$
    // filter.php
    // Edit text filter settings

    require_once "../config.php";
    require_once "$CFG->libdir/tablelib.php";

    // check for allowed access
    require_login();
    if (!isadmin()) {
        error( 'Only administrators can use the filters administration page' );
    }
    if (!$site = get_site()) {
        error( 'Site is not defined in filters administration page' );
    }

    // get parameters
    $param = new Object;
    $param->filter = required_param( 'filter' );
    $param->submit = optional_param( 'submit','',PARAM_ALPHA );
    $param->reset = optional_param( 'reset','',PARAM_ALPHA );
    $filtername =  substr( $param->filter, strpos( $param->filter, '/' )+1 ) ;

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
                set_config( $name,$value );
            }
        }
        redirect( "$CFG->wwwroot/$CFG->admin/filters.php", get_string('changessaved'), 1);
        exit;
    }

    //==============================
    // Display logic
    //==============================
    
    // $CFG->pagepath is used to generate the body and id attributes for the body tag
    // of the page. It is also used to generate the link to the Moodle Docs for this view.
    $CFG->pagepath = 'filter/' . $filtername . '/config';
    
    $filtername = ucfirst($filtername);
    print_header( "$site->shortname: $txt->managefilters", "$site->fullname",
        "<a href=\"index.php\">$txt->administration</a> -> <a href=\"configure.php\">$txt->configuration</a> " .
        "-> <a href=\"filters.php\">$txt->managefilters</a> -> $filtername" );

    print_heading( $txt->managefilters );

    print_simple_box("<center>".get_string("configwarning", "admin")."</center>", "center", "50%");
    echo "<br />";

    print_simple_box_start("center",'');

    ?>
    <form action="filter.php?filter=<?php echo urlencode($param->filter); ?>" method="post">
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

    <?php include "$CFG->dirroot/$param->filter/filterconfig.html"; ?>

    <center>
        <input type="submit" name="submit" value="<?php print_string('savechanges'); ?>" />
        <input type="submit" name="reset" value="<?php echo print_string('resettodefaults'); ?>" />
    </center>
    </form>

    <?php
    print_simple_box_end();

    print_simple_box_end();

    print_footer();
?>
