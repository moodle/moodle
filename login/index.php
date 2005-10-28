<?php // $Id$

    require_once("../config.php");

    $loginguest = optional_param('loginguest', false); // determines whether visitors are logged in as guest automatically

/// Check for timed out sessions
    if (!empty($SESSION->has_timed_out)) {
        $session_has_timed_out = true;
        $SESSION->has_timed_out = false;
    } else {
        $session_has_timed_out = false;
    }

    //HTTPS is potentially required in this page
    httpsrequired();

/// Check if the guest user exists.  If not, create one.
    if (! record_exists("user", "username", "guest")) {
        $guest->auth        = "manual"; 
        $guest->username    = "guest"; 
        $guest->password    = md5("guest");
        $guest->firstname   = addslashes(get_string("guestuser"));
        $guest->lastname    = " ";
        $guest->email       = "root@localhost";
        $guest->description = addslashes(get_string("guestuserinfo"));
        $guest->confirmed   = 1;
        $guest->lang        = $CFG->lang;
        $guest->timemodified= time();

        if (! $guest->id = insert_record("user", $guest)) {
            notify("Could not create guest user record !!!");
        }
    }

/// Load alternative login screens if necessary

    if ($CFG->auth == 'cas' && !empty($CFG->cas_enabled)) {
        require($CFG->dirroot.'/auth/cas/login.php');
    }

    if ($CFG->auth == 'shibboleth') {
        if (!empty($SESSION->shibboleth_checked) ) {  // Just come from there
            unset($SESSION->shibboleth_checked);
        } else if (empty($_POST)) {                   // No incoming data, so redirect
            redirect($CFG->wwwroot.'/auth/shibboleth/index.php');
        }
    }
    

    
/// Define variables used in page
    if (!$site = get_site()) {
        error("No site found!");
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $langmenu = popup_form ("$CFG->httpswwwroot/login/index.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    $loginsite = get_string("loginsite");

    $loginurl = (!empty($CFG->alternateloginurl)) ? $CFG->alternateloginurl : '';

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
            $frm = data_submitted($loginurl);
        }
    } else {
        $frm = data_submitted($loginurl);
    }

/// Check if the user has actually submitted login data to us

    if ($frm and (get_moodle_cookie() == '') and ($frm->username!='guest') and !$user and empty($CFG->alternateloginurl)) {    // Login without cookie

        $errormsg = get_string("cookiesnotenabled");

    } else if ($frm) {                             // Login WITH cookies

        $frm->username = trim(moodle_strtolower($frm->username));

        if (($frm->username == 'guest') and empty($CFG->guestloginbutton)) {
            $user = false;    /// Can't log in as guest if guest button is disabled
            $frm = false;
        } else if (!$user) {
            $user = authenticate_user_login($frm->username, $frm->password);
        }
        update_login_count();

        if ($user) {

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
            set_moodle_cookie($USER->username);
            set_login_session_preferences();
        
        
            //Select password change url
            if (is_internal_auth($USER->auth) || $CFG->{'auth_'.$USER->auth.'_stdchangepassword'}){
                $passwordchangeurl=$CFG->wwwroot.'/login/change_password.php';
            } elseif($CFG->changepassword) {
                $passwordchangeurl=$CFG->changepassword;
            } 
            
            // check whether the user should be changing password
            if (get_user_preferences('auth_forcepasswordchange', false) || $frm->password == 'changeme'){
                if (isset($passwordchangeurl)) {
                    redirect($passwordchangeurl);
                } else {
                    error("You cannot proceed without changing your password. 
                           However there is no available page for changing it.
                           Please contact your Moodle Administrator.");
                }
            }


            if (user_not_fully_set_up($USER)) {
                $urltogo = $CFG->wwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.SITEID;
                // We don't delete $SESSION->wantsurl yet, so we get there later

            } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0)) {
                $urltogo = $SESSION->wantsurl;    /// Because it's an address in this site
                unset($SESSION->wantsurl);

            } else {
                $urltogo = $CFG->wwwroot.'/my';      /// Go to the standard home page
                if (isadmin() || empty($CFG->mymoodleredirect) || isguest()) {
                    $urltogo = $CFG->wwwroot;       /// not needed by admins or guests or when it's turned off
                }
                unset($SESSION->wantsurl);         /// Just in case
            }

            // check if user password has expired
            // Currently supported only for ldap-authentication module
            if (isset($CFG->ldap_expiration) && $CFG->ldap_expiration == 1 ) {
                if (function_exists('auth_password_expire')){
                    $days2expire = auth_password_expire($USER->username);
                    if (intval($days2expire) > 0 && intval($days2expire) < intval($CFG->{$USER->auth.'_expiration_warning'})) {
                        print_header("$site->fullname: $loginsite", "$site->fullname", $loginsite, $focus, "", true, "<div align=\"right\">$langmenu</div>"); 
                        notice_yesno(get_string('auth_passwordwillexpire', 'auth', $days2expire), $passwordchangeurl, $urltogo); 
                        print_footer();
                        exit;
                    } elseif (intval($days2expire) < 0 ) {
                        print_header("$site->fullname: $loginsite", "$site->fullname", $loginsite, $focus, "", true, "<div align=\"right\">$langmenu</div>"); 
                        notice_yesno(get_string('auth_passwordisexpired', 'auth'), $passwordchangeurl, $urltogo);
                        print_footer();
                        exit;
                    }    
                }
            }

            reset_login_count();

            redirect($urltogo);

            exit;
    
        } else {
            $errormsg = get_string("invalidlogin");
        }
    }

    
/// We need to show a login form

/// First, let's remember where the user was trying to get to before they got here

    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = (array_key_exists('HTTP_REFERER',$_SERVER) && 
                              $_SERVER["HTTP_REFERER"] != $CFG->wwwroot && 
                              $_SERVER["HTTP_REFERER"] != $CFG->wwwroot.'/')
            ? $_SERVER["HTTP_REFERER"] : NULL;
    }

    if (!empty($loginurl)) {   // We don't want the standard forms, go elsewhere
        redirect($loginurl);
    }
    

/// Generate the login page with forms

    if (empty($errormsg)) {
        $errormsg = '';
    }
    
    if ($session_has_timed_out) {
        $errormsg = get_string('sessionerroruser', 'error');
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

    print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, 
                 '', true, '<div class="langmenu" align="right">'.$langmenu.'</div>'); 

    include("index_form.html");

    print_footer();


?>
