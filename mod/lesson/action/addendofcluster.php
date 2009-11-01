<?php
/**
 * Action for adding an end of cluster page
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    confirm_sesskey();

    // first get the preceeding page
    $pageid = required_param('pageid', PARAM_INT);

    $timenow = time();

    // the new page is not the first page (end of cluster always comes after an existing page)
    if (!$page = $DB->get_record("lesson_pages", array("id" => $pageid))) {
        print_error('cannotfindpages', 'lesson');
    }

    // could put code in here to check if the user really can insert an end of cluster

    $newpage = new stdClass;
    $newpage->lessonid = $lesson->id;
    $newpage->prevpageid = $pageid;
    $newpage->nextpageid = $page->nextpageid;
    $newpage->qtype = LESSON_ENDOFCLUSTER;
    $newpage->timecreated = $timenow;
    $newpage->title = get_string("endofclustertitle", "lesson");
    $newpage->contents = get_string("endofclustertitle", "lesson");
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
    $newanswer->jumpto = LESSON_NEXTPAGE;
    $newanswerid = $DB->insert_record("lesson_answers", $newanswer);
    lesson_set_message(get_string('addedendofcluster', 'lesson'), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
