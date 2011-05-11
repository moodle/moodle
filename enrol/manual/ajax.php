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
 * This file processes AJAX enrolment actions and returns JSON for the manual enrolments plugin
 *
 * The general idea behind this file is that any errors should throw exceptions
 * which will be returned and acted upon by the calling AJAX script.
 *
 * @package    enrol
 * @subpackage manual
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require('../../config.php');
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/group/lib.php');

// Must have the sesskey
$id      = required_param('id', PARAM_INT); // course id
$action  = required_param('action', PARAM_ACTION);

$PAGE->set_url(new moodle_url('/enrol/ajax.php', array('id'=>$id, 'action'=>$action)));

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

if ($course->id == SITEID) {
    throw new moodle_exception('invalidcourse');
}

require_login($course);
require_capability('moodle/course:enrolreview', $context);
require_sesskey();

echo $OUTPUT->header(); // send headers

$manager = new course_enrolment_manager($PAGE, $course);

$outcome = new stdClass;
$outcome->success = true;
$outcome->response = new stdClass;
$outcome->error = '';

switch ($action) {
    case 'getassignable':
        $otheruserroles = optional_param('otherusers', false, PARAM_BOOL);
        $outcome->response = array_reverse($manager->get_assignable_roles($otheruserroles), true);
        break;
    case 'searchusers':
        $enrolid = required_param('enrolid', PARAM_INT);
        $search  = optional_param('search', '', PARAM_RAW);
        $page = optional_param('page', 0, PARAM_INT);
        $outcome->response = $manager->get_potential_users($enrolid, $search, true, $page);
        foreach ($outcome->response['users'] as &$user) {
            $user->picture = $OUTPUT->user_picture($user);
            $user->fullname = fullname($user);
        }
        $outcome->success = true;
        break;
    case 'enrol':
        $enrolid = required_param('enrolid', PARAM_INT);
        $userid = required_param('userid', PARAM_INT);

        $roleid = optional_param('role', null, PARAM_INT);
        $duration = optional_param('duration', 0, PARAM_INT);
        $startdate = optional_param('startdate', 0, PARAM_INT);
        $recovergrades = optional_param('recovergrades', 0, PARAM_INT);

        if (empty($roleid)) {
            $roleid = null;
        }

        switch($startdate) {
            case 2:
                $timestart = $course->startdate;
                break;
            case 3:
            default:
                $today = time();
                $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
                $timestart = $today;
                break;
        }
        if ($duration <= 0) {
            $timeend = 0;
        } else {
            $timeend = $timestart + ($duration*24*60*60);
        }

        $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
        $instances = $manager->get_enrolment_instances();
        $plugins = $manager->get_enrolment_plugins();
        if (!array_key_exists($enrolid, $instances)) {
            throw new enrol_ajax_exception('invalidenrolinstance');
        }
        $instance = $instances[$enrolid];
        $plugin = $plugins[$instance->enrol];
        if ($plugin->allow_enrol($instance) && has_capability('enrol/'.$plugin->get_name().':enrol', $context)) {
            $plugin->enrol_user($instance, $user->id, $roleid, $timestart, $timeend);
            if ($recovergrades) {
                require_once($CFG->libdir.'/gradelib.php');
                grade_recover_history_grades($user->id, $instance->courseid);
            }
        } else {
            throw new enrol_ajax_exception('enrolnotpermitted');
        }
        $outcome->success = true;
        break;

    default:
        throw new enrol_ajax_exception('unknowajaxaction');
}

echo json_encode($outcome);