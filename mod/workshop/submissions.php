<?PHP  // $Id: lib.php,v 1.1 22 Aug 2003

/*************************************************
	ACTIONS handled are:

	adminconfirmdelete
	admindelete
	adminlist
	calculatefinalgrades
	displayfinalgrades (teachers only)
	displayfinalweights
	listallsubmissions
	listforassessmentstudent
	listforassessmentteacher
	userconfirmdelete
	userdelete
	

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

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strsubmissions = get_string("submissions", "workshop");

	// ... print the header and...
    print_header("$course->shortname: $workshop->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strworkshops</A> -> 
                  <A HREF=\"view.php?a=$workshop->id\">$workshop->name</A> -> $strsubmissions", 
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
		<input type="hidden" name="action" value="adminupdatetitle">
		<input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
		<input type="hidden" name="sid" value="<?PHP echo $_REQUEST['sid'] ?>">
		<center>
		<table celpadding="5" border="1">
		<?PHP

		// now get the comment
		echo "<tr valign=\"top\">\n";
		echo "	<td align=\"right\"><P><B>". get_string("title", "workshop").":</b></p></td>\n";
		echo "	<td>\n";
		echo "		<input type=\"text\" name=\"title\" size=\"60\" maxlength=\"100\" value=\"$submission->title\">\n";
		echo "	</td></tr></table>\n";
		echo "<input type=submit VALUE=\"".get_string("amendtitle", "workshop")."\">\n";
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
	

	/*************** calculate final grades (by teacher) ***************************/
	elseif ($action == 'calculatefinalgrades') {

		$form = (object)$_POST;
		
		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		// Get all the students in surname order
		if (!$users = get_course_students($course->id, "u.firstname, u.lastname")) {
			print_heading(get_string("nostudentsyet"));
			print_footer($course);
			exit;
			}
		
		// set up the weights from the calculate final grades form...
		if (isset($form->teacherweight)) {
			$teacherweight = $form->teacherweight;
			// ...and save them 
			set_field("workshop", "teacherweight", $teacherweight, "id", "$workshop->id");
			}
		
		if (isset($form->peerweight)) {
			$peerweight = $form->peerweight;
			// ...and save them 
			set_field("workshop", "peerweight", $peerweight, "id", "$workshop->id");
			}
		
		// get the include teachers grade flag
		if (isset($form->includeteachersgrade)) {
			$includeteachersgrade = $form->includeteachersgrade;
			set_field("workshop", "includeteachersgrade", $includeteachersgrade, "id", "$workshop->id");
			}
			
		if (isset($form->biasweight)) {
			$biasweight = $form->biasweight;
			// ...and save them 
			set_field("workshop", "biasweight", $biasweight, "id", "$workshop->id");
			}
	
		if (isset($form->reliabilityweight)) {
			$reliabilityweight = $form->reliabilityweight;
			// ...and save them 
			set_field("workshop", "reliabilityweight", $reliabilityweight, "id", "$workshop->id");
			}
	
		if (isset($form->gradingweight)) {
			$gradingweight = $form->gradingweight;
			// ...and save them 
			set_field("workshop", "gradingweight", $gradingweight, "id", "$workshop->id");
			}
	
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
		if ((($workshop->ntassessments >= 3) or ($workshop->nsassessments >= 3))  and ($useteachersgrades or $usepeergrades)
				and $biasweight ) {
			$usebiasgrades = 1;
			}
		else {
			$usebiasgrades = 0;
			}
		// reliability grades?
		if ((($workshop->ntassessments >= 3) or ($workshop->nsassessments >= 3)) and ($useteachersgrades or $usepeergrades)
				and $reliabilityweight ) {
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
		
		// start to calculate the grand means
		$sumallteachergrades = 0.0;
		$nallteachergrades = 0;
		$sumallpeergrades = 0.0;
		$nallpeergrades = 0;
		// get the grades of each student's submission...
		// the method used allowed a submission to be graded by more than one teacher
		if (workshop_count_student_submissions($workshop)) {
			echo "<CENTER><B>".get_string("studentsubmissions", "workshop", $course->student);
			echo "<BR><TABLE BORDER=1 WIDTH=\"90%\"><TR>\n";
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>$course->student</B></TD>\n";
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("submissions","workshop")."</B></TD>\n";
			if ($useteachersgrades) {
				echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("assessmentsby", "workshop", $course->teachers)."</B></TD>\n";
				echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("numberofassessments", "workshop")."</B></TD>\n";
				}
			if ($usepeergrades) {
				echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("assessmentsby", "workshop", $course->students)."</B></TD>\n";
				echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("numberofassessments", "workshop")."</B></TD>\n";
				}
			if ($useteachersgrades or $usepeergrades) {
				echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("gradeofsubmission", "workshop")."</B></TD></TR>\n";
				}
			// display weights
			echo "<TR><TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("weights","workshop")."</B></TD>\n";
			echo "<TD BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			if ($useteachersgrades) {
				echo "<TD COLSPAN=\"2\" BGCOLOR=\"$THEME->cellheading2\"><CENTER><B>$WORKSHOP_FWEIGHTS[$teacherweight]</B></CENTER></TD>\n";
				}
			if ($usepeergrades) {
				echo "<TD COLSPAN=\"2\" BGCOLOR=\"$THEME->cellheading2\"><CENTER><B>$WORKSHOP_FWEIGHTS[$peerweight]</B></CENTER></TD>\n";
				}
			if ($useteachersgrades or $usepeergrades) {
				echo "<TD BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				}
			echo "<TR>\n";
			// go through the submissions in "user" order, makes comparing the two tables easier
			foreach ($users as $user) {
				if ($submissions = workshop_get_user_submissions($workshop, $user)) {
					foreach ($submissions as $submission) {
						$sumteachergrades = 0.0;
						$nteachergrades = 0;
						$sumpeergrades = 0.0;
						$npeergrades = 0;
						// have a look at each assessment and add to arrays
						if ($assessments = workshop_get_assessments($submission)) {
							foreach ($assessments as $assessment) {
								if (isteacher($workshop->course, $assessment->userid)) { 
									// it's a teacher's
									$sumteachergrades += $assessment->grade;
									$nteachergrades++;
									if ($includeteachersgrade) { // add it to the student grades
										$sumpeergrades += $assessment->grade;
										$npeergrades++;
										}
									}
								else {
									// its' a student's
									$sumpeergrades += $assessment->grade;
									$npeergrades++;
									}
								}
							}
						if ($nteachergrades) {
							$teachergrade = intval($sumteachergrades / $nteachergrades + 0.5);
							$sumallteachergrades += $teachergrade;
							$nallteachergrades++;
							}
						else {
							$teachergrade = 0;
							}
						if ($npeergrades) {
							$peergrade = intval($sumpeergrades / $npeergrades + 0.5);
							$sumallpeergrades += $peergrade;
							$nallpeergrades++;
							}
						else {
							$peergrade = 0;
							}
						if ($teacherweight + $peerweight > 0) {
							$grade = intval((($useteachersgrades * $teachergrade * $WORKSHOP_FWEIGHTS[$teacherweight] + 
								$usepeergrades * $peergrade * $WORKSHOP_FWEIGHTS[$peerweight]) / 
								($useteachersgrades * $WORKSHOP_FWEIGHTS[$teacherweight] + 
								$usepeergrades * $WORKSHOP_FWEIGHTS[$peerweight])) + 0.5);
							}
						else {
							$grade = 0;
							}
						// display the grades...
						echo "<TR><TD>$user->firstname $user->lastname</TD>\n";
						echo "<TD>".workshop_print_submission_title($workshop, $submission)."</TD>\n";
						if ($useteachersgrades) {
							echo "<TD>$teachergrade</TD>\n";
							echo "<TD>[$nteachergrades]</TD>\n";
							}
						if ($usepeergrades) {
							echo "<TD>$peergrade</TD>\n";
							echo "<TD>[$npeergrades]</TD>\n";
							}
						if ($useteachersgrades or $usepeergrades) {
							echo "<TD>$grade</TD></TR>\n";
							}
						// ...and save in the database 
						set_field("workshop_submissions", "teachergrade", $teachergrade, "id", $submission->id);
						set_field("workshop_submissions", "peergrade", $peergrade, "id", $submission->id);
						}
					}
				}
			echo "</TABLE></CENTER>\n";
			}
		// now display the overall teacher and peer means
		if ($nallteachergrades) {
			$grandteachergrade = $sumallteachergrades / $nallteachergrades;
			}
		else {
			$grandteachergrade = 0;
			}
		if ($nallpeergrades) {
			$grandpeergrade = $sumallpeergrades / $nallpeergrades;
			}
		else {
			$grandpeergrade = 0;
			}
		if ($useteachersgrades) {
			echo "<P><B>".get_string("overallteachergrade", "workshop", number_format($grandteachergrade, 2))." [$nallteachergrades]</B>\n";
			}
		if ($usepeergrades) {
			echo "<P><B>".get_string("overallpeergrade", "workshop", number_format($grandpeergrade, 2))." [$nallpeergrades]</B><BR>\n"; 
			}
		// run thru each users and see how their assessments faired, we may junk the grading stats but what the heck!
		foreach ($users as $user) {
			// we need to calculate a user bias before we can calculate their reliability
			$sumbias = 0.0;
			// nbias is used later to show how many peer assessments the user has done
			$nbias[$user->id] = 0;
			if ($workshop->nsassessments) { // peer assessments?
				// run thru this user's assessments on the STUDENT submissions
				if ($assessments = workshop_get_student_assessments($workshop, $user)) {
					foreach ($assessments as $assessment) {
						$submission = get_record("workshop_submissions", "id", $assessment->submissionid);
						// calculate the sum of "validity" values, the bias in a user's grading...
						$sumbias += $submission->peergrade - $assessment->grade;
						$nbias[$user->id]++;
						}
					}
				}
			if ($nbias[$user->id] > 1) {
				// we could divide by n-1 to remove own score from calculation of mean but we don't because we re-use bias
				// values in the calculation of reliability, and it's all relative anyway
				$bias[$user->id] = $sumbias / $nbias[$user->id];
				}
			else {
				$bias[$user->id] = 0.0;
				}
			// now look at all the user's assessments of both the TEACHER's submissions and the STUDENT submissions
			// to calculate their overall grading grade
			$sumgradinggrade = 0.0;
			$ngradinggrades = 0;
			if ($workshop->ntassessments or $workshop->nsassessments) { // worth looking?
				if ($assessments = workshop_get_user_assessments($workshop, $user)) {
					foreach ($assessments as $assessment) {
						if ($assessment->timegraded > 0) {
							$sumgradinggrade += $assessment->gradinggrade;
							$ngradinggrades++;
							}
						}
					}
				}
			if ($ngradinggrades) {
				$gradinggrade[$user->id] = $sumgradinggrade / $ngradinggrades;
				}
			else {
				$gradinggrade[$user->id] = 0.0;
				}
			}
		
		// calculate the mean value of "reliability", the accuracy of a user's grading (disregarding bias)
		// use a linear function rather than a squared function for reliability
		// at the same time calculate a reliability of a "dumb user" whose grades everything at the grand mean value
		$sumdumbreliability = 0.0;
		$ndumbreliability =0;
		foreach ($users as $user) {
			$sumreliability = 0.0;
			$nreliability = 0;
			if ($workshop->nsassessments) { // worth a look?
				// look only at the user's assessment of STUDENT submissions
				if ($assessments = workshop_get_student_assessments($workshop, $user)) {
					foreach ($assessments as $assessment) {
						$submission = get_record("workshop_submissions", "id", $assessment->submissionid);
						$sumreliability += abs($submission->peergrade - $assessment->grade - $bias[$user->id]);
						$nreliability++;
						$sumdumbreliability += abs($submission->peergrade - $grandpeergrade);
						$ndumbreliability++;
						}
					}
				}
			// calculate the mean reliability values
			if ($nreliability) {
				$reliability[$user->id] = $sumreliability / $nreliability;
				}
			else {
				$reliability[$user->id] = 999999; // big number
				}
			}
		if ($ndumbreliability) {
			$dumbreliability = $sumdumbreliability / $ndumbreliability;
			}
		else {
			$dumbreliability = 999999; // big number
			}

		// convert bias and reliability values into scales where 1 is prefect, 0 is no grading done...
		// ...but first find the largest (absolute) value of the bias measures
		if (max($bias) > abs(min($bias))) {
			$maxbias = max($bias);
			}
		else {
			$maxbias = abs(min($bias));
			}

		echo "<P><CENTER>".get_string("studentgrades", "workshop", $course->student)."<BR>\n";
		echo "<CENTER><TABLE BORDER=1 WIDTH=\"90%\"><TR>
			<TD BGCOLOR=\"$THEME->cellheading2\"><B>$course->student</B></TD>\n";
		if ($useteachersgrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("assessmentsby", "workshop", $course->teachers)."</B></TD>\n";
			}
		if ($usepeergrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("assessmentsby", "workshop", $course->students)."</B></TD>\n";
			}
		if ($usebiasgrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("gradeforbias", "workshop")."</B></TD>\n";
			}
		if ($usereliabilitygrades) {
				echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("gradeforreliability", "workshop")."</B></TD>\n";
			}
		if ($usegradinggrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("gradeforassessments", "workshop")."</B></TD>\n";
			}
		echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("overallgrade", "workshop")."</B></TD></TR>\n";
		// now display the weights
		echo "<TR><TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("weights","workshop")."</B></TD>\n";
		if ($useteachersgrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>$WORKSHOP_FWEIGHTS[$teacherweight]</B></TD>\n";
			}
		if ($usepeergrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>$WORKSHOP_FWEIGHTS[$peerweight]</B></TD>\n";
			}
		if ($usebiasgrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>$WORKSHOP_FWEIGHTS[$biasweight]</B></TD>\n";
			}
		if ($usereliabilitygrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>$WORKSHOP_FWEIGHTS[$reliabilityweight]</B></TD>\n";
			}
		if ($usegradinggrades) {
			echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>$WORKSHOP_FWEIGHTS[$gradingweight]</B></TD>\n";
			}
		echo "<TD BGCOLOR=\"$THEME->cellheading2\"><B>&nbsp;</B></TD></TR>\n";
		foreach ($users as $user) {
			// get user's best submission
			$bestgrade = -1;
			$teachergrade = 0;
			$peergrade = 0;
			if ($submissions = workshop_get_user_submissions($workshop, $user)) {
				foreach ($submissions as $submission) {
					$grade = ($submission->teachergrade * $WORKSHOP_FWEIGHTS[$teacherweight] + 
							$submission->peergrade * $WORKSHOP_FWEIGHTS[$peerweight]) / 
							($WORKSHOP_FWEIGHTS[$teacherweight] + $WORKSHOP_FWEIGHTS[$peerweight]);
					if ($grade > $bestgrade) {
						$bestgrade = $grade;
						$teachergrade = $submission->teachergrade;
						$peergrade = $submission->peergrade;
						$bestsubmission = $submission;
						}
					}
				}
			else { // funny this user did not submit - create a dummy submission to hold any grades they might have
				$bestsubmission->workshopid = $workshop->id;
				$bestsubmission->userid = $user->id;
				$bestsubmission->title = "No Submission";
				$bestsubmission->timecreated = 0;
				if (!$bestsubmission->id = insert_record("workshop_submissions", $bestsubmission)) {
					error("Unable to create dummy submission record");
					}
				}
			// biasgrade is scaled between zero and one NEED TO ADD TEST FOR NO PEER ASSESSMENTS OF SUBMITTED WORK
			if ($maxbias) {
				$biasgrade = max(($nbias[$user->id] / $workshop->nsassessments) - (abs($bias[$user->id]) / $maxbias),
					0.0);
				}
			else {
				$biasgrade = 0;
				}
			// reliabilitygrade is scaled between zero and one
			
			if ($dumbreliability and $workshop->nsassessments) {
				// nbias used here as it contains the number of assessments the user has done
				$reliabilitygrade = max(($nbias[$user->id] / $workshop->nsassessments) - 
							($reliability[$user->id] / $dumbreliability), 0.0);
				}
			else {
				$reliabilitygrade = 0;
				}
			$biasscaled = intval($biasgrade * $workshop->grade + 0.5);
			$reliabilityscaled = intval($reliabilitygrade * $workshop->grade + 0.5);
			$gradingscaled = intval($gradinggrade[$user->id] * $workshop->grade / COMMENTSCALE + 0.5);
			$finalgrade = intval((($teachergrade * $WORKSHOP_FWEIGHTS[$teacherweight] * $useteachersgrades) + 
				($peergrade * $WORKSHOP_FWEIGHTS[$peerweight] * $usepeergrades) + 
				($biasscaled * $WORKSHOP_FWEIGHTS[$biasweight] * $usebiasgrades) + 
				($reliabilityscaled * $WORKSHOP_FWEIGHTS[$reliabilityweight] * $usereliabilitygrades) + 
				($gradingscaled * $WORKSHOP_FWEIGHTS[$gradingweight] * $usegradinggrades) + 0.5) / 
				(($WORKSHOP_FWEIGHTS[$teacherweight] * $useteachersgrades) + 
				($WORKSHOP_FWEIGHTS[$peerweight] * $usepeergrades) + 
				($WORKSHOP_FWEIGHTS[$biasweight] * $usebiasgrades) +
				($WORKSHOP_FWEIGHTS[$reliabilityweight] * $usereliabilitygrades) +
				($WORKSHOP_FWEIGHTS[$gradingweight] * $usegradinggrades)));
			echo "<TR><TD>$user->firstname $user->lastname</TD>";
			if ($useteachersgrades) {
				echo "<TD>$teachergrade</TD>";
				}
			if ($usepeergrades) {
				echo "<TD>$peergrade</TD>";
				}
			if ($usebiasgrades) {
				echo "<TD>$biasscaled</TD>";
				}
			if ($usereliabilitygrades) {
				echo "<TD>$reliabilityscaled</TD>";
				}
			if ($usegradinggrades) {
				echo "<TD>$gradingscaled</TD>";
				}
			echo "<TD>$finalgrade</TD></TR>\n";

			// save the grades
			set_field("workshop_submissions", "biasgrade", $biasscaled, "id", $bestsubmission->id);
			set_field("workshop_submissions", "reliabilitygrade", $reliabilityscaled, "id", $bestsubmission->id);
			set_field("workshop_submissions", "gradinggrade", $gradingscaled, "id", $bestsubmission->id);
			set_field("workshop_submissions", "finalgrade", $finalgrade, "id", $bestsubmission->id);
			}
		echo "</TABLE><BR CLEAR=ALL>\n";
		print_string("allgradeshaveamaximumof", "workshop", $workshop->grade);
		echo "</CENTER><BR>\n";

		print_continue("view.php?a=$workshop->id");
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
		if ((($workshop->ntassessments >= 3) or ($workshop->nsassessments >= 3)) and ($useteachersgrades or $usepeergrades) 
				and $biasweight ) {
			$usebiasgrades = 1;
			}
		else {
			$usebiasgrades = 0;
			}
		// reliability grades?
		if ((($workshop->ntassessments >= 3) or ($workshop->nsassessments >= 3)) and ($useteachersgrades or $usepeergrades) 
				and $reliabilityweight ) {
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
		echo "<center><table border=\"1\" width=\"90%\"><tr>
			<td bgcolor=\"$THEME->cellheading2\"><b>".$course->student."</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\"><b>".get_string("submissions", "workshop")."</b></td>";
		if ($useteachersgrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsby", "workshop", $course->teachers)."</b></td>";
			}
		if ($usepeergrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsby", "workshop", $course->students)."</b></td>";
			}
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("assessmentsdone", "workshop")."</b></td>";
		if ($usebiasgrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforbias", "workshop")."</b></td>";
			}
		if ($usereliabilitygrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforreliability", "workshop")."</b></td>";
			}
		if ($usegradinggrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("gradeforassessments", "workshop")."</b></td>";
			}
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".get_string("overallgrade", "workshop")."</b></td></TR>\n";
		// now the weights
		echo "<TR><td bgcolor=\"$THEME->cellheading2\"><b>".get_string("weights", "workshop")."</b></td>";
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>&nbsp;</b></td>\n";
		if ($useteachersgrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>$WORKSHOP_FWEIGHTS[$teacherweight]</b></td>\n";
			}
		if ($usepeergrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>$WORKSHOP_FWEIGHTS[$peerweight]</b></td>\n";
			}
		echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>&nbsp;</b></td>\n";
		if ($usebiasgrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>$WORKSHOP_FWEIGHTS[$biasweight]</b></td>\n";
			}
		if ($usereliabilitygrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>$WORKSHOP_FWEIGHTS[$reliabilityweight]</b></td>\n";
			}
		if ($usegradinggrades) {
			echo "<td bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>$WORKSHOP_FWEIGHTS[$gradingweight]</b></td>\n";
			}
		echo "<td bgcolor=\"$THEME->cellheading2\"><b>&nbsp;</b></td></tr>\n";
		foreach ($users as $user) {
			if ($submissions = workshop_get_user_submissions($workshop, $user)) {
				foreach ($submissions as $submission) {
					echo "<tr><td>$user->firstname $user->lastname</td>";
					echo "<td>".workshop_print_submission_title($workshop, $submission)."</td>\n";
					if ($useteachersgrades) {
						echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, $submission, "teacher")."</td>";
						}
					if ($usepeergrades) {
						echo "<td align=\"center\">".workshop_print_submission_assessments($workshop, $submission, "student")."</td>";
						}
					echo "<td align=\"center\">".workshop_print_user_assessments($workshop, $user)."</td>";
					if ($usebiasgrades) {
						echo "<td align=\"center\">$submission->biasgrade</td>";
						}
					if ($usereliabilitygrades) {
						echo "<td align=\"center\">$submission->reliabilitygrade</td>";
						}
					if ($usegradinggrades) {
						echo "<td align=\"center\">$submission->gradinggrade</td>";
						}
					echo "<td align=\"center\">$submission->finalgrade</td></tr>\n";
					}
				}
			}
		echo "</table><br clear=\"all\">\n";
		workshop_print_league_table($workshop);
		echo "<br clear=\"all\">\n";
		print_string("allgradeshaveamaximumof", "workshop", $workshop->grade);
		print_continue("view.php?a=$workshop->id");
		}


	/*************** display final weights (by teacher) ***************************/
	elseif ($action == 'displayfinalweights') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		if ($workshop->phase != 3) { // is this at the expected phase?
			print_heading(get_string("assignmentnotinthecorrectphase", "workshop"));
			print_continue("view.php?a=$workshop->id");
			}
		else {
			
			?>
			<form name="weightsform" method="post" action="submissions.php">
			<INPUT TYPE="hidden" NAME="id" VALUE="<?PHP echo $cm->id ?>">
			<input type="hidden" name="action" value="calculatefinalgrades">
			<CENTER>
			<?PHP
	
			// get the final weights from the database
			$teacherweight = get_field("workshop","teacherweight", "id", $workshop->id);
			$peerweight = get_field("workshop","peerweight", "id", $workshop->id);
			$includeteachersgrade = get_field("workshop","includeteachersgrade", "id", $workshop->id);
			$biasweight = get_field("workshop","biasweight", "id", $workshop->id);
			$reliabilityweight = get_field("workshop","reliabilityweight", "id", $workshop->id);
			$gradingweight = get_field("workshop","gradingweight", "id", $workshop->id);
	
			// now show the weights used in the final grades
			print_heading_with_help(get_string("calculationoffinalgrades", "workshop"), "calculatingfinalgrade", "workshop");
			echo "<TABLE WIDTH=\"50%\" BORDER=\"1\">\n";
			echo "<TR><td COLSPAN=\"2\" bgcolor=\"$THEME->cellheading2\"><CENTER><B>".
				get_string("weightsusedforfinalgrade", "workshop")."</B></CENTER></TD></TR>\n";
			echo "<tr><td align=\"right\">".get_string("weightforteacherassessments", "workshop", $course->teacher).":</td>\n";
			echo "<TD>";
			workshop_choose_from_menu($WORKSHOP_FWEIGHTS, "teacherweight", $teacherweight, "");
			echo "</TD></TR>\n";
			echo "<TR><TD ALIGN=\"right\">".get_string("weightforpeerassessments", "workshop").":</TD>\n";
			echo "<TD>";
			workshop_choose_from_menu($WORKSHOP_FWEIGHTS, "peerweight", $peerweight, "");
			echo "</TD></TR>\n";
			echo "<TR><TD ALIGN=\"right\">".get_string("weightforbias", "workshop").":</TD>\n";
			echo "<TD>";
			workshop_choose_from_menu($WORKSHOP_FWEIGHTS, "biasweight", $biasweight, "");
			echo "</TD></TR>\n";
			echo "<TR><TD ALIGN=\"right\">".get_string("weightforreliability", "workshop").":</TD>\n";
			echo "<TD>";
			workshop_choose_from_menu($WORKSHOP_FWEIGHTS, "reliabilityweight", $reliabilityweight, "");
			echo "</TD></TR>\n";
			echo "<TR><TD ALIGN=\"right\">".get_string("weightforgradingofassessments", "workshop").":</TD>\n";
			echo "<TD>";
			workshop_choose_from_menu($WORKSHOP_FWEIGHTS, "gradingweight", $gradingweight, "");
			echo "</TD></TR>\n";
			echo "<TR><TD COLSPAN=\"2\" bgcolor=\"$THEME->cellheading2\"><CENTER><B>".
				get_string("optionforpeergrade", "workshop")."</B></CENTER></TD></TR>\n";
			echo "<TR><TD ALIGN=\"right\">".get_string("includeteachersgrade", "workshop").":</TD>\n";
			echo "<TD>";
			$options[0] = get_string("no"); $options[1] = get_string("yes");
			choose_from_menu($options, "includeteachersgrade", $includeteachersgrade, "");
			helpbutton("includeteachersgrade", get_string("includeteachersgrade", "workshop"), "workshop");
			echo "</TD></TR>\n";
			echo "</TABLE>\n";
			echo "<INPUT TYPE=submit VALUE=\"".get_string("calculationoffinalgrades", "workshop")."\">\n";
			echo "</CENTER>";
			echo "</FORM>\n";
		}
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
		if (!$users = get_course_students($course->id)) {
			print_heading(get_string("nostudentsyet"));
			print_footer($course);
			exit;
			}
		workshop_list_unassessed_teacher_submissions($workshop, $USER);
		print_continue("view.php?id=$cm->id");
		
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

