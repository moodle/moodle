<?php  // $Id$

    define('NO_MOODLE_COOKIES', true); // session not used here

    require('../../../config.php');
    require('../lib.php');

    $chat_sid = required_param('chat_sid', PARAM_ALPHANUM);

    if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
        print_error('notlogged', 'chat');
    }

    //Get the user theme
    $USER = $DB->get_record('user', array('id'=>$chatuser->userid));

    //Setup course, lang and theme
    $PAGE->set_course($DB->get_record('course', array('id' => $chatuser->course)));
    $PAGE->requires->js('mod/chat/gui_sockets/chat_gui_sockets.js')->in_head();
    $PAGE->requires->js_function_call('setfocus');
    // TODO: there will be two onload in body tag, does it matter?
    print_header('', '', '', 'inputform.chat_message', '', false, '&nbsp;', '', false);

?>

    <form action="../empty.php" method="get" target="empty" id="inputform"
          onsubmit="return empty_field_and_submit();">
        <input type="text" name="chat_message" size="60" value="" />
        <?php helpbutton("chatting", get_string("helpchatting", "chat"), "chat", true, false); ?>
    </form>
    
    <form action="<?php echo "http://$CFG->chat_serverhost:$CFG->chat_serverport/"; ?>" method="get" target="empty" id="sendform">
        <input type="hidden" name="win" value="message" />
        <input type="hidden" name="chat_message" value="" />
        <input type="hidden" name="chat_msgidnr" value="0" />
        <input type="hidden" name="chat_sid" value="<?php echo $chat_sid ?>" />
    </form>
<?php
    print_footer('empty');
?>
