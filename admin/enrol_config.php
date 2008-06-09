<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('enrolment');

    $enrol = required_param('enrol', PARAM_ALPHA);
    $CFG->pagepath = 'enrol/' . $enrol;


    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

    $enrolment = enrolment_factory::factory($enrol);

/// If data submitted, then process and store.

    if ($frm = data_submitted()) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', 'error');
        }
        if ($enrolment->process_config($frm)) {
            redirect("enrol.php?sesskey=$USER->sesskey", get_string("changessaved"), 1);
        }
    } else {
        $frm = $CFG;
    }

/// Otherwise fill and print the form.

    /// get language strings
    $str = get_strings(array('enrolmentplugins', 'configuration', 'users', 'administration'));

    unset($options);

    $modules = get_list_of_plugins("enrol");
    foreach ($modules as $module) {
        $options[$module] = get_string("enrolname", "enrol_$module");
    }
    asort($options);

    admin_externalpage_print_header();

    echo "<form $CFG->frametarget id=\"enrolmenu\" method=\"post\" action=\"enrol_config.php\">";
    echo "<div>";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\" />";
    echo "<input type=\"hidden\" name=\"enrol\" value=\"".$enrol."\" />";

/// Print current enrolment type description
    print_simple_box_start("center", "80%");
    print_heading($options[$enrol]);

    print_simple_box_start("center", "60%", '', 5, 'informationbox');
    print_string("description", "enrol_$enrol");
    print_simple_box_end();

    echo "<hr />";

    $enrolment->config_form($frm);

    echo "<p class=\"centerpara\"><input type=\"submit\" value=\"".get_string("savechanges")."\" /></p>\n";
    print_simple_box_end();
    echo "</div>";
    echo "</form>";

    admin_externalpage_print_footer();

    exit;
?>
