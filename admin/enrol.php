<?PHP  // $Id$
       // enrol.php - allows admin to edit all enrollment variables
       //             Yes, enrol is correct English spelling.

    include("../config.php");
    require_login();

    if (!$site = get_site()) {
        redirect("index.php");
    }

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

	if (isset($_GET['enrolment'])) {
	    $enrolname = $_GET['enrol'];
	} else {
        $enrolname = $CFG->enrol;
	} 
    
    require_once("$CFG->dirroot/enrol/$enrolname/enrol.php");   /// Open the class

    $enrolment = new enrolment_plugin();


/// If data submitted, then process and store.

	if ($config = data_submitted()) {
        if ($enrolment->process_config($config)) {
            redirect("enrol.php", get_string("changessaved"), 1);
        }
	}

/// Otherwise fill and print the form.

    $str = get_strings(array('enrolments', 'users', 'administration'));

    if (empty($config)) {
        $page->config = $CFG;
    } else {
        $page->config = $config;
    }

    $modules = get_list_of_plugins("enrol");
    foreach ($modules as $module) {
        $page->options[$module] = get_string("enrolname", "enrol_$module");
    }
    asort($page->options);

    $form = $enrolment->config_form($page);

    print_header("$site->shortname: $str->enrolments", "$site->fullname",
                  "<a href=\"index.php\">$str->administration</a> -> 
                   <a href=\"users.php\">$str->users</a> -> $str->enrolments");

    print_heading($page->options[$CFG->enrol]);

    echo "<CENTER><P><B>";
    echo "NOT COMPLETE";
    echo "<P><B>";
    echo "<form TARGET=\"{$CFG->framename}\" NAME=\"authmenu\" method=\"post\" action=\"auth.php\">";
    print_string("chooseauthmethod","auth");

	choose_from_menu ($options, "auth", $auth, "","document.location='auth.php?auth='+document.authmenu.auth.options[document.authmenu.auth.selectedIndex].value", "");

    echo "</B></P></CENTER>";
        

    

    print_footer();

    exit;
?>
