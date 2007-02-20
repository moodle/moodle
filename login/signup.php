<?php  // $Id$

    require_once('../config.php');
    require_once('signup_form.php');

    if (empty($CFG->registerauth)) {
        error("Sorry, you may not use this page.");
    }
    $authplugin = get_auth_plugin($CFG->registerauth);

    if (!method_exists($authplugin, 'user_create')) {
        error("Sorry, you may not use this page.");
    }

    //HTTPS is potentially required in this page
    httpsrequired();

    $mform_signup = new login_signup_form_1();

    if ($mform_signup->is_cancelled()) {
        redirect($CFG->httpswwwroot.'/login/index.php');
    } else if ($user = $mform_signup->get_data()) {

        $plainpass = $user->password;
        $user->password    = hash_internal_user_password($plainpass);
        $user->confirmed   = 0;
        $user->lang        = current_language();
        $user->firstaccess = time();
        $user->mnethostid  = $CFG->mnet_localhost_id;
        $user->secret      = random_string(15);
        $user->auth        = $CFG->registerauth;

        if (! $authplugin->user_exists($user->username)) {
            if (! $authplugin->user_create($user, $plainpass)) {
                error("Could not add user to authentication module!");
            }
        } else {
            error("User already exists on authentication database.");
        }

        $authplugin = get_auth_plugin($CFG->registerauth);
        $signedup = $authplugin->user_signup($user, $notify=true);
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
    print_header($newaccount, $newaccount, "<a href=\"index.php\">$login</a> -> $newaccount", $mform_signup->focus(), "", true, "<div class=\"langmenu\">$langmenu</div>");

    $mform_signup->display();
    print_footer();


?>