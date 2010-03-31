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
 * Shows the result of has_capability for every capability for a user in a context.
 *
 * @package    moodlecore
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');

$contextid = required_param('contextid',PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);
$PAGE->set_url('/admin/roles/check.php', array('contextid' => $contextid));
$PAGE->set_context($context);

if ($course) {
    $isfrontpage = ($context->contextlevel == CONTEXT_COURSE and $context->instanceid == SITEID);

} else {
    $isfrontpage = false;
    if ($context->contextlevel == CONTEXT_USER) {
        $courseid = optional_param('courseid', SITEID, PARAM_INT); // needed for user/tabs.php
        $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
        $PAGE->url->param('courseid', $courseid);
        $userid = $context->instanceid;
    } else {
        $course = $SITE;
    }
}

// security first
require_login($course, false, $cm);
$canview = has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
        'moodle/role:override', 'moodle/role:manage'), $context);
if (!$canview) {
    print_error('nopermissions', 'error', '', get_string('checkpermissions', 'role'));
}

$courseid = $course->id;
$contextname = print_context_name($context);

// These are needed early because of tabs.php
$assignableroles = get_assignable_roles($context, ROLENAME_BOTH);
$overridableroles = get_overridable_roles($context, ROLENAME_BOTH);

// Get the user_selector we will need.
// Teachers within a course just get to see the same list of people they can
// assign roles to. Admins (people with moodle/role:manage) can run this report for any user.
$options = array('context' => $context, 'roleid' => 0);
if (has_capability('moodle/role:manage', $context)) {
    $userselector = new potential_assignees_course_and_above('reportuser', $options);
} else {
    $userselector = roles_get_potential_user_selector($context, 'reportuser', $options);
}
$userselector->set_multiselect(false);
$userselector->set_rows(10);

// Work out an appropriate page title.
$title = get_string('checkpermissionsin', 'role', $contextname);
$straction = get_string('checkpermissions', 'role'); // Used by tabs.php

// Print the header and tabs
if ($context->contextlevel == CONTEXT_USER) {
    $user = $DB->get_record('user', array('id' => $userid));
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));

    $PAGE->set_title($title);
    if ($courseid != SITEID) {
        if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $courseid))) {
            $PAGE->navbar->add(get_string('participants'), new moodle_url('/user/index.php', array('id'=>$courseid)));
        }
        $PAGE->set_heading($fullname);
    } else {
        $PAGE->set_heading($course->fullname);
    }
    $PAGE->navbar->add($fullname, new moodle_url("$CFG->wwwroot/user/view.php", array('id'=>$userid,'course'=>$courseid)));
    $PAGE->navbar->add($straction);
    echo $OUTPUT->header();

    $showroles = 1;
    $currenttab = 'check';
    include($CFG->dirroot.'/user/tabs.php');

} else if ($context->contextlevel == CONTEXT_SYSTEM) {
    admin_externalpage_setup('checkpermissions', '', array('contextid' => $contextid));
    echo $OUTPUT->header();

} else if ($context->contextlevel == CONTEXT_COURSE and $context->instanceid == SITEID) {
    admin_externalpage_setup('frontpageroles', '', array('contextid' => $contextid), $CFG->wwwroot . '/' . $CFG->admin . '/roles/check.php');
    echo $OUTPUT->header();
    $currenttab = 'check';
    include('tabs.php');

} else {
    echo $OUTPUT->header();
    $currenttab = 'check';
    include('tabs.php');
}

// Print heading.
echo $OUTPUT->heading_with_help($title, 'checkpermissions');

// If a user has been chosen, show all the permissions for this user.
$reportuser = $userselector->get_selected_user();
if (!is_null($reportuser)) {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    echo $OUTPUT->heading(get_string('permissionsforuser', 'role', fullname($reportuser)), 3);

    $table = new check_capability_table($context, $reportuser, $contextname);
    $table->display();
    echo $OUTPUT->box_end();

    $selectheading = get_string('selectanotheruser', 'role');
} else {
    $selectheading = get_string('selectauser', 'role');
}

// Show UI for choosing a user to report on.
echo $OUTPUT->box_start('generalbox boxwidthnormal boxaligncenter', 'chooseuser');
echo '<form method="get" action="' . $CFG->wwwroot . '/' . $CFG->admin . '/roles/check.php" >';

// Hidden fields.
echo '<input type="hidden" name="contextid" value="' . $context->id . '" />';
if (!empty($userid)) {
    echo '<input type="hidden" name="userid" value="' . $userid . '" />';
}
if ($courseid && $courseid != SITEID) {
    echo '<input type="hidden" name="courseid" value="' . $courseid . '" />';
}

// User selector.
echo $OUTPUT->heading('<label for="reportuser">' . $selectheading . '</label>', 3);
$userselector->display();

// Submit button and the end of the form.
echo '<p id="chooseusersubmit"><input type="submit" value="' . get_string('showthisuserspermissions', 'role') . '" /></p>';
echo '</form>';
echo $OUTPUT->box_end();

// Appropriate back link.
if ($context->contextlevel > CONTEXT_USER) {
    echo '<div class="backlink"><a href="' . get_context_url($context) . '">' . get_string('backto', '', $contextname) . '</a></div>';
}

echo $OUTPUT->footer();

