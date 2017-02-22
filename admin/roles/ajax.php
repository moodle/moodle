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
 * This file processes AJAX requests and returns JSON
 *
 * This is a server part of yui permissions manager module
 *
 * @package core_role
 * @copyright 2015 Martin Mastny
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);

require(__DIR__ . '/../../config.php');

$contextid = required_param('contextid', PARAM_INT);
$getroles = optional_param('getroles', 0, PARAM_BOOL);

list($context, $course, $cm) = get_context_info_array($contextid);

$PAGE->set_context($context);

require_login($course, false, $cm);
require_capability('moodle/role:review', $context);
require_sesskey();

$OUTPUT->header();

list($overridableroles, $overridecounts, $nameswithcounts) = get_overridable_roles($context,
        ROLENAME_BOTH, true);

if ($getroles) {
    echo json_encode($overridableroles);
    die();
}

$capability = required_param('capability', PARAM_CAPABILITY);
$roleid = required_param('roleid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);

$capability = $DB->get_record('capabilities', array('name' => $capability), '*', MUST_EXIST);

if (!isset($overridableroles[$roleid])) {
    throw new moodle_exception('invalidarguments');
}

if (!has_capability('moodle/role:override', $context)) {
    if (!has_capability('moodle/role:safeoverride', $context) || !is_safe_capability($capability)) {
        require_capability('moodle/role:override', $context);
    }
}

switch ($action) {
    case 'allow':
        role_change_permission($roleid, $context, $capability->name, CAP_ALLOW);
        break;
    case 'prevent':
        role_change_permission($roleid, $context, $capability->name, CAP_PREVENT);
        break;
    case 'prohibit':
        role_change_permission($roleid, $context, $capability->name, CAP_PROHIBIT);
        break;
    case 'unprohibit':
        role_change_permission($roleid, $context, $capability->name, CAP_INHERIT);
        break;
    default:
        throw new moodle_exception('invalidarguments');
}

echo json_encode($action);
die();