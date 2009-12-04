<?php  // $Id$

// workshop constants and standard Moodle functions plus the workshop functions
// called by the standard functions

// see also locallib.php for other non-standard workshop functions

require_once($CFG->libdir.'/filelib.php');

/*** Constants **********************************/


$WORKSHOP_EWEIGHTS = array(  0 => -4.0, 1 => -2.0, 2 => -1.5, 3 => -1.0, 4 => -0.75, 5 => -0.5,  6 => -0.25,
                             7 => 0.0, 8 => 0.25, 9 => 0.5, 10 => 0.75, 11=> 1.0, 12 => 1.5, 13=> 2.0,
                             14 => 4.0);

$WORKSHOP_FWEIGHTS = array(  0 => 0, 1 => 0.1, 2 => 0.25, 3 => 0.5, 4 => 0.75, 5 => 1.0,  6 => 1.5,
                             7 => 2.0, 8 => 3.0, 9 => 5.0, 10 => 7.5, 11=> 10.0, 12=>50.0);


$WORKSHOP_ASSESSMENT_COMPS = array (
                          0 => array('name' => get_string('verylax', 'workshop'), 'value' => 1),
                          1 => array('name' => get_string('lax', 'workshop'), 'value' => 0.6),
                          2 => array('name' => get_string('fair', 'workshop'), 'value' => 0.4),
                          3 => array('name' => get_string('strict', 'workshop'), 'value' => 0.33),
                          4 => array('name' => get_string('verystrict', 'workshop'), 'value' => 0.2) );


/*** Moodle 1.7 compatibility functions *****
 *
 ********************************************/
function workshop_context($workshop) {
    //TODO: add some $cm caching if needed
    if (is_object($workshop)) {
        $workshop = $workshop->id;
    }
    if (! $cm = get_coursemodule_from_instance('workshop', $workshop)) {
        error('Course Module ID was incorrect');
    }

    return get_context_instance(CONTEXT_MODULE, $cm->id);
}

function workshop_is_teacher($workshop, $userid=NULL) {
    return has_capability('mod/workshop:manage', workshop_context($workshop), $userid);
}

function workshop_is_teacheredit($workshop, $userid=NULL) {
    return has_capability('mod/workshop:manage', workshop_context($workshop), $userid)
       and has_capability('moodle/site:accessallgroups', workshop_context($workshop), $userid);
}

function workshop_is_student($workshop, $userid=NULL) {
    return has_capability('mod/workshop:participate', workshop_context($workshop), $userid);
}

function workshop_get_students($workshop, $sort='u.lastaccess', $fields='u.*') {
    return $users = get_users_by_capability(workshop_context($workshop), 'mod/workshop:participate', $fields, $sort);
}

function workshop_get_teachers($workshop, $sort='u.lastaccess', $fields='u.*') {
    return $users = get_users_by_capability(workshop_context($workshop), 'mod/workshop:manage', $fields, $sort);
}


