<?PHP  // $Id: lib.php,v 1.1 23 Aug 2003

// workshop constants and standard Moodle functions plus the workshop functions 
// called by the standard functions

// see also locallib.php for other non-standard workshop functions

include_once("$CFG->dirroot/files/mimetypes.php");

/*** Constants **********************************/

$WORKSHOP_TYPE = array (0 => get_string("notgraded", "workshop"),
                          1 => get_string("accumulative", "workshop"),
                          2 => get_string("errorbanded", "workshop"),
                          3 => get_string("criterion", "workshop"),
                          4 => get_string("rubric", "workshop") );

$WORKSHOP_SHOWGRADES = array (0 => get_string("dontshowgrades", "workshop"),
                          1 => get_string("showgrades", "workshop") );

$WORKSHOP_SCALES = array( 
                    0 => array( 'name' => get_string("scaleyes", "workshop"), 'type' => 'radio', 
                        'size' => 2, 'start' => get_string("yes"), 'end' => get_string("no")),
                    1 => array( 'name' => get_string("scalepresent", "workshop"), 'type' => 'radio', 
                        'size' => 2, 'start' => get_string("present", "workshop"), 
                        'end' => get_string("absent", "workshop")),
                    2 => array( 'name' => get_string("scalecorrect", "workshop"), 'type' => 'radio', 
                        'size' => 2, 'start' => get_string("correct", "workshop"), 
                        'end' => get_string("incorrect", "workshop")), 
                    3 => array( 'name' => get_string("scalegood3", "workshop"), 'type' => 'radio', 
                        'size' => 3, 'start' => get_string("good", "workshop"), 
                        'end' => get_string("poor", "workshop")), 
                    4 => array( 'name' => get_string("scaleexcellent4", "workshop"), 'type' => 'radio', 
                        'size' => 4, 'start' => get_string("excellent", "workshop"), 
                        'end' => get_string("verypoor", "workshop")),
                    5 => array( 'name' => get_string("scaleexcellent5", "workshop"), 'type' => 'radio', 
                        'size' => 5, 'start' => get_string("excellent", "workshop"), 
                        'end' => get_string("verypoor", "workshop")),
                    6 => array( 'name' => get_string("scaleexcellent7", "workshop"), 'type' => 'radio', 
                        'size' => 7, 'start' => get_string("excellent", "workshop"), 
                        'end' => get_string("verypoor", "workshop")),
                    7 => array( 'name' => get_string("scale10", "workshop"), 'type' => 'selection', 
                        'size' => 10),
                    8 => array( 'name' => get_string("scale20", "workshop"), 'type' => 'selection', 
                            'size' => 20),
                    9 => array( 'name' => get_string("scale100", "workshop"), 'type' => 'selection', 
                            'size' => 100)); 

$WORKSHOP_EWEIGHTS = array(  0 => -4.0, 1 => -2.0, 2 => -1.5, 3 => -1.0, 4 => -0.75, 5 => -0.5,  6 => -0.25, 
                             7 => 0.0, 8 => 0.25, 9 => 0.5, 10 => 0.75, 11=> 1.0, 12 => 1.5, 13=> 2.0, 
                             14 => 4.0); 

$WORKSHOP_FWEIGHTS = array(  0 => 0, 1 => 0.1, 2 => 0.25, 3 => 0.5, 4 => 0.75, 5 => 1.0,  6 => 1.5, 
                             7 => 2.0, 8 => 3.0, 9 => 5.0, 10 => 7.5, 11=> 10.0, 12=>50.0); 

if (!defined("COMMENTSCALE")) {
    define("COMMENTSCALE", 20);
    }


/*** Standard Moodle functions ******************
workshop_add_instance($workshop) 
workshop_cron () 
workshop_delete_instance($id) 
workshop_grades($workshopid) 
workshop_print_recent_activity(&$logs, $isteacher=false) 
workshop_refresh_events($workshop) 
workshop_update_instance($workshop) 
workshop_user_complete($course, $user, $mod, $workshop) 
workshop_user_outline($course, $user, $mod, $workshop) 
**********************************************/

