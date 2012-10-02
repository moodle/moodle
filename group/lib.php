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
 * Extra library for groups and groupings.
 *
 * @copyright 2006 The Open University, J.White AT open.ac.uk, Petr Skoda (skodak)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core_group
 */

/*
 * INTERNAL FUNCTIONS - to be used by moodle core only
 * require_once $CFG->dirroot.'/group/lib.php' must be used
 */

/**
 * Adds a specified user to a group
 *
 * @param mixed $grouporid  The group id or group object
 * @param mixed $userorid   The user id or user object
 * @param string $component Optional component name e.g. 'enrol_imsenterprise'
 * @param int $itemid Optional itemid associated with component
 * @return bool True if user added successfully or the user is already a
 * member of the group, false otherwise.
 */
function groups_add_member($grouporid, $userorid, $component=null, $itemid=0) {
    global $DB;

    if (is_object($userorid)) {
        $userid = $userorid->id;
        $user   = $userorid;
        if (!isset($user->deleted)) {
            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
        }
    } else {
        $userid = $userorid;
        $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
    }

    if ($user->deleted) {
        return false;
    }

    if (is_object($grouporid)) {
        $groupid = $grouporid->id;
        $group   = $grouporid;
    } else {
        $groupid = $grouporid;
        $group = $DB->get_record('groups', array('id'=>$groupid), '*', MUST_EXIST);
    }

    //check if the user a participant of the group course
    if (!is_enrolled(context_course::instance($group->courseid), $userid)) {
        return false;
    }

    if (groups_is_member($groupid, $userid)) {
        return true;
    }

    $member = new stdClass();
    $member->groupid   = $groupid;
    $member->userid    = $userid;
    $member->timeadded = time();
    $member->component = '';
    $member->itemid = 0;

    // Check the component exists if specified
    if (!empty($component)) {
        $dir = get_component_directory($component);
        if ($dir && is_dir($dir)) {
            // Component exists and can be used
            $member->component = $component;
            $member->itemid = $itemid;
        } else {
            throw new coding_exception('Invalid call to groups_add_member(). An invalid component was specified');
        }
    }

    if ($itemid !== 0 && empty($member->component)) {
        // An itemid can only be specified if a valid component was found
        throw new coding_exception('Invalid call to groups_add_member(). A component must be specified if an itemid is given');
    }

    $DB->insert_record('groups_members', $member);

    //update group info
    $DB->set_field('groups', 'timemodified', $member->timeadded, array('id'=>$groupid));

    //trigger groups events
    $eventdata = new stdClass();
    $eventdata->groupid = $groupid;
    $eventdata->userid  = $userid;
    $eventdata->component = $member->component;
    $eventdata->itemid = $member->itemid;
    events_trigger('groups_member_added', $eventdata);

    return true;
}

/**
 * Checks whether the current user is permitted (using the normal UI) to
 * remove a specific group member, assuming that they have access to remove
 * group members in general.
 *
 * For automatically-created group member entries, this checks with the
 * relevant plugin to see whether it is permitted. The default, if the plugin
 * doesn't provide a function, is true.
 *
 * For other entries (and any which have already been deleted/don't exist) it
 * just returns true.
 *
 * @param mixed $grouporid The group id or group object
 * @param mixed $userorid The user id or user object
 * @return bool True if permitted, false otherwise
 */
function groups_remove_member_allowed($grouporid, $userorid) {
    global $DB;

    if (is_object($userorid)) {
        $userid = $userorid->id;
    } else {
        $userid = $userorid;
    }
    if (is_object($grouporid)) {
        $groupid = $grouporid->id;
    } else {
        $groupid = $grouporid;
    }

    // Get entry
    if (!($entry = $DB->get_record('groups_members',
            array('groupid' => $groupid, 'userid' => $userid), '*', IGNORE_MISSING))) {
        // If the entry does not exist, they are allowed to remove it (this
        // is consistent with groups_remove_member below).
        return true;
    }

    // If the entry does not have a component value, they can remove it
    if (empty($entry->component)) {
        return true;
    }

    // It has a component value, so we need to call a plugin function (if it
    // exists); the default is to allow removal
    return component_callback($entry->component, 'allow_group_member_remove',
            array($entry->itemid, $entry->groupid, $entry->userid), true);
}

