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

define('MESSAGE_DEFAULT_REFRESH', 5);

require_login();

if (has_capability('moodle/legacy:guest', get_context_instance(CONTEXT_SYSTEM), 0, false)) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

$PAGE->set_pagelayout('popup');
$PAGE->set_title(get_string('messages', 'message').' - '.format_string($SITE->fullname));
$PAGE->set_url('/message/refresh.php');
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

$PAGE->requires->js_init_call('M.core_message.init_refresh_parent_frame', array($jsmessages, $jsmessages));

echo $OUTPUT->header();
if (!empty($playbeep)) {
    echo '<embed src="bell.wav" autostart="true" hidden="true" name="bell" />';
    $PAGE->requires->js_function_call('parent.send.focus');
}

// Javascript for Mozilla to cope with the redirect bug from editor being on in this page
$PAGE->requires->js_init_call('M.core_message.init_refresh_page', array(($wait*1000), "refresh.php?id=$userid&name=".urlencode($userfullname)."&wait=$wait"));

echo $OUTPUT->footer();

