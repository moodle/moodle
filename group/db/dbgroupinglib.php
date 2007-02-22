<?php
/**
 * Functions to make changes to groupings in the database. In general these 
 * access the tables:
 *     groups_groupings, groups_courses_groupings and groups_groupings_groups
 * although some access all the tables that store information about groups.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once($CFG->libdir.'/datalib.php');

/*******************************************************************************
        Access/List functions  
 ******************************************************************************/

/**
 * Gets a list of the groupings for a specified course
 * @param int $courseid The id of the course
 * @return array | false An array of the ids of the groupings, or false if there 
 * are none or there was an error. 
 */
function groups_db_get_groupings($courseid) {
    if (!$courseid) {
        $groupingids = false;
    } else {
        $groupings = get_records('groups_courses_groupings', 'courseid ', 
                                 $courseid, '', $fields='id, groupingid');
		if (!$groupings) {
			$groupingids = false;
		} else {
	        // Put the results into an array
	        $groupingids = array();
	        foreach ($groupings as $grouping) {
	            array_push($groupingids, $grouping->groupingid);
	        }
		}
    }
    
    return $groupingids;
}


/**
 * Gets a list of the groups in a specified grouping
 * @param int $groupingid The id of the grouping
 * @return array | false. An array of the ids of the groups, or false if there
 * are none or an error occurred.
 */
function groups_db_get_groups_in_grouping($groupingid) {
    if (!$groupingid) {
        $groupid = false;
    } else {

        $groups = get_records('groups_groupings_groups', 'groupingid ', 
                              $groupingid, '', $fields='id, groupid');
        if (!$groups) {
			$groupids = false;
		} else {
			// Put the results into an array
			$groupids = array();
			foreach ($groups as $group) {
				array_push($groupids, $group->groupid);
			}
		}
    }
    
    return $groupids;
}


/*
 * Gets the groupings that a group belongs to 
 * @param int $groupid The id of the group
 * @return array An array of the ids of the groupings that the group belongs to, 
 * or false if there are none or if an error occurred. 
 */
function groups_db_get_groupings_for_group($groupid) {
	if (!$groupid) {
		$groupingids = false;
	} else {
		$groupings = get_records('groups_groupings_groups', 'groupid ', 
		                         $groupid, '', $fields='id, groupingid');
		if (!$groupings) {
				$groupingids = false;
		} else {
			// Put the results into an array
	        $groupingids = array();
	        foreach ($groupings as $grouping) {
	            array_push($groupingids, $grouping->groupingid);
	        }
		} 
	}
	
	return $groupingids;
}


/**
 * Gets the information about a specified grouping
 * @param int $groupingid
 * @return object The grouping settings object - properties are name and 
 * description. 
 */
function groups_db_get_grouping_settings($groupingid) {
   if (!$groupingid) {
        $groupingsettings = false;
    } else {
        global $CFG;
        $sql = "SELECT *
                FROM {$CFG->prefix}groups_groupings
                WHERE id = $groupingid";
        $groupingsettings = get_record_sql($sql);
    }
        
    return $groupingsettings;
}

/**
 * Gets the grouping to use for a particular instance of a module in a course
 * @param int $coursemoduleid The id of the instance of the module in the course
 * @return int The id of the grouping or false if there is no such id recorded 
 * or if an error occurred. 
 */
function groups_db_get_grouping_for_coursemodule($cm) {
	if (is_object($cm) and isset($cm->course) and isset($cm->groupingid)) {
        //Do NOT test cm->module!
        return $cm->groupingid;
    } elseif (is_numeric($cm)) {
        // Treat param as the course module ID.
        $coursemoduleid = $cm;
        $record = get_record('course_modules', 'id', $coursemoduleid, 'id, groupingid');
        if ($record and isset($record->groupingid)) {
            return $record->groupingid;
        }
    }
    return false;
}


