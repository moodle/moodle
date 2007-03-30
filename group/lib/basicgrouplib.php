<?php
/**
 * Library of basic group functions.
 *
 * These functions are essentially just wrappers for the equivalent database
 * functions in db/dbgrouplib.php
 * 
 * It is advised that you do not create groups that do not belong to a 
 * grouping, although to allow maximum flexibility, functions are 
 * provided that allow you to do this. 
 * Note that groups (and groupings - see groupinglib.php) must belong to a 
 * course. There is no reason why a group cannot belong to more than one
 * course, although this might cause problems when group members are not
 * users of one of the courses.
 * At the moment, there are no checks that group members are also users of a 
 * course.
 * 
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once($CFG->dirroot.'/group/db/dbbasicgrouplib.php');


/***************************** 
    List functions  
 *****************************/

/**
 * Gets a list of the group IDs for a specified course.
 * @param int $courseid The id of the course.
 * @return array | false Returns an array of the group IDs or false if no records
 * or an error occurred.
 */
function groups_get_groups($courseid) {
	$groupids = groups_db_get_groups($courseid);
    return $groupids;
}


/**
 * Returns the ids of the users in the specified group.
 * @param int $groupid The groupid to get the users for
 * @param string $membertype Either 'student', 'teacher' or false. The function 
 * only returns these
 * types of group members. If set to false, returns all group members. 
 * @return array | false Returns an array of the user ids for the specified
 * group or false if no users or an error returned.
 */
function groups_get_members($groupid, $membertype = false) {
	$userids = groups_db_get_members($groupid);

    return $userids;
}

/**
 * Get the user ID and time added for each member of a group, for backup4.
 * @return array An array of member records.
 */
function groups_get_member_records($groupid) {
    if (!$groupid) {
        return false;
    }
    $members = get_records('groups_members', 'groupid ', $groupid, '', 
                          $fields='id, userid, timeadded');

    return $members;
}


/**
 * Gets the groups to which a user belongs for a specified course. 
 * @param int $userid The id of the specified user
 * @param int $courseid The id of the course.
 * @param boolean $usedatabase. If true, gets the information from  
 * @return array | false An array of the group ids of the groups to which the
 * user belongs or false if there are no groups or an error occurred.
 */
function groups_get_groups_for_user($userid, $courseid) {  
    $groupids = groups_db_get_groups_for_user($userid, $courseid);
    return $groupids;
}

/**
 * Get the groups to which a user belongs in any course on site.
 * @return array | false An array of the group IDs, or false on error.
 */
function groups_get_all_groups_for_user($userid) {
    $groups = get_records('groups_members', 'userid', $userid);
    if (! $groups) {
        return false;
    }
    // Put the results into an array. TODO: check.
    $groupids = array();
    foreach ($groups as $group) {
        array_push($groupids, $group->id);    
    }
    return $groupids;
}

/**
 * Gets the groups for the current user and specified course 
 * @param int $courseid The id of the course
 * @param int $usedatabase Set to true if the information is to be obtained 
 * directly
 * from the database, false if it is to be obtained from the $USER object. 
 * @return array An array of the groupids. 
 */
function groups_get_groups_for_current_user($courseid) {
	global $USER;
	$groupids = groups_get_groups_for_user($USER->id, $courseid);
	return $groupids;
}


/**
 * Get the group settings object for a group - this contains the following 
 * properties:
 * name, description, lang, theme, picture, hidepicture
 * @param int $groupid The id of the gruop
 * @return object The group settings object 
 */
function groups_get_group_settings($groupid, $courseid=false, $alldata=false) {
	return groups_db_get_group_settings($groupid, $courseid, $alldata);
}

/**
 * Gets the path where the image for a particular group can be found (if it 
 * exists)
 * @param int $groupid The id of the group
 * @return string The path of the image for the group
 */
function groups_get_group_image_path($groupid) {
    //TODO: groupid=1, /user/pixgroup.php/1/f1.jpg ??
	return $CFG->wwwroot.'/pixgroup.php/'.$groupid.'/f1.jpg';
}

