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

require_login();

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

if (has_capability('moodle/site:sendmessage', get_context_instance(CONTEXT_SYSTEM))) {

    $PAGE->set_generaltype('popup');
    $PAGE->set_title('send');
    $PAGE->requires->js('message/message.js');

    echo $OUTPUT->header();

/// Script parameters
    $userid   = required_param('id', PARAM_INT);
    $message  = optional_param('message', '', PARAM_CLEANHTML);
    $format   = optional_param('format', FORMAT_MOODLE, PARAM_INT);

    $url = new moodle_url($CFG->wwwroot.'/message/send.php', array('id'=>$userid));
    if ($message !== 0) {
        $url->param('message', $message);
    }
    if ($format !== 0) {
        $url->param('format', $format);
    }
    $PAGE->set_url($url);

/// Check the user we are talking to is valid
    if (! $user = $DB->get_record('user', array('id'=>$userid))) {
        print_error('invaliduserid');
    }

/// Check that the user is not blocking us!!
    if ($contact = $DB->get_record('message_contacts', array('userid'=>$user->id, 'contactid'=>$USER->id))) {
        if ($contact->blocked and !has_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM))) {
            echo $OUTPUT->heading(get_string('userisblockingyou', 'message'), 1);
            echo $OUTPUT->footer();
            exit;
        }
    }
    $userpreferences = get_user_preferences(NULL, NULL, $user->id);

    if (!empty($userpreferences['message_blocknoncontacts'])) {  // User is blocking non-contacts
        if (empty($contact)) {   // We are not a contact!
            echo $OUTPUT->heading(get_string('userisblockingyounoncontact', 'message'), 1);
            echo $OUTPUT->footer();
            exit;
        }
    }

    if ($message!='' and confirm_sesskey()) {   /// Current user has just sent a message

        /// Save it to the database...
        $messageid = message_post_message($USER, $user, $message, $format, 'direct');

        /// Format the message as HTML
        $options = NULL;
        $options->para = false;
        $options->newlines = true;
        $message = format_text($message, $format, $options);

        $time = userdate(time(), get_string('strftimedatetimeshort'));
        $message = '<div class="message me"><span class="author">'.fullname($USER).'</span> '.
                   '<span class="time">['.$time.']</span>: '.
                   '<span class="content">'.$message.'</span></div>';
        //$PAGE->requires->js_function_call('parent.messages.document.write', Array($message));
        $PAGE->requires->js_function_call('add_message', Array($message));
        $PAGE->requires->js_function_call('parent.messages.scroll', Array(1,5000000));

        add_to_log(SITEID, 'message', 'write', 'history.php?user1='.$user->id.'&amp;user2='.$USER->id.'#m'.$messageid, $user->id);
    }

    echo '<form id="editing" method="post" action="send.php">';
    echo '<div class="message-form">';
    echo '<input type="hidden" name="id" value="'.$user->id.'" />';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

    $usehtmleditor = (can_use_html_editor() && get_user_preferences('message_usehtmleditor', 0));
    if ($usehtmleditor) {
        echo '<div class="message-send-box">';
        print_textarea($usehtmleditor, 5, 34, 0, 0, 'message', '', 0, false, '', 'form-textarea-simple');
        echo '</div>';
        echo '<input class="message-send-button" type="submit" value="'.get_string('sendmessage', 'message').'" />';
        echo '<input type="hidden" name="format" value="'.FORMAT_HTML.'" />';
    } else {
        print_textarea(false, 5, 34, 0, 0, 'message', '');
        echo '<input type="hidden" name="format" value="'.FORMAT_MOODLE.'" />';
        echo '<br /><input class="message-send-button" type="submit" value="'.get_string('sendmessage', 'message').'" />';
    }
    echo '</div>';
    echo '</form>';
    if (!empty($CFG->messagewasjustemailed)) {
        $OUTPUT->notifcation(get_string('mailsent', 'message'), 'notifysuccess');
    }
    echo '<div class="noframesjslink"><a target="_parent" href="discussion.php?id='.$userid.'&amp;noframesjs=1">'.get_string('noframesjs', 'message').'</a></div>';

    $PAGE->requires->js_function_call('set_focus', Array('edit-message'));

    echo $OUTPUT->footer();
}