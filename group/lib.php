<?php
/**
 * Extra library for groups and groupings.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */

/*
 * INTERNAL FUNCTIONS - to be used by moodle core only
 * require_once $CFG->dirroot.'/group/lib.php' must be used
 */

/**
 * Adds a specified user to a group
 * @param int $userid   The user id
 * @param int $groupid  The group id
 * @return boolean True if user added successfully or the user is already a 
 * member of the group, false otherwise. 
 */
function groups_add_member($groupid, $userid) {
    if (!groups_group_exists($groupid)) {
        return false;
    }

    if (groups_is_member($groupid, $userid)) {
        return true;
    }

    $member = new object();
    $member->groupid   = $groupid;
    $member->userid    = $userid;
    $member->timeadded = time();

    if (!insert_record('groups_members', $member)) {
        return false;
    }

    //update group info
    set_field('groups', 'timemodified', $member->timeadded, 'id', $groupid);

    // MDL-9983
    $eventdata = new object();
    $eventdata->groupid = $groupid;
    $eventdata->userid = $userid;
    events_trigger('group_user_added', $eventdata);      

    return true;
}

/**
 * Deletes the link between the specified user and group.
 * @param int $groupid The group to delete the user from
 * @param int $userid The user to delete
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_remove_member($groupid, $userid) {
    if (!groups_group_exists($groupid)) {
        return false;
    }

    if (!groups_is_member($groupid, $userid)) {
        return true;
    }

    if (!delete_records('groups_members', 'groupid', $groupid, 'userid', $userid)) {
        return false;
    }    
    //update group info
    set_field('groups', 'timemodified', time(), 'id', $groupid);

    return true;
}

/**
 * Add a new group
 * @param object $data group properties (with magic quotes);
 * @param object $um upload manager with group picture
 * @return id of group or false if error
 */
function groups_create_group($data, $um=false) {
    global $CFG;
    require_once("$CFG->libdir/gdlib.php");

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    $id = insert_record('groups', $data);

    if ($id and $um) {
        //update image
        if (save_profile_image($id, $um, 'groups')) {
            set_field('groups', 'picture', 1, 'id', $id);
        }
    }

    return $id;
}

/**
 * Add a new group
 * @param object $data group properties (with magic quotes);
 * @param object $um upload manager with group picture
 * @return boolean success
 */
function groups_update_group($data, $um=false) {
    global $CFG;
    require_once("$CFG->libdir/gdlib.php");

    $data->timemodified = time();
    $result = update_record('groups', $data);

    if ($result and $um) {
        //update image
        if (save_profile_image($data->id, $um, 'groups')) {
            set_field('groups', 'picture', 1, 'id', $data->id);
        }
    }

    return $result;
}

/**
 * Delete a group best effort, first removing members and links with courses and groupings.
 * Removes group avatar too.
 * @param int $groupid The group to delete
 * @return boolean True if deletion was successful, false otherwise
 */
function groups_delete_group($groupid) {
    global $CFG;
    require_once($CFG->libdir.'/gdlib.php');

    if (empty($groupid)) {
        return false;
    }

    //first delete usage in groupings_groups
    delete_records('groupings_groups', 'groupid', $groupid);
    //delete members
    delete_records('groups_members', 'groupid', $groupid);
    //then imge
    delete_profile_image($groupid, 'groups');
    //group itself last
    return delete_records('groups', 'id', $groupid);
}

function groups_delete_grouping($groupingid) {
    if (empty($groupingid)) {
        return false;

    }

    //first delete usage in groupings_groups
    delete_records('groupings_groups', 'groupingid', $groupingid);
    // remove the default groupingid from course
    set_field('course', 'defaultgroupingid', 0, 'defaultgroupingid', $groupingid);
    // remove the groupingid from all course modules
    set_field('course_modules', 'groupingid', 0, 'groupingid', $groupingid);
    //group itself last
    return delete_records('groupings', 'id', $groupingid);
}

