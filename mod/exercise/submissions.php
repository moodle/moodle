<?php  // $Id$

/*************************************************
    ACTIONS handled are:

    adminamendtitle
    adminclearlate
    adminconfirmdelete
    admindelete
    adminlateflag
    adminlist
    displayfinalgrades (teachers only)
    listforassessmentstudent
    listforassessmentteacher
    saveweights
    userconfirmdelete
    userdelete


************************************************/

    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");
    require_once("version.php");

    $id     = required_param('id', PARAM_INT); // Course Module ID
    $action = required_param('action', PARAM_ALPHA);
    $aid    = optional_param('aid', 0, PARAM_INT);
    $sid    = optional_param('sid', 0, PARAM_INT);
    $title  = optional_param('title', '', PARAM_CLEAN);

    // get some essential stuff...
    if (! $cm = get_coursemodule_from_id('exercise', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $exercise = get_record("exercise", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");
    $strsubmissions = get_string("submissions", "exercise");

    // ... print the header and...
    $navlinks = array();
    $navlinks[] = array('name' => $strexercises, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($exercise->name), 'link' => "view.php?id=$cm->id", 'type' => 'activityinstance');
    $navlinks[] = array('name' => $strsubmissions, 'link' => '', 'type' => 'title');
    
    $navigation = build_navigation($navlinks);
    print_header_simple(format_string($exercise->name), "", $navigation,
                  "", "", true);


    /******************* admin amend title ************************************/
    if ($action == 'adminamendtitle' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }
        if (empty($sid)) {
            error("Admin Amend Title: submission id missing");
        }

        $submission = get_record("exercise_submissions", "id", $sid);
        print_heading(get_string("amendtitle", "exercise"));
        ?>
        <form id="amendtitleform" action="submissions.php" method="post">
        <input type="hidden" name="action" value="adminupdatetitle" />
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="sid" value="<?php echo $sid ?>" />
        <center>
        <table celpadding="5" border="1">
        <?php

        // now get the comment
        echo "<tr valign=\"top\">\n";
        echo "    <td align=\"right\"><p><b>". get_string("title", "exercise").":</b></p></td>\n";
        echo "    <td>\n";
        echo "        <input type=\"text\" name=\"title\" size=\"60\" maxlength=\"100\" value=\"$submission->title\" />\n";
        echo "    </td></tr></table>\n";
        echo "<input type=\"submit\" value=\"".get_string("amendtitle", "exercise")."\" />\n";
        echo "</center></form>\n";

        }


    /******************* admin clear late (flag) ************************************/
    elseif ($action == 'adminclearlate' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }
        if (empty($sid)) {
            error("Admin clear late flag: submission id missing");
        }

        if (!$submission = get_record("exercise_submissions", "id", $sid)) {
            error("Admin clear late flag: can not get submission record");
        }
        if (set_field("exercise_submissions", "late", 0, "id", $sid)) {
            print_heading(get_string("clearlateflag", "exercise")." ".get_string("ok"));
        }

        add_to_log($course->id, "exercise", "late flag cleared", "view.php?id=$cm->id", "submission $submission->id");

        redirect("submissions.php?id=$cm->id&amp;action=adminlist");
    }


    /******************* admin confirm delete ************************************/
    elseif ($action == 'adminconfirmdelete' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }
        if (empty($sid)) {
            error("Admin confirm delete: submission id missing");
        }
        if (!$submission = get_record("exercise_submissions", "id", $sid)) {
            error("Admin delete: can not get submission record");
        }

        if (isteacher($course->id, $submission->userid)) {
            if (!isteacheredit($course->id)) {
                error("Only teacher with editing permissions can delete teacher submissions.");
            }
            if ($assessments = exercise_get_assessments($submission)) {
                echo "<p align=\"center\">".get_string("deletesubmissionwarning", "exercise", count($assessments)).
                    "</p>\n";
            }
        }
        notice_yesno(get_string("confirmdeletionofthisitem","exercise", get_string("submission", "exercise")),
             "submissions.php?action=admindelete&amp;id=$cm->id&amp;sid=$sid", "submissions.php?id=$cm->id&amp;action=adminlist");
        }


    /******************* admin delete ************************************/
    elseif ($action == 'admindelete' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($sid)) {
            error("Admin delete: submission id missing");
            }

        if (!$submission = get_record("exercise_submissions", "id", $sid)) {
            error("Admin delete: can not get submission record");
            }
        print_string("deleting", "exercise");
        // first get any assessments...
        if ($assessments = exercise_get_assessments($submission)) {
            foreach($assessments as $assessment) {
                // ...and all the associated records...
                delete_records("exercise_grades", "assessmentid", $assessment->id);
                echo ".";
                }
            // ...now delete the assessments...
            delete_records("exercise_assessments", "submissionid", $submission->id);
            }
        // ...and the submission record...
        delete_records("exercise_submissions", "id", $submission->id);
        // ..and finally the submitted file
        exercise_delete_submitted_files($exercise, $submission);
        add_to_log($course->id, "exercise", "delete", "view.php?id=$cm->id", "submission $submission->id");

        print_continue("submissions.php?id=$cm->id&amp;action=adminlist");
        }


    /******************* admin (confirm) late flag ************************************/
    elseif ($action == 'adminlateflag' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($sid)) {
            error("Admin confirm late flag: submission id missing");
            }
        if (!$submission = get_record("exercise_submissions", "id", $sid)) {
            error("Admin confirm late flag: can not get submission record");
            }

        notice_yesno(get_string("clearlateflag","exercise")."?",
             "submissions.php?action=adminclearlate&amp;id=$cm->id&amp;sid=$sid",
             "submissions.php?id=$cm->id&amp;action=adminlist");
        }


    /******************* list all submissions ************************************/
    elseif ($action == 'adminlist' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }

        echo "<p><small>Exercise Version-> $module->version</small></p>";
        exercise_list_submissions_for_admin($exercise);
        print_continue("view.php?id=$cm->id");

    }


    /******************* admin update title ************************************/
    elseif ($action == 'adminupdatetitle' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($sid)) {
            error("Admin Update Title: submission id missing");
            }

        if (set_field("exercise_submissions", "title", $title, "id", $sid)) {
            print_heading(get_string("amendtitle", "exercise")." ".get_string("ok"));
            }
        redirect("submissions.php?id=$cm->id&amp;action=adminlist");
        }


    /*************** display final grades (by teacher) ***************************/
    elseif ($action == 'displayfinalgrades') {
        $groupid = get_current_group($course->id);
        // Get all the students
        if (!$users = get_course_students($course->id, "u.lastname, u.firstname")) {
            print_heading(get_string("nostudentsyet"));
            print_footer($course);
            exit;
        }

        // show the final grades as stored in the tables...
        print_heading_with_help(get_string("displayoffinalgrades", "exercise"), "finalgrades", "exercise");
        echo "<center><table border=\"1\" width=\"90%\"><tr>\n";
        echo "<td><b>".$course->student."</b></td>";
        echo "<td><b>".get_string("submission", "exercise")."</b></td>";
        echo "<td align=\"center\"><b>".get_string("gradeforassessment", "exercise")."</b></td>";
        echo "<td align=\"center\"><b>".get_string("gradeforsubmission", "exercise")."</b></td>";
        echo "<td align=\"center\"><b>".get_string("overallgrade", "exercise")."</b></td></tr>\n";
        // now the weights
        echo "<tr><td><b>".get_string("maximumgrade")."</b></td>";
        echo "<td><b>&nbsp;</b></td>\n";
        echo "<td align=\"center\"><b>$exercise->gradinggrade</b></td>\n";
        echo "<td align=\"center\"><b>$exercise->grade</b></td>\n";
        echo "<td><b>&nbsp;</b></td></tr>\n";
        foreach ($users as $user) {
            // check group membership, if necessary
            if ($groupid) {
                // check user's group
                if (!ismember($groupid, $user->id)) {
                    continue; // skip this user
                }
            }
            // first get user's own assessment reord, it should contain their grading grade
            if ($ownassessments = exercise_get_user_assessments($exercise, $user)) {
                foreach ($ownassessments as $ownassessment) {
                    break; // there should only be one
                }
            }
            else {
                $ownassessment->gradinggrade = 0;
            }
            if ($submissions = exercise_get_user_submissions($exercise, $user)) {
                foreach ($submissions as $submission) {
                    if ($assessments = exercise_get_assessments($submission)) {
                        foreach ($assessments as $assessment) { // (normally there should only be one
                            $gradinggrade = number_format($ownassessment->gradinggrade * $exercise->gradinggrade /
                                    100.0, 1);
                            $grade = number_format($assessment->grade * $exercise->grade / 100.0, 1);
                            $overallgrade = number_format(($assessment->grade * $exercise->grade / 100.0) +
                                ($ownassessment->gradinggrade * $exercise->gradinggrade / 100.0), 1);
                            if ($submission->late) {
                                $grade = "<font color=\"red\">(".$grade.")</font>";
                                $overallgrade = "<font color=\"red\">(".$overallgrade.")</font>";
                            }
                            echo "<tr><td>".fullname($user)."</td>\n";
                            echo "<td>".exercise_print_submission_title($exercise, $submission)."</td>\n";
                            echo "<td align=\"center\">$gradinggrade</td>";
                            echo "<td align=\"center\">$grade</td>";
                            echo "<td align=\"center\">$overallgrade</td></tr>\n";
                        }
                    }
                }
            }
        }
        echo "</table><br clear=\"all\" />\n";
        if ($exercise->showleaguetable) {
            exercise_print_league_table($exercise);
            echo "<br />\n";
        }
        echo get_string("maximumgrade").": $exercise->grade\n";
        print_continue("view.php?id=$cm->id");
    }


    /******************* list for assessment student (submissions) ************************************/
    elseif ($action == 'listforassessmentstudent' ) {
        if (!$users = get_course_students($course->id)) {
            print_heading(get_string("nostudentsyet"));
            print_footer($course);
            exit;
            }
        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        exercise_list_unassessed_student_submissions($exercise, $USER);
        print_continue("view.php?id=$cm->id");

        }


    /******************* list for assessment teacher (submissions) ************************************/
    elseif ($action == 'listforassessmentteacher' ) {
        if (!$users = get_course_students($course->id)) {
            print_heading(get_string("nostudentsyet"));
            print_footer($course);
            exit;
            }
        exercise_list_unassessed_teacher_submissions($exercise, $USER);
        print_continue("view.php?id=$cm->id");

        }


    /****************** save league table entries and anonimity setting (by teacher) **************/
    elseif ($action == 'saveleaguetable') {

        $form = (object)$_POST;

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }

        // save the number of league table entries from the form...
        if ($form->nentries == 'All') {
            $nentries = 99;
        } else {
            $nentries = $form->nentries;
        }
        // ...and save it
        set_field("exercise", "showleaguetable", $nentries, "id", "$exercise->id");

        // ...and save the anonimity setting
        set_field("exercise", "anonymous", $form->anonymous, "id", "$exercise->id");

        redirect("submissions.php?id=$cm->id&amp;action=adminlist", get_string("entriessaved", "exercise"));
        }

        /*************** save weights (by teacher) ***************************/
    elseif ($action == 'saveweights') {

        $form = (object)$_POST;

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }

        // save the weights from the form...
        if (isset($form->teacherweight)) {
            $teacherweight = $form->teacherweight;
            // ...and save them
            set_field("exercise", "teacherweight", $teacherweight, "id", "$exercise->id");
            }

        if (isset($form->gradingweight)) {
            $gradingweight = $form->gradingweight;
            // ...and save them
            set_field("exercise", "gradingweight", $gradingweight, "id", "$exercise->id");
            }

        redirect("submissions.php?id=$cm->id&amp;action=adminlist", get_string("weightssaved", "exercise"));
        }


    /******************* user confirm delete ************************************/
    elseif ($action == 'userconfirmdelete' ) {

        if (empty($sid)) {
            error("User Confirm Delete: submission id missing");
            }

        notice_yesno(get_string("confirmdeletionofthisitem","exercise", get_string("submission", "exercise")),
             "submissions.php?action=userdelete&amp;id=$cm->id&amp;sid=$sid", "view.php?id=$cm->id");
        }


    /******************* user delete ************************************/
    elseif ($action == 'userdelete' ) {

        if (empty($sid)) {
            error("User Delete: submission id missing");
            }

        if (!$submission = get_record("exercise_submissions", "id", $sid)) {
            error("User Delete: can not get submission record");
            }
        print_string("deleting", "exercise");
        // first get any assessments...
        if ($assessments = exercise_get_assessments($submission)) {
            foreach($assessments as $assessment) {
                // ...and all the associated records...
                delete_records("exercise_grades", "assessmentid", $assessment->id);
                echo ".";
                }
            // ...now delete the assessments...
            delete_records("exercise_assessments", "submissionid", $submission->id);
            }
        // ...and the submission record...
        delete_records("exercise_submissions", "id", $submission->id);
        // ..and finally the submitted file
        exercise_delete_submitted_files($exercise, $submission);
        add_to_log($course->id, "exercise", "delete", "view.php?id=$cm->id", "submission $submission->id");

        print_continue("view.php?id=$cm->id");
        }


    /*************** no man's land **************************************/

    else {

        error("Fatal Error: Unknown Action: ".$action."\n");

        }


    print_footer($course);

?>
