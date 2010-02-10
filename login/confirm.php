<?php

require_once("../config.php");

$data = optional_param('data', '', PARAM_CLEAN);  // Formatted as:  secret/username

$p = optional_param('p', '', PARAM_ALPHANUM);     // Old parameter:  secret
$s = optional_param('s', '', PARAM_CLEAN);        // Old parameter:  username

$PAGE->set_url('/login/confirm.php');

if (empty($CFG->registerauth)) {
    print_error('cannotusepage2');
}
$authplugin = get_auth_plugin($CFG->registerauth);

if (!$authplugin->can_confirm()) {
    print_error('cannotusepage2');
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
        $PAGE->set_title(get_string("alreadyconfirmed"));
        $PAGE->set_heading(get_string("alreadyconfirmed"));
        echo $OUTPUT->header();
        echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
        echo "<h3>".get_string("thanks").", ". fullname($user) . "</h3>\n";
        echo "<p>".get_string("alreadyconfirmed")."</p>\n";
        echo $OUTPUT->single_button("$CFG->wwwroot/course/", get_string('courses'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;

    } else if ($confirmed == AUTH_CONFIRM_OK) {

        // The user has confirmed successfully, let's log them in

        if (!$user = get_complete_user_data('username', $username)) {
            print_error('cannotfinduser', '', '', $username);
        }

        complete_user_login($user);

        if ( ! empty($SESSION->wantsurl) ) {   // Send them where they were going
            $goto = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
            redirect($goto);
        }

        $PAGE->set_title(get_string("confirmed"));
        $PAGE->set_heading(get_string("confirmed"));
        echo $OUTPUT->header();
        echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
        echo "<h3>".get_string("thanks").", ". fullname($USER) . "</h3>\n";
        echo "<p>".get_string("confirmed")."</p>\n";
        echo $OUTPUT->single_button("$CFG->wwwroot/course/", get_string('courses'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    } else {
        print_error('invalidconfirmdata');
    }
} else {
    print_error("errorwhenconfirming");
}

redirect("$CFG->wwwroot/");
