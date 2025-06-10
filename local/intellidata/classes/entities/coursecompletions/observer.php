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
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\coursecompletions;



use local_intellidata\entities\coursecompletions\coursecompletion;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'course_completed' event is triggered.
     *
     * @param \core\event\course_completed $event
     */
    public static function course_completed(\core\event\course_completed $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $completion = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);

            $data = new \stdClass();
            $data->courseid = $eventdata['courseid'];
            $data->userid = $eventdata['relateduserid'];
            $data->timecompleted = $completion->timecompleted;
            $data->id = $eventdata['objectid'];

            self::export_event($data, $eventdata);
        }
    }

    /**
     * Triggered when 'course_completion_updated' event is triggered.
     *
     * @param \core\event\course_completion_updated $event
     */
    public static function course_completion_updated(\core\event\course_completion_updated $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $data = new \stdClass();
            $data->courseid = $eventdata['courseid'];
            $data->id = $eventdata['objectid'];

            self::export_event($data, $eventdata);
        }
    }

    /**
     * Export event data.
     *
     * @param $data
     * @param $eventdata
     * @param $fields
     * @return void
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($data, $eventdata, $fields = []) {
        $data->crud = $eventdata['crud'];

        $entity = new coursecompletion($data, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

}