function groups_delete_group_members($courseid, $showfeedback=false) {
    global $CFG;

    $sql = "DELETE FROM {$CFG->prefix}groups_members
             WHERE groupid in (SELECT id FROM {$CFG->prefix}groups g WHERE g.courseid = $courseid)";

    execute_sql($sql, false);
    if ($showfeedback) {
        notify(get_string('deleted').' groups_members');
    }

    return true;
}

function groups_delete_groups($courseid, $showfeedback=false) {
    global $CFG;
    require_once($CFG->libdir.'/gdlib.php');

    // delete any uses of groups
    $sql = "DELETE FROM {$CFG->prefix}groupings_groups
             WHERE groupid in (SELECT id FROM {$CFG->prefix}groups g WHERE g.courseid = $courseid)";
    execute_sql($sql, false);

    groups_delete_group_members($courseid, false);

    // delete group pictures
    if ($groups = get_records('groups', 'courseid', $courseid)) {
        foreach($groups as $group) {
            delete_profile_image($group->id, 'groups');
        }
    }

    delete_records('groups', 'courseid', $courseid);
    if ($showfeedback) {
        notify(get_string('deleted').' groups');
    }

    return true;
}

function groups_delete_groupings($courseid, $showfeedback=false) {
    global $CFG;

    // delete any uses of groupings
    $sql = "DELETE FROM {$CFG->prefix}groupings_groups
             WHERE groupingid in (SELECT id FROM {$CFG->prefix}groupings g WHERE g.courseid = $courseid)";
    execute_sql($sql, false);

    // remove the default groupingid from course
    set_field('course', 'defaultgroupingid', 0, 'id', $courseid);
    // remove the groupingid from all course modules
    set_field('course_modules', 'groupingid', 0, 'courseid', $courseid);

    delete_records('groupings', 'courseid', $courseid);
    if ($showfeedback) {
        notify(get_string('deleted').' groupings');
    }

    return true;
}

/* =================================== */
/* various functions used by groups UI */
/* =================================== */

/**
 * Gets the users for a course who are not in a specified group
 * @param int $groupid The id of the group
 * @param string searchtext similar to searchtext in role assign, search
 * @return array An array of the userids of the non-group members,  or false if
 * an error occurred.
 * This function was changed to get_users_by_capability style
 * mostly because of the searchtext requirement
 */
function groups_get_users_not_in_group($courseid, $groupid, $searchtext='') {

    global $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    if ($searchtext !== '') {   // Search for a subset of remaining users
        $LIKE      = sql_ilike();
        $FULLNAME  = sql_fullname();
        $wheresearch = " AND u.id IN (SELECT id FROM {$CFG->prefix}user WHERE $FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%' )";
    } else {
        $wheresearch = '';
    }

    $capability = 'moodle/course:view';
    $doanything = false;

    // find all possible "student" roles
    if ($possibleroles = get_roles_with_capability($capability, CAP_ALLOW, $context)) {
        if (!$doanything) {
            if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM)) {
                return false;    // Something is seriously wrong
            }
            $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext);
        }

        $validroleids = array();
        foreach ($possibleroles as $possiblerole) {
            if (!$doanything) {
                if (isset($doanythingroles[$possiblerole->id])) {  // We don't want these included
                    continue;
                }
            }
            if ($caps = role_context_capabilities($possiblerole->id, $context, $capability)) { // resolved list
                if (isset($caps[$capability]) && $caps[$capability] > 0) { // resolved capability > 0
                    $validroleids[] = $possiblerole->id;
                }
            }
        }
        if (empty($validroleids)) {
            return false;
        }
        $roleids =  '('.implode(',', $validroleids).')';
    } else {
        return false;  // No need to continue, since no roles have this capability set
    }

/// Construct the main SQL
    $select = " SELECT u.id, u.firstname, u.lastname";
    $from   = " FROM {$CFG->prefix}user u
                INNER JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid";
    $where  = " WHERE ra.contextid ".get_related_contexts_string($context)."
                  AND u.deleted = 0
                  AND ra.roleid in $roleids
                  AND u.id NOT IN (SELECT userid
                                   FROM {$CFG->prefix}groups_members
                                   WHERE groupid = $groupid)
                  $wheresearch";

    return get_records_sql($select.$from.$where);;
}

?>