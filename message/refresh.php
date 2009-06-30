<?php // $Id$

    require('../config.php');
    require('lib.php');

    define('MESSAGE_DEFAULT_REFRESH', 5);

    require_login();

    if (isguest()) {
        redirect($CFG->wwwroot);
    }

    if (empty($CFG->messaging)) {
        print_error('disabled', 'message');
    }

    $PAGE->set_generaltype('popup');
    $PAGE->set_title(get_string('messages', 'message').' - '.format_string($SITE->fullname));
    header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: text/html; charset=utf-8');

    /// Script parameters
    $userid       = required_param('id', PARAM_INT);
    $userfullname = strip_tags(required_param('name', PARAM_RAW));
    $wait         = optional_param('wait', MESSAGE_DEFAULT_REFRESH, PARAM_INT);

    /*Get still to be read message, use message/lib.php funtion*/
    $messages = message_get_popup_messages($USER->id, $userid);
    $jsmessages = Array();
    if ($messages ) {
        foreach ($messages as $message) {
            $time = userdate($message->timecreated, get_string('strftimedatetimeshort'));

            $options = new object();
            $options->para = false;
            $options->newlines = true;
            $printmessage = format_text($message->fullmessage, $message->fullmessageformat, $options, 0);
            $printmessage = '<div class="message other"><span class="author">'.s($userfullname).'</span> '.
                '<span class="time">['.$time.']</span>: '.
                '<span class="content">'.$printmessage.'</span></div>';
            $jsmessages[count($jsmessages)] = $printmessage;
        }
        if (get_user_preferences('message_beepnewmessage', 0)) {
            $playbeep = true;
        }
        $wait = MESSAGE_DEFAULT_REFRESH;
    } else {
        if ($wait < 300) {                     // Until the wait is five minutes
            $wait = ceil(1.2 * (float)$wait);  // Exponential growth
        }
    }

    $PAGE->requires->js('message/message.js')->in_head();
    $PAGE->requires->js_function_call('refresh_parent_messages_frame');
    $PAGE->requires->data_for_js('chatmessages', Array('msgcount'=>count($jsmessages), 'msg'=>$jsmessages))->in_head();

    echo $OUTPUT->header();
    if (!empty($playbeep)) {
        echo '<embed src="bell.wav" autostart="true" hidden="true" name="bell" />';
        echo $PAGE->requires->js_function_call('parent.send.focus')->asap();
    }

    // Javascript for Mozilla to cope with the redirect bug from editor being on in this page
    $PAGE->requires->js_function_call('refresh_page', Array(($wait*1000), "refresh.php?id=$userid&name=".urlencode($userfullname)."&wait=$wait"));

    echo $OUTPUT->footer();

?>
