<?php
    // filters.php
    // Edit list of available text filters

    require_once "../config.php";
    require_once "$CFG->libdir/tablelib.php";

    // defines
    define( 'FILTER_TABLE','filter_administration_table' );

    // check for allowed access
    require_login();
    if (!isadmin()) {
        error( 'Only administrators can use the filters administration page' );
    }
    if (!$site = get_site()) {
        error( 'Site is not defined in filters administration page' );
    }

    // get values from page
    $params = new Object;
    $params->action = optional_param( 'action','',PARAM_ALPHA );
    $params->filterpath = optional_param( 'filterpath', '' );
    $params->cachetext = optional_param( 'cachetext','0',PARAM_INT );
    $params->filteruploadedfiles = optional_param( 'filteruploadedfiles','',PARAM_ALPHA );
    $params->filterall = optional_param( 'filterall','',PARAM_ALPHA );

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

    // get a list of possible filters (and translate name if possible)
    // note filters can be in the dedicated filters area OR in their 
    // associated modules
    $allfilters = array();
    $filtersettings = array();
    $filterlocations = array('mod','filter');
    foreach ($filterlocations as $filterlocation) {
        $plugins = get_list_of_plugins( $filterlocation );
        foreach ($plugins as $plugin) {
            $pluginpath = "$CFG->dirroot/$filterlocation/$plugin/filter.php";
            $settingspath = "$CFG->dirroot/$filterlocation/$plugin/filterconfig.html";
            if (is_readable( $pluginpath )) {
                $name = trim( get_string("filtername", $plugin) );
                if (empty($name) or ($name == '[[filtername]]')) {
                    $name = $plugin;
                }
            $allfilters[ "$filterlocation/$plugin" ] = ucfirst( $name );
            }
            if (is_readable($settingspath)) {
                $filtersettings[] = "$filterlocation/$plugin";
            }
        }
    }

    // get all the currently selected filters
    if (!empty($CFG->textfilters)) {
        $installedfilters = explode( ',', $CFG->textfilters );    
    }
    else { 
        $installedfilters = array();
    }

    // take this opportunity to clean up filters
    $oldinstalledfilters = $installedfilters;
    $installedfilters = array();
    foreach ($oldinstalledfilters as $key => $installedfilter ) {
        if (!empty( $installedfilter ) and array_key_exists( $installedfilter, $allfilters ) ) {
            $installedfilters[ $key ] = $installedfilter;
        }
    }
    set_config( 'textfilters', implode( ',', $installedfilters ) );

    //======================
    // Process Actions
    //======================

    // echo "<pre>"; print_r( $installedfilters ); die;    

    if (($params->action=="hide") and confirm_sesskey()) {
        $key=array_search( $params->filterpath, $installedfilters );
        // check filterpath is valid
        if ($key===false) {
            error( "Filter $params->filterpath is not a currently installed filter" );
        }    
        // just delete it
        unset( $installedfilters[ $key ] );
        set_config( 'textfilters', implode( ',', $installedfilters ) );
    }

    if (($params->action=="show") and confirm_sesskey()) {
        // check filterpath is valid
        if (!array_key_exists( $params->filterpath, $allfilters ) ) {
            error( "Filter $params->filterpath is not a currently installed filter" );
        }    
        if (array_search( $params->filterpath,$installedfilters ) ) {
            error( "Filter $params->filterpath is already active" );
        } 
        // add it to installed filters
        $installedfilters[] = $params->filterpath;
        set_config( 'textfilters', implode( ',', $installedfilters ) );
    }

    if (($params->action=="down") and confirm_sesskey()) {
        $key=array_search( $params->filterpath, $installedfilters );
        // check filterpath is valid
        if ($key===false ) {
            error( "Filter $params->filterpath is not a currently installed filter" );
        }    
        if ($key>=(count($installedfilters)-1)) {
            error( "Filter $params->filterpath cannot be moved any further down" );
        } 
        // swap with $key+1
        $fsave = $installedfilters[$key];
        $installedfilters[$key] = $installedfilters[$key+1];
        $installedfilters[$key+1] = $fsave;
        set_config( 'textfilters', implode( ',', $installedfilters ) );
    }

    if (($params->action=="up") and confirm_sesskey()) {
        $key=array_search( $params->filterpath, $installedfilters );
        // check filterpath is valid
        if ($key===false ) {
            error( "Filter $params->filterpath is not a currently installed filter" );
        }    
        if ($key<1) {
            error( "Filter $params->filterpath cannot be moved any further up" );
        } 
        // swap with $key-1
        $fsave = $installedfilters[$key];
        $installedfilters[$key] = $installedfilters[$key-1];
        $installedfilters[$key-1] = $fsave;
        set_config( 'textfilters', implode( ',', $installedfilters ) );
    }

    if (($params->action=="config") and confirm_sesskey()) {
        set_config( 'cachetext', $params->cachetext );
        set_config( 'filteruploadedfiles', $params->filteruploadedfiles );
        set_config( 'filterall', $params->filterall );
    }

    //======================
    // Build Display Objects
    //======================

    // construct the display array with installed filters
    // at the top in the right order
    $displayfilters = array();
    foreach ($installedfilters as $installedfilter) {
        $name = $allfilters[ $installedfilter ];
        $displayfilters[ $installedfilter ] = $name;
    }
    foreach ($allfilters as $key => $filter) {
        if (!array_key_exists( $key, $displayfilters )) {
            $displayfilters[ $key ] = $filter;
        }
    }

    // construct the flexible table ready to display
    $table = new flexible_table(FILTER_TABLE);
    $table->define_columns( array( 'name', 'hideshow', 'order', 'settings' ) );
    $table->define_headers( array( $txt->name, $txt->hideshow, $txt->updown, $txt->settings ) );
    $table->define_baseurl( "$CFG->wwwroot/admin/filters.php" );
    $table->set_attribute( 'id', 'blocks' );
    $table->set_attribute( 'class', 'flexible generaltable generalbox' );
    $table->setup();

    // some basic information 
    $url = strip_querystring( qualified_me() );
    $myurl = "$url?sesskey=" . sesskey();
    $img = "$CFG->pixpath/t";

    // iterate through filters adding to display table
    $updowncount = 1;
    $installedfilterscount = count( $installedfilters );
    foreach( $displayfilters as $path => $name ) {
        $upath = urlencode( $path );
        // get hide/show link
        if (in_array( $path, $installedfilters )) {
            $hideshow = "<a href=\"$myurl&amp;action=hide&amp;filterpath=$path\">";
            $hideshow .= "<img src=\"$img/hide.gif\" alt=\"hide\"></a>";
            $hidden = false;
        }
        else {
            $hideshow = "<a href=\"$myurl&amp;action=show&amp;filterpath=$path\">";
            $hideshow .= "<img src=\"$img/show.gif\" alt=\"show\"></a>";
            $hidden = true;
        }

        // get up/down link (only if not hidden)
        $updown = '';
        if (!$hidden) {
            if ($updowncount>1) {
                $updown .= "<a href=\"$myurl&amp;action=up&amp;filterpath=$path\">";
                $updown .= "<img src=\"$img/up.gif\" alt=\"up\"></a>&nbsp;";
            }
            else {
                $updown .= "<img src=\"$CFG->pixpath/spacer.gif\" height=\"16\" width=\"16\" alt=\"\">&nbsp;";
            }
            if ($updowncount<$installedfilterscount) {
                $updown .= "<a href=\"$myurl&amp;action=down&amp;filterpath=$path\">";
                $updown .= "<img src=\"$img/down.gif\" alt=\"down\"></a>";
            }
            else {
                $updown .= "<img src=\"$CFG->pixpath/spacer.gif\" height=\"16\" width=\"16\" alt=\"\">";
            }
            ++$updowncount;
        }
 
        // settings link (if defined)
        $settings = '';
        if (in_array( $path, $filtersettings )) {
            $settings = "<a href=\"filter.php?filter=" . urlencode($path) . "\">";
            $settings .= "settings</a>";
        }

        // write data into the table object
        $table->add_data( array( $name, $hideshow, $updown, $settings ) );
    }

    // build options list for cache lifetime
    $seconds = array(604800,86400,43200,10800,7200,3600,2700,1800,900,600,540,480,420,360,300,240,180,120,60,30,0);
    unset($lifetimeoptions);
    foreach ($seconds as $second) {
        if ($second>=86400) {
            $options[$second] = get_string('numdays','',$second/86400);
        }
        elseif ($second>=3600) {
            $options[$second] = get_string('numhours','',$second/3600);
        }
        elseif ($second>=60) {
            $options[$second] = get_string('numminutes','',$second/60);
        }
        elseif ($second>=1) {
            $options[$second] = get_string('numseconds','',$second);
        }
        else {
            $options[$second] = get_string('no');
        }
    }

    //==============================
    // Display logic
    //==============================

    print_header( "$site->shortname: $txt->managefilters", "$site->fullname",
        "<a href=\"index.php\">$txt->administration</a> -> <a href=\"configure.php\">$txt->configuration</a> " .
        "-> $txt->managefilters" );

    print_heading( $txt->managefilters );

    // print the table of all the filters
    $table->print_html();

    // print the table for the cache controls
    print_heading( $txt->cachecontrols ); 
    print_simple_box_start('center');
    ?>

    <form name="options" id="options" method="post" action="<?php echo $url; ?>" >
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
        <input type="hidden" name="action" value="config" />
        <table cellpadding="20">
            <tr valign="top">
                <td nowrap="nowrap" align="right"><?php echo $txt->cachetext; ?></td>
                <td><?php choose_from_menu( $options, "cachetext", $CFG->cachetext, "", "", "" ); ?></td>
                <td><?php echo $txt->configcachetext; ?></td>
            </tr>
            <tr valign="top">
                <td nowrap="nowrap" align="right"><?php echo $txt->filteruploadedfiles; ?></td>
                <td><?php choose_from_menu( array('no','yes'), "filteruploadedfiles", $CFG->filteruploadedfiles,"","",""); ?></td>
                <td><?php echo $txt->configfilteruploadedfiles; ?></td>
            </tr>
            <tr valign="top">
                <td nowrap="nowrap" align="right"><?php echo $txt->filterall; ?></td>
                <td><?php choose_from_menu( array('no','yes'), "filterall", $CFG->filterall,"","",""); ?></td>
                <td><?php echo $txt->configfilterall; ?></td>
            </tr>
            <tr valign="top">
                <td>&nbsp;</td>
                <td><input type="submit" value="<?php print_string('savechanges'); ?>" /></td>
                <td>&nbsp;</td>
            </tr>
        </table>   
    </form>

    <?php
    print_simple_box_end();

    print_footer();
?>
