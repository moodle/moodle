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

    // Check if the user a participant of the group course.
    $context = context_course::instance($group->courseid);
    if (!is_enrolled($context, $userid)) {
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
        $dir = core_component::get_component_directory($component);
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

    // Update group info, and group object.
    $DB->set_field('groups', 'timemodified', $member->timeadded, array('id'=>$groupid));
    $group->timemodified = $member->timeadded;

    // Invalidate the group and grouping cache for users.
    cache_helper::invalidate_by_definition('core', 'user_group_groupings', array(), array($userid));

    // Trigger group event.
    $params = array(
        'context' => $context,
        'objectid' => $groupid,
        'relateduserid' => $userid,
        'other' => array(
            'component' => $member->component,
            'itemid' => $member->itemid
        )
    );
    $event = \core\event\group_member_added::create($params);
    $event->add_record_snapshot('groups', $group);
    $event->trigger();

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
    } else {
        $userid = $userorid;
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

    // Update group info.
    $time = time();
    $DB->set_field('groups', 'timemodified', $time, array('id' => $groupid));
    $group->timemodified = $time;

    // Invalidate the group and grouping cache for users.
    cache_helper::invalidate_by_definition('core', 'user_group_groupings', array(), array($userid));

    // Trigger group event.
    $params = array(
        'context' => context_course::instance($group->courseid),
        'objectid' => $groupid,
        'relateduserid' => $userid
    );
    $event = \core\event\group_member_removed::create($params);
    $event->add_record_snapshot('groups', $group);
    $event->trigger();

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

    // Invalidate the grouping cache for the course
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($course->id));

    // Trigger group event.
    $params = array(
        'context' => $context,
        'objectid' => $group->id
    );
    $event = \core\event\group_created::create($params);
    $event->add_record_snapshot('groups', $group);
    $event->trigger();

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
    $data->id = $id;

    if ($editoroptions !== null) {
        $description = new stdClass;
        $description->id = $data->id;
        $description->description_editor = $data->description_editor;
        $description = file_postupdate_standard_editor($description, 'description', $editoroptions, $editoroptions['context'], 'grouping', 'description', $description->id);
        $DB->update_record('groupings', $description);
    }

    // Invalidate the grouping cache for the course
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($data->courseid));

    // Trigger group event.
    $params = array(
        'context' => context_course::instance($data->courseid),
        'objectid' => $id
    );
    $event = \core\event\grouping_created::create($params);
    $event->trigger();

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
    $newpicture = $group->picture;

    if (!empty($data->deletepicture)) {
        $fs->delete_area_files($context->id, 'group', 'icon', $group->id);
        $newpicture = 0;
    } else if ($iconfile = $editform->save_temp_file('imagefile')) {
        if ($rev = process_new_icon($context, 'group', 'icon', $group->id, $iconfile)) {
            $newpicture = $rev;
        } else {
            $fs->delete_area_files($context->id, 'group', 'icon', $group->id);
            $newpicture = 0;
        }
        @unlink($iconfile);
    }

    if ($newpicture != $group->picture) {
        $DB->set_field('groups', 'picture', $newpicture, array('id' => $group->id));
        $group->picture = $newpicture;

        // Invalidate the group data as we've updated the group record.
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($group->courseid));
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
    if (isset($data->name)) {
        $data->name = trim($data->name);
    }
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

    // Invalidate the group data.
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($data->courseid));

    $group = $DB->get_record('groups', array('id'=>$data->id));

    if ($editform) {
        groups_update_group_icon($group, $data, $editform);
    }

    // Trigger group event.
    $params = array(
        'context' => $context,
        'objectid' => $group->id
    );
    $event = \core\event\group_updated::create($params);
    $event->add_record_snapshot('groups', $group);
    $event->trigger();

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
    if (isset($data->name)) {
        $data->name = trim($data->name);
    }
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

    // Invalidate the group data.
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($data->courseid));

    // Trigger group event.
    $params = array(
        'context' => context_course::instance($data->courseid),
        'objectid' => $data->id
    );
    $event = \core\event\grouping_updated::create($params);
    $event->trigger();

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

    // Invalidate the grouping cache for the course
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($group->courseid));
    // Purge the group and grouping cache for users.
    cache_helper::purge_by_definition('core', 'user_group_groupings');

    // Trigger group event.
    $params = array(
        'context' => $context,
        'objectid' => $groupid
    );
    $event = \core\event\group_deleted::create($params);
    $event->add_record_snapshot('groups', $group);
    $event->trigger();

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

    // Invalidate the grouping cache for the course
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($grouping->courseid));
    // Purge the group and grouping cache for users.
    cache_helper::purge_by_definition('core', 'user_group_groupings');

    // Trigger group event.
    $params = array(
        'context' => $context,
        'objectid' => $groupingid
    );
    $event = \core\event\grouping_deleted::create($params);
    $event->add_record_snapshot('groupings', $grouping);
    $event->trigger();

    return true;
}

