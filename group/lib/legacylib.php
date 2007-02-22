<?php
/**
 * Legacy groups functions - these were in moodlelib.php, datalib.php, weblib.php
 *
 * @@@ Don't look at this file - still tons to do! 
 *
 * TODO: For the moment these functions are in /lib/deprecatedlib.php
 *   get_group_students
 *   get_group_teachers
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk and others
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */


/**
 * Returns the groupid of a group with the name specified for the course 
 * specified. If there's more than one with the name specified it returns the 
 * first one it finds it the database, so you need to be careful how you use it! 
 * This is needed to support upload of users into the database
 * @param int $courseid The id of the course
 * @param string $groupname
 * @return int $groupid
 */
function groups_get_group_by_name($courseid, $groupname) {
	//uploaduser.php, get_record("groups","courseid",$course[$i]->id,"name",$addgroup[$i])
    $groupids = groups_db_get_groups($courseid);
    if (! $groupids) {
        return false;
    }
    foreach ($groupids as $id) {
        if (groups_get_group_name($id) == $groupname) {
            return $id;
        }
    }
    return false;
}

/**
 * Returns an array of group objects that the user is a member of
 * in the given course.  If userid isn't specified, then return a
 * list of all groups in the course.
 *
 * @uses $CFG
 * @param int $courseid The id of the course in question.
 * @param int $userid The id of the user in question as found in the 'user' 
 * table 'id' field.
 * @return object
 */
function get_groups($courseid, $userid=0) {
	if ($userid) {
		$groupids = groups_get_groups_for_user($userid, $courseid);
	} else {
		$groupids = groups_get_groups($courseid);
	}

	return groups_groupids_to_groups($groupids, $courseid, $alldata=true);
}


/**
 * Returns the user's group in a particular course
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $groupid The id of the group the user is in.
 * @return object
 */
function user_group($courseid, $userid) {
    $groupids = groups_get_groups_for_user($userid, $courseid);
    return groups_groupids_to_groups($groupids);
}


/**
 * Determines if the user is a member of the given group.
 * TODO: replace all calls with 'groups_is_member'.
 *
 * @param int $groupid The group to check for membership.
 * @param int $userid The user to check against the group.
 * @return boolean True if the user is a member, false otherwise.
 */
function ismember($groupid, $userid = null) {
	return groups_is_member($groupid, $userid);
}

/**
 * Returns an array of user objects
 *
 * @uses $CFG
 * @param int $groupid The group in question.
 * @param string $sort ?
 * @param string $exceptions ?
 * @return object
 * @todo Finish documenting this function
 */
function get_group_users($groupid, $sort='u.lastaccess DESC', $exceptions='', 
                         $fields='u.*') {
    global $CFG;
    if (!empty($exceptions)) {
        $except = ' AND u.id NOT IN ('. $exceptions .') ';
    } else {
        $except = '';
    }
    // in postgres, you can't have things in sort that aren't in the select, so...
    $extrafield = str_replace('ASC','',$sort);
    $extrafield = str_replace('DESC','',$extrafield);
    $extrafield = trim($extrafield);
    if (!empty($extrafield)) {
        $extrafield = ','.$extrafield;
    }
    return get_records_sql("SELECT DISTINCT $fields $extrafield
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}groups_members m
                             WHERE m.groupid = '$groupid'
                               AND m.userid = u.id $except
                          ORDER BY $sort");
}




/**
 * Add a user to a group, return true upon success or if user already a group 
 * member
 *
 * @param int $groupid  The group id to add user to
 * @param int $userid   The user id to add to the group
 * @return bool
 */
function add_user_to_group($groupid, $userid) {
    return groups_add_member($groupid, $userid);
}


/**
 * Get the IDs for the user's groups in the given course.
 *
 * @uses $USER
 * @param int $courseid The course being examined - the 'course' table id field.
 * @return array An _array_ of groupids.
 * (Was return $groupids[0] - consequences!)
 */
function mygroupid($courseid) {
    global $USER;
	$groupids = groups_get_groups_for_user($USER->id, $courseid);
   	return $groupids;
}

