<?php // $Id$
/**
 * Action for adding an end of branch page
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    require_sesskey();

    // first get the preceeding page
    $pageid = required_param('pageid', PARAM_INT);
    
    $timenow = time();

    // the new page is not the first page (end of branch always comes after an existing page)
    if (!$page = get_record("lesson_pages", "id", $pageid)) {
        error("Add end of branch: page record not found");
    }
    // chain back up to find the (nearest branch table)
    $btpageid = $pageid;
    if (!$btpage = get_record("lesson_pages", "id", $btpageid)) {
        error("Add end of branch: btpage record not found");
    }
    while (($btpage->qtype != LESSON_BRANCHTABLE) AND ($btpage->prevpageid > 0)) {
        $btpageid = $btpage->prevpageid;
        if (!$btpage = get_record("lesson_pages", "id", $btpageid)) {
            error("Add end of branch: btpage record not found");
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
        if (!$newpageid = insert_record("lesson_pages", $newpage)) {
            error("Insert page: new page not inserted");
        }
        // update the linked list...
        if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $pageid)) {
            error("Add end of branch: unable to update link");
        }
        if ($page->nextpageid) {
            // the new page is not the last page
            if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->nextpageid)) {
                error("Insert page: unable to update previous link");
            }
        }
        // ..and the single "answer"
        $newanswer = new stdClass;
        $newanswer->lessonid = $lesson->id;
        $newanswer->pageid = $newpageid;
        $newanswer->timecreated = $timenow;
        $newanswer->jumpto = $btpageid;
        if(!$newanswerid = insert_record("lesson_answers", $newanswer)) {
            error("Add end of branch: answer record not inserted");
        }
        
        lesson_set_message(get_string('addedanendofbranch', 'lesson'), 'notifysuccess');
    } else {
        lesson_set_message(get_string('nobranchtablefound', 'lesson'));
    }
    
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
?>
