<?php  // $Id$

// This script uses installed report plugins to print quiz reports

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/quiz/locallib.php');
    require_once($CFG->dirroot.'/mod/quiz/report/reportlib.php');

    $id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
    $q = optional_param('q',0,PARAM_INT);     // quiz ID

    $mode = optional_param('mode', 'overview', PARAM_ALPHA);        // Report mode

    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
            error("There is no coursemodule with id $id");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("The quiz with id $cm->instance corresponding to this coursemodule $id is missing");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("There is no quiz with id $q");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("The course with id $quiz->course that the quiz with id $q belongs to is missing");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("The course module for the quiz with id $q is missing");
        }
    }

    require_login($course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/quiz:viewreports', $context);

    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and has_capability('mod/quiz:manage', $context)) {
        redirect('edit.php?cmid='.$cm->id);
    }

    // Upgrade any attempts that have not yet been upgraded to the
    // Moodle 1.5 model (they will not yet have the timestamp set)
    if ($attempts = get_records_sql("SELECT a.*".
           "  FROM {$CFG->prefix}quiz_attempts a, {$CFG->prefix}question_states s".
           " WHERE a.quiz = '$quiz->id' AND s.attempt = a.uniqueid AND s.timestamp = 0")) {
        foreach ($attempts as $attempt) {
            quiz_upgrade_states($attempt);
        }
    }

    add_to_log($course->id, "quiz", "report", "report.php?id=$cm->id", "$quiz->id", "$cm->id");

/// Open the selected quiz report and display it

    $mode = clean_param($mode, PARAM_SAFEDIR);

    if (! is_readable("report/$mode/report.php")) {
        error("Report not known ($mode)");
    }

    include("report/default.php");  // Parent class
    include("report/$mode/report.php");

    $report = new quiz_report();

    if (! $report->display($quiz, $cm, $course)) {             // Run the report!
        error("Error occurred during pre-processing!");
    }

/// Print footer

    print_footer($course);

?>
