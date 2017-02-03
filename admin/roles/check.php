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
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$contextid = required_param('contextid', PARAM_INT);
$returnurl  = optional_param('returnurl', null, PARAM_LOCALURL);

list($context, $course, $cm) = get_context_info_array($contextid);

$url = new moodle_url('/admin/roles/check.php', array('contextid' => $contextid));

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
if (!has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:manage'), $context)) {
    print_error('nopermissions', 'error', '', get_string('checkpermissions', 'core_role'));
}

navigation_node::override_active_url($url);
$pageurl = new moodle_url($url);
if ($returnurl) {
    $pageurl->param('returnurl', $returnurl);
}
$PAGE->set_url($pageurl);

if ($context->contextlevel == CONTEXT_USER and $USER->id != $context->instanceid) {
    $PAGE->navbar->includesettingsbase = true;
    $PAGE->navigation->extend_for_user($user);
    $PAGE->set_context(context_course::instance($course->id));
} else {
    $PAGE->set_context($context);
}

$PAGE->set_context($context);

$courseid = $course->id;
$contextname = $context->get_context_name();

// Get the user_selector we will need.
// Teachers within a course just get to see the same list of enrolled users.
// Admins (people with moodle/role:manage) can run this report for any user.
$options = array('accesscontext' => $context);
$userselector = new core_role_check_users_selector('reportuser', $options);
$userselector->set_rows(20);

// Work out an appropriate page title.
$title = get_string('checkpermissionsin', 'core_role', $contextname);

$PAGE->set_pagelayout('admin');
if ($context->contextlevel == CONTEXT_BLOCK) {
    // Do not show blocks when changing block's settings, it is confusing.
    $PAGE->blocks->show_only_fake_blocks(true);
}
$PAGE->set_title($title);

switch ($context->contextlevel) {
    case CONTEXT_SYSTEM:
        require_once($CFG->libdir.'/adminlib.php');
        admin_externalpage_setup('checkpermissions', '', array('contextid' => $contextid));
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

// Get the list of the reported-on user's role assignments - must be after
// the page setup code above, or the language might be wrong.
$reportuser = $userselector->get_selected_user();
if (!is_null($reportuser)) {
    $roleassignments = get_user_roles_with_special($context, $reportuser->id);
    $rolenames = role_get_names($context);
}

echo $OUTPUT->header();

// Print heading.
echo $OUTPUT->heading($title);

// If a user has been chosen, show all the permissions for this user.
if (!is_null($reportuser)) {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');

    if (!empty($roleassignments)) {
        echo $OUTPUT->heading(get_string('rolesforuser', 'core_role', fullname($reportuser)), 3);
        echo html_writer::start_tag('ul');

        $systemcontext = context_system::instance();
        foreach ($roleassignments as $ra) {
            $racontext = context::instance_by_id($ra->contextid);
            $link = html_writer::link($racontext->get_url(), $racontext->get_context_name());

            $rolename = $rolenames[$ra->roleid]->localname;
            if (has_capability('moodle/role:manage', $systemcontext)) {
                $rolename = html_writer::link(new moodle_url('/admin/roles/define.php',
                        array('action' => 'view', 'roleid' => $ra->roleid)), $rolename);
            }

            echo html_writer::tag('li', get_string('roleincontext', 'core_role',
                    array('role' => $rolename, 'context' => $link)));
        }
        echo html_writer::end_tag('ul');
    }

    echo $OUTPUT->heading(get_string('permissionsforuser', 'core_role', fullname($reportuser)), 3);
    $table = new core_role_check_capability_table($context, $reportuser, $contextname);
    $table->display();
    echo $OUTPUT->box_end();

    $selectheading = get_string('selectanotheruser', 'core_role');
} else {
    $selectheading = get_string('selectauser', 'core_role');
}

// Show UI for choosing a user to report on.
echo $OUTPUT->box_start('generalbox boxwidthnormal boxaligncenter', 'chooseuser');
echo '<form method="post" action="' . $PAGE->url . '" >';

// User selector.
echo $OUTPUT->heading('<label for="reportuser">' . $selectheading . '</label>', 3);
$userselector->display();

// Submit button and the end of the form.
echo '<p id="chooseusersubmit"><input type="submit" value="' . get_string('showthisuserspermissions', 'core_role') . '" ' .
     'class="btn btn-primary"/></p>';
echo '</form>';
echo $OUTPUT->box_end();

// Appropriate back link.
if ($context->contextlevel > CONTEXT_USER) {
    echo html_writer::start_tag('div', array('class'=>'backlink'));
    if ($returnurl) {
        $backurl = new moodle_url($returnurl);
    } else {
        $backurl = $context->get_url();
    }
    echo html_writer::link($backurl, get_string('backto', '', $contextname));
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();

