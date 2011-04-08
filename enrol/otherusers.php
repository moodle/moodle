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
 * List and modify users that are not enrolled but still have a role in course.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/renderer.php");
require_once("$CFG->dirroot/group/lib.php");

$id      = required_param('id', PARAM_INT); // course id
$action  = optional_param('action', '', PARAM_ACTION);
$filter  = optional_param('ifilter', 0, PARAM_INT);

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

require_login($course);
require_capability('moodle/role:assign', $context);

if ($course->id == SITEID) {
    redirect("$CFG->wwwroot/");
}

$PAGE->set_pagelayout('admin');

$manager = new course_enrolment_manager($course, $filter);
$table = new course_enrolment_other_users_table($manager, $PAGE);
$PAGE->set_url('/enrol/otherusers.php', $manager->get_url_params()+$table->get_url_params());

/***
 * Actions will go here
 */

/*$fields = array(
    'userdetails' => array (
        'picture' => false,
        'firstname' => get_string('firstname'),
        'lastname' => get_string('lastname'),
        'email' => get_string('email')
    ),
    'lastseen' => get_string('lastaccess'),
    'role' => array(
        'roles' => get_string('roles', 'role'),
        'context' => get_string('context')
    )
);*/
$fields = array(
    'userdetails' => array (
        'picture' => false,
        'firstname' => get_string('firstname'),
        'lastname' => get_string('lastname'),
        'email' => get_string('email')
    ),
    'lastseen' => get_string('lastaccess'),
    'role' => get_string('roles', 'role')
);
$table->set_fields($fields, $OUTPUT);

//$users = $manager->get_other_users($table->sort, $table->sortdirection, $table->page, $table->perpage);

$renderer = $PAGE->get_renderer('core_enrol');
$canassign = has_capability('moodle/role:assign', $manager->get_context());
$users = $manager->get_other_users_for_display($renderer, $PAGE->url, $table->sort, $table->sortdirection, $table->page, $table->perpage);
$assignableroles = $manager->get_assignable_roles(true);
foreach ($users as $userid=>&$user) {
    $user['picture'] = $OUTPUT->render($user['picture']);
    $user['role'] = $renderer->user_roles_and_actions($userid, $user['roles'], $assignableroles, $canassign, $PAGE->url);
}

$table->set_total_users($manager->get_total_other_users());
$table->set_users($users);

$PAGE->set_title($course->fullname.': '.get_string('totalotherusers', 'enrol', $manager->get_total_other_users()));
$PAGE->set_heading($PAGE->title);

echo $OUTPUT->header();
echo $renderer->render($table);
echo $OUTPUT->footer();
