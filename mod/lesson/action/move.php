<?php // $Id$
/**
 * Action that displays an interface for moving a page
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
   
    $pageid = required_param('pageid', PARAM_INT);
    $title = get_field("lesson_pages", "title", "id", $pageid);
    print_heading(get_string("moving", "lesson", format_string($title)));
   
    if (!$page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
        error("Move: first page not found");
    }

    echo "<center><table cellpadding=\"5\" border=\"1\">\n";
    echo "<tr><td><a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=moveit&amp;pageid=$pageid&amp;after=0\"><small>".
        get_string("movepagehere", "lesson")."</small></a></td></tr>\n";
    while (true) {
        if ($page->id != $pageid) {
            if (!$title = trim(format_string($page->title))) {
                $title = "<< ".get_string("notitle", "lesson")."  >>";
            }
            echo "<tr><td><b>$title</b></td></tr>\n";
            echo "<tr><td><a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=moveit&amp;pageid=$pageid&amp;after={$page->id}\"><small>".
                get_string("movepagehere", "lesson")."</small></a></td></tr>\n";
        }
        if ($page->nextpageid) {
            if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                error("Teacher view: Next page not found!");
            }
        } else {
            // last page reached
            break;
        }
    }
    echo "</table>\n";
?>
