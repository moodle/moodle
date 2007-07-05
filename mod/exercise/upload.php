<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");

    $id    = required_param('id', PARAM_INT);           // course module ID
    $title = optional_param('title', '', PARAM_CLEAN);

    $timenow = time();

    // get some esential stuff...
    if (! $cm = get_coursemodule_from_id('exercise', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $exercise = get_record("exercise", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise  = get_string("modulename", "exercise");
    $strupload      = get_string("upload");

    $navlinks = array();
    $navlinks[] = array('name' => $strexercises, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($exercise->name), 'link' => "view.php?id=$cm->id", 'type' => 'activityinstance');
    $navlinks[] = array('name' => $strupload, 'link' => '', 'type' => 'title');
    $navigation = build_navigation($navlinks);

    print_header_simple(format_string($exercise->name)." : $strupload", "", $navigation,
                  "", "", true);

    // check that this is not a "rapid" second submission, caused by using the back button
    // only check if a student, teachers may want to submit a set of exercise variants
    if (isstudent($course->id)) {
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) {
            // returns all submissions, newest on first
            foreach ($submissions as $submission) {
                if ($submission->timecreated > $timenow - $CFG->maxeditingtime) {
                    // ignore this submission
                    redirect("view.php?id=$cm->id");
                    print_footer($course);
                    exit();
                }
            }
        }
    }

    // check existence of title
    if ($title == '') {
        notify(get_string("notitlegiven", "exercise") );
    }
    else {
        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager('newfile',false,false,$course,false,$exercise->maxbytes);
        if ($um->preprocess_files()) {
            $newsubmission->exerciseid   = $exercise->id;
            if (isteacher($course->id)) {
                // it's an exercise submission, flag it as such
                $newsubmission->userid         = 0;
                $newsubmission->isexercise = 1;  // it's a description of an exercise
            }
            else {
                $newsubmission->userid = $USER->id;
            }
            $newsubmission->title  = $title;
            $newsubmission->timecreated  = $timenow;
            if ($timenow > $exercise->deadline) {
                $newsubmission->late = 1;
            }
            if (!$newsubmission->id = insert_record("exercise_submissions", $newsubmission)) {
                error("exercise upload: Failure to create new submission record!");
            }
            $dir = exercise_file_area_name($exercise, $newsubmission);
            if ($um->save_files($dir)) {
                add_to_log($course->id, "exercise", "submit", "view.php?id=$cm->id", "$exercise->id");
                print_heading(get_string("uploadsuccess", "assignment", $um->get_new_filename()) );
            }
            // upload manager will print errors.
            // clear resubmit flags
            if (!set_field("exercise_submissions", "resubmit", 0, "exerciseid", $exercise->id, "userid", $USER->id)) {
                error("Exercise Upload: unable to reset resubmit flag");
            }
        }
        // upload manager will print errors.
    }
    print_continue("view.php?id=$cm->id");

    print_footer($course);

?>
