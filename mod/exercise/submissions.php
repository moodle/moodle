<?PHP  // $Id: submissions.php,v 1.0 22 Aug 2003

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

    require("../../config.php");
    require("lib.php");
    require("version.php");

    require_variable($id);    // Course Module ID
    
    // get some essential stuff...
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $exercise = get_record("exercise", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");
    $strsubmissions = get_string("submissions", "exercise");

    // ... print the header and...
    print_header("$course->shortname: $exercise->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strexercises</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$exercise->name</A> -> $strsubmissions", 
                  "", "", true);

    //...get the action!
    require_variable($action);
    

    /******************* admin amend title ************************************/
    if ($action == 'adminamendtitle' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['sid'])) {
            error("Admin Amend Title: submission id missing");
            }
        
        $submission = get_record("exercise_submissions", "id", $_GET['sid']);
        print_heading(get_string("amendtitle", "exercise"));
        ?>
        <form name="amendtitleform" action="submissions.php" method="post">
        <input type="hidden" name="action" value="adminupdatetitle">
        <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
        <input type="hidden" name="sid" value="<?PHP echo $_REQUEST['sid'] ?>">
        <center>
        <table celpadding="5" border="1">
        <?PHP

        // now get the comment
        echo "<tr valign=\"top\">\n";
        echo "    <td align=\"right\"><P><B>". get_string("title", "exercise").":</b></p></td>\n";
        echo "    <td>\n";
        echo "        <input type=\"text\" name=\"title\" size=\"60\" maxlength=\"100\" value=\"$submission->title\">\n";
        echo "    </td></tr></table>\n";
        echo "<input type=submit VALUE=\"".get_string("amendtitle", "exercise")."\">\n";
        echo "</center></form>\n";

        }
    

    /******************* admin clear late (flag) ************************************/
    elseif ($action == 'adminclearlate' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }
        if (empty($_GET['sid'])) {
            error("Admin clear late flag: submission id missing");
        }
    
        if (!$submission = get_record("exercise_submissions", "id", $_GET['sid'])) {
            error("Admin clear late flag: can not get submission record");
        }
        if (set_field("exercise_submissions", "late", 0, "id", $_GET['sid'])) {
            print_heading(get_string("clearlateflag", "exercise")." ".get_string("ok"));
        }
        
        add_to_log($course->id, "exercise", "late flag cleared", "view.php?id=$cm->id", "submission $submission->id");
        
        print_continue("submissions.php?id=$cm->id&action=adminlist");
    }
    

    /******************* admin confirm delete ************************************/
    elseif ($action == 'adminconfirmdelete' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['sid'])) {
            error("Admin confirm delete: submission id missing");
            }
        if (!$submission = get_record("exercise_submissions", "id", $_GET['sid'])) {
            error("Admin delete: can not get submission record");
            }

        if (isteacher($course->id, $submission->userid)) {
            if (!isteacheredit($course->id)) {
                error("Only teacher with editing permissions can delete teacher submissions.");
            }
        }
        notice_yesno(get_string("confirmdeletionofthisitem","exercise", get_string("submission", "exercise")), 
             "submissions.php?action=admindelete&id=$cm->id&sid=$_GET[sid]", "submissions.php?id=$cm->id&action=adminlist");
        }
    

    /******************* admin delete ************************************/
    elseif ($action == 'admindelete' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['sid'])) {
            error("Admin delete: submission id missing");
            }
    
        if (!$submission = get_record("exercise_submissions", "id", $_GET['sid'])) {
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
        
        print_continue("submissions.php?id=$cm->id&action=adminlist");
        }
    

    /******************* admin (confirm) late flag ************************************/
    elseif ($action == 'adminlateflag' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['sid'])) {
            error("Admin confirm late flag: submission id missing");
            }
        if (!$submission = get_record("exercise_submissions", "id", $_GET['sid'])) {
            error("Admin confirm late flag: can not get submission record");
            }

        notice_yesno(get_string("clearlateflag","exercise")."?", 
             "submissions.php?action=adminclearlate&id=$cm->id&sid=$_GET[sid]", 
             "submissions.php?id=$cm->id&action=adminlist");
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
        if (empty($_POST['sid'])) {
            error("Admin Update Title: submission id missing");
            }
    
        if (set_field("exercise_submissions", "title", $_POST['title'], "id", $_POST['sid'])) {
            print_heading(get_string("amendtitle", "exercise")." ".get_string("ok"));
            }
        print_continue("submissions.php?id=$cm->id&action=adminlist");
        }
    

    /*************** display final grades (by teacher) ***************************/
    elseif ($action == 'displayfinalgrades') {
        // Get all the students
        if (!$users = get_course_students($course->id, "u.firstname, u.lastname")) {
            print_heading(get_string("nostudentsyet"));
            print_footer($course);
            exit;
        }
        
        // get the final weights from the database
        $teacherweight = get_field("exercise","teacherweight", "id", $exercise->id);
        $gradingweight = get_field("exercise","gradingweight", "id", $exercise->id);
        // show the final grades as stored in the tables...
        print_heading_with_help(get_string("displayoffinalgrades", "exercise"), "finalgrades", "exercise");
        echo "<center><table border=\"1\" width=\"90%\"><tr>\n";
        echo "<td bgcolor=\"$THEME->cellheading2\"><b>".$course->student."</b></td>";
        echo "<td bgcolor=\"$THEME->cellheading2\"><b>".get_string("submission", "exercise")."</b></td>";
        echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforassessment", "exercise")."</b></td>";
        echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforsubmission", "exercise")."</b></td>";
        echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("overallgrade", "exercise")."</b></td></TR>\n";
        // now the weights
        echo "<tr><td bgcolor=\"$THEME->cellheading2\"><b>".get_string("weights", "exercise")."</b></td>";
        echo "<td bgcolor=\"$THEME->cellheading2\"><b>&nbsp;</b></td>\n";
        echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>$EXERCISE_FWEIGHTS[$gradingweight]</b></td>\n";
        echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>$EXERCISE_FWEIGHTS[$teacherweight]</b></td>\n";
        echo "<td bgcolor=\"$THEME->cellheading2\"><b>&nbsp;</b></td></tr>\n";
        foreach ($users as $user) {
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
                            $grade = number_format($assessment->grade * $exercise->grade / 100.0, 1);
                            $overallgrade = number_format(((($assessment->grade * 
                                $EXERCISE_FWEIGHTS[$teacherweight] / 100.0) + 
                                ($ownassessment->gradinggrade * $EXERCISE_FWEIGHTS[$gradingweight]/
                                COMMENTSCALE )) * $exercise->grade) / ($EXERCISE_FWEIGHTS[$teacherweight] + 
                                $EXERCISE_FWEIGHTS[$gradingweight]), 1);
                            if ($submission->late) {
                                $grade = "<font color=\"red\">(".$grade.")</font>";
                                $overallgrade = "<font color=\"red\">(".$overallgrade.")</font>";
                            }
                            echo "<tr><td>$user->firstname $user->lastname</td>\n";
                            echo "<td>".exercise_print_submission_title($exercise, $submission)."</td>\n";
                            echo "<td align=\"center\">".number_format($ownassessment->gradinggrade * $exercise->grade / COMMENTSCALE, 1)."</td>";
                            echo "<td align=\"center\">$grade</td>";
                            echo "<td align=\"center\">$overallgrade</td></tr>\n";
                        }
                    }
                }
            }
        }
        echo "</table><br clear=\"all\">\n";
        if ($exercise->showleaguetable) {
            exercise_print_league_table($exercise);
            echo "<br />\n";
        }
        print_string("allgradeshaveamaximumof", "exercise", $exercise->grade)."\n";
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
    
        redirect("submissions.php?id=$cm->id&action=adminlist", get_string("entriessaved", "exercise"));
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
    
        redirect("submissions.php?id=$cm->id&action=adminlist", get_string("weightssaved", "exercise"));
        }
                
    
    /******************* user confirm delete ************************************/
    elseif ($action == 'userconfirmdelete' ) {

        if (empty($_GET['sid'])) {
            error("User Confirm Delete: submission id missing");
            }
            
        notice_yesno(get_string("confirmdeletionofthisitem","exercise", get_string("submission", "exercise")), 
             "submissions.php?action=userdelete&id=$cm->id&sid=$_GET[sid]", "view.php?id=$cm->id");
        }
    

    /******************* user delete ************************************/
    elseif ($action == 'userdelete' ) {

        if (empty($_GET['sid'])) {
            error("User Delete: submission id missing");
            }
    
        if (!$submission = get_record("exercise_submissions", "id", $_GET['sid'])) {
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
