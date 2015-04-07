<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays the lesson statistics.
 *
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');

$id     = required_param('id', PARAM_INT);    // Course Module ID
$pageid = optional_param('pageid', null, PARAM_INT);    // Lesson Page ID
$action = optional_param('action', 'reportoverview', PARAM_ALPHA);  // action to take
$nothingtodisplay = false;

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST));

require_login($course, false, $cm);

$currentgroup = groups_get_activity_group($cm, true);

$context = context_module::instance($cm->id);
require_capability('mod/lesson:viewreports', $context);

// Only load students if there attempts for this lesson.
if ($attempts = $DB->record_exists('lesson_attempts', array('lessonid' => $lesson->id))) {
    list($esql, $params) = get_enrolled_sql($context, '', $currentgroup, true);
    list($sort, $sortparams) = users_order_by_sql('u');

    $params['lessonid'] = $lesson->id;
    $ufields = user_picture::fields('u');
    $sql = "SELECT DISTINCT $ufields
            FROM {user} u
            JOIN {lesson_attempts} a ON u.id = a.userid
            JOIN ($esql) ue ON ue.id = a.userid
            WHERE a.lessonid = :lessonid
            ORDER BY $sort";

    $students = $DB->get_recordset_sql($sql, $params);
    if (!$students->valid()) {
        $nothingtodisplay = true;
    }
} else {
    $nothingtodisplay = true;
}

$url = new moodle_url('/mod/lesson/report.php', array('id'=>$id));
$url->param('action', $action);
if ($pageid !== null) {
    $url->param('pageid', $pageid);
}
$PAGE->set_url($url);
if ($action == 'reportoverview') {
    $PAGE->navbar->add(get_string('reports', 'lesson'));
    $PAGE->navbar->add(get_string('overview', 'lesson'));
}

$lessonoutput = $PAGE->get_renderer('mod_lesson');

$attempts = $DB->get_recordset('lesson_attempts', array('lessonid' => $lesson->id), 'timeseen');
if (!$attempts->valid()) {
    $nothingtodisplay = true;
}

if (! $grades = $DB->get_records('lesson_grades', array('lessonid' => $lesson->id), 'completed')) {
    $grades = array();
}

if (! $times = $DB->get_records('lesson_timer', array('lessonid' => $lesson->id), 'starttime')) {
    $times = array();
}

if ($nothingtodisplay) {
    echo $lessonoutput->header($lesson, $cm, $action, false, null, get_string('nolessonattempts', 'lesson'));
    if (!empty($currentgroup)) {
        $groupname = groups_get_group_name($currentgroup);
        echo $OUTPUT->notification(get_string('nolessonattemptsgroup', 'lesson', $groupname));
    } else {
        echo $OUTPUT->notification(get_string('nolessonattempts', 'lesson'));
    }
    groups_print_activity_menu($cm, $url);
    echo $OUTPUT->footer();
    exit();
}

