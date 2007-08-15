<?php  //$Id$

// folowing files will be removed soon
require_once($CFG->dirroot.'/group/lib/basicgrouplib.php');
require_once($CFG->dirroot.'/group/lib/groupinglib.php');
require_once($CFG->dirroot.'/group/lib/utillib.php');
require_once($CFG->dirroot.'/group/lib/legacylib.php');

/**
 * Returns the groupid of a group with the name specified for the course.
 * Group names should be unique in course
 * @param int $courseid The id of the course
 * @param string $name name of group (without magic quotes)
 * @return int $groupid
 */
function groups_get_group_by_name($courseid, $name) {
    if (!$group = get_record('groups', 'courseid', $courseid, 'name', addslashes($name))) {
        return false;
    }

    return $group->id;
}

/**
 * Get the group object
 * @param groupid ID of the group.
 * @return group object
 */
function groups_get_group($groupid) {
    return get_record('groups', 'id', $groupid);
}

/**
 * Gets array of all groups in a specified course.
 * @param int $courseid The id of the course.
 * @param int $userid optional user id, returns only groups of the user.
 * @return array | false Returns an array of the group IDs or false if no records
 * or an error occurred.
 */
function groups_get_all_groups($courseid, $userid=0) {
    global $CFG;

    if (empty($userdi)) {
        return get_records('groups', 'courseid', $courseid, 'name ASC');

    } else {
        return get_records_sql("SELECT g.*
                                 FROM {$CFG->prefix}groups g,
                                      {$CFG->prefix}groups_members m
                                 WHERE g.courseid = '$courseid'
                                   AND g.id = m.groupid
                                   AND m.userid = '$userid'
                                   ORDER BY name ASC");
    }
}

/**
 * Determines if the user is a member of the given group.
 *
 * @uses $USER If $userid is null, use the global object.
 * @param int $groupid The group to check for membership.
 * @param int $userid The user to check against the group.
 * @return boolean True if the user is a member, false otherwise.
 */
function groups_is_member($groupid, $userid=null) {
    global $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    return record_exists('groups_members', 'groupid', $groupid, 'userid', $userid);
}


?>