///////////////////////////////////////////////////////////////////////////////
function workshop_add_instance($workshop) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $workshop->timemodified = time();
    
    $workshop->deadline = make_timestamp($workshop->deadlineyear, 
            $workshop->deadlinemonth, $workshop->deadlineday, $workshop->deadlinehour, 
            $workshop->deadlineminute);

    if ($returnid = insert_record("workshop", $workshop)) {

        $event = NULL;
        $event->name        = $workshop->name;
        $event->description = $workshop->description;
        $event->courseid    = $workshop->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'workshop';
        $event->instance    = $returnid;
        $event->eventtype   = 'deadline';
        $event->timestart   = $workshop->deadline;
        $event->timeduration = 0;

        add_event($event);
    }

    return $returnid;
}


///////////////////////////////////////////////////////////////////////////////
function workshop_cron () {
// Function to be run periodically according to the moodle cron

    global $CFG, $USER;
    
    // Find all workshop notifications that have yet to be mailed out, and mails them
    $cutofftime = time() - $CFG->maxeditingtime;

    // look for new assessments
    if ($assessments = workshop_get_unmailed_assessments($cutofftime)) {
        $timenow = time();

        foreach ($assessments as $assessment) {

            echo "Processing workshop assessment $assessment->id\n";
            
            // only process the entry once
            if (! set_field("workshop_assessments", "mailed", "1", "id", "$assessment->id")) {
                echo "Could not update the mailed field for id $assessment->id\n";
            }
            
            if (! $submission = get_record("workshop_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
                continue;
            }
            if (! $workshop = get_record("workshop", "id", $submission->workshopid)) {
                echo "Could not find workshop id $submission->workshopid\n";
                continue;
            }
            if (! $course = get_record("course", "id", $workshop->course)) {
                error("Could not find course id $workshop->course");
                continue;
            }
            if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
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
            if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, 
                        $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, 
                        $assessmentowner->id)) {
                continue;  // Not an active participant
            }
            // don't sent self assessment
            if ($submissionowner->id == $assessmentowner->id) {
                continue;
            }
            $strworkshops = get_string("modulenameplural", "workshop");
            $strworkshop  = get_string("modulename", "workshop");
    
            // it's an assessment, tell the submission owner
            $USER->lang = $submissionowner->lang;
            $sendto = $submissionowner;
            // "Your assignment \"$submission->title\" has been assessed by"
            if (isstudent($course->id, $assessmentowner->id)) {
                $msg = get_string("mail1", "workshop", $submission->title)." a $course->student.\n";
            }
            else {
                $msg = get_string("mail1", "workshop", $submission->title).
                    " $assessmentowner->firstname $assessmentowner->lastname.\n";
            }
            // "The comments and grade can be seen in the workshop assignment '$workshop->name'
            $msg .= get_string("mail2", "workshop", $workshop->name)."\n\n";
    
            $postsubject = "$course->shortname: $strworkshops: $workshop->name";
            $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            // "You can see it in your workshop assignment"
            $posttext .= get_string("mail3", "workshop").":\n";
            $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?id=$cm->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
                $posthtml = "<P><FONT FACE=sans-serif>".
                    "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A></FONT></P>";
                $posthtml .= "<HR><FONT FACE=sans-serif>";
                $posthtml .= "<P>$msg</P>";
                $posthtml .= "<P>".get_string("mail3", "workshop").
                    " <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A>.</P></FONT><HR>";
            } else {
                $posthtml = "";
            }
    
            if (!$teacher = get_teacher($course->id)) {
                echo "Error: can not find teacher for course $course->id!\n";
            }
                
            if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: workshop cron: Could not send out mail for id $submission->id to 
                    user $sendto->id ($sendto->email)\n";
            }
        }
    }
        
    // look for new assessments of resubmissions
    if ($assessments = workshop_get_unmailed_resubmissions($cutofftime)) {
        $timenow = time();

        foreach ($assessments as $assessment) {

            echo "Processing workshop assessment $assessment->id\n";
            
            // only process the entry once
            if (! set_field("workshop_assessments", "mailed", "1", "id", "$assessment->id")) {
                echo "Could not update the mailed field for id $assessment->id\n";
            }
            
            if (! $submission = get_record("workshop_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
                continue;
            }
            if (! $workshop = get_record("workshop", "id", $submission->workshopid)) {
                echo "Could not find workshop id $submission->workshopid\n";
                continue;
            }
            if (! $course = get_record("course", "id", $workshop->course)) {
                error("Could not find course id $workshop->course");
                continue;
            }
            if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
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
            if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, 
                        $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, 
                        $assessmentowner->id)) {
                continue;  // Not an active participant
            }
    
            $strworkshops = get_string("modulenameplural", "workshop");
            $strworkshop  = get_string("modulename", "workshop");
    
            // it's a resubission assessment, tell the assessment owner to (re)assess
            $USER->lang = $assessmentowner->lang;
            $sendto = $assessmentowner;
            // "The assignment \"$submission->title\" is a revised piece of work. "
            $msg = get_string("mail8", "workshop", $submission->title)."\n";
            // "Please assess it in the workshop assignment '$workshop->name'
            $msg .= get_string("mail9", "workshop", $workshop->name)."\n\n";
    
            $postsubject = "$course->shortname: $strworkshops: $workshop->name";
            $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            // "You can assess it in your workshop assignment"
            $posttext .= get_string("mail10", "workshop").":\n";
            $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?id=$cm->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
                $posthtml = "<P><FONT FACE=sans-serif>".
                  "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
                  "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
                  "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A></FONT></P>";
                $posthtml .= "<HR><FONT FACE=sans-serif>";
                $posthtml .= "<P>$msg</P>";
                $posthtml .= "<P>".get_string("mail3", "workshop").
                  " <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A>.</P></FONT><HR>";
            } 
            else {
              $posthtml = "";
            }
    
            if (!$teacher = get_teacher($course->id)) {
                echo "Error: can not find teacher for course $course->id!\n";
            }
                
            if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: workshop cron: Could not send out mail for id $submission->id to 
                    user $sendto->id ($sendto->email)\n";
            }
        }
    }
    
    // look for new comments
    if ($comments = workshop_get_unmailed_comments($cutofftime)) {
        $timenow = time();

        foreach ($comments as $comment) {

            echo "Processing workshop comment $comment->id\n";
            
            // only process the entry once
            if (! set_field("workshop_comments", "mailed", "1", "id", "$comment->id")) {
                echo "Could not update the mailed field for comment id $comment->id\n";
            }
            
            if (! $assessment = get_record("workshop_assessments", "id", "$comment->assessmentid")) {
                echo "Could not find assessment $comment->assessmentid\n";
                continue;
            }
            if (! $submission = get_record("workshop_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
                continue;
            }
            if (! $workshop = get_record("workshop", "id", $submission->workshopid)) {
                echo "Could not find workshop id $submission->workshopid\n";
                continue;
            }
            if (! $course = get_record("course", "id", $workshop->course)) {
                error("Could not find course id $workshop->course");
                continue;
            }
            if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
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
            if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, 
                        $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, 
                        $assessmentowner->id)) {
                continue;  // Not an active participant
            }
    
            $strworkshops = get_string("modulenameplural", "workshop");
            $strworkshop  = get_string("modulename", "workshop");
    
            // see if the submission owner needs to be told
            if ($comment->userid != $submission->userid) {
                $USER->lang = $submissionowner->lang;
                $sendto = $submissionowner;
                // "A comment has been added to the assignment \"$submission->title\" by
                if (isstudent($course->id, $assessmentowner->id)) {
                    $msg = get_string("mail4", "workshop", $submission->title)." a $course->student.\n";
                    }
                else {
                    $msg = get_string("mail4", "workshop", $submission->title)." $assessmentowner->firstname $assessmentowner->lastname.\n";
                    }
                // "The new comment can be seen in the workshop assignment '$workshop->name'
                $msg .= get_string("mail5", "workshop", $workshop->name)."\n\n";
    
                $postsubject = "$course->shortname: $strworkshops: $workshop->name";
                $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
                $posttext .= "---------------------------------------------------------------------\n";
                $posttext .= $msg;
                // "You can see it in your workshop assignment"
                $posttext .= get_string("mail3", "workshop").":\n";
                $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?id=$cm->id\n";
                $posttext .= "---------------------------------------------------------------------\n";
                if ($sendto->mailformat == 1) {  // HTML
                    $posthtml = "<P><FONT FACE=sans-serif>".
                    "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A></FONT></P>";
                    $posthtml .= "<HR><FONT FACE=sans-serif>";
                    $posthtml .= "<P>$msg</P>";
                    $posthtml .= "<P>".get_string("mail3", "workshop").
                        " <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A>
                        .</P></FONT><HR>";
                    } 
                else {
                    $posthtml = "";
                    }
    
                if (!$teacher = get_teacher($course->id)) {
                    echo "Error: can not find teacher for course $course->id!\n";
                    }
                    
                if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                    echo "Error: workshop cron: Could not send out mail for id $submission->id to user 
                        $sendto->id ($sendto->email)\n";
                    }
                }
            // see if the assessor needs to to told
            if ($comment->userid != $assessment->userid) {
                $USER->lang = $assessmentowner->lang;
                $sendto = $assessmentowner;
                // "A comment has been added to the assignment \"$submission->title\" by
                if (isstudent($course->id, $submissionowner->id)) {
                    $msg = get_string("mail4", "workshop", $submission->title)." a $course->student.\n";
                    }
                else {
                    $msg = get_string("mail4", "workshop", $submission->title).
                        " $submissionowner->firstname $submissionowner->lastname.\n";
                    }
                // "The new comment can be seen in the workshop assignment '$workshop->name'
                $msg .= get_string("mail5", "workshop", $workshop->name)."\n\n";
    
                $postsubject = "$course->shortname: $strworkshops: $workshop->name";
                $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
                $posttext .= "---------------------------------------------------------------------\n";
                $posttext .= $msg;
                // "You can see it in your workshop assignment"
                $posttext .= get_string("mail3", "workshop").":\n";
                $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?id=$cm->id\n";
                $posttext .= "---------------------------------------------------------------------\n";
                if ($sendto->mailformat == 1) {  // HTML
                    $posthtml = "<P><FONT FACE=sans-serif>".
                    "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A></FONT></P>";
                    $posthtml .= "<HR><FONT FACE=sans-serif>";
                    $posthtml .= "<P>$msg</P>";
                    $posthtml .= "<P>".get_string("mail3", "workshop").
                        " <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A>.</P></FONT><HR>";
                    } 
                else {
                    $posthtml = "";
                    }
    
                if (!$teacher = get_teacher($course->id)) {
                    echo "Error: can not find teacher for course $course->id!\n";
                    }
                    
                if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                    echo "Error: workshop cron: Could not send out mail for id $submission->id to user 
                        $sendto->id ($sendto->email)\n";
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
            
            // only process the entry once
            if (! set_field("workshop_assessments", "mailed", "1", "id", "$assessment->id")) {
                echo "Could not update the mailed field for id $assessment->id\n";
            }

            if (! $submission = get_record("workshop_submissions", "id", "$assessment->submissionid")) {
                echo "Could not find submission $assessment->submissionid\n";
                continue;
            }

            if (! $workshop = get_record("workshop", "id", $submission->workshopid)) {
                echo "Could not find workshop id $submission->workshopid\n";
                continue;
            }
            if (! $course = get_record("course", "id", $workshop->course)) {
                error("Could not find course id $workshop->course");
                continue;
            }
            if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $course->id)) {
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
            if (! isstudent($course->id, $submissionowner->id) and !isteacher($course->id, 
                        $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! isstudent($course->id, $assessmentowner->id) and !isteacher($course->id, 
                        $assessmentowner->id)) {
                continue;  // Not an active participant
            }

            $strworkshops = get_string("modulenameplural", "workshop");
            $strworkshop  = get_string("modulename", "workshop");

            // it's a grading tell the assessment owner
            $USER->lang = $assessmentowner->lang;
            $sendto = $assessmentowner;
            // Your assessment of the assignment \"$submission->title\" has by reviewed
            $msg = get_string("mail6", "workshop", $submission->title).".\n";
            // The comments given by the $course->teacher can be seen in the Workshop Assignment 
            $msg .= get_string("mail7", "workshop", $course->teacher)." '$workshop->name'.\n\n";

            $postsubject = "$course->shortname: $strworkshops: $workshop->name";
            $posttext  = "$course->shortname -> $strworkshops -> $workshop->name\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            // "You can see it in your workshop assignment"
            $posttext .= get_string("mail3", "workshop").":\n";
            $posttext .= "   $CFG->wwwroot/mod/workshop/view.php?id=$cm->id\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
                $posthtml = "<P><FONT FACE=sans-serif>".
                    "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</A> ->".
                    "<A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A></FONT></P>";
                $posthtml .= "<HR><FONT FACE=sans-serif>";
                $posthtml .= "<P>$msg</P>";
                $posthtml .= "<P>".get_string("mail3", "workshop").
                    " <A HREF=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">$workshop->name</A>.</P></FONT><HR>";
            } else {
              $posthtml = "";
            }

            if (!$teacher = get_teacher($course->id)) {
                echo "Error: can not find teacher for course $course->id!\n";
                }
                
            if (! email_to_user($sendto, $teacher, $postsubject, $posttext, $posthtml)) {
                echo "Error: workshop cron: Could not send out mail for id $submission->id to user 
                    $sendto->id ($sendto->email)\n";
            }
        }
    }

    return true;
}


