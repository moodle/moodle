<?php

////////////////////////////////////////////////////////////////////
/// Default class for report plugins
///
/// Doesn't do anything on it's own -- it needs to be extended.
/// This class displays quiz reports.  Because it is called from
/// within /mod/quiz/report.php you can assume that the page header
/// and footer are taken care of.
///
/// This file can refer to itself as report.php to pass variables
/// to itself - all these will also be globally available.  You must
/// pass "id=$cm->id" or q=$quiz->id", and "mode=reportname".
////////////////////////////////////////////////////////////////////

// Included by ../report.php

class quiz_default_report {

    function display($cm, $course, $quiz) {     /// This function just displays the report
        return true;
    }

    function print_header_and_tabs($cm, $course, $quiz, $reportmode="overview") {
        global $CFG, $PAGE, $OUTPUT;
    /// Define some strings
        $strquizzes = get_string("modulenameplural", "quiz");
        $strquiz  = get_string("modulename", "quiz");
    /// Print the page header
        $PAGE->set_title(format_string($quiz->name));
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        $course_context = get_context_instance(CONTEXT_COURSE, $course->id);
    }
}


