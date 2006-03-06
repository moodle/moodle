<?PHP  // $Id$
       // module.php - allows admin to edit all local configuration variables for a module

    require_once('../config.php');

    $module = required_param('module', PARAM_SAFEDIR);

    require_login();

    if (!isadmin()) {
        error("Only an admin can use this page");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    require_once("$CFG->dirroot/mod/$module/lib.php");

/// If data submitted, then process and store.

    if ($config = data_submitted()) {

        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
        }

        if ($module) {
            // if the config.html contains a hidden form field giving
            // the module name then the form does not have to prefix all
            // its variable names, we will do it here.
            $moduleprefix = $module.'_';
            // let the module process the form data if it has to,
            // $config is passed to this function by reference
            $moduleconfig = $module.'_process_options';
            if (function_exists($moduleconfig)) {
                $moduleconfig($config);
            }
        } else {
            $moduleprefix = '';
        }

        foreach ($config as $name => $value) {
            set_config($moduleprefix.$name, $value);
        }
        redirect("$CFG->wwwroot/$CFG->admin/modules.php", get_string("changessaved"), 1);
        exit;
    }

/// Otherwise print the form.

    $stradmin = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strmanagemodules = get_string("managemodules");
    $strmodulename = get_string("modulename", $module);
    
    // $CFG->pagepath is used to generate the body and id attributes for the body tag
    // of the page. It is also used to generate the link to the Moodle Docs for this view.
    $CFG->pagepath = 'mod/' . $module . '/config';
    
    print_header("$site->shortname: $strmodulename: $strconfiguration", $site->fullname,
                  "<a href=\"index.php\">$stradmin</a> -> ".
                  "<a href=\"configure.php\">$strconfiguration</a> -> ".
                  "<a href=\"modules.php\">$strmanagemodules</a> -> $strmodulename");

    print_heading($strmodulename);

    print_simple_box("<center>".get_string("configwarning", 'admin')."</center>", "center", "50%");
    echo "<br />";

    print_simple_box_start("center", "");
    include("$CFG->dirroot/mod/$module/config.html");
    print_simple_box_end();

    print_footer();

?>
