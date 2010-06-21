<?php  // $Id$

// exercise constants and standard moodle functions plus those functions called directly
// see locallib.php for other non-standard exercise functions

require_once($CFG->libdir.'/filelib.php');
require_once('locallib.php');

/*** Constants **********************************/

$EXERCISE_TYPE = array (0 => get_string("notgraded", "exercise"),
                          1 => get_string("accumulative", "exercise"),
                          2 => get_string("errorbanded", "exercise"),
                          3 => get_string("criterion", "exercise"),
                          4 => get_string("rubric", "exercise") );

$EXERCISE_SCALES = array(
                    0 => array( 'name' => get_string("scaleyes", "exercise"), 'type' => 'radio', 'size' => 2, 'start' => get_string("yes"), 'end' => get_string("no")),
                    1 => array( 'name' => get_string("scalepresent", "exercise"), 'type' => 'radio', 'size' => 2, 'start' => get_string("present", "exercise"), 'end' => get_string("absent", "exercise")),
                    2 => array( 'name' => get_string("scalecorrect", "exercise"), 'type' => 'radio', 'size' => 2, 'start' => get_string("correct", "exercise"), 'end' => get_string("incorrect", "exercise")),
                    3 => array( 'name' => get_string("scalegood3", "exercise"), 'type' => 'radio', 'size' => 3, 'start' => get_string("good", "exercise"), 'end' => get_string("poor", "exercise")),
                    4 => array( 'name' => get_string("scaleexcellent4", "exercise"), 'type' => 'radio', 'size' => 4, 'start' => get_string("excellent", "exercise"), 'end' => get_string("verypoor", "exercise")),
                    5 => array( 'name' => get_string("scaleexcellent5", "exercise"), 'type' => 'radio', 'size' => 5, 'start' => get_string("excellent", "exercise"), 'end' => get_string("verypoor", "exercise")),
                    6 => array( 'name' => get_string("scaleexcellent7", "exercise"), 'type' => 'radio', 'size' => 7, 'start' => get_string("excellent", "exercise"), 'end' => get_string("verypoor", "exercise")),
                    7 => array( 'name' => get_string("scale10", "exercise"), 'type' => 'selection', 'size' => 10),
                    8 => array( 'name' => get_string("scale20", "exercise"), 'type' => 'selection', 'size' => 20),
                    9 => array( 'name' => get_string("scale100", "exercise"), 'type' => 'selection', 'size' => 100));

$EXERCISE_EWEIGHTS = array(  0 => -4.0, 1 => -2.0, 2 => -1.5, 3 => -1.0, 4 => -0.75, 5 => -0.5,  6 => -0.25,
                             7 => 0.0, 8 => 0.25, 9 => 0.5, 10 => 0.75, 11=> 1.0, 12 => 1.5, 13=> 2.0, 14 => 4.0);

$EXERCISE_ASSESSMENT_COMPS = array (
                          0 => array('name' => get_string("verylax", "exercise"), 'value' => 1),
                          1 => array('name' => get_string("lax", "exercise"), 'value' => 0.6),
                          2 => array('name' => get_string("fair", "exercise"), 'value' => 0.4),
                          3 => array('name' => get_string("strict", "exercise"), 'value' => 0.33),
                          4 => array('name' => get_string("verystrict", "exercise"), 'value' => 0.2) );

/*** Standard Moodle functions ******************
function exercise_add_instance($exercise)
function exercise_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
function exercise_cron ()
function exercise_delete_instance($id)
function exercise_grades($exerciseid)
function exercise_print_recent_activity(&$logs, $isteacher=false)
function exercise_update_instance($exercise)
function exercise_user_outline($course, $user, $mod, $exercise)
function exercise_user_complete($course, $user, $mod, $exercise)
**********************************************/

/*******************************************************************/
function exercise_add_instance($exercise) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will create a new instance and return the id number
// of the new instance.

    $exercise->timemodified = time();

    // encode password if necessary
    if (!empty($exercise->password)) {
        $exercise->password = md5($exercise->password);
    } else {
        unset($exercise->password);
    }

    if ($returnid = insert_record("exercise", $exercise)) {

        $event = NULL;
        $event->name        = $exercise->name;
        $event->description = $exercise->description;
        $event->courseid    = $exercise->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'exercise';
        $event->instance    = $returnid;
        $event->eventtype   = 'deadline';
        $event->timestart   = $exercise->deadline;
        $event->timeduration = 0;

        add_event($event);
    }


    return $returnid;
}


