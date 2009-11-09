<?php // $Id$
    require_once("../../config.php");
    require_once("lib.php");

    $attemptid = required_param('attemptid', PARAM_INT);

    // get attempt, hotpot, course and course_module records
    if (! $attempt = get_record("hotpot_attempts", "id", $attemptid)) {
        error("Hot Potatoes attempt record $attemptid could not be accessed: ".$db->ErrorMsg());
    }
    if ($attempt->userid != $USER->id) {
        error("User ID is incorrect");
    }
    if (! $hotpot = get_record("hotpot", "id", $attempt->hotpot)) {
        error("Hot Potatoes ID is incorrect (attempt id = $attempt->id)");
    }
    if (! $course = get_record("course", "id", $hotpot->course)) {
        error("Course ID is incorrect (hotpot id = $hotpot->id)");
    }
    if (! $cm = get_coursemodule_from_instance("hotpot", $hotpot->id, $course->id)) {
        error("Course Module ID is incorrect");
    }

    // make sure this user is enrolled in this course and can access this HotPot
    require_login($course);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/hotpot:attempt', $context);

    $next_url = "$CFG->wwwroot/course/view.php?id=$course->id";
    $time = time();

    // check user can access this hotpot activity
    if (!hotpot_is_visible($cm)) {
        print_error("activityiscurrentlyhidden", 'hotpot', $next_url);
    }

    // update attempt record fields using incoming data
    $attempt->score = optional_param('mark', NULL, PARAM_INT);
    $attempt->status = optional_param('status', NULL, PARAM_INT);
    $attempt->details = optional_param('detail', NULL, PARAM_RAW);
    $attempt->endtime = optional_param('endtime', NULL, PARAM_ALPHA);
    $attempt->starttime = optional_param('starttime', NULL, PARAM_ALPHA);
    $attempt->timefinish = $time;

    // convert times, if necessary
    if (empty($attempt->starttime)) {
        $attempt->starttime = 0;
    } else {
         $attempt->starttime = strtotime($attempt->starttime);
    }
    if (empty($attempt->endtime)) {
        $attempt->endtime = 0;
    } else {
         $attempt->endtime = strtotime($attempt->endtime);
    }

    // set clickreportid, (for click reporting)
    $attempt->clickreportid = $attempt->id;

    $quiztype = optional_param('quiztype', 0, PARAM_INT);

    if (empty($attempt->details)) {
        hotpot_set_attempt_details($attempt);
        $javascript_is_off = true;
    } else {
        $javascript_is_off = false;
    }

    if (empty($attempt->status)) {
        if (empty($attempt->endtime)) {
            $attempt->status = HOTPOT_STATUS_INPROGRESS;
        } else {
            $attempt->status = HOTPOT_STATUS_COMPLETED;
        }
    }

    // check if this is the second (or subsequent) click
    if (get_field("hotpot_attempts", "timefinish", "id", $attempt->id)) {

        if ($hotpot->clickreporting==HOTPOT_YES) {
            // add attempt record for each form submission
            // records are linked via the "clickreportid" field

            // update status in previous records in this group
            set_field("hotpot_attempts", "status", $attempt->status, "clickreportid", $attempt->clickreportid);

            // add new attempt record
            unset ($attempt->id);
            $attempt->id = insert_record("hotpot_attempts", $attempt);

            if (empty($attempt->id)) {
                error("Could not insert attempt record: ".$db->ErrorMsg(), $next_url);
            }

            // add attempt details record, if necessary
            if (!empty($attempt->details)) {
                unset($details);
                $details->attempt = $attempt->id;
                $details->details = $attempt->details;
                if (! insert_record("hotpot_details", $details, false)) {
                    error("Could not insert attempt details record: ".$db->ErrorMsg(), $next_url);
                }
            }
        } else {
            // remove previous responses for this attempt, if required
            // (N.B. this does NOT remove the attempt record, just the responses)
            delete_records("hotpot_responses", "attempt", $attempt->id);
        }
    }

    // remove slashes added by lib/setup.php
    $attempt->details = stripslashes($attempt->details);

    // add details of this attempt
    hotpot_add_attempt_details($attempt);

    // add slashes again, so the details can be added to the database
    $attempt->details = addslashes($attempt->details);

    // update the attempt record
    if (! update_record("hotpot_attempts", $attempt)) {
        error("Could not update attempt record: ".$db->ErrorMsg(), $next_url);
    }

    // update grades for this user
    hotpot_update_grades($hotpot, $attempt->userid);

    // get previous attempt details record, if any
    $details_exist = record_exists("hotpot_details", "attempt", $attempt->id);

    // delete/update/add the attempt details record
    if (empty($attempt->details)) {
        if ($details_exist) {
            delete_records("hotpot_details", "attempt", $attempt->id);
        }
    } else {
        if ($details_exist) {
            set_field("hotpot_details", "details", $attempt->details, "attempt", $attempt->id);
        } else {
            unset($details);
            $details->attempt = $attempt->id;
            $details->details = $attempt->details;
            if (! insert_record("hotpot_details", $details)) {
                error("Could not insert attempt details record: ".$db->ErrorMsg(), $next_url);
            }
        }
    }

    if ($attempt->status==HOTPOT_STATUS_INPROGRESS) {
        if ($javascript_is_off) {
            // regenerate HTML page
            define('HOTPOT_FIRST_ATTEMPT', false);
            include ("$CFG->hotpotroot/view.php");
        } else {
            // continue without reloading the page
            header("Status: 204");
            header("HTTP/1.0 204 No Response");
        }

    } else { // quiz is finished

        add_to_log($course->id, "hotpot", "submit", "review.php?id=$cm->id&attempt=$attempt->id", "$hotpot->id", "$cm->id");

        if ($hotpot->shownextquiz==HOTPOT_YES) {
            if (is_numeric($next_cm = hotpot_get_next_cm($cm))) {
                $next_url = "$CFG->wwwroot/mod/hotpot/view.php?id=$next_cm";
            }
        }

        // redirect to the next quiz or the course page
        redirect($next_url, get_string('resultssaved', 'hotpot'));
    }

