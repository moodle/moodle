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
 * Class containing helper methods for processing data requests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use coding_exception;
use context_system;
use core\invalid_persistent_exception;
use core\message\message;
use core\task\manager;
use core_user;
use dml_exception;
use moodle_exception;
use moodle_url;
use required_capability_exception;
use stdClass;
use tool_dataprivacy\task\initiate_data_request_task;
use tool_dataprivacy\task\process_data_request_task;
use tool_dataprivacy\purpose;
use tool_dataprivacy\category;
use tool_dataprivacy\contextlevel;
use tool_dataprivacy\context_instance;
use tool_dataprivacy\data_registry;
use tool_dataprivacy\expired_context;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing helper methods for processing data requests.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /** Data export request type. */
    const DATAREQUEST_TYPE_EXPORT = 1;

    /** Data deletion request type. */
    const DATAREQUEST_TYPE_DELETE = 2;

    /** Other request type. Usually of enquiries to the DPO. */
    const DATAREQUEST_TYPE_OTHERS = 3;

    /** Newly submitted and we haven't yet started finding out where they have data. */
    const DATAREQUEST_STATUS_PENDING = 0;

    /** Newly submitted and we have started to find the location of data. */
    const DATAREQUEST_STATUS_PREPROCESSING = 1;

    /** Metadata ready and awaiting review and approval by the Data Protection officer. */
    const DATAREQUEST_STATUS_AWAITING_APPROVAL = 2;

    /** Request approved and will be processed soon. */
    const DATAREQUEST_STATUS_APPROVED = 3;

    /** The request is now being processed. */
    const DATAREQUEST_STATUS_PROCESSING = 4;

    /** Data request completed. */
    const DATAREQUEST_STATUS_COMPLETE = 5;

    /** Data request cancelled by the user. */
    const DATAREQUEST_STATUS_CANCELLED = 6;

    /** Data request rejected by the DPO. */
    const DATAREQUEST_STATUS_REJECTED = 7;

    /**
     * Determines whether the user can contact the site's Data Protection Officer via Moodle.
     *
     * @return boolean True when tool_dataprivacy|contactdataprotectionofficer is enabled.
     * @throws dml_exception
     */
    public static function can_contact_dpo() {
        return get_config('tool_dataprivacy', 'contactdataprotectionofficer') == 1;
    }

    /**
     * Check's whether the current user has the capability to manage data requests.
     *
     * @param int $userid The user ID.
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function can_manage_data_requests($userid) {
        $context = context_system::instance();

        // A user can manage data requests if he/she has the site DPO role and has the capability to manage data requests.
        return self::is_site_dpo($userid) && has_capability('tool/dataprivacy:managedatarequests', $context, $userid);
    }

    /**
     * Checks if the current user can manage the data registry at the provided id.
     *
     * @param int $contextid Fallback to system context id.
     * @throws \required_capability_exception
     * @return null
     */
    public static function check_can_manage_data_registry($contextid = false) {
        if ($contextid) {
            $context = \context_helper::instance_by_id($contextid);
        } else {
            $context = \context_system::instance();
        }

        require_capability('tool/dataprivacy:managedataregistry', $context);
    }

    /**
     * Fetches the list of users with the Data Protection Officer role.
     *
     * @throws dml_exception
     */
    public static function get_site_dpos() {
        // Get role(s) that can manage data requests.
        $dporoles = explode(',', get_config('tool_dataprivacy', 'dporoles'));

        $dpos = [];
        $context = context_system::instance();
        foreach ($dporoles as $roleid) {
            if (empty($roleid)) {
                continue;
            }
            // Fetch users that can manage data requests.
            $dpos += get_role_users($roleid, $context, false, 'u.*');
        }

        // If the site has no data protection officer, defer to site admin(s).
        if (empty($dpos)) {
            $dpos = get_admins();
        }
        return $dpos;
    }

    /**
     * Checks whether a given user is a site DPO.
     *
     * @param int $userid The user ID.
     * @return bool
     * @throws dml_exception
     */
    public static function is_site_dpo($userid) {
        $dpos = self::get_site_dpos();
        return array_key_exists($userid, $dpos);
    }

    /**
     * Lodges a data request and sends the request details to the site Data Protection Officer(s).
     *
     * @param int $foruser The user whom the request is being made for.
     * @param int $type The request type.
     * @param string $comments Request comments.
     * @return data_request
     * @throws invalid_persistent_exception
     * @throws coding_exception
     */
    public static function create_data_request($foruser, $type, $comments = '') {
        global $USER;

        $datarequest = new data_request();
        // The user the request is being made for.
        $datarequest->set('userid', $foruser);
        // The user making the request.
        $datarequest->set('requestedby', $USER->id);
        // Set status.
        $datarequest->set('status', self::DATAREQUEST_STATUS_PENDING);
        // Set request type.
        $datarequest->set('type', $type);
        // Set request comments.
        $datarequest->set('comments', $comments);

        // Store subject access request.
        $datarequest->create();

        // Fire an ad hoc task to initiate the data request process.
        $task = new initiate_data_request_task();
        $task->set_custom_data(['requestid' => $datarequest->get('id')]);
        manager::queue_adhoc_task($task, true);

        return $datarequest;
    }

    /**
     * Fetches the list of the data requests.
     *
     * If user ID is provided, it fetches the data requests for the user.
     * Otherwise, it fetches all of the data requests, provided that the user has the capability to manage data requests.
     * (e.g. Users with the Data Protection Officer roles)
     *
     * @param int $userid The User ID.
     * @return data_request[]
     * @throws dml_exception
     */
    public static function get_data_requests($userid = 0) {
        global $USER;
        $results = [];
        if ($userid) {
            // Get the data requests for the user or data requests made by the user.
            $select = "userid = :userid OR requestedby = :requestedby";
            $params = [
                'userid' => $userid,
                'requestedby' => $userid
            ];
            $results = data_request::get_records_select($select, $params, 'status DESC, timemodified DESC');
        } else {
            // If the current user is one of the site's Data Protection Officers, then fetch all data requests.
            if (self::is_site_dpo($USER->id)) {
                $results = data_request::get_records(null, 'status DESC, timemodified DESC', '');
            }
        }

        return $results;
    }

    /**
     * Checks whether there is already an existing pending/in-progress data request for a user for a given request type.
     *
     * @param int $userid The user ID.
     * @param int $type The request type.
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function has_ongoing_request($userid, $type) {
        global $DB;

        // Check if the user already has an incomplete data request of the same type.
        $nonpendingstatuses = [
            self::DATAREQUEST_STATUS_COMPLETE,
            self::DATAREQUEST_STATUS_CANCELLED,
            self::DATAREQUEST_STATUS_REJECTED,
        ];
        list($insql, $inparams) = $DB->get_in_or_equal($nonpendingstatuses, SQL_PARAMS_NAMED);
        $select = 'type = :type AND userid = :userid AND status NOT ' . $insql;
        $params = array_merge([
            'type' => $type,
            'userid' => $userid
        ], $inparams);

        return data_request::record_exists_select($select, $params);
    }

    /**
     * Determines whether a request is active or not based on its status.
     *
     * @param int $status The request status.
     * @return bool
     */
    public static function is_active($status) {
        // List of statuses which doesn't require any further processing.
        $finalstatuses = [
            self::DATAREQUEST_STATUS_COMPLETE,
            self::DATAREQUEST_STATUS_CANCELLED,
            self::DATAREQUEST_STATUS_REJECTED,
        ];

        return !in_array($status, $finalstatuses);
    }

    /**
     * Cancels the data request for a given request ID.
     *
     * @param int $requestid The request identifier.
     * @param int $status The request status.
     * @param int $dpoid The user ID of the Data Protection Officer
     * @return bool
     * @throws invalid_persistent_exception
     * @throws coding_exception
     */
    public static function update_request_status($requestid, $status, $dpoid = 0) {
        // Update the request.
        $datarequest = new data_request($requestid);
        $datarequest->set('status', $status);
        if ($dpoid) {
            $datarequest->set('dpo', $dpoid);
        }
        return $datarequest->update();
    }

    /**
     * Fetches a request based on the request ID.
     *
     * @param int $requestid The request identifier
     * @return data_request
     */
    public static function get_request($requestid) {
        return new data_request($requestid);
    }

    /**
     * Approves a data request based on the request ID.
     *
     * @param int $requestid The request identifier
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_persistent_exception
     * @throws required_capability_exception
     * @throws moodle_exception
     */
    public static function approve_data_request($requestid) {
        global $USER;

        // Check first whether the user can manage data requests.
        if (!self::can_manage_data_requests($USER->id)) {
            $context = context_system::instance();
            throw new required_capability_exception($context, 'tool/dataprivacy:managedatarequests', 'nopermissions', '');
        }

        // Check if request is already awaiting for approval.
        $request = new data_request($requestid);
        if ($request->get('status') != self::DATAREQUEST_STATUS_AWAITING_APPROVAL) {
            throw new moodle_exception('errorrequestnotwaitingforapproval', 'tool_dataprivacy');
        }

        // Update the status and the DPO.
        $result = self::update_request_status($requestid, self::DATAREQUEST_STATUS_APPROVED, $USER->id);

        // Fire an ad hoc task to initiate the data request process.
        $task = new process_data_request_task();
        $task->set_custom_data(['requestid' => $requestid]);
        if ($request->get('type') == self::DATAREQUEST_TYPE_EXPORT) {
            $task->set_userid($request->get('userid'));
        }
        manager::queue_adhoc_task($task, true);

        return $result;
    }

    /**
     * Rejects a data request based on the request ID.
     *
     * @param int $requestid The request identifier
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_persistent_exception
     * @throws required_capability_exception
     * @throws moodle_exception
     */
    public static function deny_data_request($requestid) {
        global $USER;

        if (!self::can_manage_data_requests($USER->id)) {
            $context = context_system::instance();
            throw new required_capability_exception($context, 'tool/dataprivacy:managedatarequests', 'nopermissions', '');
        }

        // Check if request is already awaiting for approval.
        $request = new data_request($requestid);
        if ($request->get('status') != self::DATAREQUEST_STATUS_AWAITING_APPROVAL) {
            throw new moodle_exception('errorrequestnotwaitingforapproval', 'tool_dataprivacy');
        }

        // Update the status and the DPO.
        return self::update_request_status($requestid, self::DATAREQUEST_STATUS_REJECTED, $USER->id);
    }

    /**
     * Sends a message to the site's Data Protection Officer about a request.
     *
     * @param stdClass $dpo The DPO user record
     * @param data_request $request The data request
     * @return int|false
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function notify_dpo($dpo, data_request $request) {
        global $PAGE, $SITE;

        // Create message to send to the Data Protection Officer(s).
        $typetext = null;
        switch ($request->get('type')) {
            case self::DATAREQUEST_TYPE_EXPORT:
                $typetext = get_string('requesttypeexport', 'tool_dataprivacy');
                break;
            case self::DATAREQUEST_TYPE_DELETE:
                $typetext = get_string('requesttypedelete', 'tool_dataprivacy');
                break;
            case self::DATAREQUEST_TYPE_OTHERS:
                $typetext = get_string('requesttypeothers', 'tool_dataprivacy');
                break;
            default:
                throw new moodle_exception('errorinvalidrequesttype', 'tool_dataprivacy');
        }
        $subject = get_string('datarequestemailsubject', 'tool_dataprivacy', $typetext);

        $requestedby = core_user::get_user($request->get('requestedby'));
        $datarequestsurl = new moodle_url('/admin/tool/dataprivacy/datarequests.php');
        $message = new message();
        $message->courseid          = $SITE->id;
        $message->component         = 'tool_dataprivacy';
        $message->name              = 'contactdataprotectionofficer';
        $message->userfrom          = $requestedby;
        $message->replyto           = $requestedby->email;
        $message->replytoname       = fullname($requestedby->email);
        $message->subject           = $subject;
        $message->fullmessageformat = FORMAT_HTML;
        $message->notification      = 1;
        $message->contexturl        = $datarequestsurl;
        $message->contexturlname    = get_string('datarequests', 'tool_dataprivacy');

        // Prepare the context data for the email message body.
        $messagetextdata = [
            'requestedby' => fullname($requestedby),
            'requesttype' => $typetext,
            'requestdate' => userdate($request->get('timecreated')),
            'requestcomments' => text_to_html($request->get('comments')),
            'datarequestsurl' => $datarequestsurl
        ];
        $requestingfor = core_user::get_user($request->get('userid'));
        if ($requestedby->id == $requestingfor->id) {
            $messagetextdata['requestfor'] = $messagetextdata['requestedby'];
        } else {
            $messagetextdata['requestfor'] = fullname($requestingfor);
        }

        $output = $PAGE->get_renderer('tool_dataprivacy');
        // Email the data request to the Data Protection Officer(s)/Admin(s).
        $messagetextdata['dponame'] = fullname($dpo);
        // Render message email body.
        $messagehtml = $output->render_from_template('tool_dataprivacy/data_request_email', $messagetextdata);
        $message->userto = $dpo;
        $message->fullmessage = html_to_text($messagehtml);
        $message->fullmessagehtml = $messagehtml;

        // Send message.
        return message_send($message);
    }

    /**
     * Creates a new data purpose.
     *
     * @param stdClass $record
     * @return \tool_dataprivacy\purpose.
     */
    public static function create_purpose(stdClass $record) {
        self::check_can_manage_data_registry();

        $purpose = new purpose(0, $record);
        $purpose->create();

        return $purpose;
    }

    /**
     * Updates an existing data purpose.
     *
     * @param stdClass $record
     * @return \tool_dataprivacy\purpose.
     */
    public static function update_purpose(stdClass $record) {
        self::check_can_manage_data_registry();

        $purpose = new purpose($record->id);
        $purpose->from_record($record);

        $result = $purpose->update();

        return $purpose;
    }

    /**
     * Deletes a data purpose.
     *
     * @param int $id
     * @return bool
     */
    public static function delete_purpose($id) {
        self::check_can_manage_data_registry();

        $purpose = new purpose($id);
        if ($purpose->is_used()) {
            throw new \moodle_exception('Purpose with id ' . $id . ' can not be deleted because it is used.');
        }
        return $purpose->delete();
    }

    /**
     * Get all system data purposes.
     *
     * @return \tool_dataprivacy\purpose[]
     */
    public static function get_purposes() {
        self::check_can_manage_data_registry();

        return purpose::get_records([], 'name', 'ASC');
    }

    /**
     * Creates a new data category.
     *
     * @param stdClass $record
     * @return \tool_dataprivacy\category.
     */
    public static function create_category(stdClass $record) {
        self::check_can_manage_data_registry();

        $category = new category(0, $record);
        $category->create();

        return $category;
    }

    /**
     * Updates an existing data category.
     *
     * @param stdClass $record
     * @return \tool_dataprivacy\category.
     */
    public static function update_category(stdClass $record) {
        self::check_can_manage_data_registry();

        $category = new category($record->id);
        $category->from_record($record);

        $result = $category->update();

        return $category;
    }

    /**
     * Deletes a data category.
     *
     * @param int $id
     * @return bool
     */
    public static function delete_category($id) {
        self::check_can_manage_data_registry();

        $category = new category($id);
        if ($category->is_used()) {
            throw new \moodle_exception('Category with id ' . $id . ' can not be deleted because it is used.');
        }
        return $category->delete();
    }

    /**
     * Get all system data categories.
     *
     * @return \tool_dataprivacy\category[]
     */
    public static function get_categories() {
        self::check_can_manage_data_registry();

        return category::get_records([], 'name', 'ASC');
    }

    /**
     * Sets the context instance purpose and category.
     *
     * @param \stdClass $record
     * @return \tool_dataprivacy\context_instance
     */
    public static function set_context_instance($record) {
        self::check_can_manage_data_registry($record->contextid);

        if ($instance = context_instance::get_record_by_contextid($record->contextid, false)) {
            // Update.
            $instance->from_record($record);

            if (empty($record->purposeid) && empty($record->categoryid)) {
                // We accept one of them to be null but we delete it if both are null.
                self::unset_context_instance($instance);
                return;
            }

        } else {
            // Add.
            $instance = new context_instance(0, $record);
        }
        $instance->save();

        return $instance;
    }

    /**
     * Unsets the context instance record.
     *
     * @param \tool_dataprivacy\context_instance $instance
     * @return null
     */
    public static function unset_context_instance(context_instance $instance) {
        self::check_can_manage_data_registry($instance->get('contextid'));
        $instance->delete();
    }

    /**
     * Sets the context level purpose and category.
     *
     * @throws \coding_exception
     * @param \stdClass $record
     * @return contextlevel
     */
    public static function set_contextlevel($record) {
        global $DB;

        // Only manager at system level can set this.
        self::check_can_manage_data_registry();

        if ($record->contextlevel != CONTEXT_SYSTEM && $record->contextlevel != CONTEXT_USER) {
            throw new \coding_exception('Only context system and context user can set a contextlevel ' .
                'purpose and retention');
        }

        if ($contextlevel = contextlevel::get_record_by_contextlevel($record->contextlevel, false)) {
            // Update.
            $contextlevel->from_record($record);
        } else {
            // Add.
            $contextlevel = new contextlevel(0, $record);
        }
        $contextlevel->save();

        // We sync with their defaults as we removed these options from the defaults page.
        $classname = \context_helper::get_class_for_level($record->contextlevel);
        list($purposevar, $categoryvar) = data_registry::var_names_from_context($classname);
        set_config($purposevar, $record->purposeid, 'tool_dataprivacy');
        set_config($categoryvar, $record->categoryid, 'tool_dataprivacy');

        return $contextlevel;
    }

    /**
     * Returns the effective category given a context instance.
     *
     * @param \context $context
     * @param int $forcedvalue Use this categoryid value as if this was this context instance category.
     * @return category|false
     */
    public static function get_effective_context_category(\context $context, $forcedvalue=false) {
        self::check_can_manage_data_registry($context->id);
        if (!data_registry::defaults_set()) {
            return false;
        }

        return data_registry::get_effective_context_value($context, 'category', $forcedvalue);
    }

    /**
     * Returns the effective purpose given a context instance.
     *
     * @param \context $context
     * @param int $forcedvalue Use this purposeid value as if this was this context instance purpose.
     * @return purpose|false
     */
    public static function get_effective_context_purpose(\context $context, $forcedvalue=false) {
        self::check_can_manage_data_registry($context->id);
        if (!data_registry::defaults_set()) {
            return false;
        }

        return data_registry::get_effective_context_value($context, 'purpose', $forcedvalue);
    }

    /**
     * Returns the effective category given a context level.
     *
     * @param int $contextlevel
     * @param int $forcedvalue Use this categoryid value as if this was this context level category.
     * @return category|false
     */
    public static function get_effective_contextlevel_category($contextlevel, $forcedvalue=false) {
        self::check_can_manage_data_registry(\context_system::instance()->id);
        if (!data_registry::defaults_set()) {
            return false;
        }

        return data_registry::get_effective_contextlevel_value($contextlevel, 'category', $forcedvalue);
    }

    /**
     * Returns the effective purpose given a context level.
     *
     * @param int $contextlevel
     * @param int $forcedvalue Use this purposeid value as if this was this context level purpose.
     * @return purpose|false
     */
    public static function get_effective_contextlevel_purpose($contextlevel, $forcedvalue=false) {
        self::check_can_manage_data_registry(\context_system::instance()->id);
        if (!data_registry::defaults_set()) {
            return false;
        }

        return data_registry::get_effective_contextlevel_value($contextlevel, 'purpose', $forcedvalue);
    }

    /**
     * Creates an expired context record for the provided context id.
     *
     * @param int $contextid
     * @return \tool_dataprivacy\expired_context
     */
    public static function create_expired_context($contextid) {
        self::check_can_manage_data_registry();

        $record = (object)[
            'contextid' => $contextid,
            'status' => expired_context::STATUS_EXPIRED,
        ];
        $expiredctx = new expired_context(0, $record);
        $expiredctx->save();

        return $expiredctx;
    }

    /**
     * Deletes an expired context record.
     *
     * @param int $id The tool_dataprivacy_ctxexpire id.
     * @return bool True on success.
     */
    public static function delete_expired_context($id) {
        self::check_can_manage_data_registry();

        $expiredcontext = new expired_context($id);
        return $expiredcontext->delete();
    }

    /**
     * Updates the status of an expired context.
     *
     * @param \tool_dataprivacy\expired_context $expiredctx
     * @param int $status
     * @return null
     */
    public static function set_expired_context_status(expired_context $expiredctx, $status) {
        self::check_can_manage_data_registry();

        $expiredctx->set('status', $status);
        $expiredctx->save();
    }
}
