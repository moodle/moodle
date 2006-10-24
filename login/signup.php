<?php // $Id$

    require_once("../config.php");
    require_once("../auth/$CFG->auth/lib.php");

    //HTTPS is potentially required in this page
    httpsrequired();
    include("signup_form.php");
    $mform_signup = new login_signup_form('signup.php','');

    if ($CFG->auth != 'email' and (empty($CFG->auth_user_create) or !(function_exists('auth_user_create'))) ) {
        error("Sorry, you may not use this page.");
    }

    if ($fromform = $mform_signup->data_submitted()) {

        $plainpass = $fromform->password;
        $fromform->password = hash_internal_user_password($plainpass);
        $fromform->confirmed = 0;
        $fromform->lang = current_language();
        $fromform->firstaccess = time();
        $fromform->secret = random_string(15);
        $fromform->auth = $CFG->auth;
        if (!empty($CFG->auth_user_create) and function_exists('auth_user_create') ){
            if (! auth_user_exists($fromform->username)) {
                if (! auth_user_create($fromform,$plainpass)) {
                    error("Could not add user to authentication module!");
                }
            } else {
                error("User already exists on authentication database.");
            }
        }

        if (! ($fromform->id = insert_record("user", $fromform)) ) {
            error("Could not add your record to the database!");
        }

        if (! send_confirmation_email($fromform)) {
            error("Tried to send you an email but failed!");
        }

        $emailconfirm = get_string("emailconfirm");
        print_header($emailconfirm, $emailconfirm, $emailconfirm);
        notice(get_string("emailconfirmsent", "", $fromform->email), "$CFG->wwwroot/index.php");
        exit;
    }




    $newaccount = get_string("newaccount");
    $login = get_string("login");

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $langmenu = popup_form ("$CFG->wwwroot/login/signup.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }
    print_header($newaccount, $newaccount, "<a href=\"index.php\">$login</a> -> $newaccount", $mform_signup->focus(), "", true, "<div align=\"right\">$langmenu</div>");

    $mform_signup->display();
    print_footer();




?>