/*** Standard Moodle functions ******************
workshop_add_instance($workshop)
workshop_check_dates($workshop)
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

    $workshop->submissionstart = make_timestamp($workshop->submissionstartyear,
            $workshop->submissionstartmonth, $workshop->submissionstartday, $workshop->submissionstarthour,
            $workshop->submissionstartminute);

    $workshop->assessmentstart = make_timestamp($workshop->assessmentstartyear,
            $workshop->assessmentstartmonth, $workshop->assessmentstartday, $workshop->assessmentstarthour,
            $workshop->assessmentstartminute);

    $workshop->submissionend = make_timestamp($workshop->submissionendyear,
            $workshop->submissionendmonth, $workshop->submissionendday, $workshop->submissionendhour,
            $workshop->submissionendminute);

    $workshop->assessmentend = make_timestamp($workshop->assessmentendyear,
            $workshop->assessmentendmonth, $workshop->assessmentendday, $workshop->assessmentendhour,
            $workshop->assessmentendminute);

    $workshop->releasegrades = make_timestamp($workshop->releaseyear,
            $workshop->releasemonth, $workshop->releaseday, $workshop->releasehour,
            $workshop->releaseminute);

    if (!workshop_check_dates($workshop)) {
        return get_string('invaliddates', 'workshop');
    }

    // set the workshop's type
    $wtype = 0; // 3 phases, no grading grades
    if ($workshop->includeself or $workshop->ntassessments) $wtype = 1; // 3 phases with grading grades
    if ($workshop->nsassessments) $wtype = 2; // 5 phases with grading grades
    $workshop->wtype = $wtype;

    if ($returnid = insert_record("workshop", $workshop)) {

        $event = NULL;
        $event->name        = get_string('submissionstartevent','workshop', $workshop->name);
        $event->description = $workshop->description;
        $event->courseid    = $workshop->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'workshop';
        $event->instance    = $returnid;
        $event->eventtype   = 'submissionstart';
        $event->timestart   = $workshop->submissionstart;
        $event->timeduration = 0;
        add_event($event);

        $event->name        = get_string('submissionendevent','workshop', $workshop->name);
        $event->eventtype   = 'submissionend';
        $event->timestart   = $workshop->submissionend;
        add_event($event);

        $event->name        = get_string('assessmentstartevent','workshop', $workshop->name);
        $event->eventtype   = 'assessmentstart';
        $event->timestart   = $workshop->assessmentstart;
        add_event($event);

        $event->name        = get_string('assessmentendevent','workshop', $workshop->name);
        $event->eventtype   = 'assessmentend';
        $event->timestart   = $workshop->assessmentend;
        add_event($event);
    }

    return $returnid;
}

///////////////////////////////////////////////////////////////////////////////
// returns true if the dates are valid, false otherwise
function workshop_check_dates($workshop) {
    // allow submission and assessment to start on the same date and to end on the same date
    // but enforce non-empty submission period and non-empty assessment period.
    return ($workshop->submissionstart < $workshop->submissionend and
            $workshop->submissionstart <= $workshop->assessmentstart and
            $workshop->assessmentstart < $workshop->assessmentend and
            $workshop->submissionend <= $workshop->assessmentend);
}


///////////////////////////////////////////////////////////////////////////////
function workshop_cron () {
// Function to be run periodically according to the moodle cron

    global $CFG, $USER;

    // if there any ungraded assessments run the grading routine
    if ($workshops = get_records("workshop")) {
        foreach ($workshops as $workshop) {
            // automatically grade assessments if workshop has examples and/or peer assessments
            if ($workshop->gradingstrategy and ($workshop->ntassessments or $workshop->nsassessments)) {
                workshop_grade_assessments($workshop);
            }
        }
    }
    $timenow = time();

    // Find all workshop notifications that have yet to be mailed out, and mails them
    $cutofftime = $timenow - $CFG->maxeditingtime;

    // look for new assessments
    if ($assessments = workshop_get_unmailed_assessments($cutofftime)) {
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
            if (! workshop_is_student($workshop, $submissionowner->id) and !workshop_is_teacher($workshop,
                        $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! workshop_is_student($workshop, $assessmentowner->id) and !workshop_is_teacher($workshop,
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
            if (workshop_is_student($workshop, $assessmentowner->id)) {
                $msg = get_string("mail1", "workshop", $submission->title)." a $course->student.\n";
            }
            else {
                $msg = get_string("mail1", "workshop", $submission->title).
                    " ".fullname($assessmentowner)."\n";
            }
            // "The comments and grade can be seen in the workshop assignment '$workshop->name'
            // I have taken the following line out because the info is repeated below.
            // $msg .= get_string("mail2", "workshop", $workshop->name)."\n\n";

            $postsubject = "$course->shortname: $strworkshops: ".format_string($workshop->name,true);
            $posttext  = "$course->shortname -> $strworkshops -> ".format_string($workshop->name,true)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            // "The comments and grade can be seen in ..."
            $posttext .= get_string("mail2", "workshop",
                format_string($workshop->name,true).",   $CFG->wwwroot/mod/workshop/view.php?id=$cm->id")."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
                $posthtml = "<p><font face=\"sans-serif\">".
                    "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a></font></p>";
                $posthtml .= "<hr><font face=\"sans-serif\">";
                $posthtml .= "<p>$msg</p>";
                $posthtml .= "<p>".get_string("mail2", "workshop",
                    " <a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a>")."</p></font><hr>";
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
            if (! workshop_is_student($workshop, $submissionowner->id) and !workshop_is_teacher($workshop,
                        $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! workshop_is_student($workshop, $assessmentowner->id) and !workshop_is_teacher($workshop,
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
            // $msg .= get_string("mail9", "workshop", $workshop->name)."\n\n";

            $postsubject = "$course->shortname: $strworkshops: ".format_string($workshop->name,true);
            $posttext  = "$course->shortname -> $strworkshops -> ".format_string($workshop->name,true)."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            $posttext .= $msg;
            // "Please assess it in ..."
            $posttext .= get_string("mail9", "workshop",
                           format_string($workshop->name,true).", $CFG->wwwroot/mod/workshop/view.php?id=$cm->id")."\n";
            $posttext .= "---------------------------------------------------------------------\n";
            if ($sendto->mailformat == 1) {  // HTML
                $posthtml = "<p><font face=\"sans-serif\">".
                  "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                  "<a href=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</a> ->".
                  "<a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a></font></p>";
                $posthtml .= "<hr><font face=\"sans-serif\">";
                $posthtml .= "<p>$msg</p>";
                $posthtml .= "<p>".get_string("mail9", "workshop",
                  " <a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a>").'</p></font><hr>';
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
            if (! workshop_is_student($workshop, $submissionowner->id) and !workshop_is_teacher($workshop,
                        $submissionowner->id)) {
                continue;  // Not an active participant
            }
            if (! workshop_is_student($workshop, $assessmentowner->id) and !workshop_is_teacher($workshop,
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
                if (workshop_is_student($workshop, $assessmentowner->id)) {
                    $msg = get_string("mail4", "workshop", $submission->title)." a $course->student.\n";
                }
                else {
                    $msg = get_string("mail4", "workshop", $submission->title)." ".fullname($assessmentowner)."\n";
                }
                // "The new comment can be seen in the workshop assignment '$workshop->name'
                // $msg .= get_string("mail5", "workshop", $workshop->name)."\n\n";

                $postsubject = "$course->shortname: $strworkshops: ".format_string($workshop->name,true);
                $posttext  = "$course->shortname -> $strworkshops -> ".format_string($workshop->name,true)."\n";
                $posttext .= "---------------------------------------------------------------------\n";
                $posttext .= $msg;
                // "The new comment can be seen in ..."
                $posttext .= get_string("mail5", "workshop",
                    format_string($workshop->name,true).",   $CFG->wwwroot/mod/workshop/view.php?id=$cm->id")."\n";
                $posttext .= "---------------------------------------------------------------------\n";
                if ($sendto->mailformat == 1) {  // HTML
                    $posthtml = "<p><font face=\"sans-serif\">".
                    "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a></font></p>";
                    $posthtml .= "<hr><font face=\"sans-serif\">";
                    $posthtml .= "<p>$msg</p>";
                    $posthtml .= "<p>".get_string("mail5", "workshop",
                        " <a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a>")
                        ."</p></font><hr>";
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
                if (workshop_is_student($workshop, $submissionowner->id)) {
                    $msg = get_string("mail4", "workshop", $submission->title)." a $course->student.\n";
                }
                else {
                    $msg = get_string("mail4", "workshop", $submission->title).
                        " ".fullname($submissionowner)."\n";
                }
                // "The new comment can be seen in the workshop assignment '$workshop->name'
                // $msg .= get_string("mail5", "workshop", $workshop->name)."\n\n";

                $postsubject = "$course->shortname: $strworkshops: ".format_string($workshop->name,true);
                $posttext  = "$course->shortname -> $strworkshops -> ".format_string($workshop->name,true)."\n";
                $posttext .= "---------------------------------------------------------------------\n";
                $posttext .= $msg;
                // "The new comment can be seen in ..."
                $posttext .= get_string("mail5", "workshop",
                    format_string($workshop->name,true).",  $CFG->wwwroot/mod/workshop/view.php?id=$cm->id")."\n";
                $posttext .= "---------------------------------------------------------------------\n";
                if ($sendto->mailformat == 1) {  // HTML
                    $posthtml = "<p><font face=\"sans-serif\">".
                    "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/workshop/index.php?id=$course->id\">$strworkshops</a> ->".
                    "<a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a></font></p>";
                    $posthtml .= "<hr><font face=\"sans-serif\">";
                    $posthtml .= "<p>$msg</p>";
                    $posthtml .= "<p>".get_string("mail5", "workshop",
                        " <a href=\"$CFG->wwwroot/mod/workshop/view.php?id=$cm->id\">".format_string($workshop->name,true)."</a>")
                        ."</p></font><hr>";
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

    if (! delete_records("workshop_stockcomments", "workshopid", "$workshop->id")) {
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
/// only returns grades once assessment has started
/// returns nothing if workshop is not graded
    global $CFG;

    $return = null;
    if ($workshop = get_record("workshop", "id", $workshopid)) {
        if (($workshop->assessmentstart < time()) and $workshop->gradingstrategy) {
            if ($students = workshop_get_students($workshop)) {
                foreach ($students as $student) {
                    if ($workshop->wtype) {
                        $gradinggrade = workshop_gradinggrade($workshop, $student);
                    } else { // ignore grading grades for simple assignments
                        $gradinggrade = 0;
                    }
                    $bestgrade = 0;
                    if ($submissions = workshop_get_user_submissions($workshop, $student)) {
                        foreach ($submissions as $submission) {
                            if (!$submission->late) {
                                $grade = workshop_submission_grade($workshop, $submission);
                            } else {
                                $grade = 0.01;
                            }
                            if ($grade > $bestgrade) {
                                $bestgrade = $grade;
                            }
                        }
                    }
                    $return->grades[$student->id] = $gradinggrade + $bestgrade;
                }
            }
        }
        // set maximum grade if graded
        if ($workshop->gradingstrategy) {
            if ($workshop->wtype) {
                $return->maxgrade = $workshop->grade + $workshop->gradinggrade;
            } else { // ignore grading grades for simple assignemnts
                $return->maxgrade = $workshop->grade;
            }
        }
    }
    return $return;
}

//////////////////////////////////////////////////////////////////////////////////////
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
//
// NOTE: $isteacher usage should be converted to use roles.
// TODO: Fix this function.
//
function workshop_print_recent_activity($course, $viewfullanmes, $timestart) {
    global $CFG;

    $isteacher = has_capability('mod/workshop:manage', get_context_instance(CONTEXT_COURSE, $course->id));

    $modinfo = get_fast_modinfo($course);

    // have a look for agreed assessments for this user (agree)
    $agreecontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = workshop_get_agree_logs($course, $timestart)) {
            $agreecontent = true;
            print_headline(get_string("workshopagreedassessments", "workshop").":");
            foreach ($logs as $log) {
                if (!workshop_is_teacher($workshop, $log->userid)) {  // don't break anonymous rule
                    $log->firstname = $course->student;
                    $log->lastname = '';
                }
                print_recent_activity_note($log->time, $log, $log->name,
                                           $CFG->wwwroot.'/mod/workshop/'.$log->url);
            }
        }
    }

    // have a look for new assessments for this user (assess)
    $assesscontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = workshop_get_assess_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $id=>$log) {
                $cm = $modinfo->instances['workshop'][$log->workshopid];
                if (!$cm->uservisible) {
                    unset($logs[$id]);
                    continue;
                }
            }
            // if we got some "live" ones then output them
            if ($logs) {
                $assesscontent = true;
                print_headline(get_string("workshopassessments", "workshop").":");
                foreach ($logs as $log) {
                    if (!workshop_is_teacher($tempmod->id, $log->userid)) {  // don't break anonymous rule
                        $log->firstname = $course->student;    // Keep anonymous
                        $log->lastname = '';
                    }
                    print_recent_activity_note($log->time, $log, $log->name,
                                               $CFG->wwwroot.'/mod/workshop/'.$log->url);
                }
            }
        }
    }
    // have a look for new comments for this user (comment)
    $commentcontent = false;
    if (!$isteacher) { // teachers only need to see submissions
        if ($logs = workshop_get_comment_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $id=>$log) {
                $cm = $modinfo->instances['workshop'][$log->workshopid];
                if (!$cm->uservisible) {
                    unset($logs[$id]);
                    continue;
                }
            }
            // if we got some "live" ones then output them
            if ($logs) {
                $commentcontent = true;
                print_headline(get_string("workshopcomments", "workshop").":");
                foreach ($logs as $log) {
                    $log->firstname = $course->student;    // Keep anonymous
                    $log->lastname = '';
                    print_recent_activity_note($log->time, $log, $log->name,
                                               $CFG->wwwroot.'/mod/workshop/'.$log->url);
                }
            }
        }
    }

    // have a look for new assessment gradings for this user (grade)
    $gradecontent = false;
    if ($logs = workshop_get_grade_logs($course, $timestart)) {
        // got some, see if any belong to a visible module
        foreach ($logs as $id=>$log) {
            $cm = $modinfo->instances['workshop'][$log->workshopid];
            if (!$cm->uservisible) {
                unset($logs[$id]);
                continue;
            }
        }
        // if we got some "live" ones then output them
        if ($logs) {
            $gradecontent = true;
            print_headline(get_string("workshopfeedback", "workshop").":");
            foreach ($logs as $log) {
                $log->firstname = $course->teacher;    // Keep anonymous
                $log->lastname = '';
                print_recent_activity_note($log->time, $log, $log->name,
                                           $CFG->wwwroot.'/mod/workshop/'.$log->url);
            }
        }
    }

    // have a look for new submissions (only show to teachers) (submit)
    $submitcontent = false;
    if ($isteacher) {
        if ($logs = workshop_get_submit_logs($course, $timestart)) {
            // got some, see if any belong to a visible module
            foreach ($logs as $id=>$log) {
                $cm = $modinfo->instances['workshop'][$log->workshopid];
                if (!$cm->uservisible) {
                    unset($logs[$id]);
                    continue;
                }
            }
            // if we got some "live" ones then output them
            if ($logs) {
                $submitcontent = true;
                print_headline(get_string("workshopsubmissions", "workshop").":");
                foreach ($logs as $log) {
                    print_recent_activity_note($log->time, $log, $log->name,
                                               $CFG->wwwroot.'/mod/workshop/'.$log->url);
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

        $dates = array(
            'submissionstart' => $workshop->submissionstart,
            'submissionend' => $workshop->submissionend,
            'assessmentstart' => $workshop->assessmentstart,
            'assessmentend' => $workshop->assessmentend
        );

        foreach ($dates as $type => $date) {

            if ($date) {
                if ($event = get_record('event', 'modulename', 'workshop', 'instance', $workshop->id, 'eventtype', $type)) {
                    $event->name        = addslashes(get_string($type.'event','workshop', $workshop->name));
                    $event->description = addslashes($workshop->description);
                    $event->eventtype   = $type;
                    $event->timestart   = $date;
                    update_event($event);
                } else {
                    $event->courseid    = $workshop->course;
                    $event->modulename  = 'workshop';
                    $event->instance    = $workshop->id;
                    $event->name        = addslashes(get_string($type.'event','workshop', $workshop->name));
                    $event->description = addslashes($workshop->description);
                    $event->eventtype   = $type;
                    $event->timestart   = $date;
                    $event->timeduration = 0;
                    $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $workshop->id);
                    add_event($event);
                }
            }
        }
    }
    return true;
}


///////////////////////////////////////////////////////////////////////////////
function workshop_update_instance($workshop) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will update an existing instance with new data.
    global $CFG;

    $workshop->timemodified = time();

    $workshop->submissionstart = make_timestamp($workshop->submissionstartyear,
            $workshop->submissionstartmonth, $workshop->submissionstartday, $workshop->submissionstarthour,
            $workshop->submissionstartminute);

    $workshop->assessmentstart = make_timestamp($workshop->assessmentstartyear,
            $workshop->assessmentstartmonth, $workshop->assessmentstartday, $workshop->assessmentstarthour,
            $workshop->assessmentstartminute);

    $workshop->submissionend = make_timestamp($workshop->submissionendyear,
            $workshop->submissionendmonth, $workshop->submissionendday, $workshop->submissionendhour,
            $workshop->submissionendminute);

    $workshop->assessmentend = make_timestamp($workshop->assessmentendyear,
            $workshop->assessmentendmonth, $workshop->assessmentendday, $workshop->assessmentendhour,
            $workshop->assessmentendminute);

    $workshop->releasegrades = make_timestamp($workshop->releaseyear,
            $workshop->releasemonth, $workshop->releaseday, $workshop->releasehour,
            $workshop->releaseminute);

    if (!workshop_check_dates($workshop)) {
        return get_string('invaliddates', 'workshop');
    }

    // set the workshop's type
    $wtype = 0; // 3 phases, no grading grades
    if ($workshop->includeself or $workshop->ntassessments) $wtype = 1; // 3 phases with grading grades
    if ($workshop->nsassessments) $wtype = 2; // 5 phases with grading grades
    $workshop->wtype = $wtype;

    // encode password if necessary
    if (!empty($workshop->password)) {
        $workshop->password = md5($workshop->password);
    } else {
        unset($workshop->password);
    }

    $workshop->id = $workshop->instance;

    if ($returnid = update_record("workshop", $workshop)) {

        $dates = array(
            'submissionstart' => $workshop->submissionstart,
            'submissionend' => $workshop->submissionend,
            'assessmentstart' => $workshop->assessmentstart,
            'assessmentend' => $workshop->assessmentend
        );
        $moduleid = get_field('modules', 'id', 'name', 'workshop');

        foreach ($dates as $type => $date) {
            if ($event = get_record('event', 'modulename', 'workshop', 'instance', $workshop->id, 'eventtype', $type)) {
                $event->name        = get_string($type.'event','workshop', $workshop->name);
                $event->description = $workshop->description;
                $event->eventtype   = $type;
                $event->timestart   = $date;
                update_event($event);
            } else if ($date) {
                $event = NULL;
                $event->name        = get_string($type.'event','workshop', $workshop->name);
                $event->description = $workshop->description;
                $event->courseid    = $workshop->course;
                $event->groupid     = 0;
                $event->userid      = 0;
                $event->modulename  = 'workshop';
                $event->instance    = $workshop->instance;
                $event->eventtype   = $type;
                $event->timestart   = $date;
                $event->timeduration = 0;
                $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $workshop->id);
                add_event($event);
            }
        }
    }

    if (time() > $workshop->assessmentstart) {
        // regrade all the submissions...
        set_field("workshop_submissions", "nassessments", 0, "workshopid", $workshop->id);
        workshop_grade_assessments($workshop);
    }

    return $returnid;
}

///////////////////////////////////////////////////////////////////////////////
function workshop_user_complete($course, $user, $mod, $workshop) {
    if ($submission = workshop_get_student_submission($workshop, $user)) {
        if ($basedir = workshop_file_area($workshop, $user)) {
            if ($files = get_directory_list($basedir)) {
                $countfiles = count($files).' '.get_string('submissions', 'workshop');
                foreach ($files as $file) {
                    $countfiles .= "; $file";
                }
            }
        }

        print_simple_box_start();

        echo $submission->description.'<br />';

        if (!empty($countfiles)) {
            echo $countfiles,'<br />';
        }

        workshop_print_feedback($course, $submission);

        print_simple_box_end();

    } else {
        print_string('notsubmittedyet', 'workshop');
    }
}

//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_feedback($course, $submission) {
    global $CFG, $RATING;

    if (! $feedbacks = get_records('workshop_assessments', 'submissionid', $submission->id)) {
        return;
    }

    $strgrade = get_string('grade');
    $strnograde = get_string('nograde');

    foreach ($feedbacks as $feedback) {
        if (! $user = get_record('user', 'id', $feedback->userid)) {
            /// Weird error but we'll just ignore it and continue with other feedback
            continue;
        }

        echo '<table cellspacing="0" class="workshop_feedbackbox">';

        echo '<tr>';
        echo '<td class="picture left">';
        print_user_picture($user->id, $course->id, $user->picture);
        echo '</td>';
        echo '<td><span class="author">'.fullname($user).'</span>';
        echo '<span class="time">'.userdate($feedback->timegraded).'</span>';
        echo '</tr>';

        echo '<tr><td class="left side">&nbsp;</td>';
        echo '<td class="content">';

        if ($feedback->grade) {
            echo $strgrade.': '.$feedback->grade;
        } else {
            echo $strnograde;
        }

        echo '<span class="comment">'.format_text($feedback->generalcomment).'</span>';
        echo '<span class="teachercomment">'.format_text($feedback->teachercomment).'</span>';
        echo '</td></tr></table>';

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
    $st_submissions = get_records_sql("SELECT DISTINCT u.id, u.id
                                       FROM {$CFG->prefix}user u,
                                            {$CFG->prefix}workshop_submissions s
                                       WHERE s.workshopid = '$workshopid' and
                                             u.id = s.userid");
    //Get students from workshop_assessments
    $st_assessments = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}workshop_assessments a
                                 WHERE a.workshopid = '$workshopid' and
                                       u.id = a.userid");

    //Get students from workshop_comments
    $st_comments = get_records_sql("SELECT DISTINCT u.id, u.id
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
function workshop_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid,
                                           $workshop="0", $user="", $groupid="") {
    // Returns all workshop posts since a given time.  If workshop is specified then
    // this restricts the results

    global $CFG;

    if ($workshop) {
        $workshopselect = " AND cm.id = '$workshop'";
    } else {
        $workshopselect = "";
    }

    if ($user) {
        $userselect = " AND u.id = '$user'";
    } else {
        $userselect = "";
    }

    $posts = get_records_sql("SELECT s.*, u.firstname, u.lastname,
            u.picture, cm.instance, w.name, cm.section, cm.groupmode,
            cm.course, cm.groupingid, cm.groupmembersonly, cm.id as cmid
            FROM {$CFG->prefix}workshop_submissions s,
            {$CFG->prefix}user u,
            {$CFG->prefix}course_modules cm,
            {$CFG->prefix}workshop w
            WHERE s.timecreated  > '$sincetime' $workshopselect
            AND s.userid = u.id $userselect
            AND w.course = '$courseid'
            AND cm.instance = w.id
            AND cm.course = w.course
            AND s.workshopid = w.id
            ORDER BY s.id");


    if (empty($posts)) {
        return;
    }

    foreach ($posts as $post) {
        if ((empty($groupid) || groups_is_member($groupid, $post->userid)) && groups_course_module_visible($post)) {

            $tmpactivity = new Object;

            $tmpactivity->type = "workshop";
            $tmpactivity->defaultindex = $index;
            $tmpactivity->instance = $post->instance;
            $tmpactivity->name = $post->name;
            $tmpactivity->section = $post->section;

            $tmpactivity->content->id = $post->id;
            $tmpactivity->content->title = $post->title;

            $tmpactivity->user->userid = $post->userid;
            $tmpactivity->user->fullname = fullname($post);
            $tmpactivity->user->picture = $post->picture;
            $tmpactivity->cmid = $post->cmid;

            $tmpactivity->timestamp = $post->timecreated;
            $activities[] = $tmpactivity;

            $index++;
        }
    }

    return;
}

//////////////////////////////////////////////////////////////////////////////////////
function workshop_print_recent_mod_activity($activity, $course, $detail=false) {

    global $CFG;

    echo '<table border="0" cellpadding="3" cellspacing="0">';

    if (!empty($activity->content->parent)) {
        $openformat = "<font size=\"2\"><i>";
        $closeformat = "</i></font>";
    } else {
        $openformat = "<b>";
        $closeformat = "</b>";
    }

    echo "<tr><td class=\"workshoppostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td>$openformat";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
            "class=\"icon\" alt=\"".strip_tags(format_string($activity->name,true))."\" />  ";
    }
    echo "<a href=\"$CFG->wwwroot/mod/workshop/submissions.php?"
        . "id=" . $activity->cmid . "&action=showsubmission&sid=".$activity->content->id."\">".$activity->content->title;
    echo "</a>$closeformat";

    echo "<br /><font size=\"2\">";
    echo "<a href=\"$CFG->wwwroot/user/view.php?id=" . $activity->user->userid . "&amp;course=" . "$course\">"
        . $activity->user->fullname . "</a>";
    echo " - " . userdate($activity->timestamp) . "</font></td></tr>";
    echo "</table>";

    return;

}


//////////////////////////////////////////////////////////////////////////////////////
// Non-standard workshop functions
///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_compare_assessments($workshop, $assessment1, $assessment2) {
    global $WORKSHOP_ASSESSMENT_COMPS, $WORKSHOP_EWEIGHTS;
    // first get the assignment elements for maxscores...
    $elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC");
    foreach ($elementsraw as $element) {
        $maxscore[] = $element->maxscore;   // to renumber index 0,1,2...
        $weight[] = $WORKSHOP_EWEIGHTS[$element->weight];   // get real value and renumber index 0,1,2...
    }

    $grades = array();
    for ($i = 0; $i < 2; $i++) {
        if ($i) {
            $rawgrades = get_records("workshop_grades", "assessmentid", $assessment1->id, "elementno ASC");
        } else {
            $rawgrades = get_records("workshop_grades", "assessmentid", $assessment2->id, "elementno ASC");
        }
        if ($rawgrades) {
            foreach ($rawgrades as $grade) {
                $grades[$i][] = $grade->grade;
            }
        }
    }
    $sumdiffs = 0;
    $sumweights = 0;
    switch ($workshop->gradingstrategy) {
        case 1 : // accumulative grading and...
        case 4 : // ...rubic grading
            for ($i=0; $i < $workshop->nelements; $i++) {
                $diff = ($grades[0][$i] - $grades[1][$i]) * $weight[$i] / $maxscore[$i];
                $sumdiffs += $diff * $diff; // use squared distances
                $sumweights += $weight[$i];
                }
            break;
        case 2 :  // error banded grading
            // ignore maxscores here, the grades are either 0 or 1,
            for ($i=0; $i < $workshop->nelements; $i++) {
                $diff = ($grades[0][$i] - $grades[1][$i]) * $weight[$i];
                $sumdiffs += $diff * $diff; // use squared distances
                $sumweights += $weight[$i];
                }
            break;
        case 3 : // criterion grading
            // here we only need to look at the difference between the "zero" grade elements
            $diff = ($grades[0][0] - $grades[1][0]) / (count($elementsraw) - 1);
            $sumdiffs = $diff * $diff;
            $sumweights = 1;
            break;
    }
    // convert to a sensible grade (always out of 100)
    $COMP = (object)$WORKSHOP_ASSESSMENT_COMPS[$workshop->assessmentcomps];
    $factor = $COMP->value;
    $gradinggrade = (($factor - ($sumdiffs / $sumweights)) / $factor) * 100;
    if ($gradinggrade < 0) {
        $gradinggrade = 0;
    }
    return $gradinggrade;
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_assessments($submission) {
    // Return the (real) assessments for this submission,
    $timenow = time();
   return count_records_select("workshop_assessments",
           "submissionid = $submission->id AND timecreated < $timenow");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_count_ungraded_assessments($workshop) {
    // function returns the number of ungraded assessments by students
    global $CFG;

    $timenow = time();
    $n = 0;
    // get all the cold assessments that have not been graded
    if ($assessments = get_records_select("workshop_assessments", "workshopid = $workshop->id AND
            (timecreated + $CFG->maxeditingtime) < $timenow AND timegraded = 0")) {
        foreach ($assessments as $assessment) {
            if (workshop_is_student($workshop, $assessment->userid)) {
                $n++;
            }
        }
    }
    return $n;
}


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
    // get the "agree" entries for this user (the assessment owner) and add the first and last names
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
                                AND a.id = ".sql_cast_char2int('l.info') ." AND s.id = a.submissionid AND s.userid = $USER->id
                                AND u.id = a.userid AND e.id = a.workshopid");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_assessments($submission, $all = '', $order = '') {
    // Return assessments for this submission ordered oldest first, newest last
    // new assessments made within the editing time are NOT returned unless they
    // belong to the user or the second argument is set to ALL
    global $CFG, $USER;

    $timenow = time();
    if (!$order) {
        $order = "timecreated DESC";
    }
    if ($all != 'ALL') {
        return get_records_select("workshop_assessments", "(submissionid = $submission->id) AND
            ((timecreated < $timenow - $CFG->maxeditingtime) or
                ((timecreated < $timenow) AND (userid = $USER->id)))", $order);
    } else {
        return get_records_select("workshop_assessments", "submissionid = $submission->id AND
            (timecreated < $timenow)", $order);
    }
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
                                AND a.id = ".sql_cast_char2int('l.info') ." AND s.id = a.submissionid AND a.userid = $USER->id
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


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_student_submissions($workshop, $order = "title") {
// Return all  ENROLLED student submissions
    global $CFG;

    if ($order == "title") {
        $order = "s.title";
        }
    if ($order == "name") {
        $order = "a.lastname, a.firstname";
        }
    if ($order == "time") {
        $order = "s.timecreated ASC";
    }

    if (!$students = workshop_get_students($workshop)) {
        return false;
    }
    $list = "(";
    foreach ($students as $student) {
        $list .= "$student->id,";
    }
    $list = rtrim($list, ',').")";

    return get_records_sql("SELECT s.* FROM {$CFG->prefix}workshop_submissions s, {$CFG->prefix}user a
                            WHERE s.userid IN $list
                              AND s.workshopid = $workshop->id
                              AND s.timecreated > 0
                              AND s.userid = a.id
                            ORDER BY $order");
}


///////////////////////////////////////////////////////////////////////////////////////////////
function workshop_get_submit_logs($course, $timestart) {
    // get the "submit" entries and add the first and last names...
    global $CFG, $USER;

    $timethen = time() - $CFG->maxeditingtime;
    return get_records_sql("SELECT l.time, l.url, u.firstname, u.lastname, l.info as workshopid, e.name
                             FROM {$CFG->prefix}log l,
                                {$CFG->prefix}workshop e,
                                {$CFG->prefix}user u
                            WHERE l.time > $timestart AND l.time < $timethen
                                AND l.course = $course->id AND l.module = 'workshop'
                                AND l.action = 'submit'
                                AND e.id = ".sql_cast_char2int('l.info') ."
                                AND u.id = l.userid");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_unmailed_assessments($cutofftime) {
    /// Return list of assessments that have not been mailed out
    global $CFG;
    return get_records_sql("SELECT a.*, g.course, g.name
                              FROM {$CFG->prefix}workshop_assessments a, {$CFG->prefix}workshop g
                             WHERE a.mailed = 0
                               AND a.timecreated < $cutofftime
                               AND g.id = a.workshopid
                               AND g.releasegrades < $cutofftime");
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
function workshop_get_user_assessments($workshop, $user) {
// Return all the  user's assessments, newest first, oldest last (hot, warm and cold ones)
    return get_records_select("workshop_assessments", "workshopid = $workshop->id AND userid = $user->id",
                "timecreated DESC");
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_get_user_submissions($workshop, $user) {
    // return real submissions of user newest first, oldest last. Ignores the dummy submissions
    // which get created to hold the final grades for users that make no submissions
    return get_records_select("workshop_submissions", "workshopid = $workshop->id AND
        userid = $user->id AND timecreated > 0", "timecreated DESC" );
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_grade_assessments($workshop, $verbose=false) {
    global $WORKSHOP_EWEIGHTS;

    // timeout after 10 minutes
    @set_time_limit(600);

    $timenow = time();

    // set minumim value for the variance (of the elements)
    $minvar = 0.05;

    // check when the standard deviations were calculated
    $oldtotalassessments = get_field("workshop_elements", "totalassessments", "workshopid", $workshop->id,
                "elementno", 0);
    $totalassessments = count_records("workshop_assessments", "workshopid", $workshop->id);
    // calculate the std. devs every 10 assessments for low numbers of assessments, thereafter every 100 new assessments
    if ((($totalassessments < 100) and (($totalassessments - $oldtotalassessments) > 10)) or
            (($totalassessments - $oldtotalassessments) > 100)) {
        // calculate the means for each submission using just the "good" assessments
        if ($submissions = get_records("workshop_submissions", "workshopid", $workshop->id)) {
            foreach ($submissions as $submission) {
                $nassessments[$submission->id] = 0;
                if ($assessments = workshop_get_assessments($submission)) {
                    foreach ($assessments as $assessment) {
                        // test if assessment is "good", a teacher assessment always "good", but may be weighted out
                        if (workshop_is_teacher($workshop, $assessment->userid)) {
                            if (!$workshop->teacherweight) {
                                // drop teacher's assessment as weight is zero
                                continue;
                            }
                        } elseif ((!$assessment->gradinggrade and $assessment->timegraded) or
                                ($workshop->agreeassessments and !$assessment->timeagreed)) {
                            // it's a duff assessment, or it's not been agreed
                            continue;
                        }
                        if (isset($num[$submission->id])) {
                            if (workshop_is_teacher($workshop, $assessment->userid)) {
                                $num[$submission->id] += $workshop->teacherweight; // weight teacher's assessment
                            } else {
                                $num[$submission->id]++; // number of assessments
                            }
                            $nassessments[$submission->id]++;
                        } else {
                            if (workshop_is_teacher($workshop, $assessment->userid)) {
                                $num[$submission->id] = $workshop->teacherweight;
                            } else {
                                $num[$submission->id] = 1;
                            }
                            $nassessments[$submission->id] = 1;
                        }
                        for ($i = 0; $i < $workshop->nelements; $i++) {
                            $grade =  get_field("workshop_grades", "grade",
                                    "assessmentid", $assessment->id, "elementno", $i);
                            if (isset($sum[$submission->id][$i])) {
                                if (workshop_is_teacher($workshop, $assessment->userid)) {
                                    $sum[$submission->id][$i] += $workshop->teacherweight * $grade; // teacher's grade
                                } else {
                                    $sum[$submission->id][$i] += $grade; // student's grade
                                }
                            } else {
                                if (workshop_is_teacher($workshop, $assessment->userid)) {
                                    $sum[$submission->id][$i] = $workshop->teacherweight * $grade; // teacher's grade
                                } else {
                                    $sum[$submission->id][$i] = $grade; // students's grade
                                }
                            }
                        }
                    }
                }
            }

            if (!isset($num)) {
                // no assessments yet
                return;
            }
            reset($num);
            // calculate the means for each submission
            $total = 0;
            foreach ($num as $submissionid => $n) {
                if ($n) { // stop division by zero
                    for ($i = 0; $i < $workshop->nelements; $i++) {
                        $mean[$submissionid][$i] = $sum[$submissionid][$i] / $n;
                        // echo "Submission: $submissionid; Element: $i; Mean: {$mean[$submissionid][$i]}<br />\n";
                    }
                    $total += $n; // weighted total
                }
            }
            if ($verbose) {
                echo "<p style=\"text-align:center\">".get_string("numberofsubmissions", "workshop", count($num))."<br />\n";
                echo get_string("numberofassessmentsweighted", "workshop", $total)."</p>\n";
            }

            // now get an estimate of the standard deviation of each element in the assessment
            // this is just a rough measure, all assessments are included and teacher's assesments are not weighted
            $n = 0;
            for ($i = 0; $i < $workshop->nelements; $i++) {
                $var[$i] = 0;
            }
            foreach ($submissions as $submission) {
                if ($assessments = workshop_get_assessments($submission)) {
                    foreach ($assessments as $assessment) {
                        $n++;
                        for ($i = 0; $i < $workshop->nelements; $i++) {
                            $grade =  get_field("workshop_grades", "grade",
                                    "assessmentid", $assessment->id, "elementno", $i);
                            $temp = $mean[$submission->id][$i] - $grade;
                            $var[$i] += $temp * $temp;
                        }
                    }
                }
            }
            for ($i = 0; $i < $workshop->nelements; $i++) {
                if ($n > 1) {
                    $sd[$i] = sqrt($var[$i] / ($n - 1));
                } else {
                    $sd[$i] = 0;
                }
                set_field("workshop_elements", "stddev", $sd[$i], "workshopid", $workshop->id, "elementno", $i);
                set_field("workshop_elements", "totalassessments", $totalassessments, "workshopid", $workshop->id,
                        "elementno", $i);
                if ($verbose) {
                    echo get_string("standarddeviationofelement", "workshop", $i+1)." $sd[$i]<br />";
                    if ($sd[$i] <= $minvar) {
                        print_string("standarddeviationnote", "workshop")."<br />\n";
                    }
                }
            }
        }
    }

    // this section looks at each submission if the number of assessments made has increased it recalculates the
    // grading grades for those assessments
    // first get the assignment elements for the weights and the stddevs...
    if ($elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC")) {
        foreach ($elementsraw as $element) {
            $weight[] = $element->weight;   // to renumber index 0,1,2...
            $sd[] = $element->stddev;   // to renumber index 0,1,2...
        }
    }

    unset($num); // may have been used in calculating stddevs
    unset($sum); // ditto
    if ($submissions = get_records("workshop_submissions", "workshopid", $workshop->id)) {
        foreach ($submissions as $submission) {
            // see if the number of assessments has changed
            $nassessments = workshop_count_assessments($submission);
            if ($submission->nassessments <> $nassessments) {
                // ...if there are three or more assessments calculate the variance of each assessment.
                // Use the variance to find the "best" assessment. (When there is only one or two assessments they
                // are not altered by this routine.)
                if ($verbose) {
                    echo "Processing submission $submission->id ($nassessments asessments)...\n";
                }
                if ($nassessments > 2) {
                    $num = 0; // weighted number of assessments
                    for ($i = 0; $i < $workshop->nelements; $i++) {
                        $sum[$i] = 0; // weighted sum of grades
                    }
                    if ($assessments = workshop_get_assessments($submission)) {
                        // first calculate the mean grades for each element
                        foreach ($assessments as $assessment) {
                            // test if assessment is "good", a teacher assessment always "good", but may be weighted out
                            if (workshop_is_teacher($workshop, $assessment->userid)) {
                                if (!$workshop->teacherweight) {
                                    // drop teacher's assessment as weight is zero
                                    continue;
                                }
                            } else if ((!$assessment->gradinggrade and $assessment->timegraded) or
                                    ($workshop->agreeassessments and !$assessment->timeagreed)) {
                                // it's a duff assessment, or it's not been agreed
                                continue;
                            }
                            if (workshop_is_teacher($workshop, $assessment->userid)) {
                                $num += $workshop->teacherweight; // weight teacher's assessment
                            } else {
                                $num++; // student assessment just add one
                            }
                            for ($i = 0; $i < $workshop->nelements; $i++) {
                                $grade =  get_field("workshop_grades", "grade",
                                        "assessmentid", $assessment->id, "elementno", $i);
                                if (workshop_is_teacher($workshop, $assessment->userid)) {
                                    $sum[$i] += $workshop->teacherweight * $grade; // teacher's grade
                                } else {
                                    $sum[$i] += $grade; // student's grade
                                }
                            }
                        }
                        if ($num) { // could all the assessments be duff?
                            for ($i = 0; $i < $workshop->nelements; $i++) {
                                $mean[$i] = $sum[$i] / $num;
                                if ($verbose) echo "Submission: $submission->id; Element: $i; Mean: {$mean[$i]}\n";
                            }
                        } else {
                            continue; // move to the next submission
                        }
                        // run through the assessments again to see which is the "best" one (the one
                        // closest to the mean)
                        $lowest = 10e9;
                        foreach ($assessments as $assessment) {
                            if ($workshop->agreeassessments and !$assessment->timeagreed) {
                                // ignore assessments that have not been agreed
                                continue;
                            }
                            $var = 0;
                            for ($i = 0; $i < $workshop->nelements; $i++) {
                                $grade =  get_field("workshop_grades", "grade",
                                        "assessmentid", $assessment->id, "elementno", $i);
                                if ($sd[$i] > $minvar) {
                                    $temp = ($mean[$i] - $grade) *
                                        $WORKSHOP_EWEIGHTS[$weight[$i]] / $sd[$i];
                                } else {
                                    $temp = 0;
                                }
                                $var += $temp * $temp;
                            }
                            // find the "best" assessment of this submission
                            if ($lowest > $var) {
                                $lowest = $var;
                                $bestassessmentid = $assessment->id;
                            }
                        }

                        if (!$best = get_record("workshop_assessments", "id", $bestassessmentid)) {
                            notify("Workshop grade assessments: cannot find best assessment");
                            continue;
                        }
                        if ($verbose) {
                            echo "Best assessment is $bestassessmentid;\n";
                        }
                        foreach ($assessments as $assessment) {
                            // don't overwrite teacher's grade
                            if ($assessment->teachergraded) {
                                continue;
                            }
                            if ($assessment->id == $bestassessmentid) {
                                // it's the best one, set the grading grade to the maximum
                                set_field("workshop_assessments", "gradinggrade", 100, "id", $assessment->id);
                                set_field("workshop_assessments", "timegraded", $timenow, "id", $assessment->id);
                            } else {
                                // it's one of the pack, compare with the best...
                                $gradinggrade = round(workshop_compare_assessments($workshop, $best, $assessment));
                                // ...and save the grade for the assessment
                                set_field("workshop_assessments", "gradinggrade", $gradinggrade, "id", $assessment->id);
                                set_field("workshop_assessments", "timegraded", $timenow, "id", $assessment->id);
                            }
                        }
                    }
                } else {
                    // there are less than 3 assessments for this submission
                    if ($assessments = workshop_get_assessments($submission)) {
                        foreach ($assessments as $assessment) {
                            if (!$assessment->timegraded and !$assessment->teachergraded) {
                                // set the grading grade to the maximum and say it's been graded
                                set_field("workshop_assessments", "gradinggrade", 100, "id", $assessment->id);
                                set_field("workshop_assessments", "timegraded", $timenow, "id", $assessment->id);
                            }
                        }
                    }
                }
                // set the number of assessments for this submission
                set_field("workshop_submissions", "nassessments", $nassessments, "id", $submission->id);
            }
        }
    }
    return;
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_gradinggrade($workshop, $student) {
    // returns the current (external) grading grade of the based on their (cold) assessments
    // (needed as it's called by grade)
    global $CFG;
    require_once(dirname(__FILE__) . '/locallib.php');

    $gradinggrade = 0;
    if ($assessments = workshop_get_user_assessments_done($workshop, $student)) {
        $n = 0;
        foreach ($assessments as $assessment) {
            $gradinggrade += $assessment->gradinggrade;
            $n++;
        }
        if ($n < ($workshop->ntassessments + $workshop->nsassessments)) { // the minimum students should do
            $n = $workshop->ntassessments + $workshop->nsassessments;
        }
        $gradinggrade = $gradinggrade / $n;
    }
    return number_format($gradinggrade * $workshop->gradinggrade / 100, 1);
}


//////////////////////////////////////////////////////////////////////////////////////
function workshop_submission_grade($workshop, $submission) {
    // returns the current (external) grade of the submission based on the "good" (cold) assessments
    // (needed as it's called by grade)

    $grade = 0;
    if ($assessments = workshop_get_assessments($submission)) {
        $n = 0;
        foreach ($assessments as $assessment) {
            if ($workshop->agreeassessments and !$assessment->timeagreed) {
                // ignore assessments which have not been agreed
                continue;
            }
            if ($assessment->gradinggrade or !$assessment->timegraded) {
                // a good assessment (or one that has not been graded yet)
                if (workshop_is_teacher($workshop, $assessment->userid)) {
                    $timenow = time();
                    if ($timenow > $workshop->releasegrades) {
                        // teacher's grade is available
                        $grade += $workshop->teacherweight * $assessment->grade;
                        $n += $workshop->teacherweight;
                    }
                } else {
                    $grade += $assessment->grade;
                    $n++;
                }
            }
        }
        if ($n) { // stop division by zero
            $grade = $grade / $n;
        }
    }
    return number_format($grade * $workshop->grade / 100, 1);
}


/////////////////////////////////////////////////////////////////////////////
function workshop_fullname($userid, $courseid) {
    global $CFG;
    if (!$user = get_record('user', 'id', $userid)) {
        return '';
    }
    return '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$courseid.'">'.
        fullname($user).'</a>';
}

function workshop_get_view_actions() {
    return array('view','view all');
}

function workshop_get_post_actions() {
    return array('agree','assess','comment','grade','newattachment','removeattachments','resubmit','submit');
}

/**
 * Returns all other caps used in module
 */
