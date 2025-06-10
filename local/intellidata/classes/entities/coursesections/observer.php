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
 * Observer
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2023 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\coursesections;

use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'course_module_created' event is triggered.
     *
     * @param \core\event\course_module_created $event
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $cm = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            if ($cm && $cm->section) {
                $section = $event->get_record_snapshot('course_sections', $cm->section);
                self::generate_section_name($section);

                self::export_event($section, ['crud' => 'u']);
            }
        }
    }

    /**
     * Triggered when 'course_section_created' event is triggered.
     *
     * @param \core\event\course_section_created $event
     */
    public static function course_section_created(\core\event\course_section_created $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $section = $event->get_record_snapshot('course_sections', $eventdata['objectid']);
            self::generate_section_name($section, true);

            self::export_event($section, $eventdata);
        }
    }

    /**
     * Triggered when 'course_section_updated' event is triggered.
     *
     * @param \core\event\course_section_updated $event
     */
    public static function course_section_updated(\core\event\course_section_updated $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $section = $event->get_record_snapshot('course_sections', $eventdata['objectid']);
            self::generate_section_name($section);

            self::export_event($section, $eventdata);
        }
    }

    /**
     * Triggered when 'course_section_deleted' event is triggered.
     *
     * @param \core\event\course_section_deleted $event
     */
    public static function course_section_deleted(\core\event\course_section_deleted $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $section = new \stdClass();
            $section->id = $eventdata['objectid'];

            self::export_event($section, $eventdata);
        }
    }

    /**
     * Export event.
     *
     * @param $sectiondata
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($sectiondata, $eventdata, $fields = []) {
        $sectiondata->crud = $eventdata['crud'];

        $entity = new sections($sectiondata, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

    /**
     * Generate default section name when section name is empty.
     *
     * @param \stdClass $section
     *
     * @return void
     */
    private static function generate_section_name(&$section, $default = false) {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');
        if (empty($section->name)) {
            try {
                $section->name = !$default ? get_section_name($section->course, $section->section) :
                    course_get_format($section->course)->get_default_section_name($section);
            } catch (\Exception $e) {
                DebugHelper::error_log($e->getMessage());
            }
        }
    }
}

