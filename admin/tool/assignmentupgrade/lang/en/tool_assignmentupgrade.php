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
 * Strings for the assignment upgrade tool
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['areyousure'] = 'Are you sure?';
$string['areyousuremessage'] = 'Are you sure you want to upgrade the assignment "{$a->name}"?';
$string['assignmentid'] = 'Assignment ID';
$string['assignmentnotfound'] = 'Assignment could not be found (id={$a})';
$string['assignmentsperpage'] = 'Assignments per page';
$string['assignmenttype'] = 'Assignment type';
$string['backtoindex'] = 'Back to index';
$string['batchoperations'] = 'Batch operations';
$string['batchupgrade'] = 'Upgrade multiple assignments';
$string['confirmbatchupgrade'] = 'Confirm batch upgrade assignments';
$string['conversioncomplete'] = 'Assignment converted';
$string['conversionfailed'] = 'The assignment conversion was not successful. The log from the upgrade was: <br />{$a}';
$string['listnotupgraded'] = 'List assignments that have not been upgraded';
$string['listnotupgraded_desc'] = 'You can upgrade individual assignments from here';
$string['noassignmentsselected'] = 'No assignments selected';
$string['noassignmentstoupgrade'] = 'There are no assignments that require upgrading';
$string['notsupported'] = '';
$string['notupgradedintro'] = 'This page lists the assignments created with an older version of Moodle that have not been upgraded to the new assignment module in Moodle 2.3. Not all assignments can be upgraded - if they were created with a custom assignment subtype, then that subtype will need to be upgraded to the new assignment plugin format in order to complete the upgrade.';
$string['notupgradedtitle'] = 'Assignments not upgraded';
$string['pluginname'] = 'Assignment upgrade helper';
$string['select'] = 'Select';
$string['submissions'] = 'Submissions';
$string['supported'] = 'Upgrade';
$string['updatetable'] = 'Update table';
$string['unknown'] = 'Unknown';
$string['upgradeassignmentsummary'] = 'Upgrade assignment: {$a->name} (Course: {$a->shortname})';
$string['upgradeassignmentsuccess'] = 'Result: Upgrade successful';
$string['upgradeassignmentfailed'] = 'Result: Upgrade failed. The log from the upgrade was: <br/><div class="tool_assignmentupgrade_upgradelog">{$a->log}</div>';
$string['upgradable'] = 'Upgradable';
$string['upgradeselected'] = 'Upgrade selected assignments';
$string['upgradeselectedcount'] = 'Upgrade {$a} selected assignments?';
$string['upgradeall'] = 'Upgrade all assignments';
$string['upgradeallconfirm'] = 'Upgrade all assignments?';
$string['upgradeprogress'] = 'Upgrade assignment {$a->current} of {$a->total}';
$string['upgradesingle'] = 'Upgrade single assignment';
$string['viewcourse'] = 'View the course with the converted assignment';
$string['privacy:metadata:preference:perpage'] = 'The assignment upgrade records per page preference set for the user.';
