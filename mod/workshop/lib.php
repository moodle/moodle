<?PHP  // $Id: lib.php,v 1.0 30th April 2003

include_once("$CFG->dirroot/files/mimetypes.php");

/*** Constants **********************************/

$WORKSHOP_TYPE = array (0 => get_string("notgraded", "workshop"),
                          1 => get_string("accumulative", "workshop"),
                          2 => get_string("errorbanded", "workshop"),
                          3 => get_string("criteria", "workshop") );

$WORKSHOP_SHOWGRADES = array (0 => get_string("dontshowgrades", "workshop"),
                          1 => get_string("showgrades", "workshop") );

$WORKSHOP_SCALES = array( 
					0 => array( 'name' => get_string("scaleyes", "workshop"), 'type' => 'radio', 'size' => 2, 'start' => 'yes', 'end' => 'no'),
					1 => array( 'name' => get_string("scalepresent", "workshop"), 'type' => 'radio', 'size' => 2, 'start' => 'present', 'end' => 'absent'),
					2 => array( 'name' => get_string("scalecorrect", "workshop"), 'type' => 'radio', 'size' => 2, 'start' => 'correct', 'end' => 'incorrect'), 
					3 => array( 'name' => get_string("scalegood3", "workshop"), 'type' => 'radio', 'size' => 3, 'start' => 'good', 'end' => 'poor'), 
					4 => array( 'name' => get_string("scaleexcellent4", "workshop"), 'type' => 'radio', 'size' => 4, 'start' => 'excellent', 'end' => 'very poor'),
					5 => array( 'name' => get_string("scaleexcellent5", "workshop"), 'type' => 'radio', 'size' => 5, 'start' => 'excellent', 'end' => 'very poor'),
					6 => array( 'name' => get_string("scaleexcellent7", "workshop"), 'type' => 'radio', 'size' => 7, 'start' => 'excellent', 'end' => 'very poor'),
					7 => array( 'name' => get_string("scale10", "workshop"), 'type' => 'selection', 'size' => 10),
					8 => array( 'name' => get_string("scale20", "workshop"), 'type' => 'selection', 'size' => 20),
					9 => array( 'name' => get_string("scale100", "workshop"), 'type' => 'selection', 'size' => 100)); 

$WORKSHOP_EWEIGHTS = array(  0 => -4.0, 1 => -2.0, 2 => -1.5, 3 => -1.0, 4 => -0.75, 5 => -0.5,  6 => -0.25, 
											7 => 0.0, 8 => 0.25, 9 => 0.5, 10 => 0.75, 11=> 1.0, 12 => 1.5, 13=> 2.0, 14 => 4.0); 

$WORKSHOP_FWEIGHTS = array(  0 => 0, 1 => 0.1, 2 => 0.25, 3 => 0.5, 4 => 0.75, 5 => 1,  6 => 1.5, 
											7 => 2.0, 8 => 3.0, 9 => 5.0, 10 => 7.5, 11=> 10.0); 

define("COMMENTSCALE", 20);

/*** Standard Moodle functions ******************
function workshop_add_instance($workshop) 
function workshop_update_instance($workshop) 
function workshop_delete_instance($id) 
function workshop_user_outline($course, $user, $mod, $workshop) 
function workshop_user_complete($course, $user, $mod, $workshop) 
function workshop_cron () 
function workshop_print_recent_activity(&$logs, $isteacher=false) 
function workshop_grades($workshopid) 
**********************************************/

function workshop_add_instance($workshop) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $workshop->timemodified = time();
    
    $workshop->deadline = make_timestamp($workshop->deadlineyear, 
			$workshop->deadlinemonth, $workshop->deadlineday, $workshop->deadlinehour, 
			$workshop->deadlineminute);

    return insert_record("workshop", $workshop);
}


function workshop_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
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

function workshop_update_instance($workshop) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $workshop->timemodified = time();

    $workshop->deadline = make_timestamp($workshop->deadlineyear, 
			$workshop->deadlinemonth, $workshop->deadlineday, $workshop->deadlinehour, 
			$workshop->deadlineminute);

    $workshop->id = $workshop->instance;

    return update_record("workshop", $workshop);
}


function workshop_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $workshop = get_record("workshop", "id", "$id")) {
        return false;
    }
	
	// delete all the associated records in the workshop tables, start positive...
    $result = true;

    if (! delete_records("workshop_grades", "workshopid", "$workshop->id")) {
        $result = false;
    }

    if (! delete_records("workshop_elements", "workshopid", "$workshop->id")) {
        $result = false;
    }

    if (! delete_records("workshop_assessments", "workshopid", "$workshop->id")) {
        $result = false;
    }

    if (! delete_records("workshop_submissions", "workshopid", "$workshop->id")) {
        $result = false;
    }

    if (! delete_records("workshop", "id", "$workshop->id")) {
        $result = false;
    }

    return $result;
}

function workshop_user_outline($course, $user, $mod, $workshop) {
    if ($submission = workshop_get_submission($workshop, $user)) {
        
        if ($submission->grade) {
            $result->info = get_string("grade").": $submission->grade";
        }
        $result->time = $submission->timemodified;
        return $result;
    }
    return NULL;
}

function workshop_user_complete($course, $user, $mod, $workshop) {
    if ($submission = workshop_get_submission($workshop, $user)) {
        if ($basedir = workshop_file_area($workshop, $user)) {
            if ($files = get_directory_list($basedir)) {
                $countfiles = count($files)." ".get_string("submissions", "workshop");
                foreach ($files as $file) {
                    $countfiles .= "; $file";
                }
            }
        }

        print_simple_box_start();
        echo "<P><FONT SIZE=1>";
        echo get_string("lastmodified").": ";
        echo userdate($submission->timemodified);
        echo workshop_print_difference($workshop->timedue - $submission->timemodified);
        echo "</FONT></P>";

        workshop_print_user_files($workshop, $user);

        echo "<BR>";

        workshop_print_feedback($course, $submission);

        print_simple_box_end();

    } else {
        print_string("notsubmittedyet", "workshop");
    }
}


