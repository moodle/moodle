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
 *
 * @package    block_helpdesk
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Philip Cali
 */

require_once('../../config.php');
require_once($CFG->libdir . '/grouplib.php');
require_once('lib.php');
require_login();

$id = required_param('id', PARAM_INT);
$group = optional_param('group', 0, PARAM_INT);
$roleid = optional_param('roleid', 0, PARAM_INT);
$sitecontext = context_system::instance();

require_capability('block/helpdesk:viewenrollments', $sitecontext);

$context = context_course::instance($id);
$frontpagectx = context_course::instance(SITEID);

$course = $DB->get_record('course', array('id' => $id));

$blockname = get_string('pluginname', 'block_helpdesk');
$search = get_string('search_courses', 'block_helpdesk');

$PAGE->set_context($sitecontext);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($search);
$PAGE->set_title($blockname . ': '. $search);
$PAGE->set_heading($blockname);
$PAGE->set_url('/blocks/helpdesk/participants.php', array(
    'id' => $id, 'group' => $group, 'roleid' => $roleid
));

echo $OUTPUT->header();

$heading = $course->fullname;

if ($group > 0) {
    $heading .= ' ' . $DB->get_field('groups', 'name', array('id' => $group));
}

echo $OUTPUT->heading($heading);

$course->groupmode = 2;
groups_print_course_menu($course, "participants.php?id=$id&amp;roleid=$roleid");

$rolenamesurl = new moodle_url('/blocks/helpdesk/participants.php', array(
    'id' => $id, 'group' => $group
));
$roles = get_roles_used_in_context($context, true);
$rolenames = array(0 => get_string('allparticipants'));
foreach ($roles as $role) {
    $rolenames[$role->id] = strip_tags(role_get_name($role, $context));
}

$users = get_role_users($roleid
                      , $context
                      , false
                      , 'ra.id, u.id, u.picture, u.lastname, u.firstname, ' .
                            'u.idnumber, u.lastaccess, u.firstnamephonetic, ' .
                            'u.lastnamephonetic, u.middlename, ' .
                            'u.alternatename, u.imagealt, u.email'
                      , 'u.lastname, u.firstname'
                      , null
                      , $group
);

if ($roleid > 0) {
    $a = new stdClass;
    $a->role = $rolenames[$roleid];
    $header = format_string(get_string('xuserswiththerole', 'role', $a));
} else {
    $header = get_string('allparticipants');
}

if ($group) {
    $a->group = $DB->get_field('groups', 'name', array('id' => $group));
    $header .= ' ' . format_string(get_string('ingroup', 'role', $a));
}

$header .= ': '. count($users);

$select = new single_select($rolenamesurl, 'roleid', $rolenames, $roleid, null);
$select->set_label(get_string('currentrole', 'role'));
echo $OUTPUT->render($select);

$table = new html_table();
$table->head = array(get_string('userpic') 
                   , get_string('fullname')
                   , get_string('idnumber') 
                   , get_string('lastaccess')
                    );

$neverstr = get_string('never');

foreach ($users as $user) {
    $user->imagealt = '';
    $url = new moodle_url('/user/view.php', array('id' => $user->id));
    $line = array($OUTPUT->user_picture($user, array('courseid' => $id, 'alttext' => false))
                , html_writer::link($url, fullname($user))
                , $user->idnumber
                , empty($user->lastaccess) ? $neverstr : userdate($user->lastaccess)
                 );

    $table->data[] = new html_table_row($line);
}

echo $OUTPUT->heading($header, 3);
echo html_writer::tag('div', html_writer::table($table), array('class' => 'box'));

echo $OUTPUT->footer();
