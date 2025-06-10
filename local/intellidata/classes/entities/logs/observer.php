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
 * @copyright  2021 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\logs;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\services\events_service;

/**
 * Event observer for participations.
 */
class observer {

    /**
     * Triggered when any event is triggered.
     *
     * @param \core\event\base $event
     */
    public static function log_created(\core\event\base $event) {
        if (TrackingHelper::enabled() && TrackingHelper::trackinglogs_enabled()) {
            $eventdata = $event->get_data();

            $exportlogrepository = new export_log_repository();
            $datatypes = $exportlogrepository->get_logs_datatypes_with_config();

            if (!count($datatypes)) {
                return;
            }

            foreach ($datatypes as $datatype) {
                 self::process_datatype($datatype, $eventdata);
            }
        }
    }

    /**
     * Process single datatype.
     *
     * @param $datatype
     * @param $eventdata
     * @throws \core\invalid_persistent_exception
     */
    private static function process_datatype($datatype, $eventdata) {
        $trackevent = true; $empty = 0;

        // Ignrore if no params.
        if (!$datatype->params) {
            return;
        }

        foreach ($datatype->params as $paramname => $paramvalue) {
            if (!empty($paramvalue) && $eventdata[$paramname] != $paramvalue) {
                $trackevent = false;
            } else if (empty($paramvalue)) {
                $empty++;
            }
        }

        if ($trackevent && $empty != count((array)$datatype->params)) {
            self::export_event($datatype->datatype, $eventdata);
        }
    }

    /**
     * Export event.
     *
     * @param $datatypename
     * @param $eventdata
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($datatypename, $eventdata) {
        $record = (object)$eventdata;
        $record->other = (is_array($record->other)) ? json_encode($record) : $record->other;

        $entity = new log($record, []);
        $data = $entity->export();

        $tracking = new events_service($datatypename);
        $tracking->track($data);
    }

}
