<?php
/**
 * A grouping is a set of groups that belong to a course. 
 * There may be any number of groupings for a course and a group may
 * belong to more than one grouping.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once($CFG->dirroot.'/group/lib/basicgrouplib.php');
require_once($CFG->dirroot.'/group/db/dbgroupinglib.php');

define('GROUP_NOT_IN_GROUPING', -1);
define('GROUP_ANY_GROUPING',     0);

/*****************************
        Access/List functions  
 *****************************/


/**
 * Gets a list of the groupings for a specified course
 * @param int $courseid The id of the course
 * @return array | false An array of the ids of the groupings, or false if there 
 * are none or there was an error. 
 */
function groups_get_groupings($courseid) {
    return groups_db_get_groupings($courseid);
}


function groups_get_grouping_records($courseid) {
    global $CFG;
    if (! $courseid) {
        return false;
    }
    $sql = "SELECT gg.*
        FROM {$CFG->prefix}groups_groupings gg
        INNER JOIN {$CFG->prefix}groups_courses_groupings cg ON gg.id = cg.groupingid
        WHERE cg.courseid = '$courseid'";
    $groupings = get_records_sql($sql);
    return $groupings;
}

/**
 * Gets a list of the group IDs in a specified grouping
 * @param int $groupingid The id of the grouping
 * @return array | false. An array of the ids of the groups, or false if there
 * are none or an error occurred.
 */
function groups_get_groups_in_grouping($groupingid) {
    return groups_db_get_groups_in_grouping($groupingid);
}

/**
 * Gets data linking a grouping to each group it contains.
 * @param int $groupingid The ID of the grouping.
 * @return array | false An array of grouping-group records, or false on error.
 */
function groups_get_groups_in_grouping_records($groupingid) {
    if (! $groupingid) {
        return false;
    }
    $grouping_groups = get_records('groups_groupings_groups', 'groupingid ', 
                              $groupingid, '', $fields='id, groupid, timeadded');

    return $grouping_groups;
}


/** 
 * Gets the groupings that a group belongs to 
 * @param int $groupid The id of the group
 * @return array An array of the ids of the groupings that the group belongs to, 
 * or false if there are none or if an error occurred. 
 */
function groups_get_groupings_for_group($groupid) {
	return groups_db_get_groupings_for_group($groupid);
}

/**
 * Gets the information about a specified grouping
 * @param int $groupingid
 * @return object The grouping settings object - properties are name and 
 * description. 
 */
function groups_get_grouping_settings($groupingid) {
	return groups_db_get_grouping_settings($groupingid);
}

/**
 * Set information about a grouping
 * @param int $groupingid The grouping to update the info for.
 * @param object $groupingsettings 
 */
function groups_set_grouping_settings($groupingid, $groupingsettings) {
	return groups_db_set_grouping_settings($groupingid, $groupingsettings);
}


/**
 * Gets the name of a grouping with a specified ID
 * @param int $groupid The grouping ID.
 * @return string The name of the grouping.
 */
function groups_get_grouping_name($groupingid) {
    if (GROUP_NOT_IN_GROUPING == $groupingid) {
        return get_string('notingrouping', 'group');
    }
    elseif (GROUP_ANY_GROUPING == $groupingid) {
        return get_string('anygrouping', 'group');
    }
    $settings = groups_get_grouping_settings($groupingid);
    if ($settings && isset($settings->name)) {
        return $settings->name;
    }
    return false;
}


/**
 * Get array of group IDs for the user in a grouping.
 * @param int $userid
 * @param int $groupingid
 * @return array If the user has groups an array of group IDs, else false.
 */
function groups_get_groups_for_user_in_grouping($userid, $groupingid) {
    global $CFG;
    $sql = "SELECT gg.groupid
        FROM {$CFG->prefix}groups_groupings_groups gg
        INNER JOIN {$CFG->prefix}groups_members gm ON gm.groupid = gg.groupid
        WHERE gm.userid = '$userid'
        AND gg.groupingid = '$groupingid'";
    $records = get_records_sql($sql);

//print_object($records);
    return groups_groups_to_groupids($records);
}

