<?php
require_once('../../../config.php');
require_once('../lib.php');

$id      = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT); //only for teachers
$theme   = optional_param('theme', 'compact', PARAM_ALPHANUM);

$url = new moodle_url('/mod/chat/gui_ajax/index.php', array('id'=>$id));
if ($groupid !== 0) {
    $url->param('groupid', $groupid);
}
$PAGE->set_url($url);

$chat = $DB->get_record('chat', array('id'=>$id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$chat->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id, false, MUST_EXIST);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_login($course->id, false, $cm);
require_capability('mod/chat:chat',$context);

if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $cm->id))) {
    notice(get_string("activityiscurrentlyhidden"));
    exit;
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

if (!file_exists(dirname(__FILE__) . '/theme/'.$theme.'/chat.css')) {
    $theme = 'bubble';
}

$module = array(
    'name'      => 'mod_chat_ajax',
    'fullpath'  => '/mod/chat/gui_ajax/module.js',
    'requires'  => array('base', 'dom', 'event', 'event-mouseenter', 'event-key', 'json-parse', 'io', 'overlay', 'yui2-resize', 'yui2-layout', 'yui2-menu')
);
$modulecfg = array(array(
    'home'=>$CFG->httpswwwroot.'/mod/chat/view.php?id='.$cm->id,
    'chaturl'=>$CFG->httpswwwroot.'/mod/chat/gui_ajax/index.php?id='.$id,
    'theme'=>$theme,
    'userid'=>$USER->id,
    'sid'=>$chat_sid,
    'timer'=>3000,
    'chat_lasttime'=>0,
    'chat_lastrow'=>null,
    'chatroom_name'=>format_string($course->shortname) . ": ".format_string($chat->name,true).$groupname
));
$PAGE->requires->js_init_call('M.mod_chat.ajax.init', $modulecfg, false, $module);
$PAGE->requires->strings_for_js(array('send','sending','inputarea','userlist','modulename','beep','talk'), 'chat');

$PAGE->set_title('Chat');
$PAGE->add_body_class('yui-skin-sam');
$PAGE->set_pagelayout('embedded');

echo $OUTPUT->header();
echo $OUTPUT->box('<ul id="users-list"></ul>', '', 'chat-userlist');
echo $OUTPUT->box('', '', 'chat-options');
echo $OUTPUT->box('<ul id="messages-list"></ul>', '', 'chat-messages');
$table = new html_table();
$table->data = array(
    array(
        ' &raquo;<input type="text" disabled="true" id="input-message" value="Loading..." size="50" /><input type="button" id="button-send" value="'.get_string('send', 'chat').'" />',
        '<a id="choosetheme" href="###">'.get_string('themes').' ▶</a>'
    )
);
echo $OUTPUT->box($OUTPUT->table($table), '', 'chat-input-area');
echo $OUTPUT->box('', '', 'chat-notify');
echo $OUTPUT->footer();

