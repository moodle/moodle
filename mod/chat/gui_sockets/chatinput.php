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

define('NO_MOODLE_COOKIES', true); // Session not used here.

require('../../../config.php');
require_once('../lib.php');

$chatsid = required_param('chat_sid', PARAM_ALPHANUM);

$PAGE->set_url('/mod/chat/gui_sockets/chatinput.php', array('chat_sid' => $chatsid));
$PAGE->set_popup_notification_allowed(false);

if (!$chatuser = $DB->get_record('chat_users', array('sid' => $chatsid))) {
    print_error('notlogged', 'chat');
}

// Get the user theme.
$USER = $DB->get_record('user', array('id' => $chatuser->userid));

// Setup course, lang and theme.
$PAGE->set_pagelayout('embedded');
$PAGE->set_course($DB->get_record('course', array('id' => $chatuser->course)));
$PAGE->requires->js('/mod/chat/gui_sockets/chat_gui_sockets.js', true);
$PAGE->requires->js_function_call('setfocus');
$PAGE->set_focuscontrol('chat_message');
$PAGE->set_cacheable(false);
echo $OUTPUT->header();

?>

    <form action="../empty.php" method="get" target="empty" id="inputform"
          onsubmit="return empty_field_and_submit();">
        <label class="accesshide" for="chat_message"><?php print_string('entermessage', 'chat'); ?></label>
        <input type="text" name="chat_message" id="chat_message" size="60" value="" />
    </form>

    <form action="<?php echo "http://$CFG->chat_serverhost:$CFG->chat_serverport/"; ?>" method="get" target="empty" id="sendform">
        <input type="hidden" name="win" value="message" />
        <input type="hidden" name="chat_message" value="" />
        <input type="hidden" name="chat_msgidnr" value="0" />
        <input type="hidden" name="chat_sid" value="<?php echo $chatsid ?>" />
    </form>
<?php
echo $OUTPUT->footer();

