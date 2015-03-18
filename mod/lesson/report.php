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
$action = optional_param('action', 'reportoverview', PARAM_ALPHA);  // Action to take.
$nothingtodisplay = false;

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST));

require_login($course, false, $cm);

$currentgroup = groups_get_activity_group($cm, true);

$context = context_module::instance($cm->id);
require_capability('mod/lesson:viewreports', $context);

// Only load students if there attempts for this lesson.

list($esql, $params) = get_enrolled_sql($context, '', $currentgroup, true);
list($sort, $sortparams) = users_order_by_sql('u');

$params['lessonid'] = $lesson->id;
$ufields = user_picture::fields('u');
$sql = "SELECT $ufields, a.retry as try, a.userid
        FROM {user} u
        JOIN {lesson_attempts} a ON u.id = a.userid
        JOIN ($esql) ue ON ue.id = a.userid
        WHERE a.lessonid = :lessonid
        GROUP BY $ufields, a.retry, a.userid
        ORDER BY $sort, a.retry";

$studentattempts = $DB->get_recordset_sql($sql, $params);
if (!$studentattempts->valid()) {
    $nothingtodisplay = true;
}

$url = new moodle_url('/mod/lesson/report.php', array('id' => $id));
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
    // Process any form data before fetching attempts, grades and times.
    if (has_capability('mod/lesson:edit', $context) and $form = data_submitted() and confirm_sesskey()) {
        // Cycle through array of userids with nested arrays of tries.
        if (!empty($form->attempts)) {
            foreach ($form->attempts as $userid => $tries) {
                // Modifier IS VERY IMPORTANT!  What does it do?
                //      Well, it is for when you delete multiple attempts for the same user.
                //      If you delete try 1 and 3 for a user, then after deleting try 1, try 3 then
                //      becomes try 2 (because try 1 is gone and all tries after try 1 get decremented).
                //      So, the modifier makes sure that the submitted try refers to the current try in the
                //      database - hope this all makes sense.
                $modifier = 0;

                foreach ($tries as $try => $junk) {
                    $try -= $modifier;

                    // Clean up the timer table by removing using the order.
                    // this is silly, it should be linked to specific attempt (skodak).
                    $params = array ("userid" => $userid, "lessonid" => $lesson->id);
                    $timers = $DB->get_records_sql("SELECT id FROM {lesson_timer}
                                                     WHERE userid = :userid AND lessonid = :lessonid
                                                  ORDER BY starttime", $params, $try, 1);
                    if ($timers) {
                        $timer = reset($timers);
                        $DB->delete_records('lesson_timer', array('id' => $timer->id));
                    }

                    // Remove the grade from the grades and high_scores tables
                    // this is silly, it should be linked to specific attempt (skodak).
                    $grades = $DB->get_records_sql("SELECT id FROM {lesson_grades}
                                                     WHERE userid = :userid AND lessonid = :lessonid
                                                  ORDER BY completed", $params, $try, 1);

                    if ($grades) {
                        $grade = reset($grades);
                        $DB->delete_records('lesson_grades', array('id' => $grade->id));
                        $params = array('gradeid' => $grade->id, 'lessonid' => $lesson->id, 'userid' => $userid);
                        $DB->delete_records('lesson_high_scores', $params);
                    }

                    // Remove attempts and update the retry number.
                    $DB->delete_records('lesson_attempts', array('userid' => $userid, 'lessonid' => $lesson->id, 'retry' => $try));
                    $sql = "UPDATE {lesson_attempts} SET retry = retry - 1 WHERE userid = ? AND lessonid = ? AND retry > ?";
                    $DB->execute($sql, array($userid, $lesson->id, $try));

                    // Remove seen branches and update the retry number.
                    $DB->delete_records('lesson_branch', array('userid' => $userid, 'lessonid' => $lesson->id, 'retry' => $try));
                    $sql = "UPDATE {lesson_branch} SET retry = retry - 1 WHERE userid = ? AND lessonid = ? AND retry > ?";
                    $DB->execute($sql, array($userid, $lesson->id, $try));

                    // Update central gradebook.
                    lesson_update_grades($lesson, $userid);

                    $modifier++;
                }
            }
        }
    }
    redirect(new moodle_url($PAGE->url, array('action' => 'reportoverview')));

} else if ($action === 'reportoverview') {
    /**************************************************************************
    this action is for default view and overview view
    **************************************************************************/
    echo $lessonoutput->header($lesson, $cm, $action, false, null, get_string('overview', 'lesson'));
    groups_print_activity_menu($cm, $url);

    $coursecontext = context_course::instance($course->id);
    if (has_capability('gradereport/grader:view', $coursecontext) && has_capability('moodle/grade:viewall', $coursecontext)) {
        $seeallgradeslink = new moodle_url('/grade/report/grader/index.php', array('id' => $course->id));
        $seeallgradeslink = html_writer::link($seeallgradeslink, get_string('seeallcoursegrades', 'grades'));
        echo $OUTPUT->box($seeallgradeslink, 'allcoursegrades');
    }

    // Build an array for output.
    $studentdata = array();
    $prevstudentid = null;
    $grades = null;
    $timers = null;
    foreach ($studentattempts as $studentattempt) {
        if ($studentattempt->userid != $prevstudentid) {
            $studentdata[$studentattempt->userid] = array();

            // Get the grades and timers for this user.
            $params = array("lessonid" => $lesson->id, "userid" => $studentattempt->userid);
            $grades = $DB->get_records("lesson_grades", $params, "completed");
            $grades = array_values($grades);
            $timers = $DB->get_records("lesson_timer", $params, "starttime");
            $timers = array_values($timers);
        }

        if (isset($grades[$studentattempt->try])) {
            $grade = $grades[$studentattempt->try]->grade;
        } else {
            $grade = null;
        }

        if (isset($timers[$studentattempt->try])) {
            $timestart = $timers[$studentattempt->try]->starttime;
            $timeend = $timers[$studentattempt->try]->lessontime;
        } else {
            $timestart = "";
            $timeend = "";
        }

        $studentdata[$studentattempt->userid][$studentattempt->try] = array(  "firstname" => $studentattempt->firstname,
                                                                                "lastname" => $studentattempt->lastname,
                                                                                "timestart" => $timestart,
                                                                                "timeend" => $timeend,
                                                                                "grade" => $grade,
                                                                                "try" => $studentattempt->try,
                                                                                "userid" => $studentattempt->userid
                                                                            );
        $prevstudentid = $studentattempt->userid;
    }
    $studentattempts->close();

    // Set all the stats variables.
    $numofattempts = 0;
    $avescore      = 0;
    $avetime       = 0;
    $highscore     = null;
    $lowscore      = null;
    $hightime      = null;
    $lowtime       = null;

    $table = new html_table();

    // Set up the table object.
    $table->head = array(get_string('name'), get_string('attempts', 'lesson'), get_string('highscore', 'lesson'));
    $table->align = array('center', 'left', 'left');
    $table->wrap = array('nowrap', 'nowrap', 'nowrap');
    $table->attributes['class'] = 'standardtable generaltable';
    $table->size = array(null, '70%', null);

    $prevstudentid = null;
    foreach ($studentdata as $userid => $tries) {
        // Set/reset some variables.
        $attempts = array();
        $bestgrade = 0;
        $studentname = "";

        foreach ($tries as $n => $try) {
            if ($n == 0) {
                $student = $DB->get_record('user', array("id" => $try["userid"]));
                $studentname = fullname($student, true);
            }

            // Start to build up the checkbox and link.
            if (has_capability('mod/lesson:edit', $context)) {
                $temp = '<input type="checkbox" id="attempts" name="attempts['.$try['userid'].']['.$try['try'].']" /> ';
            } else {
                $temp = '';
            }

            $temp .= "<a href=\"report.php?id=$cm->id&amp;action=reportdetail&amp;";
            $temp .= "userid=".$try['userid']."&amp;try=".$try['try']." class=\"lesson-attempt-link\">";
            if ($try["grade"] !== null) { // If null then not done yet.
                // This is what the link does when the user has completed the try.
                $timetotake = $try["timeend"] - $try["timestart"];

                $temp .= $try["grade"]."%";
                if ($try["grade"] > $bestgrade) {
                    $bestgrade = $try["grade"];
                }
                if (empty($try["timestart"])) {
                    $temp .= "&nbsp;---";
                    $temp .= ",&nbsp;(---)</a>";
                } else {
                    $temp .= "&nbsp;".userdate($try["timestart"]);
                    $temp .= ",&nbsp;(".format_time($timetotake).")</a>";
                }
            } else {
                // This is what the link does/looks like when the user has not completed the try.
                $temp .= get_string("notcompleted", "lesson");
                $temp .= "&nbsp;".userdate($try["timestart"])."</a>";
                $timetotake = null;
            }
            // Build up the attempts array.
            $attempts[] = $temp;

            // Run these lines for the stats only if the user finnished the lesson.
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
        // Get line breaks in after each attempt.
        $attempts = implode("<br />\n", $attempts);
        // Add it to the table data[] object.
        $table->data[] = array($studentname, $attempts, $bestgrade."%");

    }

    // Print it all out!
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
        $options = array('delete' => get_string('deleteselected'));
        $nothing = array('' => 'choosedots');
        $attributes = array('id' => 'actionid', 'class' => 'autosubmit');
        $checklinks .= html_writer::select($options, 'action', 0, $nothing, $attributes);
        $PAGE->requires->yui_module('moodle-core-formautosubmit',
            'M.core.init_formautosubmit',
            array(array('selectid' => 'actionid', 'nothing' => false))
        );
        echo $OUTPUT->box($checklinks, 'center');
        echo '</form>';
    }

    // Some stat calculations.
    if ($numofattempts == 0) {
        $avescore = get_string("notcompleted", "lesson");
    } else {
        $avescore = format_float($avescore / $numofattempts, 2);
    }
    if ($avetime == null) {
        $avetime = get_string("notcompleted", "lesson");
    } else {
        $avetime = format_float($avetime / $numofattempts, 0);
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

    // Output the stats.
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

    $coursecontext = context_course::instance($course->id);
    if (has_capability('gradereport/grader:view', $coursecontext) && has_capability('moodle/grade:viewall', $coursecontext)) {
        $seeallgradeslink = new moodle_url('/grade/report/grader/index.php', array('id' => $course->id));
        $seeallgradeslink = html_writer::link($seeallgradeslink, get_string('seeallcoursegrades', 'grades'));
        echo $OUTPUT->box($seeallgradeslink, 'allcoursegrades');
    }

    $formattextdefoptions = new stdClass;
    $formattextdefoptions->para = false;  // I'll use it widely in this page.
    $formattextdefoptions->overflowdiv = true;

    $userid = optional_param('userid', null, PARAM_INT); // If empty, then will display the general detailed view.
    $try    = optional_param('try', null, PARAM_INT);

    $lessonpages = $lesson->load_all_pages();
    foreach ($lessonpages as $lessonpage) {
        if ($lessonpage->prevpageid == 0) {
            $pageid = $lessonpage->id;
        }
    }

    // Now gather the stats into an object.
    $firstpageid = $pageid;
    $pagestats = array();
    while ($pageid != 0) { // EOL.
        $page = $lessonpages[$pageid];
        $params = array ("lessonid" => $lesson->id, "pageid" => $page->id);
        $where = "lessonid = :lessonid AND pageid = :pageid";
        if ($allanswers = $DB->get_records_select("lesson_attempts", $where, $params, "timeseen")) {
            // Get them ready for processing.
            $orderedanswers = array();
            foreach ($allanswers as $singleanswer) {
                // Ordering them like this, will help to find the single attempt record that we want to keep.
                $orderedanswers[$singleanswer->userid][$singleanswer->retry][] = $singleanswer;
            }
            // This is foreach user and for each try for that user, keep one attempt record.
            foreach ($orderedanswers as $orderedanswer) {
                foreach ($orderedanswer as $tries) {
                    $page->stats($pagestats, $tries);
                }
            }
        }
        $pageid = $page->nextpageid;
    }

    $manager = lesson_page_type_manager::get($lesson);
    $qtypes = $manager->get_page_type_strings();

    $answerpages = array();
    $answerpage = "";
    $pageid = $firstpageid;
    // Cycle through all the pages
    // foreach page, add to the $answerpages[] array all the data that is needed
    // from the question, the users attempt, and the statistics
    // grayout pages that the user did not answer and Branch, end of branch, cluster
    // and end of cluster pages.
    while ($pageid != 0) { // EOL.
        $page = $lessonpages[$pageid];
        $answerpage = new stdClass;
        $data = '';

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
            // There is no userid, so set these vars and display stats.
            $answerpage->grayout = 0;
            $useranswer = null;
            $params = array("lessonid" => $lesson->id, "userid" => $userid, "retry" => $try, "pageid" => $page->id);
        } else if ($useranswers = $DB->get_records("lesson_attempts", $params, "timeseen")) {
            // Get the user's answer for this page.
            // Need to find the right one.
            $i = 0;
            foreach ($useranswers as $userattempt) {
                $useranswer = $userattempt;
                $i++;
                if ($lesson->maxattempts == $i) {
                    break; // Reached maxattempts, break out.
                }
            }
        } else {
            // User did not answer this page, gray it out and set some nulls.
            $answerpage->grayout = 1;
            $useranswer = null;
        }
        $i = 0;
        $n = 0;
        $answerpages[] = $page->report_answers(clone($answerpage), clone($answerdata), $useranswer, $pagestats, $i, $n);
        $pageid = $page->nextpageid;
    }

    // Actually start printing something.
    $table = new html_table();
    $table->wrap = array();
    $table->width = "60%";
    if (!empty($userid)) {
        // If looking at a students try, print out some basic stats at the top.
        echo $OUTPUT->heading(get_string('attempt', 'lesson', $try + 1), 3);

        $table->head = array();
        $table->align = array('right', 'left');
        $table->attributes['class'] = 'compacttable generaltable';

        $params = array("lessonid" => $lesson->id, "userid" => $userid);
        $where = "lessonid = :lessonid and userid = :userid";
        if (!$grades = $DB->get_records_select("lesson_grades", $where, $params, "completed", "*", $try, 1)) {
            $grade = -1;
            $completed = -1;
        } else {
            $grade = current($grades);
            $completed = $grade->completed;
            $grade = round($grade->grade, 2);
        }
        $where = "lessonid = :lessonid and userid = :userid";
        if (!$times = $DB->get_records_select("lesson_timer", $where, $params, "starttime", "*", $try, 1)) {
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

            $name = $OUTPUT->user_picture($user, array('courseid' => $course->id)).fullname($user, true);
            $table->data[] = array(get_string('name').':', $name);
            $table->data[] = array(get_string("timetaken", "lesson").":", format_time($timetotake));
            $table->data[] = array(get_string("completed", "lesson").":", userdate($completed));
            $table->data[] = array(get_string('rawgrade', 'lesson').':', $gradeinfo->earned.'/'.$gradeinfo->total);
            $table->data[] = array(get_string("grade", "lesson").":", $grade."%");
        }
        echo html_writer::table($table);

        // Don't want this class for later tables.
        $table->attributes['class'] = '';
    }


    $table->align = array('left', 'left');
    $table->size = array('70%', null);
    $table->attributes['class'] = 'compacttable generaltable';

    foreach ($answerpages as $page) {
        unset($table->data);
        if ($page->grayout) { // Set the color of text.
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

        $title = $fontstart2.$page->qtype.": ".format_string($page->title).$fontend2;
        $table->head = array($title, $fontstart2.get_string("classstats", "lesson").$fontend2);
        $title = $fontend.$fontstart2.$page->contents.$fontend2;
        $table->data[] = array($fontstart.get_string("question", "lesson").": <br />".$title, " ");
        $table->data[] = array($fontstart.get_string("answer", "lesson").":".$fontend, ' ');
        // Apply the font to each answer.
        if (!empty($page->answerdata)) {
            foreach ($page->answerdata->answers as $answer) {
                $modified = array();
                foreach ($answer as $single) {
                    // Need to apply a font to each one.
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

// Finish the page.
echo $OUTPUT->footer();
