<?PHP  // $Id$
       // config.php - allows admin to edit all configuration variables

    include("../config.php");

    require_login();

    if (!$site = get_site()) {
        redirect("index.php");
    }

    if (!isadmin()) {
        error("Only the admin can use this page");
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
            redirect("auth.php", get_string("changessaved"), 1);
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

    $modules = get_list_of_plugins("auth");
    foreach ($modules as $module) {
        $options[$module] = get_string("auth_$module"."title", "auth");
    }
    asort($options);
	if (isset($_GET['auth'])) {
	    $auth = $_GET['auth'];
	} else {
        $auth = $config->auth;
	} 

    if (! isset($config->guestloginbutton)) {
        $config->guestloginbutton = 1;
    }
    $guestoptions[0] = get_string("hide");
    $guestoptions[1] = get_string("show");

    $stradministration        = get_string("administration");
    $strauthentication        = get_string("authentication");
    $strauthenticationoptions = get_string("authenticationoptions","auth");
    $strsettings = get_string("settings");

    print_header("$site->shortname: $strauthenticationoptions", "$site->fullname",
                  "<A HREF=\"index.php\">$stradministration</A> -> $strauthenticationoptions", "$focus");

    echo "<CENTER><P><B>";
    echo "<form TARGET=\"_top\" NAME=\"authmenu\" method=\"post\" action=\"auth.php\">";
    print_string("chooseauthmethod","auth");

	choose_from_menu ($options, "auth", $auth, "","top.location='auth.php?auth='+document.authmenu.auth.options[document.authmenu.auth.selectedIndex].value", "");

    echo "</B></P></CENTER>";
        
    print_simple_box_start("center", "100%", "$THEME->cellheading");
    print_heading($options[$auth]);

    echo "<CENTER><P>";
    print_string("auth_$auth"."description", "auth");
    echo "</P></CENTER>";

    echo "<HR>";

    print_heading($strsettings);

    echo "<table border=\"0\" width=\"100%\" cellpadding=\"4\">";

    require("$CFG->dirroot/auth/$auth/config.html");

    echo "<tr valign=\"top\">";
	echo "<td align=right nowrap><p>";
    print_string("guestloginbutton", "auth");
    echo ":</p></td>";
	echo "<td>";
    choose_from_menu($guestoptions, "guestloginbutton", $config->guestloginbutton, "");
    echo "</td>";
    echo "<td>";
    print_string("showguestlogin","auth");
    echo "</td></tr></table>";

    echo "<CENTER><P><INPUT TYPE=\"submit\" VALUE=\"";
    print_string("savechanges");
    echo "\"></P></CENTER></FORM>";

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
