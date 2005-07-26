<?php
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
    $params = new Object;
    $param->filter = required_param( 'filter' );

    // get translated strings for use on page
    $txt = new Object;
    $txt->managefilters = get_string( 'managefilters' );
    $txt->administration = get_string( 'administration' );
    $txt->configuration = get_string( 'configuration' );
    $txt->name = get_string( 'name' );
    $txt->hide = get_string( 'hide' );
    $txt->show = get_string( 'show' );
    $txt->hideshow = "$txt->hide/$txt->show";
    $txt->settings = get_string( 'settings' );
    $txt->up = get_string( 'up' );
    $txt->down = get_string( 'down' );
    $txt->updown = "$txt->up/$txt->down";
    $txt->cachetext = get_string( 'cachetext', 'admin' );
    $txt->configcachetext = get_string( 'configcachetext', 'admin' );
    $txt->filteruploadedfiles = get_string( 'filteruploadedfiles','admin' );
    $txt->configfilteruploadedfiles = get_string( 'configfilteruploadedfiles','admin' );
    $txt->filterall = get_string( 'filterall','admin' );
    $txt->configfilterall = get_string( 'configfilterall','admin' );
    $txt->cachecontrols = get_string( 'cachecontrols' );


    //======================
    // Process Actions
    //======================

    if ($config = data_submitted()) {

        // check session key
        if (!confirm_sesskey()) {
             error( get_string('confirmsesskeybad', 'error' ) );
        }

        // run through submitted data
        // reject if does not start with filter_
        foreach ($config as $name => $value) {
            set_config( $name,$value );
        }
        redirect( "$CFG->wwwroot/$CFG->admin/filters.php", get_string('changessaved'), 1);
        exit;
    }

    //==============================
    // Display logic
    //==============================

    print_header( "$site->shortname: $txt->managefilters", "$site->fullname",
        "<a href=\"index.php\">$txt->administration</a> -> <a href=\"configure.php\">$txt->configuration</a> " .
        "-> $txt->managefilters" );

    print_heading( $txt->managefilters );

    print_simple_box("<center>".get_string("configwarning", "admin")."</center>", "center", "50%");
    echo "<br />";

    print_simple_box_start("center",'');

    ?>
    <form action="filter.php?filter=<?php echo urlencode($param->filter); ?>" method="post">
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />

    <?php include "$CFG->dirroot/$param->filter/filterconfig.html"; ?>

    <center><input type="submit" value="<?php print_string('savechanges'); ?>" /></center>
    </form>

    <?php
    print_simple_box_end();

    print_simple_box_end();

    print_footer();
?>
