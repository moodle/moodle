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

namespace core_message\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/message/lib.php');

use external_api;
use external_function_parameters;
use external_value;
use context_system;
use core_user;
use moodle_exception;

/**
 * External service to get number of unread notifications
 *
 * @package   core_message
 * @category  external
 * @copyright 2021 Dani Palou <dani@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.0
 */
class get_unread_notification_count extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'useridto' => new external_value(PARAM_INT, 'user id who received the notification, 0 for any user', VALUE_REQUIRED),
        ]);
    }

    /**
     * Get number of unread notifications.
     *
     * @param int $useridto the user id who received the notification, 0 for any user
     * @return int number of unread notifications
     * @throws \moodle_exception
     */
    public static function execute(int $useridto): int {
        global $USER, $DB;

        $params = self::validate_parameters(
            self::execute_parameters(),
            ['useridto' => $useridto],
        );

        $context = context_system::instance();
        self::validate_context($context);

        $useridto = $params['useridto'];

        if (!empty($useridto)) {
            if (core_user::is_real_user($useridto)) {
                $userto = core_user::get_user($useridto, '*', MUST_EXIST);
            } else {
                throw new moodle_exception('invaliduser');
            }
        }

        // Check if the current user is the sender/receiver or just a privileged user.
        if ($useridto != $USER->id and !has_capability('moodle/site:readallmessages', $context)) {
            throw new moodle_exception('accessdenied', 'admin');
        }

        return $DB->count_records_sql(
            "SELECT COUNT(n.id)
               FROM {notifications} n
          LEFT JOIN {user} u ON (u.id = n.useridfrom AND u.deleted = 0)
              WHERE n.useridto = ?
                    AND n.timeread IS NULL",
            [$useridto],
        );
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_INT, 'The count of unread notifications.');
    }
}
