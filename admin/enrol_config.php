<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    $adminroot = admin_get_root();
    admin_externalpage_setup('enrolment', $adminroot);

    $enrol = required_param('enrol', PARAM_ALPHA);
    $CFG->pagepath = 'enrol/' . $enrol;


    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

    $enrolment = enrolment_factory::factory($enrol);

/// If data submitted, then process and store.

    if ($frm = data_submitted()) {
        if (!confirm_sesskey()) {
            error(get_string('confirmsesskeybad', 'error'));
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

    admin_externalpage_print_header($adminroot);

    if (empty($CFG->framename) or $CFG->framename=='_top') { 
        $target = '';
    } else {
        $target = ' target="'.$CFG->framename.'"';
    }

    echo "<form$target name=\"enrolmenu\" method=\"post\" action=\"enrol_config.php\">";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\">";
    echo "<div align=\"center\"><p><b>";


/// Choose an enrolment method
    echo get_string('chooseenrolmethod').': ';
    choose_from_menu ($options, "enrol", $enrol, "",
                      "document.location='enrol_config.php?enrol='+document.enrolmenu.enrol.options[document.enrolmenu.enrol.selectedIndex].value", "");

    echo "</b></p></div>";

/// Print current enrolment type description
    print_simple_box_start("center", "80%");
    print_heading($options[$enrol]);

    print_simple_box_start("center", "60%", '', 5, 'informationbox');
    print_string("description", "enrol_$enrol");
    print_simple_box_end();

    echo "<hr />";

    $enrolment->config_form($frm);

    echo "<center><p><input type=\"submit\" value=\"".get_string("savechanges")."\"></p></center>\n";
    echo "</form>";

    print_simple_box_end();

    admin_externalpage_print_footer($adminroot);

    exit;
?>
