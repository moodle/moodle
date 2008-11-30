<?php  // $Id$
require_once("../config.php");
require_once($CFG->dirroot .'/notes/lib.php');

$id    = required_param('id', PARAM_INT);              // course id
$users = optional_param('userid', array(), PARAM_INT); // array of user id
$contents = optional_param('contents', array(), PARAM_RAW); // array of user notes
$states = optional_param('states', array(), PARAM_ALPHA); // array of notes states
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

if (!empty($users) && confirm_sesskey()) {
    if (count($users) != count($contents) || count($users) != count($states)) {
        error('Parameters malformation', $CFG->wwwroot.'/user/index.php?id='.$id);
    }

    $note = new object();
    $note->courseid = $id;
    $note->format = FORMAT_PLAIN;
    foreach ($users as $k => $v) {
        if(!$user = get_record('user', 'id', $v) || empty($contents[$k])) {
            continue;
        }
        $note->id = 0;
        $note->content = $contents[$k];
        $note->publishstate = $states[$k];
        $note->userid = $v;
        if (note_save($note)) {
            add_to_log($note->courseid, 'notes', 'add', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id , 'add note');
        }
    }
    redirect("$CFG->wwwroot/user/index.php?id=$id");
}

/// Print headers

$straddnote = get_string('addnewnote', 'notes');

$navlinks = array();
$navlinks[] = array('name' => $straddnote, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$course->shortname: ".get_string('extendenrol'), $course->fullname, $navigation, "", "", true, "&nbsp;", navmenu($course));

// this will contain all available the based On select options, but we'll disable some on them on a per user basis

print_heading($straddnote);
echo '<form method="post" action="addnote.php">';
echo '<fieldset class="invisiblefieldset">';
echo '<input type="hidden" name="id" value="'.$course->id.'" />';
echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
echo '</fieldset>';
$table->head  = array (get_string('fullname'),
    get_string('content', 'notes') . helpbutton('writing', get_string('helpwriting'), 'moodle', true, false, '', true),
    get_string('publishstate', 'notes') . helpbutton('status', get_string('publishstate', 'notes'), 'notes', true, false, '', true),
    );
$table->align = array ('left', 'center', 'center');
$state_names = note_get_state_names();

// the first time list hack
if (empty($users)) {
    foreach ($_POST as $k => $v) {
        if (preg_match('/^user(\d+)$/',$k,$m)) {
            $users[] = $m[1];
        }
    }
}

foreach ($users as $k => $v) {
    if(!$user = get_record('user', 'id', $v)) {
        continue;
    }
    $checkbox = choose_from_menu($state_names, 'states[' . $k . ']', empty($states[$k]) ? NOTES_STATE_PUBLIC : $states[$k], '', '', '0', true);
    $table->data[] = array(
        '<input type="hidden" name="userid['.$k.']" value="'.$v.'" />'. fullname($user, true),
        '<textarea name="contents['. $k . ']" rows="2" cols="40">' . strip_tags(@$contents[$k]) . '</textarea>',
        $checkbox
    );
}
print_table($table);
echo '<div style="width:100%;text-align:center;"><input type="submit" value="' . get_string('savechanges'). '" /></div></form>';
print_footer($course);
?>
