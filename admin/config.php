<?PHP  // $Id$
       // config.php - allows admin to edit all configuration variables

    include("../config.php");

    if (isset($phpinfo)) {    // For debugging purposes, protected by password
        if (md5($phpinfo) == "caf9b6b99962bf5c2264824231d7a40c") {
            if ($site = get_site()) {
                print_heading("$site->fullname");
            }
            phpinfo();
            exit;
        }
    }

    if (isset($config)) {    // For debugging purposes, protected by password
        if (md5($config) == "caf9b6b99962bf5c2264824231d7a40c") {
            if ($site = get_site()) {
                print_heading("$site->fullname");
            }
            $TEMPCFG = $CFG;
            unset($TEMPCFG->dbuser);
            unset($TEMPCFG->dbpass);
            print_object($TEMPCFG);
            exit;
        }
    }

    if ($site = get_site()) {   // If false then this is a new installation
        require_login();
        if (!isadmin()) {
            error("Only the admin can use this page");
        }
    }


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $config = (object)$HTTP_POST_VARS;

        validate_form($config, $err);

        if (count($err) == 0) {
            print_header();
            foreach ($config as $name => $value) {
                unset($conf);
                $conf->name  = $name;
                $conf->value = $value;
                if ($current = get_record("config", "name", $name)) {
                    $conf->id = $current->id;
                    if (! update_record("config", $conf)) {
                        notify("Could not update $name to $value");
                    }
                } else {
                    if (! insert_record("config", $conf)) {
                        notify("Error: could not add new variable $name !");
                    }
                }
            }
            redirect("$CFG->wwwroot/admin/index.php", get_string("changessaved"), 1);
            exit;

        } else {
            foreach ($err as $key => $value) {
                $focus = "form.$key";
            }
        }
	}

/// Otherwise fill and print the form.

    if (!isset($config)) {
        $config = $CFG;
    }

    $stradmin = get_string("administration");
    $strconfigvariables = get_string("configvariables");

    if ($site) {
        print_header("$site->shortname: $strconfigvariables", "$site->fullname",
                      "<A HREF=\"$CFG->wwwroot/admin/\">$stradmin</A> -> $strconfigvariables", "$focus");
        print_heading($strconfigvariables);
    } else {
        print_header();
        print_heading($strconfigvariables);
        print_simple_box(get_string("configintro"), "center");
        echo "<BR>";
    }

    print_simple_box_start("center", "", "$THEME->cellheading");
	include("config.html");
    print_simple_box_end();
    print_footer();

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form(&$form, &$err) {

   // if (empty($form->fullname))
   //     $err["fullname"] = get_string("missingsitename");

    return;
}


?>