/**
 * This now returns either false or SEPARATEGROUPS. If you want VISIBLE GROUPS 
 * with legacy code, you'll need to upgrade. 
 */
function groupmode($course, $cm=null) {

    if (is_object($cm) && isset($cm->groupmode) && !isset($course->groupmodeforce)) {
        return $cm->groupmode;
    }
    return $course->groupmode;
    
    /*if ($cm and !$course->groupingid) {
        //TODO: was $coursemodule
        return groups_has_groups_setup_for_instance($cm);
    } else {
    	return groups_has_groups_setup($course->id);
    }*/
}


/**
 * Sets the current group in the session variable
 * When $SESSION->currentgroup[$courseid] is set to 0 it means, show all groups. 
 * Sets currentgroup[$courseid] in the session variable appropriately.
 * Does not do any permission checking. 
 * @uses $SESSION
 * @param int $courseid The course being examined - relates to id field in 
 * 'course' table.
 * @param int $groupid The group being examined.
 * @return int Current group id which was set by this function
 */
function set_current_group($courseid, $groupid) {
    global $SESSION;
    return $SESSION->currentgroup[$courseid] = $groupid;
}


/**
 * Gets the current group - either from the session variable or from the database. 
 *
 * @uses $USER
 * @uses $SESSION
 * @param int $courseid The course being examined - relates to id field in 
 * 'course' table.
 * @param bool $full If true, the return value is a full record object. 
 * If false, just the id of the record.
 */
function get_current_group($courseid, $full = false) {
    global $SESSION;

    $mygroupid = mygroupid($courseid);
    if (is_array($mygroupid)) {
        $mygroupid = array_shift($mygroupid);
    }

    if (isset($SESSION->currentgroup[$courseid])) {
    	$currentgroup = $SESSION->currentgroup[$courseid];
    } else {
    	$currentgroup = $mygroupid;
    }
    
    if ($currentgroup) {
    	$SESSION->currentgroup[$courseid] = $mygroupid;
    }

    if ($full) {
        return groups_groupid_to_group($currentgroup);
    } else {
        return $currentgroup;
    }
}


/**
 * A combination function to make it easier for modules
 * to set up groups.
 *
 * It will use a given "groupid" parameter and try to use
 * that to reset the current group for the user.
 *
 * @uses VISIBLEGROUPS
 * @param course $course A {@link $COURSE} object
 * @param int $groupmode Either NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
 * @param int $groupid Will try to use this optional parameter to
 *            reset the current group for the user
 * @return int|false Returns the current group id or false if error.
 */
