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
 * Adhoc task that processes a data request and prepares the user's metadata for review.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\task;

use coding_exception;
use core\task\adhoc_task;
use moodle_exception;
use tool_dataprivacy\api;
use tool_dataprivacy\data_request;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that processes a data request and prepares the user's metadata for review.
 *
 * Custom data accepted:
 * - requestid -> The ID of the data request to be processed.
 *
 * @package     tool_dataprivacy
 * @copyright   2018 Jun Pataleta
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class initiate_data_request_task extends adhoc_task {

    /**
     * Run the task to initiate the data request process.
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/admin/tool/dataprivacy/lib.php');

        if (!isset($this->get_custom_data()->requestid)) {
            throw new coding_exception('The custom data \'requestid\' is required.');
        }
        $requestid = $this->get_custom_data()->requestid;

        $datarequest = new data_request($requestid);

        // Check if this request still needs to be processed. e.g. The user might have cancelled it before this task has run.
        $status = $datarequest->get('status');
        if (!api::is_active($status)) {
            mtrace('Request ' . $requestid . ' with status ' . $status . ' doesn\'t need to be processed. Skipping...');
            return;
        }

        // Update the status of this request as pre-processing.
        mtrace('Generating user metadata...');
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_PREPROCESSING);

        // TODO: Add code here to process the request and prepare the metadata to for review.

        // When the preparation of the metadata finishes, update the request status to awaiting approval.
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        mtrace('User metadata generation complete...');

        // Get the list of the site Data Protection Officers.
        $dpos = api::get_site_dpos();

        // Email the data request to the Data Protection Officer(s)/Admin(s).
        foreach ($dpos as $dpo) {
            $dponame = fullname($dpo);
            if (api::notify_dpo($dpo, $datarequest)) {
                mtrace('Message sent to DPO: ' . $dponame);
            } else {
                mtrace('A problem was encountered while sending the message to the DPO: ' . $dponame);
            }
        }
    }
}
