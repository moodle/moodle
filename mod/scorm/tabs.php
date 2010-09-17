<?php
/**
 * Sets up the tabs used by the scorm pages based on the users capabilities.
 *
 * @author Dan Marsden and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package scorm
 */

if (empty($scorm)) {
    error('You cannot call this script in that way');
}
if (!isset($currenttab)) {
    $currenttab = '';
}
if (!isset($cm)) {
    $cm = get_coursemodule_from_instance('scorm', $scorm->id);
}


$contextmodule = get_context_instance(CONTEXT_MODULE, $cm->id);

$tabs = array();
$row  = array();
$inactive = array();
$activated = array();

if (has_capability('mod/scorm:savetrack', $contextmodule)) {
	$row[] = new tabobject('info', "$CFG->wwwroot/mod/scorm/view.php?id=$cm->id", get_string('info', 'scorm'));
}
if (has_capability('mod/scorm:viewreport', $contextmodule)) {
    $row[] = new tabobject('reports', "$CFG->wwwroot/mod/scorm/report.php?id=$cm->id", get_string('results', 'scorm'));
}

if ($currenttab == 'info' && count($row) == 1) {
    // Don't show only an info tab (e.g. to students).
} else {
    $tabs[] = $row;
}

print_tabs($tabs, $currenttab, $inactive, $activated);
