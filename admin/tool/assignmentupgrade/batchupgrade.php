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
admin_externalpage_setup('assignmentupgrade', '', array(), tool_assignmentupgrade_url('batchupgrade'));
$PAGE->navbar->add(get_string('batchupgrade', 'tool_assignmentupgrade'));

$renderer = $PAGE->get_renderer('tool_assignmentupgrade');

$confirm = required_param('confirm', PARAM_BOOL);
if (!$confirm) {
    print_error('invalidrequest');
    die();
}
$result = tool_assignmentupgrade_upgrade_multiple_assignments(optional_param('upgradeall', 0, PARAM_BOOL),
                                                explode(',', optional_param('selected', '', PARAM_TEXT)));

echo $renderer->convert_multiple_assignments_result($result);
