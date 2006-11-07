<?PHP  
/*******************************************************************************
 * Library of basic group functions. 
 * 
 * These functions are essentially just wrappers for the equivalent database
 * functions in db/dbgrouplib.php
 * 
 * It is advised that you do not create groups that do not belong to a grouping, 
 * although to allow maximum 
 * flexibility, functions are provided that allow you to do this. 
 * Note that groups (and groupings - see groupinglib.php) must belong to a 
 * course. There is no reason why
 * a group cannot belong to more than one course, although this might cause 
 * problems when group members are not
 * users of one of the courses. 
 * At the moment, there are no checks that group members are also users of a 
 * course.  
 ******************************************************************************/

include_once($CFG->dirroot.'/course/groups/db/dbbasicgrouplib.php');


/***************************** 
    List functions  
 *****************************/

/**
 * Gets a list of the group ids for a specified course id 
 * @param int $courseid The id of the course. 
 * @return array | false Returns an array of the userids or false if no records
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
function groups_get_group_settings($groupid) {
	return groups_db_get_group_settings($groupid);
}

/**
 * Gets the path where the image for a particular group can be found (if it 
 * exists)
 * @param int $groupid The id of the group
 * @return string The path of the image for the group
 */
function groups_get_group_image_path($groupid) {
	return $CFG->dataroot.'/groups/'.$groupid.'/'.$image;
}

/**
 * Gets the name of a group with a specified id
 * @param int $groupid The id of the group
 * @return string The name of the group
 */
function groups_get_group_name($groupid) {
	$settings = groups_get_group_settings($groupid);
	return $settings->name;
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
	return groups_db_users_in_common_group($userid1, $userid1); 
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
 * Determines if a specified user is a member of a specified group
 * @param int $groupid The group about which the request has been made
 * @param int $userid The user about which the request has been made
 * @return boolean True if the user is a member of the group, false otherwise
 */
 function groups_is_member($groupid, $userid) { 
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
        $groupinfo->picture = '';
    }

    if (!isset($groupinfo->hidepicture)) {
        $groupinfo->hidepicture = '1';
    }
    
    if (isset($groupinfo->hidepicture)) {
    	if ($groupinfo->hidepicture != '0' and $groupinfo->hidepicture != '1') {
    		$groupinfo->hidepicture = '1';
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
 * Sets the information about a group
 * Only sets the string for the picture - does not upload the picture! 
 * @param object $groupsettings An object containing some or all of the 
 * following properties: name, description, lang, theme, picture, hidepicture
 * @return boolean True if info was added successfully, false otherwise. 
 */
function groups_set_group_settings($groupid, $groupsettings) {	
	return  groups_db_set_group_settings($groupid, $groupsettings);
}


/**
 * Adds a specified user to a group
 * @param int $groupid  The group id
 * @param int $userid   The user id
 * @return boolean True if user added successfully or the user is already a 
 * member of the group, false otherwise. 
 * See comment above on web service autoupdating. 
 */
function groups_add_member($userid, $groupid) {
	$useradded = false;
    
    $alreadymember = groups_is_member($groupid, $userid);
    if (!groups_group_exists($groupid)) {
    	$useradded = false;
    } elseif ($alreadymember) {
    	$useradded = true;
    } else {
		$useradded = groups_db_add_member($userid, $groupid);
    }

	return $useradded;
}


/*****************************
        Deletion functions  
 *****************************/


/**
 * Deletes a group best effort
 * @param int $groupid The group to delete
 * @return boolean True if deletion was successful, false otherwise
 * See comment above on web service autoupdating. 
 */
function groups_delete_group($groupid) {
	$groupdeleted = groups_db_delete_group($groupid);

    return $groupdeleted;
}


/**
 * Deletes the specified user from the specified group
 * @param int $userid The user to delete
 * @param int $groupid The group to delete the user from
 * @return boolean True if deletion was successful, false otherwise
 * See comment above on web service autoupdating. 
 */
function groups_remove_member($userid, $groupid) {
	return  groups_db_remove_member($userid, $groupid);
}
?>