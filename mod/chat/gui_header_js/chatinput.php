<?php  // $Id$

    define('NO_MOODLE_COOKIES', true); // session not used here

    require('../../../config.php');
    require('../lib.php');

    $chat_sid = required_param('chat_sid', PARAM_ALPHANUM);
    $chatid   = required_param('chat_id', PARAM_INT);

    if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
        print_error('notlogged', 'chat');
    }
    if (!$chat = $DB->get_record('chat', array('id'=>$chatid))) {
        error('Could not find that chat room!');
    }

    if (!$course = $DB->get_record('course', array('id'=>$chat->course))) {
        error('Could not find the course this belongs to!');
    }

    if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
        error('Course Module ID was incorrect');
    }
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    

    //Get the user theme
    $USER = $DB->get_record('user', array('id'=>$chatuser->userid));

    //Setup course, lang and theme
    $PAGE->set_course($course);
    $PAGE->requires->js('mod/chat/gui_header_js/chat_gui_header.js')->in_head();

    print_header('', '', '', 'input_chat_message', '', false);

?>
    <form action="../empty.php" method="post" target="empty" id="inputForm"
          onsubmit="return empty_field_and_submit()" style="margin:0">
        <input type="text" id="input_chat_message" name="chat_message" size="50" value="" />
        <?php helpbutton('chatting', get_string('helpchatting', 'chat'), 'chat', true, false); ?><br />
        <input type="checkbox" id="auto" size="50" value="" checked='true' /><label for="auto"><?php echo get_string('autoscroll', 'chat');?></label>
    </form>

    <form action="insert.php" method="post" target="empty" id="sendForm">
        <input type="hidden" name="chat_sid" value="<?php echo $chat_sid ?>" />
        <input type="hidden" name="chat_message" />
    </form>
<?php
    print_footer('empty');
?>