function get_and_set_current_group($course, $groupmode, $groupid=-1) {
	//TODO: ?? groups_has_permission($userid, $groupingid, $courseid, $groupid, $permissiontype);

	// Sets to the specified group, provided the current user has view permission 
    if (!$groupmode) {   // Groups don't even apply
        return false;
    }

    $currentgroupid = get_current_group($course->id);

    if ($groupid < 0) {  // No change was specified
        return $currentgroupid;
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if ($groupid) {      // Try to change the current group to this groupid
        if (groups_group_belongs_to_course($groupid, $course->id)) { // Exists  TODO:check.
            if (has_capability('moodle/site:accessallgroups', $context)) {  // Sets current default group
                $currentgroupid = set_current_group($course->id, $groupid);

            } elseif ($groupmode == VISIBLEGROUPS) {
                  // All groups are visible
                //if (ismember($group->id)){
                    $currentgroupid = set_current_group($course->id, $groupid); //set this since he might post
                /*)}else {
                    $currentgroupid = $group->id;*/
            } elseif ($groupmode == SEPARATEGROUPS) { // student in separate groups switching
                if (ismember($group->id)) { //check if is a member
                    $currentgroupid = set_current_group($course->id, $groupid); //might need to set_current_group?
                }
                else {
                    echo($group->id);
                    notify('you do not belong to this group!',error);
                }
            }
        }
    } else { // When groupid = 0 it means show ALL groups
        // this is changed, non editting teacher needs access to group 0 as well,
        // for viewing work in visible groups (need to set current group for multiple pages)
        if (has_capability('moodle/site:accessallgroups', $context) AND ($groupmode == VISIBLEGROUPS)) { // Sets current default group
            $currentgroupid = set_current_group($course->id, 0);

        } elseif ($groupmode == VISIBLEGROUPS) {  // All groups are visible
            $currentgroupid = 0;
        }
    }

    return $currentgroupid;
}


/**
 * A big combination function to make it easier for modules
 * to set up groups.
 *
 * Terminates if the current user shouldn't be looking at this group
 * Otherwise returns the current group if there is one
 * Otherwise returns false if groups aren't relevant
 *
 * @uses SEPARATEGROUPS
 * @uses VISIBLEGROUPS
 * @param course $course A {@link $COURSE} object
 * @param int $groupmode Either NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS
 * @param string $urlroot ?
 * @return int|false
 */
function setup_and_print_groups($course, $groupmode, $urlroot) {

    global $USER, $SESSION; //needs his id, need to hack his groups in session

    $changegroup = optional_param('group', -1, PARAM_INT);

    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);
    if ($currentgroup === false) {
        return false;
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if ($groupmode == VISIBLEGROUPS
        or ($groupmode and has_capability('moodle/site:accessallgroups', $context))) {
        groups_instance_print_grouping_selector();
    }//added code here to allow non-editting teacher to swap in-between his own groups
    //added code for students in separategrous to swtich groups
    else if ($groupmode == SEPARATEGROUPS and has_capability('moodle/course:view', $context)) {
        groups_instance_print_group_selector();
    }

    return $currentgroup;
}


function groups_instance_print_grouping_selector() {
    //TODO: ??
}
function groups_instance_print_group_selector() {
    //TODO: ??
}


function oldgroups_print_user_group_info($currentgroup, $isseparategroups, $courseid) {
	global $CFG;
	$context = get_context_instance(CONTEXT_COURSE, $courseid);
    
    if ($currentgroup and (!$isseparategroups or has_capability('moodle/site:accessallgroups', $context))) {    /// Display info about the group
        if ($group = get_record('groups', 'id', $currentgroup)) {              
            if (!empty($group->description) or (!empty($group->picture) and empty($group->hidepicture))) { 
                echo '<table class="groupinfobox"><tr><td class="left side picture">';
                print_group_picture($group, $course->id, true, false, false);
                echo '</td><td class="content">';
                echo '<h3>'.$group->name;
                if (has_capability('moodle/site:accessallgroups', $context)) {
                    echo '&nbsp;<a title="'.get_string('editgroupprofile').'" href="../course/groups.php?id='.$course->id.'&amp;group='.$group->id.'">';
                    echo '<img src="'.$CFG->pixpath.'/t/edit.gif" alt="" border="0">';
                    echo '</a>';
                }
                echo '</h3>';
                echo format_text($group->description);
                echo '</td></tr></table>';
            }
        }
    }
}

/**
 * Get the group object, including the course ID by default.
 * @param groupid ID of the group.
 * @param getcourse (default true), include the course ID in the return.
 * @return group object, optionally including 'courseid'.
 */
function groups_get_group($groupid, $getcourse=true) {
    $group = groups_db_get_group_settings($groupid);
    if ($group && $getcourse) {
        $group->courseid = groups_get_course($groupid);
    }
    return $group;
}


/**
 * Get an array of groups, as id => name.
 * Replaces, get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")
 * (For /user/index.php)
 */
function groups_get_groups_names($courseid) {
    $groupids = groups_db_get_groups($courseid);
    if (! $groupids) {
        return false;
    }
    $groups_names = array();
    foreach ($groupids as $id) {
        $groups_names[$id] = groups_get_group_name($id);
    }
//TODO: sort. SQL?
    return $groups_names;
}

/**
 * Get the groups that a number of users are in.
 * (For block_quiz_results.php)
 */
function groups_get_groups_users($userids, $courseid) {
    global $CFG;
    $groups_users = get_records_sql(
        'SELECT gm.userid, gm.groupid, g.name FROM '.$CFG->prefix.'groups g LEFT JOIN '.$CFG->prefix.'groups_members gm ON g.id = gm.groupid '.
        'WHERE g.courseid = '.$courseid.' AND gm.userid IN ('.implode(',', $userids).')'
        );
    return $groups_users;
}

?>