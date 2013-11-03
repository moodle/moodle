<?php

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);   // course

$PAGE->set_url('/mod/chat/index.php', array('id'=>$id));

if (! $course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');

add_to_log($course->id, 'chat', 'view all', "index.php?id=$course->id", '');


/// Get all required strings

$strchats = get_string('modulenameplural', 'chat');
$strchat  = get_string('modulename', 'chat');


/// Print the header
$PAGE->navbar->add($strchats);
$PAGE->set_title($strchats);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

/// Get all the appropriate data

if (! $chats = get_all_instances_in_course('chat', $course)) {
    notice(get_string('thereareno', 'moodle', $strchats), "../../course/view.php?id=$course->id");
    die();
}

$usesections = course_format_uses_sections($course->format);

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strname  = get_string('name');

$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname);
    $table->align = array ('center', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left');
}

$currentsection = '';
foreach ($chats as $chat) {
    if (!$chat->visible) {
        //Show dimmed if the mod is hidden
        $link = "<a class=\"dimmed\" href=\"view.php?id=$chat->coursemodule\">".format_string($chat->name,true)."</a>";
    } else {
        //Show normal if the mod is visible
        $link = "<a href=\"view.php?id=$chat->coursemodule\">".format_string($chat->name,true)."</a>";
    }
    $printsection = '';
    if ($chat->section !== $currentsection) {
        if ($chat->section) {
            $printsection = get_section_name($course, $chat->section);
        }
        if ($currentsection !== '') {
            $table->data[] = 'hr';
        }
        $currentsection = $chat->section;
    }
    if ($usesections) {
        $table->data[] = array ($printsection, $link);
    } else {
        $table->data[] = array ($link);
    }
}

echo '<br />';

echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer();