/**
 * Deletes the link between the specified user and group.
 *
 * @param mixed $grouporid  The group id or group object
 * @param mixed $userorid   The user id or user object
 * @return bool True if deletion was successful, false otherwise
 */
function groups_remove_member($grouporid, $userorid) {
    global $DB;

    if (is_object($userorid)) {
        $userid = $userorid->id;
        $user   = $userorid;
    } else {
        $userid = $userorid;
        $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
    }

    if (is_object($grouporid)) {
        $groupid = $grouporid->id;
        $group   = $grouporid;
    } else {
        $groupid = $grouporid;
        $group = $DB->get_record('groups', array('id'=>$groupid), '*', MUST_EXIST);
    }

    if (!groups_is_member($groupid, $userid)) {
        return true;
    }

    $DB->delete_records('groups_members', array('groupid'=>$groupid, 'userid'=>$userid));

    //update group info
    $DB->set_field('groups', 'timemodified', time(), array('id'=>$groupid));

    //trigger groups events
    $eventdata = new stdClass();
    $eventdata->groupid = $groupid;
    $eventdata->userid  = $userid;
    events_trigger('groups_member_removed', $eventdata);

    return true;
}

/**
 * Add a new group
 *
 * @param stdClass $data group properties
 * @param stdClass $editform
 * @param array $editoroptions
 * @return id of group or false if error
 */
function groups_create_group($data, $editform = false, $editoroptions = false) {
    global $CFG, $DB;

    //check that courseid exists
    $course = $DB->get_record('course', array('id' => $data->courseid), '*', MUST_EXIST);
    $context = context_course::instance($course->id);

    $data->timecreated  = time();
    $data->timemodified = $data->timecreated;
    $data->name         = trim($data->name);
    if (isset($data->idnumber)) {
        $data->idnumber = trim($data->idnumber);
        if (groups_get_group_by_idnumber($course->id, $data->idnumber)) {
            throw new moodle_exception('idnumbertaken');
        }
    }

    if ($editform and $editoroptions) {
        $data->description = $data->description_editor['text'];
        $data->descriptionformat = $data->description_editor['format'];
    }

    $data->id = $DB->insert_record('groups', $data);

    if ($editform and $editoroptions) {
        // Update description from editor with fixed files
        $data = file_postupdate_standard_editor($data, 'description', $editoroptions, $context, 'group', 'description', $data->id);
        $upd = new stdClass();
        $upd->id                = $data->id;
        $upd->description       = $data->description;
        $upd->descriptionformat = $data->descriptionformat;
        $DB->update_record('groups', $upd);
    }

    $group = $DB->get_record('groups', array('id'=>$data->id));

    if ($editform) {
        groups_update_group_icon($group, $data, $editform);
    }

    //trigger groups events
    events_trigger('groups_group_created', $group);

    return $group->id;
}

/**
 * Add a new grouping
 *
 * @param stdClass $data grouping properties
 * @param array $editoroptions
 * @return id of grouping or false if error
 */
function groups_create_grouping($data, $editoroptions=null) {
    global $DB;

    $data->timecreated  = time();
    $data->timemodified = $data->timecreated;
    $data->name         = trim($data->name);
    if (isset($data->idnumber)) {
        $data->idnumber = trim($data->idnumber);
        if (groups_get_grouping_by_idnumber($data->courseid, $data->idnumber)) {
            throw new moodle_exception('idnumbertaken');
        }
    }

    if ($editoroptions !== null) {
        $data->description = $data->description_editor['text'];
        $data->descriptionformat = $data->description_editor['format'];
    }

    $id = $DB->insert_record('groupings', $data);

    //trigger groups events
    $data->id = $id;

    if ($editoroptions !== null) {
        $description = new stdClass;
        $description->id = $data->id;
        $description->description_editor = $data->description_editor;
        $description = file_postupdate_standard_editor($description, 'description', $editoroptions, $editoroptions['context'], 'grouping', 'description', $description->id);
        $DB->update_record('groupings', $description);
    }

    events_trigger('groups_grouping_created', $data);

    return $id;
}

/**
 * Update the group icon from form data
 *
 * @param stdClass $group group information
 * @param stdClass $data
 * @param stdClass $editform
 */
