<?php  // $Id: view.php, v1.1 23 Aug 2003

/*************************************************
    ACTIONS handled are:

    displayfinalgrade (for students)
    notavailable (for students)
    studentsview
    submitexample 
    teachersview
    
************************************************/

    require("../../config.php");
    require("lib.php");
    require("locallib.php");
    
    require_variable($id);    // Course Module ID

    // get some useful stuff...
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }
    if (! $workshop = get_record("workshop", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);

    // ...log activity...
    add_to_log($course->id, "workshop", "view", "view.php?id=$cm->id", $workshop->id, $cm->id);

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");

    // ...display header...
    print_header_simple("$workshop->name", "",
                 "<a href=\"index.php?id=$course->id\">$strworkshops</a> -> $workshop->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strworkshop), navmenu($course, $cm));

    // ...and if necessary set default action 
    
    optional_variable($action);
    if (isteacher($course->id)) {
        if (empty($action)) { // no action specified, either go straight to elements page else the admin page
            // has the assignment any elements
            if (count_records("workshop_elements", "workshopid", $workshop->id) >= $workshop->nelements) {
                $action = "teachersview";
            }
            else {
                redirect("assessments.php?action=editelements&id=$cm->id");
            }
        }
    }
    elseif (!isguest()) { // it's a student then
        if (!$cm->visible) {
            notice(get_string("activityiscurrentlyhidden"));
        }
        if (time() < $workshop->submissionstart) { 
            $action = 'notavailable'; 
        } else if (time() < $workshop->assessmentend) {
            $action = 'studentsview';
        } else {
            $action = 'displayfinalgrade';
        }
    }
    else { // it's a guest, oh no!
        $action = 'notavailable';
    }


    /****************** display final grade (for students) ************************************/
    if ($action == 'displayfinalgrade' ) {

        // show the final grades as stored in the tables...
        print_heading_with_help(get_string("displayoffinalgrades", "workshop"), "finalgrades", "workshop");
        if ($submissions = workshop_get_user_submissions($workshop, $USER)) { // any submissions from user?
            echo "<center><table border=\"1\" width=\"90%\"><tr>";
            echo "<td><b>".get_string("submissions", "workshop")."</b></td>";
            if ($workshop->wtype) {
                echo "<td align=\"center\"><b>".get_string("assessmentsdone", "workshop")."</b></td>";
                echo "<td align=\"center\"><b>".get_string("gradeforassessments", "workshop")."</b></td>";
            }
            echo "<td align=\"center\"><b>".get_string("teacherassessments", "workshop", 
                        $course->teacher)."</b></td>";
            if ($workshop->wtype) {
                echo "<td align=\"center\"><b>".get_string("studentassessments", "workshop", 
                        $course->student)."</b></td>";
            }
            echo "<td align=\"center\"><b>".get_string("gradeforsubmission", "workshop")."</b></td>";
            echo "<td align=\"center\"><b>".get_string("overallgrade", "workshop")."</b></td></tr>\n";
            $gradinggrade = workshop_gradinggrade($workshop, $USER);
            foreach ($submissions as $submission) {
                $grade = workshop_submission_grade($workshop, $submission);
                echo "<tr><td>".workshop_print_submission_title($workshop, $submission)."</td>\n";
                if ($workshop->wtype) {
                    echo "<td align=\"center\">".workshop_print_user_assessments($workshop, $USER)."</td>";
                    echo "<td align=\"center\">$gradinggrade</td>";
                }
                echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, 
                            $submission, "teacher")."</td>";
                if ($workshop->wtype) {
                    echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, 
                            $submission, "student")."</td>";
                }
                echo "<td align=\"center\">$grade</td>";
                echo "<td align=\"center\">".number_format($gradinggrade + $grade, 1)."</td></tr>\n";
            }
        }
        echo "</table><br clear=\"all\" />\n";
        workshop_print_key($workshop);
        if ($workshop->showleaguetable) {
            workshop_print_league_table($workshop);
        }
    }   

    
    /****************** assignment not available (for students)***********************/
    elseif ($action == 'notavailable') {
        print_heading(get_string("notavailable", "workshop"));
    }


    /****************** student's view could be in 1 of 4 stages ***********************/
    elseif ($action == 'studentsview') {
        // is a password needed?
        if ($workshop->usepassword) {
            $correctpass = false;
            if (isset($_POST['userpassword'])) {
                if ($workshop->password == md5(trim($_POST['userpassword']))) {
                    $USER->workshoploggedin[$workshop->id] = true;
                    $correctpass = true;
                }
            } elseif (isset($USER->workshoploggedin[$workshop->id])) {
                $correctpass = true;
            }

            if (!$correctpass) {
                print_simple_box_start("center");
                echo "<form name=\"password\" method=\"post\" action=\"view.php\">\n";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
                echo "<table cellpadding=\"7px\">";
                if (isset($_POST['userpassword'])) {
                    echo "<tr align=\"center\" style='color:#DF041E;'><td>".get_string("wrongpassword", "workshop").
                        "</td></tr>";
                }
                echo "<tr align=\"center\"><td>".get_string("passwordprotectedworkshop", "workshop", $workshop->name).
                    "</td></tr>";
                echo "<tr align=\"center\"><td>".get_string("enterpassword", "workshop").
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
        workshop_print_assignment_info($workshop);
        // in Stage 1? - are there any teacher's submissions? and...
        // ...has student assessed the required number of the teacher's submissions 
        if ($workshop->ntassessments and (!workshop_test_user_assessments($workshop, $USER))) {
            print_heading(get_string("pleaseassesstheseexamplesfromtheteacher", "workshop", 
                        $course->teacher));
            workshop_list_teacher_submissions($workshop, $USER);
        }
        // in stage 2? - submit own first attempt
        else {
            if ($workshop->ntassessments) { 
                // show assessment the teacher's examples, there may be feedback from teacher
                print_heading(get_string("yourassessmentsofexamplesfromtheteacher", "workshop", 
                            $course->teacher));
                workshop_list_teacher_submissions($workshop, $USER);
            }
            // has user submitted anything yet? 
            if (!workshop_get_user_submissions($workshop, $USER)) {
                if (time() < $workshop->submissionend) {
                    // print upload form
                    print_heading(get_string("submitassignmentusingform", "workshop").":");
                    workshop_print_upload_form($workshop);
                } else {
                    print_heading(get_string("submissionsnolongerallowed", "workshop"));
                }
            }   
            // in stage 3? - grade other student's submissions, resubmit and list all submissions
            else {
                // is self assessment used in this workshop?
                if ($workshop->includeself) {
                    // prints a table if there are any submissions which have not been self assessed yet
                    workshop_list_self_assessments($workshop, $USER);
                }
                // if peer assessments are being done then show some  to assess...
                if ($workshop->nsassessments and ($workshop->assessmentstart > time() and $workshop->assessmentend < time())) {  
                    workshop_list_student_submissions($workshop, $USER);
                }
                // ..and any they have already done (and have gone cold)...
                if (workshop_count_user_assessments($workshop, $USER, "student")) {
                    print_heading(get_string("yourassessments", "workshop"));
                    workshop_list_assessed_submissions($workshop, $USER);
                }
                // list any assessments by teachers
                $timenow = time();
                if (workshop_count_teacher_assessments_by_user($workshop, $USER) and ($timenow > $workshop->releasegrades)) {
                    print_heading(get_string("assessmentsby", "workshop", $course->teachers));
                    workshop_list_teacher_assessments_by_user($workshop, $USER);
                }
                // ... and show peer assessments
                if (workshop_count_peer_assessments($workshop, $USER)) {
                    print_heading(get_string("assessmentsby", "workshop", $course->students));
                    workshop_list_peer_assessments($workshop, $USER);
                }
                // list previous submissions
                print_heading(get_string("submissions", "workshop"));
                workshop_list_user_submissions($workshop, $USER);
                // are resubmissions allowed and the workshop is in submission/assessment phase?
                if ($workshop->resubmit and (time() > $workshop->assessmentstart and time() < $workshop->submissionend)) {
                    // see if there are any cold assessments of the last submission
                    // if there are then print upload form
                    if ($submissions = workshop_get_user_submissions($workshop, $USER)) {
                        foreach ($submissions as $submission) {
                            $lastsubmission = $submission;
                            break;
                        }
                        $n = 0; // number of cold assessments (not include self assessments)
                        if ($assessments = workshop_get_assessments($lastsubmission)) {
                            foreach ($assessments as $assessment) {
                                if ($assessment->userid <> $USER->id) {
                                    $n++;
                                }
                            }
                        }
                        if ($n) {
                            echo "<hr size=\"1\" noshade=\"noshade\" />";
                            print_heading(get_string("submitrevisedassignment", "workshop").":");
                            workshop_print_upload_form($workshop);
                            echo "<hr size=\"1\" noshade=\"noshade\" />";
                        }
                    }
                }
            }
        }
    }


    /****************** submission of example by teacher only***********************/
    elseif ($action == 'submitexample') {
    
        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }
    
        workshop_print_assignment_info($workshop);
        
        // list previous submissions from teacher 
        workshop_list_user_submissions($workshop, $USER);
    
        echo "<hr size=\"1\" noshade=\"noshade\" />";
    
        // print upload form
        print_heading(get_string("submitexampleassignment", "workshop").":");
        workshop_print_upload_form($workshop);
    }


    /****************** teacher's view - display admin page  ************/
    elseif ($action == 'teachersview') {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }

        /// Check to see if groups are being used in this workshop
        /// and if so, set $currentgroup to reflect the current group
        $changegroup = isset($_GET['group']) ? $_GET['group'] : -1;  // Group change requested?
        $groupmode = groupmode($course, $cm);   // Groups are being used?
        $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);
        
        /// Allow the teacher to change groups (for this session)
        if ($groupmode) {
            if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {
                print_group_menu($groups, $groupmode, $currentgroup, "view.php?id=$cm->id");
            }
        }
        
        print_heading_with_help(get_string("managingassignment", "workshop"), "managing2", "workshop");
        
        workshop_print_assignment_info($workshop);
        
        echo "<center>\n";
        
        // if there are assessment elements show link to edit them
        if ($workshop->nelements) {
            echo "<br /><b><a href=\"assessments.php?id=$cm->id&amp;action=editelements\">".
                get_string("amendassessmentelements", "workshop")."</a></b> \n";
            helpbutton("elements", get_string("amendassessmentelements", "workshop"), "workshop");
        }
        
        // if teacher examples show submission and assessment links
        if ($workshop->ntassessments) { 
            // submission link for teacher examples
            echo "<br /><b><a href=\"view.php?id=$cm->id&amp;action=submitexample\">".
                get_string("submitexampleassignment", "workshop")."</a></b> \n";
            helpbutton("submissionofexamples", get_string("submitexampleassignment", "workshop"),
                    "workshop");
            // show assessment link for teachers examples only once there are such examples
            if ($n = workshop_count_teacher_submissions_for_assessment($workshop, $USER)) {
                echo "<br /><b><a href=\"submissions.php?id=$cm->id&amp;action=listforassessmentteacher\">".
                    get_string("teachersubmissionsforassessment", "workshop", $n)."</a></b> \n";
                helpbutton("assessmentofexamples", get_string("teachersubmissionsforassessment", 
                            "workshop"), "workshop");
            }
        }

        if ($workshop->wtype) {
            // only show grading assessments if there are grading grades involved
            if ($numberofassessments = workshop_count_ungraded_assessments($workshop)) {
                echo "<br /><b><a href=\"assessments.php?id=$cm->id&amp;action=gradeallassessments\">".
                    get_string("ungradedassessments", "workshop", 
                    $numberofassessments)."</a></b> \n";
                helpbutton("ungradedassessments", 
                    get_string("ungradedassessments", "workshop"), "workshop");
            }
        }

        // Show link to student submissions for assessment only if assessment has started
        if (time() > $workshop->assessmentstart) {
            if ($numberofsubmissions = workshop_count_student_submissions_for_assessment($workshop, $USER)) {
                echo "<br /><b><a href=\"submissions.php?id=$cm->id&amp;action=listforassessmentstudent\">".
                    get_string("studentsubmissionsforassessment", "workshop", 
                    $numberofsubmissions)."</a></b> \n";
                helpbutton("gradingsubmissions", 
                    get_string("studentsubmissionsforassessment", "workshop"), "workshop");
            }
        }
        
        // Show link to current grades
        if (time() > $workshop->assessmentstart) {
            if (time() < $workshop->assessmentend) {
                echo "<br /><b><a href=\"submissions.php?id=$cm->id&amp;action=displaycurrentgrades\">".
                        get_string("displayofcurrentgrades", "workshop")."</a></b> \n";
            } else {
                echo "<br /><b><a href=\"submissions.php?id=$cm->id&amp;action=displayfinalgrades\">".
                        get_string("displayoffinalgrades", "workshop")."</a></b> \n";
            }
        }

        echo "<br /><b><a href=\"submissions.php?id=$cm->id&amp;action=adminlist\">".
            get_string("administration")."</a></b> \n";
        
        echo '</center><br />';
    }
    
    
    /*************** no man's land **************************************/
    else {
        error("Fatal Error: Unknown Action: ".$action."\n");
    }

    print_footer($course);
    
?>
