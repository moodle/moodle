<?PHP  // $Id$

/// Library of extra functions and module workshop 

//////////////////////////////////////////////////////////////////////////////////////

/*** Functions for the workshop module ******

workshop_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", 
    $nothingvalue="0", $return=false) {

workshop_compare_assessments($workshop, $assessment1, $assessment2) { ---> in lib.php
workshop_count_all_submissions_for_assessment($workshop, $user) {
workshop_count_assessments($submission) {
workshop_count_comments($assessment) {
workshop_count_peer_assessments($workshop, $user) {
workshop_count_self_assessments($workshop, $user) {
workshop_count_student_submissions($workshop) {
workshop_count_student_submissions_for_assessment($workshop, $user) {
workshop_count_teacher_assessments($workshop, $user) {
workshop_count_teacher_submissions($workshop) {
workshop_count_teacher_submissions_for_assessment($workshop, $user) {
workshop_count_ungraded_assessments($workshop) { --->in lib.php
workshop_count_ungraded_assessments_student($workshop) {
workshop_count_ungraded_assessments_teacher($workshop) {
workshop_count_user_assessments($worshop, $user, $type = "all") { $type is all, student or teacher
workshop_count_user_submissions($workshop, $user) {

workshop_delete_submitted_files($workshop, $submission) {
workshop_delete_user_files($workshop, $user, $exception) {

workshop_file_area($workshop, $submission) { ---> in lib.php
workshop_file_area_name($workshop, $submission) { ---> in lib.php

workshop_get_assessments($submission, $all = '') { ---> in lib.php
workshop_get_comments($assessment) {
workshop_get_participants($workshopid) {
workshop_get_student_assessments($workshop, $user) {
workshop_get_student_submission($workshop, $user) { ---> in lib.php
workshop_get_student_submission_assessments($workshop) {
workshop_get_student_submissions($workshop) { ---> in lib.php
workshop_get_submission_assessment($submission, $user) {
workshop_get_teacher_submission_assessments($workshop) {
workshop_get_teacher_submissions($workshop) {
workshop_get_ungraded_assessments($workshop) {
workshop_get_unmailed_assessments($cutofftime) {
workshop_get_unmailed_marked_assessments($cutofftime) {
workshop_get_user_assessments($workshop, $user) { ---> in lib.php
workshop_get_user_submissions($workshop, $user) { ---> in lib.php
workshop_get_users_done($workshop) {

workshop_grade_assessments($workshop) { ---> in lib.php

workshop_list_all_submissions($workshop) {
workshop_list_all_ungraded_assessments($workshop) {
workshop_list_assessed_submissions($workshop, $user) {
workshop_list_peer_assessments($workshop, $user) {
workshop_list_student_submissions($workshop, $user) {
workshop_list_submissions_for_admin($workshop, $order) {
workshop_list_teacher_assessments($workshop, $user) {
workshop_list_teacher_submissions($workshop) {
workshop_list_unassessed_student_submissions($workshop, $user) {
workshop_list_unassessed_teacher_submissions($workshop, $user) {
workshop_list_ungraded_assessments($workshop, $stype) {
workshop_list_user_submissions($workshop, $user) {


workshop_print_assessment($workshop, $assessment, $allowchanges, $showcommentlinks, $returnto)
workshop_print_assessments_by_user_for_admin($workshop, $user) {
workshop_print_assessments_for_admin($workshop, $submission) {
workshop_print_assignment_info($cm, $workshop) {
workshop_print_difference($time) {
workshop_print_feedback($course, $submission) {
workshop_print_league_table($workshop) {
workshop_print_submission_assessments($workshop, $submission, $type) {
workshop_print_submission_title($workshop, $user) {
workshop_print_tabbed_table($table) {
workshop_print_time_to_deadline($time) {
workshop_print_upload_form($workshop) {
workshop_print_user_assessments($workshop, $user) {

workshop_submission_grade($submission) { ---> in lib.php
    
workshop_test_user_assessments($workshop, $user) {
***************************************/


///////////////////////////////////////////////////////////////////////////////
function workshop_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", 
        $nothingvalue="0", $return=false) {
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

    $output = "<SELECT NAME=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <OPTION VALUE=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " SELECTED";
        }
        $output .= ">$nothing</OPTION>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <OPTION VALUE=\"$value\"";
            if ($value == $selected) {
                $output .= " SELECTED";
            }
            // stop zero label being replaced by array index value
            // if ($label) {
            //    $output .= ">$label</OPTION>\n";
            // } else {
            //     $output .= ">$value</OPTION>\n";
            //  }
            $output .= ">$label</OPTION>\n";
            
        }
    }
    $output .= "</SELECT>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}   


///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_copy_assessment($assessment, $submission, $withfeedback = false) {
    // adds a copy of the given assessment for the submission specified to the workshop_assessments table. 
    // The grades and optionally the comments are added to the workshop_grades table. Returns the new
    // assessment object. The owner of the assessment is not changed.
    
    $yearfromnow = time() + 365 * 86400;
    $newassessment->workshopid = $assessment->workshopid;
    $newassessment->submissionid = $submission->id;
    $newassessment->userid = $assessment->userid;
    $newassessment->timecreated = $yearfromnow;
    $newassessment->grade = $assessment->grade;
    if ($withfeedback) {
        $newassessment->generalcomment = addslashes($assessment->generalcomment);
        $newassessment->teachercomment = addslashes($assessment->teachercomment);
    }
    if (!$newassessment->id = insert_record("workshop_assessments", $newassessment)) {
        error("Copy Assessment: Could not insert workshop assessment!");
    }
    
    if ($grades = get_records("workshop_grades", "assessmentid", $assessment->id)) {
        foreach ($grades as $grade) {
            if (!$withfeedback) {
                $grade->feedback = '';
            }
            else {
                $grade->feedback = addslashes($grade->feedback);
            }
            $grade->assessmentid = $newassessment->id;
            if (!$grade->id = insert_record("workshop_grades", $grade)) {
                error("Copy Assessment: Could not insert workshop grade!");
            }
        }
    }
    if ($withfeedback) {
        // remove the slashes from comments as the new assessment record might be used, 
        // currently this function is only called in upload which does not!
        $newassessment->generalcomment = stripslashes($assessment->generalcomment);
        $newassessment->teachercomment = stripslashes($assessment->teachercomment);
    }
    return $newassessment;
}



