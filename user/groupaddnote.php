<?php  // $Id$
require_once("../config.php");
require_once($CFG->dirroot .'/notes/lib.php');

$id    = required_param('id', PARAM_INT);              // course id
$users = optional_param('userid', array(), PARAM_INT); // array of user id
$content = optional_param('content', '', PARAM_RAW); // note content
$state = optional_param('state', '', PARAM_ALPHA); // note publish state

if (! $course = get_record('course', 'id', $id)) {
    error("Course ID is incorrect");
}

$context = get_context_instance(CONTEXT_COURSE, $id);
require_login($course->id);

// to create notes the current user needs a capability
require_capability('moodle/notes:manage', $context);

if (empty($CFG->enablenotes)) {
    print_error('notesdisabled', 'notes');
}

if (!empty($users) && !empty($content) && confirm_sesskey()) {
    $note = new object();
    $note->courseid = $id;
    $note->format = FORMAT_PLAIN;
    $note->content = $content;
    $note->publishstate = $state;
    foreach ($users as $k => $v) {
        if(!$user = get_record('user', 'id', $v)) {
            continue;
        }
        $note->id = 0;
        $note->userid = $v;
        if (note_save($note)) {
            add_to_log($note->courseid, 'notes', 'add', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id , 'add note');
        }
    }

    redirect("$CFG->wwwroot/user/index.php?id=$id");
}

/// Print headers

$straddnote = get_string('groupaddnewnote', 'notes');

$navlinks = array();
$navlinks[] = array('name' => $straddnote, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$course->shortname: ".get_string('extendenrol'), $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));

// this will contain all available the based On select options, but we'll disable some on them on a per user basis

print_heading($straddnote);
echo '<form method="post" action="groupaddnote.php" >';
echo '<div style="width:100%;text-align:center;">';
echo '<input type="hidden" name="id" value="'.$course->id.'" />';
echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
$state_names = note_get_state_names();

// the first time list hack
if (empty($users)) {
    foreach ($_POST as $k => $v) {
        if (preg_match('/^user(\d+)$/',$k,$m)) {
            $users[] = $m[1];
        }
    }
}

$strpublishstate = get_string('publishstate', 'notes');

$userlist = array();
foreach ($users as $k => $v) {
    if(!$user = get_record('user', 'id', $v)) {
        continue;
    }
    echo '<input type="hidden" name="userid['.$k.']" value="'.$v.'" />';
    $userlist[] = fullname($user, true);
}
echo '<p>';
echo get_string('users'). ': ' . implode(', ', $userlist) . '.';
echo '</p>';

echo '<p>' . get_string('content', 'notes');
helpbutton('writing', get_string('helpwriting'));
echo '<br /><textarea name="content" rows="5" cols="50">' . strip_tags(@$content) . '</textarea></p>';

echo '<p>' . $strpublishstate;
helpbutton('status', $strpublishstate, 'notes');
choose_from_menu($state_names, 'state', empty($state) ? NOTES_STATE_PUBLIC : $state, '');
echo '</p>';

echo '<input type="submit" value="' . get_string('savechanges'). '" /></div></form>';
print_footer($course);