/**
 * Gets a list of the groups not in a specified grouping
 * @param int $groupingid The grouping specified
 * @return array An array of the group ids
 */
function groups_get_groups_not_in_grouping($groupingid, $courseid) {
    $allgroupids = groups_get_groups($courseid);
    $groupids = array();
    foreach($allgroupids as $groupid) {
        if (!groups_belongs_to_grouping($groupid, $groupingid)) {
            array_push($groupids, $groupid);
        }
    }
    return $groupids;
}

/**
 * Gets a list of the groups not in any grouping, but in this course.
 * TODO: move to dbgroupinglib.php
 * @param $courseid If null or false, returns groupids 'not in a grouping sitewide'.
 * @return array An array of group IDs.
 */
function groups_get_groups_not_in_any_grouping($courseid) {
    global $CFG;

    $join = '';
    $where= '';
    if ($courseid) {
        $join = "INNER JOIN {$CFG->prefix}groups_courses_groups cg ON g.id = cg.groupid";
        $where= "AND cg.courseid = '$courseid'";
    }
    $sql = "SELECT g.id
        FROM {$CFG->prefix}groups g
        $join
        WHERE g.id NOT IN 
        (SELECT groupid FROM {$CFG->prefix}groups_groupings_groups)
        $where";

    $records = get_records_sql($sql);
    $groupids = groups_groups_to_groupids($records, $courseid);

    return $groupids;
}

/**
 * Gets the users for the course who are not in any group of a grouping.
 * @param int $courseid The id of the course
 * @param int $groupingid The id of the grouping
 * @param int $groupid Excludes members of a particular group
 * @return array An array of the userids of the users not in any group of 
 * the grouping or false if an error occurred. 
 */
function groups_get_users_not_in_any_group_in_grouping($courseid, $groupingid, 
                                                       $groupid = false) {
	$users = get_course_users($courseid);
    $userids = groups_users_to_userids($users); 
    $nongroupmembers = array();
    if (! $userids) {
        return $nongroupmembers;
    }
    foreach($userids as $userid) {
	    if (!groups_is_member_of_some_group_in_grouping($userid, $groupingid)) {
	      	// If a group has been specified don't include members of that group
	       	if ($groupid  and !groups_is_member($userid, $groupid)) {
	           	array_push($nongroupmembers, $userid);
	       	} else {
	       		///array_push($nongroupmembers, $userid);
	       	}
        }
    }
    return $nongroupmembers;
}


/**
 * Determines if a user is in more than one group in a grouping
 * @param int $userid The id of the user
 * @param int $groupingid The id of the grouping
 * @return boolean True if the user is in more than one group, false otherwise 
 * or if an error occurred. 
 */
function groups_user_is_in_multiple_groups($userid, $groupingid) {
	$inmultiplegroups = false;
    //TODO: $courseid?
	$groupids = groups_get_groups_for_user($courseid);
	if ($groupids != false) {
		$groupinggroupids = array();
		foreach($groupids as $groupid) {
			if (groups_belongs_to_grouping($groupid, $groupingid)) {
				array_push($groupinggroupids, $groupid);
			}
		}
		if (count($groupinggroupids) > 1) {
			$inmultiplegroups = true;
		}
	}
	return $inmultiplegroups;
}


/**
 * Returns an object with the default grouping settings values - these can of 
 * course be overridden if desired.
 * Can also be used to set the default for any values not set
 * @return object The grouping settings object. 
 */
