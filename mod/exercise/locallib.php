<?php  // $Id$

/// Library of extra functions for the exercise module

//////////////////////////////////////////////////////////////////////////////////////

/*** Functions for the exercise module ******

function exercise_add_custom_scales($exercise) {
function exercise_compare_assessments($exercise, $assessment1, $assessment2) {
function exercise_copy_assessment($assessment, $submission, $withfeedback = false) {
function exercise_count_all_submissions_for_assessment($exercise, $user) {
function exercise_count_assessments($submission) {
function exercise_count_assessments_by_teacher($exercise, $teacher) {
function exercise_count_student_submissions($exercise) {
function exercise_count_teacher_assessments($exercise, $user) {
function exercise_count_teacher_submissions($exercise) {
function exercise_count_teacher_submissions_for_assessment($exercise, $user) {
function exercise_count_unassessed_student_submissions($exercise) {
function exercise_count_ungraded_assessments_student($exercise) {
function exercise_count_ungraded_assessments_teacher($exercise) {
function exercise_count_user_assessments($exercise, $user, $type = "all") { $type is all, student or teacher
function exercise_count_user_assessments_done($exercise,$user) {
function exercise_count_user_submissions($exercise, $user) {

function exercise_delete_submitted_files($exercise, $submission) {
function exercise_delete_user_files($exercise, $user, $exception) {

function exercise_file_area($exercise, $submission) { <--- in lib.php
function exercise_file_area_name($exercise, $submission) { <--- in lib.php

function exercise_get_assess_logs($course, $timestart) {  <--- in lib.php
function exercise_get_assessments($submission)   <--- in lib.php{
function exercise_get_best_submission_grades($exercise) {  <--- in lib.php
function exercise_get_grade_logs($course, $timestart) {  <--- in lib.php
function exercise_get_mean_submission_grades($exercise) {  <--- in lib.php
function exercise_get_student_submission($exercise, $user) {
function exercise_get_student_submissions($exercise) {
function exercise_get_submission_assessment($submission, $user) {
function exercise_get_submit_logs($course, $timestart) {
function exercise_get_teacher_submission_assessments($exercise) {  <--- in lib.php
function exercise_get_teacher_submissions($exercise) {
function exercise_get_ungraded_assessments($exercise) {
function exercise_get_unmailed_assessments($cutofftime) {  <--- in lib.php
function exercise_get_unmailed_graded_assessments($cutofftime) {  <--- in lib.php
function exercise_get_user_assessments($exercise, $user) {
function exercise_get_user_submissions($exercise, $user) {

function exercise_list_all_ungraded_assessments($exercise) {
function exercise_list_submissions_for_admin($exercise) {
function exercise_list_teacher_assessments($exercise, $user) {
function exercise_list_teacher_submissions($exercise, $user, $reassess) {
function exercise_list_unassessed_student_submissions($exercise, $user) {
function exercise_list_unassessed_teacher_submissions($exercise, $user) {
function exercise_list_ungraded_assessments($exercise, $stype) {
function exercise_list_user_submissions($exercise, $user) {


function exercise_print_assessment_form($exercise, $assessment, $allowchanges, $returnto)
function exercise_print_assessments_by_user_for_admin($exercise, $user) {
function exercise_print_assessments_for_admin($exercise, $submission) {
function exercise_print_assignment_info($exercise) {
function exercise_print_difference($time) {
function exercise_print_feedback($course, $submission) {
function exercise_print_league_table($exercise) {
function exercise_print_submission_assessments($exercise, $submission, $type) {
function exercise_print_submission_title($exercise, $submission) {  <--- in lib.php
function exercise_print_tabbed_table($table) {
function exercise_print_teacher_assessment_form($exercise, $assessment, $submission, $returnto)
function exercise_print_teacher_table($course) {
function exercise_print_time_to_deadline($time) {
function exercise_print_upload_form($exercise) {
function exercise_print_user_assessments($exercise, $user) {

function exercise_test_for_resubmission($exercise, $user) {
function exercise_test_user_assessments($exercise, $user) {
***************************************/

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_add_custom_scales($exercise) {
    global $EXERCISE_SCALES;

    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }

    if ($scales = get_records("scale", "courseid", $course->id, "name ASC")) {
        foreach ($scales as $scale) {
            $labels = explode(",", $scale->scale);
            $EXERCISE_SCALES[] = array('name' => $scale->name, 'type' => 'radio', 'size' => count($labels), 
                    'start' => trim($labels[0]), 'end' => trim($labels[count($labels) - 1]));
        }
    }
    return;
}

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_compare_assessments($exercise, $assessment1, $assessment2) {
    global $EXERCISE_ASSESSMENT_COMPS, $EXERCISE_EWEIGHTS;
    // first get the assignment elements for maxscores...
    $elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC");
    foreach ($elementsraw as $element) {
        $maxscore[] = $element->maxscore;   // to renumber index 0,1,2...
        $weight[] = $EXERCISE_EWEIGHTS[$element->weight];   // get real value and renumber index 0,1,2...
    }
    for ($i = 0; $i < 2; $i++) {
        if ($i) {
            $rawgrades = get_records("exercise_grades", "assessmentid", $assessment1->id, "elementno ASC");
        } else {
            $rawgrades = get_records("exercise_grades", "assessmentid", $assessment2->id, "elementno ASC");
        }
        foreach ($rawgrades as $grade) {
            $grades[$i][] = $grade->grade;
        }
    }
    $sumdiffs = 0;
    $sumweights = 0;
    switch ($exercise->gradingstrategy) {
        case 1 : // accumulative grading and...
        case 4 : // ...rubic grading
            for ($i=0; $i < $exercise->nelements; $i++) {
                $diff = ($grades[0][$i] - $grades[1][$i]) * $weight[$i] / $maxscore[$i];
                $sumdiffs += $diff * $diff; // use squared distances
                $sumweights += $weight[$i];
                }
            break;
        case 2 :  // error banded grading
            // ignore maxscores here, the grades are either 0 or 1,
            for ($i=0; $i < $exercise->nelements; $i++) {
                $diff = ($grades[0][$i] - $grades[1][$i]) * $weight[$i];
                $sumdiffs += $diff * $diff; // use squared distances
                $sumweights += $weight[$i];
                }
            break;
        case 3 : // criterion grading
            // here we only need to look at the difference between the "zero" grade elements
            $diff = ($grades[0][0] - $grades[1][0]) / (count($elementsraw) - 1);
            $sumdiffs = $diff * $diff;
            $sumweights = 1;
            break;
    }            
    // convert to a sensible grade (always out of 100)
    $COMP = (object)$EXERCISE_ASSESSMENT_COMPS[$exercise->assessmentcomps];
    $factor = $COMP->value;
    $gradinggrade = (($factor - ($sumdiffs / $sumweights)) / $factor) * 100;
    if ($gradinggrade < 0) {
        $gradinggrade = 0;
    }
    return $gradinggrade;
}
 
///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_copy_assessment($assessment, $submission, $withfeedback = false) {
    // adds a copy of the given assessment for the submission specified to the exercise_assessemnts table. 
    // The grades and optionally the comments are added to the exercise_grades table. Returns the new
    // assessment object
    global $USER;
    
    $yearfromnow = time() + 365 * 86400;
    $newassessment->exerciseid = $assessment->exerciseid;
    $newassessment->submissionid = $submission->id;
    $newassessment->userid = $USER->id;
    $newassessment->timecreated = $yearfromnow;
    $newassessment->timegraded = 0;
    $newassessment->grade = $assessment->grade;
    if ($withfeedback) {
        $newassessment->generalcomment = addslashes($assessment->generalcomment);
        $newassessment->teachercomment = addslashes($assessment->teachercomment);
    }
    if (!$newassessment->id = insert_record("exercise_assessments", $newassessment)) {
        error("Copy Assessment: Could not insert exercise assessment!");
    }

    if ($grades = get_records("exercise_grades", "assessmentid", $assessment->id)) {
        foreach ($grades as $grade) {
            unset($grade->id); // clear id, insert record now seems to believe it!
            if (!$withfeedback) {
                $grade->feedback = '';
            }
            else {
                $grade->feedback = addslashes($grade->feedback);
                // $grade->feedback = $grade->feedback;
            }
            $grade->assessmentid = $newassessment->id;
            if (!$grade->id = insert_record("exercise_grades", $grade)) {
                error("Copy Assessment: Could not insert exercise grade!");
            }
        }
    }
    if ($withfeedback) {
        // remove the slashes from comments as the new assessment record is used 
        // in assessments and in lib
        $newassessment->generalcomment = stripslashes($assessment->generalcomment);
        $newassessment->teachercomment = stripslashes($assessment->teachercomment);
    }
 
    return $newassessment;
}