/*******************************************************************************
        Membership functions  
 ******************************************************************************/
 
 
 /**
 * Determines if a grouping with a specified id exists
 * @param int $groupingid The grouping id. 
 * @return True if the grouping exists, false otherwise or if an error occurred. 
 */  
function groups_db_grouping_exists($groupingid) {
    if (!$groupingid) {
        $exists = false;
    } else {
        $exists = record_exists('groups_groupings', 'id', 
                                  $groupingid);
    }
    
    return $exists;
}


 /**
  * Determines if a group belongs to any grouping for the course that it belongs
  * to
  * @param int $groupid The id of the group
  * @return boolean. True if the group belongs to a grouping, false otherwise or
  * if an error has occurred.
  */
 function groups_db_belongs_to_any_grouping($groupid) {
    if (!$groupid) {
        $isingrouping = false;
    } else {
        $isingrouping = record_exists('groups_groupings_groups', 'groupid', 
                                  $groupid);
    }
    
    return $isingrouping;
 }

 
 /**
  * Determines if a group belongs to a specified grouping
  * @param int $groupid The id of the group
  * @param int $groupingid The id of the grouping
  * @return boolean. True if the group belongs to a grouping, false otherwise or
  * if an error has occurred.
  */
 function groups_db_belongs_to_grouping($groupid, $groupingid) {
    if (!$groupid or !$groupingid) {
        $isingrouping = false;
    } else {
        $isingrouping = record_exists('groups_groupings_groups', 'groupid', 
                                  $groupid, 'groupingid', $groupingid);
    }
    
    return $isingrouping;
 }
 
 
  /**
  * Detemines if a specified user belongs to any group of a specified grouping.
  * @param int $userid The id of the user
  * @param int $groupingid The id of the grouping
  * @return boolean True if the user belongs to some group in the grouping,
  * false otherwise or if an error occurred. 
  */
 function groups_db_is_member_of_some_group_in_grouping($userid, $groupingid) {
    if (!$userid or !$groupingid) {
        $belongstogroup = false;
    } else {
        global $CFG;
        $sql = "SELECT gm.id
        FROM {$CFG->prefix}groups_groupings_groups gg
        INNER JOIN {$CFG->prefix}groups_members gm
        ON gg.groupid = gm.groupid
        WHERE gm.userid = '$userid' AND gg.groupingid = '$groupingid'";
        $belongstogroup = record_exists_sql($sql);
    }
    return $belongstogroup;
 }
 
 
  /** 
  * Determines if a grouping belongs to a specified course
  * @param int $groupingid The id of the grouping
  * @param int $courseid The id of the course
  * @return boolean True if the grouping belongs to the course, false otherwise, 
  * or if an error occurred. 
  */
 function groups_db_grouping_belongs_to_course($groupingid, $courseid) {
    if (!$groupingid or !$courseid) {
        $belongstocourse = false;
    } else {
        $belongstocourse = record_exists('groups_courses_groupings', 
                                         'groupingid', $groupingid, 'courseid', 
                                         $courseid);
    }
    
    return $belongstocourse;
 }




/*******************************************************************************
        Creation/Update functions  
 ******************************************************************************/


/**
 * Marks a set of groups as a grouping. 
 * 
 * @param array $groupidarray An array of the ids of the groups to marks as a
 * grouping. 
 * @param int $courseid The id of the course for which the groups should form
 * a grouping
 * @return int | false The id of the grouping, or false if an error occurred. 
 * Also returns false if any of the groups specified do not belong to the 
 * course. 
 */
function groups_db_create_grouping($courseid, $groupingsettings = false) {
    if (!$courseid or !groups_get_course_info($courseid)) {
        $groupingid = false; 
    } else {
    	// Replace any empty groupsettings
        $groupingsettings = groups_set_default_grouping_settings($groupingsettings);
        $record = $groupingsettings;
        $record->timecreated = time();

        $groupingid = insert_record('groups_groupings', $record);
        if ($groupingid != false) {
            $record2 = new Object();
	        $record2->courseid = $courseid;
	        $record2->groupingid = $groupingid;
	        $record2->timeadded = time();
	        $id= insert_record('groups_courses_groupings', $record2);
	        if (!$id) {
	        	$groupingid = false;
	        }
        } 
    }
    
    return $groupingid;
}


