<?php  // $Id$

    require_once('../config.php');
    require_once("../auth/$CFG->auth/lib.php");
    require_once('signup_form.php');

    //HTTPS is potentially required in this page
    httpsrequired();

    if ($CFG->auth != 'email' and (empty($CFG->auth_user_create) or !(function_exists('auth_user_create'))) ) {
        error("Sorry, you may not use this page.");
    }

    $mform_signup = new login_signup_form_1();

    if ($mform_signup->is_cancelled()) {
        redirect($CFG->httpswwwroot.'/login/index.php');
    } else if ($user = $mform_signup->data_submitted()) {

        $plainpass = $user->password;
        $user->password    = hash_internal_user_password($plainpass);
        $user->confirmed   = 0;
        $user->lang        = current_language();
        $user->firstaccess = time();
        $user->secret      = random_string(15);
        $user->auth        = $CFG->auth;

        if (!empty($CFG->auth_user_create) and function_exists('auth_user_create') ){
            if (! auth_user_exists($user->username)) {
                if (! auth_user_create($user, $plainpass)) {
                    error("Could not add user to authentication module!");
                }
            } else {
                error("User already exists on authentication database.");
            }
        }

        if (! ($user->id = insert_record('user', $user))) {
            error("Could not add your record to the database!");
        }

        if (! send_confirmation_email($user)) {
            error("Tried to send you an email but failed!");
        }

        $emailconfirm = get_string("emailconfirm");
        print_header($emailconfirm, $emailconfirm, $emailconfirm);
        notice(get_string("emailconfirmsent", "", $user->email), "$CFG->wwwroot/index.php");
        exit;
    }


    $newaccount = get_string('newaccount');
    $login      = get_string('login');

    if (empty($CFG->langmenu)) {
        $langmenu = '';
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $langmenu = popup_form ("$CFG->wwwroot/login/signup.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }
    print_header($newaccount, $newaccount, "<a href=\"index.php\">$login</a> -> $newaccount", $mform_signup->focus(), "", true, "<div align=\"right\">$langmenu</div>");

    $mform_signup->display();
    print_footer();


?>