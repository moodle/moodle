<?PHP  // $Id: lib.php,v 1.1 23 Aug 2003

include_once("$CFG->dirroot/files/mimetypes.php");

/*** Constants **********************************/

$EXERCISE_TYPE = array (0 => get_string("notgraded", "exercise"),
                          1 => get_string("accumulative", "exercise"),
                          2 => get_string("errorbanded", "exercise"),
                          3 => get_string("criterion", "exercise"),
						  4 => get_string("rubric", "exercise") );

$EXERCISE_SCALES = array( 
					0 => array( 'name' => get_string("scaleyes", "exercise"), 'type' => 'radio', 'size' => 2, 'start' => get_string("yes"), 'end' => get_string("no")),
					1 => array( 'name' => get_string("scalepresent", "exercise"), 'type' => 'radio', 'size' => 2, 'start' => get_string("present", "exercise"), 'end' => get_string("absent", "exercise")),
					2 => array( 'name' => get_string("scalecorrect", "exercise"), 'type' => 'radio', 'size' => 2, 'start' => get_string("correct", "exercise"), 'end' => get_string("incorrect", "exercise")), 
					3 => array( 'name' => get_string("scalegood3", "exercise"), 'type' => 'radio', 'size' => 3, 'start' => get_string("good", "exercise"), 'end' => get_string("poor", "exercise")), 
					4 => array( 'name' => get_string("scaleexcellent4", "exercise"), 'type' => 'radio', 'size' => 4, 'start' => get_string("excellent", "exercise"), 'end' => get_string("verypoor", "exercise")),
					5 => array( 'name' => get_string("scaleexcellent5", "exercise"), 'type' => 'radio', 'size' => 5, 'start' => get_string("excellent", "exercise"), 'end' => get_string("verypoor", "exercise")),
					6 => array( 'name' => get_string("scaleexcellent7", "exercise"), 'type' => 'radio', 'size' => 7, 'start' => get_string("excellent", "exercise"), 'end' => get_string("verypoor", "exercise")),
					7 => array( 'name' => get_string("scale10", "exercise"), 'type' => 'selection', 'size' => 10),
					8 => array( 'name' => get_string("scale20", "exercise"), 'type' => 'selection', 'size' => 20),
					9 => array( 'name' => get_string("scale100", "exercise"), 'type' => 'selection', 'size' => 100)); 

$EXERCISE_EWEIGHTS = array(  0 => -4.0, 1 => -2.0, 2 => -1.5, 3 => -1.0, 4 => -0.75, 5 => -0.5,  6 => -0.25, 
											7 => 0.0, 8 => 0.25, 9 => 0.5, 10 => 0.75, 11=> 1.0, 12 => 1.5, 13=> 2.0, 14 => 4.0); 

$EXERCISE_FWEIGHTS = array(  0 => 0, 1 => 0.1, 2 => 0.25, 3 => 0.5, 4 => 0.75, 5 => 1.0,  6 => 1.5, 
											7 => 2.0, 8 => 3.0, 9 => 5.0, 10 => 7.5, 11=> 10.0); 

if (!defined("COMMENTSCALE")) {
	define("COMMENTSCALE", 20);
	}

/*** Standard Moodle functions ******************
function exercise_add_instance($exercise) 
function exercise_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
function exercise_cron () 
function exercise_delete_instance($id) 
function exercise_grades($exerciseid) 
function exercise_print_recent_activity(&$logs, $isteacher=false) 
function exercise_update_instance($exercise) 
function exercise_user_outline($course, $user, $mod, $exercise) 
function exercise_user_complete($course, $user, $mod, $exercise) 
**********************************************/

/*******************************************************************/
function exercise_add_instance($exercise) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $exercise->timemodified = time();
    
    $exercise->deadline = make_timestamp($exercise->deadlineyear, 
			$exercise->deadlinemonth, $exercise->deadlineday, $exercise->deadlinehour, 
			$exercise->deadlineminute);

    return insert_record("exercise", $exercise);
}


/*******************************************************************/
function exercise_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
/// Given an array of value, creates a popup menu to be part of a form
/// $options["value"]["label"]
    
    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    if ($script) {
        $javascript = "onChange=\"$script\"";
    } else {
        $javascript = "";
    }

    $output = "<SELECT NAME=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <OPTION VALUE=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " SELECTED";
        }
        $output .= ">$nothing</OPTION>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <OPTION VALUE=\"$value\"";
            if ($value == $selected) {
                $output .= " SELECTED";
            }
			// stop zero label being replaced by array index value
            // if ($label) {
            //    $output .= ">$label</OPTION>\n";
            // } else {
            //     $output .= ">$value</OPTION>\n";
			//  }
			$output .= ">$label</OPTION>\n";
            
        }
    }
    $output .= "</SELECT>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}   


/*******************************************************************/
function exercise_cron () {
// Function to be run periodically according to the moodle cron
// Finds all exercise notifications that have yet to be mailed out, and mails them

    global $CFG, $USER;

    $cutofftime = time() - $CFG->maxeditingtime;

	// look for new assessments
	if ($assessments = exercise_get_unmailed_assessments($cutofftime)) {
        $timenow = time();

        foreach ($assessments as $assessment) {

			echo "Processing exercise assessment $assessment->id\n";
			if (! $submission = get_record("exercise_submissions", "id", "$assessment->submissionid")) {
				echo "Could not find submission $assessment->submissionid\n";
				continue;
			}
			if (! $exercise = get_record("exercise", "id", $submission->exerciseid)) {
				echo "Could not find exercise record for id $submission->exerciseid\n";
				continue;
			}
			if (! $course = get_record("course", "id", "$exercise->course")) {
				echo "Could not find course $exercise->course\n";
				continue;
			}
            if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
                error("Course Module ID was incorrect");
                continue;
            }
			if (! $submissionowner = get_record("user", "id", "$submission->userid")) {
				echo "Could not find user $submission->userid\n";
				continue;
			}
			if (! $assessmentowner = get_record("user", "id", "$assessment->userid")) {
				echo "Could not find user $assessment->userid\n";
				continue;
			}
			if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, $submissionowner->id)) {
				continue;  // Not an active participant
			}
			if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, $assessmentowner->id)) {
				continue;  // Not an active participant
			}
	
			$strexercises = get_string("modulenameplural", "exercise");
			$strexercise  = get_string("modulename", "exercise");
	
			// it's an assessment, tell the submission owner
			$USER->lang = $submissionowner->lang;
			$sendto = $submissionowner;
			// "Your assignment \"$submission->title\" has been assessed by"
			$msg = get_string("mail1", "exercise", $submission->title)." $assessmentowner->firstname $assessmentowner->lastname.\n";
			// "The comments and grade can be seen in the exercise assignment '$exercise->name'
			$msg .= get_string("mail2", "exercise", $exercise->name)."\n\n";
	
			$postsubject = "$course->shortname: $strexercises: $exercise->name";
			$posttext  = "$course->shortname -> $strexercises -> $exercise->name\n";
			$posttext .= "---------------------------------------------------------------------\n";
			$posttext .= $msg;
			// "You can see it in your exercise assignment"
			$posttext .= get_string("mail3", "exercise").":\n";
			$posttext .= "   $CFG->wwwroot/mod/exercise/view.php?id=$cm->id\n";
			$posttext .= "---------------------------------------------------------------------\n";
			if ($sendto->mailformat == 1) {  // HTML
				$posthtml = "<P><FONT FACE=sans-serif>".
			  "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
			  "<A HREF=\"$CFG->wwwroot/mod/exercise/index.php?id=$course->id\">$strexercises</A> ->".
			  "<A HREF=\"$CFG->wwwroot/mod/exercise/view.php?id=$cm->id\">$exercise->name</A></FONT></P>";
			  $posthtml .= "<HR><FONT FACE=sans-serif>";
			  $posthtml .= "<P>$msg</P>";
			  $posthtml .= "<P>".get_string("mail3", "exercise").
				  " <A HREF=\"$CFG->wwwroot/mod/exercise/view.php?id=$cm->id\">$exercise->name</A>.</P></FONT><HR>";
			} else {
			  $posthtml = "";
			}
	
			if (!$teacher = get_teacher($course->id)) {
				echo "Error: can not find teacher for course $course->id!\n";
				}
				
			if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
				echo "Error: exercise cron: Could not send out mail for id $submission->id to user $sendto->id ($sendto->email)\n";
				}
			if (! set_field("exercise_assessments", "mailed", "1", "id", "$assessment->id")) {
				echo "Could not update the mailed field for id $assessment->id\n";
				}
			}
		}
		
	// look for new gradings
	if ($assessments = exercise_get_unmailed_graded_assessments($cutofftime)) {
        $timenow = time();

        foreach ($assessments as $assessment) {

            echo "Processing exercise assessment $assessment->id (graded)\n";

			if (! $submission = get_record("exercise_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
                continue;
            }
			if (! $exercise = get_record("exercise", "id", $submission->exerciseid)) {
				echo "Could not find exercise record for id $submission->exerciseid\n";
				continue;
			}
			if (! $course = get_record("course", "id", "$exercise->course")) {
				echo "Could not find course $exercise->course\n";
				continue;
			}
            if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
                error("Course Module ID was incorrect");
                continue;
            }

			if (! $assessmentowner = get_record("user", "id", "$assessment->userid")) {
                echo "Could not find user $assessment->userid\n";
                continue;
            }

            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, $assessmentowner->id)) {
                continue;  // Not an active participant
            }

            $strexercises = get_string("modulenameplural", "exercise");
            $strexercise  = get_string("modulename", "exercise");

			// it's a grading tell the assessment owner
			$USER->lang = $assessmentowner->lang;
			$sendto = $assessmentowner;
			// Your assessment of the assignment \"$submission->title\" has by reviewed
			$msg = get_string("mail6", "exercise", $submission->title).".\n";
			// The comments given by the $course->teacher can be seen in the exercise Assignment 
			$msg .= get_string("mail7", "exercise", $course->teacher)." '$exercise->name'.\n\n";

			$postsubject = "$course->shortname: $strexercises: $exercise->name";
            $posttext  = "$course->shortname -> $strexercises -> $exercise->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
			// "You can see it in your exercise assignment"
			$posttext .= get_string("mail3", "exercise").":\n";
			$posttext .= "   $CFG->wwwroot/mod/exercise/view.php?id=$cm->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
				$posthtml = "<P><FONT FACE=sans-serif>".
					"<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
					"<A HREF=\"$CFG->wwwroot/mod/exercise/index.php?id=$cm->id\">$strexercises</A> ->".
					"<A HREF=\"$CFG->wwwroot/mod/exercise/view.php?a=$exercise->id\">$exercise->name</A></FONT></P>";
				$posthtml .= "<HR><FONT FACE=sans-serif>";
				$posthtml .= "<P>$msg</P>";
				$posthtml .= "<P>".get_string("mail3", "exercise").
					" <A HREF=\"$CFG->wwwroot/mod/exercise/view.php?id=$cm->id\">$exercise->name</A>.</P></FONT><HR>";
            } else {
              $posthtml = "";
            }

			if (!$teacher = get_teacher($course->id)) {
				echo "Error: can not find teacher for course $course->id!\n";
				}
				
            if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: exercise cron: Could not send out mail for id $submission->id to user $sendto->id ($sendto->email)\n";
            }
            if (! set_field("exercise_assessments", "mailed", "1", "id", "$assessment->id")) {
                echo "Could not update the mailed field for id $assessment->id\n";
            }
        }
    }
    return true;
}


/*******************************************************************/
function exercise_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $exercise = get_record("exercise", "id", "$id")) {
        return false;
    }
	
	// delete all the associated records in the exercise tables, start positive...
    $result = true;

    if (! delete_records("exercise_grades", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_rubrics", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_elements", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_assessments", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise_submissions", "exerciseid", "$exercise->id")) {
        $result = false;
    }

    if (! delete_records("exercise", "id", "$exercise->id")) {
        $result = false;
    }

    return $result;
}


/*******************************************************************/
function exercise_grades($exerciseid) {
/// Must return an array of grades, indexed by user, and a max grade.
global $EXERCISE_FWEIGHTS;
	
	if (!$exercise = get_record("exercise", "id", $exerciseid)) {
		error("Exercise record not found");
	}
	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }

	// calculate scaling factor
	$scaling = $exercise->grade / (100.0 * ($EXERCISE_FWEIGHTS[$exercise->gradingweight] +
		$EXERCISE_FWEIGHTS[$exercise->teacherweight]));
	// how to handle multiple submissions?
	if ($exercise->usemaximum) {
		// first get the teacher's grade for the best submission
		if ($bestgrades = exercise_get_best_submission_grades($exercise)) {
			foreach ($bestgrades as $grade) {
				$usergrade[$grade->userid] = $grade->grade * 
					$EXERCISE_FWEIGHTS[$exercise->teacherweight] * $scaling;
			}
		}
	}
	else { // use mean values
		if ($meangrades = exercise_get_mean_submission_grades($exercise)) {
			foreach ($meangrades as $grade) {
				$usergrade[$grade->userid] = $grade->grade * 
					$EXERCISE_FWEIGHTS[$exercise->teacherweight] * $scaling;
			}
		}
	}
	// now get the users grading grades
	if ($assessments = exercise_get_teacher_submission_assessments($exercise)) {
		foreach ($assessments as $assessment) {
			// add the grading grade if the student's work has been assessed
			if (isset($usergrade[$assessment->userid])) {
				$usergrade[$assessment->userid] += $assessment->gradinggrade * 
					$EXERCISE_FWEIGHTS[$exercise->gradingweight] * $scaling * 100.0 / COMMENTSCALE;
			}
		}
	}
    // tidy the numbers and set up the return array
    if (isset($usergrade)) {
        foreach ($usergrade as $userid => $g) {
            $return->grades[$userid] = number_format($g, 1);
        }
    }
    $return->maxgrade = $exercise->grade;
    
    return $return;
}


