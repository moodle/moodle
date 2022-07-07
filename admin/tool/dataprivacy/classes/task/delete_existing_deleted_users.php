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
 * Scheduled task to create delete data request for pre-existing deleted users.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\task;

use core\task\scheduled_task;
use tool_dataprivacy\api;
use tool_dataprivacy\data_request;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

/**
 * Scheduled task to create delete data request for pre-existing deleted users.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_existing_deleted_users extends scheduled_task {

    /**
     * Returns the task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('deleteexistingdeleteduserstask', 'tool_dataprivacy');
    }

    /**
     * Run the task to delete expired data request files and update request statuses.
     *
     */
    public function execute() {
        global $DB;

        // Automatic creation of deletion requests must be enabled.
        if (get_config('tool_dataprivacy', 'automaticdeletionrequests')) {
            // Select all deleted users that do not have any delete data requests created for them.
            $sql = "SELECT DISTINCT(u.id)
                  FROM {user} u
             LEFT JOIN {tool_dataprivacy_request} r
                       ON u.id = r.userid
                 WHERE u.deleted = ?
                       AND (r.id IS NULL
                           OR r.type != ?)";

            $params = [
                1,
                api::DATAREQUEST_TYPE_DELETE
            ];

            $deletedusers = $DB->get_records_sql($sql, $params);
            $createdrequests = 0;

            foreach ($deletedusers as $user) {
                api::create_data_request($user->id, api::DATAREQUEST_TYPE_DELETE,
                    get_string('datarequestcreatedfromscheduledtask', 'tool_dataprivacy'),
                    data_request::DATAREQUEST_CREATION_AUTO);
                $createdrequests++;
            }

            if ($createdrequests > 0) {
                mtrace($createdrequests . ' delete data request(s) created for existing deleted users');
            }
        }
    }
}
