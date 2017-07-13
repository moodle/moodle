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
 * Local stuff for cohort enrolment plugin.
 *
 * @package    enrol_cohort
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/enrol/locallib.php');
require_once($CFG->dirroot . '/cohort/lib.php');


/**
 * Event handler for cohort enrolment plugin.
 *
 * We try to keep everything in sync via listening to events,
 * it may fail sometimes, so we always do a full sync in cron too.
 */
class enrol_cohort_handler {
    /**
     * Event processor - cohort member added.
     * @param \core\event\cohort_member_added $event
     * @return bool
     */
    public static function member_added(\core\event\cohort_member_added $event) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/group/lib.php");

        if (!enrol_is_enabled('cohort')) {
            return true;
        }

        // Does any enabled cohort instance want to sync with this cohort?
        $sql = "SELECT e.*, r.id as roleexists
                  FROM {enrol} e
             LEFT JOIN {role} r ON (r.id = e.roleid)
                 WHERE e.customint1 = :cohortid AND e.enrol = 'cohort' AND e.status = :enrolstatus
              ORDER BY e.id ASC";
        $params['cohortid'] = $event->objectid;
        $params['enrolstatus'] = ENROL_INSTANCE_ENABLED;
        if (!$instances = $DB->get_records_sql($sql, $params)) {
            return true;
        }

        $plugin = enrol_get_plugin('cohort');
        foreach ($instances as $instance) {
            if ($instance->status != ENROL_INSTANCE_ENABLED ) {
                // No roles for disabled instances.
                $instance->roleid = 0;
            } else if ($instance->roleid and !$instance->roleexists) {
                // Invalid role - let's just enrol, they will have to create new sync and delete this one.
                $instance->roleid = 0;
            }
            unset($instance->roleexists);
            // No problem if already enrolled.
            $plugin->enrol_user($instance, $event->relateduserid, $instance->roleid, 0, 0, ENROL_USER_ACTIVE);

            // Sync groups.
            if ($instance->customint2) {
                if (!groups_is_member($instance->customint2, $event->relateduserid)) {
                    if ($group = $DB->get_record('groups', array('id'=>$instance->customint2, 'courseid'=>$instance->courseid))) {
                        groups_add_member($group->id, $event->relateduserid, 'enrol_cohort', $instance->id);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Event processor - cohort member removed.
     * @param \core\event\cohort_member_removed $event
     * @return bool
     */
    public static function member_removed(\core\event\cohort_member_removed $event) {
        global $DB;

        // Does anything want to sync with this cohort?
        if (!$instances = $DB->get_records('enrol', array('customint1'=>$event->objectid, 'enrol'=>'cohort'), 'id ASC')) {
            return true;
        }

        $plugin = enrol_get_plugin('cohort');
        $unenrolaction = $plugin->get_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        foreach ($instances as $instance) {
            if (!$ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$event->relateduserid))) {
                continue;
            }
            if ($unenrolaction == ENROL_EXT_REMOVED_UNENROL) {
                $plugin->unenrol_user($instance, $event->relateduserid);

            } else {
                if ($ue->status != ENROL_USER_SUSPENDED) {
                    $plugin->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                    $context = context_course::instance($instance->courseid);
                    role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$context->id, 'component'=>'enrol_cohort', 'itemid'=>$instance->id));
                }
            }
        }

        return true;
    }

    /**
     * Event processor - cohort deleted.
     * @param \core\event\cohort_deleted $event
     * @return bool
     */
    public static function deleted(\core\event\cohort_deleted $event) {
        global $DB;

        // Does anything want to sync with this cohort?
        if (!$instances = $DB->get_records('enrol', array('customint1'=>$event->objectid, 'enrol'=>'cohort'), 'id ASC')) {
            return true;
        }

        $plugin = enrol_get_plugin('cohort');
        $unenrolaction = $plugin->get_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);

        foreach ($instances as $instance) {
            if ($unenrolaction == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
                $context = context_course::instance($instance->courseid);
                role_unassign_all(array('contextid'=>$context->id, 'component'=>'enrol_cohort', 'itemid'=>$instance->id));
                $plugin->update_status($instance, ENROL_INSTANCE_DISABLED);
            } else {
                $plugin->delete_instance($instance);
            }
        }

        return true;
    }
}


/**
 * Sync all cohort course links.
 * @param progress_trace $trace
 * @param int $courseid one course, empty mean all
 * @return int 0 means ok, 1 means error, 2 means plugin disabled
 */
