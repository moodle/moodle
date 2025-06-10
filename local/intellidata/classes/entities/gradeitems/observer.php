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

namespace local_intellidata\entities\gradeitems;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'grade_item_deleted' event is triggered.
     *
     * @param \core\event\grade_item_deleted $event
     */
    public static function grade_item_deleted(\core\event\grade_item_deleted $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $gradeitem = new \stdClass();
            $gradeitem->id = $eventdata['objectid'];

            self::export_event($gradeitem, $eventdata);
        }
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

        $entity = new gradeitem($data, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

}
