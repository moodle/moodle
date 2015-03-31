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
 * User roles report list all the users who have been assigned a particular
 * role in all contexts.
 *
 * @package    core_role
 * @copyright  &copy; 2007 The Open University and others
 * @author     t.j.hunt@open.ac.uk and others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Get params.
$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Validate them and get the corresponding objects.
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$usercontext = context_user::instance($user->id);
$coursecontext = context_course::instance($course->id);
$systemcontext = context_system::instance();

$baseurl = new moodle_url('/admin/roles/usersroles.php', array('userid'=>$userid, 'courseid'=>$courseid));

$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('admin');

// Check login and permissions.
if ($course->id == SITEID) {
    require_login();
    $PAGE->set_context($usercontext);
} else {
    require_login($course);
    $PAGE->set_context($coursecontext);
}

$canview = has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
        'moodle/role:override', 'moodle/role:manage'), $usercontext);
if (!$canview) {
    print_error('nopermissions', 'error', '', get_string('checkpermissions', 'core_role'));
}

if ($userid != $USER->id) {
    // If its not the current user we need to extend the navigation for that user to ensure
    // their navigation is loaded and this page found upon it.
    $PAGE->navigation->extend_for_user($user);
}
if ($course->id != $SITE->id || $userid != $USER->id) {
    // If we're within a course OR if we're viewing another user then we need to include the
    // settings base on the navigation to ensure that the navbar will contain the users name.
    $PAGE->navbar->includesettingsbase = true;
}

// Now get the role assignments for this user.
$sql = "SELECT ra.id, ra.userid, ra.contextid, ra.roleid, ra.component, ra.itemid, c.path
          FROM {role_assignments} ra
          JOIN {context} c ON ra.contextid = c.id
          JOIN {role} r ON ra.roleid = r.id
         WHERE ra.userid = ?
      ORDER BY contextlevel DESC, contextid ASC, r.sortorder ASC";
$roleassignments = $DB->get_records_sql($sql, array($user->id));

$allroles = role_fix_names(get_all_roles());

// In order to display a nice tree of contexts, we need to get all the
// ancestors of all the contexts in the query we just did.
$requiredcontexts = array();
foreach ($roleassignments as $ra) {
    $requiredcontexts = array_merge($requiredcontexts, explode('/', trim($ra->path, '/')));
}
$requiredcontexts = array_unique($requiredcontexts);

// Now load those contexts.
if ($requiredcontexts) {
    list($sqlcontexttest, $contextparams) = $DB->get_in_or_equal($requiredcontexts);
    $contexts = get_sorted_contexts('ctx.id ' . $sqlcontexttest, $contextparams);
} else {
    $contexts = array();
}

// Prepare some empty arrays to hold the data we are about to compute.
foreach ($contexts as $conid => $con) {
    $contexts[$conid]->children = array();
    $contexts[$conid]->roleassignments = array();
}

// Put the contexts into a tree structure.
foreach ($contexts as $conid => $con) {
    $context = context::instance_by_id($conid);
    $parentcontext = $context->get_parent_context();
    if ($parentcontext) {
        $contexts[$parentcontext->id]->children[] = $conid;
    }
}

// Put the role capabilities into the context tree.
foreach ($roleassignments as $ra) {
    $contexts[$ra->contextid]->roleassignments[$ra->roleid] = $ra;
}

$assignableroles = get_assignable_roles($usercontext, ROLENAME_BOTH);
$overridableroles = get_overridable_roles($usercontext, ROLENAME_BOTH);

// Print the header.
$fullname = fullname($user, has_capability('moodle/site:viewfullnames', $coursecontext));
$straction = get_string('thisusersroles', 'core_role');
$title = get_string('xroleassignments', 'core_role', $fullname);

// Course header.
$PAGE->set_title($title);
if ($courseid == SITEID) {
    $PAGE->set_heading($fullname);
} else {
    $PAGE->set_heading($course->fullname.': '.$fullname);
}
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');

// Display them.
if (!$roleassignments) {
    echo '<p>', get_string('noroleassignments', 'core_role'), '</p>';
} else {
    print_report_tree($systemcontext->id, $contexts, $systemcontext, $fullname, $allroles);
}

// End of page.
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

function print_report_tree($contextid, $contexts, $systemcontext, $fullname, $allroles) {
    global $CFG, $OUTPUT;

    // Only compute lang strings, etc once.
    static $stredit = null, $strcheckpermissions, $globalroleassigner, $assignurl, $checkurl;
    if (is_null($stredit)) {
        $stredit = get_string('edit');
        $strcheckpermissions = get_string('checkpermissions', 'core_role');
        $globalroleassigner = has_capability('moodle/role:assign', $systemcontext);
        $assignurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/assign.php';
        $checkurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/check.php';
    }

    // Pull the current context into an array for convenience.
    $context = context::instance_by_id($contextid);

    // Print the context name.
    echo $OUTPUT->heading(html_writer::link($context->get_url(), $context->get_context_name()),
            4, 'contextname');

    // If there are any role assignments here, print them.
    foreach ($contexts[$contextid]->roleassignments as $ra) {
        $role = $allroles[$ra->roleid];

        $value = $ra->contextid . ',' . $ra->roleid;
        $inputid = 'unassign' . $value;

        echo '<p>';
        echo $role->localname;
        if (has_capability('moodle/role:assign', $context)) {
            $raurl = $assignurl . '?contextid=' . $ra->contextid . '&amp;roleid=' .
                    $ra->roleid . '&amp;removeselect[]=' . $ra->userid;
            $churl = $checkurl . '?contextid=' . $ra->contextid . '&amp;reportuser=' . $ra->userid;
            if ($context->contextlevel == CONTEXT_USER) {
                $raurl .= '&amp;userid=' . $context->instanceid;
                $churl .= '&amp;userid=' . $context->instanceid;
            }
            $a = new stdClass;
            $a->fullname = $fullname;
            $a->contextlevel = $context->get_level_name();
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $strgoto = get_string('gotoassignsystemroles', 'core_role');
                $strcheck = get_string('checksystempermissionsfor', 'core_role', $a);
            } else {
                $strgoto = get_string('gotoassignroles', 'core_role', $a);
                $strcheck = get_string('checkuserspermissionshere', 'core_role', $a);
            }
            echo ' <a title="' . $strgoto . '" href="' . $raurl . '"><img class="iconsmall" src="' .
                    $OUTPUT->pix_url('t/edit') . '" alt="' . $stredit . '" /></a> ';
            echo ' <a title="' . $strcheck . '" href="' . $churl . '"><img class="iconsmall" src="' .
                    $OUTPUT->pix_url('t/preview') . '" alt="' . $strcheckpermissions . '" /></a> ';
            echo "</p>\n";
        }
    }

    // If there are any child contexts, print them recursively.
    if (!empty($contexts[$contextid]->children)) {
        echo '<ul>';
        foreach ($contexts[$contextid]->children as $childcontextid) {
            echo '<li>';
            print_report_tree($childcontextid, $contexts, $systemcontext, $fullname, $allroles);
            echo '</li>';
        }
        echo '</ul>';
    }
}
