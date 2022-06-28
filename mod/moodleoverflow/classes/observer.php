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
 * Event observers used in moodleoverflow.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Event observer for mod_moodleoverflow.
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_moodleoverflow_observer {

    /**
     * Triggered via user_enrolment_deleted event.
     *
     * @param \core\event\user_enrolment_deleted $event
     */
    public static function user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
        global $DB;

        // Get user enrolment info from event.
        $cp = (object) $event->other['userenrolment'];

        // Check if the user was enrolled.
        if ($cp->lastenrol) {

            // Get the moodleoverflow instances from which the user was unenrolled from.
            $moodleoverflows = $DB->get_records('moodleoverflow', array('course' => $cp->courseid), '', 'id');

            // Do not continue if there are no connected moodleoverflow instances.
            if (!$moodleoverflows) {
                return;
            }

            // Get the sql parameters for the moodleoverflow instances and add the user ID.
            list($select, $params) = $DB->get_in_or_equal(array_keys($moodleoverflows), SQL_PARAMS_NAMED);
            $params['userid'] = $cp->userid;

            // Delete all records that are connected to those moodleoverflow instances.
            $DB->delete_records_select('moodleoverflow_subscriptions', 'userid = :userid AND moodleoverflow ' . $select, $params);
            $DB->delete_records_select('moodleoverflow_read', 'userid = :userid AND moodleoverflowid ' . $select, $params);
        }
    }

    /**
     * Observer for role_assigned event.
     *
     * @param \core\event\role_assigned $event
     *
     * @return void
     */
    public static function role_assigned(\core\event\role_assigned $event) {
        global $CFG, $DB;

        // Get the context level.
        $context = context::instance_by_id($event->contextid, MUST_EXIST);

        // Check whether the context level is at course level.
        // Only at this level the user is enrolled in the course and can subscribe.
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        // Require the libriary of the plugin. It is needed for the variable.
        require_once($CFG->dirroot . '/mod/moodleoverflow/locallib.php');

        // Get the related user.
        $userid = $event->relateduserid;

        // Retrieve all moodleoverflows in this course.
        $sql             = "SELECT m.id, m.course as course, cm.id AS cmid, m.forcesubscribe
                  FROM {moodleoverflow} m
                  JOIN {course_modules} cm ON (cm.instance = m.id)
                  JOIN {modules} mo ON (mo.id = cm.module)
             LEFT JOIN {moodleoverflow_subscriptions} ms ON (ms.moodleoverflow = m.id AND ms.userid = :userid)
                 WHERE m.course = :courseid AND m.forcesubscribe = :initial AND mo.name = 'moodleoverflow' AND ms.id IS NULL";
        $params          = array('courseid' => $context->instanceid,
                                 'userid' => $userid,
                                 'initial' => MOODLEOVERFLOW_INITIALSUBSCRIBE);
        $moodleoverflows = $DB->get_records_sql($sql, $params);

        // Loop through all moodleoverflows.
        foreach ($moodleoverflows as $moodleoverflow) {

            // If user doesn't have allowforcesubscribe capability then don't subscribe.

            // Retrieve the context of the module.
            $modulecontext = context_module::instance($moodleoverflow->cmid);

            // Check if the user is allowed to be forced to be subscribed.
            $allowforce = has_capability('mod/moodleoverflow:allowforcesubscribe', $modulecontext, $userid);

            // If the user has the right to be forced to be subscribed, subscribe the user.
            if ($allowforce) {
                \mod_moodleoverflow\subscriptions::subscribe_user($userid, $moodleoverflow, $modulecontext);
            }
        }
    }

    /**
     * Observer for \core\event\course_module_created event.
     *
     * @param \core\event\course_module_created $event
     *
     * @return void
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        global $DB, $CFG;

        // Check if a moodleoverflow instance was created.
        if ($event->other['modulename'] === 'moodleoverflow') {

            // Require the library.
            require_once($CFG->dirroot . '/mod/moodleoverflow/lib.php');

            // Create a snapshot of the created moodleoverflow record.
            $moodleoverflow = $DB->get_record('moodleoverflow', array('id' => $event->other['instanceid']));

            // Trigger the function for a created moodleoverflow instance.
            moodleoverflow_instance_created($event->get_context(), $moodleoverflow);
        }
    }
}