/**
 * Remove all users (or one user) from all groups in course
 *
 * @param int $courseid
 * @param int $userid 0 means all users
 * @param bool $unused - formerly $showfeedback, is no longer used.
 * @return bool success
 */
function groups_delete_group_members($courseid, $userid=0, $unused=false) {
    global $DB, $OUTPUT;

    // Get the users in the course which are in a group.
    $sql = "SELECT gm.id as gmid, gm.userid, g.*
              FROM {groups_members} gm
        INNER JOIN {groups} g
                ON gm.groupid = g.id
             WHERE g.courseid = :courseid";
    $params = array();
    $params['courseid'] = $courseid;
    // Check if we want to delete a specific user.
    if ($userid) {
        $sql .= " AND gm.userid = :userid";
        $params['userid'] = $userid;
    }
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $usergroup) {
        groups_remove_member($usergroup, $usergroup->userid);
    }
    $rs->close();

    // TODO MDL-41312 Remove events_trigger_legacy('groups_members_removed').
    // This event is kept here for backwards compatibility, because it cannot be
    // translated to a new event as it is wrong.
    $eventdata = new stdClass();
    $eventdata->courseid = $courseid;
    $eventdata->userid   = $userid;
    events_trigger_legacy('groups_members_removed', $eventdata);

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
    $results = $DB->get_recordset_select('groupings_groups', "groupid IN ($groupssql)",
        array($courseid), '', 'groupid, groupingid');

    foreach ($results as $result) {
        groups_unassign_grouping($result->groupingid, $result->groupid, false);
    }

    // Invalidate the grouping cache for the course
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));
    // Purge the group and grouping cache for users.
    cache_helper::purge_by_definition('core', 'user_group_groupings');

    // TODO MDL-41312 Remove events_trigger_legacy('groups_groupings_groups_removed').
    // This event is kept here for backwards compatibility, because it cannot be
    // translated to a new event as it is wrong.
    events_trigger_legacy('groups_groupings_groups_removed', $courseid);

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

    $groups = $DB->get_recordset('groups', array('courseid' => $courseid));
    foreach ($groups as $group) {
        groups_delete_group($group);
    }

    // Invalidate the grouping cache for the course
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));
    // Purge the group and grouping cache for users.
    cache_helper::purge_by_definition('core', 'user_group_groupings');

    // TODO MDL-41312 Remove events_trigger_legacy('groups_groups_deleted').
    // This event is kept here for backwards compatibility, because it cannot be
    // translated to a new event as it is wrong.
    events_trigger_legacy('groups_groups_deleted', $courseid);

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

    $groupings = $DB->get_recordset_select('groupings', 'courseid = ?', array($courseid));
    foreach ($groupings as $grouping) {
        groups_delete_grouping($grouping);
    }

    // Invalidate the grouping cache for the course.
    cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));
    // Purge the group and grouping cache for users.
    cache_helper::purge_by_definition('core', 'user_group_groupings');

    // TODO MDL-41312 Remove events_trigger_legacy('groups_groupings_deleted').
    // This event is kept here for backwards compatibility, because it cannot be
    // translated to a new event as it is wrong.
    events_trigger_legacy('groups_groupings_deleted', $courseid);

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
 * @param mixed $source restrict to cohort, grouping or group id
 * @param string $orderby The column to sort users by
 * @param int $notingroup restrict to users not in existing groups
 * @param bool $onlyactiveenrolments restrict to users who have an active enrolment in the course
 * @return array An array of the users
 */