/*******************************************************************/
function exercise_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

	// have a look for new submissions (only show to teachers)
    $submitcontent = false;
	if ($isteacher) {
		if ($logs = exercise_get_submit_logs($course, $timestart)) {
			// got some, see if any belong to a visible module
			foreach ($logs as $log) {
				// Create a temp valid module structure (only need courseid, moduleid)
				$tempmod->course = $course->id;
				$tempmod->id = $log->exerciseid;
				//Obtain the visible property from the instance
				if (instance_is_visible("exercise",$tempmod)) {
					$submitcontent = true;
					break;
					}
				}
			// if we got some "live" ones then output them
			if ($submitcontent) {
				$strftimerecent = get_string("strftimerecent");
				print_headline(get_string("exercisesubmissions", "exercise").":");
				foreach ($logs as $log) {
					//Create a temp valid module structure (only need courseid, moduleid)
					$tempmod->course = $course->id;
					$tempmod->id = $log->exerciseid;
					//Obtain the visible property from the instance
					if (instance_is_visible("exercise",$tempmod)) {
						$date = userdate($log->time, $strftimerecent);
						echo "<p><font size=1>$date - $log->firstname $log->lastname<br />";
						echo "\"<a href=\"$CFG->wwwroot/mod/exercise/$log->url\">";
						echo "$log->name";
						echo "</a>\"</font></p>";
						}
					}
				}
			}
		}

	// have a look for new assessment gradings for this user 
    $gradecontent = false;
	if ($logs = exercise_get_grade_logs($course, $timestart)) {
		// got some, see if any belong to a visible module
		foreach ($logs as $log) {
			// Create a temp valid module structure (only need courseid, moduleid)
			$tempmod->course = $course->id;
			$tempmod->id = $log->exerciseid;
			//Obtain the visible property from the instance
			if (instance_is_visible("exercise",$tempmod)) {
				$gradecontent = true;
				break;
				}
			}
		// if we got some "live" ones then output them
		if ($gradecontent) {
			$strftimerecent = get_string("strftimerecent");
			print_headline(get_string("exercisefeedback", "exercise").":");
			foreach ($logs as $log) {
				//Create a temp valid module structure (only need courseid, moduleid)
				$tempmod->course = $course->id;
				$tempmod->id = $log->exerciseid;
				//Obtain the visible property from the instance
				if (instance_is_visible("exercise",$tempmod)) {
					$date = userdate($log->time, $strftimerecent);
					echo "<p><font size=1>$date - $log->firstname $log->lastname<br />";
					echo "\"<a href=\"$CFG->wwwroot/mod/exercise/$log->url\">";
					echo "$log->name";
					echo "</a>\"</font></p>";
					}
				}
			}
		}

	// have a look for new assessments for this user 
    $assesscontent = false;
	if (!$isteacher) { // teachers only need to see submissions
		if ($logs = exercise_get_assess_logs($course, $timestart)) {
			// got some, see if any belong to a visible module
			foreach ($logs as $log) {
				// Create a temp valid module structure (only need courseid, moduleid)
				$tempmod->course = $course->id;
				$tempmod->id = $log->exerciseid;
				//Obtain the visible property from the instance
				if (instance_is_visible("exercise",$tempmod)) {
					$assesscontent = true;
					break;
					}
				}
			// if we got some "live" ones then output them
			if ($assesscontent) {
				$strftimerecent = get_string("strftimerecent");
				print_headline(get_string("exerciseassessments", "exercise").":");
				foreach ($logs as $log) {
					//Create a temp valid module structure (only need courseid, moduleid)
					$tempmod->course = $course->id;
					$tempmod->id = $log->exerciseid;
					//Obtain the visible property from the instance
					if (instance_is_visible("exercise",$tempmod)) {
						$date = userdate($log->time, $strftimerecent);
						echo "<p><font size=1>$date - $log->firstname $log->lastname<br />";
						echo "\"<a href=\"$CFG->wwwroot/mod/exercise/$log->url\">";
						echo "$log->name";
						echo "</a>\"</font></p>";
						}
					}
				}
			}
		}
    return $submitcontent or $gradecontent or $assesscontent;
}


/*******************************************************************/
function exercise_update_instance($exercise) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $exercise->timemodified = time();

    $exercise->deadline = make_timestamp($exercise->deadlineyear, 
			$exercise->deadlinemonth, $exercise->deadlineday, $exercise->deadlinehour, 
			$exercise->deadlineminute);

    $exercise->id = $exercise->instance;

    return update_record("exercise", $exercise);
}


/*******************************************************************/
function exercise_user_complete($course, $user, $mod, $exercise) {
    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        print_simple_box_start();
		$table->head = array (get_string("submission", "exercise"),  get_string("submitted", "exercise"),
			get_string("assessed", "exercise"), get_string("grade"));
		$table->width = "100%";
		$table->align = array ("left", "left", "left", "center");
		$table->size = array ("*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;

		foreach ($submissions as $submission) {
			if ($assessments = exercise_get_assessments($submission)) {
				// should only be one but we'll loop anyway
				foreach ($assessments as $assessment) {
					$table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                            userdate($submission->timecreated), userdate($assessment->timecreated), 
                            $assessment->grade * $exercise->grade / 100.0);
				}
			} else {
                // submission not yet assessed (by teacher)
       			$table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                        userdate($submission->timecreated), get_string("notassessedyet", "exercise"), 0);
            }
        }
		print_table($table);
	    print_simple_box_end();
    } else {
        print_string("nosubmissions", "exercise");
    }
}


/*******************************************************************/
function exercise_user_outline($course, $user, $mod, $exercise) {
    if ($submissions = exercise_get_user_submissions($exercise, $user)) {
		$result->info = count($submissions)." ".get_string("submissions", "exercise");
		foreach ($submissions as $submission) { // the first one is the most recent one
			$result->time = $submission->timecreated;
			break;
			}
        return $result;
    }
    return NULL;
}


/*******************************************************************/
function exercise_get_participants($exerciseid) {
//Returns the users with data in one exercise
//(users with records in exercise_submissions and exercise_assessments, students)

    global $CFG;

    //Get students from exercise_submissions
    $st_submissions = get_records_sql("SELECT DISTINCT u.*
                                       FROM {$CFG->prefix}user u,
                                            {$CFG->prefix}exercise_submissions s
                                       WHERE s.exerciseid = '$exerciseid' and
                                             u.id = s.userid");
    //Get students from exercise_assessments
    $st_assessments = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}exercise_assessments a
                                 WHERE a.exerciseid = '$exerciseid' and
                                       u.id = a.userid");

    //Add st_assessments to st_submissions
    if ($st_assessments) {
        foreach ($st_assessments as $st_assessment) {
            $st_submissions[$st_assessment->id] = $st_assessment;
        }
    }
    //Return st_submissions array (it contains an array of unique users)
    return ($st_submissions);
}

//////////////////////////////////////////////////////////////////////////////////////

/*** Functions for the exercise module ******

function exercise_copy_assessment($assessment, $submission, $withfeedback = false) {
function exercise_count_all_submissions_for_assessment($exercise, $user) {
function exercise_count_assessments($submission) {
function exercise_count_assessments_by_teacher($exercise, $teacher) {
function exercise_count_student_submissions($exercise) {
function exercise_count_teacher_assessments($exercise, $user) {
function exercise_count_teacher_submissions($exercise) {
function exercise_count_teacher_submissions_for_assessment($exercise, $user) {
function exercise_count_unassessed_student_submissions($exercise) {
function exercise_count_ungraded_assessments_student($exercise) {
function exercise_count_ungraded_assessments_teacher($exercise) {
function exercise_count_user_assessments($exercise, $user, $type = "all") { $type is all, student or teacher
function exercise_count_user_assessments_done($exercise,$user) {
function exercise_count_user_submissions($exercise, $user) {

function exercise_delete_submitted_files($exercise, $submission) {
function exercise_delete_user_files($exercise, $user, $exception) {

function exercise_file_area($exercise, $submission) {
function exercise_file_area_name($exercise, $submission) {

function exercise_get_assess_logs($course, $timestart) {
function exercise_get_assessments($submission) {
function exercise_get_best_submission_grades($exercise) {
function exercise_get_grade_logs($course, $timestart) {
function exercise_get_mean_submission_grades($exercise) {
function exercise_get_student_submission($exercise, $user) {
function exercise_get_student_submissions($exercise) {
function exercise_get_submission_assessment($submission, $user) {
function exercise_get_submit_logs($course, $timestart) {
function exercise_get_teacher_submission_assessments($exercise) {
function exercise_get_teacher_submissions($exercise) {
function exercise_get_ungraded_assessments($exercise) {
function exercise_get_unmailed_assessments($cutofftime) {
function exercise_get_unmailed_graded_assessments($cutofftime) {
function exercise_get_user_assessments($exercise, $user) {
function exercise_get_user_submissions($exercise, $user) {

function exercise_list_all_ungraded_assessments($exercise) {
function exercise_list_submissions_for_admin($exercise) {
function exercise_list_teacher_assessments($exercise, $user) {
function exercise_list_teacher_submissions($exercise, $user, $reassess) {
function exercise_list_unassessed_student_submissions($exercise, $user) {
function exercise_list_unassessed_teacher_submissions($exercise, $user) {
function exercise_list_ungraded_assessments($exercise, $stype) {
function exercise_list_user_submissions($exercise, $user) {


function exercise_print_assessment_form($exercise, $assessment, $allowchanges, $returnto)
function exercise_print_assessments_by_user_for_admin($exercise, $user) {
function exercise_print_assessments_for_admin($exercise, $submission) {
function exercise_print_assignment_info($exercise) {
function exercise_print_difference($time) {
function exercise_print_dual_assessment_form($exercise, $assessment, $submission, $returnto)
function exercise_print_feedback($course, $submission) {
function exercise_print_league_table($exercise) {
function exercise_print_submission_assessments($exercise, $submission, $type) {
function exercise_print_submission_title($exercise, $submission) {
function exercise_print_tabbed_table($table) {
function exercise_print_teacher_table($course) {
function exercise_print_time_to_deadline($time) {
function exercise_print_upload_form($exercise) {
function exercise_print_user_assessments($exercise, $user) {

function exercise_test_for_resubmission($exercise, $user) {
function exercise_test_user_assessments($exercise, $user) {
***************************************/


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_copy_assessment($assessment, $submission, $withfeedback = false) {
	// adds a copy of the given assessment for the submission specified to the exercise_assessemnts table. 
	// The grades and optionally the comments are added to the exercise_grades table. Returns the new
	// assessment object
	global $USER;
	
    $yearfromnow = time() + 365 * 86400;
	$newassessment->exerciseid = $assessment->exerciseid;
	$newassessment->submissionid = $submission->id;
	$newassessment->userid = $USER->id;
	$newassessment->timecreated = $yearfromnow;
	$newassessment->timegraded = 0;
	$newassessment->grade = $assessment->grade;
	if ($withfeedback) {
		$newassessment->generalcomment = addslashes($assessment->generalcomment);
		$newassessment->teachercomment = addslashes($assessment->teachercomment);
	}
	if (!$newassessment->id = insert_record("exercise_assessments", $newassessment)) {
		error("Copy Assessment: Could not insert exercise assessment!");
	}

	if ($grades = get_records("exercise_grades", "assessmentid", $assessment->id)) {
		foreach ($grades as $grade) {
            unset($grade->id); // clear id, insert record now seems to believe it!
            if (!$withfeedback) {
				$grade->feedback = '';
			}
            else {
                $grade->feedback = addslashes($grade->feedback);
                // $grade->feedback = $grade->feedback;
            }
			$grade->assessmentid = $newassessment->id;
			if (!$grade->id = insert_record("exercise_grades", $grade)) {
				error("Copy Assessment: Could not insert exercise grade!");
			}
		}
	}
	if ($withfeedback) {
        // remove the slashes from comments as the new assessment record is used 
        // in assessments and in lib
       	$newassessment->generalcomment = stripslashes($assessment->generalcomment);
		$newassessment->teachercomment = stripslashes($assessment->teachercomment);
    }
 
    return $newassessment;
}



