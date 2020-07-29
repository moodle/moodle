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

define('AJAX_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once(__DIR__ . '/lib.php');

$action       = optional_param('action', '', PARAM_ALPHANUM);
$beepid       = optional_param('beep', '', PARAM_RAW);
$chatsid      = required_param('chat_sid', PARAM_ALPHANUM);
$theme        = required_param('chat_theme', PARAM_ALPHANUMEXT);
$chatmessage  = optional_param('chat_message', '', PARAM_RAW);
$chatlasttime = optional_param('chat_lasttime', 0, PARAM_INT);
$chatlastrow  = optional_param('chat_lastrow', 1, PARAM_INT);

if (!confirm_sesskey()) {
    throw new moodle_exception('invalidsesskey', 'error');
}

if (!$chatuser = $DB->get_record('chat_users', array('sid' => $chatsid))) {
    throw new moodle_exception('notlogged', 'chat');
}
if (!$chat = $DB->get_record('chat', array('id' => $chatuser->chatid))) {
    throw new moodle_exception('invaliduserid', 'error');
}
if (!$course = $DB->get_record('course', array('id' => $chat->course))) {
    throw new moodle_exception('invalidcourseid', 'error');
}
if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    throw new moodle_exception('invalidcoursemodule', 'error');
}

if (!isloggedin()) {
    throw new moodle_exception('notlogged', 'chat');
}

// Set up $PAGE so that format_text will work properly.
$PAGE->set_cm($cm, $course, $chat);
$PAGE->set_url('/mod/chat/chat_ajax.php', array('chat_sid' => $chatsid));

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/chat:chat', $context);

ob_start();
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

switch ($action) {
    case 'init':
        $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
        $users = chat_format_userlist($users, $course);
        $response['users'] = $users;
        echo json_encode($response);
        break;

    case 'chat':
        \core\session\manager::write_close();
        chat_delete_old_users();
        $chatmessage = clean_text($chatmessage, FORMAT_MOODLE);

        if (!empty($beepid)) {
            $chatmessage = 'beep '.$beepid;
        }

        if (!empty($chatmessage)) {

            chat_send_chatmessage($chatuser, $chatmessage, 0, $cm);

            $chatuser->lastmessageping = time() - 2;
            $DB->update_record('chat_users', $chatuser);

            // Response OK message.
            echo json_encode(true);
            ob_end_flush();
        }
        break;

    case 'update':
        if ((time() - $chatlasttime) > $CFG->chat_old_ping) {
            chat_delete_old_users();
        }

        if ($latestmessage = chat_get_latest_message($chatuser->chatid, $chatuser->groupid)) {
            $chatnewlasttime = $latestmessage->timestamp;
        } else {
            $chatnewlasttime = 0;
        }

        if ($chatlasttime == 0) {
            $chatlasttime = time() - $CFG->chat_old_ping;
        }

        $messages = chat_get_latest_messages($chatuser, $chatlasttime);

        if (!empty($messages)) {
            $num = count($messages);
        } else {
            $num = 0;
        }
        $chatnewrow = ($chatlastrow + $num) % 2;
        $senduserlist = false;
        if ($messages && ($chatlasttime != $chatnewlasttime)) {
            foreach ($messages as $n => &$message) {
                $tmp = new stdClass();
                // When somebody enter room, user list will be updated.
                if (!empty($message->issystem)) {
                    $senduserlist = true;
                }
                if ($html = chat_format_message_theme($message, $chatuser, $USER, $cm->groupingid, $theme)) {
                    $message->mymessage = ($USER->id == $message->userid);
                    $message->message  = $html->html;
                    if (!empty($html->type)) {
                        $message->type = $html->type;
                    }
                } else {
                    unset($messages[$n]);
                }
            }
        }

        if ($senduserlist) {
            // Return users when system message arrives.
            $users = chat_format_userlist(chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid), $course);
            $response['users'] = $users;
        }

        $DB->set_field('chat_users', 'lastping', time(), array('id' => $chatuser->id));

        $response['lasttime'] = $chatnewlasttime;
        $response['lastrow']  = $chatnewrow;
        if ($messages) {
            $response['msgs'] = $messages;
        }

        echo json_encode($response);
        header('Content-Length: ' . ob_get_length());

        ob_end_flush();
        break;

    default:
        break;
}
