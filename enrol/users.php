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
 * Main course enrolment management UI, this is not compatible with frontpage course.
 *
 * @package    core_enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/users_forms.php");
require_once("$CFG->dirroot/enrol/renderer.php");
require_once("$CFG->dirroot/group/lib.php");

$id      = required_param('id', PARAM_INT); // course id
$action  = optional_param('action', '', PARAM_ALPHANUMEXT);
$filter  = optional_param('ifilter', 0, PARAM_INT);
$search  = optional_param('search', '', PARAM_RAW);
$role    = optional_param('role', 0, PARAM_INT);
$fgroup  = optional_param('filtergroup', 0, PARAM_INT);
$status  = optional_param('status', -1, PARAM_INT);
$newcourse = optional_param('newcourse', false, PARAM_BOOL);

// When users reset the form, redirect back to first page without other params.
if (optional_param('resetbutton', '', PARAM_RAW) !== '') {
    redirect('users.php?id=' . $id . '&newcourse=' . $newcourse);
}

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

if ($course->id == SITEID) {
    redirect(new moodle_url('/'));
}

require_login($course);
require_capability('moodle/course:enrolreview', $context);
$PAGE->set_pagelayout('admin');

$manager = new course_enrolment_manager($PAGE, $course, $filter, $role, $search, $fgroup, $status);
$table = new course_enrolment_users_table($manager, $PAGE);
$PAGE->set_url('/enrol/users.php', $manager->get_url_params()+$table->get_url_params()+array('newcourse' => $newcourse));
navigation_node::override_active_url(new moodle_url('/enrol/users.php', array('id' => $id)));

// Check if there is an action to take
if ($action) {

    // Check if the page is confirmed (and sesskey is correct)
    $confirm = optional_param('confirm', false, PARAM_BOOL) && confirm_sesskey();

    $actiontaken = false;
    $pagetitle = '';
    $pageheading = '';
    $mform = null;
    $pagecontent = null;

    switch ($action) {
        /**
         * Removes a role from the user with this course
         */
        case 'unassign':
            if (has_capability('moodle/role:assign', $manager->get_context())) {
                $role = required_param('roleid', PARAM_INT);
                $user = required_param('user', PARAM_INT);
                if ($confirm && $manager->unassign_role_from_user($user, $role)) {
                    redirect($PAGE->url);
                } else {
                    $user = $DB->get_record('user', array('id'=>$user), '*', MUST_EXIST);
                    $allroles = $manager->get_all_roles();
                    $role = $allroles[$role];
                    $yesurl = new moodle_url($PAGE->url, array('action'=>'unassign', 'roleid'=>$role->id, 'user'=>$user->id, 'confirm'=>1, 'sesskey'=>sesskey()));
                    $message = get_string('unassignconfirm', 'role', array('user'=>fullname($user, true), 'role'=>$role->localname));
                    $pagetitle = get_string('unassignarole', 'role', $role->localname);
                    $pagecontent = $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                }
                $actiontaken = true;
            }
            break;
        /**
         * Assigns a new role to a user enrolled within this course.
         * A user must be enrolled in the course in order for this script to action
         */
        case 'assign':
            $user = $DB->get_record('user', array('id'=>required_param('user', PARAM_INT)), '*', MUST_EXIST);
            if (is_enrolled($context, $user) && has_capability('moodle/role:assign', $manager->get_context())) {
                $mform = new enrol_users_assign_form(NULL, array('user'=>$user, 'course'=>$course, 'assignable'=>$manager->get_assignable_roles()));
                $mform->set_data($PAGE->url->params());
                $data = $mform->get_data();
                if ($mform->is_cancelled() || ($data && array_key_exists($data->roleid, $manager->get_assignable_roles()) && $manager->assign_role_to_user($data->roleid, $user->id))) {
                    redirect($PAGE->url);
                } else {
                    $pagetitle = get_string('assignroles', 'role');
                }
                $actiontaken = true;
            }
            break;
        /**
         * Removes the user from the given group
         */
        case 'removemember':
            if (has_capability('moodle/course:managegroups', $manager->get_context())) {
                $groupid = required_param('group', PARAM_INT);
                $userid  = required_param('user', PARAM_INT);
                $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
                if ($confirm && $manager->remove_user_from_group($user, $groupid)) {
                    redirect($PAGE->url);
                } else {
                    $group = $manager->get_group($groupid);
                    if (!$group) {
                        break;
                    }
                    $yesurl = new moodle_url($PAGE->url, array('action'=>'removemember', 'group'=>$groupid, 'user'=>$userid, 'confirm'=>1, 'sesskey'=>sesskey()));
                    $message = get_string('removefromgroupconfirm', 'group', array('user'=>fullname($user, true), 'group'=>$group->name));
                    $pagetitle = get_string('removefromgroup', 'group', $group->name);
                    $pagecontent = $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                }
                $actiontaken = true;
            }
            break;
        /**
         * Makes the user a member of a given group
         */
        case 'addmember':
            if (has_capability('moodle/course:managegroups', $manager->get_context())) {
                $userid = required_param('user', PARAM_INT);
                $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);

                $mform = new enrol_users_addmember_form(NULL, array('user'=>$user, 'course'=>$course, 'allgroups'=>$manager->get_all_groups()));
                $mform->set_data($PAGE->url->params());
                $data = $mform->get_data();
                if ($mform->is_cancelled()) {
                    redirect($PAGE->url);
                } if (!empty($data->groupids)) {
                    foreach ($data->groupids as $groupid) {
                        $manager->add_user_to_group($user, $groupid);
                    }
                    redirect($PAGE->url);
                } else {
                    $pagetitle = get_string('addgroup', 'group');
                }
                $actiontaken = true;
            }
            break;
    }

    // If we took an action display we need to display something special.
    if ($actiontaken) {
        if (empty($pageheading)) {
            $pageheading = $pagetitle;
        }
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pageheading);
        echo $OUTPUT->header();
        echo $OUTPUT->heading(fullname($user));
        if (!is_null($mform)) {
            $mform->display();
        } else {
            echo $pagecontent;
        }
        echo $OUTPUT->footer();
        exit;
    }
}