///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_all_submissions_for_assessment($exercise, $user) {
	// looks at all submissions and deducts the number which has been assessed by this user
	$n = 0;
	if ($submissions = get_records_select("exercise_submissions", "exerciseid = $exercise->id AND timecreated > 0")) {
		$n =count($submissions);
		foreach ($submissions as $submission) {
			$n -= count_records("exercise_assessments", "submissionid", $submission->id, "userid", $user->id);
			}
		}
	return $n;
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_assessments($submission) {
	// Return the (cold) assessments for this submission, 
	global $CFG;
	
	$timethen = time() - $CFG->maxeditingtime;
   return count_records_select("exercise_assessments", "submissionid = $submission->id AND
	   timecreated < $timethen");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_assessments_by_teacher($exercise, $teacher) {
	// Return the number of assessments done by a teacher 
	
   return count_records_select("exercise_assessments", "exerciseid = $exercise->id AND
	   userid = $teacher->id");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_student_submissions($exercise) {
	global $CFG;
	
	 return count_records_sql("SELECT count(*) FROM {$CFG->prefix}exercise_submissions s, {$CFG->prefix}user_students u
							WHERE u.course = $exercise->course
                              AND s.userid = u.userid
                              AND s.exerciseid = $exercise->id
							  AND timecreated > 0");
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_teacher_assessments($exercise, $user) {
	// returns the number of assessments made by teachers on user's submissions
	
	$n = 0;
	if ($submissions = exercise_get_user_submissions($exercise, $user)) {
		foreach ($submissions as $submission) {
			if ($assessments = exercise_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					// count only teacher assessments
					if (isteacher($exercise->course, $assessment->userid)) {
						$n++;
						}
					}
				}
			}
		}
	return $n;
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_teacher_submissions($exercise) {
	
	 return count_records("exercise_submissions", "isexercise", 1, "exerciseid", $exercise->id);
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_teacher_submissions_for_assessment($exercise, $user) {

	$n = 0;
	if ($submissions = exercise_get_teacher_submissions($exercise)) {
		$n =count($submissions);
		foreach ($submissions as $submission) {
			$n -= count_records("exercise_assessments", "submissionid", $submission->id, "userid", $user->id);
			}
		}
	return $n;
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_unassessed_student_submissions($exercise) {
// returns the number of students submissions which have not been assessed by a teacher
	global $CFG;
	
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
	$timenow = time();
	$n = 0;
	if ($submissions = exercise_get_student_submissions($exercise)) {
		foreach ($submissions as $submission) {
			// only look at "cold" submissions
			if ($submission->timecreated < $timenow - $CFG->maxeditingtime) {
				$teacherassessed = false;
				if ($assessments = exercise_get_assessments($submission)) {
					foreach ($assessments as $assessment) {
						// exercise_get_assessments only returns "cold" assessments, look for one made by a teacher
						if (isteacher($course->id, $assessment->userid)) {
							$teacherassessed = true;
							break; // no need to look further
						}
					}
				}
				if (!$teacherassessed) {
    				$n++;
				}
			}
		}
	}
	return $n;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_ungraded_assessments_student($exercise) {
	// function returns the number of ungraded assessments by students of STUDENT submissions
    $n = 0;
	if ($submissions = exercise_get_student_submissions($exercise)) {
		foreach ($submissions as $submission) {
			if ($assessments = exercise_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					if ($assessment->timegraded == 0) {
						// ignore teacher assessments
						if (!isteacher($exercise->course, $assessment->userid)) {
							$n++;
							}
						}
					}
				}
			}
		}
	return $n;
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_ungraded_assessments_teacher($exercise) {
	// function returns the number of ungraded assessments by students of TEACHER submissions
	global $CFG;

	$timenow = time();
	$n = 0;
	if ($submissions = exercise_get_teacher_submissions($exercise)) {
		foreach ($submissions as $submission) {
			if ($assessments = exercise_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					if ($assessment->timegraded == 0) {
						// ignore teacher assessments
						if (!isteacher($exercise->course, $assessment->userid)) {
							// must have created a little time ago
							if (($timenow - $assessment->timecreated) > $CFG->maxeditingtime) {
								$n++;
								}
							}
						}
					}
				}
			}
		}
	return $n;
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_user_assessments($exercise, $user, $stype = "all") {
	// returns the number of assessments allocated/made by a user, all of them, or just those for the student or teacher submissions
	// the student's self assessments are included in the count
	// the maxeditingtime is NOT taken into account here also allocated assessments which have not yet
	// been done are counted as well
	
	$n = 0;
	if ($assessments = exercise_get_user_assessments($exercise, $user)) {
		 foreach ($assessments as $assessment) {
			switch ($stype) {
				case "all" :
					$n++;
					break;
				case "student" :
					 $submission = get_record("exercise_submissions", "id", $assessment->submissionid);
					if (isstudent($exercise->course, $submission->userid)) {
						$n++;
						}
					break;
				case "teacher" :
					 $submission = get_record("exercise_submissions", "id", $assessment->submissionid);
					if (isteacher($exercise->course, $submission->userid)) {
						$n++;
						}
					break;
				}
			}
		}
	return $n;
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_user_assessments_done($exercise, $user) {
	// returns the number of assessments actually done by a user
	// the student's self assessments are included in the count
	// the maxeditingtime is NOT taken into account here 
	
	$n = 0;
	$timenow = time();
	if ($assessments = exercise_get_user_assessments($exercise, $user)) {
		 foreach ($assessments as $assessment) {
			if ($assessment->timecreated < $timenow) {
				$n++;
			}
		}
	}
	return $n;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_count_user_submissions($exercise, $user) {
	// returns the number of submissions make by this user
	return count_records("exercise_submissions", "exerciseid", $exercise->id, "userid", $user->id);
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_delete_submitted_files($exercise, $submission) {
// Deletes the files in the exercise area for this submission

	if ($basedir = exercise_file_area($exercise, $submission)) {
		if ($files = get_directory_list($basedir)) {
			foreach ($files as $file) {
				if (unlink("$basedir/$file")) {
					notify("Existing file '$file' has been deleted!");
				}
				else {
					notify("Attempt to delete file $basedir/$file has failed!");
				}
			}
		}
	}
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_delete_user_files($exercise, $user, $exception) {
// Deletes all the user files in the exercise area for a user
// EXCEPT for any file named $exception

    if (!$submissions = exercise_get_submissions($exercise, $user)) {
		notify("No submissions!");
		return;
		}
	foreach ($submissions as $submission) {
		if ($basedir = exercise_file_area($exercise, $submission)) {
			if ($files = get_directory_list($basedir)) {
				foreach ($files as $file) {
					if ($file != $exception) {
						unlink("$basedir/$file");
						notify("Existing file '$file' has been deleted!");
						}
					}
				}
			}
		}
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_file_area($exercise, $submission) {
    return make_upload_directory( exercise_file_area_name($exercise, $submission) );
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_file_area_name($exercise, $submission) {
//  Creates a directory file name, suitable for make_upload_directory()
    global $CFG;

    return "$exercise->course/$CFG->moddata/exercise/$submission->id";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_assess_logs($course, $timestart) {
	// get the "assess" entries for this user and add the first and last names...
	global $CFG, $USER;
	
	$timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.exerciseid, e.name
                             FROM {$CFG->prefix}log l,
								{$CFG->prefix}exercise e, 
        						{$CFG->prefix}exercise_submissions s, 
        						{$CFG->prefix}exercise_assessments a, 
        						{$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
								AND l.course = $course->id AND l.module = 'exercise'	AND l.action = 'assess'
								AND a.id = l.info AND s.id = a.submissionid AND s.userid = $USER->id
								AND u.id = a.userid AND e.id = a.exerciseid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_assessments($submission) {
	// Return all assessments for this submission provided they are after the editing time, 
    // ordered oldest first, newest last
	global $CFG;

	$timenow = time();
    return get_records_select("exercise_assessments", "(submissionid = $submission->id) AND 
		(timecreated < $timenow - $CFG->maxeditingtime)", "timecreated ASC");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_best_grade($submission) {
// Returns the best grade of students' submission (there may, occassionally be more than one assessment)
	global $CFG;
	
	return get_record_sql("SELECT MAX(a.grade) grade FROM 
                        {$CFG->prefix}exercise_assessments a 
                            WHERE a.submissionid = $submission->id
							  GROUP BY a.submissionid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_best_submission_grades($exercise) {
// Returns the grades of students' best submissions
	global $CFG;
	
	return get_records_sql("SELECT DISTINCT u.userid, MAX(a.grade) grade FROM 
                        {$CFG->prefix}exercise_submissions s, 
						{$CFG->prefix}exercise_assessments a, {$CFG->prefix}user_students u 
                            WHERE u.course = $exercise->course
                              AND s.userid = u.userid
							  AND s.exerciseid = $exercise->id
                              AND s.late = 0
							  AND a.submissionid = s.id
							  GROUP BY u.userid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_grade_logs($course, $timestart) {
	// get the "grade" entries for this user and add the first and last names...
	global $CFG, $USER;
	
	$timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.exerciseid, e.name
                             FROM {$CFG->prefix}log l,
								{$CFG->prefix}exercise e, 
        						{$CFG->prefix}exercise_submissions s, 
        						{$CFG->prefix}exercise_assessments a, 
        						{$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
								AND l.course = $course->id AND l.module = 'exercise'	AND l.action = 'grade'
								AND a.id = l.info AND s.id = a.submissionid AND a.userid = $USER->id
								AND u.id = s.userid AND e.id = a.exerciseid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_mean_grade($submission) {
// Returns the mean grade of students' submission (may, very occassionally, be more than one assessment)
	global $CFG;
	
	return get_record_sql("SELECT AVG(a.grade) grade FROM 
                        {$CFG->prefix}exercise_assessments a 
                            WHERE a.submissionid = $submission->id
							  GROUP BY a.submissionid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_mean_submission_grades($exercise) {
// Returns the mean grades of students' submissions
// ignores hot assessments
	global $CFG;
	
    $timenow = time();
	$grades = get_records_sql("SELECT DISTINCT u.userid, AVG(a.grade) grade FROM 
                        {$CFG->prefix}exercise_submissions s, 
						{$CFG->prefix}exercise_assessments a, {$CFG->prefix}user_students u 
                            WHERE u.course = $exercise->course
                              AND s.userid = u.userid
							  AND s.exerciseid = $exercise->id
                              AND s.late = 0
							  AND a.submissionid = s.id
                              AND a.timecreated < $timenow
							  GROUP BY u.userid");
    return $grades;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_student_submission($exercise, $user) {
// Return a submission for a particular user
	global $CFG;

    $submission = get_record("exercise_submissions", "exerciseid", $exercise->id, "userid", $user->id);
    if (!empty($submission->timecreated)) {
        return $submission;
    }
    return NULL;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_student_submissions($exercise, $order = "") {
// Return all  ENROLLED student submissions
// if order can grade|title|name|nothing, nothing is oldest first, youngest last
	global $CFG;
	
	if ($order == "grade") {
		// allow for multiple assessments of submissions (coming from different teachers)
		return get_records_sql("SELECT s.*, AVG(a.grade) grade FROM {$CFG->prefix}exercise_submissions s, 
							{$CFG->prefix}user_students u, {$CFG->prefix}exercise_assessments a
							WHERE u.course = $exercise->course
								AND s.userid = u.userid
								AND s.exerciseid = $exercise->id
								AND a.submissionid = s.id
							GROUP BY s.id
							ORDER BY a.grade DESC");
	}

	if ($order == "title") {
		$order = "s.title";
	} elseif ($order == "name") {
		$order = "n.firstname, n.lastname, s.timecreated DESC";
	} else {
        $order = "s.timecreated";
    }
    
	return get_records_sql("SELECT s.* FROM {$CFG->prefix}exercise_submissions s, 
                           {$CFG->prefix}user_students u, {$CFG->prefix}user n  
                            WHERE u.course = $exercise->course
                              AND s.userid = u.userid
                              AND n.id = u.userid
							  AND s.exerciseid = $exercise->id
							ORDER BY $order");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_submission_assessment($submission, $user) {
	// Return the user's assessment for this submission
	return get_record("exercise_assessments", "submissionid", $submission->id, "userid", $user->id);
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_submit_logs($course, $timestart) {
	// get the "submit" entries and add the first and last names...
	global $CFG, $USER;
	
	$timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, l.info exerciseid, e.name
                             FROM {$CFG->prefix}log l,
								{$CFG->prefix}exercise e, 
        						{$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
								AND l.course = $course->id AND l.module = 'exercise'
								AND l.action = 'submit'
								AND e.id = l.info 
								AND u.id = l.userid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_teacher_submission_assessments($exercise) {
// Return all assessments on the teacher submissions, order by youngest first, oldest last
	global $CFG;
	
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}exercise_submissions s, {$CFG->prefix}exercise_assessments a
                            WHERE s.isexercise = 1
                              AND s.exerciseid = $exercise->id
							  AND a.submissionid = s.id
							  ORDER BY a.timecreated DESC");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_teacher_submissions($exercise) {
// Return all  teacher submissions, ordered by title
	global $CFG;
	
	return get_records_sql("SELECT s.* FROM {$CFG->prefix}exercise_submissions s
						WHERE s.isexercise = 1
                              AND s.exerciseid = $exercise->id 
							  ORDER BY s.title");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_ungraded_assessments($exercise) {
	global $CFG;
	// Return all assessments which have not been graded or just graded
	$cutofftime =time() - $CFG->maxeditingtime;
    return get_records_select("exercise_assessments", "exerciseid = $exercise->id AND (timegraded = 0 OR 
				timegraded > $cutofftime)", "timecreated"); 
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_ungraded_assessments_student($exercise) {
	global $CFG;
	// Return all assessments which have not been graded or just graded of student's submissions
	
	$cutofftime =time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}exercise_submissions s, {$CFG->prefix}user_students u,
							{$CFG->prefix}exercise_assessments a
                            WHERE u.course = $exercise->course
                              AND s.userid = u.userid
                              AND s.exerciseid = $exercise->id
							  AND a.submissionid = s.id
							  AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
							  AND a.timecreated < $cutofftime
							  ORDER BY a.timecreated ASC"); 
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_ungraded_assessments_teacher($exercise) {
	global $CFG;
	// Return all assessments which have not been graded or just graded of teacher's submissions
	
	$cutofftime =time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}exercise_submissions s, {$CFG->prefix}exercise_assessments a
                            WHERE s.isexercise = 1
                              AND s.exerciseid = $exercise->id
							  AND a.submissionid = s.id
							  AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
							  AND a.timecreated < $cutofftime
							  ORDER BY a.timecreated ASC"); 
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_unmailed_assessments($cutofftime) {
	/// Return list of (ungraded) assessments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT a.*, g.course, g.name
                              FROM {$CFG->prefix}exercise_assessments a, {$CFG->prefix}exercise g
                             WHERE a.mailed = 0 
							   AND a.timegraded = 0
                               AND a.timecreated < $cutofftime 
                               AND g.id = a.exerciseid");
}


function exercise_get_unmailed_graded_assessments($cutofftime) {
	/// Return list of graded assessments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT a.*, g.course, g.name
                              FROM {$CFG->prefix}exercise_assessments a, {$CFG->prefix}exercise g
                             WHERE a.mailed = 0 
							   AND a.timegraded < $cutofftime 
							   AND a.timegraded > 0
                               AND g.id = a.exerciseid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_user_assessments($exercise, $user) {
	// Return all the  user's assessments, newest first, oldest last
	return get_records_select("exercise_assessments", "exerciseid = $exercise->id AND userid = $user->id", 
				"timecreated DESC");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_get_user_submissions($exercise, $user) {
	// return submission of user oldest first, newest last
	// teachers submit "exercises"
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
	if (isteacher($course->id, $user->id)) {
		return get_records_select("exercise_submissions",
             "exerciseid = $exercise->id AND isexercise = 1", "timecreated" );
		}
    return get_records_select("exercise_submissions",
             "exerciseid = $exercise->id AND userid = $user->id", "timecreated" );
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_all_ungraded_assessments($exercise) {
	// lists all the assessments for comment by teacher
	global $CFG;
	
	$table->head = array (get_string("title", "exercise"), get_string("timeassessed", "exercise"), get_string("action", "exercise"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;
	$timenow = time();
	
	if ($assessments = exercise_get_ungraded_assessments($exercise)) {
		foreach ($assessments as $assessment) {
			if (!isteacher($exercise->course, $assessment->userid)) {
				if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&a=$exercise->id&aid=$assessment->id\">".
						get_string("edit", "exercise")."</A>";
					}
				else {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&a=$exercise->id&aid=$assessment->id\">".
						get_string("gradeassessment", "exercise")."</A>";
					}
				$submission = get_record("exercise_submissions", "id", $assessment->submissionid);
				$table->data[] = array(exercise_print_submission_title($exercise, $submission), 
					userdate($assessment->timecreated), $action);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}
	

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_submissions_for_admin($exercise) {
	// list the teacher sublmissions first
	global $CFG, $EXERCISE_FWEIGHTS, $THEME, $USER;
	
    if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

	exercise_print_assignment_info($exercise);
    print_heading_with_help(get_string("administration"), "administration", "exercise");
    echo"<p align=\"center\"><b><a href=\"assessments.php?action=teachertable&id=$cm->id\">".
            get_string("teacherassessmenttable", "exercise", $course->teacher)."</a></b></p>\n";


    if (isteacheredit($course->id)) {
        ?>
            <form name="weightsform" method="post" action="submissions.php">
            <INPUT TYPE="hidden" NAME="id" VALUE="<?PHP echo $cm->id ?>">
            <input type="hidden" name="action" value="saveweights">
            <CENTER>
            <?PHP

            // get the final weights from the database
            $teacherweight = get_field("exercise","teacherweight", "id", $exercise->id);
        $gradingweight = get_field("exercise","gradingweight", "id", $exercise->id);

        // now show the weights used in the grades
        echo "<TABLE WIDTH=\"50%\" BORDER=\"1\">\n";
        echo "<tr><td colspan=\"2\" bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".
            get_string("weightsusedforoverallgrade", "exercise")."</b></td></tr>\n";
        echo "<TR><TD ALIGN=\"right\">".get_string("weightforgradingofassessments", "exercise").":</TD>\n";
        echo "<TD>";
        exercise_choose_from_menu($EXERCISE_FWEIGHTS, "gradingweight", $gradingweight, "");
        echo "</td></tr>\n";
        echo "<tr><td align=\"right\">".get_string("weightforteacherassessments", "exercise", 
                $course->teacher).":</td>\n";
        echo "<td>";
        exercise_choose_from_menu($EXERCISE_FWEIGHTS, "teacherweight", $teacherweight, "");
        echo "</td></tr>\n";
        echo "<tr><td colspan=\"2\" align=\"center\">"; 
        echo "<INPUT TYPE=submit VALUE=\"".get_string("saveweights", "exercise")."\">\n";
        echo "</td></tr>\n";
        echo "</TABLE>\n";
        echo "</CENTER><br />";
        echo "</FORM>\n";

        ?>
            <form name="leagueform" method="post" action="submissions.php">
            <INPUT TYPE="hidden" NAME="id" VALUE="<?PHP echo $cm->id ?>">
            <input type="hidden" name="action" value="saveleaguetable">
            <CENTER>
            <?PHP

            echo "<TABLE WIDTH=\"50%\" BORDER=\"1\">\n";
        echo "<tr><td align=\"center\" colspan=\"2\" bgcolor=\"$THEME->cellheading2\"><b>".
            get_string("leaguetable", "exercise")."</b></td></tr>\n";
        echo "<tr><td align=\"right\">".get_string("numberofentries", "exercise").":</td>\n";
        echo "<TD>";
        $numbers[22] = 'All';
        $numbers[21] = 50;
        for ($i=20; $i>=0; $i--) {
            $numbers[$i] = $i;
        }
        $nentries = $exercise->showleaguetable;
        if ($nentries == 99) {
            $nentries = 'All';
        }
        choose_from_menu($numbers, "nentries", "$nentries", "");
        echo "</td></tr>\n";
        echo "<tr><td align=\"right\">".get_string("hidenamesfromstudents", "exercise", 
                $course->students)."</td><td>\n";
        $options[0] = get_string("no"); $options[1] = get_string("yes");
        choose_from_menu($options, "anonymous", $exercise->anonymous, "");
        echo "</td></tr>\n";
        echo "<tr><td colspan=\"2\" align=\"center\">"; 
        echo "<INPUT TYPE=submit VALUE=\"".get_string("saveentries", "exercise")."\">\n";
        echo "</td></tr>\n";
        echo "</table>\n";
        echo "</CENTER>";
        echo "</FORM>\n";


        // list any teacher submissions
        $table->head = array (get_string("title", "exercise"), get_string("submitted", "exercise"), get_string("action", "exercise"));
        $table->align = array ("left", "left", "left");
        $table->size = array ("*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        if ($submissions = exercise_get_teacher_submissions($exercise)) {
            foreach ($submissions as $submission) {
                $action = "<a href=\"submissions.php?action=adminamendtitle&id=$cm->id&sid=$submission->id\">".
                    get_string("amendtitle", "exercise")."</a>";
                $action .= " | <a href=\"submissions.php?action=adminconfirmdelete&id=$cm->id&sid=$submission->id\">".
                    get_string("delete", "exercise")."</a>";
                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                        userdate($submission->timecreated), $action);
            }
            print_heading(get_string("studentsubmissions", "exercise", $course->teacher), "center");
            print_table($table);
        }
    }

	// list student assessments
	// Get all the students...
	if ($users = get_course_students($course->id, "u.firstname, u.lastname")) {
		$timenow = time();
		unset($table);
		$table->head = array(get_string("name"), get_string("title", "exercise"), 
                get_string("assessed", "exercise"), get_string("action", "exercise"));
		$table->align = array ("left", "left", "left", "left");
		$table->size = array ("*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;
        $nassessments = 0;
		foreach ($users as $user) {
			if ($assessments = exercise_get_user_assessments($exercise, $user)) {
				$title ='';
				foreach ($assessments as $assessment) {
					if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
						error("exercise_list_submissions_for_admin: Submission record not found!");
						}
					$title .= $submission->title;
					// test for allocated assesments which have not been done
					if ($assessment->timecreated < $timenow) {
                        // show only warm or cold assessments
						$title .= " {".number_format($assessment->grade * $exercise->grade / 100.0, 0);
                        if ($assessment->timegraded) {
                            $title .= "/".number_format($assessment->gradinggrade * $exercise->grade / 
                                    COMMENTSCALE, 0);
                        }
                        $title .= "} ";
                        if ($realassessments = exercise_count_user_assessments_done($exercise, $user)) {
                            $action = "<a href=\"assessments.php?action=adminlistbystudent&id=$cm->id&userid=$user->id\">".
                                get_string("view", "exercise")."</a>";
                        }
                        else {
                            $action ="";
                        }
                        $nassessments++;
				        $table->data[] = array("$user->firstname $user->lastname", $title, 
                                userdate($assessment->timecreated), $action);
                    }
				}
			}
        }
		if (isset($table->data)) {
			print_heading(get_string("studentassessments", "exercise", $course->student)." [$nassessments]");
			print_table($table);
			echo "<p align=\"center\">".get_string("noteonstudentassessments", "exercise")."</p>\n";
		}
    }

	// now the sudent submissions
	unset($table);
	if ($users) {
        $table->head = array (get_string("submittedby", "exercise"), get_string("title", "exercise"),
            get_string("submitted", "exercise"), get_string("action", "exercise"));
        $table->align = array ("left", "left", "left", "left");
        $table->size = array ("*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        $nsubmissions = 0;
        foreach ($users as $user) {
            if ($submissions = exercise_get_user_submissions($exercise, $user)) {
                foreach ($submissions as $submission) {
                    $action = "<a href=\"submissions.php?action=adminamendtitle&id=$cm->id&sid=$submission->id\">".
                        get_string("amendtitle", "exercise")."</a>";
                    // has teacher already assessed this submission
                    if ($assessment = get_record_select("exercise_assessments", 
                                "submissionid = $submission->id AND userid = $USER->id")) {
                        $curtime = time();
                        if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
                            $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                                get_string("reassess", "exercise")."</a>";
                        }
                        else { // there's still time left to edit...
                            $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                                get_string("edit", "exercise")."</a>";
                        }
                    }
                    else { // user has not assessed this submission
                        $action .= " | <a href=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
                            get_string("assess", "exercise")."</a>";
                    }
                    if ($nassessments = exercise_count_assessments($submission)) {
                        $action .= " | <a href=\"assessments.php?action=adminlist&id=$cm->id&sid=$submission->id\">".
                            get_string("view", "exercise")." ($nassessments)</a>";
                    }
                    if ($submission->late) {
                        $action .= " | <a href=\"submissions.php?action=adminlateflag&id=$cm->id&sid=$submission->id\">".
                            get_string("clearlateflag", "exercise")."</a>";
                    }
                    $action .= " | <a href=\"submissions.php?action=adminconfirmdelete&id=$cm->id&sid=$submission->id\">".
                        get_string("delete", "exercise")."</a>";
                    $title = $submission->title;
                    if ($submission->resubmit) {
                        $title .= "*";
                    }
                    $datesubmitted = userdate($submission->timecreated);
                    if ($submission->late) {
                        $datesubmitted = "<font color=\"red\">".$datesubmitted."</font>";
                    }
                    $table->data[] = array("$user->firstname $user->lastname", $title.
                            " ".exercise_print_submission_assessments($exercise, $submission), 
                            $datesubmitted, $action);
                    $nsubmissions++;
                }
            }
        }
		if (isset($table->data)) {
            print_heading(get_string("studentsubmissions", "exercise", $course->student)." [$nsubmissions]",
                "center");
            print_table($table);
            echo "<p align=\"center\">".get_string("resubmitnote", "exercise", $course->student)."</p>\n";
        }
        echo "<p align=\"center\">".get_string("allgradeshaveamaximumof", "exercise", $exercise->grade).
            "</p></center>\n";
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_teacher_assessments($exercise, $user) {
	global $CFG;
	
	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title", "exercise"), get_string("action", "exercise"), get_string("comment", "exercise"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	// get user's submissions
	if ($submissions = exercise_get_user_submissions($exercise, $user)) {
		foreach ($submissions as $submission) {
			// get the assessments
			if ($assessments = exercise_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					if (isteacher($exercise->course, $assessment->userid)) { // assessments by teachers only
						$action = "<A HREF=\"assessments.php?action=viewassessment&a=$exercise->id&aid=$assessment->id\">".
							get_string("view", "exercise")."</A>";
						// has teacher commented on teacher's assessment? shouldn't happen but leave test in
						if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
							$comment = get_string("gradedbyteacher", "exercise", $course->teacher);
							}
						else {
							$comment = userdate($assessment->timecreated);
							}
						$table->data[] = array(exercise_print_submission_title($exercise, $submission), $action, $comment);
						}
					}
				}
			}
		}
	if (isset($table->data)) {
		print_table($table);
		}
	else {
		echo "<CENTER>".get_string("noassessmentsdone", "exercise")."</CENTER>\n";
		}
	}



///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_teacher_submissions($exercise, $user, $reassess = false) {
	// always allow user to reassess if that flag is true
	global $CFG;
	
	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
	if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
		error("Course Module ID was incorrect");
	}

	$strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");

	// get any assessment this user has done (could include hot one)
	if (!$assessment = get_record_select("exercise_assessments", "exerciseid = $exercise->id
					AND userid = $user->id")) {
		// the user has not  yet assessed this exercise, set up a hot assessment record for this user for one 
        // of the teacher submissions, first count the number of assessments for each teacher submission...
		if ($submissions = exercise_get_teacher_submissions($exercise)) {
			mt_srand ((float)microtime()*1000000); // initialise random number generator
			foreach ($submissions as $submission) {
				$n = count_records("exercise_assessments", "submissionid", $submission->id);
				// ...OK to have zero, we add a small random number to randomise things...
				$nassessments[$submission->id] = $n + mt_rand(0, 99) / 100;
			}
			// ...put the submissions with the lowest number of assessments first...
			asort($nassessments);
			reset($nassessments);
			foreach ($nassessments as $submissionid => $n) { // break out of loop after the first element
				$submission = get_record("exercise_submissions", "id", $submissionid);
				// ... provided the user has NOT already assessed that submission...
				if (!$assessment = exercise_get_submission_assessment($submission, $user)) {
					$yearfromnow = time() + 365 * 86400;
					// ...create one and set timecreated way in the future, reset when record is updated
					$assessment->exerciseid = $exercise->id;
					$assessment->submissionid = $submission->id;
					$assessment->userid = $user->id;
					$assessment->grade = -1; // set impossible grade
					$assessment->timecreated = $yearfromnow;
					if (!$assessment->id = insert_record("exercise_assessments", $assessment)) {
						error("Could not insert exercise assessment!");
					}
					break;
				}
			}
		}
	} else {
        // get hold of the teacher submission
        if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
            error("List teacher submissions: submission record not found");
        }
    }
    print_simple_box_start("center");
    print_heading_with_help(get_string("theexercise", "exercise"), "junk", "exercise");
    print_simple_box_start("center");
    echo "<p align=\"center\"><b>".get_string("description", "exercise").": </b>\n";
    echo exercise_print_submission_title($exercise, $submission);
    echo "</p>\n";
    print_simple_box_end();
    print_simple_box_end();
 
   	$table->head = array (get_string("action", "exercise"), get_string("assessed", "exercise"),
        get_string("comment", "exercise"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	// now list user's assessments (but only list those which come from teacher submissions)
	print_heading(get_string("yourassessment", "exercise"));
	if ($assessments = exercise_get_user_assessments($exercise, $user)) {
		$timenow = time();
		foreach ($assessments as $assessment) {
			if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
				error ("exercise_list_teacher_submissions: unable to get submission");
			}
			// submission from a teacher, i.e an exercise submission?
			if ($submission->isexercise) {
				$comment = '';
				if ($reassess) {  // just show re-assess
					$action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
						get_string("reassess", "exercise")."</A>";
				}
				else { // reassess is false - assessment is a "normal state"
					// user assessment has three states: record created but not assessed (date created 
                    // in the future); just assessed but still editable; and "static" (may or may not 
                    // have been graded by teacher, that is shown in the comment) 
					if ($assessment->timecreated > $timenow) { // user needs to assess this submission
						$action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
							get_string("assess", "exercise")."</A>";
					}
					elseif ($assessment->timecreated > ($timenow - $CFG->maxeditingtime)) { 
                        // there's still time left to edit...
						$action = "<A HREF=\"assessments.php?action=assesssubmission&id=$cm->id&sid=$submission->id\">".
							get_string("edit", "exercise")."</A>";
					}
					else { 
						$action = "<A HREF=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">"
							.get_string("view", "exercise")."</A>";
					}
				}
				// show the date if in the past (otherwise the user hasn't done the assessment yet
				$assessmentdate = '';
				if ($assessment->timecreated < $timenow) {
					$assessmentdate = userdate($assessment->timecreated);
					// if user has submitted work, see if teacher has graded assessment
					if (exercise_count_user_submissions($exercise, $user) > 0) {
						if ($assessment->timegraded and (($timenow - $assessment->timegraded) > $CFG->maxeditingtime)) {
							$comment .= get_string("thereisfeedbackfromthe", "exercise", $course->teacher);
						}
						else {
							$comment .= get_string("awaitingfeedbackfromthe", "exercise", $course->teacher);
						}
					}
				}
				$table->data[] = array($action, $assessmentdate, $comment);
			}
		}
	    print_table($table);
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_unassessed_student_submissions($exercise, $user) {
	// list the student submissions not assessed by the teacher
	global $CFG;
	
	$timenow = time();
	
	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
	if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
		error("Course Module ID was incorrect");
	}

	$table->head = array (get_string("title", "exercise"), get_string("submittedby", "exercise"),
		get_string("submitted", "exercise"), get_string("action", "exercise"), 
        get_string("comment", "exercise"));
	$table->align = array ("LEFT", "LEFT", "LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

    // get all the submissions, oldest first, youngest last
	if ($submissions = exercise_get_student_submissions($exercise)) {
		foreach ($submissions as $submission) {
			// only consider "cold" submissions
			if ($submission->timecreated < $timenow - $CFG->maxeditingtime) {
				$comment = "";
				// see if student has already submitted
				$submissionowner = get_record("user", "id", $submission->userid);
				if (exercise_count_user_submissions($exercise, $submissionowner) == 1) {
					// it's the student's first submission 
                    // see if there are no cold assessments for this submission
                    if (!exercise_count_assessments($submission)) {
                        // now see if the teacher has already assessed this submission
                        $warm = false;
                        if ($assessments = get_records("exercise_assessments", "submissionid", $submission->id)) {
                            foreach ($assessments as $assessment) {
                                if (isteacher($course->id, $assessment->userid)) {
                                    if ($assessment->timecreated > $timenow -$CFG->maxeditingtime) {
                                        $warm = true;
                                    }
                                    break;  // no need to look further
                                }
                            }
                        }
                        // get their assessment
                        if ($assessments = exercise_get_user_assessments($exercise, $submissionowner)) {
                            foreach ($assessments as $assessment) {
                                $studentassessment = $assessment;
                                break; // there should only be one!
                            }
                            $timegap = get_string("ago", "exercise", format_time($submission->timecreated -
                                        $timenow));
                            if ($submission->late) {
                                $timegap = "<font color=\"red\">".$timegap."</font>";
                            }
                            if ($warm) {
                                // last chance salon
                                $action = "<A HREF=\"assessments.php?action=teacherassessment&id=$cm->id&aid=$studentassessment->id&sid=$submission->id\">".
                                    get_string("edit", "exercise")."</A>";
                                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                                        $submissionowner->firstname." ".$submissionowner->lastname, 
                                        $timegap, $action, $comment);
                            } else {
                                $action = "<A HREF=\"assessments.php?action=teacherassessment&id=$cm->id&aid=$studentassessment->id&sid=$submission->id\">".
                                    get_string("assess", "exercise")."</A>";
                                $table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                                        $submissionowner->firstname." ".$submissionowner->lastname, 
                                        $timegap, $action, $comment);
                            }
                        } else {
                            // there's no student assessment, odd!!
                        }
                    }
				}
				// this is student's second... submission
				else {
					$teacherassessed = false;
					$warm = false;
					if ($assessments = get_records("exercise_assessments", "submissionid", $submission->id)) {
						foreach ($assessments as $assessment) {
							if (isteacher($course->id, $assessment->userid)) {
								$teacherassessed = true;
                                if (!$teacher = get_record("user", "id", $assessment->userid)) {
                                    error("List unassessed student submissions: teacher record not found");
                                }
                                $comment = get_string("resubmissionfor", "exercise",
                                                "$teacher->firstname $teacher->lastname");
								if ($assessment->timecreated > $timenow - $CFG->maxeditingtime) {
									$warm = true;
								}
								break; // no need to look further
							}
						}
					}
					if ($teacherassessed and $warm) {
						// last chance salon
						$action = "<A HREF=\"assessments.php?action=assessresubmission&id=$cm->id&sid=$submission->id\">".
							get_string("edit", "exercise")."</A>";
                        $timegap = get_string("ago", "exercise", format_time($submission->timecreated -
                                    $timenow));
                        if ($submission->late) {
                            $timegap = "<font color=\"red\">".$timegap."</font>";
                        }
						$table->data[] = array(exercise_print_submission_title($exercise, $submission), 
							$submissionowner->firstname." ".$submissionowner->lastname, 
                            $timegap, $action, $comment);
					}
					if (!$teacherassessed) { 
						// no teacher's assessment
                        // find who did the previous assessment
            			if (!$submissions = exercise_get_user_submissions($exercise, $submissionowner)) {
			            	error("List unassessed student submissions: submission records not found");
				        }
                        // get the oldest submission, exercise_get_user_submissions returns that first
			            foreach ($submissions as $tempsubmission) {
                            $prevsubmission = $tempsubmission;
		        		    break;
				        }
            			// get the teacher's assessment of the student's previous submission
		            	if ($assessments = get_records("exercise_assessments", "submissionid", 
                                    $prevsubmission->id)) {
                            foreach ($assessments as $assessment) {
                                if (isteacher($course->id, $assessment->userid)) {
                                    if (!$teacher = get_record("user", "id", $assessment->userid)) {
                                        error("List unassessed student submissions: teacher record not found");
                                    }
                                    $comment = get_string("resubmissionfor", "exercise",
                                                    "$teacher->firstname $teacher->lastname");
								    break; // no need to look further
                                    
				                }
                            }
                        }
						$action = "<A HREF=\"assessments.php?action=assessresubmission&id=$cm->id&sid=$submission->id\">".
							get_string("assess", "exercise")."</A>";
                        $timegap = get_string("ago", "exercise", format_time($submission->timecreated -
                                    $timenow));
                        if ($submission->late) {
                             $timegap = "<font color=\"red\">".$timegap."</font>";
                        }
						$table->data[] = array(exercise_print_submission_title($exercise, $submission), 
							$submissionowner->firstname." ".$submissionowner->lastname, 
                            $timegap, $action, $comment);
					}
				}
			}
		}
		if (isset($table->data)) {
			print_table($table);
		}
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_unassessed_teacher_submissions($exercise, $user) {
	// list the teacher submissions not assessed by this user
	global $CFG;
	
	$table->head = array (get_string("title", "exercise"), get_string("action", "exercise"), get_string("comment", "exercise"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = exercise_get_teacher_submissions($exercise)) {
		foreach ($submissions as $submission) {
			$comment = "";
			// see if user already graded this assessment
			if ($assessment = get_record_select("exercise_assessments", "submissionid = $submission->id
					AND userid = $user->id")) {
				$timenow = time();
				if (($timenow - $assessment->timecreated < $CFG->maxeditingtime)) {
					// last chance salon
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$exercise->id&sid=$submission->id\">".
						get_string("edit", "exercise")."</A>";
					$table->data[] = array(exercise_print_submission_title($exercise, $submission), $action, $comment);
					}
				}
			else { // no assessment
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$exercise->id&sid=$submission->id\">".
					get_string("assess", "exercise")."</A>";
				$table->data[] = array(exercise_print_submission_title($exercise, $submission), $action, $comment);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_ungraded_assessments($exercise, $stype) {
	global $CFG;
	
	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
	if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
		error("Course Module ID was incorrect");
	}

	// lists all the assessments of student submissions for grading by teacher
	$table->head = array (get_string("title", "exercise"), get_string("submittedby", "exercise"),
	get_string("assessor", "exercise"), get_string("timeassessed", "exercise"), get_string("action", "exercise"));
	$table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;
	$timenow = time();
	
	switch ($stype) {
		case "student" :
			$assessments = exercise_get_ungraded_assessments_student($exercise);
			break;
		case "teacher" :
			$assessments = exercise_get_ungraded_assessments_teacher($exercise);
			break;
		}
	if ($assessments) {
		foreach ($assessments as $assessment) {
			if (!isteacher($exercise->course, $assessment->userid)) { // don't let teacher grade their own assessments
				if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&id=$cm->id&stype=$stype&aid=$assessment->id\">".
						get_string("edit", "exercise")."</A>";
					}
				else {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&id=$cm->id&stype=$stype&aid=$assessment->id\">".
						get_string("grade", "exercise")."</A>";
					}
				$submission = get_record("exercise_submissions", "id", $assessment->submissionid);
				$submissionowner = get_record("user", "id", $submission->userid);
				$assessor = get_record("user", "id", $assessment->userid);
				$table->data[] = array(exercise_print_submission_title($exercise, $submission), 
					$submissionowner->firstname." ".$submissionowner->lastname, 
					$assessor->firstname." ".$assessor->lastname, userdate($assessment->timecreated), $action);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}
	

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_list_user_submissions($exercise, $user) {
	global $CFG;

	if (! $course = get_record("course", "id", $exercise->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
	
	$timenow = time();
	$table->head = array (get_string("title", "exercise"),  get_string("action", "exercise"),
		get_string("submitted", "exercise"),  get_string("assessment", "exercise"));
	$table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = exercise_get_user_submissions($exercise, $user)) {
        foreach ($submissions as $submission) {
			$action = '';
			$comment = '';
			// allow user to delete submission if it's warm
			if ($submission->timecreated > $timenow - $CFG->maxeditingtime) {
				$action = "<a href=\"submissions.php?action=userconfirmdelete&id=$cm->id&sid=$submission->id\">".
					get_string("delete", "exercise")."</a>";
			}
            // if this is a teacher's submission (an exercise descrription) ignore any assessments
            if (!$submission->isexercise) {
                // get the teacher assessments (could be more than one, if unlikely, when multiple teachers)
                if ($assessments = get_records_select("exercise_assessments", "exerciseid = $exercise->id AND 
                            submissionid = $submission->id")) {
                    foreach ($assessments as $assessment) {
                        // make sure it's real
                        if ($assessment->timecreated < $timenow - $CFG->maxeditingtime) { // it's cold
                            if ($action) {
                                $action .= " | ";
                            }
                            $action .= "<a href=\"assessments.php?action=viewassessment&id=$cm->id&aid=$assessment->id\">".
                                get_string("viewassessment", "exercise")."</a>";
                            if ($comment) {
                                $action .= " | ";
                            }
                            $grade = number_format($assessment->grade * $exercise->grade / 100.0, 1);
                            if ($submission->late) {
                                $comment .= get_string("grade").
                                    ": <font color=\"red\">($grade)</font>";
                            } else {
                                $comment .= get_string("grade").": $grade";
                            }
                        }
                    }
                }
            }
   			if (!$comment and isstudent($course->id, $user->id)) {
				$comment = get_string("awaitingassessmentbythe", "exercise", $course->teacher);
			}
            $submissiondate = userdate($submission->timecreated);
            if ($submission->late) {
                $submissiondate = "<font color=\"red\">".$submissiondate."</font>";
            }
			$table->data[] = array(exercise_print_submission_title($exercise, $submission), $action,
				$submissiondate, $comment);
		}
		print_table($table);
	}
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assessment_form($exercise, $assessment = false, $allowchanges = false, $returnto = '') {
	// prints several variants of the assessment form
	global $CFG, $THEME, $USER, $EXERCISE_SCALES, $EXERCISE_EWEIGHTS;
	
	if (! $course = get_record("course", "id", $exercise->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
	
	$timenow = time();

	if ($assessment) {
			
		if (!$submission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
			error ("exercise_print_assessment_form: Submission record not found");
			}
		// test if this assessment is from a teacher or student.
        // Teacher's assessments are more complicated as we need to go back a couple of steps
        // to find the exercise. Student's assessments are directly associated with an exercise.
		if (isteacher($course->id, $assessment->userid)) { 
			// A teacher's assessment, requires getting the student's assessment(s) 
            // and finding which of those assessments which comes from a teacher submission,
            // that is the exercise
			$exercisefound = false;
			if (!$submissionowner = get_record("user", "id", $submission->userid)) {
				error ("exercise_print_assessment_form: User record not found");
				}
			if ($initialassessments = exercise_get_user_assessments($exercise, $submissionowner)) {
				// should only be one but we'll loop anyway
				foreach($initialassessments as $initialassessment) {
					if (!$teachersubmission = get_record("exercise_submissions", "id", $initialassessment->submissionid)) {
						error ("exercise_print_assessment_form: Teacher Submission record not found");
						}
					if ($teachersubmission->isexercise) {
						$exercisefound = true;
						break;
						}
					}
				}
			if ($exercisefound) {
				print_heading(get_string("theexerciseandthesubmissionby", "exercise", 
					"$submissionowner->firstname $submissionowner->lastname"));
				echo "<CENTER><TABLE BORDER=\"1\" WIDTH=\"30%\"><TR>
					<TD ALIGN=CENTER BGCOLOR=\"$THEME->cellcontent\">\n";
				echo exercise_print_submission_title($exercise, $teachersubmission);
				echo "</TD></TR></TABLE><BR CLEAR=ALL>\n";
				}
			}
		else { 
			// it's a student assessment, print instructions if it's their own assessment
			if ($assessment->userid == $USER->id) {
				print_heading_with_help(get_string("pleaseusethisform", "exercise"), "grading", "exercise");
				}
			}
			
		echo "<CENTER><TABLE BORDER=\"1\" WIDTH=\"30%\"><TR>
			<TD ALIGN=CENTER BGCOLOR=\"$THEME->cellcontent\">\n";
		echo exercise_print_submission_title($exercise, $submission);
		echo "</TD></TR></TABLE><BR CLEAR=ALL>\n";
		
		// only show the grade if grading strategy > 0 and the grade is positive
		if ($exercise->gradingstrategy and $assessment->grade >= 0) { 
			echo "<CENTER><B>".get_string("thegradeis", "exercise").": ".
				number_format($assessment->grade * $exercise->grade / 100.0, 2)." (".
				get_string("maximumgrade")." ".number_format($exercise->grade, 0).")</B></CENTER><BR CLEAR=ALL>\n";
			}
		}
		
	// now print the grading form with the teacher's comments if any
	// FORM is needed for Mozilla browsers, else radio bttons are not checked
		?>
	<form name="assessmentform" method="post" action="assessments.php">
	<INPUT TYPE="hidden" NAME="id" VALUE="<?PHP echo $cm->id ?>">
	<input type="hidden" name="aid" value="<?PHP echo $assessment->id ?>">
	<input type="hidden" name="action" value="updateassessment">
	<input type="hidden" name="resubmit" value="0">
	<input type="hidden" name="returnto" value="<?PHP echo $returnto ?>">
	<?PHP
	if ($assessment) {
		if (!$assessmentowner = get_record("user", "id", $assessment->userid)) {
			error("Exercise_print_assessment_form: could not find user record");
			}
		if ($assessmentowner->id == $USER->id) {
			$formtitle = get_string("yourassessment", "exercise");
			}
		else {
			$formtitle = get_string("assessmentby", "exercise", "$assessmentowner->firstname $assessmentowner->lastname");
			}
		}
	else {
		$formtitle = get_string("assessmentform", "exercise");
		}
	echo "<center><table cellpadding=\"2\" border=\"1\">\n";
	echo "<tr valign=top>\n";
	echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\"><center><b>$formtitle</b></center></td>\n";
	echo "</tr>\n";

	// get the assignment elements...
	if (!$elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC")) {
		print_string("noteonassignmentelements", "exercise");
		}
	else {
		foreach ($elementsraw as $element) {
			$elements[] = $element;   // to renumber index 0,1,2...
			}
		}

	if ($assessment) {
		// get any previous grades...
		if ($gradesraw = get_records_select("exercise_grades", "assessmentid = $assessment->id", "elementno")) {
			foreach ($gradesraw as $grade) {
				$grades[] = $grade;   // to renumber index 0,1,2...
				}
			}
		}
	else {
		// setup dummy grades array
		for($i = 0; $i < count($elementsraw); $i++) { // gives a suitable sized loop
			$grades[$i]->feedback = get_string("yourfeedbackgoeshere", "exercise");
			$grades[$i]->grade = 0;
			}
		}
				
	// determine what sort of grading
	switch ($exercise->gradingstrategy) {
		case 0:  // no grading
			// now print the form
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					echo text_to_html($grades[$i]->feedback);
					}
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				}
			break;
			
		case 1: // accumulative grading
			// now print the form
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
				
				// get the appropriate scale
				$scalenumber=$elements[$i]->scale;
				$SCALE = (object)$EXERCISE_SCALES[$scalenumber];
				switch ($SCALE->type) {
					case 'radio' :
							// show selections highest first
							echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
							for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
								$checked = false;
								if (isset($grades[$i]->grade)) { 
									if ($j == $grades[$i]->grade) {
										$checked = true;
										}
									}
								else { // there's no previous grade so check the lowest option
									if ($j == 0) {
										$checked = true;
										}
									}
								if ($checked) {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
									}
								else {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
									}
								}
							echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
							break;
					case 'selection' :	
							unset($numbers);
							for ($j = $SCALE->size; $j >= 0; $j--) {
								$numbers[$j] = $j;
								}
							if (isset($grades[$i]->grade)) {
								choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
								}
							else {
								choose_from_menu($numbers, "grade[$i]", 0, "");
								}
							break;
						
					echo "	</TD>\n";
					echo "</TR>\n";
					}
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					echo text_to_html($grades[$i]->feedback);
					}
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				}
			break;
			
		case 2: // error banded grading
			// now run through the elements
			$error = 0;
			for ($i=0; $i < count($elements) - 1; $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
					
				// get the appropriate scale - yes/no scale (0)
				$SCALE = (object) $EXERCISE_SCALES[0];
				switch ($SCALE->type) {
					case 'radio' :
							// show selections highest first
							echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
							for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
								$checked = false;
								if (isset($grades[$i]->grade)) { 
									if ($j == $grades[$i]->grade) {
										$checked = true;
										}
									}
								else { // there's no previous grade so check the lowest option
									if ($j == 0) {
										$checked = true;
										}
									}
								if ($checked) {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
									}
								else {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
									}
								}
							echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
							break;
					case 'selection' :	
							unset($numbers);
							for ($j = $SCALE->size; $j >= 0; $j--) {
								$numbers[$j] = $j;
								}
							if (isset($grades[$i]->grade)) {
								choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
								}
							else {
								choose_from_menu($numbers, "grade[$i]", 0, "");
								}
							break;
					}
		
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[$i]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					if (isset($grades[$i]->feedback)) {
						echo text_to_html($grades[$i]->feedback);
						}
					}
				echo "&nbsp;</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				if (empty($grades[$i]->grade)) {
					$error += $EXERCISE_EWEIGHTS[$elements[$i]->weight];
					}
				}
			// print the number of negative elements
			// echo "<TR><TD>".get_string("numberofnegativeitems", "exercise")."</TD><TD>$negativecount</TD></TR>\n";
			// echo "<TR valign=top>\n";
			// echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "</TABLE></CENTER>\n";
			// now print the grade table
			echo "<P><CENTER><B>".get_string("gradetable","exercise")."</B></CENTER>\n";
			echo "<CENTER><TABLE cellpadding=5 border=1><TR><TD ALIGN=\"CENTER\">".
				get_string("numberofnegativeresponses", "exercise");
			echo "</TD><TD>". get_string("suggestedgrade", "exercise")."</TD></TR>\n";
			for ($i=0; $i<=$exercise->nelements; $i++) {
				if ($i == intval($error + 0.5)) {
					echo "<TR><TD ALIGN=\"CENTER\"><IMG SRC=\"$CFG->pixpath/t/right.gif\"> $i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				else {
					echo "<TR><TD ALIGN=\"CENTER\">$i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				}
			echo "</TABLE></CENTER>\n";
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD align=\"right\"><b>".
				get_string("optionaladjustment", "exercise").":</b></TD><TD>\n";
			unset($numbers);
			for ($j = 20; $j >= -20; $j--) {
				$numbers[$j] = $j;
				}
			if (isset($grades[$exercise->nelements]->grade)) {
				choose_from_menu($numbers, "grade[$exercise->nelements]", $grades[$exercise->nelements]->grade, "");
				}
			else {
				choose_from_menu($numbers, "grade[$exercise->nelements]", 0, "");
				}
			echo "</TD></TR>\n";
			break;
			
		case 3: // criteria grading
			echo "<TR valign=top>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","exercise")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("select", "exercise")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("suggestedgrade", "exercise")."</B></TD>\n";
			// find which criteria has been selected (saved in the zero element), if any
			if (isset($grades[0]->grade)) {
				$selection = $grades[0]->grade;
				}
			else {
				$selection = 0;
				}
			// now run through the elements
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD>$iplus1</TD><TD>".text_to_html($elements[$i]->description)."</TD>\n";
				if ($selection == $i) {
					echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[0]\" VALUE=\"$i\" CHECKED></TD>\n";
					}
				else {
					echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[0]\" VALUE=\"$i\"></TD>\n";
					}
				echo "<TD align=center>{$elements[$i]->maxscore}</TD></TR>\n";
				}
			echo "</TABLE></CENTER>\n";
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD align=\"right\"><b>".
				get_string("optionaladjustment", "exercise")."</b></TD><TD>\n";
			unset($numbers);
			for ($j = 20; $j >= -20; $j--) {
				$numbers[$j] = $j;
				}
			if (isset($grades[1]->grade)) {
				choose_from_menu($numbers, "grade[1]", $grades[1]->grade, "");
				}
			else {
				choose_from_menu($numbers, "grade[1]", 0, "");
				}
			echo "</TD></TR>\n";
			break;
			
		case 4: // rubric grading
			// now run through the elements...
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=\"top\">\n";
				echo "<TD align=\"right\"><b>".get_string("element", "exercise")." $iplus1:</b></TD>\n";
				echo "<TD>".text_to_html($elements[$i]->description).
					 "<P align=\"right\"><font size=\"1\">Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font></TD></tr>\n";
				echo "<TR valign=\"top\">\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\" align=\"center\"><B>".get_string("select", "exercise")."</B></TD>\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","exercise")."</B></TD></tr>\n";
				if (isset($grades[$i])) {
					$selection = $grades[$i]->grade;
					} else {
					$selection = 0;
					}
				// ...and the rubrics
				if ($rubricsraw = get_records_select("exercise_rubrics", "exerciseid = $exercise->id AND 
						elementno = $i", "rubricno ASC")) {
					unset($rubrics);
					foreach ($rubricsraw as $rubic) {
						$rubrics[] = $rubic;   // to renumber index 0,1,2...
						}
					for ($j=0; $j<5; $j++) {
						if (empty($rubrics[$j]->description)) {
							break; // out of inner for loop
							}
						echo "<TR valign=top>\n";
						if ($selection == $j) {
							echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED></TD>\n";
							}else {
							echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"></TD>\n";
							}
						echo "<TD>".text_to_html($rubrics[$j]->description)."</TD>\n";
						}
					echo "<TR valign=top>\n";
					echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
					echo "	<TD>\n";
					if ($allowchanges) {
						echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
						if (isset($grades[$i]->feedback)) {
							echo $grades[$i]->feedback;
							}
						echo "</textarea>\n";
						}
					else {
						echo text_to_html($grades[$i]->feedback);
						}
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr valign=\"top\">\n";
					echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</tr>\n";
					}
				}
			break;
		} // end of outer switch
	
	// now get the general comment (present in all types)
	echo "<tr valign=\"top\">\n";
	switch ($exercise->gradingstrategy) {
		case 0:
		case 1:
		case 4 : // no grading, accumulative and rubic
			echo "	<td align=\"right\"><P><B>". get_string("generalcomment", "exercise").":</B></P></TD>\n";
			break; 
		default : 
			echo "	<td align=\"right\"><P><B>". get_string("reasonforadjustment", "exercise").":</B></P></TD>\n";
		}
	echo "	<td>\n";
	if ($allowchanges) {
		echo "		<textarea name=\"generalcomment\" rows=5 cols=75 wrap=\"virtual\">\n";
		if (isset($assessment->generalcomment)) {
			echo $assessment->generalcomment;
			}
		echo "</textarea>\n";
		}
	else {
		if ($assessment) {
			if (isset($assessment->generalcomment)) {
				echo text_to_html($assessment->generalcomment);
				}
			}
		else {
			print_string("yourfeedbackgoeshere", "exercise");
			}
		}
	echo "&nbsp;</td>\n";
	echo "</tr>\n";
	echo "<tr valign=\"top\">\n";
	echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
	echo "</tr>\n";
	
	$timenow = time();
	// the teacher's comment on the assessment
	// always allow the teacher to change their comment and grade if it's not their assessment!
	if (isteacher($course->id) and ($assessment->userid != $USER->id)) {  
		echo "<tr><td align=\"right\"><b>".get_string("gradeforstudentsassessment", "exercise", $course->student).
			"</td><td>\n";
		// set up coment scale
		for ($i=COMMENTSCALE; $i>=0; $i--) {
			$num[$i] = $i;
			}
		choose_from_menu($num, "gradinggrade", $assessment->gradinggrade, "");
		echo "</td></tr>\n";
		echo "<tr valign=\"top\">\n";
		echo "	<td align=\"right\"><p><b>". get_string("teacherscomment", "exercise").":</b></p></td>\n";
		echo "	<td>\n";
		echo "<textarea name=\"teachercomment\" rows=\"5\" cols=\"75\" wrap=\"virtual\">\n";
		if (isset($assessment->teachercomment)) {
			echo $assessment->teachercomment;
			}
		echo "</textarea>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		}
	elseif ($assessment->timegraded and ($assessment->timegraded < ($timenow - $CFG->maxeditingtime))) {
		// now show the teacher's comment (but not the grade) to the student if available...
		echo "<tr valign=top>\n";
		echo "	<td align=\"right\"><p><b>". get_string("teacherscomment", "exercise").":</b></p></td>\n";
		echo "	<td>\n";
		echo text_to_html($assessment->teachercomment);
		echo "&nbsp;</td>\n";
		echo "</tr>\n";
		echo "<tr valign=\"top\">\n";
		echo "<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</td>\n";
		echo "</tr>\n";
		}
		
	// ...and close the table, show buttons if needed...
	echo "</table><br />\n";
	if ($assessment and $allowchanges) {  
		if (isteacher($course->id)) { 
			// ...show two buttons...to resubmit or not to resubmit
			echo "<input type=\"button\" value=\"".get_string("studentnotallowed", "exercise", $course->student)."\" 
				onclick=\"document.assessmentform.submit();\">\n";
			echo "<input type=\"button\" value=\"".get_string("studentallowedtoresubmit", "exercise", $course->student)."\" 
				onclick=\"document.assessmentform.resubmit.value='1';document.assessmentform.submit();\">\n";
			}
		else {
			// ... show save button
			echo "<input type=\"submit\" value=\"".get_string("savemyassessment", "exercise")."\">\n";
			}
		}
	echo "</center></form>\n";
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assessments_by_user_for_admin($exercise, $user) {

	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
		}

	if ($assessments =exercise_get_user_assessments($exercise, $user)) {
		foreach ($assessments as $assessment) {
			echo "<p><center><b>".get_string("assessmentby", "exercise", $user->firstname." ".$user->lastname)."</b></center></p>\n";
			exercise_print_assessment_form($exercise, $assessment);
			echo "<p align=\"right\"><a href=\"assessments.php?action=adminamendgradinggrade&id=$cm->id&aid=$assessment->id\">".
                get_string("amend", "exercise")." ".get_string("gradeforstudentsassessment","exercise",
                $course->student)."</a>\n";
			echo " | <a href=\"assessments.php?action=adminconfirmdelete&id=$cm->id&aid=$assessment->id\">".
				get_string("delete", "exercise")."</a></p><hr>\n";
			}
		}
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assessments_for_admin($exercise, $submission) {

	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
		}

	if ($assessments =exercise_get_assessments($submission)) {
		foreach ($assessments as $assessment) {
			if (!$user = get_record("user", "id", $assessment->userid)) {
				error (" exercise_print_assessments_for_admin: unable to get user record");
				}
			echo "<p><center><b>".get_string("assessmentby", "exercise", $user->firstname." ".$user->lastname)."</b></center></p>\n";
			exercise_print_assessment_form($exercise, $assessment);
			echo "<p align=\"right\"><a href=\"assessments.php?action=adminconfirmdelete&id=$cm->id&aid=$assessment->id\">".
				get_string("delete", "exercise")."</a></p><hr>\n";
			}
		}
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_assignment_info($exercise) {

	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
	// print standard assignment heading
	$strdifference = format_time($exercise->deadline - time());
	if (($exercise->deadline - time()) < 0) {
		$strdifference = "<font color=\"red\">$strdifference</font>";
	}
	$strduedate = userdate($exercise->deadline)." ($strdifference)";
	print_simple_box_start("center");
	print_heading($exercise->name, "center");
	print_simple_box_start("center");
	echo "<b>".get_string("duedate", "exercise")."</b>: $strduedate<br />";
	echo "<b>".get_string("maximumgrade")."</b>: $exercise->grade<br />";
	echo "<b>".get_string("handlingofmultiplesubmissions", "exercise")."</b>:";
	if ($exercise->usemaximum) {
		echo get_string("usemaximum", "exercise")."<br />\n";
	}
	else {
		echo get_string("usemean", "exercise")."<br />\n";
	}
	echo "<b>".get_string("detailsofassessment", "exercise")."</b>: 
		<a href=\"assessments.php?id=$cm->id&action=displaygradingform\">".
		get_string("specimenassessmentform", "exercise")."</a><br />";
	print_simple_box_end();
	print_simple_box_end();
	echo "<br />";	
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_difference($time) {
    if ($time < 0) {
        $timetext = get_string("late", "assignment", format_time($time));
        return " (<FONT COLOR=RED>$timetext</FONT>)";
    } else {
        $timetext = get_string("early", "assignment", format_time($time));
        return " ($timetext)";
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_dual_assessment_form($exercise, $assessment, $submission, $returnto = '') {
	// prints the user's assessment and a blank form for the user's submission (for teachers only)
	global $CFG, $THEME, $USER, $EXERCISE_SCALES, $EXERCISE_EWEIGHTS;
	
	if (! $course = get_record("course", "id", $exercise->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
	
	$timenow = time();

	if(!$submissionowner = get_record("user", "id", $submission->userid)) {
		error("Print dual assessment form: User record not found");
		}

	echo "<CENTER><TABLE BORDER=\"1\" WIDTH=\"30%\"><TR>
		<TD ALIGN=CENTER BGCOLOR=\"$THEME->cellcontent\">\n";
	if (!$teachersubmission = get_record("exercise_submissions", "id", $assessment->submissionid)) {
		error ("exercise_print_assessment_form: Submission record not found");
		}
	echo exercise_print_submission_title($exercise, $teachersubmission);
	echo "</TD></TR></TABLE><BR CLEAR=ALL>\n";
	
	print_heading_with_help(get_string("pleasegradetheassessment", "exercise", 
		"$submissionowner->firstname $submissionowner->lastname"), "gradinggrade", "exercise");
	
	echo "<CENTER><TABLE BORDER=\"1\" WIDTH=\"30%\"><TR>
		<TD ALIGN=CENTER BGCOLOR=\"$THEME->cellcontent\">\n";
	echo exercise_print_submission_title($exercise, $submission);
	echo "</TD></TR></TABLE></center><BR CLEAR=ALL>\n";

	// only show the grade if grading strategy > 0 and the grade is positive
	if ($exercise->gradingstrategy and $assessment->grade >= 0) { 
		echo "<CENTER><B>".get_string("thegradeis", "exercise").": ".
			number_format($assessment->grade * $exercise->grade / 100.0, 2)." (".
			get_string("maximumgrade")." ".number_format($exercise->grade, 0).")</B></CENTER><BR CLEAR=ALL>\n";
		}
		
	// now print the student's assessment form with the teacher's comments if any
	// in this (first) form only allow teachers to change their comment and the grading grade
	// the other "active" elements in thie form are suffixed with "_0" to stop conflicts with the teacher's
	// assessment form
	$allowchanges = false;
	
	// FORM is needed for Mozilla browsers, else radio bttons are not checked
	?>
	<form name="assessmentform" method="post" action="assessments.php">
	<INPUT TYPE="hidden" NAME="id" VALUE="<?PHP echo $cm->id ?>">
	<input type="hidden" name="aid" value="<?PHP echo $assessment->id ?>">
	<input type="hidden" name="sid" value="<?PHP echo $submission->id ?>">
	<input type="hidden" name="action" value="updatedualassessment">
	<input type="hidden" name="resubmit" value="0">
	<input type="hidden" name="returnto" value="<?PHP echo $returnto ?>">
	<?PHP
	if (!$assessmentowner = get_record("user", "id", $assessment->userid)) {
		error("Exercise_print_dual_assessment_form: could not find user record");
		}
	echo "<center><table cellpadding=\"2\" border=\"1\">\n";
	echo "<tr valign=top>\n";
	echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\"><center><B>".get_string("assessmentby", 
		"exercise", "$assessmentowner->firstname $assessmentowner->lastname")."</b></center></td>\n";
	echo "</tr>\n";

	// get the assignment elements...
	if (!$elementsraw = get_records("exercise_elements", "exerciseid", $exercise->id, "elementno ASC")) {
		print_string("noteonassignmentelements", "exercise");
		}
	else {
		foreach ($elementsraw as $element) {
			$elements[] = $element;   // to renumber index 0,1,2...
			}
		}

	// get any previous grades...
	if ($gradesraw = get_records_select("exercise_grades", "assessmentid = $assessment->id", "elementno")) {
		foreach ($gradesraw as $grade) {
			$grades[] = $grade;   // to renumber index 0,1,2...
			}
		}
				
	// determine what sort of grading
	switch ($exercise->gradingstrategy) {
		case 0:  // no grading
			// now print the form
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					echo text_to_html($grades[$i]->feedback);
					}
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				}
			break;
			
		case 1: // accumulative grading
			// now print the form
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
				
				// get the appropriate scale
				$scalenumber=$elements[$i]->scale;
				$SCALE = (object)$EXERCISE_SCALES[$scalenumber];
				switch ($SCALE->type) {
					case 'radio' :
							// show selections highest first
							echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
							for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
								$checked = false;
								if (isset($grades[$i]->grade)) { 
									if ($j == $grades[$i]->grade) {
										$checked = true;
										}
									}
								else { // there's no previous grade so check the lowest option
									if ($j == 0) {
										$checked = true;
										}
									}
								if ($checked) {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade_0[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
									}
								else {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade_0[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
									}
								}
							echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
							break;
					case 'selection' :	
							unset($numbers);
							for ($j = $SCALE->size; $j >= 0; $j--) {
								$numbers[$j] = $j;
								}
							if (isset($grades[$i]->grade)) {
								choose_from_menu($numbers, "grade2_0[$i]", $grades[$i]->grade, "");
								}
							else {
								choose_from_menu($numbers, "grade2_0[$i]", 0, "");
								}
							break;
			
					echo "	</TD>\n";
					echo "</TR>\n";
					}
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					echo text_to_html($grades[$i]->feedback);
					}
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				}
			break;
			
		case 2: // error banded grading
			// now run through the elements
			$error = 0;
			for ($i=0; $i < count($elements) - 1; $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
					
				// get the appropriate scale - yes/no scale (0)
				$SCALE = (object) $EXERCISE_SCALES[0];
				switch ($SCALE->type) {
					case 'radio' :
							// show selections highest first
							echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
							for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
								$checked = false;
								if (isset($grades[$i]->grade)) { 
									if ($j == $grades[$i]->grade) {
										$checked = true;
										}
									}
								else { // there's no previous grade so check the lowest option
									if ($j == 0) {
										$checked = true;
										}
									}
								if ($checked) {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade_0[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
									}
								else {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade_0[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
									}
								}
							echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
							break;
					case 'selection' :	
							unset($numbers);
							for ($j = $SCALE->size; $j >= 0; $j--) {
								$numbers[$j] = $j;
								}
							if (isset($grades[$i]->grade)) {
								choose_from_menu($numbers, "grade_0[$i]", $grades[$i]->grade, "");
								}
							else {
								choose_from_menu($numbers, "grade_0[$i]", 0, "");
								}
							break;
					}
		
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[$i]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					if (isset($grades[$i]->feedback)) {
						echo text_to_html($grades[$i]->feedback);
						}
					}
				echo "&nbsp;</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				if (empty($grades[$i]->grade)) {
					$error += $EXERCISE_EWEIGHTS[$elements[$i]->weight];
					}
				}
			// print the number of negative elements
			// echo "<TR><TD>".get_string("numberofnegativeitems", "exercise")."</TD><TD>$negativecount</TD></TR>\n";
			// echo "<TR valign=top>\n";
			// echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "</TABLE></CENTER>\n";
			// now print the grade table
			echo "<P><CENTER><B>".get_string("gradetable","exercise")."</B></CENTER>\n";
			echo "<CENTER><TABLE cellpadding=5 border=1><TR><TD ALIGN=\"CENTER\">".
				get_string("numberofnegativeresponses", "exercise");
			echo "</TD><TD>". get_string("suggestedgrade", "exercise")."</TD></TR>\n";
			for ($i=0; $i<=$exercise->nelements; $i++) {
				if ($i == intval($error + 0.5)) {
					echo "<TR><TD ALIGN=\"CENTER\"><IMG SRC=\"$CFG->pixpath/t/right.gif\"> $i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				else {
					echo "<TR><TD ALIGN=\"CENTER\">$i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				}
			echo "</TABLE></CENTER>\n";
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD align=\"right\"><b>".
				get_string("optionaladjustment", "exercise")."</b></TD><TD>\n";
			unset($numbers);
			for ($j = 20; $j >= -20; $j--) {
				$numbers[$j] = $j;
				}
			if (isset($grades[$exercise->nelements]->grade)) {
				choose_from_menu($numbers, "grade_0[$exercise->nelements]", $grades[$exercise->nelements]->grade, "");
				}
			else {
				choose_from_menu($numbers, "grade_0[$exercise->nelements]", 0, "");
				}
			echo "</TD></TR>\n";
			break;
			
		case 3: // criteria grading
			echo "<TR valign=top>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","exercise")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("select", "exercise")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("suggestedgrade", "exercise")."</B></TD>\n";
			// find which criteria has been selected (saved in the zero element), if any
			if (isset($grades[0]->grade)) {
				$selection = $grades[0]->grade;
				}
			else {
				$selection = 0;
				}
			// now run through the elements
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD>$iplus1</TD><TD>".text_to_html($elements[$i]->description)."</TD>\n";
				if ($selection == $i) {
					echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade_0[0]\" VALUE=\"$i\" CHECKED></TD>\n";
					}
				else {
					echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade_0[0]\" VALUE=\"$i\"></TD>\n";
					}
				echo "<TD align=center>{$elements[$i]->maxscore}</TD></TR>\n";
				}
			echo "</TABLE></CENTER>\n";
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD align=\"right\"><b>".
				get_string("optionaladjustment", "exercise")."</b></TD><TD>\n";
			unset($numbers);
			for ($j = 20; $j >= -20; $j--) {
				$numbers[$j] = $j;
				}
			if (isset($grades[1]->grade)) {
				choose_from_menu($numbers, "grade_0[1]", $grades[1]->grade, "");
				}
			else {
				choose_from_menu($numbers, "grade[1]", 0, "");
				}
			echo "</TD></TR>\n";
			break;
			
		case 4: // rubric grading
			// now run through the elements...
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=\"top\">\n";
				echo "<TD align=\"right\"><b>".get_string("element", "exercise")." $iplus1:</b></TD>\n";
				echo "<TD>".text_to_html($elements[$i]->description).
					 "<P align=\"right\"><font size=\"1\">Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font></TD></tr>\n";
				echo "<TR valign=\"top\">\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\" align=\"center\"><B>".get_string("select", "exercise")."</B></TD>\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","exercise")."</B></TD></tr>\n";
				if (isset($grades[$i])) {
					$selection = $grades[$i]->grade;
					} else {
					$selection = 0;
					}
				// ...and the rubrics
				if ($rubricsraw = get_records_select("exercise_rubrics", "exerciseid = $exercise->id AND 
						elementno = $i", "rubricno ASC")) {
					unset($rubrics);
					foreach ($rubricsraw as $rubic) {
						$rubrics[] = $rubic;   // to renumber index 0,1,2...
						}
					for ($j=0; $j<5; $j++) {
						if (empty($rubrics[$j]->description)) {
							break; // out of inner for loop
							}
						echo "<TR valign=top>\n";
						if ($selection == $j) {
							echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade_0[$i]\" VALUE=\"$j\" CHECKED></TD>\n";
							}else {
							echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade_0[$i]\" VALUE=\"$j\"></TD>\n";
							}
						echo "<TD>".text_to_html($rubrics[$j]->description)."</TD>\n";
						}
					echo "<TR valign=top>\n";
					echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
					echo "	<TD>\n";
					if ($allowchanges) {
						echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
						if (isset($grades[$i]->feedback)) {
							echo $grades[$i]->feedback;
							}
						echo "</textarea>\n";
						}
					else {
						echo text_to_html($grades[$i]->feedback);
						}
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr valign=\"top\">\n";
					echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</tr>\n";
					}
				}
			break;
		} // end of outer switch
	
	// now get the general comment (present in all types)
	echo "<tr valign=\"top\">\n";
	switch ($exercise->gradingstrategy) {
		case 0:
		case 1:
		case 4 : // no grading, accumulative and rubic
			echo "	<td align=\"right\"><P><B>". get_string("generalcomment", "exercise").":</B></P></TD>\n";
			break; 
		default : 
			echo "	<td align=\"right\"><P><B>". get_string("reasonforadjustment", "exercise").":</B></P></TD>\n";
		}
	echo "	<td>\n";
	if ($allowchanges) {
		echo "		<textarea name=\"generalcomment\" rows=5 cols=75 wrap=\"virtual\">\n";
		if (isset($assessment->generalcomment)) {
			echo $assessment->generalcomment;
			}
		echo "</textarea>\n";
		}
	else {
		if ($assessment) {
			if (isset($assessment->generalcomment)) {
				echo text_to_html($assessment->generalcomment);
				}
			}
		else {
			print_string("yourfeedbackgoeshere", "exercise");
			}
		}
	echo "&nbsp;</td>\n";
	echo "</tr></table>\n";
	
	// the teacher's comment on the assessment
	// always allow the teacher to change/add their comment and grade if it's not their assessment!
	echo "<p><center><table cellpadding=\"5\" border=\"1\">\n";
	if (isteacher($course->id) and ($assessment->userid != $USER->id)) {  
		echo "<tr valign=\"top\">\n";
		echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\" align=\"center\"><b>".
			get_string("pleasegradetheassessment", "exercise", "$submissionowner->firstname $submissionowner->lastname").
			"</b></td>\n";
		echo "</tr>\n";
		echo "<tr><td align=\"right\"><b>".get_string("gradeforstudentsassessment", "exercise", $course->student).
			"</td><td>\n";
		// set up coment scale
		for ($i=COMMENTSCALE; $i>=0; $i--) {
			$num[$i] = $i;
			}
		choose_from_menu($num, "gradinggrade", $assessment->gradinggrade, "");
		echo "</td></tr>\n";
		echo "<tr valign=\"top\">\n";
		echo "	<td align=\"right\"><p><b>". get_string("teacherscomment", "exercise").":</b></p></td>\n";
		echo "	<td>\n";
		echo "<textarea name=\"teachercomment\" rows=\"5\" cols=\"75\" wrap=\"virtual\">\n";
		if (isset($assessment->teachercomment)) {
			echo $assessment->teachercomment;
			}
		echo "</textarea>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		}
	elseif ($assessment->timegraded and (($timenow - $assessment->timegraded) > $CFG->maxeditingtime)) {
		// now show the teacher's comment (but not the grade) to the student if available...
		echo "<tr valign=\"top\">\n";
		echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
		echo "</tr>\n";
		echo "<tr valign=top>\n";
		echo "	<td align=\"right\"><p><b>". get_string("teacherscomment", "exercise", $course->teacher).":</b></p></td>\n";
		echo "	<td>\n";
		echo text_to_html($assessment->teachercomment);
		echo "&nbsp;</td>\n";
		echo "</tr>\n";
		echo "<tr valign=\"top\">\n";
		echo "<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</td>\n";
		echo "</tr>\n";
		}
	// ...and close the table
	echo "</table><br /><hr>\n";
	
	// ****************************second form******************************************
	// now print a normal assessment form based on the student's assessment for this submission 
	// and allow the teacher to grade and add comments
	$studentassessment = $assessment;
	$allowchanges = true;
	
	print_heading_with_help(get_string("nowpleasemakeyourownassessment", "exercise",
		"$submissionowner->firstname $submissionowner->lastname"), "grading", "exercise");
	
	// is there an existing assessment for the submission
	if (!$assessment = exercise_get_submission_assessment($submission, $USER)) {
		// copy student's assessment without the comments for the student's submission
		$assessment = exercise_copy_assessment($studentassessment, $submission);
		}

	// only show the grade if grading strategy > 0 and the grade is positive
	if ($exercise->gradingstrategy and $assessment->grade >= 0) { 
		echo "<CENTER><B>".get_string("thegradeis", "exercise").": ".
			number_format($assessment->grade * $exercise->grade / 100.0, 2)." (".
			get_string("maximumgrade")." ".number_format($exercise->grade, 0).")</B></CENTER><BR CLEAR=ALL>\n";
		}
		
	echo "<center><table cellpadding=\"2\" border=\"1\">\n";
	echo "<tr valign=top>\n";
	echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\"><center><b>".get_string("yourassessment", "exercise").
		"</b></center></td>\n";
	echo "</tr>\n";
	
	
	unset($grades);
	// get any previous grades...
	if ($gradesraw = get_records_select("exercise_grades", "assessmentid = $assessment->id", "elementno")) {
		foreach ($gradesraw as $grade) {
			$grades[] = $grade;   // to renumber index 0,1,2...
			}
		}
				
	// determine what sort of grading
	switch ($exercise->gradingstrategy) {
		case 0:  // no grading
			// now print the form
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					echo text_to_html($grades[$i]->feedback);
					}
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				}
			break;
			
		case 1: // accumulative grading
			// now print the form
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
				
				// get the appropriate scale
				$scalenumber=$elements[$i]->scale;
				$SCALE = (object)$EXERCISE_SCALES[$scalenumber];
				switch ($SCALE->type) {
					case 'radio' :
							// show selections highest first
							echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
							for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
								$checked = false;
								if (isset($grades[$i]->grade)) { 
									if ($j == $grades[$i]->grade) {
										$checked = true;
										}
									}
								else { // there's no previous grade so check the lowest option
									if ($j == 0) {
										$checked = true;
										}
									}
								if ($checked) {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
									}
								else {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
									}
								}
							echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
							break;
					case 'selection' :	
							unset($numbers);
							for ($j = $SCALE->size; $j >= 0; $j--) {
								$numbers[$j] = $j;
								}
							if (isset($grades[$i]->grade)) {
								choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
								}
							else {
								choose_from_menu($numbers, "grade[$i]", 0, "");
								}
							break;
		
					echo "	</TD>\n";
					echo "</TR>\n";
					}
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					echo text_to_html($grades[$i]->feedback);
					}
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				}
			break;
			
		case 2: // error banded grading
			// now run through the elements
			$error = 0;
			for ($i=0; $i < count($elements) - 1; $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","exercise")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
					
				// get the appropriate scale - yes/no scale (0)
				$SCALE = (object) $EXERCISE_SCALES[0];
				switch ($SCALE->type) {
					case 'radio' :
							// show selections highest first
							echo "<CENTER><B>$SCALE->start</B>&nbsp;&nbsp;&nbsp;";
							for ($j = $SCALE->size - 1; $j >= 0 ; $j--) {
								$checked = false;
								if (isset($grades[$i]->grade)) { 
									if ($j == $grades[$i]->grade) {
										$checked = true;
										}
									}
								else { // there's no previous grade so check the lowest option
									if ($j == 0) {
										$checked = true;
										}
									}
								if ($checked) {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED> &nbsp;&nbsp;&nbsp;\n";
									}
								else {
									echo " <INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"> &nbsp;&nbsp;&nbsp;\n";
									}
								}
							echo "&nbsp;&nbsp;&nbsp;<B>$SCALE->end</B></CENTER>\n";
							break;
					case 'selection' :	
							unset($numbers);
							for ($j = $SCALE->size; $j >= 0; $j--) {
								$numbers[$j] = $j;
								}
							if (isset($grades[$i]->grade)) {
								choose_from_menu($numbers, "grade[$i]", $grades[$i]->grade, "");
								}
							else {
								choose_from_menu($numbers, "grade[$i]", 0, "");
								}
							break;
					}
		
				echo "	</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
				echo "	<TD>\n";
				if ($allowchanges) {
					echo "		<textarea name=\"feedback[$i]\" rows=3 cols=75 wrap=\"virtual\">\n";
					if (isset($grades[$i]->feedback)) {
						echo $grades[$i]->feedback;
						}
					echo "</textarea>\n";
					}
				else {
					if (isset($grades[$i]->feedback)) {
						echo text_to_html($grades[$i]->feedback);
						}
					}
				echo "&nbsp;</TD>\n";
				echo "</TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
				echo "</TR>\n";
				if (empty($grades[$i]->grade)) {
						$error += $EXERCISE_EWEIGHTS[$elements[$i]->weight];
					}
				}
			// print the number of negative elements
			// echo "<TR><TD>".get_string("numberofnegativeitems", "exercise")."</TD><TD>$negativecount</TD></TR>\n";
			// echo "<TR valign=top>\n";
			// echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "</TABLE></CENTER>\n";
			// now print the grade table
			echo "<P><CENTER><B>".get_string("gradetable","exercise")."</B></CENTER>\n";
			echo "<CENTER><TABLE cellpadding=5 border=1><TR><TD ALIGN=\"CENTER\">".
				get_string("numberofnegativeresponses", "exercise");
			echo "</TD><TD>". get_string("suggestedgrade", "exercise")."</TD></TR>\n";
			for ($i=0; $i<=$exercise->nelements; $i++) {
				if ($i == intval($error + 0.5)) {
					echo "<TR><TD ALIGN=\"CENTER\"><IMG SRC=\"$CFG->pixpath/t/right.gif\"> $i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				else {
					echo "<TR><TD ALIGN=\"CENTER\">$i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				}
			echo "</TABLE></CENTER>\n";
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD align=\"right\"><b>".
				get_string("optionaladjustment", "exercise")."</b></TD><TD>\n";
			unset($numbers);
			for ($j = 20; $j >= -20; $j--) {
				$numbers[$j] = $j;
				}
			if (isset($grades[$exercise->nelements]->grade)) {
				choose_from_menu($numbers, "grade[$exercise->nelements]", $grades[$exercise->nelements]->grade, "");
				}
			else {
				choose_from_menu($numbers, "grade[$exercise->nelements]", 0, "");
				}
			echo "</TD></TR>\n";
			break;
			
		case 3: // criteria grading
			echo "<TR valign=top>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","exercise")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("select", "exercise")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("suggestedgrade", "exercise")."</B></TD>\n";
			// find which criteria has been selected (saved in the zero element), if any
			if (isset($grades[0]->grade)) {
				$selection = $grades[0]->grade;
				}
			else {
				$selection = 0;
				}
			// now run through the elements
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD>$iplus1</TD><TD>".text_to_html($elements[$i]->description)."</TD>\n";
				if ($selection == $i) {
					echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[0]\" VALUE=\"$i\" CHECKED></TD>\n";
					}
				else {
					echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[0]\" VALUE=\"$i\"></TD>\n";
					}
				echo "<TD align=center>{$elements[$i]->maxscore}</TD></TR>\n";
				}
			echo "</TABLE></CENTER>\n";
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD align=\"right\"><b>".
				get_string("optionaladjustment", "exercise")."</b></TD><TD>\n";
			unset($numbers);
			for ($j = 20; $j >= -20; $j--) {
				$numbers[$j] = $j;
				}
			if (isset($grades[1]->grade)) {
				choose_from_menu($numbers, "grade[1]", $grades[1]->grade, "");
				}
			else {
				choose_from_menu($numbers, "grade[1]", 0, "");
				}
			echo "</TD></TR>\n";
			break;
			
		case 4: // rubric grading
			// now run through the elements...
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=\"top\">\n";
				echo "<TD align=\"right\"><b>".get_string("element", "exercise")." $iplus1:</b></TD>\n";
				echo "<TD>".text_to_html($elements[$i]->description).
					 "<P align=\"right\"><font size=\"1\">Weight: "
					.number_format($EXERCISE_EWEIGHTS[$elements[$i]->weight], 2)."</font></TD></tr>\n";
				echo "<TR valign=\"top\">\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\" align=\"center\"><B>".get_string("select", "exercise")."</B></TD>\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","exercise")."</B></TD></tr>\n";
				if (isset($grades[$i])) {
					$selection = $grades[$i]->grade;
					} else {
					$selection = 0;
					}
				// ...and the rubrics
				if ($rubricsraw = get_records_select("exercise_rubrics", "exerciseid = $exercise->id AND 
						elementno = $i", "rubricno ASC")) {
					unset($rubrics);
					foreach ($rubricsraw as $rubic) {
						$rubrics[] = $rubic;   // to renumber index 0,1,2...
						}
					for ($j=0; $j<5; $j++) {
						if (empty($rubrics[$j]->description)) {
							break; // out of inner for loop
							}
						echo "<TR valign=top>\n";
						if ($selection == $j) {
							echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\" CHECKED></TD>\n";
							}else {
							echo "	<TD align=center><INPUT TYPE=\"RADIO\" NAME=\"grade[$i]\" VALUE=\"$j\"></TD>\n";
							}
						echo "<TD>".text_to_html($rubrics[$j]->description)."</TD>\n";
						}
					echo "<TR valign=top>\n";
					echo "	<TD align=right><P><B>". get_string("feedback").":</B></P></TD>\n";
					echo "	<TD>\n";
					if ($allowchanges) {
						echo "		<textarea name=\"feedback[]\" rows=3 cols=75 wrap=\"virtual\">\n";
						if (isset($grades[$i]->feedback)) {
							echo $grades[$i]->feedback;
							}
						echo "</textarea>\n";
						}
					else {
						echo text_to_html($grades[$i]->feedback);
						}
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr valign=\"top\">\n";
					echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
					echo "</tr>\n";
					}
				}
			break;
		} // end of outer switch
	
	// now get the general comment (present in all types)
	echo "<tr valign=\"top\">\n";
	switch ($exercise->gradingstrategy) {
		case 0:
		case 1:
		case 4 : // no grading, accumulative and rubic
			echo "	<td align=\"right\"><P><B>". get_string("generalcomment", "exercise").":</B></P></TD>\n";
			break; 
		default : 
			echo "	<td align=\"right\"><P><B>". get_string("reasonforadjustment", "exercise").":</B></P></TD>\n";
		}
	echo "	<td>\n";
	if ($allowchanges) {
		echo "		<textarea name=\"generalcomment\" rows=5 cols=75 wrap=\"virtual\">\n";
		if (isset($assessment->generalcomment)) {
			echo $assessment->generalcomment;
			}
		echo "</textarea>\n";
		}
	else {
		if ($assessment) {
			if (isset($assessment->generalcomment)) {
				echo text_to_html($assessment->generalcomment);
				}
			}
		else {
			print_string("yourfeedbackgoeshere", "exercise");
			}
		}
	echo "&nbsp;</td>\n";
	echo "</tr>\n";
	echo "<tr valign=\"top\">\n";
	echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
	echo "</tr>\n";
	
	// ...and close the table and show two buttons...to resubmit or not to resubmit
	echo "</table>\n";
	echo "<br /><input type=\"button\" value=\"".get_string("studentnotallowed", "exercise", $course->student)."\" 
		onclick=\"document.assessmentform.submit();\">\n";
	echo "<input type=\"button\" value=\"".get_string("studentallowedtoresubmit", "exercise", $course->student)."\" 
		onclick=\"document.assessmentform.resubmit.value='1';document.assessmentform.submit();\">\n";
	echo "</center></form>\n";
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_feedback($course, $submission) {
    global $CFG, $THEME, $RATING;

    if (! $teacher = get_record("user", "id", $submission->teacher)) {
        error("Weird exercise error");
    }

    echo "\n<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 ALIGN=CENTER><TR><TD BGCOLOR=#888888>";
    echo "\n<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 VALIGN=TOP>";

    echo "\n<TR>";
    echo "\n<TD ROWSPAN=3 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($teacher->id, $course->id, $teacher->picture);
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$THEME->cellheading\">$teacher->firstname $teacher->lastname";
    echo "&nbsp;&nbsp;<FONT SIZE=2><I>".userdate($submission->timemarked)."</I>";
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";

    echo "<P ALIGN=RIGHT><FONT SIZE=-1><I>";
    if ($submission->grade) {
        echo get_string("grade").": $submission->grade";
    } else {
        echo get_string("nograde");
    }
    echo "</I></FONT></P>";

    echo text_to_html($submission->assessorcomment);
    echo "</TD></TR></TABLE>";
    echo "</TD></TR></TABLE>";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_league_table($exercise) {
	// print an order table of (student) submissions in grade order, only print the student's best submission when
	// there are multiple submissions
	if (! $course = get_record("course", "id", $exercise->course)) {
		error("Print league table: Course is misconfigured");
	}
	$nentries = $exercise->showleaguetable;
	if ($nentries == 99) {
		$nentries = 999999; // a large number
		}

	if ($exercise->anonymous and isstudent($course->id)) {
        $table->head = array (get_string("title", "exercise"), get_string("grade"));
        $table->align = array ("left", "center");
        $table->size = array ("*", "*");
    } else { // show names
        $table->head = array (get_string("title", "exercise"),  get_string("name"), get_string("grade"));
        $table->align = array ("left", "left", "center");
        $table->size = array ("*", "*", "*");
    }
    $table->cellpadding = 2;
    $table->cellspacing = 0;

	if ($submissions = exercise_get_student_submissions($exercise, "grade")) {
        $n = 1;
		foreach ($submissions as $submission) {
			if (empty($done[$submission->userid])) {
                if ($submission->late) {
                    continue;
                }
				if (!$user = get_record("user", "id", $submission->userid)) {
					error("Print league table: user not found");
					}
	            if ($exercise->anonymous and isstudent($course->id)) {
    				$table->data[] = array(exercise_print_submission_title($exercise, $submission),
                            number_format($submission->grade * $exercise->grade / 100.0, 1));
                } else {
    				$table->data[] = array(exercise_print_submission_title($exercise, $submission), 
                            $user->firstname." ".$user->lastname, 
                            number_format($submission->grade * $exercise->grade / 100.0, 1));
                }
				$n++;
                if ($n > $nentries) {
                    break;
                }
                $done[$submission->userid] = 'ok';
				}
			}
		print_heading(get_string("leaguetable", "exercise"));
		print_table($table);
		}
	}
	

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_submission_assessments($exercise, $submission) {
	// Returns a list of grades for this submission
	
	if (! $course = get_record("course", "id", $exercise->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
	
	$str = '';
	if ($assessments = exercise_get_assessments($submission)) {
		foreach ($assessments as $assessment) {
			if (isteacher($exercise->course, $assessment->userid)) {
				$str .= "[".number_format($assessment->grade * $exercise->grade / 100.0, 0)."] ";
				}
			else { // assessment by student - shouldn't happen!
				$str .= "{".number_format($assessment->grade * $exercise->grade / 100.0, 0)."} ";
				}
			}
		}
	if (!$str) {
		$str = "&nbsp;";   // be kind to Mozilla browsers!
		}
    return $str;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_submission_title($exercise, $submission) {
    global $CFG;
	
	if (!$submission->timecreated) { // a "no submission"
		return $submission->title;
		}

    $filearea = exercise_file_area_name($exercise, $submission);
    if ($basedir = exercise_file_area($exercise, $submission)) {
        if (list($file) = get_directory_list($basedir)) {
            $icon = mimeinfo("icon", $file);
            if ($CFG->slasharguments) {
                $ffurl = "file.php/$filearea/$file";
            } else {
                $ffurl = "file.php?file=/$filearea/$file";
            }
            return "<IMG SRC=\"$CFG->pixpath/f/$icon\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"File\">".
                "&nbsp;<A TARGET=\"uploadedfile\" HREF=\"$CFG->wwwroot/$ffurl\">$submission->title</A>";
        }
    }
}


//////////////////////////////////////////////////////////////////////////////////////
function exercise_print_tabbed_heading($tabs) {
// Prints a tabbed heading where one of the tabs highlighted.
// $tabs is an object with several properties.
// 		$tabs->names      is an array of tab names
//		$tabs->urls       is an array of links
// 		$tabs->align     is an array of column alignments (defaults to "center")
// 		$tabs->size      is an array of column sizes
// 		$tabs->wrap      is an array of "nowrap"s or nothing
// 		$tabs->highlight    is an index (zero based) of "active" heading .
// 		$tabs->width     is an percentage of the page (defualts to 80%)
// 		$tabs->cellpadding    padding on each cell (defaults to 5)

	global $CFG, $THEME;
	
    if (isset($tabs->names)) {
        foreach ($tabs->names as $key => $name) {
            if (!empty($tabs->urls[$key])) {
				$url =$tabs->urls[$key];
				if ($tabs->highlight == $key) {
					$tabcontents[$key] = "<b>$name</b>";
				} else {
					$tabcontents[$key] = "<a class= \"dimmed\" href=\"$url\"><b>$name</b></a>";
				}
            } else {
                $tabcontents[$key] = "<b>$name</b>";
            }
        }
    }

    if (empty($tabs->width)) {
        $tabs->width = "80%";
    }

    if (empty($tabs->cellpadding)) {
        $tabs->cellpadding = "5";
    }

    // print_simple_box_start("center", "$table->width", "#ffffff", 0);
    echo "<table width=\"$tabs-width\" border=\"0\" valign=\"top\" align=\"center\" ";
    echo " cellpadding=\"$tabs->cellpadding\" cellspacing=\"0\" class=\"generaltable\">\n";

    if (!empty($tabs->names)) {
        echo "<tr>";
		echo "<td  class=\"generaltablecell\">".
			"<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td>\n";
        foreach ($tabcontents as $key => $tab) {
            if (isset($align[$key])) {
				$alignment = "align=\"$align[$key]\"";
			} else {
                $alignment = "align=\"center\"";
            }
            if (isset($size[$key])) {
                $width = "width=\"$size[$key]\"";
            } else {
				$width = "";
			}
            if (isset($wrap[$key])) {
				$wrapping = "no wrap";
			} else {
                $wrapping = "";
            }
			if ($key == $tabs->highlight) {
				echo "<td valign=top class=\"generaltabselected\" $alignment $width $wrapping bgcolor=\"$THEME->cellheading2\">$tab</td>\n";
			} else {
				echo "<td valign=top class=\"generaltab\" $alignment $width $wrapping bgcolor=\"$THEME->cellheading\">$tab</td>\n";
			}
		echo "<td  class=\"generaltablecell\">".
			"<img width=\"10\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td>\n";
        }
        echo "</tr>\n";
    } else {
		echo "<tr><td>No names specified</td></tr>\n";
	}
	// bottom stripe
	$ncells = count($tabs->names)*2 +1;
	$height = 2;
	echo "<tr><td colspan=\"$ncells\" bgcolor=\"$THEME->cellheading2\">".
		"<img height=\"$height\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\"></td></tr>\n";
    echo "</table>\n";
	// print_simple_box_end();

    return true;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_time_to_deadline($time) {
    if ($time < 0) {
        $timetext = get_string("afterdeadline", "exercise", format_time($time));
        return " (<FONT COLOR=RED>$timetext</FONT>)";
    } else {
        $timetext = get_string("beforedeadline", "exercise", format_time($time));
        return " ($timetext)";
    }
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_teacher_table($course) {
// print how many assessments each teacher has done in each exercise

    if (! $exercises = get_all_instances_in_course("exercise", $course)) {
        notice("There are no exercises", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();
    
    $table->head[] = '';
    $table->align[] = 'left';
    $table->size[] = '*';
    $table->head[] = get_string("total");
    $table->align[] = "center";
    $table->size[] = "*";
        foreach ($exercises as $exercise) {
        $table->head[] = $exercise->name;
	    $table->align[] = "center";
	    $table->size[] = "*";
        }
 	$table->cellpadding = 2;
	$table->cellspacing = 0;

    if (!$teachers = get_course_teachers($course->id, "u.firstname, u.lastname")) {
        error("No teachers on this course!");
    }
    for ($j = 0; $j < count($exercises); $j++) {
        $grand[$j] = 0;
    }
    $grandtotal = 0;
    foreach ($teachers as $teacher) {
        unset($n);
        $total = 0;
        $j = 0;
        foreach ($exercises as $exercise) {
            $i = exercise_count_assessments_by_teacher($exercise, $teacher);
            $n[] = $i;
            $total += $i;
            $grand[$j] += $i;
            $j++;
        }
        $grandtotal += $total;
		$table->data[] = array_merge(array("$teacher->firstname $teacher->lastname"), array($total), $n);
	}
	$table->data[] = array_merge(array(get_string("total")), array($grandtotal), $grand);
	print_heading(get_string("teacherassessmenttable", "exercise", $course->teacher));
	print_table($table);
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_upload_form($exercise) {

	if (! $course = get_record("course", "id", $exercise->course)) {
        error("Course is misconfigured");
        }
    if (! $cm = get_coursemodule_from_instance("exercise", $exercise->id, $course->id)) {
        error("Course Module ID was incorrect");
		}

    echo "<DIV ALIGN=CENTER>";
    echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"POST\" ACTION=upload.php>";
    echo " <INPUT TYPE=hidden NAME=MAX_FILE_SIZE value=\"$exercise->maxbytes\">";
    echo " <INPUT TYPE=hidden NAME=id VALUE=\"$cm->id\">";
	echo "<b>".get_string("title", "exercise")."</b>: <INPUT NAME=\"title\" TYPE=\"text\" SIZE=\"60\" MAXSIZE=\"100\"><BR><BR>\n";
    echo " <INPUT NAME=\"newfile\" TYPE=\"file\" size=\"50\">";
    echo " <INPUT TYPE=submit NAME=save VALUE=\"".get_string("uploadthisfile")."\">";
    echo "</FORM>";
    echo "</DIV>";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_print_user_assessments($exercise, $user) {
	// Returns the number of assessments and a hyperlinked list of grading grades for the assessments made by this user

	if ($assessments = exercise_get_user_assessments($exercise, $user)) {
		$n = count($assessments);
		$str = "$n  (";
		foreach ($assessments as $assessment) {
			if ($assessment->timegraded) {
				$gradingscaled = intval($assessment->gradinggrade * $exercise->grade / COMMENTSCALE);
				$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$exercise->id&aid=$assessment->id\">";
				$str .= "$gradingscaled</A> ";
				}
			else {
				$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$exercise->id&aid=$assessment->id\">";
				$str .= "-</A> ";
				}
			}
		$str .= ")";
		}
	else {
		$str ="0";
		}
    return $str;
	}


///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_test_for_resubmission($exercise, $user) {
	// see if any of the user's submissions have the resubmit flag set
	$result = false;
	if ($submissions = exercise_get_user_submissions($exercise, $user)) {
		foreach ($submissions as $submission) {
			if ($submission->resubmit) {
				$result =true;
				break;
				}
			}
		}
	return $result;
	}
	

///////////////////////////////////////////////////////////////////////////////////////////////
function exercise_test_user_assessments($exercise, $user) {
	// see if user has assessed one of  teacher's exercises/submissions...
	global $CFG;
	
	$result = false;
	$timenow =time();
	if ($submissions = exercise_get_teacher_submissions($exercise)) {
		foreach ($submissions as $submission) {
			if ($assessment = exercise_get_submission_assessment($submission, $user)) {
				// ...the date stamp on the assessment should be in the past 
				if ($assessment->timecreated < $timenow) {
					$result = true;
					break;
					}
				}
			}
		}
	return $result;
	}

?>
