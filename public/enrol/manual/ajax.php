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
require_once($CFG->dirroot . '/enrol/manual/classes/enrol_users_form.php');

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
$outcome->count = 0;

$searchanywhere = get_user_preferences('userselector_searchtype') === USER_SEARCH_CONTAINS;

switch ($action) {
    case 'enrol':
        $enrolid = required_param('enrolid', PARAM_INT);
        $cohorts = $users = [];

        $userids = optional_param_array('userlist', [], PARAM_SEQUENCE);
        $userid = optional_param('userid', 0, PARAM_INT);
        if ($userid) {
            $userids[] = $userid;
        }
        if ($userids) {
            foreach ($userids as $userid) {
                $users[] = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
            }
        }
        $cohortids = optional_param_array('cohortlist', [], PARAM_SEQUENCE);
        $cohortid = optional_param('cohortid', 0, PARAM_INT);
        if ($cohortid) {
            $cohortids[] = $cohortid;
        }
        if ($cohortids) {
            foreach ($cohortids as $cohortid) {
                $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
                if (!cohort_can_view_cohort($cohort, $context)) {
                    throw new enrol_ajax_exception('invalidenrolinstance'); // TODO error text!
                }
                $cohorts[] = $cohort;
            }
        }

        $roleid = optional_param('roletoassign', null, PARAM_INT);
        $duration = optional_param('duration', 0, PARAM_INT);
        $startdate = optional_param('startdate', 0, PARAM_INT);
        $startdateselect = optional_param_array('startdateselect', [], PARAM_INT);
        $recovergrades = optional_param('recovergrades', 0, PARAM_INT);
        $timeend = optional_param_array('timeend', [], PARAM_INT);
        $group = optional_param('group', null, PARAM_INT);

        if (empty($roleid)) {
            $roleid = null;
        } else {
            if (!has_capability('moodle/role:assign', $context)) {
                throw new enrol_ajax_exception('assignnotpermitted');
            }
            if (!array_key_exists($roleid, get_assignable_roles($context, ROLENAME_ALIAS, false))) {
                throw new enrol_ajax_exception('invalidrole');
            }
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
            case 5:
                // User has made explicit selection of start date.
                $timestart = make_timestamp(
                    $startdateselect['year'],
                    $startdateselect['month'],
                    $startdateselect['day'],
                    $startdateselect['hour'],
                    $startdateselect['minute'],
                );
                break;
            case 3:
            default:
                $today = time();
                $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
                $timestart = $today;
                break;
        }
        if ($timeend) {
            $timeend = make_timestamp($timeend['year'], $timeend['month'], $timeend['day'], $timeend['hour'], $timeend['minute']);
        } else if ($duration <= 0) {
            $timeend = 0;
        } else {
            $timeend = $timestart + $duration;
        }

        $mform = new enrol_manual_enrol_users_form(null, (object)["context" => $context]);
        $userenroldata = [
                'group' => $group,
                'startdate' => $timestart,
                'timeend' => $timeend,
        ];
        $mform->set_data($userenroldata);
        $validationerrors = $mform->validation($userenroldata, null);
        if (!empty($validationerrors)) {
            throw new enrol_ajax_exception('invalidenrolduration');
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
            foreach ($users as $user) {
                $plugin->enrol_user($instance, $user->id, $roleid, $timestart, $timeend, null, $recovergrades);
                if ($group && has_capability('moodle/course:managegroups', $context)) {
                    groups_add_member($group, $user->id);
                }
            }
            $outcome->count += count($users);
            foreach ($cohorts as $cohort) {
                $totalenrolledusers = $plugin->enrol_cohort($instance, $cohort->id, $roleid, $timestart, $timeend, null, $recovergrades);
                $outcome->count += $totalenrolledusers;
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
