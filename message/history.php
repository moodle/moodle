<?php // $Id$
      // For listing message histories between any two users

    require('../config.php');
    require('lib.php');

    require_login();

    if (isguestuser()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        error("Messaging is disabled on this site");
    }

    // We expect 2 users and by default user2 is the current user
    $userid1 = required_param('user1', PARAM_INT);
    $userid2 = optional_param('user2', $USER->id, PARAM_INT);
    $search = optional_param('search', '', PARAM_CLEAN);

    // Check if user1 is the current user, this should not occur but just incase
    if ($userid1 == $USER->id) {
        // user1 is the current user :(
        $user1 = $USER;
    } else if ($userid2 == $USER->id) {
        // user2 is the current user
        $user2 = $USER;
    } else {
        // Neither user is the current user, so check the user has the readallmessages
        // capability
        require_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM));
    }
    // Load user1 if it wasn't set above (only set if user1 is the current user)
    if (!isset($user1) && !($user1 = get_record("user", "id", $userid1))) {
        error("User ID 1 was incorrect");
    }
    // Load user2 if it wasn't set above (only set if user2 is the current user)
    if (!isset($user2) && !($user2 = get_record("user", "id", $userid2))) {
        error("User ID 2 was incorrect");
    }

    // If either user has been deleted then print a page that details that information
    // we can't use print_error because this is in a popup and the continue button
    // would cause mayhem
    if ($user1->deleted || $user2->deleted) {
        print_header();
        print_heading(get_string('userdeleted', 'moodle'));
        print_footer();
    }

    add_to_log(SITEID, 'message', 'history', 'history.php?user1='.$userid1.'&amp;user2='.$userid2, $userid1);

/// Our two users are defined - let's set up the page

    print_header(get_string('messagehistory', 'message'), '', '', '', '<base target="_blank" />');

/// Print out a heading including the users we are looking at

    print_simple_box_start('center');
    echo '<table align="center" cellpadding="10"><tr>';
    echo '<td align="center">';
    echo print_user_picture($user1, SITEID, $user1->picture, 100, true, true, 'userwindow').'<br />';
    echo fullname($user1);
    echo '</td>';
    echo '<td align="center">';
    echo '<img src="'.$CFG->wwwroot.'/pix/t/left.gif" alt="'.get_string('from').'" />';
    echo '<img src="'.$CFG->wwwroot.'/pix/t/right.gif" alt="'.get_string('to').'" />';
    echo '</td>';
    echo '<td align="center">';
    echo print_user_picture($user2, SITEID, $user2->picture, 100, true, true, 'userwindow').'<br />';
    echo fullname($user2);
    echo '</td>';
    echo '</tr></table>';
    print_simple_box_end();


/// Get all the messages and print them

    if ($messages = message_get_history($user1, $user2)) {
        $current->mday = '';
        $current->month = '';
        $current->year = '';
        $messagedate = get_string('strftimetime');
        $blockdate   = get_string('strftimedaydate');
        foreach ($messages as $message) {
            $date = usergetdate($message->timecreated);
            if ($current->mday != $date['mday'] | $current->month != $date['month'] | $current->year != $date['year']) {
                $current->mday = $date['mday'];
                $current->month = $date['month'];
                $current->year = $date['year'];
                echo '<a name="'.$date['year'].$date['mon'].$date['mday'].'"></a>';
                print_heading(userdate($message->timecreated, $blockdate), 'center', 4);
            }
            if ($message->useridfrom == $user1->id) {
                echo message_format_message($message, $user1, $messagedate, $search, 'other');
            } else {
                echo message_format_message($message, $user2, $messagedate, $search, 'me');
            }
        }
    } else {
        print_heading(get_string('nomessagesfound', 'message'));
    }

    print_footer('none');

?>
