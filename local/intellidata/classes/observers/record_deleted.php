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

namespace local_intellidata\observers;

use local_intellidata\entities\custom\entity;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\EventsHelper;
use local_intellidata\services\events_service;
use local_intellidata\repositories\export_log_repository;

/**
 * Event observer for deletedrecords.
 */
class record_deleted {

    /**
     * Executes when any event is triggered.
     *
     * @param \core\event\base $event
     */
    public static function execute(\core\event\base $event) {

        // Check if there is framework. For MWP.
        $frameworkid = false;
        if (!empty($event->other['frameworkid'])) {
            $frameworkid = $event->other['frameworkid'];
        }

        // Process only deleted events which includes objecttable and objectid.
        if ($event->crud != EventsHelper::CRUD_DELETED || empty($event->objecttable) ||
            (!$frameworkid && empty($event->objectid))) {
            return;
        }

        $exportdeletedrecords = (int)SettingsHelper::get_setting('exportdeletedrecords');
        if (TrackingHelper::enabled() && $exportdeletedrecords == SettingsHelper::EXPORTDELETED_TRACKEVENTS) {

            $eventdata = $event->get_data();
            if ($frameworkid && empty($event->objectid)) {
                $eventdata['objectid'] = $frameworkid;
            }

            $exportlogrepository = new export_log_repository();

            $datatype = $exportlogrepository->get_datatype_from_event($eventdata['eventname']);

            if (!empty($datatype)) {
                self::export_event($datatype, $eventdata);
            }
        }
    }

    /**
     * Export event.
     *
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($datatype, $eventdata) {

        $record = new \stdClass();
        $record->id = $eventdata['objectid'];
        $record->crud = $eventdata['crud'];

        $entity = new entity($datatype, $record);
        $data = $entity->export();

        $tracking = new events_service($datatype);
        $tracking->track($data);
    }

}
