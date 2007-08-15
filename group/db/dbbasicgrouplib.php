<?php 
/**
 * Functions to make changes to groups in the database i.e. functions that 
 * access tables:
 *     groups and groups_members.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

/*******************************************************************************
 * Utility functions
 ******************************************************************************/

/**
 * Returns the user record for a given userid - I can't seem to find a function 
 * anywhere else to do this
 * (and we need it to use the fullname() function). 
 * @param int $userid The id of the user to get the record for
 * @return object The user record
 */
function groups_db_get_user($userid) {
    $query = get_record('user', 'id', $userid);
    return $query;
}


/******************************************************************************* 
    List functions  
 ******************************************************************************/


/**
 * Returns all the ids of all the groups for the specified course.
 * @uses $CFG
 * @param int $courseid The courseid to get the groups for.
 * @return array|false Returns an array of the group ids or false if no groups
 * or an error returned
 */
 function groups_db_get_groups($courseid) {
    if (! $courseid) {
        return false;
    }
    $records = get_records('groups', 'courseid', $courseid, 
                           '', $fields='id');
    if (! $records) {
        return false;
    }
    // Put the results into an array, note these are NOT 'group' objects.
    $groupids = array();
    foreach ($records as $record) {
        array_push($groupids, $record->id);
    }

    return $groupids;
}


/**
 * Returns the ids of the users in the specified group.
 * @param int $groupid The groupid to get the users for
 * @return array| false Returns an array of the user ids for the specified
 * group or false if no users or an error returned.
 */
function groups_db_get_members($groupid) {
    if (!$groupid) {
        $userids = false;
    } else {
        $users = get_records('groups_members', 'groupid ', $groupid, '', 
                             $fields='id, userid');
        if (!$users) {
            $userids = false;
        } else {
            $userids = array();
            foreach ($users as $user) {
                array_push($userids, $user->userid);
            }
        }
    }
    return $userids;
}


/**
 * Gets the groups to which a user belongs for a specified course. 
 * @uses $CFG
 * @param int $userid The id of the specified user
 * @param int $courseid The id of the course. 
 * @return array | false An array of the group ids of the groups to which the
 * user belongs or false if there are no groups or an error occurred.
 */
function groups_db_get_groups_for_user($userid, $courseid) {
    if (!$userid or !$courseid) {
        $groupids = false;
    } else {  
        global $CFG;
        $sql = "SELECT g.id, gm.userid 
                FROM {$CFG->prefix}groups_members gm 
                INNER JOIN {$CFG->prefix}groups g
                ON gm.groupid = g.id
                WHERE g.courseid  = '$courseid' AND gm.userid = '$userid'";
                
        $groups = get_records_sql($sql);
        $groupids = groups_groups_to_groupids($groups);
    }

    return $groupids;
}
 

/**
 * Get the group settings object for a group - this contains the following 
 * properties:
 * name, description, picture, hidepicture
 * @param int $groupid The id of the group
 * @param $courseid Optionally add the course ID, for backwards compatibility.
 * @return object The group settings object 
 */
function groups_db_get_group_settings($groupid, $courseid=false, $alldata=false) {
   if (!$groupid) {
        $groupsettings = false;
    } else {
        global $CFG;
        $select = ($alldata) ? '*' : 'id, name, description, picture, hidepicture';
        $sql = "SELECT $select
                FROM {$CFG->prefix}groups
                WHERE id = $groupid";
        $groupsettings = get_record_sql($sql);
        if ($courseid && $groupsettings) {
            $groupsettings->courseid = $courseid;
        }
    }

    return $groupsettings;

}

/******************************************************************************* 
   Membership functions  
 ******************************************************************************/


/**
 * Determines if a group with a given groupid exists. 
 * @param int $groupid The groupid to check for
 * @return boolean True if the group exists, false otherwise or if an error 
 * occurred. 
 */
function groups_db_group_exists($groupid) {
    if (!$groupid) {
        $exists = false;
    } else {
        $exists = record_exists($table = 'groups', 'id', $groupid);
    }

    return $exists;
}

/**
 * Determines if a specified group is a group for a specified course
 * @param int $groupid The group about which the request has been made
 * @param int $courseid The course for which the request has been made
 * @return boolean True if the group belongs to the course, false otherwise
 */
