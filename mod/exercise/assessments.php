<?PHP  // $Id: lib.php,v 1.1 22 Aug 2003

/*************************************************
	ACTIONS handled are:

	adminconfirmdelete
	admindelete
    adminedit
	adminlist
	adminlistbystudent
	assessresubmission
	assesssubmission
	displaygradingform
	editelements (teachers only)
	insertelements (for teachers)
	listungradedstudentsubmissions (for teachers)
	listungradedstudentassessments (for teachers)
	listteachersubmissions
	teacherassessment (for teachers)
    teachertable
	updateassessment
	updatedualassessment
	userconfirmdelete
	userdelete
	viewassessment

************************************************/

    require("../../config.php");
	require("lib.php");

	require_variable($id);    // Course Module ID
 
    // get some esential stuff...
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
	
    $navigation = "";
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");
    $strassessments = get_string("assessments", "exercise");
	
	// ... print the header and...
    print_header("$course->shortname: $exercise->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strexercises</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$exercise->name</A> -> $strassessments", 
                  "", "", true);

	//...get the action 
	require_variable($action);
	

	/******************* admin confirm delete ************************************/
	if ($action == 'adminconfirmdelete' ) {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		if (empty($_GET['aid'])) {
			error("Admin confirm delete: assessment id missing");
			}
			
		notice_yesno(get_string("confirmdeletionofthisitem","exercise", get_string("assessment", "exercise")), 
			 "assessments.php?action=admindelete&id=$cm->id&aid=$_GET[aid]", 
             "submissions.php?action=adminlist&id=$cm->id");
		}
	

	/******************* admin delete ************************************/
	elseif ($action == 'admindelete' ) {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		if (empty($_GET['aid'])) {
			error("Admin delete: submission id missing");
			}
			
		print_string("deleting", "exercise");
		// first delete all the associated records...
		delete_records("exercise_grades", "assessmentid", $_GET['aid']);
		// ...now delete the assessment...
		delete_records("exercise_assessments", "id", $_GET['aid']);
		
		print_continue("submissions.php?id=$cm->id&action=adminlist");
		}
	

	/******************* admin amend Grading Grade ************************************/
	if ($action == 'adminamendgradinggrade' ) {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		if (empty($_GET['aid'])) {
			error("Admin Amend Grading grade: assessment id missing");
			}
			
		if (!$assessment = get_record("exercise_assessments", "id", $_GET['aid'])) {
		    error("Amin Amend Grading grade: assessment not found");
        }
        print_heading(get_string("amend", "exercise")." ".get_string("gradeforstudentsassessment", 
                    "exercise", $course->student));
        echo "<form name=\"amendgrade\" method=\"post\" action=\"assessments.php\">\n";
        echo "<input type=\"hidden\" name=\"aid\" value=\"$_GET[aid]\">\n";
        echo "<input type=\"hidden\" name=\"action\" value=\"updategradinggrade\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
        echo "<table width=\"50%\" align=\"center\" border=\"1\">\n";
		echo "<tr><td align=\"right\"><b>".get_string("gradeforstudentsassessment", "exercise", 
                $course->student)." :</td><td>\n";
		// set up coment scale
		for ($i=COMMENTSCALE; $i>=0; $i--) {
			$num[$i] = $i;
			}
		choose_from_menu($num, "gradinggrade", $assessment->gradinggrade, "");
		echo "</td></tr>\n";
        echo "<tr><td colspan=\"2\" align=\"center\">"; 
        echo "<INPUT TYPE=submit VALUE=\"".get_string("amend", "exercise")."\">\n";
        echo "</td></tr>\n";
        echo "</table>\n";
        echo "</CENTER>";
        echo "</FORM>\n";



    }
	

	/*********************** admin list of asssessments (of a submission) (by teachers)**************/
	elseif ($action == 'adminlist') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
			
		if (empty($_GET['sid'])) {
			error ("exercise asssessments: adminlist called with no sid");
			}
		$submission = get_record("exercise_submissions", "id", $_GET['sid']);
		exercise_print_assessments_for_admin($exercise, $submission);
		print_continue("submissions.php?action=adminlist&id=$cm->id");
		}


	/****************** admin list of asssessments by a student (used by teachers only )******************/
	elseif ($action == 'adminlistbystudent') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
			
		if (empty($_GET['userid'])) {
			error ("exercise asssessments: adminlistbystudent called with no userid");
			}
		$user = get_record("user", "id", $_GET['userid']);
		exercise_print_assessments_by_user_for_admin($exercise, $user);
		print_continue("submissions.php?action=adminlist&id=$cm->id");
		}


	/****************** Assess resubmission (by teacher) ***************************/
	elseif ($action == 'assessresubmission') {

		require_variable($sid);
		
		if (! $submission = get_record("exercise_submissions", "id", $sid)) {
			error("Assess submission is misconfigured - no submission record!");
		}
		if (!$submissionowner = get_record("user", "id", $submission->userid)) {
			error("Assess resubmission: user record not found");
		}
		
		// there can be an assessment record, if there isn't...
		if (!$assessment = exercise_get_submission_assessment($submission, $USER)) {
			if (!$submissions = exercise_get_user_submissions($exercise, $submissionowner)) {
				error("Assess resubmission: submission records not found");
			}
			$lastone= '';
            // just the last but one submission
			foreach ($submissions as $submission) {
			    $prevsubmission = $lastone;
                $lastone = $submission;
            }
			// get the teacher's assessment of the student's previous submission
			if (!$prevassessment = exercise_get_submission_assessment($prevsubmission, $USER)) {
				error("Assess resubmission: Previous assessment record not found");
			}
			// copy this assessment with comments...
			$assessment = exercise_copy_assessment($prevassessment, $submission, true);
		}
		
		print_heading(get_string("thisisaresubmission", "exercise", 
            "$submissionowner->firstname $submissionowner->lastname"));
		// show assessment and allow changes
		exercise_print_assessment_form($exercise, $assessment, true, $_SERVER["HTTP_REFERER"]);
	}


	/****************** Assess submission (by teacher or student) ***************************/
	elseif ($action == 'assesssubmission') {

		require_variable($sid);
		
		if (! $submission = get_record("exercise_submissions", "id", $sid)) {
			error("Assess submission is misconfigured - no submission record!");
		}
		
		// there can be an assessment record (for teacher submissions), if there isn't...
		if (!$assessment = exercise_get_submission_assessment($submission, $USER)) {
			$yearfromnow = time() + 365 * 86400;
			// ...create one and set timecreated way in the future, this is reset when record is updated
			$assessment->exerciseid = $exercise->id;
			$assessment->submissionid = $submission->id;
			$assessment->userid = $USER->id;
			$assessment->grade = -1; // set impossible grade
			$assessment->timecreated = $yearfromnow;
			$assessment->timegraded = 0;
			if (!$assessment->id = insert_record("exercise_assessments", $assessment)) {
				error("Could not insert exercise assessment!");
			}
		}
		
		// show assessment and allow changes
		exercise_print_assessment_form($exercise, $assessment, true, $_SERVER["HTTP_REFERER"]);
	}


	/****************** display grading form (viewed by student) *********************************/
	elseif ($action == 'displaygradingform') {

	print_heading_with_help(get_string("specimenassessmentform", "exercise"), "specimen", "exercise");
	
	exercise_print_assessment_form($exercise); // called with no assessment
	print_continue("view.php?id=$cm->id");
	}


	/****************** edit assessment elements (for teachers) ***********************/
	elseif ($action == 'editelements') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}
		
		$count = count_records("exercise_grades", "exercise", $exercise->id);
		if ($exercise->phase > 1 and $count) {
			notify(get_string("warningonamendingelements", "exercise"));
		}
		// set up heading, form and table
		print_heading_with_help(get_string("editingassessmentelements", "exercise"), "elements", "exercise");
		?>
		<form name="form" method="post" action="assessments.php">
		<input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
		<input type="hidden" name="action" value="insertelements">
		<CENTER><TABLE cellpadding=5 border=1>
		<?PHP
		
		// get existing elements, if none set up appropriate default ones
		if ($elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC" )) {
			foreach ($elementsraw as $element) {
				$elements[] = $element;   // to renumber index 0,1,2...
			}
		}
		// check for missing elements (this happens either the first time round or when the number 
        // of elements is icreased)
		for ($i=0; $i<$exercise->nelements; $i++) {
			if (!isset($elements[$i])) {
				$elements[$i]->description = '';
				$elements[$i]->scale =0;
				$elements[$i]->maxscore = 0;
				$elements[$i]->weight = 11;
			}
		}
		switch ($exercise->gradingstrategy) {
			case 0: // no grading
				for ($i=0; $i<$exercise->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","exercise")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[]\" rows=3 cols=75 wrap=\"virtual\">".
                        $elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
				}
				break;

			case 1: // accumulative grading
				// set up scales name
				foreach ($EXERCISE_SCALES as $KEY => $SCALE) {
					$SCALES[] = $SCALE['name'];
				}
				for ($i=0; $i<$exercise->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","exercise")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[]\" rows=3 cols=75 wrap=\"virtual\">".
                        $elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD align=right><P><B>". get_string("typeofscale", "exercise"). ":</B></P></TD>\n";
					echo "<TD valign=\"top\">\n";
					choose_from_menu($SCALES, "scale[]", $elements[$i]->scale, "");
					if ($elements[$i]->weight == '') { // not set
						$elements[$i]->weight = 11; // unity
					}
					echo "</TR>\n";
					echo "<TR valign=top><TD ALIGN=RIGHT><B>".get_string("elementweight", "exercise").
                        ":</B></TD><TD>\n";
					exercise_choose_from_menu($EXERCISE_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
					echo "		</TD>\n";
					echo "</TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
				}
				break;
				
			case 2: // error banded grading
				for ($i=0; $i<$exercise->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","exercise")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[$i]\" rows=3 cols=75 wrap=\"virtual\">".
                        $elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					if ($elements[$i]->weight == '') { // not set
						$elements[$i]->weight = 11; // unity
					}
					echo "</TR>\n";
					echo "<TR valign=top><TD ALIGN=RIGHT><B>".get_string("elementweight", "exercise").
                        ":</B></TD><TD>\n";
					exercise_choose_from_menu($EXERCISE_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
					echo "		</TD>\n";
					echo "</TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
				}
				echo "</CENTER></TABLE><BR>\n";
				echo "<P><CENTER><B>".get_string("gradetable","exercise")."</B></CENTER>\n";
				echo "<CENTER><TABLE cellpadding=5 border=1><TR><TD ALIGN=\"CENTER\">".
					get_string("numberofnegativeresponses", "exercise");
				echo "</TD><TD>". get_string("suggestedgrade", "exercise")."</TD></TR>\n";
				for ($j = $exercise->grade; $j >= 0; $j--) {
					$numbers[$j] = $j;
				}
				for ($i=0; $i<=$exercise->nelements; $i++) {
					echo "<TR><TD ALIGN=\"CENTER\">$i</TD><TD ALIGN=\"CENTER\">";
					if (!isset($elements[$i])) {  // the "last one" will be!
						$elements[$i]->description = "";
						$elements[$i]->maxscore = 0;
					}
					choose_from_menu($numbers, "maxscore[$i]", $elements[$i]->maxscore, "");
					echo "</TD></TR>\n";
				}
				break;
				
			case 3: // criterion grading
				for ($j = 100; $j >= 0; $j--) {
					$numbers[$j] = $j;
				}
				for ($i=0; $i<$exercise->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("criterion","exercise")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[$i]\" rows=3 cols=75 wrap=\"virtual\">".
                        $elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR><TD><B>". get_string("suggestedgrade", "exercise").":</B></TD><TD>\n";
					choose_from_menu($numbers, "maxscore[$i]", $elements[$i]->maxscore, "");
					echo "</TD></TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
				}
				break;

			case 4: // rubric
				for ($j = 100; $j >= 0; $j--) {
					$numbers[$j] = $j;
				}
				if ($rubricsraw = get_records("exercise_rubrics", "exerciseid", $exercise->id)) {
					foreach ($rubricsraw as $rubric) {
						$rubrics[$rubric->elementno][$rubric->rubricno] = $rubric->description; // reindex 0,1,2...
					}
				}
				for ($i=0; $i<$exercise->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","exercise")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[$i]\" rows=3 cols=75 wrap=\"virtual\">".
                        $elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR valign=top><TD ALIGN=RIGHT><B>".get_string("elementweight", "exercise").
                        ":</B></TD><TD>\n";
					exercise_choose_from_menu($EXERCISE_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
					echo "		</TD>\n";
					echo "</TR>\n";

					for ($j=0; $j<5; $j++) {
						$jplus1 = $j+1;
						if (empty($rubrics[$i][$j])) {
							$rubrics[$i][$j] = "";
						}
						echo "<TR valign=top>\n";
						echo "	<TD ALIGN=RIGHT><P><B>". get_string("grade","exercise")." $j:</B></TD>\n";
						echo "<TD><textarea name=\"rubric[$i][$j]\" rows=3 cols=75 wrap=\"virtual\">".
                            $rubrics[$i][$j]."</textarea>\n";
						echo "	</TD></TR>\n";
					}
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
				}
				break;
		}
		// close table and form
		?>
		</table><br />
		<input type="submit" value="<?php  print_string("savechanges") ?>">
		<input type="submit" name=cancel value="<?php  print_string("cancel") ?>">
		</center>
		</form>
		<?PHP
	}
	
	
	/****************** insert/update assignment elements (for teachers)***********************/
	elseif ($action == 'insertelements') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		$form = (object)$HTTP_POST_VARS;
		
		// let's not fool around here, dump the junk!
		delete_records("exercise_elements", "exerciseid", $exercise->id);
		
		// determine wich type of grading
		switch ($exercise->gradingstrategy) {
			case 0: // no grading
				// Insert all the elements that contain something
				foreach ($form->description as $key => $description) {
					if ($description) {
						unset($element);
						$element->description   = $description;
						$element->exerciseid = $exercise->id;
						$element->elementno = $key;
						if (!$element->id = insert_record("exercise_elements", $element)) {
							error("Could not insert exercise element!");
						}
					}
				}
				break;
				
			case 1: // accumulative grading
				// Insert all the elements that contain something
				foreach ($form->description as $key => $description) {
					if ($description) {
						unset($element);
						$element->description   = $description;
						$element->exerciseid = $exercise->id;
						$element->elementno = $key;
						if (isset($form->scale[$key])) {
							$element->scale = $form->scale[$key];
							switch ($EXERCISE_SCALES[$form->scale[$key]]['type']) {
								case 'radio' :	$element->maxscore = $EXERCISE_SCALES[$form->scale[$key]]['size'] - 1;
												break;
								case 'selection' :	$element->maxscore = $EXERCISE_SCALES[$form->scale[$key]]['size'];
													break;
							}
						}
						if (isset($form->weight[$key])) {
							$element->weight = $form->weight[$key];
						}
						if (!$element->id = insert_record("exercise_elements", $element)) {
							error("Could not insert exercise element!");
						}
					}
				}
				break;
				
			case 2: // error banded grading...
			case 3: // ...and criterion grading
				// Insert all the elements that contain something, the number of descriptions is 
                // one less than the number of grades
				foreach ($form->maxscore as $key => $themaxscore) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->elementno = $key;
					$element->maxscore = $themaxscore;
					if (isset($form->description[$key])) {
						$element->description   = $form->description[$key];
					}
					if (isset($form->weight[$key])) {
						$element->weight = $form->weight[$key];
					}
					if (!$element->id = insert_record("exercise_elements", $element)) {
						error("Could not insert exercise element!");
					}
				}
				break;
				
			case 4: // ...and criteria grading
				// Insert all the elements that contain something
				foreach ($form->description as $key => $description) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->elementno = $key;
					$element->description   = $description;
					$element->weight = $form->weight[$key];
					for ($j=0;$j<5;$j++) {
						if (empty($form->rubric[$key][$j]))
							break;
					}
					$element->maxscore = $j - 1;
					if (!$element->id = insert_record("exercise_elements", $element)) {
						error("Could not insert exercise element!");
					}
				}
				// let's not fool around here, dump the junk!
				delete_records("exercise_rubrics", "exerciseid", $exercise->id);
				for ($i=0;$i<$exercise->nelements;$i++) {
					for ($j=0;$j<5;$j++) {
						unset($element);
						if (empty($form->rubric[$i][$j])) {  // OK to have an element with fewer than 5 items
							 break;
					    }
						$element->exerciseid = $exercise->id;
						$element->elementno = $i;
						$element->rubricno = $j;
						$element->description   = $form->rubric[$i][$j];
						if (!$element->id = insert_record("exercise_rubrics", $element)) {
							error("Could not insert exercise element!");
						}
					}
				}
				break;
		} // end of switch
		redirect("view.php?id=$cm->id", get_string("savedok", "exercise"));
	}


	/****************** list assessments for grading (Student submissions)(by teachers)*********************/
	elseif ($action == 'listungradedstudentsubmissions') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}
		exercise_list_ungraded_assessments($exercise, "student");
		print_continue("view.php?id=$cm->id");
	}


	/***************** list assessments for grading student assessments ( linked to the 
    ******************Teacher's submissions) (by teachers)****/
	elseif ($action == 'listungradedstudentassessments') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}
		exercise_list_ungraded_assessments($exercise, "teacher");
		print_continue("view.php?id=$cm->id");
	}


	/****************** list teacher submissions ***********************/
	elseif ($action == 'listteachersubmissions') {

		exercise_list_teacher_submissions($exercise, $USER);
		print_continue("view.php?id=$cm->id");
	}


	/****************** teacher assessment : grading of assessment and submission (from student) ************/
	elseif ($action == 'teacherassessment') {
		
		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		require_variable($aid);
		require_variable($sid);
		if (!$assessment = get_record("exercise_assessments", "id", $aid)) {
			error("Teacher assessment: User's assessment record not found");
		}
		if (!$submission = get_record("exercise_submissions", "id", $sid)) {
			error("Teacher assessment: User's submission record not found");
		}
		exercise_print_dual_assessment_form($exercise, $assessment, $submission, $_SERVER["HTTP_REFERER"]);
	}


	/****************** teacher table : show assessments by exercise and teacher ************/
	elseif ($action == 'teachertable') {
		
		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		exercise_print_teacher_table($course);
        print_continue("index.php?id=$course->id");
	}


	/****************** update assessment (by teacher or student) ***************************/
	elseif ($action == 'updateassessment') {

		$timenow = time();
		$form = (object)$HTTP_POST_VARS;

		require_variable($aid);
		if (! $assessment = get_record("exercise_assessments", "id", $aid)) {
			error("exercise assessment is misconfigured");
		}

		// first get the assignment elements for maxscores and weights...
		if (!$elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC")) {
			print_string("noteonassignmentelements", "exercise");
		}
    	else {
			foreach ($elementsraw as $element) {
				$elements[] = $element;   // to renumber index 0,1,2...
			}
		}

        // don't fiddle about, delete all the old and then add the new!
		delete_records("exercise_grades", "assessmentid",  $assessment->id);
		
		//determine what kind of grading we have
		switch ($exercise->gradingstrategy) {
			case 0: // no grading
				// Insert all the elements that contain something
				foreach ($form->feedback as $key => $thefeedback) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $thefeedback;
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
						}
					}
				$grade = 0; // set to satisfy save to db
				break;
				
			case 1: // accumulative grading
				// Insert all the elements that contain something
				foreach ($form->grade as $key => $thegrade) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $form->feedback[$key];
					$element->grade = $thegrade;
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
					}
				}
				// now work out the grade...
				$rawgrade=0;
				$totalweight=0;
				foreach ($form->grade as $key => $grade) {
					$maxscore = $elements[$key]->maxscore;
					$weight = $EXERCISE_EWEIGHTS[$elements[$key]->weight];
					if ($weight > 0) { 
						$totalweight += $weight;
					}
					$rawgrade += ($grade / $maxscore) * $weight;
					// echo "\$key, \$maxscore, \$weight, \$totalweight, \$grade, \$rawgrade : $key, $maxscore, $weight, $totalweight, $grade, $rawgrade<BR>";
				}
				$grade = 100.0 * ($rawgrade / $totalweight);
				break;

			case 2: // error banded graded
				// Insert all the elements that contain something
				$error = 0.0; 
				for ($i =0; $i < $exercise->nelements; $i++) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $i;
					$element->feedback   = $form->feedback[$i];
					$element->grade = $form->grade[$i];
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
					}
					if (empty($form->grade[$i])){
						$error += $EXERCISE_EWEIGHTS[$elements[$i]->weight];
					}
				}
				// now save the adjustment
				unset($element);
				$i = $exercise->nelements;
				$element->exerciseid = $exercise->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = $i;
				$element->grade = $form->grade[$i];
				if (!$element->id = insert_record("exercise_grades", $element)) {
					error("Could not insert exercise element!");
				}
				$grade = ($elements[intval($error + 0.5)]->maxscore + $form->grade[$i])
                    * 100.0 / $exercise->grade;
			    // echo "<P><B>".get_string("weightederrorcount", "exercise", intval($error + 0.5)).
				//	" ".get_string("adjustment", "exercise").": ".$form->grade[$i]."</B>\n";
				// check the grade for sanity!
				if ($grade > 100.0) {
					$grade = 100.0;
				}
				if ($grade < 0.0) {
					$grade = 0.0;
				}
				break;
			
			case 3: // criteria grading
				// save in the selected criteria value in element zero, 
				unset($element);
				$element->exerciseid = $exercise->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = 0;
				$element->grade = $form->grade[0];
				if (!$element->id = insert_record("exercise_grades", $element)) {
					error("Could not insert exercise element!");
				}
				// now save the adjustment in element one
				unset($element);
				$element->exerciseid = $exercise->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = 1;
				$element->grade = $form->grade[1];
				if (!$element->id = insert_record("exercise_grades", $element)) {
					error("Could not insert exercise element!");
				}
				$grade = ($elements[$form->grade[0]]->maxscore + $form->grade[1]);
				// check the grade for sanity!
				if ($grade >100.0) {
					$grade = 100.0;
				}
				if ($grade < 0.0) {
					$grade = 0.0;
				}
				break;

			case 4: // rubric grading (identical to accumulative grading)
				// Insert all the elements that contain something
				foreach ($form->grade as $key => $thegrade) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $form->feedback[$key];
					$element->grade = $thegrade;
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
					}
				}
				// now work out the grade...
				$rawgrade=0;
				$totalweight=0;
				foreach ($form->grade as $key => $grade) {
					$maxscore = $elements[$key]->maxscore;
					$weight = $EXERCISE_EWEIGHTS[$elements[$key]->weight];
					if ($weight > 0) { 
						$totalweight += $weight;
					}
					$rawgrade += ($grade / $maxscore) * $weight;
				}
				$grade = 100.0 * ($rawgrade / $totalweight);
				break;

		} // end of switch
			
		// update the time of the assessment record (may be re-edited)...
		set_field("exercise_assessments", "timecreated", $timenow, "id", $assessment->id);
		set_field("exercise_assessments", "grade", $grade, "id", $assessment->id);
		// ...and clear any grading of this assessment (these assessments are never graded but...)
		set_field("exercise_assessments", "timegraded", 0, "id", $assessment->id);
		set_field("exercise_assessments", "gradinggrade", 0, "id", $assessment->id);
		
		// any comment?
		if (!empty($form->generalcomment)) {
			set_field("exercise_assessments", "generalcomment", $form->generalcomment, "id", $assessment->id);
		}
			
		// is user allowed to resubmit?
		if (isteacher($course->id)) {
			if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
				error ("Updateassessment: submission record not found");
			}
			if ($form->resubmit == 1) {
				set_field("exercise_submissions", "resubmit", 1, "id", $submission->id);
			}
			else {
				// clear resubmit flag
				set_field("exercise_submissions", "resubmit", 0, "id", $submission->id);
			}
		}
		
	    add_to_log($course->id, "exercise", "assess", "view.php?id=$cm->id", "$assessment->id");
		
		// set up return address
		if (!$returnto = $form->returnto) {
			$returnto = "view.php?id=$cm->id";
		}
			
		// show grade if grading strategy is not zero
		if ($exercise->gradingstrategy) {
			redirect($returnto, "<p align=\"center\"><b>".get_string("thegradeis", "exercise").": ".
                number_format($grade * $exercise->grade / 100.0, 1)." (".get_string("maximumgrade").
				" ".number_format($exercise->grade).")</b></p>", 1);
		}
		else {
			redirect($returnto);
		}
	}


	/****************** update dual assessment (by teacher only) ***************************/
	elseif ($action == 'updatedualassessment') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		$timenow = time();
		$form = (object)$HTTP_POST_VARS;

		// first do the teacher's comments and grading grade of the user's assessment
		if (!$assessment = get_record("exercise_assessments", "id", $form->aid)) {
			error("Update dual assessment: user's assessment record not found");
		}
		//save the comment and grade for the assessment 
		if (isset($form->teachercomment)) {
			set_field("exercise_assessments", "teachercomment", $form->teachercomment, "id", $assessment->id);
			set_field("exercise_assessments", "gradinggrade", $form->gradinggrade, "id", $assessment->id);
			set_field("exercise_assessments", "timegraded", $timenow, "id", $assessment->id);
			set_field("exercise_assessments", "mailed", 0, "id", $assessment->id);
			echo "<CENTRE><B>".get_string("savedok", "exercise")."</B></CENTRE><BR>\n";
			
			add_to_log($course->id, "exercise", "grade", "view.php?id=$cm->id", "$assessment->id");
		}
		
		// now do the assessment of a user's submission
		if (! $submission = get_record("exercise_submissions", "id", $form->sid)) {
			error("Update dual assessment: user's submission record not found");
		}
		if (!$assessment = exercise_get_submission_assessment($submission, $USER)) {
			error("Update dual assessment: teacher's assessment record not found");
		}

		// first get the assignment elements for maxscores and weights...
		if (!$elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC")) {
			print_string("noteonassignmentelements", "exercise");
		}
		else {
			foreach ($elementsraw as $element) {
				$elements[] = $element;   // to renumber index 0,1,2...
			}
		}

        // don't fiddle about, delete all the old and then add the new!
		delete_records("exercise_grades", "assessmentid",  $assessment->id);
		
		//determine what kind of grading we have
		switch ($exercise->gradingstrategy) {
			case 0: // no grading
				// Insert all the elements that contain something
				foreach ($form->feedback as $key => $thefeedback) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $thefeedback;
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
					}
				}
				$grade = 0; // set to satisfy save to db
				break;
				
			case 1: // accumulative grading
				// Insert all the elements that contain something
				foreach ($form->grade as $key => $thegrade) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $form->feedback[$key];
					$element->grade = $thegrade;
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
					}
				}
				// now work out the grade...
				$rawgrade=0;
				$totalweight=0;
				foreach ($form->grade as $key => $grade) {
					$maxscore = $elements[$key]->maxscore;
					$weight = $EXERCISE_EWEIGHTS[$elements[$key]->weight];
					if ($weight > 0) { 
						$totalweight += $weight;
					}
					$rawgrade += ($grade / $maxscore) * $weight;
					// echo "\$key, \$maxscore, \$weight, \$totalweight, \$grade, \$rawgrade : $key, $maxscore, $weight, $totalweight, $grade, $rawgrade<BR>";
				}
				$grade = 100.0 * ($rawgrade / $totalweight);
				break;

			case 2: // error banded graded
				// Insert all the elements that contain something
				$error = 0.0; 
				for ($i =0; $i < $exercise->nelements; $i++) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $i;
					$element->feedback   = $form->feedback[$i];
					$element->grade = $form->grade[$i];
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
					}
					if (empty($form->grade[$i])){
						$error += $EXERCISE_EWEIGHTS[$elements[$i]->weight];
					}
				}
				// now save the adjustment
				unset($element);
				$i = $exercise->nelements;
				$element->exerciseid = $exercise->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = $i;
				$element->grade = $form->grade[$i];
				if (!$element->id = insert_record("exercise_grades", $element)) {
					error("Could not insert exercise element!");
				}
				$grade = ($elements[intval($error + 0.5)]->maxscore + $form->grade[$i]);
				echo "<P><B>".get_string("weightederrorcount", "exercise", intval($error + 0.5))."</B>\n";
				break;
			
			case 3: // criteria grading
				// save in the selected criteria value in element zero, 
				unset($element);
				$element->exerciseid = $exercise->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = 0;
				$element->grade = $form->grade[0];
				if (!$element->id = insert_record("exercise_grades", $element)) {
					error("Could not insert exercise element!");
				}
				// now save the adjustment in element one
				unset($element);
				$element->exerciseid = $exercise->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = 1;
				$element->grade = $form->grade[1];
				if (!$element->id = insert_record("exercise_grades", $element)) {
					error("Could not insert exercise element!");
				}
				$grade = ($elements[$form->grade[0]]->maxscore + $form->grade[1]);
				break;

			case 4: // rubric grading (identical to accumulative grading)
				// Insert all the elements that contain something
				foreach ($form->grade as $key => $thegrade) {
					unset($element);
					$element->exerciseid = $exercise->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $form->feedback[$key];
					$element->grade = $thegrade;
					if (!$element->id = insert_record("exercise_grades", $element)) {
						error("Could not insert exercise element!");
					}
				}
				// now work out the grade...
				$rawgrade=0;
				$totalweight=0;
				foreach ($form->grade as $key => $grade) {
					$maxscore = $elements[$key]->maxscore;
					$weight = $EXERCISE_EWEIGHTS[$elements[$key]->weight];
					if ($weight > 0) { 
						$totalweight += $weight;
					}
					$rawgrade += ($grade / $maxscore) * $weight;
				}
				$grade = 100.0 * ($rawgrade / $totalweight);
				break;

		} // end of switch
			
		// update the time of the assessment record (may be re-edited)...
		set_field("exercise_assessments", "timecreated", $timenow, "id", $assessment->id);
		set_field("exercise_assessments", "grade", $grade, "id", $assessment->id);
		// ...and clear any grading of this assessment (never needed but...)
		set_field("exercise_assessments", "timegraded", 0, "id", $assessment->id);
		set_field("exercise_assessments", "gradinggrade", 0, "id", $assessment->id);
		
		// any comment?
		if (!empty($form->generalcomment)) {
			set_field("exercise_assessments", "generalcomment", $form->generalcomment, "id", $assessment->id);
		}
			
		// is user allowed to resubmit?
		if ($form->resubmit == 1) {
			set_field("exercise_submissions", "resubmit", 1, "id", $submission->id);
		}
		else {
			// clear resubmit flag
			set_field("exercise_submissions", "resubmit", 0, "id", $submission->id);
		}
		
	    add_to_log($course->id, "exercise", "assess", "view.php?id=$cm->id", "$assessment->id");
		
		// set up return address
		if (!$returnto = $form->returnto) {
			$returnto = "view.php?id=$cm->id";
		}
			
		// show grade if grading strategy is not zero
		if ($exercise->gradingstrategy) {
			redirect($returnto, "<p align=\"center\"><b>".get_string("thegradeis", "exercise").": ".
                number_format($grade * $exercise->grade / 100.0, 1)." (".get_string("maximumgrade").
				" ".number_format($exercise->grade).")</b></p>", 1);
		}
		else {
			redirect($returnto);
		}
	}


	/****************** update grading grade(by teacher) ***************************/
	elseif ($action == 'updategradinggrade') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

        require_variable($aid);
		if (!set_field("exercise_assessments", "gradinggrade", $_POST['gradinggrade'], "id", 
                    $_POST['aid'])) {
			error("Update grading grade: asseesment not updated");
		}
        redirect("submissions.php?id=$cm->id&action=adminlist", get_string("savedok", "exercise"), 1);
	}


	/****************** user confirm delete ************************************/
	elseif ($action == 'userconfirmdelete' ) {

		if (empty($_GET['aid'])) {
			error("User confirm delete: assessment id missing");
		}
			
		notice_yesno(get_string("confirmdeletionofthisitem","exercise", get_string("assessment", "exercise")), 
			 "assessments.php?action=userdelete&id=$cm->id&aid=$_GET[aid]", "view.php?id=$cm->id");
	}
	

	/****************** user delete ************************************/
	elseif ($action == 'userdelete' ) {

		if (empty($_GET['aid'])) {
			error("User delete: assessment id missing");
		}
			
		print_string("deleting", "exercise");
		// first delete all the associated records...
		delete_records("exercise_grades", "assessmentid", $_GET['aid']);
		// ...now delete the assessment...
		delete_records("exercise_assessments", "id", $_GET['aid']);
		
		print_continue("view.php?id=$cm->id");
	}
	

	/****************** view assessment ***********************/
	elseif ($action == 'viewassessment') {

		// get the assessment record
		if (!$assessment = get_record("exercise_assessments", "id", $_GET['aid'])) {
			error("Assessment record not found");
		}		

		// show assessment but don't allow changes
		exercise_print_assessment_form($exercise, $assessment);
		
		print_continue("view.php?id=$cm->id");
	}


	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

	print_footer($course);
 
?>

