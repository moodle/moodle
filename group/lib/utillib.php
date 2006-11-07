<?php

/******************************************************
 * Functions to get information about users and courses that we could do with 
 * that don't use any of the groups and that I can't find anywhere else!
 ********************************************************/


require_once($CFG->libdir.'/moodlelib.php');


/**********************************
 * Functions to get display names
 **********************************
 */


/**
 * Gets the number of members of a group
 * @param int $groupid The group specified
 * @return int The number of members of the group
 */
function groups_get_no_group_members($groupid) {
    $userids = groups_get_members($groupid);
    if (!$userids) {
    	$nomembers = 0;
    } else {
    	$nomembers = count($userids);
    }

    return $nomembers;
}


/**
 * Gets the number of groups in a specified grouping
 * @param int $groupingid The grouping specified
 * @return int The number of groups in the grouping
 */
function groups_get_no_groups_in_grouping($groupingid) {
    $groupids = groups_get_groups_in_grouping($groupingid);
    if (!$groupids) {
    	$nogroups = 0;
    } else {
    	$nogroups = count($groupids);
    }
    return $nogroups;
}


/**
 * Returns the display name of a user. This is the full name which if the user 
 * is a teacher, is prefixed by if the teacher has edit permission and - 
 * otherwise.
 * @param int $userid The id of the user specified
 * @param boolean $teacher True if the user is a teacher, false otherwise
 * @return string The display name of the user 
 */
function groups_get_user_displayname($userid, $courseid) {
	if ($courseid == false) {
		$fullname = false; 
	} else {
		$user = groups_get_user($userid);
	    $fullname = fullname($user, true);
	    if (isteacher($courseid, $userid)) {
	        if (isteacheredit($courseid, $userid)) {
	            $prefix = '# ';
	        } else {
	             $prefix = '- ';
	        }
	        
	        $fullname = $prefix.$fullname;
	    }
	}
    
    return $fullname;
}


/**
 * Returns the display name of a group - this is the group name followed by the 
 * number of group members in brackets
 * @param int $groupid The groupid
 * @return string The display name of the group
 */
function groups_get_group_displayname($groupid) {
	$groupsettings = groups_get_group_settings($groupid);
    $groupname = $groupsettings->name;
	$nogroupmembers = groups_get_no_group_members($groupid);
	$displayname= "$groupname ($nogroupmembers)"; 
	return $displayname;
}


/**
 * Returns the display name of a grouping - this is the grouping name followed 
 * by the number of groups in the
 * grouping in brackets
 * @param int $groupingid The grouping id
 * @return string The display name of the grouping
 */
function groups_get_grouping_displayname($groupingid) {
	$groupingsettings = groups_get_grouping_settings($groupingid);
	$groupingname = $groupingsettings->name;
    $nogroups= groups_get_no_groups_in_grouping($groupingid);
    $displayname = "$groupingname ($nogroups)";
    return $displayname;
}


/**
 * Takes an array of users (i.e of objects) and converts it in the corresponding 
 * array of userids. 
 * @param $users array The array of users
 * @return array The array of user ids, or false if an error occurred 
 */
function groups_users_to_userids($users) {
	if (!$users) {
		$userids = false;
	} else {	
		$userids = array();
		foreach($users as $user) {
			array_push($userids, $user->id);
		}
	}
	return $userids;
}

/**
 * Takes an array of groups (i.e of objects) and converts it in the 
 * corresponding array of groupids. 
 * @param $groups array The array of group
 * @return array The array of group ids, or false if an error occurred 
 */
function groups_groups_to_groupids($groups) {
	$groupids = array();
	foreach ($groups as $group) {
		array_push($groupids, $group->id);
	}
	return $groupids;
}

// @@@ TO DO 
function groups_groupid_to_group($groupid) {
}

// @@@ TO DO 
function groups_groupids_to_groups($groupids) {
}


/**
 * Gets the user object for a given userid. Can't find a function anywhere to 
 * do this and we need this
 * for fullname()
 * 
 * @param $userid int The userid
 * @return object The corresponding user object, or false if an error occurred
 */
function groups_get_user($userid) {
	return groups_db_get_user($userid);
}


/**
 * Gets the course information object for a given course id  
 * @param $courseid int The course id
 * @return object The course info object, or false if an error occurred. 
 * @@@ TO DO - need to put the database bit into a db file 
 */
function groups_get_course_info($courseid){
	if (!$courseid) {
		$courseinfo = false;
	} else {
		$courseinfo = get_record('course', 'id', $courseid);
	}
    return $courseinfo;
}
?>