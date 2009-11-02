<?php // $Id$
/**
 * Action for processing page answers by users
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    require_sesskey();

    require_once($CFG->dirroot.'/mod/lesson/pagelib.php');
    require_once($CFG->libdir.'/blocklib.php');

    // left menu code
    // check to see if the user can see the left menu
    if (!has_capability('mod/lesson:manage', $context)) {
        $lesson->displayleft = lesson_displayleftif($lesson);
    }

    // This is the code updates the lesson time for a timed test
    // get time information for this user
    $timer = new stdClass;
    if (!has_capability('mod/lesson:manage', $context)) {
        if (!$timer = get_records_select('lesson_timer', "lessonid = $lesson->id AND userid = $USER->id", 'starttime')) {
            error('Error: could not find records');
        } else {
            $timer = array_pop($timer); // this will get the latest start time record
        }
        
        if ($lesson->timed) {
            $timeleft = ($timer->starttime + $lesson->maxtime * 60) - time();

            if ($timeleft <= 0) {
                // Out of time
                lesson_set_message(get_string('eolstudentoutoftime', 'lesson'));
                redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=".LESSON_EOL."&outoftime=normal");
                die; // Shouldn't be reached, but make sure
            } else if ($timeleft < 60) {
                // One minute warning
                lesson_set_message(get_string("studentoneminwarning", "lesson"));
            }
        }
        
        $timer->lessontime = time();
        if (!update_record("lesson_timer", $timer)) {
            error("Error: could not update lesson_timer table");
        }
    }

    // record answer (if necessary) and show response (if none say if answer is correct or not)
    $pageid = required_param('pageid', PARAM_INT);
    if (!$page = get_record("lesson_pages", "id", $pageid)) {
        error("Continue: Page record not found");
    }
    // set up some defaults
    $answerid        = 0;
    $noanswer        = false;
    $correctanswer   = false;
    $isessayquestion = false;   // use this to turn off review button on essay questions
    $newpageid       = 0;       // stay on the page
    $studentanswer   = '';      // use this to store student's answer(s) in order to display it on feedback page
    switch ($page->qtype) {
         case LESSON_ESSAY :
            $isessayquestion = true;
            if (!$useranswer = $_POST['answer']) {
                $noanswer = true;
                break;
            }
            $useranswer = clean_param($useranswer, PARAM_RAW);
        
            if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                error("Continue: No answers found");
            }
            $correctanswer = false;
            foreach ($answers as $answer) {
                $answerid = $answer->id;
                $newpageid = $answer->jumpto;
            }
            /// 6/29/04 //
            $userresponse->sent=0;
            $userresponse->graded = 0;
            $userresponse->score = 0;
            $userresponse->answer = $useranswer;
            $userresponse->response = "";
            $userresponse = addslashes(serialize($userresponse));
            
            $studentanswer = s(stripslashes_safe($useranswer));
            break;
         case LESSON_SHORTANSWER :
            if (isset($_POST['answer'])) {
                $useranswer = $_POST['answer'];
            } else {
                $noanswer = true;
                break;
            }            
            $useranswer = s(stripslashes(clean_param($useranswer, PARAM_RAW)));
            $userresponse = addslashes($useranswer);
            if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                error("Continue: No answers found");
            }
            $i=0;
            foreach ($answers as $answer) {
                $i += 1;
                $expectedanswer  = $answer->answer; // for easier handling of $answer->answer
                $ismatch         = false; 
                $markit          = false; 
                $useregexp       = false;

                if ($page->qoption) {
                    $useregexp = true;
                }
                
                if ($useregexp) { //we are using 'normal analysis', which ignores case
                    $ignorecase = '';
                    if ( substr($expectedanswer,strlen($expectedanswer) - 2, 2) == '/i') {
                        $expectedanswer = substr($expectedanswer,0,strlen($expectedanswer) - 2);
                        $ignorecase = 'i';
                    }
                } else {
                    $expectedanswer = str_replace('*', '#####', $expectedanswer);
                    $expectedanswer = preg_quote($expectedanswer, '/');
                    $expectedanswer = str_replace('#####', '.*', $expectedanswer);
                }
                // see if user typed in any of the correct answers
                if ((!$lesson->custom && lesson_iscorrect($pageid, $answer->jumpto)) or ($lesson->custom && $answer->score > 0) ) {
                    if (!$useregexp) { // we are using 'normal analysis', which ignores case
                        if (preg_match('/^'.$expectedanswer.'$/i',$useranswer)) {
                            $ismatch = true;
                        }
                    } else {
                        if (preg_match('/^'.$expectedanswer.'$/'.$ignorecase,$useranswer)) {
                            $ismatch = true;
                        }
                    }
                    if ($ismatch == true) {
                        $correctanswer = true;
                    }
                } else {
                   if (!$useregexp) { //we are using 'normal analysis'
                        // see if user typed in any of the wrong answers; don't worry about case
                        if (preg_match('/^'.$expectedanswer.'$/i',$useranswer)) {
                            $ismatch = true;
                        }
                    } else { // we are using regular expressions analysis
                        $startcode = substr($expectedanswer,0,2);
                        switch ($startcode){
                            //1- check for absence of required string in $useranswer (coded by initial '--')
                            case "--":
                                $expectedanswer = substr($expectedanswer,2);
                                if (!preg_match('/^'.$expectedanswer.'$/'.$ignorecase,$useranswer)) {
                                    $ismatch = true;
                                }
                                break;                                      
                            //2- check for code for marking wrong strings (coded by initial '++')
                            case "++":
                                $expectedanswer=substr($expectedanswer,2);
                                $markit = true;
                                //check for one or several matches
                                if (preg_match_all('/'.$expectedanswer.'/'.$ignorecase,$useranswer, $matches)) {
                                    $ismatch   = true;
                                    $nb        = count($matches[0]);
                                    $original  = array(); 
                                    $marked    = array();
                                    $fontStart = '<span class="incorrect matches">';
                                    $fontEnd   = '</span>';
                                    for ($i = 0; $i < $nb; $i++) {
                                        array_push($original,$matches[0][$i]);
                                        array_push($marked,$fontStart.$matches[0][$i].$fontEnd);
                                    }
                                    $useranswer = str_replace($original, $marked, $useranswer);
                                }
                                break;
                            //3- check for wrong answers belonging neither to -- nor to ++ categories 
                            default:
                                if (preg_match('/^'.$expectedanswer.'$/'.$ignorecase,$useranswer, $matches)) {
                                    $ismatch = true;
                                }
                                break;
                        }
                        $correctanswer = false;
                    }
                }
                if ($ismatch) {
                    $newpageid = $answer->jumpto;
                    if (trim(strip_tags($answer->response))) {
                        $response = $answer->response;
                    }
                    $answerid = $answer->id;
                    break; // quit answer analysis immediately after a match has been found
                }
            }
            $studentanswer = $useranswer;
            break;
        
        case LESSON_TRUEFALSE :
            if (empty($_POST['answerid'])) {
                $noanswer = true;
                break;
            }
            $answerid = required_param('answerid', PARAM_INT); 
            if (!$answer = get_record("lesson_answers", "id", $answerid)) {
                error("Continue: answer record not found");
            } 
            if (lesson_iscorrect($pageid, $answer->jumpto)) {
                $correctanswer = true;
            }
            if ($lesson->custom) {
                if ($answer->score > 0) {
                    $correctanswer = true;
                } else {
                    $correctanswer = false;
                }
            }
            $newpageid = $answer->jumpto;
            $response  = trim($answer->response);
            $studentanswer = $answer->answer;
            break;
        
        case LESSON_MULTICHOICE :
            if ($page->qoption) {
                // MULTIANSWER allowed, user's answer is an array
                if (isset($_POST['answer'])) {
                    $useranswers = $_POST['answer'];
                    foreach ($useranswers as $key => $useranswer) {
                        $useranswers[$key] = clean_param($useranswer, PARAM_INT);
                    }
                } else {
                    $noanswer = true;
                    break;
                }
                // get what the user answered
                $userresponse = implode(",", $useranswers);
                // get the answers in a set order, the id order
                if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                    error("Continue: No answers found");
                }
                $ncorrect = 0;
                $nhits = 0;
                $correctresponse = '';
                $wrongresponse = '';
                $correctanswerid = 0;
                $wronganswerid = 0;
                // store student's answers for displaying on feedback page
                foreach ($answers as $answer) {
                    foreach ($useranswers as $key => $answerid) {
                        if ($answerid == $answer->id) {
                            $studentanswer .= '<br />'.$answer->answer;
                        }
                    }
                }
                // this is for custom scores.  If score on answer is positive, it is correct                    
                if ($lesson->custom) {
                    $ncorrect = 0;
                    $nhits = 0;
                    foreach ($answers as $answer) {
                        if ($answer->score > 0) {
                            $ncorrect++;
                    
                            foreach ($useranswers as $key => $answerid) {
                                if ($answerid == $answer->id) {
                                   $nhits++;
                                }
                            }
                            // save the first jumpto page id, may be needed!...
                            if (!isset($correctpageid)) {  
                                // leave in its "raw" state - will converted into a proper page id later
                                $correctpageid = $answer->jumpto;
                            }
                            // save the answer id for scoring
                            if ($correctanswerid == 0) {
                                $correctanswerid = $answer->id;
                            }
                            // ...also save any response from the correct answers...
                            if (trim(strip_tags($answer->response))) {
                                $correctresponse = $answer->response;
                            }
                        } else {
                            // save the first jumpto page id, may be needed!...
                            if (!isset($wrongpageid)) {   
                                // leave in its "raw" state - will converted into a proper page id later
                                $wrongpageid = $answer->jumpto;
                            }
                            // save the answer id for scoring
                            if ($wronganswerid == 0) {
                                $wronganswerid = $answer->id;
                            }
                            // ...and from the incorrect ones, don't know which to use at this stage
                            if (trim(strip_tags($answer->response))) {
                                $wrongresponse = $answer->response;
                            }
                        }
                    }                    
                } else {
                    foreach ($answers as $answer) {
                        if (lesson_iscorrect($pageid, $answer->jumpto)) {
                            $ncorrect++;
                            foreach ($useranswers as $key => $answerid) {
                                if ($answerid == $answer->id) {
                                    $nhits++;
                                }
                            }
                            // save the first jumpto page id, may be needed!...
                            if (!isset($correctpageid)) {  
                                // leave in its "raw" state - will converted into a proper page id later
                                $correctpageid = $answer->jumpto;
                            }
                            // save the answer id for scoring
                            if ($correctanswerid == 0) {
                                $correctanswerid = $answer->id;
                            }
                            // ...also save any response from the correct answers...
                            if (trim(strip_tags($answer->response))) {
                                $correctresponse = $answer->response;
                            }
                        } else {
                            // save the first jumpto page id, may be needed!...
                            if (!isset($wrongpageid)) {   
                                // leave in its "raw" state - will converted into a proper page id later
                                $wrongpageid = $answer->jumpto;
                            }
                            // save the answer id for scoring
                            if ($wronganswerid == 0) {
                                $wronganswerid = $answer->id;
                            }
                            // ...and from the incorrect ones, don't know which to use at this stage
                            if (trim(strip_tags($answer->response))) {
                                $wrongresponse = $answer->response;
                            }
                        }
                    }
                }
                if ((count($useranswers) == $ncorrect) and ($nhits == $ncorrect)) {
                    $correctanswer = true;
                    $response  = $correctresponse;
                    $newpageid = $correctpageid;
                    $answerid  = $correctanswerid;
                } else {
                    $response  = $wrongresponse;
                    $newpageid = $wrongpageid;
                    $answerid  = $wronganswerid;
                }
            } else {
                // only one answer allowed
                if (empty($_POST['answerid'])) {
                    $noanswer = true;
                    break;
                }
                $answerid = required_param('answerid', PARAM_INT); 
                if (!$answer = get_record("lesson_answers", "id", $answerid)) {
                    error("Continue: answer record not found");
                }
                if (lesson_iscorrect($pageid, $answer->jumpto)) {
                    $correctanswer = true;
                }
                if ($lesson->custom) {
                    if ($answer->score > 0) {
                        $correctanswer = true;
                    } else {
                        $correctanswer = false;
                    }
                }
                $newpageid = $answer->jumpto;
                $response  = trim($answer->response);
                $studentanswer = $answer->answer;
            }
            break;
        case LESSON_MATCHING :
            if (isset($_POST['response']) && is_array($_POST['response'])) { // only arrays should be submitted
                $response = array();
                foreach ($_POST['response'] as $key => $value) {
                    $response[$key] = stripslashes($value);
                }
            } else {
                $noanswer = true;
                break;
            }

            if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                error("Continue: No answers found");
            }

            $ncorrect = 0;
            $i = 0;
            foreach ($answers as $answer) {
                if ($i == 0 || $i == 1) {
                    // ignore first two answers, they are correct response
                    // and wrong response
                    $i++;
                    continue;
                }
                if ($answer->response == $response[$answer->id]) {
                    $ncorrect++;
                }
                if ($i == 2) {
                    $correctpageid = $answer->jumpto;
                    $correctanswerid = $answer->id;
                }
                if ($i == 3) {
                    $wrongpageid = $answer->jumpto;
                    $wronganswerid = $answer->id;                        
                }
                $i++;
            }
            // get he users exact responses for record keeping
            $userresponse = array();
            foreach ($response as $key => $value) {
                foreach($answers as $answer) {
                    if ($value == $answer->response) {
                        $userresponse[] = $answer->id;
                    }
                }
                $studentanswer .= '<br />'.$answers[$key]->answer.' = '.$value;
            }
            $userresponse = implode(",", $userresponse);

            $response = '';
            if ($ncorrect == count($answers)-2) {  // dont count correct/wrong responses in the total.
                foreach ($answers as $answer) {
                    if ($answer->response == NULL && $answer->answer != NULL) {
                        $response = $answer->answer;
                        break;
                    }
                }
                if (isset($correctpageid)) {
                    $newpageid = $correctpageid;
                }
                if (isset($correctanswerid)) {
                    $answerid = $correctanswerid;
                }
                $correctanswer = true;
            } else {
                $t = 0;
                foreach ($answers as $answer) {
                    if ($answer->response == NULL && $answer->answer != NULL) {
                        if ($t == 1) {
                            $response = $answer->answer;
                            break;
                        }
                        $t++;
                    }
                }
                $newpageid = $wrongpageid;
                $answerid = $wronganswerid;
            }
            break;

        case LESSON_NUMERICAL :
            // set defaults
            $response = '';
            $newpageid = 0;

            if (isset($_POST['answer'])) {
                $useranswer = (float) optional_param('answer');  // just doing default PARAM_CLEAN, not doing PARAM_INT because it could be a float
            } else {
                $noanswer = true;
                break;
            }
            $studentanswer = $userresponse = $useranswer;
            if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                error("Continue: No answers found");
            }
            foreach ($answers as $answer) {
                if (strpos($answer->answer, ':')) {
                    // there's a pairs of values
                    list($min, $max) = explode(':', $answer->answer);
                    $minimum = (float) $min;
                    $maximum = (float) $max;
                } else {
                    // there's only one value
                    $minimum = (float) $answer->answer;
                    $maximum = $minimum;
                }
                if (($useranswer >= $minimum) and ($useranswer <= $maximum)) {
                    $newpageid = $answer->jumpto;
                    $response = trim($answer->response);
                    if (lesson_iscorrect($pageid, $newpageid)) {
                        $correctanswer = true;
                    }
                    if ($lesson->custom) {
                        if ($answer->score > 0) {
                            $correctanswer = true;
                        } else {
                            $correctanswer = false;
                        }
                    }
                    $answerid = $answer->id;
                    break;
                }
            }
            break;

        case LESSON_BRANCHTABLE:
            $noanswer = false;
            $newpageid = optional_param('jumpto', NULL, PARAM_INT);
            // going to insert into lesson_branch                
            if ($newpageid == LESSON_RANDOMBRANCH) {
                $branchflag = 1;
            } else {
                $branchflag = 0;
            }
            if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $USER->id",
                        "grade DESC")) {
                $retries = count($grades);
            } else {
                $retries = 0;
            }
            $branch = new stdClass;
            $branch->lessonid = $lesson->id;
            $branch->userid = $USER->id;
            $branch->pageid = $pageid;
            $branch->retry = $retries;
            $branch->flag = $branchflag;
            $branch->timeseen = time();
        
            if (!insert_record("lesson_branch", $branch)) {
                error("Error: could not insert row into lesson_branch table");
            }

            //  this is called when jumping to random from a branch table
            if($newpageid == LESSON_UNSEENBRANCHPAGE) {
                if (has_capability('mod/lesson:manage', $context)) {
                     $newpageid = LESSON_NEXTPAGE;
                } else {
                     $newpageid = lesson_unseen_question_jump($lesson->id, $USER->id, $pageid);  // this may return 0
                }
            }
            // convert jumpto page into a proper page id
            if ($newpageid == 0) {
                $newpageid = $pageid;
            } elseif ($newpageid == LESSON_NEXTPAGE) {
                if (!$newpageid = $page->nextpageid) {
                    // no nextpage go to end of lesson
                    $newpageid = LESSON_EOL;
                }
            } elseif ($newpageid == LESSON_PREVIOUSPAGE) {
                $newpageid = $page->prevpageid;
            } elseif ($newpageid == LESSON_RANDOMPAGE) {
                $newpageid = lesson_random_question_jump($lesson->id, $pageid);
            } elseif ($newpageid == LESSON_RANDOMBRANCH) {
                $newpageid = lesson_unseen_branch_jump($lesson->id, $USER->id);
            }
            // no need to record anything in lesson_attempts            
            redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=$newpageid");
            break;
        
    }

    $attemptsremaining  = 0;
    $maxattemptsreached = 0;
    $nodefaultresponse  = false; // Flag for redirecting when default feedback is turned off

    if ($noanswer) {
        $newpageid = $pageid; // display same page again
        $feedback  = get_string('noanswer', 'lesson');
    } else {
        $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
        if (!has_capability('mod/lesson:manage', $context)) {
            // record student's attempt
            $attempt = new stdClass;
            $attempt->lessonid = $lesson->id;
            $attempt->pageid = $pageid;
            $attempt->userid = $USER->id;
            $attempt->answerid = $answerid;
            $attempt->retry = $nretakes;
            $attempt->correct = $correctanswer;
            if(isset($userresponse)) {
                $attempt->useranswer = $userresponse;
            }
            
            $attempt->timeseen = time();
            // if allow modattempts, then update the old attempt record, otherwise, insert new answer record
            if (isset($USER->modattempts[$lesson->id])) {
                $attempt->retry = $nretakes - 1; // they are going through on review, $nretakes will be too high
            }
            if (!$newattemptid = insert_record("lesson_attempts", $attempt)) {
                error("Continue: attempt not inserted");
            }
            // "number of attempts remaining" message if $lesson->maxattempts > 1
            // displaying of message(s) is at the end of page for more ergonomic display
            if (!$correctanswer and ($newpageid == 0)) {
                // wrong answer and student is stuck on this page - check how many attempts 
                // the student has had at this page/question
                $nattempts = count_records("lesson_attempts", "pageid", $pageid, "userid", $USER->id,
                    "retry", $nretakes);
                
                // retreive the number of attempts left counter for displaying at bottom of feedback page
                if ($nattempts >= $lesson->maxattempts) {
                    if ($lesson->maxattempts > 1) { // don't bother with message if only one attempt
                        $maxattemptsreached = 1;
                    }
                    $newpageid = LESSON_NEXTPAGE;
                } else if ($lesson->maxattempts > 1) { // don't bother with message if only one attempt
                    $attemptsremaining = $lesson->maxattempts - $nattempts;
                }
            }
        }
        // TODO: merge this code with the jump code below.  Convert jumpto page into a proper page id
        if ($newpageid == 0) {
            $newpageid = $pageid;
        } elseif ($newpageid == LESSON_NEXTPAGE) {
            if ($lesson->nextpagedefault) {
                // in Flash Card mode...
                // ... first get the page ids (lessonid the 5th param is needed to make get_records play)
                $allpages = get_records("lesson_pages", "lessonid", $lesson->id, "id", "id,lessonid,qtype");
                shuffle ($allpages);
                $found = false;
                if ($lesson->nextpagedefault == LESSON_UNSEENPAGE) {
                    foreach ($allpages as $thispage) {
                        if (!count_records("lesson_attempts", "pageid", $thispage->id, "userid", 
                                    $USER->id, "retry", $nretakes)) {
                            $found = true;
                            break;
                        }
                    }
                } elseif ($lesson->nextpagedefault == LESSON_UNANSWEREDPAGE) {
                    foreach ($allpages as $thispage) {
                        if ($thispage->qtype == LESSON_ESSAY) {
                            if (!count_records_select("lesson_attempts", "pageid = $thispage->id AND
                                        userid = $USER->id AND retry = $nretakes")) {
                                $found = true;
                                break;
                            }
                        } else {                             
                            if (!count_records_select("lesson_attempts", "pageid = $thispage->id AND
                                        userid = $USER->id AND correct = 1 AND retry = $nretakes")) {
                                $found = true;
                                break;
                            }
                        }
                    }
                }
                if ($found) {
                    $newpageid = $thispage->id;
                    if ($lesson->maxpages) {
                        // check number of pages viewed (in the lesson)
                        if (count_records("lesson_attempts", "lessonid", $lesson->id, "userid", $USER->id,
                                "retry", $nretakes) >= $lesson->maxpages) {
                            $newpageid = LESSON_EOL;
                        }
                    }
                } else {
                    $newpageid = LESSON_EOL;
                }
            } elseif (!$newpageid = $page->nextpageid) {
                // no nextpage go to end of lesson
                $newpageid = LESSON_EOL;
            }
        }

        // Determine default feedback if necessary
        if (empty($response)) {
            if (!$lesson->feedback and !$noanswer and !($lesson->review and !$correctanswer and !$isessayquestion)) {
                // These conditions have been met:
                //  1. The lesson manager has not supplied feedback to the student
                //  2. Not displaying default feedback
                //  3. The user did provide an answer
                //  4. We are not reviewing with an incorrect answer (and not reviewing an essay question)
                
                $nodefaultresponse = true;  // This will cause a redirect below
            } else if ($isessayquestion) {
                $response = get_string('defaultessayresponse', 'lesson');
            } else if ($correctanswer) {
                $response = get_string('thatsthecorrectanswer', 'lesson');
            } else {
                $response = get_string('thatsthewronganswer', 'lesson');
            }
        }

        // display response (if there is one - there should be!)
        // display: lesson title, page title, question text, student's answer(s) before feedback message
        
        if ($response) {
            //optionally display question page title
            //if ($title = get_field("lesson_pages", "title", "id", $pageid)) {
            //    print_heading($title);
            //}
            if ($lesson->review and !$correctanswer and !$isessayquestion) {
                $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
                $qattempts = count_records("lesson_attempts", "userid", $USER->id, "retry", $nretakes, "pageid", $pageid);
                if ($qattempts == 1) {
                    $feedback = get_string("firstwrong", "lesson");
                } else {
                    $feedback = get_string("secondpluswrong", "lesson");
                }
            } else {
                if ($correctanswer) {
                    $class = 'response correct'; //CSS over-ride this if they exist (!important)
                } else if ($isessayquestion) {
                    $class = 'response';
                } else {
                    $class = 'response incorrect'; 
                }
                $options = new stdClass;
                $options->noclean = true;
                $options->para = true;
                $feedback = print_simple_box(format_text($page->contents, FORMAT_MOODLE, $options), 'center', '', '', 5, 'generalbox', '', true);
                $feedback .= '<em>'.get_string("youranswer", "lesson").'</em> : '.format_text($studentanswer, FORMAT_MOODLE, $options).
                                 "<div class=\"$class\">".format_text($response, FORMAT_MOODLE, $options).'</div>';
            }
        }
    }

    // TODO: merge with the jump code above.  This is where some jump numbers are interpreted
    if (isset($USER->modattempts[$lesson->id])) {
        // make sure if the student is reviewing, that he/she sees the same pages/page path that he/she saw the first time
        if ($USER->modattempts[$lesson->id] == $pageid) {  // remember, this session variable holds the pageid of the last page that the user saw
            $newpageid = LESSON_EOL;
        } else {
            $nretakes--; // make sure we are looking at the right try.
            $attempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $USER->id AND retry = $nretakes", "timeseen", "id, pageid");
            $found = false;
            $temppageid = 0;
            foreach($attempts as $attempt) {
                if ($found && $temppageid != $attempt->pageid) { // now try to find the next page, make sure next few attempts do no belong to current page
                    $newpageid = $attempt->pageid;
                    break;
                }
                if ($attempt->pageid == $pageid) {
                    $found = true; // if found current page
                    $temppageid = $attempt->pageid;
                }
            }
        }
    } elseif ($newpageid != LESSON_CLUSTERJUMP && $pageid != 0 && $newpageid > 0) {  // going to check to see if the page that the user is going to view next, is a cluster page.  If so, dont display, go into the cluster.  The $newpageid > 0 is used to filter out all of the negative code jumps.
        if (!$page = get_record("lesson_pages", "id", $newpageid)) {
            error("Error: could not find page");
        }
        if ($page->qtype == LESSON_CLUSTER) {
            $newpageid = lesson_cluster_jump($lesson->id, $USER->id, $page->id);
        } elseif ($page->qtype == LESSON_ENDOFCLUSTER) {
            $jump = get_field("lesson_answers", "jumpto", "pageid", $page->id, "lessonid", $lesson->id);
            if ($jump == LESSON_NEXTPAGE) {
                if ($page->nextpageid == 0) {
                    $newpageid = LESSON_EOL;
                } else {
                    $newpageid = $page->nextpageid;
                }
            } else {
                $newpageid = $jump;
            }
        }
    } elseif ($newpageid == LESSON_UNSEENBRANCHPAGE) {
        if (has_capability('mod/lesson:manage', $context)) {
            if ($page->nextpageid == 0) {
                $newpageid = LESSON_EOL;
            } else {
                $newpageid = $page->nextpageid;
            }
        } else {
            $newpageid = lesson_unseen_question_jump($lesson->id, $USER->id, $pageid);
        }            
    } elseif ($newpageid == LESSON_PREVIOUSPAGE) {
        $newpageid = $page->prevpageid;
    } elseif ($newpageid == LESSON_RANDOMPAGE) {
        $newpageid = lesson_random_question_jump($lesson->id, $pageid);
    } elseif ($newpageid == LESSON_CLUSTERJUMP) {
        if (has_capability('mod/lesson:manage', $context)) {
            if ($page->nextpageid == 0) {  // if teacher, go to next page
                $newpageid = LESSON_EOL;
            } else {
                $newpageid = $page->nextpageid;
            }            
        } else {
            $newpageid = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
        }
    }
    
    if ($nodefaultresponse) {
        // Don't display feedback
        redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=$newpageid");
    }
    
/// Set Messages

    // This is the warning msg for teachers to inform them that cluster and unseen does not work while logged in as a teacher
    if(has_capability('mod/lesson:manage', $context) and lesson_display_teacher_warning($lesson->id)) {
        $warningvars->cluster = get_string("clusterjump", "lesson");
        $warningvars->unseen = get_string("unseenpageinbranch", "lesson");
        lesson_set_message(get_string("teacherjumpwarning", "lesson", $warningvars));
    }
    // Inform teacher that s/he will not see the timer
    if ($lesson->timed and has_capability('mod/lesson:manage', $context)) {
        lesson_set_message(get_string("teachertimerwarning", "lesson"));
    }
    // Report attempts remaining
    if ($attemptsremaining != 0) {
        lesson_set_message(get_string('attemptsremaining', 'lesson', $attemptsremaining));
    }
    // Report if max attempts reached
    if ($maxattemptsreached != 0) { 
        lesson_set_message('('.get_string("maximumnumberofattemptsreached", "lesson").')');
    }

    $PAGE = page_create_object('mod-lesson-view', $lesson->id);
    $PAGE->set_lessonpageid($page->id);
    $pageblocks = blocks_setup($PAGE);

    $leftcolumnwidth  = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);
    $rightcolumnwidth = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);

/// Print the header, heading and tabs
    $PAGE->print_header();

    include(dirname(__FILE__).'/continue.html');
?>
