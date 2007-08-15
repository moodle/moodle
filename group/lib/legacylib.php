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
 * Returns the current group mode for a given course or activity module
 * 
 * Could be false, SEPARATEGROUPS or VISIBLEGROUPS    (<-- Martin)
 */
function groupmode($course, $cm=null) {

    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        return $cm->groupmode;
    }
    return $course->groupmode;
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
        if ($full) {
            return groups_get_group($SESSION->currentgroup[$courseid], false);
        } else {
            return $SESSION->currentgroup[$courseid];
        }
    }

    $mygroupid = mygroupid($courseid);
    if (is_array($mygroupid)) {
        $mygroupid = array_shift($mygroupid);
        set_current_group($courseid, $mygroupid);
        if ($full) {
            return groups_get_group($mygroupid, false);
        } else {
            return $mygroupid;
        }
    }

    if ($full) {
        return false;
    } else {
        return 0;
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
                //if (groups_is_member($group->id)){
                    $currentgroupid = set_current_group($course->id, $groupid); //set this since he might post
                /*)}else {
                    $currentgroupid = $group->id;*/
            } elseif ($groupmode == SEPARATEGROUPS) { // student in separate groups switching
                if (groups_is_member($groupid)) { //check if is a member
                    $currentgroupid = set_current_group($course->id, $groupid); //might need to set_current_group?
                }
                else {
                    notify('You do not belong to this group! ('.$groupid.')', 'error');
                }
            }
        }
    } else { // When groupid = 0 it means show ALL groups
        // this is changed, non editting teacher needs access to group 0 as well,
        // for viewing work in visible groups (need to set current group for multiple pages)
        if (has_capability('moodle/site:accessallgroups', $context)) { // Sets current default group
            $currentgroupid = set_current_group($course->id, 0);

        } else if ($groupmode == VISIBLEGROUPS) {  // All groups are visible
            $currentgroupid = set_current_group($course->id, 0);
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

    if ($groupmode == SEPARATEGROUPS and !$currentgroup and !has_capability('moodle/site:accessallgroups', $context)) {
        //we are in separate groups and the current group is group 0, as last set.
        //this can mean that either, this guy has no group
        //or, this guy just came from a visible all forum, and he left when he set his current group to 0 (show all)

        if ($usergroups = groups_get_all_groups($course->id, $USER->id)){
            //for the second situation, we need to perform the trick and get him a group.
            $first = reset($usergroups);
            $currentgroup = get_and_set_current_group($course, $groupmode, $first->id);

        } else {
            //else he has no group in this course
            print_heading(get_string('notingroup'));
            print_footer($course);
            exit;
        }
    }

    if ($groupmode == VISIBLEGROUPS or ($groupmode and has_capability('moodle/site:accessallgroups', $context))) {

        if ($groups = groups_get_all_groups($course->id)) {

            echo '<div class="groupselector">';
            print_group_menu($groups, $groupmode, $currentgroup, $urlroot, 1);
            echo '</div>';
        }

    } else if ($groupmode == SEPARATEGROUPS and has_capability('moodle/course:view', $context)) {
        //get all the groups this guy is in in this course
        if ($usergroups = groups_get_all_groups($course->id, $USER->id)){
            echo '<div class="groupselector">';
            //print them in the menu
            print_group_menu($usergroups, $groupmode, $currentgroup, $urlroot, 0);
            echo '</div>';
        }
    }

    return $currentgroup;

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
