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
 * External calendar API
 *
 * @package    core_calendar
 * @category   external
 * @copyright  2012 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Calendar external functions
 *
 * @package    core_calendar
 * @category   external
 * @copyright  2012 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 */
class core_calendar_external extends external_api {


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_calendar_events_parameters() {
        return new external_function_parameters(
                array('events' => new external_multiple_structure(
                        new external_single_structure(
                                array(
                                        'eventid' => new external_value(PARAM_INT, 'Event ID', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                                        'repeat'  => new external_value(PARAM_BOOL, 'Delete comeplete series if repeated event')
                                ), 'List of events to delete'
                        )
                    )
                )
        );
    }

    /**
     * Delete Calendar events
     *
     * @param array $eventids A list of event ids with repeat flag to delete
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_calendar_events($events) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/calendar/lib.php");

        // Parameter validation.
        $params = self::validate_parameters(self:: delete_calendar_events_parameters(), array('events' => $events));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['events'] as $event) {
            $eventobj = calendar_event::load($event['eventid']);

            // Let's check if the user is allowed to delete an event.
            if (!calendar_edit_event_allowed($eventobj)) {
                throw new moodle_exception("nopermissions");
            }
            // Time to do the magic.
            $eventobj->delete($event['repeat']);
        }

        // Everything done smoothly, let's commit.
        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function  delete_calendar_events_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_calendar_events_parameters() {
        return new external_function_parameters(
                array('events' => new external_single_structure(
                            array(
                                    'eventids' => new external_multiple_structure(
                                            new external_value(PARAM_INT, 'event ids')
                                            , 'List of event ids',
                                            VALUE_DEFAULT, array(), NULL_ALLOWED
                                                ),
                                    'courseids' => new external_multiple_structure(
                                            new external_value(PARAM_INT, 'course ids')
                                            , 'List of course ids for which events will be returned',
                                            VALUE_DEFAULT, array(), NULL_ALLOWED
                                                ),
                                    'groupids' => new external_multiple_structure(
                                            new external_value(PARAM_INT, 'group ids')
                                            , 'List of group ids for which events should be returned',
                                            VALUE_DEFAULT, array(), NULL_ALLOWED
                                                )
                            ), 'Event details', VALUE_DEFAULT, array()),
                    'options' => new external_single_structure(
                            array(
                                    'userevents' => new external_value(PARAM_BOOL,
                                             "Set to true to return current user's user events",
                                             VALUE_DEFAULT, true, NULL_ALLOWED),
                                    'siteevents' => new external_value(PARAM_BOOL,
                                             "Set to true to return global events",
                                             VALUE_DEFAULT, true, NULL_ALLOWED),
                                    'timestart' => new external_value(PARAM_INT,
                                             "Time from which events should be returned",
                                             VALUE_DEFAULT, 0, NULL_ALLOWED),
                                    'timeend' => new external_value(PARAM_INT,
                                             "Time to which the events should be returned",
                                             VALUE_DEFAULT, time(), NULL_ALLOWED),
                                    'ignorehidden' => new external_value(PARAM_BOOL,
                                             "Ignore hidden events or not",
                                             VALUE_DEFAULT, true, NULL_ALLOWED),

                            ), 'Options', VALUE_DEFAULT, array())
                )
        );
    }

    /**
     * Get Calendar events
     *
     * @param array $events A list of events
     * @package array $options various options
     * @return array Array of event details
     * @since Moodle 2.5
     */
    public static function get_calendar_events($events = array(), $options = array()) {
        global $SITE, $DB, $USER, $CFG;
        require_once($CFG->dirroot."/calendar/lib.php");

        // Parameter validation.
        $params = self::validate_parameters(self::get_calendar_events_parameters(), array('events' => $events, 'options' => $options));
        $funcparam = array('courses' => array(), 'groups' => array());
        $hassystemcap = has_capability('moodle/calendar:manageentries', context_system::instance());
        $warnings = array();

        // Let us findout courses that we can return events from.
        if (!$hassystemcap) {
            $courses = enrol_get_my_courses();
            $courses = array_keys($courses);
            foreach ($params['events']['courseids'] as $id) {
                if (in_array($id, $courses)) {
                    $funcparam['courses'][] = $id;
                } else {
                    $warnings[] = array('item' => $id, 'warningcode' => 'nopermissions', 'message' => 'you do not have permissions to access this course');
                }
            }
        } else {
            $courses = $params['events']['courseids'];
            $funcparam['courses'] = $courses;
        }

        // Let us findout groups that we can return events from.
        if (!$hassystemcap) {
            $groups = groups_get_my_groups();
            $groups = array_keys($groups);
            foreach ($params['events']['groupids'] as $id) {
                if (in_array($id, $groups)) {
                    $funcparam['groups'][] = $id;
                } else {
                    $warnings[] = array('item' => $id, 'warningcode' => 'nopermissions', 'message' => 'you do not have permissions to access this group');
                }
            }
        } else {
            $groups = $params['events']['groupids'];
            $funcparam['groups'] = $groups;
        }

        // Do we need user events?
        if (!empty($params['options']['userevents'])) {
            $funcparam['users'] = array($USER->id);
        } else {
            $funcparam['users'] = false;
        }

        // Do we need site events?
        if (!empty($params['options']['siteevents'])) {
            $funcparam['courses'][] = $SITE->id;
        }

        $eventlist = calendar_get_events($params['options']['timestart'], $params['options']['timeend'], $funcparam['users'], $funcparam['groups'],
                $funcparam['courses'], true, $params['options']['ignorehidden']);
        // WS expects arrays.
        $events = array();
        foreach ($eventlist as $id => $event) {
            $events[$id] = (array) $event;
        }

        // We need to get events asked for eventids.
        $eventsbyid = calendar_get_events_by_id($params['events']['eventids']);
        foreach ($eventsbyid as $eventid => $eventobj) {
            $event = (array) $eventobj;
            if (isset($events[$eventid])) {
                   continue;
            }
            if ($hassystemcap) {
                // User can see everything, no further check is needed.
                $events[$eventid] = $event;
            } else if (!empty($eventobj->modulename)) {
                $cm = get_coursemodule_from_instance($eventobj->modulename, $eventobj->instance);
                if (groups_course_module_visible($cm)) {
                    $events[$eventid] = $event;
                }
            } else {
                // Can the user actually see this event?
                $eventobj = calendar_event::load($eventobj);
                if (($eventobj->courseid == $SITE->id) ||
                            (!empty($eventobj->groupid) && in_array($eventobj->groupid, $groups)) ||
                            (!empty($eventobj->courseid) && in_array($eventobj->courseid, $courses)) ||
                            ($USER->id == $eventobj->userid) ||
                            (calendar_edit_event_allowed($eventid))) {
                    $events[$eventid] = $event;
                } else {
                    $warnings[] = array('item' => $eventid, 'warningcode' => 'nopermissions', 'message' => 'you do not have permissions to view this event');
                }
            }
        }
        return array('events' => $events, 'warnings' => $warnings);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function  get_calendar_events_returns() {
        return new external_single_structure(array(
                'events' => new external_multiple_structure( new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'event id'),
                            'name' => new external_value(PARAM_TEXT, 'event name'),
                            'description' => new external_value(PARAM_RAW, 'Description', VALUE_OPTIONAL, null, NULL_ALLOWED),
                            'format' => new external_format_value('description'),
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'groupid' => new external_value(PARAM_INT, 'group id'),
                            'userid' => new external_value(PARAM_INT, 'user id'),
                            'repeatid' => new external_value(PARAM_INT, 'repeat id'),
                            'modulename' => new external_value(PARAM_TEXT, 'module name', VALUE_OPTIONAL, null, NULL_ALLOWED),
                            'instance' => new external_value(PARAM_INT, 'instance id'),
                            'eventtype' => new external_value(PARAM_TEXT, 'Event type'),
                            'timestart' => new external_value(PARAM_INT, 'timestart'),
                            'timeduration' => new external_value(PARAM_INT, 'time duration'),
                            'visible' => new external_value(PARAM_INT, 'visible'),
                            'uuid' => new external_value(PARAM_TEXT, 'unique id of ical events', VALUE_OPTIONAL, null, NULL_NOT_ALLOWED),
                            'sequence' => new external_value(PARAM_INT, 'sequence'),
                            'timemodified' => new external_value(PARAM_INT, 'time modified'),
                            'subscriptionid' => new external_value(PARAM_INT, 'Subscription id', VALUE_OPTIONAL, null, NULL_ALLOWED),
                        ), 'event')
                 ),
                 'warnings' => new external_warnings()
                )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     * @since Moodle 2.5
     */
    public static function create_calendar_events_parameters() {
        // Userid is always current user, so no need to get it from client.
        // Module based calendar events are not allowed here. Hence no need of instance and modulename.
        // subscription id and uuid is not allowed as this is not an ical api.
        return new external_function_parameters(
                array('events' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name' => new external_value(PARAM_TEXT, 'event name', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                                'description' => new external_value(PARAM_RAW, 'Description', VALUE_DEFAULT, null, NULL_ALLOWED),
                                'format' => new external_format_value('description', VALUE_DEFAULT),
                                'courseid' => new external_value(PARAM_INT, 'course id', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                                'groupid' => new external_value(PARAM_INT, 'group id', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                                'repeats' => new external_value(PARAM_INT, 'number of repeats', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                                'eventtype' => new external_value(PARAM_TEXT, 'Event type', VALUE_DEFAULT, 'user', NULL_NOT_ALLOWED),
                                'timestart' => new external_value(PARAM_INT, 'timestart', VALUE_DEFAULT, time(), NULL_NOT_ALLOWED),
                                'timeduration' => new external_value(PARAM_INT, 'time duration', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                                'visible' => new external_value(PARAM_INT, 'visible', VALUE_DEFAULT, 1, NULL_NOT_ALLOWED),
                                'sequence' => new external_value(PARAM_INT, 'sequence', VALUE_DEFAULT, 1, NULL_NOT_ALLOWED),
                            ), 'event')
                )
            )
        );
    }

    /**
     * Delete Calendar events.
     *
     * @param array $events A list of events to create.
     * @return array array of events created.
     * @since Moodle 2.5
     * @throws moodle_exception if user doesnt have the permission to create events.
     */
    public static function create_calendar_events($events) {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot."/calendar/lib.php");

        // Parameter validation.
        $params = self::validate_parameters(self::create_calendar_events_parameters(), array('events' => $events));

        $transaction = $DB->start_delegated_transaction();
        $return = array();
        $warnings = array();

        foreach ($params['events'] as $event) {

            // Let us set some defaults.
            $event['userid'] = $USER->id;
            $event['modulename'] = '';
            $event['instance'] = 0;
            $event['subscriptionid'] = null;
            $event['uuid']= '';
            $event['format'] = external_validate_format($event['format']);
            if ($event['repeats'] > 0) {
                $event['repeat'] = 1;
            } else {
                $event['repeat'] = 0;
            }

            $eventobj = new calendar_event($event);

            // Let's check if the user is allowed to delete an event.
            if (!calendar_add_event_allowed($eventobj)) {
                $warnings [] = array('item' => $event['name'], 'warningcode' => 'nopermissions', 'message' => 'you do not have permissions to create this event');
                continue;
            }
            // Let's create the event.
            $var = $eventobj->create($event);
            $var = (array)$var->properties();
            if ($event['repeat']) {
                $children = $DB->get_records('event', array('repeatid' => $var['id']));
                foreach ($children as $child) {
                    $return[] = (array) $child;
                }
            } else {
                $return[] = $var;
            }
        }

        // Everything done smoothly, let's commit.
        $transaction->allow_commit();
        return array('events' => $return, 'warnings' => $warnings);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description.
     * @since Moodle 2.5
     */
    public static function  create_calendar_events_returns() {
            return new external_single_structure(
                    array(
                        'events' => new external_multiple_structure( new external_single_structure(
                                array(
                                    'id' => new external_value(PARAM_INT, 'event id'),
                                    'name' => new external_value(PARAM_TEXT, 'event name'),
                                    'description' => new external_value(PARAM_RAW, 'Description', VALUE_OPTIONAL),
                                    'format' => new external_format_value('description'),
                                    'courseid' => new external_value(PARAM_INT, 'course id'),
                                    'groupid' => new external_value(PARAM_INT, 'group id'),
                                    'userid' => new external_value(PARAM_INT, 'user id'),
                                    'repeatid' => new external_value(PARAM_INT, 'repeat id', VALUE_OPTIONAL),
                                    'modulename' => new external_value(PARAM_TEXT, 'module name', VALUE_OPTIONAL),
                                    'instance' => new external_value(PARAM_INT, 'instance id'),
                                    'eventtype' => new external_value(PARAM_TEXT, 'Event type'),
                                    'timestart' => new external_value(PARAM_INT, 'timestart'),
                                    'timeduration' => new external_value(PARAM_INT, 'time duration'),
                                    'visible' => new external_value(PARAM_INT, 'visible'),
                                    'uuid' => new external_value(PARAM_TEXT, 'unique id of ical events', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                                    'sequence' => new external_value(PARAM_INT, 'sequence'),
                                    'timemodified' => new external_value(PARAM_INT, 'time modified'),
                                    'subscriptionid' => new external_value(PARAM_INT, 'Subscription id', VALUE_OPTIONAL),
                                ), 'event')
                        ),
                      'warnings' => new external_warnings()
                    )
            );
    }
}
