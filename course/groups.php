<?php // $Id$

/// Editing interface to edit all the groups in a course

    require_once('../config.php');
    require_once('lib.php');

    $courseid      = required_param('id');           // Course id
    $selectedgroup = optional_param('group', NULL);  // Current group id

    if (! $course = get_record('course', 'id', $courseid) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    if (!isteacheredit($course->id)) {
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

    print_header("$course->shortname: $strgroups", "$course->fullname", 
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
                    if (!user_group($course->id, $userid)) {  // Just to make sure (another teacher could be editing)
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

    if ($students = get_course_students($course->id)) {
        foreach ($students as $student) {
            $nonmembers[$student->id] = fullname($student, true);
        }
        unset($students);
    }

    if ($teachers = get_course_teachers($course->id)) {
        foreach ($teachers as $teacher) {
            $prefix = '- ';
            if (isteacheredit($course->id, $teacher->id)) {
                $prefix = '# ';
            }
            $nonmembers[$teacher->id] = $prefix.fullname($teacher, true);
        }
        unset($teachers);
    }

/// Pull out all the members into little arrays

    if ($groups) {
        foreach ($groups as $group) {
            $countusers = 0;
            $listmembers[$group->id] = array();
            if ($groupusers = get_group_users($group->id)) {
                foreach ($groupusers as $groupuser) {
                    $listmembers[$group->id][$groupuser->id] = $nonmembers[$groupuser->id];
                    unset($nonmembers[$groupuser->id]);
                    $countusers++;
                }
                natcasesort($listmembers[$group->id]);
            }
            $listgroups[$group->id] = $group->name." ($countusers)";
        }
        natcasesort($listgroups);
    }

    natcasesort($nonmembers);

    if (empty($selectedgroup)) {    // Choose the first group by default
        if ($selectedgroup = array_shift($temparr = array_keys($listgroups))) {
            $members = $listmembers[$selectedgroup];
        }
    } else {
        $members = $listmembers[$selectedgroup];
    }

    $sesskey = !empty($USER->id) ? $USER->sesskey : '';

/// Print out the complete form

    include('groups-edit.html');

    print_footer($course);

?>