function groups_update_group_icon($group, $data, $editform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/gdlib.php");

    $fs = get_file_storage();
    $context = context_course::instance($group->courseid, MUST_EXIST);

    //TODO: it would make sense to allow picture deleting too (skodak)
    if (!empty($CFG->gdversion)) {
        if ($iconfile = $editform->save_temp_file('imagefile')) {
            if (process_new_icon($context, 'group', 'icon', $group->id, $iconfile)) {
                $DB->set_field('groups', 'picture', 1, array('id'=>$group->id));
                $group->picture = 1;
            } else {
                $fs->delete_area_files($context->id, 'group', 'icon', $group->id);
                $DB->set_field('groups', 'picture', 0, array('id'=>$group->id));
                $group->picture = 0;
            }
            @unlink($iconfile);
        }
    }
}

/**
 * Update group
 *
 * @param stdClass $data group properties (with magic quotes)
 * @param stdClass $editform
 * @param array $editoroptions
 * @return bool true or exception
 */
function groups_update_group($data, $editform = false, $editoroptions = false) {
    global $CFG, $DB;

    $context = context_course::instance($data->courseid);

    $data->timemodified = time();
    $data->name         = trim($data->name);
    if (isset($data->idnumber)) {
        $data->idnumber = trim($data->idnumber);
        if (($existing = groups_get_group_by_idnumber($data->courseid, $data->idnumber)) && $existing->id != $data->id) {
            throw new moodle_exception('idnumbertaken');
        }
    }

    if ($editform and $editoroptions) {
        $data = file_postupdate_standard_editor($data, 'description', $editoroptions, $context, 'group', 'description', $data->id);
    }

    $DB->update_record('groups', $data);

    $group = $DB->get_record('groups', array('id'=>$data->id));

    if ($editform) {
        groups_update_group_icon($group, $data, $editform);
    }

    //trigger groups events
    events_trigger('groups_group_updated', $group);


    return true;
}

/**
 * Update grouping
 *
 * @param stdClass $data grouping properties (with magic quotes)
 * @param array $editoroptions
 * @return bool true or exception
 */
function groups_update_grouping($data, $editoroptions=null) {
    global $DB;
    $data->timemodified = time();
    $data->name         = trim($data->name);
    if (isset($data->idnumber)) {
        $data->idnumber = trim($data->idnumber);
        if (($existing = groups_get_grouping_by_idnumber($data->courseid, $data->idnumber)) && $existing->id != $data->id) {
            throw new moodle_exception('idnumbertaken');
        }
    }
    if ($editoroptions !== null) {
        $data = file_postupdate_standard_editor($data, 'description', $editoroptions, $editoroptions['context'], 'grouping', 'description', $data->id);
    }
    $DB->update_record('groupings', $data);
    //trigger groups events
    events_trigger('groups_grouping_updated', $data);

    return true;
}

/**
 * Delete a group best effort, first removing members and links with courses and groupings.
 * Removes group avatar too.
 *
 * @param mixed $grouporid The id of group to delete or full group object
 * @return bool True if deletion was successful, false otherwise
 */
function groups_delete_group($grouporid) {
    global $CFG, $DB;
    require_once("$CFG->libdir/gdlib.php");

    if (is_object($grouporid)) {
        $groupid = $grouporid->id;
        $group   = $grouporid;
    } else {
        $groupid = $grouporid;
        if (!$group = $DB->get_record('groups', array('id'=>$groupid))) {
            //silently ignore attempts to delete missing already deleted groups ;-)
            return true;
        }
    }

    // delete group calendar events
    $DB->delete_records('event', array('groupid'=>$groupid));
    //first delete usage in groupings_groups
    $DB->delete_records('groupings_groups', array('groupid'=>$groupid));
    //delete members
    $DB->delete_records('groups_members', array('groupid'=>$groupid));
    //group itself last
    $DB->delete_records('groups', array('id'=>$groupid));

    // Delete all files associated with this group
    $context = context_course::instance($group->courseid);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'group', 'description', $groupid);
    $fs->delete_area_files($context->id, 'group', 'icon', $groupid);

    //trigger groups events
    events_trigger('groups_group_deleted', $group);

    return true;
}

/**
 * Delete grouping
 *
 * @param int $groupingorid
 * @return bool success
 */
