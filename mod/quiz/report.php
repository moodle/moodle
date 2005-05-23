<?php  // $Id$

// This script uses installed report plugins to print quiz reports

    require_once("../../config.php");
    require_once("locallib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID

    optional_variable($mode, "simplestat");        // Report mode

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id, false);

    if (!isteacher($course->id)) {
        error("You are not allowed to use this script");
    }

    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and isteacheredit($course->id)) {
        redirect('edit.php?quizid='.$quiz->id);
    }

    // Upgrade any attempts that have not yet been upgraded to the 
    // Moodle 1.5 model (they will not yet have the timestamp set)
    if ($attempts = get_records_sql("SELECT a.*".
           "  FROM {$CFG->prefix}quiz_attempts a, {$CFG->prefix}quiz_states s".
           " WHERE a.quiz = '$quiz->id' AND s.attempt = a.id AND s.timestamp = 0")) {
        foreach ($attempts as $attempt) {
            quiz_upgrade_states($attempt);
        }
    }

    add_to_log($course->id, "quiz", "report", "report.php?id=$cm->id", "$quiz->id", "$cm->id");


/* Code moved into each plugin report.php 
/// Define some strings

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");

/// Print the page header

    print_header_simple(format_string($quiz->name), "",
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>
                  -> ".format_string($quiz->name),
                 "", "", true, update_module_button($cm->id, $course->id, $strquiz), navmenu($course, $cm));

/// Print the tabs

    $currenttab = 'reports';
    include('tabs.php');
*/

/// Open the selected quiz report and display it

    $mode = clean_filename($mode);

    if (! is_readable("report/$mode/report.php")) {
        error("Report not known (".clean_text($mode).")");
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