///////////////////////////////////////////////////////////////////////////////
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

    if (! delete_records('event', 'modulename', 'workshop', 'instance', $workshop->id)) {
        $result = false;    
    }   

    return $result;
}


///////////////////////////////////////////////////////////////////////////////
function workshop_grades($workshopid) {
/// Must return an array of grades, indexed by user, and a max grade.
/// only retruns grades in phase 6
global $CFG;

    if ($workshop = get_record("workshop", "id", $workshopid)) {
        if ($workshop->phase == 6) {
            if ($bestsubmissions = get_records_sql("SELECT userid, max(finalgrade) finalgrade FROM
                    {$CFG->prefix}workshop_submissions WHERE workshopid = $workshopid GROUP
                    BY userid")) {
                foreach ($bestsubmissions as $bestgrade) {
                    $return->grades[$bestgrade->userid] = $bestgrade->finalgrade;
                }
            }
        }
        $return->maxgrade = $workshop->grade;
    }
    return $return;
}

function workshop_is_recent_activity($course, $isteacher, $timestart) {//jlw1 added for adding mark to courses with activity in My Moodle
    global $CFG;

    // have a look for agreed assessments for this user (agree) 
    $agreecontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = workshop_get_agree_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $log) {
                // Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->workshopid;
                //Obtain the visible property from the instance
                if (instance_is_visible("workshop",$tempmod)) {
                    $agreecontent = true;
                    break;
                }
            }
        }
    }
    return false;
}