function groups_delete_grouping($groupingorid) {
    global $DB;

    if (is_object($groupingorid)) {
        $groupingid = $groupingorid->id;
        $grouping   = $groupingorid;
    } else {
        $groupingid = $groupingorid;
        if (!$grouping = $DB->get_record('groupings', array('id'=>$groupingorid))) {
            //silently ignore attempts to delete missing already deleted groupings ;-)
            return true;
        }
    }

    //first delete usage in groupings_groups
    $DB->delete_records('groupings_groups', array('groupingid'=>$groupingid));
    // remove the default groupingid from course
    $DB->set_field('course', 'defaultgroupingid', 0, array('defaultgroupingid'=>$groupingid));
    // remove the groupingid from all course modules
    $DB->set_field('course_modules', 'groupingid', 0, array('groupingid'=>$groupingid));
    //group itself last
    $DB->delete_records('groupings', array('id'=>$groupingid));

    $context = context_course::instance($grouping->courseid);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'grouping', 'description', $groupingid);
    foreach ($files as $file) {
        $file->delete();
    }

    //trigger groups events
    events_trigger('groups_grouping_deleted', $grouping);

    return true;
}

/**
 * Remove all users (or one user) from all groups in course
 *
 * @param int $courseid
 * @param int $userid 0 means all users
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_group_members($courseid, $userid=0, $showfeedback=false) {
    global $DB, $OUTPUT;

    if (is_bool($userid)) {
        debugging('Incorrect userid function parameter');
        return false;
    }

    $params = array('courseid'=>$courseid);

    if ($userid) {
        $usersql = "AND userid = :userid";
        $params['userid'] = $userid;
    } else {
        $usersql = "";
    }

    $groupssql = "SELECT id FROM {groups} g WHERE g.courseid = :courseid";
    $DB->delete_records_select('groups_members', "groupid IN ($groupssql) $usersql", $params);

    //trigger groups events
    $eventdata = new stdClass();
    $eventdata->courseid = $courseid;
    $eventdata->userid   = $userid;
    events_trigger('groups_members_removed', $eventdata);

    if ($showfeedback) {
        echo $OUTPUT->notification(get_string('deleted').' - '.get_string('groupmembers', 'group'), 'notifysuccess');
    }

    return true;
}

/**
 * Remove all groups from all groupings in course
 *
 * @param int $courseid
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_groupings_groups($courseid, $showfeedback=false) {
    global $DB, $OUTPUT;

    $groupssql = "SELECT id FROM {groups} g WHERE g.courseid = ?";
    $DB->delete_records_select('groupings_groups', "groupid IN ($groupssql)", array($courseid));

    //trigger groups events
    events_trigger('groups_groupings_groups_removed', $courseid);

    // no need to show any feedback here - we delete usually first groupings and then groups

    return true;
}

/**
 * Delete all groups from course
 *
 * @param int $courseid
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_groups($courseid, $showfeedback=false) {
    global $CFG, $DB, $OUTPUT;

    // delete any uses of groups
    // Any associated files are deleted as part of groups_delete_groupings_groups
    groups_delete_groupings_groups($courseid, $showfeedback);
    groups_delete_group_members($courseid, 0, $showfeedback);

    // delete group pictures and descriptions
    $context = context_course::instance($courseid);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'group');

    // delete group calendar events
    $groupssql = "SELECT id FROM {groups} g WHERE g.courseid = ?";
    $DB->delete_records_select('event', "groupid IN ($groupssql)", array($courseid));

    $context = context_course::instance($courseid);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'group');

    $DB->delete_records('groups', array('courseid'=>$courseid));

    // trigger groups events
    events_trigger('groups_groups_deleted', $courseid);

    if ($showfeedback) {
        echo $OUTPUT->notification(get_string('deleted').' - '.get_string('groups', 'group'), 'notifysuccess');
    }

    return true;
}

/**
 * Delete all groupings from course
 *
 * @param int $courseid
 * @param bool $showfeedback
 * @return bool success
 */
function groups_delete_groupings($courseid, $showfeedback=false) {
    global $DB, $OUTPUT;

    // delete any uses of groupings
    $sql = "DELETE FROM {groupings_groups}
             WHERE groupingid in (SELECT id FROM {groupings} g WHERE g.courseid = ?)";
    $DB->execute($sql, array($courseid));

    // remove the default groupingid from course
    $DB->set_field('course', 'defaultgroupingid', 0, array('id'=>$courseid));
    // remove the groupingid from all course modules
    $DB->set_field('course_modules', 'groupingid', 0, array('course'=>$courseid));

    // Delete all files associated with groupings for this course
    $context = context_course::instance($courseid);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'grouping');

    $DB->delete_records('groupings', array('courseid'=>$courseid));

    // trigger groups events
    events_trigger('groups_groupings_deleted', $courseid);

    if ($showfeedback) {
        echo $OUTPUT->notification(get_string('deleted').' - '.get_string('groupings', 'group'), 'notifysuccess');
    }

    return true;
}

