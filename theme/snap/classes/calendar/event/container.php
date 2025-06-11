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
 * Wrap core Moodle class.
 *
 * @package    theme_snap
 * @copyright  2017 Open LMS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\calendar\event;
use theme_snap\calendar\event\data_access\event_vault;
use theme_snap\calendar\event\strategies\activity_retrieval_strategy;
use core_calendar\action_factory;
use core_calendar\local\event\entities\event_interface;
use core_calendar\local\event\factories\event_factory;
use core_calendar\local\event\mappers\event_mapper;

/**
 * Snap event container.
 *
 * @copyright 2017 Open LMS
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class container extends \core_calendar\local\event\container {

    /**
     * Initialises the dependency graph if it hasn't yet been.
     */
    public static function ovd_init() {
        if (empty(self::$eventvault) || get_class(self::$eventvault) != 'theme_snap\calendar\event\data_access\event_vault') {
            self::$eventvault = false;
            self::$actionfactory = new action_factory();
            self::$eventmapper = new event_mapper(
                // The event mapper we return from here needs to know how to
                // make events, so it needs an event factory. However we can't
                // give it the same one as we store and return in the container
                // as that one uses all our plumbing to control event visibility.
                //
                // So we make a new even factory that doesn't do anyting other than
                // return the instance.
                new event_factory(
                    // Never apply actions, simply return.
                    function(event_interface $event) {
                        return $event;
                    },
                    // Never hide an event.
                    function() {
                        return true;
                    },
                    // Never bail out early when instantiating an event.
                    function() {
                        return false;
                    },
                    self::$coursecache,
                    self::$modulecache
                )
            );

            self::$eventfactory = new event_factory(
                // @codingStandardsIgnoreStart
                [self::class, 'apply_component_provide_event_action'],
                [self::class, 'apply_component_is_event_visible'],
                // @codingStandardsIgnoreEnd
                function ($dbrow) {
                    if (!empty($dbrow->categoryid)) {
                        // This is a category event. Check that the category is visible to this user.
                        $category = \core_course_category::get($dbrow->categoryid, IGNORE_MISSING, true);

                        if (empty($category) || !$category->is_uservisible()) {
                            return true;
                        }
                    }

                    // At present we only have a bail-out check for events in course modules.
                    if (empty($dbrow->modulename)) {
                        return false;
                    }

                    $instances = get_fast_modinfo($dbrow->courseid)->instances;

                    // If modinfo doesn't know about the module, we should ignore it.
                    if (!isset($instances[$dbrow->modulename]) || !isset($instances[$dbrow->modulename][$dbrow->instance])) {
                        return true;
                    }

                    $cm = $instances[$dbrow->modulename][$dbrow->instance];

                    // If the module is not visible to the current user, we should ignore it.
                    // We have to check enrolment here as well because the uservisible check
                    // looks for the "view" capability however some activities (such as Lesson)
                    // have that capability set on the "Authenticated User" role rather than
                    // on "Student" role, which means uservisible returns true even when the user
                    // is no longer enrolled in the course.
                    // So, with the following we are checking -
                    // 1) Only process modules if $cm->uservisible is true.
                    // 2) Only process modules for courses a user has the capability to view OR they are enrolled in.
                    // 3) Only process modules for courses that are visible OR if the course is not visible, the user
                    // has the capability to view hidden courses.
                    $coursecontext = \context_course::instance($dbrow->courseid);
                    $canseecourse = has_capability('moodle/course:view', $coursecontext) || is_enrolled($coursecontext);
                    $canseecourse = $canseecourse &&
                        ($cm->get_course()->visible || has_capability('moodle/course:viewhiddencourses', $coursecontext));
                    if (!$cm->uservisible || !$canseecourse) {
                        return true;
                    }

                    // Ok, now check if we are looking at a completion event.
                    if ($dbrow->eventtype === \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED) {
                        // Need to have completion enabled before displaying these events.
                        $course = new \stdClass();
                        $course->id = $dbrow->courseid;
                        $completion = new \completion_info($course);

                        return (bool) !$completion->is_enabled($cm);
                    }

                    return false;
                },
                self::$coursecache,
                self::$modulecache
            );
        }

        if (empty(self::$eventvault)) {
            // Use snap retrieval strategy.
            self::$eventretrievalstrategy = new activity_retrieval_strategy();
            self::$eventvault = new event_vault(self::$eventfactory, self::$eventretrievalstrategy);
        }
    }

}
