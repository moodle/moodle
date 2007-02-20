<?php // $Id$
      // Designed to be redirected from moodle/login/index.php

    require('../../config.php');
    require('lib.php');

    if (isloggedin() && $USER->username != 'guest') {      // Nothing to do
        if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
            $urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
            unset($SESSION->wantsurl);

        } else {
            $urltogo = $CFG->wwwroot.'/';      /// Go to the standard home page
            unset($SESSION->wantsurl);         /// Just in case
        }

        redirect($urltogo);
    }

    $pluginconfig   = get_config('auth/shibboleth');

    // Check whether Shibboleth is configured properly
    if (empty($pluginconfig->user_attribute)) {
        error(get_string( 'shib_not_set_up_error', 'auth'));
     }

/// If we can find the Shibboleth attribute, save it in session and return to main login page
    if (!empty($_SERVER[$pluginconfig->user_attribute])) {    // Shibboleth auto-login
        $frm->username = $_SERVER[$pluginconfig->user_attribute];
        $frm->password = substr(base64_encode($_SERVER[$pluginconfig->user_attribute]),0,8);
        // The random password consists of the first 8 letters of the base 64 encoded user ID
        // This password is never used unless the user account is converted to manual

    /// Check if the user has actually submitted login data to us

        if ($user = authenticate_user_login($frm->username, $frm->password)) {

            // Let's get them all set up.
            $USER = $user;

            add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);

            update_user_login_times();
            set_moodle_cookie($USER->username);
            set_login_session_preferences();

            if (user_not_fully_set_up($USER)) {
                $urltogo = $CFG->wwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.SITEID;
                // We don't delete $SESSION->wantsurl yet, so we get there later

            } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
                $urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
                unset($SESSION->wantsurl);

            } else {
                $urltogo = $CFG->wwwroot.'/';      /// Go to the standard home page
                unset($SESSION->wantsurl);         /// Just in case
            }

            /// Go to my-moodle page instead of homepage if mymoodleredirect enabled
            if (!has_capability('moodle/site:config',get_context_instance(CONTEXT_SYSTEM, SITEID)) and !empty($CFG->mymoodleredirect) and !isguest()) {
                if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot.'/' or $urltogo == $CFG->wwwroot.'/index.php') {
                    $urltogo = $CFG->wwwroot.'/my/';
                }
            }

            load_all_capabilities();     /// This is what lets the user do anything on the site  :-)

            redirect($urltogo);
        }
    }

    // If we can find any (user independent) Shibboleth attributes but no user
    // attributes we probably didn't receive any user attributes
    elseif (!empty($_SERVER['HTTP_SHIB_APPLICATION_ID'])) {
        error(get_string( 'shib_no_attributes_error', 'auth' , '\''.$pluginconfig->user_attribute.'\', \''.$pluginconfig->field_map_firstname.'\', \''.$pluginconfig->field_map_lastname.'\' and \''.$pluginconfig->field_map_email.'\''));
    } else {
         error(get_string( 'shib_not_set_up_error', 'auth'));
    }

?>
