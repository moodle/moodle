<?PHP  // $Id: lib.php,v 1.1 22 Aug 2003

/*************************************************
	ACTIONS handled are:

	adminconfirmdelete
	admindelete
	adminlist
    analysisofassessments
	calculatefinalgrades
	displayfinalgrades (teachers only)
	displayfinalweights
	listallsubmissions
	listforassessmentstudent
	listforassessmentteacher
	updateoverallocation
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
	

	/*************** analysis of assessments (by teacher) ***************************/
	elseif ($action == 'analysisofassessments') {

        // timeout after 10 minutes
        @set_time_limit(600);
        
		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

        // this analysis does not use bias or reliability...
        set_field("workshop", "biasweight", 0, "id", $workshop->id);
        set_field("workshop", "reliabilityweight", 0, "id", $workshop->id);
        // ...and unity weights for teacher and peer assessments
        set_field("workshop", "teacherweight", 5, "id", $workshop->id);
        set_field("workshop", "peerweight", 5, "id", $workshop->id);

        echo "<form name=\"optionsform\" method=\"post\" action=\"submissions.php\">\n";
        echo "<INPUT TYPE=\"hidden\" NAME=\"id\" VALUE=\"$cm->id\">\n";
        echo "<input type=\"hidden\" name=\"action\" value=\"saveanalysisoptions\">\n";

        // get the options from the database...
        $teacherloading = get_field("workshop", "teacherloading", "id", $workshop->id);
        $gradingweight = get_field("workshop", "gradingweight", "id", $workshop->id);
        $assessmentstodrop = get_field("workshop", "assessmentstodrop", "id", $workshop->id);

        // ...now show the options used in a table
        print_heading_with_help(get_string("analysisofassessments", "workshop"), "analysisofassessments",
                "workshop");
        echo "<center><TABLE WIDTH=\"50%\" BORDER=\"1\">\n";
        echo "<TR><td COLSPAN=\"2\" bgcolor=\"$THEME->cellheading2\"><CENTER><B>".
            get_string("optionsusedinanalysis", "workshop")."</B></CENTER></TD></TR>\n";
        echo "<tr><td align=\"right\">".get_string("loadingforteacherassessments", "workshop", 
                $course->teacher).":</td>\n";
        echo "<TD>";
        workshop_choose_from_menu($WORKSHOP_FWEIGHTS, "teacherloading", $teacherloading, "");
        echo "</TD></TR>\n";
        echo "<tr><td align=\"right\">".get_string("weightforgradingofassessments", "workshop").":</td>\n";
        echo "<TD>";
        workshop_choose_from_menu($WORKSHOP_FWEIGHTS, "gradingweight", $gradingweight, "");
        echo "</TD></TR>\n";
        echo "<TR><TD ALIGN=\"right\">".get_string("percentageofassessments", "workshop").":</TD>\n";
        echo "<TD>";
		for ($i = 0; $i <= 100; $i++) {
			$numbers[$i] = $i;
		}
        choose_from_menu($numbers, "assessmentstodrop", $assessmentstodrop, "");
        echo "</TD></TR>\n";
        echo "</TABLE><br />\n";
        echo "<INPUT TYPE=submit VALUE=\"".get_string("repeatanalysis", "workshop")."\">\n";
        echo "</FORM>\n";


        // set up the array of users who have made assessments
   		if (!$students = get_course_students($course->id, "u.lastname, u.firstname")) {
    		print_heading(get_string("nostudentsyet"));
	    	print_footer($course);
		    exit;
        }
        $teachers = get_course_teachers($course->id);
        $users = array_merge($students, $teachers);
        $nassessments = 0;
        foreach ($users as $user) {
            if ($assessments = workshop_get_user_assessments_done($workshop, $user)) {
                // the value put into the array element is not particularly important at this stage
                // it will hold the user's assessment error after the first iteration 
                $n = count($assessments);
                $assessors[$user->id] = $n;
                $nassessments += $n;
            }
        }

        $ntodrop = intval(($assessmentstodrop * $nassessments / 100.0) + 0.5); 

        // set minumim value for the variance (of the elements)
        $minvar = 0.05;
           
        flush();
        // we now do up to five iterations, the first with all users. The second and subsequent if
        // the number of assessors is not the full set. Two or three iterations with the reduced set
        // should be enough to stablise the list of dropped assessments.
        if ($ntodrop == 0) {
            $loopcount = 1;
        } else {
            $loopcount = 7;  // max loops, should finish before that loop
        }
        for ($loop = 0; $loop < $loopcount; $loop++) {
            // calculate the means for each submission using just the "good" assessments 
            // on the first iteration all the assessments are included
            unset($num);
            unset($sum);
            foreach ($assessors as $userid => $error) {
                if (!$user = get_record("user", "id", $userid)) {
                    error("Analysis of assessments: User record not found");
                }
                $assessments = workshop_get_user_assessments_done($workshop, $user);
                foreach ($assessments as $assessment) {
                    if (isset($drop[$assessment->id])) {
                        continue;
                    }
                    if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
                        error("Analysis of Assessments: submission record not found");
                    }
                    if (isset($num[$submission->id])) {
                        if (isteacher($course->id, $userid)) {
                            $num[$submission->id] += $WORKSHOP_FWEIGHTS[$workshop->teacherloading];
                        } else {
                            $num[$submission->id]++;
                        }
                    } else {
                        if (isteacher($course->id, $userid)) {
                            $num[$submission->id] = $WORKSHOP_FWEIGHTS[$workshop->teacherloading];
                        } else {
                            $num[$submission->id] = 1;
                        }
                    }
                    for ($i = 0; $i < $workshop->nelements; $i++) {
                        $grade =  get_field("workshop_grades", "grade",
                                    "assessmentid", $assessment->id, "elementno", $i);
                        if (isset($sum[$submission->id][$i])) {
                            if (isteacher($course->id, $userid)) {
                                $sum[$submission->id][$i] += $grade * $WORKSHOP_FWEIGHTS[$workshop->teacherloading];
                            } else {
                                $sum[$submission->id][$i] += $grade;
                            }
                        } else {
                            if (isteacher($course->id, $userid)) {
                                $sum[$submission->id][$i] = $grade * $WORKSHOP_FWEIGHTS[$workshop->teacherloading];
                            } else {
                                $sum[$submission->id][$i] = $grade;
                            }
                        }
                    }
                }
	        }
            reset($num);
            if (!$loop) {
                echo "<p>".get_string("numberofsubmissions", "workshop", count($num))."</p>\n";
            }

            // (re)calculate the means for each submission
            foreach ($num as $submissionid => $n) {
                for ($i = 0; $i < $workshop->nelements; $i++) {
                    $mean[$submissionid][$i] = $sum[$submissionid][$i] / $n;
                    // echo "Submission: $submissionid; Element: $i; Mean: {$mean[$submissionid][$i]}<br />\n";
                }
            }

            // only calculate the sd's and the error from guessing once
            if (!$loop) {
                // now get an estimate of the standard deviation of each element in the assessment
                $n = 0;
                for ($i = 0; $i < $workshop->nelements; $i++) {
                    $var[$i] = 0;
                }
                foreach ($assessors as $userid => $error) {
                    if (!$user = get_record("user", "id", $userid)) {
                        error("Submissions: User record not found");
                    }
                    $assessments = workshop_get_user_assessments_done($workshop, $user);
                    foreach ($assessments as $assessment) {
                        if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
                            error("Analysis of Assessments: submission record not found");
                        }
                        $n++;
                        for ($i = 0; $i < $workshop->nelements; $i++) {
                            $grade =  get_field("workshop_grades", "grade",
                                        "assessmentid", $assessment->id, "elementno", $i);
                            $temp = $mean[$submission->id][$i] - $grade;
                            $var[$i] += $temp * $temp;
                        }
                    }
                }
                for ($i = 0; $i < $workshop->nelements; $i++) {
                    $sd[$i] = sqrt($var[$i] / ($n - 1));
                    echo get_string("standarddeviation", "workshop", $i+1)." $sd[$i]<br />";
                    if ($sd[$i] <= $minvar) {
                            get_string("standarddeviationnote", "workshop")."<br />\n";
                    }
                echo "<br />\n";
                }

                // calculate the mean variance (error) if just guessing
                // first get the assignment elements for maxscores...
                $elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC");
                foreach ($elementsraw as $element) {
                    $maxscore[] = $element->maxscore;   // to renumber index 0,1,2...
                    $weight[] = $element->weight;   // to renumber index 0,1,2...
                }
                $n = 0;
                $totvar = 0;
                foreach ($assessors as $userid => $error) {
                    if (!$user = get_record("user", "id", $userid)) {
                        error("Submissions: User record not found");
                    }
                    $assessments = workshop_get_user_assessments_done($workshop, $user);
                    foreach ($assessments as $assessment) {
                        if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
                            error("Analysis of Assessments: submission record not found");
                        }
                        $n++;
                        for ($i = 0; $i < $workshop->nelements; $i++) {
                            $grade = mt_rand(0, $maxscore[$i]);
                            if ($sd[$i] > $minvar) {
                                $temp = ($mean[$submission->id][$i] - $grade) * 
                                    $WORKSHOP_EWEIGHTS[$weight[$i]] / $sd[$i]; 
                            } else {
                                $temp = 0;
                            }
                            $totvar += $temp * $temp;
                        }
                    }
                }
                // take the average of these variances
                $varguess = $totvar / $n;
            }
            
            // calculate the variance (error) for each assessment (adjusted after the first loop)
            // and work out the user's average error with all their assessments and without
            // the dropped assessments (their "good" assessments)
            foreach ($assessors as $userid => $error) {
                if (!$user = get_record("user", "id", $userid)) {
                    error("Submissions: User record not found");
                }
                $assessments = workshop_get_user_assessments_done($workshop, $user);
                $n = 0;
                $ngood = 0;
                $totvar = 0;
                $totvargood = 0;
                foreach ($assessments as $assessment) {
                    if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
                        error("Analysis of Assessments: submission record not found");
                    }
                    $n++;
                    $var = 0;
                    for ($i = 0; $i < $workshop->nelements; $i++) {
                        $grade =  get_field("workshop_grades", "grade",
                                    "assessmentid", $assessment->id, "elementno", $i);
                        if ($sd[$i] > $minvar) {
                            $temp = ($mean[$submission->id][$i] - $grade) * 
                                $WORKSHOP_EWEIGHTS[$weight[$i]] / $sd[$i];
                        } else {
                            $temp = 0;
                        }
                        $var += $temp * $temp;
                    }
                    // the variances are adjusted by the user's overall error (once it's calculated)
                    if ($loop) {
                        $assessmentvar[$assessment->id] = $var * $error;
                    } else {
                        $assessmentvar[$assessment->id] = $var;
                    }
                    $totvar += $var;
                    if (empty($drop[$assessment->id])) {
                        $ngood++;
                        $totvargood += $var;
                    }
                }
                $nsubmissions[$userid] = $n;
                $newassessors[$userid] = $totvar / $n;
                if ($ngood) {
                    $vargood[$userid] = $totvargood / $ngood;
                } else {
                    $vargood[$userid] = 0;
                }
                // echo "$user->firstname $user->lastname Error: {$newassessors[$userid]}; n: $n<br />\n";
	        }
            
            // echo "<hr>\n";
            
            
            // now drop the assessments with the largest (adjusted) variances
            $nchanged = 0;
            if ($ntodrop) {
                asort($assessmentvar);
                $n = 1;
                foreach ($assessmentvar as $assessmentid => $adjvar) {
                    if ($n <= ($nassessments - $ntodrop)) {
                        if (isset($drop[$assessmentid])) {
                            unset($drop[$assessmentid]);
                            $nchanged++;
                        }
                    } else {
                        if (empty($drop[$assessmentid])) {
                            $drop[$assessmentid] = 1;
                            $nchanged++;
                        }
                    }
                    $n++;
                }
            }
            
            // reset the assessors array
            $assessors = $newassessors;
            // put the assessors in order (for the next iteration, if there is one)
            asort($assessors);
            reset($assessors);
            $i = $loop + 1;
            echo get_string("iteration", "workshop", "$i / $loopcount")."<br />\n";
            echo get_string("numberofassessmentschanged", "workshop", $nchanged)."<br />\n";
            flush();
            if (!$nchanged) {
                break;
            }
        } // end of iteration loop
        
        // flag the assessments which were classed as outliers
        // but first clear any existing flags
        execute_sql("UPDATE {$CFG->prefix}workshop_assessments SET donotuse = 0 
                WHERE workshopid = $workshop->id", false);
        if ($ntodrop) {
            foreach ($drop as $assessmentid => $flag) {
                if (!set_field("workshop_assessments", "donotuse", 1, "id", $assessmentid)) {
                    error("Analysis of assessments: unable to set donotuse field");
                }
                $userid = get_field("workshop_assessments", "userid", "id", $assessmentid);
                if (empty($ndropped[$userid])) {
                    $ndropped[$userid] = 1;
                } else {
                    $ndropped[$userid]++;
                }
            }
        }

        // echo "<p>".get_string("expectederror", "workshop", $varguess)."</p>\n";
        print_heading(get_string("errortable", "workshop"));
		$table->head = array(" ",get_string("name"), get_string("averageerror", "workshop"), 
                get_string("averageerror", "workshop")."<br />".
                get_string("excludingdroppedassessments", "workshop"),
                get_string("numberofassessments", "workshop"));
		$table->align = array ("left","left", "center", "center", "center");
		$table->size = array ("*", "*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;
        $n = 1;
		foreach ($assessors as $userid => $error) {
			if (!$user = get_record("user", "id", $userid)) {
                error("Assessment analysis: user record not found");
            }
            if ($vargood[$userid]) {
                $vargoodtext = number_format($vargood[$userid] * 100 / $varguess, 2)."%";
            } else {
                $vargoodtext = "-";
            }
            if (empty($ndropped[$userid])) {
                $numtext = "$nsubmissions[$userid]";
            } else {
                $numtext = "$nsubmissions[$userid] &lt;$ndropped[$userid]&gt;";
            } 
            $table->data[] = array($n, "$user->firstname $user->lastname", 
                    number_format($error * 100 / $varguess, 2)."%", $vargoodtext, 
                    $numtext);
            $n++;
		}
		print_table($table);
        echo "<p>&lt; &gt; ".get_string("assessmentsexcluded", "workshop", $course->student)."</p>\n";
        echo "<p>".get_string("submissionsused", "workshop", count($num))."</p>\n";;
        
        // display student grades
        print_heading(get_string("gradetable", "workshop"));
        unset($table);
   		$table->head = array(get_string("name"), get_string("submission", "workshop"),
                get_string("assessmentsdone", "workshop"), get_string("assessments", "workshop"), 
                get_string("studentassessments", "workshop", $course->teacher),
                get_string("studentassessments", "workshop", $course->student),
                get_string("submission", "workshop"), get_string("overallgrade", "workshop"));
		$table->align = array ("left", "center", "center", "center", "center", "center", "center", "center");
		$table->size = array ("*", "*", "*", "*", "*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;
        $table->data[] = array("<b>".get_string("weight", "workshop")."</b>", " ", " ",  
                "<b>".$WORKSHOP_FWEIGHTS[$workshop->gradingweight]."</b>", " ", " ","<b>1</b>", " ");
        $maxassessments = $workshop->nsassessments + $workshop->ntassessments;
		foreach ($students as $user) {
            if ($assessments = workshop_get_user_assessments_done($workshop, $user)) {
                $n = 0;
                foreach ($assessments as $assessment) {
                    if (!$assessment->donotuse) {
                        $n++;
                    }
                }
                if ($maxassessments) {
                    $assessmentgrade = ($n / $maxassessments) * $workshop->grade;
                } else {
                    $assessmentgrade = 0;
                }
            } else {
                // no assessments
                $assessmentgrade = 0;
            }
            if ($submissions = workshop_get_user_submissions($workshop, $user)) {
                foreach ($submissions as $submission) {
                    $submissiongrade = 0;
                    $n = 0;
                    if ($assessments = workshop_get_assessments($submission)) {
                        $sum = 0;
                        foreach ($assessments as $assessment) {
                            if (!$assessment->donotuse) {
                                $n++;
                                $sum += $assessment->grade;
                            }
                        }
                        if ($n) {
                            $submissiongrade = $sum / $n;
                        }
                    }
                    $finalgrade = ($assessmentgrade * $WORKSHOP_FWEIGHTS[$workshop->gradingweight] +
                        $submissiongrade) / ($WORKSHOP_FWEIGHTS[$workshop->gradingweight] + 1.0);
        			if ($n) {
                        $table->data[] = array("$user->firstname $user->lastname", 
                            workshop_print_submission_title($workshop, $submission),
                            workshop_print_user_assessments($workshop, $user),
                            number_format($assessmentgrade, 2),
                            workshop_print_submission_assessments($workshop, $submission, "teacher"),
                            workshop_print_submission_assessments($workshop, $submission, "student"),
                            number_format($submissiongrade, 2),
                            number_format($finalgrade, 2));
                    } else {
        			    $table->data[] = array("$user->firstname $user->lastname", 
                            workshop_print_submission_title($workshop, $submission),
                            workshop_print_user_assessments($workshop, $user),
                            number_format($assessmentgrade, 2),  
                            workshop_print_submission_assessments($workshop, $submission, "teacher"),
                            workshop_print_submission_assessments($workshop, $submission, "student"),
                            "<b>".get_string("noassessments", "workshop")."</b>",
                            number_format($finalgrade, 2));
                    }
                    // save grades in submission record
                    set_field("workshop_submissions", "teachergrade", intval($submissiongrade + 0.5), "id", 
                            $submission->id);
                    set_field("workshop_submissions", "peergrade", intval($submissiongrade + 0.5), "id", 
                            $submission->id);
                    set_field("workshop_submissions", "finalgrade", intval($finalgrade + 0.5), "id", 
                            $submission->id);
                    set_field("workshop_submissions", "gradinggrade", intval($assessmentgrade + 0.5), "id", 
                            $submission->id);
                }       
            } else {
                // no submissions
                $finalgrade = ($assessmentgrade * $WORKSHOP_FWEIGHTS[$workshop->gradingweight]) /
                    ($WORKSHOP_FWEIGHTS[$workshop->gradingweight] + 1.0);
        		$table->data[] = array("$user->firstname $user->lastname", 
                            "-", workshop_print_user_assessments($workshop, $user),
                            number_format($assessmentgrade, 2), "-", "-",
                            get_string("nosubmission", "workshop"), 
                            number_format($finalgrade,2));
            }
		}
		print_table($table);
        echo "<p>&lt; &gt; ".get_string("assessmentdropped", "workshop")."</p>\n";
        echo "</CENTER>";
		print_continue("view.php?a=$workshop->id");
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
	
		// save number of entries in showleaguetable option
		if ($form->nentries == 'All') {
			$form->nentries = 99;
			}
		set_field("workshop", "showleaguetable", $form->nentries, "id", "$workshop->id");
		
		// save the anonymous option
		set_field("workshop", "anonymous", $form->anonymous, "id", "$workshop->id");
		
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
				unset($bestsubmission);
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
		if (!$users = get_course_students($course->id, "u.lastname, u.firstname")) {
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
		echo "<td bgcolor=\"$THEME->cellheading2\"><b>".get_string("submission", "workshop")."</b></td>";
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
		if ($workshop->showleaguetable) {
			workshop_print_league_table($workshop);
			if ($workshop->anonymous) {
				echo "<p>".get_string("namesnotshowntostudents", "workshop", $course->students)."</p>\n";
			}
		}
		echo "<p>".get_string("allgradeshaveamaximumof", "workshop", $workshop->grade)."</p>\n";
		print_continue("view.php?a=$workshop->id");
	}


	/*************** display final weights (by teacher) ***************************/
	elseif ($action == 'displayfinalweights') {

		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		if ($workshop->phase != 5) { // is this at the expected phase?
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

			print_heading_with_help(get_string("leaguetable", "workshop"), "leaguetable", "workshop");
			echo "<TABLE WIDTH=\"50%\" BORDER=\"1\">\n";
			echo "<tr><td align=\"right\">".get_string("numberofentries", "workshop").":</td>\n";
			echo "<TD>";
			$numbers[22] = 'All';
		    $numbers[21] = 50;
		    for ($i=20; $i>=0; $i--) {
				$numbers[$i] = $i;
				}
			$nentries = $workshop->showleaguetable;
			if ($nentries == 99) {
				$nentries = 'All';
				}
			choose_from_menu($numbers, "nentries", "$nentries", "");
			echo "</td></tr>\n";
			echo "<tr><td align=right><p>".get_string("hidenamesfromstudents", "workshop", $course->students)."</p></td><td>\n";
            $options[0] = get_string("no"); $options[1] = get_string("yes");
			choose_from_menu($options, "anonymous", $workshop->anonymous, "");
			echo "</td></tr>\n";
			echo "</table><br />\n";
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
	

	/******************* save analysis options (for teachers only) ************************************/
	elseif ($action == 'saveanalysisoptions' ) {
	
        if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
			}

		set_field("workshop", "teacherloading", $_POST['teacherloading'], "id", "$workshop->id");
		set_field("workshop", "gradingweight", $_POST['gradingweight'], "id", "$workshop->id");
		set_field("workshop", "assessmentstodrop", $_POST['assessmentstodrop'], "id", "$workshop->id");
	    redirect("submissions.php?id=$cm->id&action=analysisofassessments", 
                get_string("savedok", "workshop"));
		
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


	/*************** update over allocation (by teacher) ***************************/
	elseif ($action == 'updateoverallocation') {
		
		if (!isteacher($course->id)) {
			error("Only teachers can look at this page");
		}

		$form = (object)$_POST;
		
		set_field("workshop", "overallocation", $form->overallocation, "id", $workshop->id);
		echo "<p align=\"center\"><b>".get_string("overallocation", "workshop").": $form->overallocation</b></p>\n";
		add_to_log($course->id, "workshop", "over allocation", "view.php?id=$cm->id", $form->overallocation,$cm->id);

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

