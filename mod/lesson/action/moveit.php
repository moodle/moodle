<?php
/**
 * Action for actually moving the page (database changes)
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    confirm_sesskey();

    $pageid = required_param('pageid', PARAM_INT); //  page to move
    if (!$page = $DB->get_record("lesson_pages", array("id" => $pageid))) {
        print_error("Moveit: page not found");
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
            if (!$after = $DB->get_field("lesson_pages", "id", array("lessonid" => $lesson->id, "nextpageid" => 0))) {
                print_error("Moveit: last page id not found");
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
        if (!$newfirstpageid = $DB->get_field("lesson_pages", "id", array("lessonid" => $lesson->id, "prevpageid" => 0))) {
            print_error("Moveit: current first page id not found");
        }
    }
    // the rest is all unconditional...

    // second step. join pages into a ring
    if (!$firstpageid = $DB->get_field("lesson_pages", "id", array("lessonid" => $lesson->id, "prevpageid" => 0))) {
        print_error("Moveit: firstpageid not found");
    }
    if (!$lastpageid = $DB->get_field("lesson_pages", "id", array("lessonid" => $lesson->id, "nextpageid" => 0))) {
        print_error("Moveit: lastpage not found");
    }
    if (!$DB->set_field("lesson_pages", "prevpageid", $lastpageid, array("id" => $firstpageid))) {
        print_error("Moveit: unable to update link");
    }
    if (!$DB->set_field("lesson_pages", "nextpageid", $firstpageid, array("id" => $lastpageid))) {
        print_error("Moveit: unable to update link");
    }

    // third step. remove the page to be moved
    if (!$prevpageid = $DB->get_field("lesson_pages", "prevpageid", array("id" => $pageid))) {
        print_error("Moveit: prevpageid not found");
    }
    if (!$nextpageid = $DB->get_field("lesson_pages", "nextpageid", array("id" => $pageid))) {
        print_error("Moveit: nextpageid not found");
    }
    if (!$DB->set_field("lesson_pages", "nextpageid", $nextpageid, array("id" => $prevpageid))) {
        print_error("Moveit: unable to update link");
    }
    if (!$DB->set_field("lesson_pages", "prevpageid", $prevpageid, array("id" => $nextpageid))) {
        print_error("Moveit: unable to update link");
    }

    // fourth step. insert page to be moved in new place...
    if (!$nextpageid = $DB->get_field("lesson_pages", "nextpageid", array("id" => $after))) {
        print_error("Movit: nextpageid not found");
    }
    if (!$DB->set_field("lesson_pages", "nextpageid", $pageid, array("id" => $after))) {
        print_error("Moveit: unable to update link");
    }
    if (!$DB->set_field("lesson_pages", "prevpageid", $pageid, array("id" => $nextpageid))) {
        print_error("Moveit: unable to update link");
    }
    // ...and set the links in the moved page
    if (!$DB->set_field("lesson_pages", "prevpageid", $after, array("id" => $pageid))) {
        print_error("Moveit: unable to update link");
    }
    if (!$DB->set_field("lesson_pages", "nextpageid", $nextpageid, array("id" => $pageid))) {
        print_error("Moveit: unable to update link");
    }

    // fifth step. break the ring
    if (!$newlastpageid = $DB->get_field("lesson_pages", "prevpageid", array("id" => $newfirstpageid))) {
        print_error("Moveit: newlastpageid not found");
    }
    if (!$DB->set_field("lesson_pages", "prevpageid", 0, array("id" => $newfirstpageid))) {
        print_error("Moveit: unable to update link");
    }
    if (!$DB->set_field("lesson_pages", "nextpageid", 0, array("id" => $newlastpageid))) {
            print_error("Moveit: unable to update link");
    }
    lesson_set_message(get_string('movedpage', 'lesson'), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");

