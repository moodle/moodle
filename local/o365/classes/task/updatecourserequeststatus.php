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
 * A scheduled task to clean up custom course request records.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\task;

use core\task\scheduled_task;
use local_o365\feature\courserequest\main;

/**
 * Scheduled task for updating status of custom course requests.
 */
class updatecourserequeststatus extends scheduled_task {
    /**
     * Get task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('courserequest_updatecourserequeststatus_taskname', 'local_o365');
    }

    /**
     * Execute the task.
     *
     * @return void
     */
    public function execute() {
        global $DB;

        $customcourserequests = $DB->get_records('local_o365_course_request',
            ['requeststatus' => main::COURSE_REQUEST_STATUS_PENDING]);

        if (empty($customcourserequests)) {
            mtrace('... No custom course requests to process.');

            return true;
        }

        $count = count($customcourserequests);
        mtrace("... Processing {$count} custom course requests.");

        foreach ($customcourserequests as $customcourserequest) {
            if (!$DB->record_exists('course_request', ['id' => $customcourserequest->requestid])) {
                $DB->set_field('local_o365_course_request', 'requeststatus', main::COURSE_REQUEST_STATUS_REJECTED,
                    ['id' => $customcourserequest->id]);
                mtrace("...... Course request with id {$customcourserequest->requestid} does not exists. " .
                    "Status of custom course request with id {$customcourserequest->id} was updated to 'Rejected'.");
            }
        }
    }
}