///////////////////////////////////////////////////////////////////////////////
function workshop_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    // have a look for agreed assessments for this user (agree) 
    $agreecontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = workshop_get_agree_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $log) {
                // Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->workshopid;
                //Obtain the visible property from the instance
                if (instance_is_visible("workshop",$tempmod)) {
                    $agreecontent = true;
                    break;
                    }
                }
            // if we got some "live" ones then output them
            if ($agreecontent) {
                $strftimerecent = get_string("strftimerecent");
                print_headline(get_string("workshopagreedassessments", "workshop").":");
                foreach ($logs as $log) {
                    //Create a temp valid module structure (only need courseid, moduleid)
                    $tempmod->course = $course->id;
                    $tempmod->id = $log->workshopid;
                    //Obtain the visible property from the instance
                    if (instance_is_visible("workshop",$tempmod)) {
                        $date = userdate($log->time, $strftimerecent);
                        if (isteacher($course->id, $log->userid)) {
                            echo "<p><font size=1>$date - $log->firstname $log->lastname<br />";
                            }
                        else { // don't break anonymous rule
                            echo "<p><font size=1>$date - A $course->student<br />";
                            }
                        echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$log->url\">";
                        echo "$log->name";
                        echo "</a>\"</font></p>";
                        }
                    }
                }
            }
        }

    // have a look for new assessments for this user (assess) 
    $assesscontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = workshop_get_assess_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $log) {
                // Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->workshopid;
                //Obtain the visible property from the instance
                if (instance_is_visible("workshop",$tempmod)) {
                    $assesscontent = true;
                    break;
                    }
                }
            // if we got some "live" ones then output them
            if ($assesscontent) {
                $strftimerecent = get_string("strftimerecent");
                print_headline(get_string("workshopassessments", "workshop").":");
                foreach ($logs as $log) {
                    //Create a temp valid module structure (only need courseid, moduleid)
                    $tempmod->course = $course->id;
                    $tempmod->id = $log->workshopid;
                    //Obtain the visible property from the instance
                    if (instance_is_visible("workshop",$tempmod)) {
                        $date = userdate($log->time, $strftimerecent);
                        if (isteacher($course->id, $log->userid)) {
                            echo "<p><font size=1>$date - $log->firstname $log->lastname<br />";
                            }
                        else { // don't break anonymous rule
                            echo "<p><font size=1>$date - A $course->student<br />";
                            }
                        echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$log->url\">";
                        echo "$log->name";
                        echo "</a>\"</font></p>";
                        }
                    }
                }
            }
        }

    // have a look for new comments for this user (comment) 
    $commentcontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = workshop_get_comment_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $log) {
                // Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->workshopid;
                //Obtain the visible property from the instance
                if (instance_is_visible("workshop",$tempmod)) {
                    $commentcontent = true;
                    break;
                    }
                }
            // if we got some "live" ones then output them
            if ($commentcontent) {
                $strftimerecent = get_string("strftimerecent");
                print_headline(get_string("workshopcomments", "workshop").":");
                foreach ($logs as $log) {
                    //Create a temp valid module structure (only need courseid, moduleid)
                    $tempmod->course = $course->id;
                    $tempmod->id = $log->workshopid;
                    //Obtain the visible property from the instance
                    if (instance_is_visible("workshop",$tempmod)) {
                        $date = userdate($log->time, $strftimerecent);
                        echo "<p><font size=1>$date - A $course->student<br />";
                        echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$log->url\">";
                        echo "$log->name";
                        echo "</a>\"</font></p>";
                        }
                    }
                }
            }
        }

    // have a look for new assessment gradings for this user (grade)
    $gradecontent = false;
    if ($logs = workshop_get_grade_logs($course, $timestart)) {
        // got some, see if any belong to a visible module
        foreach ($logs as $log) {
            // Create a temp valid module structure (only need courseid, moduleid)
            $tempmod->course = $course->id;
            $tempmod->id = $log->workshopid;
            //Obtain the visible property from the instance
            if (instance_is_visible("workshop",$tempmod)) {
                $gradecontent = true;
                break;
                }
            }
        // if we got some "live" ones then output them
        if ($gradecontent) {
            $strftimerecent = get_string("strftimerecent");
            print_headline(get_string("workshopfeedback", "workshop").":");
            foreach ($logs as $log) {
                //Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->workshopid;
                //Obtain the visible property from the instance
                if (instance_is_visible("workshop",$tempmod)) {
                    $date = userdate($log->time, $strftimerecent);
                    echo "<p><font size=1>$date - $course->teacher<br />";
                    echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$log->url\">";
                    echo "$log->name";
                    echo "</a>\"</font></p>";
                    }
                }
            }
        }

    // have a look for new submissions (only show to teachers) (submit)
    $submitcontent = false;
    if ($isteacher) {
        if ($logs = workshop_get_submit_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $log) {
                // Create a temp valid module structure (only need courseid, moduleid)
                $tempmod->course = $course->id;
                $tempmod->id = $log->workshopid;
                //Obtain the visible property from the instance
                if (instance_is_visible("workshop",$tempmod)) {
                    $submitcontent = true;
                    break;
                    }
                }
            // if we got some "live" ones then output them
            if ($submitcontent) {
                $strftimerecent = get_string("strftimerecent");
                print_headline(get_string("workshopsubmissions", "workshop").":");
                foreach ($logs as $log) {
                    //Create a temp valid module structure (only need courseid, moduleid)
                    $tempmod->course = $course->id;
                    $tempmod->id = $log->workshopid;
                    //Obtain the visible property from the instance
                    if (instance_is_visible("workshop",$tempmod)) {
                        $date = userdate($log->time, $strftimerecent);
                        echo "<p><font size=1>$date - $log->firstname $log->lastname<br />";
                        echo "\"<a href=\"$CFG->wwwroot/mod/workshop/$log->url\">";
                        echo "$log->name";
                        echo "</a>\"</font></p>";
                        }
                    }
                }
            }
        }

    return $agreecontent or $assesscontent or $commentcontent or $gradecontent or $submitcontent;
}