/**
 * Gets the name of a group with a specified id
 * @param int $groupid The id of the group
 * @return string The name of the group
 */
function groups_get_group_name($groupid) {
	$settings = groups_get_group_settings($groupid);
	if ($settings) {
        return $settings->name;
    }
    return false;
}

/**
 * Gets the users for a course who are not in a specified group
 * @param int $groupid The id of the group
 * @return array An array of the userids of the non-group members,  or false if 
 * an error occurred. 
 */
function groups_get_users_not_in_group($courseid, $groupid) {
	$users = get_course_users($courseid);
    $userids = groups_users_to_userids($users);   
    $nongroupmembers = array();
    
    foreach($userids as $userid) { 	
    	if (!groups_is_member($groupid, $userid)) {
        	array_push($nongroupmembers, $userid);
    	}
    }

    return $nongroupmembers;
}

/**
 * Given two users, determines if there exists a group to which they both belong
 * @param int $userid1 The id of the first user
 * @param int $userid2 The id of the second user
 * @return boolean True if the users are in a common group, false otherwise or 
 * if an error occurred. 
 */
function groups_users_in_common_group($userid1, $userid2) {
	return groups_db_users_in_common_group($userid1, $userid2); 
}


/*****************************
   Membership functions 
 *****************************/


/**
 * Determines if a group with a given groupid exists. 
 * @param int $groupid The groupid to check for
 * @return boolean True if the group exists, false otherwise or if an error 
 * occurred. 
 */
function groups_group_exists($groupid) {
	return groups_db_group_exists($groupid);
}


/**
 * Determine if a course ID, group name and description match a group in the database.
 *   For backup/restorelib.php
 * @return mixed A group-like object with $group->id, or false.
 */
function groups_group_matches($courseid, $grp_name, $grp_description) {
    return groups_db_group_matches($courseid, $grp_name, $grp_description);
}


/**
 * Determines if the user is a member of the given group.
 *
 * @uses $USER If $userid is null, use the global object.
 * @param int $groupid The group to check for membership.
 * @param int $userid The user to check against the group.
 * @return boolean True if the user is a member, false otherwise.
 */
function groups_is_member($groupid, $userid = null) { 
    if (! $userid) {
        global $USER;
        $userid = $USER->id;
    }
    $ismember = groups_db_is_member($groupid, $userid);
    
    return $ismember;
}


/**
 * Determines if a specified group is a group for a specified course
 * @param int $groupid The group about which the request has been made
 * @param int $courseid The course for which the request has been made
 * @return boolean True if the group belongs to the course, false otherwise
 */
function groups_group_belongs_to_course($groupid, $courseid) {
    $belongstocourse = groups_db_group_belongs_to_course($groupid, $courseid);
    return $belongstocourse;
}

/**
 * Returns an object with the default group info values - these can of course be 
 * overridden if desired.
 * Can also be used to set the default for any values not set
 * @return object The group info object. 
 */
function groups_set_default_group_settings($groupinfo = null) {
        
    if (!isset($groupinfo->name)) {
        $groupinfo->name = 'Temporary Group Name';
    }

    if (!isset($groupinfo->description)) {
        $groupinfo->description = '';
    }

    if (!isset($groupinfo->lang)) {
        $groupinfo->lang = current_language();
    }

    if (!isset($groupinfo->theme)) {
        $groupinfo->theme = '';
    }

    if (!isset($groupinfo->picture)) {
        $groupinfo->picture = 0;
    }

    if (!isset($groupinfo->hidepicture)) {
        $groupinfo->hidepicture = 1;
    }

    if (isset($groupinfo->hidepicture)) {
    	if ($groupinfo->hidepicture != 0 and $groupinfo->hidepicture != 1) {
    		$groupinfo->hidepicture = 1;
    	}
    }

	return $groupinfo;
}

/***************************** 
      Creation functions  
 *****************************/

/**
 * Creates a group for a specified course
 * All groups should really belong to a grouping (though there is nothing in 
 * this API that stops them not doing
 * so, to allow plenty of flexibility) so you should be using this in 
 * conjunction with the function to add a group to
 *  a grouping. 
 * @param int $courseid The course to create the group for
 * @return int | false The id of the group created or false if the group was
 * not created successfully. 
 * See comment above on web service autoupdating. 
 */
