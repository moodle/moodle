<?PHP  // $Id: lib.php,v 1.1 22 Aug 2003

include_once("$CFG->dirroot/files/mimetypes.php");

error_reporting(15);

/*** Constants **********************************/

$WORKSHOP_TYPE = array (0 => get_string("notgraded", "workshop"),
                          1 => get_string("accumulative", "workshop"),
                          2 => get_string("errorbanded", "workshop"),
                          3 => get_string("criterion", "workshop"),
						  4 => get_string("rubric", "workshop") );

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

$WORKSHOP_FWEIGHTS = array(  0 => 0, 1 => 0.1, 2 => 0.25, 3 => 0.5, 4 => 0.75, 5 => 1.0,  6 => 1.5, 
											7 => 2.0, 8 => 3.0, 9 => 5.0, 10 => 7.5, 11=> 10.0); 

if (!defined("COMMENTSCALE")) {
	define("COMMENTSCALE", 20);
	}

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

    if (! delete_records("workshop_comments", "workshopid", "$workshop->id")) {
        $result = false;
    }

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
    if ($submission = workshop_get_student_submission($workshop, $user)) {
		$result->info = $submission->title;
        if ($submission->finalgrade) {
            $result->info .= ", ".get_string("grade").": $submission->finalgrade";
        }
        $result->time = $submission->timecreated;
        return $result;
    }
    return NULL;
}