///////////////////////////////////////////////////////////////////////////////
function workshop_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every workshop event in the site is checked, else
// only workshop events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid == 0) {
        if (! $workshops = get_records("workshop")) {
            return true;        
        }   
    } else {
        if (! $workshops = get_records("workshop", "course", $courseid)) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'workshop');
    
    foreach ($workshops as $workshop) {
        $event = NULL;
        $event->name        = addslashes($workshop->name);
        $event->description = addslashes($workshop->description);
        $event->timestart   = $workshop->deadline;

        if ($event->id = get_field('event', 'id', 'modulename', 'workshop', 'instance', $workshop->id)) {
            update_event($event);
    
        } else {
            $event->courseid    = $workshop->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'workshop';
            $event->instance    = $workshop->id; 
            $event->eventtype   = 'deadline';
            $event->timeduration = 0;
            $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $workshop->id); 
            add_event($event);
        }

    }
    return true;
}   


///////////////////////////////////////////////////////////////////////////////
function workshop_update_instance($workshop) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $workshop->timemodified = time();

    $workshop->deadline = make_timestamp($workshop->deadlineyear, 
            $workshop->deadlinemonth, $workshop->deadlineday, $workshop->deadlinehour, 
            $workshop->deadlineminute);

    $workshop->id = $workshop->instance;

    if ($returnid = update_record("workshop", $workshop)) {

        $event = NULL;

        if ($event->id = get_field('event', 'id', 'modulename', 'workshop', 'instance', $workshop->id)) {

            $event->name        = $workshop->name;
            $event->description = $workshop->description;
            $event->timestart   = $workshop->deadline;

            update_event($event);
        }
    }

    return $returnid;
}


