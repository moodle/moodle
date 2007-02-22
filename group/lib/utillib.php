<?php
/**
 * Utility functions for groups.
 *
 * Functions we need independent of groups about users and courses.
 * And groups utility/ user-interface functions.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
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
function groups_count_group_members($groupid) {
    return count_records('groups_members', 'groupid ', $groupid);
}


/**
 * Gets the number of groups in a specified grouping
 * @param int $groupingid The grouping specified
 * @param int $courseid The related course.
 * @return int The number of groups in the grouping
 */
function groups_count_groups_in_grouping($groupingid, $courseid) {
    if (GROUP_NOT_IN_GROUPING == $groupingid) {
        $groupids = groups_get_groups_not_in_any_grouping($courseid);
        return count($groupids);
    } elseif (GROUP_ANY_GROUPING == $groupingid) {
        return count_records('groups_courses_groups', 'courseid', $courseid);
    } else {
        return count_records('groups_groupings_groups', 'groupingid ', $groupingid);
    }
}


/**
 * Returns the display name of a user - the full name of the user 
 * prefixed by '#' for editing teachers and '-' for teachers.
 * @param int $userid The ID of the user.
 * @param int $courseid The ID of the related-course.
 * @return string The display name of the user.
 */
function groups_get_user_displayname($userid, $courseid) {
	if ($courseid == false) {
		$fullname = false;
	} else {
		$user = groups_get_user($userid);
	    $fullname = fullname($user, true);
        //TODO: isteacher, isteacheredit.
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
 * Returns the display name of a group - the group name followed by 
 * the number of members in brackets.
 * @param int $groupid The group ID.
 * @return string The display name of the group
 */
function groups_get_group_displayname($groupid) {
	if ($groupname = groups_get_group_name($groupid)) {
        $count = groups_count_group_members($groupid);
        return "$groupname ($count)"; 
    }
	return false;
}


/**
 * Returns the display name of a grouping - the grouping name followed 
 * by the number of groups in the grouping in brackets.
 * @param int $groupingid The grouping ID.
 * @param int $courseid The related course.
 * @return string The display name of the grouping
 */
function groups_get_grouping_displayname($groupingid, $courseid) {
    if ($groupingname = groups_get_grouping_name($groupingid)) {
        $count = groups_count_groups_in_grouping($groupingid, $courseid);
        return "$groupingname ($count)";
    }
    return false;
}


/**
 * Takes an array of users (i.e of objects) and converts it in the corresponding 
 * array of user IDs. 
 * @param $users array The array of users
 * @return array The array of user IDs, or false if an error occurred 
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
 * Get an sorted array of user-id/display-name objects.
 */
function groups_userids_to_user_names($userids, $courseid) {
    if (! $userids) {
        return array();
    }
    $member_names = array();
    foreach ($userids as $id) {
        $user = new object;
        $user->id = $id;
        $user->name = groups_get_user_displayname($id, $courseid);
        $member_names[] = $user;
    }
    if (! usort($member_names, 'groups_compare_name')) {
        debug('Error usort [groups_compare_name].');
    }
    return $member_names;
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
        if (isset($group->id)) {
		    array_push($groupids, $group->id);
        } else {
            array_push($groupids, $group->groupid);
        }
	}
	return $groupids;
}


/**
 * Given an array of group IDs get an array of group objects.
 * TODO: quick and dirty. Replace with SQL?
 * @param $groupids Array of group IDs.
 * @param $courseid Default false, or the course ID for backwards compatibility.
 * @param $alldata Default false, or get complete record for group.
 * @return array Array of group objects, with basic or all data.
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


/**
 * Get a sorted array of group-id/display-name objects.
 */
function groups_groupids_to_group_names($groupids) {
    if (! $groupids) {
        return array();
    }
    $group_names = array();
    foreach ($groupids as $id) {
        $gname = new object;
        $gname->id = $id;
        $gname->name = groups_get_group_displayname($id);
        $group_names[] = $gname;
    }
    if (! usort($group_names, 'groups_compare_name')) {
        debug('Error usort [groups_compare_name].');
    }
    /*// Put the groups into a hash and sort them
    foreach($groupids as $id) {
        $listgroups[$id] = groups_get_group_displayname($id);
    }
    natcasesort($listgroups);

    $group_names = array();
    foreach ($listgroups as $id => $name) {
        $gname = new object;
        $gname->id = $id;
        $gname->name = $name;
        $group_names[] = $gname;
    }*/
    return $group_names;
}


/**
 * Comparison function for 'usort' on objects with a name member.
 * Equivalent to 'natcasesort'.
 */
function groups_compare_name($obj1, $obj2) {
    if (!$obj1 || !$obj2 || !isset($obj1->name) || !isset($obj2->name)) {
        debug('Error, groups_compare_name.');
    }
    return strcasecmp($obj1->name, $obj2->name);
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
 * do this and we need this for fullname()
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
 * TODO: need to put the database bit into a db file 
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
 * @param $groupingid Default false, or optionally a grouping ID.
 * @param $html Default true for HTML pages, eg. on error. False for HTTP redirects.
 * @param $param Extra parameters.
 * @return string An absolute URL.
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

/** 
 * Return the address for the grouping settings page - Internal group use only.
 * @param $courseid
 * @param $groupingid Default false, or optionally a grouping ID.
 * @param $html Default true for HTML pages, eg. on error. False for HTTP redirects.
 * @param $param Extra parameters.
 * @return string An absolute URL.
 */
function groups_grouping_edit_url($courseid, $groupingid=false, $html=true, $param=false) {
    global $CFG;
    $html ? $sep = '&amp;' : $sep = '&';
    $url = $CFG->wwwroot.'/group/grouping.php?courseid='.$courseid;
    if ($groupingid) {
        $url .= $sep.'grouping='.$groupingid;
    }
    if ($param) {
        $url .= $sep.$param;
    }
    return $url;
}

/**
 * Return the address for the add/remove users page - Internal group use only.
 * @param $courseid
 * @param $groupid
 * @param $groupingid Default false, or optionally a grouping ID.
 * @param $html Default true for HTML pages, eg. on error. False for HTTP redirects.
 * @return string An absolute URL.
 */
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
 * Return the address for the main group management page. (For admin block etc.)
 * @param $courseid
 * @param $groupid Default false, or optionally a group ID.
 * @param $groupingid Default false, or optionally a grouping ID.
 * @param $html Default true for HTML pages, eg. on error. False for HTTP redirects.
 * @return string An absolute URL.
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