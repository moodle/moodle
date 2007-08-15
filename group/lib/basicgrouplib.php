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
 * name, description, picture, hidepicture
 * @param int $groupid The group ID.
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
      
        // MDL-9983
        $eventdata = new object();
        $eventdata -> groupid = $groupid;
        $eventdata -> userid = $userid;
        events_trigger('group_user_added', $eventdata);      
        $useradded = groups_db_set_group_modified($groupid);
    }
    return $useradded;
}


/*****************************
        Deletion functions  
 *****************************/


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

?>
