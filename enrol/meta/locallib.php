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
 * Local stuff for meta course enrolment plugin.
 *
 * @package    enrol_meta
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Event handler for meta enrolment plugin.
 *
 * We try to keep everything in sync via listening to events,
 * it may fail sometimes, so we always do a full sync in cron too.
 */
class enrol_meta_handler {

    /**
     * Synchronise meta enrolments of this user in this course
     * @static
     * @param int $courseid
     * @param int $userid
     * @return void
     */
    protected static function sync_course_instances($courseid, $userid) {
        global $DB;

        static $preventrecursion = false;

        // does anything want to sync with this parent?
        if (!$enrols = $DB->get_records('enrol', array('customint1'=>$courseid, 'enrol'=>'meta'), 'id ASC')) {
            return;
        }

        if ($preventrecursion) {
            return;
        }

        $preventrecursion = true;

        try {
            foreach ($enrols as $enrol) {
                self::sync_with_parent_course($enrol, $userid);
            }
        } catch (Exception $e) {
            $preventrecursion = false;
            throw $e;
        }

        $preventrecursion = false;
    }

    /**
     * Synchronise user enrolments in given instance as fast as possible.
     *
     * All roles are removed if the meta plugin disabled.
     *
     * @static
     * @param stdClass $instance
     * @param int $userid
     * @return void
     */
    protected static function sync_with_parent_course(stdClass $instance, $userid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/group/lib.php');

        $plugin = enrol_get_plugin('meta');

        if ($instance->customint1 == $instance->courseid) {
            // can not sync with self!!!
            return;
        }

        $context = context_course::instance($instance->courseid);

        // list of enrolments in parent course (we ignore meta enrols in parents completely)
        list($enabled, $params) = $DB->get_in_or_equal(explode(',', $CFG->enrol_plugins_enabled), SQL_PARAMS_NAMED, 'e');
        $params['userid'] = $userid;
        $params['parentcourse'] = $instance->customint1;
        $sql = "SELECT ue.*, e.status AS enrolstatus
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol <> 'meta' AND e.courseid = :parentcourse AND e.enrol $enabled)
                 WHERE ue.userid = :userid";
        $parentues = $DB->get_records_sql($sql, $params);
        // current enrolments for this instance
        $ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid));

        // first deal with users that are not enrolled in parent
        if (empty($parentues)) {
            self::user_not_supposed_to_be_here($instance, $ue, $context, $plugin);
            return;
        }

        if (!$parentcontext = context_course::instance($instance->customint1, IGNORE_MISSING)) {
            // Weird, we should not get here.
            return;
        }

        $skiproles = $plugin->get_config('nosyncroleids', '');
        $skiproles = empty($skiproles) ? array() : explode(',', $skiproles);
        $syncall   = $plugin->get_config('syncall', 1);

        // roles in parent course (meta enrols must be ignored!)
        $parentroles = array();
        list($ignoreroles, $params) = $DB->get_in_or_equal($skiproles, SQL_PARAMS_NAMED, 'ri', false, -1);
        $params['contextid'] = $parentcontext->id;
        $params['userid'] = $userid;
        $select = "contextid = :contextid AND userid = :userid AND component <> 'enrol_meta' AND roleid $ignoreroles";
        foreach($DB->get_records_select('role_assignments', $select, $params) as $ra) {
            $parentroles[$ra->roleid] = $ra->roleid;
        }

        // roles from this instance
        $roles = array();
        $ras = $DB->get_records('role_assignments', array('contextid'=>$context->id, 'userid'=>$userid, 'component'=>'enrol_meta', 'itemid'=>$instance->id));
        foreach($ras as $ra) {
            $roles[$ra->roleid] = $ra->roleid;
        }
        unset($ras);

        // do we want users without roles?
        if (!$syncall and empty($parentroles)) {
            self::user_not_supposed_to_be_here($instance, $ue, $context, $plugin);
            return;
        }

        // Is parent enrol active? Find minimum timestart and maximum timeend of all active enrolments.
        $parentstatus = ENROL_USER_SUSPENDED;
        $parenttimeend = null;
        $parenttimestart = null;
        foreach ($parentues as $pue) {
            if ($pue->status == ENROL_USER_ACTIVE && $pue->enrolstatus == ENROL_INSTANCE_ENABLED) {
                $parentstatus = ENROL_USER_ACTIVE;
                if ($parenttimeend === null || $pue->timeend == 0 || ($parenttimeend && $parenttimeend < $pue->timeend)) {
                    $parenttimeend = $pue->timeend;
                }
                if ($parenttimestart === null || $parenttimestart > $pue->timestart) {
                    $parenttimestart = $pue->timestart;
                }
            }
        }

        // Enrol user if not enrolled yet or fix status/timestart/timeend. Use the minimum timestart and maximum timeend found above.
        if ($ue) {
            if ($parentstatus != $ue->status ||
                    ($parentstatus == ENROL_USER_ACTIVE && ($parenttimestart != $ue->timestart || $parenttimeend != $ue->timeend))) {
                $plugin->update_user_enrol($instance, $userid, $parentstatus, $parenttimestart, $parenttimeend);
                $ue->status = $parentstatus;
                $ue->timestart = $parenttimestart;
                $ue->timeend = $parenttimeend;
            }
        } else {
            $plugin->enrol_user($instance, $userid, NULL, (int)$parenttimestart, (int)$parenttimeend, $parentstatus);
            $ue = new stdClass();
            $ue->userid = $userid;
            $ue->enrolid = $instance->id;
            $ue->status = $parentstatus;
            if ($instance->customint2 && $group = $DB->get_record('groups', ['id' => $instance->customint2])) {
                groups_add_member($group, $userid, 'enrol_meta', $instance->id);
            }
        }

        $unenrolaction = $plugin->get_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);

        // Only active users in enabled instances are supposed to have roles (we can reassign the roles any time later).
        if ($ue->status != ENROL_USER_ACTIVE or $instance->status != ENROL_INSTANCE_ENABLED or
                ($parenttimeend and $parenttimeend < time()) or ($parenttimestart > time())) {
            if ($unenrolaction == ENROL_EXT_REMOVED_SUSPEND) {
                // Always keep the roles.
            } else if ($roles) {
                // This will only unassign roles that were assigned in this enrolment method, leaving all manual role assignments intact.
                role_unassign_all(array('userid'=>$userid, 'contextid'=>$context->id, 'component'=>'enrol_meta', 'itemid'=>$instance->id));
            }
            return;
        }

        // add new roles
        foreach ($parentroles as $rid) {
            if (!isset($roles[$rid])) {
                role_assign($rid, $userid, $context->id, 'enrol_meta', $instance->id);
            }
        }

        if ($unenrolaction == ENROL_EXT_REMOVED_SUSPEND) {
            // Always keep the roles.
            return;
        }

        // remove roles
        foreach ($roles as $rid) {
            if (!isset($parentroles[$rid])) {
                role_unassign($rid, $userid, $context->id, 'enrol_meta', $instance->id);
            }
        }
    }

    /**
     * Deal with users that are not supposed to be enrolled via this instance
     * @static
     * @param stdClass $instance
     * @param stdClass $ue
     * @param context_course $context
     * @param enrol_meta $plugin
     * @return void
     */
    protected static function user_not_supposed_to_be_here($instance, $ue, context_course $context, $plugin) {
        if (!$ue) {
            // Not enrolled yet - simple!
            return;
        }

        $userid = $ue->userid;
        $unenrolaction = $plugin->get_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);

        if ($unenrolaction == ENROL_EXT_REMOVED_UNENROL) {
            // Purges grades, group membership, preferences, etc. - admins were warned!
            $plugin->unenrol_user($instance, $userid);

        } else if ($unenrolaction == ENROL_EXT_REMOVED_SUSPEND) {
            if ($ue->status != ENROL_USER_SUSPENDED) {
                $plugin->update_user_enrol($instance, $userid, ENROL_USER_SUSPENDED);
            }

        } else if ($unenrolaction == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
            if ($ue->status != ENROL_USER_SUSPENDED) {
                $plugin->update_user_enrol($instance, $userid, ENROL_USER_SUSPENDED);
            }
            role_unassign_all(array('userid'=>$userid, 'contextid'=>$context->id, 'component'=>'enrol_meta', 'itemid'=>$instance->id));

        } else {
            debugging('Unknown unenrol action '.$unenrolaction);
        }
    }
}

