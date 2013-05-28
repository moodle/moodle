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
 * Local stuff for category enrolment plugin.
 *
 * @package    enrol_category
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Event handler for category enrolment plugin.
 *
 * We try to keep everything in sync via listening to events,
 * it may fail sometimes, so we always do a full sync in cron too.
 */
class enrol_category_handler {
    /**
     * Triggered when user is assigned a new role.
     * @static
     * @param stdClass $ra
     * @return bool
     */
    public static function role_assigned($ra) {
        global $DB;

        if (!enrol_is_enabled('category')) {
            return true;
        }

        //only category level roles are interesting
        $parentcontext = context::instance_by_id($ra->contextid);
        if ($parentcontext->contextlevel != CONTEXT_COURSECAT) {
            return true;
        }

        // Make sure the role is to be actually synchronised,
        // please note we are ignoring overrides of the synchronised capability (for performance reasons in full sync).
        $syscontext = context_system::instance();
        if (!$DB->record_exists('role_capabilities', array('contextid'=>$syscontext->id, 'roleid'=>$ra->roleid, 'capability'=>'enrol/category:synchronised', 'permission'=>CAP_ALLOW))) {
            return true;
        }

        // Add necessary enrol instances.
        $plugin = enrol_get_plugin('category');
        $sql = "SELECT c.*
                  FROM {course} c
                  JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :courselevel AND ctx.path LIKE :match)
             LEFT JOIN {enrol} e ON (e.courseid = c.id AND e.enrol = 'category')
                 WHERE e.id IS NULL";
        $params = array('courselevel'=>CONTEXT_COURSE, 'match'=>$parentcontext->path.'/%');
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $course) {
            $plugin->add_instance($course);
        }
        $rs->close();

        // Now look for missing enrolments.
        $sql = "SELECT e.*
                  FROM {course} c
                  JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :courselevel AND ctx.path LIKE :match)
                  JOIN {enrol} e ON (e.courseid = c.id AND e.enrol = 'category')
             LEFT JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                 WHERE ue.id IS NULL";
        $params = array('courselevel'=>CONTEXT_COURSE, 'match'=>$parentcontext->path.'/%', 'userid'=>$ra->userid);
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $instance) {
            $plugin->enrol_user($instance, $ra->userid, null, $ra->timemodified);
        }
        $rs->close();

        return true;
    }

    /**
     * Triggered when user role is unassigned.
     * @static
     * @param stdClass $ra
     * @return bool
     */
    public static function role_unassigned($ra) {
        global $DB;

        if (!enrol_is_enabled('category')) {
            return true;
        }

        // only category level roles are interesting
        $parentcontext = context::instance_by_id($ra->contextid);
        if ($parentcontext->contextlevel != CONTEXT_COURSECAT) {
            return true;
        }

        // Now this is going to be a bit slow, take all enrolments in child courses and verify each separately.
        $syscontext = context_system::instance();
        if (!$roles = get_roles_with_capability('enrol/category:synchronised', CAP_ALLOW, $syscontext)) {
            return true;
        }

        $plugin = enrol_get_plugin('category');

        $sql = "SELECT e.*
                  FROM {course} c
                  JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :courselevel AND ctx.path LIKE :match)
                  JOIN {enrol} e ON (e.courseid = c.id AND e.enrol = 'category')
                  JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)";
        $params = array('courselevel'=>CONTEXT_COURSE, 'match'=>$parentcontext->path.'/%', 'userid'=>$ra->userid);
        $rs = $DB->get_recordset_sql($sql, $params);

        list($roleids, $params) = $DB->get_in_or_equal(array_keys($roles), SQL_PARAMS_NAMED, 'r');
        $params['userid'] = $ra->userid;

        foreach ($rs as $instance) {
            $coursecontext = context_course::instance($instance->courseid);
            $contextids = get_parent_contexts($coursecontext);
            array_pop($contextids); // Remove system context, we are interested in categories only.

            list($contextids, $contextparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED, 'c');
            $params = array_merge($params, $contextparams);

            $sql = "SELECT ra.id
                      FROM {role_assignments} ra
                     WHERE ra.userid = :userid AND ra.contextid $contextids AND ra.roleid $roleids";
            if (!$DB->record_exists_sql($sql, $params)) {
                // User does not have any interesting role in any parent context, let's unenrol.
                $plugin->unenrol_user($instance, $ra->userid);
            }
        }
        $rs->close();

        return true;
    }
}

/**
 * Sync all category enrolments in one course
 * @param stdClass $course
 * @return void
 */
