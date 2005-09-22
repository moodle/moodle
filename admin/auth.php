<?PHP  // $Id$
       // config.php - allows admin to edit all configuration variables

    include("../config.php");

    $auth = optional_param( 'auth','',PARAM_CLEAN );

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

/// If data submitted, then process and store.


    if ($config = data_submitted()) {

        $config = (array)$config;
        validate_form($config, $err);

        // extract and sanitize the auth key explicitly
        $modules = get_list_of_plugins("auth");
        if (in_array($config['auth'], $modules)) {
            $auth = $config['auth'];            
        } else {
            notify("Error defining the authentication method");
        }

        if (count($err) == 0) {
            foreach ($config as $name => $value) {
                if (preg_match('/^pluginconfig_(.+?)$/', $name, $matches)) {
                    $plugin = "auth/$auth";
                    $name   = $matches[1];
                    if (! set_config($name, $value, $plugin)) {                        
                        notify("Problem saving config $name as $value for plugin $plugin");
                    }
                } else { // normal handling for 
                    if (! set_config($name, $value)) {
                        notify("Problem saving config $name as $value");
                    }
                }
            }
            redirect("auth.php?sesskey=$USER->sesskey", get_string("changessaved"), 1);
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
    if (!empty($auth) && in_array($auth, $modules)) {
    } else {
        $auth = $config->auth;
    }

    // changepassword link replaced by individual auth setting
    if (!empty($config->changepassword)) {
        if (empty($config->{'auth_'.$auth.'_changepasswordurl'})) {
            $config->{'auth_'.$auth.'_changepasswordurl'} = $config->changepassword;
        }
        set_config('changepassword','');
    }

    $auth = clean_filename($auth);
    require_once("$CFG->dirroot/auth/$auth/lib.php"); //just to make sure that current authentication functions are loaded
    if (! isset($config->guestloginbutton)) {
        $config->guestloginbutton = 1;
    }
    if (! isset($config->alternateloginurl)) {
        $config->alternateloginurl = '';
    }
    if (! isset($config->auth_instructions)) {
        $config->auth_instructions = "";
    }
    if (! isset($config->changepassword)) {
        $config->changepassword = "";
    }
    if (! isset($config->{'auth_'.$auth.'_changepasswordurl'})) {
        $config->{'auth_'.$auth.'_changepasswordurl'} = '';
    }
    if (! isset($config->{'auth_'.$auth.'_changepasswordhelp'})) {
        $config->{'auth_'.$auth.'_changepasswordhelp'} = '';
    }
    $user_fields = array("firstname", "lastname", "email", "phone1", "phone2", "department", "address", "city", "country", "description", "idnumber", "lang");

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
                  "<a href=\"index.php\">$stradministration</a> -> <a href=\"users.php\">$strusers</a> -> $strauthenticationoptions", "$focus");

    echo "<center><b>";
    echo "<form target=\"{$CFG->framename}\" name=\"authmenu\" method=\"post\" action=\"auth.php\">";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\" />";
    print_string("chooseauthmethod","auth");

    choose_from_menu ($options, "auth", $auth, "","document.location='auth.php?sesskey=$USER->sesskey&auth='+document.authmenu.auth.options[document.authmenu.auth.selectedIndex].value", "");

    echo "</b></center>";

    print_simple_box_start("center", "100%");
    print_heading($options[$auth]);

    print_simple_box_start("center", "60%", '', 5, 'informationbox');
    print_string("auth_$auth"."description", "auth");
    print_simple_box_end();

    echo "<hr />";

    print_heading($strsettings);

    echo "<table border=\"0\" width=\"100%\" cellpadding=\"4\">";

    require_once("$CFG->dirroot/auth/$auth/config.html");
    echo '<tr><td colspan="3">';
    print_heading(get_string('auth_common_settings', 'auth'));
    echo '<td/></tr>';

    if ($auth != "email" and $auth != "none" and $auth != "manual") {
        // display box for URL to change password. NB now on a per-method basis (multiple auth)
        echo "<tr valign=\"top\">";
        echo "<td align=\"right\" nowrap=\"nowrap\">";
        print_string("changepassword", "auth");
        echo ":</td>";
        echo "<td>";
        $passurl = $config->{'auth_'.$auth.'_changepasswordurl'};
        echo "<input type=\"text\" name=\"auth_{$auth}_changepasswordurl\" size=\"40\" value=\"$passurl\" />";
        echo "</td>";
        echo "<td>";
        print_string("auth_changepasswordurl_expl","auth",$auth);
        echo "</td></tr>";

        // display textbox for lost password help. NB now on a per-method basis (multiple auth)
        echo "<tr valign=\"top\">";
        echo "<td align=\"right\" nowrap=\"nowrap\">";
        print_string("auth_changepasswordhelp", "auth");
        echo ":</td>";
        echo "<td>";
        $passhelp = $config->{'auth_'.$auth.'_changepasswordhelp'};
        echo "<textarea name=\"auth_{$auth}_changepasswordhelp\" cols=\"30\" rows=\"10\" wrap=\"virtual\">";
        echo $passhelp;
        echo "</textarea>\n";
        echo "</td>";
        echo "<td>";
        print_string("auth_changepasswordhelp_expl","auth",$auth);
        echo "</td></tr>";

    }

    echo "<tr valign=\"top\">";
    echo "<td align=\"right\" nowrap=\"nowrap\">";
    print_string("guestloginbutton", "auth");
    echo ":</td>";
    echo "<td>";
    choose_from_menu($guestoptions, "guestloginbutton", $config->guestloginbutton, "");
    echo "</td>";
    echo "<td>";
    print_string("showguestlogin","auth");
    echo "</td></tr>";

    if (function_exists('auth_user_create')){
        echo "<tr valign=\"top\">";
        echo "<td align=\"right\" nowrap=\"nowrap\">";
        print_string("auth_user_create", "auth");
        echo ":</td>";
        echo "<td>";
        choose_from_menu($createoptions, "auth_user_create", $config->auth_user_create, "");
        echo "</td>";
        echo "<td>";
        print_string("auth_user_creation","auth");
        echo "</td></tr>";
    }


/// An alternate url for the login form. It means we can use login forms that are integrated
/// into non-moodle pages
    echo '<tr valign="top">';
    echo '<td algin="right" nowrap="nowrap">';
    print_string('alternateloginurl', 'auth');
    echo '</td>';
    echo '<td>';
    echo '<input type="text" size="40" name="alternateloginurl" alt="'.get_string('alternateloginurl', 'auth').'" value="'.$config->alternateloginurl.'" />';
    echo '</td>';
    echo '<td>';
    print_string('alternatelogin', 'auth', htmlspecialchars($CFG->wwwroot.'/login/index.php'));
    echo '</td>';
    echo '</tr>';


    echo '</table>';
    echo '<p align="center"><input type="submit" value="'.get_string('savechanges').'"></p>';
    echo '</form>';

    print_simple_box_end();

    print_footer();
    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form(&$form, &$err) {

   // if (empty($form->fullname))
   //     $err["fullname"] = get_string("missingsitename");

    return;
}

//
// Good enough for most auth plugins
// but some may want a custom one if they are offering
// other options
// Note: pluginconfig_ fields have special handling. 
function print_auth_lock_options ($auth, $user_fields, $helptext, $retrieveopts, $updateopts) {

    echo '<tr><td colspan="3">';
    if ($retrieveopts) {
        print_heading(get_string('auth_data_mapping', 'auth'));
    } else {
        print_heading(get_string('auth_fieldlocks', 'auth'));
    }
    echo '<td/></tr>';

    $lockoptions = array ('unlocked'        => get_string('unlocked', 'auth'),
                          'unlockedifempty' => get_string('unlockedifempty', 'auth'),
                          'locked'          => get_string('locked', 'auth'));
    $updatelocaloptions = array('oncreate'  => get_string('update_oncreate', 'auth'),
                                'onlogin'   => get_string('update_onlogin', 'auth'));
    $updateextoptions = array('0'  => get_string('update_never', 'auth'),
                              '1'   => get_string('update_onupdate', 'auth'));
    
    $pluginconfig = get_config("auth/$auth");
    
    // helptext is on a field with rowspan
    if (empty($helptext)) {
                $helptext = '&nbsp;';
    }

    foreach ($user_fields as $field) {

        // Define some vars we'll work with
        if(empty($pluginconfig->{"field_map_$field"})) {
            $pluginconfig->{"field_map_$field"} = '';
        }
        if(empty($pluginconfig->{"field_updatelocal_$field"})) {
            $pluginconfig->{"field_updatelocal_$field"} = '';
        }  
        if (empty($pluginconfig->{"field_updateremote_$field"})) {
            $pluginconfig->{"field_updateremote_$field"} = '';
        }
        if (empty($pluginconfig->{"field_lock_$field"})) {
            $pluginconfig->{"field_lock_$field"} = '';
        }

        // define the fieldname we display to the  user
        $fieldname = $field;
        if ($fieldname === 'lang') {
            $fieldname = get_string('language');
        } elseif (preg_match('/^(.+?)(\d+)$/', $fieldname, $matches)) {
            $fieldname =  get_string($matches[1]) . ' ' . $matches[2];
        } else {
            $fieldname = get_string($fieldname);
        }

        echo '<tr valign="top"><td align="right">';
        echo $fieldname;
        echo '</td><td>';

        if ($retrieveopts) {
            $varname = 'field_map_' . $field;

            echo "<input name=\"pluginconfig_{$varname}\" type=\"text\" size=\"30\" value=\"{$pluginconfig->$varname}\">";
            echo '<div align="right">';
            echo  get_string('auth_updatelocal', 'auth') . '&nbsp;&nbsp;';
            choose_from_menu($updatelocaloptions, "pluginconfig_field_updatelocal_{$field}", $pluginconfig->{"field_updatelocal_$field"}, "");
            echo '<br />';
            if ($updateopts) {
                echo  get_string('auth_updateremote', 'auth') . '&nbsp;&nbsp;';
                 '&nbsp;&nbsp;';
                choose_from_menu($updateextoptions, "pluginconfig_field_updateremote_{$field}", $pluginconfig->{"field_updateremote_$field"}, "");
                echo '<br />';


            }
            echo  get_string('auth_fieldlock', 'auth') . '&nbsp;&nbsp;';
            choose_from_menu($lockoptions, "pluginconfig_field_lock_{$field}", $pluginconfig->{"field_lock_$field"}, "");
            echo '</div>';
        } else {
            choose_from_menu($lockoptions, "pluginconfig_field_lock_{$field}", $pluginconfig->{"field_lock_$field"}, "");
        }
        echo '</td>';
        if (!empty($helptext)) {
            echo '<td rowspan="' . count($user_fields) . '">' . $helptext . '</td>';
            $helptext = '';
        }
        echo '</tr>';
    }
}

?>
