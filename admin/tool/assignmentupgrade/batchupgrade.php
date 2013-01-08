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

define('NO_OUTPUT_BUFFERING', true);

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/assignmentupgrade/locallib.php');
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/assignmentupgrade/upgradableassignmentstable.php');
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/assignmentupgrade/upgradableassignmentsbatchform.php');

require_sesskey();

// This calls require_login and checks moodle/site:config.
admin_externalpage_setup('assignmentupgrade', '', array(), tool_assignmentupgrade_url('batchupgrade'));

$PAGE->set_pagelayout('maintenance');
$PAGE->navbar->add(get_string('batchupgrade', 'tool_assignmentupgrade'));

$renderer = $PAGE->get_renderer('tool_assignmentupgrade');

$confirm = required_param('confirm', PARAM_BOOL);
if (!$confirm) {
    print_error('invalidrequest');
    die();
}
raise_memory_limit(MEMORY_EXTRA);
// Release session.
session_get_instance()->write_close();

echo $renderer->header();
echo $renderer->heading(get_string('batchupgrade', 'tool_assignmentupgrade'));

$current = 0;
if (optional_param('upgradeall', false, PARAM_BOOL)) {
    $assignmentids = tool_assignmentupgrade_load_all_upgradable_assignmentids();
} else {
    $assignmentids = explode(',', optional_param('selected', '', PARAM_TEXT));
}
$total = count($assignmentids);

foreach ($assignmentids as $assignmentid) {
    list($summary, $success, $log) = tool_assignmentupgrade_upgrade_assignment($assignmentid);
    $current += 1;
    $params = array('current'=>$current, 'total'=>$total);
    echo $renderer->heading(get_string('upgradeprogress', 'tool_assignmentupgrade', $params), 3);
    echo $renderer->convert_assignment_result($summary, $success, $log);
}

echo $renderer->continue_button(tool_assignmentupgrade_url('listnotupgraded'));
echo $renderer->footer();