function enrol_category_sync_course($course) {
    global $DB;

    if (!enrol_is_enabled('category')) {
        return;
    }

    $plugin = enrol_get_plugin('category');

    $syscontext = context_system::instance();
    $roles = get_roles_with_capability('enrol/category:synchronised', CAP_ALLOW, $syscontext);

    if (!$roles) {
        // Nothing to sync, so remove the instance completely if exists.
        if ($instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'category'))) {
            foreach ($instances as $instance) {
                $plugin->delete_instance($instance);
            }
        }
        return;
    }

    // First find out if any parent category context contains interesting role assignments.
    $coursecontext = context_course::instance($course->id);
    $contextids = get_parent_contexts($coursecontext);
    array_pop($contextids); // Remove system context, we are interested in categories only.

    list($roleids, $params) = $DB->get_in_or_equal(array_keys($roles), SQL_PARAMS_NAMED, 'r');
    list($contextids, $contextparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED, 'c');
    $params = array_merge($params, $contextparams);
    $params['courseid'] = $course->id;

    $sql = "SELECT 'x'
              FROM {role_assignments}
             WHERE roleid $roleids AND contextid $contextids";
    if (!$DB->record_exists_sql($sql, $params)) {
        if ($instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'category'))) {
            // Should be max one instance, but anyway.
            foreach ($instances as $instance) {
                $plugin->delete_instance($instance);
            }
        }
        return;
    }

    // Make sure the enrol instance exists - there should be always only one instance.
    $delinstances = array();
    if ($instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'category'))) {
        $instance = array_shift($instances);
        $delinstances = $instances;
    } else {
        $i = $plugin->add_instance($course);
        $instance = $DB->get_record('enrol', array('id'=>$i));
    }

    // Add new enrolments.
    $sql = "SELECT ra.userid, ra.estart
              FROM (SELECT xra.userid, MIN(xra.timemodified) AS estart
                      FROM {role_assignments} xra
                      JOIN {user} xu ON (xu.id = xra.userid AND xu.deleted = 0)
                     WHERE xra.roleid $roleids AND xra.contextid $contextids
                  GROUP BY xra.userid
                   ) ra
         LEFT JOIN {user_enrolments} ue ON (ue.enrolid = :instanceid AND ue.userid = ra.userid)
             WHERE ue.id IS NULL";
    $params['instanceid'] = $instance->id;
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $ra) {
        $plugin->enrol_user($instance, $ra->userid, null, $ra->estart);
    }
    $rs->close();

    // Remove unwanted enrolments.
    $sql = "SELECT DISTINCT ue.userid
              FROM {user_enrolments} ue
         LEFT JOIN {role_assignments} ra ON (ra.roleid $roleids AND ra.contextid $contextids AND ra.userid = ue.userid)
             WHERE ue.enrolid = :instanceid AND ra.id IS NULL";
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $ra) {
        $plugin->unenrol_user($instance, $ra->userid);
    }
    $rs->close();

    if ($delinstances) {
        // We have to do this as the last step in order to prevent temporary unenrolment.
        foreach ($delinstances as $delinstance) {
            $plugin->delete_instance($delinstance);
        }
    }
}

/**
 * Synchronise courses in all categories.
 *
 * It gets out-of-sync if:
 * - you move course to different category
 * - reorder categories
 * - disable enrol_category and enable it again
 *
 * @param progress_trace $trace
 * @return int exit code - 0 is ok, 1 means error, 2 if plugin disabled
 */
