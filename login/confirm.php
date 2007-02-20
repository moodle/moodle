<?php // $Id$

    require_once("../config.php");

    $data = optional_param('data', '', PARAM_CLEAN);  // Formatted as:  secret/username

    $p = optional_param('p', '', PARAM_ALPHANUM);     // Old parameter:  secret
    $s = optional_param('s', '', PARAM_CLEAN);        // Old parameter:  username

    if (empty($CFG->registerauth)) {
        error("Sorry, you may not use this page.");
    }
    $authplugin = get_auth_plugin($CFG->registerauth);

    if (!method_exists($authplugin, 'user_create')) {
        error("Sorry, you may not use this page.");
    }

    if (!empty($data) || (!empty($p) && !empty($s))) {    

        if (!empty($data)) {
            $dataelements = explode('/',$data);
            $usersecret = $dataelements[0];
            $username   = $dataelements[1];
        } else {
            $usersecret = $p;
            $username   = $s;
        }

        $authplugin = get_auth_plugin($CFG->registerauth);
        $confirmed = $authplugin->user_confirm($username, $usersecret);

        if ($confirmed == AUTH_CONFIRM_ALREADY) {
                $user = get_complete_user_data('username', $username);
                print_header(get_string("alreadyconfirmed"), get_string("alreadyconfirmed"), "", "");
                echo "<center><h3>".get_string("thanks").", ". fullname($user) . "</h3>\n";
                echo "<h4>".get_string("alreadyconfirmed")."</h4>\n";
                echo "<h3> -> <a href=\"$CFG->wwwroot/course/\">".get_string("courses")."</a></h3></center>\n";
                print_footer();
                exit;
        }
        if ($confirmed == AUTH_CONFIRM_OK) {
                // Activate new user if necessary
                $authplugin = get_auth_plugin($CFG->registerauth);
                if (method_exists($authplugin, 'user_activate')) {
                    if (!$authplugin->user_activate($username)) {
                        error('Could not activate this user!');
                    }
                }

                // The user has confirmed successfully, let's log them in

                if (!$USER = get_complete_user_data('username', $username)) {
                    error("Something serious is wrong with the database");
                }

                set_moodle_cookie($USER->username);

                if ( ! empty($SESSION->wantsurl) ) {   // Send them where they were going
                    $goto = $SESSION->wantsurl;
                    unset($SESSION->wantsurl);
                    redirect("$goto");
                }

                print_header(get_string("confirmed"), get_string("confirmed"), "", "");
                echo "<center><h3>".get_string("thanks").", ". fullname($USER) . "</h3>\n";
                echo "<h4>".get_string("confirmed")."</h4>\n";
                echo "<h3> -> <a href=\"$CFG->wwwroot/course/\">".get_string("courses")."</a></h3></center>\n";
                print_footer();
                exit;
        } else {
                error("Invalid confirmation data");
        }
    } else {
        error(get_string("errorwhenconfirming"));
    }

    redirect("$CFG->wwwroot/");

?>
