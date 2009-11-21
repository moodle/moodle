<?php  // $Id$

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    $id = required_param('id', PARAM_INT);          // CM ID


    if (! $cm = get_coursemodule_from_id('workshop', $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }
    if (! $workshop = get_record("workshop", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);

    $strworkshops = get_string('modulenameplural', 'workshop');
    $strworkshop = get_string('modulename', 'workshop');
    $strsubmission = get_string('submission', 'workshop');

    $navigation = build_navigation($strsubmission, $cm);
    print_header_simple(format_string($workshop->name)." : $strsubmission", "", $navigation,
                  "", "", true);
    $timenow = time();

    $form = data_submitted("nomatch"); // POST may come from two forms

    // don't be picky about not having a title
    if (!$title = $form->title) {
        $title = get_string("notitle", "workshop");
    }

    // check that this is not a "rapid" second submission, caused by using the back button
    // only check if a student, teachers may want to submit a set of workshop examples rapidly
    if (workshop_is_student($workshop)) {
        if ($submissions = workshop_get_user_submissions($workshop, $USER)) {
            // returns all submissions, newest on first
            foreach ($submissions as $submission) {
                if ($submission->timecreated > $timenow - $CFG->maxeditingtime) {
                    // ignore this new submission
                    redirect("view.php?id=$cm->id");
                    print_footer($course);
                    exit();
                }
            }
        }
    }

    // get the current set of submissions
    $submissions = workshop_get_user_submissions($workshop, $USER);
    // add new submission record
    $newsubmission->workshopid  = $workshop->id;
    $newsubmission->userid      = $USER->id;
    $newsubmission->title       = clean_param($title, PARAM_CLEAN);
    $newsubmission->description = trim(clean_param($form->description, PARAM_CLEAN));
    $newsubmission->timecreated = $timenow;
    if ($timenow > $workshop->submissionend) {
        $newsubmission->late = 1;
    }
    if (!$newsubmission->id = insert_record("workshop_submissions", $newsubmission)) {
        error("Workshop submission: Failure to create new submission record!");
    }
    // see if this is a resubmission by looking at the previous submissions...
    if ($submissions and ($workshop->submissionstart > time())) { // ...but not teacher submissions
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
                    // a Cold or Warm assessment...
                    if ($assessment->userid <> $USER->id) {
                        // only copy other students assessment not the self assessment (if present)
                        // copy it with feedback..
                        $newassessment = workshop_copy_assessment($assessment, $newsubmission, true);
                        // set the resubmission flag so student can be emailed/told about
                        // this assessment
                        set_field("workshop_assessments", "resubmission", 1, "id", $newassessment->id);
                    }
                } else {
                    // a hot assessment, was not used, just dump it
                    delete_records("workshop_assessments", "id", $assessment->id);
                }
            }
        }
        add_to_log($course->id, "workshop", "resubmit", "view.php?id=$cm->id", "$workshop->id","$cm->id");
    }
    // do something about the attachments, if there are any
    if ($workshop->nattachments) {
        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager(null,false,false,$course,false,$workshop->maxbytes);
        if ($um->preprocess_files()) {
            $dir = workshop_file_area_name($workshop, $newsubmission);
            if ($um->save_files($dir)) {
                print_heading(get_string("uploadsuccess", "workshop"));
            }
        // um will take care of printing errors.
        }
    }
    if (!$workshop->nattachments) {
        print_heading(get_string("submitted", "workshop")." ".get_string("ok"));
    }
    add_to_log($course->id, "workshop", "submit", "view.php?id=$cm->id", "$workshop->id", "$cm->id");
    print_continue("view.php?id=$cm->id");
    print_footer($course);

?>