///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_all_submissions_for_assessment($exercise, $user) {
    // looks at all submissions and deducts the number which has been assessed by this user
    $n = 0;
    if ($submissions = get_records_select("exercise_submissions", "exerciseid = $exercise->id AND timecreated > 0")) {
        $n =count($submissions);
        foreach ($submissions as $submission) {
            $n -= count_records("exercise_assessments", "submissionid", $submission->id, "userid", $user->id);
            }
        }
    return $n;
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_assessments($submission) {
    // Return the (cold) assessments for this submission, 
    global $CFG;
    
    $timethen = time() - $CFG->maxeditingtime;
   return count_records_select("exercise_assessments", "submissionid = $submission->id AND
       timecreated < $timethen");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_assessments_by_teacher($exercise, $teacher) {
    // Return the number of assessments done by a teacher 
    
   return count_records_select("exercise_assessments", "exerciseid = $exercise->id AND
       userid = $teacher->id");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_student_submissions($exercise) {
    global $CFG;

    // make sure it works on the site course
    $select = "u.course = '$exercise->course' AND";
    if ($exercise->course == SITEID) {
        $select = '';
    }

    return count_records_sql("SELECT count(*) FROM {$CFG->prefix}exercise_submissions s
                            WHERE $select 
                              AND s.exerciseid = $exercise->id
                              AND timecreated > 0");
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_teacher_assessments($exercise, $user) {
    // returns the number of assessments made by teachers on user's submissions
    
    $n = 0;
    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        foreach ($submissions as $submission) {
            if ($assessments = exercise_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    // count only teacher assessments
                    if (isteacher($exercise->course, $assessment->userid)) {
                        $n++;
                        }
                    }
                }
            }
        }
    return $n;
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_teacher_submissions($exercise) {
    
     return count_records("exercise_submissions", "isexercise", 1, "exerciseid", $exercise->id);
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_teacher_submissions_for_assessment($exercise, $user) {

    $n = 0;
    if ($submissions = exercise_get_teacher_submissions($exercise)) {
        $n =count($submissions);
        foreach ($submissions as $submission) {
            $n -= count_records("exercise_assessments", "submissionid", $submission->id, "userid", $user->id);
            }
        }
    return $n;
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_unassessed_student_submissions($exercise) {
// returns the number of students submissions which have not been assessed by a teacher
    global $CFG;
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    $timenow = time();
    $n = 0;
    // look at all the student submissions, exercise_get_student_submissions is group aware
    $groupid = get_current_group($course->id);
    if ($submissions = exercise_get_student_submissions($exercise, "time", $groupid)) {
        foreach ($submissions as $submission) {
            // only look at "cold" submissions
            if ($submission->timecreated < $timenow - $CFG->maxeditingtime) {
                $teacherassessed = false;
                if ($assessments = exercise_get_assessments($submission)) {
                    foreach ($assessments as $assessment) {
                        // exercise_get_assessments only returns "cold" assessments, look for one made by a teacher
                        if (isteacher($course->id, $assessment->userid)) {
                            $teacherassessed = true;
                            break; // no need to look further
                        }
                    }
                }
                if (!$teacherassessed) {
                    $n++;
                }
            }
        }
    }
    return $n;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_ungraded_assessments_student($exercise, $groupid = 0) {
    // function returns the number of ungraded assessments by students of STUDENT submissions
    
    $n = 0;
    if ($submissions = exercise_get_student_submissions($exercise, $groupid)) {
        foreach ($submissions as $submission) {
            if ($assessments = exercise_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    if ($assessment->timegraded == 0) {
                        // ignore teacher assessments
                        if (!isteacher($exercise->course, $assessment->userid)) {
                            $n++;
                            }
                        }
                    }
                }
            }
        }
    return $n;
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_ungraded_assessments_teacher($exercise) {
    // function returns the number of ungraded assessments by students of TEACHER submissions
    global $CFG;

    $timenow = time();
    $n = 0;
    if ($submissions = exercise_get_teacher_submissions($exercise)) {
        foreach ($submissions as $submission) {
            if ($assessments = exercise_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    if ($assessment->timegraded == 0) {
                        // ignore teacher assessments
                        if (!isteacher($exercise->course, $assessment->userid)) {
                            // must have created a little time ago
                            if (($timenow - $assessment->timecreated) > $CFG->maxeditingtime) {
                                $n++;
                                }
                            }
                        }
                    }
                }
            }
        }
    return $n;
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_user_assessments($exercise, $user, $stype = "all") {
    // returns the number of assessments allocated/made by a user, all of them, or just those for the student or teacher submissions
    // the student's self assessments are included in the count
    // the maxeditingtime is NOT taken into account here also allocated assessments which have not yet
    // been done are counted as well
    
    $n = 0;
    if ($assessments = exercise_get_user_assessments($exercise, $user)) {
         foreach ($assessments as $assessment) {
            switch ($stype) {
                case "all" :
                    $n++;
                    break;
                case "student" :
                     $submission = get_record("exercise_submissions", "id", $assessment->submissionid);
                    if (isstudent($exercise->course, $submission->userid)) {
                        $n++;
                        }
                    break;
                case "teacher" :
                     $submission = get_record("exercise_submissions", "id", $assessment->submissionid);
                    if (isteacher($exercise->course, $submission->userid)) {
                        $n++;
                        }
                    break;
                }
            }
        }
    return $n;
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_user_assessments_done($exercise, $user) {
    // returns the number of assessments actually done by a user
    // the student's self assessments are included in the count
    // the maxeditingtime is NOT taken into account here 
    
    $n = 0;
    $timenow = time();
    if ($assessments = exercise_get_user_assessments($exercise, $user)) {
         foreach ($assessments as $assessment) {
            if ($assessment->timecreated < $timenow) {
                $n++;
            }
        }
    }
    return $n;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_user_submissions($exercise, $user) {
    // returns the number of submissions make by this user
    return count_records("exercise_submissions", "exerciseid", $exercise->id, "userid", $user->id);
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_delete_submitted_files($exercise, $submission) {
// Deletes the files in the exercise area for this submission

    if ($basedir = exercise_file_area($exercise, $submission)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                if (unlink("$basedir/$file")) {
                    notify("Existing file '$file' has been deleted!");
                }
                else {
                    notify("Attempt to delete file $basedir/$file has failed!");
                }
            }
        }
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_delete_user_files($exercise, $user, $exception) {
// Deletes all the user files in the exercise area for a user
// EXCEPT for any file named $exception

    if (!$submissions = exercise_get_submissions($exercise, $user)) {
        notify("No submissions!");
        return;
        }
    foreach ($submissions as $submission) {
        if ($basedir = exercise_file_area($exercise, $submission)) {
            if ($files = get_directory_list($basedir)) {
                foreach ($files as $file) {
                    if ($file != $exception) {
                        unlink("$basedir/$file");
                        notify("Existing file '$file' has been deleted!");
                        }
                    }
                }
            }
        }
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_best_submission_grades($exercise) {
// Returns the grades of students' best submissions
    global $CFG;

    // make sure it works on the site course
    $select = "u.course = '$exercise->course' AND";
    if ($exercise->course == SITEID) {
        $select = '';
    }

    return get_records_sql("SELECT DISTINCT s.userid, MAX(a.grade) AS grade FROM 
                        {$CFG->prefix}exercise_submissions s, 
                        {$CFG->prefix}exercise_assessments a
                            WHERE $select 
                              AND s.exerciseid = $exercise->id
                              AND s.late = 0
                              AND a.submissionid = s.id
                              GROUP BY s.userid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_mean_grade($submission) {
// Returns the mean grade of students' submission (may, very occassionally, be more than one assessment)
    global $CFG;
    
    return get_record_sql("SELECT AVG(a.grade) AS grade FROM 
                        {$CFG->prefix}exercise_assessments a 
                            WHERE a.submissionid = $submission->id
                              GROUP BY a.submissionid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_student_submission($exercise, $user) {
// Return a submission for a particular user
    global $CFG;

    $submission = get_record("exercise_submissions", "exerciseid", $exercise->id, "userid", $user->id);
    if (!empty($submission->timecreated)) {
        return $submission;
    }
    return NULL;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_student_submissions($exercise, $order = "time", $groupid = 0) {
// Return all  ENROLLED student submissions
// if order can grade|title|name|nothing, nothing is oldest first, youngest last
    global $CFG;
    
    if ($groupid) { 
        // just look at a single group
        if ($order == "grade") {
            // allow for multiple assessments of submissions (coming from different teachers)
                    
            // make sure it works on the site course
            $select = "u.course = '$exercise->course' AND";
            if ($exercise->course == SITEID) {
                $select = '';
            }

            return get_records_sql("SELECT s.*, AVG(a.grade) AS grade FROM 
                    {$CFG->prefix}groups_members g, {$CFG->prefix}exercise_submissions s, 
                    {$CFG->prefix}exercise_assessments a
                    WHERE $select g.groupid = $groupid
                    AND s.exerciseid = $exercise->id
                    AND a.submissionid = s.id
                    GROUP BY s.id
                    ORDER BY a.grade DESC");
        }

        if ($order == "title") {
            $order = "s.title";
        } elseif ($order == "name") {
            $order = "n.firstname, n.lastname, s.timecreated DESC";
        } elseif ($order == "time") {
            $order = "s.timecreated";
        }

    // make sure it works on the site course
    $select = "u.course = '$exercise->course' AND";
    if ($exercise->course == SITEID) {
        $select = '';
    }

        return get_records_sql("SELECT s.* FROM  {$CFG->prefix}user n, 
                {$CFG->prefix}groups_members g, {$CFG->prefix}exercise_submissions s
                WHERE $select g.groupid = $groupid
                AND s.exerciseid = $exercise->id
                ORDER BY $order");

    } 
    else { // no group - all users
        if ($order == "grade") {
            // allow for multiple assessments of submissions (coming from different teachers)

            // make sure it works on the site course
            $select = "u.course = '$exercise->course' AND";
            if ($exercise->course == SITEID) {
                $select = '';
            }

            return get_records_sql("SELECT s.*, AVG(a.grade) AS grade FROM {$CFG->prefix}exercise_submissions s, 
                    {$CFG->prefix}exercise_assessments a
                    WHERE $select 
                    AND s.exerciseid = $exercise->id
                    AND a.submissionid = s.id
                    GROUP BY s.id
                    ORDER BY a.grade DESC");
        }

        if ($order == "title") {
            $order = "s.title";
        } elseif ($order == "name") {
            $order = "n.firstname, n.lastname, s.timecreated DESC";
        } elseif ($order == "time") {
            $order = "s.timecreated";
        }

        // make sure it works on the site course
        $select = "u.course = '$exercise->course' AND";
        if ($exercise->course == SITEID) {
            $select = '';
        }

        return get_records_sql("SELECT s.* FROM {$CFG->prefix}exercise_submissions s, 
                {$CFG->prefix}user n  
                WHERE $select 
                AND s.exerciseid = $exercise->id
                ORDER BY $order");
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_submission_assessment($submission, $user = null) {
    // Return a user's assessment for this submission
    if ($user) {
        return get_record("exercise_assessments", "submissionid", $submission->id, "userid", $user->id);
    } else { // likely to be the teacher's assessment
        return get_record("exercise_assessments", "submissionid", $submission->id);
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_teacher_submissions($exercise) {
// Return all  teacher submissions, ordered by title
    global $CFG;
    
    return get_records_sql("SELECT s.* FROM {$CFG->prefix}exercise_submissions s
                        WHERE s.isexercise = 1
                              AND s.exerciseid = $exercise->id 
                              ORDER BY s.title");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_ungraded_assessments($exercise) {
    global $CFG;
    // Return all assessments which have not been graded or just graded
    $cutofftime =time() - $CFG->maxeditingtime;
    return get_records_select("exercise_assessments", "exerciseid = $exercise->id AND (timegraded = 0 OR 
                timegraded > $cutofftime)", "timecreated"); 
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_ungraded_assessments_student($exercise) {
    global $CFG;
    // Return all assessments which have not been graded or just graded of student's submissions

    // make sure it works on the site course
    $select = "u.course = '$exercise->course' AND";
    if ($exercise->course == SITEID) {
        $select = '';
    }

    $cutofftime =time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}exercise_submissions s
                            {$CFG->prefix}exercise_assessments a
                            WHERE $select 
                              AND s.exerciseid = $exercise->id
                              AND a.submissionid = s.id
                              AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
                              AND a.timecreated < $cutofftime
                              ORDER BY a.timecreated ASC"); 
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_ungraded_assessments_teacher($exercise) {
    global $CFG;
    // Return all assessments which have not been graded or just graded of teacher's submissions
    
    $cutofftime =time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}exercise_submissions s, {$CFG->prefix}exercise_assessments a
                            WHERE s.isexercise = 1
                              AND s.exerciseid = $exercise->id
                              AND a.submissionid = s.id
                              AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
                              AND a.timecreated < $cutofftime
                              ORDER BY a.timecreated ASC"); 
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_user_assessments($exercise, $user) {
    // Return all the  user's assessments, newest first, oldest last
    // students will have only one, teachers will have more...
    return get_records_select("exercise_assessments", "exerciseid = $exercise->id AND userid = $user->id", 
                "timecreated DESC");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_all_ungraded_assessments($exercise) {
    // lists all the assessments for comment by teacher
    global $CFG;
    
    $table->head = array (get_string("title", "exercise"), get_string("timeassessed", "exercise"), get_string("action", "exercise"));
    $table->align = array ("left", "left", "left");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;
    $timenow = time();
    
    if ($assessments = exercise_get_ungraded_assessments($exercise)) {
        foreach ($assessments as $assessment) {
            if (!isteacher($exercise->course, $assessment->userid)) {
                if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
                    $action = "<a href=\"assessments.php?action=gradeassessment&amp;a=$exercise->id&amp;aid=$assessment->id\">".
                        get_string("edit", "exercise")."</a>";
                    }
                else {
                    $action = "<a href=\"assessments.php?action=gradeassessment&amp;a=$exercise->id&amp;aid=$assessment->id\">".
                        get_string("gradeassessment", "exercise")."</a>";
                    }
                $submission = get_record("exercise_submissions", "id", $assessment->submissionid);
                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                    userdate($assessment->timecreated), $action);
                }
            }
        if (isset($table->data)) {
            print_table($table);
            }
        }
    }
    

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_submissions_for_admin($exercise) {
    // list the teacher sublmissions first
    global $CFG, $USER;
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    $groupid = get_current_group($course->id);
    
    exercise_print_assignment_info($exercise);
    print_heading_with_help(get_string("administration"), "administration", "exercise");
    echo"<p align=\"center\"><b><a href=\"assessments.php?action=teachertable&amp;id=$cm->id\">".
            get_string("teacherassessmenttable", "exercise", $course->teacher)."</a></b></p>\n";


    if (isteacheredit($course->id)) {
        // list any teacher submissions
        $table->head = array (get_string("title", "exercise"), get_string("submitted", "exercise"), 
                get_string("action", "exercise"));
        $table->align = array ("left", "left", "left");
        $table->size = array ("*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        if ($submissions = exercise_get_teacher_submissions($exercise)) {
            foreach ($submissions as $submission) {
                $action = "<a href=\"submissions.php?action=adminamendtitle&amp;id=$cm->id&amp;sid=$submission->id\">".
                    get_string("amendtitle", "exercise")."</a>";
                if (isteacheredit($course->id)) {
                    $action .= " | <a href=\"submissions.php?action=adminconfirmdelete&amp;id=$cm->id&amp;sid=$submission->id\">".
                        get_string("delete", "exercise")."</a>";
                }
                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                        userdate($submission->timecreated), $action);
            }
            print_heading(get_string("studentsubmissions", "exercise", $course->teacher), "center");
            print_table($table);
        }
    }

    // list student assessments
    // Get all the students...
    if ($users = get_course_students($course->id, "u.lastname, u.firstname")) {
        $timenow = time();
        unset($table);
        $table->head = array(get_string("name"), get_string("title", "exercise"), 
                get_string("assessed", "exercise"), get_string("action", "exercise"));
        $table->align = array ("left", "left", "left", "left");
        $table->size = array ("*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;
        $nassessments = 0;
        foreach ($users as $user) {
            // check group membership, if necessary
            if ($groupid) {
                // check user's group
                if (!ismember($groupid, $user->id)) {
                    continue; // skip this user
                }
            }
            if ($assessments = exercise_get_user_assessments($exercise, $user)) {
                $title ='';
                foreach ($assessments as $assessment) {
                    if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
                        error("exercise_list_submissions_for_admin: Submission record not found!");
                        }
                    $title .= $submission->title;
                    // test for allocated assesments which have not been done
                    if ($assessment->timecreated < $timenow) {
                        // show only warm or cold assessments
                        $title .= " {".number_format($assessment->grade * $exercise->grade / 100.0, 0);
                        if ($assessment->timegraded) {
                            $title .= "/".number_format($assessment->gradinggrade * $exercise->gradinggrade / 100.0, 0);
                        }
                        $title .= "} ";
                        if ($realassessments = exercise_count_user_assessments_done($exercise, $user)) {
                            $action = "<a href=\"assessments.php?action=adminlistbystudent&amp;id=$cm->id&amp;userid=$user->id\">".
                                get_string("view", "exercise")."</a>";
                        }
                        else {
                            $action ="";
                        }
                        $nassessments++;
                        $table->data[] = array(fullname($user), $title, 
                                userdate($assessment->timecreated), $action);
                    }
                }
            }
        }
        if (isset($table->data)) {
            if ($groupid) {
                if (! groups_group_exists($groupid)) { //TODO:
                    error("List unassessed student submissions: group not found");
                }
                print_heading("$group->name ".get_string("studentassessments", "exercise", $course->student).
                        " [$nassessments]");
            } else {
                print_heading(get_string("studentassessments", "exercise", $course->student)." [$nassessments]");
            }
            print_table($table);
            echo "<p align=\"center\">".get_string("noteonstudentassessments", "exercise");
            echo "<br />{".get_string("maximumgrade").": $exercise->grade / ".
                get_string("maximumgrade").": $exercise->gradinggrade}</p>\n";
            // grading grade analysis
            unset($table);
            $table->head = array (get_string("count", "exercise"), get_string("mean", "exercise"),
                get_string("standarddeviation", "exercise"), get_string("maximum", "exercise"), 
                get_string("minimum", "exercise"));
            $table->align = array ("center", "center", "center", "center", "center");
            $table->size = array ("*", "*", "*", "*", "*");
            $table->cellpadding = 2;
            $table->cellspacing = 0;
            if ($groupid) {
                $stats = get_record_sql("SELECT COUNT(*) as count, AVG(gradinggrade) AS mean, 
                        STDDEV(gradinggrade) AS stddev, MIN(gradinggrade) AS min, MAX(gradinggrade) AS max 
                        FROM {$CFG->prefix}groups_members g, {$CFG->prefix}exercise_assessments a 
                        WHERE g.groupid = $groupid AND a.userid = g.userid AND a.timegraded > 0 
                        AND a.exerciseid = $exercise->id");
            } else { // no group/all participants
                $stats = get_record_sql("SELECT COUNT(*) as count, AVG(gradinggrade) AS mean, 
                        STDDEV(gradinggrade) AS stddev, MIN(gradinggrade) AS min, MAX(gradinggrade) AS max 
                        FROM {$CFG->prefix}exercise_assessments a 
                        WHERE a.timegraded > 0 AND a.exerciseid = $exercise->id");
            }   
            $table->data[] = array($stats->count, number_format($stats->mean * $exercise->gradinggrade / 100.0, 1), 
                    number_format($stats->stddev * $exercise->gradinggrade / 100.0, 1), 
                    number_format($stats->max * $exercise->gradinggrade / 100.0, 1), 
                    number_format($stats->min * $exercise->gradinggrade / 100.0, 1));
            print_heading(get_string("gradinggrade", "exercise")." ".get_string("analysis", "exercise"));
            print_table($table);
            echo "<p align=\"center\"><a href=\"assessments.php?id=$cm->id&amp;action=regradestudentassessments\">".
                    get_string("regradestudentassessments", "exercise")."</a> ";
            helpbutton("regrading", get_string("regradestudentassessments", "exercise"), "exercise");
            echo "</p>\n";
        }
    }

    // now the sudent submissions
    unset($table);
    if ($users) {
        $table->head = array (get_string("submittedby", "exercise"), get_string("title", "exercise"),
            get_string("submitted", "exercise"), get_string("action", "exercise"));
        $table->align = array ("left", "left", "left", "left");
        $table->size = array ("*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        $nsubmissions = 0;
        foreach ($users as $user) {
            // check group membership, if necessary
            if ($groupid) {
                // check user's group
                if (!ismember($groupid, $user->id)) {
                    continue; // skip this user
                }
            }
            if ($submissions = exercise_get_user_submissions($exercise, $user)) {
                foreach ($submissions as $submission) {
                    $action = "<a href=\"submissions.php?action=adminamendtitle&amp;id=$cm->id&amp;sid=$submission->id\">".
                        get_string("amendtitle", "exercise")."</a>";
                    // has teacher already assessed this submission
                    if ($assessment = get_record_select("exercise_assessments", 
                                "submissionid = $submission->id AND userid = $USER->id")) {
                        $curtime = time();
                        if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
                            $action .= " | <a href=\"assessments.php?action=assesssubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                                get_string("reassess", "exercise")."</a>";
                        }
                        else { // there's still time left to edit...
                            $action .= " | <a href=\"assessments.php?action=assesssubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                                get_string("edit", "exercise")."</a>";
                        }
                    }
                    else { // user has not assessed this submission
                        $action .= " | <a href=\"assessments.php?action=assesssubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                            get_string("assess", "exercise")."</a>";
                    }
                    if ($nassessments = exercise_count_assessments($submission)) {
                        $action .= " | <a href=\"assessments.php?action=adminlist&amp;id=$cm->id&amp;sid=$submission->id\">".
                            get_string("view", "exercise")." ($nassessments)</a>";
                    }
                    if ($submission->late) {
                        $action .= " | <a href=\"submissions.php?action=adminlateflag&amp;id=$cm->id&amp;sid=$submission->id\">".
                            get_string("clearlateflag", "exercise")."</a>";
                    }
                    $action .= " | <a href=\"submissions.php?action=adminconfirmdelete&amp;id=$cm->id&amp;sid=$submission->id\">".
                        get_string("delete", "exercise")."</a>";
                    $title = $submission->title;
                    if ($submission->resubmit) {
                        $title .= "*";
                    }
                    $datesubmitted = userdate($submission->timecreated);
                    if ($submission->late) {
                        $datesubmitted = "<font color=\"red\">".$datesubmitted."</font>";
                    }
                    $table->data[] = array(fullname($user), $title.
                            " ".exercise_print_submission_assessments($exercise, $submission), 
                            $datesubmitted, $action);
                    $nsubmissions++;
                }
            }
        }
        if (isset($table->data)) {
            if ($groupid) {
                if (! groups_group_exists($groupid)) {
                    error("List unassessed student submissions: group not found");
                }
                print_heading("$group->name ".get_string("studentsubmissions", "exercise", $course->student).
                        " [$nsubmissions]");
            } else {
                print_heading(get_string("studentsubmissions", "exercise", $course->student)." [$nsubmissions]",
                    "center");
            }
            print_table($table);
            echo "<p align=\"center\">[] - ".get_string("gradeforsubmission", "exercise");
            echo "<br />".get_string("maximumgrade").": $exercise->grade</p>\n";
            echo "<p align=\"center\">".get_string("resubmitnote", "exercise", $course->student)."</p>\n";
            // grade analysis
            unset($table);
            $table->head = array (get_string("count", "exercise"), get_string("mean", "exercise"),
                get_string("standarddeviation", "exercise"), get_string("maximum", "exercise"), 
                get_string("minimum", "exercise"));
            $table->align = array ("center", "center", "center", "center", "center");
            $table->size = array ("*", "*", "*", "*", "*");
            $table->cellpadding = 2;
            $table->cellspacing = 0;

            /// NOTE:  user_teachers was ripped from the following SQL without a proper fix - XXX TO DO

            if ($groupid) {
                $stats = get_record_sql("SELECT COUNT(*) as count, AVG(grade) AS mean, 
                        STDDEV(grade) AS stddev, MIN(grade) AS min, MAX(grade) AS max 
                        FROM {$CFG->prefix}groups_members g, {$CFG->prefix}exercise_assessments a, 
                        {$CFG->prefix}exercise_submissions s
                        WHERE g.groupid = $groupid AND s.userid = g.userid AND a.submissionid = s.id 
                        AND a.exerciseid = $exercise->id");
            } else { // no group/all participants
                $stats = get_record_sql("SELECT COUNT(*) as count, AVG(grade) AS mean, 
                        STDDEV(grade) AS stddev, MIN(grade) AS min, MAX(grade) AS max 
                        FROM {$CFG->prefix}exercise_assessments a
                        WHERE a.exerciseid = $exercise->id");
            }   
            $table->data[] = array($stats->count, number_format($stats->mean * $exercise->grade / 100.0, 1), 
                    number_format($stats->stddev * $exercise->grade / 100.0, 1), 
                    number_format($stats->max * $exercise->grade / 100.0, 1), 
                    number_format($stats->min * $exercise->grade / 100.0, 1));
            print_heading(get_string("grade")." ".get_string("analysis", "exercise"));
            print_table($table);
        }
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_teacher_assessments($exercise, $user) {
    global $CFG;
    $timenow = time();
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    $table->head = array (get_string("title", "exercise"), get_string("action", "exercise"), get_string("comment", "exercise"));
    $table->align = array ("left", "left", "left");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // get user's submissions
    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        foreach ($submissions as $submission) {
            // get the assessments
            if ($assessments = exercise_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    if (isteacher($exercise->course, $assessment->userid)) { // assessments by teachers only
                        $action = "<a href=\"assessments.php?action=viewassessment&amp;a=$exercise->id&amp;aid=$assessment->id\">".
                            get_string("view", "exercise")."</a>";
                        // has teacher commented on teacher's assessment? shouldn't happen but leave test in
                        if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
                            $comment = get_string("gradedbyteacher", "exercise", $course->teacher);
                            }
                        else {
                            $comment = userdate($assessment->timecreated);
                            }
                        $table->data[] = array(exercise_print_submission_title($exercise, $submission), $action, $comment);
                        }
                    }
                }
            }
        }
    if (isset($table->data)) {
        print_table($table);
        }
    else {
        echo "<center>".get_string("noassessmentsdone", "exercise")."</center>\n";
        }
    }



///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_teacher_submissions($exercise, $user, $reassess = false) {
    // always allow user to reassess if that flag is true
    global $CFG;
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");

    // get any assessment this user has done (could include hot one)
    if (!$assessment = get_record_select("exercise_assessments", "exerciseid = $exercise->id
                    AND userid = $user->id")) {
        // the user has not  yet assessed this exercise, set up a hot assessment record for this user for one 
        // of the teacher submissions, first count the number of assessments for each teacher submission...
        if ($submissions = exercise_get_teacher_submissions($exercise)) {
            // mt_srand ((float)microtime()*1000000); // initialise random number generator, assume php>=4.2.0
            foreach ($submissions as $submission) {
                $n = count_records("exercise_assessments", "submissionid", $submission->id);
                // ...OK to have zero, we add a small random number to randomise things...
                $nassessments[$submission->id] = $n + mt_rand(0, 99) / 100;
            }
            // ...put the submissions with the lowest number of assessments first...
            asort($nassessments);
            reset($nassessments);
            foreach ($nassessments as $submissionid => $n) { // break out of loop after the first element
                $submission = get_record("exercise_submissions", "id", $submissionid);
                // ... provided the user has NOT already assessed that submission...
                if (!$assessment = exercise_get_submission_assessment($submission, $user)) {
                    $yearfromnow = time() + 365 * 86400;
                    // ...create one and set timecreated way in the future, reset when record is updated
                    $assessment->exerciseid = $exercise->id;
                    $assessment->submissionid = $submission->id;
                    $assessment->userid = $user->id;
                    $assessment->grade = -1; // set impossible grade
                    $assessment->timecreated = $yearfromnow;
                    $assessment->mailed = 1; // no need to email to the teacher!
                    if (!$assessment->id = insert_record("exercise_assessments", $assessment)) {
                        error("Could not insert exercise assessment!");
                    }
                    break;
                }
            }
        }
    } else {
        // get hold of the teacher submission
        if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
            error("List teacher submissions: submission record not found");
        }
    }
    print_simple_box_start("center");
    print_heading_with_help(get_string("theexercise", "exercise"), "junk", "exercise");
    print_simple_box_start("center");
    echo "<p align=\"center\"><b>".get_string("description", "exercise").": </b>\n";
    echo exercise_print_submission_title($exercise, $submission);
    echo "</p>\n";
    print_simple_box_end();
    print_simple_box_end();
 
    $table->head = array (get_string("action", "exercise"), get_string("assessed", "exercise"),
        get_string("comment", "exercise"));
    $table->align = array ("left", "left", "left");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // now list user's assessments (but only list those which come from teacher submissions)
    print_heading(get_string("yourassessment", "exercise"));
    $assessed = false;
    if ($assessments = exercise_get_user_assessments($exercise, $user)) {
        $timenow = time();
        foreach ($assessments as $assessment) {
            if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
                error ("exercise_list_teacher_submissions: unable to get submission");
            }
            // submission from a teacher, i.e an exercise submission?
            if ($submission->isexercise) {
                $comment = '';
                if ($reassess) {  // just show re-assess
                    $action = "<a href=\"assessments.php?action=assesssubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                        get_string("reassess", "exercise")."</a>";
                }
                else { // reassess is false - assessment is a "normal state"
                    // user assessment has three states: record created but not assessed (date created 
                    // in the future); just assessed but still editable; and "static" (may or may not 
                    // have been graded by teacher, that is shown in the comment) 
                    if ($assessment->timecreated > $timenow) { // user needs to assess this submission
                        $action = "<a href=\"assessments.php?action=assesssubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                            get_string("assess", "exercise")."</a>";
                    }
                    elseif ($assessment->timecreated > ($timenow - $CFG->maxeditingtime)) { 
                        // there's still time left to edit...
                        $action = "<a href=\"assessments.php?action=assesssubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                            get_string("edit", "exercise")."</a>";
                    }
                    else { 
                        $action = "<a href=\"assessments.php?action=viewassessment&amp;id=$cm->id&amp;aid=$assessment->id\">"
                            .get_string("view", "exercise")."</a>";
                    }
                }
                // show the date if in the past (otherwise the user hasn't done the assessment yet
                $assessmentdate = '';
                if ($assessment->timecreated < $timenow) {
                    $assessmentdate = userdate($assessment->timecreated);
                    // if user has submitted work, see if teacher has graded assessment
                    if (exercise_count_user_submissions($exercise, $user) > 0) {
                        if ($assessment->timegraded and (($timenow - $assessment->timegraded) > $CFG->maxeditingtime)) {
                            $comment .= get_string("gradeforassessment", "exercise").": ".
                                number_format($assessment->gradinggrade * $exercise->gradinggrade / 100.0, 1).
                                " (".get_string("maximumgrade")." ".number_format($exercise->gradinggrade, 0).")";
                            $assessed = true;
                        }
                        else {
                            $comment .= get_string("awaitingassessmentbythe", "exercise", $course->teacher);
                        }
                    }
                }
                $table->data[] = array($action, $assessmentdate, $comment);
            }
        }
        print_table($table);
        if ($assessed) {
            echo "<p align=\"center\">".get_string("noteongradinggrade", "exercise", $course->teacher)."</p>\n";
        }
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_unassessed_student_submissions($exercise, $user) {
    // list the student submissions not assessed by the teacher
    global $CFG;
    
    $timenow = time();
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    $table->head = array (get_string("title", "exercise"), get_string("submittedby", "exercise"),
        get_string("submitted", "exercise"), get_string("action", "exercise"), 
        get_string("comment", "exercise"));
    $table->align = array ("left", "left", "left", "left", "left");
    $table->size = array ("*", "*", "*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // get all the submissions, oldest first, youngest last
    // exercise_get_student_submissions is group aware
    $groupid = get_current_group($course->id);
    if ($groupid) {
        if (! groups_group_exists($groupid)) {
            error("List unassessed student submissions: group not found");
        }
        print_heading(get_string("studentsubmissionsforassessment", "exercise", $group->name));
    }
    if ($submissions = exercise_get_student_submissions($exercise, "time", $groupid)) {
        foreach ($submissions as $submission) {
            // only consider "cold" submissions
            if ($submission->timecreated < $timenow - $CFG->maxeditingtime) {
                $comment = "";
                // see if student has already submitted
                $submissionowner = get_record("user", "id", $submission->userid);
                if (exercise_count_user_submissions($exercise, $submissionowner) == 1) {
                    // it's the student's first submission 
                    // see if there are no cold assessments for this submission
                    if (!exercise_count_assessments($submission)) {
                        // now see if the teacher has already assessed this submission
                        $warm = false;
                        if ($assessments = get_records("exercise_assessments", "submissionid", $submission->id)) {
                            foreach ($assessments as $assessment) {
                                if (isteacher($course->id, $assessment->userid)) {
                                    if ($assessment->timecreated > $timenow -$CFG->maxeditingtime) {
                                        $warm = true;
                                    }
                                    break;  // no need to look further
                                }
                            }
                        }
                        // get their assessment
                        if ($assessments = exercise_get_user_assessments($exercise, $submissionowner)) {
                            foreach ($assessments as $assessment) {
                                $studentassessment = $assessment;
                                break; // there should only be one!
                            }
                            $timegap = get_string("ago", "exercise", format_time($submission->timecreated -
                                        $timenow));
                            if ($submission->late) {
                                $timegap = "<font color=\"red\">".$timegap."</font>";
                            }
                            if ($warm) {
                                // last chance salon
                                $action = "<a href=\"assessments.php?action=teacherassessment&amp;id=$cm->id&amp;aid=$studentassessment->id&amp;sid=$submission->id\">".
                                    get_string("edit", "exercise")."</a>";
                                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                                        fullname($submissionowner), 
                                        $timegap, $action, $comment);
                            } else {
                                $action = "<a href=\"assessments.php?action=teacherassessment&amp;id=$cm->id&amp;aid=$studentassessment->id&amp;sid=$submission->id\">".
                                    get_string("assess", "exercise")."</a>";
                                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                                        fullname($submissionowner), 
                                        $timegap, $action, $comment);
                            }
                        } else {
                            // there's no student assessment, odd!!
                        }
                    }
                }
                // this is student's second... submission
                else {
                    $teacherassessed = false;
                    $warm = false;
                    if ($assessments = get_records("exercise_assessments", "submissionid", $submission->id)) {
                        foreach ($assessments as $assessment) {
                            if (isteacher($course->id, $assessment->userid)) {
                                $teacherassessed = true;
                                if (!$teacher = get_record("user", "id", $assessment->userid)) {
                                    error("List unassessed student submissions: teacher record not found");
                                }
                                $comment = get_string("resubmissionfor", "exercise",
                                                fullname($teacher));
                                if ($assessment->timecreated > $timenow - $CFG->maxeditingtime) {
                                    $warm = true;
                                }
                                break; // no need to look further
                            }
                        }
                    }
                    if ($teacherassessed and $warm) {
                        // last chance salon
                        $action = "<a href=\"assessments.php?action=assessresubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                            get_string("edit", "exercise")."</a>";
                        $timegap = get_string("ago", "exercise", format_time($submission->timecreated -
                                    $timenow));
                        if ($submission->late) {
                            $timegap = "<font color=\"red\">".$timegap."</font>";
                        }
                        $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                            fullname($submissionowner), 
                            $timegap, $action, $comment);
                    }
                    if (!$teacherassessed) { 
                        // no teacher's assessment
                        // find who did the previous assessment
                        if (!$submissions = exercise_get_user_submissions($exercise, $submissionowner)) {
                            error("List unassessed student submissions: submission records not found");
                        }
                        // get the oldest submission, exercise_get_user_submissions returns that first
                        foreach ($submissions as $tempsubmission) {
                            $prevsubmission = $tempsubmission;
                            break;
                        }
                        // get the teacher's assessment of the student's previous submission
                        if ($assessments = get_records("exercise_assessments", "submissionid", 
                                    $prevsubmission->id)) {
                            foreach ($assessments as $assessment) {
                                if (isteacher($course->id, $assessment->userid)) {
                                    if (!$teacher = get_record("user", "id", $assessment->userid)) {
                                        error("List unassessed student submissions: teacher record not found");
                                    }
                                    $comment = get_string("resubmissionfor", "exercise",
                                                    fullname($teacher));
                                    break; // no need to look further
                                    
                                }
                            }
                        }
                        $action = "<a href=\"assessments.php?action=assessresubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                            get_string("assess", "exercise")."</a>";
                        $timegap = get_string("ago", "exercise", format_time($submission->timecreated -
                                    $timenow));
                        if ($submission->late) {
                             $timegap = "<font color=\"red\">".$timegap."</font>";
                        }
                        $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                            fullname($submissionowner), 
                            $timegap, $action, $comment);
                    }
                }
            }
        }
        if (isset($table->data)) {
            print_table($table);
        }
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_unassessed_teacher_submissions($exercise, $user) {
    // list the teacher submissions not assessed by this user
    global $CFG;
    
    $table->head = array (get_string("title", "exercise"), get_string("action", "exercise"), get_string("comment", "exercise"));
    $table->align = array ("left", "left", "left");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = exercise_get_teacher_submissions($exercise)) {
        foreach ($submissions as $submission) {
            $comment = "";
            // see if user already graded this assessment
            if ($assessment = get_record_select("exercise_assessments", "submissionid = $submission->id
                    AND userid = $user->id")) {
                $timenow = time();
                if (($timenow - $assessment->timecreated < $CFG->maxeditingtime)) {
                    // last chance salon
                    $action = "<a href=\"assessments.php?action=assesssubmission&amp;a=$exercise->id&amp;sid=$submission->id\">".
                        get_string("edit", "exercise")."</a>";
                    $table->data[] = array(exercise_print_submission_title($exercise, $submission), $action, $comment);
                    }
                }
            else { // no assessment
                $action = "<a href=\"assessments.php?action=assesssubmission&amp;a=$exercise->id&amp;sid=$submission->id\">".
                    get_string("assess", "exercise")."</a>";
                $table->data[] = array(exercise_print_submission_title($exercise, $submission), $action, $comment);
                }
            }
        if (isset($table->data)) {
            print_table($table);
            }
        }
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_ungraded_assessments($exercise, $stype) {
    global $CFG;
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    // lists all the assessments of student submissions for grading by teacher
    $table->head = array (get_string("title", "exercise"), get_string("submittedby", "exercise"),
    get_string("assessor", "exercise"), get_string("timeassessed", "exercise"), get_string("action", "exercise"));
    $table->align = array ("left", "left", "left", "left");
    $table->size = array ("*", "*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;
    $timenow = time();
    
    switch ($stype) {
        case "student" :
            $assessments = exercise_get_ungraded_assessments_student($exercise);
            break;
        case "teacher" :
            $assessments = exercise_get_ungraded_assessments_teacher($exercise);
            break;
        }
    if ($assessments) {
        foreach ($assessments as $assessment) {
            if (!isteacher($exercise->course, $assessment->userid)) { // don't let teacher grade their own assessments
                if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
                    $action = "<a href=\"assessments.php?action=gradeassessment&amp;id=$cm->id&amp;stype=$stype&amp;aid=$assessment->id\">".
                        get_string("edit", "exercise")."</a>";
                    }
                else {
                    $action = "<a href=\"assessments.php?action=gradeassessment&amp;id=$cm->id&amp;stype=$stype&amp;aid=$assessment->id\">".
                        get_string("grade")."</a>";
                    }
                $submission = get_record("exercise_submissions", "id", $assessment->submissionid);
                $submissionowner = get_record("user", "id", $submission->userid);
                $assessor = get_record("user", "id", $assessment->userid);
                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                    fullname($submissionowner), 
                    fullname($assessor), userdate($assessment->timecreated), $action);
                }
            }
        if (isset($table->data)) {
            print_table($table);
            }
        }
    }
    

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_user_submissions($exercise, $user) {
    global $CFG;

    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    
    $timenow = time();
    $table->head = array (get_string("title", "exercise"),  get_string("action", "exercise"),
        get_string("submitted", "exercise"),  get_string("assessment", "exercise"));
    $table->align = array ("left", "left", "left", "left");
    $table->size = array ("*", "*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        foreach ($submissions as $submission) {
            $action = '';
            $comment = '';
            // allow user to delete submission if it's warm
            if ($submission->timecreated > $timenow - $CFG->maxeditingtime) {
                $action = "<a href=\"submissions.php?action=userconfirmdelete&amp;id=$cm->id&amp;sid=$submission->id\">".
                    get_string("delete", "exercise")."</a>";
            }
            // if this is a teacher's submission (an exercise description) ignore any assessments
            if (!$submission->isexercise) {
                // get the teacher assessments (could be more than one, if unlikely, when multiple teachers)
                if ($assessments = get_records_select("exercise_assessments", "exerciseid = $exercise->id AND 
                            submissionid = $submission->id")) {
                    foreach ($assessments as $assessment) {
                        // make sure it's real
                        if ($assessment->timecreated < $timenow - $CFG->maxeditingtime) { // it's cold
                            if ($action) {
                                $action .= " | ";
                            }
                            $action .= "<a href=\"assessments.php?action=viewassessment&amp;id=$cm->id&amp;aid=$assessment->id\">".
                                get_string("viewteacherassessment", "exercise", $course->teacher)."</a>";
                            if ($comment) {
                                $comment .= " | ";
                            }
                            $comment .= get_string("teacherassessment", "exercise", $course->teacher).": ".
                                number_format($assessment->grade * $exercise->grade / 100.0, 1).
                                " (".get_string("maximumgrade")." ".number_format($exercise->grade, 0).")";
                        }
                    }
                }
            }
            if (!$comment and isstudent($course->id, $user->id)) {
                $comment = get_string("awaitingassessmentbythe", "exercise", $course->teacher);
            }
            $submissiondate = userdate($submission->timecreated);
            if ($submission->late) {
                $submissiondate = "<font color=\"red\">".$submissiondate."</font>";
            }
            $table->data[] = array(exercise_print_submission_title($exercise, $submission), $action,
                $submissiondate, $comment);
        }
        print_table($table);
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assessment_form($exercise, $assessment = false, $allowchanges = false, $returnto = '') {
    // prints several variants of the assessment form
    global $CFG, $USER, $EXERCISE_SCALES, $EXERCISE_EWEIGHTS;
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    
    $timenow = time();

    if ($assessment) {
            
        if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
            error ("exercise_print_assessment_form: Submission record not found");
            }
        // test if this assessment is from a teacher or student.
        // Teacher's assessments are more complicated as we need to go back a couple of steps
        // to find the exercise. Student's assessments are directly associated with an exercise.
        if (isteacher($course->id, $assessment->userid)) { 
            // A teacher's assessment, requires getting the student's assessment(s) 
            // and finding which of those assessments which comes from a teacher submission,
            // that is the exercise
            $exercisefound = false;
            if (!$submissionowner = get_record("user", "id", $submission->userid)) {
                error ("exercise_print_assessment_form: User record not found");
                }
            if ($initialassessments = exercise_get_user_assessments($exercise, $submissionowner)) {
                // should only be one but we'll loop anyway
                foreach($initialassessments as $initialassessment) {
                    if (!$teachersubmission = get_record("exercise_submissions", "id", $initialassessment->submissionid)) {
                        error ("exercise_print_assessment_form: Teacher Submission record not found");
                        }
                    if ($teachersubmission->isexercise) {
                        $exercisefound = true;
                        break;
                        }
                    }
                }
            if ($exercisefound) {
                print_heading(get_string("theexerciseandthesubmissionby", "exercise", 
                    fullname($submissionowner)));
                echo "<center><table border=\"1\" width=\"30%\"><tr>
                    <td align=\"center\">\n";
                echo exercise_print_submission_title($exercise, $teachersubmission);
                echo "</td></tr></table><br clear=\"all\" />\n";
                }
            }
        else { 
            // it's a student assessment, print instructions if it's their own assessment
            if ($assessment->userid == $USER->id) {
                print_heading_with_help(get_string("pleaseusethisform", "exercise"), "grading", "exercise");
                }
            }
            
        echo "<center><table border=\"1\" width=\"30%\"><tr>
            <td align=\"center\">\n";
        echo exercise_print_submission_title($exercise, $submission);
        echo "</td></tr></table><br clear=\"all\" />\n";
        
        // only show the grade if grading strategy > 0 and the grade is positive
        if ($exercise->gradingstrategy and $assessment->grade >= 0) { 

            echo "<center><b>".get_string("thegradeis", "exercise").": ".
                number_format($assessment->grade * $exercise->grade / 100.0, 2)." (".
                get_string("maximumgrade")." ".number_format($exercise->grade, 0).")</b></center><br clear=\"all\" />\n";
            }
        }
        
    // now print the grading form with the teacher's comments if any
    // FORM is needed for Mozilla browsers, else radio bttons are not checked
        ?>
    <form id="assessmentform" method="post" action="assessments.php">
    <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
    <input type="hidden" name="aid" value="<?php echo $assessment->id ?>" />
    <input type="hidden" name="action" value="updateassessment" />
    <input type="hidden" name="resubmit" value="0" />
    <input type="hidden" name="returnto" value="<?php echo $returnto ?>" />
    <?php
    if ($assessment) {
        if (!$assessmentowner = get_record("user", "id", $assessment->userid)) {
            error("Exercise_print_assessment_form: could not find user record");
            }
        if ($assessmentowner->id == $USER->id) {
            $formtitle = get_string("yourassessment", "exercise");
            }
        else {
            $formtitle = get_string("assessmentby", "exercise", fullname($assessmentowner));
            }
        }
    else {
        $formtitle = get_string("assessmentform", "exercise");
        }
    echo "<center><table cellpadding=\"2\" border=\"1\">\n";
    echo "<tr valign=\"top\">\n";

    echo "  <td colspan=\"2\"><center><b>$formtitle</b></center></td>\n";

    echo "</tr>\n";

    // get the assignment elements...
    if (!$elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC")) {
        print_string("noteonassignmentelements", "exercise");
        }
    else {
        foreach ($elementsraw as $element) {
            $elements[] = $element;   // to renumber index 0,1,2...
            }
        }

    if ($assessment) {
        $assessment->generalcomment = clean_text($assessment->generalcomment); //clean html first
        // get any previous grades...
        if ($gradesraw = get_records_select("exercise_grades", "assessmentid = $assessment->id", "elementno")) {
            foreach ($gradesraw as $grade) {
                $grade->feedback = clean_text($grade->feedback); //clean the html first
                $grades[] = $grade;   // to renumber index 0,1,2...
                }
            }
        }
    else {
        // setup dummy grades array
        for($i = 0; $i < count($elementsraw); $i++) { // gives a suitable sized loop
            $grades[$i]->feedback = get_string("yourfeedbackgoeshere", "exercise");
            $grades[$i]->grade = 0;
            }
        }

    // determine what sort of grading
    switch ($exercise->gradingstrategy) {
        case 0:  // no grading
            // now print the form
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("element","exercise")." $iplus1:</b></p></td>\n";
                echo "  <td>".text_to_html($elements[$i]->description);
                echo "</td></tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                echo "  <td>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                        }
                    echo "</textarea>\n";
                    }
                else {
                    echo text_to_html($grades[$i]->feedback);
                    }
                echo "  </td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td colspan=\"2\">&nbsp;</td>\n";
                echo "</tr>\n";
                }
            break;
            
        case 1: // accumulative grading
            // now print the form
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("element","exercise")." $iplus1:</b></p></td>\n";

                echo "  <td>".text_to_html($elements[$i]->description);
                echo "<p align=\"right\"><font size=\"1\">Weight: "
                    .number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font>\n";
                echo "</td></tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("grade"). ":</b></p></td>\n";
                echo "  <td valign=\"top\">\n";
                
                // get the appropriate scale
                $scalenumber=$elements[$i]->scale;
                $SCALE = (object)$EXERCISE_SCALES[$scalenumber];
                switch ($SCALE->type) {
                    case 'radio' :
                            // show selections highest first
                            echo "<center><b>$SCALE->start</b>&nbsp;&nbsp;&nbsp;";
                            for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
                                $checked = false;
                                if (isset($grades[$i]->grade)) { 
                                    if ($j == $grades[$i]->grade) {
                                        $checked = true;
                                        }
                                    }
                                else { // there's no previous grade so check the lowest option
                                    if ($j == 0) {
                                        $checked = true;
                                        }
                                    }
                                if ($checked) {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" checked=\"checked\" /> &nbsp;&nbsp;&nbsp;\n";
                                    }
                                else {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" /> &nbsp;&nbsp;&nbsp;\n";
                                    }
                                }
                            echo "&nbsp;&nbsp;&nbsp;<b>$SCALE->end</b></center>\n";
                            break;
                    case 'selection' :  
                            unset($numbers);
                            for ($j = $SCALE->size; $j >= 0; $j--) {
                                $numbers[$j] = $j;
                                }
                            if (isset($grades[$i]->grade)) {
                                choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
                                }
                            else {
                                choose_from_menu($numbers, "grade[$i]", 0, "");
                                }
                            break;
                        
                    echo "  </td>\n";
                    echo "</tr>\n";
                    }
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                echo "  <td>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                        }
                    echo "</textarea>\n";
                    }
                else {
                    echo text_to_html($grades[$i]->feedback);
                    }
                echo "  </td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td colspan=\"2\">&nbsp;</td>\n";
                echo "</tr>\n";
                }
            break;
            
        case 2: // error banded grading
            // now run through the elements
            $error = 0;
            for ($i=0; $i < count($elements) - 1; $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";

                echo "  <td align=\"right\"><p><b>". get_string("element","exercise")." $iplus1:</b></p></td>\n";

                echo "  <td>".text_to_html($elements[$i]->description);
                echo "<p align=\"right\"><font size=\"1\">Weight: "
                    .number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font>\n";
                echo "</td></tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("grade"). ":</b></p></td>\n";
                echo "  <td valign=\"top\">\n";
                    
                // get the appropriate scale - yes/no scale (0)
                $SCALE = (object) $EXERCISE_SCALES[0];
                switch ($SCALE->type) {
                    case 'radio' :
                            // show selections highest first
                            echo "<center><b>$SCALE->start</b>&nbsp;&nbsp;&nbsp;";
                            for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
                                $checked = false;
                                if (isset($grades[$i]->grade)) { 
                                    if ($j == $grades[$i]->grade) {
                                        $checked = true;
                                        }
                                    }
                                else { // there's no previous grade so check the lowest option
                                    if ($j == 0) {
                                        $checked = true;
                                        }
                                    }
                                if ($checked) {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" checked=\"checked\" /> &nbsp;&nbsp;&nbsp;\n";
                                    }
                                else {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" /> &nbsp;&nbsp;&nbsp;\n";
                                    }
                                }
                            echo "&nbsp;&nbsp;&nbsp;<b>$SCALE->end</b></center>\n";
                            break;
                    case 'selection' :  
                            unset($numbers);
                            for ($j = $SCALE->size; $j >= 0; $j--) {
                                $numbers[$j] = $j;
                                }
                            if (isset($grades[$i]->grade)) {
                                choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
                                }
                            else {
                                choose_from_menu($numbers, "grade[$i]", 0, "");
                                }
                            break;
                    }
        
                echo "  </td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                echo "  <td>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[$i]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                        }
                    echo "</textarea>\n";
                    }
                else {
                    if (isset($grades[$i]->feedback)) {
                        echo text_to_html($grades[$i]->feedback);
                        }
                    }
                echo "&nbsp;</td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td colspan=\"2\">&nbsp;</td>\n";
                echo "</tr>\n";
                if (empty($grades[$i]->grade)) {
                    $error += $EXERCISE_EWEIGHTS[$elements[$i]->weight];
                    }
                }
            // print the number of negative elements
            // echo "<tr><td>".get_string("numberofnegativeitems", "exercise")."</td><td>$negativecount</td></tr>\n";
            // echo "<tr valign=\"top\">\n";
            // echo "   <td colspan=\"2\">&nbsp;</td>\n";
            echo "</table></center>\n";
            // now print the grade table
            echo "<p><center><b>".get_string("gradetable","exercise")."</b></center>\n";
            echo "<center><table cellpadding=\"5\" border=\"1\"><tr><td align=\"CENTER\">".
                get_string("numberofnegativeresponses", "exercise");
            echo "</td><td>". get_string("suggestedgrade", "exercise")."</td></tr>\n";
            for ($i=0; $i<=$exercise->nelements; $i++) {
                if ($i == intval($error + 0.5)) {
                    echo "<tr><td align=\"CENTER\"><img src=\"$CFG->pixpath/t/right.gif\" alt=\"\" /> $i</td><td align=\"CENTER\">{$elements[$i]->maxscore}</td></tr>\n";
                    }
                else {
                    echo "<tr><td align=\"CENTER\">$i</td><td align=\"CENTER\">{$elements[$i]->maxscore}</td></tr>\n";
                    }
                }
            echo "</table></center>\n";
            echo "<p><center><table cellpadding=\"5\" border=\"1\"><tr><td align=\"right\"><b>".
                get_string("optionaladjustment", "exercise").":</b></td><td>\n";
            unset($numbers);
            for ($j = 20; $j >= -20; $j--) {
                $numbers[$j] = $j;
                }
            if (isset($grades[$exercise->nelements]->grade)) {
                choose_from_menu($numbers, "grade[$exercise->nelements]", $grades[$exercise->nelements]->grade, "");
                }
            else {
                choose_from_menu($numbers, "grade[$exercise->nelements]", 0, "");
                }
            echo "</td></tr>\n";
            break;
            
        case 3: // criteria grading
            echo "<tr valign=\"top\">\n";

            echo "  <td>&nbsp;</td>\n";

            echo "  <td><b>". get_string("criterion","exercise")."</b></td>\n";

            echo "  <td><b>".get_string("select")."</b></td>\n";
            echo "  <td><b>".get_string("suggestedgrade", "exercise")."</b></td>\n";
            // find which criteria has been selected (saved in the zero element), if any
            if (isset($grades[0]->grade)) {
                $selection = $grades[0]->grade;
                }
            else {
                $selection = 0;
                }
            // now run through the elements
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";

                echo "  <td>$iplus1</td><td>".text_to_html($elements[$i]->description)."</td>\n";
                if ($selection == $i) {
                    echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[0]\" value=\"$i\" checked=\"checked\" /></td>\n";
                    }
                else {
                    echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[0]\" value=\"$i\" /></td>\n";
                    }
                echo "<td align=\"center\">{$elements[$i]->maxscore}</td></tr>\n";
                }
            echo "</table></center>\n";
            echo "<p><center><table cellpadding=\"5\" border=\"1\"><tr><td align=\"right\"><b>".
                get_string("optionaladjustment", "exercise")."</b></td><td>\n";
            unset($numbers);
            for ($j = 20; $j >= -20; $j--) {
                $numbers[$j] = $j;
                }
            if (isset($grades[1]->grade)) {
                choose_from_menu($numbers, "grade[1]", $grades[1]->grade, "");
                }
            else {
                choose_from_menu($numbers, "grade[1]", 0, "");
                }
            echo "</td></tr>\n";
            break;
            
        case 4: // rubric grading
            // now run through the elements...
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";

                echo "<td align=\"right\"><b>".get_string("element", "exercise")." $iplus1:</b></td>\n";
                echo "<td>".text_to_html($elements[$i]->description).
                     "<p align=\"right\"><font size=\"1\">Weight: "
                    .number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font></td></tr>\n";
                echo "<tr valign=\"top\">\n";

                echo "  <td align=\"center\"><b>".get_string("select")."</b></td>\n";
                echo "  <td><b>". get_string("criterion","exercise")."</b></td></tr>\n";

                if (isset($grades[$i])) {
                    $selection = $grades[$i]->grade;
                    } else {
                    $selection = 0;
                    }
                // ...and the rubrics
                if ($rubricsraw = get_records_select("exercise_rubrics", "exerciseid = $exercise->id AND 
                        elementno = $i", "rubricno ASC")) {
                    unset($rubrics);
                    foreach ($rubricsraw as $rubic) {
                        $rubrics[] = $rubic;   // to renumber index 0,1,2...
                        }
                    for ($j=0; $j<5; $j++) {
                        if (empty($rubrics[$j]->description)) {
                            break; // out of inner for loop
                            }
                        echo "<tr valign=\"top\">\n";

                        if ($selection == $j) {
                            echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" checked=\"checked\" /></td>\n";
                            }else {
                            echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" /></td>\n";
                            }
                        echo "<td>".text_to_html($rubrics[$j]->description)."</td>\n";
                        }
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                    echo "  <td>\n";
                    if ($allowchanges) {
                        echo "      <textarea name=\"feedback[]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                        if (isset($grades[$i]->feedback)) {
                            echo $grades[$i]->feedback;
                            }
                        echo "</textarea>\n";
                        }
                    else {
                        echo text_to_html($grades[$i]->feedback);
                        }
                    echo "  </td>\n";
                    echo "</tr>\n";
                    echo "<tr valign=\"top\">\n";
                    echo "  <td colspan=\"2\">&nbsp;</td>\n";
                    echo "</tr>\n";
                    }
                }
            break;
        } // end of outer switch
    
    // now get the general comment (present in all types)
    echo "<tr valign=\"top\">\n";
    switch ($exercise->gradingstrategy) {
        case 0:
        case 1:
        case 4 : // no grading, accumulative and rubic
            echo "  <td align=\"right\"><p><b>". get_string("generalcomment", "exercise").":</b></p></td>\n";
            break; 
        default : 
            echo "  <td align=\"right\"><p><b>". get_string("reasonforadjustment", "exercise").":</b></p></td>\n";
        }
    echo "  <td>\n";
    if ($allowchanges) {
        echo "      <textarea name=\"generalcomment\" rows=\"5\" cols=\"75\" wrap=\"virtual\">\n";
        if (isset($assessment->generalcomment)) {
            echo $assessment->generalcomment;
            }
        echo "</textarea>\n";
        }
    else {
        if ($assessment) {
            if (isset($assessment->generalcomment)) {
                echo text_to_html($assessment->generalcomment);
                }
            }
        else {
            print_string("yourfeedbackgoeshere", "exercise");
            }
        }

    echo "&nbsp;</td>\n";

    echo "</tr>\n";

    echo "<tr valign=\"top\">\n";

    echo "  <td colspan=\"2\">&nbsp;</td>\n";

    echo "</tr>\n";
    
    $timenow = time();
    
    // always show the teacher the grading grade if it's not their assessment!
    if (isteacher($course->id) and ($assessment->userid != $USER->id) and $exercise->gradinggrade) {  
        echo "<tr><td align=\"right\"><b>".get_string("gradeforstudentsassessment", "exercise", $course->student).
            "</td><td>\n";
        echo number_format($exercise->gradinggrade * $assessment->gradinggrade / 100.0, 0);
        echo "</td></tr>\n";
        }
        
    // ...and close the table, show buttons if needed...
    echo "</table><br />\n";
    if ($assessment and $allowchanges) {  
        if (isteacher($course->id)) { 
            // ...show two buttons...to resubmit or not to resubmit
            echo "<input type=\"button\" value=\"".get_string("studentnotallowed", "exercise", $course->student)."\" 
                onclick=\"getElementById('assessmentform').submit();\" />\n";
            echo "<input type=\"button\" value=\"".get_string("studentallowedtoresubmit", "exercise", $course->student)."\" 
                onclick=\"getElementById('assessmentform').resubmit.value='1';getElementById('assessmentform').submit();\" />\n";
            }
        else {
            // ... show save button
            echo "<input type=\"submit\" value=\"".get_string("savemyassessment", "exercise")."\" />\n";
            }
        }
    echo "</center></form>\n";
    }



///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assessments_by_user_for_admin($exercise, $user) {

    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
        }

    if ($assessments =exercise_get_user_assessments($exercise, $user)) {
        foreach ($assessments as $assessment) {
            echo "<p><center><b>".get_string("assessmentby", "exercise", fullname($user))."</b></center></p>\n";
            exercise_print_assessment_form($exercise, $assessment);
            echo "<p align=\"right\"><a href=\"assessments.php?action=adminamendgradinggrade&amp;id=$cm->id&amp;aid=$assessment->id\">".
                get_string("amend", "exercise")." ".get_string("gradeforstudentsassessment","exercise",
                $course->student)."</a>\n";
            echo " | <a href=\"assessments.php?action=adminconfirmdelete&amp;id=$cm->id&amp;aid=$assessment->id\">".
                get_string("delete", "exercise")."</a></p><hr />\n";
            }
        }
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assessments_for_admin($exercise, $submission) {

    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
        }

    if ($assessments = exercise_get_assessments($submission)) {
        foreach ($assessments as $assessment) {
            if (!$user = get_record("user", "id", $assessment->userid)) {
                error (" exercise_print_assessments_for_admin: unable to get user record");
                }
            echo "<p><center><b>".get_string("assessmentby", "exercise", fullname($user))."</b></center></p>\n";
            exercise_print_assessment_form($exercise, $assessment);
            echo "<p align=\"right\"><a href=\"assessments.php?action=adminconfirmdelete&amp;id=$cm->id&amp;aid=$assessment->id\">".
                get_string("delete", "exercise")."</a></p><hr />\n";
            }
        }
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assignment_info($exercise) {

    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    // print standard assignment heading
    $strdifference = format_time($exercise->deadline - time());
    if (($exercise->deadline - time()) < 0) {
        $strdifference = "<font color=\"red\">$strdifference</font>";
    }
    $strduedate = userdate($exercise->deadline)." ($strdifference)";
    print_simple_box_start("center");
    print_heading(format_string($exercise->name), "center");
    print_simple_box_start("center");
    echo "<b>".get_string("duedate", "exercise")."</b>: $strduedate<br />";
    $maxgrade = $exercise->grade + $exercise->gradinggrade;
    echo "<b>".get_string("maximumgrade")."</b>: $maxgrade<br />";
    echo "<b>".get_string("handlingofmultiplesubmissions", "exercise")."</b>:";
    if ($exercise->usemaximum) {
        echo get_string("usemaximum", "exercise")."<br />\n";
    }
    else {
        echo get_string("usemean", "exercise")."<br />\n";
    }
    echo "<b>".get_string("detailsofassessment", "exercise")."</b>: 
        <a href=\"assessments.php?id=$cm->id&amp;action=displaygradingform\">".
        get_string("specimenassessmentform", "exercise")."</a><br />";
    print_simple_box_end();
    print_simple_box_end();
    echo "<br />";  
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_difference($time) {
    if ($time < 0) {
        $timetext = get_string("late", "assignment", format_time($time));
        return " (<font color=\"red\">$timetext</font>)";
    } else {
        $timetext = get_string("early", "assignment", format_time($time));
        return " ($timetext)";
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_feedback($course, $submission) {
    global $CFG, $RATING;

    if (! $teacher = get_record("user", "id", $submission->teacher)) {
        error("Weird exercise error");
    }

    echo "\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\"><tr><td bgcolor=#888888>";
    echo "\n<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\" valign=\"top\">";

    echo "\n<tr>";
    echo "\n<td rowspan=\"3\" width=\"35\" valign=\"top\">";
    print_user_picture($teacher->id, $course->id, $teacher->picture);
    echo "</td>";
    echo "<td nowrap=\"nowrap\" width=\"100%\">".fullname($teacher);
    echo "&nbsp;&nbsp;<font size=\"2\"><i>".userdate($submission->timemarked)."</i>";
    echo "</tr>";

    echo "\n<tr><td width=\"100%\">";

    echo "<p align=\"right\"><font size=-1><i>";
    if ($submission->grade) {
        echo get_string("grade").": $submission->grade";
    } else {
        echo get_string("nograde");
    }
    echo "</i></font></p>";

    echo text_to_html($submission->assessorcomment);
    echo "</td></tr></table>";
    echo "</td></tr></table>";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_league_table($exercise) {
    // print an order table of (student) submissions in grade order, only print the student's best submission when
    // there are multiple submissions
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Print league table: Course is misconfigured");
    }
    $groupid = get_current_group($course->id);
    $nentries = $exercise->showleaguetable;
    if ($nentries == 99) {
        $nentries = 999999; // a large number
        }

    if ($exercise->anonymous and isstudent($course->id)) {
        $table->head = array (get_string("title", "exercise"), get_string("grade"));
        $table->align = array ("left", "center");
        $table->size = array ("*", "*");
    } else { // show names
        $table->head = array (get_string("title", "exercise"),  get_string("name"), get_string("grade"));
        $table->align = array ("left", "left", "center");
        $table->size = array ("*", "*", "*");
    }
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = exercise_get_student_submissions($exercise, "grade", $groupid)) {
        $n = 1;
        foreach ($submissions as $submission) {
            if (empty($done[$submission->userid])) {
                if ($submission->late) {
                    continue;
                }
                if (!$user = get_record("user", "id", $submission->userid)) {
                    error("Print league table: user not found");
                    }
                if ($exercise->anonymous and isstudent($course->id)) {
                    $table->data[] = array(exercise_print_submission_title($exercise, $submission),
                            number_format($submission->grade * $exercise->grade / 100.0, 1));
                } else {
                    $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                            fullname($user), 
                            number_format($submission->grade * $exercise->grade / 100.0, 1));
                }
                $n++;
                if ($n > $nentries) {
                    break;
                }
                $done[$submission->userid] = 'ok';
                }
            }
        print_heading(get_string("leaguetable", "exercise"));
        print_table($table);
        }
    }
    

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_submission_assessments($exercise, $submission) {
    // Returns a list of grades for this submission
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    
    $str = '';
    if ($assessments = exercise_get_assessments($submission)) {
        foreach ($assessments as $assessment) {
            if (isteacher($exercise->course, $assessment->userid)) {
                $str .= "[".number_format($assessment->grade * $exercise->grade / 100.0, 0)."] ";
                }
            else { // assessment by student - shouldn't happen!
                $str .= "{".number_format($assessment->grade * $exercise->grade / 100.0, 0)."} ";
                }
            }
        }
    if (!$str) {
        $str = "&nbsp;";   // be kind to Mozilla browsers!
        }
    return $str;
}


//////////////////////////////////////////////////////////////////////////////////////
function exercise_print_tabbed_heading($tabs) {
// Prints a tabbed heading where one of the tabs highlighted.
// $tabs is an object with several properties.
//      $tabs->names      is an array of tab names
//      $tabs->urls       is an array of links
//      $tabs->align     is an array of column alignments (defaults to "center")
//      $tabs->size      is an array of column sizes
//      $tabs->wrap      is an array of "nowrap"s or nothing
//      $tabs->highlight    is an index (zero based) of "active" heading .
//      $tabs->width     is an percentage of the page (defualts to 80%)
//      $tabs->cellpadding    padding on each cell (defaults to 5)

    global $CFG;
    
    if (isset($tabs->names)) {
        foreach ($tabs->names as $key => $name) {
            if (!empty($tabs->urls[$key])) {
                $url =$tabs->urls[$key];
                if ($tabs->highlight == $key) {
                    $tabcontents[$key] = "<b>$name</b>";
                } else {
                    $tabcontents[$key] = "<a class= \"dimmed\" href=\"$url\"><b>$name</b></a>";
                }
            } else {
                $tabcontents[$key] = "<b>$name</b>";
            }
        }
    }

    if (empty($tabs->width)) {
        $tabs->width = "80%";
    }

    if (empty($tabs->cellpadding)) {
        $tabs->cellpadding = "5";
    }

    // print_simple_box_start("center", "$table->width", "#ffffff", 0);
    echo "<table width=\"$tabs->width\" border=\"0\" valign=\"top\" align=\"center\" ";
    echo " cellpadding=\"$tabs->cellpadding\" cellspacing=\"0\" class=\"generaltable\">\n";

    if (!empty($tabs->names)) {
        echo "<tr>";
        echo "<td  class=\"generaltablecell\">".
            "<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\" /></td>\n";
        foreach ($tabcontents as $key => $tab) {
            if (isset($align[$key])) {
                $alignment = "align=\"$align[$key]\"";
            } else {
                $alignment = "align=\"center\"";
            }
            if (isset($size[$key])) {
                $width = "width=\"$size[$key]\"";
            } else {
                $width = "";
            }
            if (isset($wrap[$key])) {
                $wrapping = "no wrap";
            } else {
                $wrapping = "";
            }
            if ($key == $tabs->highlight) {
                echo "<td valign=\"top\" class=\"generaltabselected\" $alignment $width $wrapping>$tab</td>\n";
            } else {
                echo "<td valign=\"top\" class=\"generaltab\" $alignment $width $wrapping>$tab</td>\n";
            }
        echo "<td  class=\"generaltablecell\">".
            "<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\" /></td>\n";
        }
        echo "</tr>\n";
    } else {
        echo "<tr><td>No names specified</td></tr>\n";
    }
    // bottom stripe
    $ncells = count($tabs->names)*2 +1;
    $height = 2;
    echo "<tr><td colspan=\"$ncells\">".
        "<img height=\"$height\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\" /></td></tr>\n";
    echo "</table>\n";
    // print_simple_box_end();

    return true;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_teacher_assessment_form($exercise, $assessment, $submission, $returnto = '') {
    // prints an assessment form based on the student's assessment 
    // if the teacher is re-assessing a submission they would use exercise_print_assessment_form()
    // (for teachers only)
    global $CFG, $USER, $EXERCISE_SCALES, $EXERCISE_EWEIGHTS;
    
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    
    $timenow = time();

    if(!$submissionowner = get_record("user", "id", $submission->userid)) {
        error("Print teacher assessment form: User record not found");
    }

    echo "<center><table border=\"1\" width=\"30%\"><tr>
        <td align=\"center\">\n";
    if (!$teachersubmission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
        error ("Print teacher assessment form: Submission record not found");
    }
    echo exercise_print_submission_title($exercise, $teachersubmission);
    echo "</td></tr></table><br clear=\"all\" />\n";
    
    echo "<center><table border=\"1\" width=\"30%\"><tr>
        <td align=\"center\">\n";
    echo exercise_print_submission_title($exercise, $submission);
    echo "</td></tr></table></center><br clear=\"all\" />\n";

    ?>
    <form id="assessmentform" method="post" action="assessments.php">
    <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
    <input type="hidden" name="said" value="<?php echo $assessment->id ?>" />
    <input type="hidden" name="sid" value="<?php echo $submission->id ?>" />
    <input type="hidden" name="action" value="updateteacherassessment" />
    <input type="hidden" name="resubmit" value="0" />
    <input type="hidden" name="returnto" value="<?php echo $returnto ?>" />
    <?php

    // now print a normal assessment form based on the student's assessment for this submission 
    // and allow the teacher to grade and add additional comments
    $studentassessment = $assessment;
    $allowchanges = true;
    
    print_heading_with_help(get_string("pleasemakeyourownassessment", "exercise",
        fullname($submissionowner)), "grading", "exercise");
    
    // is there an existing assessment for the submission
    if (!$assessment = exercise_get_submission_assessment($submission, $USER)) {
        // copy student's assessment with their comments for the teacher's assessment
        $assessment = exercise_copy_assessment($studentassessment, $submission, true);
        }

    // only show the grade if grading strategy > 0 and the grade is positive
    if ($exercise->gradingstrategy and $assessment->grade >= 0) { 
        echo "<center><b>".get_string("thegradeis", "exercise").": ".
            number_format($assessment->grade * $exercise->grade / 100.0, 2)." (".
            get_string("maximumgrade")." ".number_format($exercise->grade, 0).")</b></center><br clear=\"all\" />\n";
        }
        
    echo "<center><table cellpadding=\"2\" border=\"1\">\n";
    echo "<tr valign=\"top\">\n";
    echo "  <td colspan=\"2\"><center><b>".get_string("yourassessment", "exercise").
        "</b></center></td>\n";
    echo "</tr>\n";
    
    // get the assignment elements...
    if (!$elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC")) {
        error("Teacher assessment form: Elements not found");
    }
    foreach ($elementsraw as $element) {
        $elements[] = $element;   // to renumber index 0,1,2...
    }
     
    // ...and get any previous grades...
    if ($gradesraw = get_records_select("exercise_grades", "assessmentid = $assessment->id", "elementno")) {
        foreach ($gradesraw as $grade) {
            $grades[] = $grade;   // to renumber index 0,1,2...
        }
    }
                
    // determine what sort of grading
    switch ($exercise->gradingstrategy) {
        case 0:  // no grading
            // now print the form
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("element","exercise")." $iplus1:</b></p></td>\n";
                echo "  <td>".text_to_html($elements[$i]->description);
                echo "</td></tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                echo "  <td>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                    }
                    echo "</textarea>\n";
                }
                else {
                    echo text_to_html($grades[$i]->feedback);
                }
                echo "  </td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td colspan=\"2\">&nbsp;</td>\n";
                echo "</tr>\n";
            }
            break;
            
        case 1: // accumulative grading
            // now print the form
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("element","exercise")." $iplus1:</b></p></td>\n";
                echo "  <td>".text_to_html($elements[$i]->description);
                echo "<p align=\"right\"><font size=\"1\">Weight: "
                    .number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font>\n";
                echo "</td></tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("grade"). ":</b></p></td>\n";
                echo "  <td valign=\"top\">\n";
                
                // get the appropriate scale
                $scalenumber=$elements[$i]->scale;
                $SCALE = (object)$EXERCISE_SCALES[$scalenumber];
                switch ($SCALE->type) {
                    case 'radio' :
                            // show selections highest first
                            echo "<center><b>$SCALE->start</b>&nbsp;&nbsp;&nbsp;";
                            for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
                                $checked = false;
                                if (isset($grades[$i]->grade)) { 
                                    if ($j == $grades[$i]->grade) {
                                        $checked = true;
                                    }
                                }
                                else { // there's no previous grade so check the lowest option
                                    if ($j == 0) {
                                        $checked = true;
                                    }
                                }
                                if ($checked) {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" checked=\"checked\" /> &nbsp;&nbsp;&nbsp;\n";
                                }
                                else {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" /> &nbsp;&nbsp;&nbsp;\n";
                                }
                            }
                            echo "&nbsp;&nbsp;&nbsp;<b>$SCALE->end</b></center>\n";
                            break;
                    case 'selection' :  
                            unset($numbers);
                            for ($j = $SCALE->size; $j >= 0; $j--) {
                                $numbers[$j] = $j;
                            }
                            if (isset($grades[$i]->grade)) {
                                choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
                            }
                            else {
                                choose_from_menu($numbers, "grade[$i]", 0, "");
                            }
                            break;
        
                    echo "  </td>\n";
                    echo "</tr>\n";
                }
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                echo "  <td>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                    }
                    echo "</textarea>\n";
                }
                else {
                    echo text_to_html($grades[$i]->feedback);
                }
                echo "  </td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td colspan=\"2\">&nbsp;</td>\n";
                echo "</tr>\n";
            }
            break;
            
        case 2: // error banded grading
            // now run through the elements
            $error = 0;
            for ($i=0; $i < count($elements) - 1; $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("element","exercise")." $iplus1:</b></p></td>\n";
                echo "  <td>".text_to_html($elements[$i]->description);
                echo "<p align=\"right\"><font size=\"1\">Weight: "
                    .number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font>\n";
                echo "</td></tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("grade"). ":</b></p></td>\n";
                echo "  <td valign=\"top\">\n";
                    
                // get the appropriate scale - yes/no scale (0)
                $SCALE = (object) $EXERCISE_SCALES[0];
                switch ($SCALE->type) {
                    case 'radio' :
                            // show selections highest first
                            echo "<center><b>$SCALE->start</b>&nbsp;&nbsp;&nbsp;";
                            for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
                                $checked = false;
                                if (isset($grades[$i]->grade)) { 
                                    if ($j == $grades[$i]->grade) {
                                        $checked = true;
                                    }
                                }
                                else { // there's no previous grade so check the lowest option
                                    if ($j == 0) {
                                        $checked = true;
                                    }
                                }
                                if ($checked) {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" checked=\"checked\" /> &nbsp;&nbsp;&nbsp;\n";
                                }
                                else {
                                    echo " <input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" /> &nbsp;&nbsp;&nbsp;\n";
                                }
                            }
                            echo "&nbsp;&nbsp;&nbsp;<b>$SCALE->end</b></center>\n";
                            break;
                    case 'selection' :  
                            unset($numbers);
                            for ($j = $SCALE->size; $j >= 0; $j--) {
                                $numbers[$j] = $j;
                            }
                            if (isset($grades[$i]->grade)) {
                                choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
                            }
                            else {
                                choose_from_menu($numbers, "grade[$i]", 0, "");
                            }
                            break;
                }
        
                echo "  </td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                echo "  <td>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[$i]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                    }
                    echo "</textarea>\n";
                    }
                else {
                    if (isset($grades[$i]->feedback)) {
                        echo text_to_html($grades[$i]->feedback);
                    }
                }
                echo "&nbsp;</td>\n";
                echo "</tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td colspan=\"2\">&nbsp;</td>\n";
                echo "</tr>\n";
                if (empty($grades[$i]->grade)) {
                        $error += $EXERCISE_EWEIGHTS[$elements[$i]->weight];
                }
            }
            // print the number of negative elements
            // echo "<tr><td>".get_string("numberofnegativeitems", "exercise")."</td><td>$negativecount</td></tr>\n";
            // echo "<tr valign=\"top\">\n";
            // echo "   <td colspan=\"2\">&nbsp;</td>\n";
            echo "</table></center>\n";
            // now print the grade table
            echo "<p><center><b>".get_string("gradetable","exercise")."</b></center>\n";
            echo "<center><table cellpadding=\"5\" border=\"1\"><tr><td align=\"CENTER\">".
                get_string("numberofnegativeresponses", "exercise");
            echo "</td><td>". get_string("suggestedgrade", "exercise")."</td></tr>\n";
            for ($i=0; $i<=$exercise->nelements; $i++) {
                if ($i == intval($error + 0.5)) {
                    echo "<tr><td align=\"CENTER\"><img src=\"$CFG->pixpath/t/right.gif\" alt=\"\" /> $i</td><td align=\"CENTER\">{$elements[$i]->maxscore}</td></tr>\n";
                }
                else {
                    echo "<tr><td align=\"CENTER\">$i</td><td align=\"CENTER\">{$elements[$i]->maxscore}</td></tr>\n";
                }
            }
            echo "</table></center>\n";
            echo "<p><center><table cellpadding=\"5\" border=\"1\"><tr><td align=\"right\"><b>".
                get_string("optionaladjustment", "exercise")."</b></td><td>\n";
            unset($numbers);
            for ($j = 20; $j >= -20; $j--) {
                $numbers[$j] = $j;
            }
            if (isset($grades[$exercise->nelements]->grade)) {
                choose_from_menu($numbers, "grade[$exercise->nelements]", $grades[$exercise->nelements]->grade, "");
            }
            else {
                choose_from_menu($numbers, "grade[$exercise->nelements]", 0, "");
            }
            echo "</td></tr>\n";
            break;
            
        case 3: // criteria grading
            echo "<tr valign=\"top\">\n";
            echo "  <td>&nbsp;</td>\n";
            echo "  <td><b>". get_string("criterion","exercise")."</b></td>\n";
            echo "  <td><b>".get_string("select")."</b></td>\n";
            echo "  <td><b>".get_string("suggestedgrade", "exercise")."</b></td>\n";
            // find which criteria has been selected (saved in the zero element), if any
            if (isset($grades[0]->grade)) {
                $selection = $grades[0]->grade;
            }
            else {
                $selection = 0;
            }
            // now run through the elements
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";
                echo "  <td>$iplus1</td><td>".text_to_html($elements[$i]->description)."</td>\n";
                if ($selection == $i) {
                    echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[0]\" value=\"$i\" checked=\"checked\" /></td>\n";
                }
                else {
                    echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[0]\" value=\"$i\" /></td>\n";
                }
                echo "<td align=\"center\">{$elements[$i]->maxscore}</td></tr>\n";
            }
            echo "</table></center>\n";
            echo "<p><center><table cellpadding=\"5\" border=\"1\"><tr><td align=\"right\"><b>".
                get_string("optionaladjustment", "exercise")."</b></td><td>\n";
            unset($numbers);
            for ($j = 20; $j >= -20; $j--) {
                $numbers[$j] = $j;
            }
            if (isset($grades[1]->grade)) {
                choose_from_menu($numbers, "grade[1]", $grades[1]->grade, "");
            }
            else {
                choose_from_menu($numbers, "grade[1]", 0, "");
            }
            echo "</td></tr>\n";
            break;
            
        case 4: // rubric grading
            // now run through the elements...
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<tr valign=\"top\">\n";
                echo "<td align=\"right\"><b>".get_string("element", "exercise")." $iplus1:</b></td>\n";
                echo "<td>".text_to_html($elements[$i]->description).
                     "<p align=\"right\"><font size=\"1\">Weight: "
                    .number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font></td></tr>\n";
                echo "<tr valign=\"top\">\n";
                echo "  <td align=\"center\"><b>".get_string("select")."</b></td>\n";
                echo "  <td><b>". get_string("criterion","exercise")."</b></td></tr>\n";
                if (isset($grades[$i])) {
                    $selection = $grades[$i]->grade;
                } else {
                    $selection = 0;
                }
                // ...and the rubrics
                if ($rubricsraw = get_records_select("exercise_rubrics", "exerciseid = $exercise->id AND 
                        elementno = $i", "rubricno ASC")) {
                    unset($rubrics);
                    foreach ($rubricsraw as $rubic) {
                        $rubrics[] = $rubic;   // to renumber index 0,1,2...
                    }
                    for ($j=0; $j<5; $j++) {
                        if (empty($rubrics[$j]->description)) {
                            break; // out of inner for loop
                        }
                        echo "<tr valign=\"top\">\n";
                        if ($selection == $j) {
                            echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" checked=\"checked\" /></td>\n";
                        } else {
                            echo "  <td align=\"center\"><input type=\"RADIO\" name=\"grade[$i]\" value=\"$j\" /></td>\n";
                        }
                        echo "<td>".text_to_html($rubrics[$j]->description)."</td>\n";
                    }
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><p><b>". get_string("feedback").":</b></p></td>\n";
                    echo "  <td>\n";
                    if ($allowchanges) {
                        echo "      <textarea name=\"feedback[]\" rows=\"3\" cols=\"75\" wrap=\"virtual\">\n";
                        if (isset($grades[$i]->feedback)) {
                            echo $grades[$i]->feedback;
                        }
                        echo "</textarea>\n";
                    }
                    else {
                        echo text_to_html($grades[$i]->feedback);
                    }
                    echo "  </td>\n";
                    echo "</tr>\n";
                    echo "<tr valign=\"top\">\n";
                    echo "  <td colspan=\"2\">&nbsp;</td>\n";
                    echo "</tr>\n";
                }
            }
            break;
        } // end of outer switch
    
    // now get the general comment (present in all types)
    echo "<tr valign=\"top\">\n";
    switch ($exercise->gradingstrategy) {
        case 0:
        case 1:
        case 4 : // no grading, accumulative and rubic
            echo "  <td align=\"right\"><p><b>". get_string("generalcomment", "exercise").":</b></p></td>\n";
            break; 
        default : 
            echo "  <td align=\"right\"><p><b>". get_string("reasonforadjustment", "exercise").":</b></p></td>\n";
    }
    echo "  <td>\n";
    if ($allowchanges) {
        echo "      <textarea name=\"generalcomment\" rows=\"5\" cols=\"75\" wrap=\"virtual\">\n";
        if (isset($assessment->generalcomment)) {
            echo $assessment->generalcomment;
        }
        echo "</textarea>\n";
    }
    else {
        if ($assessment) {
            if (isset($assessment->generalcomment)) {
                echo text_to_html($assessment->generalcomment);
            }
        }
        else {
            print_string("yourfeedbackgoeshere", "exercise");
        }
    }
    echo "&nbsp;</td>\n";
    echo "</tr>\n";
    echo "<tr valign=\"top\">\n";
    echo "  <td colspan=\"2\">&nbsp;</td>\n";
    echo "</tr>\n";
    
    // ...and close the table and show two buttons...to resubmit or not to resubmit
    echo "</table>\n";
    echo "<br /><input type=\"button\" value=\"".get_string("studentnotallowed", "exercise", $course->student)."\" 
        onclick=\"getElementById('assessmentform').submit();\" />\n";
    echo "<input type=\"button\" value=\"".get_string("studentallowedtoresubmit", "exercise", $course->student)."\" 
        onclick=\"getElementById('assessmentform').resubmit.value='1';getElementById('assessmentform').submit();\" />\n";
    echo "</center></form>\n";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_teacher_table($course) {
// print how many assessments each teacher has done in each exercise

    if (! $exercises = get_all_instances_in_course("exercise", $course)) {
        notice("There are no exercises", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();
    
    $table->head[] = '';
    $table->align[] = 'left';
    $table->size[] = '*';
    $table->head[] = get_string("total");
    $table->align[] = "center";
    $table->size[] = "*";
        foreach ($exercises as $exercise) {
        $table->head[] = format_string($exercise->name);
        $table->align[] = "center";
        $table->size[] = "*";
        }
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if (!$teachers = get_course_teachers($course->id, "u.firstname, u.lastname")) {
        error("No teachers on this course!");
    }
    for ($j = 0; $j < count($exercises); $j++) {
        $grand[$j] = 0;
    }
    $grandtotal = 0;
    foreach ($teachers as $teacher) {
        unset($n);
        $total = 0;
        $j = 0;
        foreach ($exercises as $exercise) {
            $i = exercise_count_assessments_by_teacher($exercise, $teacher);
            $n[] = $i;
            $total += $i;
            $grand[$j] += $i;
            $j++;
        }
        $grandtotal += $total;
        $table->data[] = array_merge(array(fullname($teacher)), array($total), $n);
    }
    $table->data[] = array_merge(array(get_string("total")), array($grandtotal), $grand);
    print_heading(get_string("teacherassessmenttable", "exercise", $course->teacher));
    print_table($table);
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_time_to_deadline($time) {
    if ($time < 0) {
        $timetext = get_string("afterdeadline", "exercise", format_time($time));
        return " (<font color=\"red\">$timetext</font>)";
    } else {
        $timetext = get_string("beforedeadline", "exercise", format_time($time));
        return " ($timetext)";
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_upload_form($exercise) {

    global $CFG;

    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    echo "<div align=\"center\">";
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"upload.php\">";
    echo " <input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
    require_once($CFG->dirroot.'/lib/uploadlib.php');
    upload_print_form_fragment(1,array('newfile'),null,true,array('title'),$course->maxbytes,$exercise->maxbytes,false);
    echo " <input type=\"submit\" name=\"save\" value=\"".get_string("uploadthisfile")."\" />";
    echo " (".get_string("maximumupload").": ".display_size($exercise->maxbytes).")\n"; 
    echo "</form>";
    echo "</div>";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_user_assessments($exercise, $user) {
    // Returns the number of assessments and a hyperlinked list of grading grades for the assessments made by this user

    if ($assessments = exercise_get_user_assessments($exercise, $user)) {
        $n = count($assessments);
        $str = "$n  (";
        foreach ($assessments as $assessment) {
            if ($assessment->timegraded) {
                $gradingscaled = round($assessment->gradinggrade * $exercise->gradinggrade / 100.0);
                $str .= "<a href=\"assessments.php?action=viewassessment&amp;a=$exercise->id&amp;aid=$assessment->id\">";
                $str .= "$gradingscaled</a> ";
                }
            else {
                $str .= "<a href=\"assessments.php?action=viewassessment&amp;a=$exercise->id&amp;aid=$assessment->id\">";
                $str .= "-</a> ";
                }
            }
        $str .= ")";
        }
    else {
        $str ="0";
        }
    return $str;
    }


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_test_for_resubmission($exercise, $user) {
    // see if any of the user's submissions have the resubmit flag set
    $result = false;
    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        foreach ($submissions as $submission) {
            if ($submission->resubmit) {
                $result =true;
                break;
                }
            }
        }
    return $result;
    }
    

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_test_user_assessments($exercise, $user) {
    // see if user has assessed one of  teacher's exercises/submissions...
    global $CFG;
    
    $result = false;
    $timenow =time();
    if ($submissions = exercise_get_teacher_submissions($exercise)) {
        foreach ($submissions as $submission) {
            if ($assessment = exercise_get_submission_assessment($submission, $user)) {
                // ...the date stamp on the assessment should be in the past 
                if ($assessment->timecreated < $timenow) {
                    $result = true;
                    break;
                    }
                }
            }
        }
    return $result;
    }

?>
