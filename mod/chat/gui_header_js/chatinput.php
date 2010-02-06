<?php

define('NO_MOODLE_COOKIES', true); // session not used here

require('../../../config.php');
require('../lib.php');

$chat_sid = required_param('chat_sid', PARAM_ALPHANUM);
$chatid   = required_param('chat_id', PARAM_INT);

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    print_error('notlogged', 'chat');
}
if (!$chat = $DB->get_record('chat', array('id'=>$chatid))) {
    print_error('invalidid', 'chat');
}

if (!$course = $DB->get_record('course', array('id'=>$chat->course))) {
    print_error('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    print_error('invalidcoursemodule');
}

$PAGE->set_url('/mod/chat/gui_header_js/chatinput.php', array('chat_sid'=>$chat_sid, 'chat_id'=>$chatid));

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

//Get the user theme
$USER = $DB->get_record('user', array('id'=>$chatuser->userid));

//Setup course, lang and theme
$PAGE->set_course($course);
$PAGE->requires->js('/mod/chat/gui_header_js/chat_gui_header.js', true);
$PAGE->set_pagelayout('embedded');
$PAGE->set_focuscontrol('input_chat_message');
$PAGE->set_cacheable(false);
echo $OUTPUT->header();

?>
    <form action="../empty.php" method="post" target="empty" id="inputForm"
          onsubmit="return empty_field_and_submit()" style="margin:0">
        <input type="text" id="input_chat_message" name="chat_message" size="50" value="" />
        <?php echo $OUTPUT->help_icon('chatting', get_string('helpchatting', 'chat'), 'chat', true); ?><br />
        <input type="checkbox" id="auto" size="50" value="" checked="checked" /><label for="auto"><?php echo get_string('autoscroll', 'chat');?></label>
    </form>

    <form action="insert.php" method="post" target="empty" id="sendForm">
        <input type="hidden" name="chat_sid" value="<?php echo $chat_sid ?>" />
        <input type="hidden" name="chat_message" />
    </form>
<?php
    echo $OUTPUT->footer();
?>