/*******************************************************************/
function exercise_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
/// Given an array of value, creates a popup menu to be part of a form
/// $options["value"]["label"]

    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    if ($script) {
        $javascript = "onChange=\"$script\"";
    } else {
        $javascript = "";
    }

    $output = "<select name=\"$name\" $javascript>\n";
    if ($nothing) {
        $output .= "   <option value=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " selected=\"selected\"";
        }
        $output .= ">$nothing</option>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <option value=\"$value\"";
            if ($value == $selected) {
                $output .= " selected=\"selected\"";
            }
            // stop zero label being replaced by array index value
            // if ($label) {
            //    $output .= ">$label</option>\n";
            // } else {
            //     $output .= ">$value</option>\n";
            //  }
            $output .= ">$label</option>\n";

        }
    }
    $output .= "</select>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}


/*******************************************************************/
function exercise_cron() {
// Function to be run periodically according to the moodle cron
// Finds all exercise notifications that have yet to be mailed out, and mails them

    global $CFG, $USER;

    $cutofftime = time() - $CFG->maxeditingtime;

    // look for new assessments
    if ($assessments = exercise_get_unmailed_assessments($cutofftime)) {
        $timenow = time();

        foreach ($assessments as $assessment) {
            echo "Processing exercise assessment $assessment->id\n";
            // switch on mailed
            if (! set_field("exercise_assessments", "mailed", "1", "id", "$assessment->id")) {
                echo "Could not update the mailed field for id $assessment->id\n";
                }
            if (! $submission = get_record("exercise_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
                continue;
            }
            if (! $exercise = get_record("exercise", "id", $submission->exerciseid)) {
                echo "Could not find exercise record for id $submission->exerciseid\n";
                continue;
            }
            if (! $course = get_record("course", "id", "$exercise->course")) {
                echo "Could not find course $exercise->course\n";
                continue;
            }
            if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
                error("Course Module ID was incorrect");
                continue;
            }
            if (! $submissionowner = get_record("user", "id", "$submission->userid")) {
                echo "Could not find user $submission->userid\n";
                continue;
            }
            if (! $assessmentowner = get_record("user", "id", "$assessment->userid")) {
                echo "Could not find user $assessment->userid\n";
                continue;
            }
            if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, $assessmentowner->id)) {
                continue;  // Not an active participant
            }

            // if the submission belongs to a teacher it's a student assessment. No need to email anything.
            if (isteacher($course->id, $submissionowner->id)) {
                continue;
            }

            $strexercises = get_string("modulenameplural", "exercise");
            $strexercise  = get_string("modulename", "exercise");

            // it's an assessment, tell the submission owner
            $USER->lang = $submissionowner->lang;
            $sendto = $submissionowner;
            // "Your assignment \"$submission->title\" has been assessed by"
            $msg = get_string("mail1", "exercise", $submission->title).' '.fullname($assessmentowner).".\n";
            // "The comments and grade can be seen in the exercise assignment '$exercise->name'
            $msg .= get_string("mail2", "exercise", format_string($exercise->name,true))."\n\n";

            $postsubject = "$course->shortname: $strexercises: ".format_string($exercise->name,true);
            $posttext  = "$course->shortname -> $strexercises -> ".format_string($exercise->name,true)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            // "You can see it in your exercise assignment"
            $posttext .= get_string("mail3", "exercise").":\n";
            $posttext .= "   $CFG->wwwroot/mod/exercise/view.php?id=$cm->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
                $posthtml = "<p><font face=\"sans-serif\">".
              "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
              "<a href=\"$CFG->wwwroot/mod/exercise/index.php?id=$course->id\">$strexercises</a> ->".
              "<a href=\"$CFG->wwwroot/mod/exercise/view.php?id=$cm->id\">".format_string($exercise->name,true)."</a></font></p>";
              $posthtml .= "<hr /><font face=\"sans-serif\">";
              $posthtml .= "<p>$msg</p>";
              $posthtml .= "<p>".get_string("mail3", "exercise").
                  " <a href=\"$CFG->wwwroot/mod/exercise/view.php?id=$cm->id\">".format_string($exercise->name,true)."</a>.</p></font><hr />";
            } else {
              $posthtml = "";
            }

            if (!$teacher = get_teacher($course->id)) {
                echo "Error: can not find teacher for course $course->id!\n";
            }

            if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: exercise cron: Could not send out mail for id $submission->id to user $sendto->id ($sendto->email)\n";
            }
        }
    }
    return true;
}


