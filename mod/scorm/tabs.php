<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Sets up the tabs used by the scorm pages based on the users capabilities.
 *
 * @author Dan Marsden and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_scorm
 */

if (empty($scorm)) {
    print_error('cannotaccess', 'mod_scorm');
}
if (!isset($currenttab)) {
    $currenttab = '';
}
if (!isset($cm)) {
    $cm = get_coursemodule_from_instance('scorm', $scorm->id);
}

$contextmodule = context_module::instance($cm->id);

$tabs = array();
$row = array();
$inactive = array();
$activated = array();

if (has_capability('mod/scorm:savetrack', $contextmodule)) {
    $row[] = new tabobject('info', "$CFG->wwwroot/mod/scorm/view.php?id=$cm->id", get_string('info', 'scorm'));
}
if (has_capability('mod/scorm:viewreport', $contextmodule)) {
    $row[] = new tabobject('reports', "$CFG->wwwroot/mod/scorm/report.php?id=$cm->id", get_string('reports', 'scorm'));
}

if (!($currenttab == 'info' && count($row) == 1)) {
    $tabs[] = $row;
}

if ($currenttab == 'reports' && !empty($reportlist) && count($reportlist) > 1) {
    $row2 = array();
    foreach ($reportlist as $rep) {
        $row2[] = new tabobject('scorm_'.$rep, $CFG->wwwroot."/mod/scorm/report.php?id=$cm->id&mode=$rep",
                                get_string('pluginname', 'scormreport_'.$rep));
    }
    $tabs[] = $row2;
}

print_tabs($tabs, $currenttab, $inactive, $activated);
