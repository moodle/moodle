<?PHP  // $Id$
       // config.php - allows admin to edit all configuration variables

    require_once("../config.php");


    if ($site = get_site()) {   // If false then this is a new installation
        require_login();
        if (!isadmin()) {
            error("Only the admin can use this page");
        }
    }

/// This is to overcome the "insecure forms paradox"
    if (isset($secureforms) and $secureforms == 0) {
        $match = "nomatch";
    } else {
        $match = "";
    }

/// If data submitted, then process and store.

	if ($config = data_submitted($match)) {  

        validate_form($config, $err);

        if (count($err) == 0) {
            print_header();
            foreach ($config as $name => $value) {
                if ($name == "sessioncookie") {
                    $value = eregi_replace("[^a-zA-Z]", "", $value);
                }
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
            redirect("index.php", get_string("changessaved"), 1);
            exit;

        } else {
            foreach ($err as $key => $value) {
                $focus = "form.$key";
            }
        }
	}

/// Otherwise fill and print the form.

    if (empty($config)) {
        $config = $CFG;
        if (!$config->locale = get_field("config", "value", "name", "locale")) {
            $config->locale = $CFG->lang;
        }
    }
    if (empty($focus)) {
        $focus = "";
    }

    $stradmin = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strconfigvariables = get_string("configvariables");

    if ($site) {
        print_header("$site->shortname: $strconfigvariables", $site->fullname,
                      "<a href=\"index.php\">$stradmin</a> -> ".
                      "<a href=\"configure.php\">$strconfiguration</a> -> $strconfigvariables", $focus);
        print_heading($strconfigvariables);
    } else {
        print_header();
        print_heading($strconfigvariables);
        print_simple_box(get_string("configintro"), "center", "50%");
        echo "<br />";
    }

    print_simple_box_start("center", "", "$THEME->cellheading");
	include("config.html");
    print_simple_box_end();

    if ($site) {
        print_footer();
    }

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form(&$form, &$err) {

   // if (empty($form->fullname))
   //     $err["fullname"] = get_string("missingsitename");

    return;
}


?>
