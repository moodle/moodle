<?php
/**
 * Action for processing the form from addpage action and inserts the page.
 *
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
        if (!$page = $DB->get_record("lesson_pages", array("id" => $form->pageid))) {
            print_error('cannotfindpages', 'lesson');
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
        $newpage->title = $newpage->title;
        $newpageid = $DB->insert_record("lesson_pages", $newpage);
        // update the linked list (point the previous page to this new one)
        $DB->set_field("lesson_pages", "nextpageid", $newpageid, array("id" => $newpage->prevpageid));
        if ($page->nextpageid) {
            // new page is not the last page
            $DB->set_field("lesson_pages", "prevpageid", $newpageid, array("id" => $page->nextpageid));
        }
    } else {
        // new page is the first page
        // get the existing (first) page (if any)
        $params = array ("lessonid" => $lesson->id, "prevpageid" => 0);
        if (!$page = $DB->get_record_select("lesson_pages", "lessonid = :lessonid AND prevpageid = :prevpageid", $params)) {
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
            $newpage->title = $newpage->title;
            $newpageid = $DB->insert_record("lesson_pages", $newpage);
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
            $newpage->title = $newpage->title;
            $newpageid = $DB->insert_record("lesson_pages", $newpage);
            // update the linked list
            $DB->set_field("lesson_pages", "prevpageid", $newpageid, array("id" => $newpage->nextpageid));
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
        $newanswerid = $DB->insert_record("lesson_answers", $newanswer);
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
                $newanswerid = $DB->insert_record("lesson_answers", $newanswer);
            } else {
                if ($form->qtype == LESSON_MATCHING) {
                    if ($i < 2) {
                        $newanswer->lessonid = $lesson->id;
                        $newanswer->pageid = $newpageid;
                        $newanswer->timecreated = $timenow;
                        $newanswerid = $DB->insert_record("lesson_answers", $newanswer);
                    }
                } else {
                    break;
                }
            }
        }
    }

    lesson_set_message(get_string('insertedpage', 'lesson').': '.format_string($newpage->title, true), 'notifysuccess');
    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");