function enrol_cohort_sync(progress_trace $trace, $courseid = NULL) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/group/lib.php");

    // Purge all roles if cohort sync disabled, those can be recreated later here by cron or CLI.
    if (!enrol_is_enabled('cohort')) {
        $trace->output('Cohort sync plugin is disabled, unassigning all plugin roles and stopping.');
        role_unassign_all(array('component'=>'enrol_cohort'));
        return 2;
    }

    // Unfortunately this may take a long time, this script can be interrupted without problems.
    core_php_time_limit::raise();
    raise_memory_limit(MEMORY_HUGE);

    $trace->output('Starting user enrolment synchronisation...');

    $allroles = get_all_roles();
    $instances = array(); //cache

    $plugin = enrol_get_plugin('cohort');
    $unenrolaction = $plugin->get_config('unenrolaction', ENROL_EXT_REMOVED_UNENROL);


    // Iterate through all not enrolled yet users.
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    $sql = "SELECT cm.userid, e.id AS enrolid, ue.status
              FROM {cohort_members} cm
              JOIN {enrol} e ON (e.customint1 = cm.cohortid AND e.enrol = 'cohort' AND e.status = :enrolstatus $onecourse)
              JOIN {user} u ON (u.id = cm.userid AND u.deleted = 0)
         LEFT JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = cm.userid)
             WHERE ue.id IS NULL OR ue.status = :suspended";
    $params = array();
    $params['courseid'] = $courseid;
    $params['suspended'] = ENROL_USER_SUSPENDED;
    $params['enrolstatus'] = ENROL_INSTANCE_ENABLED;
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $ue) {
        if (!isset($instances[$ue->enrolid])) {
            $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
        }
        $instance = $instances[$ue->enrolid];
        if ($ue->status == ENROL_USER_SUSPENDED) {
            $plugin->update_user_enrol($instance, $ue->userid, ENROL_USER_ACTIVE);
            $trace->output("unsuspending: $ue->userid ==> $instance->courseid via cohort $instance->customint1", 1);
        } else {
            $plugin->enrol_user($instance, $ue->userid);
            $trace->output("enrolling: $ue->userid ==> $instance->courseid via cohort $instance->customint1", 1);
        }
    }
    $rs->close();


    // Unenrol as necessary.
    $sql = "SELECT ue.*, e.courseid
              FROM {user_enrolments} ue
              JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'cohort' $onecourse)
         LEFT JOIN {cohort_members} cm ON (cm.cohortid = e.customint1 AND cm.userid = ue.userid)
             WHERE cm.id IS NULL";
    $rs = $DB->get_recordset_sql($sql, array('courseid'=>$courseid));
    foreach($rs as $ue) {
        if (!isset($instances[$ue->enrolid])) {
            $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
        }
        $instance = $instances[$ue->enrolid];
        if ($unenrolaction == ENROL_EXT_REMOVED_UNENROL) {
            // Remove enrolment together with group membership, grades, preferences, etc.
            $plugin->unenrol_user($instance, $ue->userid);
            $trace->output("unenrolling: $ue->userid ==> $instance->courseid via cohort $instance->customint1", 1);

        } else { // ENROL_EXT_REMOVED_SUSPENDNOROLES
            // Just disable and ignore any changes.
            if ($ue->status != ENROL_USER_SUSPENDED) {
                $plugin->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                $context = context_course::instance($instance->courseid);
                role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$context->id, 'component'=>'enrol_cohort', 'itemid'=>$instance->id));
                $trace->output("suspending and unsassigning all roles: $ue->userid ==> $instance->courseid", 1);
            }
        }
    }
    $rs->close();
    unset($instances);


    // Now assign all necessary roles to enrolled users - skip suspended instances and users.
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    $sql = "SELECT e.roleid, ue.userid, c.id AS contextid, e.id AS itemid, e.courseid
              FROM {user_enrolments} ue
              JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'cohort' AND e.status = :statusenabled $onecourse)
              JOIN {role} r ON (r.id = e.roleid)
              JOIN {context} c ON (c.instanceid = e.courseid AND c.contextlevel = :coursecontext)
              JOIN {user} u ON (u.id = ue.userid AND u.deleted = 0)
         LEFT JOIN {role_assignments} ra ON (ra.contextid = c.id AND ra.userid = ue.userid AND ra.itemid = e.id AND ra.component = 'enrol_cohort' AND e.roleid = ra.roleid)
             WHERE ue.status = :useractive AND ra.id IS NULL";
    $params = array();
    $params['statusenabled'] = ENROL_INSTANCE_ENABLED;
    $params['useractive'] = ENROL_USER_ACTIVE;
    $params['coursecontext'] = CONTEXT_COURSE;
    $params['courseid'] = $courseid;

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $ra) {
        role_assign($ra->roleid, $ra->userid, $ra->contextid, 'enrol_cohort', $ra->itemid);
        $trace->output("assigning role: $ra->userid ==> $ra->courseid as ".$allroles[$ra->roleid]->shortname, 1);
    }
    $rs->close();


    // Remove unwanted roles - sync role can not be changed, we only remove role when unenrolled.
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    $sql = "SELECT ra.roleid, ra.userid, ra.contextid, ra.itemid, e.courseid
              FROM {role_assignments} ra
              JOIN {context} c ON (c.id = ra.contextid AND c.contextlevel = :coursecontext)
              JOIN {enrol} e ON (e.id = ra.itemid AND e.enrol = 'cohort' $onecourse)
         LEFT JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = ra.userid AND ue.status = :useractive)
             WHERE ra.component = 'enrol_cohort' AND (ue.id IS NULL OR e.status <> :statusenabled)";
    $params = array();
    $params['statusenabled'] = ENROL_INSTANCE_ENABLED;
    $params['useractive'] = ENROL_USER_ACTIVE;
    $params['coursecontext'] = CONTEXT_COURSE;
    $params['courseid'] = $courseid;

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $ra) {
        role_unassign($ra->roleid, $ra->userid, $ra->contextid, 'enrol_cohort', $ra->itemid);
        $trace->output("unassigning role: $ra->userid ==> $ra->courseid as ".$allroles[$ra->roleid]->shortname, 1);
    }
    $rs->close();


    // Finally sync groups.
    $affectedusers = groups_sync_with_enrolment('cohort', $courseid);
    foreach ($affectedusers['removed'] as $gm) {
        $trace->output("removing user from group: $gm->userid ==> $gm->courseid - $gm->groupname", 1);
    }
    foreach ($affectedusers['added'] as $ue) {
        $trace->output("adding user to group: $ue->userid ==> $ue->courseid - $ue->groupname", 1);
    }

    $trace->output('...user enrolment synchronisation finished.');

    return 0;
}