function workshop_user_complete($course, $user, $mod, $workshop) {
    if ($submission = workshop_get_student_submission($workshop, $user)) {
        if ($basedir = workshop_file_area($workshop, $user)) {
            if ($files = get_directory_list($basedir)) {
                $countfiles = count($files)." ".get_string("submissions", "workshop");
                foreach ($files as $file) {
                    $countfiles .= "; $file";
                }
            }
        }

        print_simple_box_start();

        //workshop_print_user_files($workshop, $user);

        echo "Submission was made but no way to show you yet.";   //xxx

        //workshop_print_feedback($course, $submission);

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
			if ($sendto->mailformat == 1) {  // HTML
				$posthtml = "<P><FONT FACE=sans-serif>".
			  "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
			  "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
			  "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">$workshop->name</A></FONT></P>";
			  $posthtml .= "<HR><FONT FACE=sans-serif>";
			  $posthtml .= "<P>$msg</P>";
			  $posthtml .= "<P>You can see it <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">";
			  $posthtml .= "in your workshop assignment</A>.</P></FONT><HR>";
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
		
	// look for new comments
	if ($comments = workshop_get_unmailed_comments($cutofftime)) {
        $timenow = time();

        foreach ($comments as $comment) {

			echo "Processing workshop comment $comment->id\n";
			if (! $assessment = get_record("workshop_assessments", "id", "$comment->assessmentid")) {
				echo "Could not find assessment $comment->assessmentid\n";
				continue;
			}
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
			if (! $course = get_record("course", "id", "$comment->course")) {
				echo "Could not find course $comment->course\n";
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
	
			// see if the submission owner needs to be told
			if ($comment->userid != $submission->userid) {
				$USER->lang = $submissionowner->lang;
				$sendto = $submissionowner;
				$msg = "A comment has been added to the assignment \"$submission->title\".\n".
					"The new comment can be seen in ".
					"the workshop assignment '$workshop->name'\n\n";
	
				$postsubject = "$course->shortname: $strworkshops: $workshop->name";
				$posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
				$posttext .= "---------------------------------------------------------------------\n";
				$posttext .= $msg;
				$posttext .= "You can see it in your workshop assignment:\n";
				$posttext .= "   $CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\n";
				$posttext .= "---------------------------------------------------------------------\n";
				if ($sendto->mailformat == 1) {  // HTML
					$posthtml = "<P><FONT FACE=sans-serif>".
					"<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
					"<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
					"<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">$workshop->name</A></FONT></P>";
					$posthtml .= "<HR><FONT FACE=sans-serif>";
					$posthtml .= "<P>$msg</P>";
					$posthtml .= "<P>You can see it <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">";
					$posthtml .= "in your workshop assignment</A>.</P></FONT><HR>";
					} 
				else {
					$posthtml = "";
					}
	
				if (!$teacher = get_teacher($course->id)) {
					echo "Error: can not find teacher for course $course->id!\n";
					}
					
				if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
					echo "Error: workshop cron: Could not send out mail for id $submission->id to user $sendto->id ($sendto->email)\n";
					}
				if (! set_field("workshop_comments", "mailed", "1", "id", "$comment->id")) {
					echo "Could not update the mailed field for comment id $comment->id\n";
					}
				}
			// see if the assessor needs to to told
			if ($comment->userid != $assessment->userid) {
				$USER->lang = $assessmentowner->lang;
				$sendto = $assessmentowner;
				$msg = "A comment has been added to the assignment \"$submission->title\".\n".
					"The new comment can be seen in ".
					"the workshop assignment '$workshop->name'\n\n";
	
				$postsubject = "$course->shortname: $strworkshops: $workshop->name";
				$posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
				$posttext .= "---------------------------------------------------------------------\n";
				$posttext .= $msg;
				$posttext .= "You can see it in your workshop assignment:\n";
				$posttext .= "   $CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\n";
				$posttext .= "---------------------------------------------------------------------\n";
				if ($sendto->mailformat == 1) {  // HTML
					$posthtml = "<P><FONT FACE=sans-serif>".
					"<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
					"<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
					"<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">$workshop->name</A></FONT></P>";
					$posthtml .= "<HR><FONT FACE=sans-serif>";
					$posthtml .= "<P>$msg</P>";
					$posthtml .= "<P>You can see it <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">";
					$posthtml .= "in your workshop assignment</A>.</P></FONT><HR>";
					} 
				else {
					$posthtml = "";
					}
	
				if (!$teacher = get_teacher($course->id)) {
					echo "Error: can not find teacher for course $course->id!\n";
					}
					
				if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
					echo "Error: workshop cron: Could not send out mail for id $submission->id to user $sendto->id ($sendto->email)\n";
					}
				if (! set_field("workshop_comments", "mailed", "1", "id", "$comment->id")) {
					echo "Could not update the mailed field for comment id $comment->id\n";
					}
				}
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

			$postsubject = "$course->shortname: $strworkshops: $workshop->name";
            $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            $posttext .= "You can see it in your workshop assignment:\n";
            $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
                $posthtml = "<P><FONT FACE=sans-serif>".
              "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
              "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">$workshop->name</A></FONT></P>";
              $posthtml .= "<HR><FONT FACE=sans-serif>";
              $posthtml .= "<P>$msg</P>";
              $posthtml .= "<P>You can see it <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?a=$workshop->id\">";
              $posthtml .= "in your workshop assignment</A>.</P></FONT><HR>";
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


function workshop_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    $content = false;
    $submissions = NULL;

	// only show submissions and assessments to teachers
	if ($isteacher) {
		if ($logs = get_records_select("log", "time > '$timestart' AND ".
											   "course = '$course->id' AND ".
											   "module = 'workshop' AND ".
											   "action = 'submit' ", "time ASC")) {
	 
			foreach ($logs as $log) {
				//Create a temp valid module structure (course,id)
				$tempmod->course = $log->course;
				$tempmod->id = $log->info;
				//Obtain the visible property from the instance
				$modvisible = instance_is_visible($log->module,$tempmod);
		   
				//Only if the mod is visible
				if ($modvisible) {
					$submissions[$log->info] = workshop_log_info($log);
					$submissions[$log->info]->time = $log->time;
					$submissions[$log->info]->url  = $log->url;
				}
			}
		
			if ($submissions) {
				$strftimerecent = get_string("strftimerecent");
				$content = true;
				print_headline(get_string("newsubmissions", "workshop").":");
				foreach ($submissions as $submission) {
					$date = userdate($submission->time, $strftimerecent);
					echo "<p><font size=1>$date - $submission->firstname $submission->lastname<br />";
					echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$submission->url\">";
					echo "$submission->name";
					echo "</a>\"</font></p>";
				}
			}
		} 
	
	
		$assessments = NULL;
	
		if ($logs = get_records_select("log", "time > '$timestart' AND ".
											   "course = '$course->id' AND ".
											   "module = 'workshop' AND ".
											   "action = 'assess' ", "time ASC")) {
	 
			foreach ($logs as $log) {
				//Create a temp valid module structure (course,id)
				$tempmod->course = $log->course;
				$tempmod->id = $log->info;
				//Obtain the visible property from the instance
				$modvisible = instance_is_visible($log->module,$tempmod);
		   
				//Only if the mod is visible
				if ($modvisible) {
					$assessments[$log->info] = workshop_log_info($log);
					$assessments[$log->info]->time = $log->time;
					$assessments[$log->info]->url  = $log->url;
				}
			}
		
			if ($assessments) {
				$strftimerecent = get_string("strftimerecent");
				$content = true;
				print_headline(get_string("newassessments", "workshop").":");
				foreach ($assessments as $assessment) {
					$date = userdate($assessment->time, $strftimerecent);
					echo "<p><font size=1>$date - $assessment->firstname $assessment->lastname<br />";
					echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$assessment->url\">";
					echo "$assessment->name";
					echo "</a>\"</font></p>";
				}
			}
		} 
	}
	
    $gradings = NULL;

    if ($logs = get_records_select("log", "time > '$timestart' AND ".
                                           "course = '$course->id' AND ".
                                           "module = 'workshop' AND ".
                                           "action = 'grade' ", "time ASC")) {
 
		foreach ($logs as $log) {
			//Create a temp valid module structure (course,id)
			$tempmod->course = $log->course;
			$tempmod->id = $log->info;
			//Obtain the visible property from the instance
			$modvisible = instance_is_visible($log->module,$tempmod);
	   
			//Only if the mod is visible
			if ($modvisible) {
				$gradings[$log->info] = workshop_log_info($log);
				$gradings[$log->info]->time = $log->time;
				$gradings[$log->info]->url  = $log->url;
			}
		}
	
		if ($gradings) {
			$strftimerecent = get_string("strftimerecent");
			$content = true;
			print_headline(get_string("newgradings", "workshop").":");
			foreach ($gradings as $grading) {
				$date = userdate($grading->time, $strftimerecent);
				echo "<p><font size=1>$date - $grading->firstname $grading->lastname<br />";
				echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$grading->url\">";
				echo "$grading->name";
				echo "</a>\"</font></p>";
			}
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


function workshop_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT a.name, u.firstname, u.lastname
                             FROM {$CFG->prefix}workshop a, 
                                  {$CFG->prefix}user u
                            WHERE a.id = '$log->info' 
                              AND u.id = '$log->userid'");
}


//////////////////////////////////////////////////////////////////////////////////////

/*** Functions for the workshop module ******

function workshop_count_all_submissions_for_assessment($workshop, $user) {
function workshop_count_assessments($submission) {
function workshop_count_comments($assessment) {
function workshop_count_peer_assessments($workshop, $user) {
function workshop_count_self_assessments($workshop, $user) {
function workshop_count_student_submissions($workshop) {
function workshop_count_student_submissions_for_assessment($workshop, $user) {
function workshop_count_teacher_assessments($workshop, $user) {
function workshop_count_teacher_submissions($workshop) {
function workshop_count_teacher_submissions_for_assessment($workshop, $user) {
function workshop_count_ungraded_assessments_student($workshop) {
function workshop_count_ungraded_assessments_teacher($workshop) {
function workshop_count_user_assessments($worshop, $user, $type = "all") { $type is all, student or teacher
function workshop_count_user_submissions($workshop, $user) {

function workshop_delete_submitted_files($workshop, $submission) {
function workshop_delete_user_files($workshop, $user, $exception) {

function workshop_file_area($workshop, $submission) {
function workshop_file_area_name($workshop, $submission) {

function workshop_get_assessments($submission) {
function workshop_get_comments($assessment) {
function workshop_get_student_assessments($workshop, $user) {
function workshop_get_student_submission($workshop, $user) {
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
function workshop_list_submissions_for_admin($workshop, $order) {
function workshop_list_teacher_assessments($workshop, $user) {
function workshop_list_teacher_submissions($workshop) {
function workshop_list_unassessed_student_submissions($workshop, $user) {
function workshop_list_unassessed_teacher_submissions($workshop, $user) {
function workshop_list_ungraded_assessments($workshop, $stype) {
function workshop_list_user_submissions($workshop, $user) {


function workshop_print_assessment($workshop, $assessment, $allowchanges, $showcommentlinks)
function workshop_print_assessments_by_user_for_admin($workshop, $user) {
function workshop_print_assessments_for_admin($workshop, $submission) {
function workshop_print_difference($time) {
function workshop_print_feedback($course, $submission) {
function workshop_print_league_table($workshop) {
function workshop_print_submission_assessments($workshop, $submission, $type) {
function workshop_print_submission_title($workshop, $user) {
function workshop_print_tabbed_table($table) {
function workshop_print_time_to_deadline($time) {
function workshop_print_upload_form($workshop) {
function workshop_print_user_assessments($workshop, $user) {

function workshop_test_user_assessments($workshop, $user) {
***************************************/


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


function workshop_count_assessments($submission) {
	// Return the (real) assessments for this submission, 
	$timenow = time();
   return count_records_select("workshop_assessments", "submissionid = $submission->id AND timecreated < $timenow");
}

function workshop_count_comments($assessment) {
	// Return the number of comments for this assessment provided they are newer than the assessment, 
   return count_records_select("workshop_comments", "(assessmentid = $assessment->id) AND 
		timecreated > $assessment->timecreated");
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


function workshop_count_self_assessments($workshop, $user) {
	// returns the number of assessments made by user on their own submissions
	
	$n = 0;
	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			if ($assessment = get_record_select("workshop_assessments", "userid = $user->id AND 
					submissionid = $submission->id")) {
				$n++;
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


function workshop_count_user_assessments($workshop, $user, $stype = "all") {
	// returns the number of assessments allocated/made by a user, all of them, or just those for the student or teacher submissions
	// the student's self assessments are included in the count
	// the maxeditingtime is NOT taken into account here also allocated assessments which have not yet
	// been done are counted as well
	
	$n = 0;
	if ($assessments = workshop_get_user_assessments($workshop, $user)) {
		 foreach ($assessments as $assessment) {
			switch ($stype) {
				case "all" :
					$n++;
					break;
				case "student" :
					 $submission = get_record("workshop_submissions", "id", $assessment->submissionid);
					if (isstudent($workshop->course, $submission->userid)) {
						$n++;
						}
					break;
				case "teacher" :
					 $submission = get_record("workshop_submissions", "id", $assessment->submissionid);
					if (isteacher($workshop->course, $submission->userid)) {
						$n++;
						}
					break;
				}
			}
		}
	return $n;
	}


function workshop_count_user_assessments_done($workshop, $user) {
	// returns the number of assessments actually done by a user
	// the student's self assessments are included in the count
	// the maxeditingtime is NOT taken into account here 
	
	$n = 0;
	$timenow = time();
	if ($assessments = workshop_get_user_assessments($workshop, $user)) {
		 foreach ($assessments as $assessment) {
			if ($assessment->timecreated < $timenow) {
				$n++;
				}
			}
		}
	return $n;
	}


function workshop_count_user_submissions($workshop, $user) {
	// returns the number of submissions make by this user
	return count_records("workshop_submissions", "workshopid", $workshop->id, "userid", $user->id);
	}


function workshop_delete_submitted_files($workshop, $submission) {
// Deletes the files in the workshop area for this submission

	if ($basedir = workshop_file_area($workshop, $submission)) {
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
	// Return all assessments for this submission provided they are after the editing time, ordered oldest first, newest last
	global $CFG;

	$timenow = time();
    return get_records_select("workshop_assessments", "(submissionid = $submission->id) AND 
		(timecreated < $timenow - $CFG->maxeditingtime)", "timecreated DESC");
}


function workshop_get_comments($assessment) {
	// Return all comments for this assessment provided they are newer than the assessment, 
	// and ordered oldest first, newest last
   return get_records_select("workshop_comments", "(assessmentid = $assessment->id) AND 
		timecreated > $assessment->timecreated",
		"timecreated DESC");
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


function workshop_get_student_submission($workshop, $user) {
// Return a submission for a particular user
	global $CFG;

    $submission = get_record("workshop_submissions", "workshopid", $workshop->id, "userid", $user->id);
    if (!empty($submission->timecreated)) {
        return $submission;
    }
    return NULL;
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


function workshop_get_student_submissions($workshop, $order = "title") {
// Return all  ENROLLED student submissions
	global $CFG;
	
	if ($order == "title") {
		$order = "s.title";
		}
	if ($order == "name") {
		$order = "a.firstname, a.lastname";
		}
	if ($order == "grade") {
		$order = "$workshop->teacherweight * s.teachergrade + $workshop->peerweight * s.peergrade DESC";
		}
	return get_records_sql("SELECT s.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user_students u,
							{$CFG->prefix}user a 
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
							  AND a.id = u.userid
                              AND s.workshopid = $workshop->id
							  AND s.timecreated > 0
							  ORDER BY $order");
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
							  ORDER BY a.timecreated ASC"); 
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
							  ORDER BY a.timecreated ASC"); 
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


function workshop_get_unmailed_comments($cutofftime) {
	/// Return list of comments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT c.*, g.course, g.name
                              FROM {$CFG->prefix}workshop_comments c, {$CFG->prefix}workshop g
                             WHERE c.mailed = 0 
						       AND c.timecreated < $cutofftime 
                               AND g.id = c.workshopid");
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
	$table->align = array ("left", "left", "left");
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
				$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id&".
					"allowcomments=$workshop->agreeassessments\">".
					get_string("view", "workshop")."</A>";
				if ($workshop->agreeassessments and !$assessment->timeagreed) {
					$action .= " | <A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("reassess", "workshop")."</A>";
					}
				}
			else { // there's still time left to edit...
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("edit", "workshop")."</A> | <A HREF=\"assessments.php?action=userconfirmdelete&a=$workshop->id&aid=$assessment->id\">".
					get_string("delete", "workshop")."</A>";
				}
			$comment = get_string("assessedon", "workshop", userdate($assessment->timecreated));
			if ($submission->userid == $user->id) { // self assessment?
				$comment .= "; ".get_string("ownwork", "workshop"); // just in case they don't know!
				}
			// has teacher commented on user's assessment?
			if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
				$comment .= "; ".get_string("gradedbyteacher", "workshop", $course->teacher);
				}
			// if peer agrrements show whether agreement has been reached
			if ($workshop->agreeassessments) {
				if ($assessment->timeagreed) {
					$comment .= "; ".get_string("assessmentwasagreedon", "workshop", userdate($assessment->timeagreed));
					}
				else {
					$comment .= "; ".get_string("assessmentnotyetagreed", "workshop");
					}
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
					// assessments by students only and exclude any self assessments
					if (isstudent($workshop->course, $assessment->userid) and ($assessment->userid != $user->id)) { 
						$timenow = time();
						if (($timenow - $assessment->timecreated) > $CFG->maxeditingtime) {
							$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id&".
								"allowcomments=$workshop->agreeassessments\">".
								get_string("view", "workshop")."</A>";
							$comment = get_string("assessedon", "workshop", userdate($assessment->timecreated));
							// has teacher commented on user's assessment?
							if ($assessment->timegraded and ($timenow - $assessment->timegraded > $CFG->maxeditingtime)) {
								$comment .= "; ".get_string("gradedbyteacher", "workshop", $course->teacher);
								}
							// if peer agrrements show whether agreement has been reached
							if ($workshop->agreeassessments) {
								if ($assessment->timeagreed) {
									$comment .= "; ".get_string("assessmentwasagreedon", "workshop", userdate($assessment->timeagreed));
									}
								else {
									$comment .= "; ".get_string("assessmentnotyetagreed", "workshop");
									}
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



function workshop_list_self_assessments($workshop, $user) {
	// list  user's submissions for the user to assess
	global $CFG;
	
	if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	$table->head = array (get_string("title", "workshop"), get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	// get the user's submissions 
	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			$comment = "";
			if (!$assessment = get_record_select("workshop_assessments", "submissionid = $submission->id AND
					userid = $user->id")) {
				if ($submission->userid == $user->id) { // this will always be true
					$comment = get_string("ownwork", "workshop"); // just in case they don't know!
					}
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</A>";
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
				}
			}
		}
	if (isset($table->data)) {
		echo "<P><CENTER><B>".get_string("pleaseassessyoursubmissions", "workshop", $course->student).
			"</B></CENTER><BR>\n";
		print_table($table);
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

	// get the number of assessments this user has done on student submission, deduct self assessments
	$nassessed = workshop_count_user_assessments($workshop, $user, "student") - 
		workshop_count_self_assessments($workshop, $user);

	// count the number of assessments for each student submission
	if ($submissions = workshop_get_student_submissions($workshop)) {
		srand ((float)microtime()*1000000); // initialise random number generator
		foreach ($submissions as $submission) {
			$n = count_records("workshop_assessments", "submissionid", $submission->id);
			// ...OK to have zero, we add a small random number to randomise things
			$nassessments[$submission->id] = $n + rand(0, 99) / 100;
			}
			
		// put the submissions with the lowest number of assessments first
		asort($nassessments);
		reset($nassessments);
		$nsassessments = $workshop->nsassessments;
		foreach ($nassessments as $submissionid =>$n) {
			$comment = "";
			$submission = get_record("workshop_submissions", "id", $submissionid);
			if ($submission->userid != $user->id) {
				// add if user has NOT already assessed this submission
				if (!$assessment = get_record_select("workshop_assessments", "submissionid = $submissionid
						AND userid = $user->id")) {
					if ($submission->userid == $user->id) {
						$comment = get_string("ownwork", "workshop");
						}
					if ($nassessed < $nsassessments) { 
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


function workshop_list_submissions_for_admin($workshop, $order) {
	// list the teacher sublmissions first
	global $CFG, $USER;
	
    if (! $course = get_record("course", "id", $workshop->course)) {
        error("Course is misconfigured");
        }
	
	$table->head = array (get_string("title", "workshop"), get_string("submittedby", "workshop"), get_string("action", "workshop"));
	$table->align = array ("left", "left", "left");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_teacher_submissions($workshop)) {
		foreach ($submissions as $submission) {
			$action = "<a href=\"submissions.php?action=adminamendtitle&a=$workshop->id&sid=$submission->id\">".
				get_string("amendtitle", "workshop")."</a>";
			// has user already assessed this submission
			if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
					AND userid = $USER->id")) {
				$curtime = time();
				if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
					$action .= " | <a href=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">"
						.get_string("reassess", "workshop")."</a>";
					}
				else { // there's still time left to edit...
					$action .= " | <a href=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</a>";
					}
				}
			else { // user has not graded this submission
				$action .= " | <a href=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</a>";
				}
			if ($assessments = workshop_get_assessments($submission)) {
				$action .= " | <a href=\"assessments.php?action=adminlist&a=$workshop->id&sid=$submission->id\">".
					get_string("listassessments", "workshop")."</a>";
				}
			$action .= " | <a href=\"submissions.php?action=adminconfirmdelete&a=$workshop->id&sid=$submission->id\">".
					get_string("delete", "workshop")."</a>";
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), $course->teacher, $action);
			}
		print_table($table);
		}

	// list student assessments
	// Get all the students...
	if ($users = get_course_students($course->id, "u.firstname, u.lastname")) {
		$timenow = time();
		print_heading(get_string("studentassessments", "workshop", $course->student));
		unset($table);
		$table->head = array(get_string("name"), get_string("title", "workshop"), get_string("action", "workshop"));
		$table->align = array ("left", "left", "left");
		$table->size = array ("*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;
		foreach ($users as $user) {
			if ($assessments = workshop_get_user_assessments($workshop, $user)) {
				$title ='';
				foreach ($assessments as $assessment) {
					if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
						error("Workshop_list_submissions_for_admin: Submission record not found!");
						}
					$title .= $submission->title;
					// test for allocated assesments which have not been done
					if ($assessment->timecreated < $timenow) {
						$title .= " {".number_format($assessment->grade, 0)."%";
						}
					else { // assessment record created but user has not yet assessed this submission
						$title .= " {-";
						}
					if ($assessment->timegraded) {
						$title .= "/".number_format($assessment->gradinggrade * 100 / COMMENTSCALE, 0)."%";
						}
					$title .= "} ";
					if ($realassessments = workshop_count_user_assessments_done($workshop, $user)) {
						$action = "<a href=\"assessments.php?action=adminlistbystudent&a=$workshop->id&userid=$user->id\">".
							get_string("liststudentsassessments", "workshop")." ($realassessments)</a>";
						}
					else {
						$action ="";
						}
					}
				$table->data[] = array("$user->firstname $user->lastname", $title, $action);
				}
			}
		print_table($table);
		}

	// now the sudent submissions
	echo "<CENTER><P><B>".get_string("studentsubmissions", "workshop", $course->student)."</B></CENTER><BR>\n";
	unset($table);
	switch ($order) {
		case "title" :
			$table->head = array("<a href=\"submissions.php?action=adminlist&a=$workshop->id&order=name\">".
				 get_string("submittedby", "workshop")."</a>", get_string("title", "workshop"), get_string("action", "workshop"));
			break;
		case "name" :
			$table->head = array (get_string("submittedby", "workshop"), 
				"<a href=\"submissions.php?action=adminlist&a=$workshop->id&order=title\">".
				get_string("title", "workshop")."</a>", get_string("action", "workshop"));
			break;
		}
	$table->align = array ("left", "left", "left");
	$table->size = array ("*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_student_submissions($workshop, $order)) {
		foreach ($submissions as $submission) {
			if (!$user = get_record("user", "id", $submission->userid)) {
				error("workshop_list_submissions_for_admin: failure to get user record");
				}
			$action = "<a href=\"submissions.php?action=adminamendtitle&a=$workshop->id&sid=$submission->id\">".
				get_string("amendtitle", "workshop")."</a>";
			// has teacher already assessed this submission
			if ($assessment = get_record_select("workshop_assessments", "submissionid = $submission->id
					AND userid = $USER->id")) {
				$curtime = time();
				if (($curtime - $assessment->timecreated) > $CFG->maxeditingtime) {
					$action .= " | <a href=\"assessments.php?action=assesssubmission&a=$workshop->id&aid=$assessment->id\">".
						get_string("reassess", "workshop")."</a>";
					}
				else { // there's still time left to edit...
					$action .= " | <a href=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</a>";
					}
				}
			else { // user has not assessed this submission
				$action .= " | <a href=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</a>";
				}
			if ($nassessments = workshop_count_assessments($submission)) {
				$action .= " | <a href=\"assessments.php?action=adminlist&a=$workshop->id&sid=$submission->id\">".
					get_string("listassessments", "workshop")." ($nassessments)</a>";
				}
			$action .= " | <a href=\"submissions.php?action=adminconfirmdelete&a=$workshop->id&sid=$submission->id\">".
				get_string("delete", "workshop")."</a>";
			$table->data[] = array("$user->firstname $user->lastname", $submission->title.
				" ".workshop_print_submission_assessments($workshop, $submission, "teacher").
				" ".workshop_print_submission_assessments($workshop, $submission, "student"), $action);
			}
		print_table($table);
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
	if ($nassessed < $workshop->ntassessments) { 
		// if user has not assessed enough, set up "future" assessment records for this user for the teacher submissions...
		// ... first count the number of assessments for each teacher submission...
		if ($submissions = workshop_get_teacher_submissions($workshop)) {
			srand ((float)microtime()*1000000); // initialise random number generator
			foreach ($submissions as $submission) {
				$n = count_records("workshop_assessments", "submissionid", $submission->id);
				// ...OK to have zero, we add a small random number to randomise things...
				$nassessments[$submission->id] = $n + rand(0, 99) / 100;
				}
			// ...put the submissions with the lowest number of assessments first...
			asort($nassessments);
			reset($nassessments);
			foreach ($nassessments as $submissionid => $n) { // break out of loop when we allocated enough assessments...
				$submission = get_record("workshop_submissions", "id", $submissionid);
				// ... provided the user has NOT already assessed that submission...
				if (!$assessment = workshop_get_submission_assessment($submission, $user)) {
					$yearfromnow = time() + 365 * 86400;
					// ...create one and set timecreated way in the future, this is reset when record is updated
					$assessment->workshopid = $workshop->id;
					$assessment->submissionid = $submission->id;
					$assessment->userid = $user->id;
					$assessment->grade = -1; // set impossible grade
					$assessment->timecreated = $yearfromnow;
					if (!$assessment->id = insert_record("workshop_assessments", $assessment)) {
						error("Could not insert workshop assessment!");
						}
					$nassessed++;
					if ($nassessed >= $workshop->ntassessments) {
						break;
						}
					}
				}
			}
		}
	// now list user's assessments (but only list those which come from teacher submissions)
	if ($assessments = workshop_get_user_assessments($workshop, $user)) {
		$timenow = time();
		foreach ($assessments as $assessment) {
			if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
				error ("workshop_list_teacher_submissions: unable to get submission");
				}
			// submission from a teacher?
			if (isteacher($workshop->course, $submission->userid)) {
				$comment = '';
				// user assessment has three states: record created but not assessed (date created in the future); 
				// just assessed but still editable; and "static" (may or may not have been graded by teacher, that
				// is shown in the comment) 
				if ($assessment->timecreated> $timenow) { // user needs to assess this submission
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("assess", "workshop")."</A>";
					}
				elseif (($timenow - $assessment->timecreated) < $CFG->maxeditingtime) { // there's still time left to edit...
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</A>";
					}
				else { 
					$action = "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">"
						.get_string("view", "workshop")."</A>";
					}
				// see if teacher has graded assessment
				if ($assessment->timegraded and (($timenow - $assessment->timegraded) > $CFG->maxeditingtime)) {
					$comment .= get_string("thereisfeedbackfromtheteacher", "workshop", $course->teacher);
					}
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action, $comment);
				}
			}
		}
	print_table($table);
	}


function workshop_list_unassessed_student_submissions($workshop, $user) {
	// list the student submissions not assessed by this user
	global $CFG;
	
	$table->head = array (get_string("title", "workshop"), get_string("submittedby", "workshop"),
		get_string("action", "workshop"), get_string("comment", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*", "*");
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
					$submissionowner = get_record("user", "id", $submission->userid);
					$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
						get_string("edit", "workshop")."</A>";
					$table->data[] = array(workshop_print_submission_title($workshop, $submission), 
						$submissionowner->firstname." ".$submissionowner->lastname, $action, $comment);
					}
				}
			else { // no assessment
				$submissionowner = get_record("user", "id", $submission->userid);
				$action = "<A HREF=\"assessments.php?action=assesssubmission&a=$workshop->id&sid=$submission->id\">".
					get_string("assess", "workshop")."</A>";
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), 
					$submissionowner->firstname." ".$submissionowner->lastname, $action, $comment);
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
	$table->head = array (get_string("title", "workshop"), get_string("submittedby", "workshop"),
	get_string("assessor", "workshop"), get_string("timeassessed", "workshop"), get_string("action", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*", "*");
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
				$submissionowner = get_record("user", "id", $submission->userid);
				$assessor = get_record("user", "id", $assessment->userid);
				$table->data[] = array(workshop_print_submission_title($workshop, $submission), 
					$submissionowner->firstname." ".$submissionowner->lastname, 
					$assessor->firstname." ".$assessor->lastname, userdate($assessment->timecreated), $action);
				}
			}
		if (isset($table->data)) {
			print_table($table);
			}
		}
	}
	

function workshop_list_user_submissions($workshop, $user) {
	global $CFG;

	$timenow = time();
	$table->head = array (get_string("title", "workshop"),  get_string("action", "workshop"),
		get_string("submitted", "assignment"),  get_string("assessments", "workshop"));
	$table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
	$table->size = array ("*", "*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_user_submissions($workshop, $user)) {
		foreach ($submissions as $submission) {
			// allow user to delete submissions if there is more than one submission or if it's fresh
			if ((count($submissions) > 1) or (($timenow - $submission->timecreated) < $CFG->maxeditingtime)) {
				$action = "<a href=\"submissions.php?action=userconfirmdelete&a=$workshop->id&sid=$submission->id\">".
					get_string("delete", "workshop")."</a>";
				}
			else {
				$action = '';
				}
				$n = count_records("workshop_assessments", "submissionid", $submission->id);
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), $action,
				userdate($submission->timecreated), $n);
			}
		print_table($table);
		}
	}


function workshop_print_assessment($workshop, $assessment = false, $allowchanges = false, 
	$showcommentlinks = false) {
	// $allowchanges added 14/7/03
	global $CFG, $THEME, $USER, $WORKSHOP_SCALES, $WORKSHOP_EWEIGHTS;
	if (! $course = get_record("course", "id", $workshop->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
	
	$timenow = time();

	// reset the internal flags
	if ($assessment) {
		$showgrades = false;
		}
	else { // if no assessment, i.e. specimen grade form always show grading scales
		$showgrades = true;
		}
	
	if ($assessment) {
		// set the internal flag is necessary
		if ($allowchanges or !$workshop->agreeassessments or !$workshop->hidegrades or $assessment->timeagreed) {
			$showgrades = true;
			}
			
		echo "<CENTER><TABLE BORDER=\"1\" WIDTH=\"30%\"><TR>
			<TD ALIGN=CENTER BGCOLOR=\"$THEME->cellcontent\">\n";
		if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
			error ("Workshop_print_assessment: Submission record not found");
			}
		echo workshop_print_submission_title($workshop, $submission);
		echo "</TD></TR></TABLE><BR CLEAR=ALL>\n";
		
		// print agreement time if the workshop requires peer agreement
		if ($workshop->agreeassessments and $assessment->timeagreed) {
			echo "<P>".get_string("assessmentwasagreedon", "workshop", userdate($assessment->timeagreed));
			}

		// first print any comments on this assessment
		if ($comments = workshop_get_comments($assessment)) {
			echo "<TABLE CELLPADDING=2 BORDER=1>\n";
			$firstcomment = TRUE;
			foreach ($comments as $comment) {
				echo "<TR valign=top><TD BGCOLOR=\"$THEME->cellheading2\"><P><B>".get_string("commentby","workshop")." ";
				if (isteacher($workshop->course, $comment->userid)) {
					echo $course->teacher;
					}
				elseif ($assessment->userid == $comment->userid) {
					print_string("assessor", "workshop");
					}
				else {
					print_string("authorofsubmission", "workshop");
					}
				echo " ".get_string("on", "workshop", userdate($comment->timecreated))."</B></P></TD></TR><TR><TD>\n";
				echo text_to_html($comment->comments)."&nbsp;\n";
				// add the links if needed
				if ($firstcomment and $showcommentlinks and !$assessment->timeagreed) {
					// show links depending on who doing the viewing
					$firstcomment = FALSE;
					if (isteacher($workshop->course, $USER->id) and ($comment->userid != $USER->id)) {
						echo "<P ALIGN=RIGHT><A HREF=\"assessments.php?action=addcomment&a=$workshop->id&aid=$assessment->id\">".
							get_string("reply", "workshop")."</A>\n";
						}
					elseif (($comment->userid ==$USER->id) and (($timenow - $comment->timecreated) < $CFG->maxeditingtime)) {
						echo "<P ALIGN=RIGHT><A HREF=\"assessments.php?action=editcomment&a=$workshop->id&cid=$comment->id\">".
							get_string("edit", "workshop")."</A>\n";
						if ($USER->id == $submission->userid) {
							echo " | <A HREF=\"assessments.php?action=agreeassessment&a=$workshop->id&aid=$assessment->id\">".
								get_string("agreetothisassessment", "workshop")."</A>\n";
							}
						}
					elseif (($comment->userid != $USER->id) and (($USER->id == $assessment->userid) or 
						($USER->id == $submission->userid))) {
						echo "<P ALIGN=RIGHT><A HREF=\"assessments.php?action=addcomment&a=$workshop->id&aid=$assessment->id\">".
							get_string("reply", "workshop")."</A>\n";
						if ($USER->id == $submission->userid) {
							echo " | <A HREF=\"assessments.php?action=agreeassessment&a=$workshop->id&aid=$assessment->id\">".
								get_string("agreetothisassessment", "workshop")."</A>\n";
							}
						}
					}
				echo "</TD></TR>\n";
				}
			echo "</TABLE>\n";
			}
			
		// only show the grade if grading strategy > 0 and the grade is positive
		if ($showgrades and $assessment->grade >= 0) { 
			echo "<CENTER><B>".get_string("thegradeis", "workshop").": ".number_format($assessment->grade, 2)."% (".
				get_string("maximumgrade")." ".number_format($workshop->grade, 0)."%)</B></CENTER><BR CLEAR=ALL>\n";
			}
		}
		
	// now print the grading form with the teacher's comments if any
	// FORM is needed for Mozilla browsers, else radio bttons are not checked
		?>
	<form name="assessmentform" method="post" action="assessments.php">
	<INPUT TYPE="hidden" NAME="id" VALUE="<?PHP echo $cm->id ?>">
	<input type="hidden" name="aid" value="<?PHP echo $assessment->id ?>">
	<input type="hidden" name="action" value="updateassessment">
	<CENTER>
	<TABLE CELLPADDING=2 BORDER=1>
	<?PHP
	echo "<TR valign=top>\n";
	echo "	<TD colspan=2 BGCOLOR=\"$THEME->cellheading2\"><CENTER><B>".get_string("assessment", "workshop").
		"</B></CENTER></TD>\n";
	echo "</TR>\n";

	// get the assignment elements...
	if (!$elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC")) {
		print_string("noteonassignmentelements", "workshop");
		}
	else {
		foreach ($elementsraw as $element) {
			$elements[] = $element;   // to renumber index 0,1,2...
			}
		}

	if ($assessment) {
		// get any previous grades...
		if ($gradesraw = get_records_select("workshop_grades", "assessmentid = $assessment->id", "elementno")) {
			foreach ($gradesraw as $grade) {
				$grades[] = $grade;   // to renumber index 0,1,2...
				}
			}
		}
	else {
		// setup dummy grades array
		for($i = 0; $i < count($elementsraw); $i++) { // gives a suitable sized loop
			$grades[$i]->feedback = get_string("yourfeedbackgoeshere", "workshop");
			$grades[$i]->grade = 0;
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
					.number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
				echo "</TD></TR>\n";
				if ($showgrades) {
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
			$negativecount = 0;
			for ($i=0; $i < count($elements) - 1; $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=top>\n";
				echo "	<TD align=right><P><B>". get_string("element","workshop")." $iplus1:</B></P></TD>\n";
				echo "	<TD>".text_to_html($elements[$i]->description);
				echo "<P align=right><FONT size=1>Weight: "
					.number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight], 2)."</FONT>\n";
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
			break;
			
		case 4: // rubric grading
			// now run through the elements...
			for ($i=0; $i < count($elements); $i++) {
				$iplus1 = $i+1;
				echo "<TR valign=\"top\">\n";
				echo "<TD align=\"right\"><b>".get_string("element", "workshop")." $iplus1:</b></TD>\n";
				echo "<TD>".text_to_html($elements[$i]->description).
					 "<P align=\"right\"><font size=\"1\">Weight: "
					.number_format($WORKSHOP_EWEIGHTS[$elements[$i]->weight], 2)."</font></TD></tr>\n";
				echo "<TR valign=\"top\">\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\" align=\"center\"><B>".get_string("select", "workshop")."</B></TD>\n";
				echo "	<TD BGCOLOR=\"$THEME->cellheading2\"><B>". get_string("criterion","workshop")."</B></TD></tr>\n";
				if (isset($grades[$i])) {
					$selection = $grades[$i]->grade;
					} else {
					$selection = 0;
					}
				// ...and the rubrics
				if ($rubricsraw = get_records_select("workshop_rubrics", "workshopid = $workshop->id AND 
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
	echo "	<td align=\"right\"><P><B>". get_string("generalcomment", "workshop").":</B></P></TD>\n";
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
			print_string("yourfeedbackgoeshere", "workshop");
			}
		}
	echo "&nbsp;</td>\n";
	echo "</tr>\n";
	echo "<tr valign=\"top\">\n";
	echo "	<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</TD>\n";
	echo "</tr>\n";
	
	$timenow = time();
	// now show the teacher's comment if available...
	if ($assessment->timegraded and (($timenow - $assessment->timegraded) > $CFG->maxeditingtime)) {
		echo "<tr valign=top>\n";
		echo "	<td align=\"right\"><p><b>". get_string("teacherscomment", "workshop").":</b></p></td>\n";
		echo "	<td>\n";
		echo text_to_html($assessment->teachercomment);
		echo "&nbsp;</td>\n";
		echo "</tr>\n";
		// only show the grading grade if it's the teacher
		if (isteacher($course->id)) {
			echo "<tr valign=\"top\">\n";
			echo "	<td align=\"right\"><p><b>". get_string("teachersgrade", "workshop").":</b></p></td>\n";
			echo "	<td>\n";
			echo number_format($assessment->gradinggrade * 100 / COMMENTSCALE, 0)."%";
			echo "&nbsp;</td>\n";
			echo "</tr>\n";
			}
		echo "<tr valign=\"top\">\n";
		echo "<td colspan=\"2\" bgcolor=\"$THEME->cellheading2\">&nbsp;</td>\n";
		echo "</tr>\n";
		}
		
	// ...and close the table, show submit button if needed...
	echo "</table>\n";
	if ($assessment) {
		if ($allowchanges) {  
			echo "<input type=\"submit\" VALUE=\"".get_string("savemyassessment", "workshop")."\">\n";
			}
		// ...if user is author, assessment not agreed, there's no comments, the showcommentlinks flag is set and 
		// it's not self assessment then show some buttons!
		if (($submission->userid == $USER->id) and !$assessment->timeagreed and !$comments and $showcommentlinks and 
				$submission->userid != $assessment->userid) {
			echo "<input type=button VALUE=\"".get_string("agreetothisassessment", "workshop")."\" 
				onclick=\"document.assessmentform.action.value='agreeassessment';document.assessmentform.submit();\">\n";
			echo "<input type=submit value=\"".get_string("disagreewiththisassessment", "workshop")."\"
				onclick=\"document.assessmentform.action.value='addcomment';document.assessmentform.submit();\">\n";
			}
		}
	echo "</center>";
	echo "</form>\n";
	}


function workshop_print_assessments_by_user_for_admin($workshop, $user) {

	if ($assessments =workshop_get_user_assessments($workshop, $user)) {
		foreach ($assessments as $assessment) {
			echo "<p><center><b>".get_string("assessmentby", "workshop", $user->firstname." ".$user->lastname)."</b></center></p>\n";
			workshop_print_assessment($workshop, $assessment);
			echo "<p align=\"right\"><a href=\"assessments.php?action=adminconfirmdelete&a=$workshop->id&aid=$assessment->id\">".
				get_string("delete", "workshop")."</a></p><hr>\n";
			}
		}
	}


function workshop_print_assessments_for_admin($workshop, $submission) {

	if ($assessments =workshop_get_assessments($submission)) {
		foreach ($assessments as $assessment) {
			if (!$user = get_record("user", "id", $assessment->userid)) {
				error (" workshop_print_assessments_for_admin: unable to get user record");
				}
			echo "<p><center><b>".get_string("assessmentby", "workshop", $user->firstname." ".$user->lastname)."</b></center></p>\n";
			workshop_print_assessment($workshop, $assessment);
			echo "<p align=\"right\"><a href=\"assessments.php?action=adminconfirmdelete&a=$workshop->id&aid=$assessment->id\">".
				get_string("delete", "workshop")."</a></p><hr>\n";
			}
		}
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


function workshop_print_league_table($workshop) {
	// print an order table of (student) submissions showing teacher's and student's assessments
	if (! $course = get_record("course", "id", $workshop->course)) {
		error("Print league table: Course is misconfigured");
	}
	$table->head = array (get_string("title", "workshop"),  get_string("name"),
		get_string("teacherassessments", "workshop", $course->teacher),  
		get_string("studentassessments", "workshop",	$course->student), get_string("overallgrade", "workshop"));
	$table->align = array ("left", "left", "center", "center", "center");
	$table->size = array ("*", "*", "*", "*", "*");
	$table->cellpadding = 2;
	$table->cellspacing = 0;

	if ($submissions = workshop_get_student_submissions($workshop, "grade")) {
		foreach ($submissions as $submission) {
			if (!$user = get_record("user", "id", $submission->userid)) {
				error("Print league table: user not found");
				}
			$table->data[] = array(workshop_print_submission_title($workshop, $submission), $user->firstname." ".
				$user->lastname, workshop_print_submission_assessments($workshop, $submission, "teacher"),
				workshop_print_submission_assessments($workshop, $submission, "student"),
				number_format(($workshop->teacherweight * $submission->teachergrade + $workshop->peerweight *
					$submission->peergrade) / ($workshop->teacherweight + $workshop->peerweight), 1)) ;
			}
		print_heading(get_string("leaguetable", "workshop"));
		print_table($table);
		}
	}
	

function workshop_print_submission_assessments($workshop, $submission, $type) {
	// Returns the teacher or peer grade and a hyperlinked list of grades for this submission
	
	$str = '';
	if ($assessments = workshop_get_assessments($submission)) {
		switch ($type) {
			case "teacher" : 
				if ($submission->teachergrade) { // if there's a final teacher's grade...
					$str = "$submission->teachergrade  ";
					}
				foreach ($assessments as $assessment) {
					if (isteacher($workshop->course, $assessment->userid)) {
						$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">[";
						$str .= number_format($assessment->grade, 0)."%";
						if ($assessment->gradinggrade) { // funny, teacher is grading self!
							$str .= "/".number_format($assessment->gradinggrade*100/COMMENTSCALE, 0)."%";
							}
						$str .= "]</A> ";
						}
					}
				break;
			case "student" : 
				if ($submission->peergrade) { // if there's a final peer grade...
					$str = "$submission->peergrade ";
					}
				foreach ($assessments as $assessment) {
					if (isstudent($workshop->course, $assessment->userid)) {
						$str .= "<A HREF=\"assessments.php?action=viewassessment&a=$workshop->id&aid=$assessment->id\">{";
						$str .= number_format($assessment->grade, 0)."%";
						if ($assessment->gradinggrade) {
							$str .= "/".number_format($assessment->gradinggrade*100/COMMENTSCALE, 0)."%";
							}
						$str .= "}</A> ";
						}
					}
				break;
			}
		}
	if (!$str) {
		$str = "&nbsp;";   // be kind to Mozilla browsers!
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


function workshop_print_tabbed_heading($tabs) {
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
				echo "<td valign=top $alignment $width $wrapping bgcolor=\"$THEME->cellheading2\">$tab</td>\n";
			} else {
				echo "<td valign=top $alignment $width $wrapping bgcolor=\"$THEME->body\">$tab</td>\n";
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
	echo "<b>".get_string("title", "workshop")."</b>: <INPUT NAME=\"title\" TYPE=\"text\" SIZE=\"60\" MAXSIZE=\"100\"><BR><BR>\n";
    echo " <INPUT NAME=\"newfile\" TYPE=\"file\" size=\"50\">";
    echo " <INPUT TYPE=submit NAME=save VALUE=\"".get_string("uploadthisfile")."\">";
    echo "</FORM>";
    echo "</DIV>";
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


function workshop_test_user_assessments($workshop, $user) {
	// see if user has assessed required number of assessments of teachers submissions...
	global $CFG;
	
	$result = true;
	$n = 0;
	$timenow =time();
	if ($submissions = workshop_get_teacher_submissions($workshop)) {
		foreach ($submissions as $submission) {
			if ($assessment = workshop_get_submission_assessment($submission, $user)) {
				// ...the date stamp on the assessment should be in the past 
				if ($assessment->timecreated < $timenow) {
					$n++;
					}
				}
			}
		if ($n < min($workshop->ntassessments, workshop_count_teacher_submissions($workshop))) {
			$result = false; 
			}
		}
	return $result;
	}

?>
