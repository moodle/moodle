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
 * Adhoc task that processes a data request and prepares the user's relevant contexts for review.
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
use tool_dataprivacy\contextlist_context;
use tool_dataprivacy\data_request;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that processes a data request and prepares the user's relevant contexts for review.
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

        require_once($CFG->dirroot . "/{$CFG->admin}/tool/dataprivacy/lib.php");

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
        mtrace('Generating the contexts containing personal data for the user...');
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_PREPROCESSING);

        // Add the list of relevant contexts to the request, and mark all as pending approval.
        $privacymanager = new \core_privacy\manager();
        $privacymanager->set_observer(new \tool_dataprivacy\manager_observer());

        $contextlistcollection = $privacymanager->get_contexts_for_userid($datarequest->get('userid'));
        api::add_request_contexts_with_status($contextlistcollection, $requestid, contextlist_context::STATUS_PENDING);

        // When the preparation of the contexts finishes, update the request status to awaiting approval.
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        mtrace('Context generation complete...');

        // Get the list of the site Data Protection Officers.
        $dpos = api::get_site_dpos();

        // We should prevent DPO(s)/Admin(s) being flooded with notifications for each request when bulk delete
        // data requests are being created.
        // NOTE: This should be improved, we should not totally disable the notifications for automatically
        // created requests. Possibly, we should create one notification for these such cases.
        if ($datarequest->get('creationmethod') != data_request::DATAREQUEST_CREATION_AUTO) {
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
}
