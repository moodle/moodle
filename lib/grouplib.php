<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 * @subpackage group
 */

defined('MOODLE_INTERNAL') || die();

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
 *
 * @global object
 * @param int $groupid The groupid to check for
 * @return boolean True if the group exists, false otherwise or if an error
 * occurred.
 */
function groups_group_exists($groupid) {
    global $DB;
    return $DB->record_exists('groups', array('id'=>$groupid));
}

/**
 * Gets the name of a group with a specified id
 *
 * @global object
 * @param int $groupid The id of the group
 * @return string The name of the group
 */
function groups_get_group_name($groupid) {
    global $DB;
    return $DB->get_field('groups', 'name', array('id'=>$groupid));
}

/**
 * Gets the name of a grouping with a specified id
 *
 * @global object
 * @param int $groupingid The id of the grouping
 * @return string The name of the grouping
 */
function groups_get_grouping_name($groupingid) {
    global $DB;
    return $DB->get_field('groupings', 'name', array('id'=>$groupingid));
}

/**
 * Returns the groupid of a group with the name specified for the course.
 * Group names should be unique in course
 *
 * @global object
 * @param int $courseid The id of the course
 * @param string $name name of group (without magic quotes)
 * @return int $groupid
 */
function groups_get_group_by_name($courseid, $name) {
    global $DB;
    if ($groups = $DB->get_records('groups', array('courseid'=>$courseid, 'name'=>$name))) {
        return key($groups);
    }
    return false;
}

/**
 * Returns the groupingid of a grouping with the name specified for the course.
 * Grouping names should be unique in course
 *
 * @global object
 * @param int $courseid The id of the course
 * @param string $name name of group (without magic quotes)
 * @return int $groupid
 */
function groups_get_grouping_by_name($courseid, $name) {
    global $DB;
    if ($groupings = $DB->get_records('groupings', array('courseid'=>$courseid, 'name'=>$name))) {
        return key($groupings);
    }
    return false;
}

/**
 * Get the group object
 *
 * @param int $groupid ID of the group.
 * @return object group object
 */
function groups_get_group($groupid, $fields='*', $strictness=IGNORE_MISSING) {
    global $DB;
    return $DB->get_record('groups', array('id'=>$groupid), $fields, $strictness);
}

/**
 * Get the grouping object
 *
 * @param int $groupingid ID of the group.
 * @param string $fields
 * @return object group object
 */
function groups_get_grouping($groupingid, $fields='*', $strictness=IGNORE_MISSING) {
    global $DB;
    return $DB->get_record('groupings', array('id'=>$groupingid), $fields, $strictness);
}

/**
 * Gets array of all groups in a specified course.
 *
 * @param int $courseid The id of the course.
 * @param mixed $userid optional user id or array of ids, returns only groups of the user.
 * @param int $groupingid optional returns only groups in the specified grouping.
 * @param string $fields
 * @return array Returns an array of the group objects (userid field returned if array in $userid)
 */