/**
 * Adds a specified group to a specified grouping. 
 * @param int $groupid The id of the group
 * @param int $groupingid The id of the grouping
 * @return boolean True if the group was added successfully, false otherwise
 */
function groups_db_add_group_to_grouping($groupid, $groupingid) {
   if (!$groupid or !$groupingid or !groups_db_group_exists($groupid) 
       or !groups_db_grouping_exists($groupingid)) {
        $success = false;
    } else {
        $success = true;
        $record = new Object();
        $record->groupingid = $groupingid;
        $record->groupid = $groupid;
        $record->timeadded = time();

        $results = insert_record('groups_groupings_groups', $record);
        if (!$results) {
            $success = false;
        }
    }
    
    return $groupingid;   
}


/**
 * Set information about a grouping
 * @param int $groupingid The grouping to update the info for.
 * @param object $groupingsettings 
 */
function groups_db_set_grouping_settings($groupingid, $groupingsettings) {
	$success = true;
    if (!$groupingid or !$groupingsettings 
        or !groups_db_grouping_exists($groupingid)) {
        $success = false; 
    } else {
    	// Replace any empty group settings. 
    	$record = $groupingsettings;
        $record->id = $groupingid;
        $record->timemodified = time();
        $result = update_record('groups_groupings', $record);
        if (!$result) {
            $success = false;
        }
    }
    
    return $success;
}


/**
 * Sets a grouping to use for a particular instance of a module in a course
 * @param int $groupingid The id of the grouping
 * @param int $coursemoduleid The id of the instance of the module in the course
 * @return boolean True if the operation was successful, false otherwise
 */
function groups_db_set_grouping_for_coursemodule($groupingid, $coursemoduleid) {
	$success = true;
	if (!$groupingid or !$coursemoduleid) {
		$success = false;
	} else {
        $record = new Object();
		$record->id = $coursemoduleid;
		$record->groupingid = $groupingid;
		$result = update_record('course_modules', $record);
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
 * Removes a specified group from a specified grouping. Note that this does 
 * not delete the group. 
 * @param int $groupid The id of the group.
 * @param int $groupingid The id of the grouping
 * @return boolean True if the deletion was successful, false otherwise. 
 */
function groups_db_remove_group_from_grouping($groupid, $groupingid) {
	$success = true;
    if (!$groupingid or !$groupid) {
        $success = false;
    } else {
        $results = delete_records('groups_groupings_groups', 'groupid', 
                                  $groupid, 'groupingid', $groupingid);
        // delete_records returns an array of the results from the sql call, 
        // not a boolean, so we have to set our return variable
        if ($results == false) {
            $success = false;
        } 
    }
    
    return $success;
}


/** 
 * Removes a grouping from a course - note that this function removes but does 
 * not delete any of the groups in the grouping.  
 * @param int $groupingid The id of the grouping
 * @return boolean True if the deletion was successful, false otherwise.
 */ 
function groups_db_delete_grouping($groupingid) {
	$success = true;
    if (!$groupingid) {
        $success = false;
    } else {

        $results = delete_records('groups_courses_groupings', 'groupingid', 
                                  $groupingid);
        if ($results == false) {
            $success = false;
        }                           
               
        $results = delete_records('groups_groupings_groups', 'groupingid', 
                                  $groupingid);
        if ($results == false) {
            $success = false;
        } 
        
        $results = delete_records('groups_groupings', 'id', $groupingid);
        if ($results == false) {
            $success = false;
        } 
    }

    return $success;
    
}

?>