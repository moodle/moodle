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
 * Adhoc task that processes an approved data request and prepares/deletes the user's data.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\task;

use action_link;
use coding_exception;
use context_system;
use core\message\message;
use core\task\adhoc_task;
use core_user;
use moodle_exception;
use moodle_url;
use tool_dataprivacy\api;
use tool_dataprivacy\data_request;

/**
 * Class that processes an approved data request and prepares/deletes the user's data.
 *
 * Custom data accepted:
 * - requestid -> The ID of the data request to be processed.
 *
 * @package     tool_dataprivacy
 * @copyright   2018 Jun Pataleta
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_data_request_task extends adhoc_task {

    /**
     * Run the task to initiate the data request process.
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function execute() {
        global $CFG, $PAGE, $SITE;

        require_once($CFG->dirroot . "/{$CFG->admin}/tool/dataprivacy/lib.php");

        if (!isset($this->get_custom_data()->requestid)) {
            throw new coding_exception('The custom data \'requestid\' is required.');
        }
        $requestid = $this->get_custom_data()->requestid;

        $requestpersistent = new data_request($requestid);
        $request = $requestpersistent->to_record();

        // Check if this request still needs to be processed. e.g. The user might have cancelled it before this task has run.
        $status = $requestpersistent->get('status');
        if (!api::is_active($status)) {
            mtrace("Request {$requestid} with status {$status} doesn't need to be processed. Skipping...");
            return;
        }

        if (!\tool_dataprivacy\data_registry::defaults_set()) {
            // Warn if no site purpose is defined.
            mtrace('Warning: No purpose is defined at the system level. Deletion will delete all.');
        }

        // Grab the manager.
        // We set an observer against it to handle failures.
        $allowfiltering = get_config('tool_dataprivacy', 'allowfiltering');
        $manager = new \core_privacy\manager();
        $manager->set_observer(new \tool_dataprivacy\manager_observer());

        // Get the user details now. We might not be able to retrieve it later if it's a deletion processing.
        $foruser = core_user::get_user($request->userid);

        // Update the status of this request as pre-processing.
        mtrace('Pre-processing request...');
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_PROCESSING);
        $contextlistcollection = $manager->get_contexts_for_userid($requestpersistent->get('userid'));

        mtrace('Fetching approved contextlists from collection');

        mtrace('Processing request...');
        $completestatus = api::DATAREQUEST_STATUS_COMPLETE;
        $deleteuser = false;

        if ($request->type == api::DATAREQUEST_TYPE_EXPORT) {
            // Get the user context.
            if ($allowfiltering) {
                // Get the collection of approved_contextlist objects needed for core_privacy data export.
                $approvedclcollection = api::get_approved_contextlist_collection_for_request($requestpersistent);
            } else {
                $approvedclcollection = api::get_approved_contextlist_collection_for_collection(
                    $contextlistcollection,
                    $foruser,
                    $request->type,
                );
            }

            $usercontext = \context_user::instance($foruser->id, IGNORE_MISSING);
            if (!$usercontext) {
                mtrace("Request {$requestid} cannot be processed due to a missing user context instance for the user
                    with ID {$foruser->id}. Skipping...");
                return;
            }

            // Export the data.
            $exportedcontent = $manager->export_user_data($approvedclcollection);

            $fs = get_file_storage();
            $filerecord = new \stdClass;
            $filerecord->component = 'tool_dataprivacy';
            $filerecord->contextid = $usercontext->id;
            $filerecord->userid    = $foruser->id;
            $filerecord->filearea  = 'export';
            $filerecord->filename  = 'export.zip';
            $filerecord->filepath  = '/';
            $filerecord->itemid    = $requestid;
            $filerecord->license   = $CFG->sitedefaultlicense;
            $filerecord->author    = fullname($foruser);
            // Save somewhere.
            $thing = $fs->create_file_from_pathname($filerecord, $exportedcontent);
            $completestatus = api::DATAREQUEST_STATUS_DOWNLOAD_READY;
        } else if ($request->type == api::DATAREQUEST_TYPE_DELETE) {
            // Delete the data for users other than the primary admin, which is rejected.
            if (is_primary_admin($foruser->id)) {
                $completestatus = api::DATAREQUEST_STATUS_REJECTED;
            } else {
                $approvedclcollection = api::get_approved_contextlist_collection_for_collection(
                    $contextlistcollection,
                    $foruser,
                    $request->type,
                );
                $manager = new \core_privacy\manager();
                $manager->set_observer(new \tool_dataprivacy\manager_observer());

                $manager->delete_data_for_user($approvedclcollection);
                $completestatus = api::DATAREQUEST_STATUS_DELETED;
                $deleteuser = !$foruser->deleted;
            }
        }

        // When the preparation of the metadata finishes, update the request status to awaiting approval.
        api::update_request_status($requestid, $completestatus);
        mtrace('The processing of the user data request has been completed...');

        // Create message to notify the user regarding the processing results.
        $message = new message();
        $message->courseid = $SITE->id;
        $message->component = 'tool_dataprivacy';
        $message->name = 'datarequestprocessingresults';
        if (empty($request->dpo)) {
            // Use the no-reply user as the sender if the privacy officer is not set. This is the case for automatically
            // approved requests.
            $fromuser = core_user::get_noreply_user();
        } else {
            $fromuser = core_user::get_user($request->dpo);
            $message->replyto = $fromuser->email;
            $message->replytoname = fullname($fromuser);
        }
        $message->userfrom = $fromuser;

        $typetext = null;
        // Prepare the context data for the email message body.
        $messagetextdata = [
            'username' => fullname($foruser)
        ];

        $output = $PAGE->get_renderer('tool_dataprivacy');
        $emailonly = false;
        $notifyuser = true;
        switch ($request->type) {
            case api::DATAREQUEST_TYPE_EXPORT:
                // Check if the user is allowed to download their own export. (This is for
                // institutions which centrally co-ordinate subject access request across many
                // systems, not just one Moodle instance, so we don't want every instance emailing
                // the user.)
                if (!api::can_download_data_request_for_user($request->userid, $request->requestedby, $request->userid)) {
                    $notifyuser = false;
                }

                $typetext = get_string('requesttypeexport', 'tool_dataprivacy');
                // We want to notify the user in Moodle about the processing results.
                $message->notification = 1;
                $datarequestsurl = new moodle_url('/admin/tool/dataprivacy/mydatarequests.php');
                $message->contexturl = $datarequestsurl;
                $message->contexturlname = get_string('datarequests', 'tool_dataprivacy');
                // Message to the recipient.
                $messagetextdata['message'] = get_string('resultdownloadready', 'tool_dataprivacy',
                    format_string($SITE->fullname, true, ['context' => context_system::instance()]));
                // Prepare download link.
                $downloadurl = moodle_url::make_pluginfile_url($usercontext->id, 'tool_dataprivacy', 'export', $thing->get_itemid(),
                    $thing->get_filepath(), $thing->get_filename(), true);
                $downloadlink = new action_link($downloadurl, get_string('download', 'tool_dataprivacy'));
                $messagetextdata['downloadlink'] = $downloadlink->export_for_template($output);
                break;
            case api::DATAREQUEST_TYPE_DELETE:
                $typetext = get_string('requesttypedelete', 'tool_dataprivacy');
                // No point notifying a deleted user in Moodle.
                $message->notification = 0;
                // Message to the recipient.
                $messagetextdata['message'] = get_string('resultdeleted', 'tool_dataprivacy',
                    format_string($SITE->fullname, true, ['context' => context_system::instance()]));
                // Message will be sent to the deleted user via email only.
                $emailonly = true;
                break;
            default:
                throw new moodle_exception('errorinvalidrequesttype', 'tool_dataprivacy');
        }

        $subject = get_string('datarequestemailsubject', 'tool_dataprivacy', $typetext);
        $message->subject           = $subject;
        $message->fullmessageformat = FORMAT_HTML;
        $message->userto = $foruser;

        // Render message email body.
        $messagehtml = $output->render_from_template('tool_dataprivacy/data_request_results_email', $messagetextdata);
        $message->fullmessage = html_to_text($messagehtml);
        $message->fullmessagehtml = $messagehtml;

        // Send message to the user involved.
        if ($notifyuser) {
            $messagesent = false;
            if ($emailonly) {
                // Do not sent an email if the user has been deleted. The user email has been previously deleted.
                if (!$foruser->deleted) {
                    $messagesent = email_to_user($foruser, $fromuser, $subject, $message->fullmessage, $messagehtml);
                }
            } else {
                $messagesent = message_send($message);
            }

            if ($messagesent) {
                mtrace('Message sent to user: ' . $messagetextdata['username']);
            }
        }

        // Send to requester as well in some circumstances.
        if ($foruser->id != $request->requestedby) {
            $sendtorequester = false;
            switch ($request->type) {
                case api::DATAREQUEST_TYPE_EXPORT:
                    // Send to the requester as well if they can download it, unless they are the
                    // DPO. If we didn't notify the user themselves (because they can't download)
                    // then send to requester even if it is the DPO, as in that case the requester
                    // needs to take some action.
                    if (api::can_download_data_request_for_user($request->userid, $request->requestedby, $request->requestedby)) {
                        $sendtorequester = !$notifyuser || !api::is_site_dpo($request->requestedby);
                    }
                    break;
                case api::DATAREQUEST_TYPE_DELETE:
                    // Send to the requester if they are not the DPO and if they are allowed to
                    // create data requests for the user (e.g. Parent).
                    $sendtorequester = !api::is_site_dpo($request->requestedby) &&
                            api::can_create_data_request_for_user($request->userid, $request->requestedby);
                    break;
                default:
                    throw new moodle_exception('errorinvalidrequesttype', 'tool_dataprivacy');
            }

            // Ensure the requester has the capability to make data requests for this user.
            if ($sendtorequester) {
                $requestedby = core_user::get_user($request->requestedby);
                $message->userto = $requestedby;
                $messagetextdata['username'] = fullname($requestedby);
                // Render message email body.
                $messagehtml = $output->render_from_template('tool_dataprivacy/data_request_results_email', $messagetextdata);
                $message->fullmessage = html_to_text($messagehtml);
                $message->fullmessagehtml = $messagehtml;

                // Send message.
                if ($emailonly) {
                    email_to_user($requestedby, $fromuser, $subject, $message->fullmessage, $messagehtml);
                } else {
                    message_send($message);
                }
                mtrace('Message sent to requester: ' . $messagetextdata['username']);
            }
        }

        if ($deleteuser) {
            // Delete the user.
            delete_user($foruser);
        }
    }
}
