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

	if ($config = data_submitted()) {

        $config = (array)$config;
        validate_form($config, $err);

        if (count($err) == 0) {
            print_header();
            foreach ($config as $name => $value) {
                if (! set_config($name, $value)) {
                    notify("Problem saving config $name as $value");
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

    if (empty($config)) {
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
    require_once("$CFG->dirroot/auth/$auth/lib.php"); //just to make sure that current authentication functions are loaded
    if (! isset($config->guestloginbutton)) {
        $config->guestloginbutton = 1;
    }
    if (! isset($config->auth_instructions)) {
        $config->auth_instructions = "";
    }
    if (! isset($config->changepassword)) {
        $config->changepassword = "";
    }
    $user_fields = array("firstname", "lastname", "email", "phone1", "phone2", "department", "address", "city", "country", "description", "idnumber", "lang");

    foreach ($user_fields as $user_field) {
        $user_field = "auth_user_$user_field";
        if (! isset($config->$user_field)) {
            $config->$user_field = "";
        }
    }

    if (empty($focus)) {
        $focus = "";
    }

    $guestoptions[0] = get_string("hide");
    $guestoptions[1] = get_string("show");

    $createoptions[0] = get_string("no");
    $createoptions[1] = get_string("yes");

    $stradministration        = get_string("administration");
    $strauthentication        = get_string("authentication");
    $strauthenticationoptions = get_string("authenticationoptions","auth");
    $strsettings = get_string("settings");
    $strusers = get_string("users");

    print_header("$site->shortname: $strauthenticationoptions", "$site->fullname",
                  "<A HREF=\"index.php\">$stradministration</A> -> <a href=\"users.php\">$strusers</a> -> $strauthenticationoptions", "$focus");

    echo "<CENTER><P><B>";
    echo "<form TARGET=\"{$CFG->framename}\" NAME=\"authmenu\" method=\"post\" action=\"auth.php\">";
    print_string("chooseauthmethod","auth");

	choose_from_menu ($options, "auth", $auth, "","document.location='auth.php?auth='+document.authmenu.auth.options[document.authmenu.auth.selectedIndex].value", "");

    echo "</B></P></CENTER>";
        
    print_simple_box_start("center", "100%", "$THEME->cellheading");
    print_heading($options[$auth]);

    print_simple_box_start("center", "60%", "$THEME->cellcontent");
    print_string("auth_$auth"."description", "auth");
    print_simple_box_end();

    echo "<hr>";

    print_heading($strsettings);

    echo "<table border=\"0\" width=\"100%\" cellpadding=\"4\">";

    require_once("$CFG->dirroot/auth/$auth/config.html");

    if ($auth != "email" and $auth != "none" and $auth != "manual") {
        echo "<tr valign=\"top\">";
	    echo "<td align=right nowrap><p>";
        print_string("changepassword", "auth");
        echo ":</p></td>";
	    echo "<td>";
        echo "<input type=\"text\" name=\"changepassword\" size=40 value=\"$config->changepassword\">";
        echo "</td>";
        echo "<td>";
        print_string("changepasswordhelp","auth");
        echo "</td></tr>";

    }

    echo "<tr valign=\"top\">";
	echo "<td align=right nowrap><p>";
    print_string("guestloginbutton", "auth");
    echo ":</p></td>";
	echo "<td>";
    choose_from_menu($guestoptions, "guestloginbutton", $config->guestloginbutton, "");
    echo "</td>";
    echo "<td>";
    print_string("showguestlogin","auth");
    echo "</td></tr>";

    if (function_exists('auth_user_create')){    
	    echo "<tr valign=\"top\">";
		echo "<td align=right nowrap><p>";
		print_string("auth_user_create", "auth");
		echo ":</p></td>";
		echo "<td>";
		choose_from_menu($createoptions, "auth_user_create", $config->auth_user_create, "");
		echo "</td>";
		echo "<td>";
		print_string("auth_user_creation","auth");
		echo "</td></tr>";
	}

    echo "</table><center><p><input type=\"submit\" value=\"";
    print_string("savechanges");
    echo "\"></p></center></form>";

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
