<?php
require_once('../../../config.php');
require_once('../lib.php');
$id      = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT); //only for teachers
if (!$chat = $DB->get_record('chat', array('id'=>$id))) {
    print_error('invalidid', 'chat');
}

if (!$course = $DB->get_record('course', array('id'=>$chat->course))) {
    print_error('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    print_error('invalidcoursemodule');
}

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_login($course->id, false, $cm);
require_capability('mod/chat:chat',$context);

if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $cm->id)))
{
    print_header();
    notice(get_string("activityiscurrentlyhidden"));
}

/// Check to see if groups are being used here
 if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
    if ($groupid = groups_get_activity_group($cm)) {
        if (!$group = groups_get_group($groupid, false)) {
            print_error('invalidgroupid');
        }
        $groupname = ': '.$group->name;
    } else {
        $groupname = ': '.get_string('allparticipants');
    }
} else {
    $groupid = 0;
    $groupname = '';
}

// login chat room
if (!$chat_sid = chat_login_user($chat->id, 'ajax', $groupid, $course)) {
    print_error('cantlogin', 'chat');
}

$str_title = format_string($course->shortname) . ": ".format_string($chat->name,true).$groupname;
$str_send  = get_string('send', 'chat'); 

$PAGE->set_generaltype('popup');
$PAGE->set_title('Chat');
$PAGE->requires->yui_lib('dragdrop');
$PAGE->requires->yui_lib('resize');
$PAGE->requires->yui_lib('layout');
$PAGE->requires->yui_lib('container');
$PAGE->requires->yui_lib('connection');
$PAGE->requires->yui_lib('json');
$PAGE->requires->yui_lib('button');
$PAGE->requires->yui_lib('selector');
$PAGE->requires->data_for_js('chat_cfg', array(
    'home'=>$CFG->httpswwwroot.'/mod/chat/view.php?id='.$cm->id,
    'userid'=>$USER->id,
    'sid'=>$chat_sid,
    'timer'=>5000,
    'chat_lasttime'=>0,
    'chat_lastrow'=>null,
    'chatroom_name'=>$str_title
));

$PAGE->requires->string_for_js('send', 'chat');
$PAGE->requires->string_for_js('sending', 'chat');
$PAGE->requires->string_for_js('inputarea', 'chat');
$PAGE->requires->string_for_js('userlist', 'chat');
$PAGE->requires->string_for_js('modulename', 'chat');
$PAGE->requires->string_for_js('beep', 'chat');
$PAGE->requires->string_for_js('talk', 'chat');

$PAGE->requires->js('mod/chat/gui_ajax/script.js');
$PAGE->requires->yui_lib('animation')->in_head();
$PAGE->requires->css('mod/chat/chat.css');

$PAGE->add_body_class('yui-skin-sam');

echo $OUTPUT->header();
echo $OUTPUT->heading($str_title, 1);
$intro = format_text($chat->intro, $chat->introformat);

echo <<<DIVS
<div id="chat-header">
{$chat->name} {$intro}
</div>
<div id="chat-userlist">
    <ul id="users-list">
        <li></li>
    </ul>
</div>
<div id="chat_options">
</div>
<div id="chat-messages">
    <div>
        <ul id="messages-list">
            <li></li>
        <ul>
    </div>
</div>
<div id="chat-input">
    <input type="text" id="input_msgbox" value="" size="70" />
    <input type="button" id="btn_send" value="$str_send" />
</div>
<div id="notify">
</div>
DIVS;
echo $OUTPUT->footer();
?>
