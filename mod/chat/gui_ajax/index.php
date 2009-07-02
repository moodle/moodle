<?php
require_once('../../../config.php');
require_once('../lib.php');
require_once('common.php');
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

// language string
$str_chat     = get_string('modulename', 'chat'); // must be before current_language() in chat_login_user() to force course language!!!
$str_send    = get_string('send', 'chat');
$str_sending = get_string('sending', 'chat');
$str_title   = format_string($course->shortname) . ": ".format_string($chat->name,true).$groupname;
$str_inputarea = get_string('inputarea', 'chat');
$str_userlist  = get_string('userlist',  'chat');

$PAGE->set_generaltype('popup');
$PAGE->set_title('Chat');

$PAGE->requires->yui_lib('dom');
$PAGE->requires->yui_lib('element');
$PAGE->requires->yui_lib('dragdrop');
$PAGE->requires->yui_lib('resize');
$PAGE->requires->yui_lib('layout');
$PAGE->requires->yui_lib('container');
$PAGE->requires->yui_lib('connection');
$PAGE->requires->yui_lib('json');
$PAGE->requires->yui_lib('button');
$PAGE->requires->yui_lib('selector');
$PAGE->requires->data_for_js('chat_cfg', array('home'=>$CFG->httpswwwroot.'/mod/chat/view.php?id='.$cm->id, 'userid'=>$USER->id, 'sid'=>$chat_sid,'timer'=>5000, 'chat_lasttime'=>0,'chat_lastrow'=>null,'header_title'=>$str_chat,'chatroom_name'=>$str_title));
$PAGE->requires->data_for_js('chat_lang', array('send'=>$str_send, 'sending'=>$str_sending, 'inputarea'=>$str_inputarea, 'userlist'=>$str_userlist));
$PAGE->requires->js('mod/chat/gui_ajax/script.js');
$PAGE->add_body_class('yui-skin-sam');

$PAGE->requires->css('lib/yui/reset-fonts-grids/reset-fonts-grids.css');
$PAGE->requires->css('lib/yui/resize/assets/skins/sam/resize.css');
$PAGE->requires->css('lib/yui/layout/assets/skins/sam/layout.css');
$PAGE->requires->css('lib/yui/button/assets/skins/sam/button.css');

echo $OUTPUT->header();
echo "<style type='text/css'> #listing a{text-decoration:none;color:gray} #listing a:hover {text-decoration:underline;color:white;background:blue} #listing{padding: .5em}</style>";
echo $OUTPUT->heading($str_title,1);
echo <<<DIVS
<div id="chat_header">
</div>
<div id="chat_input">
    <input type="text" id="input_msgbox" value="" size="48" />
    <input type="button" id="btn_send" value="$str_send" />
</div>
<div id="chat_user_list">
<ul id="listing">
</ul>
</div>
<div id="chat_options">
</div>
<div id="chat_panel">
<ul id="msg_list">
<ul>
</div>
<div id="notify">
</div>
DIVS;
echo $OUTPUT->footer();
?>
