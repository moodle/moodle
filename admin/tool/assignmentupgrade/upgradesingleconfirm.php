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
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/assignmentupgrade/locallib.php');

require_sesskey();

$assignmentid = required_param('id', PARAM_INT);

// This calls require_login and checks moodle/site:config.
admin_externalpage_setup('assignmentupgrade',
                         '',
                         array(),
                         tool_assignmentupgrade_url('upgradesingle', array('id' => $assignmentid)));

$PAGE->navbar->add(get_string('upgradesingle', 'tool_assignmentupgrade'));
$renderer = $PAGE->get_renderer('tool_assignmentupgrade');

$assignmentinfo = tool_assignmentupgrade_get_assignment($assignmentid);

echo $renderer->convert_assignment_are_you_sure($assignmentinfo);
