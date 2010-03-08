<?php
/**
 * Sets up the tabs used by the quiz pages based on the users capabilites.
 *
 * @author Tim Hunt and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */
global $DB, $OUTPUT;
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
$stredit=get_string('edit');
if (has_capability('mod/quiz:view', $context)) {
    $row[] = new tabobject('info', "$CFG->wwwroot/mod/quiz/view.php?id=$cm->id", get_string('info', 'quiz'));
}
if (has_capability('mod/quiz:viewreports', $context)) {
    $row[] = new tabobject('reports', "$CFG->wwwroot/mod/quiz/report.php?q=$quiz->id", get_string('results', 'quiz'));
}
if (has_capability('mod/quiz:preview', $context)) {
    $strpreview = get_string('preview', 'quiz');
    $row[] = new tabobject('preview', "$CFG->wwwroot/mod/quiz/startattempt.php?cmid=$cm->id&amp;sesskey=" . sesskey(), "<img src=\"" . $OUTPUT->pix_url('t/preview') . "\" class=\"iconsmall\" alt=\"$strpreview\" /> $strpreview", $strpreview);
}
if (has_capability('mod/quiz:manage', $context)) {
    $row[] = new tabobject('edit', "$CFG->wwwroot/mod/quiz/edit.php?cmid=$cm->id", "<img src=\"" . $OUTPUT->pix_url('t/edit') . "\" class=\"iconsmall\" alt=\"$stredit\" /> $stredit",$stredit);
}
if (has_capability('mod/quiz:manageoverrides', $context)) {
    $row[] = new tabobject('overrides', "$CFG->wwwroot/mod/quiz/overrides.php?cmid=$cm->id", get_string('overrides', 'quiz'));
}

if ($currenttab == 'info' && count($row) == 1) {
    // Don't show only an info tab (e.g. to students).
} else {
    //$reports is passed in from report.php
    $tabs[] = $row;
}

if ($currenttab == 'reports' and isset($mode)) {
    $activated[] = 'reports';



    $row  = array();
    $currenttab = '';

    $reportlist = quiz_report_list($context);

    foreach ($reportlist as $report) {
        $row[] = new tabobject($report, "$CFG->wwwroot/mod/quiz/report.php?q=$quiz->id&amp;mode=$report",
                                get_string($report, 'quiz_'.$report));
        if ($report == $mode) {
            $currenttab = $report;
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
        $row[] = new tabobject('editq', "$CFG->wwwroot/mod/quiz/edit.php?cmid=$cm->id", $stredit, $streditingquiz);
        $row[] = new tabobject('reorder', "$CFG->wwwroot/mod/quiz/edit.php?reordertool=1&amp;cmid=$cm->id", get_string('orderandpaging','quiz'), $streditingquiz);
    }
    //questionbank_navigation_tabs($row, $contexts, $thispageurl->params());
    $tabs[] = $row;

}

if ($currenttab == 'overrides' and isset($mode)) {
    $activated[] = 'overrides';

    $row  = array();
    $currenttab = $mode;

    $strgroup = get_string('groupoverrides', 'quiz');
    $struser = get_string('useroverrides', 'quiz');

    $row[] = new tabobject('group', "$CFG->wwwroot/mod/quiz/overrides.php?cmid=$cm->id", $strgroup);
    $row[] = new tabobject('user', "$CFG->wwwroot/mod/quiz/overrides.php?cmid=$cm->id&amp;mode=user", $struser);
    $tabs[] = $row;
}

if (!$quiz->questions) {
    $inactive += array('info', 'reports', 'preview');
}

print_tabs($tabs, $currenttab, $inactive, $activated);


