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
 * @package    enrol_manual
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require('../../config.php');
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

$id      = required_param('id', PARAM_INT); // Course id.
$action  = required_param('action', PARAM_ALPHANUMEXT);

$PAGE->set_url(new moodle_url('/enrol/ajax.php', array('id'=>$id, 'action'=>$action)));

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

if ($course->id == SITEID) {
    throw new moodle_exception('invalidcourse');
}

require_login($course);
require_capability('moodle/course:enrolreview', $context);
require_sesskey();

echo $OUTPUT->header(); // Send headers.

$manager = new course_enrolment_manager($PAGE, $course);

$outcome = new stdClass();
$outcome->success = true;
$outcome->response = new stdClass();
$outcome->error = '';

$searchanywhere = get_user_preferences('userselector_searchanywhere', false);

switch ($action) {
    case 'getassignable':
        $otheruserroles = optional_param('otherusers', false, PARAM_BOOL);
        $outcome->response = array_reverse($manager->get_assignable_roles($otheruserroles), true);
        break;
    case 'searchusers':
        $enrolid = required_param('enrolid', PARAM_INT);
        $search = optional_param('search', '', PARAM_RAW);
        $page = optional_param('page', 0, PARAM_INT);
        $addedenrollment = optional_param('enrolcount', 0, PARAM_INT);
        $perpage = optional_param('perpage', 25, PARAM_INT);  //  This value is hard-coded to 25 in quickenrolment.js
        $outcome->response = $manager->get_potential_users($enrolid, $search, $searchanywhere, $page, $perpage, $addedenrollment);
        $extrafields = get_extra_user_fields($context);
        $useroptions = array();
        // User is not enrolled yet, either link to site profile or do not link at all.
        if (has_capability('moodle/user:viewdetails', context_system::instance())) {
            $useroptions['courseid'] = SITEID;
        } else {
            $useroptions['link'] = false;
        }
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context);
        foreach ($outcome->response['users'] as &$user) {
            $user->picture = $OUTPUT->user_picture($user, $useroptions);
            $user->fullname = fullname($user, $viewfullnames);
            $fieldvalues = array();
            foreach ($extrafields as $field) {
                $fieldvalues[] = s($user->{$field});
                unset($user->{$field});
            }
            $user->extrafields = implode(', ', $fieldvalues);
        }
        // Chrome will display users in the order of the array keys, so we need
        // to ensure that the results ordered array keys. Fortunately, the JavaScript
        // does not care what the array keys are. It uses user.id where necessary.
        $outcome->response['users'] = array_values($outcome->response['users']);
        $outcome->success = true;
        break;
    case 'searchcohorts':
        $enrolid = required_param('enrolid', PARAM_INT);
        $search = optional_param('search', '', PARAM_RAW);
        $page = optional_param('page', 0, PARAM_INT);
        $addedenrollment = optional_param('enrolcount', 0, PARAM_INT);
        $perpage = optional_param('perpage', 25, PARAM_INT);  //  This value is hard-coded to 25 in quickenrolment.js
        $outcome->response = enrol_manual_get_potential_cohorts($context, $enrolid, $search, $page, $perpage, $addedenrollment);
        $outcome->success = true;
        break;
    case 'enrol':
        $enrolid = required_param('enrolid', PARAM_INT);
        $cohort = $user = null;
        $cohortid = optional_param('cohortid', 0, PARAM_INT);
        if (!$cohortid) {
            $userid = required_param('userid', PARAM_INT);
            $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        } else {
            $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
            if (!cohort_can_view_cohort($cohort, $context)) {
                throw new enrol_ajax_exception('invalidenrolinstance'); // TODO error text!
            }
        }

        $roleid = optional_param('role', null, PARAM_INT);
        $duration = optional_param('duration', 0, PARAM_FLOAT);
        $startdate = optional_param('startdate', 0, PARAM_INT);
        $recovergrades = optional_param('recovergrades', 0, PARAM_INT);

        if (empty($roleid)) {
            $roleid = null;
        }

        if (empty($startdate)) {
            if (!$startdate = get_config('enrol_manual', 'enrolstart')) {
                // Default to now if there is no system setting.
                $startdate = 4;
            }
        }

        switch($startdate) {
            case 2:
                $timestart = $course->startdate;
                break;
            case 4:
                // We mimic get_enrolled_sql round(time(), -2) but always floor as we want users to always access their
                // courses once they are enrolled.
                $timestart = intval(substr(time(), 0, 8) . '00') - 1;
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
            $timeend = $timestart + intval($duration*24*60*60);
        }

        $instances = $manager->get_enrolment_instances();
        $plugins = $manager->get_enrolment_plugins(true); // Do not allow actions on disabled plugins.
        if (!array_key_exists($enrolid, $instances)) {
            throw new enrol_ajax_exception('invalidenrolinstance');
        }
        $instance = $instances[$enrolid];
        if (!isset($plugins[$instance->enrol])) {
            throw new enrol_ajax_exception('enrolnotpermitted');
        }
        $plugin = $plugins[$instance->enrol];
        if ($plugin->allow_enrol($instance) && has_capability('enrol/'.$plugin->get_name().':enrol', $context)) {
            if ($user) {
                $plugin->enrol_user($instance, $user->id, $roleid, $timestart, $timeend, null, $recovergrades);
            } else {
                $plugin->enrol_cohort($instance, $cohort->id, $roleid, $timestart, $timeend, null, $recovergrades);
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
