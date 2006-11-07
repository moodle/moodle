<?php 
/*******************************************************************************
 * Functions to make changes to groups in the database i.e. functions that 
 * access the groups_courses_groups, 
 * groups_groups and groups_groups_users.
 ******************************************************************************/

require_once($CFG->libdir.'/datalib.php');
require_once($CFG->dirroot.'/group/lib/lib.php');


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
    if (!$courseid) {
        $groupid = false;
    } else {
        $groups = get_records('groups_courses_groups', 'courseid', $courseid, 
                              '', $fields='id, groupid');
        // Put the results into an array
        $groupids = array();
        if (!$groups) {
        	$groupids = false;
        } else {
	        foreach ($groups as $group) {
	            array_push($groupids, $group->groupid);
	        }
        }
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
        $users = get_records('groups_groups_users', 'groupid ', $groupid, '', 
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
        $groupid = false;
    } else {  
        global $CFG;
        $table_prefix = $CFG->prefix;
        $sql = "SELECT g.id, userid 
                FROM {$table_prefix}groups_groups_users AS gm 
                INNER JOIN {$table_prefix}groups_groups AS g
                ON gm.groupid = g.id
                INNER JOIN {$table_prefix}groups_courses_groups AS cg
                ON g.id = cg.groupid
                WHERE cg.courseid  = $courseid AND gm.userid=$userid";
                
        $groups = get_records_sql($sql);
        
        if (!$groups) {
        	$groupids = false;
        } else { 
	        // Put the results into an array
	        $groupids = array();
	        foreach ($groups as $group) {
	            array_push($groupids, $group->id);    
	        }
        }
    }
	
    return $groupids;
}
 

/**
 * Get the group settings object for a group - this contains the following 
 * properties:
 * name, description, lang, theme, picture, hidepicture
 * @param int $groupid The id of the gruop
 * @return object The group settings object 
 */
function groups_db_get_group_settings($groupid) {
   if (!$groupid) {
        $groupsettings = false;
    } else {
        global $CFG;
        $tableprefix = $CFG->prefix;
        $sql = "SELECT id, name, description, lang, theme, picture, hidepicture 
                FROM {$tableprefix}groups_groups
                WHERE id = $groupid";
        $groupsettings = get_record_sql($sql);
    }
  
    return $groupsettings;	

}

/**
 * Given two users, determines if there exists a group to which they both belong
 * @param int $userid1 The id of the first user
 * @param int $userid2 The id of the second user
 * @return boolean True if the users are in a common group, false otherwise or 
 * if an error occurred. 
 */
function groups_db_users_in_common_group($userid1, $userid2) {
	$havecommongroup = false;
	$sql = "SELECT gm1.groupid, 1 FROM {$tableprefix}groups_members AS gm1 " .
			"INNER JOIN {$tableprefix}groups_members AS gm2 " .
			"ON gm1.groupid =gm2.groupid" .
			"WHERE gm1.userid = $userid1 AND gm2.userid = $userid2";
    $commonggroups = get_record_sql($sql);
    if ($commongroups) {
    	$havecommongroup = true;
    }
    
    return $havecommongroup;           
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
        $exists = record_exists($table = 'groups_groups', 'id', $groupid);
    }
    
    return $exists;
}


/**
 * Determines if a specified user is a member of a specified group
 * @param int $groupid The group about which the request has been made
 * @param int $userid The user about which the request has been made
 * @return boolean True if the user is a member of the group, false otherwise
 */
function groups_db_is_member($groupid, $userid) {
    if (!$groupid or !$userid) {
        $ismember = false;
    } else {
        $ismember = record_exists($table = 'groups_groups_users', 'groupid', 
                                  $groupid, 'userid', $userid);
    }
    
    return $ismember;
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
        $ismember = record_exists($table = 'groups_courses_groups', 
                                  'groupid', $groupid, 
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
function groups_db_create_group($courseid, $groupsettings = false) {
	// Check we have a valid course id
    if (!$courseid) {
        $groupid = false; 
    } else {      
    	$groupsettings = groups_set_default_group_settings($groupsettings);
    		
    	$record = $groupsettings;
        $record->timecreated = time();
        $record->timemodified = time();
        print_r($record);
        $groupid = insert_record('groups_groups', $record);

        
        if ($groupid != false) {
	        $record2->courseid = $courseid;
	        $record2->groupid = $groupid;
	        $record2->timeadded = time();
	        $groupadded = insert_record('groups_courses_groups', $record2);
	        if (!$groupadded) {
	        	$groupid = false;
	        }
        }
    }
    return $groupid;
}


/**
 * Adds a specified user to a group
 * @param int $groupid  The group id
 * @param int $userid   The user id
 * @return boolean True if user added successfully, false otherwise. 
 */
function groups_db_add_member($userid, $groupid) {
	// Check that the user and group are valid
    if (!$userid or !$groupid or !groups_db_group_exists($groupid)) {
        $useradded = false;
    // If the user is already a member of the group, just return success
    } elseif (groups_is_member($groupid, $userid)) {
		$useradded = true;
	} else {
        // Add the user to the group
		$record->groupid = $groupid;
		$record->userid = $userid;
		$record->timeadded = time();
		$useradded = insert_record($table = 'groups_groups_users', $record);
	}
	
	return $useradded;
}


/**
 * Sets the information about a group
 * @param object $groupsettings An object containing some or all of the 
 * following properties:
 * name, description, lang, theme, picture, hidepicture
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
        $result = update_record('groups_groups', $record);
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
 * @param int $userid The user to delete
 * @param int $groupid The group to delete the user from
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_db_remove_member($userid, $groupid) {
    if (!$userid or !$groupid) {
        $success = false;
    } else {
        $results = delete_records('groups_groups_users', 
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
 * Deletes a specified group
 * @param int $groupid The group to delete
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_db_delete_group($groupid) {
    if (!$groupid) {
        $success = false;
    } else {
        $success = true;
        // Get a list of the users for the group and delete them all from the 
        // group

        $userids = groups_db_get_members($groupid);
        if ($userids != false) {
	        foreach($userids as $userid) {
	            $userdeleted = groups_db_remove_member($userid, $groupid);
	            if (!$userdeleted) {
	                $success = false;
	            }
	        }
        }
    	
       // Remove any groupings that the group belongs to     
	   $groupingids = groups_get_groupings_for_group($groupid); 
	   if ($groupingids != false) {
		   foreach($groupingids as $groupingid) {
		       $groupremoved = groups_remove_group_from_grouping($groupid, 
		                                                         $groupingid);
		       if(!$groupremoved) {
		       		$success = false; 
		       }
		   }
	   }
		
		$results = delete_records('groups_courses_groups', 'groupid', $groupid);
		if ($results == false) {
			$success = false;
		}
		
        // Delete the group itself
        $results = delete_records($table = 'groups_groups', $field1 = 'id', 
                                  $value1 = $groupid);
        // delete_records returns an array of the results from the sql call, 
        // not a boolean, so we have to set our return variable
        if ($results == false) {
            $success = false;
        }
    }
    
    return $success;
}
?>