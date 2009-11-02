<?php // $Id$
/**
 * Action for deleting a page
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    require_sesskey();

    $pageid = required_param('pageid', PARAM_INT);
    if (!$thispage = get_record("lesson_pages", "id", $pageid)) {
        error("Delete: page record not found");
    }

    // first delete all the associated records...
    delete_records("lesson_attempts", "pageid", $pageid);
    // ...now delete the answers...
    delete_records("lesson_answers", "pageid", $pageid);
    // ..and the page itself
    delete_records("lesson_pages", "id", $pageid);

    // repair the hole in the linkage
    if (!$thispage->prevpageid AND !$thispage->nextpageid) {
        //This is the only page, no repair needed
    } elseif (!$thispage->prevpageid) {
        // this is the first page...
        if (!$page = get_record("lesson_pages", "id", $thispage->nextpageid)) {
            error("Delete: next page not found");
        }
        if (!set_field("lesson_pages", "prevpageid", 0, "id", $page->id)) {
            error("Delete: unable to set prevpage link");
        }
    } elseif (!$thispage->nextpageid) {
        // this is the last page...
        if (!$page = get_record("lesson_pages", "id", $thispage->prevpageid)) {
            error("Delete: prev page not found");
        }
        if (!set_field("lesson_pages", "nextpageid", 0, "id", $page->id)) {
            error("Delete: unable to set nextpage link");
        }
    } else {
        // page is in the middle...
        if (!$prevpage = get_record("lesson_pages", "id", $thispage->prevpageid)) {
            error("Delete: prev page not found");
        }
        if (!$nextpage = get_record("lesson_pages", "id", $thispage->nextpageid)) {
            error("Delete: next page not found");
        }
        if (!set_field("lesson_pages", "nextpageid", $nextpage->id, "id", $prevpage->id)) {
            error("Delete: unable to set next link");
        }
        if (!set_field("lesson_pages", "prevpageid", $prevpage->id, "id", $nextpage->id)) {
            error("Delete: unable to set prev link");
        }
    }
    lesson_set_message(get_string('deletedpage', 'lesson').': '.format_string($thispage->title, true), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
?>
