<?php  // $Id$
/// This file to be included so we can assume config.php has already been included.

    if (empty($quiz)) {
        error('You cannot call this script in that way');
    }
    if (!isset($currenttab)) {
        $currenttab = '';
    }
    if (!isset($cm)) {
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
    }
    if (!isset($course)) {
        $course = get_record('course', 'id', $quiz->course);
    }

    //print_heading(format_string($quiz->name));

    $tabs = array();
    $row  = array();
    $inactive = array();

    $row[] = new tabobject('info', "view.php?q=$quiz->id", get_string('info', 'quiz'));
    $row[] = new tabobject('attempts', "attempts.php?q=$quiz->id", get_string('attempts', 'quiz'));
    $row[] = new tabobject('preview', "attempt.php?q=$quiz->id", get_string('preview', 'quiz'));
    if (isteacheredit($course->id)) {
        $row[] = new tabobject('edit', "edit.php?quizid=$quiz->id", get_string('editquiz', 'quiz'));
        //$row[] = new tabobject('update', "$CFG->wwwroot/course/mod.php?update=$cm->id&amp;sesskey=$USER->sesskey", get_string('updatethis', '', get_string('modulename', 'quiz')));
    }
    $row[] = new tabobject('reports', "report.php?q=$quiz->id", get_string('reports', 'quiz'));

    $tabs[] = $row;

    if ($currenttab == 'reports' and isset($mode)) {
        $inactive[] = 'reports';
        $allreports = get_list_of_plugins("mod/quiz/report");
        $reportlist = array ('simplestat', 'fullstat');   // Standard reports we want to show first

        foreach ($allreports as $report) {
            if (!in_array($report, $reportlist)) {
                $reportlist[] = $report;
            }
        }

        $row  = array();
        $currenttab = '';
        foreach ($reportlist as $report) {
            $row[] = new tabobject($report, "report.php?q=$quiz->id&amp;mode=$report",
                                    get_string("report$report", "quiz"));
            if ($report == $mode) {
                $currenttab = $report;
            }
        }
        $tabs[] = $row;
    }

    print_tabs($tabs, $currenttab, $inactive);

?>
