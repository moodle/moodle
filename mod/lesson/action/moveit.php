<?php // $Id$
/**
 * Action for actually moving the page (database changes)
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    require_sesskey();

    $pageid = required_param('pageid', PARAM_INT); //  page to move
    if (!$page = get_record("lesson_pages", "id", $pageid)) {
        error("Moveit: page not found");
    }
    $after = required_param('after', PARAM_INT); // target page

    // first step. determine the new first page
    // (this is done first as the current first page will be lost in the next step)
    if (!$after) {
        // the moved page is the new first page
        $newfirstpageid = $pageid;
        // reset $after so that is points to the last page 
        // (when the pages are in a ring this will in effect be the first page)
        if ($page->nextpageid) {
            if (!$after = get_field("lesson_pages", "id", "lessonid", $lesson->id, "nextpageid", 0)) {
                error("Moveit: last page id not found");
            }
        } else {
            // the page being moved is the last page, so the new last page will be
            $after = $page->prevpageid;
        }
    } elseif (!$page->prevpageid) {
        // the page to be moved was the first page, so the following page must be the new first page
        $newfirstpageid = $page->nextpageid;
    } else {
        // the current first page remains the first page
        if (!$newfirstpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Moveit: current first page id not found");
        }
    }
    // the rest is all unconditional...
    
    // second step. join pages into a ring 
    if (!$firstpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
        error("Moveit: firstpageid not found");
    }
    if (!$lastpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "nextpageid", 0)) {
        error("Moveit: lastpage not found");
    }
    if (!set_field("lesson_pages", "prevpageid", $lastpageid, "id", $firstpageid)) {
        error("Moveit: unable to update link");
    }
    if (!set_field("lesson_pages", "nextpageid", $firstpageid, "id", $lastpageid)) {
        error("Moveit: unable to update link");
    }

    // third step. remove the page to be moved
    if (!$prevpageid = get_field("lesson_pages", "prevpageid", "id", $pageid)) {
        error("Moveit: prevpageid not found");
    }
    if (!$nextpageid = get_field("lesson_pages", "nextpageid", "id", $pageid)) {
        error("Moveit: nextpageid not found");
    }
    if (!set_field("lesson_pages", "nextpageid", $nextpageid, "id", $prevpageid)) {
        error("Moveit: unable to update link");
    }
    if (!set_field("lesson_pages", "prevpageid", $prevpageid, "id", $nextpageid)) {
        error("Moveit: unable to update link");
    }
    
    // fourth step. insert page to be moved in new place...
    if (!$nextpageid = get_field("lesson_pages", "nextpageid", "id", $after)) {
        error("Movit: nextpageid not found");
    }
    if (!set_field("lesson_pages", "nextpageid", $pageid, "id", $after)) {
        error("Moveit: unable to update link");
    }
    if (!set_field("lesson_pages", "prevpageid", $pageid, "id", $nextpageid)) {
        error("Moveit: unable to update link");
    }
    // ...and set the links in the moved page
    if (!set_field("lesson_pages", "prevpageid", $after, "id", $pageid)) {
        error("Moveit: unable to update link");
    }
    if (!set_field("lesson_pages", "nextpageid", $nextpageid, "id", $pageid)) {
        error("Moveit: unable to update link");
    }
    
    // fifth step. break the ring
    if (!$newlastpageid = get_field("lesson_pages", "prevpageid", "id", $newfirstpageid)) {
        error("Moveit: newlastpageid not found");
    }
    if (!set_field("lesson_pages", "prevpageid", 0, "id", $newfirstpageid)) {
        error("Moveit: unable to update link");
    }
    if (!set_field("lesson_pages", "nextpageid", 0, "id", $newlastpageid)) {
            error("Moveit: unable to update link");
    }
    lesson_set_message(get_string('movedpage', 'lesson'), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
?>
