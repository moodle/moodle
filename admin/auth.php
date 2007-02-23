<?php

/**
 * Allows admin to edit all auth plugin settings.
 *
 * JH: copied and Hax0rd from admin/enrol.php and admin/filters.php
 *
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$adminroot = admin_get_root();
admin_externalpage_setup('userauthentication', $adminroot);

$action = optional_param('action', '', PARAM_ACTION);
$auth   = optional_param('auth', '', PARAM_SAFEDIR);

// get currently installed and enabled auth plugins
$authsavailable = get_list_of_plugins('auth');

//revert auth_plugins_enabled
if (isset($CFG->auth_plugins_enabled)) {
    set_config('auth', $CFG->auth_plugins_enabled);
    delete_records('config', 'name', 'auth_plugins_enabled');
    unset($CFG->auth_plugins_enabled);
}

if (empty($CFG->auth)) {
    $authsenabled = array();
} else {
    $authsenabled = explode(',', $CFG->auth);
    $authsenabled = array_unique($authsenabled);
}

$key = array_search('manual', $authsenabled);
if ($key !== false) {
    unset($authsenabled[$key]); // manual is always enabled anyway
    set_config('auth', implode(',', $authsenabled));
}

if (!isset($CFG->registerauth)) {
    set_config('registerauth', '');
}

if (!isset($CFG->auth_instructions)) {
    set_config('auth_instructions', '');
}

if (!empty($auth) and !exists_auth_plugin($auth)) {
    error(get_string('pluginnotinstalled', 'auth', $auth), $url);
}


////////////////////////////////////////////////////////////////////////////////
// process actions

$status = '';

switch ($action) {

    case 'save':
        if (data_submitted() and confirm_sesskey()) {

            // save settings
            set_config('guestloginbutton', required_param('guestloginbutton', PARAM_BOOL));
            set_config('alternateloginurl', stripslashes(trim(required_param('alternateloginurl', PARAM_RAW))));
            set_config('registerauth', required_param('register', PARAM_SAFEDIR));
            set_config('auth_instructions', stripslashes(trim(required_param('auth_instructions', PARAM_RAW))));

            // enable registerauth in $CFG->auth if needed
            if (!empty($CFG->registerauth) and !in_array($CFG->registerauth, $authsenabled)) {
                $authsenabled[] = $CFG->registerauth;
                set_config('auth', implode(',', $authsenabled));
            }
            $status = get_string('changessaved');
        }
        break;

    case 'disable':
        // remove from enabled list
        $key = array_search($auth, $authsenabled);
        if ($key !== false) {
            unset($authsenabled[$key]);
            set_config('auth', implode(',', $authsenabled));
        }

        if ($auth == $CFG->registerauth) {
            set_config('registerauth', '');
        }
        break;

    case 'enable':
        // add to enabled list
        if (!in_array($auth, $authsenabled)) {
            $authsenabled[] = $auth;
            $authsenabled = array_unique($authsenabled);
            set_config('auth', implode(',', $authsenabled));
        }
        break;

    case 'down':
        $key = array_search($auth, $authsenabled);
        // check auth plugin is valid
        if ($key === false) {
            error(get_string('pluginnotenabled', 'auth', $auth), $url);
        }
        // move down the list
        if ($key < (count($authsenabled) - 1)) {
            $fsave = $authsenabled[$key];
            $authsenabled[$key] = $authsenabled[$key + 1];
            $authsenabled[$key + 1] = $fsave;
            set_config('auth', implode(',', $authsenabled));
        }
        break;

    case 'up':
        $key = array_search($auth, $authsenabled);
        // check auth is valid
        if ($key === false) {
            error(get_string('pluginnotenabled', 'auth', $auth), $url);
        }
        // move up the list
        if ($key >= 1) {
            $fsave = $authsenabled[$key];
            $authsenabled[$key] = $authsenabled[$key - 1];
            $authsenabled[$key - 1] = $fsave;
            set_config('auth', implode(',', $authsenabled));
        }
        break;

    default:
        break;
}

// display strings
$txt = get_strings(array('authenticationplugins', 'users', 'administration',
                         'settings', 'edit', 'name', 'enable', 'disable',
                         'up', 'down', 'none'));
$txt->updown = "$txt->up/$txt->down";

// construct the display array, with enabled auth plugins at the top, in order
$displayauths = array();
$registrationauths = array();
$registrationauths[''] = $txt->disable;
foreach ($authsenabled as $auth) {
    $authplugin = get_auth_plugin($auth);
    $displayauths[$auth] = get_string("auth_{$auth}title", 'auth');
    if (method_exists($authplugin, 'user_signup')) {
        $registrationauths[$auth] = get_string("auth_{$auth}title", 'auth');
    }
}

foreach ($authsavailable as $auth) {
    if (array_key_exists($auth, $displayauths)) {
        continue; //already in the list
    }
    $authplugin = get_auth_plugin($auth);
    $displayauths[$auth] = get_string("auth_{$auth}title", 'auth');
    if (method_exists($authplugin, 'user_signup')) {
        $registrationauths[$auth] = get_string("auth_{$auth}title", 'auth');
    }
}

// build the display table
$table = new flexible_table('auth_admin_table');
$table->define_columns(array('name', 'enable', 'order', 'settings'));
$table->define_headers(array($txt->name, $txt->enable, $txt->updown, $txt->settings));
$table->define_baseurl("{$CFG->wwwroot}/{$CFG->admin}/auth.php");
$table->set_attribute('id', 'blocks');
$table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
$table->setup();

//add always enabled plugins first
$displayname = "<span>".$displayauths['manual']."</span>";
$settings = "<a href=\"auth_config.php?sesskey={$USER->sesskey}&amp;auth=manual\">{$txt->settings}</a>";
$table->add_data(array($displayname, '', '', $settings));
$displayname = "<span>".$displayauths['nologin']."</span>";
$settings = "<a href=\"auth_config.php?sesskey={$USER->sesskey}&amp;auth=nologin\">{$txt->settings}</a>";
$table->add_data(array($displayname, '', '', $settings));


// iterate through auth plugins and add to the display table
$updowncount = 1;
$authcount = count($authsenabled);
$url = "auth.php?sesskey=" . sesskey();
foreach ($displayauths as $auth => $name) {
    if ($auth == 'manual' or $auth == 'nologin') {
        continue;
    }
    // hide/show link
    if (in_array($auth, $authsenabled)) {
        $hideshow = "<a href=\"$url&amp;action=disable&amp;auth=$auth\">";
        $hideshow .= "<img src=\"{$CFG->pixpath}/i/hide.gif\" class=\"icon\" alt=\"disable\" /></a>";
        // $hideshow = "<a href=\"$url&amp;action=disable&amp;auth=$auth\"><input type=\"checkbox\" checked /></a>";
        $enabled = true;
        $displayname = "<span>$name</span>";
    }
    else {
        $hideshow = "<a href=\"$url&amp;action=enable&amp;auth=$auth\">";
        $hideshow .= "<img src=\"{$CFG->pixpath}/i/show.gif\" class=\"icon\" alt=\"enable\" /></a>";
        // $hideshow = "<a href=\"$url&amp;action=enable&amp;auth=$auth\"><input type=\"checkbox\" /></a>";
        $enabled = false;
        $displayname = "<span class=\"dimmed_text\">$name</span>";
    }

    // up/down link (only if auth is enabled)
    $updown = '';
    if ($enabled) {
        if ($updowncount > 1) {
            $updown .= "<a href=\"$url&amp;action=up&amp;auth=$auth\">";
            $updown .= "<img src=\"{$CFG->pixpath}/t/up.gif\" alt=\"up\" /></a>&nbsp;";
        }
        else {
            $updown .= "<img src=\"{$CFG->pixpath}/spacer.gif\" class=\"icon\" alt=\"\" />&nbsp;";
        }
        if ($updowncount < $authcount) {
            $updown .= "<a href=\"$url&amp;action=down&amp;auth=$auth\">";
            $updown .= "<img src=\"{$CFG->pixpath}/t/down.gif\" alt=\"down\" /></a>";
        }
        else {
            $updown .= "<img src=\"{$CFG->pixpath}/spacer.gif\" class=\"icon\" alt=\"\" />";
        }
        ++ $updowncount;
    }

    // settings link
    $settings = "<a href=\"auth_config.php?sesskey={$USER->sesskey}&amp;auth=$auth\">{$txt->settings}</a>";

    // add a row to the table
    $table->add_data(array($displayname, $hideshow, $updown, $settings));
}

// output form
admin_externalpage_print_header($adminroot);

//print stus messages
if ($status !== '') {
    notify($status, 'notifysuccess');
}

print_simple_box(get_string('configauthenticationplugins', 'admin'), 'center', '700');

$table->print_html();

////////////////////////////////////////////////////////////////////////////////

$guestoptions = array();
$guestoptions[0] = get_string("hide");
$guestoptions[1] = get_string("show");

echo '<hr />';

echo '<form '.$CFG->frametarget.' id="adminsettings" method="post" action="auth.php">';
echo '<div class="settingsform">';
print_heading(get_string('auth_common_settings', 'auth'));
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<input type="hidden" name="action" value="save" />';
echo '<fieldset>';
##echo '<table cellspacing="0" cellpadding="5" border="0" style="margin-left:auto;margin-right:auto">';

// User self registration
echo '<div class="form-item" id="admin-register">';
echo '<label for = "menuregister">' . get_string("selfregistration", "auth");
echo '<span class="form-shortname">registerauth</span>';
echo '</label>';
choose_from_menu($registrationauths, "register", $CFG->registerauth, "");
echo '<div class="description">' . get_string("selfregistration_help", "auth") . '</div>';
echo '</div>';

// Login as guest button enabled
echo '<div class="form-item" id="admin-guestloginbutton">';
echo '<label for = "menuguestloginbutton">' . get_string("guestloginbutton", "auth");
echo '<span class="form-shortname">guestloginbutton</span>';
echo '</label>';
choose_from_menu($guestoptions, "guestloginbutton", $CFG->guestloginbutton, "");
echo '<div class="description">' . get_string("showguestlogin", "auth") . '</div>';
echo '</div>';

/// An alternate url for the login form. It means we can use login forms that are integrated
/// into non-moodle pages
echo '<div class="form-item" id="admin-alternateloginurl">';
echo '<label for = "alternateloginurl">' . get_string("alternateloginurl", "auth");
echo '<span class="form-shortname">alternateloginurl</span>';
echo '</label>';
echo '<input type="text" size="60" name="alternateloginurl" id="alternateloginurl" value="'.$CFG->alternateloginurl."\" />\n";
echo '<div class="description">' . get_string("alternatelogin", "auth", htmlspecialchars($CFG->wwwroot.'/login/index.php')) . '</div>';
echo '</div>';

/// Instructions about login/password
/// to be showed to users
echo '<div class="form-item" id="admin-auth_instructions">';
echo '<label for = "auth_instructions">' . get_string("instructions", "auth");
echo '<span class="form-shortname">auth_instructions</span>';
echo '</label>';
echo '<textarea cols="30" rows="4" name="auth_instructions" id="auth_instructions">'.s($CFG->auth_instructions)."</textarea>\n";
echo '<div class="description">' . get_string("authinstructions", "auth") . '</div>';
echo '</div>';

echo '</fieldset>';

////////////////////////////////////////////////////////////////////////////////
echo '<div class="form-buttons"><input class="form-submit" type="submit" value="'.get_string('savechanges', 'admin').'" /></div>';
echo '</div>';
echo '</form>';
admin_externalpage_print_footer($adminroot);

?>
