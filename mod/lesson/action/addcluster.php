<?php
/**
 * Action for adding a cluster page
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    confirm_sesskey();

    // first get the preceeding page
    // if $pageid = 0, then we are inserting a new page at the beginning of the lesson
    $pageid = required_param('pageid', PARAM_INT);

    $timenow = time();

    if ($pageid == 0) {
        if (!$page = $DB->get_record("lesson_pages", array("prevpageid" => 0, "lessonid" => $lesson->id))) {
            print_error('cannotfindpagerecord', 'lesson');
        }
    } else {
        if (!$page = $DB->get_record("lesson_pages", array("id" => $pageid))) {
            print_error('cannotfindpagerecord', 'lesson');
        }
    }
    $newpage = new stdClass;
    $newpage->lessonid = $lesson->id;
    $newpage->prevpageid = $pageid;
    if ($pageid != 0) {
        $newpage->nextpageid = $page->nextpageid;
    } else {
        $newpage->nextpageid = $page->id;
    }
    $newpage->qtype = LESSON_CLUSTER;
    $newpage->timecreated = $timenow;
    $newpage->title = get_string("clustertitle", "lesson");
    $newpage->contents = get_string("clustertitle", "lesson");
    $newpageid = $DB->insert_record("lesson_pages", $newpage);
    // update the linked list...
    if ($pageid != 0) {
        $DB->set_field("lesson_pages", "nextpageid", $newpageid, array("id" => $pageid));
    }

    if ($pageid == 0) {
        $page->nextpageid = $page->id;
    }
    if ($page->nextpageid) {
        // the new page is not the last page
        $DB->set_field("lesson_pages", "prevpageid", $newpageid, array("id" => $page->nextpageid));
    }
    // ..and the single "answer"
    $newanswer = new stdClass;
    $newanswer->lessonid = $lesson->id;
    $newanswer->pageid = $newpageid;
    $newanswer->timecreated = $timenow;
    $newanswer->jumpto = LESSON_CLUSTERJUMP;
    $newanswerid = $DB->insert_record("lesson_answers", $newanswer);
    lesson_set_message(get_string('addedcluster', 'lesson'), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");

