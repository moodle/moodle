<?PHP  // $Id$
       // module.php - allows admin to edit all local configuration variables for a module

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

/// If data submitted, then process and store.

    if ($config = data_submitted()) {
        $module = optional_param('module', '', PARAM_SAFEDIR);

        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', 'error');
        }

        if ($module != '') {
            include_once("$CFG->dirroot/mod/$module/lib.php");
            admin_externalpage_setup('modsetting'.$module);
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
            admin_externalpage_setup('managemodules');

            $moduleprefix = '';
        }

        unset($config->sesskey);
        unset($config->module);

        foreach ($config as $name => $value) {
            set_config($moduleprefix.$name, $value);
        }
        redirect("$CFG->wwwroot/$CFG->admin/modules.php", get_string("changessaved"), 1);
        exit;
    }

/// Otherwise print the form.
    $module = required_param('module', PARAM_SAFEDIR);
    include_once("$CFG->dirroot/mod/$module/lib.php");
    admin_externalpage_setup('modsetting'.$module);

    $strmodulename = get_string("modulename", $module);

    // $CFG->pagepath is used to generate the body and id attributes for the body tag
    // of the page. It is also used to generate the link to the Moodle Docs for this view.
    $CFG->pagepath = 'mod/' . $module . '/config';

    admin_externalpage_print_header();

    print_heading($strmodulename);

    print_simple_box(get_string("configwarning", 'admin'), "center", "60%");
    echo "<br />";

    print_simple_box_start("center", "");
    include("$CFG->dirroot/mod/$module/config.html");
    print_simple_box_end();

    admin_externalpage_print_footer();

?>
