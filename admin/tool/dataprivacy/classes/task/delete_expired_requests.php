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
 * Scheduled task to delete files and update statuses of expired data requests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\task;

use coding_exception;
use core\task\scheduled_task;
use tool_dataprivacy\api;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

/**
 * Scheduled task to delete files and update request statuses once they expire.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_expired_requests extends scheduled_task {

    /**
     * Returns the task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('deleteexpireddatarequeststask', 'tool_dataprivacy');
    }

    /**
     * Run the task to delete expired data request files and update request statuses.
     *
     */
    public function execute() {
        $expiredrequests = \tool_dataprivacy\data_request::get_expired_requests();
        $deletecount = count($expiredrequests);

        if ($deletecount > 0) {
            \tool_dataprivacy\data_request::expire($expiredrequests);

            mtrace($deletecount . ' expired completed data requests have been deleted');
        }
    }
}