/* =================================== */
/* various functions used by groups UI */
/* =================================== */

/**
 * Obtains a list of the possible roles that group members might come from,
 * on a course. Generally this includes only profile roles.
 *
 * @param context $context Context of course
 * @return Array of role ID integers, or false if error/none.
 */
function groups_get_possible_roles($context) {
    $roles = get_profile_roles($context);
    return array_keys($roles);
}


/**
 * Gets potential group members for grouping
 *
 * @param int $courseid The id of the course
 * @param int $roleid The role to select users from
 * @param int $cohortid restrict to cohort id
 * @param string $orderby The column to sort users by
 * @return array An array of the users
 */
function groups_get_potential_members($courseid, $roleid = null, $cohortid = null, $orderby = 'lastname ASC, firstname ASC') {
    global $DB;

    $context = context_course::instance($courseid);

    // we are looking for all users with this role assigned in this context or higher
    $listofcontexts = get_related_contexts_string($context);

    list($esql, $params) = get_enrolled_sql($context);

    if ($roleid) {
        $params['roleid'] = $roleid;
        $where = "WHERE u.id IN (SELECT userid
                                   FROM {role_assignments}
                                  WHERE roleid = :roleid AND contextid $listofcontexts)";
    } else {
        $where = "";
    }

    if ($cohortid) {
        $cohortjoin = "JOIN {cohort_members} cm ON (cm.userid = u.id AND cm.cohortid = :cohortid)";
        $params['cohortid'] = $cohortid;
    } else {
        $cohortjoin = "";
    }

    $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.idnumber
              FROM {user} u
              JOIN ($esql) e ON e.id = u.id
       $cohortjoin
            $where
          ORDER BY $orderby";

    return $DB->get_records_sql($sql, $params);

}

/**
 * Parse a group name for characters to replace
 *
 * @param string $format The format a group name will follow
 * @param int $groupnumber The number of the group to be used in the parsed format string
 * @return string the parsed format string
 */
function groups_parse_name($format, $groupnumber) {
    if (strstr($format, '@') !== false) { // Convert $groupnumber to a character series
        $letter = 'A';
        for($i=0; $i<$groupnumber; $i++) {
            $letter++;
        }
        $str = str_replace('@', $letter, $format);
    } else {
        $str = str_replace('#', $groupnumber+1, $format);
    }
    return($str);
}

/**
 * Assigns group into grouping
 *
 * @param int groupingid
 * @param int groupid
 * @return bool true or exception
 */
function groups_assign_grouping($groupingid, $groupid) {
    global $DB;

    if ($DB->record_exists('groupings_groups', array('groupingid'=>$groupingid, 'groupid'=>$groupid))) {
        return true;
    }
    $assign = new stdClass();
    $assign->groupingid = $groupingid;
    $assign->groupid    = $groupid;
    $assign->timeadded  = time();
    $DB->insert_record('groupings_groups', $assign);

    return true;
}

/**
 * Unassigns group grom grouping
 *
 * @param int groupingid
 * @param int groupid
 * @return bool success
 */
function groups_unassign_grouping($groupingid, $groupid) {
    global $DB;
    $DB->delete_records('groupings_groups', array('groupingid'=>$groupingid, 'groupid'=>$groupid));

    return true;
}

/**
 * Lists users in a group based on their role on the course.
 * Returns false if there's an error or there are no users in the group.
 * Otherwise returns an array of role ID => role data, where role data includes:
 * (role) $id, $shortname, $name
 * $users: array of objects for each user which include the specified fields
 * Users who do not have a role are stored in the returned array with key '-'
 * and pseudo-role details (including a name, 'No role'). Users with multiple
 * roles, same deal with key '*' and name 'Multiple roles'. You can find out
 * which roles each has by looking in the $roles array of the user object.
 *
 * @param int $groupid
 * @param int $courseid Course ID (should match the group's course)
 * @param string $fields List of fields from user table prefixed with u, default 'u.*'
 * @param string $sort SQL ORDER BY clause, default (when null passed) is what comes from users_order_by_sql.
 * @param string $extrawheretest extra SQL conditions ANDed with the existing where clause.
 * @param array $whereorsortparams any parameters required by $extrawheretest (named parameters).
 * @return array Complex array as described above
 */
