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
 * Contains class used to return information to display for the message popup.
 *
 * @package    message_popup
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_popup;

defined('MOODLE_INTERNAL') || die();

/**
 * Class used to return information to display for the message popup.
 *
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {
    /**
     * Get popup notifications for the specified users. Nothing is returned if notifications are disabled.
     *
     * @param int $useridto the user id who received the notification
     * @param string $sort the column name to order by including optionally direction
     * @param int $limit limit the number of result returned
     * @param int $offset offset the result set by this amount
     * @return array notification records
     * @throws \moodle_exception
     * @since 3.2
     */
    public static function get_popup_notifications($useridto = 0, $sort = 'DESC', $limit = 0, $offset = 0) {
        global $DB, $USER;

        $sort = strtoupper($sort);
        if ($sort != 'DESC' && $sort != 'ASC') {
            throw new \moodle_exception('invalid parameter: sort: must be "DESC" or "ASC"');
        }

        if (empty($useridto)) {
            $useridto = $USER->id;
        }

        $params = [
            'useridto1' => $useridto,
            'useridto2' => $useridto,
        ];

        // Is notification enabled ?
        if ($useridto == $USER->id) {
            $disabled = $USER->emailstop;
        } else {
            $user = \core_user::get_user($useridto, "emailstop", MUST_EXIST);
            $disabled = $user->emailstop;
        }
        if ($disabled) {
            // Notifications are disabled, no need to run giant queries.
            return array();
        }

        $sql = "SELECT * FROM (
                    SELECT concat('r', r.id) as uniqueid, r.id, r.useridfrom, r.useridto,
                        r.subject, r.fullmessage, r.fullmessageformat,
                        r.fullmessagehtml, r.smallmessage, r.notification, r.contexturl,
                        r.contexturlname, r.timecreated, r.timeuserfromdeleted, r.timeusertodeleted,
                        r.component, r.eventtype, r.timeread
                    FROM {message_read} r
                    WHERE r.notification = 1
                    AND r.id IN (SELECT messageid FROM {message_popup} WHERE isread = 1)
                    AND r.useridto = :useridto1
                UNION ALL
                    SELECT concat('u', u.id) as uniqueid, u.id, u.useridfrom, u.useridto,
                        u.subject, u.fullmessage, u.fullmessageformat,
                        u.fullmessagehtml, u.smallmessage, u.notification, u.contexturl,
                        u.contexturlname, u.timecreated, u.timeuserfromdeleted, u.timeusertodeleted,
                        u.component, u.eventtype, 0 as timeread
                    FROM {message} u
                    WHERE u.notification = 1
                    AND u.id IN (SELECT messageid FROM {message_popup} WHERE isread = 0)
                    AND u.useridto = :useridto2
                ) f ORDER BY timecreated $sort, timeread $sort, id $sort";

        return array_values($DB->get_records_sql($sql, $params, $offset, $limit));
    }

    /**
     * Count the unread notifications for a user.
     *
     * @param int $useridto the user id who received the notification
     * @return int count of the unread notifications
     * @since 3.2
     */
    public static function count_unread_popup_notifications($useridto = 0) {
        global $USER, $DB;

        if (empty($useridto)) {
            $useridto = $USER->id;
        }

        return $DB->count_records_sql(
            "SELECT count(id)
            FROM {message}
            WHERE id IN (SELECT messageid FROM {message_popup} WHERE isread = 0)
            AND useridto = ?",
            [$useridto]
        );
    }
}
