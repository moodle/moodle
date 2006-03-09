<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    include("../config.php");

    $enrol = required_param('enrol', PARAM_ALPHA);

    require_login();

    if (!$site = get_site()) {
        redirect("index.php");
    }

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    }

    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

    $enrolment = enrolment_factory::factory($enrol);

/// If data submitted, then process and store.

    if ($frm = data_submitted()) {
        if ($enrolment->process_config($frm)) {
            redirect("enrol.php?sesskey=$USER->sesskey", get_string("changessaved"), 1);
        }
    } else {
        $frm = $CFG;
    }

/// Otherwise fill and print the form.

    /// get language strings
    $str = get_strings(array('enrolmentplugins', 'configuration', 'users', 'administration'));


    $modules = get_list_of_plugins("enrol");
    foreach ($modules as $module) {
        $options[$module] = get_string("enrolname", "enrol_$module");
    }
    asort($options);

    print_header("$site->shortname: $str->enrolmentplugins", "$site->fullname",
                  "<a href=\"index.php\">$str->administration</a> -> 
                   <a href=\"users.php\">$str->users</a> -> 
                   <a href=\"enrol.php?sesskey=$USER->sesskey\">$str->enrolmentplugins</a> -> 
                   $str->configuration");

    echo "<form target=\"{$CFG->framename}\" name=\"enrolmenu\" method=\"post\" action=\"enrol_config.php\">";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\">";
    echo "<div align=\"center\"><p><b>";


/// Choose an enrolment method
    echo get_string('chooseenrolmethod').': ';
    choose_from_menu ($options, "enrol", $enrol, "",
                      "document.location='enrol_config.php?sesskey=$USER->sesskey&enrol='+document.enrolmenu.enrol.options[document.enrolmenu.enrol.selectedIndex].value", "");

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

    print_footer();

    exit;
?>
