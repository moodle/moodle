<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file is part of the login section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package login
 */

require_once("../config.php");

redirect_if_major_upgrade_required();

$loginguest  = optional_param('loginguest', 0, PARAM_BOOL); // determines whether visitors are logged in as guest automatically
$testcookies = optional_param('testcookies', 0, PARAM_BOOL); // request cookie test

$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_course($SITE);
$PAGE->set_pagelayout('login');

/// Initialize variables
$errormsg = '';
$errorcode = 0;

/// Check for timed out sessions
if (!empty($SESSION->has_timed_out)) {
    $session_has_timed_out = true;
    unset($SESSION->has_timed_out);
} else {
    $session_has_timed_out = false;
}

/// auth plugins may override these - SSO anyone?
$frm  = false;
$user = false;

$authsequence = get_enabled_auth_plugins(true); // auths, in sequence
foreach($authsequence as $authname) {
    $authplugin = get_auth_plugin($authname);
    $authplugin->loginpage_hook();
}

//HTTPS is potentially required in this page
httpsrequired();

$PAGE->set_url("$CFG->httpswwwroot/login/index.php");


/// Define variables used in page
$site = get_site();

$loginsite = get_string("loginsite");
$PAGE->navbar->add($loginsite);

if ($user !== false or $frm !== false) {
    // some auth plugin already supplied these

} else if ((!empty($SESSION->wantsurl) and strstr($SESSION->wantsurl,'username=guest')) or $loginguest) {
    /// Log in as guest automatically (idea from Zbigniew Fiedorowicz)
    $frm->username = 'guest';
    $frm->password = 'guest';

} else if (!empty($SESSION->wantsurl) && file_exists($CFG->dirroot.'/login/weblinkauth.php')) {
    // Handles the case of another Moodle site linking into a page on this site
    //TODO: move weblink into own auth plugin
    include($CFG->dirroot.'/login/weblinkauth.php');
    if (function_exists('weblink_auth')) {
        $user = weblink_auth($SESSION->wantsurl);
    }
    if ($user) {
        $frm->username = $user->username;
    } else {
        $frm = data_submitted();
    }

} else {
    $frm = data_submitted();
}

/// Check if the user has actually submitted login data to us

if (empty($CFG->usesid) and $testcookies and (get_moodle_cookie() == '')) {    // Login without cookie when test requested

    $errormsg = get_string("cookiesnotenabled");
    $errorcode = 1;

} else if ($frm) {                             // Login WITH cookies

    $frm->username = trim(moodle_strtolower($frm->username));

    if (is_enabled_auth('none') ) {
        $string = clean_param($frm->username, PARAM_USERNAME);
        if (strcmp($frm->username, $string)) {
            $errormsg = get_string('username').': '.get_string("invalidusername");
            $errorcode = 2;
            $user = null;
        }
    }

    if ($user) {
        //user already supplied by aut plugin prelogin hook
    } else if (($frm->username == 'guest') and empty($CFG->guestloginbutton)) {
        $user = false;    /// Can't log in as guest if guest button is disabled
        $frm = false;
    } else {
        if (empty($errormsg)) {
            $user = authenticate_user_login($frm->username, $frm->password);
        }
    }

    // Intercept 'restored' users to provide them with info & reset password
    if (!$user and $frm and is_restored_user($frm->username)) {
        $PAGE->set_title(get_string('restoredaccount'));
        $PAGE->set_heading($site->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('restoredaccount'));
        echo $OUTPUT->box(get_string('restoredaccountinfo'), 'generalbox boxaligncenter');
        require_once('restored_password_form.php'); // Use our "supplanter" login_forgot_password_form. MDL-20846
        $form = new login_forgot_password_form('forgot_password.php', array('username' => $frm->username));
        $form->display();
        echo $OUTPUT->footer();
        die;
    }

    update_login_count();

    if ($user) {

        // language setup
        if ($user->username == 'guest') {
            // no predefined language for guests - use existing session or default site lang
            unset($user->lang);

        } else if (!empty($user->lang)) {
            // unset previous session language - use user preference instead
            unset($SESSION->lang);
        }

        if (empty($user->confirmed)) {       // This account was never confirmed
            $PAGE->set_title(get_string("mustconfirm"));
            $PAGE->set_heading($site->fullname);
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string("mustconfirm"));
            echo $OUTPUT->box(get_string("emailconfirmsent", "", $user->email), "generalbox boxaligncenter");
            echo $OUTPUT->footer();
            die;
        }

        if ($frm->password == 'changeme') {
            //force the change
            set_user_preference('auth_forcepasswordchange', true, $user->id);
        }

    /// Let's get them all set up.
        add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID,
                   $user->id, 0, $user->id);
        complete_user_login($user);

    /// Prepare redirection
        if (user_not_fully_set_up($USER)) {
            $urltogo = $CFG->wwwroot.'/user/edit.php';
            // We don't delete $SESSION->wantsurl yet, so we get there later

        } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
            $urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
            unset($SESSION->wantsurl);

        } else {
            // no wantsurl stored or external - go to homepage
            $urltogo = $CFG->wwwroot.'/';
            unset($SESSION->wantsurl);
        }

    /// Go to my-moodle page instead of homepage if mymoodleredirect enabled
        if (!has_capability('moodle/site:config', $context) and !empty($CFG->mymoodleredirect) and !has_capability('moodle/legacy:guest',$context, 0, false)) {
            if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot.'/' or $urltogo == $CFG->wwwroot.'/index.php') {
                $urltogo = $CFG->wwwroot.'/my/';
            }
        }


    /// check if user password has expired
    /// Currently supported only for ldap-authentication module
        $userauth = get_auth_plugin($USER->auth);
        if (!empty($userauth->config->expiration) and $userauth->config->expiration == 1) {
            if ($userauth->can_change_password()) {
                $passwordchangeurl = $userauth->change_password_url();
                if(!$passwordchangeurl) {
                    $passwordchangeurl = $CFG->httpswwwroot.'/login/change_password.php';
                }
            } else {
                $passwordchangeurl = $CFG->httpswwwroot.'/login/change_password.php';
            }
            $days2expire = $userauth->password_expire($USER->username);
            $PAGE->set_title("$site->fullname: $loginsite");
            $PAGE->set_heading("$site->fullname");
            if (intval($days2expire) > 0 && intval($days2expire) < intval($userauth->config->expiration_warning)) {
                echo $OUTPUT->header();
                echo $OUTPUT->confirm(get_string('auth_passwordwillexpire', 'auth', $days2expire), $passwordchangeurl, $urltogo);
                echo $OUTPUT->footer();
                exit;
            } elseif (intval($days2expire) < 0 ) {
                echo $OUTPUT->header();
                echo $OUTPUT->confirm(get_string('auth_passwordisexpired', 'auth'), $passwordchangeurl, $urltogo);
                echo $OUTPUT->footer();
                exit;
            }
        }

        reset_login_count();

        redirect($urltogo);

        exit;

    } else {
        if (empty($errormsg)) {
            $errormsg = get_string("invalidlogin");
            $errorcode = 3;
        }
    }
}

