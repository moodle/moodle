<?php  // $Id: lib.php,v 1.1 22 Aug 2003

/*************************************************
    ACTIONS handled are:

	adminamendtitle
    adminconfirmdelete
	admindelete
	adminlist
    displayfinalgrades (teachers only)
    listallsubmissions
    listforassessmentstudent
    listforassessmentteacher
    userconfirmdelete
    userdelete
    

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

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strsubmissions = get_string("submissions", "workshop");

    // ... print the header and...
    print_header_simple("$workshop->name", "",
                 "<a HREF=index.php?id=$course->id>$strworkshops</a> -> 
                  <a HREF=\"view.php?id=$cm->id\">$workshop->name</a> -> $strsubmissions", 
                  "", "", true);

    //...get the action or set up an suitable default
    optional_variable($action);
    if (empty($action)) {
        $action = "listallsubmissions";
        }


    /******************* admin amend title ************************************/
    if ($action == 'adminamendtitle' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['sid'])) {
            error("Admin Amend Title: submission id missing");
            }
        
        $submission = get_record("workshop_submissions", "id", $_GET['sid']);
        print_heading(get_string("amendtitle", "workshop"));
        ?>
        <form name="amendtitleform" action="submissions.php" method="post">
        <input type="hidden" name="action" value="adminupdatetitle" />
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="sid" value="<?php echo $_REQUEST['sid'] ?>" />
        <center>
        <table celpadding="5" border="1">
        <?php

        // now get the comment
        echo "<tr valign=\"top\">\n";
        echo "  <td align=\"right\"><p><b>". get_string("title", "workshop").":</b></p></td>\n";
        echo "  <td>\n";
        echo "      <input type=\"text\" name=\"title\" size=\"60\" maxlength=\"100\" value=\"$submission->title\" />\n";
        echo "  </td></tr></table>\n";
        echo "<input type=submit VALUE=\"".get_string("amendtitle", "workshop")."\" />\n";
        echo "</center></form>\n";

        }
    

    /******************* admin confirm delete ************************************/
    elseif ($action == 'adminconfirmdelete' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['sid'])) {
            error("Admin confirm delete: submission id missing");
            }
            
        notice_yesno(get_string("confirmdeletionofthisitem","workshop", get_string("submission", "workshop")), 
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
    
        if (!$submission = get_record("workshop_submissions", "id", $_GET['sid'])) {
            error("Admin delete: can not get submission record");
            }
        print_string("deleting", "workshop");
        // first get any assessments...
        if ($assessments = workshop_get_assessments($submission, 'ALL')) {
            foreach($assessments as $assessment) {
                // ...and all the associated records...
                delete_records("workshop_comments", "assessmentid", $assessment->id);
                delete_records("workshop_grades", "assessmentid", $assessment->id);
                echo ".";
                }
            // ...now delete the assessments...
            delete_records("workshop_assessments", "submissionid", $submission->id);
            }
        // ...and the submission record...
        delete_records("workshop_submissions", "id", $submission->id);
        // ..and finally the submitted file
        workshop_delete_submitted_files($workshop, $submission);
        
        print_continue("submissions.php?id=$cm->id&action=adminlist");
        }
    

    /******************* list all submissions ************************************/
    elseif ($action == 'adminlist' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['order'])) {
            $order = "name";
            }
        else {
            $order = $_GET['order'];
            }
            
        workshop_list_submissions_for_admin($workshop, $order);
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
    
        if (set_field("workshop_submissions", "title", $_POST['title'], "id", $_POST['sid'])) {
            print_heading(get_string("amendtitle", "workshop")." ".get_string("ok"));
            }
        print_continue("submissions.php?id=$cm->id&action=adminlist");
        }
    

	/*************** display final grades (by teacher) ***************************/
	elseif ($action == 'displayfinalgrades') {

        if (groupmode($course, $cm) == SEPARATEGROUPS) {
            $groupid = get_current_group($course->id);
        } else {
            $groupid = 0;
        }
		// Get all the students
		if (!$users = get_course_students($course->id, "u.lastname, u.firstname")) {
			print_heading(get_string("nostudentsyet"));
			print_footer($course);
			exit;
		}
		
		// show the final grades as stored in the tables...
		print_heading_with_help(get_string("displayoffinalgrades", "workshop"), "finalgrades", "workshop");
		echo "<center><table border=\"1\" width=\"90%\"><tr>
			<td bgcolor=\"$THEME->cellheading2\"><b>".$course->student."</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\"><b>".get_string("submission", "workshop")."</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsdone", "workshop").
                "</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforassessments", 
                "workshop")."</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsby", "workshop", 
                $course->teachers)."</b></td>";
	    echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsby", "workshop", 
                $course->students)."</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforsubmission", 
                "workshop")."</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("overallgrade", "workshop").
                "</b></td></tr>\n";

        foreach ($users as $user) {
            // skip if student not in group
            if ($groupid) {
                if (!ismember($groupid, $user->id)) {
                    continue;
                }
            }
			if ($submissions = workshop_get_user_submissions($workshop, $user)) {
                $gradinggrade = workshop_gradinggrade($workshop, $user);
				foreach ($submissions as $submission) {
                    $grade = workshop_submission_grade($workshop, $submission);
					echo "<tr><td>$user->firstname $user->lastname</td>";
					echo "<td>".workshop_print_submission_title($workshop, $submission)."</td>\n";
					echo "<td align=\"center\">".workshop_print_user_assessments($workshop, $user)."</td>";
					echo "<td align=\"center\">$gradinggrade</td>";
					echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, $submission, 
                            "teacher")."</td>";
					echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, $submission, 
                            "student")."</td>";
					echo "<td align=\"center\">$grade</td>";
					echo "<td align=\"center\">".number_format($gradinggrade + $grade, 1)."</td></tr>\n";
				}
			}
		}
		echo "</table><br clear=\"all\">\n";
        workshop_print_key($workshop);
		if ($workshop->showleaguetable) {
			workshop_print_league_table($workshop);
			if ($workshop->anonymous) {
				echo "<p>".get_string("namesnotshowntostudents", "workshop", $course->students)."</p>\n";
			}
		}
		echo "<p>".get_string("allgradeshaveamaximumof", "workshop", $workshop->grade)."</p>\n";
		print_continue("view.php?id=$cm->id");
	}


	/******************* list all submissions ************************************/
	elseif ($action == 'listallsubmissions' ) {
		if (!$users = get_course_students($course->id)) {
			print_heading(get_string("nostudentsyet"));
			print_footer($course);
			exit;
			}
		print_heading(get_string("listofallsubmissions", "workshop").":", "CENTER");
		workshop_list_all_submissions($workshop, $USER);
		print_continue("view.php?id=$cm->id");
		
		}
	

	/******************* list for assessment student (submissions) ************************************/
	elseif ($action == 'listforassessmentstudent' ) {
		if (!$users = get_course_students($course->id)) {
			print_heading(get_string("nostudentsyet"));
			print_footer($course);
			exit;
			}
		workshop_list_unassessed_student_submissions($workshop, $USER);
		print_continue("view.php?id=$cm->id");
		
		}
	

	/******************* list for assessment teacher (submissions) ************************************/
	elseif ($action == 'listforassessmentteacher' ) {
   
        if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		workshop_list_unassessed_teacher_submissions($workshop, $USER);
		print_continue("view.php?id=$cm->id");
		
		}
	

    /*************** update (league table options teacher) ***************************/
    elseif ($action == 'updateleaguetable') {
        
        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
        }

        $form = (object)$_POST;
        
        // save number of entries in showleaguetable option
        if ($form->nentries == 'All') {
            $form->nentries = 99;
            }
        set_field("workshop", "showleaguetable", $form->nentries, "id", "$workshop->id");
        
        // save the anonymous option
        set_field("workshop", "anonymous", $form->anonymous, "id", "$workshop->id");
        add_to_log($course->id, "workshop", "league table", "view.php?id=$cm->id", $form->nentries, $cm->id);

        redirect("submissions.php?action=adminlist&id=$cm->id");
        }


    /******************* user confirm delete ************************************/
    elseif ($action == 'userconfirmdelete' ) {

        if (empty($_GET['sid'])) {
            error("User Confirm Delete: submission id missing");
            }
            
        notice_yesno(get_string("confirmdeletionofthisitem","workshop", get_string("submission", "workshop")), 
             "submissions.php?action=userdelete&id=$cm->id&sid=$_GET[sid]", "view.php?id=$cm->id");
        }
    

    /******************* user delete ************************************/
    elseif ($action == 'userdelete' ) {

        if (empty($_GET['sid'])) {
            error("User Delete: submission id missing");
            }
    
        if (!$submission = get_record("workshop_submissions", "id", $_GET['sid'])) {
            error("User Delete: can not get submission record");
            }
        print_string("deleting", "workshop");
        // first get any assessments...
        if ($assessments = workshop_get_assessments($submission, 'ALL')) {
            foreach($assessments as $assessment) {
                // ...and all the associated records...
                delete_records("workshop_comments", "assessmentid", $assessment->id);
                delete_records("workshop_grades", "assessmentid", $assessment->id);
                echo ".";
                }
            // ...now delete the assessments...
            delete_records("workshop_assessments", "submissionid", $submission->id);
            }
        // ...and the submission record...
        delete_records("workshop_submissions", "id", $submission->id);
        // ..and finally the submitted file
        workshop_delete_submitted_files($workshop, $submission);
        
        print_continue("view.php?id=$cm->id");
        }
    

    /*************** no man's land **************************************/

    else {

        error("Fatal Error: Unknown Action: ".$action."\n");

        }


    print_footer($course);
 
?>