///////////////////////////////////////////////////////////////////////////////
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



///////////////////////////////////////////////////////////////////////////////
function workshop_user_outline($course, $user, $mod, $workshop) {
    if ($submissions = workshop_get_user_submissions($workshop, $user)) {
        $result->info = count($submissions)." ".get_string("submissions", "workshop");
        // workshop_get_user_submissions returns the newest one first
        foreach ($submissions as $submission) {
            $result->time = $submission->timecreated;
            break;
            }
        return $result;
    }
    return NULL;
}

//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_participants($workshopid) {      
//Returns the users with data in one workshop
//(users with records in workshop_submissions, workshop_assessments and workshop_comments, students)

    global $CFG;

    //Get students from workshop_submissions
    $st_submissions = get_records_sql("SELECT DISTINCT u.*
                                       FROM {$CFG->prefix}user u,
                                            {$CFG->prefix}workshop_submissions s
                                       WHERE s.workshopid = '$workshopid' and
                                             u.id = s.userid");
    //Get students from workshop_assessments
    $st_assessments = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}workshop_assessments a
                                 WHERE a.workshopid = '$workshopid' and
                                       u.id = a.userid");

    //Get students from workshop_comments
    $st_comments = get_records_sql("SELECT DISTINCT u.*
                                   FROM {$CFG->prefix}user u,
                                        {$CFG->prefix}workshop_comments c
                                   WHERE c.workshopid = '$workshopid' and
                                         u.id = c.userid");

    //Add st_assessments to st_submissions
    if ($st_assessments) {
        foreach ($st_assessments as $st_assessment) {
            $st_submissions[$st_assessment->id] = $st_assessment;
        }
    }
    //Add st_comments to st_submissions
    if ($st_comments) {
        foreach ($st_comments as $st_comment) {
            $st_submissions[$st_comment->id] = $st_comment;
        }
    }
    //Return st_submissions array (it contains an array of unique users)
    return ($st_submissions);
}


