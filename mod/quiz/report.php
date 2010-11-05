<?php

// This script uses installed report plugins to print quiz reports

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/quiz/locallib.php');
    require_once($CFG->dirroot.'/mod/quiz/report/reportlib.php');

    $id = optional_param('id',0,PARAM_INT);    // Course Module ID, or
    $q = optional_param('q',0,PARAM_INT);     // quiz ID

    $mode = optional_param('mode', '', PARAM_ALPHA);        // Report mode

    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
            print_error('invalidcoursemodule');
        }

        if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
            print_error('coursemisconf');
        }

        if (! $quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
            print_error('invalidcoursemodule');
        }

    } else {
        if (! $quiz = $DB->get_record('quiz', array('id' => $q))) {
            print_error('invalidquizid', 'quiz');
        }
        if (! $course = $DB->get_record('course', array('id' => $quiz->course))) {
            print_error('invalidcourseid');
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    }

    $url = new moodle_url('/mod/quiz/report.php', array('id' => $cm->id));
    if ($mode !== '') {
        $url->param('mode', $mode);
    }
    $PAGE->set_url($url);

    require_login($course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $PAGE->set_pagelayout('report');

    $reportlist = quiz_report_list($context);
    if (count($reportlist)==0){
        print_error('erroraccessingreport', 'quiz');
    }
    if ($mode == '') {
        // Default to first accessible report and redirect.
        $url->param('mode', reset($reportlist));
        redirect($url);
    } else if (!in_array($mode, $reportlist)){
        print_error('erroraccessingreport', 'quiz');
    }

    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and has_capability('mod/quiz:manage', $context)) {
        redirect('edit.php?cmid=' . $cm->id);
    }

    // Upgrade any attempts that have not yet been upgraded to the
    // Moodle 1.5 model (they will not yet have the timestamp set)
    if ($attempts = $DB->get_records_sql("SELECT a.*".
           "  FROM {quiz_attempts} a, {question_states} s".
           " WHERE a.quiz = ? AND s.attempt = a.uniqueid AND s.timestamp = 0", array($quiz->id))) {
        foreach ($attempts as $attempt) {
            quiz_upgrade_states($attempt);
        }
    }

    add_to_log($course->id, "quiz", "report", "report.php?id=$cm->id", "$quiz->id", "$cm->id");

/// Open the selected quiz report and display it

    if (!is_readable("report/$mode/report.php")) {
        print_error('reportnotfound', 'quiz', '', $mode);
    }

    include("report/default.php"); // Parent class
    include("report/$mode/report.php");

    $reportclassname = "quiz_{$mode}_report";
    $report = new $reportclassname();

    if (!$report->display($quiz, $cm, $course)) { // Run the report!
        print_error("preprocesserror", 'quiz');
    }

/// Print footer

    echo $OUTPUT->footer();


