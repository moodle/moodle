<?php // $Id$
      // For listing message histories between any two users
      
    require('../config.php');
    require('lib.php');

    require_login();

/// Script parameters
    $userid1 = required_param('user1', PARAM_INT);
    if (! $user1 = get_record("user", "id", $userid1)) {  // Check it's correct
        error("User ID 1 was incorrect");
    }

    if (isadmin()) {             // Able to see any discussion
        $userid2 = optional_param('user2', $USER->id, PARAM_INT);
        if (! $user2 = get_record("user", "id", $userid2)) {  // Check
            error("User ID 2 was incorrect");
        }
    } else {
        $userid2 = $USER->id;    // Can only see messages involving yourself
        $user2 = $USER; 
    }

/// Our two users are defined - let's set up the page

    $THEME->body = $THEME->cellcontent;

    print_header(get_string('messagehistory', 'message'));

/// Print out a heading including the users we are looking at

    print_simple_box_start('center');
    echo '<table align="center" cellpadding="10"><tr>';
    echo '<td align="center">';
    echo print_user_picture($user1->id, SITEID, $user1->picture, 100, true, true, 'userwindow').'<br />';
    echo fullname($user1);
    echo '</td>';
    echo '<td align="center">';
    echo '<img src="'.$CFG->wwwroot.'/pix/t/left.gif">';
    echo '<img src="'.$CFG->wwwroot.'/pix/t/right.gif">';
    echo '</td>';
    echo '<td align="center">';
    echo print_user_picture($user2->id, SITEID, $user2->picture, 100, true, true, 'userwindow').'<br />';
    echo fullname($user2);
    echo '</td>';
    echo '</tr></table>';
    print_simple_box_end();


/// Get all the messages and print them

    if ($messages = message_get_history($user1, $user2)) {
        $current->day = '';
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
                print_heading(userdate($message->timecreated, $blockdate), 'center', 4);
            }
            if ($message->useridfrom == $user1->id) {
                echo message_format_message($message, $user1, $messagedate);
            } else {
                echo message_format_message($message, $user2, $messagedate);
            }
        }
    } else {
        print_heading(get_string('nomessagesfound', 'message'));
    }


?>
