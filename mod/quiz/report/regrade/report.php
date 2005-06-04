<?php  // $Id$

// This script regrades all attempts at this quiz

    require_once($CFG->libdir.'/tablelib.php');

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report
        global $CFG, $SESSION, $db, $QUIZ_QTYPES;

    /// Print header
        $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="regrade");

        notify('Not yet implemented');

        return true;
    }
}

?>
