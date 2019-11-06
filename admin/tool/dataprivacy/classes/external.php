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
 * Class containing the external API functions functions for the Data Privacy tool.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

use coding_exception;
use context_helper;
use context_system;
use context_user;
use core\invalid_persistent_exception;
use core\notification;
use core_user;
use dml_exception;
use external_api;
use external_description;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;
use invalid_parameter_exception;
use moodle_exception;
use required_capability_exception;
use restricted_context_exception;
use tool_dataprivacy\external\category_exporter;
use tool_dataprivacy\external\data_request_exporter;
use tool_dataprivacy\external\purpose_exporter;
use tool_dataprivacy\output\data_registry_page;

/**
 * Class external.
 *
 * The external API for the Data Privacy tool.
 *
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Parameter description for cancel_data_request().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function cancel_data_request_parameters() {
        return new external_function_parameters([
            'requestid' => new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Cancel a data request.
     *
     * @since Moodle 3.5
     * @param int $requestid The request ID.
     * @return array
     * @throws invalid_persistent_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function cancel_data_request($requestid) {
        global $USER;

        $warnings = [];
        $params = external_api::validate_parameters(self::cancel_data_request_parameters(), [
            'requestid' => $requestid
        ]);
        $requestid = $params['requestid'];

        // Validate context and access to manage the registry.
        $context = context_user::instance($USER->id);
        self::validate_context($context);

        // Ensure the request exists.
        $select = 'id = :id AND (userid = :userid OR requestedby = :requestedby)';
        $params = ['id' => $requestid, 'userid' => $USER->id, 'requestedby' => $USER->id];
        $requests = data_request::get_records_select($select, $params);
        $requestexists = count($requests) === 1;

        $result = false;
        if ($requestexists) {
            $request = reset($requests);
            $datasubject = $request->get('userid');

            if ($datasubject !== $USER->id) {
                // The user is not the subject. Check that they can cancel this request.
                if (!api::can_create_data_request_for_user($datasubject)) {
                    $forusercontext = \context_user::instance($datasubject);
                    throw new required_capability_exception($forusercontext,
                            'tool/dataprivacy:makedatarequestsforchildren', 'nopermissions', '');
                }
            }

            // TODO: Do we want a request to be non-cancellable past a certain point? E.g. When it's already approved/processing.
            $result = api::update_request_status($requestid, api::DATAREQUEST_STATUS_CANCELLED);
        } else {
            $warnings[] = [
                'item' => $requestid,
                'warningcode' => 'errorrequestnotfound',
                'message' => get_string('errorrequestnotfound', 'tool_dataprivacy')
            ];
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for cancel_data_request().
     *
     * @since Moodle 3.5
     * @return external_description
     */
    public static function cancel_data_request_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for contact_dpo().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function contact_dpo_parameters() {
        return new external_function_parameters([
            'message' => new external_value(PARAM_TEXT, 'The user\'s message to the Data Protection Officer(s)', VALUE_REQUIRED)
        ]);
    }

    /**
     * Make a general enquiry to a DPO.
     *
     * @since Moodle 3.5
     * @param string $message The message to be sent to the DPO.
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws invalid_persistent_exception
     * @throws restricted_context_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function contact_dpo($message) {
        global $USER;

        $warnings = [];
        $params = external_api::validate_parameters(self::contact_dpo_parameters(), [
            'message' => $message
        ]);
        $message = $params['message'];

        // Validate context.
        $userid = $USER->id;
        $context = context_user::instance($userid);
        self::validate_context($context);

        // Lodge the request.
        $datarequest = new data_request();
        // The user the request is being made for.
        $datarequest->set('userid', $userid);
        // The user making the request.
        $datarequest->set('requestedby', $userid);
        // Set status.
        $datarequest->set('status', api::DATAREQUEST_STATUS_PENDING);
        // Set request type.
        $datarequest->set('type', api::DATAREQUEST_TYPE_OTHERS);
        // Set request comments.
        $datarequest->set('comments', $message);

        // Store subject access request.
        $datarequest->create();

        // Get the list of the site Data Protection Officers.
        $dpos = api::get_site_dpos();

        // Email the data request to the Data Protection Officer(s)/Admin(s).
        $result = true;
        foreach ($dpos as $dpo) {
            $sendresult = api::notify_dpo($dpo, $datarequest);
            if (!$sendresult) {
                $result = false;
                $warnings[] = [
                    'item' => $dpo->id,
                    'warningcode' => 'errorsendingtodpo',
                    'message' => get_string('errorsendingmessagetodpo', 'tool_dataprivacy')
                ];
            }
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for contact_dpo().
     *
     * @since Moodle 3.5
     * @return external_description
     */
    public static function contact_dpo_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for mark_complete().
     *
     * @since Moodle 3.5.2
     * @return external_function_parameters
     */
    public static function mark_complete_parameters() {
        return new external_function_parameters([
            'requestid' => new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Mark a user's general enquiry's status as complete.
     *
     * @since Moodle 3.5.2
     * @param int $requestid The request ID of the general enquiry.
     * @return array
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws invalid_persistent_exception
     * @throws restricted_context_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function mark_complete($requestid) {
        global $USER;

        $warnings = [];
        $params = external_api::validate_parameters(self::mark_complete_parameters(), [
            'requestid' => $requestid,
        ]);
        $requestid = $params['requestid'];

        // Validate context and access to manage the registry.
        $context = context_system::instance();
        self::validate_context($context);
        api::check_can_manage_data_registry();

        $message = get_string('markedcomplete', 'tool_dataprivacy');
        // Update the data request record.
        if ($result = api::update_request_status($requestid, api::DATAREQUEST_STATUS_COMPLETE, $USER->id, $message)) {
            // Add notification in the session to be shown when the page is reloaded on the JS side.
            notification::success(get_string('requestmarkedcomplete', 'tool_dataprivacy'));
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for mark_complete().
     *
     * @since Moodle 3.5.2
     * @return external_description
     */
    public static function mark_complete_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for get_data_request().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function get_data_request_parameters() {
        return new external_function_parameters([
            'requestid' => new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Fetch the details of a user's data request.
     *
     * @since Moodle 3.5
     * @param int $requestid The request ID.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     * @throws moodle_exception
     */
    public static function get_data_request($requestid) {
        global $PAGE;

        $warnings = [];
        $params = external_api::validate_parameters(self::get_data_request_parameters(), [
            'requestid' => $requestid
        ]);
        $requestid = $params['requestid'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);
        $requestpersistent = new data_request($requestid);
        require_capability('tool/dataprivacy:managedatarequests', $context);

        $exporter = new data_request_exporter($requestpersistent, ['context' => $context]);
        $renderer = $PAGE->get_renderer('tool_dataprivacy');
        $result = $exporter->export($renderer);

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for get_data_request().
     *
     * @since Moodle 3.5
     * @return external_description
     */
    public static function get_data_request_returns() {
        return new external_single_structure([
            'result' => data_request_exporter::get_read_structure(),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for approve_data_request().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function approve_data_request_parameters() {
        return new external_function_parameters([
            'requestid' => new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Approve a data request.
     *
     * @since Moodle 3.5
     * @param int $requestid The request ID.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     * @throws moodle_exception
     */
    public static function approve_data_request($requestid) {
        $warnings = [];
        $params = external_api::validate_parameters(self::approve_data_request_parameters(), [
            'requestid' => $requestid
        ]);
        $requestid = $params['requestid'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/dataprivacy:managedatarequests', $context);

        // Ensure the request exists.
        $requestexists = data_request::record_exists($requestid);

        $result = false;
        if ($requestexists) {
            $result = api::approve_data_request($requestid);

            // Add notification in the session to be shown when the page is reloaded on the JS side.
            notification::success(get_string('requestapproved', 'tool_dataprivacy'));
        } else {
            $warnings[] = [
                'item' => $requestid,
                'warningcode' => 'errorrequestnotfound',
                'message' => get_string('errorrequestnotfound', 'tool_dataprivacy')
            ];
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for approve_data_request().
     *
     * @since Moodle 3.5
     * @return external_description
     */
    public static function approve_data_request_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for bulk_approve_data_requests().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function bulk_approve_data_requests_parameters() {
        return new external_function_parameters([
            'requestids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
            )
        ]);
    }

    /**
     * Bulk approve bulk data request.
     *
     * @since Moodle 3.5
     * @param array $requestids Array consisting the request ID's.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     * @throws moodle_exception
     */
    public static function bulk_approve_data_requests($requestids) {
        $warnings = [];
        $result = false;
        $params = external_api::validate_parameters(self::bulk_approve_data_requests_parameters(), [
            'requestids' => $requestids
        ]);
        $requestids = $params['requestids'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/dataprivacy:managedatarequests', $context);

        foreach ($requestids as $requestid) {
            // Ensure the request exists.
            $requestexists = data_request::record_exists($requestid);

            if ($requestexists) {
                api::approve_data_request($requestid);
            } else {
                $warnings[] = [
                    'item' => $requestid,
                    'warningcode' => 'errorrequestnotfound',
                    'message' => get_string('errorrequestnotfound', 'tool_dataprivacy')
                ];
            }
        }

        if (empty($warnings)) {
            $result = true;
            // Add notification in the session to be shown when the page is reloaded on the JS side.
            notification::success(get_string('requestsapproved', 'tool_dataprivacy'));
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for bulk_approve_data_requests().
     *
     * @since Moodle 3.5
     * @return external_description
     */
    public static function bulk_approve_data_requests_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for deny_data_request().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function deny_data_request_parameters() {
        return new external_function_parameters([
            'requestid' => new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Deny a data request.
     *
     * @since Moodle 3.5
     * @param int $requestid The request ID.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     * @throws moodle_exception
     */
    public static function deny_data_request($requestid) {
        $warnings = [];
        $params = external_api::validate_parameters(self::deny_data_request_parameters(), [
            'requestid' => $requestid
        ]);
        $requestid = $params['requestid'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/dataprivacy:managedatarequests', $context);

        // Ensure the request exists.
        $requestexists = data_request::record_exists($requestid);

        $result = false;
        if ($requestexists) {
            $result = api::deny_data_request($requestid);

            // Add notification in the session to be shown when the page is reloaded on the JS side.
            notification::success(get_string('requestdenied', 'tool_dataprivacy'));
        } else {
            $warnings[] = [
                'item' => $requestid,
                'warningcode' => 'errorrequestnotfound',
                'message' => get_string('errorrequestnotfound', 'tool_dataprivacy')
            ];
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for deny_data_request().
     *
     * @since Moodle 3.5
     * @return external_description
     */
    public static function deny_data_request_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for bulk_deny_data_requests().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function bulk_deny_data_requests_parameters() {
        return new external_function_parameters([
            'requestids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
            )
        ]);
    }

    /**
     * Bulk deny data requests.
     *
     * @since Moodle 3.5
     * @param array $requestids Array consisting of request ID's.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     * @throws moodle_exception
     */
    public static function bulk_deny_data_requests($requestids) {
        $warnings = [];
        $result = false;
        $params = external_api::validate_parameters(self::bulk_deny_data_requests_parameters(), [
            'requestids' => $requestids
        ]);
        $requestids = $params['requestids'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/dataprivacy:managedatarequests', $context);

        foreach ($requestids as $requestid) {
            // Ensure the request exists.
            $requestexists = data_request::record_exists($requestid);

            if ($requestexists) {
                api::deny_data_request($requestid);
            } else {
                $warnings[] = [
                    'item' => $requestid,
                    'warningcode' => 'errorrequestnotfound',
                    'message' => get_string('errorrequestnotfound', 'tool_dataprivacy')
                ];
            }
        }

        if (empty($warnings)) {
            $result = true;
            // Add notification in the session to be shown when the page is reloaded on the JS side.
            notification::success(get_string('requestsdenied', 'tool_dataprivacy'));
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Parameter description for bulk_deny_data_requests().
     *
     * @since Moodle 3.5
     * @return external_description
     */
    public static function bulk_deny_data_requests_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for get_data_request().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function get_users_parameters() {
        return new external_function_parameters([
            'query' => new external_value(PARAM_TEXT, 'The search query', VALUE_REQUIRED)
        ]);
    }

    /**
     * Fetch the details of a user's data request.
     *
     * @since Moodle 3.5
     * @param string $query The search request.
     * @return array
     * @throws required_capability_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function get_users($query) {
        global $DB;
        $params = external_api::validate_parameters(self::get_users_parameters(), [
            'query' => $query
        ]);
        $query = $params['query'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/dataprivacy:managedatarequests', $context);

        $allusernames = get_all_user_name_fields(true);
        // Exclude admins and guest user.
        $excludedusers = array_keys(get_admins()) + [guest_user()->id];
        $sort = 'lastname ASC, firstname ASC';
        $fields = 'id,' . $allusernames;

        $extrafields = get_extra_user_fields($context);
        if (!empty($extrafields)) {
            $fields .= ',' . implode(',', $extrafields);
        }

        list($sql, $params) = users_search_sql($query, '', false, $extrafields, $excludedusers);
        $users = $DB->get_records_select('user', $sql, $params, $sort, $fields, 0, 30);
        $useroptions = [];
        foreach ($users as $user) {
            $useroption = (object)[
                'id' => $user->id,
                'fullname' => fullname($user)
            ];
            $useroption->extrafields = [];
            foreach ($extrafields as $extrafield) {
                // Sanitize the extra fields to prevent potential XSS exploit.
                $useroption->extrafields[] = (object)[
                    'name' => $extrafield,
                    'value' => s($user->$extrafield)
                ];
            }
            $useroptions[$user->id] = $useroption;
        }

        return $useroptions;
    }

    /**
     * Parameter description for get_users().
     *
     * @since Moodle 3.5
     * @return external_description
     * @throws coding_exception
     */
    public static function get_users_returns() {
        return new external_multiple_structure(new external_single_structure(
            [
                'id' => new external_value(core_user::get_property_type('id'), 'ID of the user'),
                'fullname' => new external_value(core_user::get_property_type('firstname'), 'The fullname of the user'),
                'extrafields' => new external_multiple_structure(
                    new external_single_structure([
                            'name' => new external_value(PARAM_TEXT, 'Name of the extrafield.'),
                            'value' => new external_value(PARAM_TEXT, 'Value of the extrafield.')
                        ]
                    ), 'List of extra fields', VALUE_OPTIONAL
                )
            ]
        ));
    }

    /**
     * Parameter description for create_purpose_form().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function create_purpose_form_parameters() {
        return new external_function_parameters([
            'jsonformdata' => new external_value(PARAM_RAW, 'The data to create the purpose, encoded as a json array')
        ]);
    }

    /**
     * Creates a data purpose from form data.
     *
     * @since Moodle 3.5
     * @param string $jsonformdata
     * @return array
     */
    public static function create_purpose_form($jsonformdata) {
        global $PAGE;

        $warnings = [];

        $params = external_api::validate_parameters(self::create_purpose_form_parameters(), [
            'jsonformdata' => $jsonformdata
        ]);

        // Validate context and access to manage the registry.
        self::validate_context(\context_system::instance());
        api::check_can_manage_data_registry();

        $serialiseddata = json_decode($params['jsonformdata']);
        $data = array();
        parse_str($serialiseddata, $data);

        $purpose = new \tool_dataprivacy\purpose(0);
        $mform = new \tool_dataprivacy\form\purpose(null, ['persistent' => $purpose], 'post', '', null, true, $data);

        $validationerrors = true;
        if ($validateddata = $mform->get_data()) {
            $purpose = api::create_purpose($validateddata);
            $validationerrors = false;
        } else if ($errors = $mform->is_validated()) {
            throw new moodle_exception('generalerror');
        }

        $exporter = new purpose_exporter($purpose, ['context' => \context_system::instance()]);
        return [
            'purpose' => $exporter->export($PAGE->get_renderer('core')),
            'validationerrors' => $validationerrors,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for create_purpose_form().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function create_purpose_form_returns() {
        return new external_single_structure([
            'purpose' => purpose_exporter::get_read_structure(),
            'validationerrors' => new external_value(PARAM_BOOL, 'Were there validation errors', VALUE_REQUIRED),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for delete_purpose().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function delete_purpose_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The purpose ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Deletes a data purpose.
     *
     * @since Moodle 3.5
     * @param int $id The ID.
     * @return array
     * @throws invalid_persistent_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function delete_purpose($id) {
        global $USER;

        $params = external_api::validate_parameters(self::delete_purpose_parameters(), [
            'id' => $id
        ]);

        // Validate context and access to manage the registry.
        self::validate_context(\context_system::instance());
        api::check_can_manage_data_registry();

        $result = api::delete_purpose($params['id']);

        return [
            'result' => $result,
            'warnings' => []
        ];
    }

    /**
     * Parameter description for delete_purpose().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function delete_purpose_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for create_category_form().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function create_category_form_parameters() {
        return new external_function_parameters([
            'jsonformdata' => new external_value(PARAM_RAW, 'The data to create the category, encoded as a json array')
        ]);
    }

    /**
     * Creates a data category from form data.
     *
     * @since Moodle 3.5
     * @param string $jsonformdata
     * @return array
     */
    public static function create_category_form($jsonformdata) {
        global $PAGE;

        $warnings = [];

        $params = external_api::validate_parameters(self::create_category_form_parameters(), [
            'jsonformdata' => $jsonformdata
        ]);

        // Validate context and access to manage the registry.
        self::validate_context(\context_system::instance());
        api::check_can_manage_data_registry();

        $serialiseddata = json_decode($params['jsonformdata']);
        $data = array();
        parse_str($serialiseddata, $data);

        $category = new \tool_dataprivacy\category(0);
        $mform = new \tool_dataprivacy\form\category(null, ['persistent' => $category], 'post', '', null, true, $data);

        $validationerrors = true;
        if ($validateddata = $mform->get_data()) {
            $category = api::create_category($validateddata);
            $validationerrors = false;
        } else if ($errors = $mform->is_validated()) {
            throw new moodle_exception('generalerror');
        }

        $exporter = new category_exporter($category, ['context' => \context_system::instance()]);
        return [
            'category' => $exporter->export($PAGE->get_renderer('core')),
            'validationerrors' => $validationerrors,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for create_category_form().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function create_category_form_returns() {
        return new external_single_structure([
            'category' => category_exporter::get_read_structure(),
            'validationerrors' => new external_value(PARAM_BOOL, 'Were there validation errors', VALUE_REQUIRED),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for delete_category().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function delete_category_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The category ID', VALUE_REQUIRED)
        ]);
    }

    /**
     * Deletes a data category.
     *
     * @since Moodle 3.5
     * @param int $id The ID.
     * @return array
     * @throws invalid_persistent_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public static function delete_category($id) {
        global $USER;

        $params = external_api::validate_parameters(self::delete_category_parameters(), [
            'id' => $id
        ]);

        // Validate context and access to manage the registry.
        self::validate_context(\context_system::instance());
        api::check_can_manage_data_registry();

        $result = api::delete_category($params['id']);

        return [
            'result' => $result,
            'warnings' => []
        ];
    }

    /**
     * Parameter description for delete_category().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function delete_category_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for set_contextlevel_form().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function set_contextlevel_form_parameters() {
        return new external_function_parameters([
            'jsonformdata' => new external_value(PARAM_RAW, 'The context level data, encoded as a json array')
        ]);
    }

    /**
     * Creates a data category from form data.
     *
     * @since Moodle 3.5
     * @param string $jsonformdata
     * @return array
     */
    public static function set_contextlevel_form($jsonformdata) {
        global $PAGE;

        $warnings = [];

        $params = external_api::validate_parameters(self::set_contextlevel_form_parameters(), [
            'jsonformdata' => $jsonformdata
        ]);

        // Validate context and access to manage the registry.
        self::validate_context(\context_system::instance());
        api::check_can_manage_data_registry();

        $serialiseddata = json_decode($params['jsonformdata']);
        $data = array();
        parse_str($serialiseddata, $data);

        $contextlevel = $data['contextlevel'];

        $customdata = \tool_dataprivacy\form\contextlevel::get_contextlevel_customdata($contextlevel);
        $mform = new \tool_dataprivacy\form\contextlevel(null, $customdata, 'post', '', null, true, $data);
        if ($validateddata = $mform->get_data()) {
            $contextlevel = api::set_contextlevel($validateddata);
        } else if ($errors = $mform->is_validated()) {
            $warnings[] = json_encode($errors);
        }

        if ($contextlevel) {
            $result = true;
        } else {
            $result = false;
        }
        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for set_contextlevel_form().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function set_contextlevel_form_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Whether the data was properly set or not'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for set_context_form().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function set_context_form_parameters() {
        return new external_function_parameters([
            'jsonformdata' => new external_value(PARAM_RAW, 'The context level data, encoded as a json array')
        ]);
    }

    /**
     * Creates a data category from form data.
     *
     * @since Moodle 3.5
     * @param string $jsonformdata
     * @return array
     */
    public static function set_context_form($jsonformdata) {
        global $PAGE;

        $warnings = [];

        $params = external_api::validate_parameters(self::set_context_form_parameters(), [
            'jsonformdata' => $jsonformdata
        ]);

        // Validate context and access to manage the registry.
        self::validate_context(\context_system::instance());
        api::check_can_manage_data_registry();

        $serialiseddata = json_decode($params['jsonformdata']);
        $data = array();
        parse_str($serialiseddata, $data);

        $context = context_helper::instance_by_id($data['contextid']);
        $customdata = \tool_dataprivacy\form\context_instance::get_context_instance_customdata($context);
        $mform = new \tool_dataprivacy\form\context_instance(null, $customdata, 'post', '', null, true, $data);
        if ($validateddata = $mform->get_data()) {
            api::check_can_manage_data_registry($validateddata->contextid);
            $context = api::set_context_instance($validateddata);
        } else if ($errors = $mform->is_validated()) {
            $warnings[] = json_encode($errors);
            throw new moodle_exception('generalerror');
        }

        if ($context) {
            $result = true;
        } else {
            $result = false;
        }
        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for set_context_form().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function set_context_form_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Whether the data was properly set or not'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameter description for tree_extra_branches().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function tree_extra_branches_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id to expand'),
            'element' => new external_value(PARAM_ALPHA, 'The element we are interested on')
        ]);
    }

    /**
     * Returns tree extra branches.
     *
     * @since Moodle 3.5
     * @param int $contextid
     * @param string $element
     * @return array
     */
    public static function tree_extra_branches($contextid, $element) {

        $params = external_api::validate_parameters(self::tree_extra_branches_parameters(), [
            'contextid' => $contextid,
            'element' => $element,
        ]);

        $context = context_helper::instance_by_id($params['contextid']);

        self::validate_context($context);
        api::check_can_manage_data_registry($context->id);

        switch ($params['element']) {
            case 'course':
                $branches = data_registry_page::get_courses_branch($context);
                break;
            case 'module':
                $branches = data_registry_page::get_modules_branch($context);
                break;
            case 'block':
                $branches = data_registry_page::get_blocks_branch($context);
                break;
            default:
                throw new \moodle_exception('Unsupported element provided.');
        }

        return [
            'branches' => $branches,
            'warnings' => [],
        ];
    }

    /**
     * Returns for tree_extra_branches().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function tree_extra_branches_returns() {
        return new external_single_structure([
            'branches' => new external_multiple_structure(self::get_tree_node_structure(true)),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameters for confirm_contexts_for_deletion().
     *
     * @since Moodle 3.5
     * @return external_function_parameters
     */
    public static function confirm_contexts_for_deletion_parameters() {
        return new external_function_parameters([
            'ids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'Expired context record ID', VALUE_REQUIRED),
                'Array of expired context record IDs', VALUE_DEFAULT, []
            ),
        ]);
    }

    /**
     * Confirm a given array of expired context record IDs
     *
     * @since Moodle 3.5
     * @param int[] $ids Array of record IDs from the expired contexts table.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function confirm_contexts_for_deletion($ids) {
        $warnings = [];
        $params = external_api::validate_parameters(self::confirm_contexts_for_deletion_parameters(), [
            'ids' => $ids
        ]);
        $ids = $params['ids'];

        // Validate context and access to manage the registry.
        self::validate_context(\context_system::instance());
        api::check_can_manage_data_registry();

        $result = true;
        if (!empty($ids)) {
            $expiredcontextstoapprove = [];
            // Loop through the deletion of expired contexts and their children if necessary.
            foreach ($ids as $id) {
                $expiredcontext = new expired_context($id);
                $targetcontext = context_helper::instance_by_id($expiredcontext->get('contextid'));

                if (!$targetcontext instanceof \context_user) {
                    // Fetch this context's child contexts. Make sure that all of the child contexts are flagged for deletion.
                    // User context children do not need to be considered.
                    $childcontexts = $targetcontext->get_child_contexts();
                    foreach ($childcontexts as $child) {
                        if ($expiredchildcontext = expired_context::get_record(['contextid' => $child->id])) {
                            // Add this child context to the list for approval.
                            $expiredcontextstoapprove[] = $expiredchildcontext;
                        } else {
                            // This context has not yet been flagged for deletion.
                            $result = false;
                            $message = get_string('errorcontexthasunexpiredchildren', 'tool_dataprivacy',
                                $targetcontext->get_context_name(false));
                            $warnings[] = [
                                'item' => 'tool_dataprivacy_ctxexpired',
                                'warningcode' => 'errorcontexthasunexpiredchildren',
                                'message' => $message
                            ];
                            // Exit the process.
                            break 2;
                        }
                    }
                }

                $expiredcontextstoapprove[] = $expiredcontext;
            }

            // Proceed with the approval if everything's in order.
            if ($result) {
                // Mark expired contexts as approved for deletion.
                foreach ($expiredcontextstoapprove as $expired) {
                    // Only mark expired contexts that are pending approval.
                    if ($expired->get('status') == expired_context::STATUS_EXPIRED) {
                        api::set_expired_context_status($expired, expired_context::STATUS_APPROVED);
                    }
                }
            }

        } else {
            // We don't have anything to process.
            $result = false;
            $warnings[] = [
                'item' => 'tool_dataprivacy_ctxexpired',
                'warningcode' => 'errornoexpiredcontexts',
                'message' => get_string('errornoexpiredcontexts', 'tool_dataprivacy')
            ];
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for confirm_contexts_for_deletion().
     *
     * @since Moodle 3.5
     * @return external_single_structure
     */
    public static function confirm_contexts_for_deletion_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Whether the record was properly marked for deletion or not'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameters for set_context_defaults().
     *
     * @return external_function_parameters
     */
    public static function set_context_defaults_parameters() {
        return new external_function_parameters([
            'contextlevel' => new external_value(PARAM_INT, 'The context level', VALUE_REQUIRED),
            'category' => new external_value(PARAM_INT, 'The default category for the given context level', VALUE_REQUIRED),
            'purpose' => new external_value(PARAM_INT, 'The default purpose for the given context level', VALUE_REQUIRED),
            'activity' => new external_value(PARAM_PLUGIN, 'The plugin name of the activity', VALUE_DEFAULT, null),
            'override' => new external_value(PARAM_BOOL, 'Whether to override existing instances with the defaults', VALUE_DEFAULT,
                false),
        ]);
    }

    /**
     * Updates the default category and purpose for a given context level (and optionally, a plugin).
     *
     * @param int $contextlevel The context level.
     * @param int $category The ID matching the category.
     * @param int $purpose The ID matching the purpose record.
     * @param int $activity The name of the activity that we're making a defaults configuration for.
     * @param bool $override Whether to override the purpose/categories of existing instances to these defaults.
     * @return array
     */
    public static function set_context_defaults($contextlevel, $category, $purpose, $activity, $override) {
        $warnings = [];

        $params = external_api::validate_parameters(self::set_context_defaults_parameters(), [
            'contextlevel' => $contextlevel,
            'category' => $category,
            'purpose' => $purpose,
            'activity' => $activity,
            'override' => $override,
        ]);
        $contextlevel = $params['contextlevel'];
        $category = $params['category'];
        $purpose = $params['purpose'];
        $activity = $params['activity'];
        $override = $params['override'];

        // Validate context.
        $context = context_system::instance();
        self::validate_context($context);
        api::check_can_manage_data_registry();

        // Set the context defaults.
        $result = api::set_context_defaults($contextlevel, $category, $purpose, $activity, $override);

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for set_context_defaults().
     *
     * @return external_single_structure
     */
    public static function set_context_defaults_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Whether the context defaults were successfully set or not'),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameters for get_category_options().
     *
     * @return external_function_parameters
     */
    public static function get_category_options_parameters() {
        return new external_function_parameters([
            'includeinherit' => new external_value(PARAM_BOOL, 'Include option "Inherit"', VALUE_DEFAULT, true),
            'includenotset' => new external_value(PARAM_BOOL, 'Include option "Not set"', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Fetches a list of data category options containing category IDs as keys and the category name for the value.
     *
     * @param bool $includeinherit Whether to include the "Inherit" option.
     * @param bool $includenotset Whether to include the "Not set" option.
     * @return array
     */
    public static function get_category_options($includeinherit, $includenotset) {
        $warnings = [];

        $params = self::validate_parameters(self::get_category_options_parameters(), [
            'includeinherit' => $includeinherit,
            'includenotset' => $includenotset
        ]);
        $includeinherit = $params['includeinherit'];
        $includenotset = $params['includenotset'];

        $context = context_system::instance();
        self::validate_context($context);
        api::check_can_manage_data_registry();

        $categories = api::get_categories();
        $options = data_registry_page::category_options($categories, $includenotset, $includeinherit);
        $categoryoptions = [];
        foreach ($options as $id => $name) {
            $categoryoptions[] = [
                'id' => $id,
                'name' => $name,
            ];
        }

        return [
            'options' => $categoryoptions,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for get_category_options().
     *
     * @return external_single_structure
     */
    public static function get_category_options_returns() {
        $optiondefinition = new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'The category ID'),
                'name' => new external_value(PARAM_TEXT, 'The category name'),
            ]
        );

        return new external_single_structure([
            'options' => new external_multiple_structure($optiondefinition),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameters for get_purpose_options().
     *
     * @return external_function_parameters
     */
    public static function get_purpose_options_parameters() {
        return new external_function_parameters([
            'includeinherit' => new external_value(PARAM_BOOL, 'Include option "Inherit"', VALUE_DEFAULT, true),
            'includenotset' => new external_value(PARAM_BOOL, 'Include option "Not set"', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Fetches a list of data storage purposes containing purpose IDs as keys and the purpose name for the value.
     *
     * @param bool $includeinherit Whether to include the "Inherit" option.
     * @param bool $includenotset Whether to include the "Not set" option.
     * @return array
     */
    public static function get_purpose_options($includeinherit, $includenotset) {
        $warnings = [];

        $params = self::validate_parameters(self::get_category_options_parameters(), [
            'includeinherit' => $includeinherit,
            'includenotset' => $includenotset
        ]);
        $includeinherit = $params['includeinherit'];
        $includenotset = $params['includenotset'];

        $context = context_system::instance();
        self::validate_context($context);

        $purposes = api::get_purposes();
        $options = data_registry_page::purpose_options($purposes, $includenotset, $includeinherit);
        $purposeoptions = [];
        foreach ($options as $id => $name) {
            $purposeoptions[] = [
                'id' => $id,
                'name' => $name,
            ];
        }

        return [
            'options' => $purposeoptions,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for get_purpose_options().
     *
     * @return external_single_structure
     */
    public static function get_purpose_options_returns() {
        $optiondefinition = new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'The purpose ID'),
                'name' => new external_value(PARAM_TEXT, 'The purpose name'),
            ]
        );

        return new external_single_structure([
            'options' => new external_multiple_structure($optiondefinition),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Parameters for get_activity_options().
     *
     * @return external_function_parameters
     */
    public static function get_activity_options_parameters() {
        return new external_function_parameters([
            'nodefaults' => new external_value(PARAM_BOOL, 'Whether to fetch all activities or only those without defaults',
                VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Fetches a list of activity options for setting data registry defaults.
     *
     * @param boolean $nodefaults If false, it will fetch all of the activities. Otherwise, it will only fetch the activities
     *                            that don't have defaults yet (e.g. when adding a new activity module defaults).
     * @return array
     */
    public static function get_activity_options($nodefaults) {
        $warnings = [];

        $params = self::validate_parameters(self::get_activity_options_parameters(), [
            'nodefaults' => $nodefaults,
        ]);
        $nodefaults = $params['nodefaults'];

        $context = context_system::instance();
        self::validate_context($context);

        // Get activity module plugin info.
        $pluginmanager = \core_plugin_manager::instance();
        $modplugins = $pluginmanager->get_enabled_plugins('mod');
        $modoptions = [];

        // Get the module-level defaults. data_registry::get_defaults falls back to this when there are no activity defaults.
        list($levelpurpose, $levelcategory) = data_registry::get_defaults(CONTEXT_MODULE);
        foreach ($modplugins as $name) {
            // Check if we have default purpose and category for this module if we want don't want to fetch everything.
            if ($nodefaults) {
                list($purpose, $category) = data_registry::get_defaults(CONTEXT_MODULE, $name);
                // Compare this with the module-level defaults.
                if ($purpose !== $levelpurpose || $category !== $levelcategory) {
                    // If the defaults for this activity has been already set, there's no need to add this in the list of options.
                    continue;
                }
            }

            $displayname = $pluginmanager->plugin_name('mod_' . $name);
            $modoptions[] = (object)[
                'name' => $name,
                'displayname' => $displayname
            ];
        }

        return [
            'options' => $modoptions,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns for get_category_options().
     *
     * @return external_single_structure
     */
    public static function get_activity_options_returns() {
        $optionsdefinition = new external_single_structure(
            [
                'name' => new external_value(PARAM_TEXT, 'The plugin name of the activity'),
                'displayname' => new external_value(PARAM_TEXT, 'The display name of the activity'),
            ]
        );

        return new external_single_structure([
            'options' => new external_multiple_structure($optionsdefinition),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Gets the structure of a tree node (link + child branches).
     *
     * @since Moodle 3.5
     * @param bool $allowchildbranches
     * @return array
     */
    private static function get_tree_node_structure($allowchildbranches = true) {
        $fields = [
            'text' => new external_value(PARAM_TEXT, 'The node text', VALUE_REQUIRED),
            'expandcontextid' => new external_value(PARAM_INT, 'The contextid this node expands', VALUE_REQUIRED),
            'expandelement' => new external_value(PARAM_ALPHA, 'What element is this node expanded to', VALUE_REQUIRED),
            'contextid' => new external_value(PARAM_INT, 'The node contextid', VALUE_REQUIRED),
            'contextlevel' => new external_value(PARAM_INT, 'The node contextlevel', VALUE_REQUIRED),
            'expanded' => new external_value(PARAM_INT, 'Is it expanded', VALUE_REQUIRED),
        ];

        if ($allowchildbranches) {
            // Passing false as we will not have more than 1 sub-level.
            $fields['branches'] = new external_multiple_structure(
                self::get_tree_node_structure(false),
                'Children node structure',
                VALUE_OPTIONAL
            );
        } else {
            // We will only have 1 sub-level and we don't want an infinite get_tree_node_structure, this is a hacky
            // way to prevent this infinite loop when calling get_tree_node_structure recursively.
            $fields['branches'] = new external_multiple_structure(
                new external_value(
                    PARAM_TEXT,
                    'Nothing really, it will always be an empty array',
                    VALUE_OPTIONAL
                )
            );
        }

        return new external_single_structure($fields, 'Node structure', VALUE_OPTIONAL);
    }
}
