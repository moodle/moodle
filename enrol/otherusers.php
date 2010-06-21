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

$id      = required_param('id', PARAM_INT); // course id
$action  = optional_param('action', '', PARAM_ACTION);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$ifilter = optional_param('ifilter', 0, PARAM_INT); // only one instance
$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$sort    = optional_param('sort', 'lastname', PARAM_ALPHA);
$dir     = optional_param('dir', 'ASC', PARAM_ALPHA);

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

require_login($course);
require_capability('moodle/role:assign', $context);

if ($course->id == SITEID) {
    redirect("$CFG->wwwroot/");
}

$instances = enrol_get_instances($course->id, true);
$plugins   = enrol_get_plugins(true);
$inames    = array();
foreach ($instances as $k=>$i) {
    if (!isset($plugins[$i->enrol])) {
        // weird, some broken stuff in plugin
        unset($instances[$k]);
        continue;
    }
    $inames[$k] = $plugins[$i->enrol]->get_instance_name($i);
}

// validate paging params
if ($ifilter != 0 and !isset($instances[$ifilter])) {
    $ifilter = 0;
}
if ($perpage < 3) {
    $perpage = 3;
}
if ($page < 0) {
    $page = 0;
}
if (!in_array($dir, array('ASC', 'DESC'))) {
    $dir = 'ASC';
}
if (!in_array($sort, array('firstname', 'lastname', 'email', 'lastseen'))) {
    $dir = 'lastname';
}

$PAGE->set_url('/enrol/notenrolled.php', array('id'=>$course->id, 'page'=>$page, 'sort'=>$sort, 'dir'=>$dir, 'perpage'=>$perpage, 'ifilter'=>$ifilter));
$PAGE->set_pagelayout('admin');

//lalala- nav hack
navigation_node::override_active_url(new moodle_url('/enrol/otherusers.php', array('id'=>$course->id)));

echo $OUTPUT->header();

//TODO: MDL-22854 add some new role related UI for users that are not enrolled but still got a role somehow in this course context

notify('This page is not implemented yet, sorry. See MDL-21782 in our tracker for more information.');

echo $OUTPUT->single_button(new moodle_url('/admin/roles/assign.php', array('contextid'=>$context->id)), 'Continue to old Assign roles UI');

echo $OUTPUT->footer();