function groups_get_members_by_role($groupid, $courseid, $fields='u.*',
        $sort=null, $extrawheretest='', $whereorsortparams=array()) {
    global $CFG, $DB;

    // Retrieve information about all users and their roles on the course or
    // parent ('related') contexts
    $context = context_course::instance($courseid);

    if ($extrawheretest) {
        $extrawheretest = ' AND ' . $extrawheretest;
    }

    if (is_null($sort)) {
        list($sort, $sortparams) = users_order_by_sql('u');
        $whereorsortparams = array_merge($whereorsortparams, $sortparams);
    }

    $sql = "SELECT r.id AS roleid, u.id AS userid, $fields
              FROM {groups_members} gm
              JOIN {user} u ON u.id = gm.userid
         LEFT JOIN {role_assignments} ra ON (ra.userid = u.id AND ra.contextid ".get_related_contexts_string($context).")
         LEFT JOIN {role} r ON r.id = ra.roleid
             WHERE gm.groupid=:mgroupid
                   ".$extrawheretest."
          ORDER BY r.sortorder, $sort";
    $whereorsortparams['mgroupid'] = $groupid;
    $rs = $DB->get_recordset_sql($sql, $whereorsortparams);

    return groups_calculate_role_people($rs, $context);
}

/**
 * Internal function used by groups_get_members_by_role to handle the
 * results of a database query that includes a list of users and possible
 * roles on a course.
 *
 * @param moodle_recordset $rs The record set (may be false)
 * @param int $context ID of course context
 * @return array As described in groups_get_members_by_role
 */
function groups_calculate_role_people($rs, $context) {
    global $CFG, $DB;

    if (!$rs) {
        return array();
    }

    $allroles = role_fix_names(get_all_roles($context), $context);

    // Array of all involved roles
    $roles = array();
    // Array of all retrieved users
    $users = array();
    // Fill arrays
    foreach ($rs as $rec) {
        // Create information about user if this is a new one
        if (!array_key_exists($rec->userid, $users)) {
            // User data includes all the optional fields, but not any of the
            // stuff we added to get the role details
            $userdata = clone($rec);
            unset($userdata->roleid);
            unset($userdata->roleshortname);
            unset($userdata->rolename);
            unset($userdata->userid);
            $userdata->id = $rec->userid;

            // Make an array to hold the list of roles for this user
            $userdata->roles = array();
            $users[$rec->userid] = $userdata;
        }
        // If user has a role...
        if (!is_null($rec->roleid)) {
            // Create information about role if this is a new one
            if (!array_key_exists($rec->roleid, $roles)) {
                $role = $allroles[$rec->roleid];
                $roledata = new stdClass();
                $roledata->id        = $role->id;
                $roledata->shortname = $role->shortname;
                $roledata->name      = $role->localname;
                $roledata->users = array();
                $roles[$roledata->id] = $roledata;
            }
            // Record that user has role
            $users[$rec->userid]->roles[] = $roles[$rec->roleid];
        }
    }
    $rs->close();

    // Return false if there weren't any users
    if (count($users) == 0) {
        return false;
    }

    // Add pseudo-role for multiple roles
    $roledata = new stdClass();
    $roledata->name = get_string('multipleroles','role');
    $roledata->users = array();
    $roles['*'] = $roledata;

    $roledata = new stdClass();
    $roledata->name = get_string('noroles','role');
    $roledata->users = array();
    $roles[0] = $roledata;

    // Now we rearrange the data to store users by role
    foreach ($users as $userid=>$userdata) {
        $rolecount = count($userdata->roles);
        if ($rolecount == 0) {
            // does not have any roles
            $roleid = 0;
        } else if($rolecount > 1) {
            $roleid = '*';
        } else {
            $roleid = $userdata->roles[0]->id;
        }
        $roles[$roleid]->users[$userid] = $userdata;
    }

    // Delete roles not used
    foreach ($roles as $key=>$roledata) {
        if (count($roledata->users)===0) {
            unset($roles[$key]);
        }
    }

    // Return list of roles containing their users
    return $roles;
}
