<?php  // $Id$

    require_once('../config.php');
    
    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     * @return bool
     */
    function signup_captcha_enabled() {
        global $CFG;
        return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && get_config('auth/email', 'recaptcha');
    }
    
    require_once('signup_form.php');
    

    if (empty($CFG->registerauth)) {
        print_error("Sorry, you may not use this page.");
    }
    $authplugin = get_auth_plugin($CFG->registerauth);

    if (!$authplugin->can_signup()) {
        print_error("Sorry, you may not use this page.");
    }

    //HTTPS is potentially required in this page
    httpsrequired();

    $mform_signup = new login_signup_form();

    if ($mform_signup->is_cancelled()) {
        redirect(get_login_url());

    } else if ($user = $mform_signup->get_data()) {
        $user->confirmed   = 0;
        $user->lang        = current_language();
        $user->firstaccess = time();
        $user->mnethostid  = $CFG->mnet_localhost_id;
        $user->secret      = random_string(15);
        $user->auth        = $CFG->registerauth;

        $authplugin->user_signup($user, true); // prints notice and link to login/index.php
        exit; //never reached
    }

    $newaccount = get_string('newaccount');
    $login      = get_string('login');

    if (empty($CFG->langmenu)) {
        $langmenu = '';
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $select = html_select::make_popup_form("$CFG->wwwroot/login/signup.php", 'lang', $langs, 'chooselang', $currlang);
        $select->nothinglabel = false;
        $langmenu = $OUTPUT->select($select);
    }

    $navlinks = array();
    $navlinks[] = array('name' => $login, 'link' => "index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $newaccount, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($newaccount, $newaccount, $navigation, $mform_signup->focus(), "", true, "<div class=\"langmenu\">$langmenu</div>");
    
    $mform_signup->display();
    echo $OUTPUT->footer();


?>