function groups_get_all_groups($courseid, $userid=0, $groupingid=0, $fields='g.*') {
    global $DB;

    if (empty($userid)) {
        $userfrom  = "";
        $userwhere = "";
        $params = array();

    } else {
        list($usql, $params) = $DB->get_in_or_equal($userid);
        $userfrom  = ", {groups_members} gm";
        $userwhere = "AND g.id = gm.groupid AND gm.userid $usql";
    }

    if (!empty($groupingid)) {
        $groupingfrom  = ", {groupings_groups} gg";
        $groupingwhere = "AND g.id = gg.groupid AND gg.groupingid = ?";
        $params[] = $groupingid;
    } else {
        $groupingfrom  = "";
        $groupingwhere = "";
    }

    array_unshift($params, $courseid);

    return $DB->get_records_sql("SELECT $fields
                                   FROM {groups} g $userfrom $groupingfrom
                                  WHERE g.courseid = ? $userwhere $groupingwhere
                               ORDER BY name ASC", $params);
}

/**
 * Returns info about user's groups in course.
 *
 * @param int $courseid
 * @param int $userid $USER if not specified
 * @return array Array[groupingid][groupid] including grouping id 0 which means all groups
 */
function groups_get_user_groups($courseid, $userid=0) {
    global $USER, $DB;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $sql = "SELECT g.id, gg.groupingid
              FROM {groups} g
                   JOIN {groups_members} gm   ON gm.groupid = g.id
              LEFT JOIN {groupings_groups} gg ON gg.groupid = g.id
             WHERE gm.userid = ? AND g.courseid = ?";
    $params = array($userid, $courseid);

    $rs = $DB->get_recordset_sql($sql, $params);

    if (!$rs->valid()) {
        $rs->close(); // Not going to iterate (but exit), close rs
        return array('0' => array());
    }

    $result    = array();
    $allgroups = array();

    foreach ($rs as $group) {
        $allgroups[$group->id] = $group->id;
        if (is_null($group->groupingid)) {
            continue;
        }
        if (!array_key_exists($group->groupingid, $result)) {
            $result[$group->groupingid] = array();
        }
        $result[$group->groupingid][$group->id] = $group->id;
    }
    $rs->close();

    $result['0'] = array_keys($allgroups); // all groups

    return $result;
}

/**
 * Gets array of all groupings in a specified course.
 *
 * @global object
 * @global object
 * @param int $courseid return only groupings in this with this courseid
 * @return array|bool Returns an array of the grouping objects or false if no records
 * or an error occurred.
 */
function groups_get_all_groupings($courseid) {
    global $CFG, $DB;

    return $DB->get_records_sql("SELECT *
                                   FROM {groupings}
                                  WHERE courseid = ?
                               ORDER BY name ASC", array($courseid));
}



/**
 * Determines if the user is a member of the given group.
 *
 * If $userid is null, use the global object.
 *
 * @global object
 * @global object
 * @param int $groupid The group to check for membership.
 * @param int $userid The user to check against the group.
 * @return boolean True if the user is a member, false otherwise.
 */
function groups_is_member($groupid, $userid=null) {
    global $USER, $DB;

    if (!$userid) {
        $userid = $USER->id;
    }

    return $DB->record_exists('groups_members', array('groupid'=>$groupid, 'userid'=>$userid));
}

/**
 * Determines if current or specified is member of any active group in activity
 *
 * @global object
 * @global object
 * @global object
 * @staticvar array $cache
 * @param object $cm coruse module object
 * @param int $userid id of user, null menas $USER->id
 * @return booelan true if user member of at least one group used in activity
 */
function groups_has_membership($cm, $userid=null) {
    global $CFG, $USER, $DB;

    static $cache = array();

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
                  FROM {groups_members} gm, {groupings_groups} gg
                 WHERE gm.userid = ? AND gm.groupid = gg.groupid AND gg.groupingid = ?";
        $params = array($userid, $cm->groupingid);

    } else {
        // no grouping used - check all groups in course
        $sql = "SELECT 'x'
                  FROM {groups_members} gm, {groups} g
                 WHERE gm.userid = ? AND gm.groupid = g.id AND g.courseid = ?";
        $params = array($userid, $cm->course);
    }

    $cache[$cachekey] = $DB->record_exists_sql($sql, $params);

    return $cache[$cachekey];
}

/**
 * Returns the users in the specified group.
 *
 * @global object
 * @param int $groupid The groupid to get the users for
 * @param int $fields The fields to return
 * @param int $sort optional sorting of returned users
 * @return array|bool Returns an array of the users for the specified
 * group or false if no users or an error returned.
 */
function groups_get_members($groupid, $fields='u.*', $sort='lastname ASC') {
    global $DB;

    return $DB->get_records_sql("SELECT $fields
                                   FROM {user} u, {groups_members} gm
                                  WHERE u.id = gm.userid AND gm.groupid = ?
                               ORDER BY $sort", array($groupid));
}


/**
 * Returns the users in the specified grouping.
 *
 * @global object
 * @param int $groupingid The groupingid to get the users for
 * @param int $fields The fields to return
 * @param int $sort optional sorting of returned users
 * @return array|bool Returns an array of the users for the specified
 * group or false if no users or an error returned.
 */
function groups_get_grouping_members($groupingid, $fields='u.*', $sort='lastname ASC') {
    global $DB;

    return $DB->get_records_sql("SELECT $fields
                                   FROM {user} u
                                     INNER JOIN {groups_members} gm ON u.id = gm.userid
                                     INNER JOIN {groupings_groups} gg ON gm.groupid = gg.groupid
                                  WHERE  gg.groupingid = ?
                               ORDER BY $sort", array($groupingid));
}

/**
 * Returns effective groupmode used in course
 *
 * @return integer group mode
 */
function groups_get_course_groupmode($course) {
    return $course->groupmode;
}

/**
 * Returns effective groupmode used in activity, course setting
 * overrides activity setting if groupmodeforce enabled.
 *
 * @global object
 * @global object
 * @param object $cm the course module object. Only the ->course and ->groupmode need to be set.
 * @param object $course object optional course object to improve perf
 * @return integer group mode
 */
function groups_get_activity_groupmode($cm, $course=null) {
    global $COURSE, $DB;

    // get course object (reuse COURSE if possible)
    if (isset($course->id) and $course->id == $cm->course) {
        //ok
    } else if ($cm->course == $COURSE->id) {
        $course = $COURSE;
    } else {
        if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
            print_error('invalidcourseid');
        }
    }

    return empty($course->groupmodeforce) ? $cm->groupmode : $course->groupmode;
}

/**
 * Print group menu selector for course level.
 *
 * @param stdClass $course course object
 * @param string|moodle_url $urlroot return address
 * @param boolean $return return as string instead of printing
 * @return mixed void or string depending on $return param
 */
function groups_print_course_menu($course, $urlroot, $return=false) {
    global $USER, $OUTPUT;

    if (!$groupmode = $course->groupmode) {
        if ($return) {
            return '';
        } else {
            return;
        }
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $aag = has_capability('moodle/site:accessallgroups', $context);

    if ($groupmode == VISIBLEGROUPS or $aag) {
        $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
    } else {
        $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
    }

    $activegroup = groups_get_course_group($course, true, $allowedgroups);

    $groupsmenu = array();
    if (!$allowedgroups or $groupmode == VISIBLEGROUPS or $aag) {
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

    if ($aag and $course->defaultgroupingid) {
        if ($grouping = groups_get_grouping($course->defaultgroupingid)) {
            $grouplabel = $grouplabel . ' (' . format_string($grouping->name) . ')';
        }
    }

    if (count($groupsmenu) == 1) {
        $groupname = reset($groupsmenu);
        $output = $grouplabel.': '.$groupname;
    } else {
        $select = new single_select(new moodle_url($urlroot), 'group', $groupsmenu, $activegroup, null, 'selectgroup');
        $select->label = $grouplabel;
        $output = $OUTPUT->render($select);
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
 *
 * @param stdClass $cm course module object
 * @param string|moodle_url $urlroot return address that users get to if they choose an option;
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
    global $USER, $OUTPUT;

    if ($urlroot instanceof moodle_url) {
        // no changes necessary

    } else {
        if (strpos($urlroot, 'http') !== 0) { // Will also work for https
            // Display error if urlroot is not absolute (this causes the non-JS version to break)
            debugging('groups_print_activity_menu requires absolute URL for ' .
                      '$urlroot, not <tt>' . s($urlroot) . '</tt>. Example: ' .
                      'groups_print_activity_menu($cm, $CFG->wwwroot . \'/mod/mymodule/view.php?id=13\');',
                      DEBUG_DEVELOPER);
        }
        $urlroot = new moodle_url($urlroot);
    }

    if (!$groupmode = groups_get_activity_groupmode($cm)) {
        if ($return) {
            return '';
        } else {
            return;
        }
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $aag = has_capability('moodle/site:accessallgroups', $context);

    if ($groupmode == VISIBLEGROUPS or $aag) {
        $allowedgroups = groups_get_all_groups($cm->course, 0, $cm->groupingid); // any group in grouping
    } else {
        $allowedgroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid); // only assigned groups
    }

    $activegroup = groups_get_activity_group($cm, true, $allowedgroups);

    $groupsmenu = array();
    if ((!$allowedgroups or $groupmode == VISIBLEGROUPS or $aag) and !$hideallparticipants) {
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

    if ($aag and $cm->groupingid) {
        if ($grouping = groups_get_grouping($cm->groupingid)) {
            $grouplabel = $grouplabel . ' (' . format_string($grouping->name) . ')';
        }
    }

    if (count($groupsmenu) == 1) {
        $groupname = reset($groupsmenu);
        $output = $grouplabel.': '.$groupname;
    } else {
        $select = new single_select($urlroot, 'group', $groupsmenu, $activegroup, null, 'selectgroup');
        $select->label = $grouplabel;
        $output = $OUTPUT->render($select);
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
 * @param stdClass $course course bject
 * @param boolean $update change active group if group param submitted
 * @param array $allowedgroups list of groups user may access (INTERNAL, to be used only from groups_print_course_menu())
 * @return mixed false if groups not used, int if groups used, 0 means all groups (access must be verified in SEPARATE mode)
 */
function groups_get_course_group($course, $update=false, $allowedgroups=null) {
    global $USER, $SESSION;

    if (!$groupmode = $course->groupmode) {
        // NOGROUPS used
        return false;
    }

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (has_capability('moodle/site:accessallgroups', $context)) {
        $groupmode = 'aag';
    }

    if (!is_array($allowedgroups)) {
        if ($groupmode == VISIBLEGROUPS or $groupmode === 'aag') {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        }
    }

    _group_verify_activegroup($course->id, $groupmode, $course->defaultgroupingid, $allowedgroups);

    // set new active group if requested
    $changegroup = optional_param('group', -1, PARAM_INT);
    if ($update and $changegroup != -1) {

        if ($changegroup == 0) {
            // do not allow changing to all groups without accessallgroups capability
            if ($groupmode == VISIBLEGROUPS or $groupmode === 'aag') {
                $SESSION->activegroup[$course->id][$groupmode][$course->defaultgroupingid] = 0;
            }

        } else {
            if ($allowedgroups and array_key_exists($changegroup, $allowedgroups)) {
                $SESSION->activegroup[$course->id][$groupmode][$course->defaultgroupingid] = $changegroup;
            }
        }
    }

    return $SESSION->activegroup[$course->id][$groupmode][$course->defaultgroupingid];
}

/**
 * Returns group active in activity, changes the group by default if 'group' page param present
 *
 * @param stdClass $cm course module object
 * @param boolean $update change active group if group param submitted
 * @param array $allowedgroups list of groups user may access (INTERNAL, to be used only from groups_print_activity_menu())
 * @return mixed false if groups not used, int if groups used, 0 means all groups (access must be verified in SEPARATE mode)
 */
function groups_get_activity_group($cm, $update=false, $allowedgroups=null) {
    global $USER, $SESSION;

    if (!$groupmode = groups_get_activity_groupmode($cm)) {
        // NOGROUPS used
        return false;
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('moodle/site:accessallgroups', $context)) {
        $groupmode = 'aag';
    }

    if (!is_array($allowedgroups)) {
        if ($groupmode == VISIBLEGROUPS or $groupmode === 'aag') {
            $allowedgroups = groups_get_all_groups($cm->course, 0, $cm->groupingid);
        } else {
            $allowedgroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid);
        }
    }

    _group_verify_activegroup($cm->course, $groupmode, $cm->groupingid, $allowedgroups);

    // set new active group if requested
    $changegroup = optional_param('group', -1, PARAM_INT);
    if ($update and $changegroup != -1) {

        if ($changegroup == 0) {
            // allgroups visible only in VISIBLEGROUPS or when accessallgroups
            if ($groupmode == VISIBLEGROUPS or $groupmode === 'aag') {
                $SESSION->activegroup[$cm->course][$groupmode][$cm->groupingid] = 0;
            }

        } else {
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
 *
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
 *
 * $USER If $userid is null, use the global object.
 *
 * @param int $cm The course module
 * @param int $userid The user to check against the group.
 * @return boolean True if the user can view the course module, false otherwise.
 */
function groups_course_module_visible($cm, $userid=null) {
    global $CFG, $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }
    if (empty($CFG->enablegroupmembersonly)) {
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

/**
 * Internal method, sets up $SESSION->activegroup and verifies previous value
 *
 * @private
 * @param int $courseid
 * @param int|string $groupmode SEPARATEGROUPS, VISIBLEGROUPS or 'aag' (access all groups)
 * @param int $groupingid 0 means all groups
 * @param all $allowedgroups list of groups user can see
 * @return void
 */
function _group_verify_activegroup($courseid, $groupmode, $groupingid, array $allowedgroups) {
    global $SESSION, $USER;

    // init activegroup array if necessary
    if (!isset($SESSION->activegroup)) {
        $SESSION->activegroup = array();
    }
    if (!array_key_exists($courseid, $SESSION->activegroup)) {
        $SESSION->activegroup[$courseid] = array(SEPARATEGROUPS=>array(), VISIBLEGROUPS=>array(), 'aag'=>array());
    }

    // make sure that the current group info is ok
    if (array_key_exists($groupingid, $SESSION->activegroup[$courseid][$groupmode]) and !array_key_exists($SESSION->activegroup[$courseid][$groupmode][$groupingid], $allowedgroups)) {
        // active group does not exist anymore or is 0
        if ($SESSION->activegroup[$courseid][$groupmode][$groupingid] > 0 or $groupmode == SEPARATEGROUPS) {
            // do not do this if all groups selected and groupmode is not separate
            unset($SESSION->activegroup[$courseid][$groupmode][$groupingid]);
        }
    }

    // set up defaults if necessary
    if (!array_key_exists($groupingid, $SESSION->activegroup[$courseid][$groupmode])) {
        if ($groupmode == 'aag') {
            $SESSION->activegroup[$courseid][$groupmode][$groupingid] = 0; // all groups by default if user has accessallgroups

        } else if ($allowedgroups) {
            if ($groupmode != SEPARATEGROUPS and $mygroups = groups_get_all_groups($courseid, $USER->id, $groupingid)) {
                $firstgroup = reset($mygroups);
            } else {
                $firstgroup = reset($allowedgroups);
            }
            $SESSION->activegroup[$courseid][$groupmode][$groupingid] = $firstgroup->id;

        } else {
            // this happen when user not assigned into group in SEPARATEGROUPS mode or groups do not exist yet
            // mod authors must add extra checks for this when SEPARATEGROUPS mode used (such as when posting to forum)
            $SESSION->activegroup[$courseid][$groupmode][$groupingid] = 0;
        }
    }
}