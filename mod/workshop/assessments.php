<?PHP  // $Id: lib.php,v 1.1 22 Aug 2003

/*************************************************
	ACTIONS handled are:

	addcomment
	adminconfirmdelete
	admindelete
	adminlist
	agreeassessment
	assesssubmission
	displaygradingform
	editcomment
	editelements (teachers only)
	gradeassessment (teachers only)
	insertcomment
	insertelements (for teachers)
	listungradedstudentsubmissions (for teachers)
	listungradedteachersubmissions (for teachers)
	listteachersubmissions
	updateassessment
	updatecomment
	updategrading
	userconfirmdelete
	userdelete
	viewassessment

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
	
    $navigation = "";
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strassessments = get_string("assessments", "workshop");

	// ... print the header and...
    print_header("$course->shortname: $workshop->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strworkshops</A> -> 
                  <A HREF=\"view.php?a=$workshop->id\">$workshop->name</A> -> $strassessments", 
                  "", "", true);

	//...get the action 
	require_variable($action);
	

	/*************** add comment to assessment (by author, assessor or teacher) ***************************/
	if ($action == 'addcomment') {
		
		print_heading_with_help(get_string("addacomment", "workshop"), "addingacomment", "workshop");
		// get assessment record
		if (!$assessmentid = $_REQUEST['aid']) { // comes from link or hidden form variable
			error("Assessment id not given");
			}
		$assessment = get_record("workshop_assessments", "id", $assessmentid);
		if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
			error("Submission not found");
			}
		?>
		<FORM NAME="commentform" ACTION="assessments.php" METHOD="post">
		<INPUT TYPE="HIDDEN" NAME="action" VALUE="insertcomment">
		<INPUT TYPE="HIDDEN" NAME="id" VALUE="<?PHP echo $cm->id ?>">
		<INPUT TYPE="HIDDEN" NAME="aid" VALUE="<?PHP echo $_REQUEST['aid'] ?>">
		<CENTER>
		<TABLE CELLPADDING=5 BORDER=1>
		<?PHP

		// now get the comment
		echo "<TR valign=top>\n";
		echo "	<TD align=right><P><B>". get_string("comment", "workshop").":</B></P></TD>\n";
		echo "	<TD>\n";
		echo "		<textarea name=\"comments\" rows=5 cols=75 wrap=\"virtual\">\n";
		echo "</textarea>\n";
		echo "	</TD></TR></TABLE>\n";
		echo "<INPUT TYPE=submit VALUE=\"".get_string("savemycomment", "workshop")."\">\n";
		echo "</CENTER></FORM>\n";
		echo "<P><CENTER><B>".get_string("assessment", "workshop"). "</B></CENTER>\n";
		workshop_print_assessment($workshop, $assessment);
		}


	/******************* admin confirm delete ************************************/
	elseif ($action == 'adminconfirmdelete' ) {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		if (empty($_GET['aid'])) {
			error("Admin confirm delete: assessment id missing");
			}
			
		notice_yesno(get_string("confirmdeletionofthisitem","workshop", get_string("assessment", "workshop")), 
			 "assessments.php?action=admindelete&id=$cm->id&aid=$_GET[aid]", "submissions.php?action=adminlist&id=$cm->id");
		}
	

	/******************* admin delete ************************************/
	elseif ($action == 'admindelete' ) {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		if (empty($_GET['aid'])) {
			error("Admin delete: submission id missing");
			}
			
		print_string("deleting", "workshop");
		// first delete all the associated records...
		delete_records("workshop_comments", "assessmentid", $_GET['aid']);
		delete_records("workshop_grades", "assessmentid", $_GET['aid']);
		// ...now delete the assessment...
		delete_records("workshop_assessments", "id", $_GET['aid']);
		
		print_continue("submissions.php?id=$cm->id&action=adminlist");
		}
	

	/*********************** admin list of asssessments (of a submission) (by teachers)**************/
	elseif ($action == 'adminlist') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
			
		if (empty($_GET['sid'])) {
			error ("Workshop asssessments: adminlist called with no sid");
			}
		$submission = get_record("workshop_submissions", "id", $_GET['sid']);
		workshop_print_assessments_for_admin($workshop, $submission);
		print_continue("submissions.php?action=adminlist&a=$workshop->id");
		}


	/*********************** admin list of asssessments by a student (used by teachers only )******************/
	elseif ($action == 'adminlistbystudent') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
			
		if (empty($_GET['userid'])) {
			error ("Workshop asssessments: adminlistbystudent called with no userid");
			}
		$user = get_record("user", "id", $_GET['userid']);
		workshop_print_assessments_by_user_for_admin($workshop, $user);
		print_continue("submissions.php?action=adminlist&a=$workshop->id");
		}


	/*************** agree (to) assessment (by student) ***************************/
	elseif ($action == 'agreeassessment') {
		$timenow = time();
		// assessment id comes from link or hidden form variable
		if (!$assessment = get_record("workshop_assessments", "id", $_REQUEST['aid'])) { 
			error("Assessment : agree assessment failed");
			}
		//save time of agreement
		set_field("workshop_assessments", "timeagreed", $timenow, "id", $assessment->id);
		echo "<CENTRE><B>".get_string("savedok", "workshop")."</B></CENTER><BR>\n";
			
		add_to_log($course->id, "workshop", "agree", "assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id", "$assessment->id");
		print_continue("view.php?id=$cm->id");
		}


	/*************** Assess submission (by teacher or student) ***************************/
	elseif ($action == 'assesssubmission') {

		require_variable($sid);
		
		optional_variable($allowcomments);
		if (!isset($allowcomments)) {
			$allowcomments = false;
			}
	
		if (! $submission = get_record("workshop_submissions", "id", $sid)) {
			error("Assess submission is misconfigured - no submission record!");
			}
		
		// there can be an assessment record (for teacher submissions), if there isn't...
		if (!$assessment = get_record("workshop_assessments", "submissionid", $submission->id, "userid", 
                    $USER->id)) {
			$yearfromnow = time() + 365 * 86400;
			// ...create one and set timecreated way in the future, this is reset when record is updated
			$assessment->workshopid = $workshop->id;
			$assessment->submissionid = $submission->id;
			$assessment->userid = $USER->id;
			$assessment->grade = -1; // set impossible grade
			$assessment->timecreated = $yearfromnow;
			$assessment->timegraded = 0;
			$assessment->timeagreed = 0;
			$assessment->resubmission = 0;
			if (!$assessment->id = insert_record("workshop_assessments", $assessment)) {
				error("Could not insert workshop assessment!");
				}
			}
		
		print_heading_with_help(get_string("assessthissubmission", "workshop"), "grading", "workshop");
		
		// show assessment and allow changes
		workshop_print_assessment($workshop, $assessment, true, $allowcomments, $_SERVER["HTTP_REFERER"]);
		}


	/*************** display grading form (viewed by student) *********************************/
	elseif ($action == 'displaygradingform') {

	print_heading_with_help(get_string("specimenassessmentform", "workshop"), "specimen", "workshop");
	
	workshop_print_assessment($workshop); // called with no assessment
	print_continue("view.php?a=$workshop->id");
	}


	/*************** edit comment on assessment (by author, assessor or teacher) ***************************/
	elseif ($action == 'editcomment') {
		
		print_heading_with_help(get_string("editacomment", "workshop"), "editingacomment", "workshop");
		// get the comment record...
		if (!$comment = get_record("workshop_comments", "id", $_GET['cid'])) {
			error("Edit Comment: Comment not found");
			}
		if (!$assessment = get_record("workshop_assessments", "id", $comment->assessmentid)) {
			error("Edit Comment: Assessment not found");
			}
		if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
			error("Edit Comment: Submission not found");
			}
		?>
		<FORM NAME="gradingform" ACTION="assessments.php" METHOD="post">
		<INPUT TYPE="HIDDEN" NAME="action" VALUE="updatecomment">
		<INPUT TYPE="HIDDEN" NAME="id" VALUE="<?PHP echo $cm->id ?>">
		<INPUT TYPE="HIDDEN" NAME="cid" VALUE="<?PHP echo $_GET['cid'] ?>">
		<CENTER>
		<TABLE CELLPADDING=5 BORDER=1>
		<?PHP

		// now show the comment
		echo "<TR valign=top>\n";
		echo "	<TD align=right><P><B>". get_string("comment", "workshop").":</B></P></TD>\n";
		echo "	<TD>\n";
		echo "		<textarea name=\"comments\" rows=5 cols=75 wrap=\"virtual\">\n";
		if (isset($comment->comments)) {
			echo $comment->comments;
			}
		echo "	    </textarea>\n";
		echo "	</TD></TR></TABLE>\n";
		echo "<INPUT TYPE=submit VALUE=\"".get_string("savemycomment", "workshop")."\">\n";
		echo "</CENTER></FORM>\n";
		workshop_print_assessment($workshop, $assessment);
		}


	/*********************** edit assessment elements (for teachers) ***********************/
	elseif ($action == 'editelements') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		
		$count = count_records("workshop_grades", "workshop", $workshop->id);
		if ($workshop->phase > 1 and $count) {
			notify(get_string("warningonamendingelements", "workshop"));
			}
		// set up heading, form and table
		print_heading_with_help(get_string("editingassessmentelements", "workshop"), "elements", "workshop");
		?>
		<form name="form" method="post" action="assessments.php">
		<input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
		<input type="hidden" name="action" value="insertelements">
		<CENTER><TABLE cellpadding=5 border=1>
		<?PHP
		
		// get existing elements, if none set up appropriate default ones
		if ($elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC" )) {
			foreach ($elementsraw as $element) {
				$elements[] = $element;   // to renumber index 0,1,2...
				}
			}
		// check for missing elements (this happens either the first time round or when the number of elements is icreased)
		for ($i=0; $i<$workshop->nelements; $i++) {
			if (!isset($elements[$i])) {
				$elements[$i]->description = '';
				$elements[$i]->scale =0;
				$elements[$i]->maxscore = 0;
				$elements[$i]->weight = 11;
				}
			}
        
		switch ($workshop->gradingstrategy) {
			case 0: // no grading
				for ($i=0; $i<$workshop->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","workshop")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[]\" rows=3 cols=75 wrap=\"virtual\">".$elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
					}
				break;

			case 1: // accumulative grading
				// set up scales name
				foreach ($WORKSHOP_SCALES as $KEY => $SCALE) {
					$SCALES[] = $SCALE['name'];
					}
				for ($i=0; $i<$workshop->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","workshop")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[]\" rows=3 cols=75 wrap=\"virtual\">".$elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD align=right><P><B>". get_string("typeofscale", "workshop"). ":</B></P></TD>\n";
					echo "<TD valign=\"top\">\n";
					choose_from_menu($SCALES, "scale[]", $elements[$i]->scale, "");
					if ($elements[$i]->weight == '') { // not set
						$elements[$i]->weight = 11; // unity
						}
					echo "</TR>\n";
					echo "<TR valign=top><TD ALIGN=RIGHT><B>".get_string("elementweight", "workshop").":</B></TD><TD>\n";
					workshop_choose_from_menu($WORKSHOP_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
					echo "		</TD>\n";
					echo "</TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
					}
				break;
				
			case 2: // error banded grading
				for ($i=0; $i<$workshop->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","workshop")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[$i]\" rows=3 cols=75 wrap=\"virtual\">".$elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					if ($elements[$i]->weight == '') { // not set
						$elements[$i]->weight = 11; // unity
						}
					echo "</TR>\n";
					echo "<TR valign=top><TD ALIGN=RIGHT><B>".get_string("elementweight", "workshop").":</B></TD><TD>\n";
					workshop_choose_from_menu($WORKSHOP_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
					echo "		</TD>\n";
					echo "</TR>\n";
					echo "<TR valign=top>\n";
					echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</TR>\n";
					}
				echo "</CENTER></TABLE><BR>\n";
				echo "<P><CENTER><B>".get_string("gradetable","workshop")."</B></CENTER>\n";
				echo "<CENTER><TABLE cellpadding=5 border=1><TR><TD ALIGN=\"CENTER\">".
					get_string("numberofnegativeresponses", "workshop");
				echo "</TD><TD>". get_string("suggestedgrade", "workshop")."</TD></TR>\n";
				for ($j = $workshop->grade; $j >= 0; $j--) {
					$numbers[$j] = $j;
					}
				for ($i=0; $i<=$workshop->nelements; $i++) {
					echo "<TR><TD ALIGN=\"CENTER\">$i</TD><TD ALIGN=\"CENTER\">";
					if (!isset($elements[$i])) {  // the "last one" will be!
						$elements[$i]->description = "";
						$elements[$i]->maxscore = 0;
						}
					choose_from_menu($numbers, "maxscore[$i]", $elements[$i]->maxscore, "");
					echo "</TD></TR>\n";
					}
				echo "</TABLE></CENTER>\n";
				break;
				
			case 3: // criterion grading
				for ($j = 100; $j >= 0; $j--) {
					$numbers[$j] = $j;
					}
				for ($i=0; $i<$workshop->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("criterion","workshop")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[$i]\" rows=3 cols=75 wrap=\"virtual\">".$elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR><TD><B>". get_string("suggestedgrade", "workshop").":</B></TD><TD>\n";
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
				if ($rubricsraw = get_records("workshop_rubrics", "workshopid", $workshop->id)) {
					foreach ($rubricsraw as $rubric) {
						$rubrics[$rubric->elementno][$rubric->rubricno] = $rubric->description;   // reindex 0,1,2...
						}
					}
				for ($i=0; $i<$workshop->nelements; $i++) {
					$iplus1 = $i+1;
					echo "<TR valign=top>\n";
					echo "	<TD ALIGN=RIGHT><P><B>". get_string("element","workshop")." $iplus1:</B></TD>\n";
					echo "<TD><textarea name=\"description[$i]\" rows=3 cols=75 wrap=\"virtual\">".$elements[$i]->description."</textarea>\n";
					echo "	</TD></TR>\n";
					echo "<TR valign=top><TD ALIGN=RIGHT><B>".get_string("elementweight", "workshop").":</B></TD><TD>\n";
					workshop_choose_from_menu($WORKSHOP_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
					echo "		</TD>\n";
					echo "</TR>\n";

					for ($j=0; $j<5; $j++) {
						$jplus1 = $j+1;
						if (empty($rubrics[$i][$j])) {
							$rubrics[$i][$j] = "";
							}
						echo "<TR valign=top>\n";
						echo "	<TD ALIGN=RIGHT><P><B>". get_string("grade","workshop")." $j:</B></TD>\n";
						echo "<TD><textarea name=\"rubric[$i][$j]\" rows=3 cols=75 wrap=\"virtual\">".$rubrics[$i][$j]."</textarea>\n";
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
		</TABLE>
		<input type="submit" value="<?php  print_string("savechanges") ?>">
		<input type="submit" name=cancel value="<?php  print_string("cancel") ?>">
		</CENTER>
		</FORM>
		<?PHP
		}
	
	
	/*************** grade (student's) assessment (by teacher) ***************************/
	elseif ($action == 'gradeassessment') {
		
		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		// set up coment scale
		for ($i=COMMENTSCALE; $i>=0; $i--) {
			$num[$i] = $i;
			}
		
		print_heading_with_help(get_string("gradeassessment", "workshop"), "gradingassessments", "workshop");
		// get assessment record
		if (!$assessmentid = $_GET['aid']) {
			error("Assessment id not given");
			}
		$assessment = get_record("workshop_assessments", "id", $assessmentid);
		if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
			error("Submission not found");
			}
		// get the teacher's assessment first
		if ($teachersassessment = workshop_get_submission_assessment($submission, $USER)) {
			echo "<P><CENTER><B>".get_string("teacherassessments", "workshop", $course->teacher)."</B></CENTER>\n";
			workshop_print_assessment($workshop, $teachersassessment);
			}
		// now the student's assessment (don't allow changes)
		$user = get_record("user", "id", $assessment->userid);
		echo "<P><CENTER><B>".get_string("assessmentby", "workshop", $user->firstname." ".$user->lastname)."</B></CENTER>\n";
		workshop_print_assessment($workshop, $assessment);
		
		?>
		<FORM NAME="gradingform" ACTION="assessments.php" METHOD="post">
		<INPUT TYPE="HIDDEN" NAME="action" VALUE="updategrading">
		<INPUT TYPE="HIDDEN" NAME="id" VALUE="<?PHP echo $cm->id ?>">
		<INPUT TYPE="HIDDEN" NAME="stype" VALUE="<?PHP echo $_GET['stype'] ?>">
		<INPUT TYPE="HIDDEN" NAME="aid" VALUE="<?PHP echo $_GET['aid'] ?>">
		<CENTER>
		<TABLE CELLPADDING=5 BORDER=1>
		<?PHP

		// now get the teacher's comment
		echo "<TR valign=top>\n";
		echo "	<TD align=right><P><B>". get_string("teacherscomment", "workshop").":</B></P></TD>\n";
		echo "	<TD>\n";
		echo "		<textarea name=\"teachercomment\" rows=5 cols=75 wrap=\"virtual\">\n";
		if (isset($assessment->teachercomment)) {
			echo $assessment->teachercomment;
			}
		echo "</textarea>\n";
		echo "	</TD>\n";
		echo "</TR>\n";
		echo "<TR><TD ALIGN=RIGHT><B>".get_string("gradeforstudentsassessment", "workshop")."</TD><TD>\n";
		choose_from_menu($num, "gradinggrade", $assessment->gradinggrade, "");
		echo "</TD></TR></TABLE>\n";
		echo "<INPUT TYPE=submit VALUE=\"".get_string("savemygrading", "workshop")."\">\n";
		echo "</CENTER></FORM>\n";
		}


	/*************** insert (new) comment (by author, assessor or teacher) ***************************/
	elseif ($action == 'insertcomment') {
		$timenow = time();

		$form = (object)$_POST;
		
		if (!$assessment = get_record("workshop_assessments", "id", $_POST['aid'])) {
			error("Unable to insert comment");
			}
		// save the comment...
		$comment->workshopid = $workshop->id;
		$comment->assessmentid   = $assessment->id;
		$comment->userid   = $USER->id;
		$comment->timecreated   = $timenow;
		$comment->comments   = $form->comments;
		if (!$comment->id = insert_record("workshop_comments", $comment)) {
			error("Could not insert workshop comment!");
			}
			
		add_to_log($course->id, "workshop", "comment", "view.php?id=$cm->id", "$comment->id");

		print_continue("assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id");
		}


	/*********************** insert/update assignment elements (for teachers)***********************/
	elseif ($action == 'insertelements') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		$form = (object)$HTTP_POST_VARS;
		
		// let's not fool around here, dump the junk!
		delete_records("workshop_elements", "workshopid", $workshop->id);
		
		// determine wich type of grading
		switch ($workshop->gradingstrategy) {
			case 0: // no grading
				// Insert all the elements that contain something
				foreach ($form->description as $key => $description) {
					if ($description) {
						unset($element);
						$element->description   = $description;
						$element->workshopid = $workshop->id;
						$element->elementno = $key;
						if (!$element->id = insert_record("workshop_elements", $element)) {
							error("Could not insert workshop element!");
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
						$element->workshopid = $workshop->id;
						$element->elementno = $key;
						if (isset($form->scale[$key])) {
							$element->scale = $form->scale[$key];
							switch ($WORKSHOP_SCALES[$form->scale[$key]]['type']) {
								case 'radio' :	$element->maxscore = $WORKSHOP_SCALES[$form->scale[$key]]['size'] - 1;
														break;
								case 'selection' :	$element->maxscore = $WORKSHOP_SCALES[$form->scale[$key]]['size'];
														break;
							}
						}
						if (isset($form->weight[$key])) {
							$element->weight = $form->weight[$key];
						}
						if (!$element->id = insert_record("workshop_elements", $element)) {
							error("Could not insert workshop element!");
						}
					}
				}
				break;
				
			case 2: // error banded grading...
			case 3: // ...and criterion grading
				// Insert all the elements that contain something, the number of descriptions is one less than the number of grades
				foreach ($form->maxscore as $key => $themaxscore) {
					unset($element);
					$element->workshopid = $workshop->id;
					$element->elementno = $key;
					$element->maxscore = $themaxscore;
					if (isset($form->description[$key])) {
						$element->description   = $form->description[$key];
					}
					if (isset($form->weight[$key])) {
						$element->weight = $form->weight[$key];
					}
					if (!$element->id = insert_record("workshop_elements", $element)) {
						error("Could not insert workshop element!");
					}
				}
				break;
				
			case 4: // ...and criteria grading
				// Insert all the elements that contain something
				foreach ($form->description as $key => $description) {
					unset($element);
					$element->workshopid = $workshop->id;
					$element->elementno = $key;
					$element->description   = $description;
					$element->weight = $form->weight[$key];
					for ($j=0;$j<5;$j++) {
						if (empty($form->rubric[$key][$j]))
							break;
					}
					$element->maxscore = $j - 1;
					if (!$element->id = insert_record("workshop_elements", $element)) {
						error("Could not insert workshop element!");
					}
				}
				// let's not fool around here, dump the junk!
				delete_records("workshop_rubrics", "workshopid", $workshop->id);
				for ($i=0;$i<$workshop->nelements;$i++) {
					for ($j=0;$j<5;$j++) {
						unset($element);
						if (empty($form->rubric[$i][$j])) {  // OK to have an element with fewer than 5 items
							 break;
						 }
						$element->workshopid = $workshop->id;
						$element->elementno = $i;
						$element->rubricno = $j;
						$element->description   = $form->rubric[$i][$j];
						if (!$element->id = insert_record("workshop_rubrics", $element)) {
							error("Could not insert workshop element!");
						}
					}
				}
				break;
		} // end of switch

		redirect("view.php?id=$cm->id", get_string("savedok","workshop"));
	}


	/*********************** list assessments for grading (Student submissions)(by teachers)***********************/
	elseif ($action == 'listungradedstudentsubmissions') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		workshop_list_ungraded_assessments($workshop, "student");
		print_continue("view.php?a=$workshop->id");
		}


	/*********************** list assessments for grading (Teacher submissions) (by teachers)***********************/
	elseif ($action == 'listungradedteachersubmissions') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}
		workshop_list_ungraded_assessments($workshop, "teacher");
		print_continue("view.php?a=$workshop->id");
		}


	/****************** list teacher submissions ***********************/
	elseif ($action == 'listteachersubmissions') {

		workshop_list_teacher_submissions($workshop, $USER);
		print_continue("view.php?a=$workshop->id");
	}


	/*************** update assessment (by teacher or student) ***************************/
	elseif ($action == 'updateassessment') {

		require_variable($aid);
		if (! $assessment = get_record("workshop_assessments", "id", $aid)) {
			error("workshop assessment is misconfigured");
		}

		// first get the assignment elements for maxscores and weights...
		if (!$elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC")) {
			print_string("noteonassignmentelements", "workshop");
		}
		else {
			foreach ($elementsraw as $element) {
				$elements[] = $element;   // to renumber index 0,1,2...
			}
		}

		$timenow = time();
        // don't fiddle about, delete all the old and add the new!
		delete_records("workshop_grades", "assessmentid",  $assessment->id);
		
		$form = (object)$HTTP_POST_VARS;
		
		//determine what kind of grading we have
		switch ($workshop->gradingstrategy) {
			case 0: // no grading
				// Insert all the elements that contain something
				foreach ($form->feedback as $key => $thefeedback) {
					unset($element);
					$element->workshopid = $workshop->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $thefeedback;
					if (!$element->id = insert_record("workshop_grades", $element)) {
						error("Could not insert workshop element!");
					}
				}
				$grade = 0; // set to satisfy save to db
				break;
				
			case 1: // accumulative grading
				// Insert all the elements that contain something
				foreach ($form->grade as $key => $thegrade) {
					unset($element);
					$element->workshopid = $workshop->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $form->feedback[$key];
					$element->grade = $thegrade;
					if (!$element->id = insert_record("workshop_grades", $element)) {
						error("Could not insert workshop element!");
						}
					}
				// now work out the grade...
				$rawgrade=0;
				$totalweight=0;
				foreach ($form->grade as $key => $grade) {
					$maxscore = $elements[$key]->maxscore;
					$weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
					if ($weight > 0) { 
						$totalweight += $weight;
					}
					$rawgrade += ($grade / $maxscore) * $weight;
					// echo "\$key, \$maxscore, \$weight, \$totalweight, \$grade, \$rawgrade : $key, $maxscore, $weight, $totalweight, $grade, $rawgrade<BR>";
				}
				$grade = $workshop->grade * ($rawgrade / $totalweight);
				break;

			case 2: // error banded graded
				// Insert all the elements that contain something
				$error = 0.0; 
				for ($i =0; $i < $workshop->nelements; $i++) {
					unset($element);
					$element->workshopid = $workshop->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $i;
					$element->feedback   = $form->feedback[$i];
					$element->grade = $form->grade[$i];
					if (!$element->id = insert_record("workshop_grades", $element)) {
						error("Could not insert workshop element!");
					}
	    			if (empty($form->grade[$i])){
						$error += $WORKSHOP_EWEIGHTS[$elements[$i]->weight];
					}
				}
				// now save the adjustment
				unset($element);
				$i = $workshop->nelements;
				$element->workshopid = $workshop->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = $i;
				$element->grade = $form->grade[$i];
				if (!$element->id = insert_record("workshop_grades", $element)) {
					error("Could not insert workshop element!");
				}
				$grade = ($elements[intval($error + 0.5)]->maxscore + $form->grade[$i]);
				echo "<P><B>".get_string("weightederrorcount", "workshop", intval($error + 0.5))."</B>\n";
				break;
			
			case 3: // criteria grading
				// save in the selected criteria value in element zero, 
				unset($element);
				$element->workshopid = $workshop->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = 0;
				$element->grade = $form->grade[0];
				if (!$element->id = insert_record("workshop_grades", $element)) {
					error("Could not insert workshop element!");
				}
				// now save the adjustment in element one
				unset($element);
				$element->workshopid = $workshop->id;
				$element->assessmentid = $assessment->id;
				$element->elementno = 1;
				$element->grade = $form->grade[1];
				if (!$element->id = insert_record("workshop_grades", $element)) {
					error("Could not insert workshop element!");
				}
				$grade = ($elements[$form->grade[0]]->maxscore + $form->grade[1]) * $workshop->grade / 100;
				break;

			case 4: // rubric grading (identical to accumulative grading)
				// Insert all the elements that contain something
				foreach ($form->grade as $key => $thegrade) {
					unset($element);
					$element->workshopid = $workshop->id;
					$element->assessmentid = $assessment->id;
					$element->elementno = $key;
					$element->feedback   = $form->feedback[$key];
					$element->grade = $thegrade;
					if (!$element->id = insert_record("workshop_grades", $element)) {
						error("Could not insert workshop element!");
					}
				}
				// now work out the grade...
				$rawgrade=0;
				$totalweight=0;
				foreach ($form->grade as $key => $grade) {
					$maxscore = $elements[$key]->maxscore;
					$weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
					if ($weight > 0) { 
						$totalweight += $weight;
					}
					$rawgrade += ($grade / $maxscore) * $weight;
				}
				$grade = $workshop->grade * ($rawgrade / $totalweight);
				break;

		} // end of switch
			
		// update the time of the assessment record (may be re-edited)...
		set_field("workshop_assessments", "timecreated", $timenow, "id", $assessment->id);
		
		if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
			error ("Updateassessment: submission record not found");
		}
		
		// if the workshop does need peer agreement AND it's self assessment then set timeagreed
		if ($workshop->agreeassessments and ($submission->userid == $assessment->userid)) {
			set_field("workshop_assessments", "timeagreed", $timenow, "id", $assessment->id);
		}
		
		set_field("workshop_assessments", "grade", $grade, "id", $assessment->id);
		// ...and clear any grading of this assessment...
		set_field("workshop_assessments", "timegraded", 0, "id", $assessment->id);
		set_field("workshop_assessments", "gradinggrade", 0, "id", $assessment->id);
		// ...and the resubmission flag
        set_field("workshop_assessments", "resubmission", 0, "id", $assessment->id);
		
        // any comment?
		if (!empty($form->generalcomment)) {
			set_field("workshop_assessments", "generalcomment", $form->generalcomment, "id", $assessment->id);
		}
			
	    add_to_log($course->id, "workshop", "assess",
                "assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id", "$assessment->id", "$cm->id");
		
		// set up return address
		if (!$returnto = $form->returnto) {
			$returnto = "view.php?id=$cm->id";
		}
			
		// show grade if grading strategy is not zero
		if ($workshop->gradingstrategy) {
			redirect($returnto, get_string("thegradeis", "workshop").": ".number_format($grade, 2).
                    " (".get_string("maximumgrade")." ".number_format($workshop->grade).")");
		}
		else {
			redirect($returnto);
		}
	}


	/****************** update comment (by author, assessor or teacher) ********************/
	elseif ($action == 'updatecomment') {
		$timenow = time();

		$form = (object)$_POST;
		
		// get the comment record...
		if (!$comment = get_record("workshop_comments", "id", $_POST['cid'])) {
			error("Update to Comment failed");
		}
		if (!$assessment = get_record("workshop_assessments", "id", $comment->assessmentid)) {
			error("Update Comment: Assessment not found");
		}
		//save the comment for the assessment...
		if (isset($form->comments)) {
			set_field("workshop_comments", "comments", $form->comments, "id", $comment->id);
			set_field("workshop_comments", "timecreated", $timenow, "id", $comment->id);
			// ..and kick to comment into life (probably not needed but just in case)
			set_field("workshop_comments", "mailed", 0, "id", $comment->id);
			echo "<CENTRE><B>".get_string("savedok", "workshop")."</B></CENTER><BR>\n";
			
			add_to_log($course->id, "workshop", "comment", 
                    "assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id", "$comment->id");
		}

		print_continue("assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id");
	}


	/****************** update grading (by teacher) ***************************/
	elseif ($action == 'updategrading') {
		$timenow = time();

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		$form = (object)$_POST;
		
		if (!$assessment = get_record("workshop_assessments", "id", $_POST['aid'])) {
			error("Update Grading failed");
		}
		//save the comment and grade for the assessment 
		if (isset($form->teachercomment)) {
			set_field("workshop_assessments", "teachercomment", $form->teachercomment, "id", $assessment->id);
			set_field("workshop_assessments", "gradinggrade", $form->gradinggrade, "id", $assessment->id);
			set_field("workshop_assessments", "timegraded", $timenow, "id", $assessment->id);
			set_field("workshop_assessments", "mailed", 0, "id", $assessment->id);
			echo "<CENTRE><B>".get_string("savedok", "workshop")."</B></CENTRE><BR>\n";
			
			add_to_log($course->id, "workshop", "grade", 
                 "assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id", "$assessment->id", "$cm->id");
		}
		switch ($form->stype) {
			case "student" : 
				redirect("assessments.php?action=listungradedstudentsubmissions&id=$cm->id");
				break;
			case "teacher" : 
				redirect("assessments.php?action=listungradedteachersubmissions&id=$cm->id");
				break;
		}
	}


	/****************** user confirm delete ************************************/
	elseif ($action == 'userconfirmdelete' ) {

		if (empty($_GET['aid'])) {
			error("User confirm delete: assessment id missing");
		}
			
		notice_yesno(get_string("confirmdeletionofthisitem","workshop", 
                get_string("assessment", "workshop")), 
                "assessments.php?action=userdelete&id=$cm->id&aid=$_GET[aid]", "view.php?id=$cm->id");
	}
	

	/****************** user delete ************************************/
	elseif ($action == 'userdelete' ) {

		if (empty($_GET['aid'])) {
			error("User delete: assessment id missing");
		}
			
		print_string("deleting", "workshop");
		// first delete all the associated records...
		delete_records("workshop_comments", "assessmentid", $_GET['aid']);
		delete_records("workshop_grades", "assessmentid", $_GET['aid']);
		// ...now delete the assessment...
		delete_records("workshop_assessments", "id", $_GET['aid']);
		
		print_continue("view.php?id=$cm->id");
	}
	

	/****************** view all assessments ***********************/
	elseif ($action == 'viewallassessments') {
		
		if (!$submission = get_record("workshop_submissions", "id", $_GET['sid'])) {
			error("View All Assessments: submission record not found");
		}		
			
		if ($assessments = workshop_get_assessments($submission)) {
			foreach ($assessments as $assessment) {
				workshop_print_assessment($workshop, $assessment);
			}
		}
		// only called from list all submissions
		print_continue("submissions.php?action=listallsubmissions&id=$cm->id");
	}


	/****************** view assessment *****************************/
	elseif ($action == 'viewassessment') {

		optional_variable($allowcomments);
		if (!isset($allowcomments)) {
			$allowcomments = false;
		}
	
		// get the assessment record
		if (!$assessment = get_record("workshop_assessments", "id", $_GET['aid'])) {
			error("Assessment record not found");
		}		

		// show assessment but don't allow changes
		workshop_print_assessment($workshop, $assessment, false, $allowcomments);
		
		print_continue("view.php?a=$workshop->id");
	}


	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

	print_footer($course);
 
?>

