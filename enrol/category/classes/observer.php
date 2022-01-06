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
class enrol_category_observer {
    /**
     * Triggered when user is assigned a new role.
     *
     * @param \core\event\role_assigned $event
     */
    public static function role_assigned(\core\event\role_assigned $event) {
        global $DB;

        if (!enrol_is_enabled('category')) {
            return;
        }

        $ra = new stdClass();
        $ra->roleid = $event->objectid;
        $ra->userid = $event->relateduserid;
        $ra->contextid = $event->contextid;

        //only category level roles are interesting
        $parentcontext = context::instance_by_id($ra->contextid);
        if ($parentcontext->contextlevel != CONTEXT_COURSECAT) {
            return;
        }

        // Make sure the role is to be actually synchronised,
        // please note we are ignoring overrides of the synchronised capability (for performance reasons in full sync).
        $syscontext = context_system::instance();
        if (!$DB->record_exists('role_capabilities', array('contextid'=>$syscontext->id, 'roleid'=>$ra->roleid, 'capability'=>'enrol/category:synchronised', 'permission'=>CAP_ALLOW))) {
            return;
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
            $plugin->enrol_user($instance, $ra->userid, null, time());
        }
        $rs->close();
    }

    /**
     * Triggered when user role is unassigned.
     *
     * @param \core\event\role_unassigned $event
     */
    public static function role_unassigned(\core\event\role_unassigned $event) {
        global $DB;

        if (!enrol_is_enabled('category')) {
            return;
        }

        $ra = new stdClass();
        $ra->userid = $event->relateduserid;
        $ra->contextid = $event->contextid;

        // only category level roles are interesting
        $parentcontext = context::instance_by_id($ra->contextid);
        if ($parentcontext->contextlevel != CONTEXT_COURSECAT) {
            return;
        }

        // Now this is going to be a bit slow, take all enrolments in child courses and verify each separately.
        $syscontext = context_system::instance();
        if (!$roles = get_roles_with_capability('enrol/category:synchronised', CAP_ALLOW, $syscontext)) {
            return;
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
            $contextids = $coursecontext->get_parent_context_ids();
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
    }
}
