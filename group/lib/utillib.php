<?php
/**
 * Utility functions for groups.
 *
 * Functions to get information about users and courses that we could do with 
 * that don't use any of the groups and that I can't find anywhere else!
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
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
    if ($groupsettings) {
        $groupname = $groupsettings->name;
        $count = groups_get_no_group_members($groupid);
        return "$groupname ($count)"; 
    }
	return false;
}


/**
 * Returns the display name of a grouping - this is the grouping name followed 
 * by the number of groups in the
 * grouping in brackets
 * @param int $groupingid The grouping id
 * @return string The display name of the grouping
 */
function groups_get_grouping_displayname($groupingid) {
    if (GROUP_NOT_IN_GROUPING == $groupingid) {
        return get_string('notingrouping', 'group');
    }    
	$groupingsettings = groups_get_grouping_settings($groupingid);
    if ($groupingsettings) {	
        $groupingname = $groupingsettings->name;
        $count = groups_get_no_groups_in_grouping($groupingid);
        return "$groupingname ($count)";
    }
    return false;
}


/**
 * Takes an array of users (i.e of objects) and converts it in the corresponding 
 * array of userids. 
 * @param $users array The array of users
 * @return array The array of user ids, or false if an error occurred 
 */
function groups_users_to_userids($users) {
    if (! $users) {
        return false;
    }
    $userids = array();
    foreach($users as $user) {
        array_push($userids, $user->id);
    }
    return $userids;
}

/**
 * Takes an array of groups (i.e of objects) and converts it to the 
 * corresponding array of group IDs. 
 * @param $groups array The array of group-like objects, only the $group->id member is required. 
 * @return array The array of group IDs, or false if an error occurred 
 */
function groups_groups_to_groupids($groups) {
	if (! $groups) {
        return false;
    }
    $groupids = array();
	foreach ($groups as $group) {
		array_push($groupids, $group->id);
	}
	return $groupids;
}

// @@@ TO DO 
function groups_groupid_to_group($groupid) {
}

/**
 * Given an array of group IDs get an array of group objects.
 * TODO: quick and dirty. Replace with SQL?
 * @param $groupids Array of group IDs.
 * @param $courseid Default false, or Course ID.
 * @param $alldata Default false, or get complete record for group.
 * @param array Array of group objects, with basic or all data.
 */
function groups_groupids_to_groups($groupids, $courseid=false, $alldata=false) {
    if (! $groupids) {
        return false;
    }
    $groups = array();
    foreach ($groupids as $id) {
        $groups[] = groups_get_group_settings($id, $courseid, $alldata);
    }
    return $groups;
}

function groups_groupingids_to_groupings($groupingids) {
    if (! $groupingids) {
        return false;
    }
    $groupings = array();
    foreach ($groupingids as $id) {
        $groupings[] = groups_get_grouping_settings($id);
    }
    return $groupings;
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

/**
 * Gets the course ID for a given group.
 */
function groups_get_course($groupid) {
    $course_group = get_record('groups_courses_groups', 'groupid', $groupid);
    if ($course_group) {
        return $course_group->courseid;
    }
    return false;
}

/**
 * Return the address for the group settings page.
 * (For /user/index.php etc.)
 * @param $courseid
 * @param $groupid
 * @param $groupingid Optional grouping ID
 * @param $html True for HTML pages, eg. on error. False for HTTP redirects.
 * @param $param Extra parameters.
 */
function groups_group_edit_url($courseid, $groupid, $groupingid=false, $html=true, $param=false) {
    global $CFG;
    $html ? $sep = '&amp;' : $sep = '&';
    $url = $CFG->wwwroot.'/group/group.php?courseid='.$courseid;
    if ($groupid) {
        $url .= $sep.'group='.$groupid;
    }
    if ($groupingid) {
        $url .= $sep.'grouping='.$groupingid;
    }
    if ($param) {
        $url .= $sep.$param;
    }
    return $url;
}

/** Internal use only. */
function groups_grouping_edit_url($courseid, $groupingid=false, $html=true) {
    global $CFG;
    $html ? $sep = '&amp;' : $sep = '&';
    $url = $CFG->wwwroot.'/group/grouping.php?courseid='.$courseid;
    if ($groupingid) {
        $url .= $sep.'grouping='.$groupingid;
    }
    return $url;
}

/** Internal use only. */
function groups_members_add_url($courseid, $groupid, $groupingid=false, $html=true) {
    global $CFG;
    $html ? $sep = '&amp;' : $sep = '&';
    $url = $CFG->wwwroot.'/group/assign.php?courseid='.$courseid.$sep.'group='.$groupid;
    if ($groupingid) {
        $url .= $sep.'grouping='.$groupingid;
    }
    return $url;
}

/**
 * Return the address for the main group management page.
 * (For admin block.)
 */
function groups_home_url($courseid, $groupid=false, $groupingid=false, $html=true) {
    global $CFG;
    $html ? $sep = '&amp;' : $sep = '&';
    $url = $CFG->wwwroot.'/group/index.php?id='.$courseid;
    if ($groupid) {
        $url .= $sep.'group='.$groupid;
    }
    if ($groupingid) {
        $url .= $sep.'grouping='.$groupingid;
    }
    return $url;
}

/**
 * Returns the first button action with the given prefix, taken from
 * POST or GET, otherwise returns false.
 * See /lib/moodlelib.php function optional_param.
 * @param $prefix 'act_' as in 'action'.
 * @return string The action without the prefix, or false if no action found.
 */
function groups_param_action($prefix = 'act_') {
    $action = false;
//($_SERVER['QUERY_STRING'] && preg_match("/$prefix(.+?)=(.+)/", $_SERVER['QUERY_STRING'], $matches)) { //b_(.*?)[&;]{0,1}/

    if ($_POST) {
        $form_vars = $_POST;
    }
    elseif ($_GET) {
        $form_vars = $_GET; 
    }
    if ($form_vars) {
        foreach ($form_vars as $key => $value) {
            if (preg_match("/$prefix(.+)/", $key, $matches)) {
                $action = $matches[1];
                break;
            }
        }
    }
    if ($action && !preg_match('/^\w+$/', $action)) {
        $action = false;
        error('Action had wrong type.');
    }
    ///if (debugging()) echo 'Debug: '.$action;
    return $action;
}

?>