function groups_get_potential_members($courseid, $roleid = null, $source = null,
                                      $orderby = 'lastname ASC, firstname ASC',
                                      $notingroup = null, $onlyactiveenrolments = false) {
    global $DB;

    $context = context_course::instance($courseid);

    list($esql, $params) = get_enrolled_sql($context, '', 0, $onlyactiveenrolments);

    $notingroupsql = "";
    if ($notingroup) {
        // We want to eliminate users that are already associated with a course group.
        $notingroupsql = "u.id NOT IN (SELECT userid
                                         FROM {groups_members}
                                        WHERE groupid IN (SELECT id
                                                            FROM {groups}
                                                           WHERE courseid = :courseid))";
        $params['courseid'] = $courseid;
    }

    if ($roleid) {
        // We are looking for all users with this role assigned in this context or higher.
        list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($context->get_parent_context_ids(true),
                                                                       SQL_PARAMS_NAMED,
                                                                       'relatedctx');

        $params = array_merge($params, $relatedctxparams, array('roleid' => $roleid));
        $where = "WHERE u.id IN (SELECT userid
                                   FROM {role_assignments}
                                  WHERE roleid = :roleid AND contextid $relatedctxsql)";
        $where .= $notingroup ? "AND $notingroupsql" : "";
    } else if ($notingroup) {
        $where = "WHERE $notingroupsql";
    } else {
        $where = "";
    }

    $sourcejoin = "";
    if (is_int($source)) {
        $sourcejoin .= "JOIN {cohort_members} cm ON (cm.userid = u.id AND cm.cohortid = :cohortid) ";
        $params['cohortid'] = $source;
    } else {
        // Auto-create groups from an existing cohort membership.
        if (isset($source['cohortid'])) {
            $sourcejoin .= "JOIN {cohort_members} cm ON (cm.userid = u.id AND cm.cohortid = :cohortid) ";
            $params['cohortid'] = $source['cohortid'];
        }
        // Auto-create groups from an existing group membership.
        if (isset($source['groupid'])) {
            $sourcejoin .= "JOIN {groups_members} gp ON (gp.userid = u.id AND gp.groupid = :groupid) ";
            $params['groupid'] = $source['groupid'];
        }
        // Auto-create groups from an existing grouping membership.
        if (isset($source['groupingid'])) {
            $sourcejoin .= "JOIN {groupings_groups} gg ON gg.groupingid = :groupingid ";
            $sourcejoin .= "JOIN {groups_members} gm ON (gm.userid = u.id AND gm.groupid = gg.groupid) ";
            $params['groupingid'] = $source['groupingid'];
        }
    }

    $allusernamefields = get_all_user_name_fields(true, 'u');
    $sql = "SELECT DISTINCT u.id, u.username, $allusernamefields, u.idnumber
              FROM {user} u
              JOIN ($esql) e ON e.id = u.id
       $sourcejoin
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
 * @param int $timeadded  The time the group was added to the grouping.
 * @param bool $invalidatecache If set to true the course group cache and the user group cache will be invalidated as well.
 * @return bool true or exception
 */
function groups_assign_grouping($groupingid, $groupid, $timeadded = null, $invalidatecache = true) {
    global $DB;

    if ($DB->record_exists('groupings_groups', array('groupingid'=>$groupingid, 'groupid'=>$groupid))) {
        return true;
    }
    $assign = new stdClass();
    $assign->groupingid = $groupingid;
    $assign->groupid    = $groupid;
    if ($timeadded != null) {
        $assign->timeadded = (integer)$timeadded;
    } else {
        $assign->timeadded = time();
    }
    $DB->insert_record('groupings_groups', $assign);

    $courseid = $DB->get_field('groupings', 'courseid', array('id' => $groupingid));
    if ($invalidatecache) {
        // Invalidate the grouping cache for the course
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));
        // Purge the group and grouping cache for users.
        cache_helper::purge_by_definition('core', 'user_group_groupings');
    }

    // Trigger event.
    $params = array(
        'context' => context_course::instance($courseid),
        'objectid' => $groupingid,
        'other' => array('groupid' => $groupid)
    );
    $event = \core\event\grouping_group_assigned::create($params);
    $event->trigger();

    return true;
}