/// Detect problems with timedout sessions
if ($session_has_timed_out and !data_submitted()) {
    $errormsg = get_string('sessionerroruser', 'error');
    $errorcode = 4;
}

/// First, let's remember where the user was trying to get to before they got here

if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = (array_key_exists('HTTP_REFERER',$_SERVER) &&
                          $_SERVER["HTTP_REFERER"] != $CFG->wwwroot &&
                          $_SERVER["HTTP_REFERER"] != $CFG->wwwroot.'/' &&
                          $_SERVER["HTTP_REFERER"] != $CFG->httpswwwroot.'/login/' &&
                          $_SERVER["HTTP_REFERER"] != $CFG->httpswwwroot.'/login/index.php')
        ? $_SERVER["HTTP_REFERER"] : NULL;
}

/// Redirect to alternative login URL if needed
if (!empty($CFG->alternateloginurl)) {
    $loginurl = $CFG->alternateloginurl;

    if (strpos($SESSION->wantsurl, $loginurl) === 0) {
        //we do not want to return to alternate url
        $SESSION->wantsurl = NULL;
    }

    if ($errorcode) {
        if (strpos($loginurl, '?') === false) {
            $loginurl .= '?';
        } else {
            $loginurl .= '&';
        }
        $loginurl .= 'errorcode='.$errorcode;
    }

    redirect($loginurl);
}


/// Generate the login page with forms

if (get_moodle_cookie() == '') {
    set_moodle_cookie('nobody');   // To help search for cookies
}

if (empty($frm->username) && $authsequence[0] != 'shibboleth') {  // See bug 5184
    if (!empty($_GET["username"])) {
        $frm->username = $_GET["username"];
    } else {
        $frm->username = get_moodle_cookie() === 'nobody' ? '' : get_moodle_cookie();
    }

    $frm->password = "";
}

if (!empty($frm->username)) {
    $focus = "password";
} else {
    $focus = "username";
}

if (!empty($CFG->registerauth) or is_enabled_auth('none') or !empty($CFG->auth_instructions)) {
    $show_instructions = true;
} else {
    $show_instructions = false;
}

$potentialidps = array();
foreach($authsequence as $authname) {
    $authplugin = get_auth_plugin($authname);
    $potentialidps = array_merge($potentialidps, $authplugin->loginpage_idp_list($SESSION->wantsurl));
}

$PAGE->set_title("$site->fullname: $loginsite");
$PAGE->set_heading("$site->fullname");
$PAGE->set_focuscontrol($focus);

echo $OUTPUT->header();
include("index_form.html");
echo $OUTPUT->footer();
