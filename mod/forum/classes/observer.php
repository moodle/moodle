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
 * Event observers used in forum.
 *
 * @package    mod_forum
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer for mod_forum.
 */
class mod_forum_observer {

    /**
     * Triggered via user_enrolment_deleted event.
     *
     * @param \core\event\user_enrolment_deleted $event
     */
    public static function user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
        global $DB;

        // NOTE: this has to be as fast as possible.
        // Get user enrolment info from event.
        $cp = (object)$event->other['userenrolment'];
        if ($cp->lastenrol) {
            if (!$forums = $DB->get_records('forum', array('course' => $cp->courseid), '', 'id')) {
                return;
            }
            list($forumselect, $params) = $DB->get_in_or_equal(array_keys($forums), SQL_PARAMS_NAMED);
            $params['userid'] = $cp->userid;

            $DB->delete_records_select('forum_digests', 'userid = :userid AND forum '.$forumselect, $params);
            $DB->delete_records_select('forum_subscriptions', 'userid = :userid AND forum '.$forumselect, $params);
            $DB->delete_records_select('forum_track_prefs', 'userid = :userid AND forumid '.$forumselect, $params);
            $DB->delete_records_select('forum_read', 'userid = :userid AND forumid '.$forumselect, $params);
        }
    }

    /**
     * Observer for role_assigned event.
     *
     * @param \core\event\role_assigned $event
     * @return void
     */
    public static function role_assigned(\core\event\role_assigned $event) {
        global $CFG, $DB;

        $context = context::instance_by_id($event->contextid, MUST_EXIST);

        // If contextlevel is course then only subscribe user. Role assignment
        // at course level means user is enroled in course and can subscribe to forum.
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        // Forum lib required for the constant used below.
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $userid = $event->relateduserid;
        $sql = "SELECT f.id, f.course as course, cm.id AS cmid, f.forcesubscribe
                  FROM {forum} f
                  JOIN {course_modules} cm ON (cm.instance = f.id)
                  JOIN {modules} m ON (m.id = cm.module)
             LEFT JOIN {forum_subscriptions} fs ON (fs.forum = f.id AND fs.userid = :userid)
                 WHERE f.course = :courseid
                   AND f.forcesubscribe = :initial
                   AND m.name = 'forum'
                   AND fs.id IS NULL";
        $params = array('courseid' => $context->instanceid, 'userid' => $userid, 'initial' => FORUM_INITIALSUBSCRIBE);

        $forums = $DB->get_records_sql($sql, $params);
        foreach ($forums as $forum) {
            // If user doesn't have allowforcesubscribe capability then don't subscribe.
            $modcontext = context_module::instance($forum->cmid);
            if (has_capability('mod/forum:allowforcesubscribe', $modcontext, $userid)) {
                \mod_forum\subscriptions::subscribe_user($userid, $forum, $modcontext);
            }
        }
    }

    /**
     * Observer for \core\event\course_module_created event.
     *
     * @param \core\event\course_module_created $event
     * @return void
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        global $CFG;

        if ($event->other['modulename'] === 'forum') {
            // Include the forum library to make use of the forum_instance_created function.
            require_once($CFG->dirroot . '/mod/forum/lib.php');

            $forum = $event->get_record_snapshot('forum', $event->other['instanceid']);
            forum_instance_created($event->get_context(), $forum);
        }
    }

    /**
     * Observer for \core\event\course_created event.
     *
     * @param \core\event\course_created $event
     * @return void
     */
    public static function course_created(\core\event\course_created $event) {
        global $CFG;

        $course = $event->get_record_snapshot('course', $event->objectid);
        $format = course_get_format($course);
        $courseformat = $format->get_format();

        $forumformat = '';
        if ($format->supports_news() && !empty($course->newsitems)) {
            $forumformat = 'news';
        } else if ($courseformat === 'social') {
            $forumformat = 'social';
        }

        if ($forumformat) {
            require_once($CFG->dirroot . '/mod/forum/lib.php');

            // Auto-create the course forum if necessary.
            forum_get_course_forum($event->objectid, $forumformat);
        }
    }

    /**
     * Observer for \core\event\course_updated event.
     *
     * @param \core\event\course_updated $event
     * @return void
     */
    public static function course_updated(\core\event\course_updated $event) {
        global $CFG;

        $course = $event->get_record_snapshot('course', $event->objectid);
        $format = course_get_format($course);
        $courseformat = $format->get_format();

        $forumformat = '';
        if ($format->supports_news() && !empty($course->newsitems)) {
            $forumformat = 'news';
        } else if ($courseformat === 'social') {
            $forumformat = 'social';
        }

        if ($forumformat) {
            require_once($CFG->dirroot . '/mod/forum/lib.php');

            // Auto-create the course forum if necessary.
            forum_get_course_forum($event->objectid, $forumformat);
        }
    }
}
