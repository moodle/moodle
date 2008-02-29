<?php  // $Id$
/**
 * Displays the lesson statistics.
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once('lib.php');
    require_once('locallib.php');

    $id     = required_param('id', PARAM_INT);    // Course Module ID
    $pageid = optional_param('pageid', NULL, PARAM_INT);    // Lesson Page ID
    $action = optional_param('action', 'reportoverview', PARAM_ALPHA);  // action to take
    $nothingtodisplay = false;

    list($cm, $course, $lesson) = lesson_get_basics($id);
    
    if (!empty($CFG->enablegroupings) && !empty($cm->groupingid)) {
        $sql = "SELECT DISTINCT u.*
                FROM {$CFG->prefix}lesson_attempts a 
                    INNER JOIN {$CFG->prefix}user u ON u.id = a.userid
                    INNER JOIN {$CFG->prefix}groups_members gm ON gm.userid = u.id
                    INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = {$cm->groupingid}
                WHERE a.lessonid = '$lesson->id'
                ORDER BY u.lastname";
    } else {
        $sql = "SELECT u.*
                FROM {$CFG->prefix}user u,
                     {$CFG->prefix}lesson_attempts a
                WHERE a.lessonid = '$lesson->id' and
                      u.id = a.userid
                ORDER BY u.lastname";
    }
    
    if (! $students = get_records_sql($sql)) {
        $nothingtodisplay = true;
    }
    
// make sure people are where they should be
    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/lesson:manage', $context);

/// Process any form data before fetching attempts, grades and times
    if (has_capability('mod/lesson:edit', $context) and 
        $form = data_submitted($CFG->wwwroot.'/mod/lesson/report.php') and 
        confirm_sesskey()) {
    /// Cycle through array of userids with nested arrays of tries
        if (!empty($form->attempts)) {
            foreach ($form->attempts as $userid => $tries) {
                // Modifier IS VERY IMPORTANT!  What does it do?
                //      Well, it is for when you delete multiple attempts for the same user.
                //      If you delete try 1 and 3 for a user, then after deleting try 1, try 3 then
                //      becomes try 2 (because try 1 is gone and all tries after try 1 get decremented).
                //      So, the modifier makes sure that the submitted try refers to the current try in the
                //      database - hope this all makes sense :)
                $modifier = 0;
            
                foreach ($tries as $try => $junk) {
                    $try -= $modifier;
                
                /// Clean up the timer table
                    $timeid = get_field_sql("SELECT id FROM {$CFG->prefix}lesson_timer 
                                             WHERE userid = $userid AND lessonid = $lesson->id 
                                             ORDER BY starttime", $try, 1);
                
                    delete_records('lesson_timer', 'id', $timeid);
            
                /// Remove the grade from the grades and high_scores tables
                    $gradeid = get_field_sql("SELECT id FROM {$CFG->prefix}lesson_grades 
                                              WHERE userid = $userid AND lessonid = $lesson->id 
                                              ORDER BY completed", $try, 1);
                
                    delete_records('lesson_grades', 'id', $gradeid);
                    delete_records('lesson_high_scores', 'gradeid', $gradeid, 'lessonid', $lesson->id, 'userid', $userid);
            
                /// Remove attempts and update the retry number
                    delete_records('lesson_attempts', 'userid', $userid, 'lessonid', $lesson->id, 'retry', $try);
                    execute_sql("UPDATE {$CFG->prefix}lesson_attempts SET retry = retry - 1 WHERE userid = $userid AND lessonid = $lesson->id AND retry > $try", false);
            
                /// Remove seen branches and update the retry number    
                    delete_records('lesson_branch', 'userid', $userid, 'lessonid', $lesson->id, 'retry', $try);
                    execute_sql("UPDATE {$CFG->prefix}lesson_branch SET retry = retry - 1 WHERE userid = $userid AND lessonid = $lesson->id AND retry > $try", false);

                /// update central gradebook
                    lesson_update_grades($lesson, $userid);

                    $modifier++;
                }
            }
            lesson_set_message(get_string('attemptsdeleted', 'lesson'), 'notifysuccess');
        }
    }

    if (! $attempts = get_records('lesson_attempts', 'lessonid', $lesson->id, 'timeseen')) {
        $nothingtodisplay = true;
    }

    if (! $grades = get_records('lesson_grades', 'lessonid', $lesson->id, 'completed')) {
        $grades = array();
    }
    
    if (! $times = get_records('lesson_timer', 'lessonid', $lesson->id, 'starttime')) {
        $times = array();
    }

    lesson_print_header($cm, $course, $lesson, $action);
    
    $course_context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (has_capability('gradereport/grader:view', $course_context) && has_capability('moodle/grade:viewall', $course_context)) {
        echo '<div class="allcoursegrades"><a href="' . $CFG->wwwroot . '/grade/report/grader/index.php?id=' . $course->id . '">' 
            . get_string('seeallcoursegrades', 'grades') . '</a></div>';
    }

    if ($nothingtodisplay) {
        notify(get_string('nolessonattempts', 'lesson'));
        print_footer($course);
        exit();
    }

    /**************************************************************************
    this action is for default view and overview view
    **************************************************************************/
    if ($action == 'reportoverview') {
        $studentdata = array();

        // build an array for output
        foreach ($attempts as $attempt) {
            // if the user is not in the array or if the retry number is not in the sub array, add the data for that try.
            if (!array_key_exists($attempt->userid, $studentdata) || !array_key_exists($attempt->retry, $studentdata[$attempt->userid])) {
                // restore/setup defaults
                $n = 0;
                $timestart = 0;
                $timeend = 0;
                $usergrade = NULL;

                // search for the grade record for this try. if not there, the nulls defined above will be used.
                foreach($grades as $grade) {
                    // check to see if the grade matches the correct user
                    if ($grade->userid == $attempt->userid) {
                        // see if n is = to the retry
                        if ($n == $attempt->retry) {
                            // get grade info
                            $usergrade = round($grade->grade, 2); // round it here so we only have to do it once
                            break;
                        }
                        $n++; // if not equal, then increment n
                    }
                }
                $n = 0;
                // search for the time record for this try. if not there, the nulls defined above will be used.
                foreach($times as $time) {
                    // check to see if the grade matches the correct user
                    if ($time->userid == $attempt->userid) {
                        // see if n is = to the retry
                        if ($n == $attempt->retry) {
                            // get grade info
                            $timeend = $time->lessontime;
                            $timestart = $time->starttime;
                            break;
                        }
                        $n++; // if not equal, then increment n
                    }
                }

                // build up the array.
                // this array represents each student and all of their tries at the lesson
                $studentdata[$attempt->userid][$attempt->retry] = array( "timestart" => $timestart,
                                                                        "timeend" => $timeend,
                                                                        "grade" => $usergrade,
                                                                        "try" => $attempt->retry,
                                                                        "userid" => $attempt->userid);
            }
        }
        // set all the stats variables
        $numofattempts = 0;
        $avescore      = 0;
        $avetime       = 0;
        $highscore     = NULL;
        $lowscore      = NULL;
        $hightime      = NULL;
        $lowtime       = NULL;
        $table         = new stdClass;

        // set up the table object
        $table->head = array(get_string('studentname', 'lesson', $course->student), get_string('attempts', 'lesson'), get_string('highscore', 'lesson'));
        $table->align = array("center", "left", "left");
        $table->wrap = array("nowrap", "nowrap", "nowrap");
        $table->width = "90%";
        $table->size = array("*", "70%", "*");

        // print out the $studentdata array
        // going through each student that has attempted the lesson, so, each student should have something to be displayed
        foreach ($students as $student) {
            // check to see if the student has attempts to print out
            if (array_key_exists($student->id, $studentdata)) {
                // set/reset some variables
                $attempts = array();
                // gather the data for each user attempt
                $bestgrade = 0;
                $bestgradefound = false;
                // $tries holds all the tries/retries a student has done
                $tries = $studentdata[$student->id];
                $studentname = "{$student->lastname},&nbsp;$student->firstname";
                foreach ($tries as $try) {
                // start to build up the checkbox and link
                    if (has_capability('mod/lesson:edit', $context)) {
                        $temp = '<input type="checkbox" id="attempts" name="attempts['.$try['userid'].']['.$try['try'].']" /> ';
                    } else {
                        $temp = '';
                    }
                    
                    $temp .= "<a href=\"report.php?id=$cm->id&amp;action=reportdetail&amp;userid=".$try['userid'].'&amp;try='.$try['try'].'">';
                    if ($try["grade"] !== NULL) { // if NULL then not done yet
                        // this is what the link does when the user has completed the try
                        $timetotake = $try["timeend"] - $try["timestart"];

                        $temp .= $try["grade"]."%";
                        $bestgradefound = true;
                        if ($try["grade"] > $bestgrade) {
                            $bestgrade = $try["grade"];
                        }
                        $temp .= "&nbsp;".userdate($try["timestart"]);
                        $temp .= ",&nbsp;(".format_time($timetotake).")</a>";
                    } else {
                        // this is what the link does/looks like when the user has not completed the try
                        $temp .= get_string("notcompleted", "lesson");
                        $temp .= "&nbsp;".userdate($try["timestart"])."</a>";
                        $timetotake = NULL;
                    }
                    // build up the attempts array
                    $attempts[] = $temp;

                    // run these lines for the stats only if the user finnished the lesson
                    if ($try["grade"] !== NULL) {
                        $numofattempts++;
                        $avescore += $try["grade"];
                        $avetime += $timetotake;
                        if ($try["grade"] > $highscore || $highscore == NULL) {
                            $highscore = $try["grade"];
                        }
                        if ($try["grade"] < $lowscore || $lowscore == NULL) {
                            $lowscore = $try["grade"];
                        }
                        if ($timetotake > $hightime || $hightime == NULL) {
                            $hightime = $timetotake;
                        }
                        if ($timetotake < $lowtime || $lowtime == NULL) {
                            $lowtime = $timetotake;
                        }
                    }
                }
                // get line breaks in after each attempt
                $attempts = implode("<br />\n", $attempts);
                // add it to the table data[] object
                $table->data[] = array($studentname, $attempts, $bestgrade."%");
            }
        }
        // print it all out !
        if (has_capability('mod/lesson:edit', $context)) {
            echo  "<form id=\"theform\" method=\"post\" action=\"report.php\">\n
                   <input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />\n
                   <input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n
                   <input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
        }
        print_table($table);
        
        if (has_capability('mod/lesson:edit', $context)) {
            echo '<br /><table width="90%" align="center"><tr><td>'.
                 '<a href="javascript: checkall();">'.get_string('selectall').'</a> / '.
                 '<a href="javascript: checknone();">'.get_string('deselectall').'</a> ';
             
            $options = array();
            $options['delete'] = get_string('deleteselected');
            choose_from_menu($options, 'attemptaction', 0, 'choose', 'submitFormById(\'theform\')');
        
            echo '</td></tr></table></form>';
        }
        
        // some stat calculations
        if ($numofattempts == 0) {
            $avescore = get_string("notcompleted", "lesson");
        } else {
            $avescore = format_float($avescore/$numofattempts, 2);
        }
        if ($avetime == NULL) {
            $avetime = get_string("notcompleted", "lesson");
        } else {
            $avetime = format_float($avetime/$numofattempts, 0);
            $avetime = format_time($avetime);
        }
        if ($hightime == NULL) {
            $hightime = get_string("notcompleted", "lesson");
        } else {
            $hightime = format_time($hightime);
        }
        if ($lowtime == NULL) {
            $lowtime = get_string("notcompleted", "lesson");
        } else {
            $lowtime = format_time($lowtime);
        }
        if ($highscore == NULL) {
            $highscore = get_string("notcompleted", "lesson");
        }
        if ($lowscore == NULL) {
            $lowscore = get_string("notcompleted", "lesson");
        }

        // output the stats
        print_heading(get_string('lessonstats', 'lesson'));
        $stattable = new stdClass;
        $stattable->head = array(get_string('averagescore', 'lesson'), get_string('averagetime', 'lesson'),
                                get_string('highscore', 'lesson'), get_string('lowscore', 'lesson'),
                                get_string('hightime', 'lesson'), get_string('lowtime', 'lesson'));
        $stattable->align = array("center", "center", "center", "center", "center", "center");
        $stattable->wrap = array("nowrap", "nowrap", "nowrap", "nowrap", "nowrap", "nowrap");
        $stattable->width = "90%";
        $stattable->data[] = array($avescore.'%', $avetime, $highscore.'%', $lowscore.'%', $hightime, $lowtime);

        print_table($stattable);
}
    /**************************************************************************
    this action is for a student detailed view and for the general detailed view

        General flow of this section of the code
        1.  Generate a object which holds values for the statistics for each question/answer
        2.  Cycle through all the pages to create a object.  Foreach page, see if the student actually answered
            the page.  Then process the page appropriatly.  Display all info about the question,
            Highlight correct answers, show how the user answered the question, and display statistics
            about each page
        3.  Print out info about the try (if needed)
        4.  Print out the object which contains all the try info

    **************************************************************************/
    else if ($action == 'reportdetail') {

        $formattextdefoptions = new stdClass;
        $formattextdefoptions->para = false;  //I'll use it widely in this page

        $userid = optional_param('userid', NULL, PARAM_INT); // if empty, then will display the general detailed view
        $try    = optional_param('try', NULL, PARAM_INT);

        if (! $lessonpages = get_records("lesson_pages", "lessonid", $lesson->id)) {
            error("Could not find Lesson Pages");
        }
        if (! $pageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Could not find first page");
        }

        // now gather the stats into an object
        $firstpageid = $pageid;
        $pagestats = array();
        while ($pageid != 0) { // EOL
            $page = $lessonpages[$pageid];

            if ($allanswers = get_records_select("lesson_attempts", "lessonid = $lesson->id AND pageid = $page->id", "timeseen")) {
                // get them ready for processing
                $orderedanswers = array();
                foreach ($allanswers as $singleanswer) {
                    // ordering them like this, will help to find the single attempt record that we want to keep.
                    $orderedanswers[$singleanswer->userid][$singleanswer->retry][] = $singleanswer;
                }
                // this is foreach user and for each try for that user, keep one attempt record
                foreach ($orderedanswers as $orderedanswer) {
                    foreach($orderedanswer as $tries) {
                        if(count($tries) > $lesson->maxattempts) { // if there are more tries than the max that is allowed, grab the last "legal" attempt
                            $temp = $tries[$lesson->maxattempts - 1];
                        } else {
                            // else, user attempted the question less than the max, so grab the last one
                            $temp = end($tries);
                        }
                        // page interpretation
                        // depending on the page type, process stat info for that page
                        switch ($page->qtype) {
                            case LESSON_MULTICHOICE:
                            case LESSON_TRUEFALSE:
                                if ($page->qoption) {
                                    $userresponse = explode(",", $temp->useranswer);
                                    foreach ($userresponse as $response) {
                                        if (isset($pagestats[$temp->pageid][$response])) {
                                            $pagestats[$temp->pageid][$response]++;
                                        } else {
                                            $pagestats[$temp->pageid][$response] = 1;
                                        }
                                    }
                                } else {
                                    if (isset($pagestats[$temp->pageid][$temp->answerid])) {
                                        $pagestats[$temp->pageid][$temp->answerid]++;
                                    } else {
                                        $pagestats[$temp->pageid][$temp->answerid] = 1;
                                    }
                                }
                                if (isset($pagestats[$temp->pageid]["total"])) {
                                    $pagestats[$temp->pageid]["total"]++;
                                } else {
                                    $pagestats[$temp->pageid]["total"] = 1;
                                }
                                break;
                            case LESSON_SHORTANSWER:
                            case LESSON_NUMERICAL:
                                if (isset($pagestats[$temp->pageid][$temp->useranswer])) {
                                    $pagestats[$temp->pageid][$temp->useranswer]++;
                                } else {
                                    $pagestats[$temp->pageid][$temp->useranswer] = 1;
                                }
                                if (isset($pagestats[$temp->pageid]["total"])) {
                                    $pagestats[$temp->pageid]["total"]++;
                                } else {
                                    $pagestats[$temp->pageid]["total"] = 1;
                                }
                                break;
                            case LESSON_MATCHING:
                                if ($temp->correct) {
                                    if (isset($pagestats[$temp->pageid]["correct"])) {
                                        $pagestats[$temp->pageid]["correct"]++;
                                    } else {
                                        $pagestats[$temp->pageid]["correct"] = 1;
                                    }
                                }
                                if (isset($pagestats[$temp->pageid]["total"])) {
                                    $pagestats[$temp->pageid]["total"]++;
                                } else {
                                    $pagestats[$temp->pageid]["total"] = 1;
                                }
                                break;
                            case LESSON_ESSAY:
                                $essayinfo = unserialize($temp->useranswer);
                                if ($essayinfo->graded) {
                                    if (isset($pagestats[$temp->pageid])) {
                                        $essaystats = $pagestats[$temp->pageid];
                                        $essaystats->totalscore += $essayinfo->score;
                                        $essaystats->total++;
                                        $pagestats[$temp->pageid] = $essaystats;
                                    } else {
                                        $essaystats->totalscore = $essayinfo->score;
                                        $essaystats->total = 1;
                                        $pagestats[$temp->pageid] = $essaystats;
                                    }
                                }
                                break;
                        }
                    }
                }

            } else {
                // no one answered yet...
            }
            //unset($orderedanswers);  initialized above now
            $pageid = $page->nextpageid;
        }



        $answerpages = array();
        $answerpage = "";
        $pageid = $firstpageid;
        // cycle through all the pages
        //  foreach page, add to the $answerpages[] array all the data that is needed
        //  from the question, the users attempt, and the statistics
        // grayout pages that the user did not answer and Branch, end of branch, cluster
        // and end of cluster pages
        while ($pageid != 0) { // EOL
            $page = $lessonpages[$pageid];
            $answerpage = new stdClass;
            $data ='';
            $answerdata = new stdClass;
            
            $answerpage->title = format_string($page->title);
            
            $options = new stdClass;
            $options->noclean = true;
            $answerpage->contents = format_text($page->contents, FORMAT_MOODLE, $options);

            // get the page qtype
            switch ($page->qtype) {
                case LESSON_ESSAY :
                case LESSON_MATCHING :
                case LESSON_TRUEFALSE :
                case LESSON_NUMERICAL :
                    $answerpage->qtype = $LESSON_QUESTION_TYPE[$page->qtype];
                    $answerpage->grayout = 0;
                    break;
                case LESSON_SHORTANSWER :
                    $answerpage->qtype = $LESSON_QUESTION_TYPE[$page->qtype];
                    if ($page->qoption) {
                        $answerpage->qtype .= " - ".get_string("casesensitive", "lesson");
                    }
                    $answerpage->grayout = 0;
                    break;
                case LESSON_MULTICHOICE :
                    $answerpage->qtype = $LESSON_QUESTION_TYPE[$page->qtype];
                    if ($page->qoption) {
                        $answerpage->qtype .= " - ".get_string("multianswer", "lesson");
                    }
                    $answerpage->grayout = 0;
                    break;
                case LESSON_BRANCHTABLE :
                    $answerpage->qtype = get_string("branchtable", "lesson");
                    $answerpage->grayout = 1;
                    break;
                case LESSON_ENDOFBRANCH :
                    $answerpage->qtype = get_string("endofbranch", "lesson");
                    $answerpage->grayout = 1;
                    break;
                case LESSON_CLUSTER :
                    $answerpage->qtype = get_string("clustertitle", "lesson");
                    $answerpage->grayout = 1;
                    break;
                case LESSON_ENDOFCLUSTER :
                    $answerpage->qtype = get_string("endofclustertitle", "lesson");
                    $answerpage->grayout = 1;
                    break;
            }


            if (empty($userid)) {
                // there is no userid, so set these vars and display stats.
                $answerpage->grayout = 0;
                $useranswer = NULL;
                $answerdata->score = NULL;
                $answerdata->response = NULL;
            } elseif ($useranswers = get_records_select("lesson_attempts",
                                                         "lessonid = $lesson->id AND userid = $userid AND retry = $try AND pageid = $page->id", "timeseen")) {
                                                         // get the user's answer for this page
                // need to find the right one
                $i = 0;
                foreach ($useranswers as $userattempt) {
                    $useranswer = $userattempt;
                    $i++;
                    if ($lesson->maxattempts == $i) {
                        break; // reached maxattempts, break out
                    }
                }
            } else {
                // user did not answer this page, gray it out and set some nulls
                $answerpage->grayout = 1;
                $useranswer = NULL;
                $answerdata->score = NULL;
                $answerdata->response = NULL;

            }
            // build up the answer data
            if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                $i = 0;
                $n = 0;
                // go through each answer and display it properly with statistics, highlight if correct answer,
                // and display what the user entered
                foreach ($answers as $answer) {
                    switch ($page->qtype) {
                        case LESSON_MULTICHOICE:
                        case LESSON_TRUEFALSE:
                            if ($page->qoption) {
                                if ($useranswer == NULL) {
                                    $userresponse = array();
                                } else {
                                    $userresponse = explode(",", $useranswer->useranswer);
                                }
                                if (in_array($answer->id, $userresponse)) {
                                    // make checked
                                    $data = "<input  readonly=\"readonly\" disabled=\"disabled\" name=\"answer[$i]\" checked=\"checked\" type=\"checkbox\" value=\"1\" />";
                                    if (!isset($answerdata->response)) {
                                        if ($answer->response == NULL) {
                                            if ($useranswer->correct) {
                                                $answerdata->response = get_string("thatsthecorrectanswer", "lesson");
                                            } else {
                                                $answerdata->response = get_string("thatsthewronganswer", "lesson");
                                            }
                                        } else {
                                            $answerdata->response = $answer->response;
                                        }
                                    }
                                    if (!isset($answerdata->score)) {
                                        if ($lesson->custom) {
                                            $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                                        } elseif ($useranswer->correct) {
                                            $answerdata->score = get_string("receivedcredit", "lesson");
                                        } else {
                                            $answerdata->score = get_string("didnotreceivecredit", "lesson");
                                        }
                                    }
                                } else {
                                    // unchecked
                                    $data = "<input type=\"checkbox\" readonly=\"readonly\" name=\"answer[$i]\" value=\"0\" disabled=\"disabled\" />";
                                }
                                if (($answer->score > 0 && $lesson->custom) || (lesson_iscorrect($page->id, $answer->jumpto) && !$lesson->custom)) {
                                    $data .= "<font class=highlight>".format_text($answer->answer,FORMAT_MOODLE,$formattextdefoptions)."</font>";
                                } else {
                                    $data .= format_text($answer->answer,FORMAT_MOODLE,$formattextdefoptions);
                                }
                            } else {
                                if ($useranswer != NULL and $answer->id == $useranswer->answerid) {
                                    // make checked
                                    $data = "<input  readonly=\"readonly\" disabled=\"disabled\" name=\"answer[$i]\" checked=\"checked\" type=\"checkbox\" value=\"1\" />";
                                    if ($answer->response == NULL) {
                                        if ($useranswer->correct) {
                                            $answerdata->response = get_string("thatsthecorrectanswer", "lesson");
                                        } else {
                                            $answerdata->response = get_string("thatsthewronganswer", "lesson");
                                        }
                                    } else {
                                        $answerdata->response = $answer->response;
                                    }
                                    if ($lesson->custom) {
                                        $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                                    } elseif ($useranswer->correct) {
                                        $answerdata->score = get_string("receivedcredit", "lesson");
                                    } else {
                                        $answerdata->score = get_string("didnotreceivecredit", "lesson");
                                    }
                                } else {
                                    // unchecked
                                    $data = "<input type=\"checkbox\" readonly=\"readonly\" name=\"answer[$i]\" value=\"0\" disabled=\"disabled\" />";
                                }
                                if (($answer->score > 0 && $lesson->custom) || (lesson_iscorrect($page->id, $answer->jumpto) && !$lesson->custom)) {
                                    $data .= "<font class=\"highlight\">".format_text($answer->answer,FORMAT_MOODLE,$formattextdefoptions)."</font>";
                                } else {
                                    $data .= format_text($answer->answer,FORMAT_MOODLE,$formattextdefoptions);
                                }
                            }
                            if (isset($pagestats[$page->id][$answer->id])) {
                                $percent = $pagestats[$page->id][$answer->id] / $pagestats[$page->id]["total"] * 100;
                                $percent = round($percent, 2);
                                $percent .= "% ".get_string("checkedthisone", "lesson");
                            } else {
                                $percent = get_string("noonecheckedthis", "lesson");
                            }

                            $answerdata->answers[] = array($data, $percent);
                            break;
                        case LESSON_SHORTANSWER:
                        case LESSON_NUMERICAL:
                            if ($useranswer == NULL && $i == 0) {
                                // I have the $i == 0 because it is easier to blast through it all at once.
                                if (isset($pagestats[$page->id])) {
                                    $stats = $pagestats[$page->id];
                                    $total = $stats["total"];
                                    unset($stats["total"]);
                                    foreach ($stats as $valentered => $ntimes) {
                                        $data = '<input type="text" size="50" disabled="disabled" readonly="readonly" value="'.s($valentered).'" />';
                                        $percent = $ntimes / $total * 100;
                                        $percent = round($percent, 2);
                                        $percent .= "% ".get_string("enteredthis", "lesson");
                                        $answerdata->answers[] = array($data, $percent);
                                    }
                                } else {
                                    $answerdata->answers[] = array(get_string("nooneansweredthisquestion", "lesson"), " ");
                                }
                                $i++;
                            } else if ($useranswer != NULL and $answer->id == $useranswer->answerid) {
                                // get in here when a user answer matches one of the answers to the page
                                $data = '<input type="text" size="50" disabled="disabled" readonly="readonly" value="'.s($useranswer->useranswer).'">';
                                if (isset($pagestats[$page->id][$useranswer->useranswer])) {
                                    $percent = $pagestats[$page->id][$useranswer->useranswer] / $pagestats[$page->id]["total"] * 100;
                                    $percent = round($percent, 2);
                                    $percent .= "% ".get_string("enteredthis", "lesson");
                                } else {
                                    $percent = get_string("nooneenteredthis", "lesson");
                                }
                                $answerdata->answers[] = array($data, $percent);

                                if ($answer->response == NULL) {
                                    if ($useranswer->correct) {
                                        $answerdata->response = get_string("thatsthecorrectanswer", "lesson");
                                    } else {
                                        $answerdata->response = get_string("thatsthewronganswer", "lesson");
                                    }
                                } else {
                                    $answerdata->response = $answer->response;
                                }
                                if ($lesson->custom) {
                                    $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                                } elseif ($useranswer->correct) {
                                    $answerdata->score = get_string("receivedcredit", "lesson");
                                } else {
                                    $answerdata->score = get_string("didnotreceivecredit", "lesson");
                                }
                            } elseif ($answer == end($answers) && empty($answerdata) && $useranswer != NULL) {
                                // get in here when what the user entered is not one of the answers
                                $data = '<input type="text" size="50" disabled="disabled" readonly="readonly" value="'.s($useranswer->useranswer).'">';
                                if (isset($pagestats[$page->id][$useranswer->useranswer])) {
                                    $percent = $pagestats[$page->id][$useranswer->useranswer] / $pagestats[$page->id]["total"] * 100;
                                    $percent = round($percent, 2);
                                    $percent .= "% ".get_string("enteredthis", "lesson");
                                } else {
                                    $percent = get_string("nooneenteredthis", "lesson");
                                }
                                $answerdata->answers[] = array($data, $percent);

                                $answerdata->response = get_string("thatsthewronganswer", "lesson");
                                if ($lesson->custom) {
                                    $answerdata->score = get_string("pointsearned", "lesson").": 0";
                                } else {
                                    $answerdata->score = get_string("didnotreceivecredit", "lesson");
                                }
                            }
                            break;
                        case LESSON_MATCHING:
                            if ($n == 0 && $useranswer != NULL && $useranswer->correct) {
                                if ($answer->response == NULL && $useranswer != NULL) {
                                    $answerdata->response = get_string("thatsthecorrectanswer", "lesson");
                                } else {
                                    $answerdata->response = $answer->response;
                                }
                            } elseif ($n == 1 && $useranswer != NULL && !$useranswer->correct) {
                                if ($answer->response == NULL && $useranswer != NULL) {
                                    $answerdata->response = get_string("thatsthewronganswer", "lesson");
                                } else {
                                    $answerdata->response = $answer->response;
                                }
                            } elseif ($n > 1) {
                                if ($n == 2 && $useranswer != NULL && $useranswer->correct) {
                                    if ($lesson->custom) {
                                        $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                                    } else {
                                        $answerdata->score = get_string("receivedcredit", "lesson");
                                    }
                                } elseif ($n == 3 && $useranswer != NULL && !$useranswer->correct) {
                                    if ($lesson->custom) {
                                        $answerdata->score = get_string("pointsearned", "lesson").": ".$answer->score;
                                    } else {
                                        $answerdata->score = get_string("didnotreceivecredit", "lesson");
                                    }
                                }
                                $data = "<select disabled=\"disabled\"><option selected=\"selected\">".strip_tags(format_string($answer->answer))."</option></select>";
                                if ($useranswer != NULL) {
                                    $userresponse = explode(",", $useranswer->useranswer);
                                    $data .= "<select disabled=\"disabled\"><option selected=\"selected\">".strip_tags(format_string($answers[$userresponse[$i]]->response))."</option></select>";
                                } else {
                                    $data .= "<select disabled=\"disabled\"><option selected=\"selected\">".strip_tags(format_string($answer->response))."</option></select>";
                                }

                                if ($n == 2) {
                                    if (isset($pagestats[$page->id])) {
                                        $percent = $pagestats[$page->id]["correct"] / $pagestats[$page->id]["total"] * 100;
                                        $percent = round($percent, 2);
                                        $percent .= "% ".get_string("answeredcorrectly", "lesson");
                                    } else {
                                        $percent = get_string("nooneansweredthisquestion", "lesson");
                                    }
                                } else {
                                    $percent = "";
                                }

                                $answerdata->answers[] = array($data, $percent);
                                $i++;
                            }
                            $n++;
                            break;
                        case LESSON_ESSAY :
                            if ($useranswer != NULL) {
                                $essayinfo = unserialize($useranswer->useranswer);
                                if ($essayinfo->response == NULL) {
                                    $answerdata->response = get_string("nocommentyet", "lesson");
                                } else {
                                    $answerdata->response = s($essayinfo->response);
                                }
                                if (isset($pagestats[$page->id])) {
                                    $percent = $pagestats[$page->id]->totalscore / $pagestats[$page->id]->total * 100;
                                    $percent = round($percent, 2);
                                    $percent = get_string("averagescore", "lesson").": ". $percent ."%";
                                } else {
                                    // dont think this should ever be reached....
                                    $percent = get_string("nooneansweredthisquestion", "lesson");
                                }
                                if ($essayinfo->graded) {
                                    if ($lesson->custom) {
                                        $answerdata->score = get_string("pointsearned", "lesson").": ".$essayinfo->score;
                                    } elseif ($essayinfo->score) {
                                        $answerdata->score = get_string("receivedcredit", "lesson");
                                    } else {
                                        $answerdata->score = get_string("didnotreceivecredit", "lesson");
                                    }
                                } else {
                                    $answerdata->score = get_string("havenotgradedyet", "lesson");
                                }
                            } else {
                                $essayinfo->answer = get_string("didnotanswerquestion", "lesson");
                            }

                            if (isset($pagestats[$page->id])) {
                                $avescore = $pagestats[$page->id]->totalscore / $pagestats[$page->id]->total;
                                $avescore = round($avescore, 2);
                                $avescore = get_string("averagescore", "lesson").": ". $avescore ;
                            } else {
                                // dont think this should ever be reached....
                                $avescore = get_string("nooneansweredthisquestion", "lesson");
                            }
                            $answerdata->answers[] = array(s(stripslashes_safe($essayinfo->answer)), $avescore);
                            break;
                        case LESSON_BRANCHTABLE :
                            $data = "<input type=\"button\" name=\"$answer->id\" value=\"".strip_tags(format_text($answer->answer, FORMAT_MOODLE,$formattextdefoptions))."\" disabled=\"disabled\"> ";
                            $data .= get_string('jumpsto', 'lesson', lesson_get_jump_name($answer->jumpto));

                            $answerdata->answers[] = array($data, "");
                            $answerpage->grayout = 1; // always grayed out
                            break;
                        case LESSON_ENDOFBRANCH :
                        case LESSON_CLUSTER :
                        case LESSON_ENDOFCLUSTER :
                            $data = get_string('jumpsto', 'lesson', lesson_get_jump_name($answer->jumpto));

                            $answerdata->answers[] = array($data, "");
                            $answerpage->grayout = 1; // always grayed out
                            break;
                    }
                    if (isset($answerdata)) {
                        $answerpage->answerdata = $answerdata;
                    }
                }
                $answerpages[] = $answerpage;
            }
            $pageid = $page->nextpageid;
        }

        /// actually start printing something
        $table = new stdClass;
        $table->wrap = array();
        $table->width = "60%";


        if (!empty($userid)) {
            // if looking at a students try, print out some basic stats at the top
            
                // print out users name
                //$headingobject->lastname = $students[$userid]->lastname;
                //$headingobject->firstname = $students[$userid]->firstname;
                //$headingobject->attempt = $try + 1;
                //print_heading(get_string("studentattemptlesson", "lesson", $headingobject));
            print_heading(get_string('attempt', 'lesson', $try+1));
            
            $table->head = array();
            $table->align = array("right", "left");
            $table->class = 'generaltable userinfotable';

            if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid = $userid", "completed", "*", $try, 1)) {
                $grade = -1;
                $completed = -1;
            } else {
                $grade = current($grades);
                $completed = $grade->completed;
                $grade = round($grade->grade, 2);
            }
            if (!$times = get_records_select("lesson_timer", "lessonid = $lesson->id and userid = $userid", "starttime", "*", $try, 1)) {
                $timetotake = -1;
            } else {
                $timetotake = current($times);
                $timetotake = $timetotake->lessontime - $timetotake->starttime;
            }

            if ($timetotake == -1 || $completed == -1 || $grade == -1) {
                $table->align = array("center");

                $table->data[] = array(get_string("notcompleted", "lesson"));
            } else {
                $user = $students[$userid];
                
                $gradeinfo = lesson_grade($lesson, $try, $user->id);
                
                $table->data[] = array($course->student.':', print_user_picture($user->id, $course->id, $user->picture, 0, true).fullname($user, true));
                $table->data[] = array(get_string("timetaken", "lesson").":", format_time($timetotake));
                $table->data[] = array(get_string("completed", "lesson").":", userdate($completed));
                $table->data[] = array(get_string('rawgrade', 'lesson').':', $gradeinfo->earned.'/'.$gradeinfo->total);
                $table->data[] = array(get_string("grade", "lesson").":", $grade."%");
            }
            print_table($table);
            
            // Don't want this class for later tables
            unset($table->class);
            echo "<br />";
        }


        $table->align = array("left", "left");
        $table->size = array("70%", "*");

        foreach ($answerpages as $page) {
            unset($table->data);
            if ($page->grayout) { // set the color of text
                $fontstart = "<span class=\"dimmed\">";
                $fontend = "</font>";
                $fontstart2 = $fontstart;
                $fontend2 = $fontend;
            } else {
                $fontstart = "";
                $fontend = "";
                $fontstart2 = "";
                $fontend2 = "";
            }

            $table->head = array($fontstart2.$page->qtype.": ".format_string($page->title).$fontend2, $fontstart2.get_string("classstats", "lesson").$fontend2);
            $table->data[] = array($fontstart.get_string("question", "lesson").": <br />".$fontend.$fontstart2.$page->contents.$fontend2, " ");
            $table->data[] = array($fontstart.get_string("answer", "lesson").":".$fontend);
            // apply the font to each answer
            foreach ($page->answerdata->answers as $answer){
                $modified = array();
                foreach ($answer as $single) {
                    // need to apply a font to each one
                    $modified[] = $fontstart2.$single.$fontend2;
                }
                $table->data[] = $modified;
            }
            if ($page->answerdata->response != NULL) {
                $table->data[] = array($fontstart.get_string("response", "lesson").": <br />".$fontend.$fontstart2.format_text($page->answerdata->response,FORMAT_MOODLE,$formattextdefoptions).$fontend2, " ");
            }
            $table->data[] = array($page->answerdata->score, " ");
            print_table($table);
            echo "<br />";
        }
    }

    else {
        error("Fatal Error: Unknown Action: ".$action."\n");
    }

/// Finish the page
    print_footer($course);

?>