function enrol_category_sync_full(progress_trace $trace) {
    global $DB;


    if (!enrol_is_enabled('category')) {
        $trace->finished();
        return 2;
    }

    // We may need a lot of time here.
    @set_time_limit(0);

    $plugin = enrol_get_plugin('category');

    $syscontext = context_system::instance();

    // Any interesting roles worth synchronising?
    if (!$roles = get_roles_with_capability('enrol/category:synchronised', CAP_ALLOW, $syscontext)) {
        // yay, nothing to do, so let's remove all leftovers
        $trace->output("No roles with 'enrol/category:synchronised' capability found.");
        if ($instances = $DB->get_records('enrol', array('enrol'=>'category'))) {
            $trace->output("Deleting all category enrol instances...");
            foreach ($instances as $instance) {
                $trace->output("deleting category enrol instance from course {$instance->courseid}", 1);
                $plugin->delete_instance($instance);
            }
            $trace->output("...all instances deleted.");
        }
        $trace->finished();
        return 0;
    }
    $rolenames = role_fix_names($roles, null, ROLENAME_SHORT, true);
    $trace->output('Synchronising category enrolments for roles: '.implode(', ', $rolenames).'...');

    list($roleids, $params) = $DB->get_in_or_equal(array_keys($roles), SQL_PARAMS_NAMED, 'r');
    $params['courselevel'] = CONTEXT_COURSE;
    $params['catlevel'] = CONTEXT_COURSECAT;

    // First of all add necessary enrol instances to all courses.
    $parentcat = $DB->sql_concat("cat.path", "'/%'");
    $parentcctx = $DB->sql_concat("cctx.path", "'/%'");
    // Need whole course records to be used by add_instance(), use inner view (ci) to
    // get distinct records only.
    // TODO: Moodle 2.1. Improve enrol API to accept courseid / courserec
    $sql = "SELECT c.*
              FROM {course} c
              JOIN (
                SELECT DISTINCT c.id
                  FROM {course} c
                  JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :courselevel)
                  JOIN (SELECT DISTINCT cctx.path
                          FROM {course_categories} cc
                          JOIN {context} cctx ON (cctx.instanceid = cc.id AND cctx.contextlevel = :catlevel)
                          JOIN {role_assignments} ra ON (ra.contextid = cctx.id AND ra.roleid $roleids)
                       ) cat ON (ctx.path LIKE $parentcat)
             LEFT JOIN {enrol} e ON (e.courseid = c.id AND e.enrol = 'category')
                 WHERE e.id IS NULL) ci ON (c.id = ci.id)";

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $course) {
        $plugin->add_instance($course);
    }
    $rs->close();

    // Now look for courses that do not have any interesting roles in parent contexts,
    // but still have the instance and delete them.
    $sql = "SELECT e.*
              FROM {enrol} e
              JOIN {context} ctx ON (ctx.instanceid = e.courseid AND ctx.contextlevel = :courselevel)
         LEFT JOIN ({course_categories} cc
                      JOIN {context} cctx ON (cctx.instanceid = cc.id AND cctx.contextlevel = :catlevel)
                      JOIN {role_assignments} ra ON (ra.contextid = cctx.id AND ra.roleid $roleids)
                   ) ON (ctx.path LIKE $parentcctx)
             WHERE e.enrol = 'category' AND cc.id IS NULL";

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $instance) {
        $plugin->delete_instance($instance);
    }
    $rs->close();

    // Add missing enrolments.
    $sql = "SELECT e.*, cat.userid, cat.estart
              FROM {enrol} e
              JOIN {context} ctx ON (ctx.instanceid = e.courseid AND ctx.contextlevel = :courselevel)
              JOIN (SELECT cctx.path, ra.userid, MIN(ra.timemodified) AS estart
                      FROM {course_categories} cc
                      JOIN {context} cctx ON (cctx.instanceid = cc.id AND cctx.contextlevel = :catlevel)
                      JOIN {role_assignments} ra ON (ra.contextid = cctx.id AND ra.roleid $roleids)
                  GROUP BY cctx.path, ra.userid
                   ) cat ON (ctx.path LIKE $parentcat)
         LEFT JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = cat.userid)
             WHERE e.enrol = 'category' AND ue.id IS NULL";
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $instance) {
        $userid = $instance->userid;
        $estart = $instance->estart;
        unset($instance->userid);
        unset($instance->estart);
        $plugin->enrol_user($instance, $userid, null, $estart);
        $trace->output("enrolling: user $userid ==> course $instance->courseid", 1);
    }
    $rs->close();

    // Remove stale enrolments.
    $sql = "SELECT e.*, ue.userid
              FROM {enrol} e
              JOIN {context} ctx ON (ctx.instanceid = e.courseid AND ctx.contextlevel = :courselevel)
              JOIN {user_enrolments} ue ON (ue.enrolid = e.id)
         LEFT JOIN ({course_categories} cc
                      JOIN {context} cctx ON (cctx.instanceid = cc.id AND cctx.contextlevel = :catlevel)
                      JOIN {role_assignments} ra ON (ra.contextid = cctx.id AND ra.roleid $roleids)
                   ) ON (ctx.path LIKE $parentcctx AND ra.userid = ue.userid)
             WHERE e.enrol = 'category' AND cc.id IS NULL";
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $instance) {
        $userid = $instance->userid;
        unset($instance->userid);
        $plugin->unenrol_user($instance, $userid);
        $trace->output("unenrolling: user $userid ==> course $instance->courseid", 1);
    }
    $rs->close();

    $trace->output('...user enrolment synchronisation finished.');
    $trace->finished();

    return 0;
}