function groups_db_group_belongs_to_course($groupid, $courseid) {   
    if (!$groupid or !$courseid) {
        $ismember = false;
    } else {
        $ismember = record_exists($table = 'groups', 
                                  'id', $groupid, 
                                  'courseid', $courseid);
    }

    return $ismember;
}


/*******************************************************************************
   Creation functions  
 ******************************************************************************/


/** 
 * Creates a group for a specified course
 * @param int $courseid The course to create the group for
 * @return int The id of the group created or false if the create failed.
 */
function groups_db_create_group($courseid, $groupsettings=false, $copytime=false) {
    // Check we have a valid course id
    if (!$courseid) {
        $groupid = false; 
    } else {      
        $groupsettings = groups_set_default_group_settings($groupsettings);

        $record = $groupsettings;
        if (! $copytime) {
            $now = time();
            $record->timecreated = $now;
            $record->timemodified = $now;
        }
        //print_r($record);
        $groupid = insert_record('groups', $record);
        
    }
    return $groupid;
}

/** 
 * Upgrades a group for a specified course. To preserve the group ID we do a raw insert.
 * @param int $courseid The course to create the group for
 * @return int The id of the group created or false if the insert failed.
 */
function groups_db_upgrade_group($courseid, $group) {
    global $CFG;
    // Check we have a valid course id
    if (!$courseid || !$group || !isset($group->id)) {
        return false; 
    }

    $r = addslashes_object($group);
    $sql = "INSERT INTO {$CFG->prefix}groups
        (id,courseid,name,description, enrolmentkey,picture,hidepicture, timecreated,timemodified)
        VALUES ('$r->id','$r->courseid','$r->name','$r->description', '$r->enrolmentkey','$r->picture',
                '$r->hidepicture', '$r->timecreated','$r->timemodified')";

    $result = execute_sql($sql);
    return $group->id;
}


/**
 * Adds a specified user to a group
 * @param int $groupid  The group id
 * @param int $userid   The user id
 * @return boolean True if user added successfully, false otherwise. 
 */
function groups_db_add_member($groupid, $userid, $copytime=false) {
    // Check that the user and group are valid
    if (!$userid or !$groupid or !groups_db_group_exists($groupid)) {
        $useradded = false;
        // If the user is already a member of the group, just return success
    } elseif (groups_is_member($groupid, $userid)) {
        $useradded = true;
    } else {
        // Add the user to the group
        $record = new Object();
        $record->groupid = $groupid;
        $record->userid = $userid;
        if ($copytime) {
            $record->timeadded = $copytime;
        } else {
            $record->timeadded = time();
        }
        $useradded = insert_record($table = 'groups_members', $record);
    }

    return $useradded;
}


/**
 * Sets the information about a group
 * @param object $groupsettings An object containing some or all of the 
 * following properties:
 * name, description, picture, hidepicture
 * @return boolean True if info was added successfully, false otherwise. 
 */
function groups_db_set_group_settings($groupid, $groupsettings) {
    $success = true;
    if (!$groupid or !$groupsettings or !groups_db_group_exists($groupid)) {
        $success = false; 
    } else {
        $record = $groupsettings;
        $record->id = $groupid;
        $record->timemodified = time();
        $result = update_record('groups', $record);
        if (!$result) {
            $success = false;
        }
    }

    return $success;
}

/******************************************************************************* 
    Deletion functions  
 ******************************************************************************/


/**
 * Deletes the specified user from the specified group
 * @param int $groupid The group to delete the user from
 * @param int $userid The user to delete
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_db_remove_member($groupid, $userid) {
    if (!$userid or !$groupid) {
        $success = false;
    } else {
        $results = delete_records('groups_members', 
                                  'groupid', $groupid, 'userid', $userid);
        // delete_records returns an array of the results from the sql call, 
        // not a boolean, so we have to set our return variable
        if ($results == false) {
            $success = false;
        } else {
            $success = true;
        }
    }

    return $success;
}


/**
 * Internal function to set the time a group was modified.
 */
function groups_db_set_group_modified($groupid) {
    return set_field('groups', 'timemodified', time(), 'id', $groupid);
}


?>
