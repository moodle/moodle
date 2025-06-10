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

namespace local_intellidata\entities\cohorts;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'cohort_created' event is triggered.
     *
     * @param \core\event\cohort_created $event
     */
    public static function cohort_created(\core\event\cohort_created $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $cohort = $event->get_record_snapshot('cohort', $eventdata['objectid']);

            self::export_event($cohort, $eventdata);
        }
    }

    /**
     * Triggered when 'cohort_updated' event is triggered.
     *
     * @param \core\event\cohort_updated $event
     */
    public static function cohort_updated(\core\event\cohort_updated $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $cohort = $event->get_record_snapshot('cohort', $eventdata['objectid']);

            self::export_event($cohort, $eventdata);
        }
    }

    /**
     * Triggered when 'cohort_deleted' event is triggered.
     *
     * @param \core\event\cohort_deleted $event
     */
    public static function cohort_deleted(\core\event\cohort_deleted $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $cohort = new \stdClass();
            $cohort->id = $eventdata['objectid'];

            self::export_event($cohort, $eventdata);
        }
    }

    /**
     * Export data event.
     *
     * @param $cohortdata
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($cohortdata, $eventdata, $fields = []) {
        $cohortdata->crud = $eventdata['crud'];

        $entity = new cohort($cohortdata, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

}

