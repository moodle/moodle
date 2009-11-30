<?php  //$Id$

/**
 * Groups not used in course or activity
 */
define('NOGROUPS', 0);

/**
 * Groups used, users do not see other groups
 */
define('SEPARATEGROUPS', 1);

/**
 * Groups used, students see other groups
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
 * Gets the name of a grouping with a specified id
 * @param int $groupingid The id of the grouping
 * @return string The name of the grouping
 */
function groups_get_grouping_name($groupingid) {
    return get_field('groupings', 'name', 'id', $groupingid);
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
 * Get the grouping object
 * @param groupingid ID of the group.
 * @return group object
 */
function groups_get_grouping($groupingid) {
    return get_record('groupings', 'id', $groupingid);
}

/**
 * Gets array of all groups in a specified course.
 * @param int $courseid The id of the course.
 * @param mixed $userid optional user id or array of ids, returns only groups of the user.
 * @param int $groupingid optional returns only groups in the specified grouping.
 * @return array | false Returns an array of the group objects or false if no records
 * or an error occurred. (userid field returned if array in $userid)
 */
function groups_get_all_groups($courseid, $userid=0, $groupingid=0, $fields='g.*') {
    global $CFG;

    // groupings are ignored when not enabled
    if (empty($CFG->enablegroupings)) {
        $groupingid = 0;
    }

    if (empty($userid)) {
        $userfrom  = "";
        $userwhere = "";

    } else if (is_array($userid)) {
        $userids = implode(',', $userid);
        $userfrom  = ", {$CFG->prefix}groups_members gm";
        $userwhere = "AND g.id = gm.groupid AND gm.userid IN ($userids)";

    } else {
        $userfrom  = ", {$CFG->prefix}groups_members gm";
        $userwhere = "AND g.id = gm.groupid AND gm.userid = '$userid'";
    }

    if (!empty($groupingid)) {
        $groupingfrom  = ", {$CFG->prefix}groupings_groups gg";
        $groupingwhere = "AND g.id = gg.groupid AND gg.groupingid = '$groupingid'";
    } else {
        $groupingfrom  = "";
        $groupingwhere = "";
    }

    return get_records_sql("SELECT $fields
                              FROM {$CFG->prefix}groups g $userfrom $groupingfrom
                             WHERE g.courseid = $courseid $userwhere $groupingwhere
                          ORDER BY name ASC");
}

/**
 * Returns info about user's groups in course.
 * @param int $courseid
 * @param int $userid $USER if not specified
 * @return array[groupingid][groupid] including grouping id 0 which means all groups
 */
function groups_get_user_groups($courseid, $userid=0) {
    global $CFG, $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    if (!$rs = get_recordset_sql("SELECT g.id, gg.groupingid
                                    FROM {$CFG->prefix}groups g
                                         JOIN {$CFG->prefix}groups_members gm        ON gm.groupid = g.id
                                         LEFT JOIN {$CFG->prefix}groupings_groups gg ON gg.groupid = g.id
                                   WHERE gm.userid = $userid AND g.courseid = $courseid")) {
        return array('0' => array());
    }

    $result    = array();
    $allgroups = array();
    
    while ($group = rs_fetch_next_record($rs)) {
        $allgroups[$group->id] = $group->id;
        if (is_null($group->groupingid)) {
            continue;
        }
        if (!array_key_exists($group->groupingid, $result)) {
            $result[$group->groupingid] = array();
        }
        $result[$group->groupingid][$group->id] = $group->id;
    }
    rs_close($rs);

    $result['0'] = array_keys($allgroups); // all groups

    return $result;
}

/**
 * Gets array of all groupings in a specified course.
 * @param int $courseid return only groupings in this with this courseid
 * @return array | false Returns an array of the grouping objects or false if no records
 * or an error occurred.
 */
function groups_get_all_groupings($courseid) {
    global $CFG;

    // groupings are ignored when not enabled
    if (empty($CFG->enablegroupings)) {
        return(false);
    }
    return get_records_sql("SELECT *
                              FROM {$CFG->prefix}groupings
                             WHERE courseid = $courseid
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

    static $cache = array();

    // groupings are ignored when not enabled
    if (empty($CFG->enablegroupings)) {
        $cm->groupingid = 0;
    }

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cachekey = $userid.'|'.$cm->course.'|'.$cm->groupingid;
    if (isset($cache[$cachekey])) {
        return($cache[$cachekey]);
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

    $cache[$cachekey] = record_exists_sql($sql);

    return $cache[$cachekey];
}

/**
 * Returns the users in the specified group.
 * @param int $groupid The groupid to get the users for
 * @param int $fields The fields to return
 * @param int $sort optional sorting of returned users
 * @return array | false Returns an array of the users for the specified
 * group or false if no users or an error returned.
 */
function groups_get_members($groupid, $fields='u.*', $sort='lastname ASC') {
    global $CFG;

    return get_records_sql("SELECT $fields
                              FROM {$CFG->prefix}user u, {$CFG->prefix}groups_members gm
                             WHERE u.id = gm.userid AND gm.groupid = '$groupid'
                          ORDER BY $sort");
}


/**
 * Returns the users in the specified grouping.
 * @param int $groupingid The groupingid to get the users for
 * @param int $fields The fields to return
 * @param int $sort optional sorting of returned users
 * @return array | false Returns an array of the users for the specified
 * group or false if no users or an error returned.
 */
function groups_get_grouping_members($groupingid, $fields='u.*', $sort='lastname ASC') {
    global $CFG;

    return get_records_sql("SELECT $fields
                              FROM {$CFG->prefix}user u
                                INNER JOIN {$CFG->prefix}groups_members gm ON u.id = gm.userid
                                INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = gg.groupid
                             WHERE  gg.groupingid = $groupingid
                          ORDER BY $sort");
}

/**
 * Returns effective groupmode used in course
 * @return integer group mode
 */
function groups_get_course_groupmode($course) {
    return $course->groupmode;
}

/**
 * Returns effective groupmode used in activity, course setting
 * overrides activity setting if groupmodeforce enabled.
 * @param $cm the course module object. Only the ->course and ->groupmode need to be set.
 * @param $course object optional course object to improve perf
 * @return integer group mode
 */
function groups_get_activity_groupmode($cm, $course=null) {
    global $COURSE;

    // get course object (reuse COURSE if possible)
    if (isset($course->id) and $course->id == $cm->course) {
        //ok
    } else if ($cm->course == $COURSE->id) {
        $course = $COURSE;
    } else {
        if (!$course = get_record('course', 'id', $cm->course)) {
            error('Incorrect course id in cm');
        }
    }

    return empty($course->groupmodeforce) ? $cm->groupmode : $course->groupmode;
}

/**
 * Print group menu selector for course level.
 * @param object $course course object
 * @param string $urlroot return address
 * @param boolean $return return as string instead of printing
 * @return mixed void or string depending on $return param
 */
function groups_print_course_menu($course, $urlroot, $return=false) {
    global $CFG, $USER, $SESSION;

    if (!$groupmode = $course->groupmode) {
        if ($return) {
            return '';
        } else {
            return;
        }
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $context)) {
        $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        // detect changes related to groups and fix active group
        if (!empty($SESSION->activegroup[$course->id][VISIBLEGROUPS][0])) {
            if (!array_key_exists($SESSION->activegroup[$course->id][VISIBLEGROUPS][0], $allowedgroups)) {
                // active does not exist anymore
                unset($SESSION->activegroup[$course->id][VISIBLEGROUPS][0]);
            }
        }
        if (!empty($SESSION->activegroup[$course->id]['aag'][0])) {
            if (!array_key_exists($SESSION->activegroup[$course->id]['aag'][0], $allowedgroups)) {
                // active group does not exist anymore
                unset($SESSION->activegroup[$course->id]['aag'][0]);
            }
        }

    } else {
        $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        // detect changes related to groups and fix active group
        if (isset($SESSION->activegroup[$course->id][SEPARATEGROUPS][0])) {
            if ($SESSION->activegroup[$course->id][SEPARATEGROUPS][0] == 0) {
                if ($allowedgroups) {
                    // somebody must have assigned at least one group, we can select it now - yay!
                    unset($SESSION->activegroup[$course->id][SEPARATEGROUPS][0]);
                }
            } else {
                if (!array_key_exists($SESSION->activegroup[$course->id][SEPARATEGROUPS][0], $allowedgroups)) {
                    // active group not allowed or does not exist anymore
                    unset($SESSION->activegroup[$course->id][SEPARATEGROUPS][0]);
                }
            }
        }
    }

    $activegroup = groups_get_course_group($course, true);

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
 * Print group menu selector for activity.
 * @param object $cm course module object
 * @param string $urlroot return address that users get to if they choose an option;
 *   should include any parameters needed, e.g. "$CFG->wwwroot/mod/forum/view.php?id=34"
 * @param boolean $return return as string instead of printing
 * @param boolean $hideallparticipants If true, this prevents the 'All participants'
 *   option from appearing in cases where it normally would. This is intended for
 *   use only by activities that cannot display all groups together. (Note that
 *   selecting this option does not prevent groups_get_activity_group from
 *   returning 0; it will still do that if the user has chosen 'all participants'
 *   in another activity, or not chosen anything.)
 * @return mixed void or string depending on $return param
 */
function groups_print_activity_menu($cm, $urlroot, $return=false, $hideallparticipants=false) {
    global $CFG, $USER, $SESSION;

    // Display error if urlroot is not absolute (this causes the non-JS version
    // to break)
    if (strpos($urlroot, 'http') !== 0) { // Will also work for https
        debugging('groups_print_activity_menu requires absolute URL for ' .
            '$urlroot, not <tt>' . s($urlroot) . '</tt>. Example: ' .
            'groups_print_activity_menu($cm, $CFG->wwwroot . \'/mod/mymodule/view.php?id=13\');',
            DEBUG_DEVELOPER);
    }
    // groupings are ignored when not enabled
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
        // detect changes related to groups and fix active group
        if (!empty($SESSION->activegroup[$cm->course][VISIBLEGROUPS][$cm->groupingid])) {
            if (!array_key_exists($SESSION->activegroup[$cm->course][VISIBLEGROUPS][$cm->groupingid], $allowedgroups)) {
                // active group does not exist anymore
                unset($SESSION->activegroup[$cm->course][VISIBLEGROUPS][$cm->groupingid]);
            }
        }
        if (!empty($SESSION->activegroup[$cm->course]['aag'][$cm->groupingid])) {
            if (!array_key_exists($SESSION->activegroup[$cm->course]['aag'][$cm->groupingid], $allowedgroups)) {
                // active group does not exist anymore
                unset($SESSION->activegroup[$cm->course]['aag'][$cm->groupingid]);
            }
        }

    } else {
        $allowedgroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid); // only assigned groups
        // detect changes related to groups and fix active group
        if (isset($SESSION->activegroup[$cm->course][SEPARATEGROUPS][$cm->groupingid])) {
            if ($SESSION->activegroup[$cm->course][SEPARATEGROUPS][$cm->groupingid] == 0) {
                if ($allowedgroups) {
                    // somebody must have assigned at least one group, we can select it now - yay!
                    unset($SESSION->activegroup[$cm->course][SEPARATEGROUPS][$cm->groupingid]);
                }
            } else {
                if (!array_key_exists($SESSION->activegroup[$cm->course][SEPARATEGROUPS][$cm->groupingid], $allowedgroups)) {
                    // active group not allowed or does not exist anymore
                    unset($SESSION->activegroup[$cm->course][SEPARATEGROUPS][$cm->groupingid]);
                }
            }
        }
    }

    $activegroup = groups_get_activity_group($cm, true);

    $groupsmenu = array();
    if ((!$allowedgroups or $groupmode == VISIBLEGROUPS or
      has_capability('moodle/site:accessallgroups', $context)) and !$hideallparticipants) {
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
 * Returns group active in course, changes the group by default if 'group' page param present
 *
 * @param object $course course bject
 * @param boolean $update change active group if group param submitted
 * @return mixed false if groups not used, int if groups used, 0 means all groups (access must be verified in SEPARATE mode)
 */
function groups_get_course_group($course, $update=false) {
    global $CFG, $USER, $SESSION;

    if (!$groupmode = $course->groupmode) {
        // NOGROUPS used
        return false;
    }

    // init activegroup array
    if (!array_key_exists('activegroup', $SESSION)) {
        $SESSION->activegroup = array();
    }
    if (!array_key_exists($course->id, $SESSION->activegroup)) {
        $SESSION->activegroup[$course->id] = array(SEPARATEGROUPS=>array(), VISIBLEGROUPS=>array(), 'aag'=>array());
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (has_capability('moodle/site:accessallgroups', $context)) {
        $groupmode = 'aag';
    }

    // grouping used the first time - add first user group as default
    if (!array_key_exists(0, $SESSION->activegroup[$course->id][$groupmode])) {
        if ($groupmode == 'aag') {
            $SESSION->activegroup[$course->id][$groupmode][0] = 0; // all groups by default if user has accessallgroups

        } else if ($usergroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid)) {
            $fistgroup = reset($usergroups);
            $SESSION->activegroup[$course->id][$groupmode][0] = $fistgroup->id;

        } else {
            // this happen when user not assigned into group in SEPARATEGROUPS mode or groups do not exist yet
            // mod authors must add extra checks for this when SEPARATEGROUPS mode used (such as when posting to forum)
            $SESSION->activegroup[$course->id][$groupmode][0] = 0;
        }
    }

    // set new active group if requested
    $changegroup = optional_param('group', -1, PARAM_INT);
    if ($update and $changegroup != -1) {

        if ($changegroup == 0) {
            // do not allow changing to all groups without accessallgroups capability
            if ($groupmode == VISIBLEGROUPS or $groupmode == 'aag') {
                $SESSION->activegroup[$course->id][$groupmode][0] = 0;
            }

        } else {
            // first make list of allowed groups
            if ($groupmode == VISIBLEGROUPS or $groupmode == 'aag') {
                $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
            } else {
                $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
            }

            if ($allowedgroups and array_key_exists($changegroup, $allowedgroups)) {
                $SESSION->activegroup[$course->id][$groupmode][0] = $changegroup;
            }
        }
    }

    return $SESSION->activegroup[$course->id][$groupmode][0];
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

    // groupings are ignored when not enabled
    if (empty($CFG->enablegroupings)) {
        $cm->groupingid = 0;
    }

    if (!$groupmode = groups_get_activity_groupmode($cm)) {
        // NOGROUPS used
        return false;
    }

    // init activegroup array
    if (!array_key_exists('activegroup', $SESSION)) {
        $SESSION->activegroup = array();
    }
    if (!array_key_exists($cm->course, $SESSION->activegroup)) {
        $SESSION->activegroup[$cm->course] = array(SEPARATEGROUPS=>array(), VISIBLEGROUPS=>array(), 'aag'=>array());
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('moodle/site:accessallgroups', $context)) {
        $groupmode = 'aag';
    }

    // grouping used the first time - add first user group as default
    if (!array_key_exists($cm->groupingid, $SESSION->activegroup[$cm->course][$groupmode])) {
        if ($groupmode == 'aag') {
            $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid] = 0; // all groups by default if user has accessallgroups

        } else if ($usergroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid)) {
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

        if ($changegroup == 0) {
            // allgroups visible only in VISIBLEGROUPS or when accessallgroups
            if ($groupmode == VISIBLEGROUPS or $groupmode == 'aag') {
                $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid] = 0;
            }

        } else {
            // first make list of allowed groups
            if ($groupmode == VISIBLEGROUPS or $groupmode == 'aag') {
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

/**
 * Gets a list of groups that the user is allowed to access within the
 * specified activity.
 * @param object $cm Course-module
 * @param int $userid User ID (defaults to current user)
 * @return array An array of group objects, or false if none
 */
function groups_get_activity_allowed_groups($cm,$userid=0) {
    // Use current user by default
    global $USER;
    if(!$userid) {
        $userid=$USER->id;
    }

    // Get groupmode for activity, taking into account course settings
    $groupmode=groups_get_activity_groupmode($cm);

    // If visible groups mode, or user has the accessallgroups capability,
    // then they can access all groups for the activity...
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $context)) {
        return groups_get_all_groups($cm->course, 0, $cm->groupingid);
    } else {
        // ...otherwise they can only access groups they belong to
        return groups_get_all_groups($cm->course, $userid, $cm->groupingid);
    }
}

/**
 * Determine if a course module is currently visible to a user
 * @uses $USER If $userid is null, use the global object.
 * @param int $cm The course module
 * @param int $userid The user to check against the group.
 * @return boolean True if the user can view the course module, false otherwise.
 */
function groups_course_module_visible($cm, $userid=null) {
    global $CFG, $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }
    if (empty($CFG->enablegroupings)) {
        return true;
    }
    if (empty($cm->groupmembersonly)) {
        return true;
    }
    if (has_capability('moodle/site:accessallgroups', get_context_instance(CONTEXT_MODULE, $cm->id), $userid) or groups_has_membership($cm, $userid)) {
        return true;
    }
    return false;
}

?>
