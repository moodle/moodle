<?php // $Id$
      
    require('../config.php');
    require('lib.php');

    require_login();

    if (isguest()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        print_error('disabled', 'message');
    }

/// Script parameters
    $userid = required_param('id', PARAM_INT);

    $addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
    $removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
    $blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
    $unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact

/// Check the user we are talking to is valid
    if (! $user = $DB->get_record('user', array('id'=>$userid))) {
        print_error('invaliduserid');
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

    //$PAGE->set_title('Message History');
    $PAGE->set_generaltype('popup');
    echo $OUTPUT->header();
    echo '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
    echo '<td width="100">';
    $userpic = moodle_user_picture::make($user, SITEID);
    $userpic->size = 48;
    $userpic->link = true;
    echo $OUTPUT->user_picture($userpic) .'</td>';
    echo '<td valign="middle" align="center">';

    echo '<div class="name">'.fullname($user).'</div>';

    echo '<div class="commands">';
    if ($contact = $DB->get_record('message_contacts', array('userid'=>$USER->id, 'contactid'=>$user->id))) {
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

    echo $OUTPUT->footer();

?>
