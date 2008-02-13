<?php // $Id$
      
    require('../config.php');
    require('lib.php');

    require_login();

    if (isguest()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        error("Messaging is disabled on this site");
    }

/// Script parameters
    $userid = required_param('id', PARAM_INT);

    $addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
    $removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
    $blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
    $unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact

/// Check the user we are talking to is valid
    if (! $user = get_record('user', 'id', $userid)) {
        error("User ID was incorrect");
    }

/// Possibly change some contacts if requested

    if ($addcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'add contact', 
                   'history.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
        message_add_contact($addcontact);
    }
    if ($removecontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'remove contact', 
                   'history.php?user1='.$removecontact.'&amp;user2='.$USER->id, $removecontact);
        message_remove_contact($removecontact);
    }
    if ($blockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'block contact', 
                   'history.php?user1='.$blockcontact.'&amp;user2='.$USER->id, $blockcontact);
        message_block_contact($blockcontact);
    }
    if ($unblockcontact and confirm_sesskey()) {
        add_to_log(SITEID, 'message', 'unblock contact', 
                   'history.php?user1='.$unblockcontact.'&amp;user2='.$USER->id, $unblockcontact);
        message_unblock_contact($unblockcontact);
    }

    print_header('','','','','',false,'','',false,'');
    echo '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
    echo '<td width="100">';
    echo print_user_picture($user, SITEID, $user->picture, true, true, true, 'userwindow').'</td>';
    echo '<td valign="middle" align="center">';

    echo '<div class="name">'.fullname($user).'</div>';

    //echo '<br /><font size="1">';     /// Print login status of this user
    //if ($user->lastaccess) {
    //    if (time() - $user->lastaccess > $CFG->message_offline_time) {
    //        echo get_string('offline', 'message').': '.format_time(time() - $user->lastaccess);
    //    } else {
    //        echo get_string('lastaccess').': '.get_string('ago', 'message', format_time(time() - $user->lastaccess));
    //    }
    //} else {
    //    echo get_string("lastaccess").":". get_string("never");
    //}
    //echo '</font>';

    echo '<div class="commands">';
    if ($contact = get_record('message_contacts', 'userid', $USER->id, 'contactid', $user->id)) {
         if ($contact->blocked) {
             message_contact_link($user->id, 'add', false, 'user.php?id='.$user->id, true); 
             message_contact_link($user->id, 'unblock', false, 'user.php?id='.$user->id, true); 
         } else {
             message_contact_link($user->id, 'remove', false, 'user.php?id='.$user->id, true); 
             message_contact_link($user->id, 'block', false, 'user.php?id='.$user->id, true); 
         }
    } else {
         message_contact_link($user->id, 'add', false, 'user.php?id='.$user->id, true);
         message_contact_link($user->id, 'block', false, 'user.php?id='.$user->id, true);
    }
    message_history_link($user->id, 0, false, '', '', 'both');
    echo '</div>';

    echo '</td></tr></table>';

    print_footer('empty');

?>
