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
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

require_once("../../../config.php");
require_once($CFG->dirroot.'/local/intelliboard/attendance/attendancelib.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');

$sesskey = required_param('sesskey', PARAM_TEXT);
$courseid = optional_param('course_id', 0, PARAM_INT);

require_login();

if(!get_config('local_intelliboard', 'enableattendance') or $sesskey !== sesskey()){
    throw new moodle_exception('invalidaccess', 'error');
}

$params = array(
    'do'=>'learner',
    'mode'=> 1
);
$intelliboard = intelliboard($params);

if(!isset($intelliboard) || !$intelliboard->token) {
    echo sprintf(
        '<div class="alert alert-error alert-block fade in " role="alert">%s</div>',
        get_string('intelliboardaccess', 'local_intelliboard')
    );
    echo $OUTPUT->footer();
    exit;
}

$context = context_system::instance();

$ltiservice = \local_intelliboard\tools\lti_tool::service();
list($endpoint, $parms) = $ltiservice->lti_get_launch_data(
    ['course_id' => $courseid]
);

$content = lti_post_launch_html($parms, $endpoint);

echo $content;