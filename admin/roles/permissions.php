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
 * Change permissions.
 *
 * @package    core_role
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$contextid  = required_param('contextid', PARAM_INT);

$roleid     = optional_param('roleid', 0, PARAM_INT);
$capability = optional_param('capability', false, PARAM_CAPABILITY);
$confirm    = optional_param('confirm', 0, PARAM_BOOL);
$prevent    = optional_param('prevent', 0, PARAM_BOOL);
$allow      = optional_param('allow', 0, PARAM_BOOL);
$unprohibit = optional_param('unprohibit', 0, PARAM_BOOL);
$prohibit   = optional_param('prohibit', 0, PARAM_BOOL);
$returnurl  = optional_param('returnurl', null, PARAM_LOCALURL);

list($context, $course, $cm) = get_context_info_array($contextid);

$url = new moodle_url('/admin/roles/permissions.php', array('contextid' => $contextid));

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
require_capability('moodle/role:review', $context);

navigation_node::override_active_url($url);
$pageurl = new moodle_url($url);
if ($returnurl) {
    $pageurl->param('returnurl', $returnurl);
}
$PAGE->set_url($pageurl);

if ($context->contextlevel == CONTEXT_USER and $USER->id != $context->instanceid) {
    $PAGE->navbar->includesettingsbase = true;
    $PAGE->navigation->extend_for_user($user);
    $PAGE->set_context(context_user::instance($user->id));
} else {
    $PAGE->set_context($context);
}

$courseid = $course->id;


// These are needed early because of tabs.php.
$assignableroles = get_assignable_roles($context, ROLENAME_BOTH);
list($overridableroles, $overridecounts, $nameswithcounts) = get_overridable_roles($context, ROLENAME_BOTH, true);
if ($capability) {
    $capability = $DB->get_record('capabilities', array('name'=>$capability), '*', MUST_EXIST);
}

$allowoverrides     = has_capability('moodle/role:override', $context);
$allowsafeoverrides = has_capability('moodle/role:safeoverride', $context);

$contextname = $context->get_context_name();
$title = get_string('permissionsincontext', 'core_role', $contextname);
$straction = get_string('permissions', 'core_role'); // Used by tabs.php.
$currenttab = 'permissions';

$PAGE->set_pagelayout('admin');
if ($context->contextlevel == CONTEXT_BLOCK) {
    // Do not show blocks when changing block's settings, it is confusing.
    $PAGE->blocks->show_only_fake_blocks(true);
}

$PAGE->set_title($title);
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

// Handle confirmations and actions.
// We have a capability and overrides are allowed or safe overrides are allowed and this is safe.
if ($capability && ($allowoverrides || ($allowsafeoverrides && is_safe_capability($capability)))) {
    // If we already know the the role ID, it is overrideable, and we are setting prevent or unprohibit.
    if (isset($overridableroles[$roleid]) && ($prevent || $unprohibit)) {
        // We are preventing.
        if ($prevent) {
            if ($confirm && data_submitted() && confirm_sesskey()) {
                role_change_permission($roleid, $context, $capability->name, CAP_PREVENT);
                redirect($PAGE->url);

            } else {
                $a = (object)array('cap'=>get_capability_docs_link($capability)." ($capability->name)", 'role'=>$overridableroles[$roleid], 'context'=>$contextname);
                $message = get_string('confirmroleprevent', 'core_role', $a);
                $continueurl = new moodle_url($PAGE->url,
                    array('contextid'=>$context->id, 'roleid'=>$roleid, 'capability'=>$capability->name, 'prevent'=>1, 'sesskey'=>sesskey(), 'confirm'=>1));
            }
        }
        // We are unprohibiting.
        if ($unprohibit) {
            if ($confirm && data_submitted() && confirm_sesskey()) {
                role_change_permission($roleid, $context, $capability->name, CAP_INHERIT);
                redirect($PAGE->url);
            } else {
                $a = (object)array('cap'=>get_capability_docs_link($capability)." ($capability->name)", 'role'=>$overridableroles[$roleid], 'context'=>$contextname);
                $message = get_string('confirmroleunprohibit', 'core_role', $a);
                $continueurl = new moodle_url($PAGE->url,
                    array('contextid'=>$context->id, 'roleid'=>$roleid, 'capability'=>$capability->name, 'unprohibit'=>1, 'sesskey'=>sesskey(), 'confirm'=>1));
            }
        }
        // Display and print.
        echo $OUTPUT->header();
        echo $OUTPUT->heading($title);
        echo $OUTPUT->confirm($message, $continueurl, $PAGE->url);
        echo $OUTPUT->footer();
        die;
    }

    if ($allow || $prohibit) {
        if ($allow) {
            $mform = new core_role_permission_allow_form(null, array($context, $capability, $overridableroles));
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            } else if ($data = $mform->get_data() and !empty($data->roleid)) {
                $roleid = $data->roleid;
                if (isset($overridableroles[$roleid])) {
                    role_change_permission($roleid, $context, $capability->name, CAP_ALLOW);
                }
                redirect($PAGE->url);
            } else {
                $a = (object)array('cap'=>get_capability_docs_link($capability)." ($capability->name)", 'context'=>$contextname);
                $message = get_string('roleallowinfo', 'core_role', $a);
            }
        }
        if ($prohibit) {
            $mform = new core_role_permission_prohibit_form(null, array($context, $capability, $overridableroles));
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            } else if ($data = $mform->get_data() and !empty($data->roleid)) {
                $roleid = $data->roleid;
                if (isset($overridableroles[$roleid])) {
                    role_change_permission($roleid, $context, $capability->name, CAP_PROHIBIT);
                }
                redirect($PAGE->url);
            } else {
                $a = (object)array('cap'=>get_capability_docs_link($capability)." ($capability->name)", 'context'=>$contextname);
                $message = get_string('roleprohibitinfo', 'core_role', $a);
            }
        }
        echo $OUTPUT->header();
        echo $OUTPUT->heading($title);
        echo $OUTPUT->box($message);
        $mform->display();
        echo $OUTPUT->footer();
        die;
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$adminurl = new moodle_url('/admin/');
$arguments = array('contextid' => $contextid,
                'contextname' => $contextname,
                'adminurl' => $adminurl->out());
$PAGE->requires->strings_for_js(
                                array('roleprohibitinfo', 'roleprohibitheader', 'roleallowinfo', 'roleallowheader',
                                    'confirmunassigntitle', 'confirmroleunprohibit', 'confirmroleprevent', 'confirmunassignyes',
                                    'confirmunassignno', 'deletexrole'), 'core_role');
$PAGE->requires->js_call_amd('core/permissionmanager', 'initialize', array($arguments));
$table = new core_role_permissions_table($context, $contextname, $allowoverrides, $allowsafeoverrides, $overridableroles);
echo $OUTPUT->box_start('generalbox capbox');
// Print link to advanced override page.
if ($overridableroles) {
    $overrideurl = new moodle_url('/admin/roles/override.php', array('contextid' => $context->id));
    $select = new single_select($overrideurl, 'roleid', $nameswithcounts);
    $select->label = get_string('advancedoverride', 'core_role');
    echo html_writer::tag('div', $OUTPUT->render($select), array('class'=>'advancedoverride'));
}
$table->display();
echo $OUTPUT->box_end();


if ($context->contextlevel > CONTEXT_USER) {

    if ($returnurl) {
        $url = new moodle_url($returnurl);
    } else {
        $url = $context->get_url();
    }

    echo html_writer::start_tag('div', array('class'=>'backlink'));
    echo html_writer::tag('a', get_string('backto', '', $contextname), array('href' => $url));
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer($course);
