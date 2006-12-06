<?php // $Id$

/****************** continue ************************************/

    confirm_sesskey();

    // left menu code
    // check to see if the user can see the left menu
    if (!isteacher($course->id)) {
        $lesson->displayleft = lesson_displayleftif($lesson);
    }
    if ($lesson->displayleft) {
       if($firstpageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
            // print the pages
            echo '<table><tr valign="top"><td>';
            // skip navigation link
            echo '<a href="#maincontent" class="skip">'.get_string('skip', 'lesson').'</a>';
            echo "<div class=\"leftmenu_container\">\n";
                echo '<div class="leftmenu_title">'.get_string('lessonmenu', 'lesson')."</div>\n";
                echo "<div class=\"leftmenu_courselink\">\n";
                echo "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".get_string("mainmenu", "lesson")."</a>\n";
                echo "</div>\n";
                echo "<div class=\"leftmenu_links\">\n";
                lesson_print_tree_menu($lesson->id, $firstpageid, $cm->id);
                echo "</div>\n";
            echo "</div>\n";
            echo '</td><td align="center" width="100%">';
            // skip to anchor
            echo '<a name="maincontent" id="maincontent" title="'.get_string('anchortitle', 'lesson').'"></a>';
        }
    }

    // This is the warning msg for teachers to inform them that cluster and unseen does not work while logged in as a teacher
    if(isteacher($course->id)) {
        if (lesson_display_teacher_warning($lesson->id)) {
            $warningvars->cluster = get_string("clusterjump", "lesson");
            $warningvars->unseen = get_string("unseenpageinbranch", "lesson");
            echo "<p align=\"center\">".get_string("teacherjumpwarning", "lesson", $warningvars)."</p>";
        }
    }        

    // This is the code updates the lesson time for a timed test
    // get time information for this user
    if (!isteacher($course->id)) {
        if (!$timer = get_records_select('lesson_timer', "lessonid = $lesson->id AND userid = $USER->id", 'starttime')) {
            error('Error: could not find records');
        } else {
            $timer = array_pop($timer); // this will get the latest start time record
        }
    }
    $outoftime = false;
    if($lesson->timed) {
        if(isteacher($course->id)) {
            echo "<p align=\"center\">".get_string("teachertimerwarning", "lesson")."</p>";
        } else {
            if ((($timer->starttime + $lesson->maxtime * 60) - time()) > 0) {
                // code for the clock
                print_simple_box_start("right", "150px", "#ffffff", 0);
                echo "<table border=\"0\" valign=\"top\" align=\"center\" class=\"generaltable\" width=\"100%\" cellspacing=\"0\">".
                    "<tr><th valign=\"top\" class=\"generaltableheader\">".get_string("timeremaining", "lesson").
                    "</th></tr><tr><td align=\"center\" class=\"generaltablecell\">";
                echo "<script language=\"javascript\">\n";
                    echo "var starttime = ". $timer->starttime . ";\n";
                    echo "var servertime = ". time() . ";\n";
                    echo "var testlength = ". $lesson->maxtime * 60 .";\n";
                    echo "document.write('<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"timer.js\"><\/SCRIPT>');\n";
                    echo "window.onload = function () { show_clock(); }\n";
                echo "</script>\n";
                echo "</td></tr></table>";
                print_simple_box_end();
                echo "<br /><br /><br /><br />";
            } else {
                redirect("view.php?id=$cm->id&action=navigation&pageid=".LESSON_EOL."&outoftime=normal", get_string("outoftime", "lesson"));
            }
            if ((($timer->starttime + $lesson->maxtime * 60) - time()) < 60 && !((($timer->starttime + $lesson->maxtime * 60) - time()) < 0)) {
                echo "<p align=\"center\">".get_string("studentoneminwarning", "lesson")."</p>";
            } elseif (($timer->starttime + $lesson->maxtime * 60) < time()) {
                echo "<p align=\"center\">".get_string("studentoutoftime", "lesson")."</p>";
                $outoftime = true;
            }
        }
    }
    // update the clock
    if (!isteacher($course->id)) {
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
            $useranswer = clean_param($useranswer, PARAM_CLEAN);
        
            if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                error("Continue: No answers found");
            }
            $correctanswer = false;
            $response = get_string('defaultessayresponse', 'lesson');
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
            
            $studentanswer = $useranswer;
            break;
         case LESSON_SHORTANSWER :
            if (!$useranswer = $_POST['answer']) {
                $noanswer = true;
                break;
            }            
            $useranswer = stripslashes(clean_param($useranswer, PARAM_CLEAN));
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
                if (lesson_iscorrect($pageid, $answer->jumpto) or ($lesson->custom && $answer->score > 0) ) {
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
            if (!isset($response)) { //if no feedback message provided, use default message
                if ($correctanswer) {
                    $response = get_string("thatsthecorrectanswer", "lesson");
                } else {
                    $response = get_string("thatsthewronganswer", "lesson");
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
            if (!$response = trim($answer->response)) {
                if ($correctanswer) {
                    $response = get_string("thatsthecorrectanswer", "lesson");
                } else {
                    $response = get_string("thatsthewronganswer", "lesson");
                }
            }
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
                            // ...and from the incorrect ones, don't know which to use at this stage
                            if (trim(strip_tags($answer->response))) {
                                $wrongresponse = $answer->response;
                            }
                        }
                    }
                }
                if ((count($useranswers) == $ncorrect) and ($nhits == $ncorrect)) {
                    $correctanswer = true;
                    if (!$response = $correctresponse) {
                        $response = get_string("thatsthecorrectanswer", "lesson");
                    }
                    $newpageid = $correctpageid;
                } else {
                    if (!$response = $wrongresponse) {
                        $response = get_string("thatsthewronganswer", "lesson");
                    }
                    $newpageid = $wrongpageid;
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
                if (!$response = trim($answer->response)) {
                    if ($correctanswer) {
                        $response = get_string("thatsthecorrectanswer", "lesson");
                    } else {
                        $response = get_string("thatsthewronganswer", "lesson");
                    }
                }
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

            if ($ncorrect == count($answers)-2) {  // dont count correct/wrong responses in the total.
                $response = get_string("thatsthecorrectanswer", "lesson");
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
                $response = get_string("thatsthewronganswer", "lesson");
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
            $userresponse = $useranswer;
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
            if ($correctanswer) {
                if (!$response) {
                    $response = get_string("thatsthecorrectanswer", "lesson");
                }
            } else {
                if (!$response) {
                    $response = get_string("thatsthewronganswer", "lesson");
                }
            }
            
            $studentanswer = $userresponse;
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
                if (isteacher($course->id)) {
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
            redirect("view.php?id=$cm->id&amp;action=navigation&amp;pageid=$newpageid", '', 0);
            print_footer($course);
            exit();
            break;
        
    }

    $attemptsremaining = 0;
    $maxattemptsreached = 0;

    if ($noanswer) {
        $newpageid = $pageid; // display same page again
        print_simple_box(get_string("noanswer", "lesson"), "center");
    } else {
        $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
        if (isstudent($course->id)) {
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
            // dont want to insert the attempt if they ran out of time
            if (!$outoftime) {
                // if allow modattempts, then update the old attempt record, otherwise, insert new answer record
                if (isset($USER->modattempts[$lesson->id])) {
                    $attempt->retry = $nretakes - 1; // they are going through on review, $nretakes will be too high
                }
                if (!$newattemptid = insert_record("lesson_attempts", $attempt)) {
                    error("Continue: attempt not inserted");
                }
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
        // convert jumpto page into a proper page id
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
    
        // This calculates and prints the ongoing score message
        if ($lesson->ongoing) {
            lesson_print_ongoing_score($lesson);
        }

        // display response (if there is one - there should be!)
        // display: lesson title, page title, question text, student's answer(s) before feedback message
        if ($response) {
            //optionally display question page title
            //if ($title = get_field("lesson_pages", "title", "id", $pageid)) {
            //    print_heading($title);
            //}
            echo "<table width=\"80%\" border=\"0\" align=\"center\"><tr><td>\n";
            if ($lesson->review && !$correctanswer && !$isessayquestion) {
                $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
                $qattempts = count_records("lesson_attempts", "userid", $USER->id, "retry", $nretakes, "pageid", $pageid);
                echo "<br /><br />";
                if ($qattempts == 1) {
                    print_simple_box(get_string("firstwrong", "lesson"), "center");
                } else {
                    print_simple_box(get_string("secondpluswrong", "lesson"), "center");
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
                $options->para = false;
                print_simple_box(format_text($page->contents, FORMAT_MOODLE, $options), 'center');
                echo '<br />';
                print_simple_box('<em>'.get_string("youranswer", "lesson").'</em> : '.format_text($studentanswer, FORMAT_MOODLE, $options).
                                 "<div class=\"$class\">".format_text($response, FORMAT_MOODLE, $options), 'center').'</div>';
            }
            echo "</td></tr></table>\n";
        }
    }

    // this is where some jump numbers are interpreted
    if($outoftime) {
        $newpageid = LESSON_EOL;  // ran out of time for the test, so go to eol
    } elseif (isset($USER->modattempts[$lesson->id])) {
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
        if (isteacher($course->id)) {
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
        if (isteacher($course->id)) {
            if ($page->nextpageid == 0) {  // if teacher, go to next page
                $newpageid = LESSON_EOL;
            } else {
                $newpageid = $page->nextpageid;
            }            
        } else {
            $newpageid = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
        }
    }

    // NOTE:  Should this code be coverted from form/javascript to just longer links with the variables in the url?
    echo "<form name=\"pageform\" method =\"post\" action=\"view.php\">\n";
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
    echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
    echo "<input type=\"hidden\" name=\"pageid\" value=\"$newpageid\">\n";

    if (isset($USER->modattempts[$lesson->id])) {
        echo "<p align=\"center\">".
            get_string("savechangesandeol", "lesson")."<br /><br />".
            "<div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.pageform.pageid.value=".LESSON_EOL.";document.pageform.submit();\">".
            get_string("savechanges", "lesson")."</a></div></p>\n";
        echo "<p align=\"center\">".get_string("or", "lesson")."<br /><br />".
            get_string("continuetoanswer", "lesson")."</p>\n";
    }

    if ($lesson->review && !$correctanswer && !$noanswer && !$isessayquestion) {
        echo "<p><div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.pageform.pageid.value=$pageid;document.pageform.submit();\">".
            get_string("reviewquestionback", "lesson")."</a></div></p>\n";
        echo "<p><div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.pageform.submit();\">".
            get_string("reviewquestioncontinue", "lesson")."</a></div></p>\n";
    } else {
        echo "<p><div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.pageform.submit();\">".
            get_string("continue", "lesson")."</a></div></p>\n";
    }
    echo "</form>\n";
    
/// Report attempts remaining
    if ($attemptsremaining != 0) {
        echo "<p align=\"center\">".get_string('attemptsremaining', 'lesson', $attemptsremaining);
    }
/// Report if max attempts reached
    if ($maxattemptsreached != 0) {
        echo "<p align=\"center\">(".get_string("maximumnumberofattemptsreached", "lesson").")</p>\n";
    }

    if ($lesson->displayleft) {
        echo "</td></tr></table>";
    }
    
?>
