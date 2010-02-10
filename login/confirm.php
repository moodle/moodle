<?php // $Id$

    require_once("../config.php");

    $data = optional_param('data', '', PARAM_CLEAN);  // Formatted as:  secret/username

    $p = optional_param('p', '', PARAM_ALPHANUM);     // Old parameter:  secret
    $s = optional_param('s', '', PARAM_CLEAN);        // Old parameter:  username

    if (empty($CFG->registerauth)) {
        error("Sorry, you may not use this page.");
    }
    $authplugin = get_auth_plugin($CFG->registerauth);

    if (!$authplugin->can_confirm()) {
        error("Sorry, you may not use this page.");
    }

    if (!empty($data) || (!empty($p) && !empty($s))) {

        if (!empty($data)) {
            $dataelements = explode('/',$data, 2); // Stop after 1st slash. Rest is username. MDL-7647
            $usersecret = $dataelements[0];
            $username   = $dataelements[1];
        } else {
            $usersecret = $p;
            $username   = $s;
        }

        $confirmed = $authplugin->user_confirm($username, $usersecret);

        if ($confirmed == AUTH_CONFIRM_ALREADY) {
            $user = get_complete_user_data('username', $username);
            print_header(get_string("alreadyconfirmed"), get_string("alreadyconfirmed"), array(), "");
            print_box_start('generalbox centerpara boxwidthnormal boxaligncenter');
            echo "<h3>".get_string("thanks").", ". fullname($user) . "</h3>\n";
            echo "<p>".get_string("alreadyconfirmed")."</p>\n";
            print_single_button("$CFG->wwwroot/course/", null, get_string('courses'));
            print_box_end();
            print_footer();
            exit;

        } else if ($confirmed == AUTH_CONFIRM_OK) {

            // The user has confirmed successfully, let's log them in

            if (!$USER = get_complete_user_data('username', $username)) {
                error("Something serious is wrong with the database");
            }

            set_moodle_cookie($USER->username);

            if ( ! empty($SESSION->wantsurl) ) {   // Send them where they were going
                $goto = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
                redirect($goto);
            }

            print_header(get_string("confirmed"), get_string("confirmed"), array(), "");
            print_box_start('generalbox centerpara boxwidthnormal boxaligncenter');
            echo "<h3>".get_string("thanks").", ". fullname($USER) . "</h3>\n";
            echo "<p>".get_string("confirmed")."</p>\n";
            print_single_button("$CFG->wwwroot/course/", null, get_string('courses'));
            print_box_end();
            print_footer();
            exit;
        } else {
            error("Invalid confirmation data");
        }
    } else {
        print_error("errorwhenconfirming");
    }

    redirect("$CFG->wwwroot/");

?>
