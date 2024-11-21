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
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\bb_collaborate;

use cache;

class bb_collaborate_repository {
    /**
     * Key of cached access token
     */
    const ACCESS_TOKEN_CACHE_KEY = 'bb_col_access_token';

    /** @var string Type key in table {local_intelliboard_att_sync} */
    const ATT_SYNC_TYPE = 'bb_collaborate';

    /**
     * Get finished non-tracked sessions
     *
     * @return array
     * @throws \dml_exception
     */
    public function getNonTrackedSessions() {
        global $DB;

        $sessions = $DB->get_records_sql(
            "SELECT c.*, iat.data as sync_data
               FROM {collaborate} c
          LEFT JOIN {local_intelliboard_att_sync} iat ON iat.instance = c.id AND
                                                         iat.type = :type
          LEFT JOIN {local_intelliboard_bb_trck_m} libtm ON libtm.sessionuid = c.sessionuid
              WHERE c.sessionuid IS NOT NULL AND c.timeend <= :currenttime AND
                    libtm.id IS NULL
           ORDER BY c.timecreated DESC", [
                'currenttime' => time(),
                'type' => self::ATT_SYNC_TYPE
        ]);

        return $sessions;
    }

    /**
     * Get cached access token of bb collaborate
     *
     * @return false|mixed
     * @throws \coding_exception
     */
    public function cached_access_token() {
        $cache = cache::make(
            'local_intelliboard', 'bb_collaborate_access_token'
        );

        return $cache->get(self::ACCESS_TOKEN_CACHE_KEY);
    }

    /**
     * List of BB collabroate sessions, which not synchronized with InAttendance
     *
     * @return array
     * @throws \dml_exception
     */
    public function not_synchronized_sessions() {
        global $DB;

        return $DB->get_records_sql(
            "SELECT cb.id, cb.course, cm.id as activity,
                    cb.name as session_name, cb.timestart, cb.timeend,
                    cb.intro as description
               FROM {collaborate} cb
          LEFT JOIN {local_intelliboard_att_sync} lt ON lt.type = :type AND
                                                        lt.instance = cb.id
          LEFT JOIN {modules} m ON m.name = 'collaborate'
          LEFT JOIN {course_modules} cm ON cm.course =  cb.course AND
                                           cm.module = m.id AND
                                           cm.instance = cb.id
              WHERE lt.id IS NULL",
            ['type' => self::ATT_SYNC_TYPE]
        );
    }
}