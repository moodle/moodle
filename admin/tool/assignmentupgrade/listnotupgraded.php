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
 * Script to show all the assignments that have not been upgraded after the main upgrade.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/upgradableassignmentstable.php');
require_once(dirname(__FILE__) . '/upgradableassignmentsbatchform.php');
require_once($CFG->libdir . '/adminlib.php');

// admin_externalpage_setup calls require_login and checks moodle/site:config
admin_externalpage_setup('assignmentupgrade', '', array(), tool_assignmentupgrade_url('listnotupgraded'));
$PAGE->navbar->add(get_string('listnotupgraded', 'tool_assignmentupgrade'));

$renderer = $PAGE->get_renderer('tool_assignmentupgrade');

$perpage = get_user_preferences('tool_assignmentupgrade_perpage', 5);
$assignments = new tool_assignmentupgrade_assignments_table($perpage);

$batchform = new tool_assignmentupgrade_batchoperations_form();
$data = $batchform->get_data();
if ($data && $data->selectedassignments != '' || $data && isset($data->upgradeall)) {
    echo $renderer->confirm_batch_operation_page($data);
} else {
    echo $renderer->assignment_list_page($assignments, $batchform);
}


