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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class EventsHelper {

    /**
     * Grud created.
     */
    const CRUD_CREATED = 'c';
    /**
     * Crud read.
     */
    const CRUD_READ = 'r';
    /**
     * Crud updated.
     */
    const CRUD_UPDATED = 'u';
    /**
     * Crud deleted.
     */
    const CRUD_DELETED = 'd';

    /**
     * Returns list of deleted events.
     *
     * @return array
     */
    public static function deleted_eventslist() {
        $eventslist = self::events_list();
        return self::filter_deleted_events($eventslist);
    }

    /**
     * Retrievs list of all events.
     *
     * @return array
     */
    protected static function events_list() {
        return (method_exists('\tool_monitor\eventlist', 'get_all_eventlist'))
            ? \tool_monitor\eventlist::get_all_eventlist(true)
            : [];
    }

    /**
     * Filters deleted events from the list.
     *
     * @param array $eventslist
     * @return array
     */
    protected static function filter_deleted_events(array $eventslist) {
        $filteredevents = [];

        if (count($eventslist)) {
            foreach ($eventslist as $eventclass => $eventname) {
                try {
                    $eventdata = $eventclass::get_static_info();
                } catch (\Exception $e) {
                    DebugHelper::error_log($e->getMessage());
                    continue;
                }

                if (isset($eventdata['crud']) && $eventdata['crud'] == self::CRUD_DELETED &&
                    !empty($eventdata['objecttable'])) {
                    $filteredevents[$eventdata['objecttable']] = $eventclass;
                }
            }
        }

        return $filteredevents;
    }
}
