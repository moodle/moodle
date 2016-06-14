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
 * Clean the tool_monitor_events table.
 *
 * @package    tool_monitor
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor\task;

/**
 * Simple task to clean the tool_monitor_events table.
 */
class clean_events extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskcleanevents', 'tool_monitor');
    }

    /**
     * Performs the cleaning of events.
     */
    public function execute() {
        global $DB;

        if (!get_config('tool_monitor', 'enablemonitor')) {
            return; // The tool is disabled. Nothing to do.
        }

        // Array to store which events have been triggered in which course.
        $courses = array();

        // Firstly, let's go through the site wide rules. There may be multiple rules for the site that rely on
        // the same event being triggered, so we only remove the events when they reach the max timewindow.
        if ($siterules = $DB->get_recordset('tool_monitor_rules', array('courseid' => 0), 'timewindow DESC')) {
            // Go through each rule and check if there are any events we can remove.
            foreach ($siterules as $rule) {
                // Check if we have already processed this event.
                if (isset($courses[$rule->courseid][$rule->eventname])) {
                    continue;
                }
                // Store the timewindow for this event.
                $courses[$rule->courseid][$rule->eventname] = $rule->timewindow;
                // Delete any events that may exist that have exceeded the timewindow.
                $DB->delete_records_select('tool_monitor_events', 'eventname = :eventname AND
                    courseid = :courseid AND timecreated <= :timewindow',
                    array('eventname' => $rule->eventname, 'courseid' => $rule->courseid,
                        'timewindow' => time() - $rule->timewindow));
            }
            // Free resources.
            $siterules->close();
        }

        // Now, get the course rules. The same applies here - there may be multiple rules for the course that rely on
        // the same event being triggered, so we only remove the events when they reach the max timewindow.
        if ($rules = $DB->get_recordset_select('tool_monitor_rules', 'courseid != 0', array(), 'timewindow DESC')) {
            // Go through each rule and check if there are any events we can remove.
            foreach ($rules as $rule) {
                // Check if we have already processed this event for this particular course.
                if (isset($courses[$rule->courseid][$rule->eventname])) {
                    continue;
                }
                // Add the course and event to the list.
                $courses[$rule->courseid][$rule->eventname] = $rule->timewindow;
                // If there is a site wide rule listening for this event do not remove it unless the maximum
                // timewindow between the two has exceeded.
                $timewindow = $rule->timewindow;
                if (isset($courses[0][$rule->eventname]) && ($courses[0][$rule->eventname] > $timewindow)) {
                    $timewindow = $courses[0][$rule->eventname];
                }
                // Delete any events that may exist that have exceeded the timewindow.
                $DB->delete_records_select('tool_monitor_events', 'eventname = :eventname AND
                    courseid = :courseid AND timecreated <= :timewindow',
                        array('eventname' => $rule->eventname, 'courseid' => $rule->courseid,
                            'timewindow' => time() - $timewindow));
            }
            // Free resources.
            $rules->close();
        }

        if ($siterules || $rules) { // Check that there are rules present.
            // Get a list of all the events we have been through.
            $allevents = array();
            foreach ($courses as $key => $value) {
                foreach ($courses[$key] as $event => $notused) {
                    $allevents[] = $event;
                }
            }
            // Remove all the events in the table that are not applicable to any rule. There may be a rule in one course
            // listening for a certain event, but not in another course, so we can delete the event from the course
            // where there is no rule. We also have to consider site wide rules. We may have an event that is triggered
            // in a course we can't remove because there is a site wide rule for this event.
            if ($events = $DB->get_recordset('tool_monitor_events')) {
                // Array to store which events we need to remove.
                $eventstodelete = array();
                // Store the current time.
                $now = time();
                foreach ($events as $event) {
                    // If the event is not required for a course rule and there is no site wide rule for it, or
                    // it has extended past or equal to the timewindow for the site rule - it can be deleted.
                    if (!isset($courses[$event->courseid][$event->eventname]) && (!isset($courses[0][$event->eventname])
                        || $courses[0][$event->eventname] <= ($now - $event->timecreated))) {
                        $eventstodelete[] = $event->id;
                    }
                }
                // Free resources.
                $events->close();

                // Remove the events.
                if (!empty($eventstodelete)) {
                    list($eventidsql, $params) = $DB->get_in_or_equal($eventstodelete);
                    $DB->delete_records_select('tool_monitor_events', "id $eventidsql", $params);
                }
            }

            // Remove all the events in the table that are not used by any rule.
            if (!empty($allevents)) {
                list($eventnamesql, $params) = $DB->get_in_or_equal($allevents, SQL_PARAMS_QM, 'param', false);
                $DB->delete_records_select('tool_monitor_events', "eventname $eventnamesql", $params);
            }
        } else { // No rules, just remove everything.
            $DB->delete_records('tool_monitor_events');
        }
    }
}
