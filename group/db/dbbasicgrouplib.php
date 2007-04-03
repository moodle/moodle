<?php 
/**
 * Functions to make changes to groups in the database i.e. functions that 
 * access tables:
 *     groups_courses_groups, groups and groups_members.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once($CFG->libdir.'/datalib.php');
require_once($CFG->dirroot.'/group/lib.php');


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
    $records = get_records('groups_courses_groups', 'courseid', $courseid, 
                           '', $fields='id, groupid');
    if (! $records) {
        return false;
    }
    // Put the results into an array, note these are NOT 'group' objects.
    $groupids = array();
    foreach ($records as $record) {
        array_push($groupids, $record->groupid);
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
                INNER JOIN {$CFG->prefix}groups_courses_groups cg
                ON g.id = cg.groupid
                WHERE cg.courseid  = '$courseid' AND gm.userid = '$userid'";
                
        $groups = get_records_sql($sql);
        $groupids = groups_groups_to_groupids($groups);
    }
	
    return $groupids;
}
 

/**
 * Get the group settings object for a group - this contains the following 
 * properties:
 * name, description, lang, theme, picture, hidepicture
 * @param int $groupid The id of the group
 * @param $courseid Optionally add the course ID, for backwards compatibility.
 * @return object The group settings object 
 */
function groups_db_get_group_settings($groupid, $courseid=false, $alldata=false) {
   if (!$groupid) {
        $groupsettings = false;
    } else {
        global $CFG;
        $select = ($alldata) ? '*' : 'id, name, description, lang, theme, picture, hidepicture';
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

/**
 * Given two users, determines if there exists a group to which they both belong
 * @param int $userid1 The id of the first user
 * @param int $userid2 The id of the second user
 * @return boolean True if the users are in a common group, false otherwise or 
 * if an error occurred. 
 */
function groups_db_users_in_common_group($userid1, $userid2) {
	global $CFG;
    $havecommongroup = false;
	$sql = "SELECT gm1.groupid, 1 FROM {$CFG->prefix}groups_members gm1 " .
			"INNER JOIN {$CFG->prefix}groups_members gm2 " .
			"ON gm1.groupid = gm2.groupid" .
			"WHERE gm1.userid = '$userid1' AND gm2.userid = '$userid2'";
    $commongroups = get_record_sql($sql);
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
        $exists = record_exists($table = 'groups', 'id', $groupid);
    }

    return $exists;
}


/**
 * Determine if a course ID, group name and description match a group in the database.
 *   For backup/restorelib.php
 * @return mixed A group-like object with $group->id, or false.
 */
function groups_db_group_matches($courseid, $grp_name, $grp_description) {
//$gro_db->id; $gro_db = get_record("groups","courseid",$restore->course_id,"name",$gro->name,"description",$gro->description);    
    global $CFG;
    $sql = "SELECT g.id, g.name, g.description
        FROM {$CFG->prefix}groups g
        INNER JOIN {$CFG->prefix}groups_courses_groups cg ON g.id = cg.groupid
        WHERE g.name = '$grp_name'
        AND g.description = '$grp_description'
        AND cg.courseid = '$courseid'";
    $records = get_records_sql($sql);
    $group = false;
    if ($records) {
        $group = $records[0];
    } 
    return $group;
}

/**
 * Determine if a course ID, and group name match a group in the database.
 * @return mixed A group-like object with $group->id, or false.
 */
function groups_db_group_name_exists($courseid, $grp_name) {
    global $CFG;
    $sql = "SELECT g.id, g.name
        FROM {$CFG->prefix}groups g
        INNER JOIN {$CFG->prefix}groups_courses_groups cg ON g.id = cg.groupid
        WHERE g.name = '$grp_name'
        AND cg.courseid = '$courseid'";
    $records = get_records_sql($sql);
    $group = false;
    if ($records) {
        $group = current($records);
    } 
    return $group;
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
        $ismember = record_exists($table = 'groups_members', 'groupid', 
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

        if ($groupid != false) {
            $record2 = new Object();
	        $record2->courseid = $courseid;
	        $record2->groupid = $groupid;
            if ($copytime) {
                $record2->timeadded = $record->timemodified;
            } else {
                $record2->timeadded = $now;
            }
	        $groupadded = insert_record('groups_courses_groups', $record2);
	        if (!$groupadded) {
	        	$groupid = false;
	        }
        }
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
        (id,name,description, enrolmentkey,lang,theme,picture,hidepicture, timecreated,timemodified)
        VALUES ('$r->id','$r->name','$r->description', '$r->enrolmentkey','$r->lang',
        '$r->theme','$r->picture','$r->hidepicture', '$r->timecreated','$r->timemodified')";

    if ($result = execute_sql($sql)) {
        $record2 = new Object();
        $record2->courseid = $courseid;
        $record2->groupid = $group->id;
        $record2->timeadded = $group->timemodified;

        $groupadded = insert_record('groups_courses_groups', $record2);
        if (! $groupadded) {
            $groupid = false;
        }
    }
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
 * Delete a specified group, first removing members and links with courses and groupings. 
 * @param int $groupid The group to delete
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_db_delete_group($groupid) {
    if (!$groupid) {
        $success = false;
    } else {
        $success = true;
        // Get a list of users for the group and remove them all.

        $userids = groups_db_get_members($groupid);
        if ($userids != false) {
	        foreach($userids as $userid) {
	            $userdeleted = groups_db_remove_member($userid, $groupid);
	            if (!$userdeleted) {
	                $success = false;
	            }
	        }
        }

        // Remove any links with groupings to which the group belongs.
        //TODO: dbgroupinglib also seems to delete these links - duplication?
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

        // Remove links with courses.
		$results = delete_records('groups_courses_groups', 'groupid', $groupid);
		if ($results == false) {
			$success = false;
		}

        // Delete the group itself
        $results = delete_records($table = 'groups', $field1 = 'id', 
                                  $value1 = $groupid);
        // delete_records returns an array of the results from the sql call, 
        // not a boolean, so we have to set our return variable
        if ($results == false) {
            $success = false;
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


/******************************************************************************
 * Groups SQL clauses for modules and core.
 */

/**
 * Returns the table in which group members are stored, with a prefix 'gm'.
 * @return SQL string.
 */
function groups_members_from_sql() {
    global $CFG;
    return " {$CFG->prefix}groups_members gm ";
}

/**
 * Returns a join testing user.id against member's user ID.
 * Relies on 'user' table being included as 'user u'.
 * Used in Quiz module reports.
 * @param group ID, optional to include a test for this in the SQL.
 * @return SQL string.
 */
function groups_members_join_sql($groupid=false) {    
    $sql = ' JOIN '.groups_members_from_sql().' ON u.id = gm.userid ';
    if ($groupid) {
        $sql = "AND gm.groupid = '$groupid' ";
    }
    return $sql;
    //return ' INNER JOIN '.$CFG->prefix.'role_assignments ra ON u.id=ra.userid'.
    //       ' INNER JOIN '.$CFG->prefix.'context c ON ra.contextid=c.id AND c.contextlevel='.CONTEXT_GROUP.' AND c.instanceid='.$groupid;
}

/**
 * Returns SQL for a WHERE clause testing the group ID.
 * Optionally test the member's ID against another table's user ID column. 
 * @param groupid
 * @param userid_sql Optional user ID column selector, example "mdl_user.id", or false.
 * @return SQL string.
 */
function groups_members_where_sql($groupid, $userid_sql=false) {
    $sql = " gm.groupid = '$groupid' ";
    if ($userid_sql) {
        $sql .= "AND $userid_sql = gm.userid ";
    }
    return $sql;
}

?>
