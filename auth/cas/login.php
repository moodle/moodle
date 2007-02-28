<?php
// $Id$
// author: romualdLorthioir $
//CHANGELOG:
//05.03.2005 replace /login/index.php
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

    //Define variables used in page
    if (!$site = get_site()) {
        print_error('nosite', '', '', NULL, true);
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http:','https:',$CFG->wwwroot);
        }
        $langmenu = popup_form ("$wwwroot/login/index.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    $loginsite = get_string("loginsite");
    $casauth = get_auth_plugin('cas');
    $ldapauth = get_auth_plugin('ldap');


    $frm = false;
    $user = false;
    if ((!empty($SESSION->wantsurl) and strstr($SESSION->wantsurl,'username=guest')) or $loginguest) {
        /// Log in as guest automatically (idea from Zbigniew Fiedorowicz)
        $frm->username = 'guest';
        $frm->password = 'guest';
    } else if (!empty($SESSION->wantsurl) && file_exists($CFG->dirroot.'/login/weblinkauth.php')) {
        // Handles the case of another Moodle site linking into a page on this site
        include($CFG->dirroot.'/login/weblinkauth.php');
        if (function_exists(weblink_auth)) {
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

    if ($frm and (get_moodle_cookie() == '')) {    // Login without cookie

        $errormsg = get_string("cookiesnotenabled");

    } else if ($frm) {                             // Login WITH cookies

        $frm->username = trim(moodle_strtolower($frm->username));

        if (($frm->username == 'guest') and empty($CFG->guestloginbutton)) {
            $user = false;    /// Can't log in as guest if guest button is disabled
            $frm = false;
        } else if (!$user) {
            if ($CFG->auth == "cas" && $frm->username != 'guest') { /// Cas SSO case
               $user = $casauth->authenticate_user_login($frm->username, $frm->password);
            }else{
               $user = authenticate_user_login($frm->username, $frm->password);
            }
        }
        update_login_count();

        if ($user) {
            if (! $user->confirmed ) {       // they never confirmed via email
                print_header(get_string("mustconfirm"), get_string("mustconfirm") );
                print_heading(get_string("mustconfirm"));
                print_simple_box(get_string("emailconfirmsent", "", $user->email), "center");
                print_footer();
                die;
            }

            $USER = $user;
            if (!empty($USER->description)) {
                $USER->description = true;   // No need to cart all of it around
            }
            $USER->loggedin = true;
            $USER->site     = $CFG->wwwroot; // for added security, store the site in the session
            sesskey();                       // for added security, used to check script parameters

            if ($USER->username == "guest") {
                $USER->lang       = $CFG->lang;               // Guest language always same as site
                $USER->firstname  = get_string("guestuser");  // Name always in current language
                $USER->lastname   = " ";
            }

            if (!update_user_login_times()) {
                error("Wierd error: could not update login records");
            }

            set_moodle_cookie($USER->username);

            unset($SESSION->lang);
            $SESSION->justloggedin = true;

            // Restore the calendar filters, if saved
            if (intval(get_user_preferences('calendar_persistflt', 0))) {
                include_once($CFG->dirroot.'/calendar/lib.php');
                calendar_set_filters_status(get_user_preferences('calendar_savedflt', 0xff));
            }

            //Select password change url
            $userauth = get_auth_plugin($USER->auth);
            if (method_exists($userauth, 'can_change_password') and $userauth->can_change_password()) {
                $passwordchangeurl=$CFG->wwwroot.'/login/change_password.php';
            }

            // check whether the user should be changing password
            if (get_user_preferences('auth_forcepasswordchange', false)) {
                if (isset($passwordchangeurl)) {
                    redirect($passwordchangeurl);
                } else {
                    print_error('auth_cas_broken_password','auth');
                }
            }


            add_to_log(SITEID, "user", "login", "view.php?id=$user->id&course=".SITEID, $user->id, 0, $user->id);

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

            // check if user password has expired
            // Currently supported only for ldap-authentication module
            if ($ldapauth->config->expiration == 1) {
                    $days2expire = $ldapauth->password_expire($USER->username);
                    if (intval($days2expire) > 0 && intval($days2expire) < intval($CFG->{$USER->auth.'_expiration_warning'})) {
                        print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, "", true, "<div align=\"right\">$langmenu</div>");
                        notice_yesno(get_string('auth_passwordwillexpire', 'auth', $days2expire), $passwordchangeurl, $urltogo);
                        print_footer();
                        exit;
                    } elseif (intval($days2expire) < 0 ) {
                        print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, "", true, "<div align=\"right\">$langmenu</div>");
                        notice_yesno(get_string('auth_passwordisexpired', 'auth'), $passwordchangeurl, $urltogo);
                        print_footer();
                        exit;
                    }
            }

            reset_login_count();

            load_all_capabilities();     /// This is what lets the user do anything on the site  :-)

            redirect($urltogo);

            exit;

        } else {
          if ($CFG->auth == "cas" ) { /// CAS error login
            $errormsg = get_string("invalidcaslogin");
            phpCAS::logout("$CFG->wwwroot/auth/cas/forbidden.php");
          }else{
            $errormsg = get_string("invalidlogin");
          }
        }
    }
    $user = $casauth->automatic_authenticate($user);
    if ($user) {
        if (! $user->confirmed ) {       // they never confirmed via email
            print_header(get_string("mustconfirm"), get_string("mustconfirm") );
            print_heading(get_string("mustconfirm"));
            print_simple_box(get_string("emailconfirmsent", "", $user->email), "center");
            print_footer();
            die;
        }

        $USER = $user;
        if (!empty($USER->description)) {
            $USER->description = true;   // No need to cart all of it around
        }
        $USER->loggedin = true;
        $USER->site     = $CFG->wwwroot; // for added security, store the site in the session
        sesskey();                       // for added security, used to check script parameters

        if ($USER->username == "guest") {
            $USER->lang       = $CFG->lang;               // Guest language always same as site
            $USER->firstname  = get_string("guestuser");  // Name always in current language
            $USER->lastname   = " ";
        }

        if (!update_user_login_times()) {
            error("Wierd error: could not update login records");
        }

        set_moodle_cookie($USER->username);

        unset($SESSION->lang);
        $SESSION->justloggedin = true;

        // Restore the calendar filters, if saved
        if (intval(get_user_preferences('calendar_persistflt', 0))) {
            include_once($CFG->dirroot.'/calendar/lib.php');
            calendar_set_filters_status(get_user_preferences('calendar_savedflt', 0xff));
        }

        //Select password change url
        $userauth = get_auth_plugin($USER->auth);
        if (method_exists($userauth, 'can_change_password') and $userauth->can_change_password()) {
            $passwordchangeurl=$CFG->wwwroot.'/login/change_password.php';
        }

        // check whether the user should be changing password
        if (get_user_preferences('auth_forcepasswordchange', false)) {
            if (isset($passwordchangeurl)) {
                redirect($passwordchangeurl);
            } else {
                print_error('auth_cas_broken_password','auth');
            }
        }


        add_to_log(SITEID, "user", "login", "view.php?id=$user->id&course=".SITEID, $user->id, 0, $user->id);

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

        // check if user password has expired
        // Currently supported only for ldap-authentication module
        if ($ldapauth->config->expiration == 1) {
                $days2expire = $ldapauth->password_expire($USER->username);
                if (intval($days2expire) > 0 && intval($days2expire) < intval($CFG->{$USER->auth.'_expiration_warning'})) {
                    print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, "", true, "<div class=\"langmenu\">$langmenu</div>");
                    notice_yesno(get_string('auth_passwordwillexpire', 'auth', $days2expire), $passwordchangeurl, $urltogo);
                    print_footer();
                    exit;
                } elseif (intval($days2expire) < 0 ) {
                    print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, "", true, "<div class=\"langmenu\">$langmenu</div>");
                    notice_yesno(get_string('auth_passwordisexpired', 'auth'), $passwordchangeurl, $urltogo);
                    print_footer();
                    exit;
                }
        }

        reset_login_count();

        load_all_capabilities();     /// This is what lets the user do anything on the site  :-)

        redirect($urltogo);

        exit;
    } else {
       if (!$CFG->guestloginbutton) {
           $errormsg = get_string("invalidcaslogin");
           phpCAS::logout("$CFG->wwwroot/auth/cas/forbidden.php");
       }
    }

    if (empty($errormsg)) {
        $errormsg = "";
    }

    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = array_key_exists('HTTP_REFERER',$_SERVER) ? $_SERVER["HTTP_REFERER"] : $CFG->wwwroot.'/';
    }

    if (get_moodle_cookie() == '') {
        set_moodle_cookie('nobody');   // To help search for cookies
    }

    if (empty($frm->username)) {
        $frm->username = get_moodle_cookie() === 'nobody' ? '' : get_moodle_cookie();
        $frm->password = "";
    }

    if (!empty($frm->username)) {
        $focus = "login.password";
    } else {
        $focus = "login.username";
    }

    if ($CFG->auth == "email" or $CFG->auth == "none" or chop($CFG->auth_instructions) <> "" ) {
        $show_instructions = true;
    } else {
        $show_instructions = false;
    }

    print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, "", true, "<div align=\"right\">$langmenu</div>");
    include($CFG->dirroot.'/auth/cas/index_form.html');
    print_footer();

    exit;

    // No footer on this page

?>
