<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
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

/// Editing interface to edit all the groups in a course

    require_once('../config.php');
    require_once('lib.php');

    $courseid      = required_param('id', PARAM_INT);           // Course id
    $selectedgroup = optional_param('group', NULL, PARAM_INT);  // Current group id
    $roleid        = optional_param('roleid', 0, PARAM_INT);    // Current role id

    if (! $course = get_record('course', 'id', $courseid) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if (!has_capability('moodle/course:managegroups', $context)) {
        redirect("group.php?id=$course->id");   // Not allowed to see all groups
    }

/// Get the current list of groups and check the selection is valid

    $groups = get_groups($course->id);

    if ($selectedgroup and !isset($groups[$selectedgroup])) {
        $selectedgroup = NULL;
    }


/// Print the header of the page

    $strgroup = get_string('group');
    $strgroups = get_string('groups');
    $streditgroupprofile = get_string('editgroupprofile');
    $strgroupmembers = get_string('groupmembers');
    $strgroupmemberssee = get_string('groupmemberssee');
    $strparticipants = get_string('participants');

    print_header("$course->shortname: $strgroups",  $course->fullname, 
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">$strparticipants</a> ".
                 "-> $strgroups", "", "", true, '', user_login_string($course, $USER));


/// First, process any inputs there may be.

    if ($data = data_submitted() and confirm_sesskey()) {

        // Clean ALL incoming parameters which go in SQL queries here for good measure
        $data->id      = required_param('id', PARAM_INT);
        $data->groups  = optional_param('groups', 0, PARAM_INT);
        $data->groupid = optional_param('groupid', 0, PARAM_INT);
        $data->members = optional_param('members', array(), PARAM_INT);

        if (!empty($data->nonmembersadd)) {            /// Add people to a group
            if (!empty($data->nonmembers) and !empty($data->groupid)) {
                $groupmodified = false;
                foreach ($data->nonmembers as $userid) {
                    //since we allow people to be in more than 1 group, this has to go.
                    if (!ismember($data->groupid,$userid)) {// Just to make sure (another teacher could be editing)
                        $record->groupid = $data->groupid;
                        $record->userid = $userid;
                        $record->timeadded = time();
                        if (!insert_record('groups_members', $record)) {
                            notify("Error occurred while adding user $userid to group $data->groupid");
                        }
                        $groupmodified = true;
                    }
                }
                if ($groupmodified) {
                    set_field('groups', 'timemodified', time(), 'id', $data->groupid);
                }
            }
            $selectedgroup = $data->groupid;


        } else if (!empty($data->nonmembersrandom)) {  /// Add all non members to groups
            notify("Random adding of people into groups is not functional yet.");

        } else if (!empty($data->nonmembersinfo)) {    /// Return info about the selected users
            notify("You must turn Javascript on");

        } else if (!empty($data->groupsremove)) {      /// Remove a group, all members become nonmembers
            if (!empty($data->groups)) {
                if(!isset($groups[$data->groups])) {
                    error("This is not a valid group to remove");
                }
                delete_records("groups", "id", $data->groups);
                delete_records("groups_members", "groupid", $data->groups);
                unset($groups[$data->groups]);
            }
            

        } else if (!empty($data->groupsinfo)) {        /// Display full info for a group
            notify("You must turn Javascript on");

        } else if (!empty($data->groupsadd)) {         /// Create a new group
            if (!empty($data->newgroupname)) {
                $newgroup->name = $data->newgroupname;
                $newgroup->courseid = $course->id;
                $newgroup->lang = current_language();
                $newgroup->timecreated = time();
                $newgroup->description = ''; // can not be null MDL-7300
                if (!insert_record("groups", $newgroup)) {
                    notify("Could not insert the new group '$newgroup->name'");
                }
                $groups = get_groups($course->id);
            }

        } else if (!empty($data->membersremove)) {     /// Remove selected people from a particular group

            if (!empty($data->members) and !empty($data->groupid)) {
                foreach ($data->members as $userid) {
                    delete_records('groups_members', 'userid', $userid, "groupid", $data->groupid);
                }
                set_field('groups', 'timemodified', time(), 'id', $data->groupid);
            }
            $selectedgroup = $data->groupid;

        } else if (!empty($data->membersinfo)) {       /// Return info about the selected users
            notify("You must turn Javascript on");

        }
    }


/// Calculate data ready to create the editing interface

    $strmemberincourse = get_string('memberincourse');
    $strgroupnonmembers = get_string('groupnonmembers');
    $strgroupmembersselected = get_string('groupmembersselected');
    $strgroupremovemembers = get_string('groupremovemembers');
    $strgroupinfomembers = get_string('groupinfomembers');
    $strgroupadd = get_string('groupadd');
    $strgroupremove = get_string('groupremove');
    $strgroupinfo = get_string('groupinfo');
    $strgroupinfoedit = get_string('groupinfoedit');
    $strgroupinfopeople = get_string('groupinfopeople');
    $strgrouprandomassign = get_string('grouprandomassign');
    $strgroupaddusers = get_string('groupaddusers');
    $courseid = $course->id;
    $listgroups = array();
    $listmembers = array();
    $nonmembers = array();
    $groupcount = count($groups);


/// First, get everyone into the nonmembers array

    if ($contextusers = get_role_users($roleid, $context)) {
        foreach ($contextusers as $contextuser) {
            $nonmembers[$contextuser->id] = fullname($contextuser, true);
        }
    }
    unset($contextusers);

/// Pull out all the members into little arrays

    if ($groups) {
        foreach ($groups as $group) {
            $countusers = 0;
            $listmembers[$group->id] = array();
            if ($groupusers = get_group_users($group->id, 'u.lastname ASC, u.firstname ASC')) {
                foreach ($groupusers as $key=>$groupuser) {
                    if (!array_key_exists($groupuser->id, $nonmembers)) {
                        // group member with another role
                        unset($groupusers[$key]);
                    } else {
                        $listmembers[$group->id][$groupuser->id] = $nonmembers[$groupuser->id];
                        //we do not remove people from $nonmembers, everyone is displayed
                        //this is to enable people to be registered in multiple groups
                        //unset($nonmembers[$groupuser->id]);
                        $countusers++;
                    }
                }
            }
            $listgroups[$group->id] = $group->name." ($countusers)";
        }
        natcasesort($listgroups);
    }

    if (empty($selectedgroup)) {    // Choose the first group by default
        if ($selectedgroup = array_shift($temparr = array_keys($listgroups))) {
            $members = $listmembers[$selectedgroup];
        }
    } else {
        $members = $listmembers[$selectedgroup];
    }

    $sesskey = !empty($USER->id) ? $USER->sesskey : '';

//DONOTCOMMIT: TODO:
if (debugging()) {
    echo '<p>[ <a href="../group/groupui/?id='. $courseid .'">AJAX groups</a>
         | <a href="../group/index.php?id='. $courseid .'">New groups</a> - debugging.]</p>';
}

/// Print out the complete form

    print_heading(get_string('groups'));

    include('groups-edit.html');

    print_footer($course);

?>