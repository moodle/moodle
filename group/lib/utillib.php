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
        
        if ($groupids === false) {
            return false;
        }
        
        return count($groupids);
    } elseif (GROUP_ANY_GROUPING == $groupingid) {
        return count_records('groups', 'courseid', $courseid);
    } else {
        return count_records('groupings_groups', 'groupingid ', $groupingid);
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
            //Warn if there's no "groupid" member.
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
 * @return array Array of group objects INDEXED by group ID, with basic or all data.
 */
function groups_groupids_to_groups($groupids, $courseid=false, $alldata=false) {
    if (! $groupids) {
        return false;
    }
    $groups = array();
    foreach ($groupids as $id) {
        $groups[$id] = groups_get_group_settings($id, $courseid, $alldata);
    }
    return $groups;
}


/**
 * Get a sorted array of group-id/display-name objects.
 * @param array $groupids Array of group IDs
 * @param bool $justnames Return names only as values, not objects. Needed
 *   for print_group_menu in weblib
 * @return array If $justnames is set, returns an array of id=>name. Otherwise
 *   returns an array without specific keys of objects containing id, name
 */
function groups_groupids_to_group_names($groupids, $justnames=false) {
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

    if ($justnames) {
        $namesonly = array();
        foreach ($group_names as $id => $object) {
            $namesonly[$object->id] = $object->name;
        }
        return $namesonly;
    }

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
 * Gets the course ID for a given group.
 */
function groups_get_course($groupid) {
    $course_group = get_record('groups', 'id', $groupid);
    if ($course_group) {
        return $course_group->courseid;
    }
    return false;
}


?>
