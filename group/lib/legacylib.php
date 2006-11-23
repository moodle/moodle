<?php
/**
 * Legacy groups functions - these were in moodlelib.php.
 *
 * @@@ Don't look at this file - still tons to do! 
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
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
 * @return int $groupname
 */
function groups_get_group_by_name($courseid, $groupname) {
	// TO DO                                 
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
	
	return groups_groupids_to_groups($groupids);
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
 * Determines if the user a member of the given group
 *
 * @uses $USER
 * @param int $groupid The group to check the membership of
 * @param int $userid The user to check against the group
 * @return bool
 */
function ismember($groupid, $userid=0) {
	if (!$userid) {
	}
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
 * Returns an array of user objects
 *
 * @uses $CFG
 * @param int $groupid The group(s) in question.
 * @param string $sort How to sort the results
 * @return object (changed to groupids)
 */
function get_group_students($groupids, $sort='u.lastaccess DESC') {

    global $CFG;

    if (is_array($groupids)){
        $groups = $groupids;
        $groupstr = '(m.groupid = '.array_shift($groups);
        foreach ($groups as $index => $value){
            $groupstr .= ' OR m.groupid = '.$value;
        }
        $groupstr .= ')';
    }
    else {
        $groupstr = 'm.groupid = '.$groupids;
    }

    return get_records_sql("SELECT DISTINCT u.*
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}groups_members m,
                                   {$CFG->prefix}groups g,
                                   {$CFG->prefix}user_students s
                             WHERE $groupstr
                               AND m.userid = u.id
                               AND m.groupid = g.id
                               AND g.courseid = s.course
                               AND s.userid = u.id
                          ORDER BY $sort");
}

/**
 * Returns list of all the teachers who can access a group
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @param int $groupid The group in question.
 * @return object
 */
function get_group_teachers($courseid, $groupid) {
/// Returns a list of all the teachers who can access a group
    if ($teachers = get_course_teachers($courseid)) {
        foreach ($teachers as $key => $teacher) {
            if ($teacher->editall) {             // These can access anything
                continue;
            }
            if (($teacher->authority > 0) and ismember($groupid, $teacher->id)) 
            {  // Specific group teachers
                continue;
            }
            unset($teachers[$key]);
        }
    }
    return $teachers;
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
 * Get the group ID of the current user in the given course
 *
 * @uses $USER
 * @param int $courseid The course being examined - relates to id field in 
 * 'course' table.
 * @return array An array of the groupids that the user belongs to. 
 */
function mygroupid($courseid) {
    global $USER;
	// TODO: check whether needs to be groups or groupids. 
	$groupids = groups_get_groups_for_user($USER->id, $courseid);
   	return $groupids[0];
}

/**
 * This now returns either false or SEPARATEGROUPS. If you want VISIBLE GROUPS 
 * with legacy code, you'll need to upgrade. 
 */
function groupmode($course, $cm=null) {

    if ($cm and !$course->groupingid) {
        //TODO: was $coursemodule
        return groups_has_groups_setup_for_instance($cm);
    } else {
    	return groups_has_groups_setup($course->id);
    }
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
    
    if (isset($SESSION->currentgroup[$courseid])) {
    	$currentgroup = $SESSION->currentgroup[$courseid];
    } else {
    	$currentgroup = mygroupid($courseid);
    }
    
    if ($currentgroup) {
    	$SESSION->currentgroup[$courseid] = mygroupid($courseid);
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

    if ($groupid) {      // Try to change the current group to this groupid
        if ($group = get_record('groups', 'id', $groupid, 'courseid', $course->id)) { // Exists
            if (isteacheredit($course->id)) {          // Sets current default group
                $currentgroupid = set_current_group($course->id, $group->id);

            } elseif ($groupmode == VISIBLEGROUPS) {
                  // All groups are visible
                //if (ismember($group->id)){
                    $currentgroupid = set_current_group($course->id, $group->id); //set this since he might post
                /*)}else {
                    $currentgroupid = $group->id;*/
            } elseif ($groupmode == SEPARATEGROUPS) { // student in separate groups switching
                if (ismember($group->id)) { //check if is a member
                    $currentgroupid = set_current_group($course->id, $group->id); //might need to set_current_group?
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
        if (isteacheredit($course->id) OR (isteacher($course->id) AND ($groupmode == VISIBLEGROUPS))) {          // Sets current default group
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

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        groups_instance_print_grouping_selector();
    }//added code here to allow non-editting teacher to swap in-between his own groups
    //added code for students in separategrous to swtich groups
    else if ($groupmode == SEPARATEGROUPS and (isteacher($course->id) or isstudent($course->id))) {
        groups_instance_print_group_selector();
    }

    return $currentgroup;
}


function oldgroups_print_user_group_info($currentgroup, $isseparategroups, $courseid) {
	global $CFG;
	    if ($currentgroup and (!$isseparategroups or isteacheredit($courseid))) {    /// Display info about the group
        if ($group = get_record('groups', 'id', $currentgroup)) {              
            if (!empty($group->description) or (!empty($group->picture) and empty($group->hidepicture))) { 
                echo '<table class="groupinfobox"><tr><td class="left side picture">';
                print_group_picture($group, $course->id, true, false, false);
                echo '</td><td class="content">';
                echo '<h3>'.$group->name;
                if (isteacheredit($courseid)) {
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

?>