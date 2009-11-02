<?php // $Id$
/**
 * Action for processing the form in editpage action and saves the page
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    require_sesskey();
    
    $redirect = optional_param('redirect', '', PARAM_ALPHA);

    $timenow = time();
    $form = data_submitted();

    $page = new stdClass;
    $page->id = clean_param($form->pageid, PARAM_INT);
    
    // check to see if the cancel button was pushed
    if (optional_param('cancel', '', PARAM_ALPHA)) {
        if ($redirect == 'navigation') {
            // redirect to viewing the page
            redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=$page->id");
        } else {
            redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
        }
    }

    $page->timemodified = $timenow;
    $page->qtype = clean_param($form->qtype, PARAM_INT);
    if (isset($form->qoption)) {
        $page->qoption = clean_param($form->qoption, PARAM_INT);
    } else {
        $page->qoption = 0;
    }
    if (isset($form->layout)) {
        $page->layout = clean_param($form->layout, PARAM_INT);
    } else {
        $page->layout = 0;
    }
    if (isset($form->display)) {
        $page->display = clean_param($form->display, PARAM_INT);
    } else {
        $page->display = 0;
    }
    $page->title = clean_param($form->title, PARAM_CLEANHTML);
    $page->contents = trim($form->contents);
    $page->title = addslashes($page->title);
    
    if (!update_record("lesson_pages", $page)) {
        error("Update page: page not updated");
    }
    if ($page->qtype == LESSON_ENDOFBRANCH || $page->qtype == LESSON_ESSAY || $page->qtype == LESSON_CLUSTER || $page->qtype == LESSON_ENDOFCLUSTER) {
        // there's just a single answer with a jump
        $oldanswer = new stdClass;
        $oldanswer->id = $form->answerid[0];
        $oldanswer->timemodified = $timenow;
        $oldanswer->jumpto = clean_param($form->jumpto[0], PARAM_INT);
        if (isset($form->score[0])) {
            $oldanswer->score = clean_param($form->score[0], PARAM_INT);
        }
        // delete other answers  this if mainly for essay questions.  If one switches from using a qtype like Multichoice,
        // then switches to essay, the old answers need to be removed because essay is
        // supposed to only have one answer record
        if ($answers = get_records_select("lesson_answers", "pageid = ".$page->id)) {
            foreach ($answers as $answer) {
                if ($answer->id != clean_param($form->answerid[0], PARAM_INT)) {
                    if (!delete_records("lesson_answers", "id", $answer->id)) {
                        error("Update page: unable to delete answer record");
                    }
                }
            }
        }        
        if (!update_record("lesson_answers", $oldanswer)) {
            error("Update page: EOB not updated");
        }
    } else {
        // it's an "ordinary" page
        if ($page->qtype == LESSON_MATCHING) {
            // need to add two to offset correct response and wrong response
            $lesson->maxanswers = $lesson->maxanswers + 2;
        }
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            // strip tags because the editor gives <p><br />...
            // also save any answers where the editor is (going to be) used
            if ((isset($form->answer[$i]) and (trim(strip_tags($form->answer[$i]))) != '') or isset($form->answereditor[$i]) or isset($form->responseeditor[$i])) {
                if ($form->answerid[$i]) {
                    $oldanswer = new stdClass;
                    $oldanswer->id = clean_param($form->answerid[$i], PARAM_INT);
                    if (!isset($form->answereditor[$i])) {
                        $form->answereditor[$i] = 0;
                    }
                    if (!isset($form->responseeditor[$i])) {
                        $form->responseeditor[$i] = 0;
                    }
                    $oldanswer->flags = $form->answereditor[$i] * LESSON_ANSWER_EDITOR +
                                        $form->responseeditor[$i] * LESSON_RESPONSE_EDITOR;
                    $oldanswer->timemodified = $timenow;
                    $oldanswer->answer = trim($form->answer[$i]);
                    if (isset($form->response[$i])) {
                        $oldanswer->response = trim($form->response[$i]);
                    } else {
                        $oldanswer->response = '';
                    }
                    $oldanswer->jumpto = clean_param($form->jumpto[$i], PARAM_INT);
                    if (isset($form->score[$i])) {
                        $oldanswer->score = clean_param($form->score[$i], PARAM_INT);
                    }
                    if (!update_record("lesson_answers", $oldanswer)) {
                        error("Update page: answer $i not updated");
                    }
                } else {
                    // it's a new answer
                    $newanswer = new stdClass; // need to clear id if more than one new answer is ben added
                    $newanswer->lessonid = $lesson->id;
                    $newanswer->pageid = $page->id;
                    if (!isset($form->answereditor[$i])) {
                        $form->answereditor[$i] = 0;
                    }
                    if (!isset($form->responseeditor[$i])) {
                        $form->responseeditor[$i] = 0;
                    }
                    $newanswer->flags = $form->answereditor[$i] * LESSON_ANSWER_EDITOR +
                                        $form->responseeditor[$i] * LESSON_RESPONSE_EDITOR;
                    $newanswer->timecreated = $timenow;
                    $newanswer->answer = trim($form->answer[$i]);
                    if (isset($form->response[$i])) {
                        $newanswer->response = trim($form->response[$i]);
                    }
                    $newanswer->jumpto = clean_param($form->jumpto[$i], PARAM_INT);
                    if (isset($form->score[$i])) {
                        $newanswer->score = clean_param($form->score[$i], PARAM_INT);
                    }
                    $newanswerid = insert_record("lesson_answers", $newanswer);
                    if (!$newanswerid) {
                        error("Update page: answer record not inserted");
                    }
                }
            } else {
                 if ($form->qtype == LESSON_MATCHING) {
                    if ($i >= 2) {
                        if ($form->answerid[$i]) {
                            // need to delete blanked out answer
                            if (!delete_records("lesson_answers", "id", clean_param($form->answerid[$i], PARAM_INT))) {
                                error("Update page: unable to delete answer record");
                            }
                        }
                    } else {
                        $oldanswer = new stdClass;
                        $oldanswer->id = clean_param($form->answerid[$i], PARAM_INT);
                        if (!isset($form->answereditor[$i])) {
                            $form->answereditor[$i] = 0;
                        }
                        if (!isset($form->responseeditor[$i])) {
                            $form->responseeditor[$i] = 0;
                        }                        
                        $oldanswer->flags = $form->answereditor[$i] * LESSON_ANSWER_EDITOR +
                                            $form->responseeditor[$i] * LESSON_RESPONSE_EDITOR;
                        $oldanswer->timemodified = $timenow;
                        $oldanswer->answer = NULL;
                        if (!update_record("lesson_answers", $oldanswer)) {
                            error("Update page: answer $i not updated");
                        }
                    }                        
                } elseif (!empty($form->answerid[$i])) {
                    // need to delete blanked out answer
                    if (!delete_records("lesson_answers", "id", clean_param($form->answerid[$i], PARAM_INT))) {
                        error("Update page: unable to delete answer record");
                    }
                }
            }
        }
    }

    if ($form->redisplay) {
        redirect("$CFG->wwwroot/mod/lesson/lesson.php?id=$cm->id&amp;action=editpage&amp;pageid=$page->id&amp;redirect=$redirect");
    }
    
    lesson_set_message(get_string('updatedpage', 'lesson').': '.format_string($page->title, true), 'notifysuccess');
    if ($redirect == 'navigation') {
        // takes us back to viewing the page
        redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=$page->id");
    } else {
        redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
    }
?>
