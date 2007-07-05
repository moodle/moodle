<?php  // $Id$
require_once("../config.php");
require_once($CFG->dirroot .'/notes/lib.php');

$id    = required_param('id', PARAM_INT);              // course id
$users = optional_param('userid', array(), PARAM_INT); // array of user id
$contents = optional_param('contents', array(), PARAM_RAW); // array of user notes
$ratings = optional_param('ratings', array(), PARAM_INT); // array of notes ratings
$states = optional_param('states', array(), PARAM_ALPHA); // array of notes states
if (! $course = get_record('course', 'id', $id)) {
    error("Course ID is incorrect");
}

$context = get_context_instance(CONTEXT_COURSE, $id);
require_login($course->id);

// to create notes the current user needs a capability
require_capability('moodle/notes:manage', $context);

if (!empty($users) && confirm_sesskey()) {
    if (count($users) != count($contents) || count($users) != count($ratings) || count($users) != count($states)) {
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
        $note->rating = $ratings[$k];
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
if ($course->id != SITEID) {
    print_header("$course->shortname: ".get_string('extendenrol'), $course->fullname,
    "<a href=\"../course/view.php?id=$course->id\">$course->shortname</a> -> ".
    $straddnote, "", "", true, "&nbsp;", navmenu($course));
} else {
    print_header("$course->shortname: ".get_string('extendenrol'), $course->fullname,
    $straddnote, "", "", true, "&nbsp;", navmenu($course));
}

// this will contain all available the based On select options, but we'll disable some on them on a per user basis

print_heading($straddnote);
echo '<form method="post" action="addnote.php">';
echo '<input type="hidden" name="id" value="'.$course->id.'" />';
echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
$table->head  = array (get_string('fullname'), get_string('content', 'notes'), get_string('rating', 'notes'), get_string('publishstate', 'notes'), );
$table->align = array ('left', 'center', 'center', 'center');
$rating_names = note_get_rating_names();
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
    $checkbox = choose_from_menu($rating_names, 'ratings[' . $k . ']', empty($ratings[$k]) ? NOTES_RATING_NORMAL : $ratings[$k], '', '', '0', true);
    $checkbox2 = choose_from_menu($state_names, 'states[' . $k . ']', empty($states[$k]) ? NOTES_STATE_PUBLIC : $states[$k], '', '', '0', true);
    $table->data[] = array(
        '<input type="hidden" name="userid['.$k.']" value="'.$v.'" />'. fullname($user, true),
        '<textarea name="contents['. $k . ']" rows="2" cols="30">' . strip_tags(@$contents[$k]) . '</textarea>',
        $checkbox,
        $checkbox2,
    );
}
print_table($table);
echo '<div style="width:100%;text-align:center;"><input type="submit" value="' . get_string('savechanges'). '" /></div></form>';
print_footer($course);
