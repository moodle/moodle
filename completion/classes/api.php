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
 * Contains class containing completion API.
 *
 * @package    core_completion
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_completion;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing completion API.
 *
 * @package    core_completion
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * @var string The completion expected on event.
     */
    const COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED = 'expectcompletionon';

    /**
     * Creates, updates or deletes an event for the expected completion date.
     *
     * @param int $cmid The course module id
     * @param string $modulename The name of the module (eg. assign, quiz)
     * @param \stdClass|int $instanceorid The instance object or ID.
     * @param int|null $completionexpectedtime The time completion is expected, null if not set
     * @return bool
     */
    public static function update_completion_date_event($cmid, $modulename, $instanceorid, $completionexpectedtime) {
        global $CFG, $DB;

        // Required for calendar constant CALENDAR_EVENT_TYPE_ACTION.
        require_once($CFG->dirroot . '/calendar/lib.php');

        $instance = null;
        if (is_object($instanceorid)) {
            $instance = $instanceorid;
        } else {
            $instance = $DB->get_record($modulename, array('id' => $instanceorid), '*', IGNORE_MISSING);
        }
        if (!$instance) {
            return false;
        }
        $course = get_course($instance->course);

        $completion = new \completion_info($course);

        // Can not create/update an event if completion is disabled.
        if (!$completion->is_enabled() && $completionexpectedtime !== null) {
            return true;
        }

        // Create the \stdClass we will be using for our language strings.
        $lang = new \stdClass();
        $lang->modulename = get_string('pluginname', $modulename);
        $lang->instancename = $instance->name;

        // Create the calendar event.
        $event = new \stdClass();
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = self::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED;
        if ($event->id = $DB->get_field('event', 'id', array('modulename' => $modulename,
                'instance' => $instance->id, 'eventtype' => $event->eventtype))) {
            if ($completionexpectedtime !== null) {
                // Calendar event exists so update it.
                $event->name = get_string('completionexpectedfor', 'completion', $lang);
                $event->description = format_module_intro($modulename, $instance, $cmid, false);
                $event->format = FORMAT_HTML;
                $event->timestart = $completionexpectedtime;
                $event->timesort = $completionexpectedtime;
                $event->visible = instance_is_visible($modulename, $instance);
                $event->timeduration = 0;

                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->update($event, false);
            } else {
                // Calendar event is no longer needed.
                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->delete();
            }
        } else {
            // Event doesn't exist so create one.
            if ($completionexpectedtime !== null) {
                $event->name = get_string('completionexpectedfor', 'completion', $lang);
                $event->description = format_module_intro($modulename, $instance, $cmid, false);
                $event->format = FORMAT_HTML;
                $event->courseid = $instance->course;
                $event->groupid = 0;
                $event->userid = 0;
                $event->modulename = $modulename;
                $event->instance = $instance->id;
                $event->timestart = $completionexpectedtime;
                $event->timesort = $completionexpectedtime;
                $event->visible = instance_is_visible($modulename, $instance);
                $event->timeduration = 0;

                \calendar_event::create($event, false);
            }
        }

        return true;
    }

    /**
     * Mark users who completed course based on activity criteria.
     * @param array $userdata If set only marks specified user in given course else checks all courses/users.
     * @return int Completion record id if $userdata is set, 0 else.
     * @since Moodle 4.0
     */
    public static function mark_course_completions_activity_criteria($userdata = null): int {
        global $DB;

        // Get all users who meet this criteria
        $sql = "SELECT DISTINCT c.id AS course,
                                cr.id AS criteriaid,
                                ra.userid AS userid,
                                mc.timemodified AS timecompleted
                  FROM {course_completion_criteria} cr
            INNER JOIN {course} c ON cr.course = c.id
            INNER JOIN {context} con ON con.instanceid = c.id
            INNER JOIN {role_assignments} ra ON ra.contextid = con.id
            INNER JOIN {course_modules} cm ON cm.id = cr.moduleinstance
            INNER JOIN {course_modules_completion} mc ON mc.coursemoduleid = cr.moduleinstance AND mc.userid = ra.userid
             LEFT JOIN {course_completion_crit_compl} cc ON cc.criteriaid = cr.id AND cc.userid = ra.userid
                 WHERE cr.criteriatype = :criteriatype
                       AND con.contextlevel = :contextlevel
                       AND c.enablecompletion = 1
                       AND cc.id IS NULL
                       AND (
                            mc.completionstate = :completionstate
                            OR (cm.completionpassgrade = 1 AND mc.completionstate = :completionstatepass1)
                            OR (cm.completionpassgrade = 0 AND (mc.completionstate = :completionstatepass2
                                                                OR mc.completionstate = :completionstatefail))
                            )";

        $params = [
            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY,
            'contextlevel' => CONTEXT_COURSE,
            'completionstate' => COMPLETION_COMPLETE,
            'completionstatepass1' => COMPLETION_COMPLETE_PASS,
            'completionstatepass2' => COMPLETION_COMPLETE_PASS,
            'completionstatefail' => COMPLETION_COMPLETE_FAIL
        ];

        if ($userdata) {
            $params['courseid'] = $userdata['courseid'];
            $params['userid'] = $userdata['userid'];
            $sql .= " AND c.id = :courseid AND ra.userid = :userid";
            // Mark as complete.
            $record = $DB->get_record_sql($sql, $params);
            if ($record) {
                $completion = new \completion_criteria_completion((array) $record, DATA_OBJECT_FETCH_BY_KEY);
                $result = $completion->mark_complete($record->timecompleted);
                return $result;
            }
        } else {
            // Loop through completions, and mark as complete.
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $record) {
                $completion = new \completion_criteria_completion((array) $record, DATA_OBJECT_FETCH_BY_KEY);
                $completion->mark_complete($record->timecompleted);
            }
            $rs->close();
        }
        return 0;
    }
}
