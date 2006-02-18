<?php  // $Id$
/**
* Sets up the tabs used by the quiz pages for teachers.
*
* @version $Id$
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

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
    $row[] = new tabobject('reports', "report.php?q=$quiz->id", get_string('reports', 'quiz'));
    $row[] = new tabobject('preview', "attempt.php?q=$quiz->id", get_string('preview', 'quiz'), get_string('previewquiz', 'quiz', format_string($quiz->name)));
    if (isteacheredit($course->id)) {
        $row[] = new tabobject('edit', "edit.php?quizid=$quiz->id", get_string('edit'), get_string('editquizquestions', 'quiz'));
        $row[] = new tabobject('manualgrading', "grading.php?quizid=$quiz->id", get_string("manualgrading", "quiz")); 
    }

    $tabs[] = $row;

    if ($currenttab == 'reports' and isset($mode)) {
        $inactive[] = 'reports';
        $allreports = get_list_of_plugins("mod/quiz/report");
        $reportlist = array ('overview', 'regrade', 'analysis');   // Standard reports we want to show first

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

    if ($currenttab == 'edit' and isset($mode)) {
        $inactive[] = 'edit';

        $row  = array();
        $currenttab = $mode;
        
        $row[] = new tabobject('editq', "edit.php?quizid=$quiz->id", get_string('questions', 'quiz'), get_string('editquizquestions', 'quiz'));
        $row[] = new tabobject('categories', "category.php?id=$course->id", get_string('categories', 'quiz'), get_string('editqcats', 'quiz'));
        $row[] = new tabobject('import', "import.php?course=$course->id", get_string('import', 'quiz'), get_string('importquestions', 'quiz'));
        $row[] = new tabobject('export', "export.php?courseid=$course->id", get_string('export', 'quiz'), get_string('exportquestions', 'quiz'));
        $row[] = new tabobject('update', "$CFG->wwwroot/course/mod.php?update=$cm->id&amp;sesskey=$USER->sesskey", get_string('settings'), get_string('updatesettings'));

        $tabs[] = $row;
    }

    print_tabs($tabs, $currenttab, $inactive);

?>