//////////////////////////////////////////////////////////////////////////////////////
// Non-standard workshop functions
//////////////////////////////////////////////////////////////////////////////////////
function workshop_file_area($workshop, $submission) {
    return make_upload_directory( workshop_file_area_name($workshop, $submission) );
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_file_area_name($workshop, $submission) {
//  Creates a directory file name, suitable for make_upload_directory()
    global $CFG;

    return "$workshop->course/$CFG->moddata/workshop/$submission->id";
}


///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_get_agree_logs($course, $timestart) {
    // get the "agree" entries for this user (the assessment owner and add the first and last names 
    // the last two probably wont be used...
    global $CFG, $USER;
    if (empty($USER->id)) {
        return false;
    }
    
    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.workshopid, a.userid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}workshop e, 
                                {$CFG->prefix}workshop_submissions s, 
                                {$CFG->prefix}workshop_assessments a, 
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
                                AND l.course = $course->id AND l.module = 'workshop' AND l.action = 'agree'
                                AND a.id = l.info AND s.id = a.submissionid AND a.userid = $USER->id
                                AND u.id = s.userid AND e.id = a.workshopid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_get_assess_logs($course, $timestart) {
    // get the "assess" entries for this user and add the first and last names...
    global $CFG, $USER;
    if (empty($USER->id)) {
        return false;
    }
    
    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.workshopid, a.userid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}workshop e, 
                                {$CFG->prefix}workshop_submissions s, 
                                {$CFG->prefix}workshop_assessments a, 
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
                                AND l.course = $course->id AND l.module = 'workshop' AND l.action = 'assess'
                                AND a.id = l.info AND s.id = a.submissionid AND s.userid = $USER->id
                                AND u.id = a.userid AND e.id = a.workshopid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_get_comment_logs($course, $timestart) {
    // get the "comment" entries for this user and add the first and last names (which may not be used)...
    global $CFG, $USER;
    if (empty($USER->id)) {
        return false;
    }
    
    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.workshopid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}workshop e, 
                                {$CFG->prefix}workshop_submissions s, 
                                {$CFG->prefix}workshop_assessments a, 
                                {$CFG->prefix}workshop_comments c, 
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
                                AND l.course = $course->id AND l.module = 'workshop' AND l.action = 'comment'
                                AND c.id = l.info AND c.userid != $USER->id AND a.id = c.assessmentid
                                AND s.id = a.submissionid AND (s.userid = $USER->id OR a.userid = $USER->id)
                                AND u.id = a.userid AND e.id = a.workshopid");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_get_grade_logs($course, $timestart) {
    // get the "grade" entries for this user and add the first and last names (of submission owner, 
    // better to get name of teacher...
    // ...but not available in assessment record...)
    global $CFG, $USER;
    if (empty($USER->id)) {
        return false;
    }
    
    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, a.workshopid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}workshop e, 
                                {$CFG->prefix}workshop_submissions s, 
                                {$CFG->prefix}workshop_assessments a, 
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
                                AND l.course = $course->id AND l.module = 'workshop'    AND l.action = 'grade'
                                AND a.id = l.info AND s.id = a.submissionid AND a.userid = $USER->id
                                AND u.id = s.userid AND e.id = a.workshopid");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_student_submission($workshop, $user) {
