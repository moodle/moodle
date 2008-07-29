<?php  // $Id$
/**
 * Sets up the tabs used by the quiz pages based on the users capabilites.
 *
 * @author Tim Hunt and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */
global $DB;
if (empty($quiz)) {
    if (empty($attemptobj)) {
        print_error('cannotcallscript');
    }
    $quiz = $attemptobj->get_quiz();
    $cm = $attemptobj->get_cm();
}
if (!isset($currenttab)) {
    $currenttab = '';
}
if (!isset($cm)) {
    $cm = get_coursemodule_from_instance('quiz', $quiz->id);
}


$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (!isset($contexts)){
    $contexts = new question_edit_contexts($context);
}
$tabs = array();
$row  = array();
$inactive = array();
$activated = array();

if (has_capability('mod/quiz:view', $context)) {
    $row[] = new tabobject('info', "$CFG->wwwroot/mod/quiz/view.php?id=$cm->id", get_string('info', 'quiz'));
}
if (has_capability('mod/quiz:viewreports', $context)) {
    $row[] = new tabobject('reports', "$CFG->wwwroot/mod/quiz/report.php?q=$quiz->id", get_string('results', 'quiz'));
}
if (has_capability('mod/quiz:preview', $context)) {
    $row[] = new tabobject('preview', "$CFG->wwwroot/mod/quiz/startattempt.php?cmid=$cm->id&amp;sesskey=" . sesskey(), get_string('preview', 'quiz'));
}
if (has_capability('mod/quiz:manage', $context)) {
    $row[] = new tabobject('edit', "$CFG->wwwroot/mod/quiz/edit.php?cmid=$cm->id", get_string('edit'));
}

if ($currenttab == 'info' && count($row) == 1) {
    // Don't show only an info tab (e.g. to students).
} else {
    $tabs[] = $row;
}

if ($currenttab == 'reports' and isset($mode)) {
    $activated[] = 'reports';

    // Standard reports we want to show first.
    $reportrs = $DB->get_recordset('quiz_report', null, 'displayorder DESC', 'id, name');
    // Reports that are restricted by capability.
    $reportrestrictions = array(
        'regrade' => 'mod/quiz:grade',
        'grading' => 'mod/quiz:grade'
    );
    $reportdirs = get_list_of_plugins("mod/quiz/report");
    //order the reports tab in descending order of displayorder
    $reportlist = array();
    foreach ($reportrs as $key => $rs) {
        if (in_array($rs->name, $reportdirs)) {
            $reportlist[]=$rs->name;
        }
    }

    //add any other reports on the end
    foreach ($reportdirs as $report) {
        if (!in_array($report, $reportlist)) {
            $reportlist[]=$report;
        }
    }

    $row  = array();
    $currenttab = '';
    foreach ($reportlist as $report) {
        if (!isset($reportrestrictions[$report]) || has_capability($reportrestrictions[$report], $context)) {
            $row[] = new tabobject($report, "$CFG->wwwroot/mod/quiz/report.php?q=$quiz->id&amp;mode=$report",
                                    get_string($report, 'quiz_'.$report));
            if ($report == $mode) {
                $currenttab = $report;
            }
        }
    }
    $tabs[] = $row;
}

if ($currenttab == 'edit' and isset($mode)) {
    $activated[] = 'edit';

    $row  = array();
    $currenttab = $mode;

    $strquiz = get_string('modulename', 'quiz');
    $streditingquiz = get_string("editinga", "moodle", $strquiz);

    if (has_capability('mod/quiz:manage', $context) && $contexts->have_one_edit_tab_cap('editq')) {
        $row[] = new tabobject('editq', "$CFG->wwwroot/mod/quiz/edit.php?".$thispageurl->get_query_string(), $strquiz, $streditingquiz);
    }
    questionbank_navigation_tabs($row, $contexts, $thispageurl->get_query_string());
    $tabs[] = $row;

}

if (!$quiz->questions) {
    $inactive += array('info', 'reports', 'preview');
}

print_tabs($tabs, $currenttab, $inactive, $activated);

?>