function workshop_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames');
}

/**
 * Called by course/reset.php
 * @param $mform form passed by reference
 */
function workshop_reset_course_form_definition(&$mform) {

    $mform->addElement('header', ' workshopheader', get_string('modulenameplural', 'workshop'));
    $mform->addElement('checkbox', 'reset_workshop_all', get_string('resetworkshopall','workshop'));
}

/**
 * Course reset form defaults.
 */
function workshop_reset_course_form_defaults($course) {
    return array('reset_workshop_all'=>1);
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all issued certificates from the specified course
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function workshop_reset_userdata($data) {
    global $CFG;

    $status = array();

    if (!empty($data->reset_workshop_all)) {
        $workshopids = get_records('workshop', 'course', $data->courseid, '', 'id');
        if (!empty($workshopids)) {
            $workshopidslist = implode(',', array_keys($workshopids));
            // delete all students participation info, keep assessment forms elements and stock comments
            delete_records_select('workshop_submissions', "workshopid IN ($workshopidslist)");
            delete_records_select('workshop_grades', "workshopid IN ($workshopidslist)");
            delete_records_select('workshop_comments', "workshopid IN ($workshopidslist)");
            delete_records_select('workshop_assessments', "workshopid IN ($workshopidslist)");

            set_field_select('workshop_elements', 'stddev', 0, "workshopid IN ($workshopidslist)");
            set_field_select('workshop_elements', 'totalassessments', 0, "workshopid IN ($workshopidslist)");
        }

        // delete module data (submissions)
        $basedir = $CFG->dataroot.'/'.$data->courseid.'/'.$CFG->moddata.'/workshop/';
        remove_dir("$basedir");

        // fill return info
        $status[] = array('component' => get_string('modulenameplural', 'workshop'),
                            'item' => get_string('resetworkshopall', 'workshop'), 'error' => false);
    }

    return $status;
}

?>
