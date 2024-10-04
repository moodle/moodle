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

namespace customfield_number;

use customfield_number\local\numberproviders\nofactivities;
use customfield_number\task\recalculate;

/**
 * Event observers for customfield_number
 *
 * @package    customfield_number
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * When a 'number' custom field was created, schedule recalculation for the field data
     *
     * @param \core_customfield\event\field_created $event
     */
    public static function field_created(\core_customfield\event\field_created $event): void {
        $field = $event->get_record_snapshot('customfield_field', $event->objectid);
        if ($field->type === 'number') {
            recalculate::schedule_for_field($event->objectid);
        }
    }

    /**
     * When a 'number' custom field was updated, schedule recalculation for the field data
     *
     * @param \core_customfield\event\field_updated $event
     */
    public static function field_updated(\core_customfield\event\field_updated $event): void {
        $field = $event->get_record_snapshot('customfield_field', $event->objectid);
        if ($field->type === 'number') {
            recalculate::schedule_for_field($event->objectid);
        }
    }

    /**
     * When a course module was created, schedule recalculation for all 'nofactivities' custom fields
     *
     * @param \core\event\course_module_created $event
     */
    public static function course_module_created(\core\event\course_module_created $event): void {
        if (self::has_nofactivities_fields()) {
            recalculate::schedule_for_fieldtype(fieldtype: nofactivities::class,
                component: 'core_course', area: 'course', instanceid: $event->courseid);
        }
    }

    /**
     * When a course module was deleted, schedule recalculation for all 'nofactivities' custom fields
     *
     * @param \core\event\course_module_deleted $event
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event): void {
        if (self::has_nofactivities_fields()) {
            recalculate::schedule_for_fieldtype(fieldtype: nofactivities::class,
                component: 'core_course', area: 'course', instanceid: $event->courseid);
        }
    }

    /**
     * When a course module was updated, schedule recalculation for all 'nofactivities' custom fields
     *
     * Module visibility may change following an 'updated' event and it will affect the activities count
     *
     * @param \core\event\course_module_updated $event
     */
    public static function course_module_updated(\core\event\course_module_updated $event): void {
        if (self::has_nofactivities_fields()) {
            recalculate::schedule_for_fieldtype(fieldtype: nofactivities::class,
                component: 'core_course', area: 'course', instanceid: $event->courseid);
        }
    }

    /**
     * Checks if a 'number' field with 'nofactivities' provider exists in the course fields
     *
     * This method is very fast, it only performs one DB query and only once per request
     *
     * @return bool
     */
    protected static function has_nofactivities_fields(): bool {
        $handler = \core_course\customfield\course_handler::create();
        foreach ($handler->get_categories_with_fields() as $category) {
            foreach ($category->get_fields() as $field) {
                if ($field->get('type') === 'number' &&
                        $field->get_configdata_property('fieldtype') === nofactivities::class) {
                    return true;
                }
            }
        }
        return false;
    }
}