/**
 * Unassigns group from grouping
 *
 * @param int groupingid
 * @param int groupid
 * @param bool $invalidatecache If set to true the course group cache and the user group cache will be invalidated as well.
 * @return bool success
 */
function groups_unassign_grouping($groupingid, $groupid, $invalidatecache = true) {
    global $DB;
    $DB->delete_records('groupings_groups', array('groupingid'=>$groupingid, 'groupid'=>$groupid));

    $courseid = $DB->get_field('groupings', 'courseid', array('id' => $groupingid));
    if ($invalidatecache) {
        // Invalidate the grouping cache for the course
        cache_helper::invalidate_by_definition('core', 'groupdata', array(), array($courseid));
        // Purge the group and grouping cache for users.
        cache_helper::purge_by_definition('core', 'user_group_groupings');
    }

    // Trigger event.
    $params = array(
        'context' => context_course::instance($courseid),
        'objectid' => $groupingid,
        'other' => array('groupid' => $groupid)
    );
    $event = \core\event\grouping_group_unassigned::create($params);
    $event->trigger();

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
    global $DB;

    // Retrieve information about all users and their roles on the course or
    // parent ('related') contexts
    $context = context_course::instance($courseid);

    // We are looking for all users with this role assigned in this context or higher.
    list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'relatedctx');

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
         LEFT JOIN {role_assignments} ra ON (ra.userid = u.id AND ra.contextid $relatedctxsql)
         LEFT JOIN {role} r ON r.id = ra.roleid
             WHERE gm.groupid=:mgroupid
                   ".$extrawheretest."
          ORDER BY r.sortorder, $sort";
    $whereorsortparams = array_merge($whereorsortparams, $relatedctxparams, array('mgroupid' => $groupid));
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
            $users[$rec->userid]->roles[$rec->roleid] = $roles[$rec->roleid];
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
            $userrole = reset($userdata->roles);
            $roleid = $userrole->id;
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

/**
 * Synchronises enrolments with the group membership
 *
 * Designed for enrolment methods provide automatic synchronisation between enrolled users
 * and group membership, such as enrol_cohort and enrol_meta .
 *
 * @param string $enrolname name of enrolment method without prefix
 * @param int $courseid course id where sync needs to be performed (0 for all courses)
 * @param string $gidfield name of the field in 'enrol' table that stores group id
 * @return array Returns the list of removed and added users. Each record contains fields:
 *                  userid, enrolid, courseid, groupid, groupname
 */
function groups_sync_with_enrolment($enrolname, $courseid = 0, $gidfield = 'customint2') {
    global $DB;
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    $params = array(
        'enrolname' => $enrolname,
        'component' => 'enrol_'.$enrolname,
        'courseid' => $courseid
    );

    $affectedusers = array(
        'removed' => array(),
        'added' => array()
    );

    // Remove invalid.
    $sql = "SELECT ue.userid, ue.enrolid, e.courseid, g.id AS groupid, g.name AS groupname
              FROM {groups_members} gm
              JOIN {groups} g ON (g.id = gm.groupid)
              JOIN {enrol} e ON (e.enrol = :enrolname AND e.courseid = g.courseid $onecourse)
              JOIN {user_enrolments} ue ON (ue.userid = gm.userid AND ue.enrolid = e.id)
             WHERE gm.component=:component AND gm.itemid = e.id AND g.id <> e.{$gidfield}";

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $gm) {
        groups_remove_member($gm->groupid, $gm->userid);
        $affectedusers['removed'][] = $gm;
    }
    $rs->close();

    // Add missing.
    $sql = "SELECT ue.userid, ue.enrolid, e.courseid, g.id AS groupid, g.name AS groupname
              FROM {user_enrolments} ue
              JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = :enrolname $onecourse)
              JOIN {groups} g ON (g.courseid = e.courseid AND g.id = e.{$gidfield})
              JOIN {user} u ON (u.id = ue.userid AND u.deleted = 0)
         LEFT JOIN {groups_members} gm ON (gm.groupid = g.id AND gm.userid = ue.userid)
             WHERE gm.id IS NULL";

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $ue) {
        groups_add_member($ue->groupid, $ue->userid, 'enrol_'.$enrolname, $ue->enrolid);
        $affectedusers['added'][] = $ue;
    }
    $rs->close();

    return $affectedusers;
}
