<?php  //$Id$

/**
 * No groups used?
 */
define('NOGROUPS', 0);

/**
 * Groups used?
 */
define('SEPARATEGROUPS', 1);

/**
 * Groups visible?
 */
define('VISIBLEGROUPS', 2);


/**
 * Determines if a group with a given groupid exists.
 * @param int $groupid The groupid to check for
 * @return boolean True if the group exists, false otherwise or if an error
 * occurred.
 */
function groups_group_exists($groupid) {
    return record_exists('groups', 'id', $groupid);
}

/**
 * Gets the name of a group with a specified id
 * @param int $groupid The id of the group
 * @return string The name of the group
 */
function groups_get_group_name($groupid) {
    return get_field('groups', 'name', 'id', $groupid);
}

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

    if (empty($CFG->enablegroupings)) {
        $groupingid = 0;
    }

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
 * Determines if current or specified is member of any active group in activity
 * @param object $cm coruse module object
 * @param int $userid id of user, null menas $USER->id
 * @return booelan true if user member of at least one group used in activity
 */
function groups_has_membership($cm, $userid=null) {
    global $CFG, $USER;

    if (empty($CFG->enablegroupings)) {
        $cm->groupingid = 0;
    }

    if (empty($userid)) {
        $userid = $USER->id;
    }

    if ($cm->groupingid) {
        // find out if member of any group in selected activity grouping
        $sql = "SELECT 'x'
                  FROM {$CFG->prefix}groups_members gm, {$CFG->prefix}groupings_groups gg
                 WHERE gm.userid = $userid AND gm.groupid = gg.groupid AND gg.groupingid = {$cm->groupingid}";

    } else {
        // no grouping used - check all groups in course
        $sql = "SELECT 'x'
                  FROM {$CFG->prefix}groups_members gm, {$CFG->prefix}groups g
                 WHERE gm.userid = $userid AND gm.groupid = g.id AND g.courseid = {$cm->course}";
    }

    return record_exists_sql($sql);
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

/**
 * Returns effective groupmode used in activity, course setting
 * overrides activity setting if groupmodeforce enabled.
 * @return integer group mode
 */
function groups_get_activity_groupmode($cm) {
    global $COURSE;

    // get course object (reuse COURSE if possible)
    if ($cm->course == $COURSE->id) {
        $course = $COURSE;
    } else {
        if (!$course = get_record('course', 'id', $cm->course)) {
            error('Incorrect course id in cm');
        }
    }

    return empty($course->groupmodeforce) ? $cm->groupmode : $course->groupmode;
}

/**
 * Print group menu selector for activity.
 * @param object $cm course module object
 * @param string $urlroot return address
 * @param boolean $return return as string instead of printing
 * @return mixed void or string depending on $return param
 */
function groups_print_activity_menu($cm, $urlroot, $return=false) {
    global $CFG, $USER;

    if (empty($CFG->enablegroupings)) {
        $cm->groupingid = 0;
    }

    if (!$groupmode = groups_get_activity_groupmode($cm)) {
        if ($return) {
            return '';
        } else {
            return;
        }
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $context)) {
        $allowedgroups = groups_get_all_groups($cm->course, 0, $cm->groupingid); // any group in grouping (all if groupings not used)
    } else {
        $allowedgroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid); // only assigned groups
    }

    $activegroup = groups_get_activity_group($cm, true);

    $groupsmenu = array();
    if (!$allowedgroups or $groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $context)) {
        $groupsmenu[0] = get_string('allparticipants');
    }

    if ($allowedgroups) {
        foreach ($allowedgroups as $group) {
            $groupsmenu[$group->id] = format_string($group->name);
        }
    }

    if ($groupmode == VISIBLEGROUPS) {
        $grouplabel = get_string('groupsvisible');
    } else {
        $grouplabel = get_string('groupsseparate');
    }

    if (count($groupsmenu) == 1) {
        $groupname = reset($groupsmenu);
        $output = $grouplabel.': '.$groupname;
    } else {
        $output = popup_form($urlroot.'&amp;group=', $groupsmenu, 'selectgroup', $activegroup, '', '', '', true, 'self', $grouplabel);
    }

    $output = '<div class="groupselector">'.$output.'</div>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Returns group active in activity, changes the group by default if 'group' page param present
 *
 * @param object $cm course module object
 * @param boolean $update change active group if group param submitted
 * @return mixed false if groups not used, int if groups used, 0 means all groups (access must be verified in SEPARATE mode)
 */
function groups_get_activity_group($cm, $update=false) {
    global $CFG, $USER, $SESSION;

    if (empty($CFG->enablegroupings)) {
        $cm->groupingid = 0;
    }

    if (!$groupmode = groups_get_activity_groupmode($cm)) {
        // NOGROUPS used
        return false;
    }

    // innit activegroup array
    if (!array_key_exists('activegroup', $SESSION)) {
        $SESSION->activegroup = array();
    }
    if (!array_key_exists($cm->course, $SESSION->activegroup)) {
        $SESSION->activegroup[$cm->course] = array(SEPARATEGROUPS=>array(), VISIBLEGROUPS=>array());
    }

    // grouping used the first time - add first user group as default
    if (!array_key_exists($cm->groupingid, $SESSION->activegroup[$cm->course][$groupmode])) {
        if ($usergroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid)) {
            $fistgroup = reset($usergroups);
            $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid] = $fistgroup->id;
        } else {
            // this happen when user not assigned into group in SEPARATEGROUPS mode or groups do not exist yet
            // mod authors must add extra checks for this when SEPARATEGROUPS mode used (such as when posting to forum)
            $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid] = 0;
        }
    }

    // set new active group if requested
    $changegroup = optional_param('group', -1, PARAM_INT);
    if ($update and $changegroup != -1) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        if ($changegroup == 0) {
            // do not allow changing to all groups without accessallgroups capability
            if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $context)) {
                $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid] = 0;
            }

        } else {
            // first make list of allowed groups
            if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $context)) {
                $allowedgroups = groups_get_all_groups($cm->course, 0, $cm->groupingid); // any group in grouping (all if groupings not used)
            } else {
                $allowedgroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid); // only assigned groups
            }

            if ($allowedgroups and array_key_exists($changegroup, $allowedgroups)) {
                $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid] = $changegroup;
            }
        }
    }

    return $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid];
}

?>
