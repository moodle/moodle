<?PHP  // $Id: view.php, v1.0 30th April 2003

/*************************************************
	ACTIONS handled are:

	close workshop( for teachers)
	displayfinalgrade (for students)
	makefinalgradesunavailable (for teachers)
	notavailable (for students)
	open workshop (for teachers)
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
	add_to_log($course->id, "workshop", "view", "view.php?id=$cm->id", "$workshop->id");

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
		switch ($workshop->phase) {
			case 0 :
			case 1 : $action = 'notavailable'; break;
			case 2 : $action = 'studentsview'; break;
			case 3 : $action = 'notavailable'; break;
			case 4 : $action = 'displayfinalgrade';
			}
		}
	else { // it's a guest, oh no!
		$action = 'notavailable';
		}
	
	
	/*********************** close workshop for student assessments and submissions (move to phase 3) (for teachers)**/
	if ($action == 'closeworkshop') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		// move phase along
		if ($workshop->phase == 2) { // force phase to open workshop
			set_field("workshop", "phase", 3, "id", "$workshop->id");
			echo "<CENTER><B>".get_string("movedtophase", "workshop", 3)."</B></CENTER>\n";
			add_to_log($course->id, "workshop", "close", "view.php?a=$workshop->id", "$workshop->id");
			}
			
		print_continue("view.php?a=$workshop->id");

		}
	

	/******************* display final grade (for students) ************************************/
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
			echo "<CENTER><TABLE BORDER=1 WIDTH=\"90%\"><TR>";
			echo "<TD><B>".get_string("submissions", "workshop")."</B></TD>";
			if ($useteachersgrades) {
				echo "<TD><B>".get_string("teachersassessment", "workshop")."</B></TD>";
				}
			if ($usepeergrades) {
				echo "<TD><B>".get_string("studentsassessment", "workshop")."</B></TD>";
				}
			echo "<TD><B>".get_string("assessmentsdone", "workshop")."</B></TD>";
			if ($usebiasgrades) {
				echo "<TD><B>".get_string("gradeforbias", "workshop")."</B></TD>";
				}
			if ($usereliabilitygrades) {
				echo "<TD><B>".get_string("gradeforreliability", "workshop")."</B></TD>";
				}
			if ($usegradinggrades) {
				echo "<TD><B>".get_string("gradeforassessments", "workshop")."</B></TD>";
				}
			echo "<TD><B>".get_string("overallgrade", "workshop")."</B></TD></TR>\n";
			// now the weights
			echo "<TR><TD><B>".get_string("weights", "workshop")."</B></TD>";
			if ($useteachersgrades) {
				echo "<TD><B>$workshop_FWEIGHTS[$teacherweight]</B></TD>\n";
				}
			if ($usepeergrades) {
				echo "<TD><B>$workshop_FWEIGHTS[$peerweight]</B></TD>\n";
				}
			echo "<TD><B>&nbsp;</B></TD>\n";
			if ($usebiasgrades) {
				echo "<TD><B>$workshop_FWEIGHTS[$biasweight]</B></TD>\n";
				}
			if ($usereliabilitygrades) {
				echo "<TD><B>$workshop_FWEIGHTS[$reliabilityweight]</B></TD>\n";
				}
			if ($usegradinggrades) {
				echo "<TD><B>$workshop_FWEIGHTS[$gradingweight]</B></TD>\n";
				}
			echo "<TD><B>&nbsp;</B></TD></TR>\n";
			foreach ($submissions as $submission) {
				echo "<TR><TD>".workshop_print_submission_title($workshop, $submission)."</TD>\n";
				if ($useteachersgrades) {
					echo "<TD>".workshop_print_submission_assessments($workshop, $submission, "teacher")."</TD>";
					}
				if ($usepeergrades) {
					echo "<TD>".workshop_print_submission_assessments($workshop, $submission, "student")."</TD>";
					}
				echo "<TD>".workshop_print_user_assessments($workshop, $USER)."</TD>";
				if ($usebiasgrades) {
					echo "<TD>$submission->biasgrade</TD>";
					}
				if ($usereliabilitygrades) {
					echo "<TD>$submission->reliabilitygrade</TD>";
					}
				if ($usegradinggrades) {
					echo "<TD>$submission->gradinggrade</TD>";
					}
				echo "<TD>$submission->finalgrade</TD></TR>\n";
				}
			}
		echo "</TABLE><BR CLEAR=ALL>\n";
		print_string("allgradeshaveamaximumof", "workshop", $workshop->grade);
		print_continue("view.php?a=$workshop->id");
		}


	/*********************** make final grades available (for teachers only)**************/
	elseif ($action == 'makefinalgradesavailable') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		if ($workshop->phase == 3) { // is this at the expected phase?
			set_field("workshop", "phase", 4, "id", "$workshop->id");
			echo "<CENTER><B>".get_string("movedtophase", "workshop", 4)."</B></CENTER>\n";
			}
		else {
			echo "<CENTER><B>".get_string("assignmentnotinthecorrectphase", "workshop")."</B></CENTER>\n";
			}
		print_continue("view.php?a=$workshop->id");
		add_to_log($course->id, "workshop", "display grades", "view.php?a=$workshop->id", "$workshop->id");
		}
	
	
	/*********************** make final grades unavailable (for teachers only)**************/
	elseif ($action == 'makefinalgradesunavailable') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		if ($workshop->phase == 4) { // is this at the expected phase?
			set_field("workshop", "phase", 3, "id", "$workshop->id");
			echo "<CENTER><B>".get_string("movedtophase", "workshop", 3)."</B></CENTER>\n";
			}
		else {
			echo "<CENTER><B>".get_string("assignmentnotinthecorrectphase", "workshop")."</B></CENTER>\n";
			}
		print_continue("view.php?a=$workshop->id");
		add_to_log($course->id, "workshop", "hide grades", "view.php?a=$workshop->id", "$workshop->id");
		}
	
	
	/*********************** assignment not available (for students)***********************/
	elseif ($action == 'notavailable') {
		echo "<p><center>".get_string("notavailable", "workshop")."</center>\n";
		}


	/*********************** open workshop for student assessments and submissions (move to phase 2) (for teachers)**/
	elseif ($action == 'openworkshop') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		// move phase along
		if (!($workshop->phase == 2)) { // force phase to open workshop
			set_field("workshop", "phase", 2, "id", "$workshop->id");
			echo "<CENTER><B>".get_string("movedtophase", "workshop", 2)."</B></CENTER>\n";
			}
			
		print_continue("view.php?id=$cm->id");

		add_to_log($course->id, "workshop", "open", "view.php?a=$workshop->id", "$workshop->id");
		}


	/*********************** set up assignemnt (move back to phase 1) (for teachers)***********************/
	elseif ($action == 'setupassignment') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		if ($workshop->phase == 2) { // phase must be correct
			set_field("workshop", "phase", 1, "id", "$workshop->id");
			echo "<CENTER><B>".get_string("movedtophase", "workshop", 1)."</B></CENTER>\n";
			}
		else {
			echo "<CENTER><B>".get_string("assignmentnotinthecorrectphase", "workshop")."</B></CENTER>\n";
			}
		print_continue("view.php?a=$workshop->id");
		}
	
	
	/*********************** student's view could be in 1 of 4 stages ***********************/
	elseif ($action == 'studentsview') {
		// print standard assignment heading
		$strdifference = format_time($workshop->deadline - time());
		if (($workshop->deadline - time()) < 0) {
			$strdifference = "<FONT COLOR=RED>$strdifference</FONT>";
		}
		$strduedate = userdate($workshop->deadline)." ($strdifference)";
		print_simple_box_start("CENTER");
		print_heading($workshop->name, "CENTER");
		print_simple_box_start("CENTER");
		echo "<B>".get_string("duedate", "assignment")."</B>: $strduedate<BR>";
		echo "<B>".get_string("maximumgrade")."</B>: $workshop->grade<BR>";
		echo "<B>".get_string("detailsofassessment", "workshop")."</B>: 
			<A HREF=\"assessments.php?id=$cm->id&action=displaygradingform\">".
			get_string("specimenassessmentform", "workshop")."</A><BR>";
		print_simple_box_end();
		echo "<BR>";
		echo format_text($workshop->description, $workshop->format);
		print_simple_box_end();
		echo "<BR>";
		// in Stage 1? - assess teacher's submissions to a satisfactory level
		if (!workshop_test_user_assessments($workshop, $USER)) {
			echo "<CENTER><B>".get_string("pleaseassesstheseexamplesfromtheteacher", "workshop", $course->teacher)."</B></CENTER><BR>\n";
			workshop_list_teacher_submissions($workshop, $USER);
			echo "<CENTER><B>".get_string("theseasessmentsaregradedbytheteacher", "workshop", $course->teacher)."</B></CENTER><BR>\n";
			}
		// in stage 2? - submit own first attempt
		else {
			if ($workshop->ntassessments) { // display message if student had to assess the teacher's examples
				echo "<P><CENTER><B><A HREF=\"assessments.php?action=listteachersubmissions&id=$cm->id\">".
					get_string("assessmentsareok", "workshop")."</A></B></CENTER>\n";
				}
			if (!workshop_get_user_submissions($workshop, $USER)) {
				// print upload form
				print_heading(get_string("submitassignment", "assignment").":", "CENTER");
				workshop_print_upload_form($workshop);
				}
			// in stage 3? - grade other student's submissions, resubmit and list all submissions
			else {
				// list any assessments by teachers
				if (workshop_count_teacher_assessments($workshop, $USER)) {
					echo "<P><CENTER><B>".get_string("assessmentsby", "workshop", $course->teachers)."</B></CENTER><BR>\n";
					workshop_list_teacher_assessments($workshop, $USER);
					}
				// if student assessments show any to assess...
				if ($workshop->nsassessments) { // if there are student assessments display them... 
					workshop_list_student_submissions($workshop, $USER);
					// ..and any they have already done...
					echo "<P><CENTER><B>".get_string("yourassessments", "workshop")."</B></CENTER><BR>\n";
					workshop_list_assessed_submissions($workshop, $USER);
					// ... and show peer assessments
					if (workshop_count_peer_assessments($workshop, $USER)) {
						echo "<P><CENTER><B>".get_string("assessmentsby", "workshop", $course->students)."</B></CENTER><BR>\n";
						workshop_list_peer_assessments($workshop, $USER);
						}
					}
				// list previous submissions
				echo "<P><CENTER><B>".get_string("submissions", "workshop")."</B></CENTER><BR>\n";
				workshop_list_user_submissions($workshop, $USER);
				echo "<HR SIZE=1 NOSHADE>";
				if ($workshop->resubmit) {
					// if resubmissions allowed print upload form
					print_heading(get_string("submitassignment", "assignment").":", "CENTER");
					workshop_print_upload_form($workshop);
					echo "<HR SIZE=1 NOSHADE>";
					}
				echo "<CENTER><B><A HREF=\"submissions.php?action=listallsubmissions&id=$cm->id\">".
					get_string("listofallsubmissions", "workshop")."</A></B></CENTER>\n";
				}
			}
		}


	/*********************** submission of assignment by a student/teacher ***********************/
	elseif ($action == 'submitassignment') {
		$strdifference = format_time($workshop->deadline - time());
		if (($workshop->deadline - time()) < 0) {
			$strdifference = "<FONT COLOR=RED>$strdifference</FONT>";
		}
		$strduedate = userdate($workshop->deadline)." ($strdifference)";
	
		print_simple_box_start("CENTER");
		print_heading($workshop->name, "CENTER");
		print_simple_box_start("CENTER");
		echo "<B>".get_string("duedate", "assignment")."</B>: $strduedate<BR>";
		echo "<B>".get_string("maximumgrade")."</B>: $workshop->grade<BR>";
		echo "<B>".get_string("detailsofassessment", "workshop")."</B>: 
			<A HREF=\"assessments.php?id=$cm->id&action=displayelements\">".
			get_string("specimenassessmentform", "workshop")."</A><BR>";
		print_simple_box_end();
		echo "<BR>";
		echo format_text($workshop->description, $workshop->format);
		print_simple_box_end();
		echo "<BR>";
		
		workshop_list_teacher_submissions($workshop, $USER);
		echo "<HR SIZE=1 NOSHADE>";
		echo "<BR>";
		
		workshop_list_student_submissions($workshop, $USER);
		echo "<HR SIZE=1 NOSHADE>";
		echo "<BR>";
		
		// list previous submissions
		workshop_list_user_submissions($workshop, $USER);
	
		echo "<HR SIZE=1 NOSHADE>";
	
		// print upload form
		print_heading(get_string("submitassignment", "assignment").":", "CENTER");
		workshop_print_upload_form($workshop);
		}


	/*********************** teacher's view - display admin page (current phase options) ************/
	elseif ($action == 'teachersview') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		print_heading_with_help(get_string("managingassignment", "workshop"), "managing", "workshop");
		echo "<CENTER><P>\n";
			switch ($workshop->phase) {
				case 0:
				case 1: // set up assignment
					echo "<B><U>".get_string("phase1", "workshop")."</U></B>";
					echo "<P><B><A HREF=\"assessments.php?id=$cm->id&action=editelements\">".
						  get_string("amendassessmentelements", "workshop")."</A></B></P>";
					echo "<P><B><A HREF=\"view.php?id=$cm->id&action=submitassignment\">".
						get_string("submitexampleassignment", "workshop")."</A></B>";
					echo "<P><B><A HREF=\"submissions.php?id=$cm->id&action=listforassessmentteacher\">".
						  get_string("teachersubmissionsforassessment", "workshop", 
							  workshop_count_teacher_submissions_for_assessment($workshop, $USER)).
						  "</A></B></P>\n";
					echo "<P><B><A HREF=\"view.php?id=$cm->id&action=openworkshop\">".
						  get_string("moveonto", "workshop")." ".get_string("phase2", "workshop", $course->student)."</A></B></P>";
					break;
					
				case 2: // submissions and assessments
					echo "<B><U>".get_string("phase2", "workshop", $course->student)."</U></B>";
					echo "<P><B><A HREF=\"assessments.php?id=$cm->id&action=listungradedteachersubmissions\">".
						  get_string("ungradedassessmentsofteachersubmissions", "workshop", workshop_count_ungraded_assessments_teacher($workshop))."</A></B>\n";
					echo "<P><B><A HREF=\"assessments.php?id=$cm->id&action=listungradedstudentsubmissions\">".
						  get_string("ungradedassessmentsofstudentsubmissions", "workshop", workshop_count_ungraded_assessments_student($workshop))."</A></B>\n";
					echo "<P><B><A HREF=\"submissions.php?id=$cm->id&action=listforassessmentstudent\">".
						  get_string("studentsubmissionsforassessment", "workshop", workshop_count_student_submissions_for_assessment($workshop, $USER)).
						  "</A></B>\n";
					echo "<P><B><A HREF=\"view.php?id=$cm->id&action=closeworkshop\">".
						  get_string("moveonto", "workshop")." ".get_string("phase3", "workshop")."</A></B></P>";
					echo "<P><FONT SIZE=1>[".get_string("deadlineis", "workshop", userdate($workshop->deadline))."]</FONT></P>\n";
					echo "<P><B><A HREF=\"view.php?id=$cm->id&action=setupassignment\">(".
						get_string("returnto", "workshop")." ".get_string("phase1", "workshop").")</A></B></P>";
					break;
					
				case 3: // calculate final grades
					echo "<B><U>".get_string("phase3", "workshop")."</U></B>";
					echo "<P><B><A HREF=\"assessments.php?id=$cm->id&action=listungradedstudentsubmissions\">".
						  get_string("ungradedassessmentsofstudentsubmissions", "workshop", workshop_count_ungraded_assessments_student($workshop))."</A></B>\n";
					echo "<P><B><A HREF=\"submissions.php?id=$cm->id&action=listforassessmentstudent\">".
						  get_string("studentsubmissionsforassessment", "workshop", workshop_count_student_submissions_for_assessment($workshop, $USER)).
						  "</A></B>\n";
					echo "<P><B><A HREF=\"submissions.php?id=$cm->id&action=displayfinalweights\">".
						  get_string("calculationoffinalgrades", "workshop")."</A></B></P>";
					echo "<P><B><A HREF=\"view.php?id=$cm->id&action=makefinalgradesavailable\">".
						  get_string("moveonto", "workshop")." ".get_string("phase4", "workshop")."</A></B></P>";
					echo "<P><B><A HREF=\"view.php?id=$cm->id&action=openworkshop\">(".
						  get_string("returnto", "workshop")." ".get_string("phase2", "workshop", $course->student).")</A></B></P>";
					break;
					
				case 4: // show final grades
					echo "<B><U>".get_string("phase4", "workshop")."</U></B>";
					echo "<P><B><A HREF=\"submissions.php?id=$cm->id&action=displayfinalgrades\">".
						  get_string("displayoffinalgrades", "workshop")."</A></B></P>";
					echo "<P><B><A HREF=\"view.php?id=$cm->id&action=makefinalgradesunavailable\">(".
						  get_string("returnto", "workshop")." ".get_string("phase3", "workshop").")</A></B></P>";
			}
					echo "<P><B><A HREF=\"submissions.php?id=$cm->id&action=listallsubmissions\">".
						get_string("listofallsubmissions", "workshop")."</A></B></P>\n";
		}
	
	
	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
		}

	print_footer($course);
	
?>