function groups_set_default_grouping_settings($groupingsettings = null) {
        
    if (!isset($groupingsettings->name)) {
        $groupingsettings->name = 'Temporary Grouping Name';
    }

    if (!isset($groupingsettings->description)) {
        $groupingsettings->description = '';
    }

    if (!isset($groupingsettings->viewowngroup)) {
    	$groupingsettings->viewowngroup = 1;
    }

    if (!isset($groupingsettings->viewallgroupsmembers)) {
    	$groupingsettings->viewallgroupsmembers = 0;
    }

    if (!isset($groupingsettings->viewallgroupsactivities)) {
    	$groupingsettings->viewallgroupsactivities = 0;
    }

    if (!isset($groupingsettings->teachersgroupmark)) {
    	$groupingsettings->teachersgroupmark = 0;
    }  

    if (!isset($groupingsettings->teachersgroupview)) {
    	$groupingsettings->teachersgroupview = 0;
    }               

    if (!isset($groupingsettings->teachersoverride)) {
    	$groupingsettings->teachersoverride = 1;
    }  

	return $groupingsettings;
}


/**
 * Gets the grouping ID to use for a particular instance of a module in a course
 * @param int $coursemoduleid The id of the instance of the module in the course
 * @return int The id of the grouping or false if there is no such id recorded 
 * or if an error occurred. 
 */
function groups_get_grouping_for_coursemodule($coursemodule) {
	return groups_db_get_grouping_for_coursemodule($coursemodule);
}

/*****************************
        Membership functions  
 *****************************/


/**
 * Determines if a grouping with a specified id exists
 * @param int $groupingid The grouping id. 
 * @return True if the grouping exists, false otherwise or if an error occurred. 
 */  
function groups_grouping_exists($groupingid) {
	return groups_db_grouping_exists($groupingid);
}

/**
 * Determine if a course ID, grouping name and description match a grouping in the database.
 *   For backup/restorelib.php
 * @return mixed A grouping-like object with $grouping->id, or false.
 */
function groups_grouping_matches($courseid, $gg_name, $gg_description) {
    global $CFG;
    $sql = "SELECT gg.id, gg.name, gg.description
        FROM {$CFG->prefix}groups_groupings gg
        INNER JOIN {$CFG->prefix}groups_courses_groupings cg ON gg.id = cg.groupingid
        WHERE gg.name = '$gg_name'
        AND gg.description = '$gg_description'
        AND cg.courseid = '$courseid'";
    $records = get_records_sql($sql);
    $grouping = false;
    if ($records) {
        $grouping = $records[0];
    } 
    return $grouping;
}

/**
  * Determines if a group belongs to a specified grouping
  * @param int $groupid The id of the group
  * @param int $groupingid The id of the grouping
  * @return boolean. True if the group belongs to a grouping, false otherwise or
  * if an error has occurred.
  */
 function groups_belongs_to_grouping($groupid, $groupingid) {
     return groups_db_belongs_to_grouping($groupid, $groupingid);
 }
 

 /**
  * Detemines if a specified user belongs to any group of a specified grouping.
  * @param int $userid The id of the user
  * @param int $groupingid The id of the grouping
  * @return boolean True if the user belongs to some group in the grouping,
  * false otherwise or if an error occurred. 
  */
 function groups_is_member_of_some_group_in_grouping($userid, $groupingid) {
     return groups_db_is_member_of_some_group_in_grouping($userid, $groupingid);
 }
 
 /** 
  * Determines if a grouping belongs to a specified course
  * @param int $groupingid The id of the grouping
  * @param int $courseid The id of the course
  * @return boolean True if the grouping belongs to the course, false otherwise, 
  * or if an error occurred. 
  */
 function groups_grouping_belongs_to_course($groupingid, $courseid) {
 	return groups_db_grouping_belongs_to_course($groupingid, $courseid);
 }


/*****************************
        Creation functions  
 *****************************/


/**
 * Marks a set of groups as a grouping. This is a best effort operation.
 * It can also be used to create an 'empty' grouping to which
 * groups can be added by passing an empty array for the group ids.
 * @param array $groupids An array of the ids of the groups to marks as a
 * grouping. 
 * @param int $courseid The id of the course for which the groups should form
 * a grouping
 * @return int | false The id of the grouping, or false if an error occurred. 
 * Also returns false if any of the groups specified do not belong to the 
 * course. 
 */
