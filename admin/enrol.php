<?PHP  // $Id$
       // enrol.php - allows admin to edit all enrollment variables
       //             Yes, enrol is correct English spelling.

    include("../config.php");
    require_login();
    optional_variable($enrol, $CFG->enrol);

    if (!$site = get_site()) {
        redirect("index.php");
    }

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    require_once("$CFG->dirroot/enrol/$enrol/enrol.php");   /// Open the class

    $enrolment = new enrolment_plugin();


/// If data submitted, then process and store.

	if ($frm = data_submitted()) {
        if ($enrolment->process_config($frm)) {
            set_config('enrol', $frm->enrol);
            redirect("enrol.php", get_string("changessaved"), 1);
        }
	} else {
        $frm = $CFG;
    }

/// Otherwise fill and print the form.

    /// get language strings
    $str = get_strings(array('enrolments', 'users', 'administration', 'settings'));


    $modules = get_list_of_plugins("enrol");
    foreach ($modules as $module) {
        $options[$module] = get_string("enrolname", "enrol_$module");
    }
    asort($options);

    print_header("$site->shortname: $str->enrolments", "$site->fullname",
                  "<a href=\"index.php\">$str->administration</a> -> 
                   <a href=\"users.php\">$str->users</a> -> $str->enrolments");

    echo "<form target=\"{$CFG->framename}\" name=\"enrolmenu\" method=\"post\" action=\"enrol.php\">";
    echo "<div align=\"center\"><p><b>";


/// Choose an enrolment method
    echo get_string('chooseenrolmethod').': ';
	choose_from_menu ($options, "enrol", $enrol, "",
                      "document.location='enrol.php?enrol='+document.enrolmenu.enrol.options[document.enrolmenu.enrol.selectedIndex].value", "");

    echo "</b></p></div>";
    
/// Print current enrolment type description    
    print_simple_box_start("center", "80%", "$THEME->cellheading");
    print_heading($options[$enrol]);

    print_simple_box_start("center", "60%", "$THEME->cellcontent");
    print_string("description", "enrol_$enrol");
    print_simple_box_end();

    echo "<hr>";
   // print_heading($str->settings);
    
    $enrolment->config_form($frm);

    echo "<center><p><input type=\"submit\" value=\"".get_string("savechanges")."\"></p></center>\n";
    echo "</form>";

    print_simple_box_end();

    print_footer();

    exit;
?>