/*******************************************************************/
function exercise_delete_instance($id) {
// Given an ID of an instance of this module,
// this function will permanently delete the instance
// and any data that depends on it.

    if (! $exercise = get_record("exercise", "id", "$id")) {
        return false;
    }

    // delete all the associated records in the exercise tables, start positive...
    $result = true;

    if (! delete_records("exercise_grades", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_rubrics", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_elements", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_assessments", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_submissions", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise", "id", "$exercise->id")) {
        $result = false;
    }

    return $result;
}


/*******************************************************************/
function exercise_grades($exerciseid) {
/// Must return an array of grades, indexed by user, and a max grade.
    if (!$exercise = get_record("exercise", "id", $exerciseid)) {
        error("Exercise record not found");
    }

    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }

    if (!$return->maxgrade = ($exercise->grade + $exercise->gradinggrade)) {
        return NULL;
    }
    // how to handle multiple submissions?
    if ($exercise->usemaximum) {
        if ($bestgrades = exercise_get_best_submission_grades($exercise)) {
            foreach ($bestgrades as $grade) {
                $usergrade[$grade->userid] = $grade->grade * $exercise->grade / 100.0;
            }
        }
    }

    else { // use mean values
        if ($meangrades = exercise_get_mean_submission_grades($exercise)) {
            foreach ($meangrades as $grade) {
                $usergrade[$grade->userid] = $grade->grade * $exercise->grade / 100.0;
            }
        }
    }
    // now get the users grading grades
    if ($assessments = exercise_get_teacher_submission_assessments($exercise)) {
        foreach ($assessments as $assessment) {
            // add the grading grade if the student's work has been assessed
            if (isset($usergrade[$assessment->userid])) {
                $usergrade[$assessment->userid] += $assessment->gradinggrade * $exercise->gradinggrade / 100.0;
            }
        }
    }
    // tidy the numbers and set up the return array
    if (isset($usergrade)) {
        foreach ($usergrade as $userid => $g) {
            $return->grades[$userid] = number_format($g, 1);
        }
    }
    return $return;
}


/*******************************************************************/
function exercise_print_recent_activity($course, $viewfullanmes, $timestart) {
    global $CFG;

    $isteacher = has_capability('mod/exercise:assess', get_context_instance(CONTEXT_COURSE, $course->id));

    $modinfo = get_fast_modinfo($course);

    // have a look for new submissions (only show to teachers)
    $submitcontent = false;
    if ($isteacher) {
        if ($logs = exercise_get_submit_logs($course, $timestart)) {
            // if we got some 'live' ones then output them
            $submitcontent = true;
            print_headline(get_string('exercisesubmissions', 'exercise').':');
            foreach ($logs as $log) {
                print_recent_activity_note($log->time, $log, $log->name,
                                           $CFG->wwwroot.'/mod/exercise/'.str_replace('&', '&amp;', $log->url));
            }
        }
    }

    // have a look for new assessment gradings for this user
    $gradecontent = false;
    if ($logs = exercise_get_grade_logs($course, $timestart)) {
        // got some, see if any belong to a visible module
        foreach ($logs as $id=>$log) {
            $cm = $modinfo->instances['exercise'][$log->exerciseid];
            if (!$cm->uservisible) {
                unset($logs[$id]);
                continue;
            }
        }
        // if we got some "live" ones then output them
        if ($logs) {
            $gradecontent = true;
            print_headline(get_string('exercisefeedback', 'exercise').':');
            foreach ($logs as $log) {
                print_recent_activity_note($log->time, $log, $log->name,
                                           $CFG->wwwroot.'/mod/exercise/'.str_replace('&', '&amp;', $log->url));
            }
        }
    }

    // have a look for new assessments for this user
    $assesscontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = exercise_get_assess_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $id=>$log) {
                $cm = $modinfo->instances['exercise'][$log->exerciseid];
                if (!$cm->uservisible) {
                    unset($logs[$id]);
                    continue;
                }
            }
            // if we got some "live" ones then output them
            if ($logs) {
                $assesscontent = true;
                print_headline(get_string('exerciseassessments', 'exercise').':');
                foreach ($logs as $log) {
                    print_recent_activity_note($log->time, $log, $log->name,
                                               $CFG->wwwroot.'/mod/exercise/'.str_replace('&', '&amp;', $log->url));
                }
            }
        }
    }
    return $submitcontent or $gradecontent or $assesscontent;
}


