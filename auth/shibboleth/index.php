<?php

    // Designed to be redirected from moodle/login/index.php

    require('../../config.php');

    $context = context_system::instance();
    $PAGE->set_url('/auth/shibboleth/index.php');
    $PAGE->set_context($context);

    // Support for WAYFless URLs.
    $target = optional_param('target', '', PARAM_LOCALURL);
    if (!empty($target) && empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $target;
    }

    if (isloggedin() && !isguestuser()) {      // Nothing to do
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
    $shibbolethauth = get_auth_plugin('shibboleth');

    // Check whether Shibboleth is configured properly
    if (empty($pluginconfig->user_attribute)) {
        print_error('shib_not_set_up_error', 'auth_shibboleth');
     }

/// If we can find the Shibboleth attribute, save it in session and return to main login page
    if (!empty($_SERVER[$pluginconfig->user_attribute])) {    // Shibboleth auto-login
        $frm = new stdClass();
        $frm->username = strtolower($_SERVER[$pluginconfig->user_attribute]);
        // The password is never actually used, but needs to be passed to the functions 'user_login' and
        // 'authenticate_user_login'. Shibboleth returns true for the function 'prevent_local_password', which is
        // used when setting the password in 'update_internal_user_password'. When 'prevent_local_password'
        // returns true, the password is set to 'not cached' (AUTH_PASSWORD_NOT_CACHED) in the Moodle DB. However,
        // rather than setting the password to a hard-coded value, we will generate one each time, in case there are
        // changes to the Shibboleth plugin and it is actually used.
        $frm->password = generate_password(8);

    /// Check if the user has actually submitted login data to us

        if ($shibbolethauth->user_login($frm->username, $frm->password)
                && $user = authenticate_user_login($frm->username, $frm->password)) {
            complete_user_login($user);

            if (user_not_fully_set_up($USER, true)) {
                $urltogo = $CFG->wwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.SITEID;
                // We don't delete $SESSION->wantsurl yet, so we get there later

            } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
                $urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
                unset($SESSION->wantsurl);

            } else {
                $urltogo = $CFG->wwwroot.'/';      /// Go to the standard home page
                unset($SESSION->wantsurl);         /// Just in case
            }

            /// Go to my-moodle page instead of homepage if defaulthomepage enabled
            if (!has_capability('moodle/site:config',context_system::instance()) and !empty($CFG->defaulthomepage) && $CFG->defaulthomepage == HOMEPAGE_MY and !isguestuser()) {
                if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot.'/' or $urltogo == $CFG->wwwroot.'/index.php') {
                    $urltogo = $CFG->wwwroot.'/my/';
                }
            }

            redirect($urltogo);

            exit;
        }

        else {
            // The Shibboleth user couldn't be mapped to a valid Moodle user
            print_error('shib_invalid_account_error', 'auth_shibboleth');
        }
    }

    // If we can find any (user independent) Shibboleth attributes but no user
    // attributes we probably didn't receive any user attributes
    elseif (!empty($_SERVER['HTTP_SHIB_APPLICATION_ID']) || !empty($_SERVER['Shib-Application-ID'])) {
        print_error('shib_no_attributes_error', 'auth_shibboleth' , '', '\''.$pluginconfig->user_attribute.'\', \''.$pluginconfig->field_map_firstname.'\', \''.$pluginconfig->field_map_lastname.'\' and \''.$pluginconfig->field_map_email.'\'');
    } else {
        print_error('shib_not_set_up_error', 'auth_shibboleth');
    }


