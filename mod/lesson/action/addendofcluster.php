<?php // $Id$
/**
 * Action for adding an end of cluster page
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    require_sesskey();

    // first get the preceeding page
    $pageid = required_param('pageid', PARAM_INT);
        
    $timenow = time();
    
    // the new page is not the first page (end of cluster always comes after an existing page)
    if (!$page = get_record("lesson_pages", "id", $pageid)) {
        error("Error: Could not find page");
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
    if (!$newpageid = insert_record("lesson_pages", $newpage)) {
        error("Insert page: end of cluster page not inserted");
    }
    // update the linked list...
    if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $pageid)) {
        error("Add end of cluster: unable to update link");
    }
    if ($page->nextpageid) {
        // the new page is not the last page
        if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->nextpageid)) {
            error("Insert end of cluster: unable to update previous link");
        }
    }
    // ..and the single "answer"
    $newanswer = new stdClass;
    $newanswer->lessonid = $lesson->id;
    $newanswer->pageid = $newpageid;
    $newanswer->timecreated = $timenow;
    $newanswer->jumpto = LESSON_NEXTPAGE;
    if(!$newanswerid = insert_record("lesson_answers", $newanswer)) {
        error("Add end of cluster: answer record not inserted");
    }
    lesson_set_message(get_string('addedendofcluster', 'lesson'), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");