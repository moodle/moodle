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
 * Contains event class for providing the related objects when exporting a list of calendar events.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use \core_calendar\local\event\entities\event_interface;

/**
 * Class to providing the related objects when exporting a list of calendar events.
 *
 * This class is only meant for use with exporters. It attempts to bulk load
 * the related objects for a list of events and cache them to avoid having
 * to query the database when exporting each individual event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_related_objects_cache {

    /**
     * @var array $events The events for which we need related objects.
     */
    protected $events;

    /**
     * @var array $courses The related courses.
     */
    protected $courses = null;

    /**
     * @var array $groups The related groups.
     */
    protected $groups = null;

    /**
     * @var array $coursemodules The related course modules.
     */
    protected $coursemodules = [];

    /**
     * @var array $moduleinstances The related module instances.
     */
    protected $moduleinstances = null;

    /**
     * Constructor.
     *
     * @param array $events Array of event_interface events
     * @param array $courses Array of courses to populate the cache with
     */
    public function __construct(array $events, ?array $courses = null) {
        $this->events = $events;

        if (!is_null($courses)) {
            $this->courses = [];

            foreach ($courses as $course) {
                $this->courses[$course->id] = $course;
            }
        }
    }

    /**
     * Get the related course object for a given event.
     *
     * @param event_interface $event The event object.
     * @return \stdClass|null
     */
    public function get_course(event_interface $event) {
        if (is_null($this->courses)) {
            $this->load_courses();
        }

        if ($course = $event->get_course()) {
            $courseid = $course->get('id');
            return isset($this->courses[$courseid]) ? $this->courses[$courseid] : null;
        } else {
            return null;
        }
    }

    /**
     * Get the related context for a given event.
     *
     * @param event_interface $event The event object.
     * @return \context|null
     */
    public function get_context(event_interface $event) {
        global $USER;

        $categoryid = $event->get_category() ? $event->get_category()->get('id') : null;
        $courseid = $event->get_course() ? $event->get_course()->get('id') : null;
        $groupid = $event->get_group() ? $event->get_group()->get('id') : null;
        $userid = $event->get_user() ? $event->get_user()->get('id') : null;
        $moduleid = $event->get_course_module() ? $event->get_course_module()->get('id') : null;

        if (!empty($categoryid)) {
            return \context_coursecat::instance($categoryid);
        } else if (!empty($courseid)) {
            return \context_course::instance($event->get_course()->get('id'));
        } else if (!empty($groupid)) {
            $group = $this->get_group($event);
            return \context_course::instance($group->courseid);
        } else if (!empty($userid) && $userid == $USER->id) {
            return \context_user::instance($userid);
        } else if (!empty($userid) && $userid != $USER->id && $moduleid && $moduleid > 0) {
            $cm = $this->get_course_module($event);
            return \context_course::instance($cm->course);
        } else {
            return \context_user::instance($userid);
        }
    }

    /**
     * Get the related group object for a given event.
     *
     * @param event_interface $event The event object.
     * @return \stdClass|null
     */
    public function get_group(event_interface $event) {
        if (is_null($this->groups)) {
            $this->load_groups();
        }

        if ($group = $event->get_group()) {
            $groupid = $group->get('id');
            return isset($this->groups[$groupid]) ? $this->groups[$groupid] : null;
        } else {
            return null;
        }
    }

    /**
     * Get the related course module for a given event.
     *
     * @param event_interface $event The event object.
     * @return \stdClass|null
     */
    public function get_course_module(event_interface $event) {
        if (!$event->get_course_module()) {
            return null;
        }

        $id = $event->get_course_module()->get('id');
        $name = $event->get_course_module()->get('modname');
        $key = $name . '_' . $id;

        if (!isset($this->coursemodules[$key])) {
            $this->coursemodules[$key] = get_coursemodule_from_instance($name, $id, 0, false, MUST_EXIST);
        }

        return $this->coursemodules[$key];
    }

    /**
     * Get the related module instance for a given event.
     *
     * @param event_interface $event The event object.
     * @return \stdClass|null
     */
    public function get_module_instance(event_interface $event) {
        if (!$event->get_course_module()) {
            return null;
        }

        if (is_null($this->moduleinstances)) {
            $this->load_module_instances();
        }

        $id = $event->get_course_module()->get('instance');
        $name = $event->get_course_module()->get('modname');

        if (isset($this->moduleinstances[$name])) {
            if (isset($this->moduleinstances[$name][$id])) {
                return $this->moduleinstances[$name][$id];
            }
        }

        return null;
    }

    /**
     * Load the list of all of the distinct courses required for the
     * list of provided events and save the result in memory.
     */
    protected function load_courses() {
        global $DB;

        $courseids = [];
        foreach ($this->events as $event) {
            if ($course = $event->get_course()) {
                $id = $course->get('id');
                $courseids[$id] = true;
            }
        }

        if (empty($courseids)) {
            $this->courses = [];
            return;
        }

        list($idsql, $params) = $DB->get_in_or_equal(array_keys($courseids));
        $sql = "SELECT * FROM {course} WHERE id {$idsql}";

        $this->courses = $DB->get_records_sql($sql, $params);
    }

    /**
     * Load the list of all of the distinct groups required for the
     * list of provided events and save the result in memory.
     */
    protected function load_groups() {
        global $DB;

        $groupids = [];
        foreach ($this->events as $event) {
            if ($group = $event->get_group()) {
                $id = $group->get('id');
                $groupids[$id] = true;
            }
        }

        if (empty($groupids)) {
            $this->groups = [];
            return;
        }

        list($idsql, $params) = $DB->get_in_or_equal(array_keys($groupids));
        $sql = "SELECT * FROM {groups} WHERE id {$idsql}";

        $this->groups = $DB->get_records_sql($sql, $params);
    }

    /**
     * Load the list of all of the distinct module instances required for the
     * list of provided events and save the result in memory.
     */
    protected function load_module_instances() {
        global $DB;

        $this->moduleinstances = [];
        $modulestoload = [];
        foreach ($this->events as $event) {
            if ($module = $event->get_course_module()) {
                $id = $module->get('instance');
                $name = $module->get('modname');

                $ids = isset($modulestoload[$name]) ? $modulestoload[$name] : [];
                $ids[$id] = true;
                $modulestoload[$name] = $ids;
            }
        }

        if (empty($modulestoload)) {
            return;
        }

        foreach ($modulestoload as $modulename => $ids) {
            list($idsql, $params) = $DB->get_in_or_equal(array_keys($ids));
            $sql = "SELECT * FROM {" . $modulename . "} WHERE id {$idsql}";
            $this->moduleinstances[$modulename] = $DB->get_records_sql($sql, $params);
        }
    }
}