// =================
//  functions
// =================

function hotpot_get_next_cm(&$cm) {
    // gets the next module in this section of the course
    // that is the same type of module as the current module

    $next_mod = false;

    // get a list of $ids of modules in this section
    if ($ids = get_field('course_sections', 'sequence', 'id', $cm->section)) {

        $found = false;
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            if ($found && ($cm->module==get_field('course_modules', 'module', 'id', $id))) {
                $next_mod = $id;
                break;
            } else if ($cm->id==$id) {
                $found = true;
            }
        }
    }
    return $next_mod;
}
function hotpot_set_attempt_details(&$attempt) {
    global $CFG, $HOTPOT_QUIZTYPE;

    // optional_param('showallquestions', 0, PARAM_INT);

    $attempt->details = '';
    $attempt->score = 0;
    $attempt->status = HOTPOT_STATUS_COMPLETED;

    $buttons = array('clues', 'hints', 'checks');
    $textfields = array('correct', 'wrong', 'ignored');

    $ok = false;
    $quiztype = optional_param('quiztype', 0, PARAM_ALPHANUM);
    if ($quiztype) {
        if (is_numeric($quiztype)) {
            $ok = array_key_exists($quiztype, $HOTPOT_QUIZTYPE);
        } else {
            $quiztype = array_search($quiztype, $HOTPOT_QUIZTYPE);
            $ok = is_numeric($quiztype);
        }
    }
    if (!$ok) {
        return;
        // error('Quiz type is missing or invalid');
        // print_error('error_invalidquiztype', 'hotpot');
        //
        // script finishes here if quiztype is invalid
        //
    }

    // special flag to detect jquiz multiselect
    $is_jquiz_multiselect = false;

    // set maximum question number
    $q_max = 0;;
    do {
        switch ($quiztype) {
            case HOTPOT_JCLOZE:
            case HOTPOT_JQUIZ:
                $field="q{$q_max}_a0_text";
                break;
            case HOTPOT_JCB:
            case HOTPOT_JCROSS:
            case HOTPOT_JMATCH:
            case HOTPOT_JMIX:
            default:
                $field = '';
        }
    } while ($field && isset($_POST[$field]) && ($q_max = $q_max+1));

    // check JQuiz navigation buttons
    switch (true) {
        case isset($_POST['ShowAllQuestionsButton']):
            $_POST['ShowAllQuestions'] = 1;
            break;
        case isset($_POST['ShowOneByOneButton']):
            $_POST['ShowAllQuestions'] = 0;
            break;
        case isset($_POST['PrevQButton']):
            $_POST['ThisQuestion']--;
            break;
        case isset($_POST['NextQButton']):
            $_POST['ThisQuestion']++;
            break;
    }

    $q = 0;
    while ($q<$q_max) {
        $responsefield="q{$q}";

        $questiontype = optional_param("{$responsefield}_questiontype", 0, PARAM_INT);
        $is_jquiz_multiselect = ($quiztype==HOTPOT_JQUIZ && $questiontype==HOTPOT_JQUIZ_MULTISELECT);

        if (isset($_POST[$responsefield]) && is_array($_POST[$responsefield])) {
            $responsevalue = array();
            foreach ($_POST[$responsefield] as $key=>$value) {
                $responsevalue[$key] = clean_param($value, PARAM_CLEAN);
            }
        } else {
            $responsevalue = optional_param($responsefield, '');
        }
        if (is_array($responsevalue)) {
            // incomplete jquiz multi-select
            $responsevalues = $responsevalue;
            $responsevalue = implode('+', $responsevalue);
        } else {
            $responsevalues = explode('+', $responsevalue);
        }

        // initialize $response object
        $response = new stdClass();
        $response->correct = array();
        $response->wrong   = array();
        $response->ignored = array();
        $response->clues  = 0;
        $response->hints  = 0;
        $response->checks = 0;
        $response->score  = 0;
        $response->weighting = 0;

        // create another empty object to hold all previous responses (from database)
        $oldresponse = new stdClass();
        $vars = get_object_vars($response);
        foreach($vars as $name=>$value) {
            $oldresponse->$name = $value;
        }

        foreach ($buttons as $button) {
            if (($field = "q{$q}_{$button}_button") && isset($_POST[$field])) {
                $value = optional_param($field, '', PARAM_RAW);
                if (!empty($value)) {
                    $response->$button++;
                }
            }
        }

        // loop through possible answers to this question
        $firstcorrectvalue = '';
        $percents = array();
        $a = 0;
        while (($valuefield="q{$q}_a{$a}_text") && isset($_POST[$valuefield])) {
            $value = optional_param($valuefield, '', PARAM_RAW);

            if (($percentfield="q{$q}_a{$a}_percent") && isset($_POST[$percentfield])) {
                $percent = optional_param($percentfield, 0, PARAM_INT);
                if ($percent) {
                    $percents[$value] = $percent;
                }
            }

            if (($correctfield="q{$q}_a{$a}_correct") && isset($_POST[$correctfield])) {
                $correct = optional_param($correctfield, 0, PARAM_INT);
            } else {
                $correct = false;
            }

            if ($correct && empty($firstcorrectvalue)) {
                $firstcorrectvalue = $value;
            }

            if ($is_jquiz_multiselect) {
                $selected = in_array($value, $responsevalues);
                if ($correct) {
                    $response->correct[] = $value;
                    if (empty($selected)) {
                        $response->wrong[] = true;
                    }
                } else {
                    if ($selected) {
                        $response->wrong[] = true;
                    }
                }
            } else {
                // single answer only required
                if ($responsevalue==$value) {
                    if ($correct) {
                        $response->correct[] = $value;
                    } else {
                        $response->wrong[] = $value;
                    }
                } else {
                    $response->ignored[] = $value;
                }
            }
            $a++;
        }

        // number of answers for this question
        $a_max = $a;

        if ($is_jquiz_multiselect) {
            if (empty($response->wrong) && count($responsevalues)==count($response->correct)) {
                $response->wrong = array();
                $response->correct = array($responsevalue);
            } else {
                $response->correct = array();
                $response->wrong = array($responsevalue);
            }
        } else {
            // if response did not match any answer, then this response is wrong
            if (empty($response->correct) && empty($response->wrong)) {
                $response->wrong[] = $responsevalue;
            }
        }

        // if this question has not been answered correctly, quiz is still in progress
        if (empty($response->correct)) {

            if (isset($_POST["q{$q}_ShowAnswers_button"])) {
                    $_POST[$responsefield] = $firstcorrectvalue;
            } else {
                $attempt->status = HOTPOT_STATUS_INPROGRESS;

                if (isset($_POST["q{$q}_Hint_button"])) {
                    // a particular hint button in JQuiz shortanswer
                    $_POST['HintButton'] = true;
                }

                // give a hint, if necessary
                if (isset($_POST['HintButton']) && $firstcorrectvalue) {

                    // make sure we only come through here once
                    unset($_POST['HintButton']);

                    $correctlen = strlen($firstcorrectvalue);
                    $responselen = strlen($responsevalue);

                    // check how many letters are the same
                    $i = 0;
                    while ($i<$responselen && $i<$correctlen && $responsevalue{$i}==$firstcorrectvalue{$i}) {
                        $i++;
                    }

                    if ($i<$responselen) {
                        // remove incorrect characters on the end of the response
                        $responsevalue = substr($responsevalue, 0, $i);
                    }
                    if ($i<$correctlen) {
                        // append next correct letter
                        $responsevalue .= $firstcorrectvalue{$i};
                    }
                    $_POST[$responsefield] = $responsevalue;
                    $response->hints++;
                } // end if hint
            }
        } // end if not correct

        // get clue text, if any
        if (($field="q{$q}_clue") && isset($_POST[$field])) {
            $response->clue_text = optional_param($field, '', PARAM_RAW);
        }

        // get question name
        $qq = sprintf('%02d', $q); // (a padded, two-digit version of $q)
        if (($field="q{$q}_name") && isset($_POST[$field])) {
            $questionname = optional_param($field, '',  PARAM_RAW);
            $questionname = strip_tags($questionname);
        } else {
            $questionname = $qq;
        }

        // get previous responses to this question (if any)
        $records = get_records_sql("
            SELECT
                r.*
            FROM
                {$CFG->prefix}hotpot_attempts a,
                {$CFG->prefix}hotpot_questions q,
                {$CFG->prefix}hotpot_responses r
            WHERE
                a.clickreportid = $attempt->clickreportid AND
                a.id = r.attempt AND
                r.question = q.id AND
                q.name = '$questionname' AND
                q.hotpot = $attempt->hotpot
            ORDER BY
                a.timefinish
        ");

        if ($records) {
            foreach ($records as $record) {
                foreach ($buttons as $button) {
                    $oldresponse->$button = max($oldresponse->$button, $record->$button);
                }
                foreach ($textfields as $field) {
                    if ($record->$field && ($field=='correct' || $field=='wrong')) {
                        $values = explode(',', hotpot_strings($record->$field));
                        $oldresponse->$field = array_merge($oldresponse->$field, $values);
                    }
                }
            }
        }

        // remove "correct" and "wrong" values from "ignored" values
        $response->ignored = array_diff($response->ignored,
            $response->correct, $response->wrong, $oldresponse->correct, $oldresponse->wrong
        );

        foreach ($buttons as $button) {
            $response->$button += $oldresponse->$button;
        }

        $value_has_changed = false;
        foreach ($textfields as $field) {
            $response->$field = array_merge($oldresponse->$field, $response->$field);
            $response->$field = array_unique($response->$field);
            $response->$field  = implode(',', $response->$field);

            if ($field=='correct' || $field=='wrong') {
                $array = $oldresponse->$field;
                $array = array_unique($array);
                $oldresponse->$field  = implode(',', $array);
                if ($response->$field<>$oldresponse->$field) {
                    $value_has_changed = true;
                }
            }
        }
        if ($value_has_changed) {
            $response->checks++;
        }

        // $response now holds amalgamation of all responses so far to this question

        // set question score and weighting
        if ($response->correct) {
            switch ($quiztype) {
                case HOTPOT_JCB:
                    break;
                case HOTPOT_JCLOZE:
                    $strlen = strlen($response->correct);
                    $response->score = 100*($strlen-($response->checks-1))/$strlen;
                    $attempt->score += $response->score;
                    break;
                case HOTPOT_JCROSS:
                    break;
                case HOTPOT_JMATCH:
                    break;
                case HOTPOT_JMIX:
                    break;
                case HOTPOT_JQUIZ:
                    switch ($questiontype) {
                        case HOTPOT_JQUIZ_MULTICHOICE:
                            $wrong = explode(',', $response->wrong);
                            foreach ($wrong as $value) {
                                if (isset($percents[$value])) {
                                    $percent = $percents[$value];
                                } else {
                                    $percent = 0;
                                }
                            }
                        case HOTPOT_JQUIZ_SHORTANSWER:
                            $strlen = strlen($response->correct);
                            $response->score = 100*($strlen-($response->checks-1))/$strlen;
                            break;
                        case HOTPOT_JQUIZ_MULTISELECT:
                            if (isset($percents[$response->correct])) {
                                $percent = $percents[$response->correct];
                            } else {
                                $percent = 0;
                            }
                            if ($a_max>0 && $response->checks>0 && $a_max>$response->checks) {
                                $response->score = $percent*($a_max-($response->checks-1))/$a_max;
                            }
                            break;
                    }
                    $attempt->score += $response->score;
                    break;
            }
        }

        $fieldname = $HOTPOT_QUIZTYPE[$quiztype]."_q{$qq}_name";
        $attempt->details .= "<field><fieldname>$fieldname</fieldname><fielddata>$questionname</fielddata></field>";

        // encode $response fields as XML
        $vars = get_object_vars($response);
        foreach($vars as $name=>$value) {
            if (!empty($value)) {
                $fieldname = $HOTPOT_QUIZTYPE[$quiztype]."_q{$qq}_{$name}";
                $attempt->details .= "<field><fieldname>$fieldname</fieldname><fielddata>$value</fielddata></field>";
            }
        }

        $q++;
    } // end main loop through $q(uestions)

    // set attempt score
    if ($q>0) {
        switch ($quiztype) {
            case HOTPOT_JCB:
                break;
            case HOTPOT_JCLOZE:
                $attempt->score = floor($attempt->score / $q);
                break;
            case HOTPOT_JCROSS:
                break;
            case HOTPOT_JMATCH:
                break;
            case HOTPOT_JMIX:
                break;
            case HOTPOT_JQUIZ:
                break;
        }
    }

    if ($attempt->details) {
        $attempt->details = '<?xml version="1.0"?><hpjsresult><fields>'.$attempt->details.'</fields></hpjsresult>';
    }

//  print "forcing status to in progress ..<br/>\n";
//  $attempt->status = HOTPOT_STATUS_INPROGRESS;
}

?>