function groups_create_group($courseid, $groupsettings = false) {	
	return groups_db_create_group($courseid, $groupsettings);
}

/**
 * Restore a group for a specified course.
 *   For backup/restorelib.php 
 */
function groups_restore_group($courseid, $groupsettings) {
    return groups_db_create_group($courseid, $groupsettings, $copytime=true);
}


/**
 * Sets the information about a group
 * Only sets the string for the picture - does not upload the picture! 
 * @param object $groupsettings An object containing some or all of the 
 * following properties: name, description, lang, theme, picture, hidepicture
 * @return boolean True if info was added successfully, false otherwise. 
 */
function groups_set_group_settings($groupid, $groupsettings) {	
	return groups_db_set_group_settings($groupid, $groupsettings);
}


/**
 * Adds a specified user to a group
 * @param int $userid   The user id
 * @param int $groupid  The group id
 * @return boolean True if user added successfully or the user is already a 
 * member of the group, false otherwise. 
 * See comment above on web service autoupdating. 
 */
function groups_add_member($groupid, $userid) {
	$useradded = false;
    
    $alreadymember = groups_is_member($groupid, $userid);
    if (!groups_group_exists($groupid)) {
    	$useradded = false;
    } elseif ($alreadymember) {
    	$useradded = true;
    } else {
		$useradded = groups_db_add_member($groupid, $userid);
    }
    if ($useradded) {
        $useradded = groups_db_set_group_modified($groupid);
    }
	return $useradded;
}

/**
 * Restore a user to the group specified in $member.
 *   For backup/restorelib.php
 * @param $member object Group member object.
 */
function groups_restore_member($member) {
    $alreadymember = groups_is_member($member->groupid, $member->userid);
    if (! groups_group_exists($member->groupid)) {
        return false;
    } elseif ($alreadymember) {
        return true;
    } else {
        $useradded = groups_db_add_member($member->groupid, $member->userid, $member->timeadded);
    }
    return $useradded;
}


/*****************************
        Deletion functions  
 *****************************/


/**
 * Delete a group best effort, first removing members and links with courses and groupings. 
 * @param int $groupid The group to delete
 * @return boolean True if deletion was successful, false otherwise
 * See comment above on web service autoupdating. 
 */
function groups_delete_group($groupid) {
    $groupdeleted = groups_db_delete_group($groupid);

    return $groupdeleted;
}


/**
 * Deletes the link between the specified user and group.
 * @param int $groupid The group to delete the user from
 * @param int $userid The user to delete
 * @return boolean True if deletion was successful, false otherwise
 * See comment above on web service autoupdating. 
 */
function groups_remove_member($groupid, $userid) {
	$success = groups_db_remove_member($groupid, $userid);    
    if ($success) {
        $success = groups_db_set_group_modified($groupid);
    }
    return $success;
}

/**
 * Removes all users from the specified group.
 * @param int $groupid The ID of the group.
 * @return boolean True for success, false otherwise.
 */
function groups_remove_all_members($groupid) {
    if (! groups_group_exists($groupid)) {
        //Woops, delete group last!
        return false;
    }
    $userids = groups_get_members($groupid);
    if (! $userids) {
        return false;
    }
    $success = true;
    foreach ($userids as $id) {
        $success = $success && groups_db_remove_member($groupid, $id);
    }
    $success = $success && groups_db_set_group_modified($groupid);
    return $success;
}

/* 
 * Update a group and return true or false
 *
 * @param object $data  - all the data needed for an entry in the 'groups' table
 */
function groups_update_group($data, $courseid) {
    $oldgroup = get_record('groups', 'id', $data->id); // should not fail, already tested above

    // Update with the new data
    if (update_record('groups', $data)) {

        $group = get_record('groups', 'id', $data->id);

        add_to_log($group->id, "groups", "update", "edit.php?id=$courseid&amp;group=$group->id", "");

        return true;

    }

    return false;
}
?>
