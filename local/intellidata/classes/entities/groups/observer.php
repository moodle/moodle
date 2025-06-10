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

namespace local_intellidata\entities\groups;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'group_created' event is triggered.
     *
     * @param \core\event\group_created $event
     */
    public static function group_created(\core\event\group_created $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $group = $event->get_record_snapshot('groups', $eventdata['objectid']);

            self::export_event($group, $eventdata);
        }
    }

    /**
     * Triggered when 'group_updated' event is triggered.
     *
     * @param \core\event\group_updated $event
     */
    public static function group_updated(\core\event\group_updated $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $group = $event->get_record_snapshot('groups', $eventdata['objectid']);

            self::export_event($group, $eventdata);
        }
    }

    /**
     * Triggered when 'group_deleted' event is triggered.
     *
     * @param \core\event\group_deleted $event
     */
    public static function group_deleted(\core\event\group_deleted $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $group = new \stdClass();
            $group->id = $eventdata['objectid'];

            self::export_event($group, $eventdata);
        }
    }

    /**
     * Export event.
     *
     * @param $groupdata
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($groupdata, $eventdata, $fields = []) {
        $groupdata->crud = $eventdata['crud'];

        $entity = new group($groupdata, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }
}

