<?php  // $Id: lib.php,v 1.1 22 Aug 2003

/*************************************************
    ACTIONS handled are:

    dminamendtitle
    adminconfirmdelete
	admindelete
	adminlist
    displayfinalgrades (teachers only)
    editsubmission
    listallsubmissions
    listforassessmentstudent
    listforassessmentteacher
    showsubmission
    updatesubmission
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
                 "<a href=\"index.php?id=$course->id\">$strworkshops</a> -> 
                  <a href=\"view.php?id=$cm->id\">$workshop->name</a> -> $strsubmissions", 
                  "", "", true);

    //...get the action or set up an suitable default
    optional_variable($action);
    if (empty($action)) {
        $action = "listallsubmissions";
        }


/******************* admin amend title ************************************/
    elseif ($action == 'adminamendtitle' ) {

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
        echo "<input type=\"submit\" value=\"".get_string("amendtitle", "workshop")."\" />\n";
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
    
        if (!$submission = get_record("workshop_submissions", "id", $_GET['sid'])) {
            error("Admin clear late flag: can not get submission record");
        }
        if (set_field("workshop_submissions", "late", 0, "id", $_GET['sid'])) {
            print_heading(get_string("clearlateflag", "workshop")." ".get_string("ok"));
        }
        
        add_to_log($course->id, "workshop", "late flag cleared", "view.php?id=$cm->id", "submission $submission->id");
        
        redirect("submissions.php?id=$cm->id&amp;action=adminlist");
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
             "submissions.php?action=admindelete&amp;id=$cm->id&amp;sid=$_GET[sid]", "submissions.php?id=$cm->id&amp;action=adminlist");
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
        
        print_continue("submissions.php?id=$cm->id&amp;action=adminlist");
        }
    

    /******************* admin (confirm) late flag ************************************/
    elseif ($action == 'adminlateflag' ) {

        if (!isteacher($course->id)) {
            error("Only teachers can look at this page");
            }
        if (empty($_GET['sid'])) {
            error("Admin confirm late flag: submission id missing");
            }
        if (!$submission = get_record("workshop_submissions", "id", $_GET['sid'])) {
            error("Admin confirm late flag: can not get submission record");
            }

        notice_yesno(get_string("clearlateflag","workshop")."?", 
             "submissions.php?action=adminclearlate&amp;id=$cm->id&amp;sid=$_GET[sid]", 
             "submissions.php?id=$cm->id&amp;action=adminlist");
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
        print_continue("submissions.php?id=$cm->id&amp;action=adminlist");
        }
    

    /******************* confirm remove attachments ************************************/
    elseif ($action == 'confirmremoveattachments' ) {

        if (empty($_GET['sid'])) {
            error("Admin confirm delete: submission id missing");
            }
        if (!$submission = get_record("workshop_submissions", "id", $_GET['sid'])) {
            error("Admin delete: can not get submission record");
            }

        notice_yesno(get_string("confirmremoveattachments","workshop"), 
             "submissions.php?action=removeattachments&amp;id=$cm->id&amp;sid=$_GET[sid]", 
             "view.php?id=$cm->id");
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
		if ($workshop->wtype) {
            echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsdone", "workshop").
                "</b></td>";
		    echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforassessments", 
                "workshop")."</b></td>";
        }
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsby", "workshop", 
                $course->teachers)."</b></td>";
	    if ($workshop->wtype) {
            echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsby", "workshop", 
                $course->students)."</b></td>";
        }
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
					if ($workshop->wtype) {
                        echo "<td align=\"center\">".workshop_print_user_assessments($workshop, $user)."</td>";
					    echo "<td align=\"center\">$gradinggrade</td>";
                    }
					echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, $submission, 
                            "teacher")."</td>";
					if ($workshop->wtype) {
                        echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, $submission, 
                            "student")."</td>";
                    }
					echo "<td align=\"center\">$grade</td>";
					echo "<td align=\"center\">".number_format($gradinggrade + $grade, 1)."</td></tr>\n";
				}
			}
		}
		echo "</table><br clear=\"all\" />\n";
        workshop_print_key($workshop);
		if ($workshop->showleaguetable) {
			workshop_print_league_table($workshop);
			if ($workshop->anonymous) {
				echo "<p>".get_string("namesnotshowntostudents", "workshop", $course->students)."</p>\n";
			}
		}
		print_continue("view.php?id=$cm->id");
	}


    /******************* edit submission ************************************/
    elseif ($action == 'editsubmission' ) {

        if (empty($_GET['sid'])) {
            error("Edit submission: submission id missing");
        }
        $usehtmleditor = can_use_html_editor();
        
        $submission = get_record("workshop_submissions", "id", $_GET['sid']);
        print_heading(get_string("editsubmission", "workshop"));
        if ($submission->userid <> $USER->id) {
            error("Edit submission: Userids do not match");
        }
        ?>
        <form name="editform" enctype="multipart/form-data" action="submissions.php" method="post">
        <input type="hidden" name="action" value="updatesubmission" />
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="sid" value="<?php echo $_GET['sid'] ?>" />
        <center>
        <table celpadding="5" border="1">
        <?php
        echo "<tr valign=\"top\"><td><b>". get_string("title", "workshop").":</b>\n";
        echo "<input type=\"text\" name=\"title\" size=\"60\" maxlength=\"100\" value=\"$submission->title\" />\n";
        echo "</td></tr><tr><td><b>".get_string("submission", "workshop").":</b><br />\n";
        print_textarea($usehtmleditor, 25,70, 630, 400, "description", $submission->description);
        use_html_editor("description");
        echo "</td></tr>\n";
        if ($workshop->nattachments) {
            $filearea = workshop_file_area_name($workshop, $submission);
            if ($basedir = workshop_file_area($workshop, $submission)) {
                if ($files = get_directory_list($basedir)) {
                    echo "<tr><td><b>".get_string("attachments", "workshop").
                        "</b><div align=\"right\"><input type=\"button\" value=\"".get_string("removeallattachments",
                        "workshop")."\" onclick=\"document.editform.action.value='removeattachments';
                        document.editform.submit();\"/></div></td></tr>\n";
                    $n = 1;
                    foreach ($files as $file) {
                        $icon = mimeinfo("icon", $file);
                        if ($CFG->slasharguments) {
                            $ffurl = "file.php/$filearea/$file";
                        } else {
                            $ffurl = "file.php?file=/$filearea/$file";
                        }
                        echo "<tr><td>".get_string("attachment", "workshop")." $n: <img src=\"$CFG->pixpath/f/$icon\" 
                            height=\"16\" width=\"16\" border=\"0\" alt=\"File\" />".
                            "&nbsp;<a target=\"uploadedfile\" href=\"$CFG->wwwroot/$ffurl\">$file</a></td></tr>\n";
                    }
                } else {
                    echo "<tr><td><b>".get_string("noattachments", "workshop")."</b></td></tr>\n";
                }
            }
            echo "<tr><td>\n";
            require_once($CFG->dirroot.'/lib/uploadlib.php');
            for ($i=0; $i < $workshop->nattachments; $i++) {
                $iplus1 = $i + 1;
                $tag[$i] = get_string("newattachment", "workshop")." $iplus1:";
            }
            upload_print_form_fragment($workshop->nattachments,null,$tag,false,null,$course->maxbytes,
                $workshop->maxbytes,false);
            echo "</td></tr>\n";
        }

        echo "</table>\n";
        echo "<input type=\"submit\" value=\"".get_string("savemysubmission", "workshop")."\" />\n";
        echo "</center></form>\n";
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
	

    /******************* remove (all) attachments ************************************/
    elseif ($action == 'removeattachments' ) {

        $form = data_submitted();
        
        if (empty($form->sid)) {
            error("Update submission: submission id missing");
            }
        $submission = get_record("workshop_submissions", "id", $form->sid);
        // amend title... just in case they were modified
        // check existence of title
        if (empty($form->title)) {
            notify(get_string("notitlegiven", "workshop"));
        } else {
            set_field("workshop_submissions", "title", $form->title, "id", $submission->id);
            set_field("workshop_submissions", "description", trim($form->description), "id", $submission->id);
        } 
        print_string("removeallattachments", "workshop");
        workshop_delete_submitted_files($workshop, $submission);
        add_to_log($course->id, "workshop", "removeattachments", "view.php?id=$cm->id", "submission $submission->id");
        
        print_continue("view.php?id=$cm->id");
        }
    

    /******************* show submission ************************************/
    elseif ($action == 'showsubmission' ) {

        if (empty($_GET['sid'])) {
            error("Edit submission: submission id missing");
            }
        
        $submission = get_record("workshop_submissions", "id", $_GET['sid']);
        workshop_print_submission($workshop, $submission);
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

        redirect("submissions.php?action=adminlist&amp;id=$cm->id");
        }


    /*************** update submission ***************************/
    elseif ($action == 'updatesubmission') {

        $timenow = time();
        $form = data_submitted();
        
        if (empty($form->sid)) {
            error("Update submission: submission id missing");
            }
        $submission = get_record("workshop_submissions", "id", $form->sid);
        // check existence of title
        if (empty($form->title)) {
            $title = get_string("notitle", "workshop");
        } 
        set_field("workshop_submissions", "title", $form->title, "id", $submission->id);
        set_field("workshop_submissions", "description", trim($form->description), "id", $submission->id);
        set_field("workshop_submissions", "timecreated", $timenow, "id", $submission->id);
        if ($workshop->nattachments) {
            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager(null,false,false,$course,false,$workshop->maxbytes);
            if ($um->preprocess_files()) {
                $dir = workshop_file_area_name($workshop, $submission);
                if ($um->save_files($dir)) {
                    add_to_log($course->id, "workshop", "newattachment", "view.php?id=$cm->id", "$workshop->id");
                    print_heading(get_string("uploadsuccess", "workshop"));
                }
                // upload manager will print errors.
            } 
            print_continue("view.php?id=$cm->id");
        } else {
            redirect("view.php?id=$cm->id");
        }
    }
                
     /******************* user confirm delete ************************************/
    elseif ($action == 'userconfirmdelete' ) {

        if (empty($_GET['sid'])) {
            error("User Confirm Delete: submission id missing");
            }
            
        notice_yesno(get_string("confirmdeletionofthisitem","workshop", get_string("submission", "workshop")), 
             "submissions.php?action=userdelete&amp;id=$cm->id&amp;sid=$_GET[sid]", "view.php?id=$cm->id");
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

