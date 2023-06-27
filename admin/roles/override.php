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
 * Lets you override role definitions in contexts.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$contextid = required_param('contextid', PARAM_INT);
$roleid    = required_param('roleid', PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

$url = new moodle_url('/admin/roles/override.php', array('contextid' => $contextid, 'roleid' => $roleid));

if ($course) {
    $isfrontpage = ($course->id == SITEID);
} else {
    $isfrontpage = false;
    if ($context->contextlevel == CONTEXT_USER) {
        $course = $DB->get_record('course', array('id'=>optional_param('courseid', SITEID, PARAM_INT)), '*', MUST_EXIST);
        $user = $DB->get_record('user', array('id'=>$context->instanceid), '*', MUST_EXIST);
        $url->param('courseid', $course->id);
        $url->param('userid', $user->id);
    } else {
        $course = $SITE;
    }
}

// Security first.
require_login($course, false, $cm);
$safeoverridesonly = false;
if (!has_capability('moodle/role:override', $context)) {
    require_capability('moodle/role:safeoverride', $context);
    $safeoverridesonly = true;
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

if ($context->contextlevel == CONTEXT_USER and $USER->id != $context->instanceid) {
    $PAGE->navigation->extend_for_user($user);
    $PAGE->set_context(context_course::instance($course->id));
    navigation_node::override_active_url(new moodle_url('/admin/roles/permissions.php',
        array('contextid'=>$context->id, 'userid'=>$context->instanceid, 'courseid'=>$course->id)));

} else {
    $PAGE->set_context($context);
    navigation_node::override_active_url(new moodle_url('/admin/roles/permissions.php', array('contextid'=>$context->id)));
}

$courseid = $course->id;

$returnurl = new moodle_url('/admin/roles/permissions.php', array('contextid' => $context->id));

// Handle the cancel button.
if (optional_param('cancel', false, PARAM_BOOL)) {
    redirect($returnurl);
}

$role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);

// These are needed early.
$assignableroles  = get_assignable_roles($context, ROLENAME_BOTH);
list($overridableroles, $overridecounts, $nameswithcounts) = get_overridable_roles($context, ROLENAME_BOTH, true);

// Work out an appropriate page title.
$contextname = $context->get_context_name();
$straction = get_string('overrideroles', 'core_role'); // Used by tabs.php.
$a = (object)array('context' => $contextname, 'role' => $overridableroles[$roleid]);
$title = get_string('overridepermissionsforrole', 'core_role', $a);

$currenttab = 'permissions';

$PAGE->set_title($title);
$PAGE->navbar->add($straction);
switch ($context->contextlevel) {
    case CONTEXT_SYSTEM:
        print_error('cannotoverridebaserole', 'error');
        break;
    case CONTEXT_USER:
        $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));
        $PAGE->set_heading($fullname);
        $showroles = 1;
        break;
    case CONTEXT_COURSECAT:
        $PAGE->set_heading($SITE->fullname);
        break;
    case CONTEXT_COURSE:
        if ($isfrontpage) {
            $PAGE->set_heading(get_string('frontpage', 'admin'));
        } else {
            $PAGE->set_heading($course->fullname);
        }
        break;
    case CONTEXT_MODULE:
        $PAGE->set_heading($context->get_context_name(false));
        $PAGE->set_cacheable(false);
        break;
    case CONTEXT_BLOCK:
        $PAGE->set_heading($PAGE->course->fullname);
        break;
}

// Make sure this user can override that role.
if (empty($overridableroles[$roleid])) {
    $a = new stdClass;
    $a->roleid = $roleid;
    $a->context = $contextname;
    print_error('cannotoverriderolehere', '', $context->get_url(), $a);
}

// If we are actually overriding a role, create the table object, and save changes if appropriate.
$overridestable = new core_role_override_permissions_table_advanced($context, $roleid, $safeoverridesonly);
$overridestable->read_submitted_permissions();

if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
    $overridestable->save_changes();
    $rolename = $overridableroles[$roleid];

    redirect($returnurl);
}

// Finally start page output.
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($title, 'overridepermissions', 'core_role');

// Show UI for overriding roles.
if (!empty($capabilities)) {
    echo $OUTPUT->box(get_string('nocapabilitiesincontext', 'core_role'), 'generalbox boxaligncenter');

} else {
    // Print the capabilities overrideable in this context.
    echo $OUTPUT->box_start('generalbox capbox');
    echo html_writer::start_tag('form', array('id'=>'overrideform', 'action'=>$PAGE->url->out(), 'method'=>'post'));
    echo html_writer::start_tag('div');
    echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));
    echo html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'roleid', 'value'=>$roleid));
    echo html_writer::tag('p', get_string('highlightedcellsshowinherit', 'core_role'), array('class'=>'overridenotice'));

    $overridestable->display();
    if ($overridestable->has_locked_capabilities()) {
        echo '<p class="overridenotice">' . get_string('safeoverridenotice', 'core_role') . "</p>\n";
    }

    echo html_writer::start_tag('div', array('class'=>'submit_buttons'));
    $attrs = array('type'=>'submit', 'name'=>'savechanges', 'value'=>get_string('savechanges'), 'class'=>'btn btn-primary');
    echo html_writer::empty_tag('input', $attrs);
    $attrs = array('type'=>'submit', 'name'=>'cancel', 'value'=>get_string('cancel'), 'class' => 'btn btn-secondary');
    echo html_writer::empty_tag('input', $attrs);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('form');
    echo $OUTPUT->box_end();
}

// Print a form to swap roles, and a link back to the all roles list.
echo html_writer::start_tag('div', array('class'=>'backlink'));
$select = new single_select($PAGE->url, 'roleid', $nameswithcounts, $roleid, null);
$select->label = get_string('overrideanotherrole', 'core_role');
echo $OUTPUT->render($select);
echo html_writer::tag('p', html_writer::tag('a', get_string('backtoallroles', 'core_role'), array('href'=>$returnurl)));
echo html_writer::end_tag('div');

echo $OUTPUT->footer();
