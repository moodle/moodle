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

namespace local_intellidata\entities\groupmembers;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'group_member_removed' event is triggered.
     *
     * @param \core\event\group_member_removed $event
     */
    public static function group_member_removed(\core\event\group_member_removed $event) {
        if (TrackingHelper::enabled()) {

            self::export_event($event->get_data());
        }
    }

    /**
     * Triggered when 'group_member_added' event is triggered.
     *
     * @param \core\event\group_member_added $event
     */
    public static function group_member_added(\core\event\group_member_added $event) {
        if (TrackingHelper::eventstracking_enabled()) {

            self::export_event($event->get_data());
        }
    }

    /**
     * Export event.
     *
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($eventdata, $fields = []) {
        global $DB;

        $groupdata = $DB->get_record('groups_members', [
            'groupid' => $eventdata['objectid'],
            'userid' => $eventdata['relateduserid'],
        ]);

        if (!$groupdata) {
            $groupdata = new \stdClass();
            $groupdata->groupid = $eventdata['objectid'];
            $groupdata->userid = $eventdata['relateduserid'];
        }
        $groupdata->crud = $eventdata['crud'];

        $entity = new groupmember($groupdata, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }
}

