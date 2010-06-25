<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @author Luis Rodrigues and Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package message
 */

require('../config.php');
require('lib.php');
require('send_form.php');

require_login();

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

if (has_capability('moodle/site:sendmessage', get_context_instance(CONTEXT_SYSTEM))) {

    $PAGE->set_pagelayout('popup');
    $PAGE->set_title('send');

/// Script parameters
    $userid   = required_param('id', PARAM_INT);

    $PAGE->set_url('/message/send.php', array('id'=>$userid));

/// Check the user we are talking to is valid
    if (! $user = $DB->get_record('user', array('id'=>$userid))) {
        print_error('invaliduserid');
    }

/// Check that the user is not blocking us!!
    if ($contact = $DB->get_record('message_contacts', array('userid'=>$user->id, 'contactid'=>$USER->id))) {
        if ($contact->blocked and !has_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM))) {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('userisblockingyou', 'message'), 1);
            echo $OUTPUT->footer();
            exit;
        }
    }
    $userpreferences = get_user_preferences(NULL, NULL, $user->id);

    if (!empty($userpreferences['message_blocknoncontacts'])) {  // User is blocking non-contacts
        if (empty($contact)) {   // We are not a contact!
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('userisblockingyounoncontact', 'message'), 1);
            echo $OUTPUT->footer();
            exit;
        }
    }

    $mform = new send_form();
    $defaultmessage = new stdClass;
    $defaultmessage->id = $userid;
    $defaultmessage->message = '';
    if (can_use_html_editor() && get_user_preferences('message_usehtmleditor', 0)) {
        $defaultmessage->messageformat = FORMAT_HTML;
    } else {
        $defaultmessage->messageformat = FORMAT_MOODLE;
    }
    $mform->set_data($defaultmessage);

    echo $OUTPUT->header();
    if ($data = $mform->get_data()) {   /// Current user has just sent a message

        if (!confirm_sesskey()) {
            print_error('invalidsesskey');
        }

        /// Save it to the database...
        $messageid = message_post_message($USER, $user, $data->message, $data->messageformat, 'direct');

        /// Format the message as HTML
        $options = new stdClass;
        $options->para = false;
        $options->newlines = true;
        $message = format_text($data->message, $data->messageformat, $options);

        $time = userdate(time(), get_string('strftimedatetimeshort'));
        $message = '<div class="message me"><span class="author">'.fullname($USER).'</span> '.
                   '<span class="time">['.$time.']</span>: '.
                   '<span class="content">'.$message.'</span></div>';
        //$PAGE->requires->js_function_call('parent.messages.document.write', Array($message));
        $PAGE->requires->js_function_call('parent.refresh.add_message', Array($message));
        $PAGE->requires->js_function_call('parent.messages.scroll', Array(1,5000000));

        add_to_log(SITEID, 'message', 'write', 'history.php?user1='.$user->id.'&amp;user2='.$USER->id.'#m'.$messageid, $user->id);
        echo $OUTPUT->notification(get_string('mailsent', 'message'), 'notifysuccess');
        $mform->reset_message();
    }

    $mform->display();
    /* TODO: frames are a nono, this has to be redesigned
    echo $OUTPUT->box_start('noframesjslink');
    $aurl = new moodle_url('/message/discussion.php', array('id'=>$userid, 'noframesjs'=>1));
    echo $OUTPUT->action_link($aurl, get_string('noframesjs', 'message'), );
    echo $OUTPUT->box_end();
    */

    $PAGE->requires->js_init_call('M.core_message.init_focus', array('id_message_editor'));

    echo $OUTPUT->footer();
}