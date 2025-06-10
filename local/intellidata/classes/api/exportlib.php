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

use local_intellidata\api\apilib;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\services\database_service;
use local_intellidata\services\datatypes_service;
use local_intellidata\services\encryption_service;
use local_intellidata\services\export_service;
use local_intellidata\task\export_adhoc_task;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\persistent\datatypeconfig;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * Export lib.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_intellidata_exportlib extends external_api {

    /**
     * Validate credentials parameters.
     *
     * @return external_function_parameters
     */
    public static function validate_credentials_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Validate credentials.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function validate_credentials($data) {

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(
            self::validate_credentials_parameters(), [
                'data' => $data,
            ]
        );

        // Validate if credentials not empty.
        $encryptionservice = new encryption_service();
        if (!$encryptionservice->validate_credentials()) {
            return [
                'data' => 'emptycredentials',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'code' => PARAM_TEXT,
        ]);

        // Validate code.
        if (empty($params['code'])) {
            return [
                'data' => 'cannotdecode',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Validate clientid.
        if ($params['code'] != $encryptionservice->clientidentifier) {
            return [
                'data' => 'wrongclientid',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        return [
            'data' => 'ok',
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Validate credentials returns.
     *
     * @return external_single_structure
     */
    public static function validate_credentials_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Validation message'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Export data parameters.
     *
     * @return external_function_parameters
     */
    public static function export_data_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Export data.
     *
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function export_data() {

        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(
            self::export_data_parameters(), []
        );

        // Export static tables.
        $databaseservice = new database_service(false);
        $databaseservice->export_tables();

        $encryptionservice = new encryption_service();
        $exportservice = new export_service();

        // Generate and save files to filesdir.
        $exportservice->save_files();

        $context = [
            'files' => $exportservice->get_files(),
            'metadata' => ParamsHelper::get_exportfiles_metadata(),
        ];

        return [
            'data' => $encryptionservice->encrypt(json_encode($context)),
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Export data returns.
     *
     * @return external_single_structure
     */
    public static function export_data_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Encrypted datafiles (files list) and metadata'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Get data files parameters.
     *
     * @return external_function_parameters
     */
    public static function get_data_files_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Get data files.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function get_data_files($data) {

        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(
            self::get_data_files_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'timestart' => PARAM_INT,
            'timeend' => PARAM_INT,
            'datatype' => PARAM_TEXT,
        ]);

        $encryptionservice = new encryption_service();
        $exportservice = new export_service();
        $exportlogrepository = new export_log_repository();

        $migrateddatatypes = $exportlogrepository->get_migrated_datatypes();
        $alldatatypes = [];
        foreach ($exportservice->get_datatypes() as $name => $datatype) {
            if ($datatype['migration'] && $datatype['tabletype'] == datatypeconfig::TABLETYPE_REQUIRED) {
                $alldatatypes[] = $name;
            }
        }

        $notmigrateddatatypes = array_diff($alldatatypes, $migrateddatatypes);
        if ($notmigrateddatatypes) {
            return [
                'data' => 'Migrations not ready: ' . implode(', ', $notmigrateddatatypes),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        $context = [
            'files' => $exportservice->get_files($params),
            'metadata' => ParamsHelper::get_exportfiles_metadata(),
        ];

        return [
            'data' => $encryptionservice->encrypt(json_encode($context)),
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Get data files returns.
     *
     * @return external_single_structure
     */
    public static function get_data_files_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Encrypted datafiles (files list) and metadata'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Get live data parameters.
     *
     * @return external_function_parameters
     */
    public static function get_live_data_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Get live data.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function get_live_data($data) {

        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(
            self::get_live_data_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'datatype' => PARAM_TEXT,
        ]);

        if (!in_array($params['datatype'], ['roles', 'categories', 'courses'])) {
            return [
                'data' => 'Unsupported datatype for real time',
                'status' => apilib::STATUS_ERROR,
            ];
        }
        $encryptionservice = new encryption_service();
        $exportservice = new export_service();
        $datatype = $exportservice->get_datatype($params['datatype']);

        $params['start'] = 0;
        $params['limit'] = 100000;
        $migration = datatypes_service::init_migration($datatype, 'json');
        $data = $migration->get_records($params);

        return [
            'data' => $encryptionservice->encrypt(json_encode($data)),
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Get live data returns.
     *
     * @return external_single_structure
     */
    public static function get_live_data_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Encrypted data'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Set datatype parameters.
     *
     * @return external_function_parameters
     */
    public static function set_datatype_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Insert new datatype for export.
     *
     * @param $data
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function set_datatype($data) {

        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(
            self::set_datatype_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'datatypes' => PARAM_RAW,
            'exportfiles' => PARAM_INT,
            'callbackurl' => PARAM_URL,
        ]);
        $params['datatypes'] = json_decode($params['datatypes']);

        // Insert or update log record for datatype.
        $exportlogrepository = new export_log_repository();
        $exportdatatypes = [];
        try {
            foreach ($params['datatypes'] as $datatypename) {
                $datatypename = datatypes_service::generate_optional_datatype($datatypename);
                $exportlogrepository->insert_datatype($datatypename);
                $exportdatatypes[] = $datatypename;
            }
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Add adhoc task to process datatype.
        if ($params['exportfiles']) {
            $exporttask = new export_adhoc_task();
            $exporttask->set_custom_data([
                'datatypes' => $exportdatatypes,
                'callbackurl' => $params['callbackurl'],
            ]);
            \core\task\manager::queue_adhoc_task($exporttask);
        }

        return [
            'data' => 'Datatype successfully added.',
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Set datatype returns.
     *
     * @return external_single_structure
     */
    public static function set_datatype_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Response message.'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }


    /**
     * Enable processing parameters.
     *
     * @return external_function_parameters
     */
    public static function enable_processing_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Enable processing.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function enable_processing($data) {

        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(
            self::enable_processing_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'status' => PARAM_INT,
            'callbackurl' => PARAM_URL,
        ]);

        // Set plugin settings.
        set_config('ispluginsetup', $params['status'], ParamsHelper::PLUGIN);
        set_config('migrationcallbackurl', $params['callbackurl'], ParamsHelper::PLUGIN);

        return [
            'data' => 'ok',
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Enable processing returns.
     *
     * @return external_single_structure
     */
    public static function enable_processing_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Response'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Get bbcollsessions parameters.
     *
     * @return external_function_parameters
     */
    public static function get_bbcollsessions_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_TEXT, 'Request params'),
        ]);
    }

    /**
     * Get bbcollsessions.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function get_bbcollsessions($data) {
        global $CFG, $DB;

        require_once($CFG->libdir . "/adminlib.php");

        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(
            self::get_bbcollsessions_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'sessionslist' => PARAM_TEXT,
            'limit' => PARAM_INT,
            'offset' => PARAM_INT,
        ]);

        $sessionslist = explode(',', $params['sessionslist']);

        if (empty($sessionslist)) {
            return [
                'data' => 'Empty list of sessions',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        if ($params['limit'] > 1000 || $params['limit'] < 1) {
            return [
                'data' => 'Limit is required',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        if (!get_component_version('mod_collaborate')) {
            return [
                'data' => 'Blackboard Collaborate not installed',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        list($in, $params) = $DB->get_in_or_equal($sessionslist);
        $data = $DB->get_records_select(
            'collaborate', "sessionuid $in", $params, '',
            'id,course,sessionuid', $params['limit'], $params['offset']
        );

        $encryptionservice = new encryption_service();
        return [
            'data' => $encryptionservice->encrypt(json_encode($data)),
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Get bbcollsessions returns.
     *
     * @return external_single_structure
     */
    public static function get_bbcollsessions_returns() {
        return new external_single_structure([
            'data' => new external_value(PARAM_TEXT, 'Encrypted data'),
            'status' => new external_value(PARAM_TEXT, 'Response status'),
        ]);
    }

}
