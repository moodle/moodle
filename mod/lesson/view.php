<?php  // $Id$
/**
 * This page prints a particular instance of lesson
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/lesson/locallib.php');
    require_once($CFG->dirroot.'/mod/lesson/lib.php');
    require_once($CFG->dirroot.'/mod/lesson/pagelib.php');
    require_once($CFG->libdir.'/blocklib.php');

    $id      = required_param('id', PARAM_INT);             // Course Module ID
    $pageid  = optional_param('pageid', NULL, PARAM_INT);   // Lesson Page ID
    $edit    = optional_param('edit', -1, PARAM_BOOL);
    $userpassword = optional_param('userpassword','',PARAM_CLEAN);
    
    list($cm, $course, $lesson) = lesson_get_basics($id);

    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

/// Check these for students only TODO: Find a better method for doing this!
///     Check lesson availability
///     Check for password
///     Check dependencies
///     Check for high scores
    if (!has_capability('mod/lesson:manage', $context)) {

        if (($lesson->available != 0 and time() < $lesson->available) or
            ($lesson->deadline != 0 and time() > $lesson->deadline)) {  // Deadline restrictions
            if ($lesson->deadline != 0 and time() > $lesson->deadline) {
                $message = get_string('lessonclosed', 'lesson', userdate($lesson->deadline));
            } else {
                $message = get_string('lessonopen', 'lesson', userdate($lesson->available));
            }
            
            lesson_print_header($cm, $course, $lesson);
            print_simple_box_start('center');
            echo '<div style="text-align:center;">';
            echo '<p>'.$message.'</p>';
            echo '<div class="lessonbutton standardbutton" style="padding: 5px;"><a href="'.$CFG->wwwroot.'/course/view.php?id='. $course->id .'">'. get_string('returnto', 'lesson', format_string($course->fullname, true)) .'</a></div>';
            echo '</div>';
            print_simple_box_end();
            print_footer($course);
            exit();
        
        } else if ($lesson->usepassword and empty($USER->lessonloggedin[$lesson->id])) { // Password protected lesson code
            $correctpass = false;
            if (!empty($userpassword)) {
                // with or without md5 for backward compatibility (MDL-11090)
                if (($lesson->password == md5(trim($userpassword))) or ($lesson->password == trim($userpassword))) {
                    $USER->lessonloggedin[$lesson->id] = true;
                    $correctpass = true;
                    if ($lesson->highscores) {
                        // Logged in - redirect so we go through all of these checks before starting the lesson.
                        redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id");
                    }
                }
            }

            if (!$correctpass) {
                lesson_print_header($cm, $course, $lesson);
                echo "<div class=\"password-form\">\n";
                print_simple_box_start('center');
                echo '<form id="password" method="post" action="'.$CFG->wwwroot.'/mod/lesson/view.php" autocomplete="off">' . "\n";
                echo '<fieldset class="invisiblefieldset">';
                echo '<input type="hidden" name="id" value="'. $cm->id .'" />' . "\n";
                if (optional_param('userpassword', 0, PARAM_CLEAN)) {
                    notify(get_string('loginfail', 'lesson'));
                }

                echo get_string('passwordprotectedlesson', 'lesson', format_string($lesson->name))."<br /><br />\n".
                     get_string('enterpassword', 'lesson')." <input type=\"password\" name=\"userpassword\" /><br /><br />\n<center>".
                     '<span class="lessonbutton standardbutton"><a href="'.$CFG->wwwroot.'/course/view.php?id='. $course->id .'">'. get_string('cancel', 'lesson') .'</a></span> ';

                lesson_print_submit_link(get_string('continue', 'lesson'), 'password', 'center', 'standardbutton submitbutton');
                echo '</fieldset></form>';
                print_simple_box_end();
                echo "</div>\n";
                print_footer($course);
                exit();
            }
        
        } else if ($lesson->dependency) { // check for dependencies
            if ($dependentlesson = get_record('lesson', 'id', $lesson->dependency)) {
                // lesson exists, so we can proceed            
                $conditions = unserialize($lesson->conditions);
                // assume false for all
                $timespent = false;
                $completed = false;
                $gradebetterthan = false;
                // check for the timespent condition
                if ($conditions->timespent) {
                    if ($attempttimes = get_records_select('lesson_timer', "userid = $USER->id AND lessonid = $dependentlesson->id")) {
                        // go through all the times and test to see if any of them satisfy the condition
                        foreach($attempttimes as $attempttime) {
                            $duration = $attempttime->lessontime - $attempttime->starttime;
                            if ($conditions->timespent < $duration/60) {
                                $timespent = true;
                            }
                        }
                    } 
                } else {
                    $timespent = true; // there isn't one set
                }

                // check for the gradebetterthan condition
                if($conditions->gradebetterthan) {
                    if ($studentgrades = get_records_select('lesson_grades', "userid = $USER->id AND lessonid = $dependentlesson->id")) {
                        // go through all the grades and test to see if any of them satisfy the condition
                        foreach($studentgrades as $studentgrade) {
                            if ($studentgrade->grade >= $conditions->gradebetterthan) {
                                $gradebetterthan = true;
                            }
                        }
                    }
                } else {
                    $gradebetterthan = true; // there isn't one set
                }

                // check for the completed condition
                if ($conditions->completed) {
                    if (count_records('lesson_grades', 'userid', $USER->id, 'lessonid', $dependentlesson->id)) {
                        $completed = true;
                    }
                } else {
                    $completed = true; // not set
                }

                $errors = array();
                // collect all of our error statements
                if (!$timespent) {
                    $errors[] = get_string('timespenterror', 'lesson', $conditions->timespent);
                }
                if (!$completed) {
                    $errors[] = get_string('completederror', 'lesson');
                }
                if (!$gradebetterthan) {
                    $errors[] = get_string('gradebetterthanerror', 'lesson', $conditions->gradebetterthan);
                }
                if (!empty($errors)) {  // print out the errors if any
                    lesson_print_header($cm, $course, $lesson);
                    echo '<p>';
                    print_simple_box_start('center');
                    print_string('completethefollowingconditions', 'lesson', $dependentlesson->name);
                    echo '<p style="text-align:center;">'.implode('<br />'.get_string('and', 'lesson').'<br />', $errors).'</p>';
                    print_simple_box_end();
                    echo '</p>';
                    print_footer($course);
                    exit();
                } 
            }
    
        } else if ($lesson->highscores and !$lesson->practice and !optional_param('viewed', 0) and empty($pageid)) {
            // Display high scores before starting lesson
            redirect("$CFG->wwwroot/mod/lesson/highscores.php?id=$cm->id");
        }
    }
    
    // set up some general variables
    $path = $CFG->wwwroot .'/course';

    // this is called if a student leaves during a lesson
    if($pageid == LESSON_UNSEENBRANCHPAGE) {
        $pageid = lesson_unseen_question_jump($lesson->id, $USER->id, $pageid);
    }
    
    // display individual pages and their sets of answers
    // if pageid is EOL then the end of the lesson has been reached
           // for flow, changed to simple echo for flow styles, michaelp, moved lesson name and page title down
   $attemptflag = false;
    if (empty($pageid)) {
        // make sure there are pages to view
        if (!get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
            if (!has_capability('mod/lesson:manage', $context)) {
                lesson_set_message(get_string('lessonnotready', 'lesson', $course->teacher)); // a nice message to the student
            } else {
                if (!count_records('lesson_pages', 'lessonid', $lesson->id)) {
                    redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id"); // no pages - redirect to add pages
                } else {
                    lesson_set_message(get_string('lessonpagelinkingbroken', 'lesson'));  // ok, bad mojo
                }
            }
        }
        
        add_to_log($course->id, 'lesson', 'start', 'view.php?id='. $cm->id, $lesson->id, $cm->id);
        
        // if no pageid given see if the lesson has been started
        if ($grades = get_records_select('lesson_grades', 'lessonid = '. $lesson->id .' AND userid = '. $USER->id,
                    'grade DESC')) {
            $retries = count($grades);
        } else {
            $retries = 0;
        }
        if ($retries) {
            $attemptflag = true;
        }
        
        if (isset($USER->modattempts[$lesson->id])) { 
            unset($USER->modattempts[$lesson->id]);  // if no pageid, then student is NOT reviewing
        }
        
        // if there are any questions have been answered correctly in this attempt
        if ($attempts = get_records_select('lesson_attempts', 
                    "lessonid = $lesson->id AND userid = $USER->id AND retry = $retries AND 
                    correct = 1", 'timeseen DESC')) {
            
            foreach ($attempts as $attempt) {
                $jumpto = get_field('lesson_answers', 'jumpto', 'id', $attempt->answerid);
                // convert the jumpto to a proper page id
                if ($jumpto == 0) { // unlikely value!
                    $lastpageseen = $attempt->pageid;
                } elseif ($jumpto == LESSON_NEXTPAGE) {
                    if (!$lastpageseen = get_field('lesson_pages', 'nextpageid', 'id', 
                                $attempt->pageid)) {
                        // no nextpage go to end of lesson
                        $lastpageseen = LESSON_EOL;
                    }
                } else {
                    $lastpageseen = $jumpto;
                }
                break; // only look at the latest correct attempt 
            }
        } else {
            $attempts = NULL;
        }

        if ($branchtables = get_records_select('lesson_branch', 
                            "lessonid = $lesson->id AND userid = $USER->id AND retry = $retries", 'timeseen DESC')) {
            // in here, user has viewed a branch table
            $lastbranchtable = current($branchtables);
            if ($attempts != NULL) {
                foreach($attempts as $attempt) {
                    if ($lastbranchtable->timeseen > $attempt->timeseen) {
                        // branch table was viewed later than the last attempt
                        $lastpageseen = $lastbranchtable->pageid;
                    }
                    break;
                }
            } else {
                // hasnt answered any questions but has viewed a branch table
                $lastpageseen = $lastbranchtable->pageid;
            }
        }
        //if ($lastpageseen != $firstpageid) {
        if (isset($lastpageseen) and count_records('lesson_attempts', 'lessonid', $lesson->id, 'userid', $USER->id, 'retry', $retries) > 0) {
            // get the first page
            if (!$firstpageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id,
                        'prevpageid', 0)) {
                error('Navigation: first page not found');
            }
            lesson_print_header($cm, $course, $lesson);
            if ($lesson->timed) {
                if ($lesson->retake) {
                    print_simple_box('<p style="text-align:center;">'. get_string('leftduringtimed', 'lesson') .'</p>', 'center');
                    echo '<div style="text-align:center;" class="lessonbutton standardbutton">'.
                              '<a href="view.php?id='.$cm->id.'&amp;pageid='.$firstpageid.'&amp;startlastseen=no">'.
                                get_string('continue', 'lesson').'</a></div>';
                } else {
                    print_simple_box_start('center');
                    echo '<div style="text-align:center;">';
                    echo get_string('leftduringtimednoretake', 'lesson');
                    echo '<br /><br /><div class="lessonbutton standardbutton"><a href="../../course/view.php?id='. $course->id .'">'. get_string('returntocourse', 'lesson') .'</a></div>';
                    echo '</div>';
                    print_simple_box_end();
                }
                
            } else {
                print_simple_box("<p style=\"text-align:center;\">".get_string('youhaveseen','lesson').'</p>',
                        "center");
                
                echo '<div style="text-align:center;">';
                echo '<span class="lessonbutton standardbutton">'.
                        '<a href="view.php?id='.$cm->id.'&amp;pageid='.$lastpageseen.'&amp;startlastseen=yes">'.
                        get_string('yes').'</a></span>&nbsp;&nbsp;&nbsp;';
                echo '<span class="lessonbutton standardbutton">'.
                        '<a href="view.php?id='.$cm->id.'&amp;pageid='.$firstpageid.'&amp;startlastseen=no">'.
                        get_string('no').'</a></div>';
                echo '</span>';
            }
            print_footer($course);
            exit();
        }
        
        if ($grades) {
            foreach ($grades as $grade) {
                $bestgrade = $grade->grade;
                break;
            }
            if (!$lesson->retake) {
                lesson_print_header($cm, $course, $lesson, 'view');
                print_simple_box_start('center');
                echo "<div style=\"text-align:center;\">";
                echo get_string("noretake", "lesson");
                echo "<br /><br /><div class=\"lessonbutton standardbutton\"><a href=\"../../course/view.php?id=$course->id\">".get_string('returntocourse', 'lesson').'</a></div>';
                echo "</div>";
                print_simple_box_end();
                print_footer($course);
                exit();
                  //redirect("../../course/view.php?id=$course->id", get_string("alreadytaken", "lesson"));
            // allow student to retake course even if they have the maximum grade
            // } elseif ($bestgrade == 100) {
              //     redirect("../../course/view.php?id=$course->id", get_string("maximumgradeachieved",
            //                 "lesson"));
            }
        }
        // start at the first page
        if (!$pageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
                error('Navigation: first page not found');
        }
        /// This is the code for starting a timed test
        if(!isset($USER->startlesson[$lesson->id]) && !has_capability('mod/lesson:manage', $context)) {
            $USER->startlesson[$lesson->id] = true;
            $startlesson = new stdClass;
            $startlesson->lessonid = $lesson->id;
            $startlesson->userid = $USER->id;
            $startlesson->starttime = time();
            $startlesson->lessontime = time();
            
            if (!insert_record('lesson_timer', $startlesson)) {
                error('Error: could not insert row into lesson_timer table');
            }
            if ($lesson->timed) {
                lesson_set_message(get_string('maxtimewarning', 'lesson', $lesson->maxtime), 'center');
            }
        }
    }
    if ($pageid != LESSON_EOL) {
        /// This is the code updates the lessontime for a timed test
        if ($startlastseen = optional_param('startlastseen', '', PARAM_ALPHA)) {  /// this deletes old records  not totally sure if this is necessary anymore
            if ($startlastseen == 'no') {
                if ($grades = get_records_select('lesson_grades', "lessonid = $lesson->id AND userid = $USER->id",
                            'grade DESC')) {
                    $retries = count($grades);
                } else {
                    $retries = 0;
                }
                if (!delete_records('lesson_attempts', 'userid', $USER->id, 'lessonid', $lesson->id, 'retry', $retries)) {
                    error('Error: could not delete old attempts');
                }
                if (!delete_records('lesson_branch', 'userid', $USER->id, 'lessonid', $lesson->id, 'retry', $retries)) {
                    error('Error: could not delete old seen branches');
                }
            }
        }
        
        add_to_log($course->id, 'lesson', 'view', 'view.php?id='. $cm->id, $pageid, $cm->id);
        
        if (!$page = get_record('lesson_pages', 'id', $pageid)) {
            error('Navigation: the page record not found');
        }

        if ($page->qtype == LESSON_CLUSTER) {  //this only gets called when a user starts up a new lesson and the first page is a cluster page
            if (!has_capability('mod/lesson:manage', $context)) {
                // get new id
                $pageid = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
                // get new page info
                if (!$page = get_record('lesson_pages', 'id', $pageid)) {
                    error('Navigation: the page record not found');
                }
                add_to_log($course->id, 'lesson', 'view', 'view.php?id='. $cm->id, $pageid, $cm->id);
            } else {
                // get the next page
                $pageid = $page->nextpageid;
                if (!$page = get_record('lesson_pages', 'id', $pageid)) {
                    error('Navigation: the page record not found');
                }
            }
        } elseif ($page->qtype == LESSON_ENDOFCLUSTER) { // Check for endofclusters
            if ($page->nextpageid == 0) {
                $nextpageid = LESSON_EOL;
            } else {
                $nextpageid = $page->nextpageid;
            }
            redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=$nextpageid");
        } else if ($page->qtype == LESSON_ENDOFBRANCH) { // Check for endofbranches
            if ($answers = get_records('lesson_answers', 'pageid', $page->id, 'id')) {
                // print_heading(get_string('endofbranch', 'lesson'));
                foreach ($answers as $answer) {
                    // just need the first answer
                    if ($answer->jumpto == LESSON_RANDOMBRANCH) {
                        $answer->jumpto = lesson_unseen_branch_jump($lesson->id, $USER->id);
                    } elseif ($answer->jumpto == LESSON_CLUSTERJUMP) {
                        if (!has_capability('mod/lesson:manage', $context)) {
                            $answer->jumpto = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
                        } else {
                            if ($page->nextpageid == 0) {  
                                $answer->jumpto = LESSON_EOL;
                            } else {
                                $answer->jumpto = $page->nextpageid;
                            }
                        }
                    } else if ($answer->jumpto == LESSON_NEXTPAGE) {
                        if ($page->nextpageid == 0) {  
                            $answer->jumpto = LESSON_EOL;
                        } else {
                            $answer->jumpto = $page->nextpageid;
                        }
                    } else if ($answer->jumpto == 0) {
                        $answer->jumpto = $page->id;
                    } else if ($answer->jumpto == LESSON_PREVIOUSPAGE) {
                        $answer->jumpto = $page->prevpageid;                            
                    }
                    redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=$answer->jumpto");
                    break;
                } 
            } else {
                error('Navigation: No answers on EOB');
            }
        }
        
        // check to see if the user can see the left menu
        if (!has_capability('mod/lesson:manage', $context)) {
            $lesson->displayleft = lesson_displayleftif($lesson);
        }
        
        // This is where several messages (usually warnings) are displayed
        // all of this is displayed above the actual page
        
        // clock code
        // get time information for this user
        $timer = new stdClass;
        if(!has_capability('mod/lesson:manage', $context)) {
            if (!$timer = get_records_select('lesson_timer', "lessonid = $lesson->id AND userid = $USER->id", 'starttime')) {
                error('Error: could not find records');
            } else {
                $timer = array_pop($timer); // this will get the latest start time record
            }
        }

        $startlastseen = optional_param('startlastseen', '', PARAM_ALPHA);
        if ($startlastseen == 'yes') {  // continue a previous test, need to update the clock  (think this option is disabled atm)
            $timer->starttime = time() - ($timer->lessontime - $timer->starttime);
            $timer->lessontime = time();
        } else if ($startlastseen == 'no') {  // starting over
            // starting over, so reset the clock
            $timer->starttime = time();
            $timer->lessontime = time();
        }
            
        // for timed lessons, display clock
        if ($lesson->timed) {
            if(has_capability('mod/lesson:manage', $context)) {
                lesson_set_message(get_string('teachertimerwarning', 'lesson'));
            } else {
                $timeleft = ($timer->starttime + $lesson->maxtime * 60) - time();

                if ($timeleft <= 0) {
                    // Out of time
                    lesson_set_message(get_string('eolstudentoutoftime', 'lesson'));
                    redirect("$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;pageid=".LESSON_EOL."&amp;outoftime=normal");
                    die; // Shouldn't be reached, but make sure
                } else if ($timeleft < 60) {
                    // One minute warning
                    lesson_set_message(get_string('studentoneminwarning', 'lesson'));
                }
            }
        }

        // update the clock
        if (!has_capability('mod/lesson:manage', $context)) {
            $timer->lessontime = time();
            if (!update_record('lesson_timer', $timer)) {
                error('Error: could not update lesson_timer table');
            }
        }

         ///  This is the warning msg for teachers to inform them that cluster and unseen does not work while logged in as a teacher
        if(has_capability('mod/lesson:manage', $context)) {
            if (lesson_display_teacher_warning($lesson->id)) {
                $warningvars->cluster = get_string('clusterjump', 'lesson');
                $warningvars->unseen = get_string('unseenpageinbranch', 'lesson');
                lesson_set_message(get_string('teacherjumpwarning', 'lesson', $warningvars));
            }
        }
        
        if ($page->qtype == LESSON_BRANCHTABLE) {
            if ($lesson->minquestions and !has_capability('mod/lesson:manage', $context)) {
                // tell student how many questions they have seen, how many are required and their grade
                $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
                
                $gradeinfo = lesson_grade($lesson, $ntries);
                
                if ($gradeinfo->attempts) {
                    if ($gradeinfo->nquestions < $lesson->minquestions) {
                        $a = new stdClass;
                        $a->nquestions   = $gradeinfo->nquestions;
                        $a->minquestions = $lesson->minquestions;
                        lesson_set_message(get_string('numberofpagesviewednotice', 'lesson', $a));
                    }
                    lesson_set_message(get_string("numberofcorrectanswers", "lesson", $gradeinfo->earned), 'notify');
                    $a = new stdClass;
                    $a->grade = number_format($gradeinfo->grade * $lesson->grade / 100, 1);
                    $a->total = $lesson->grade;
                    lesson_set_message(get_string('yourcurrentgradeisoutof', 'lesson', $a), 'notify');
                }
            }
        }

        $PAGE = page_create_instance($lesson->id);
        $PAGE->set_lessonpageid($page->id);
        $pageblocks = blocks_setup($PAGE);

        $leftcolumnwidth  = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);
        $rightcolumnwidth = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);

        if (($edit != -1) and $PAGE->user_allowed_editing()) {
            $USER->editing = $edit;
        }

    /// Print the page header, heading and tabs
        $PAGE->print_header();

        if ($attemptflag) {
            print_heading(get_string('attempt', 'lesson', $retries + 1));
        }

        /// This calculates and prints the ongoing score
        if ($lesson->ongoing and !empty($pageid)) {
            lesson_print_ongoing_score($lesson);
        }

        require($CFG->dirroot.'/mod/lesson/viewstart.html');

        // now starting to print the page's contents   
        if ($page->qtype == LESSON_BRANCHTABLE) {
            print_heading(format_string($page->title));
        } else {
            $lesson->slideshow = false; // turn off slide show for all pages other than LESSON_BRANTCHTABLE
        }
        
        if (!$lesson->slideshow) {
            $options = new stdClass;
            $options->noclean = true;
            print_simple_box('<div class="contents">'.
                            format_text($page->contents, FORMAT_MOODLE, $options).
                            '</div>', 'center');
        }
        
        // this is for modattempts option.  Find the users previous answer to this page,
        //   and then display it below in answer processing
        if (isset($USER->modattempts[$lesson->id])) {            
            $retries = count_records('lesson_grades', "lessonid", $lesson->id, "userid", $USER->id);
            $retries--;
            if (! $attempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $USER->id AND pageid = $page->id AND retry = $retries", "timeseen")) {
                error("Previous attempt record could not be found!");
            }
            $attempt = end($attempts);
        }
        
        // get the answers in a set order, the id order
        if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
            if ($page->qtype != LESSON_BRANCHTABLE) {  // To fix XHTML problem (BT have their own forms)
                echo "<form id=\"answerform\" method =\"post\" action=\"lesson.php\" autocomplete=\"off\">";
                echo '<fieldset class="invisiblefieldset">';
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
                echo "<input type=\"hidden\" name=\"action\" value=\"continue\" />";
                echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\" />";
                echo "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";
                print_simple_box_start("center");
                echo '<table width="100%">';
            }
            // default format text options
            $options = new stdClass;
            $options->para = false; // no <p></p>
            $options->noclean = true;
            // echo "qtype is $page->qtype"; // debug
            switch ($page->qtype) {
                case LESSON_SHORTANSWER :
                case LESSON_NUMERICAL :
                    if (isset($USER->modattempts[$lesson->id])) {     
                        $value = 'value="'.s($attempt->useranswer).'"';
                    } else {
                        $value = "";
                    }       
                    echo '<tr><td style="text-align:center;"><label for="answer">'.get_string('youranswer', 'lesson').'</label>'.
                        ": <input type=\"text\" id=\"answer\" name=\"answer\" size=\"50\" maxlength=\"200\" $value />\n";
                    echo '</td></tr></table>';
                    print_simple_box_end();
                    lesson_print_submit_link(get_string('pleaseenteryouranswerinthebox', 'lesson'), 'answerform');
                    break;
                case LESSON_TRUEFALSE :
                    shuffle($answers);
                    $i = 0;
                    foreach ($answers as $answer) {
                        echo '<tr><td valign="top">';
                        if (isset($USER->modattempts[$lesson->id]) && $answer->id == $attempt->answerid) {
                            $checked = 'checked="checked"';
                        } else {
                            $checked = '';
                        } 
                        echo "<input type=\"radio\" id=\"answerid$i\" name=\"answerid\" value=\"{$answer->id}\" $checked />";
                        echo "</td><td>";
                        echo "<label for=\"answerid$i\">".format_text(trim($answer->answer), FORMAT_MOODLE, $options).'</label>';
                        echo '</td></tr>';
                        if ($answer != end($answers)) {
                            echo "<tr><td><br /></td></tr>";                            
                        }
                        $i++;
                    }
                    echo '</table>';
                    print_simple_box_end();
                    lesson_print_submit_link(get_string('pleasecheckoneanswer', 'lesson'), 'answerform');
                    break;
                case LESSON_MULTICHOICE :
                    $i = 0;
                    shuffle($answers);

                    foreach ($answers as $answer) {
                        echo '<tr><td valign="top">';
                        if ($page->qoption) {
                            $checked = '';
                            if (isset($USER->modattempts[$lesson->id])) {
                                $answerids = explode(",", $attempt->useranswer);
                                if (in_array($answer->id, $answerids)) {
                                    $checked = ' checked="checked"';
                                } else {
                                    $checked = '';
                                }
                            }
                            // more than one answer allowed 
                            echo "<input type=\"checkbox\" id=\"answerid$i\" name=\"answer[$i]\" value=\"{$answer->id}\"$checked />";
                        } else {
                            if (isset($USER->modattempts[$lesson->id]) && $answer->id == $attempt->answerid) {
                                $checked = ' checked="checked"';
                            } else {
                                $checked = '';
                            } 
                            // only one answer allowed
                            echo "<input type=\"radio\" id=\"answerid$i\" name=\"answerid\" value=\"{$answer->id}\"$checked />";
                        }
                        echo '</td><td>';
                        echo "<label for=\"answerid$i\" >".format_text(trim($answer->answer), FORMAT_MOODLE, $options).'</label>'; 
                        echo '</td></tr>';
                        if ($answer != end($answers)) {
                            echo '<tr><td><br /></td></tr>';
                        } 
                        $i++;
                    }
                    echo '</table>';
                    print_simple_box_end();
                    if ($page->qoption) {
                        $linkname = get_string('pleasecheckoneormoreanswers', 'lesson');
                    } else {
                        $linkname = get_string('pleasecheckoneanswer', 'lesson');
                    }
                    lesson_print_submit_link($linkname, 'answerform');
                    break;
                    
                case LESSON_MATCHING :
                    // don't suffle answers (could be an option??)
                    foreach ($answers as $answer) {
                        // get all the response
                        if ($answer->response != NULL) {
                            $responses[] = trim($answer->response);
                        }
                    }
                    
                    $responseoptions = array();
                    if (!empty($responses)) {
                        shuffle($responses);
                        $responses = array_unique($responses);                     
                        foreach ($responses as $response) {
                            $responseoptions[htmlspecialchars(trim($response))] = $response;
                        }
                    }
                    if (isset($USER->modattempts[$lesson->id])) {
                        $useranswers = explode(',', $attempt->useranswer);
                        $t = 0;
                    }
                    foreach ($answers as $answer) {
                        if ($answer->response != NULL) {
                            echo '<tr><td align="right">';
                            echo "<b><label for=\"menuresponse[$answer->id]\">".
                                    format_text($answer->answer,FORMAT_MOODLE,$options).
                                    '</label>: </b></td><td valign="bottom">';

                            if (isset($USER->modattempts[$lesson->id])) {
                                $selected = htmlspecialchars(trim($answers[$useranswers[$t]]->response));  // gets the user's previous answer
                                choose_from_menu ($responseoptions, "response[$answer->id]", $selected);
                                $t++;
                            } else {
                                choose_from_menu ($responseoptions, "response[$answer->id]");
                            }
                            echo '</td></tr>';
                            if ($answer != end($answers)) {
                                echo '<tr><td><br /></td></tr>';
                            } 
                        }
                    }
                    echo '</table>';
                    print_simple_box_end();
                    lesson_print_submit_link(get_string('pleasematchtheabovepairs', 'lesson'), 'answerform');
                    break;
                case LESSON_BRANCHTABLE :                  
                    $options = new stdClass;
                    $options->para = false;
                    $buttons = array();
                    $i = 0;
                    foreach ($answers as $answer) {
                        // Each button must have its own form inorder for it to work with JavaScript turned off
                        $button  = "<form id=\"answerform$i\" method=\"post\" action=\"$CFG->wwwroot/mod/lesson/lesson.php\">\n".
                                   '<div>'.
                                   "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n".
                                   "<input type=\"hidden\" name=\"action\" value=\"continue\" />\n".
                                   "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\" />\n".
                                   "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />\n".
                                   "<input type=\"hidden\" name=\"jumpto\" value=\"$answer->jumpto\" />\n".
                                   lesson_print_submit_link(strip_tags(format_text($answer->answer, FORMAT_MOODLE, $options)), "answerform$i", '', '', '', '', true).
                                   '</div>'.
                                   '</form>';
                        
                        $buttons[] = $button;
                        $i++;
                    }
                    
                /// Set the orientation
                    if ($page->layout) {
                        $orientation = 'horizontal';
                    } else {
                        $orientation = 'vertical';
                    }
                    
                    $fullbuttonhtml = "\n<div class=\"branchbuttoncontainer $orientation\">\n" .
                                      implode("\n", $buttons).
                                      "\n</div>\n";
                
                    if ($lesson->slideshow) {
                        $options = new stdClass;
                        $options->noclean = true;
                        echo '<div class="contents">'.format_text($page->contents, FORMAT_MOODLE, $options)."</div>\n";
                        echo '</div><!--end slideshow div-->';
                        echo $fullbuttonhtml;
                    } else {
                        echo $fullbuttonhtml;
                    }
                    
                    break;
                case LESSON_ESSAY :
                    if (isset($USER->modattempts[$lesson->id])) {
                        $essayinfo = unserialize($attempt->useranswer);
                        $value = s(stripslashes_safe($essayinfo->answer));
                    } else {
                        $value = "";
                    }
                    echo '<tr><td style="text-align:center;" valign="top" nowrap="nowrap"><label for="answer">'.get_string("youranswer", "lesson").'</label>:</td><td>'.
                         '<textarea id="answer" name="answer" rows="15" cols="60">'.$value."</textarea>\n";
                    echo '</td></tr></table>';
                    print_simple_box_end();
                    lesson_print_submit_link(get_string('pleaseenteryouranswerinthebox', 'lesson'), 'answerform');
                    break;
                default: // close the tags MDL-7861
                    echo ('</table>');
                    print_simple_box_end();
                break;
            }
            if ($page->qtype != LESSON_BRANCHTABLE) {  // To fix XHTML problem (BT have their own forms)
                echo '</fieldset>';
                echo "</form>\n"; 
            }
        } else {
            // a page without answers - find the next (logical) page
            echo "<form id=\"pageform\" method=\"post\" action=\"$CFG->wwwroot/mod/lesson/view.php\">\n";
            echo '<div>';
            echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
            if ($lesson->nextpagedefault) {
                // in Flash Card mode...
                // ...first get number of retakes
                $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
                // ...then get the page ids (lessonid the 5th param is needed to make get_records play)
                $allpages = get_records("lesson_pages", "lessonid", $lesson->id, "id", "id,lessonid");
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
                        if (!count_records_select("lesson_attempts", "pageid = $thispage->id AND
                                    userid = $USER->id AND correct = 1 AND retry = $nretakes")) {
                            $found = true;
                            break;
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
            } else {
                // in normal lesson mode...
                if (!$newpageid = get_field("lesson_pages", "nextpageid", "id", $pageid)) {
                    // this is the last page - flag end of lesson
                    $newpageid = LESSON_EOL;
                }
            }
            echo "<input type=\"hidden\" name=\"pageid\" value=\"$newpageid\" />\n";
            lesson_print_submit_link(get_string('continue', 'lesson'), 'pageform');
            echo '</div>';
            echo "</form>\n";
        }
        
        // Finish of the page
        lesson_print_progress_bar($lesson, $course);
        require($CFG->dirroot.'/mod/lesson/viewend.html');
    } else {
        // end of lesson reached work out grade
        
        // Used to check to see if the student ran out of time
        $outoftime = optional_param('outoftime', '', PARAM_ALPHA);

        // Update the clock / get time information for this user
        if (!has_capability('mod/lesson:manage', $context)) {
            unset($USER->startlesson[$lesson->id]);
            if (!$timer = get_records_select('lesson_timer', "lessonid = $lesson->id AND userid = $USER->id", 'starttime')) {
                error('Error: could not find records');
            } else {
                $timer = array_pop($timer); // this will get the latest start time record
            }
            $timer->lessontime = time();
            
            if (!update_record("lesson_timer", $timer)) {
                error("Error: could not update lesson_timer table");
            }
        }
        
        add_to_log($course->id, "lesson", "end", "view.php?id=$cm->id", "$lesson->id", $cm->id);
        
        lesson_print_header($cm, $course, $lesson, 'view');
        print_heading(get_string("congratulations", "lesson"));
        print_simple_box_start("center");
        $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
        if (isset($USER->modattempts[$lesson->id])) {
            $ntries--;  // need to look at the old attempts :)
        }
        if (!has_capability('mod/lesson:manage', $context)) {
            
            $gradeinfo = lesson_grade($lesson, $ntries);
            
            if ($gradeinfo->attempts) {
                if (!$lesson->custom) {
                    echo "<p style=\"text-align:center;\">".get_string("numberofpagesviewed", "lesson", $gradeinfo->nquestions).
                        "</p>\n";
                    if ($lesson->minquestions) {
                        if ($gradeinfo->nquestions < $lesson->minquestions) {
                            // print a warning and set nviewed to minquestions
                            echo "<p style=\"text-align:center;\">".get_string("youshouldview", "lesson", 
                                    $lesson->minquestions)."</p>\n";
                        }
                    }
                    echo "<p style=\"text-align:center;\">".get_string("numberofcorrectanswers", "lesson", $gradeinfo->earned).
                        "</p>\n";
                }
                $a = new stdClass;
                $a->score = $gradeinfo->earned;
                $a->grade = $gradeinfo->total;
                if ($gradeinfo->nmanual) {
                    $a->tempmaxgrade = $gradeinfo->total - $gradeinfo->manualpoints;
                    $a->essayquestions = $gradeinfo->nmanual;
                    echo "<div style=\"text-align:center;\">".get_string("displayscorewithessays", "lesson", $a)."</div>";
                } else {
                    echo "<div style=\"text-align:center;\">".get_string("displayscorewithoutessays", "lesson", $a)."</div>";                        
                }
                $a = new stdClass;
                $a->grade = number_format($gradeinfo->grade * $lesson->grade / 100, 1);
                $a->total = $lesson->grade;
                echo "<p style=\"text-align:center;\">".get_string('yourcurrentgradeisoutof', 'lesson', $a)."</p>\n";
                    
                $grade->lessonid = $lesson->id;
                $grade->userid = $USER->id;
                $grade->grade = $gradeinfo->grade;
                $grade->completed = time();
                if (!$lesson->practice) {
                    if (isset($USER->modattempts[$lesson->id])) { // if reviewing, make sure update old grade record
                        if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid = $USER->id", "completed")) {
                            error("Could not find Grade Records");
                        }
                        $oldgrade = end($grades);
                        $grade->id = $oldgrade->id;
                        if (!$update = update_record("lesson_grades", $grade)) {
                            error("Navigation: grade not updated");
                        }
                    } else {
                        if (!$newgradeid = insert_record("lesson_grades", $grade)) {
                            error("Navigation: grade not inserted");
                        }
                    }
                } else {
                    if (!delete_records("lesson_attempts", "lessonid", $lesson->id, "userid", $USER->id, "retry", $ntries)) {
                        error("Could not delete lesson attempts");
                    }
                }
            } else {
                if ($lesson->timed) {
                    if ($outoftime == 'normal') {
                        $grade = new stdClass;
                        $grade->lessonid = $lesson->id;
                        $grade->userid = $USER->id;
                        $grade->grade = 0;
                        $grade->completed = time();
                        if (!$lesson->practice) {
                            if (!$newgradeid = insert_record("lesson_grades", $grade)) {
                                error("Navigation: grade not inserted");
                            }
                        }
                        echo get_string("eolstudentoutoftimenoanswers", "lesson");
                    }
                } else {
                    echo get_string("welldone", "lesson");
                }
            }

            // update central gradebook
            lesson_update_grades($lesson, $USER->id);

        } else { 
            // display for teacher
            echo "<p style=\"text-align:center;\">".get_string("displayofgrade", "lesson")."</p>\n";
        }
        print_simple_box_end(); //End of Lesson button to Continue.

        // after all the grade processing, check to see if "Show Grades" is off for the course
        // if yes, redirect to the course page
        if (!$course->showgrades) {
            redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
        }

        // high scores code
        if ($lesson->highscores && !has_capability('mod/lesson:manage', $context) && !$lesson->practice) {
            echo "<div style=\"text-align:center;\"><br />";
            if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
                $madeit = false;
                if ($highscores = get_records_select("lesson_high_scores", "lessonid = $lesson->id")) {
                    // get all the high scores into an array
                    $topscores = array();
                    $uniquescores = array();
                    foreach ($highscores as $highscore) {
                        $grade = $grades[$highscore->gradeid]->grade;
                        $topscores[] = $grade;
                        $uniquescores[$grade] = 1;
                    }
                    // sort to find the lowest score
                    sort($topscores);
                    $lowscore = $topscores[0];
                    
                    if ($gradeinfo->grade >= $lowscore || count($uniquescores) <= $lesson->maxhighscores) {
                        $madeit = true;
                    }
                }
                if (!$highscores or $madeit) {
                    echo '<p>'.get_string("youmadehighscore", "lesson", $lesson->maxhighscores).
                         '</p>
                          <form method="post" id="highscores" action="'.$CFG->wwwroot.'/mod/lesson/highscores.php">
                          <div>
                          <input type="hidden" name="mode" value="add" />
                          <input type="hidden" name="id" value="'.$cm->id.'" />
                          <input type="hidden" name="sesskey" value="'.sesskey().'" />
                          <p>';
                          lesson_print_submit_link(get_string('clicktopost', 'lesson'), 'highscores');
                    echo '</p>
                          </div>
                          </form>';
                } else {
                    echo get_string("nothighscore", "lesson", $lesson->maxhighscores)."<br />";
                }
            }
            echo "<br /><div style=\"padding: 5px;\" class=\"lessonbutton standardbutton\"><a href=\"$CFG->wwwroot/mod/lesson/highscores.php?id=$cm->id&amp;link=1\">".get_string("viewhighscores", "lesson").'</a></div>';
            echo "</div>";                            
        }

        if ($lesson->modattempts && !has_capability('mod/lesson:manage', $context)) {
            // make sure if the student is reviewing, that he/she sees the same pages/page path that he/she saw the first time
            // look at the attempt records to find the first QUESTION page that the user answered, then use that page id
            // to pass to view again.  This is slick cause it wont call the empty($pageid) code
            // $ntries is decremented above
            if (!$attempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $USER->id AND retry = $ntries", "timeseen")) {
                $attempts = array();
            }
            $firstattempt = current($attempts);
            $pageid = $firstattempt->pageid;
            // IF the student wishes to review, need to know the last question page that the student answered.  This will help to make
            // sure that the student can leave the lesson via pushing the continue button.
            $lastattempt = end($attempts);
            $USER->modattempts[$lesson->id] = $lastattempt->pageid;
            echo "<div style=\"text-align:center; padding:5px;\" class=\"lessonbutton standardbutton\"><a href=\"view.php?id=$cm->id&amp;pageid=$pageid\">".get_string("reviewlesson", "lesson")."</a></div>\n"; 
        } elseif ($lesson->modattempts && has_capability('mod/lesson:manage', $context)) {
            echo "<p style=\"text-align:center;\">".get_string("modattemptsnoteacher", "lesson")."</p>";                
        }
        
        if ($lesson->activitylink) {
            if ($module = get_record('course_modules', 'id', $lesson->activitylink)) {
                if ($modname = get_field('modules', 'name', 'id', $module->module))
                    if ($instance = get_record($modname, 'id', $module->instance)) {
                        echo "<div style=\"text-align:center; padding:5px;\" class=\"lessonbutton standardbutton\">".
                                "<a href=\"$CFG->wwwroot/mod/$modname/view.php?id=$lesson->activitylink\">".
                                get_string('activitylinkname', 'lesson', $instance->name)."</a></div>\n";
                    }
            }
        }

        echo "<div style=\"text-align:center; padding:5px;\" class=\"lessonbutton standardbutton\"><a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".get_string('returnto', 'lesson', format_string($course->fullname, true))."</a></div>\n";
        echo "<div style=\"text-align:center; padding:5px;\" class=\"lessonbutton standardbutton\"><a href=\"$CFG->wwwroot/grade/index.php?id=$course->id\">".get_string('viewgrades', 'lesson')."</a></div>\n";
    }

/// Finish the page
    print_footer($course);

?>
