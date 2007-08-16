<?php  //$Id$

// folowing files will be removed soon
require_once($CFG->dirroot.'/group/lib/basicgrouplib.php');
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
    if ($groups = get_records_select('groups', "courseid=$courseid AND name='".addslashes($name)."'")) {
        return key($groups);
    }
    return false;
}

/**
 * Returns the groupingid of a grouping with the name specified for the course.
 * Grouping names should be unique in course
 * @param int $courseid The id of the course
 * @param string $name name of group (without magic quotes)
 * @return int $groupid
 */
function groups_get_grouping_by_name($courseid, $name) {
    if ($groupings = get_records_select('groupings', "courseid=$courseid AND name='".addslashes($name)."'")) {
        return key($groupings);
    }
    return false;
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
 * @param int $groupingid optional returns only groups in the specified grouping.
 * @return array | false Returns an array of the group IDs or false if no records
 * or an error occurred.
 */
function groups_get_all_groups($courseid, $userid=0, $groupingid=0) {
    global $CFG;

    if (!empty($userid)) {
        $userfrom  = ", {$CFG->prefix}groups_members gm";
        $userwhere = "AND g.id = gm.groupid AND gm.userid = '$userid'";
    } else {
        $userfrom  = "";
        $userwhere = "";
    }

    if (!empty($groupingid)) {
        $groupingfrom  = ", {$CFG->prefix}groupings_groups gg";
        $groupingwhere = "AND g.id = gg.groupid AND gg.groupingid = '$groupingid'";
    } else {
        $groupingfrom  = "";
        $groupingwhere = "";
    }

    return get_records_sql("SELECT g.*
                              FROM {$CFG->prefix}groups g $userfrom $groupingfrom
                             WHERE g.courseid = '$courseid' $userwhere $groupingwhere
                          ORDER BY name ASC");
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

/**
 * Returns the users in the specified group.
 * @param int $groupid The groupid to get the users for
 * @param int $sort optional sorting of returned users
 * @return array | false Returns an array of the users for the specified
 * group or false if no users or an error returned.
 */
function groups_get_members($groupid, $sort='lastname ASC') {
    global $CFG;

    return get_records_sql("SELECT u.*
                              FROM {$CFG->prefix}user u, {$CFG->prefix}groups_members gm
                             WHERE u.id = gm.userid AND gm.groupid = '$groupid'
                          ORDER BY $sort");
}



?>
