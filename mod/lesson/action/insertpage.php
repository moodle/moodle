<?php // $Id$
/**
 * Action for processing the form from addpage action and inserts the page.
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    require_sesskey();

    // check to see if the cancel button was pushed
    if (optional_param('cancel', '', PARAM_ALPHA)) {
        redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
    }

    $timenow = time();
    
    $form = data_submitted();
    $newpage = new stdClass;
    $newanswer = new stdClass;
    if ($form->pageid) {
        // the new page is not the first page
        if (!$page = get_record("lesson_pages", "id", $form->pageid)) {
            error("Insert page: page record not found");
        }
        $newpage->lessonid = clean_param($lesson->id, PARAM_INT);
        $newpage->prevpageid = clean_param($form->pageid, PARAM_INT);
        $newpage->nextpageid = clean_param($page->nextpageid, PARAM_INT);
        $newpage->timecreated = $timenow;
        $newpage->qtype = $form->qtype;
        if (isset($form->qoption)) {
            $newpage->qoption = clean_param($form->qoption, PARAM_INT);
        } else {
            $newpage->qoption = 0;
        }
        if (isset($form->layout)) {
            $newpage->layout = clean_param($form->layout, PARAM_INT);
        } else {
            $newpage->layout = 0;
        }
        if (isset($form->display)) {
            $newpage->display = clean_param($form->display, PARAM_INT);
        } else {
            $newpage->display = 0;
        }
        $newpage->title = clean_param($form->title, PARAM_CLEANHTML);
        $newpage->contents = trim($form->contents);
        $newpage->title = addslashes($newpage->title);
        $newpageid = insert_record("lesson_pages", $newpage);
        if (!$newpageid) {
            error("Insert page: new page not inserted");
        }
        // update the linked list (point the previous page to this new one)
        if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $newpage->prevpageid)) {
            error("Insert page: unable to update next link");
        }
        if ($page->nextpageid) {
            // new page is not the last page
            if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->nextpageid)) {
                error("Insert page: unable to update previous link");
            }
        }
    } else {
        // new page is the first page
        // get the existing (first) page (if any)
        if (!$page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
            // there are no existing pages
            $newpage->lessonid = $lesson->id;
            $newpage->prevpageid = 0; // this is a first page
            $newpage->nextpageid = 0; // this is the only page
            $newpage->timecreated = $timenow;
            $newpage->qtype = clean_param($form->qtype, PARAM_INT);
            if (isset($form->qoption)) {
                $newpage->qoption = clean_param($form->qoption, PARAM_INT);
            } else {
                $newpage->qoption = 0;
            }
            if (isset($form->layout)) {
                $newpage->layout = clean_param($form->layout, PARAM_INT);
            } else {
                $newpage->layout = 0;
            }
            if (isset($form->display)) {
                $newpage->display = clean_param($form->display, PARAM_INT);
            } else {
                $newpage->display = 0;
            }                
            $newpage->title = clean_param($form->title, PARAM_CLEANHTML);
            $newpage->contents = trim($form->contents);
            $newpage->title = addslashes($newpage->title);
            $newpageid = insert_record("lesson_pages", $newpage);
            if (!$newpageid) {
                error("Insert page: new first page not inserted");
            }
        } else {
            // there are existing pages put this at the start
            $newpage->lessonid = $lesson->id;
            $newpage->prevpageid = 0; // this is a first page
            $newpage->nextpageid = $page->id;
            $newpage->timecreated = $timenow;
            $newpage->qtype = clean_param($form->qtype, PARAM_INT);
            if (isset($form->qoption)) {
                $newpage->qoption = clean_param($form->qoption, PARAM_INT);
            } else {
                $newpage->qoption = 0;
            }
            if (isset($form->layout)) {
                $newpage->layout = clean_param($form->layout, PARAM_INT);
            } else {
                $newpage->layout = 0;
            }
            if (isset($form->display)) {
                $newpage->display = clean_param($form->display, PARAM_INT);
            } else {
                $newpage->display = 0;
            }                
            $newpage->title = clean_param($form->title, PARAM_CLEANHTML);
            $newpage->contents = trim($form->contents);
            $newpage->title = addslashes($newpage->title);
            $newpageid = insert_record("lesson_pages", $newpage);
            if (!$newpageid) {
                error("Insert page: first page not inserted");
            }
            // update the linked list
            if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $newpage->nextpageid)) {
                error("Insert page: unable to update link");
            }
        }
    }
    // now add the answers
    if ($form->qtype == LESSON_ESSAY) {
        $newanswer->lessonid = $lesson->id;
        $newanswer->pageid = $newpageid;
        $newanswer->timecreated = $timenow;
        if (isset($form->jumpto[0])) {
            $newanswer->jumpto = clean_param($form->jumpto[0], PARAM_INT);
        }
        if (isset($form->score[0])) {
            $newanswer->score = clean_param($form->score[0], PARAM_INT);
        }
        $newanswerid = insert_record("lesson_answers", $newanswer);
        if (!$newanswerid) {
            error("Insert Page: answer record not inserted");
        }
    } else {
        if ($form->qtype == LESSON_MATCHING) {
            // need to add two to offset correct response and wrong response
            $lesson->maxanswers = $lesson->maxanswers + 2;
        }
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            if (!empty($form->answer[$i]) and trim(strip_tags($form->answer[$i]))) { // strip_tags because the HTML editor adds <p><br />...
                $newanswer->lessonid = $lesson->id;
                $newanswer->pageid = $newpageid;
                $newanswer->timecreated = $timenow;
                $newanswer->answer = trim($form->answer[$i]);
                if (isset($form->response[$i])) {
                    $newanswer->response = trim($form->response[$i]);
                }
                if (isset($form->jumpto[$i])) {
                    $newanswer->jumpto = clean_param($form->jumpto[$i], PARAM_INT);
                }
                if ($lesson->custom) {
                    if (isset($form->score[$i])) {
                        $newanswer->score = clean_param($form->score[$i], PARAM_INT);
                    }
                }
                $newanswerid = insert_record("lesson_answers", $newanswer);
                if (!$newanswerid) {
                    error("Insert Page: answer record $i not inserted");
                }
            } else {
                if ($form->qtype == LESSON_MATCHING) {
                    if ($i < 2) {
                        $newanswer->lessonid = $lesson->id;
                        $newanswer->pageid = $newpageid;
                        $newanswer->timecreated = $timenow;
                        $newanswerid = insert_record("lesson_answers", $newanswer);
                        if (!$newanswerid) {
                            error("Insert Page: answer record $i not inserted");
                        }
                    }
                } else {
                    break;
                }
            }
        }
    }
    
    lesson_set_message(get_string('insertedpage', 'lesson').': '.format_string($newpage->title, true), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
?>
