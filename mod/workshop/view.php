<?PHP  // $Id: view.php, v1.1 23 Aug 2003

/*************************************************
	ACTIONS handled are:

	allowassessments (for teachers)
    allowboth (for teachers)
    allowsubmissions (for teachers)
    close workshop( for teachers)
	displayfinalgrade (for students)
	notavailable (for students)
	setupassignment (for teachers)
	studentsview
	submitassignment 
	teachersview
	
************************************************/

	require("../../config.php");
    require("lib.php");
	
	optional_variable($id);    // Course Module ID
    optional_variable($a);    // workshop ID

    // get some useful stuff...
	if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $workshop = get_record("workshop", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $workshop = get_record("workshop", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $workshop->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    // ...log activity...
	add_to_log($course->id, "workshop", "view", "view.php?id=$cm->id", $workshop->id, $cm->id);

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");

    // ...display header...
	print_header("$course->shortname: $workshop->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strworkshops</A> -> $workshop->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strworkshop), navmenu($course, $cm));

	// ...and if necessary set default action 
	
	optional_variable($action);
    if (isteacher($course->id)) {
		if (empty($action)) { // no action specified, either go straight to elements page else the admin page
			// has the assignment any elements
			if (count_records("workshop_elements", "workshopid", $workshop->id)) {
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
		switch ($workshop->phase) {
			case 0 :
			case 1 : $action = 'notavailable'; break;
			case 2 :
			case 3 :
			case 4 : $action = 'studentsview'; break;
			case 5 : $action = 'notavailable'; break;
			case 6 : $action = 'displayfinalgrade';
		}
	}
	else { // it's a guest, oh no!
		$action = 'notavailable';
	}
	
	
	/************** allow (peer) assessments only (move to phase 4) (for teachers)**/
	if ($action == 'allowassessments') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		// move to phase 4
		set_field("workshop", "phase", 4, "id", "$workshop->id");
		add_to_log($course->id, "workshop", "assessments only", "view.php?id=$cm->id", "$workshop->id", $cm->id);
		redirect("view.php?a=$workshop->id", get_string("movingtophase", "workshop", 4));
	}
	

	/************** allow both (submissions and assessments) (move to phase 3) (for teachers)**/
	if ($action == 'allowboth') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		// move to phase 3
		set_field("workshop", "phase", 3, "id", "$workshop->id");
		add_to_log($course->id, "workshop", "allow both", "view.php?id=$cm->id", "$workshop->id", $cm->id);
		redirect("view.php?a=$workshop->id", get_string("movingtophase", "workshop", 3));
	}
	

	/************** allow submissions only (move to phase 2) (for teachers)**/
	if ($action == 'allowsubmissions') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

        // move to phase 2, check that teacher has made enough submissions
		if (workshop_count_teacher_submissions($workshop) < $workshop->ntassessments) {
			redirect("view.php?id=$cm->id", get_string("notenoughexamplessubmitted", "workshop", 
                        $course->teacher));
		}
		else {
			set_field("workshop", "phase", 2, "id", "$workshop->id");
			add_to_log($course->id, "workshop", "submissions", "view.php?id=$cm->id", "$workshop->id", $cm->id);
			redirect("view.php?id=$cm->id", get_string("movingtophase", "workshop", 2));
		}
	}
	

	/****************** close workshop for student assessments/submissions (move to phase 5) (for teachers)**/
	elseif ($action == 'closeworkshop') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		// move to phase 5
		set_field("workshop", "phase", 5, "id", "$workshop->id");
		add_to_log($course->id, "workshop", "close", "view.php?id=$cm->id", "$workshop->id", $cm->id);
		redirect("view.php?a=$workshop->id", get_string("movingtophase", "workshop", 5));
	}
	

	/****************** display final grade (for students) ************************************/
	elseif ($action == 'displayfinalgrade' ) {

		// get the final weights from the database
		$teacherweight = get_field("workshop","teacherweight", "id", $workshop->id);
		$peerweight = get_field("workshop","peerweight", "id", $workshop->id);
		$includeteachersgrade = get_field("workshop","includeteachersgrade", "id", $workshop->id);
		$biasweight = get_field("workshop","biasweight", "id", $workshop->id);
		$reliabilityweight = get_field("workshop","reliabilityweight", "id", $workshop->id);
		$gradingweight = get_field("workshop","gradingweight", "id", $workshop->id);
		// work out what to show in the final grades tables and what to include in the calculation of the final grade
		// teacher grades?
		if ($workshop->gradingstrategy and $teacherweight) {
			$useteachersgrades = 1;
		}
		else {
			$useteachersgrades = 0;
		}
		// peergrades?
		if ($workshop->gradingstrategy and $workshop->nsassessments and $peerweight) {
			$usepeergrades = 1;
		}
		else {
			$usepeergrades = 0;
		}
		// bias grades?
		if ((($workshop->ntassessments >= 3) or ($workshop->nsassessments >= 3)) and $biasweight ) {
			$usebiasgrades = 1;
		}
		else {
			$usebiasgrades = 0;
		}
		// reliability grades?
		if ((($workshop->ntassessments >= 3) or ($workshop->nsassessments >= 3)) and $reliabilityweight ) {
			$usereliabilitygrades = 1;
		}
		else {
			$usereliabilitygrades = 0;
		}
		// grading grades?
		if (($workshop->ntassessments or $workshop->nsassessments) and $gradingweight ) {
			$usegradinggrades = 1;
		}
		else {
			$usegradinggrades = 0;
		}
		
		// show the final grades as stored in the tables...
		print_heading_with_help(get_string("displayoffinalgrades", "workshop"), "finalgrades", "workshop");
		if ($submissions = workshop_get_user_submissions($workshop, $USER)) { // any submissions from user?
			echo "<center><table border=\"1\" width=\"90%\"><tr>";
			echo "<td><b>".get_string("submissions", "workshop")."</b></td>";
			if ($useteachersgrades) {
				echo "<td align=\"center\"><b>".get_string("teacherassessments", "workshop", 
                        $course->teacher)."</b></td>";
			}
			if ($usepeergrades) {
				echo "<td align=\"center\"><b>".get_string("studentassessments", "workshop", 
                        $course->student)."</b></td>";
			}
			echo "<td align=\"center\"><b>".get_string("assessmentsdone", "workshop")."</b></td>";
			if ($usebiasgrades) {
				echo "<td align=\"center\"><b>".get_string("gradeforbias", "workshop")."</b></td>";
			}
			if ($usereliabilitygrades) {
				echo "<td align=\"center\"><b>".get_string("gradeforreliability", "workshop")."</b></td>";
			}
			if ($usegradinggrades) {
				echo "<td align=\"center\"><b>".get_string("gradeforassessments", "workshop")."</b></td>";
			}
			echo "<td align=\"center\"><b>".get_string("overallgrade", "workshop")."</b></td></TR>\n";
			// now the weights
			echo "<TR><td><b>".get_string("weights", "workshop")."</b></td>";
			if ($useteachersgrades) {
				echo "<td align=\"center\"><b>$WORKSHOP_FWEIGHTS[$teacherweight]</b></td>\n";
			}
			if ($usepeergrades) {
				echo "<td align=\"center\"><b>$WORKSHOP_FWEIGHTS[$peerweight]</b></td>\n";
			}
			echo "<td><b>&nbsp;</b></td>\n";
			if ($usebiasgrades) {
				echo "<td align=\"center\"><b>$WORKSHOP_FWEIGHTS[$biasweight]</b></td>\n";
			}
			if ($usereliabilitygrades) {
				echo "<td align=\"center\"><b>$WORKSHOP_FWEIGHTS[$reliabilityweight]</b></td>\n";
			}
			if ($usegradinggrades) {
				echo "<td align=\"center\"><b>$WORKSHOP_FWEIGHTS[$gradingweight]</b></td>\n";
			}
			echo "<td><b>&nbsp;</b></td></TR>\n";
			foreach ($submissions as $submission) {
				echo "<TR><td>".workshop_print_submission_title($workshop, $submission)."</td>\n";
				if ($useteachersgrades) {
					echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, 
                            $submission, "teacher")."</td>";
				}
				if ($usepeergrades) {
					echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, 
                            $submission, "student")."</td>";
				}
				echo "<td align=\"center\">".workshop_print_user_assessments($workshop, $USER)."</td>";
				if ($usebiasgrades) {
					echo "<td align=\"center\">$submission->biasgrade</td>";
				}
				if ($usereliabilitygrades) {
					echo "<td align=\"center\">$submission->reliabilitygrade</td>";
				}
				if ($usegradinggrades) {
					echo "<td align=\"center\">$submission->gradinggrade</td>";
				}
				echo "<td align=\"center\">$submission->finalgrade</td></TR>\n";
			}
		}
		echo "</TABLE><BR CLEAR=ALL>\n";
        echo "<p>&lt; &gt; ".get_string("assessmentdropped", "workshop")."</p>\n";
		if ($workshop->showleaguetable) {
			workshop_print_league_table($workshop);
		}
		echo "<br />".get_string("allgradeshaveamaximumof", "workshop", $workshop->grade);
	}


	/****************** make final grades available (go to phase 6) (for teachers only)********/
	elseif ($action == 'makefinalgradesavailable') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		set_field("workshop", "phase", 6, "id", "$workshop->id");
		add_to_log($course->id, "workshop", "display grades", "view.php?id=$cm->id", "$workshop->id", $cm->id);
		redirect("view.php?a=$workshop->id", get_string("movingtophase", "workshop", 6));
	}
	
	
	/****************** assignment not available (for students)***********************/
	elseif ($action == 'notavailable') {
		print_heading(get_string("notavailable", "workshop"));
	}


	/****************** set up assignment (move back to phase 1) (for teachers)***********************/
	elseif ($action == 'setupassignment') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		set_field("workshop", "phase", 1, "id", "$workshop->id");
		add_to_log($course->id, "workshop", "set up", "view.php?id=$cm->id", "$workshop->id", $cm->id);
		redirect("view.php?a=$workshop->id", get_string("movingtophase", "workshop", 1));
	}
	
	
	/****************** student's view could be in 1 of 4 stages ***********************/
	elseif ($action == 'studentsview') {
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
            // has user submitted anything yet? (only allowed in phases 2 and 3)
			if (!workshop_get_user_submissions($workshop, $USER)) {
				if ($workshop->phase < 4) {
                    // print upload form
				    print_heading(get_string("submitassignmentusingform", "workshop").":");
				    workshop_print_upload_form($workshop);
				} else {
                    print_heading(get_string("submissionsnolongerallowed", "workshop"));
                }
            }   
			// in stage 3? - grade other student's submissions, resubmit and list all submissions
			else {
				// list any assessments by teachers
				if (workshop_count_teacher_assessments($workshop, $USER)) {
					print_heading(get_string("assessmentsby", "workshop", $course->teachers));
					workshop_list_teacher_assessments($workshop, $USER);
				}
				// is self assessment used in this workshop?
				if ($workshop->includeself) {
					// prints a table if there are any submissions which have not been self assessed yet
					workshop_list_self_assessments($workshop, $USER);
				}
				// if peer assessments are being done and workshop is in phase 3 then show some  to assess...
				if ($workshop->nsassessments and ($workshop->phase > 2)) {  
					workshop_list_student_submissions($workshop, $USER);
				}
				// ..and any they have already done (and have gone cold)...
				if (workshop_count_user_assessments($workshop, $USER, "student")) {
					print_heading(get_string("yourassessments", "workshop"));
					workshop_list_assessed_submissions($workshop, $USER);
				}
				// ... and show peer assessments
				if (workshop_count_peer_assessments($workshop, $USER)) {
					print_heading(get_string("assessmentsby", "workshop", $course->students));
					workshop_list_peer_assessments($workshop, $USER);
				}
				// list previous submissions
				print_heading(get_string("submissions", "workshop"));
				workshop_list_user_submissions($workshop, $USER);
				// are resubmissions allowed and the workshop is in submission phases (2 and 3)?
                if ($workshop->resubmit and ($workshop->phase < 4)) {
					// see if there are any cold (warm included as well) assessments of the last submission
                    // if there are then print upload form
                    if ($submissions = workshop_get_user_submissions($workshop, $USER)) {
                        foreach ($submissions as $submission) {
                            $lastsubmission = $submission;
                            break;
                        }
                        if (workshop_count_assessments($lastsubmission)) {
        					echo "<hr size=\"1\" noshade>";
		        			print_heading(get_string("submitrevisedassignment", "workshop").":");
				        	workshop_print_upload_form($workshop);
        					echo "<hr size=\"1\" noshade>";
                        }
                    }
				}
				// allow user to list their submissions and assessments in a general way????
				// print_heading("<A HREF=\"submissions.php?action=listallsubmissions&id=$cm->id\">".
				// 	get_string("listofallsubmissions", "workshop"));
			}
		}
	}


	/****************** submission of assignment by teacher only***********************/
	elseif ($action == 'submitassignment') {
	
		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}
			
		$strdifference = format_time($workshop->deadline - time());
		if (($workshop->deadline - time()) < 0) {
			$strdifference = "<FONT COLOR=RED>$strdifference</FONT>";
		}
		$strduedate = userdate($workshop->deadline)." ($strdifference)";
	
		workshop_print_assignment_info($workshop);
		
		// list previous submissions from teacher 
		workshop_list_user_submissions($workshop, $USER);
	
		echo "<HR SIZE=1 NOSHADE>";
	
		// print upload form
		print_heading(get_string("submitassignment", "assignment").":");
		workshop_print_upload_form($workshop);
	}


	/****************** teacher's view - display admin page (current phase options) ************/
	elseif ($action == 'teachersview') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		print_heading_with_help(get_string("managingassignment", "workshop"), "managing", "workshop");
		
		workshop_print_assignment_info($workshop);
		
		$tabs->names = array("1. ".get_string("phase1", "workshop"), 
                        "2. ".get_string("phase2", "workshop", $course->student), 
			            "3. ".get_string("phase3", "workshop", $course->student), 
                        "4. ".get_string("phase4", "workshop", $course->student), 
                        "5. ".get_string("phase5", "workshop"),
                        "6. ".get_string("phase6", "workshop"));
		$tabs->urls = array("view.php?id=$cm->id&action=setupassignment", 
			"view.php?id=$cm->id&action=allowsubmissions",
			"view.php?id=$cm->id&action=allowboth",
			"view.php?id=$cm->id&action=allowassessments",
			"view.php?id=$cm->id&action=closeworkshop",
			"view.php?id=$cm->id&action=makefinalgradesavailable");
		if ($workshop->phase) { // phase 1 or more
			$tabs->highlight = $workshop->phase - 1;
		} else {
			$tabs->highlight = 0; // phase is zero
		}
		workshop_print_tabbed_heading($tabs);
		echo "<center>\n";
			switch ($workshop->phase) {
				case 0:
				case 1: // set up assignment
					echo "<p><b><a href=\"assessments.php?id=$cm->id&action=editelements\">".
						  get_string("amendassessmentelements", "workshop")."</a></b> \n";
					helpbutton("elements", get_string("amendassessmentelements", "workshop"), "workshop");
					if ($workshop->ntassessments) { 
                        // if teacher examples show submission and assessment links
						echo "<p><b><a href=\"view.php?id=$cm->id&action=submitassignment\">".
							get_string("submitexampleassignment", "workshop")."</a></b> \n";
						helpbutton("submissionofexamples", get_string("submitexampleassignment", "workshop"),
                                "workshop");
						echo "<p><b><a href=\"submissions.php?id=$cm->id&action=listforassessmentteacher\">".
							  get_string("teachersubmissionsforassessment", "workshop", 
								  workshop_count_teacher_submissions_for_assessment($workshop, $USER)).
							  "</a></b> \n";
						helpbutton("assessmentofexamples", get_string("teachersubmissionsforassessment", 
                                    "workshop"), "workshop");
					}
					break;
					
				case 2: // submissions and assessments
				case 3:
                case 4:
					if ($workshop->ntassessments) { // if teacher example show student assessments link
						echo "<p><b><a href=\"assessments.php?id=$cm->id&action=listungradedteachersubmissions\">".
						  get_string("ungradedassessmentsofteachersubmissions", "workshop", 
						  workshop_count_ungraded_assessments_teacher($workshop))."</a></b> \n";
						helpbutton("ungradedassessments_teacher", 
                               get_string("ungradedassessmentsofteachersubmissions", "workshop"), "workshop");
					}
					echo "<p><b><a href=\"assessments.php?id=$cm->id&action=listungradedstudentsubmissions\">".
						  get_string("ungradedassessmentsofstudentsubmissions", "workshop", 
						  workshop_count_ungraded_assessments_student($workshop))."</a></b> \n";
					helpbutton("ungradedassessments_student", 
                            get_string("ungradedassessmentsofstudentsubmissions", "workshop"), "workshop");
					echo "<p><b><a href=\"submissions.php?id=$cm->id&action=listforassessmentstudent\">".
						  get_string("studentsubmissionsforassessment", "workshop", 
						  workshop_count_student_submissions_for_assessment($workshop, $USER))."</a></b> \n";
					helpbutton("gradingsubmissions", 
                            get_string("studentsubmissionsforassessment", "workshop"), "workshop");
					break;
					
				case 5: // calculate final grades
					if ($workshop->ntassessments) { // if teacher example show student assessments link
						echo "<p><b><a href=\"assessments.php?id=$cm->id&action=listungradedteachersubmissions\">".
						  get_string("ungradedassessmentsofteachersubmissions", "workshop", 
						  workshop_count_ungraded_assessments_teacher($workshop))."</a></b> \n";
						helpbutton("ungradedassessments_teacher", 
                               get_string("ungradedassessmentsofteachersubmissions", "workshop"), "workshop");
					}
					echo "<p><b><a href=\"assessments.php?id=$cm->id&action=listungradedstudentsubmissions\">".
						  get_string("ungradedassessmentsofstudentsubmissions", "workshop", 
						  workshop_count_ungraded_assessments_student($workshop))."</a></b> \n";
					helpbutton("ungradedassessments_student", 
                            get_string("ungradedassessmentsofstudentsubmissions", "workshop"), "workshop");
					echo "<p><b><a href=\"submissions.php?id=$cm->id&action=listforassessmentstudent\">".
						  get_string("studentsubmissionsforassessment", "workshop", 
						  workshop_count_student_submissions_for_assessment($workshop, $USER))."</a></b> \n";
					helpbutton("gradingsubmissions", 
                            get_string("studentsubmissionsforassessment", "workshop"), "workshop");
					print_heading("<a href=\"submissions.php?id=$cm->id&action=displayfinalweights\">".
						  get_string("calculationoffinalgrades", "workshop")."</a>");
					print_heading("<a href=\"submissions.php?id=$cm->id&action=analysisofassessments\">".
						  get_string("analysisofassessments", "workshop")."</a>");
					break;
					
				case 6: // show final grades
					print_heading("<A HREF=\"submissions.php?id=$cm->id&action=displayfinalgrades\">".
						  get_string("displayoffinalgrades", "workshop")."</A>");
		}
		print_heading("<A HREF=\"submissions.php?id=$cm->id&action=adminlist\">".
			get_string("administration")."</A>");
	}
	
	
	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

	print_footer($course);
	
?>
