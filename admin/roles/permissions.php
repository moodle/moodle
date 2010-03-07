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
 * This script serves draft files of current user
 *
 * @package    moodlecore
 * @subpackage role
 * @copyright  2009 Petr Skoda (skodak) info@skodak.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/$CFG->admin/roles/lib.php");
require_once("permissions_forms.php");

$contextid  = required_param('contextid',PARAM_INT);

$roleid     = optional_param('roleid', 0, PARAM_INT);
$capability = optional_param('capability', false, PARAM_CAPABILITY);
$confirm    = optional_param('confirm', 0, PARAM_BOOL);
$prevent    = optional_param('prevent', 0, PARAM_BOOL);
$allow      = optional_param('allow', 0, PARAM_BOOL);
$unprohibit = optional_param('unprohibit', 0, PARAM_BOOL);
$prohibit   = optional_param('prohibit', 0, PARAM_BOOL);

// security first
list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);
require_capability('moodle/role:review', $context);

$PAGE->set_url('/admin/roles/permissions.php', array('contextid' => $contextid));
$PAGE->set_context($context);

$userid  = 0;
$tabfile = null;

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

$courseid = $course->id;


// These are needed early because of tabs.php
$assignableroles = get_assignable_roles($context, ROLENAME_BOTH);
list($overridableroles, $overridecounts, $nameswithcounts) = get_overridable_roles($context, ROLENAME_BOTH, true);

if ($capability) {
    $capability = $DB->get_record('capabilities', array('name'=>$capability), '*', MUST_EXIST);
}

$allowoverrides     = has_capability('moodle/role:override', $context);
$allowsafeoverrides = has_capability('moodle/role:safeoverride', $context);

$contextname = print_context_name($context);
$title = get_string('permissionsincontext', 'role', $contextname);
$straction = get_string('permissions', 'role'); // Used by tabs.php
$PAGE->set_title($title);

// Print the header and tabs
if ($context->contextlevel == CONTEXT_SYSTEM) {
    print_error('cannotoverridebaserole', 'error');

} else if ($context->contextlevel == CONTEXT_USER) {
    // NOTE: this is not linked from UI for now
    $userid = $context->instanceid;
    $user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
    $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));

    // course header
    if ($isfrontpage) {
        $PAGE->set_heading($course->fullname);
    } else {
        if (has_capability('moodle/course:viewparticipants', get_context_instance(CONTEXT_COURSE, $courseid))) {
            $PAGE->navbar->add(get_string('participants'), new moodle_url('/user/index.php', array('id'=>$courseid)));
        }
        $PAGE->set_heading($fullname);
    }
    $PAGE->navbar->add($fullname, new moodle_url("$CFG->wwwroot/user/view.php", array('id'=>$userid,'course'=>$courseid)));
    $PAGE->navbar->add($straction);

    $showroles = 1;
    $currenttab = 'permissions';
    $tabfile = $CFG->dirroot.'/user/tabs.php';

} else if ($isfrontpage) {
    admin_externalpage_setup('frontpageroles', '', array(), $PAGE->url);
    $currenttab = 'permissions';
    $tabfile = 'tabs.php';

} else {
    $currenttab = 'permissions';
    $tabfile = 'tabs.php';
}

// handle confirmations and actions
if ($prevent and isset($overridableroles[$roleid]) and $capability) {
    if ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability))) {
        if ($confirm and data_submitted() and confirm_sesskey()) {
            role_change_permission($roleid, $context, $capability->name, CAP_PREVENT);
            redirect($PAGE->url);

        } else {
            $a = (object)array('cap'=>get_capability_docs_link($capability)." ($capability->name)", 'role'=>$overridableroles[$roleid], 'context'=>$contextname);
            $message = get_string('confirmroleprevent', 'role', $a);
            $continueurl = new moodle_url($PAGE->url, array('contextid'=>$context->id, 'roleid'=>$roleid, 'capability'=>$capability->name, 'prevent'=>1, 'sesskey'=>sesskey(), 'confirm'=>1));

            echo $OUTPUT->header();
            if ($tabfile) {
                include($tabfile);
            }
            echo $OUTPUT->heading($title);
            echo $OUTPUT->confirm($message, $continueurl, $PAGE->url);
            echo $OUTPUT->footer();
            die;
        }
    }
}

if ($unprohibit and isset($overridableroles[$roleid]) and $capability) {
    if ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability))) {
        if ($confirm and data_submitted() and confirm_sesskey()) {
            role_change_permission($roleid, $context, $capability->name, CAP_INHERIT);
            redirect($PAGE->url);

        } else {
            $a = (object)array('cap'=>get_capability_docs_link($capability)." ($capability->name)", 'role'=>$overridableroles[$roleid], 'context'=>$contextname);
            $message = get_string('confirmroleunprohibit', 'role', $a);
            $continueurl = new moodle_url($PAGE->url, array('contextid'=>$context->id, 'roleid'=>$roleid, 'capability'=>$capability->name, 'unprohibit'=>1, 'sesskey'=>sesskey(), 'confirm'=>1));

            echo $OUTPUT->header();
            if ($tabfile) {
                include($tabfile);
            }
            echo $OUTPUT->heading($title);
            echo $OUTPUT->confirm($message, $continueurl, $PAGE->url);
            echo $OUTPUT->footer();
            die;
        }
    }
}

if ($allow and $capability) {
    if ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability))) {
        $mform = new role_allow_form(null, array($context, $capability, $overridableroles));
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
            $message = get_string('roleallowinfo', 'role', $a);

            echo $OUTPUT->header();
            if ($tabfile) {
                include($tabfile);
            }
            echo $OUTPUT->heading($title);
            echo $OUTPUT->box($message);
            $mform->display();
            echo $OUTPUT->footer();
            die;
        }
    }
}

if ($prohibit and $capability) {
    if ($allowoverrides or ($allowsafeoverrides and is_safe_capability($capability))) {
        $mform = new role_prohibit_form(null, array($context, $capability, $overridableroles));
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
            $message = get_string('roleprohibitinfo', 'role', $a);

            echo $OUTPUT->header();
            if ($tabfile) {
                include($tabfile);
            }
            echo $OUTPUT->box($message);
            $mform->display();
            echo $OUTPUT->footer();
            die;
        }
    }
}

echo $OUTPUT->header();
if ($tabfile) {
    include($tabfile);
}
echo $OUTPUT->heading($title);

$table = new permissions_table($context, $contextname, $allowoverrides, $allowsafeoverrides, $overridableroles);
echo $OUTPUT->box_start('generalbox capbox');
// print link to advanced override page
if ($overridableroles) {
    $overrideurl = new moodle_url('/admin/roles/override.php', array('contextid' => $context->id));
    $select = new single_select($overrideurl, 'roleid', $nameswithcounts);
    $select->label = get_string('advancedoverride', 'role');
    echo '<div class="advancedoverride">'.$OUTPUT->render($select).'</div>';
}
$table->display();
echo $OUTPUT->box_end();


if ($context->contextlevel > CONTEXT_USER) {
    echo '<div class="backlink"><a href="' . get_context_url($context) . '">' . get_string('backto', '', $contextname) . '</a></div>';
}

echo $OUTPUT->footer($course);

