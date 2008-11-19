<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
require_once('../../../config.php');
require_once('../lib.php');
require_once('common.php');
$time_start = microtime_float();
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

if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $cm->id))) {
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

$strchat = get_string('modulename', 'chat'); // must be before current_language() in chat_login_user() to force course language!!!
$str_send    = get_string('send', 'chat');
$str_sending = get_string('sending', 'chat');
$str_title   = format_string($course->shortname) . ": ".format_string($chat->name,true).$groupname;
if (!$chat_sid = chat_login_user($chat->id, 'ajax', $groupid, $course)) {
    print_error('cantlogin', 'chat');
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->httpswwwroot;?>/lib/yui/reset-fonts-grids/reset-fonts-grids.css" />

<link rel="stylesheet" type="text/css" href="<?php echo $CFG->httpswwwroot;?>/lib/yui/resize/assets/skins/sam/resize.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->httpswwwroot;?>/lib/yui/layout/assets/skins/sam/layout.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->httpswwwroot;?>/lib/yui/button/assets/skins/sam/button.css" />
<?php
print_js_config(array('userid'=>$USER->id, 'sid'=>$chat_sid,'timer'=>5000, 'chat_lasttime'=>0,'chat_lastrow'=>null,'header_title'=>$strchat,'chatroom_name'=>$str_title), 'chat_cfg');
print_js_config(array('send'=>$str_send, 'sending'=>$str_sending), 'chat_lang');
?>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/element/element-beta-min.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/resize/resize-min.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/layout/layout-min.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/container/container.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/connection/connection-min.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/json/json-min.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/button/button-min.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot;?>/lib/yui/selector/selector-beta-min.js"></script>
<script type="text/javascript" src="script.js"></script>
</head>
<body class=" yui-skin-sam">
<div id="chat_header">
<h1><?php echo $str_title;?></h1>
</div>
<div id="chat_input">
    <input type="text" id="input_msgbox" value="" size="48" />
    <input type="button" id="btn_send" value="<?php echo $str_send;?>" />
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
</body>
</html>
