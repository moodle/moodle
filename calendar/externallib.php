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
require_once($CFG->dirroot . '/calendar/lib.php');

use \core_calendar\local\api as local_api;
use \core_calendar\local\event\container as event_container;
use \core_calendar\local\event\forms\create as create_event_form;
use \core_calendar\local\event\forms\update as update_event_form;
use \core_calendar\local\event\mappers\create_update_form_mapper;
use \core_calendar\external\event_exporter;
use \core_calendar\external\events_exporter;
use \core_calendar\external\events_grouped_by_course_exporter;
use \core_calendar\external\events_related_objects_cache;

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
        global $DB;

        // Parameter validation.
        $params = self::validate_parameters(self:: delete_calendar_events_parameters(), array('events' => $events));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['events'] as $event) {
            $eventobj = calendar_event::load($event['eventid']);

            // Let's check if the user is allowed to delete an event.
            if (!calendar_delete_event_allowed($eventobj)) {
                throw new moodle_exception('nopermissions', 'error', '', get_string('deleteevent', 'calendar'));
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
                                            VALUE_DEFAULT, array()),
                                    'courseids' => new external_multiple_structure(
                                            new external_value(PARAM_INT, 'course ids')
                                            , 'List of course ids for which events will be returned',
                                            VALUE_DEFAULT, array()),
                                    'groupids' => new external_multiple_structure(
                                            new external_value(PARAM_INT, 'group ids')
                                            , 'List of group ids for which events should be returned',
                                            VALUE_DEFAULT, array()),
                                    'categoryids' => new external_multiple_structure(
                                            new external_value(PARAM_INT, 'Category ids'),
                                            'List of category ids for which events will be returned',
                                            VALUE_DEFAULT, array()),
                            ), 'Event details', VALUE_DEFAULT, array()),
                    'options' => new external_single_structure(
                            array(
                                    'userevents' => new external_value(PARAM_BOOL,
                                             "Set to true to return current user's user events",
                                             VALUE_DEFAULT, true, NULL_ALLOWED),
                                    'siteevents' => new external_value(PARAM_BOOL,
                                             "Set to true to return site events",
                                             VALUE_DEFAULT, true, NULL_ALLOWED),
                                    'timestart' => new external_value(PARAM_INT,
                                             "Time from which events should be returned",
                                             VALUE_DEFAULT, 0, NULL_ALLOWED),
                                    'timeend' => new external_value(PARAM_INT,
                                             "Time to which the events should be returned. We treat 0 and null as no end",
                                             VALUE_DEFAULT, 0, NULL_ALLOWED),
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
     * @param array $options various options
     * @return array Array of event details
     * @since Moodle 2.5
     */
    public static function get_calendar_events($events = array(), $options = array()) {
        global $SITE, $DB, $USER;

        // Parameter validation.
        $params = self::validate_parameters(self::get_calendar_events_parameters(), array('events' => $events, 'options' => $options));
        $funcparam = array('courses' => array(), 'groups' => array(), 'categories' => array());
        $hassystemcap = has_capability('moodle/calendar:manageentries', context_system::instance());
        $warnings = array();
        $coursecategories = array();

        // Let us find out courses and their categories that we can return events from.
        if (!$hassystemcap) {
            $courseobjs = enrol_get_my_courses();
            $courses = array_keys($courseobjs);

            $coursecategories = array_flip(array_map(function($course) {
                return $course->category;
            }, $courseobjs));

            foreach ($params['events']['courseids'] as $id) {
               try {
                    $context = context_course::instance($id);
                    self::validate_context($context);
                    $funcparam['courses'][] = $id;
                } catch (Exception $e) {
                    $warnings[] = array(
                        'item' => 'course',
                        'itemid' => $id,
                        'warningcode' => 'nopermissions',
                        'message' => 'No access rights in course context '.$e->getMessage().$e->getTraceAsString()
                    );
                }
            }
        } else {
            $courses = $params['events']['courseids'];
            $funcparam['courses'] = $courses;

            if (!empty($courses)) {
                list($wheresql, $sqlparams) = $DB->get_in_or_equal($courses);
                $wheresql = "id $wheresql";
                $coursecategories = array_flip(array_map(function($course) {
                    return $course->category;
                }, $DB->get_records_select('course', $wheresql, $sqlparams, '', 'category')));
            }
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

        $categories = array();
        if ($hassystemcap || !empty($courses)) {
            // Use the category id as the key in the following array. That way we do not have to remove duplicates and
            // have a faster lookup later.
            $categories = [];

            if (!empty($params['events']['categoryids'])) {
                $catobjs = \core_course_category::get_many(
                    array_merge($params['events']['categoryids'], array_keys($coursecategories)));
                foreach ($catobjs as $catobj) {
                    if (isset($coursecategories[$catobj->id]) ||
                            has_capability('moodle/category:manage', $catobj->get_context())) {
                        // If the user has access to a course in this category or can manage the category,
                        // then they can see all parent categories too.
                        $categories[$catobj->id] = true;
                        foreach ($catobj->get_parents() as $catid) {
                            $categories[$catid] = true;
                        }
                    }
                }
                $funcparam['categories'] = array_keys($categories);
            } else {
                // Fetch all categories where this user has any enrolment, and all categories that this user can manage.
                $calcatcache = cache::make('core', 'calendar_categories');
                // Do not use cache if the user has the system capability as $coursecategories might not represent the
                // courses the user is enrolled in.
                $categories = (!$hassystemcap) ? $calcatcache->get('site') : false;
                if ($categories !== false) {
                    // The ids are stored in a list in the cache.
                    $funcparam['categories'] = $categories;
                    $categories = array_flip($categories);
                } else {
                    $categories = [];
                    foreach (\core_course_category::get_all() as $category) {
                        if (isset($coursecategories[$category->id]) ||
                                has_capability('moodle/category:manage', $category->get_context(), $USER, false)) {
                            // If the user has access to a course in this category or can manage the category,
                            // then they can see all parent categories too.
                            $categories[$category->id] = true;
                            foreach ($category->get_parents() as $catid) {
                                $categories[$catid] = true;
                            }
                        }
                    }
                    $funcparam['categories'] = array_keys($categories);
                    if (!$hassystemcap) {
                        $calcatcache->set('site', $funcparam['categories']);
                    }
                }
            }
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

        // We treat 0 and null as no end.
        if (empty($params['options']['timeend'])) {
            $params['options']['timeend'] = PHP_INT_MAX;
        }

        // Event list does not check visibility and permissions, we'll check that later.
        $eventlist = calendar_get_legacy_events($params['options']['timestart'], $params['options']['timeend'],
                $funcparam['users'], $funcparam['groups'], $funcparam['courses'], true,
                $params['options']['ignorehidden'], $funcparam['categories']);

        // WS expects arrays.
        $events = array();

        // We need to get events asked for eventids.
        if ($eventsbyid = calendar_get_events_by_id($params['events']['eventids'])) {
            $eventlist += $eventsbyid;
        }
        foreach ($eventlist as $eventid => $eventobj) {
            $event = (array) $eventobj;
            // Description formatting.
            $calendareventobj = new calendar_event($event);
            $event['name'] = $calendareventobj->format_external_name();
            list($event['description'], $event['format']) = $calendareventobj->format_external_text();

            if ($hassystemcap) {
                // User can see everything, no further check is needed.
                $events[$eventid] = $event;
            } else if (!empty($eventobj->modulename)) {
                $courseid = $eventobj->courseid;
                if (!$courseid) {
                    if (!$calendareventobj->context || !($context = $calendareventobj->context->get_course_context(false))) {
                        continue;
                    }
                    $courseid = $context->instanceid;
                }
                $instances = get_fast_modinfo($courseid)->get_instances_of($eventobj->modulename);
                if (!empty($instances[$eventobj->instance]->uservisible)) {
                    $events[$eventid] = $event;
                }
            } else {
                // Can the user actually see this event?
                $eventobj = calendar_event::load($eventobj);
                if ((($eventobj->courseid == $SITE->id) && (empty($eventobj->categoryid))) ||
                            (!empty($eventobj->categoryid) && isset($categories[$eventobj->categoryid])) ||
                            (!empty($eventobj->groupid) && in_array($eventobj->groupid, $groups)) ||
                            (!empty($eventobj->courseid) && in_array($eventobj->courseid, $courses)) ||
                            ($USER->id == $eventobj->userid) ||
                            (calendar_edit_event_allowed($eventobj))) {
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
                            'name' => new external_value(PARAM_RAW, 'event name'),
                            'description' => new external_value(PARAM_RAW, 'Description', VALUE_OPTIONAL, null, NULL_ALLOWED),
                            'format' => new external_format_value('description'),
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'categoryid' => new external_value(PARAM_INT, 'Category id (only for category events).',
                                VALUE_OPTIONAL),
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
     * @since Moodle 3.3
     * @return external_function_parameters
     */
    public static function get_calendar_action_events_by_timesort_parameters() {
        return new external_function_parameters(
            array(
                'timesortfrom' => new external_value(PARAM_INT, 'Time sort from', VALUE_DEFAULT, 0),
                'timesortto' => new external_value(PARAM_INT, 'Time sort to', VALUE_DEFAULT, null),
                'aftereventid' => new external_value(PARAM_INT, 'The last seen event id', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 20),
                'limittononsuspendedevents' => new external_value(PARAM_BOOL,
                        'Limit the events to courses the user is not suspended in', VALUE_DEFAULT, false),
                'userid' => new external_value(PARAM_INT, 'The user id', VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Get calendar action events based on the timesort value.
     *
     * @since Moodle 3.3
     * @param null|int $timesortfrom Events after this time (inclusive)
     * @param null|int $timesortto Events before this time (inclusive)
     * @param null|int $aftereventid Get events with ids greater than this one
     * @param int $limitnum Limit the number of results to this value
     * @param null|int $userid The user id
     * @return array
     */
    public static function get_calendar_action_events_by_timesort($timesortfrom = 0, $timesortto = null,
                                                       $aftereventid = 0, $limitnum = 20, $limittononsuspendedevents = false,
                                                       $userid = null) {
        global $PAGE, $USER;

        $params = self::validate_parameters(
            self::get_calendar_action_events_by_timesort_parameters(),
            [
                'timesortfrom' => $timesortfrom,
                'timesortto' => $timesortto,
                'aftereventid' => $aftereventid,
                'limitnum' => $limitnum,
                'limittononsuspendedevents' => $limittononsuspendedevents,
                'userid' => $userid,
            ]
        );
        if ($params['userid']) {
            $user = \core_user::get_user($params['userid']);
        } else {
            $user = $USER;
        }

        $context = \context_user::instance($user->id);
        self::validate_context($context);

        if (empty($params['aftereventid'])) {
            $params['aftereventid'] = null;
        }

        $renderer = $PAGE->get_renderer('core_calendar');
        $events = local_api::get_action_events_by_timesort(
            $params['timesortfrom'],
            $params['timesortto'],
            $params['aftereventid'],
            $params['limitnum'],
            $params['limittononsuspendedevents'],
            $user
        );

        $exportercache = new events_related_objects_cache($events);
        $exporter = new events_exporter($events, ['cache' => $exportercache]);

        return $exporter->export($renderer);
    }

    /**
     * Returns description of method result value.
     *
     * @since Moodle 3.3
     * @return external_description
     */
    public static function get_calendar_action_events_by_timesort_returns() {
        return events_exporter::get_read_structure();
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_calendar_action_events_by_course_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'timesortfrom' => new external_value(PARAM_INT, 'Time sort from', VALUE_DEFAULT, null),
                'timesortto' => new external_value(PARAM_INT, 'Time sort to', VALUE_DEFAULT, null),
                'aftereventid' => new external_value(PARAM_INT, 'The last seen event id', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 20)
            )
        );
    }

    /**
     * Get calendar action events for the given course.
     *
     * @since Moodle 3.3
     * @param int $courseid Only events in this course
     * @param null|int $timesortfrom Events after this time (inclusive)
     * @param null|int $timesortto Events before this time (inclusive)
     * @param null|int $aftereventid Get events with ids greater than this one
     * @param int $limitnum Limit the number of results to this value
     * @return array
     */
    public static function get_calendar_action_events_by_course(
        $courseid, $timesortfrom = null, $timesortto = null, $aftereventid = 0, $limitnum = 20) {

        global $PAGE, $USER;

        $user = null;
        $params = self::validate_parameters(
            self::get_calendar_action_events_by_course_parameters(),
            [
                'courseid' => $courseid,
                'timesortfrom' => $timesortfrom,
                'timesortto' => $timesortto,
                'aftereventid' => $aftereventid,
                'limitnum' => $limitnum,
            ]
        );
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        if (empty($params['aftereventid'])) {
            $params['aftereventid'] = null;
        }

        $courses = enrol_get_my_courses('*', null, 0, [$courseid]);
        $courses = array_values($courses);

        if (empty($courses)) {
            return [];
        }

        $course = $courses[0];
        $renderer = $PAGE->get_renderer('core_calendar');
        $events = local_api::get_action_events_by_course(
            $course,
            $params['timesortfrom'],
            $params['timesortto'],
            $params['aftereventid'],
            $params['limitnum']
        );

        $exportercache = new events_related_objects_cache($events, $courses);
        $exporter = new events_exporter($events, ['cache' => $exportercache]);

        return $exporter->export($renderer);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function get_calendar_action_events_by_course_returns() {
        return events_exporter::get_read_structure();
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_calendar_action_events_by_courses_parameters() {
        return new external_function_parameters(
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id')
                ),
                'timesortfrom' => new external_value(PARAM_INT, 'Time sort from', VALUE_DEFAULT, null),
                'timesortto' => new external_value(PARAM_INT, 'Time sort to', VALUE_DEFAULT, null),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 10)
            )
        );
    }

    /**
     * Get calendar action events for a given list of courses.
     *
     * @since Moodle 3.3
     * @param array $courseids Only include events for these courses
     * @param null|int $timesortfrom Events after this time (inclusive)
     * @param null|int $timesortto Events before this time (inclusive)
     * @param int $limitnum Limit the number of results per course to this value
     * @return array
     */
    public static function get_calendar_action_events_by_courses(
        array $courseids, $timesortfrom = null, $timesortto = null, $limitnum = 10) {

        global $PAGE, $USER;

        $user = null;
        $params = self::validate_parameters(
            self::get_calendar_action_events_by_courses_parameters(),
            [
                'courseids' => $courseids,
                'timesortfrom' => $timesortfrom,
                'timesortto' => $timesortto,
                'limitnum' => $limitnum,
            ]
        );
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        if (empty($params['courseids'])) {
            return ['groupedbycourse' => []];
        }

        $renderer = $PAGE->get_renderer('core_calendar');
        $courses = enrol_get_my_courses('*', null, 0, $params['courseids']);
        $courses = array_values($courses);

        if (empty($courses)) {
            return ['groupedbycourse' => []];
        }

        $events = local_api::get_action_events_by_courses(
            $courses,
            $params['timesortfrom'],
            $params['timesortto'],
            $params['limitnum']
        );

        if (empty($events)) {
            return ['groupedbycourse' => []];
        }

        $exportercache = new events_related_objects_cache($events, $courses);
        $exporter = new events_grouped_by_course_exporter($events, ['cache' => $exportercache]);

        return $exporter->export($renderer);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function get_calendar_action_events_by_courses_returns() {
        return events_grouped_by_course_exporter::get_read_structure();
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
     * Create calendar events.
     *
     * @param array $events A list of events to create.
     * @return array array of events created.
     * @since Moodle 2.5
     * @throws moodle_exception if user doesnt have the permission to create events.
     */
    public static function create_calendar_events($events) {
        global $DB, $USER;

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
                                    'name' => new external_value(PARAM_RAW, 'event name'),
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

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_calendar_event_by_id_parameters() {
        return new external_function_parameters(
            array(
                'eventid' => new external_value(PARAM_INT, 'The event id to be retrieved'),
            )
        );
    }

    /**
     * Get calendar event by id.
     *
     * @param int $eventid The calendar event id to be retrieved.
     * @return array Array of event details
     */
    public static function get_calendar_event_by_id($eventid) {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::get_calendar_event_by_id_parameters(), ['eventid' => $eventid]);
        $context = \context_user::instance($USER->id);

        self::validate_context($context);
        $warnings = array();

        $legacyevent = calendar_event::load($eventid);
        // Must check we can see this event.
        if (!calendar_view_event_allowed($legacyevent)) {
            // We can't return a warning in this case because the event is not optional.
            // We don't know the context for the event and it's not worth loading it.
            $syscontext = context_system::instance();
            throw new \required_capability_exception($syscontext, 'moodle/course:view', 'nopermission', '');
        }

        $legacyevent->count_repeats();

        $eventmapper = event_container::get_event_mapper();
        $event = $eventmapper->from_legacy_event_to_event($legacyevent);

        $cache = new events_related_objects_cache([$event]);
        $relatedobjects = [
            'context' => $cache->get_context($event),
            'course' => $cache->get_course($event),
        ];

        $exporter = new event_exporter($event, $relatedobjects);
        $renderer = $PAGE->get_renderer('core_calendar');

        return array('event' => $exporter->export($renderer), 'warnings' => $warnings);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_calendar_event_by_id_returns() {
        $eventstructure = event_exporter::get_read_structure();

        return new external_single_structure(array(
            'event' => $eventstructure,
            'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     */
    public static function submit_create_update_form_parameters() {
        return new external_function_parameters(
            [
                'formdata' => new external_value(PARAM_RAW, 'The data from the event form'),
            ]
        );
    }

    /**
     * Handles the event form submission.
     *
     * @param string $formdata The event form data in a URI encoded param string
     * @return array The created or modified event
     * @throws moodle_exception
     */
    public static function submit_create_update_form($formdata) {
        global $USER, $PAGE, $CFG;
        require_once($CFG->libdir."/filelib.php");

        // Parameter validation.
        $params = self::validate_parameters(self::submit_create_update_form_parameters(), ['formdata' => $formdata]);
        $context = \context_user::instance($USER->id);
        $data = [];

        self::validate_context($context);
        parse_str($params['formdata'], $data);

        if (WS_SERVER) {
            // Request via WS, ignore sesskey checks in form library.
            $USER->ignoresesskey = true;
        }

        $eventtype = isset($data['eventtype']) ? $data['eventtype'] : null;
        $coursekey = ($eventtype == 'group') ? 'groupcourseid' : 'courseid';
        $courseid = (!empty($data[$coursekey])) ? $data[$coursekey] : null;
        $editoroptions = \core_calendar\local\event\forms\create::build_editor_options($context);
        $formoptions = ['editoroptions' => $editoroptions, 'courseid' => $courseid];
        $formoptions['eventtypes'] = calendar_get_allowed_event_types($courseid);
        if ($courseid) {
            require_once($CFG->libdir . '/grouplib.php');
            $groupcoursedata = groups_get_course_data($courseid);
            if (!empty($groupcoursedata->groups)) {
                $formoptions['groups'] = [];
                foreach ($groupcoursedata->groups as $groupid => $groupdata) {
                    $formoptions['groups'][$groupid] = $groupdata->name;
                }
            }
        }

        if (!empty($data['id'])) {
            $eventid = clean_param($data['id'], PARAM_INT);
            $legacyevent = calendar_event::load($eventid);
            $legacyevent->count_repeats();
            $formoptions['event'] = $legacyevent;
            $mform = new update_event_form(null, $formoptions, 'post', '', null, true, $data);
        } else {
            $legacyevent = null;
            $mform = new create_event_form(null, $formoptions, 'post', '', null, true, $data);
        }

        if ($validateddata = $mform->get_data()) {
            $formmapper = new create_update_form_mapper();
            $properties = $formmapper->from_data_to_event_properties($validateddata);

            if (is_null($legacyevent)) {
                $legacyevent = new \calendar_event($properties);
                // Need to do this in order to initialise the description
                // property which then triggers the update function below
                // to set the appropriate default properties on the event.
                $properties = $legacyevent->properties(true);
            }

            if (!calendar_edit_event_allowed($legacyevent, true)) {
                print_error('nopermissiontoupdatecalendar');
            }

            $legacyevent->update($properties);
            $eventcontext = $legacyevent->context;

            file_remove_editor_orphaned_files($validateddata->description);

            // Take any files added to the description draft file area and
            // convert them into the proper event description file area. Also
            // parse the description text and replace the URLs to the draft files
            // with the @@PLUGIN_FILE@@ placeholder to be persisted in the DB.
            $description = file_save_draft_area_files(
                $validateddata->description['itemid'],
                $eventcontext->id,
                'calendar',
                'event_description',
                $legacyevent->id,
                create_event_form::build_editor_options($eventcontext),
                $validateddata->description['text']
            );

            // If draft files were found then we need to save the new
            // description value.
            if ($description != $validateddata->description['text']) {
                $properties->id = $legacyevent->id;
                $properties->description = $description;
                $legacyevent->update($properties);
            }

            $eventmapper = event_container::get_event_mapper();
            $event = $eventmapper->from_legacy_event_to_event($legacyevent);
            $cache = new events_related_objects_cache([$event]);
            $relatedobjects = [
                'context' => $cache->get_context($event),
                'course' => $cache->get_course($event),
            ];
            $exporter = new event_exporter($event, $relatedobjects);
            $renderer = $PAGE->get_renderer('core_calendar');

            return [ 'event' => $exporter->export($renderer) ];
        } else {
            return [ 'validationerror' => true ];
        }
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description.
     */
    public static function  submit_create_update_form_returns() {
        $eventstructure = event_exporter::get_read_structure();
        $eventstructure->required = VALUE_OPTIONAL;

        return new external_single_structure(
            array(
                'event' => $eventstructure,
                'validationerror' => new external_value(PARAM_BOOL, 'Invalid form data', VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Get data for the monthly calendar view.
     *
     * @param   int     $year The year to be shown
     * @param   int     $month The month to be shown
     * @param   int     $courseid The course to be included
     * @param   int     $categoryid The category to be included
     * @param   bool    $includenavigation Whether to include navigation
     * @param   bool    $mini Whether to return the mini month view or not
     * @param   int     $day The day we want to keep as the current day
     * @return  array
     */
    public static function get_calendar_monthly_view($year, $month, $courseid, $categoryid, $includenavigation, $mini, $day) {
        global $USER, $PAGE;

        // Parameter validation.
        $params = self::validate_parameters(self::get_calendar_monthly_view_parameters(), [
            'year' => $year,
            'month' => $month,
            'courseid' => $courseid,
            'categoryid' => $categoryid,
            'includenavigation' => $includenavigation,
            'mini' => $mini,
            'day' => $day,
        ]);

        $context = \context_user::instance($USER->id);
        self::validate_context($context);
        $PAGE->set_url('/calendar/');

        $type = \core_calendar\type_factory::get_calendar_instance();

        $time = $type->convert_to_timestamp($params['year'], $params['month'], $params['day']);
        $calendar = \calendar_information::create($time, $params['courseid'], $params['categoryid']);
        self::validate_context($calendar->context);

        $view = $params['mini'] ? 'mini' : 'month';
        list($data, $template) = calendar_get_view($calendar, $view, $params['includenavigation']);

        return $data;
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_calendar_monthly_view_parameters() {
        return new external_function_parameters(
            [
                'year' => new external_value(PARAM_INT, 'Year to be viewed', VALUE_REQUIRED),
                'month' => new external_value(PARAM_INT, 'Month to be viewed', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'Course being viewed', VALUE_DEFAULT, SITEID, NULL_ALLOWED),
                'categoryid' => new external_value(PARAM_INT, 'Category being viewed', VALUE_DEFAULT, null, NULL_ALLOWED),
                'includenavigation' => new external_value(
                    PARAM_BOOL,
                    'Whether to show course navigation',
                    VALUE_DEFAULT,
                    true,
                    NULL_ALLOWED
                ),
                'mini' => new external_value(
                    PARAM_BOOL,
                    'Whether to return the mini month view or not',
                    VALUE_DEFAULT,
                    false,
                    NULL_ALLOWED
                ),
                'day' => new external_value(PARAM_INT, 'Day to be viewed', VALUE_DEFAULT, 1),
            ]
        );
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function get_calendar_monthly_view_returns() {
        return \core_calendar\external\month_exporter::get_read_structure();
    }

    /**
     * Get data for the daily calendar view.
     *
     * @param   int     $year The year to be shown
     * @param   int     $month The month to be shown
     * @param   int     $day The day to be shown
     * @param   int     $courseid The course to be included
     * @return  array
     */
    public static function get_calendar_day_view($year, $month, $day, $courseid, $categoryid) {
        global $DB, $USER, $PAGE;

        // Parameter validation.
        $params = self::validate_parameters(self::get_calendar_day_view_parameters(), [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'courseid' => $courseid,
            'categoryid' => $categoryid,
        ]);

        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        $type = \core_calendar\type_factory::get_calendar_instance();

        $time = $type->convert_to_timestamp($params['year'], $params['month'], $params['day']);
        $calendar = \calendar_information::create($time, $params['courseid'], $params['categoryid']);
        self::validate_context($calendar->context);

        list($data, $template) = calendar_get_view($calendar, 'day');

        return $data;
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_calendar_day_view_parameters() {
        return new external_function_parameters(
            [
                'year' => new external_value(PARAM_INT, 'Year to be viewed', VALUE_REQUIRED),
                'month' => new external_value(PARAM_INT, 'Month to be viewed', VALUE_REQUIRED),
                'day' => new external_value(PARAM_INT, 'Day to be viewed', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'Course being viewed', VALUE_DEFAULT, SITEID, NULL_ALLOWED),
                'categoryid' => new external_value(PARAM_INT, 'Category being viewed', VALUE_DEFAULT, null, NULL_ALLOWED),
            ]
        );
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function get_calendar_day_view_returns() {
        return \core_calendar\external\calendar_day_exporter::get_read_structure();
    }


    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function update_event_start_day_parameters() {
        return new external_function_parameters(
            [
                'eventid' => new external_value(PARAM_INT, 'Id of event to be updated', VALUE_REQUIRED),
                'daytimestamp' => new external_value(PARAM_INT, 'Timestamp for the new start day', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Change the start day for the given calendar event to the day that
     * corresponds with the provided timestamp.
     *
     * The timestamp only needs to be anytime within the desired day as only
     * the date data is extracted from it.
     *
     * The event's original time of day is maintained, only the date is shifted.
     *
     * @param int $eventid Id of event to be updated
     * @param int $daytimestamp Timestamp for the new start day
     * @return  array
     */
    public static function update_event_start_day($eventid, $daytimestamp) {
        global $USER, $PAGE;

        // Parameter validation.
        $params = self::validate_parameters(self::update_event_start_day_parameters(), [
            'eventid' => $eventid,
            'daytimestamp' => $daytimestamp,
        ]);

        $vault = event_container::get_event_vault();
        $mapper = event_container::get_event_mapper();
        $event = $vault->get_event_by_id($eventid);

        if (!$event) {
            throw new \moodle_exception('Unable to find event with id ' . $eventid);
        }

        $legacyevent = $mapper->from_event_to_legacy_event($event);

        if (!calendar_edit_event_allowed($legacyevent, true)) {
            print_error('nopermissiontoupdatecalendar');
        }

        self::validate_context($legacyevent->context);

        $newdate = usergetdate($daytimestamp);
        $startdatestring = implode('-', [$newdate['year'], $newdate['mon'], $newdate['mday']]);
        $startdate = new DateTimeImmutable($startdatestring);
        $event = local_api::update_event_start_day($event, $startdate);
        $cache = new events_related_objects_cache([$event]);
        $relatedobjects = [
            'context' => $cache->get_context($event),
            'course' => $cache->get_course($event),
        ];
        $exporter = new event_exporter($event, $relatedobjects);
        $renderer = $PAGE->get_renderer('core_calendar');

        return array('event' => $exporter->export($renderer));
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function update_event_start_day_returns() {
        return new external_single_structure(
            array(
                'event' => event_exporter::get_read_structure()
            )
        );
    }

    /**
     * Get data for the monthly calendar view.
     *
     * @param   int     $courseid The course to be included
     * @param   int     $categoryid The category to be included
     * @return  array
     */
    public static function get_calendar_upcoming_view($courseid, $categoryid) {
        global $DB, $USER, $PAGE;

        // Parameter validation.
        $params = self::validate_parameters(self::get_calendar_upcoming_view_parameters(), [
            'courseid' => $courseid,
            'categoryid' => $categoryid,
        ]);

        $context = \context_user::instance($USER->id);
        self::validate_context($context);
        $PAGE->set_url('/calendar/');

        $calendar = \calendar_information::create(time(), $params['courseid'], $params['categoryid']);
        self::validate_context($calendar->context);

        list($data, $template) = calendar_get_view($calendar, 'upcoming');

        return $data;
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_calendar_upcoming_view_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course being viewed', VALUE_DEFAULT, SITEID, NULL_ALLOWED),
                'categoryid' => new external_value(PARAM_INT, 'Category being viewed', VALUE_DEFAULT, null, NULL_ALLOWED),
            ]
        );
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function get_calendar_upcoming_view_returns() {
        return \core_calendar\external\calendar_upcoming_exporter::get_read_structure();
    }


    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     * @since  Moodle 3.7
     */
    public static function get_calendar_access_information_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course to check, empty for site calendar events.', VALUE_DEFAULT, 0),
            ]
        );
    }

    /**
     * Convenience function to retrieve some permissions information for the given course calendar.
     *
     * @param int $courseid Course to check, empty for site.
     * @return array The access information
     * @throws moodle_exception
     * @since  Moodle 3.7
     */
    public static function get_calendar_access_information($courseid = 0) {

        $params = self::validate_parameters(self::get_calendar_access_information_parameters(), ['courseid' => $courseid]);

        if (empty($params['courseid']) || $params['courseid'] == SITEID) {
            $context = \context_system::instance();
        } else {
            $context = \context_course::instance($params['courseid']);
        }

        self::validate_context($context);

        return [
            'canmanageentries' => has_capability('moodle/calendar:manageentries', $context),
            'canmanageownentries' => has_capability('moodle/calendar:manageownentries', $context),
            'canmanagegroupentries' => has_capability('moodle/calendar:managegroupentries', $context),
            'warnings' => [],
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description.
     * @since  Moodle 3.7
     */
    public static function  get_calendar_access_information_returns() {

        return new external_single_structure(
            [
                'canmanageentries' => new external_value(PARAM_BOOL, 'Whether the user can manage entries.'),
                'canmanageownentries' => new external_value(PARAM_BOOL, 'Whether the user can manage its own entries.'),
                'canmanagegroupentries' => new external_value(PARAM_BOOL, 'Whether the user can manage group entries.'),
                'warnings' => new external_warnings(),
            ]
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters.
     * @since  Moodle 3.7
     */
    public static function get_allowed_event_types_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course to check, empty for site.', VALUE_DEFAULT, 0),
            ]
        );
    }

    /**
     * Get the type of events a user can create in the given course.
     *
     * @param int $courseid Course to check, empty for site.
     * @return array The types allowed
     * @throws moodle_exception
     * @since  Moodle 3.7
     */
    public static function get_allowed_event_types($courseid = 0) {

        $params = self::validate_parameters(self::get_allowed_event_types_parameters(), ['courseid' => $courseid]);

        if (empty($params['courseid']) || $params['courseid'] == SITEID) {
            $context = \context_system::instance();
        } else {
            $context = \context_course::instance($params['courseid']);
        }

        self::validate_context($context);

        $allowedeventtypes = array_filter(calendar_get_allowed_event_types($params['courseid']));

        return [
            'allowedeventtypes' => array_keys($allowedeventtypes),
            'warnings' => [],
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description.
     * @since  Moodle 3.7
     */
    public static function  get_allowed_event_types_returns() {

        return new external_single_structure(
            [
                'allowedeventtypes' => new external_multiple_structure(
                    new external_value(PARAM_NOTAGS, 'Allowed event types to be created in the given course.')
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }

    /**
     * Convert the specified dates into unix timestamps.
     *
     * @param   array $datetimes Array of arrays containing date time details, each in the format:
     *           ['year' => a, 'month' => b, 'day' => c,
     *            'hour' => d (optional), 'minute' => e (optional), 'key' => 'x' (optional)]
     * @return  array Provided array of dates converted to unix timestamps
     * @throws moodle_exception If one or more of the dates provided does not convert to a valid timestamp.
     */
    public static function get_timestamps($datetimes) {
        $params = self::validate_parameters(self::get_timestamps_parameters(), ['data' => $datetimes]);

        $type = \core_calendar\type_factory::get_calendar_instance();
        $timestamps = ['timestamps' => []];

        foreach ($params['data'] as $key => $datetime) {
            $hour = $datetime['hour'] ?? 0;
            $minute = $datetime['minute'] ?? 0;

            try {
                $timestamp = $type->convert_to_timestamp(
                    $datetime['year'], $datetime['month'], $datetime['day'], $hour, $minute);

                $timestamps['timestamps'][] = [
                    'key' => $datetime['key'] ?? $key,
                    'timestamp' => $timestamp,
                ];

            } catch (Exception $e) {
                throw new moodle_exception('One or more of the dates provided were invalid');
            }
        }

        return $timestamps;
    }

    /**
     * Describes the parameters for get_timestamps.
     *
     * @return external_function_parameters
     */
    public static function get_timestamps_parameters() {
        return new external_function_parameters ([
            'data' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'key' => new external_value(PARAM_ALPHANUMEXT, 'key', VALUE_OPTIONAL),
                        'year' => new external_value(PARAM_INT, 'year'),
                        'month' => new external_value(PARAM_INT, 'month'),
                        'day' => new external_value(PARAM_INT, 'day'),
                        'hour' => new external_value(PARAM_INT, 'hour', VALUE_OPTIONAL),
                        'minute' => new external_value(PARAM_INT, 'minute', VALUE_OPTIONAL),
                    ]
                )
            )
        ]);
    }

    /**
     * Describes the timestamps return format.
     *
     * @return external_single_structure
     */
    public static function get_timestamps_returns() {
        return new external_single_structure(
            [
                'timestamps' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'key' => new external_value(PARAM_ALPHANUMEXT, 'Timestamp key'),
                            'timestamp' => new external_value(PARAM_INT, 'Unix timestamp'),
                        ]
                    )
                )
            ]
        );
    }
}
