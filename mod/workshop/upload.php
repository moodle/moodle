<?php  // $Id: upload.php, v1.0 30th April 2003

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    require_variable($id);          // CM ID


    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }
    if (! $workshop = get_record("workshop", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strupload      = get_string("upload");

    print_header_simple("$workshop->name : $strupload", "",
                 "<a href=\"index.php?id=$course->id\">$strworkshops</a> -> 
                  <a href=\"view.php?a=$workshop->id\">$workshop->name</a> -> $strupload", 
                  "", "", true);
 /****
    if ($submissions = workshop_get_submissions($workshop, $USER)) {
        if ($submission->grade and !$workshop->resubmit) {
            error("You've already been graded - there's no point in uploading anything");
        }
    }
****/
    $timenow = time();
    if (!$title = $_POST['title']) {
        notify(get_string("notitlegiven", "workshop") );
    }
    else {  
        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager('newfile',false,false,$course,false,$exercise->maxbytes);
        if ($um->preprocess_files()) {
            // get the current set of submissions
            $submissions = workshop_get_user_submissions($workshop, $USER);
            // add new submission record
            $newsubmission->workshopid   = $workshop->id;
            $newsubmission->userid         = $USER->id;
            $newsubmission->title  = $title;
            $newsubmission->timecreated  = time();
            if (!$newsubmission->id = insert_record("workshop_submissions", $newsubmission)) {
                error("Workshop upload: Failure to create new submission record!");
            }
            // see if this is a resubmission by looking at the previous submissions...
            if ($submissions and ($workshop->phase >1)) { // ...but not teacher submissions
                // find the last submission
                foreach ($submissions as $submission) {
                    $lastsubmission = $submission;
                    break;
                }
                // find all the possible assessments of this submission
                // ...and if they have been assessed give the assessor a new assessment
                // based on their old assessment, if the assessment has not be made
                // just delete it!
                if ($assessments = workshop_get_assessments($submission, 'ALL')) {
                    foreach ($assessments as $assessment) {
                        if ($assessment->timecreated < $timenow) {
                            // a Cold or Warm assessment - copy it with feedback..
                            $newassessment = workshop_copy_assessment($assessment, $newsubmission, 
                                                                      true);
                            // set the resubmission flag so student can be emailed/told about 
                            // this assessment
                            set_field("workshop_assessments", "resubmission", 1, "id",
                                      $newassessment->id);
                        } else {
                            // a hot assessment, was not used, just dump it
                            delete_records("workshop_assessments", "id", $assessment->id);
                        }
                    }
                }
                add_to_log($course->id, "workshop", "resubmit", "view.php?id=$cm->id", 
                           "$workshop->id","$cm->id");
            }
            $dir = workshop_file_area_name($workshop, $newsubmission);
            if ($um->save_files($dir)) {
                print_heading(get_string("uploadsuccess", "assignment", $um->get_new_filename()) );
                add_to_log($course->id, "workshop", "submit", "view.php?id=$cm->id", "$workshop->id", "$cm->id");
            }
            // um will take care of printing errors.
        }
	}
    print_continue("view.php?id=$cm->id");

    print_footer($course);

?>
