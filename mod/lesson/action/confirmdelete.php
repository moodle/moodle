<?php // $Id$
/**
 * Action for confirming the deletion of a page
 *
 * @version $Id$
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
    notice_yesno(get_string("confirmdeletionofthispage","lesson"), 
         "lesson.php?action=delete&amp;id=$cm->id&amp;pageid=$pageid&amp;sesskey=".sesskey(),
         "view.php?id=$cm->id");
?>