///////////////////////////////////////////////////////////////////////////////
function exercise_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every exercise event in the site is checked, else
// only exercise events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid == 0) {
        if (! $exercises = get_records("exercise")) {
            return true;
        }
    } else {
        if (! $exercises = get_records("exercise", "course", $courseid)) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'exercise');

    foreach ($exercises as $exercise) {
        $event = NULL;
        $event->name        = addslashes($exercise->name);
        $event->description = addslashes($exercise->description);
        $event->timestart   = $exercise->deadline;

        if ($event->id = get_field('event', 'id', 'modulename', 'exercise', 'instance', $exercise->id)) {
            update_event($event);

        } else {
            $event->courseid    = $exercise->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'exercise';
            $event->instance    = $exercise->id;
            $event->eventtype   = 'deadline';
            $event->timeduration = 0;
            $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $exercise->id);
            add_event($event);
        }

    }
    return true;
}


/*******************************************************************/
function exercise_update_instance($exercise) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will update an existing instance with new data.

    $exercise->timemodified = time();

    // encode password if necessary
    if (!empty($exercise->password)) {
        $exercise->password = md5($exercise->password);
    } else {
        unset($exercise->password);
    }

    $exercise->id = $exercise->instance;

    if ($returnid = update_record("exercise", $exercise)) {

        $event = NULL;

        if ($event->id = get_field('event', 'id', 'modulename', 'exercise', 'instance', $exercise->id)) {

            $event->name        = $exercise->name;
            $event->description = $exercise->description;
            $event->timestart   = $exercise->deadline;

            update_event($event);
        }
    }

    return $returnid;
}


/*******************************************************************/
function exercise_user_complete($course, $user, $mod, $exercise) {
    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        print_simple_box_start();
        $table->head = array (get_string("submission", "exercise"),  get_string("submitted", "exercise"),
            get_string("assessed", "exercise"), get_string("grade"));
        $table->width = "100%";
        $table->align = array ("left", "left", "left", "center");
        $table->size = array ("*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        foreach ($submissions as $submission) {
            if ($assessments = exercise_get_assessments($submission)) {
                // should only be one but we'll loop anyway
                foreach ($assessments as $assessment) {
                    $table->data[] = array(exercise_print_submission_title($exercise, $submission),
                            userdate($submission->timecreated), userdate($assessment->timecreated),
                            $assessment->grade * $exercise->grade / 100.0);
                }
            } else {
                // submission not yet assessed (by teacher)
                $table->data[] = array(exercise_print_submission_title($exercise, $submission),
                        userdate($submission->timecreated), get_string("notassessedyet", "exercise"), 0);
            }
        }
        print_table($table);
        print_simple_box_end();
    } else {
        print_string("nosubmissions", "exercise");
    }
}


/*******************************************************************/
function exercise_user_outline($course, $user, $mod, $exercise) {
    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        $result->info = count($submissions)." ".get_string("submissions", "exercise");
        foreach ($submissions as $submission) { // the first one is the most recent one
            $result->time = $submission->timecreated;
            break;
            }
        return $result;
    }
    return NULL;
}


/*******************************************************************/
function exercise_get_participants($exerciseid) {
//Returns the users with data in one exercise
//(users with records in exercise_submissions and exercise_assessments, students)

    global $CFG;

    //Get students from exercise_submissions
    $st_submissions = get_records_sql("SELECT DISTINCT u.id, u.id
                                       FROM {$CFG->prefix}user u,
                                            {$CFG->prefix}exercise_submissions s
                                       WHERE s.exerciseid = '$exerciseid' and
                                             u.id = s.userid");
    //Get students from exercise_assessments
    $st_assessments = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}exercise_assessments a
                                 WHERE a.exerciseid = '$exerciseid' and
                                       u.id = a.userid");

    //Add st_assessments to st_submissions
    if ($st_assessments) {
        foreach ($st_assessments as $st_assessment) {
            $st_submissions[$st_assessment->id] = $st_assessment;
        }
    }
    //Return st_submissions array (it contains an array of unique users)
    return ($st_submissions);
}


