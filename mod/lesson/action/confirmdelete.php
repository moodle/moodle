<?php
/**
 * Action for confirming the deletion of a page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    confirm_sesskey();

    $pageid = required_param('pageid', PARAM_INT);
    if (!$thispage = $DB->get_record("lesson_pages", array ("id" => $pageid))) {
        print_error('cannotfindpages', 'lesson');
    }
    echo $OUTPUT->heading(get_string("deletingpage", "lesson", format_string($thispage->title)));
    // print the jumps to this page
    $params = array("lessonid" => $lesson->id, "pageid" => $pageid);
    if ($answers = $DB->get_records_select("lesson_answers", "lessonid = :lessonid AND jumpto = :pageid + 1", $params)) {
        echo $OUTPUT->heading(get_string("thefollowingpagesjumptothispage", "lesson"));
        echo "<p align=\"center\">\n";
        foreach ($answers as $answer) {
            if (!$title = $DB->get_field("lesson_pages", "title", array("id" => $answer->pageid))) {
                print_error('cannotfindpagetitle', 'lesson');
            }
            echo $title."<br />\n";
        }
    }
    echo $OUTPUT->confirm(get_string("confirmdeletionofthispage","lesson"),
         "lesson.php?action=delete&id=$cm->id&pageid=$pageid",
         "view.php?id=$cm->id");

