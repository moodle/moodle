<?php  // $Id$

/*************************************************
    ACTIONS handled are:

    displayfinalgrade (for students)
    makeleaguetableavailable (for teachers)
    notavailable (for students)
    openexercise (for teachers)
    setupassignment (for teachers)
    showsubmissions (for students)
    studentsview
    submitassignment
    teachersview

************************************************/

    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");

    $id          = required_param('id', PARAM_INT);    // Course Module ID
    $action      = optional_param('action', '', PARAM_ALPHA);
    $changegroup = optional_param('group', -1, PARAM_INT);

    // get some esential stuff...
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

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // ...log activity...
    add_to_log($course->id, "exercise", "view", "view.php?id=$cm->id", $exercise->id, $cm->id);

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");

    // ...display header...
    $navigation = build_navigation('', $cm);
    
    print_header_simple(format_string($exercise->name), "", $navigation,
                  "", "", true, update_module_button($cm->id, $course->id, $strexercise), navmenu($course, $cm));

    if (isteacher($course->id)) {
        if (empty($action)) { // no action specified, either go straight to elements page else the admin page
            // has the assignment any elements
            if (count_records("exercise_elements", "exerciseid", $exercise->id)) {
                $action = "teachersview";
            }
            else {
                redirect("assessments.php?action=editelements&amp;id=$cm->id");
            }
        }
    }
    elseif (!isguest()) { // it's a student then
        if (!$cm->visible) {
            notice(get_string("activityiscurrentlyhidden"));
        }
        switch ($exercise->phase) {
            case 0 :
            case 1 : $action = 'notavailable'; break;
            case 2 : $action = 'studentsview'; break;
            case 3 : $action = 'displayfinalgrade';
        }
    }
    else { // it's a guest, oh no!
        $action = 'notavailable';
    }


    /****************** display final grade (for students) ************************************/
    if ($action == 'displayfinalgrade' ) {

        // show the final grades as stored in the tables...
        print_heading(get_string("displayoffinalgrades", "exercise"));
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) { // any submissions from user?
            echo "<center><table border=\"1\" width=\"90%\"><tr>";
            echo "<td><b>".get_string("submissions", "exercise")."</b></td>";
            echo "<td align=\"center\"><b>".get_string("gradeforassessment", "exercise")."</b></td>";
            echo "<td align=\"center\"><b>".get_string("gradeforsubmission", "exercise", $course->teacher)."</b></td>";
            echo "<td align=\"center\"><b>".get_string("overallgrade", "exercise")."</b></td></tr>\n";
            // now the weights
            echo "<tr><td><b>".get_string("maximumgrade")."</b></td>";
            echo "<td align=\"center\"><b>$exercise->gradinggrade</b></td>\n";
            echo "<td align=\"center\"><b>$exercise->grade</b></td>\n";
            echo "<td><b>&nbsp;</b></td></tr>\n";
            // first get user's own assessment reord, it should contain their grading grade
            if ($ownassessments = exercise_get_user_assessments($exercise, $USER)) {
                foreach ($ownassessments as $ownassessment) {
                    break; // there should only be one
                }
            }
            else {
                $ownassessment->gradinggrade = 0;
            }
            foreach ($submissions as $submission) {
                if ($assessments = exercise_get_assessments($submission)) {
                    foreach ($assessments as $assessment) { // (normally there should only be one
                        $gradinggrade = number_format($ownassessment->gradinggrade * $exercise->gradinggrade / 100.0,
                                1);
                        $grade = number_format($assessment->grade * $exercise->grade / 100.0, 1);
                        $overallgrade = number_format(($assessment->grade * $exercise->grade / 100.0) +
                            ($ownassessment->gradinggrade * $exercise->gradinggrade / 100.0 ), 1);
                        if ($submission->late) {
                            $grade = "<font color=\"red\">(".$grade.")</font>";
                            $overallgrade = "<font color=\"red\">(".$overallgrade.")</font>";
                        }
                        echo "<tr><td>".exercise_print_submission_title($exercise, $submission)."</td>\n";
                        echo "<td align=\"center\">$gradinggrade</td>";
                        echo "<td align=\"center\">$grade</td>";
                        echo "<td align=\"center\">$overallgrade</td></tr>\n";
                    }
                }
            }
        }
        echo "</table></center><br clear=\"all\" />\n";
        if ($exercise->showleaguetable) {
            exercise_print_league_table($exercise);
        }
        echo "<br />".get_string("maximumgrade").": $exercise->grade<br />\n";
    }


    /****************** make final grades available (for teachers only)**************/
    elseif ($action == 'makeleaguetableavailable') {

        if (!isteacheredit($course->id)) {
            error("Only teachers with editing permissions can do this.");
        }

        set_field("exercise", "phase", 3, "id", "$exercise->id");
        add_to_log($course->id, "exercise", "display", "view.php?id=$cm->id", "$exercise->id", $cm->id);
        redirect("view.php?id=$cm->id", get_string("movingtophase", "exercise", 3));
    }


    /*********************** assignment not available (for students)***********************/
    elseif ($action == 'notavailable') {
        print_heading(get_string("notavailable", "exercise"));
    }


    /****************** open exercise for student assessments and submissions (phase 2) (for teachers)**/
    elseif ($action == 'openexercise') {

        if (!isteacheredit($course->id)) {
            error("Only teachers with editing permissions can do this.");
        }

        // move to phase 2, check that teacher has made enough submissions
        if (exercise_count_teacher_submissions($exercise) == 0) {
            redirect("view.php?id=$cm->id", get_string("noexercisedescriptionssubmitted", "exercise"));
            }
        elseif (($exercise->gradingstrategy == 3) and ($exercise->nelements ==1 )) {
            // only one criterion specified
            redirect("view.php?id=$cm->id", get_string("numberofcriterionelements", "exercise"));
        } else {
            set_field("exercise", "phase", 2, "id", "$exercise->id");
            add_to_log($course->id, "exercise", "open", "view.php?id=$cm->id", "$exercise->id", $cm->id);
            redirect("view.php?id=$cm->id", get_string("movingtophase", "exercise", 2));
        }
    }


    /****************** set up assignment (move back to phase 1) (for teachers)***********************/
    elseif ($action == 'setupassignment') {

        if (!isteacher($course->id)) {
            error("Only teachers with editing permissions can do this.");
        }

        set_field("exercise", "phase", 1, "id", "$exercise->id");
        add_to_log($course->id, "exercise", "set up", "view.php?id=$cm->id", "$exercise->id", $cm->id);
        redirect("view.php?id=$cm->id", get_string("movingtophase", "exercise", 1));
    }


    /****************** showsubmissions (for students, in phase 3)***********************/
    elseif ($action == 'showsubmissions') {
        exercise_print_assignment_info($exercise);
        print_heading(get_string("submissionsnowclosed", "exercise"));
        // show student's assessment (linked to the teacher's exercise/submission
        print_heading(get_string("yourassessment", "exercise"));
        exercise_list_teacher_submissions($exercise, $USER);
        echo "<hr size=\"1\" noshade=\"noshade\" />";
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) {
            print_heading(get_string("yoursubmission", "exercise"));
            print_simple_box_start("center");
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
                                "<a href=\"assessments.php?action=viewassessment&amp;id=$cm->id&amp;aid=$assessment->id\">".$assessment->grade * $exercise->grade / 100.0."</a>");
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
            print_heading(get_string("nosubmissions", "exercise"));
        }
        // always allow student to resubmit
        if (exercise_test_for_resubmission($exercise, $USER)) {
            // if resubmission requested print upload form
            echo "<hr size=\"1\" noshade=\"noshade\" />";
            print_heading(get_string("pleasesubmityourwork", "exercise").":");
            exercise_print_upload_form($exercise);
        }
        echo "<hr size=\"1\" noshade=\"noshade\" />";
    }


    /****************** student's view could be in 1 of 3 stages ***********************/
    elseif ($action == 'studentsview') {
        exercise_print_assignment_info($exercise);
        // is a password needed?
        if ($exercise->usepassword) {
            $correctpass = false;
            if (isset($_POST['userpassword'])) {
                if ($exercise->password == md5(trim($_POST['userpassword']))) {
                    $USER->exerciseloggedin[$exercise->id] = true;
                    $correctpass = true;
                }
            } elseif (isset($USER->exerciseloggedin[$exercise->id])) {
                $correctpass = true;
            }

            if (!$correctpass) {
                print_simple_box_start("center");
                echo "<form id=\"password\" method=\"post\" action=\"view.php\">\n";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
                echo "<table cellpadding=\"7px\">";
                if (isset($_POST['userpassword'])) {
                    echo "<tr align=\"center\" style='color:#DF041E;'><td>".get_string("wrongpassword", "exercise").
                        "</td></tr>";
                }
                echo "<tr align=\"center\"><td>".get_string("passwordprotectedexercise", "exercise", format_string($exercise->name)).
                    "</td></tr>";
                echo "<tr align=\"center\"><td>".get_string("enterpassword", "exercise").
                    " <input type=\"password\" name=\"userpassword\" /></td></tr>";

                echo "<tr align=\"center\"><td>";
                echo "<input type=\"button\" value=\"".get_string("cancel").
                    "\" onclick=\"parent.location='../../course/view.php?id=$course->id';\">  ";
                echo "<input type=\"button\" value=\"".get_string("continue").
                    "\" onclick=\"document.password.submit();\" />";
                echo "</td></tr></table>";
                print_simple_box_end();
                exit();
            }
        }
        // in Stage 1 - the student must make an assessment (linked to the teacher's exercise/submission
        if (!exercise_test_user_assessments($exercise, $USER)) {
            print_heading(get_string("pleaseviewtheexercise", "exercise", $course->teacher));
            exercise_list_teacher_submissions($exercise, $USER);
        }
        // in stage 2? - submit own first attempt
        else {
            // show assessment the teacher's examples, there may be feedback from teacher
            if (exercise_count_user_submissions($exercise, $USER) == 0) {
                print_heading(get_string("atthisstageyou", "exercise", $course->teacher));
                exercise_list_teacher_submissions($exercise, $USER, true);  // true = allow re-assessing
                // print upload form
                print_heading(get_string("pleasesubmityourwork", "exercise").":");
                exercise_print_upload_form($exercise);
            }
            // in stage 3? - awaiting grading of assessment and assessment of work by teacher,
            // may resubmit if allowed
            else {
                exercise_list_teacher_submissions($exercise, $USER);
                echo "<hr size=\"1\" noshade=\"noshade\" />";
                print_heading(get_string("yoursubmission", "exercise"));
                exercise_list_user_submissions($exercise, $USER);
                if (exercise_test_for_resubmission($exercise, $USER)) {
                    // if resubmission requested print upload form
                    echo "<hr size=\"1\" noshade=\"noshade\" />";
                    print_heading(get_string("pleasesubmityourwork", "exercise").":");
                    exercise_print_upload_form($exercise);
                    echo "<hr size=\"1\" noshade=\"noshade\" />";
                }
            }
        }
    }


    /****************** submission of assignment by teacher only***********************/
    elseif ($action == 'submitassignment') {

        if (!has_capability('mod/exercise:assess', $context)) {
            error("Only teachers with editing permissions can do this.");
        }

        exercise_print_assignment_info($exercise);

        // list previous submissions from this user
        exercise_list_user_submissions($exercise, $USER);

        echo "<hr size=\"1\" noshade=\"noshade\" />";

        // print upload form
        print_heading(get_string("submitexercisedescription", "exercise").":");
        exercise_print_upload_form($exercise);
    }


    /****************** teacher's view - display admin page (current phase options) ************/
    elseif ($action == 'teachersview') {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }

        /// Check to see if groups are being used in this exercise
        /// and if so, set $currentgroup to reflect the current group
        $groupmode = groupmode($course, $cm);   // Groups are being used?
        $currentgroup = setup_and_print_groups($course, $groupmode, "view.php?id=$cm->id");

        print_heading_with_help(get_string("managingassignment", "exercise"), "managing", "exercise");

        exercise_print_assignment_info($exercise);
        $tabs->names = array("1. ".get_string("phase1", "exercise"),
            "2. ".get_string("phase2", "exercise", $course->student),
            "3. ".get_string("phase3", "exercise", $course->student));
        if (isteacheredit($course->id)) {
            $tabs->urls = array("view.php?id=$cm->id&amp;action=setupassignment",
                "view.php?id=$cm->id&amp;action=openexercise",
                "view.php?id=$cm->id&amp;action=makeleaguetableavailable");
        } else {
            // don't allow non-editing teacher to change phase
            $tabs->urls = array("view.php?id=$cm->id",
                "view.php?id=$cm->id",
                "view.php?id=$cm->id");
        }
        if ($exercise->phase) { // phase 1 or more
            $tabs->highlight = $exercise->phase - 1;
            } else {
            $tabs->highlight = 0; // phase is zero
            }
        exercise_print_tabbed_heading($tabs);

        echo "<center>\n";
            switch ($exercise->phase) {
                case 0:
                case 1: // set up assignment
                    if (isteacheredit($course->id)) {
                        echo "<p><b><a href=\"assessments.php?id=$cm->id&amp;action=editelements\">".
                            get_string("amendassessmentelements", "exercise")."</a></b></p> \n";
                        helpbutton("elements", get_string("amendassessmentelements", "exercise"), "exercise");
                        echo "<p><b><a href=\"view.php?id=$cm->id&amp;action=submitassignment\">".
                            get_string("submitexercisedescription", "exercise")."</a></b></p> \n";
                        helpbutton("submissionofdescriptions", get_string("submitexercisedescription", "exercise"), "exercise");
                    }
                    break;

                case 2: // submissions and assessments
                    // just show student submissions link, the (self) assessments are show above the assessment form for
                    // the submissions
                    echo "<p><b><a href=\"submissions.php?id=$cm->id&amp;action=listforassessmentstudent\">".
                          get_string("studentsubmissionsforassessment", "exercise",
                          exercise_count_unassessed_student_submissions($exercise))."</a></b></p> \n";
                    helpbutton("grading", get_string("studentsubmissionsforassessment", "exercise"),
                            "exercise");
                    break;

                case 3: // show final grades
                    echo "<p><b><a href=\"submissions.php?id=$cm->id&amp;action=listforassessmentstudent\">".
                          get_string("studentsubmissionsforassessment", "exercise",
                          exercise_count_unassessed_student_submissions($exercise))."</a></b></p> \n";
                    helpbutton("grading", get_string("studentsubmissionsforassessment", "exercise"),
                            "exercise");
                    print_heading("<a href=\"submissions.php?id=$cm->id&amp;action=displayfinalgrades\">".
                          get_string("displayoffinalgrades", "exercise")."</a>");
        }
        print_heading("<a href=\"submissions.php?id=$cm->id&amp;action=adminlist\">".
            get_string("administration")."</a>");
       echo "</center>\n";
    }


    /*************** no man's land **************************************/
    else {
        error("Fatal Error: Unknown Action: ".$action."\n");
    }

    print_footer($course);

?>