if ($action === 'delete') {
    /// Process any form data before fetching attempts, grades and times
    if (has_capability('mod/lesson:edit', $context) and $form = data_submitted() and confirm_sesskey()) {
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

                /// Clean up the timer table by removing using the order - this is silly, it should be linked to specific attempt (skodak)
                    $params = array ("userid" => $userid, "lessonid" => $lesson->id);
                    $timers = $DB->get_records_sql("SELECT id FROM {lesson_timer}
                                                     WHERE userid = :userid AND lessonid = :lessonid
                                                  ORDER BY starttime", $params, $try, 1);
                    if ($timers) {
                        $timer = reset($timers);
                        $DB->delete_records('lesson_timer', array('id' => $timer->id));
                    }

                /// Remove the grade from the grades and high_scores tables - this is silly, it should be linked to specific attempt (skodak)
                    $grades = $DB->get_records_sql("SELECT id FROM {lesson_grades}
                                                     WHERE userid = :userid AND lessonid = :lessonid
                                                  ORDER BY completed", $params, $try, 1);

                    if ($grades) {
                        $grade = reset($grades);
                        $DB->delete_records('lesson_grades', array('id' => $grade->id));
                        $DB->delete_records('lesson_high_scores', array('gradeid' => $grade->id, 'lessonid' => $lesson->id, 'userid' => $userid));
                    }

                /// Remove attempts and update the retry number
                    $DB->delete_records('lesson_attempts', array('userid' => $userid, 'lessonid' => $lesson->id, 'retry' => $try));
                    $DB->execute("UPDATE {lesson_attempts} SET retry = retry - 1 WHERE userid = ? AND lessonid = ? AND retry > ?", array($userid, $lesson->id, $try));

                /// Remove seen branches and update the retry number
                    $DB->delete_records('lesson_branch', array('userid' => $userid, 'lessonid' => $lesson->id, 'retry' => $try));
                    $DB->execute("UPDATE {lesson_branch} SET retry = retry - 1 WHERE userid = ? AND lessonid = ? AND retry > ?", array($userid, $lesson->id, $try));

                /// update central gradebook
                    lesson_update_grades($lesson, $userid);

                    $modifier++;
                }
            }
        }
    }
    redirect(new moodle_url($PAGE->url, array('action'=>'reportoverview')));

} else if ($action === 'reportoverview') {
    /**************************************************************************
    this action is for default view and overview view
    **************************************************************************/
    echo $lessonoutput->header($lesson, $cm, $action, false, null, get_string('overview', 'lesson'));
    groups_print_activity_menu($cm, $url);

    $course_context = context_course::instance($course->id);
    if (has_capability('gradereport/grader:view', $course_context) && has_capability('moodle/grade:viewall', $course_context)) {
        $seeallgradeslink = new moodle_url('/grade/report/grader/index.php', array('id'=>$course->id));
        $seeallgradeslink = html_writer::link($seeallgradeslink, get_string('seeallcoursegrades', 'grades'));
        echo $OUTPUT->box($seeallgradeslink, 'allcoursegrades');
    }

    $studentdata = array();

    // build an array for output
    foreach ($attempts as $attempt) {
        // if the user is not in the array or if the retry number is not in the sub array, add the data for that try.
        if (!array_key_exists($attempt->userid, $studentdata) || !array_key_exists($attempt->retry, $studentdata[$attempt->userid])) {
            // restore/setup defaults
            $n = 0;
            $timestart = 0;
            $timeend = 0;
            $usergrade = null;

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
    $attempts->close();
    // set all the stats variables
    $numofattempts = 0;
    $avescore      = 0;
    $avetime       = 0;
    $highscore     = null;
    $lowscore      = null;
    $hightime      = null;
    $lowtime       = null;

    $table = new html_table();

    // set up the table object
    $table->head = array(get_string('name'), get_string('attempts', 'lesson'), get_string('highscore', 'lesson'));
    $table->align = array('center', 'left', 'left');
    $table->wrap = array('nowrap', 'nowrap', 'nowrap');
    $table->attributes['class'] = 'standardtable generaltable';
    $table->size = array(null, '70%', null);

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
            $studentname = fullname($student, true);
            foreach ($tries as $try) {
            // start to build up the checkbox and link
                if (has_capability('mod/lesson:edit', $context)) {
                    $temp = '<input type="checkbox" id="attempts" name="attempts['.$try['userid'].']['.$try['try'].']" /> ';
                } else {
                    $temp = '';
                }

                $temp .= "<a href=\"report.php?id=$cm->id&amp;action=reportdetail&amp;userid=".$try['userid']
                        .'&amp;try='.$try['try'].'" class="lesson-attempt-link">';
                if ($try["grade"] !== null) { // if null then not done yet
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
                    $timetotake = null;
                }
                // build up the attempts array
                $attempts[] = $temp;

                // run these lines for the stats only if the user finnished the lesson
                if ($try["grade"] !== null) {
                    $numofattempts++;
                    $avescore += $try["grade"];
                    $avetime += $timetotake;
                    if ($try["grade"] > $highscore || $highscore === null) {
                        $highscore = $try["grade"];
                    }
                    if ($try["grade"] < $lowscore || $lowscore === null) {
                        $lowscore = $try["grade"];
                    }
                    if ($timetotake > $hightime || $hightime == null) {
                        $hightime = $timetotake;
                    }
                    if ($timetotake < $lowtime || $lowtime == null) {
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
    $students->close();
    // print it all out !
    if (has_capability('mod/lesson:edit', $context)) {
        echo  "<form id=\"theform\" method=\"post\" action=\"report.php\">\n
               <input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />\n
               <input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
    }
    echo html_writer::table($table);
    if (has_capability('mod/lesson:edit', $context)) {
        $checklinks  = '<a href="javascript: checkall();">'.get_string('selectall').'</a> / ';
        $checklinks .= '<a href="javascript: checknone();">'.get_string('deselectall').'</a>';
        $checklinks .= html_writer::label('action', 'menuaction', false, array('class' => 'accesshide'));
        $checklinks .= html_writer::select(array('delete' => get_string('deleteselected')), 'action', 0, array(''=>'choosedots'), array('id'=>'actionid', 'class' => 'autosubmit'));
        $PAGE->requires->yui_module('moodle-core-formautosubmit',
            'M.core.init_formautosubmit',
            array(array('selectid' => 'actionid', 'nothing' => false))
        );
        echo $OUTPUT->box($checklinks, 'center');
        echo '</form>';
    }

    // some stat calculations
    if ($numofattempts == 0) {
        $avescore = get_string("notcompleted", "lesson");
    } else {
        $avescore = format_float($avescore/$numofattempts, 2);
    }
    if ($avetime == null) {
        $avetime = get_string("notcompleted", "lesson");
    } else {
        $avetime = format_float($avetime/$numofattempts, 0);
        $avetime = format_time($avetime);
    }
    if ($hightime == null) {
        $hightime = get_string("notcompleted", "lesson");
    } else {
        $hightime = format_time($hightime);
    }
    if ($lowtime == null) {
        $lowtime = get_string("notcompleted", "lesson");
    } else {
        $lowtime = format_time($lowtime);
    }
    if ($highscore === null) {
        $highscore = get_string("notcompleted", "lesson");
    }
    if ($lowscore === null) {
        $lowscore = get_string("notcompleted", "lesson");
    }

    // output the stats
    echo $OUTPUT->heading(get_string('lessonstats', 'lesson'), 3);
    $stattable = new html_table();
    $stattable->head = array(get_string('averagescore', 'lesson'), get_string('averagetime', 'lesson'),
                            get_string('highscore', 'lesson'), get_string('lowscore', 'lesson'),
                            get_string('hightime', 'lesson'), get_string('lowtime', 'lesson'));
    $stattable->align = array('center', 'center', 'center', 'center', 'center', 'center');
    $stattable->wrap = array('nowrap', 'nowrap', 'nowrap', 'nowrap', 'nowrap', 'nowrap');
    $stattable->attributes['class'] = 'standardtable generaltable';

    if (is_numeric($highscore)) {
        $highscore .= '%';
    }
    if (is_numeric($lowscore)) {
        $lowscore .= '%';
    }
    $stattable->data[] = array($avescore.'%', $avetime, $highscore, $lowscore, $hightime, $lowtime);

    echo html_writer::table($stattable);
} else if ($action === 'reportdetail') {
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
    echo $lessonoutput->header($lesson, $cm, $action, false, null, get_string('detailedstats', 'lesson'));
    groups_print_activity_menu($cm, $url);

    $course_context = context_course::instance($course->id);
    if (has_capability('gradereport/grader:view', $course_context) && has_capability('moodle/grade:viewall', $course_context)) {
        $seeallgradeslink = new moodle_url('/grade/report/grader/index.php', array('id'=>$course->id));
        $seeallgradeslink = html_writer::link($seeallgradeslink, get_string('seeallcoursegrades', 'grades'));
        echo $OUTPUT->box($seeallgradeslink, 'allcoursegrades');
    }

    $formattextdefoptions = new stdClass;
    $formattextdefoptions->para = false;  //I'll use it widely in this page
    $formattextdefoptions->overflowdiv = true;

    $userid = optional_param('userid', null, PARAM_INT); // if empty, then will display the general detailed view
    $try    = optional_param('try', null, PARAM_INT);

    if (!empty($userid)) {
        // Apply overrides.
        $lesson->update_effective_access($userid);
    }

    $lessonpages = $lesson->load_all_pages();
    foreach ($lessonpages as $lessonpage) {
        if ($lessonpage->prevpageid == 0) {
            $pageid = $lessonpage->id;
        }
    }

    // now gather the stats into an object
    $firstpageid = $pageid;
    $pagestats = array();
    while ($pageid != 0) { // EOL
        $page = $lessonpages[$pageid];
        $params = array ("lessonid" => $lesson->id, "pageid" => $page->id);
        if ($allanswers = $DB->get_records_select("lesson_attempts", "lessonid = :lessonid AND pageid = :pageid", $params, "timeseen")) {
            // get them ready for processing
            $orderedanswers = array();
            foreach ($allanswers as $singleanswer) {
                // ordering them like this, will help to find the single attempt record that we want to keep.
                $orderedanswers[$singleanswer->userid][$singleanswer->retry][] = $singleanswer;
            }
            // this is foreach user and for each try for that user, keep one attempt record
            foreach ($orderedanswers as $orderedanswer) {
                foreach($orderedanswer as $tries) {
                    $page->stats($pagestats, $tries);
                }
            }
        } else {
            // no one answered yet...
        }
        //unset($orderedanswers);  initialized above now
        $pageid = $page->nextpageid;
    }

    $manager = lesson_page_type_manager::get($lesson);
    $qtypes = $manager->get_page_type_strings();

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
        // Set some defaults for the answer data.
        $answerdata->score = null;
        $answerdata->response = null;
        $answerdata->responseformat = FORMAT_PLAIN;

        $answerpage->title = format_string($page->title);

        $options = new stdClass;
        $options->noclean = true;
        $options->overflowdiv = true;
        $options->context = $context;
        $answerpage->contents = format_text($page->contents, $page->contentsformat, $options);

        $answerpage->qtype = $qtypes[$page->qtype].$page->option_description_string();
        $answerpage->grayout = $page->grayout;
        $answerpage->context = $context;

        if (empty($userid)) {
            // there is no userid, so set these vars and display stats.
            $answerpage->grayout = 0;
            $useranswer = null;
        } elseif ($useranswers = $DB->get_records("lesson_attempts",array("lessonid"=>$lesson->id, "userid"=>$userid, "retry"=>$try,"pageid"=>$page->id), "timeseen")) {
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
            $useranswer = null;
        }
        $i = 0;
        $n = 0;
        $answerpages[] = $page->report_answers(clone($answerpage), clone($answerdata), $useranswer, $pagestats, $i, $n);
        $pageid = $page->nextpageid;
    }

    /// actually start printing something
    $table = new html_table();
    $table->wrap = array();
    $table->width = "60%";
    if (!empty($userid)) {
        // if looking at a students try, print out some basic stats at the top

            // print out users name
            //$headingobject->lastname = $students[$userid]->lastname;
            //$headingobject->firstname = $students[$userid]->firstname;
            //$headingobject->attempt = $try + 1;
            //print_heading(get_string("studentattemptlesson", "lesson", $headingobject));
        echo $OUTPUT->heading(get_string('attempt', 'lesson', $try+1), 3);

        $table->head = array();
        $table->align = array('right', 'left');
        $table->attributes['class'] = 'compacttable generaltable';

        $params = array("lessonid"=>$lesson->id, "userid"=>$userid);
        if (!$grades = $DB->get_records_select("lesson_grades", "lessonid = :lessonid and userid = :userid", $params, "completed", "*", $try, 1)) {
            $grade = -1;
            $completed = -1;
        } else {
            $grade = current($grades);
            $completed = $grade->completed;
            $grade = round($grade->grade, 2);
        }
        if (!$times = $DB->get_records_select("lesson_timer", "lessonid = :lessonid and userid = :userid", $params, "starttime", "*", $try, 1)) {
            $timetotake = -1;
        } else {
            $timetotake = current($times);
            $timetotake = $timetotake->lessontime - $timetotake->starttime;
        }

        if ($timetotake == -1 || $completed == -1 || $grade == -1) {
            $table->align = array("center");

            $table->data[] = array(get_string("notcompleted", "lesson"));
        } else {
            $user = $DB->get_record('user', array('id' => $userid));

            $gradeinfo = lesson_grade($lesson, $try, $user->id);

            $table->data[] = array(get_string('name').':', $OUTPUT->user_picture($user, array('courseid'=>$course->id)).fullname($user, true));
            $table->data[] = array(get_string("timetaken", "lesson").":", format_time($timetotake));
            $table->data[] = array(get_string("completed", "lesson").":", userdate($completed));
            $table->data[] = array(get_string('rawgrade', 'lesson').':', $gradeinfo->earned.'/'.$gradeinfo->total);
            $table->data[] = array(get_string("grade", "lesson").":", $grade."%");
        }
        echo html_writer::table($table);

        // Don't want this class for later tables
        $table->attributes['class'] = '';
    }


    $table->align = array('left', 'left');
    $table->size = array('70%', null);
    $table->attributes['class'] = 'compacttable generaltable';

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
        $table->data[] = array($fontstart.get_string("answer", "lesson").":".$fontend, ' ');
        // apply the font to each answer
        if (!empty($page->answerdata)) {
            foreach ($page->answerdata->answers as $answer){
                $modified = array();
                foreach ($answer as $single) {
                    // need to apply a font to each one
                    $modified[] = $fontstart2.$single.$fontend2;
                }
                $table->data[] = $modified;
            }
            if (isset($page->answerdata->response)) {
                $table->data[] = array($fontstart.get_string("response", "lesson").": <br />".$fontend
                        .$fontstart2.$page->answerdata->response.$fontend2, " ");
            }
            $table->data[] = array($page->answerdata->score, " ");
        } else {
            $table->data[] = array(get_string('didnotanswerquestion', 'lesson'), " ");
        }
        echo html_writer::start_tag('div', array('class' => 'no-overflow'));
        echo html_writer::table($table);
        echo html_writer::end_tag('div');
    }
} else {
    print_error('unknowaction');
}

/// Finish the page
echo $OUTPUT->footer();
