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

namespace local_intellidata\entities\participations;

use local_intellidata\helpers\TrackingHelper;
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
    public static function new_participation(\core\event\base $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            if (in_array($eventdata['crud'], ['c', 'u']) && $eventdata['userid'] &&
                in_array($eventdata['contextlevel'], [CONTEXT_COURSE, CONTEXT_MODULE])) {
                $record = new \stdClass();
                $record->userid = $eventdata['userid'];
                $record->type = ($eventdata['contextlevel'] == CONTEXT_MODULE) ? 'activity' : 'course';
                $record->objectid = $eventdata['contextinstanceid'];
                $record->participations = 1;
                $record->last_participation = time();

                self::export_event($record, $eventdata);
            }
        }
    }

    /**
     * Export event data.
     *
     * @param $record
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($record, $eventdata, $fields = []) {
        $record->crud = $eventdata['crud'];

        $entity = new participation($record, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }
}

