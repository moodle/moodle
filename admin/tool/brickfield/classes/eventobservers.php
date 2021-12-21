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

namespace tool_brickfield;

use core\event\base;
use stdClass;

/**
 * Observer class containing methods monitoring various events.
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class eventobservers {

    /**
     * For course events to be handled, the accessibility tool needs to be enabled, and if a courseid is specified, the course
     * has to have been added to the scheduler.
     * @param int $courseid
     * @return bool
     * @throws \dml_exception
     */
    private static function course_event_should_be_handled(int $courseid): bool {
        return accessibility::is_accessibility_enabled() && analysis::is_enabled() &&
            (empty($courseid) || (isset($courseid) && scheduler::is_course_in_schedule($courseid)));
    }

    /**
     * Content area altered event observer.
     * @param base $event The area altered event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    private static function area_altered(base $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            manager::find_new_or_updated_areas($event);
        }
    }

    /**
     * Course event observers.
     * These observer monitors course created / restored / updated events,
     * then its HTML content is processed with accessibility checking.
     */

    /**
     * Course created event observer.
     * @param \core\event\course_created $event The course created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_created(\core\event\course_created $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            // Need to trigger rerun check for automatically created forum...
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->item = 'coursererun';
            static::observer_insert($data);
            static::course_altered($event);
        }
    }

    /**
     * Course deleted event observer.
     * @param \core\event\course_deleted $event The course deleted event.
     * @throws \dml_exception
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->contextid = -1;
            $data->item = 'core_course';
            static::observer_insert($data);
        }
    }

    /**
     * Course restored event observer.
     * @param \core\event\course_restored $event The course restored event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_restored(\core\event\course_restored $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->item = 'coursererun';
            static::observer_insert($data);
            static::course_altered($event);
        }
    }

    /**
     * Course update event observer.
     * @param \core\event\course_updated $event The course updated event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_updated(\core\event\course_updated $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::course_altered($event);
        }
    }

    /**
     * Course update event observer. This is called on both course_created and course_updated, so use the base class as a type hint.
     * @param base $event The course updated event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_altered(base $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            // Need to rerun caching of allowed courseid, from category white/blacklist.
            $data = new stdClass();
            $data->courseid = $data->contextid = $event->courseid;
            $data->contextid = -1;
            $data->item = 'category';
            static::observer_insert($data);
            static::area_altered($event);
        }
    }

    /**
     * Course section created event observer.
     * @param \core\event\course_section_created $event The course section created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_section_created(\core\event\course_section_created $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * Course section deleted event observer.
     * @param \core\event\course_section_deleted $event The course section deleted event.
     * @throws \dml_exception
     */
    public static function course_section_deleted(\core\event\course_section_deleted $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->contextid = -1;
            $data->innercontextid = $event->objectid;
            $data->item = 'course_sections';
            static::observer_insert($data);
        }
    }

    /**
     * Course section update event observer.
     * @param \core\event\course_section_updated $event The course section updated event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_section_updated(\core\event\course_section_updated $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * Course module event observers.
     * These observer monitors course module created / restored / updated events,
     * then its HTML content is processed with accessibility checking.
     */

    /**
     * Course module created event observer.
     * @param \core\event\course_module_created $event The course module created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * Course module deleted event observer.
     * @param \core\event\course_module_deleted $event The course module deleted event.
     * @throws \dml_exception
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->contextid = -1;
            $data->innercontextid = $event->other['instanceid'];
            $data->item = 'mod_' . $event->other['modulename'];
            static::observer_insert($data);
        }
    }

    /**
     * Course module restored event observer.
     * @param \core\event\course_module_restored $event The course module restored event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_module_restored(\core\event\course_module_restored $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * Course module updated event observer.
     * @param \core\event\course_module_updated $event The course module updated event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_module_updated(\core\event\course_module_updated $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * course_category created event observer.
     * @param \core\event\course_category_created $event The course_category created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_category_created(\core\event\course_category_created $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * course_category deleted event observer.
     * @param \core\event\course_category_deleted $event The course_category deleted event.
     * @throws \dml_exception
     */
    public static function course_category_deleted(\core\event\course_category_deleted $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            $data = new stdClass();
            $data->contextid = $data->courseid = -1;
            $data->innercontextid = $event->objectid;
            $data->item = 'course_categories';
            static::observer_insert($data);
        }
    }

    /**
     * course_category update event observer.
     * @param \core\event\course_category_updated $event The course_category updated event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function course_category_updated(\core\event\course_category_updated $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * mod_lesson_page created event observer.
     * @param \mod_lesson\event\page_created $event The mod_lesson page created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function mod_lesson_page_created(\mod_lesson\event\page_created $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * mod_lesson_page deleted event observer.
     * @param \mod_lesson\event\page_deleted $event The mod_lesson page deleted event.
     * @throws \dml_exception
     */
    public static function mod_lesson_page_deleted(\mod_lesson\event\page_deleted $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->contextid = -1;
            $data->innercontextid = $event->objectid;
            $data->item = 'lesson_pages';
            static::observer_insert($data);
        }
    }

    /**
     * mod_lesson_page updated event observer.
     * @param \mod_lesson\event\page_updated $event The mod_lesson page updated event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function mod_lesson_page_updated(\mod_lesson\event\page_updated $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            static::area_altered($event);
        }
    }

    /**
     * core_question created observer
     * @param \core\event\question_created $event The core_question created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function core_question_created(\core\event\question_created $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            manager::find_new_or_updated_areas($event);
        }
    }

    /**
     * core_question updated observer
     * @param \core\event\question_updated $event The core_question created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function core_question_updated(\core\event\question_updated $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            manager::find_new_or_updated_areas($event);
        }
    }

    /**
     * core_question deleted observer
     * @param \core\event\question_deleted $event The core_question deleted event.
     * @throws \dml_exception
     */
    public static function core_question_deleted(\core\event\question_deleted $event) {
        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($event->courseid)) {
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->contextid = -1;
            $data->innercontextid = $event->objectid;
            $data->item = $event->objecttable;
            static::observer_insert($data);
        }
    }

    /**
     * Book chapter created event observer.
     * @param \mod_book\event\chapter_created $event The book chapter created event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function book_chapter_created(\mod_book\event\chapter_created $event) {
        // If this feature has been disabled, do nothing.
        if (accessibility::is_accessibility_enabled()) {
            static::area_altered($event);
        }
    }

    /**
     * Book chapter deleted event observer.
     * @param \mod_book\event\chapter_deleted $event The book chapter deleted event.
     * @throws \dml_exception
     */
    public static function book_chapter_deleted(\mod_book\event\chapter_deleted $event) {
        // If this feature has been disabled, do nothing.
        if (accessibility::is_accessibility_enabled()) {
            $data = new stdClass();
            $data->courseid = $event->courseid;
            $data->contextid = -1;
            $data->innercontextid = $event->objectid;
            $data->item = 'book_chapters';
            static::observer_insert($data);
        }
    }

    /**
     * Book chapter update event observer.
     * @param \mod_book\event\chapter_updated $event The book chapter updated event.
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function book_chapter_updated(\mod_book\event\chapter_updated $event) {
        // If this feature has been disabled, do nothing.
        if (accessibility::is_accessibility_enabled()) {
            static::area_altered($event);
        }
    }

    /**
     * Add an observer record if appropriate.
     * @param stdClass $data
     * @throws \dml_exception
     */
    private static function observer_insert(stdClass $data) {
        global $DB;

        // Handle if this feature is enabled and this course is in the schedule.
        if (static::course_event_should_be_handled($data->courseid)) {
            $data->timecreated = time();
            $data->timecompleted = 0;

            $DB->insert_record(manager::DB_PROCESS, $data);
        }
    }
}