///////////////////////////////////////////////////////////////////////////////////////////////
// Non-standard Exercise functions
///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_file_area($exercise, $submission) {
    return make_upload_directory( exercise_file_area_name($exercise, $submission) );
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_file_area_name($exercise, $submission) {
//  Creates a directory file name, suitable for make_upload_directory()
    global $CFG;

    return "$exercise->course/$CFG->moddata/exercise/$submission->id";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_assess_logs($course, $timestart) {
    // get the "assess" entries for this user and add the first and last names...
    global $CFG, $USER;
    if (empty($USER->id)) {
        return false;
    }
    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.exerciseid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}exercise e,
                                {$CFG->prefix}exercise_submissions s,
                                {$CFG->prefix}exercise_assessments a,
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen
                                AND l.course = $course->id AND l.module = 'exercise'    AND l.action = 'assess'
                                AND a.id = l.info AND s.id = a.submissionid AND s.userid = $USER->id
                                AND u.id = a.userid AND e.id = a.exerciseid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_assessments($submission) {
    // Return all assessments for this submission provided they are after the editing time,
    // ordered oldest first, newest last
    global $CFG;

    $timenow = time();
    return get_records_select("exercise_assessments", "(submissionid = $submission->id) AND
        (timecreated < $timenow - $CFG->maxeditingtime)", "timecreated ASC");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_best_grade($submission) {
// Returns the best grade of students' submission (there may, occassionally be more than one assessment)
    global $CFG;

    return get_record_sql("SELECT MAX(a.grade) AS grade FROM
                        {$CFG->prefix}exercise_assessments a
                            WHERE a.submissionid = $submission->id
                              GROUP BY a.submissionid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_grade_logs($course, $timestart) {
    // get the "grade" entries for this user and add the first and last names...
    global $CFG, $USER;
    if (empty($USER->id)) {
        return false;
    }

    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.exerciseid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}exercise e,
                                {$CFG->prefix}exercise_submissions s,
                                {$CFG->prefix}exercise_assessments a,
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen
                                AND l.course = $course->id AND l.module = 'exercise'    AND l.action = 'grade'
                                AND a.id = l.info AND s.id = a.submissionid AND a.userid = $USER->id
                                AND u.id = s.userid AND e.id = a.exerciseid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_mean_submission_grades($exercise) {
// Returns the mean grades of students' submissions
// ignores hot assessments
    global $CFG;

    $timenow = time();
    $grades = get_records_sql("SELECT DISTINCT u.userid, AVG(a.grade) AS grade FROM
                        {$CFG->prefix}exercise_submissions s,
                        {$CFG->prefix}exercise_assessments a
                            WHERE
                              s.exerciseid = $exercise->id
                              AND s.late = 0
                              AND a.submissionid = s.id
                              AND a.timecreated < $timenow
                              GROUP BY s.userid");
    return $grades;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_submit_logs($course, $timestart) {
    // get the "submit" entries and add the first and last names...
    global $CFG, $USER;

    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, l.info as exerciseid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}exercise e,
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen
                                AND l.course = $course->id AND l.module = 'exercise'
                                AND l.action = 'submit'
                                AND e.id = l.info
                                AND u.id = l.userid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_teacher_submission_assessments($exercise) {
// Return all assessments on the teacher submissions, order by youngest first, oldest last
    global $CFG;

    return get_records_sql("SELECT a.* FROM {$CFG->prefix}exercise_submissions s, {$CFG->prefix}exercise_assessments a
                            WHERE s.isexercise = 1
                              AND s.exerciseid = $exercise->id
                              AND a.submissionid = s.id
                              ORDER BY a.timecreated DESC");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_unmailed_assessments($cutofftime) {
    /// Return list of (ungraded) assessments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT a.*, e.course, e.name
                              FROM {$CFG->prefix}exercise_assessments a, {$CFG->prefix}exercise e
                             WHERE a.mailed = 0
                               AND a.timecreated < $cutofftime
                               AND e.id = a.exerciseid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_user_submissions($exercise, $user) {
    // return submission of user oldest first, newest last
    // teachers submit "exercises"
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (isteacher($course->id, $user->id)) {
        return get_records_select("exercise_submissions",
             "exerciseid = $exercise->id AND isexercise = 1", "timecreated" );
        }
    return get_records_select("exercise_submissions",
             "exerciseid = $exercise->id AND userid = $user->id", "timecreated" );
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_submission_title($exercise, $submission) {
    global $CFG;

    if (!$submission->timecreated) { // a "no submission"
        return $submission->title;
        }

    $filearea = exercise_file_area_name($exercise, $submission);
    if ($basedir = exercise_file_area($exercise, $submission)) {
        if (list($file) = get_directory_list($basedir)) {
            $icon = mimeinfo("icon", $file);
            require_once($CFG->libdir .'/filelib.php');
            $ffurl = get_file_url("$filearea/$file");
            return "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"".get_string('file')."\" />".
                "&nbsp;<a target=\"uploadedfile\" href=\"$ffurl\">$submission->title</a>";
        }
    }
}

function exercise_get_view_actions() {
    return array('view','view all','display');
}

function exercise_get_post_actions() {
    return array('assess','delete','grade','open','set up','submit');
}

/**
 * Returns all other caps used in module
 */
function exercise_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

?>
