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
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\usertrackings;

use local_intellidata\helpers\TrackingHelper;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'user_deleted' event is triggered.
     *
     * @param \core\event\user_deleted $event
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        global $DB;

        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();
            $userid = $eventdata['objectid'];

            $items = $DB->get_records("local_intellidata_tracking", ['userid' => $userid]);

            foreach ($items as $item) {
                $logs = $DB->get_records("local_intellidata_logs", ['trackid' => $item->id]);

                foreach ($logs as $log) {
                    $DB->delete_records('local_intellidata_trdetails', [
                        'logid' => $log->id,
                    ]);
                }
                $DB->delete_records('local_intellidata_trlogs', [
                    'trackid' => $item->id,
                ]);
            }
            $DB->delete_records('local_intellidata_tracking', [
                'userid' => $userid,
            ]);
        }
    }

    /**
     * Triggered when 'course_deleted' event is triggered.
     *
     * @param \core\event\course_deleted $event
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        global $DB;
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();
            $params = [
                'courseid' => $eventdata['objectid'],
            ];
            $items = $DB->get_records("local_intellidata_tracking", $params);

            foreach ($items as $item) {
                $logs = $DB->get_records("local_intellidata_trlogs", ['trackid' => $item->id]);

                foreach ($logs as $log) {
                    $DB->delete_records('local_intellidata_trdetails', [
                        'logid' => $log->id,
                    ]);
                }
                $DB->delete_records('local_intellidata_trlogs', [
                    'trackid' => $item->id,
                ]);
            }
            $DB->delete_records('local_intellidata_tracking', $params);
        }
    }

    /**
     * Triggered when 'course_module_deleted' event is triggered.
     *
     * @param \core\event\course_module_deleted $event
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        global $DB;
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();
            $cm = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);

            $params = [
                'page' => 'module',
                'param' => $cm->id,
            ];

            $items = $DB->get_records("local_intellidata_tracking", $params);

            foreach ($items as $item) {
                $logs = $DB->get_records("local_intellidata_trlogs", ['trackid' => $item->id]);

                foreach ($logs as $log) {
                    $DB->delete_records('local_intellidata_trdetails', [
                        'logid' => $log->id,
                    ]);
                }
                $DB->delete_records('local_intellidata_trlogs', [
                    'trackid' => $item->id,
                ]);
            }
            $DB->delete_records('local_intellidata_tracking', $params);
        }
    }

}
