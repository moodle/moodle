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

namespace local_intellidata\entities\userinfocategories;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'user_info_category_created' event is triggered.
     *
     * @param \core\event\user_info_category_created $event
     */
    public static function user_info_category_created(\core\event\user_info_category_created $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $userinfocategory = $event->get_record_snapshot('user_info_category', $eventdata['objectid']);

            self::export_event($userinfocategory, $eventdata);
        }
    }

    /**
     * Triggered when 'user_info_category_updated' event is triggered.
     *
     * @param \core\event\user_info_category_updated $event
     */
    public static function user_info_category_updated(\core\event\user_info_category_updated $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $userinfocategory = $event->get_record_snapshot('user_info_category', $eventdata['objectid']);

            self::export_event($userinfocategory, $eventdata);
        }
    }

    /**
     * Triggered when 'user_info_category_deleted' event is triggered.
     *
     * @param \core\event\user_info_category_deleted $event
     */
    public static function user_info_category_deleted(\core\event\user_info_category_deleted $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $userinfocategory = new \stdClass();
            $userinfocategory->id = $eventdata['objectid'];

            self::export_event($userinfocategory, $eventdata);
        }
    }

    /**
     * Export event method.
     *
     * @param $userinfocategory
     * @param $eventdata
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($userinfocategory, $eventdata, $fields = []) {
        $userinfocategory->crud = $eventdata['crud'];

        $entity = new userinfocategory($userinfocategory, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

}
