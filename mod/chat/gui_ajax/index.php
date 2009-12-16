<?php
require_once('../../../config.php');
require_once('../lib.php');

$id      = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT); //only for teachers
$theme   = optional_param('theme', 'compact', PARAM_ALPHANUM);

$url = new moodle_url($CFG->wwwroot.'/mod/chat/gui_ajax/index.php', array('id'=>$id));
if ($groupid !== 0) {
    $url->param('groupid', $groupid);
}
$PAGE->set_url($url);
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

if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $cm->id))) {
    echo $OUTPUT->header();
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
$str_themes = get_string('themes');

$PAGE->set_pagelayout('popup');
$PAGE->set_title('Chat');
$PAGE->requires->yui_lib('dragdrop');
$PAGE->requires->yui_lib('resize');
$PAGE->requires->yui_lib('layout');
$PAGE->requires->yui_lib('container');
$PAGE->requires->yui_lib('connection');
$PAGE->requires->yui_lib('json');
$PAGE->requires->yui_lib('animation');
$PAGE->requires->yui_lib('menu');

if (!file_exists(dirname(__FILE__) . '/theme/'.$theme.'/chat.css')) {
    $theme = 'bubble';
}
$PAGE->requires->data_for_js('chat_cfg', array(
    'home'=>$CFG->httpswwwroot.'/mod/chat/view.php?id='.$cm->id,
    'chaturl'=>$CFG->httpswwwroot.'/mod/chat/gui_ajax/index.php?id='.$id,
    'theme'=>$theme,
    'userid'=>$USER->id,
    'sid'=>$chat_sid,
    'timer'=>3000,
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

$PAGE->add_body_class('yui-skin-sam');
$PAGE->set_pagelayout('embedded');

echo $OUTPUT->header();
$intro = format_text($chat->intro, $chat->introformat);
$home_url = $CFG->httpswwwroot.'/mod/chat/gui_ajax/index.php?id='.$id;

echo <<<DIVS
<!--
<div id="chat-header">
{$chat->name} <p>{$intro}</p>
</div>
-->
<div id="chat-userlist">
    <ul id="users-list">
    </ul>
</div>
<div id="chat-options"></div>
<div id="chat-messages">
    <ul id="messages-list"><ul>
</div>
<div id="chat-input-area">
<table width="100%">
<tr>
    <td>
         &raquo;
        <input type="text" disabled="true" id="input-message" value="Loading..." size="50" />
        <input type="button" id="button-send" value="$str_send" />
    </td>
    <td align="right">
        <a id="choosetheme" href="###">{$str_themes} ▶</a>
    </td>
</tr>
</table>
</div>
<div id="chat-notify"></div>
DIVS;
echo $OUTPUT->footer();