function workshop_cron () {
// Function to be run periodically according to the moodle cron
// Finds all workshop notifications that have yet to be mailed out, and mails them

    global $CFG, $USER;

    $cutofftime = time() - $CFG->maxeditingtime;

	// look for new assessments
	if ($assessments = workshop_get_unmailed_assessments($cutofftime)) {
        $timenow = time();

        foreach ($assessments as $assessment) {

            echo "Processing workshop assessment $assessment->id\n";

			if (! $submission = get_record("workshop_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
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

            if (! $course = get_record("course", "id", "$assessment->course")) {
                echo "Could not find course $assessment->course\n";
                continue;
            }
			
            if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, $submissionowner->id)) {
                continue;  // Not an active participant
            }

            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, $assessmentowner->id)) {
                continue;  // Not an active participant
            }

            if (! $workshop = get_coursemodule_from_instance("workshop", $assessment->workshopid, $course->id)) {
                echo "Could not find course module for workshop id $submission->workshop\n";
                continue;
            }

            $strworkshops = get_string("modulenameplural", "workshop");
            $strworkshop  = get_string("modulename", "workshop");

			// it's an assessment, tell the submission owner
			$USER->lang = $submissionowner->lang;
			$sendto = $submissionowner;
			$msg = "Your assignment \"$submission->title\" has been assessed.\n".
				"The comments and grade can be seen in ".
				"the workshop assignment '$workshop->name'\n\n";

			$postsubject = "$course->shortname: $strworkshops: $workshop->name";
            $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            $posttext .= "You can see it in your workshop assignment:\n";
            $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($user->mailformat == 1) {  // HTML
                $posthtml = "<P><FONT FACE=sans-serif>".
              "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$pgassessment->id\">$workshop->name</A></FONT></P>";
              $posthtml .= "<HR><FONT FACE=sans-serif>";
              $posthtml .= "<P>$msg</P>";
              $posthtml .= "<P>You can see it <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">";
              $posthtml .= "in to your peer graded assignment</A>.</P></FONT><HR>";
            } else {
              $posthtml = "";
            }

			if (!$teacher = get_teacher($course->id)) {
				echo "Error: can not find teacher for course $course->id!\n";
				}
				
            if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: workshop cron: Could not send out mail for id $submission->id to user $sendto->id ($sendto->email)\n";
            }
            if (! set_field("workshop_assessments", "mailed", "1", "id", "$assessment->id")) {
                echo "Could not update the mailed field for id $assessment->id\n";
            }
        }

	// look for new gradings
	if ($assessments = workshop_get_unmailed_graded_assessments($cutofftime)) {
        $timenow = time();

        foreach ($assessments as $assessment) {

            echo "Processing workshop assessment $assessment->id (graded)\n";

			if (! $submission = get_record("workshop_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
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

            if (! $course = get_record("course", "id", "$assessment->course")) {
                echo "Could not find course $assessment->course\n";
                continue;
            }
			
            if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, $submissionowner->id)) {
                continue;  // Not an active participant
            }

            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, $assessmentowner->id)) {
                continue;  // Not an active participant
            }

            if (! $workshop = get_coursemodule_from_instance("workshop", $assessment->workshopid, $course->id)) {
                echo "Could not find course module for workshop id $submission->workshop\n";
                continue;
            }

            $strworkshops = get_string("modulenameplural", "workshop");
            $strworkshop  = get_string("modulename", "workshop");

			// it's a grading tell the assessment owner
			$USER->lang = $assessmentowner->lang;
			$sendto = $assessmentowner;
			$msg = "Your assessment of the assignment \"$submission->title\" has by graded.\n".
					"The comments and grade given by the $course->teacher can be seen in ".
					"the workshop assignment '$workshop->name'\n\n";
				}
			

			$postsubject = "$course->shortname: $strworkshops: $workshop->name";
            $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            $posttext .= "You can see it in your workshop assignment:\n";
            $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($user->mailformat == 1) {  // HTML
                $posthtml = "<P><FONT FACE=sans-serif>".
              "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$pgassessment->id\">$workshop->name</A></FONT></P>";
              $posthtml .= "<HR><FONT FACE=sans-serif>";
              $posthtml .= "<P>$msg</P>";
              $posthtml .= "<P>You can see it <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">";
              $posthtml .= "in to your peer graded assignment</A>.</P></FONT><HR>";
            } else {
              $posthtml = "";
            }

			if (!$teacher = get_teacher($course->id)) {
				echo "Error: can not find teacher for course $course->id!\n";
				}
				
            if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: workshop cron: Could not send out mail for id $submission->id to user $sendto->id ($sendto->email)\n";
            }
            if (! set_field("workshop_assessments", "mailed", "1", "id", "$assessment->id")) {
                echo "Could not update the mailed field for id $assessment->id\n";
            }
        }
    }

    return true;
}


