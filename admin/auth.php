<?php

/**
 * Allows admin to edit all auth plugin settings.
 *
 * JH: copied and Hax0rd from admin/enrol.php and admin/filters.php
 *
 */

require_once dirname(dirname(__FILE__)) . '/config.php';
require_once $CFG->libdir . '/tablelib.php';
require_once($CFG->libdir.'/adminlib.php');

$adminroot = admin_get_root();
admin_externalpage_setup('userauthentication', $adminroot);

// get currently installed and enabled auth plugins
$authsavailable = get_list_of_plugins('auth');
if (empty($CFG->auth_plugins_enabled)) {
    set_config('auth_plugins_enabled', $CFG->auth);
    $CFG->auth_plugins_enabled = $CFG->auth;
}
$authsenabled = explode(',', $CFG->auth_plugins_enabled);

// save form
if ($form = data_submitted()) {

    if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    }

    if (! isset($form->guestloginbutton)) {
        $form->guestloginbutton = 1;
    }
    if (empty($form->alternateloginurl)) {
        $form->alternateloginurl = '';
    }
    if (empty($form->register)) {
        $form->register = 'manual';
    }
    set_config('guestloginbutton', $form->guestloginbutton);
    set_config('alternateloginurl', $form->alternateloginurl);
    set_config('auth', $form->register);

    // add $CFG->auth to auth_plugins_enabled list
    if (!array_search($form->register, $authsenabled)) {
        $authsenabled[] = $form->register;
        $authsenabled = array_unique($authsenabled);
        set_config('auth_plugins_enabled', implode(',', $authsenabled));
    }
}

// grab GET/POST parameters
$params = new object();
$params->action    = optional_param('action', '', PARAM_ACTION);
$params->auth      = optional_param('auth', $CFG->auth, PARAM_ALPHANUM);

////////////////////////////////////////////////////////////////////////////////
// process actions

