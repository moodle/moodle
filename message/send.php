<?php // $Id$

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

    $PAGE->requires->js('message/message.js');

/// (Don't use print_header, for more speed)
/// ehm - we have to use print_header() or else this breaks after any minor change in print_header()!
    print_header();

/// Script parameters
    $userid   = required_param('id', PARAM_INT);
    $message  = optional_param('message', '', PARAM_CLEANHTML);
    $format   = optional_param('format', FORMAT_MOODLE, PARAM_INT);

/// Check the user we are talking to is valid
    if (! $user = $DB->get_record('user', array('id'=>$userid))) {
        print_error('invaliduserid');
    }

/// Check that the user is not blocking us!!
    if ($contact = $DB->get_record('message_contacts', array('userid'=>$user->id, 'contactid'=>$USER->id))) {
        if ($contact->blocked and !has_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM))) {
            print_heading(get_string('userisblockingyou', 'message'));
            print_footer('empty');
            exit;
        }
    }
    $userpreferences = get_user_preferences(NULL, NULL, $user->id);

    if (!empty($userpreferences['message_blocknoncontacts'])) {  // User is blocking non-contacts
        if (empty($contact)) {   // We are not a contact!
            print_heading(get_string('userisblockingyounoncontact', 'message'));
            print_footer('empty');
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
        $message = addslashes_js($message);  // So Javascript can write it

    /// Then write it to our own message screen immediately
        $PAGE->requires->js_function_call('parent.messages.document.write', Array($message));
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
        notify(get_string('mailsent', 'message'), 'notifysuccess');
    }
    echo '<div class="noframesjslink"><a target="_parent" href="discussion.php?id='.$userid.'&amp;noframesjs=1">'.get_string('noframesjs', 'message').'</a></div>';

    $PAGE->requires->js_function_call('set_focus', Array('edit-message'));

    print_footer('empty');
}
?>