//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_all_submissions_for_assessment($workshop, $user) {
    // looks at all submissions and deducts the number which has been assessed by this user
    $n = 0;
    if ($submissions = get_records_select("workshop_submissions", "workshopid = $workshop->id AND 
                timecreated > 0")) {
        $n =count($submissions);
        foreach ($submissions as $submission) {
            $n -= count_records("workshop_assessments", "submissionid", $submission->id, "userid", $user->id);
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_assessments($submission) {
    // Return the (real) assessments for this submission, 
    $timenow = time();
   return count_records_select("workshop_assessments", 
           "submissionid = $submission->id AND timecreated < $timenow");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_comments($assessment) {
    // Return the number of comments for this assessment provided they are newer than the assessment, 
   return count_records_select("workshop_comments", "(assessmentid = $assessment->id) AND 
        timecreated > $assessment->timecreated");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_peer_assessments($workshop, $user) {
    // returns the number of assessments made by students on user's submissions
    
    $n = 0;
    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        foreach ($submissions as $submission) {
            if ($assessments = workshop_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    // ignore teacher assessments
                    if (!isteacher($workshop->course, $assessment->userid)) {
                        $n++;
                        }
                    }
                }
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_self_assessments($workshop, $user) {
    // returns the number of assessments made by user on their own submissions
    
    $n = 0;
    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        foreach ($submissions as $submission) {
            if ($assessment = get_record_select("workshop_assessments", "userid = $user->id AND 
                    submissionid = $submission->id")) {
                $n++;
                }
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_student_submissions($workshop) {
    global $CFG;

    // make sure it works on the site course
    $select = "s.course = '$workshop->course' AND";
    if ($workshop->course == SITEID) {
        $select = '';
    }

    return count_records_sql("SELECT count(*) FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_students u
                            WHERE $select s.userid = u.userid
                              AND s.workshopid = $workshop->id
                              AND timecreated > 0");
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_student_submissions_for_assessment($workshop, $user) {
    global $CFG;

    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
    
    $timenow = time();
    if (groupmode($course, $cm) == SEPARATEGROUPS) {
        $groupid = get_current_group($course->id);
    } else {
        $groupid = 0;
    }
    
    $n = 0;
    if ($submissions = workshop_get_student_submissions($workshop)) {
        foreach ($submissions as $submission) {
            // check group membership, if necessary
            if ($groupid) {
                // check user's group
                if (!ismember($groupid, $submission->userid)) {
                    continue; // skip this user
                }
            }
            // teacher assessed this submission
            if (! count_records_select("workshop_assessments", "submissionid = $submission->id AND 
                    userid = $user->id AND timecreated < $timenow - $CFG->maxeditingtime")) {
                $n++;
            }
        }
    }
    return $n;
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_teacher_assessments($workshop, $user) {
    // returns the number of assessments made by teachers on user's submissions
    
    $n = 0;
    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        foreach ($submissions as $submission) {
            if ($assessments = workshop_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    // count only teacher assessments
                    if (isteacher($workshop->course, $assessment->userid)) {
                        $n++;
                        }
                    }
                }
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_teacher_submissions($workshop) {
    global $CFG;
    
     return count_records_sql("SELECT count(*) FROM {$CFG->prefix}workshop_submissions s, 
                     {$CFG->prefix}user_teachers u
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id");
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_teacher_submissions_for_assessment($workshop, $user) {

    $n = 0;
    if ($submissions = workshop_get_teacher_submissions($workshop)) {
        $n =count($submissions);
        foreach ($submissions as $submission) {
            $n -= count_records("workshop_assessments", "submissionid", $submission->id, "userid", $user->id);
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_ungraded_assessments_student($workshop) {
    // function returns the number of ungraded assessments by students of STUDENT submissions
    $n = 0;
    if ($submissions = workshop_get_student_submissions($workshop)) {
        foreach ($submissions as $submission) {
            if ($assessments = workshop_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    if ($assessment->timegraded == 0) {
                        // ignore teacher assessments
                        if (!isteacher($workshop->course, $assessment->userid)) {
                            $n++;
                            }
                        }
                    }
                }
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_ungraded_assessments_teacher($workshop) {
    // function returns the number of ungraded assessments by students of TEACHER submissions
    global $CFG;

    $timenow = time();
    $n = 0;
    if ($submissions = workshop_get_teacher_submissions($workshop)) {
        foreach ($submissions as $submission) {
            if ($assessments = workshop_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    if ($assessment->timegraded == 0) {
                        // ignore teacher assessments
                        if (!isteacher($workshop->course, $assessment->userid)) {
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


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_user_assessments($workshop, $user, $stype = "all") {
    // returns the number of assessments allocated/made by a user, all of them, or just those 
    // for the student or teacher submissions. The student's self assessments are included in the count.
    // The maxeditingtime is NOT taken into account here also, allocated assessments which have not yet
    // been done are counted as well
    
    $n = 0;
    if ($assessments = workshop_get_user_assessments($workshop, $user)) {
         foreach ($assessments as $assessment) {
            switch ($stype) {
                case "all" :
                    $n++;
                    break;
                case "student" :
                     $submission = get_record("workshop_submissions", "id", $assessment->submissionid);
                    if (isstudent($workshop->course, $submission->userid)) {
                        $n++;
                        }
                    break;
                case "teacher" :
                     $submission = get_record("workshop_submissions", "id", $assessment->submissionid);
                    if (isteacher($workshop->course, $submission->userid)) {
                        $n++;
                        }
                    break;
                }
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_user_assessments_done($workshop, $user) {
    // returns the number of assessments actually done by a user
    // the student's self assessments are included in the count
    // the maxeditingtime is NOT taken into account here 
    
    $n = 0;
    $timenow = time();
    if ($assessments = workshop_get_user_assessments($workshop, $user)) {
         foreach ($assessments as $assessment) {
            if ($assessment->timecreated < $timenow) {
                $n++;
                }
            }
        }
    return $n;
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_user_submissions($workshop, $user) {
    // returns the number of (real) submissions make by this user
    return count_records_select("workshop_submissions", "workshopid = $workshop->id AND 
        userid = $user->id AND timecreated > 0");
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_delete_submitted_files($workshop, $submission) {
// Deletes the files in the workshop area for this submission

    if ($basedir = workshop_file_area($workshop, $submission)) {
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


//////////////////////////////////////////////////////////////////////////////////////
function workshop_delete_user_files($workshop, $user, $exception) {
// Deletes all the user files in the workshop area for a user
// EXCEPT for any file named $exception

    if (!$submissions = workshop_get_submissions($workshop, $user)) {
        notify("No submissions!");
        return;
        }
    foreach ($submissions as $submission) {
        if ($basedir = workshop_file_area($workshop, $submission)) {
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


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_comments($assessment) {
    // Return all comments for this assessment provided they are newer than the assessment, 
    // and ordered oldest first, newest last
   return get_records_select("workshop_comments", "(assessmentid = $assessment->id) AND 
        timecreated > $assessment->timecreated",
        "timecreated DESC");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_student_assessments($workshop, $user) {
// Return all assessments on the student submissions by a user, order by youngest first, oldest last
    global $CFG;

    // make sure it works on the site course
    $select = "u.course = '$workshop->course' AND";
    if ($workshop->course == SITEID) {
        $select = '';
    }

    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_students u,
                            {$CFG->prefix}workshop_assessments a
                            WHERE $select s.userid = u.userid
                              AND s.workshopid = $workshop->id
                              AND a.submissionid = s.id
                              AND a.userid = $user->id
                              ORDER BY a.timecreated DESC");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_student_submission_assessments($workshop) {
// Return all assessments on the student submissions, order by youngest first, oldest last
    global $CFG;

    // make sure it works on the site course
    $select = "u.course = '$workshop->course' AND";
    if ($workshop->course == SITEID) {
        $select = '';
    }

    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_students u, {$CFG->prefix}workshop_assessments a
                            WHERE $select s.userid = u.userid
                              AND s.workshopid = $workshop->id
                              AND a.submissionid = s.id
                              ORDER BY a.timecreated DESC");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_submission_assessment($submission, $user) {
    // Return the user's assessment for this submission (cold or warm, not hot)
    
    $timenow = time();
    return get_record_select("workshop_assessments", "submissionid = $submission->id AND 
            userid = $user->id AND timecreated < $timenow");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_teacher_submission_assessments($workshop) {
// Return all assessments on the teacher submissions, order by youngest first, oldest last
    global $CFG;
    
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_teachers u, {$CFG->prefix}workshop_assessments a
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
                              AND a.submissionid = s.id
                              ORDER BY a.timecreated DESC");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_teacher_submissions($workshop) {
// Return all  teacher submissions, ordered by title
    global $CFG;
    
    return get_records_sql("SELECT s.* FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_teachers u
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id 
                              ORDER BY s.title");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_ungraded_assessments($workshop) {
    global $CFG;
    // Return all assessments which have not been graded or just graded
    $cutofftime =time() - $CFG->maxeditingtime;
    return get_records_select("workshop_assessments", "workshopid = $workshop->id AND (timegraded = 0 OR 
                timegraded > $cutofftime)", "timecreated"); 
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_ungraded_assessments_student($workshop) {
    global $CFG;
    // Return all assessments which have not been graded or just graded of student's submissions

    // make sure it works on the site course
    $select = "u.course = '$workshop->course' AND";
    if ($workshop->course == SITEID) {
        $select = '';
    }

    $cutofftime = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_students u, {$CFG->prefix}workshop_assessments a
                            WHERE $select s.userid = u.userid
                              AND s.workshopid = $workshop->id
                              AND a.submissionid = s.id
                              AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
                              AND a.timecreated < $cutofftime
                              ORDER BY a.timecreated ASC"); 
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_ungraded_assessments_teacher($workshop) {
    global $CFG;
    // Return all assessments which have not been graded or just graded of teacher's submissions
    
    $cutofftime =time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_teachers u, {$CFG->prefix}workshop_assessments a
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
                              AND a.submissionid = s.id
                              AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
                              AND a.timecreated < $cutofftime
                              ORDER BY a.timecreated ASC"); 
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_user_assessments_done($workshop, $user) {
// Return all the  user's assessments, oldest first, newest last (warm and cold ones only)
// ignores maxeditingtime
    $timenow = time();
    return get_records_select("workshop_assessments", "workshopid = $workshop->id AND userid = $user->id
                AND timecreated < $timenow", 
                "timecreated ASC");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_users_done($workshop) {
    global $CFG;

    // make sure it works on the site course
    $select = "s.course = '$workshop->course' AND";
    if ($workshop->course == SITEID) {
        $select = '';
    }

    return get_records_sql("SELECT u.* 
                    FROM {$CFG->prefix}user u, {$CFG->prefix}user_students s, 
                         {$CFG->prefix}workshop_submissions a
                    WHERE $select s.user = u.id
                    AND u.id = a.user AND a.workshop = '$workshop->id'
                    ORDER BY a.timemodified DESC");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_all_submissions($workshop, $user) {
    // list the teacher sublmissions first
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), 
                        get_string("comment", "workshop"));
    $table->align = array ("left", "left", "left");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = workshop_get_teacher_submissions($workshop)) {
        foreach ($submissions as $submission) {
            if ($submission->userid == $user->id) {
                $comment = get_string("ownwork", "workshop")."; ";
                }
            else {
                $comment = "";
                }
            // has user already assessed this submission
            if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
                    AND userid = $user->id")) {
                $curtime = time();
                if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
                    $action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">"
                        .get_string("view", "workshop")."</A>";
                    // has teacher graded user's assessment?
                    if ($assessment->timegraded) {
                        if (($curtime - $assessment->timegraded) > $CFG->maxeditingtime) {
                            $comment .= get_string("gradedbyteacher", "workshop", $course->teacher);
                            }
                        }
                    }
                else { // there's still time left to edit...
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("edit", "workshop")."</A>";
                    }
                }
            else { // user has not graded this submission
                $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                    get_string("assess", "workshop")."</A>";
                }
            $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, 
                                $comment);
            }
        print_table($table);
        }

    echo "<CENTER><P><B>".get_string("studentsubmissions", "workshop", $course->student).
        "</B></CENTER><BR>\n";
    unset($table);
    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), 
                        get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = workshop_get_student_submissions($workshop)) {
        foreach ($submissions as $submission) {
            if ($submission->userid == $user->id) {
                $comment = get_string("ownwork", "workshop")."; ";
                }
            else {
                $comment = "";
                }
            // has user already assessed this submission
            if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
                    AND userid = $user->id")) {
                $curtime = time();
                if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
                    $action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">".
                        get_string("view", "workshop")."</A>";
                    // has teacher graded on user's assessment?
                    if ($assessment->timegraded) {
                        if (($curtime - $assessment->timegraded) > $CFG->maxeditingtime) {
                            $comment .= get_string("gradedbyteacher", "workshop", $course->teacher)."; ";
                            }
                        }
                    $otherassessments = workshop_get_assessments($submission);
                    if (count($otherassessments) > 1) {
                        $comment .= "<A HREF=\"assessments.php?action=viewallassessments&id=$cm->id&sid=$submission->id\">".
                        get_string("viewotherassessments", "workshop")."</A>";
                        }
                    }
                else { // there's still time left to edit...
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("edit", "workshop")."</A>";
                    }
                }
            else { // user has not assessed this submission
                $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                    get_string("assess", "workshop")."</A>";
                }
            $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, 
                                $comment);
            }
        print_table($table);
        }
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_all_ungraded_assessments($workshop) {
    // lists all the assessments for comment by teacher
    global $CFG;

    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    
    $table->head = array (get_string("title", "workshop"), get_string("timeassessed", "workshop"), get_string("action", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;
    $timenow = time();
    
    if ($assessments = workshop_get_ungraded_assessments($workshop)) {
        foreach ($assessments as $assessment) {
            if (!isteacher($workshop->course, $assessment->userid)) {
                if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
                    $action = "<A HREF=\"assessments.php?action=gradeassessment&id=$cm->id&aid=$assessment->id\">".
                        get_string("edit", "workshop")."</A>";
                    }
                else {
                    $action = "<A HREF=\"assessments.php?action=gradeassessment&id=$cm->id&aid=$assessment->id\">".
                        get_string("gradeassessment", "workshop")."</A>";
                    }
                $submission = get_record("workshop_submissions", "id", $assessment->submissionid);
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), 
                    userdate($assessment->timecreated), $action);
                }
            }
        if (isset($table->data)) {
            print_table($table);
            }
        }
    }
    

//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_assessed_submissions($workshop, $user) {
    // list the submissions that have been assessed by this user and are COLD
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
    $table->head = array (get_string("title","workshop"), get_string("action","workshop"), 
                    get_string("comment","workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($assessments = workshop_get_student_assessments($workshop, $user)) {
        $timenow = time();
        foreach ($assessments as $assessment) {
            $comment = "";
            $submission = get_record("workshop_submissions", "id", $assessment->submissionid);
            // the assessment may be in three states: 
            // 1. "hot", just created but not completed (timecreated is in the future)
            // 2. "warm" just created and still capable of being edited, and 
            // 3. "cold" after the editing time
                
            if ($assessment->timecreated < ($timenow - $CFG->maxeditingtime)) { // it's cold
                if ($workshop->agreeassessments) {
                    if (!$assessment->timeagreed) {
                        $action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id&".
                            "allowcomments=$workshop->agreeassessments\">".
                            get_string("view", "workshop")."</A>";
                        $action .= " | <A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                            get_string("reassess", "workshop")."</A>";
                    } else {
                        $action = "";
                    }
                } else {
                    if ($assessment->timegraded) {
                        $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                            get_string("reassess", "workshop")."</A>";
                    } else {
                        $action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">".
                            get_string("view", "workshop")."</A>";
                    }
                }          
                if ($assessment->timecreated < $timenow) { // only show the date if it's in the past (future dates cause confusion
                    $comment = get_string("assessedon", "workshop", userdate($assessment->timecreated));
                }
                else {
                    $comment = '';
                }
                if ($submission->userid == $user->id) { // self assessment?
                    $comment .= "; ".get_string("ownwork", "workshop"); // just in case they don't know!
                }
                // has assessment been graded?
                if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
                    $comment .= "; ".get_string("thegradeforthisassessmentis", "workshop", 
                            number_format($assessment->gradinggrade * $workshop->gradinggrade / 100, 0)).
                            " / $workshop->gradinggrade";
                }
                // if peer agrrements show whether agreement has been reached
                if ($workshop->agreeassessments) {
                    if ($assessment->timeagreed) {
                        $comment .= "; ".get_string("assessmentwasagreedon", "workshop", 
                                userdate($assessment->timeagreed));
                    }
                    else {
                        $comment .= "; ".get_string("assessmentnotyetagreed", "workshop");
                    }
                }
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, 
                                    $comment);
            }
        }
    }
    if (isset($table->data)) {
        print_table($table);
    }
    else {
        echo "<CENTER>".get_string("noassessmentsdone", "workshop")."</CENTER>\n";
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_peer_assessments($workshop, $user) {
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), 
                    get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // get user's submissions
    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        foreach ($submissions as $submission) {
            // get the assessments
            if ($assessments = workshop_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    // assessments by students only and exclude any self assessments
                    if (isstudent($workshop->course, $assessment->userid) and 
                            ($assessment->userid != $user->id)) { 
                        $timenow = time();
                        if (($timenow - $assessment->timecreated) > $CFG->maxeditingtime) {
                            $action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id&".
                                "allowcomments=$workshop->agreeassessments\">".
                                get_string("view", "workshop")."</A>";
                            $comment = get_string("assessedon", "workshop", userdate($assessment->timecreated));
                            $grade = number_format($assessment->grade * $workshop->grade / 100, 1);
                            $comment .= "; ".get_string("gradeforsubmission", "workshop").
                                ": $grade / $workshop->grade"; 
                            if ($assessment->timegraded) {
                                if (!$assessment->gradinggrade) {
                                    // it's a bad assessment
                                    $comment .= "; ".get_string("thisisadroppedassessment", "workshop");
                                }
                            }
                            if (isteacher($workshop->course, $assessment->userid) and $workshop->teacherweight) {
                                $comment .= "; ".get_string("thisisadroppedassessment", "workshop");
                            }
                            // if peer agreements show whether agreement has been reached
                            if ($workshop->agreeassessments) {
                                if ($assessment->timeagreed) {
                                    $comment .= "; ".get_string("assessmentwasagreedon", "workshop", 
                                                        userdate($assessment->timeagreed));
                                    }
                                else {
                                    $comment .= "; ".get_string("assessmentnotyetagreed", "workshop");
                                    }
                                }
                            $table->data[] = array(workshop_print_submission_title($workshop, $submission), 
                                                $action, $comment);
                            }
                        }
                    }
                }
            }
        }
    if (isset($table->data)) {
        print_table($table);
        }
    else {
        echo "<CENTER>".get_string("noassessmentsdone", "workshop")."</CENTER>\n";
        }
    }



//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_self_assessments($workshop, $user) {
    // list  user's submissions for the user to assess
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), 
                       get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // get the user's submissions 
    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        foreach ($submissions as $submission) {
            $comment = "";
            if (!$assessment = get_record_select("workshop_assessments", "submissionid = $submission->id AND
                    userid = $user->id")) {
                if ($submission->userid == $user->id) { // this will always be true
                    $comment = get_string("ownwork", "workshop"); // just in case they don't know!
                    }
                $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                    get_string("assess", "workshop")."</A>";
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
                }
            }
        }
    if (isset($table->data)) {
        echo "<P><CENTER><B>".get_string("pleaseassessyoursubmissions", "workshop", $course->student).
            "</B></CENTER><BR>\n";
        print_table($table);
        }
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_student_submissions($workshop, $user) {
    // list available submissions for this user to assess, submissions with the least number 
    // of assessments are show first
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }

    $timenow = time();

    // set student's group if workshop is in SEPARATEGROUPS mode
    if (groupmode($course, $cm) == SEPARATEGROUPS) {
        $groupid = get_current_group($course->id);
    } else {
        $groupid = 0;
    }
    
    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // get the number of assessments this user has done on student submission, deduct self assessments
    $nassessed = workshop_count_user_assessments($workshop, $user, "student") - 
        workshop_count_self_assessments($workshop, $user);
    // user hasn't been allocated enough, try and get some more
    if ($nassessed < $workshop->nsassessments) {
        // count the number of assessments for each student submission
        if ($submissions = workshop_get_student_submissions($workshop)) {
            // srand ((float)microtime()*1000000); // now done automatically in PHP 4.2.0->
            foreach ($submissions as $submission) {
                // check group membership, if necessary
                if ($groupid) {
                    // check user's group
                    if (!ismember($groupid, $submission->userid)) {
                        continue; // skip this submission
                  }
                }
                // process only cold submissions
                if (($submission->timecreated + $CFG->maxeditingtime) > $timenow) {
                    continue;
                }
                $n = count_records("workshop_assessments", "submissionid", $submission->id);
                // ...OK to have zero, we add a small random number to randomise things
                $nassessments[$submission->id] = $n + rand(0, 98) / 100;
                }
                
            if (isset($nassessments)) { // make sure we end up with something to play with :-)
                // put the submissions with the lowest number of assessments first
                asort($nassessments);
                reset($nassessments);
                $nsassessments = $workshop->nsassessments;
                foreach ($nassessments as $submissionid =>$n) {
                    // only use those submissions which fall below the allocation threshold
                    if ($n < ($workshop->nsassessments + $workshop->overallocation)) {
                        $comment = "";
                        $submission = get_record("workshop_submissions", "id", $submissionid);
                        // skip submission if it belongs to this user
                        if ($submission->userid != $user->id) {
                            // add a "hot" assessment record if user has NOT already assessed this submission
                            if (!get_record("workshop_assessments", "submissionid", $submission->id, "userid",
                                        $user->id)) {
                                $yearfromnow = time() + 365 * 86400;
                                // ...create one and set timecreated way in the future, this is reset when record is updated
                                unset($assessment); // clear previous version object (if any)
                                $assessment->workshopid = $workshop->id;
                                $assessment->submissionid = $submission->id;
                                $assessment->userid = $user->id;
                                $assessment->grade = -1; // set impossible grade
                                $assessment->timecreated = $yearfromnow;
                                if (!$assessment->id = insert_record("workshop_assessments", $assessment)) {
                                    error("List Student submissions: Could not insert workshop assessment!");
                                }
                                $nassessed++;
                                // is user up to quota?
                                if ($nassessed == $nsassessments) {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    // now list the student submissions this user has been allocated, list only the hot and warm ones, 
    // the cold ones are listed in the "your assessments list" (_list_assessed submissions)
    if ($assessments = workshop_get_user_assessments($workshop, $user)) {
        $timenow = time();
        foreach ($assessments as $assessment) {
            if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
                error ("workshop_list_student_submissions: unable to get submission");
                }
            // submission from a student?
            if (isstudent($workshop->course, $submission->userid)) {
                $comment = '';
                // user assessment has three states: record created but not assessed (date created in the future) [hot]; 
                // just assessed but still editable [warm]; and "static" (may or may not have been graded by teacher, that
                // is shown in the comment) [cold] 
                if ($assessment->timecreated > $timenow) { // user needs to assess this submission
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("assess", "workshop")."</A>";
                    $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
                    }
                elseif ($assessment->timecreated > ($timenow - $CFG->maxeditingtime)) { // there's still time left to edit...
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("edit", "workshop")."</A>";
                    $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
                    }
                }
            }
        }
    
    if (isset($table->data)) {
        echo "<P><CENTER><B>".get_string("pleaseassessthesestudentsubmissions", "workshop", $course->student).
            "</B></CENTER><BR>\n";
        print_table($table);
        }
    else {
        echo "<P><CENTER><B>".get_string("nosubmissionsavailableforassessment", "workshop")."</B></CENTER><BR>\n";
        }
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_submissions_for_admin($workshop, $order) {
    // list the teacher sublmissions first
    global $CFG, $USER;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    if (groupmode($course, $cm) == SEPARATEGROUPS) {
        $groupid = get_current_group($course->id);
    } else {
        $groupid = 0;
    }
    
    workshop_print_assignment_info($workshop);

    if (isteacheredit($course->id)) {
        // list any teacher submissions
        $table->head = array (get_string("title", "workshop"), get_string("submittedby", "workshop"), 
                get_string("action", "workshop"));
        $table->align = array ("left", "left", "left");
        $table->size = array ("*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        if ($submissions = workshop_get_teacher_submissions($workshop)) {
            foreach ($submissions as $submission) {
                $action = "<a href=\"submissions.php?action=adminamendtitle&id=$cm->id&sid=$submission->id\">".
                    get_string("amendtitle", "workshop")."</a>";
                // has user already assessed this submission
                if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
                            AND userid = $USER->id")) {
                    $curtime = time();
                if ($assessment->timecreated > $curtime) { // it's a "hanging" assessment 
                    $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("assess", "workshop")."</a>";
                }
                elseif (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
                    $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">"
                        .get_string("reassess", "workshop")."</a>";
                }
                else { // there's still time left to edit...
                    $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("edit", "workshop")."</a>";
                }
            }
                else { // user has not graded this submission
                    $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("assess", "workshop")."</a>";
                }
                if ($assessments = workshop_get_assessments($submission)) {
                    $action .= " | <a href=\"assessments.php?action=adminlist&id=$cm->id&sid=$submission->id\">".
                        get_string("listassessments", "workshop")."</a>";
                }
                $action .= " | <a href=\"submissions.php?action=adminconfirmdelete&id=$cm->id&sid=$submission->id\">".
                    get_string("delete", "workshop")."</a>";
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), $course->teacher, $action);
            }
            print_heading(get_string("studentsubmissions", "workshop", $course->teacher), "center");
            print_table($table);
        }
    }

    // list student assessments
    // Get all the students...
    if ($users = get_course_students($course->id, "u.lastname, u.firstname")) {
        $timenow = time();
        unset($table);
        $table->head = array(get_string("name"), get_string("title", "workshop"), get_string("action", "workshop"));
        $table->align = array ("left", "left", "left");
        $table->size = array ("*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;
        foreach ($users as $user) {
            // check group membership, if necessary
            if ($groupid) {
                // check user's group
                if (!ismember($groupid, $user->id)) {
                    continue; // skip this user
                }
            }
            // list the assessments which have been done (exclude the hot ones)
            if ($assessments = workshop_get_user_assessments_done($workshop, $user)) {
                $title ='';
                foreach ($assessments as $assessment) {
                    if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
                        error("Workshop_list_submissions_for_admin: Submission record not found!");
                    }
                    $title .= $submission->title;
                    if ($assessment->timegraded) {
                        if ($assessment->gradinggrade) {
                            // a good assessment
                            $title .= " {".number_format($assessment->grade * $workshop->grade / 100, 0)." (".
                                number_format($assessment->gradinggrade * $workshop->gradinggrade / 100, 0).")} ";
                        } else { 
                            // a poor assessment
                            $title .= " <".number_format($assessment->grade * $workshop->grade / 100, 0)." (".
                                number_format($assessment->gradinggrade * $workshop->gradinggrade / 100, 0).")> ";
                        }
                    } else {
                        // not yet graded
                        $title .= " {".number_format($assessment->grade * $workshop->grade / 100, 0)." (-)} ";
                    }
                    if ($realassessments = workshop_count_user_assessments_done($workshop, $user)) {
                        $action = "<a href=\"assessments.php?action=adminlistbystudent&id=$cm->id&userid=$user->id\">".
                            get_string("liststudentsassessments", "workshop")." ($realassessments)</a>";
                    } else {
                        $action ="";
                    }
                }
                $table->data[] = array(fullname($user), $title, $action);
            }
        }
        if (isset($table->data)) {
            print_heading(get_string("studentassessments", "workshop", $course->student));
            print_table($table);
            workshop_print_key($workshop);
            // grading grade analysis
            unset($table);
            $table->head = array (get_string("count", "workshop"), get_string("mean", "workshop"),
                get_string("standarddeviation", "workshop"), get_string("maximum", "workshop"), 
                get_string("minimum", "workshop"));
            $table->align = array ("center", "center", "center", "center", "center");
            $table->size = array ("*", "*", "*", "*", "*");
            $table->cellpadding = 2;
            $table->cellspacing = 0;
            if ($groupid) {
                $stats = get_record_sql("SELECT COUNT(*) as count, AVG(gradinggrade) AS mean, 
                        STDDEV(gradinggrade) AS stddev, MIN(gradinggrade) AS min, MAX(gradinggrade) AS max 
                        FROM {$CFG->prefix}groups_members g, {$CFG->prefix}workshop_assessments a 
                        WHERE g.groupid = $groupid AND a.userid = g.userid AND a.timegraded > 0 
                        AND a.workshopid = $workshop->id");
            } else { // no group/all participants
                $stats = get_record_sql("SELECT COUNT(*) as count, AVG(gradinggrade) AS mean, 
                        STDDEV(gradinggrade) AS stddev, MIN(gradinggrade) AS min, MAX(gradinggrade) AS max 
                        FROM {$CFG->prefix}workshop_assessments a 
                        WHERE a.timegraded > 0 AND a.workshopid = $workshop->id");
            }   
            $table->data[] = array($stats->count, number_format($stats->mean * $workshop->gradinggrade / 100, 1), 
                    number_format($stats->stddev * $workshop->gradinggrade /100, 1), 
                    number_format($stats->max * $workshop->gradinggrade / 100, 1), 
                    number_format($stats->min* $workshop->gradinggrade / 100, 1));
            print_heading(get_string("gradinggrade", "workshop")." ".get_string("analysis", "workshop"));
            print_table($table);
        }
    }

    // now the sudent submissions
    unset($table);
    switch ($order) {
        case "title" :
            $table->head = array("<a href=\"submissions.php?action=adminlist&id=$cm->id&order=name\">".
                 get_string("submittedby", "workshop")."</a>", get_string("title", "workshop"), get_string("action", "workshop"));
            break;
        case "name" :
            $table->head = array (get_string("submittedby", "workshop"), 
                "<a href=\"submissions.php?action=adminlist&id=$cm->id&order=title\">".
                get_string("title", "workshop")."</a>", get_string("action", "workshop"));
            break;
    }
    $table->align = array ("left", "left", "left");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = workshop_get_student_submissions($workshop, $order)) {
        foreach ($submissions as $submission) {
            if (!$user = get_record("user", "id", $submission->userid)) {
                error("workshop_list_submissions_for_admin: failure to get user record");
            }
            // check group membership, if necessary
            if ($groupid) {
                // check user's group
                if (!ismember($groupid, $user->id)) {
                    continue; // skip this user
                }
            }
            $action = "<a href=\"submissions.php?action=adminamendtitle&id=$cm->id&sid=$submission->id\">".
                get_string("amendtitle", "workshop")."</a>";
            // has teacher already assessed this submission
            if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
                    AND userid = $USER->id")) {
                $curtime = time();
                if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
                    $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("reassess", "workshop")."</a>";
                }
                else { // there's still time left to edit...
                    $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("edit", "workshop")."</a>";
                }
            }
            else { // user has not assessed this submission
                $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                    get_string("assess", "workshop")."</a>";
            }
            if ($nassessments = workshop_count_assessments($submission)) {
                $action .= " | <a href=\"assessments.php?action=adminlist&id=$cm->id&sid=$submission->id\">".
                    get_string("listassessments", "workshop")." ($nassessments)</a>";
            }
            $action .= " | <a href=\"submissions.php?action=adminconfirmdelete&id=$cm->id&sid=$submission->id\">".
                get_string("delete", "workshop")."</a>";
            $table->data[] = array("$user->firstname $user->lastname", $submission->title.
                " (".get_string("grade").": ".workshop_submission_grade($workshop, $submission)." ".
                workshop_print_submission_assessments($workshop, $submission, "teacher").
                " ".workshop_print_submission_assessments($workshop, $submission, "student").")", $action);
        }
        print_heading(get_string("studentsubmissions", "workshop", $course->student), "center");
        print_table($table);
        workshop_print_key($workshop);
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_teacher_assessments($workshop, $user) {
    global $CFG;
    
    $timenow = time();
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
    }

    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // get user's submissions
    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        foreach ($submissions as $submission) {
            // get the assessments
            if ($assessments = workshop_get_assessments($submission)) {
                foreach ($assessments as $assessment) {
                    if (isteacher($workshop->course, $assessment->userid)) { // assessments by teachers only
                        $action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">".
                            get_string("view", "workshop")."</A>";
                        // has teacher commented on teacher's assessment? shouldn't happen but leave test in
                        if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
                            $comment = get_string("gradedbyteacher", "workshop", $course->teacher);
                            }
                        else {
                            $comment = userdate($assessment->timecreated);
                            }
                        $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
                        }
                    }
                }
            }
        }
    if (isset($table->data)) {
        print_table($table);
        }
    else {
        echo "<CENTER>".get_string("noassessmentsdone", "workshop")."</CENTER>\n";
        }
    }



//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_teacher_submissions($workshop, $user) {
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
    }
    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    // get the number of assessments this user has done
    $nassessed = count_records_select("workshop_assessments", "workshopid = $workshop->id
                    AND userid = $user->id");
    if ($nassessed < $workshop->ntassessments) { 
        // if user has not assessed enough, set up "future" assessment records for this user for the teacher submissions...
        // ... first count the number of assessments for each teacher submission...
        if ($submissions = workshop_get_teacher_submissions($workshop)) {
            srand ((float)microtime()*1000000); // initialise random number generator
            foreach ($submissions as $submission) {
                $n = count_records("workshop_assessments", "submissionid", $submission->id);
                // ...OK to have zero, we add a small random number to randomise things...
                $nassessments[$submission->id] = $n + rand(0, 99) / 100;
            }
            // ...put the submissions with the lowest number of assessments first...
            asort($nassessments);
            reset($nassessments);
            foreach ($nassessments as $submissionid => $n) { // break out of loop when we allocated enough assessments...
                $submission = get_record("workshop_submissions", "id", $submissionid);
                // ... provided the user has NOT already assessed that submission...
                if (!get_record("workshop_assessments", "submissionid", $submission->id, "userid",
                                    $user->id)) {
                    $yearfromnow = time() + 365 * 86400;
                    // ...create one and set timecreated way in the future, this is reset when record is updated
                    unset($assessment); // clear previous version of object (if any)
                    $assessment->workshopid = $workshop->id;
                    $assessment->submissionid = $submission->id;
                    $assessment->userid = $user->id;
                    $assessment->grade = -1; // set impossible grade
                    $assessment->timecreated = $yearfromnow;
                    if (!$assessment->id = insert_record("workshop_assessments", $assessment)) {
                        error("Could not insert workshop assessment!");
                    }
                    $nassessed++;
                    if ($nassessed >= $workshop->ntassessments) {
                        break;
                    }
                }
            }
        }
    }
    // now list user's assessments (but only list those which come from teacher submissions)
    if ($assessments = workshop_get_user_assessments($workshop, $user)) {
        $timenow = time();
        foreach ($assessments as $assessment) {
            if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
                error ("workshop_list_teacher_submissions: unable to get submission");
            }
            // submission from a teacher?
            if (isteacher($workshop->course, $submission->userid)) {
                $comment = '';
                // user assessment has two states: record created but not assessed (date created in the future); 
                // assessed but always available for re-assessment 
                if ($assessment->timecreated > $timenow) { // user needs to assess this submission
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("assess", "workshop")."</A>";
                }
                elseif ($assessment->timegraded) { 
                    // allow student to improve on their assessment once it's been graded
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("reassess", "workshop")."</A>";
                } else {
                    // allow student  just to see their assessment if it hasn't been graded
                    $action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">".
                        get_string("view", "workshop")."</A>";
                }
                // see if the assessment is graded
                if ($assessment->timegraded) {
                    // show grading grade
                    $comment = get_string("thegradeforthisassessmentis", "workshop", 
                            number_format($assessment->gradinggrade * $workshop->gradinggrade / 100, 1))." / ".
                            $workshop->gradinggrade;
                } elseif ($assessment->timecreated < $timenow) {
                    $comment = get_string("awaitinggradingbyteacher", "workshop", $course->teacher);
                }
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
            }
        }
    }
    print_table($table);
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_unassessed_student_submissions($workshop, $user) {
    // list the student submissions not assessed by this user
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }

    if (groupmode($course, $cm) == SEPARATEGROUPS) {
        $groupid = get_current_group($course->id);
    } else {
        $groupid = 0;
    }

    $table->head = array (get_string("title", "workshop"), get_string("submittedby", "workshop"),
        get_string("action", "workshop"), get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = workshop_get_student_submissions($workshop)) {
        foreach ($submissions as $submission) {
            // check group membership, if necessary
            if ($groupid) {
                // check user's group
                if (!ismember($groupid, $submission->userid)) {
                    continue; // skip this user
                }
            }
            $comment = "";
            // see if user already graded this assessment
            if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
                    AND userid = $user->id")) {
                $timenow = time();
                if (($timenow - $assessment->timecreated < $CFG->maxeditingtime)) {
                    // last chance salon
                    $submissionowner = get_record("user", "id", $submission->userid);
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("edit", "workshop")."</A>";
                    $table->data[] = array(workshop_print_submission_title($workshop, $submission), 
                        fullname($submissionowner), $action, $comment);
                    }
                }
            else { // no assessment
                $submissionowner = get_record("user", "id", $submission->userid);
                $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                    get_string("assess", "workshop")."</A>";
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), 
                    fullname($submissionowner), $action, $comment);
                }
            }
        if (isset($table->data)) {
            print_table($table);
            }
        }
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_unassessed_teacher_submissions($workshop, $user) {
    // list the teacher submissions not assessed by this user
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }

    $table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), 
            get_string("comment", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = workshop_get_teacher_submissions($workshop)) {
        foreach ($submissions as $submission) {
            $comment = "";
            // see if user already graded this assessment
            if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
                    AND userid = $user->id")) {
                $timenow = time();
                if (($timenow - $assessment->timecreated < $CFG->maxeditingtime)) {
                    // last chance salon
                    $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                        get_string("edit", "workshop")."</A>";
                    $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
                    }
                }
            else { // no assessment
                $action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                    get_string("assess", "workshop")."</A>";
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
                }
            }
        if (isset($table->data)) {
            print_table($table);
            }
        }
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_ungraded_assessments($workshop, $stype) {
    // lists all the assessments of student submissions for grading by teacher
    global $CFG;
    
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }

    $table->head = array (get_string("title", "workshop"), get_string("submittedby", "workshop"),
    get_string("assessor", "workshop"), get_string("timeassessed", "workshop"), get_string("action", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;
    $timenow = time();
    
    switch ($stype) {
        case "student" :
            $assessments = workshop_get_ungraded_assessments_student($workshop);
            break;
        case "teacher" :
            $assessments = workshop_get_ungraded_assessments_teacher($workshop);
            break;
        }
    if ($assessments) {
        foreach ($assessments as $assessment) {
            if (!isteacher($workshop->course, $assessment->userid)) { // don't let teacher grade their own assessments
                if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
                    $action = "<A HREF=\"assessments.php?action=gradeassessment&id=$cm->id&stype=$stype&aid=$assessment->id\">".
                        get_string("edit", "workshop")."</A>";
                    }
                else {
                    $action = "<A HREF=\"assessments.php?action=gradeassessment&id=$cm->id&stype=$stype&aid=$assessment->id\">".
                        get_string("grade", "workshop")."</A>";
                    }
                $submission = get_record("workshop_submissions", "id", $assessment->submissionid);
                $submissionowner = get_record("user", "id", $submission->userid);
                $assessor = get_record("user", "id", $assessment->userid);
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), 
                    fullname($submissionowner), fullname($assessor), userdate($assessment->timecreated), $action);
                }
            }
        if (isset($table->data)) {
            print_table($table);
            }
        }
    }
    

//////////////////////////////////////////////////////////////////////////////////////
function workshop_list_user_submissions($workshop, $user) {
    global $CFG;

    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }

    $timenow = time();
    $table->head = array (get_string("title", "workshop"),  get_string("action", "workshop"),
        get_string("submitted", "assignment"),  get_string("assessments", "workshop"));
    $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
    $table->size = array ("*", "*", "*", "*");
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        foreach ($submissions as $submission) {
            // allow user to delete a submission if it's warm
            if ($submission->timecreated > ($timenow - $CFG->maxeditingtime)) {
                $action = "<a href=\"submissions.php?action=userconfirmdelete&id=$cm->id&sid=$submission->id\">".
                    get_string("delete", "workshop")."</a>";
            }
            else {
                $action = '';
            }
            $n = count_records_select("workshop_assessments", "submissionid = $submission->id AND
                    timecreated < ($timenow - $CFG->maxeditingtime)");
            $table->data[] = array(workshop_print_submission_title($workshop, $submission), $action,
                userdate($submission->timecreated), $n);
        }
        print_table($table);
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_assessment($workshop, $assessment = false, $allowchanges = false, 
    $showcommentlinks = false, $returnto = '') {
    // $allowchanges added 14/7/03
    // $returnto added 28/8/03
    global $CFG, $THEME, $USER, $WORKSHOP_SCALES, $WORKSHOP_EWEIGHTS;
    
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    
    $timenow = time();

    // reset the internal flags
    if ($assessment) {
        $showgrades = false;
        }
    else { // if no assessment, i.e. specimen grade form always show grading scales
        $showgrades = true;
        }
    
    if ($assessment) {
        // set the internal flag is necessary
        if ($allowchanges or !$workshop->agreeassessments or !$workshop->hidegrades or 
                $assessment->timeagreed) {
            $showgrades = true;
            }
            
        echo "<CENTER><TABLE BORDER=\"1\" WIDTH=\"30%\"><TR>
            <TD ALIGN=CENTER BGCOLOR=\"$THEME->cellcontent\">\n";
        if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
            error ("Workshop_print_assessment: Submission record not found");
            }
        echo workshop_print_submission_title($workshop, $submission);
        echo "</TD></TR></TABLE><BR CLEAR=ALL>\n";
    
        // see if this is a pre-filled assessment for a re-submission...
        if ($assessment->resubmission) {
            // ...and print an explaination
            print_heading(get_string("assessmentofresubmission", "workshop"));
        }
        
        // print agreement time if the workshop requires peer agreement
        if ($workshop->agreeassessments and $assessment->timeagreed) {
            echo "<P>".get_string("assessmentwasagreedon", "workshop", userdate($assessment->timeagreed));
            }

        // first print any comments on this assessment
        if ($comments = workshop_get_comments($assessment)) {
            echo "<TABLE CELLPADDING=2 BORDER=1>\n";
            $firstcomment = TRUE;
            foreach ($comments as $comment) {
                echo "<TR valign=top><TD BGCOLOR=\"$THEME->cellheading2\"><P><B>".
                    get_string("commentby","workshop")." ";
                if (isteacher($workshop->course, $comment->userid)) {
                    echo $course->teacher;
                    }
                elseif ($assessment->userid == $comment->userid) {
                    print_string("assessor", "workshop");
                    }
                else {
                    print_string("authorofsubmission", "workshop");
                    }
                echo " ".get_string("on", "workshop", userdate($comment->timecreated))."</B></P></TD></TR><TR><TD>\n";
                echo text_to_html($comment->comments)."&nbsp;\n";
                // add the links if needed
                if ($firstcomment and $showcommentlinks and !$assessment->timeagreed) {
                    // show links depending on who doing the viewing
                    $firstcomment = FALSE;
                    if (isteacher($workshop->course, $USER->id) and ($comment->userid != $USER->id)) {
                        echo "<P ALIGN=RIGHT><A HREF=\"assessments.php?action=addcomment&id=$cm->id&aid=$assessment->id\">".
                            get_string("reply", "workshop")."</A>\n";
                        }
                    elseif (($comment->userid ==$USER->id) and (($timenow - $comment->timecreated) < $CFG->maxeditingtime)) {
                        echo "<P ALIGN=RIGHT><A HREF=\"assessments.php?action=editcomment&id=$cm->id&cid=$comment->id\">".
                            get_string("edit", "workshop")."</A>\n";
                        if ($USER->id == $submission->userid) {
                            echo " | <A HREF=\"assessments.php?action=agreeassessment&id=$cm->id&aid=$assessment->id\">".
                                get_string("agreetothisassessment", "workshop")."</A>\n";
                            }
                        }
                    elseif (($comment->userid != $USER->id) and (($USER->id == $assessment->userid) or 
                        ($USER->id == $submission->userid))) {
                        echo "<P ALIGN=RIGHT><A HREF=\"assessments.php?action=addcomment&id=$cm->id&aid=$assessment->id\">".
                            get_string("reply", "workshop")."</A>\n";
                        if ($USER->id == $submission->userid) {
                            echo " | <A HREF=\"assessments.php?action=agreeassessment&id=$cm->id&aid=$assessment->id\">".
                                get_string("agreetothisassessment", "workshop")."</A>\n";
                            }
                        }
                    }
                echo "</TD></TR>\n";
                }
            echo "</TABLE>\n";
            }
            
        // only show the grade if grading strategy > 0 and the grade is positive
        if ($showgrades and $workshop->gradingstrategy and $assessment->grade >= 0) { 
            echo "<CENTER><B>".get_string("thegradeis", "workshop").": ".number_format($assessment->grade, 2)." (".
                get_string("maximumgrade")." ".number_format($workshop->grade, 0).")</B></CENTER><BR CLEAR=ALL>\n";
            }
        }
        
    // now print the grading form with the grading grade if any
    // FORM is needed for Mozilla browsers, else radio bttons are not checked
        ?>
    <form name="assessmentform" method="post" action="assessments.php">
    <input type="hidden" name="id" value="<?php echo $cm->id ?>">
    <input type="hidden" name="aid" value="<?php echo $assessment->id ?>">
    <input type="hidden" name="action" value="updateassessment">
    <input type="hidden" name="returnto" value="<?php echo $returnto ?>">
    <center>
    <table cellpadding=2 border=1>
    <?php
    echo "<tr valign=top>\n";
    echo "  <td colspan=2 bgcolor=\"$THEME->cellheading2\"><center><b>".get_string("assessment", "workshop").
        "</b></center></td>\n";
    echo "</tr>\n";

    // get the assignment elements...
    $elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC");
    if (count($elementsraw) < $workshop->nelements) {
        print_string("noteonassignmentelements", "workshop");
    }
    if ($elementsraw) {
        foreach ($elementsraw as $element) {
            $elements[] = $element;   // to renumber index 0,1,2...
        }
    } else {
        $elements = null;
    }

    if ($assessment) {
        // get any previous grades...
        if ($gradesraw = get_records_select("workshop_grades", "assessmentid = $assessment->id", "elementno")) {
            foreach ($gradesraw as $grade) {
                $grades[] = $grade;   // to renumber index 0,1,2...
                }
            }
        }
    else {
        // setup dummy grades array
        for($i = 0; $i < count($elementsraw); $i++) { // gives a suitable sized loop
            $grades[$i]->feedback = get_string("yourfeedbackgoeshere", "workshop");
            $grades[$i]->grade = 0;
            }
        }
                
    // determine what sort of grading
    switch ($workshop->gradingstrategy) {
        case 0:  // no grading
            // now print the form
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<TR valign=top>\n";
                echo "  <TD align=right><P><B>". get_string("element","workshop")." $iplus1:</B></P></TD>\n";
                echo "  <TD>".text_to_html($elements[$i]->description);
                echo "</TD></TR>\n";
                echo "<TR valign=top>\n";
                echo "  <TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
                echo "  <TD>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                        }
                    echo "</textarea>\n";
                    }
                else {
                    echo text_to_html($grades[$i]->feedback);
                    }
                echo "  </TD>\n";
                echo "</TR>\n";
                echo "<TR valign=top>\n";
                echo "  <TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
                echo "</TR>\n";
                }
            break;
            
        case 1: // accumulative grading
            // now print the form
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<TR valign=top>\n";
                echo "  <TD align=right><P><B>". get_string("element","workshop")." $iplus1:</B></P></TD>\n";
                echo "  <TD>".text_to_html($elements[$i]->description);
                echo "<P align=right><FONT size=1>".get_string("weight", "workshop").": ".
                    number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
                echo "</TD></TR>\n";
                if ($showgrades) {
                    echo "<TR valign=top>\n";
                    echo "  <TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
                    echo "  <TD valign=\"top\">\n";
                    
                    // get the appropriate scale
                    $scalenumber=$elements[$i]->scale;
                    $SCALE = (object)$WORKSHOP_SCALES[$scalenumber];
                    switch ($SCALE->type) {
                        case 'radio' :
                                // show selections highest first
                                echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
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
                                        echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
                                        }
                                    else {
                                        echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
                                        }
                                    }
                                echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
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
            
                    echo "  </TD>\n";
                    echo "</TR>\n";
                    }
                echo "<TR valign=top>\n";
                echo "  <TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
                echo "  <TD>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
                    if (isset($grades[$i]->feedback)) {
                        echo $grades[$i]->feedback;
                        }
                    echo "</textarea>\n";
                    }
                else {
                    echo text_to_html($grades[$i]->feedback);
                    }
                echo "  </TD>\n";
                echo "</TR>\n";
                echo "<TR valign=top>\n";
                echo "  <TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
                echo "</TR>\n";
                }
            break;
            
        case 2: // error banded grading
            // now run through the elements
            $negativecount = 0;
            for ($i=0; $i < count($elements) - 1; $i++) {
                $iplus1 = $i+1;
                echo "<TR valign=top>\n";
                echo "  <TD align=right><P><B>". get_string("element","workshop")." $iplus1:</B></P></TD>\n";
                echo "  <TD>".text_to_html($elements[$i]->description);
                echo "<P align=right><FONT size=1>".get_string("weight", "workshop").": ".
                    number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
                echo "</TD></TR>\n";
                echo "<TR valign=top>\n";
                echo "  <TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
                echo "  <TD valign=\"top\">\n";
                    
                // get the appropriate scale - yes/no scale (0)
                $SCALE = (object) $WORKSHOP_SCALES[0];
                switch ($SCALE->type) {
                    case 'radio' :
                            // show selections highest first
                            echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
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
                                    echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
                                    }
                                else {
                                    echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
                                    }
                                }
                            echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
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
        
                echo "  </TD>\n";
                echo "</TR>\n";
                echo "<TR valign=top>\n";
                echo "  <TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
                echo "  <TD>\n";
                if ($allowchanges) {
                    echo "      <textarea name=\"feedback[$i]\" rows=3 cols=75 wrap=\"virtual\">\n";
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
                echo "&nbsp;</TD>\n";
                echo "</TR>\n";
                echo "<TR valign=top>\n";
                echo "  <TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
                echo "</TR>\n";
                if (empty($grades[$i]->grade)) {
                    $negativecount++;
                    }
                }
            // print the number of negative elements
            // echo "<TR><TD>".get_string("numberofnegativeitems", "workshop")."</TD><TD>$negativecount</TD></TR>\n";
            // echo "<TR valign=top>\n";
            // echo "   <TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
            echo "</TABLE></CENTER>\n";
            // now print the grade table
            echo "<P><CENTER><B>".get_string("gradetable","workshop")."</B></CENTER>\n";
            echo "<CENTER><TABLE cellpadding=5 border=1><TR><TD ALIGN=\"CENTER\">".
                get_string("numberofnegativeresponses", "workshop");
            echo "</TD><TD>". get_string("suggestedgrade", "workshop")."</TD></TR>\n";
            for ($j = 100; $j >= 0; $j--) {
                $numbers[$j] = $j;
                }
            for ($i=0; $i<=$workshop->nelements; $i++) {
                if ($i == $negativecount) {
                    echo "<TR><TD ALIGN=\"CENTER\"><IMG SRC=\"$CFG->pixpath/t/right.gif\"> $i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
                    }
                else {
                    echo "<TR><TD ALIGN=\"CENTER\">$i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
                    }
                }
            echo "</TABLE></CENTER>\n";
            echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD><b>".get_string("optionaladjustment", 
                    "workshop")."</b></TD><TD>\n";
            unset($numbers);
            for ($j = 20; $j >= -20; $j--) {
                $numbers[$j] = $j;
                }
            if (isset($grades[$workshop->nelements]->grade)) {
                choose_from_menu($numbers, "grade[$workshop->nelements]", $grades[$workshop->nelements]->grade, "");
                }
            else {
                choose_from_menu($numbers, "grade[$workshop->nelements]", 0, "");
                }
            echo "</TD></TR>\n";
            break;
            
        case 3: // criteria grading
            echo "<TR valign=top>\n";
            echo "  <TD BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
            echo "  <TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","workshop")."</B></TD>\n";
            echo "  <TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("select", "workshop")."</B></TD>\n";
            echo "  <TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("suggestedgrade", "workshop")."</B></TD>\n";
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
                echo "<TR valign=top>\n";
                echo "  <TD>$iplus1</TD><TD>".text_to_html($elements[$i]->description)."</TD>\n";
                if ($selection == $i) {
                    echo "  <TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[0]\" VALUE=\"$i\" CHECKED></TD>\n";
                    }
                else {
                    echo "  <TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[0]\" VALUE=\"$i\"></TD>\n";
                    }
                echo "<TD align=center>{$elements[$i]->maxscore}</TD></TR>\n";
                }
            echo "</TABLE></CENTER>\n";
            echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD><b>".get_string("optionaladjustment", 
                    "workshop")."</b></TD><TD>\n";
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
            echo "</TD></TR>\n";
            break;
            
        case 4: // rubric grading
            // now run through the elements...
            for ($i=0; $i < count($elements); $i++) {
                $iplus1 = $i+1;
                echo "<TR valign=\"top\">\n";
                echo "<TD align=\"right\"><b>".get_string("element", "workshop")." $iplus1:</b></TD>\n";
                echo "<TD>".text_to_html($elements[$i]->description).
                     "<P align=\"right\"><font size=\"1\">".get_string("weight", "workshop").": ".
                    number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight], 2)."</font></TD></tr>\n";
                echo "<TR valign=\"top\">\n";
                echo "  <TD BGCOLOR=\"$THEME->cellheading2\" align=\"center\"><B>".get_string("select", "workshop")."</B></TD>\n";
                echo "  <TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","workshop")."</B></TD></tr>\n";
                if (isset($grades[$i])) {
                    $selection = $grades[$i]->grade;
                    } else {
                    $selection = 0;
                    }
                // ...and the rubrics
                if ($rubricsraw = get_records_select("workshop_rubrics", "workshopid = $workshop->id AND 
                        elementno = $i", "rubricno ASC")) {
                    unset($rubrics);
                    foreach ($rubricsraw as $rubic) {
                        $rubrics[] = $rubic;   // to renumber index 0,1,2...
                        }
                    for ($j=0; $j<5; $j++) {
                        if (empty($rubrics[$j]->description)) {
                            break; // out of inner for loop
                            }
                        echo "<TR valign=top>\n";
                        if ($selection == $j) {
                            echo "  <TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED></TD>\n";
                            }else {
                            echo "  <TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"></TD>\n";
                            }
                        echo "<TD>".text_to_html($rubrics[$j]->description)."</TD>\n";
                        }
                    echo "<TR valign=top>\n";
                    echo "  <TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
                    echo "  <TD>\n";
                    if ($allowchanges) {
                        echo "      <textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
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
                    echo "  <td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
                    echo "</tr>\n";
                    }
                }
            break;
        } // end of outer switch
    
    // now get the general comment (present in all types)
    echo "<tr valign=\"top\">\n";
    switch ($workshop->gradingstrategy) {
        case 0:
        case 1:
        case 4 : // no grading, accumulative and rubic
            echo "  <td align=\"right\"><P><B>". get_string("generalcomment", "workshop").":</B></P></TD>\n";
            break; 
        default : 
            echo "  <td align=\"right\"><P><B>". get_string("reasonforadjustment", "workshop").":</B></P></TD>\n";
        }
    echo "  <td>\n";
    if ($allowchanges) {
        echo "      <textarea name=\"generalcomment\" rows=5 cols=75 wrap=\"virtual\">\n";
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
            print_string("yourfeedbackgoeshere", "workshop");
            }
        }
    echo "&nbsp;</td>\n";
    echo "</tr>\n";
    echo "<tr valign=\"top\">\n";
    echo "  <td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
    echo "</tr>\n";
    
    $timenow = time();
    // now show the grading grade if available...
    if ($assessment->timegraded) {
        echo "<tr valign=\"top\">\n";
        echo "  <td align=\"right\"><p><b>";
        if (isteacher($course->id, $assessment->userid)) {
            print_string("gradeforstudentsassessment", "workshop", $course->teacher);
        } else {
            print_string("gradeforstudentsassessment", "workshop", $course->student);
        }
        echo ":</b></p></td><td>\n";
        echo number_format($assessment->gradinggrade * $workshop->gradinggrade / 100, 0);
        echo "&nbsp;</td>\n";
        echo "</tr>\n";
        echo "<tr valign=\"top\">\n";
        echo "<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</td>\n";
        echo "</tr>\n";
        }
        
    // ...and close the table, show submit button if needed...
    echo "</table>\n";
    if ($assessment) {
        if ($allowchanges) {  
            echo "<input type=\"submit\" VALUE=\"".get_string("savemyassessment", "workshop")."\">\n";
            }
        // ...if user is author, assessment not agreed, there's no comments, the showcommentlinks flag is set and 
        // it's not self assessment then show some buttons!
        if (($submission->userid == $USER->id) and !$assessment->timeagreed and !$comments and $showcommentlinks and 
                $submission->userid != $assessment->userid) {
            echo "<input type=button VALUE=\"".get_string("agreetothisassessment", "workshop")."\" 
                onclick=\"document.assessmentform.action.value='agreeassessment';document.assessmentform.submit();\">\n";
            echo "<input type=submit value=\"".get_string("disagreewiththisassessment", "workshop")."\"
                onclick=\"document.assessmentform.action.value='addcomment';document.assessmentform.submit();\">\n";
            }
        }
    echo "</center>";
    echo "</form>\n";
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_assessments_by_user_for_admin($workshop, $user) {

    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }

    if ($assessments = workshop_get_user_assessments_done($workshop, $user)) {
        foreach ($assessments as $assessment) {
            echo "<p><center><b>".get_string("assessmentby", "workshop", fullname($user))."</b></center></p>\n";
            workshop_print_assessment($workshop, $assessment);
            echo "<p align=\"right\"><a href=\"assessments.php?action=adminconfirmdelete&id=$cm->id&aid=$assessment->id\">".
                get_string("delete", "workshop")."</a></p><hr>\n";
            }
        }
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_assessments_for_admin($workshop, $submission) {

    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
        error("Course Module ID was incorrect");
    }

    if ($assessments =workshop_get_assessments($submission)) {
        foreach ($assessments as $assessment) {
            if (!$user = get_record("user", "id", $assessment->userid)) {
                error (" workshop_print_assessments_for_admin: unable to get user record");
                }
            echo "<p><center><b>".get_string("assessmentby", "workshop", fullname($user))."</b></center></p>\n";
            workshop_print_assessment($workshop, $assessment);
            echo "<p align=\"right\"><a href=\"assessments.php?action=adminconfirmdelete&id=$cm->id&aid=$assessment->id\">".
                get_string("delete", "workshop")."</a></p><hr>\n";
            }
        }
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_assignment_info($workshop) {

    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    // print standard assignment heading
    $strdifference = format_time($workshop->deadline - time());
    if (($workshop->deadline - time()) < 0) {
        $strdifference = "<font color=\"red\">$strdifference</font>";
    }
    $strduedate = userdate($workshop->deadline)." ($strdifference)";
    print_simple_box_start("center");
    print_heading($workshop->name, "center");
    print_simple_box_start("center");
    echo "<b>".get_string("duedate", "assignment")."</b>: $strduedate<br />";
    $grade = $workshop->gradinggrade + $workshop->grade;
    echo "<b>".get_string("maximumgrade")."</b>: $grade<br />";
    echo "<b>".get_string("detailsofassessment", "workshop")."</b>: 
        <a href=\"assessments.php?id=$cm->id&action=displaygradingform\">".
        get_string("specimenassessmentform", "workshop")."</a><br />";
    print_simple_box_end();
    echo "<br />";
    echo format_text($workshop->description, $workshop->format);
    print_simple_box_end();
    echo "<br />";  
    }


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_difference($time) {
    if ($time < 0) {
        $timetext = get_string("late", "assignment", format_time($time));
        return " (<FONT COLOR=RED>$timetext</FONT>)";
    } else {
        $timetext = get_string("early", "assignment", format_time($time));
        return " ($timetext)";
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_feedback($course, $submission) {
    global $CFG, $THEME, $RATING;

    if (! $teacher = get_record("user", "id", $submission->teacher)) {
        error("Weird workshop error");
    }

    echo "\n<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 ALIGN=CENTER><TR><TD BGCOLOR=#888888>";
    echo "\n<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 VALIGN=TOP>";

    echo "\n<TR>";
    echo "\n<TD ROWSPAN=3 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($teacher->id, $course->id, $teacher->picture);
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$THEME->cellheading\">".fullname($teacher);
    echo "&nbsp;&nbsp;<FONT SIZE=2><I>".userdate($submission->timemarked)."</I>";
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";

    echo "<P ALIGN=RIGHT><FONT SIZE=-1><I>";
    if ($submission->grade) {
        echo get_string("grade").": $submission->grade";
    } else {
        echo get_string("nograde");
    }
    echo "</I></FONT></P>";

    echo text_to_html($submission->assessorcomment);
    echo "</TD></TR></TABLE>";
    echo "</TD></TR></TABLE>";
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_key($workshop) {
    // print an explaination of the grades
    
    if (!$course = get_record("course", "id", $workshop->course)) {
        error("Print key: course not found");
    }
    echo "<table align=\"center\">\n";
    echo "<tr><td><small>{}</small></td><td><small>".get_string("assessmentby", "workshop", $course->student).
        ";&nbsp;&nbsp; </small></td>\n";
    echo "<td><small>[]</small></td><td><small>".get_string("assessmentby", "workshop", $course->teacher).
        ";&nbsp;&nbsp; </small></td>\n";
    echo "<td><small>&lt;&gt;</small></td><td><small>".get_string("assessmentdropped", "workshop").
        ";&nbsp;&nbsp; </small></td>\n";
    echo "<td><small>()</small></td><td><small>".get_string("gradegiventoassessment", "workshop").
        ".</small></td></tr>\n";
    echo "<tr><td colspan=\"8\" align=\"center\"><small>".get_string("gradesforsubmissionsare", "workshop", $workshop->grade)."; ".
        get_string("gradesforassessmentsare", "workshop", $workshop->gradinggrade).".</small></td></tr>\n";
    echo "</table><br />\n";
    return;    
}
    

//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_league_table($workshop) {
    // print an order table of (student) submissions showing teacher's and student's assessments
    
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Print league table: Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
            error("Course Module ID was incorrect");
    }
    // set $groupid if workshop is in SEPARATEGROUPS mode
    if (groupmode($course, $cm) == SEPARATEGROUPS) {
        $groupid = get_current_group($course->id);
    } else {
        $groupid = 0;
    }
 
    $nentries = $workshop->showleaguetable;
    if ($workshop->anonymous and isstudent($course->id)) {
        $table->head = array (get_string("title", "workshop"), 
            get_string("teacherassessments", "workshop", $course->teacher),  
            get_string("studentassessments", "workshop",    $course->student), get_string("overallgrade", "workshop"));
        $table->align = array ("left",  "center", "center", "center");
        $table->size = array ("*", "*", "*", "*");
    }
    else { // show names
        $table->head = array (get_string("title", "workshop"),  get_string("name"),
            get_string("teacherassessments", "workshop", $course->teacher),  
            get_string("studentassessments", "workshop",    $course->student), get_string("overallgrade", "workshop"));
        $table->align = array ("left", "left", "center", "center", "center");
        $table->size = array ("*", "*", "*", "*", "*");
    }
    $table->cellpadding = 2;
    $table->cellspacing = 0;

    if ($submissions = workshop_get_student_submissions($workshop)) {
        foreach ($submissions as $submission) {
            if ($groupid) {
                // check submission's group
                if (!ismember($groupid, $submission->userid)) {
                    continue; // skip this submission
                }
            }
            $grades[$submission->id] = workshop_submission_grade($workshop, $submission);
        }
        arsort($grades); // largest grade first
        reset($grades);
        $n = 1;
        while (list($submissionid, $grade) = each($grades)) {
            if (!$submission = get_record("workshop_submissions", "id", $submissionid)) {
                error("Print league table: submission not found");
            }
            if (!$user = get_record("user", "id", $submission->userid)) {
                error("Print league table: user not found");
            }
            if ($workshop->anonymous and isstudent($course->id)) {
                $table->data[] = array(workshop_print_submission_title($workshop, $submission),
                        workshop_print_submission_assessments($workshop, $submission, "teacher"),
                        workshop_print_submission_assessments($workshop, $submission, "student"), $grade);
            }
            else {
                $table->data[] = array(workshop_print_submission_title($workshop, $submission), fullname($user),
                        workshop_print_submission_assessments($workshop, $submission, "teacher"),
                        workshop_print_submission_assessments($workshop, $submission, "student"), $grade);
            }
            $n++;
            if ($n > $nentries) {
                break;
            }
        }
        print_heading(get_string("leaguetable", "workshop"));
        print_table($table);
        workshop_print_key($workshop);
    }
}
    

//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_submission_assessments($workshop, $submission, $type) {
    // Returns the teacher or peer grade and a hyperlinked list of grades for this submission

    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
            error("Course Module ID was incorrect");
    }
 
    $str = '';
    if ($assessments = workshop_get_assessments($submission)) {
        switch ($type) {
            case "teacher" : 
                foreach ($assessments as $assessment) {
                    if (isteacher($workshop->course, $assessment->userid)) {
                        $str .= "<a href=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">";
                        if ($assessment->timegraded) {
                            if ($assessment->gradinggrade) {
                                $str .= "[".number_format($assessment->grade * $workshop->grade / 100, 0)." (".
                                    number_format($assessment->gradinggrade * $workshop->gradinggrade / 100, 0).
                                    ")]</a> ";
                            } else {
                                $str .= "&lt;".number_format($assessment->grade, 0)." (0)&gt;</a> ";
                            }
                        } else {
                            $str .= "[".number_format($assessment->grade, 0)." (-)]</a> ";
                        }
                    }
                }
                break;
            case "student" : 
                foreach ($assessments as $assessment) {
                    if (isstudent($workshop->course, $assessment->userid)) {
                        $str .= "<a href=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">";
                        if ($assessment->timegraded) {
                            if ($assessment->gradinggrade) {
                                $str .= "{".number_format($assessment->grade * $workshop->grade / 100, 0)." (".
                                    number_format($assessment->gradinggrade * $workshop->gradinggrade / 100, 0).
                                    ")}</a> ";
                            } else {
                                $str .= "&lt;".number_format($assessment->grade * $workshop->grade / 100, 0).
                                    " (0)&gt;</a> ";
                            }
                        } else {
                            $str .= "{".number_format($assessment->grade * $workshop->grade / 100, 0)." (-)}</a> ";
                        }
                    }
                }
                break;
        }
    }
    if (!$str) {
        $str = "&nbsp;";   // be kind to Mozilla browsers!
    }
    return $str;
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_submission_title($workshop, $submission) {
// Arguments are objects

    global $CFG;
    
    if (!$submission->timecreated) { // a "no submission"
        return $submission->title;
    }

    require_once("$CFG->dirroot/files/mimetypes.php");
    $filearea = workshop_file_area_name($workshop, $submission);
    if ($basedir = workshop_file_area($workshop, $submission)) {
        if (list($file) = get_directory_list($basedir)) {
            $icon = mimeinfo("icon", $file);
            if ($CFG->slasharguments) {
                $ffurl = "file.php/$filearea/$file";
            } else {
                $ffurl = "file.php?file=/$filearea/$file";
            }
            return "<IMG SRC=\"$CFG->pixpath/f/$icon\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"File\">".
                "&nbsp;<A TARGET=\"uploadedfile$submission->id\" HREF=\"$CFG->wwwroot/$ffurl\">$submission->title</A>";
        }
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_tabbed_heading($tabs) {
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

    global $CFG, $THEME;
    
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
    echo "<table width=\"$tabs-width\" border=\"0\" valign=\"top\" align=\"center\" ";
    echo " cellpadding=\"$tabs->cellpadding\" cellspacing=\"0\" class=\"generaltable\">\n";

    if (!empty($tabs->names)) {
        echo "<tr>";
        echo "<td  class=\"generaltablecell\">".
            "<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td>\n";
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
                echo "<td valign=top class=\"generaltabselected\" $alignment $width $wrapping bgcolor=\"$THEME->cellheading2\">$tab</td>\n";
            } else {
                echo "<td valign=top class=\"generaltab\" $alignment $width $wrapping bgcolor=\"$THEME->cellheading\">$tab</td>\n";
            }
        echo "<td  class=\"generaltablecell\">".
            "<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td>\n";
        }
        echo "</tr>\n";
    } else {
        echo "<tr><td>No names specified</td></tr>\n";
    }
    // bottom stripe
    $ncells = count($tabs->names)*2 +1;
    $height = 2;
    echo "<tr><td colspan=\"$ncells\" bgcolor=\"$THEME->cellheading2\">".
        "<img height=\"$height\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td></tr>\n";
    echo "</table>\n";
    // print_simple_box_end();

    return true;
}

function workshop_print_time_to_deadline($time) {
    if ($time < 0) {
        $timetext = get_string("afterdeadline", "workshop", format_time($time));
        return " (<FONT COLOR=RED>$timetext</FONT>)";
    } else {
        $timetext = get_string("beforedeadline", "workshop", format_time($time));
        return " ($timetext)";
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_upload_form($workshop) {
// Arguments are objects, needs title coming in

    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    echo "<DIV ALIGN=CENTER>";
    echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"POST\" ACTION=upload.php>";
    echo " <INPUT TYPE=hidden NAME=MAX_FILE_SIZE value=\"$workshop->maxbytes\">";
    echo " <INPUT TYPE=hidden NAME=id VALUE=\"$cm->id\">";
    echo "<b>".get_string("title", "workshop")."</b>: <INPUT NAME=\"title\" TYPE=\"text\" SIZE=\"60\" MAXSIZE=\"100\"><BR><BR>\n";
    echo " <INPUT NAME=\"newfile\" TYPE=\"file\" size=\"50\">";
    echo " <INPUT TYPE=submit NAME=save VALUE=\"".get_string("uploadthisfile")."\">";
    echo "</FORM>";
    echo "</DIV>";
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_user_assessments($workshop, $user) {
    // Returns the number of assessments and a hyperlinked list of grading grades for the assessments made by this user

    if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
            error("Course Module ID was incorrect");
    }
 
    if ($assessments = workshop_get_user_assessments_done($workshop, $user)) {
        $n = count($assessments);
        $str = "$n : ";
        foreach ($assessments as $assessment) {
            $str .= "<a href=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">";
            if ($assessment->timegraded) {
                if ($assessment->gradinggrade) {
                    $str .= "{".number_format($assessment->grade * $workshop->grade / 100, 0). " (".
                        number_format($assessment->gradinggrade * $workshop->gradinggrade / 100).")}</a> ";
                } else {
                    $str .= "&lt;".number_format($assessment->grade * $workshop->grade / 100, 0)." (0)&gt;</a> ";
                }
            } else {
                $str .= "{".number_format($assessment->grade * $workshop->grade / 100, 0)." (-)}</a> ";
            }
        }
    }
    else {
        $str ="0";
    }
    return $str;
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_test_user_assessments($workshop, $user) {
    // see if user has assessed required number of assessments of teachers submissions...
    global $CFG;
    
    $result = true;
    $n = 0;
    $timenow =time();
    if ($submissions = workshop_get_teacher_submissions($workshop)) {
        foreach ($submissions as $submission) {
            if ($assessment = workshop_get_submission_assessment($submission, $user)) {
                // ...the date stamp on the assessment should be in the past 
                if ($assessment->timecreated < $timenow) {
                    $n++;
                }
            }
        }
        if ($n < min($workshop->ntassessments, workshop_count_teacher_submissions($workshop))) {
            $result = false; 
        }
    }
    return $result;
}
    

?>