/**
 * Sync all meta course links.
 *
 * @param int $courseid one course, empty mean all
 * @param bool $verbose verbose CLI output
 * @return int 0 means ok, 1 means error, 2 means plugin disabled
 */
function enrol_meta_sync($courseid = NULL, $verbose = false) {
    global $CFG, $DB;
    require_once("{$CFG->dirroot}/group/lib.php");

    // purge all roles if meta sync disabled, those can be recreated later here in cron
    if (!enrol_is_enabled('meta')) {
        if ($verbose) {
            mtrace('Meta sync plugin is disabled, unassigning all plugin roles and stopping.');
        }
        role_unassign_all(array('component'=>'enrol_meta'));
        return 2;
    }

    // unfortunately this may take a long time, execution can be interrupted safely
    core_php_time_limit::raise();
    raise_memory_limit(MEMORY_HUGE);

    if ($verbose) {
        mtrace('Starting user enrolment synchronisation...');
    }

    $instances = array(); // cache instances

    $meta = enrol_get_plugin('meta');

    $unenrolaction = $meta->get_config('unenrolaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);
    $skiproles     = $meta->get_config('nosyncroleids', '');
    $skiproles     = empty($skiproles) ? array() : explode(',', $skiproles);
    $syncall       = $meta->get_config('syncall', 1);

    $allroles = get_all_roles();


    // Iterate through all not enrolled yet users. For each active enrolment of each user find the minimum
    // enrolment startdate and maximum enrolment enddate.
    // This SQL relies on the fact that ENROL_USER_ACTIVE < ENROL_USER_SUSPENDED
    // and ENROL_INSTANCE_ENABLED < ENROL_INSTANCE_DISABLED. Condition "pue.status + pe.status = 0" means
    // that enrolment is active. When MIN(pue.status + pe.status)=0 it means there exists an active
    // enrolment.
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    list($enabled, $params) = $DB->get_in_or_equal(explode(',', $CFG->enrol_plugins_enabled), SQL_PARAMS_NAMED, 'e');
    $params['courseid'] = $courseid;
    $sql = "SELECT pue.userid, e.id AS enrolid, MIN(pue.status + pe.status) AS status,
                      MIN(CASE WHEN (pue.status + pe.status = 0) THEN pue.timestart ELSE 9999999999 END) AS timestart,
                      MAX(CASE WHEN (pue.status + pe.status = 0) THEN
                                (CASE WHEN pue.timeend = 0 THEN 9999999999 ELSE pue.timeend END)
                                ELSE 0 END) AS timeend
              FROM {user_enrolments} pue
              JOIN {enrol} pe ON (pe.id = pue.enrolid AND pe.enrol <> 'meta' AND pe.enrol $enabled)
              JOIN {enrol} e ON (e.customint1 = pe.courseid AND e.enrol = 'meta' AND e.status = :enrolstatus $onecourse)
              JOIN {user} u ON (u.id = pue.userid AND u.deleted = 0)
         LEFT JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = pue.userid)
             WHERE ue.id IS NULL
             GROUP BY pue.userid, e.id";
    $params['enrolstatus'] = ENROL_INSTANCE_ENABLED;

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $ue) {
        if (!isset($instances[$ue->enrolid])) {
            $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
        }
        $instance = $instances[$ue->enrolid];

        if (!$syncall) {
            // this may be slow if very many users are ignored in sync
            $parentcontext = context_course::instance($instance->customint1);
            list($ignoreroles, $params) = $DB->get_in_or_equal($skiproles, SQL_PARAMS_NAMED, 'ri', false, -1);
            $params['contextid'] = $parentcontext->id;
            $params['userid'] = $ue->userid;
            $select = "contextid = :contextid AND userid = :userid AND component <> 'enrol_meta' AND roleid $ignoreroles";
            if (!$DB->record_exists_select('role_assignments', $select, $params)) {
                // bad luck, this user does not have any role we want in parent course
                if ($verbose) {
                    mtrace("  skipping enrolling: $ue->userid ==> $instance->courseid (user without role)");
                }
                continue;
            }
        }

        // So now we have aggregated values that we will use for the meta enrolment status, timeend and timestart.
        // Again, we use the fact that active=0 and disabled/suspended=1. Only when MIN(pue.status + pe.status)=0 the enrolment is active:
        $ue->status = ($ue->status == ENROL_USER_ACTIVE + ENROL_INSTANCE_ENABLED) ? ENROL_USER_ACTIVE : ENROL_USER_SUSPENDED;
        // Timeend 9999999999 was used instead of 0 in the "MAX()" function:
        $ue->timeend = ($ue->timeend == 9999999999) ? 0 : (int)$ue->timeend;
        // Timestart 9999999999 is only possible when there are no active enrolments:
        $ue->timestart = ($ue->timestart == 9999999999) ? 0 : (int)$ue->timestart;

        $meta->enrol_user($instance, $ue->userid, null, $ue->timestart, $ue->timeend, $ue->status);
        if ($instance->customint2 && $group = $DB->get_record('groups', ['id' => $instance->customint2])) {
            groups_add_member($group, $ue->userid, 'enrol_meta', $instance->id);
        }
        if ($verbose) {
            mtrace("  enrolling: $ue->userid ==> $instance->courseid");
        }
    }
    $rs->close();


    // unenrol as necessary - ignore enabled flag, we want to get rid of existing enrols in any case
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    list($enabled, $params) = $DB->get_in_or_equal(explode(',', $CFG->enrol_plugins_enabled), SQL_PARAMS_NAMED, 'e');
    $params['courseid'] = $courseid;
    $sql = "SELECT ue.*
              FROM {user_enrolments} ue
              JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'meta' $onecourse)
         LEFT JOIN ({user_enrolments} xpue
                      JOIN {enrol} xpe ON (xpe.id = xpue.enrolid AND xpe.enrol <> 'meta' AND xpe.enrol $enabled)
                   ) ON (xpe.courseid = e.customint1 AND xpue.userid = ue.userid)
             WHERE xpue.userid IS NULL";
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $ue) {
        if (!isset($instances[$ue->enrolid])) {
            $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
        }
        $instance = $instances[$ue->enrolid];

        if ($unenrolaction == ENROL_EXT_REMOVED_UNENROL) {
            $meta->unenrol_user($instance, $ue->userid);
            if ($verbose) {
                mtrace("  unenrolling: $ue->userid ==> $instance->courseid");
            }

        } else if ($unenrolaction == ENROL_EXT_REMOVED_SUSPEND) {
            if ($ue->status != ENROL_USER_SUSPENDED) {
                $meta->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                if ($verbose) {
                    mtrace("  suspending: $ue->userid ==> $instance->courseid");
                }
            }

        } else if ($unenrolaction == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
            if ($ue->status != ENROL_USER_SUSPENDED) {
                $meta->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                $context = context_course::instance($instance->courseid);
                role_unassign_all(array('userid'=>$ue->userid, 'contextid'=>$context->id, 'component'=>'enrol_meta', 'itemid'=>$instance->id));
                if ($verbose) {
                    mtrace("  suspending and removing all roles: $ue->userid ==> $instance->courseid");
                }
            }
        }
    }
    $rs->close();


    // Update status - meta enrols are ignored to avoid recursion.
    // Note the trick here is that the active enrolment and instance constants have value 0.
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    list($enabled, $params) = $DB->get_in_or_equal(explode(',', $CFG->enrol_plugins_enabled), SQL_PARAMS_NAMED, 'e');
    $params['courseid'] = $courseid;
    // The query builds a a list of all the non-meta enrolments that are on courses (the children) that are linked to by a meta
    // enrolment, it then groups them by the course that linked to them (the parents).
    //
    // It will only return results where the there is a difference between the status of the parent and the lowest status
    // of the children (remember that 0 is active, any other status is some form of inactive), or the time the earliest non-zero
    // start time of a child is different to the parent, or the longest effective end date has changed.
    //
    // The last two case statements in the HAVING clause are designed to ignore any inactive child records when calculating
    // the start and end time.
    $sql = "SELECT ue.userid, ue.enrolid,
                   MIN(xpue.status + xpe.status) AS pstatus,
                   MIN(CASE WHEN (xpue.status + xpe.status = 0) THEN xpue.timestart ELSE 9999999999 END) AS ptimestart,
                   MAX(CASE WHEN (xpue.status + xpe.status = 0) THEN
                                 (CASE WHEN xpue.timeend = 0 THEN 9999999999 ELSE xpue.timeend END)
                            ELSE 0 END) AS ptimeend
              FROM {user_enrolments} ue
              JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'meta' $onecourse)
              JOIN {user_enrolments} xpue ON (xpue.userid = ue.userid)
              JOIN {enrol} xpe ON (xpe.id = xpue.enrolid AND xpe.enrol <> 'meta'
                   AND xpe.enrol $enabled AND xpe.courseid = e.customint1)
          GROUP BY ue.userid, ue.enrolid
            HAVING (MIN(xpue.status + xpe.status) = 0 AND MIN(ue.status) > 0)
                   OR (MIN(xpue.status + xpe.status) > 0 AND MIN(ue.status) = 0)
                   OR ((CASE WHEN
                                  MIN(CASE WHEN (xpue.status + xpe.status = 0) THEN xpue.timestart ELSE 9999999999 END) = 9999999999
                             THEN 0
                             ELSE
                                  MIN(CASE WHEN (xpue.status + xpe.status = 0) THEN xpue.timestart ELSE 9999999999 END)
                              END) <> MIN(ue.timestart))
                   OR ((CASE
                         WHEN MAX(CASE WHEN (xpue.status + xpe.status = 0)
                                       THEN (CASE WHEN xpue.timeend = 0 THEN 9999999999 ELSE xpue.timeend END)
                                       ELSE 0 END) = 9999999999
                         THEN 0 ELSE MAX(CASE WHEN (xpue.status + xpe.status = 0)
                                              THEN (CASE WHEN xpue.timeend = 0 THEN 9999999999 ELSE xpue.timeend END)
                                              ELSE 0 END)
                          END) <> MAX(ue.timeend))";
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $ue) {
        if (!isset($instances[$ue->enrolid])) {
            $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
        }
        $instance = $instances[$ue->enrolid];
        $ue->pstatus = ($ue->pstatus == ENROL_USER_ACTIVE + ENROL_INSTANCE_ENABLED) ? ENROL_USER_ACTIVE : ENROL_USER_SUSPENDED;
        $ue->ptimeend = ($ue->ptimeend == 9999999999) ? 0 : (int)$ue->ptimeend;
        $ue->ptimestart = ($ue->ptimestart == 9999999999) ? 0 : (int)$ue->ptimestart;

        if ($ue->pstatus == ENROL_USER_ACTIVE and (!$ue->ptimeend || $ue->ptimeend > time())
                and !$syncall and $unenrolaction != ENROL_EXT_REMOVED_UNENROL) {
            // this may be slow if very many users are ignored in sync
            $parentcontext = context_course::instance($instance->customint1);
            list($ignoreroles, $params) = $DB->get_in_or_equal($skiproles, SQL_PARAMS_NAMED, 'ri', false, -1);
            $params['contextid'] = $parentcontext->id;
            $params['userid'] = $ue->userid;
            $select = "contextid = :contextid AND userid = :userid AND component <> 'enrol_meta' AND roleid $ignoreroles";
            if (!$DB->record_exists_select('role_assignments', $select, $params)) {
                // bad luck, this user does not have any role we want in parent course
                if ($verbose) {
                    mtrace("  skipping unsuspending: $ue->userid ==> $instance->courseid (user without role)");
                }
                continue;
            }
        }

        $meta->update_user_enrol($instance, $ue->userid, $ue->pstatus, $ue->ptimestart, $ue->ptimeend);
        if ($verbose) {
            if ($ue->pstatus == ENROL_USER_ACTIVE) {
                mtrace("  unsuspending: $ue->userid ==> $instance->courseid");
            } else {
                mtrace("  suspending: $ue->userid ==> $instance->courseid");
            }
        }
    }
    $rs->close();


    // now assign all necessary roles
    $enabled = explode(',', $CFG->enrol_plugins_enabled);
    foreach($enabled as $k=>$v) {
        if ($v === 'meta') {
            continue; // no meta sync of meta roles
        }
        $enabled[$k] = 'enrol_'.$v;
    }
    $enabled[] = ''; // manual assignments are replicated too

    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    list($enabled, $params) = $DB->get_in_or_equal($enabled, SQL_PARAMS_NAMED, 'e');
    $params['coursecontext'] = CONTEXT_COURSE;
    $params['courseid'] = $courseid;
    $params['activeuser'] = ENROL_USER_ACTIVE;
    $params['enabledinstance'] = ENROL_INSTANCE_ENABLED;
    $sql = "SELECT DISTINCT pra.roleid, pra.userid, c.id AS contextid, e.id AS enrolid, e.courseid
              FROM {role_assignments} pra
              JOIN {user} u ON (u.id = pra.userid AND u.deleted = 0)
              JOIN {context} pc ON (pc.id = pra.contextid AND pc.contextlevel = :coursecontext AND pra.component $enabled)
              JOIN {enrol} e ON (e.customint1 = pc.instanceid AND e.enrol = 'meta' $onecourse AND e.status = :enabledinstance)
              JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = u.id AND ue.status = :activeuser)
              JOIN {context} c ON (c.contextlevel = pc.contextlevel AND c.instanceid = e.courseid)
         LEFT JOIN {role_assignments} ra ON (ra.contextid = c.id AND ra.userid = pra.userid AND ra.roleid = pra.roleid AND ra.itemid = e.id AND ra.component = 'enrol_meta')
             WHERE ra.id IS NULL";

    if ($ignored = $meta->get_config('nosyncroleids')) {
        list($notignored, $xparams) = $DB->get_in_or_equal(explode(',', $ignored), SQL_PARAMS_NAMED, 'ig', false);
        $params = array_merge($params, $xparams);
        $sql = "$sql AND pra.roleid $notignored";
    }

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $ra) {
        role_assign($ra->roleid, $ra->userid, $ra->contextid, 'enrol_meta', $ra->enrolid);
        if ($verbose) {
            mtrace("  assigning role: $ra->userid ==> $ra->courseid as ".$allroles[$ra->roleid]->shortname);
        }
    }
    $rs->close();


    // remove unwanted roles - include ignored roles and disabled plugins too
    $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
    $params = array();
    $params['coursecontext'] = CONTEXT_COURSE;
    $params['courseid'] = $courseid;
    $params['activeuser'] = ENROL_USER_ACTIVE;
    $params['enabledinstance'] = ENROL_INSTANCE_ENABLED;
    if ($ignored = $meta->get_config('nosyncroleids')) {
        list($notignored, $xparams) = $DB->get_in_or_equal(explode(',', $ignored), SQL_PARAMS_NAMED, 'ig', false);
        $params = array_merge($params, $xparams);
        $notignored = "AND pra.roleid $notignored";
    } else {
        $notignored = "";
    }

    $sql = "SELECT ra.roleid, ra.userid, ra.contextid, ra.itemid, e.courseid
              FROM {role_assignments} ra
              JOIN {enrol} e ON (e.id = ra.itemid AND ra.component = 'enrol_meta' AND e.enrol = 'meta' $onecourse)
              JOIN {context} pc ON (pc.instanceid = e.customint1 AND pc.contextlevel = :coursecontext)
         LEFT JOIN {role_assignments} pra ON (pra.contextid = pc.id AND pra.userid = ra.userid AND pra.roleid = ra.roleid AND pra.component <> 'enrol_meta' $notignored)
         LEFT JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = ra.userid AND ue.status = :activeuser)
             WHERE pra.id IS NULL OR ue.id IS NULL OR e.status <> :enabledinstance";

    if ($unenrolaction != ENROL_EXT_REMOVED_SUSPEND) {
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach($rs as $ra) {
            role_unassign($ra->roleid, $ra->userid, $ra->contextid, 'enrol_meta', $ra->itemid);
            if ($verbose) {
                mtrace("  unassigning role: $ra->userid ==> $ra->courseid as ".$allroles[$ra->roleid]->shortname);
            }
        }
        $rs->close();
    }


    // kick out or suspend users without synced roles if syncall disabled
    if (!$syncall) {
        if ($unenrolaction == ENROL_EXT_REMOVED_UNENROL) {
            $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
            $params = array();
            $params['coursecontext'] = CONTEXT_COURSE;
            $params['courseid'] = $courseid;
            $sql = "SELECT ue.userid, ue.enrolid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'meta' $onecourse)
                      JOIN {context} c ON (e.courseid = c.instanceid AND c.contextlevel = :coursecontext)
                 LEFT JOIN {role_assignments} ra ON (ra.contextid = c.id AND ra.itemid = e.id AND ra.userid = ue.userid)
                     WHERE ra.id IS NULL";
            $ues = $DB->get_recordset_sql($sql, $params);
            foreach($ues as $ue) {
                if (!isset($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];
                $meta->unenrol_user($instance, $ue->userid);
                if ($verbose) {
                    mtrace("  unenrolling: $ue->userid ==> $instance->courseid (user without role)");
                }
            }
            $ues->close();

        } else {
            // just suspend the users
            $onecourse = $courseid ? "AND e.courseid = :courseid" : "";
            $params = array();
            $params['coursecontext'] = CONTEXT_COURSE;
            $params['courseid'] = $courseid;
            $params['active'] = ENROL_USER_ACTIVE;
            $sql = "SELECT ue.userid, ue.enrolid
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'meta' $onecourse)
                      JOIN {context} c ON (e.courseid = c.instanceid AND c.contextlevel = :coursecontext)
                 LEFT JOIN {role_assignments} ra ON (ra.contextid = c.id AND ra.itemid = e.id AND ra.userid = ue.userid)
                     WHERE ra.id IS NULL AND ue.status = :active";
            $ues = $DB->get_recordset_sql($sql, $params);
            foreach($ues as $ue) {
                if (!isset($instances[$ue->enrolid])) {
                    $instances[$ue->enrolid] = $DB->get_record('enrol', array('id'=>$ue->enrolid));
                }
                $instance = $instances[$ue->enrolid];
                $meta->update_user_enrol($instance, $ue->userid, ENROL_USER_SUSPENDED);
                if ($verbose) {
                    mtrace("  suspending: $ue->userid ==> $instance->courseid (user without role)");
                }
            }
            $ues->close();
        }
    }

    // Finally sync groups.
    $affectedusers = groups_sync_with_enrolment('meta', $courseid);
    if ($verbose) {
        foreach ($affectedusers['removed'] as $gm) {
            mtrace("removing user from group: $gm->userid ==> $gm->courseid - $gm->groupname", 1);
        }
        foreach ($affectedusers['added'] as $ue) {
            mtrace("adding user to group: $ue->userid ==> $ue->courseid - $ue->groupname", 1);
        }
    }

    if ($verbose) {
        mtrace('...user enrolment synchronisation finished.');
    }

    return 0;
}

/**
 * Create a new group with the course's name.
 *
 * @param int $courseid
 * @param int $linkedcourseid
 * @return int $groupid Group ID for this cohort.
 */
function enrol_meta_create_new_group($courseid, $linkedcourseid) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/group/lib.php');

    $coursename = $DB->get_field('course', 'fullname', array('id' => $linkedcourseid), MUST_EXIST);
    $a = new stdClass();
    $a->name = $coursename;
    $a->increment = '';
    $inc = 1;
    $groupname = trim(get_string('defaultgroupnametext', 'enrol_meta', $a));
    // Check to see if the group name already exists in this course. Add an incremented number if it does.
    while ($DB->record_exists('groups', array('name' => $groupname, 'courseid' => $courseid))) {
        $a->increment = '(' . (++$inc) . ')';
        $groupname = trim(get_string('defaultgroupnametext', 'enrol_meta', $a));
    }
    // Create a new group for the course meta sync.
    $groupdata = new stdClass();
    $groupdata->courseid = $courseid;
    $groupdata->name = $groupname;
    $groupid = groups_create_group($groupdata);

    return $groupid;
}