$renderer = $PAGE->get_renderer('core_enrol');
$userdetails = array('picture' => false, 'userfullnamedisplay' => false);
// Get all the user names in a reasonable default order.
$allusernames = get_all_user_name_fields(false, null, null, null, true);
// Initialise the variable for the user's names in the table header.
$usernameheader = null;
// Get the alternative full name format for users with the viewfullnames capability.
$fullusernames = $CFG->alternativefullnameformat;
// If fullusernames is empty or accidentally set to language then fall back to default of just first and last name.
if ($fullusernames == 'language' || empty($fullusernames)) {
    // Set $a variables to return 'firstname' and 'lastname'.
    $a = new stdClass();
    $a->firstname = 'firstname';
    $a->lastname = 'lastname';
    // Getting the fullname display will ensure that the order in the language file is maintained.
    $usernameheader = explode(' ', get_string('fullnamedisplay', null, $a));
} else {
    // If everything is as expected then put them in the order specified by the alternative full name format setting.
    $usernameheader = order_in_string($allusernames, $fullusernames);
}

// Loop through each name and return the language string.
foreach ($usernameheader as $key => $username) {
    $userdetails[$username] = get_string($username);
}
$extrafields = get_extra_user_fields($context);
foreach ($extrafields as $field) {
    $userdetails[$field] = get_user_field_name($field);
}

$fields = array(
    'userdetails' => $userdetails,
    'lastcourseaccess' => get_string('lastcourseaccess'),
    'role' => get_string('roles', 'role'),
    'group' => get_string('groups', 'group'),
    'enrol' => get_string('enrolmentinstances', 'enrol')
);

// Remove hidden fields if the user has no access
if (!has_capability('moodle/course:viewhiddenuserfields', $context)) {
    $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    if (isset($hiddenfields['lastaccess'])) {
        unset($fields['lastcourseaccess']);
    }
    if (isset($hiddenfields['groups'])) {
        unset($fields['group']);
    }
}

$filterform = new enrol_users_filter_form('users.php', array('manager' => $manager, 'id' => $id, 'newcourse' => $newcourse),
        'get', '', array('id' => 'filterform'));
$filterform->set_data(array('search' => $search, 'ifilter' => $filter, 'role' => $role,
    'filtergroup' => $fgroup, 'status' => $status));

$table->set_fields($fields, $renderer);

$canassign = has_capability('moodle/role:assign', $manager->get_context());
$users = $manager->get_users_for_display($manager, $table->sort, $table->sortdirection, $table->page, $table->perpage);
foreach ($users as $userid=>&$user) {
    $user['picture'] = $OUTPUT->render($user['picture']);
    $user['role'] = $renderer->user_roles_and_actions($userid, $user['roles'], $manager->get_assignable_roles(), $canassign, $PAGE->url);
    $user['group'] = $renderer->user_groups_and_actions($userid, $user['groups'], $manager->get_all_groups(), has_capability('moodle/course:managegroups', $manager->get_context()), $PAGE->url);
    $user['enrol'] = $renderer->user_enrolments_and_actions($user['enrolments']);
}
$table->set_total_users($manager->get_total_users());
$table->set_users($users);

$PAGE->set_title($PAGE->course->fullname.': '.get_string('totalenrolledusers', 'enrol', $manager->get_total_users()));
$PAGE->set_heading($PAGE->title);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('enrolledusers', 'enrol'));
echo $renderer->render_course_enrolment_users_table($table, $filterform);
if ($newcourse == 1) {
    echo $OUTPUT->single_button(new moodle_url('/course/view.php', array('id' => $id)),
    get_string('proceedtocourse', 'enrol'), 'GET', array('class' => 'enrol-users-page-action'));
}
echo $OUTPUT->footer();