// Return a submission for a particular user
    global $CFG;

    $submission = get_record("workshop_submissions", "workshopid", $workshop->id, "userid", $user->id);
    if (!empty($submission->timecreated)) {
        return $submission;
    }
    return NULL;
}


///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_get_submit_logs($course, $timestart) {
    // get the "submit" entries and add the first and last names...
    global $CFG, $USER;
    
    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, l.info workshopid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}workshop e, 
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen 
                                AND l.course = $course->id AND l.module = 'workshop'
                                AND l.action = 'submit'
                                AND e.id = l.info 
                                AND u.id = l.userid");
}


//////////////////////////////////////////////////////////////////////////////////////
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


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_unmailed_comments($cutofftime) {
    /// Return list of comments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT c.*, g.course, g.name
                              FROM {$CFG->prefix}workshop_comments c, {$CFG->prefix}workshop g
                             WHERE c.mailed = 0 
                               AND c.timecreated < $cutofftime 
                               AND g.id = c.workshopid");
}


//////////////////////////////////////////////////////////////////////////////////////
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


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_unmailed_resubmissions($cutofftime) {
    /// Return list of assessments of resubmissions that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT a.*, w.course, w.name
                              FROM {$CFG->prefix}workshop_assessments a, {$CFG->prefix}workshop w
                             WHERE a.mailed = 0 
                               AND a.resubmission = 1
                               AND w.id = a.workshopid");
}


//////////////////////////////////////////////////////////////////////////////////////
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
    return get_records_sql("SELECT s.* FROM {$CFG->prefix}workshop_submissions s, 
                            {$CFG->prefix}user_students u, {$CFG->prefix}user a 
                            WHERE u.course = $workshop->course
                              AND s.userid = u.userid
                              AND a.id = u.userid
                              AND s.workshopid = $workshop->id
                              AND s.timecreated > 0
                              ORDER BY $order");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_user_submissions($workshop, $user) {
    // return real submissions of user newest first, oldest last. Ignores the dummy submissions
    // which get created to hold the final grades for users for make no submissions)
    return get_records_select("workshop_submissions", "workshopid = $workshop->id AND 
        userid = $user->id AND timecreated > 0", "timecreated DESC" );
}


?>
