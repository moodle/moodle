<?php // $Id$


    require_once("../config.php");

    // check if major upgrade needed - also present in /index.php
    if ((int)$CFG->version < 2006101100) { //1.7 or older
        @require_logout();
        redirect("$CFG->wwwroot/$CFG->admin/");
    }

    $loginguest  = optional_param('loginguest', 0, PARAM_BOOL); // determines whether visitors are logged in as guest automatically
    $testcookies = optional_param('testcookies', 0, PARAM_BOOL); // request cookie test

    //initialize variables
    $errormsg = '';
    $errorcode = 0;

/// Check for timed out sessions
    if (!empty($SESSION->has_timed_out)) {
        $session_has_timed_out = true;
        $SESSION->has_timed_out = false;
    } else {
        $session_has_timed_out = false;
    }

/// Check if the guest user exists.  If not, create one.
    if (! record_exists('user', 'username', 'guest')) {
        if (! $guest = create_guest_record()) {
            notify('Could not create guest user record !!!');
        }
    }

// setup and verify auth settings

if (!isset($CFG->registerauth)) {
    set_config('registerauth', '');
}

if (!isset($CFG->auth_instructions)) {
    set_config('auth_instructions', '');
}

// auth plugins may override these - SSO anyone?
$frm  = false;
$user = false;

$authsequence = get_enabled_auth_plugins(true); // auths, in sequence
foreach($authsequence as $authname) {
    $authplugin = get_auth_plugin($authname);
    $authplugin->loginpage_hook();
}

//HTTPS is potentially required in this page
httpsrequired();

/// Define variables used in page
    if (!$site = get_site()) {
        error("No site found!");
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $langlabel = '<span class="accesshide">'.get_string('language').':</span>';
        $langmenu = popup_form ("$CFG->httpswwwroot/login/index.php?lang=", $langs, "chooselang", $currlang, "", "", "", true, 'self', $langlabel);
    }

    $loginsite = get_string("loginsite");

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

/// Check if the user has actually submitted login data to us

    if (empty($CFG->usesid) and $testcookies and (get_moodle_cookie() == '')) {    // Login without cookie when test requested

        $errormsg = get_string("cookiesnotenabled");
        $errorcode = 1;

    } else if ($frm) {                             // Login WITH cookies

        $frm->username = trim(moodle_strtolower($frm->username));

        if (is_enabled_auth('none') && empty($CFG->extendedusernamechars)) {
            $string = eregi_replace("[^(-\.[:alnum:])]", "", $frm->username);
            if (strcmp($frm->username, $string)) {
                $errormsg = get_string('username').': '.get_string("alphanumerical");
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
                print_header(get_string("mustconfirm"), get_string("mustconfirm") ); 
                print_heading(get_string("mustconfirm"));
                print_simple_box(get_string("emailconfirmsent", "", $user->email), "center");
                print_footer();
                die;
            }

            // Let's get them all set up.
            $USER = $user;

            add_to_log(SITEID, 'user', 'login', "view.php?id=$USER->id&course=".SITEID, $USER->id, 0, $USER->id);


            update_user_login_times();
            if (empty($CFG->nolastloggedin)) {
                set_moodle_cookie($USER->username);
            } else {
                // do not store last logged in user in cookie
                // auth plugins can temporarily override this from loginpage_hook()
                // do not save $CFG->nolastloggedin in database!
                set_moodle_cookie('nobody');
            }
            set_login_session_preferences();

            /// This is what lets the user do anything on the site :-)
            load_all_capabilities();
            $userauth = get_auth_plugin($USER->auth);

            // check whether the user should be changing password
            if (get_user_preferences('auth_forcepasswordchange', false) || $frm->password == 'changeme'){
                //Select password change url
                if ($userauth->can_change_password()) {
                    if ($changeurl = $userauth->change_password_url()) {
                        redirect($changeurl);
                    } else {
                        redirect($CFG->httpswwwroot.'/login/change_password.php');
                    }
                } else {
                    error(get_string('nopasswordchangeforced', 'auth'));
                }
            }


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
            if (!has_capability('moodle/site:config',get_context_instance(CONTEXT_SYSTEM)) and !empty($CFG->mymoodleredirect) and !isguest()) {
                if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot.'/' or $urltogo == $CFG->wwwroot.'/index.php') {
                    $urltogo = $CFG->wwwroot.'/my/';
                }
            }


            // check if user password has expired
            // Currently supported only for ldap-authentication module
            if (!empty($userauth->config->expiration) and $userauth->config->expiration == 1) {
                    $days2expire = $userauth->password_expire($USER->username);
                        if (intval($days2expire) > 0 && intval($days2expire) < intval($userauth->config->expiration_warning)) {
                        print_header("$site->fullname: $loginsite", "$site->fullname", $loginsite, $focus, "", true, "<div class=\"langmenu\">$langmenu</div>"); 
                        notice_yesno(get_string('auth_passwordwillexpire', 'auth', $days2expire), $passwordchangeurl, $urltogo); 
                        print_footer();
                        exit;
                    } elseif (intval($days2expire) < 0 ) {
                        print_header("$site->fullname: $loginsite", "$site->fullname", $loginsite, $focus, "", true, "<div class=\"langmenu\">$langmenu</div>"); 
                        notice_yesno(get_string('auth_passwordisexpired', 'auth'), $passwordchangeurl, $urltogo);
                        print_footer();
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

            // TODO: if the user failed to authenticate, check if the username corresponds to a remote mnet user
            if ( !empty($CFG->mnet_dispatcher_mode) 
                 && $CFG->mnet_dispatcher_mode === 'strict'
                 && is_enabled_auth('mnet')) {
                $errormsg .= get_string('loginlinkmnetuser', 'mnet', "mnet_email.php?u=$frm->username");
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
        $frm->username = get_moodle_cookie() === 'nobody' ? '' : get_moodle_cookie();
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

    print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, 
                 '', true, '<div class="langmenu">'.$langmenu.'</div>'); 

    include("index_form.html");

    print_footer();


?>
