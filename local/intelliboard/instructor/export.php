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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

define('AJAX_SCRIPT', true);

require('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');
require_once($CFG->dirroot .'/course/lib.php');

$action = optional_param('action', '', PARAM_TEXT);
$view = optional_param('view', '', PARAM_TEXT);
$daterange = clean_raw(optional_param('daterange', '', PARAM_RAW));
$course = optional_param('course', 0, PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);

if (!isloggedin() or isguestuser()) {
	return false;
}
require_login();

confirm_sesskey();

$PAGE->set_context(context_system::instance());
if ($action == 'graded_activities_overview_export') {
    return graded_activities_overview_export($course, $format);
}