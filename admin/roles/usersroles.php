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
 * @package    core
 * @subpackage role
 * @copyright  &copy; 2007 The Open University and others
 * @author     t.j.hunt@open.ac.uk and others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

// Get params.
$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Validate them and get the corresponding objects.
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$usercontext = get_context_instance(CONTEXT_USER, $user->id);
$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
$systemcontext = get_context_instance(CONTEXT_SYSTEM);

$baseurl = new moodle_url('/admin/roles/usersroles.php', array('userid'=>$userid, 'courseid'=>$courseid));

$PAGE->set_url($baseurl);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('admin');

/// Check login and permissions.
require_login($course);
$canview = has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
        'moodle/role:override', 'moodle/role:manage'), $usercontext);
if (!$canview) {
    print_error('nopermissions', 'error', '', get_string('checkpermissions', 'role'));
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

/// Now get the role assignments for this user.
$sql = "SELECT
        ra.id, ra.userid, ra.contextid, ra.roleid, ra.component, ra.itemid,
        c.path,
        r.name AS rolename,
        COALESCE(rn.name, r.name) AS localname
    FROM
        {role_assignments} ra
        JOIN {context} c ON ra.contextid = c.id
        JOIN {role} r ON ra.roleid = r.id
        LEFT JOIN {role_names} rn ON rn.roleid = ra.roleid AND rn.contextid = ra.contextid
    WHERE
        ra.userid = ?
    "./*AND ra.active = 1*/"
    ORDER BY
        contextlevel DESC, contextid ASC, r.sortorder ASC";
$roleassignments = $DB->get_records_sql($sql, array($user->id));

/// In order to display a nice tree of contexts, we need to get all the
/// ancestors of all the contexts in the query we just did.
$requiredcontexts = array();
foreach ($roleassignments as $ra) {
    $requiredcontexts = array_merge($requiredcontexts, explode('/', trim($ra->path, '/')));
}
$requiredcontexts = array_unique($requiredcontexts);

/// Now load those contexts.
if ($requiredcontexts) {
    list($sqlcontexttest, $contextparams) = $DB->get_in_or_equal($requiredcontexts);
    $contexts = get_sorted_contexts('ctx.id ' . $sqlcontexttest, $contextparams);
} else {
    $contexts = array();
}

/// Prepare some empty arrays to hold the data we are about to compute.
foreach ($contexts as $conid => $con) {
    $contexts[$conid]->children = array();
    $contexts[$conid]->roleassignments = array();
}

/// Put the contexts into a tree structure.
foreach ($contexts as $conid => $con) {
    $parentcontextid = get_parent_contextid($con);
    if ($parentcontextid) {
        $contexts[$parentcontextid]->children[] = $conid;
    }
}

/// Put the role capabilities into the context tree.
foreach ($roleassignments as $ra) {
    $contexts[$ra->contextid]->roleassignments[$ra->roleid] = $ra;
}

$assignableroles = get_assignable_roles($usercontext, ROLENAME_BOTH);
$overridableroles = get_overridable_roles($usercontext, ROLENAME_BOTH);

/// Print the header
$fullname = fullname($user, has_capability('moodle/site:viewfullnames', $coursecontext));
$straction = get_string('thisusersroles', 'role');
$title = get_string('xroleassignments', 'role', $fullname);

/// Course header
$PAGE->set_title($title);
if ($courseid != SITEID) {
    $PAGE->set_heading($fullname);
} else {
    $PAGE->set_heading($course->fullname);
}
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 3);
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');

// Display them.
if (!$roleassignments) {
    echo '<p>', get_string('noroleassignments', 'role'), '</p>';
} else {
    print_report_tree($systemcontext->id, $contexts, $systemcontext, $fullname);
}

/// End of page.
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

function print_report_tree($contextid, $contexts, $systemcontext, $fullname) {
    global $CFG, $OUTPUT;

    // Only compute lang strings, etc once.
    static $stredit = null, $strcheckpermissions, $globalroleassigner, $assignurl, $checkurl;
    if (is_null($stredit)) {
        $stredit = get_string('edit');
        $strcheckpermissions = get_string('checkpermissions', 'role');
        $globalroleassigner = has_capability('moodle/role:assign', $systemcontext);
        $assignurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/assign.php';
        $checkurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/check.php';
    }

    // Pull the current context into an array for convinience.
    $context = $contexts[$contextid];

    // Print the context name.
    echo $OUTPUT->heading(print_context_name($contexts[$contextid]), 4, 'contextname');

    // If there are any role assignments here, print them.
    foreach ($context->roleassignments as $ra) {
        $value = $ra->contextid . ',' . $ra->roleid;
        $inputid = 'unassign' . $value;

        echo '<p>';
        if ($ra->rolename == $ra->localname) {
            echo strip_tags(format_string($ra->localname));
        } else {
            echo strip_tags(format_string($ra->localname . ' (' . $ra->rolename . ')'));
        }
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
            $a->contextlevel = get_contextlevel_name($context->contextlevel);
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                $strgoto = get_string('gotoassignsystemroles', 'role');
                $strcheck = get_string('checksystempermissionsfor', 'role', $a);
            } else {
                $strgoto = get_string('gotoassignroles', 'role', $a);
                $strcheck = get_string('checkuserspermissionshere', 'role', $a);
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
            print_report_tree($childcontextid, $contexts, $systemcontext, $fullname);
            echo '</li>';
        }
        echo '</ul>';
    }
}
