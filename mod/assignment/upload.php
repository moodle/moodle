<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);          // Assignment ID

    if (! $assignment = get_record("assignment", "id", $id)) {
        error("Not a valid assignment ID");
    }

    if (! $course = get_record("course", "id", $assignment->course)) {
        error("Course is misconfigured");
    }

    if (! $cm = get_coursemodule_from_instance("assignment", $assignment->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course->id);

    $strassignments = get_string("modulenameplural", "assignment");
    $strassignment  = get_string("modulename", "assignment");
    $strupload      = get_string("upload");

    print_header_simple("$assignment->name : $strupload", "",
                 "<a href=index.php?id=$course->id>$strassignments</a> -> 
                  <a href=\"view.php?a=$assignment->id\">$assignment->name</a> -> $strupload", 
                  "", "", true);

    if ($submission = get_record("assignment_submissions", "assignment", $assignment->id, "userid", $USER->id)) {
        if ($submission->grade and !$assignment->resubmit) {
            error("You've already been graded - there's no point in uploading anything");
        }
    }

    $dir = assignment_file_area_name($assignment,$USER);
    require_once($CFG->dirroot.'/lib/uploadlib.php');
    $um = new upload_manager('newfile',true,false,$course,false,$assignment->maxbytes);
    $newfile_name = $um->get_new_filename();
    if ($um->process_file_uploads($dir)) {
        if ($submission) {
            $submission->timemodified = time();
            $submission->numfiles     = 1;
            $submission->comment = addslashes($submission->comment);
            if (update_record("assignment_submissions", $submission)) {
                print_heading(get_string('uploadedfile'));
            } else {
                notify(get_string("uploadfailnoupdate", "assignment"));
            }
        } else {
            $newsubmission->assignment   = $assignment->id;
            $newsubmission->userid       = $USER->id;
            $newsubmission->timecreated  = time();
            $newsubmission->timemodified = time();
            $newsubmission->numfiles     = 1;
            if (insert_record("assignment_submissions", $newsubmission)) {
                add_to_log($course->id, "assignment", "upload", "view.php?a=$assignment->id", "$assignment->id", $cm->id);
                print_heading(get_string('uploadedfile'));
            } else {
                notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
            }
        } 
    }
    // upload class will take care of printing out errors.

    print_continue("view.php?a=$assignment->id");

    print_footer($course);

?>