function workshop_print_recent_activity(&$logs, $isteacher=false) {
    global $CFG, $COURSE_TEACHER_COLOR;

    $content = false;
    $workshops = NULL;
	$timenow = time();

    foreach ($logs as $log) {
        if ($log->module == "workshop" and $log->action == "submit") {
            $workshops[$log->info] = get_record_sql("SELECT a.name, u.firstname, u.lastname
                                                       FROM {$CFG->prefix}workshop a, {$CFG->prefix}user u
                                                      WHERE a.id = '$log->info' AND u.id = '$log->userid'");
            $workshops[$log->info]->time = $log->time;
            $workshops[$log->info]->url  = $log->url;
        }
    }

    if ($workshops) {
        $content = true;
        print_headline(get_string("submissions", "workshop").":");
        foreach ($workshops as $workshop) {
            $date = userdate($workshop->time, "%d %b, %H:%M");
            echo "<P><FONT SIZE=1>$date - $workshop->firstname $workshop->lastname<BR>";
            echo "\"<A HREF=\"$CFG->wwwroot/mod/workshop/$workshop->url\">";
            echo "$workshop->name";
            echo "</A>\"</FONT></P>";
        }
    }
 
    $workshops = NULL;
	
	foreach ($logs as $log) {
        if ($log->module == "workshop" and $log->action == "assess") {
            if ($workshops[$log->userid] = get_record_sql("SELECT a.name, u.firstname, u.lastname
                                                       FROM {$CFG->prefix}workshop a, {$CFG->prefix}user u
                                                      WHERE a.id = '$log->info' AND u.id = '$log->userid'")) {
				$workshops[$log->userid]->time = $log->time;
				$workshops[$log->userid]->url  = $log->url;
				}
			}
		}

    if ($workshops) {
        $content = true;
        print_headline(get_string("assessments", "workshop").":");
        foreach ($workshops as $workshop) {
            $date = userdate($workshop->time, "%d %b, %H:%M");
            echo "<P><FONT SIZE=1>$date - $workshop->firstname $workshop->lastname<BR>";
            echo "\"<A HREF=\"$CFG->wwwroot/mod/workshop/$workshop->url\">";
            echo "$workshop->name";
            echo "</A>\"</FONT></P>";
        }
    }
 
    $workshops = NULL;

    foreach ($logs as $log) {
        if ($log->module == "workshop" and $log->action == "grade") {
            $workshops[$log->userid] = get_record_sql("SELECT a.name, u.firstname, u.lastname
                                                       FROM {$CFG->prefix}workshop a, {$CFG->prefix}user u
                                                      WHERE a.id = '$log->info' AND u.id = '$log->userid'");
            $workshops[$log->userid]->time = $log->time;
            $workshops[$log->userid]->url  = $log->url;
        }
    }

    if ($workshops) {
        $content = true;
        print_headline(get_string("graded", "workshop").":");
        foreach ($workshops as $workshop) {
            $date = userdate($workshop->time, "%d %b, %H:%M");
            echo "<P><FONT SIZE=1>$date - $workshop->firstname $workshop->lastname<BR>";
            echo "\"<A HREF=\"$CFG->wwwroot/mod/workshop/$workshop->url\">";
            echo "$workshop->name";
            echo "</A>\"</FONT></P>";
        }
    }
 
    $workshops = NULL;

    foreach ($logs as $log) {
        if ($log->module == "workshop" and $log->action == "close") {
            $workshops[$log->userid] = get_record_sql("SELECT a.name, u.firstname, u.lastname
                                                       FROM {$CFG->prefix}workshop a, {$CFG->prefix}user u
                                                      WHERE a.id = '$log->info' AND u.id = '$log->userid'");
            $workshops[$log->userid]->time = $log->time;
            $workshops[$log->userid]->url  = $log->url;
        }
    }

    if ($workshops) {
        $content = true;
        print_headline(get_string("closeassignment", "workshop").":");
        foreach ($workshops as $workshop) {
            $date = userdate($workshop->time, "%d %b, %H:%M");
            echo "<P><FONT SIZE=1>$date - $workshop->firstname $workshop->lastname<BR>";
            echo "\"<A HREF=\"$CFG->wwwroot/mod/workshop/$workshop->url\">";
            echo "$workshop->name";
            echo "</A>\"</FONT></P>";
        }
    }
 
    foreach ($logs as $log) {
        if ($log->module == "workshop" and $log->action == "open") {
            $workshops[$log->userid] = get_record_sql("SELECT a.name, u.firstname, u.lastname
                                                       FROM {$CFG->prefix}workshop a, {$CFG->prefix}user u
                                                      WHERE a.id = '$log->info' AND u.id = '$log->userid'");
            $workshops[$log->userid]->time = $log->time;
            $workshops[$log->userid]->url  = $log->url;
        }
    }

    if ($workshops) {
        $content = true;
        print_headline(get_string("openassignment", "workshop").":");
        foreach ($workshops as $workshop) {
            $date = userdate($workshop->time, "%d %b, %H:%M");
            echo "<P><FONT SIZE=1>$date - $workshop->firstname $workshop->lastname<BR>";
            echo "\"<A HREF=\"$CFG->wwwroot/mod/workshop/$workshop->url\">";
            echo "$workshop->name";
            echo "</A>\"</FONT></P>";
        }
    }
 
    return $content;
}

function workshop_grades($workshopid) {
/// Must return an array of grades, indexed by user, and a max grade.

    $return->grades = get_records_select_menu("workshop_submissions", 
		"workshopid = $workshopid", "", "userid, finalgrade");
    $return->maxgrade = get_field("workshop", "grade", "id", "$workshopid");
    return $return;
}

//////////////////////////////////////////////////////////////////////////////////////

/*** Functions for the workshop module ******

function workshop_count_all_assessments($workshop, $user) {
function workshop_count_all_submissions_for_assessment($workshop, $user) {
function workshop_count_peer_assessments($workshop, $user) {
function workshop_count_student_submissions($workshop) {
function workshop_count_student_submissions_for_assessment($workshop, $user) {
function workshop_count_teacher_assessments($workshop, $user) {
function workshop_count_teacher_submissions($workshop) {
function workshop_count_teacher_submissions_for_assessment($workshop, $user) {
function workshop_count_ungraded_assessments_student($workshop) {
function workshop_count_ungraded_assessments_teacher($workshop) {

function workshop_delete_user_files($workshop, $user, $exception) {

function workshop_file_area($workshop, $submission) {
function workshop_file_area_name($workshop, $submission) {

function workshop_get_assessments($submission) {
function workshop_get_student_assessments($workshop, $user) {
function workshop_get_student_submission_assessments($workshop) {
function workshop_get_student_submissions($workshop) {
function workshop_get_submission_assessment($submission, $user) {
function workshop_get_teacher_submission_assessments($workshop) {
function workshop_get_teacher_submissions($workshop) {
function workshop_get_ungraded_assessments($workshop) {
function workshop_get_unmailed_assessments($cutofftime) {
function workshop_get_unmailed_marked_assessments($cutofftime) {
function workshop_get_user_assessments($workshop, $user) {
function workshop_get_user_submissions($workshop, $user) {
function workshop_get_users_done($workshop) {

function workshop_list_all_submissions($workshop) {
function workshop_list_all_ungraded_assessments($workshop) {
function workshop_list_assessed_submissions($workshop, $user) {
function workshop_list_peer_assessments($workshop, $user) {
function workshop_list_student_submissions($workshop, $user) {
function workshop_list_teacher_assessments($workshop, $user) {
function workshop_list_teacher_submissions($workshop) {
function workshop_list_unassessed_student_submissions($workshop, $user) {
function workshop_list_unassessed_teacher_submissions($workshop, $user) {
function workshop_list_ungraded_assessments($workshop, $stype) {
function workshop_list_user_submissions($workshop, $user) {


function workshop_print_assessment($workshop, $assessment, $allowchanges)
function workshop_print_difference($time) {
function workshop_print_feedback($course, $submission) {
function workshop_print_submission_assessments($workshop, $submission, $type) {
function workshop_print_submission_title($workshop, $user) {
function workshop_print_time_to_deadline($time) {
function workshop_print_upload_form($workshop) {
function workshop_print_user_assessments($workshop, $user) {

function workshop_test_user_assessments($workshop, $user) {
***************************************/

function workshop_count_all_assessments($workshop, $user) {
	return count_records("workshop_assessments", "workshopid", $workshop->id, "userid", $user->id);
	}


function workshop_count_all_submissions_for_assessment($workshop, $user) {
	// looks at all submissions and deducts the number which has been assessed by this user
	$n = 0;
	if ($submissions = get_records_select("workshop_submissions", "workshopid = $workshop->id AND timecreated > 0")) {
		$n =count($submissions);
		foreach ($submissions as $submission) {
			$n -= count_records("workshop_assessments", "submissionid", $submission->id, "userid", $user->id);
			}
		}
	return $n;
	}


function workshop_count_assessments($workshop, $stype, $user) {
	// returns the number of assessments made by a user on either the student or teacher submissions
	// the maxeditingtime is NOT taken into account here
	
	switch ($stype) {
	case "student" :
		$submissions = workshop_get_student_submissions($workshop);
		break;
	case "teacher" :
		$submissions = workshop_get_teacher_submissions($workshop);
		break;
		}
	$n = 0;
	if ($submissions) {
		foreach ($submissions as $submission) {
			$n += count_records_select("workshop_assessments", "(submissionid = $submission->id) AND 
				(userid = $user->id)");
			}
		}
	return $n;
	}


function workshop_count_peer_assessments($workshop, $user) {
	// returns the number of assessments made by students on user's submissions
	
	$n = 0;
	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			if ($assessments = workshop_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					// ignore teacher assessments
					if (!isteacher($workshop->course, $assessment->userid)) {
						$n++;
						}
					}
				}
			}
		}
	return $n;
	}


function workshop_count_student_submissions($workshop) {
	global $CFG;
	
	 return count_records_sql("SELECT count(*) FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_students u
							WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
							  AND timecreated > 0");
	}


function workshop_count_student_submissions_for_assessment($workshop, $user) {
	global $CFG;
	
	$timenow = time();
	$n = 0;
	if ($submissions = workshop_get_student_submissions($workshop)) {
		$n =count($submissions);
		foreach ($submissions as $submission) {
			$n -= count_records_select("workshop_assessments", "submissionid = $submission->id AND 
				userid = $user->id AND timecreated < $timenow - $CFG->maxeditingtime");
			}
		}
	return $n;
	}


function workshop_count_teacher_assessments($workshop, $user) {
	// returns the number of assessments made by teachers on user's submissions
	
	$n = 0;
	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			if ($assessments = workshop_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					// count only teacher assessments
					if (isteacher($workshop->course, $assessment->userid)) {
						$n++;
						}
					}
				}
			}
		}
	return $n;
	}


function workshop_count_teacher_submissions($workshop) {
	global $CFG;
	
	 return count_records_sql("SELECT count(*) FROM {$CFG->prefix}workshop_submissions s, 
					 {$CFG->prefix}user_teachers u
							WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id");
	}


function workshop_count_teacher_submissions_for_assessment($workshop, $user) {

	$n = 0;
	if ($submissions = workshop_get_teacher_submissions($workshop)) {
		$n =count($submissions);
		foreach ($submissions as $submission) {
			$n -= count_records("workshop_assessments", "submissionid", $submission->id, "userid", $user->id);
			}
		}
	return $n;
	}


function workshop_count_ungraded_assessments_student($workshop) {
	// function returns the number of ungraded assessments by students of STUDENT submissions
	$n = 0;
	if ($submissions = workshop_get_student_submissions($workshop)) {
		foreach ($submissions as $submission) {
			if ($assessments = workshop_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					if ($assessment->timegraded == 0) {
						// ignore teacher assessments
						if (!isteacher($workshop->course, $assessment->userid)) {
							$n++;
							}
						}
					}
				}
			}
		}
	return $n;
	}


function workshop_count_ungraded_assessments_teacher($workshop) {
	// function returns the number of ungraded assessments by students of TEACHER submissions
	global $CFG;

	$timenow = time();
	$n = 0;
	if ($submissions = workshop_get_teacher_submissions($workshop)) {
		foreach ($submissions as $submission) {
			if ($assessments = workshop_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					if ($assessment->timegraded == 0) {
						// ignore teacher assessments
						if (!isteacher($workshop->course, $assessment->userid)) {
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


function workshop_delete_user_files($workshop, $user, $exception) {
// Deletes all the user files in the workshop area for a user
// EXCEPT for any file named $exception

    if (!$submissions = workshop_get_submissions($workshop, $user)) {
		notify("No submissions!");
		return;
		}
	foreach ($submissions as $submission) {
		if ($basedir = workshop_file_area($workshop, $submission)) {
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


function workshop_file_area($workshop, $submission) {
    return make_upload_directory( workshop_file_area_name($workshop, $submission) );
}


function workshop_file_area_name($workshop, $submission) {
//  Creates a directory file name, suitable for make_upload_directory()
    global $CFG;

    return "$workshop->course/$CFG->moddata/workshop/$submission->id";
}


function workshop_get_assessments($submission) {
	// Return all assessments for this submission provided they are after the editing time, oredered oldest first, newest last
	global $CFG;

	$timenow = time();
    return get_records_select("workshop_assessments", "(submissionid = $submission->id) AND 
		(timecreated < $timenow - $CFG->maxeditingtime)", "timecreated DESC");
}


function workshop_get_student_assessments($workshop, $user) {
// Return all assessments on the student submissions by a user, order by youngest first, oldest last
	global $CFG;
	
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_students u,
							{$CFG->prefix}workshop_assessments a
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
							  AND a.submissionid = s.id
							  AND a.userid = $user->id
							  ORDER BY a.timecreated DESC");
}


function workshop_get_student_submission_assessments($workshop) {
// Return all assessments on the student submissions, order by youngest first, oldest last
	global $CFG;
	
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_students u,
							{$CFG->prefix}workshop_assessments a
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
							  AND a.submissionid = s.id
							  ORDER BY a.timecreated DESC");
}


function workshop_get_student_submissions($workshop) {
// Return all  ENROLLED student submissions
	global $CFG;
	
    return get_records_sql("SELECT s.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_students u
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
							  AND s.timecreated > 0
							  ORDER BY s.title");
}


function workshop_get_submission_assessment($submission, $user) {
	// Return the user's assessment for this submission
	return get_record("workshop_assessments", "submissionid", $submission->id, "userid", $user->id);
}


function workshop_get_teacher_submission_assessments($workshop) {
// Return all assessments on the teacher submissions, order by youngest first, oldest last
	global $CFG;
	
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_teachers u,
							{$CFG->prefix}workshop_assessments a
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
							  AND a.submissionid = s.id
							  ORDER BY a.timecreated DESC");
}


function workshop_get_teacher_submissions($workshop) {
// Return all  teacher submissions, ordered by title
	global $CFG;
	
    return get_records_sql("SELECT s.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_teachers u
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id 
							  ORDER BY s.title");
}


function workshop_get_ungraded_assessments($workshop) {
	global $CFG;
	// Return all assessments which have not been graded or just graded
	$cutofftime =time() - $CFG->maxeditingtime;
    return get_records_select("workshop_assessments", "workshopid = $workshop->id AND (timegraded = 0 OR 
				timegraded > $cutofftime)", "timecreated"); 
	}


function workshop_get_ungraded_assessments_student($workshop) {
	global $CFG;
	// Return all assessments which have not been graded or just graded of student's submissions
	
	$cutofftime =time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_students u,
							{$CFG->prefix}workshop_assessments a
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
							  AND a.submissionid = s.id
							  AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
							  AND a.timecreated < $cutofftime
							  ORDER BY a.timecreated DESC"); 
	}


function workshop_get_ungraded_assessments_teacher($workshop) {
	global $CFG;
	// Return all assessments which have not been graded or just graded of teacher's submissions
	
	$cutofftime =time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT a.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_teachers u,
							{$CFG->prefix}workshop_assessments a
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND s.workshopid = $workshop->id
							  AND a.submissionid = s.id
							  AND (a.timegraded = 0 OR a.timegraded > $cutofftime)
							  AND a.timecreated < $cutofftime
							  ORDER BY a.timecreated DESC"); 
	}


function workshop_get_unmailed_assessments($cutofftime) {
	/// Return list of (ungraded) assessments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT a.*, g.course, g.name
                              FROM {$CFG->prefix}workshop_assessments a, {$CFG->prefix}workshop g
                             WHERE a.mailed = 0 
							   AND a.timegraded = 0
                               AND a.timecreated < $cutofftime 
                               AND g.id = a.workshopid");
}


function workshop_get_unmailed_graded_assessments($cutofftime) {
	/// Return list of graded assessments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT a.*, g.course, g.name
                              FROM {$CFG->prefix}workshop_assessments a, {$CFG->prefix}workshop g
                             WHERE a.mailed = 0 
							   AND a.timegraded < $cutofftime 
							   AND a.timegraded > 0
                               AND g.id = a.workshopid");
}


function workshop_get_user_assessments($workshop, $user) {
	// Return all the  user's assessments, newest first, oldest last
	return get_records_select("workshop_assessments", "workshopid = $workshop->id AND userid = $user->id", 
				"timecreated DESC");
}


function workshop_get_user_submissions($workshop, $user) {
	// return submission of user newest first, oldest last
    return get_records_select("workshop_submissions ",
             "workshopid = $workshop->id AND userid = $user->id", "timecreated DESC" );
}


function workshop_get_users_done($workshop) {
	global $CFG;
    return get_records_sql("SELECT u.* 
					FROM {$CFG->prefix}user u, {$CFG->prefix}user_students s, {$CFG->prefix}workshop_submissions a
                    WHERE s.course = '$workshop->course' AND s.user = u.id
                    AND u.id = a.user AND a.workshop = '$workshop->id'
                    ORDER BY a.timemodified DESC");
}


function workshop_list_all_submissions($workshop, $user) {
	// list the teacher sublmissions first
	global $CFG;
	
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_teacher_submissions($workshop)) {
		foreach ($submissions as $submission) {
			if ($submission->userid == $user->id) {
				$comment = get_string("ownwork", "workshop")."; ";
				}
			else {
				$comment = "";
				}
			// has user already assessed this submission
			if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
					AND userid = $user->id")) {
				$curtime = time();
				if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
					$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">"
						.get_string("view", "workshop")."</A>";
					// has teacher graded user's assessment?
					if ($assessment->timegraded) {
						if (($curtime - $assessment->timegraded) > $CFG->maxeditingtime) {
							$comment .= get_string("gradedbyteacher", "workshop", $course->teacher);
							}
						}
					}
				else { // there's still time left to edit...
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</A>";
					}
				}
			else { // user has not graded this submission
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</A>";
				}
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
			}
		print_table($table);
		}

	echo "<CENTER><P><B>".get_string("studentsubmissions", "workshop", $course->student)."</B></CENTER><BR>\n";
	unset($table);
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_student_submissions($workshop)) {
		foreach ($submissions as $submission) {
			if ($submission->userid == $user->id) {
				$comment = get_string("ownwork", "workshop")."; ";
				}
			else {
				$comment = "";
				}
			// has user already assessed this submission
			if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
					AND userid = $user->id")) {
				$curtime = time();
				if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
					$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">".
						get_string("view", "workshop")."</A>";
					// has teacher graded on user's assessment?
					if ($assessment->timegraded) {
						if (($curtime - $assessment->timegraded) > $CFG->maxeditingtime) {
							$comment .= get_string("gradedbyteacher", "workshop", $course->teacher)."; ";
							}
						}
					$otherassessments = workshop_get_assessments($submission);
					if (count($otherassessments) > 1) {
						$comment .= "<A HREF=\"assessments.php?action=viewallassessments&a=$workshop->id&sid=$submission->id\">".
						get_string("viewotherassessments", "workshop")."</A>";
						}
					}
				else { // there's still time left to edit...
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</A>";
					}
				}
			else { // user has not assessed this submission
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</A>";
				}
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
			}
		print_table($table);
		}
	}


function workshop_list_all_ungraded_assessments($workshop) {
	// lists all the assessments for comment by teacher
	global $CFG;
	
	$table->head = array (get_string("title", "workshop"), get_string("timeassessed", "workshop"), get_string("action", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;
	$timenow = time();
	
	if ($assessments = workshop_get_ungraded_assessments($workshop)) {
		foreach ($assessments as $assessment) {
			if (!isteacher($workshop->course, $assessment->userid)) {
				if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&a=$workshop->id&aid=$assessment->id\">".
						get_string("edit", "workshop")."</A>";
					}
				else {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&a=$workshop->id&aid=$assessment->id\">".
						get_string("gradeassessment", "workshop")."</A>";
					}
				$submission = get_record("workshop_submissions", "id", $assessment->submissionid);
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), 
					userdate($assessment->timecreated), $action);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}
	

function workshop_list_assessed_submissions($workshop, $user) {
	// list the submissions that have been assessed by this user
	global $CFG;
	
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title","workshop"), get_string("action","workshop"), get_string("comment","workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($assessments = workshop_get_student_assessments($workshop, $user)) {
		$timenow = time();
		foreach ($assessments as $assessment) {
			$comment = "";
			$submission = get_record("workshop_submissions", "id", $assessment->submissionid);
			if (($timenow - $assessment->timecreated) > $CFG->maxeditingtime) {
				$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">".
					get_string("view", "workshop")."</A>";
				// has teacher graded user's assessment?
				if ($assessment->timegraded) {
					if (($timenow - $assessment->timegraded) > $CFG->maxeditingtime) {
						$comment = get_string("gradedbyteacher", "workshop", $course->teacher);
						}
					}
				}
			else { // there's still time left to edit...
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("edit", "workshop")."</A>";
				}
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
			}
		}
	if (isset($table->data)) {
		print_table($table);
		}
	else {
		echo "<CENTER>".get_string("noassessmentsdone", "workshop")."</CENTER>\n";
		}
	}


function workshop_list_peer_assessments($workshop, $user) {
	global $CFG;
	
	if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	// get user's submissions
	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			// get the assessments
			if ($assessments = workshop_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					if (isstudent($workshop->course, $assessment->userid)) { // assessments by students only
						$timenow = time();
						if (($timenow - $assessment->timecreated) > $CFG->maxeditingtime) {
							$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">".
								get_string("view", "workshop")."</A>";
							// has teacher commented on user's assessment?
							if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
								$comment = get_string("gradedbyteacher", "workshop", $course->teacher);
								}
							else {
								$comment = userdate($assessment->timecreated);
								}
							$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
							}
						}
					}
				}
			}
		}
	if (isset($table->data)) {
		print_table($table);
		}
	else {
		echo "<CENTER>".get_string("noassessmentsdone", "workshop")."</CENTER>\n";
		}
	}



function workshop_list_student_submissions($workshop, $user) {
	// list available submissions for this user to assess, submissions with the least number 
	// of assessments are show first
	global $CFG;
	
	if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	// get the number of assessments this user has done
	$nassessed = workshop_count_assessments($workshop, "student", $user);

	// count the number of assessments for each student submission
	if ($submissions = workshop_get_student_submissions($workshop)) {
		foreach ($submissions as $submission) {
			$n = count_records("workshop_assessments", "submissionid", $submission->id);
			// OK to have zero
			$nassessments[$submission->id] = $n;
			}
			
		// put the submissions with the lowest number of assessments first
		asort($nassessments);
		reset($nassessments);
		$comment = "";
		foreach ($nassessments as $submissionid =>$n) {
			$submission = get_record("workshop_submissions", "id", $submissionid);
			if (($submission->userid != $user->id) or $workshop->includeself) {
				// add if user has NOT already assessed this submission
				if (!$assessment = get_record_select("workshop_assessments", "submissionid = $submissionid
						AND userid = $user->id")) {
					if ($nassessed < $workshop->nsassessments) { 
						$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
							get_string("assess", "workshop")."</A>";
						$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
						$nassessed++;
						}
					else {
						break;
						}
					}
				}
			}
		}
	if (isset($table->data)) {
		echo "<P><CENTER><B>".get_string("pleaseassessthesestudentsubmissions", "workshop", $course->student).
			"</B></CENTER><BR>\n";
		print_table($table);
		}
	else {
		echo "<P><CENTER><B>".get_string("nosubmissionsavailableforassessment", "workshop")."</B></CENTER><BR>\n";
		}
	}


function workshop_list_teacher_assessments($workshop, $user) {
	global $CFG;
	
	if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	// get user's submissions
	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			// get the assessments
			if ($assessments = workshop_get_assessments($submission)) {
				foreach ($assessments as $assessment) {
					if (isteacher($workshop->course, $assessment->userid)) { // assessments by teachers only
						$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">".
							get_string("view", "workshop")."</A>";
						// has teacher commented on teacher's assessment? shouldn't happen but leave test in
						if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
							$comment = get_string("gradedbyteacher", "workshop", $course->teacher);
							}
						else {
							$comment = userdate($assessment->timecreated);
							}
						$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
						}
					}
				}
			}
		}
	if (isset($table->data)) {
		print_table($table);
		}
	else {
		echo "<CENTER>".get_string("noassessmentsdone", "workshop")."</CENTER>\n";
		}
	}



function workshop_list_teacher_submissions($workshop, $user) {
	global $CFG;
	
	if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	// get the number of assessments this user has done
	$nassessed = count_records_select("workshop_assessments", "workshopid = $workshop->id
					AND userid = $user->id");

	if ($submissions = workshop_get_teacher_submissions($workshop)) {
		foreach ($submissions as $submission) {
			$comment = '';
			// has user already assessed this submission
			if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
					AND userid = $user->id AND timecreated > 0")) {
				$timenow = time();
				if (($timenow - $assessment->timecreated) > $CFG->maxeditingtime) {
					$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">".
						get_string("view", "workshop")."</A>";
					// has teacher graded user's assessment and is it cooked?
					if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
						// add teacher's comment to action string
						$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">".
							get_string("view", "workshop")." ".get_string("teacherscomment", "workshop")."</A>";
						// show user the teacher's assessment and if they failed allow them to resubmit assessment
						$percentage = number_format($assessment->gradinggrade*100/COMMENTSCALE, 0);
						$comment = get_string("assessmentgrade", "workshop", $percentage )."%" ;
						// is there a teacher's assessment, if so show a link to it
						$otherassessments = workshop_get_assessments($submission);
						foreach ($otherassessments as $otherassessment) {
							if (isteacher($workshop->course, $otherassessment->userid) ) {
								$comment .= " <A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$otherassessment->id\">".
									get_string("viewassessmentofteacher", "workshop", $course->teacher)."</A>";
								}
							}
						// has user failed?
						if ($assessment->gradinggrade < COMMENTSCALE*0.4) {
							$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
								get_string("edit", "workshop")."</A>";
							}
						}
					else { // teacher has not graded this assessment yet
						$comment = get_string("awaitinggradingbyteacher", "workshop", $course->teacher);
						}
					}
				else { // there's still time left to edit...
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</A>";
					}
				}
			else { // user has not graded this submission
				if ($nassessed < $workshop->ntassessments) { 
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("assess", "workshop")."</A>";
					}
				else {
					$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">".
						get_string("view", "workshop")."</A>";
					}
				}
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
			}
		print_table($table);
		}
	}


function workshop_list_unassessed_student_submissions($workshop, $user) {
	// list the student submissions not assessed by this user
	global $CFG;
	
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_student_submissions($workshop)) {
		foreach ($submissions as $submission) {
			$comment = "";
			// see if user already graded this assessment
			if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
					AND userid = $user->id")) {
				$timenow = time();
				if (($timenow - $assessment->timecreated < $CFG->maxeditingtime)) {
					// last chance salon
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</A>";
					$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
					}
				}
			else { // no assessment
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</A>";
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}


function workshop_list_unassessed_teacher_submissions($workshop, $user) {
	// list the teacher submissions not assessed by this user
	global $CFG;
	
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_teacher_submissions($workshop)) {
		foreach ($submissions as $submission) {
			$comment = "";
			// see if user already graded this assessment
			if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
					AND userid = $user->id")) {
				$timenow = time();
				if (($timenow - $assessment->timecreated < $CFG->maxeditingtime)) {
					// last chance salon
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</A>";
					$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
					}
				}
			else { // no assessment
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</A>";
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}


function workshop_list_ungraded_assessments($workshop, $stype) {
	global $CFG;
	
	// lists all the assessments of student submissions for grading by teacher
	$table->head = array (get_string("title", "workshop"), get_string("timeassessed", "workshop"), get_string("action", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;
	$timenow = time();
	
	switch ($stype) {
		case "student" :
			$assessments = workshop_get_ungraded_assessments_student($workshop);
			break;
		case "teacher" :
			$assessments = workshop_get_ungraded_assessments_teacher($workshop);
			break;
		}
	if ($assessments) {
		foreach ($assessments as $assessment) {
			if (!isteacher($workshop->course, $assessment->userid)) { // don't let teacher grade their own assessments
				if (($timenow - $assessment->timegraded) < $CFG->maxeditingtime) {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&a=$workshop->id&stype=$stype&aid=$assessment->id\">".
						get_string("edit", "workshop")."</A>";
					}
				else {
					$action = "<A HREF=\"assessments.php?action=gradeassessment&a=$workshop->id&stype=$stype&aid=$assessment->id\">".
						get_string("grade", "workshop")."</A>";
					}
				$submission = get_record("workshop_submissions", "id", $assessment->submissionid);
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), 
					userdate($assessment->timecreated), $action);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}
	

function workshop_list_user_submissions($workshop, $user) {
	$table->head = array (get_string("title", "workshop"),  get_string("submitted", "assignment"), get_string("assessments", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			$n = count_records("workshop_assessments", "submissionid", $submission->id);
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), userdate($submission->timecreated), $n);
			}
		print_table($table);
		}
	}


function workshop_print_assessment($workshop, $assessment, $allowchanges = FALSE) {
	global $CFG, $WORKSHOP_SCALES, $WORKSHOP_EWEIGHTS, $THEME;
	if (! $course = get_record("course", "id", $workshop->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
		error("Course Module ID was incorrect");
	}

	// only show the grade if grading strategy > 0 and the grade is positive
	if ($workshop->gradingstrategy and $assessment->grade >= 0) { 
		echo "<CENTER><B>".get_string("thegradeis", "workshop").": ".number_format($assessment->grade, 2)."% (".
			get_string("maximumgrade")." ".number_format($workshop->grade)."%)</B></CENTER><BR CLEAR=ALL>\n";
		}
		
	// now print the grading form with the teacher's comments if any
	// FORM is needed for Mozilla browsers, else radio bttons are not checked
		?>
	<form name="form" method="post" action="assessments.php">
	<INPUT TYPE="hidden" NAME="id" VALUE="<?PHP echo $cm->id ?>">
	<input type="hidden" name="aid" value="<?PHP echo $assessment->id ?>">
	<input type="hidden" name="action" value="updateassessment">
	<CENTER>
	<TABLE CELLPADDING=5 BORDER=1>
	<?PHP

	// get the assignment elements...
	if (!$elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC")) {
		print_string("noteonassignmentelements", "workshop");
		}
	else {
		foreach ($elementsraw as $element) {
			$elements[] = $element;   // to renumber index 0,1,2...
			}
		}

	// get any previous grades...
	if ($gradesraw = get_records_select("workshop_grades", "assessmentid = $assessment->id", "elementno")) {
		foreach ($gradesraw as $grade) {
			$grades[] = $grade;   // to renumber index 0,1,2...
			}
		}
				
	// determine what sort of grading
	switch ($workshop->gradingstrategy) {
		case 0:  // no grading
			// now print the form
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","workshop")." $iplus1:</B></P></TD>\n";
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
				echo "	<TD align=right><P><B>". get_string("element","workshop")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight],2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
				
				// get the appropriate scale
				$scalenumber=$elements[$i]->scale;
				$SCALE = (object)$WORKSHOP_SCALES[$scalenumber];
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
			$negativecount = 0;
			for ($i=0; $i < count($elements) - 1; $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","workshop")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight],2)."</FONT>\n";
				echo "</TD></TR>\n";
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("grade"). ":</B></P></TD>\n";
				echo "	<TD valign=\"top\">\n";
					
				// get the appropriate scale - yes/no scale (0)
				$SCALE = (object) $WORKSHOP_SCALES[0];
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
					$negativecount++;
					}
				}
			// print the number of negative elements
			// echo "<TR><TD>".get_string("numberofnegativeitems", "workshop")."</TD><TD>$negativecount</TD></TR>\n";
			// echo "<TR valign=top>\n";
			// echo "	<TD COLSPAN=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "</TABLE></CENTER>\n";
			// now print the grade table
			echo "<P><CENTER><B>".get_string("gradetable","workshop")."</B></CENTER>\n";
			echo "<CENTER><TABLE cellpadding=5 border=1><TR><TD ALIGN=\"CENTER\">".
				get_string("numberofnegativeresponses", "workshop");
			echo "</TD><TD>". get_string("suggestedgrade", "workshop")."</TD></TR>\n";
			for ($j = 100; $j >= 0; $j--) {
				$numbers[$j] = $j;
				}
			for ($i=0; $i<=$workshop->nelements; $i++) {
				if ($i == $negativecount) {
					echo "<TR><TD ALIGN=\"CENTER\"><IMG SRC=\"../../pix/t/right.gif\"> $i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				else {
					echo "<TR><TD ALIGN=\"CENTER\">$i</TD><TD ALIGN=\"CENTER\">{$elements[$i]->maxscore}</TD></TR>\n";
					}
				}
			echo "</TABLE></CENTER>\n";
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD>".get_string("adjustment", "workshop")."</TD><TD>\n";
			unset($numbers);
			for ($j = 20; $j >= -20; $j--) {
				$numbers[$j] = $j;
				}
			if (isset($grades[$workshop->nelements]->grade)) {
				choose_from_menu($numbers, "grade[$workshop->nelements]", $grades[$workshop->nelements]->grade, "");
				}
			else {
				choose_from_menu($numbers, "grade[$workshop->nelements]", 0, "");
				}
			echo "</TD></TR>\n";
			break;
			
		case 3: // criteria grading
			echo "<TR valign=top>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","workshop")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("select", "workshop")."</B></TD>\n";
			echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>".get_string("suggestedgrade", "workshop")."</B></TD>\n";
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
			echo "<P><CENTER><TABLE cellpadding=5 border=1><TR><TD>".get_string("adjustment", "workshop")."</TD><TD>\n";
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
		} // end of outer switch
	
	// now get the general comment (present in all types)
	echo "<TR valign=top>\n";
	echo "	<TD align=right><P><B>". get_string("generalcomment", "workshop").":</B></P></TD>\n";
	echo "	<TD>\n";
	if ($allowchanges) {
		echo "		<textarea name=\"generalcomment\" rows=5 cols=75 wrap=\"virtual\">\n";
		if (isset($assessment->generalcomment)) {
			echo $assessment->generalcomment;
			}
		echo "</textarea>\n";
		}
	else {
		if (isset($assessment->generalcomment)) {
			echo text_to_html($assessment->generalcomment);
			}
		}
	echo "&nbsp;</TD>\n";
	echo "</TR>\n";
	echo "<TR valign=top>\n";
	echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
	echo "</TR>\n";
	
	$timenow = time();
	// now show the teacher's comment if available...
	if ($assessment->timegraded and (($timenow - $assessment->timegraded) > $CFG->maxeditingtime)) {
		echo "<TR valign=top>\n";
		echo "	<TD align=right><P><B>". get_string("teacherscomment", "workshop").":</B></P></TD>\n";
		echo "	<TD>\n";
		echo text_to_html($assessment->teachercomment);
		echo "&nbsp;</TD>\n";
		echo "</TR>\n";
		echo "<TR valign=top>\n";
		echo "	<TD align=right><P><B>". get_string("teachersgrade", "workshop").":</B></P></TD>\n";
		echo "	<TD>\n";
		echo number_format($assessment->gradinggrade*100/COMMENTSCALE,0)."%";
		echo "&nbsp;</TD>\n";
		echo "</TR>\n";
		echo "<TR valign=top>\n";
		echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\">&nbsp;</TD>\n";
		echo "</TR>\n";
		}
		
	// ...and close the table, show submit button if needed and close the form
	echo "</TABLE>\n";
	if ($allowchanges) {
		echo "<INPUT TYPE=submit VALUE=\"".get_string("savemyassessment", "workshop")."\">\n";
		}
	echo "</CENTER>";
	echo "</FORM>\n";
	}


function workshop_print_difference($time) {
    if ($time < 0) {
        $timetext = get_string("late", "assignment", format_time($time));
        return " (<FONT COLOR=RED>$timetext</FONT>)";
    } else {
        $timetext = get_string("early", "assignment", format_time($time));
        return " ($timetext)";
    }
}

function workshop_print_feedback($course, $submission) {
    global $CFG, $THEME, $RATING;

    if (! $teacher = get_record("user", "id", $submission->teacher)) {
        error("Weird workshop error");
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


function workshop_print_submission_assessments($workshop, $submission, $type) {
	// Returns the teacher or peer grade and a hyperlinked list of grades for this submission

	if ($assessments = workshop_get_assessments($submission)) {
		switch ($type) {
			case "teacher" : 
				$str = "$submission->teachergrade  (";
				foreach ($assessments as $assessment) {
					if (isteacher($workshop->course, $assessment->userid)) {
						$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">";
						$str .= number_format($assessment->grade, 1)."</A> ";
						}
					}
				break;
			case "student" : 
				$str = "$submission->peergrade  (";
				foreach ($assessments as $assessment) {
					if (isstudent($workshop->course, $assessment->userid)) {
						$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">";
						$str .= number_format($assessment->grade, 1)."</A> ";
						}
					}
				break;
			}
		$str .= ")";
		}
	else {
		$str ="0";
		}
    return $str;
}


function workshop_print_submission_title($workshop, $submission) {
// Arguments are objects

    global $CFG;
	
	if (!$submission->timecreated) { // a "no submission"
		return $submission->title;
		}

    $filearea = workshop_file_area_name($workshop, $submission);
    if ($basedir = workshop_file_area($workshop, $submission)) {
        if (list($file) = get_directory_list($basedir)) {
            $icon = mimeinfo("icon", $file);
            if ($CFG->slasharguments) {
                $ffurl = "file.php/$filearea/$file";
            } else {
                $ffurl = "file.php?file=/$filearea/$file";
            }
            return "<IMG SRC=\"$CFG->wwwroot/files/pix/$icon\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"File\">".
                "&nbsp;<A TARGET=\"uploadedfile\" HREF=\"$CFG->wwwroot/$ffurl\">$submission->title</A>";
        }
    }
}


function workshop_print_user_assessments($workshop, $user) {
	// Returns the number of assessments and a hyperlinked list of grading grades for the assessments made by this user

	if ($assessments = workshop_get_user_assessments($workshop, $user)) {
		$n = count($assessments);
		$str = "$n  (";
		foreach ($assessments as $assessment) {
			if ($assessment->timegraded) {
				$gradingscaled = intval($assessment->gradinggrade * $workshop->grade / COMMENTSCALE);
				$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">";
				$str .= "$gradingscaled</A> ";
				}
			else {
				$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">";
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


function workshop_print_time_to_deadline($time) {
    if ($time < 0) {
        $timetext = get_string("afterdeadline", "workshop", format_time($time));
        return " (<FONT COLOR=RED>$timetext</FONT>)";
    } else {
        $timetext = get_string("beforedeadline", "workshop", format_time($time));
        return " ($timetext)";
    }
}


function workshop_print_upload_form($workshop) {
// Arguments are objects, needs title coming in

    echo "<DIV ALIGN=CENTER>";
    echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"POST\" ACTION=upload.php>";
    echo " <INPUT TYPE=hidden NAME=MAX_FILE_SIZE value=\"$workshop->maxbytes\">";
    echo " <INPUT TYPE=hidden NAME=a VALUE=\"$workshop->id\">";
	echo get_string("title", "workshop")." <INPUT NAME=\"title\" TYPE=\"text\" SIZE=\"60\" MAXSIZE=\"100\"><BR><BR>\n";
    echo " <INPUT NAME=\"newfile\" TYPE=\"file\" size=\"50\">";
    echo " <INPUT TYPE=submit NAME=save VALUE=\"".get_string("uploadthisfile")."\">";
    echo "</FORM>";
    echo "</DIV>";
}

function workshop_test_user_assessments($workshop, $user) {
	// see if user has passed the required number of assessments of teachers submissions
	global $CFG;
	
	$result = TRUE;
	$n = 0;
	$timenow =time();
	if ($workshop->ntassessments) { // they have to pass some!
		if ($submissions = workshop_get_teacher_submissions($workshop)) {
			foreach ($submissions as $submission) {
				if ($assessment = workshop_get_submission_assessment($submission, $user)) {
					if (($assessment->gradinggrade >= COMMENTSCALE*0.4) and 
							(($timenow - $assessment->timegraded) > $CFG->maxeditingtime)) {
						$n++;
						}
					}
				}
			}
		if ($n < min($workshop->ntassessments, workshop_count_teacher_submissions($workshop))) {
			$result = FALSE; 
			}
		}
	return $result;
	}

?>