switch ($params->action) {

    case 'disable':
        // remove from enabled list 
        $key = array_search($params->auth, $authsenabled);
        if ($key !== false and $params->auth != $CFG->auth) {
            unset($authsenabled[$key]);
            set_config('auth_plugins_enabled', implode(',', $authsenabled));
        }
        break;
        
    case 'enable':
        // check auth plugin is valid first
        if (!exists_auth_plugin($params->auth)) {
            error(get_string('pluginnotinstalled', 'auth', $params->auth), $url);
        }
        // add to enabled list
        if (!array_search($params->auth, $authsenabled)) {
            $authsenabled[] = $params->auth;
            $authsenabled = array_unique($authsenabled);
            set_config('auth_plugins_enabled', implode(',', $authsenabled));
        }
        break;
        
    case 'down':
        $key = array_search($params->auth, $authsenabled);
        // check auth plugin is valid
        if ($key === false) {
            error(get_string('pluginnotenabled', 'auth', $params->auth), $url);
        }
        // move down the list
        if ($key < (count($authsenabled) - 1)) {
            $fsave = $authsenabled[$key];
            $authsenabled[$key] = $authsenabled[$key + 1];
            $authsenabled[$key + 1] = $fsave;
            set_config('auth_plugins_enabled', implode(',', $authsenabled));
        }
        break;
        
    case 'up':
        $key = array_search($params->auth, $authsenabled);
        // check auth is valid
        if ($key === false) {
            error(get_string('pluginnotenabled', 'auth', $params->auth), $url);
        }
        // move up the list
        if ($key >= 1) {
            $fsave = $authsenabled[$key];
            $authsenabled[$key] = $authsenabled[$key - 1];
            $authsenabled[$key - 1] = $fsave;
            set_config('auth_plugins_enabled', implode(',', $authsenabled));
        }
        break;
        
    case 'save':
        // save settings
        set_config('auth_plugins_enabled', implode(',', $authsenabled));
        set_config('auth', $authsenabled[0]);
        redirect("auth.php?sesskey=$USER->sesskey", get_string('changessaved'), 1);
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
$registrationauths['manual'] = $txt->disable;
foreach ($authsenabled as $auth) {
    $displayauths[$auth] = get_string("auth_{$auth}title", 'auth');
    $authplugin = get_auth_plugin($auth);
    if (method_exists($authplugin, 'user_signup')) {
        $registrationauths[$auth] = get_string("auth_{$auth}title", 'auth');
    }    
}
foreach ($authsavailable as $auth) {
    if (!array_key_exists($auth, $displayauths)) {
        $displayauths[$auth] = get_string("auth_{$auth}title", 'auth');
    }
    $authplugin = get_auth_plugin($auth);
    if (method_exists($authplugin, 'user_signup')) {
        $registrationauths[$auth] = get_string("auth_{$auth}title", 'auth');
    }    
}

// build the display table
$table = new flexible_table('auth_admin_table');
$table->define_columns(array('name', 'enable', 'order', 'settings'));
$table->column_style('enable', 'text-align', 'center');
$table->column_style('order', 'text-align', 'center');
$table->column_style('settings', 'text-align', 'center');
$table->define_headers(array($txt->name, $txt->enable, $txt->updown, $txt->settings));
$table->define_baseurl("{$CFG->wwwroot}/{$CFG->admin}/auth.php");
$table->set_attribute('id', 'blocks');
$table->set_attribute('class', 'flexible generaltable generalbox');
$table->set_attribute('style', 'margin:auto;');
$table->set_attribute('cellpadding', '5');
$table->setup();

// iterate through auth plugins and add to the display table
$updowncount = 1;
$authcount = count($authsenabled);
$url = "auth.php?sesskey=" . sesskey();
foreach ($displayauths as $auth => $name) {
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
print_simple_box(get_string('configauthenticationplugins', 'admin'), 'center', '700');

echo "<form $CFG->frametarget id=\"authmenu\" method=\"post\" action=\"auth.php\">";
echo "<fieldset class=\"invisiblefieldset\"><input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\" /></fieldset>";
print_table($table);

////////////////////////////////////////////////////////////////////////////////

$guestoptions[0] = get_string("hide");
$guestoptions[1] = get_string("show");

echo '<hr />';
print_heading(get_string('auth_common_settings', 'auth'));
echo '<table cellspacing="0" cellpadding="5" border="0" style="margin-left:auto;margin-right:auto">';

// User self registration
echo "<tr valign=\"top\">\n";
echo "<td align=\"right\" style=\"white-space:nowrap\">\n";
print_string("selfregistration", "auth");
echo ":</td>\n";
echo "<td>\n";
choose_from_menu($registrationauths, "register", $CFG->auth, "");
echo "</td>\n";
echo "<td>\n";
print_string("selfregistration_help", "auth");
echo "</td></tr>\n";

// Login as guest button enabled
echo "<tr valign=\"top\">\n";
echo "<td style=\"white-space:nowrap;text-align:right\">\n";
print_string("guestloginbutton", "auth");
echo ":</td>\n";
echo "<td>\n";
choose_from_menu($guestoptions, "guestloginbutton", $CFG->guestloginbutton, "");
echo "</td>\n";
echo "<td>\n";
print_string("showguestlogin","auth");
echo "</td></tr>\n";

/// An alternate url for the login form. It means we can use login forms that are integrated
/// into non-moodle pages
echo "<tr valign=\"top\">\n";
echo "<td algin=\"right\" style=\"white-space:nowrap\">\n";
print_string('alternateloginurl', 'auth');
echo "</td>\n";
echo "<td>\n";
echo '<input type="text" size="40" name="alternateloginurl" alt="'.get_string('alternateloginurl', 'auth').'" value="'.$CFG->alternateloginurl."\" />\n";
echo "</td>\n";
echo "<td>\n";
print_string('alternatelogin', 'auth', htmlspecialchars($CFG->wwwroot.'/login/index.php'));
echo "</td>\n";
echo "</tr>\n";

echo "</table>\n";

////////////////////////////////////////////////////////////////////////////////


echo '<div style="text-align:center"><input type="submit" value="'.get_string('savechanges').'" /></div>';
echo '</form>';
admin_externalpage_print_footer($adminroot);

?>
