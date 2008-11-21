<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * User roles report list all the users who have been assigned a particular
 * role in all contexts.
 *
 * @copyright &copy; 2007 The Open University and others
 * @author t.j.hunt@open.ac.uk and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

require_once(dirname(__FILE__) . '/../../config.php');

// Get params.
$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Validate them and get the corresponding objects.
$user = $DB->get_record('user', array('id' => $userid));
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'error');
}
$usercontext = get_context_instance(CONTEXT_USER, $user->id);
$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

$baseurl = $CFG->wwwroot . '/' . $CFG->admin . '/roles/usersroles.php?userid='.$userid.'&amp;courseid='.$courseid;

/// Check login and permissions.
require_login($course);
$canview = has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
        'moodle/role:override', 'moodle/role:manage'), $usercontext);
if (!$canview) {
    print_error('nopermissions', 'error', '', get_string('checkpermissions', 'role'));
}

/// These are needed to determine which tabs tabs.php should show.
$assignableroles = get_assignable_roles($usercontext, ROLENAME_BOTH);
$overridableroles = get_overridable_roles($usercontext, ROLENAME_BOTH);

/// Print the header and tabs
$fullname = fullname($user, has_capability('moodle/site:viewfullnames', $coursecontext));
$straction = get_string('thisusersroles', 'role');
$title = get_string('xroleassignments', 'role', $fullname);

/// Course header
$navlinks = array();
if ($courseid != SITEID) {
    if (has_capability('moodle/course:viewparticipants', $coursecontext)) {
        $navlinks[] = array('name' => get_string('participants'), 'link' => "$CFG->wwwroot/user/index.php?id=$courseid", 'type' => 'misc');
    }
    $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid", 'type' => 'misc');
    $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header($title, $fullname, $navigation, '', '', true, '&nbsp;', navmenu($course));

/// Site header
} else {
    $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$userid&amp;course=$courseid", 'type' => 'misc');
    $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    print_header($title, $course->fullname, $navigation, '', '', true, '&nbsp;', navmenu($course));
}

$showroles = 1;
$currenttab = 'usersroles';
include_once($CFG->dirroot.'/user/tabs.php');
print_heading($title, '', 3);
echo 'Sorry, not complete yet, but I want to get this checked in before I go home.';
print_footer($course);
die; // TODO
// Standard moodleform if statement.
if ($mform->is_cancelled()) {

    // Don't think this will ever happen, but do nothing.

} else if ($fromform = $mform->get_data()){

    if (!(isset($fromform->username) && $user = $DB->get_record('user', array('username'=>$fromform->username)))) {
        
        // We got data, but the username was invalid.
        if (!isset($fromform->username)) {
            $message = get_string('unknownuser', 'report_userroles');
        } else {
            $message = get_string('unknownusername', 'report_userroles', $fromform->username);
        }
        print_heading($message, '', 3);
        
    } else {
        // We have a valid username, do stuff.
        $fullname = $fromform->username . ' (' . fullname($user) . ')';

        // Do any role unassignments that were requested.
        if ($tounassign = optional_param('unassign', array(), PARAM_SEQUENCE)) {
            echo '<form method="post" action="', $CFG->wwwroot, '/admin/report/userroles/index.php">', "\n";
            foreach ($tounassign as $assignment) {
                list($contextid, $roleid) = explode(',', $assignment);
                role_unassign($roleid, $user->id, 0, $contextid);
                echo '<input type="hidden" name="assign[]" value="', $assignment, '" />', "\n";
            }
            notify(get_string('rolesunassigned', 'report_userroles'), 'notifysuccess');
            form_fields_to_fool_mform($user->username, $mform);
            echo '<input type="submit" value="', get_string('undounassign', 'report_userroles'), '" />', "\n";
            echo '</form>', "\n";
            
        // Do any role re-assignments that were requested.
        } else if ($toassign = optional_param('assign', array(), PARAM_SEQUENCE)) {
            foreach ($toassign as $assignment) {
                list($contextid, $roleid) = explode(',', $assignment);
                role_assign($roleid, $user->id, 0, $contextid);
            }
            notify(get_string('rolesreassigned', 'report_userroles'), 'notifysuccess');
        }

        // Now get the role assignments for this user.
        $sql = "SELECT
                ra.id, ra.userid, ra.contextid, ra.roleid, ra.enrol,
                c.contextlevel, c.instanceid,
                r.name AS role
            FROM
                {role_assignments} ra,
                {context} c,
                {role} r
            WHERE
                ra.userid = :userid
            AND ra.contextid = c.id
            AND ra.roleid = r.id
            AND ra.active = 1
            ORDER BY
                contextlevel DESC, contextid ASC, r.sortorder ASC";
        $results = $DB->get_records_sql($sql,array('userid'=>$user->id));

        // Display them.
        if ($results) {
            print_heading(get_string('allassignments', 'report_userroles', $fullname), '', 3);

            // Start of unassign form.
            echo "\n\n";
            echo '<form method="post" action="', $CFG->wwwroot, '/admin/report/userroles/index.php">', "\n";

            // Print all the role assingments for this user.
            $stredit = get_string('edit');
            $strgoto = get_string('gotoassignroles', 'report_userroles');
            foreach ($results as $result) {
                $result->context = print_context_name($result, true, 'ou');
                $value = $result->contextid . ',' . $result->roleid;
                $inputid = 'unassign' . $value;
                
                $unassignable = in_array($result->enrol,
                        array('manual', 'workflowengine', 'fridayeditingcron', 'oucourserole', 'staffrequest'));
                
                echo '<p>';
                if ($unassignable) {
                    echo '<input type="checkbox" name="unassign[]" value="', $value, '" id="', $inputid, '" />', "\n";
                    echo '<label for="', $inputid, '">';
                }
                echo get_string('incontext', 'report_userroles', $result);
                if ($unassignable) {
                    echo '</label>';
                }
                echo ' <a title="', $strgoto, '" href="', $CFG->wwwroot, '/admin/roles/assign.php?contextid=',
                        $result->contextid, '&amp;roleid=', $result->roleid, '"><img ', 
                        'src="', $CFG->pixpath, '/t/edit.gif" alt="[', $stredit, ']" /></a>';
                echo "</p>\n";
            }
            
            echo "\n\n";
            form_fields_to_fool_mform($user->username, $mform);
            echo '<input type="submit" value="', get_string('unassignasabove', 'report_userroles'), '" />', "\n";
            echo '</form>', "\n";
            echo '<p>', get_string('unassignexplain', 'report_userroles'), "</p>\n\n";

        } else {
            print_heading(get_string('noassignmentsfound', 'report_userroles', $fullname), '', 3);
        }
    }
}

// Always show the form, so that the user can run another report.
echo "\n<br />\n<br />\n";
$mform->display();

admin_externalpage_print_footer();

function form_fields_to_fool_mform($username, $mform) {
    echo '<input type="hidden" name="username" value="', $username, '" />', "\n";
    echo '<input type="hidden" name="sesskey" value="', sesskey(), '" />', "\n";
    echo '<input type="hidden" name="_qf__', $mform->get_name(), '" value="1" />', "\n";
}
?>
