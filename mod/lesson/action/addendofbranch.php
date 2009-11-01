<?php
/**
 * Action for adding an end of branch page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    confirm_sesskey();

    // first get the preceeding page
    $pageid = required_param('pageid', PARAM_INT);

    $timenow = time();

    // the new page is not the first page (end of branch always comes after an existing page)
    if (!$page = $DB->get_record("lesson_pages", array("id" => $pageid))) {
        print_error('cannotfindpagerecord', 'lesson');
    }
    // chain back up to find the (nearest branch table)
    $btpageid = $pageid;
    if (!$btpage = $DB->get_record("lesson_pages", array("id" => $btpageid))) {
        print_error('cannotfindpagerecord', 'lesson');
    }
    while (($btpage->qtype != LESSON_BRANCHTABLE) AND ($btpage->prevpageid > 0)) {
        $btpageid = $btpage->prevpageid;
        if (!$btpage = $DB->get_record("lesson_pages", array("id" => $btpageid))) {
            print_error('cannotfindpagerecord', 'lesson');
        }
    }
    if ($btpage->qtype == LESSON_BRANCHTABLE) {
        $newpage = new stdClass;
        $newpage->lessonid = $lesson->id;
        $newpage->prevpageid = $pageid;
        $newpage->nextpageid = $page->nextpageid;
        $newpage->qtype = LESSON_ENDOFBRANCH;
        $newpage->timecreated = $timenow;
        $newpage->title = get_string("endofbranch", "lesson");
        $newpage->contents = get_string("endofbranch", "lesson");
        $newpageid = $DB->insert_record("lesson_pages", $newpage);
        // update the linked list...
        $DB->set_field("lesson_pages", "nextpageid", $newpageid, array("id" => $pageid));
        if ($page->nextpageid) {
            // the new page is not the last page
            $DB->set_field("lesson_pages", "prevpageid", $newpageid, array("id" => $page->nextpageid));
        }
        // ..and the single "answer"
        $newanswer = new stdClass;
        $newanswer->lessonid = $lesson->id;
        $newanswer->pageid = $newpageid;
        $newanswer->timecreated = $timenow;
        $newanswer->jumpto = $btpageid;
        $newanswerid = $DB->insert_record("lesson_answers", $newanswer);

        lesson_set_message(get_string('addedanendofbranch', 'lesson'), 'notifysuccess');
    } else {
        lesson_set_message(get_string('nobranchtablefound', 'lesson'));
    }

    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");

