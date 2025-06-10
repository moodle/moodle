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

namespace local_intellidata\entities\usergrades;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;
use local_intellidata\task\export_adhoc_task;
use core\task\manager;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'course_completed' event is triggered.
     *
     * @param \core\event\user_graded $event
     */
    public static function user_graded(\core\event\user_graded $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $gradeobject = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);

            $data = usergrade::prepare_export_data($gradeobject);

            self::export_event($data, $eventdata);
        }
    }

    /**
     * Triggered when 'grade_letter' event is triggered.
     *
     * @param \core\event\grade_letter_updated $event
     */
    public static function grade_letter_updated(\core\event\grade_letter_updated $event) {
        self::create_export_task();
    }

    /**
     * Triggered when 'grade_letter' event is triggered.
     *
     * @param \core\event\grade_letter_deleted $event
     */
    public static function grade_letter_deleted(\core\event\grade_letter_deleted $event) {
        self::create_export_task();
    }

    /**
     * Triggered when 'grade_letter' event is triggered.
     *
     * @param \core\event\grade_letter_created $event
     */
    public static function grade_letter_created(\core\event\grade_letter_created $event) {
        self::create_export_task();
    }

    /**
     * Create export task.
     *
     * @return void
     */
    public static function create_export_task() {
        $exporttask = new export_adhoc_task();
        $exporttask->set_custom_data([
            'datatypes' => [usergrade::TYPE],
            'callbackurl' => '',
        ]);

        manager::queue_adhoc_task($exporttask);
    }

    /**
     * Export data event.
     *
     * @param $data
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($data, $eventdata, $fields = []) {
        $data->crud = $eventdata['crud'];

        $entity = new usergrade($data, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }
}

