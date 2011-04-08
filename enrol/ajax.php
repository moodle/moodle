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
 * This file processes AJAX enrolment actions and returns JSON
 *
 * The general idea behind this file is that any errors should throw exceptions
 * which will be returned and acted upon by the calling AJAX script.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require('../config.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/renderer.php");
require_once("$CFG->dirroot/group/lib.php");

// Must have the sesskey
require_sesskey();
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

$manager = new course_enrolment_manager($course);

$outcome = new stdClass;
$outcome->success = true;
$outcome->response = new stdClass;
$outcome->error = '';

switch ($action) {
    case 'unenrol':
        $ue = $DB->get_record('user_enrolments', array('id'=>required_param('ue', PARAM_INT)), '*', MUST_EXIST);
        list ($instance, $plugin) = $manager->get_user_enrolment_components($ue);
        if (!$instance || !$plugin || !$plugin->allow_unenrol($instance) || !has_capability("enrol/$instance->enrol:unenrol", $manager->get_context()) || !$manager->unenrol_user($ue)) {
            throw new enrol_ajax_exception('unenrolnotpermitted');
        }
        break;
    case 'unassign':
        $role = required_param('role', PARAM_INT);
        $user = required_param('user', PARAM_INT);
        if (!has_capability('moodle/role:assign', $manager->get_context()) || !$manager->unassign_role_from_user($user, $role)) {
            throw new enrol_ajax_exception('unassignnotpermitted');
        }
        break;

    case 'assign':
        $user = $DB->get_record('user', array('id'=>required_param('user', PARAM_INT)), '*', MUST_EXIST);
        $roleid = required_param('roleid', PARAM_INT);
        if (!array_key_exists($roleid, $manager->get_assignable_roles())) {
            throw new enrol_ajax_exception('invalidrole');
        }
        if (!has_capability('moodle/role:assign', $manager->get_context()) || !$manager->assign_role_to_user($roleid, $user->id)) {
            throw new enrol_ajax_exception('assignnotpermitted');
        }
        $outcome->response->roleid = $roleid;
        break;

    case 'getassignable':
        $otheruserroles = optional_param('otherusers', false, PARAM_BOOL);
        $outcome->response = array_reverse($manager->get_assignable_roles($otheruserroles), true);
        break;
    case 'getdefaultcohortrole': //TODO: use in ajax UI MDL-24280
        $cohortenrol = enrol_get_plugin('cohort');
        $outcome->response = $cohortenrol->get_config('roleid');
        break;
    case 'getcohorts':
        require_capability('moodle/course:enrolconfig', $context);
        $outcome->response = $manager->get_cohorts();
        break;
    case 'enrolcohort':
        require_capability('moodle/course:enrolconfig', $context);
        $roleid = required_param('roleid', PARAM_INT);
        $cohortid = required_param('cohortid', PARAM_INT);
        if (!$manager->enrol_cohort($cohortid, $roleid)) {
            throw new enrol_ajax_exception('errorenrolcohort');
        }
        break;
    case 'enrolcohortusers':
        require_capability('moodle/course:enrolconfig', $context);
        $roleid = required_param('roleid', PARAM_INT);
        $cohortid = required_param('cohortid', PARAM_INT);
        $result = $manager->enrol_cohort_users($cohortid, $roleid);
        if ($result === false) {
            throw new enrol_ajax_exception('errorenrolcohortusers');
        }
        $outcome->success = true;
        $outcome->response->users = $result;
        $outcome->response->title = get_string('success');
        $outcome->response->message = get_string('enrollednewusers', 'enrol', $result);
        $outcome->response->yesLabel = get_string('ok');
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

    case 'searchotherusers':
        $search  = optional_param('search', '', PARAM_RAW);
        $page = optional_param('page', 0, PARAM_INT);
        $outcome->response = $manager->search_other_users($search, false, $page);
        foreach ($outcome->response['users'] as &$user) {
            $user->userId = $user->id;
            $user->picture = $OUTPUT->user_picture($user);
            $user->fullname = fullname($user);
            unset($user->id);
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
die();
