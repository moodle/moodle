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
 * Create and allocate users to groups
 *
 * @package    core_group
 * @copyright  Matt Clarkson mattc@catalyst.net.nz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('autogroup_form.php');

if (!defined('AUTOGROUP_MIN_RATIO')) {
    define('AUTOGROUP_MIN_RATIO', 0.7); // means minimum member count is 70% in the smallest group
}

$courseid = required_param('courseid', PARAM_INT);
$PAGE->set_url('/group/autogroup.php', array('courseid' => $courseid));

if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourseid');
}

// Make sure that the user has permissions to manage groups.
require_login($course);

$context       = context_course::instance($courseid);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id;

$strgroups           = get_string('groups');
$strparticipants     = get_string('participants');
$strautocreategroups = get_string('autocreategroups', 'group');

// Print the page and form
$preview = '';
$error = '';

/// Get applicable roles - used in menus etc later on
$rolenames = role_fix_names(get_profile_roles($context), $context, ROLENAME_ALIAS, true);

/// Create the form
$editform = new autogroup_form(null, array('roles' => $rolenames));
$editform->set_data(array('courseid' => $courseid, 'seed' => time()));

/// Handle form submission
if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data()) {

    /// Allocate members from the selected role to groups
    switch ($data->allocateby) {
        case 'no':
        case 'random':
        case 'lastname':
            $orderby = 'lastname, firstname'; break;
        case 'firstname':
            $orderby = 'firstname, lastname'; break;
        case 'idnumber':
            $orderby = 'idnumber'; break;
        default:
            print_error('unknoworder');
    }
    $users = groups_get_potential_members($data->courseid, $data->roleid, $data->cohortid, $orderby);
    $usercnt = count($users);

    if ($data->allocateby == 'random') {
        srand($data->seed);
        shuffle($users);
    }

    $groups = array();

    // Plan the allocation
    if ($data->groupby == 'groups') {
        $numgrps    = $data->number;
        $userpergrp = floor($usercnt/$numgrps);

    } else { // members
        $numgrps    = ceil($usercnt/$data->number);
        $userpergrp = $data->number;

        if (!empty($data->nosmallgroups) and $usercnt % $data->number != 0) {
            // If there would be one group with a small number of member reduce the number of groups
            $missing = $userpergrp * $numgrps - $usercnt;
            if ($missing > $userpergrp * (1-AUTOGROUP_MIN_RATIO)) {
                // spread the users from the last small group
                $numgrps--;
                $userpergrp = floor($usercnt/$numgrps);
            }
        }
    }

    // allocate the users - all groups equal count first
    for ($i=0; $i<$numgrps; $i++) {
        $groups[$i] = array();
        $groups[$i]['name']    = groups_parse_name(trim($data->namingscheme), $i);
        $groups[$i]['members'] = array();
        if ($data->allocateby == 'no') {
            continue; // do not allocate users
        }
        for ($j=0; $j<$userpergrp; $j++) {
            if (empty($users)) {
                break 2;
            }
            $user = array_shift($users);
            $groups[$i]['members'][$user->id] = $user;
        }
    }
    // now distribute the rest
    if ($data->allocateby != 'no') {
        for ($i=0; $i<$numgrps; $i++) {
            if (empty($users)) {
                break 1;
            }
            $user = array_shift($users);
            $groups[$i]['members'][$user->id] = $user;
        }
    }

    if (isset($data->preview)) {
        $table = new html_table();
        if ($data->allocateby == 'no') {
            $table->head  = array(get_string('groupscount', 'group', $numgrps));
            $table->size  = array('100%');
            $table->align = array('left');
            $table->width = '40%';
        } else {
            $table->head  = array(get_string('groupscount', 'group', $numgrps), get_string('groupmembers', 'group'), get_string('usercounttotal', 'group', $usercnt));
            $table->size  = array('20%', '70%', '10%');
            $table->align = array('left', 'left', 'center');
            $table->width = '90%';
        }
        $table->data  = array();

        foreach ($groups as $group) {
            $line = array();
            if (groups_get_group_by_name($courseid, $group['name'])) {
                $line[] = '<span class="notifyproblem">'.get_string('groupnameexists', 'group', $group['name']).'</span>';
                $error = get_string('groupnameexists', 'group', $group['name']);
            } else {
                $line[] = $group['name'];
            }
            if ($data->allocateby != 'no') {
                $unames = array();
                foreach ($group['members'] as $user) {
                    $unames[] = fullname($user, true);
                }
                $line[] = implode(', ', $unames);
                $line[] = count($group['members']);
            }
            $table->data[] = $line;
        }

        $preview .= html_writer::table($table);

    } else {
        $grouping = null;
        $createdgrouping = null;
        $createdgroups = array();
        $failed = false;

        // prepare grouping
        if (!empty($data->grouping)) {
            if ($data->grouping < 0) {
                $grouping = new stdClass();
                $grouping->courseid = $COURSE->id;
                $grouping->name     = trim($data->groupingname);
                $grouping->id = groups_create_grouping($grouping);
                $createdgrouping = $grouping->id;
            } else {
                $grouping = groups_get_grouping($data->grouping);
            }
        }

        // Save the groups data
        foreach ($groups as $key=>$group) {
            if (groups_get_group_by_name($courseid, $group['name'])) {
                $error = get_string('groupnameexists', 'group', $group['name']);
                $failed = true;
                break;
            }
            $newgroup = new stdClass();
            $newgroup->courseid = $data->courseid;
            $newgroup->name     = $group['name'];
            $groupid = groups_create_group($newgroup);
            $createdgroups[] = $groupid;
            foreach($group['members'] as $user) {
                groups_add_member($groupid, $user->id);
            }
            if ($grouping) {
                // Ask this function not to invalidate the cache, we'll do that manually once at the end.
                groups_assign_grouping($grouping->id, $groupid, null, false);
            }
        }

        // Invalidate the course groups cache seeing as we've changed it.
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));

        if ($failed) {
            foreach ($createdgroups as $groupid) {
                groups_delete_group($groupid);
            }
            if ($createdgrouping) {
                groups_delete_grouping($createdgrouping);
            }
        } else {
            redirect($returnurl);
        }
    }
}

$PAGE->navbar->add($strparticipants, new moodle_url('/user/index.php', array('id'=>$courseid)));
$PAGE->navbar->add($strgroups, new moodle_url('/group/index.php', array('id'=>$courseid)));
$PAGE->navbar->add($strautocreategroups);

/// Print header
$PAGE->set_title($strgroups);
$PAGE->set_heading($course->fullname. ': '.$strgroups);
echo $OUTPUT->header();
echo $OUTPUT->heading($strautocreategroups);

if ($error != '') {
    echo $OUTPUT->notification($error);
}

/// Display the form
$editform->display();

if($preview !== '') {
    echo $OUTPUT->heading(get_string('groupspreview', 'group'));

    echo $preview;
}

echo $OUTPUT->footer();