function groups_create_grouping($courseid, $groupingsettings = false) {
    $groupingid = groups_db_create_grouping($courseid, $groupingsettings);

    return $groupingid;
}


/**
 * Adds a specified group to a specified grouping.
 * @param int $groupid The id of the group
 * @param int $groupingid The id of the grouping
 * @return boolean True if the group was added successfully or the group already 
 * belonged to the grouping, false otherwise. Also returns false if the group 
 * doesn't belong to the same course as the grouping. 
 */
function groups_add_group_to_grouping($groupid, $groupingid) {
	if (GROUP_NOT_IN_GROUPING == $groupingid) {
        return true;
    }
    $belongstogrouping = groups_belongs_to_grouping($groupid, $groupingid);
	
    if (!groups_grouping_exists($groupingid)) {
		$groupadded = false;
	} elseif (!$belongstogrouping) {
		$groupadded = groups_db_add_group_to_grouping($groupid, $groupingid); 
	} else {
		$groupadded = true;
	}
	
    return $groupadded;  
}


/**
 * Sets the name of a grouping overwriting any current name that the grouping 
 * has
 * @param int $groupingid The id of the grouping specified
 * @param string $name The name to give the grouping
 * @return boolean True if the grouping settings was added successfully, false 
 * otherwise.
 */
function groups_set_grouping_name($groupingid, $name) {
    return groups_db_set_grouping_name($groupingid, $name);
}


/**
 * Sets a grouping to use for a particular instance of a module in a course
 * @param int $groupingid The id of the grouping
 * @param int $coursemoduleid The id of the instance of the module in the course
 * @return boolean True if the operation was successful, false otherwise
 */
function groups_set_grouping_for_coursemodule($groupingid, $coursemoduleid) {
	return groups_db_set_grouping_for_coursemodule($groupingid, 
	                                               $coursemoduleid);
}


/*****************************
        Update functions  
 *****************************/

function groups_update_grouping($data, $courseid) {
    $oldgrouping = get_record('groups_groupings', 'id', $data->id); // should not fail, already tested above

    // Update with the new data
    if (update_record('groups_groupings', $data)) {

        $grouping = get_record('groups_groupings', 'id', $data->id);

        add_to_log($grouping->id, "groups_groupings", "update", "grouping.php?courseid=$courseid&amp;id=$grouping->id", "");

        return true;

    }

    return false;
    
}
/*****************************
        Deletion functions  
 *****************************/

/** 
 * Removes a specified group from a grouping. Note that this does 
 * not delete the group. 
 * @param int $groupid The id of the group.
 * @param int $groupingid The id of the grouping
 * @return boolean True if the deletion was successful, false otherwise. 
 */
function groups_remove_group_from_grouping($groupid, $groupingid) {
    if (GROUP_NOT_IN_GROUPING == $groupingid) {
        //Quietly ignore.
        return true;
    }
    return groups_db_remove_group_from_grouping($groupid, $groupingid);
}

/** 
 * Removes a grouping from a course - note that this function does not delete 
 * any of the groups in the grouping. 
 * @param int $groupingid The id of the grouping
 * @return boolean True if the deletion was successful, false otherwise.
 */ 
function groups_delete_grouping($groupingid) {
    if (GROUP_NOT_IN_GROUPING == $groupingid) {
        return false;
    }
    return groups_db_delete_grouping($groupingid);
    
}

/**
 * Delete all groupings from a course. Groups MUST be deleted first.
 * TODO: If groups or groupings are to be shared between courses, think again!
 * @param $courseid The course ID.
 * @return boolean True if all deletes were successful, false otherwise.
 */
function groups_delete_all_groupings($courseid) {
    if (! $courseid) {
        return false;
    }
    $groupingids = groups_get_groupings($courseid);
    if (! $groupingids) {
        return true;
    }
    $success = true;
    foreach ($groupingids as $gg_id) {
        $success = $success && groups_db_delete_grouping($gg_id);
    }
    return $success;
}

?>
