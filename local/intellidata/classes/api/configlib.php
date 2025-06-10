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

use core\task\manager;
use local_intellidata\api\apilib;
use local_intellidata\services\encryption_service;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\TasksHelper;
use local_intellidata\services\config_service;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\persistent\datatypeconfig;
use local_intellidata\helpers\MigrationHelper;
use local_intellidata\task\migration_adhoc_task;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * IntelliData config lib.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_intellidata_configlib extends external_api {

    /**
     * Get plugin config parameters.
     *
     * @return external_function_parameters
     */
    public static function get_plugin_config_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Get plugin config.
     *
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function get_plugin_config($data) {

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
            self::get_plugin_config_parameters(), [
                'data' => $data,
            ]
        );

        $config = [
            'moodleconfig' => ParamsHelper::get_moodle_config(),
            'pluginversion' => ParamsHelper::get_plugin_version(),
            'pluginconfig' => SettingsHelper::get_plugin_settings(),
            'cronconfig' => TasksHelper::get_tasks_config(),
        ];

        $encryptionservice = new encryption_service();

        return [
            'data' => $encryptionservice->encrypt(json_encode($config)),
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Get plugin config returns.
     *
     * @return external_single_structure
     */
    public static function get_plugin_config_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Encrypted Logs'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Set plugin config parameters.
     *
     * @return external_function_parameters
     */
    public static function set_plugin_config_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Set plugin config.
     *
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function set_plugin_config($data) {

        $status = apilib::STATUS_ERROR;
        $message = 'error';

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
            self::set_plugin_config_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'name' => PARAM_TEXT,
            'value' => PARAM_TEXT,
        ]);

        if (SettingsHelper::is_setting_updatable($params['name'])) {
            SettingsHelper::set_setting($params['name'], $params['value']);

            $status = apilib::STATUS_SUCCESS;
            $message = 'updated';
        }

        $encryptionservice = new encryption_service();

        return [
            'data' => $encryptionservice->encrypt($message),
            'status' => $status,
        ];
    }

    /**
     * Set plugin config returns.
     *
     * @return external_single_structure
     */
    public static function set_plugin_config_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Encrypted Logs'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }


    /**
     * Parameters for reset_datatype() method.
     *
     * @return external_function_parameters
     */
    public static function reset_datatype_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Reset specific datatype for export.
     *
     * @param $data
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function reset_datatype($data) {

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
            self::reset_datatype_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'datatype' => PARAM_TEXT,
        ]);

        $exportlogrepository = new export_log_repository();
        $datatype = $exportlogrepository->get_datatype($params['datatype']);

        if ($datatype) {
            $record = datatypeconfig::get_record(['datatype' => $datatype->get('datatype')]);

            $configservice = new config_service();
            $configservice->reset_config_datatype($record);

            $encryptionservice = new encryption_service();

            return [
                'data' => $encryptionservice->encrypt('Datatype successfully resetted'),
                'status' => apilib::STATUS_SUCCESS,
            ];
        }

        return [
            'data' => 'Datatype not enabled for export.',
            'status' => apilib::STATUS_ERROR,
        ];
    }

    /**
     * Return data for reset_datatype() method.
     *
     * @return external_single_structure
     */
    public static function reset_datatype_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Response message.'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Parameters for reset_migration() method.
     *
     * @return external_function_parameters
     */
    public static function reset_migration_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Reset and restart migration.
     *
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function reset_migration() {

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

        // Reset migration.
        set_config('resetmigrationprogress', 1, 'local_intellidata');

        // Enable cron task.
        MigrationHelper::enabled_migration_task();

        $encryptionservice = new encryption_service();

        return [
            'data' => $encryptionservice->encrypt('Migration successfully restarted'),
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Return data for reset_migration() method.
     *
     * @return external_single_structure
     */
    public static function reset_migration_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Response message.'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Parameters for run_migration() method.
     *
     * @return external_function_parameters
     */
    public static function run_migration_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Run migration task.
     *
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function run_migration() {

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

        $tasksmanager = new core\task\manager();

        if (!method_exists($tasksmanager, 'get_scheduled_task') ||
            !method_exists($tasksmanager, 'is_runnable') ||
            !method_exists($tasksmanager, 'run_from_cli')) {

            return [
                'data' => "Tasks manager outdated",
                'status' => apilib::STATUS_ERROR,
            ];
        }

        $taskname = MigrationHelper::MIGRATIONS_TASK_CLASS;
        $task = \core\task\manager::get_scheduled_task($taskname);
        if (!$task) {
            return [
                'data' => "Task '$taskname' not found",
                'status' => apilib::STATUS_ERROR,
            ];
        }

        if (!\core\task\manager::is_runnable()) {
            return [
                'data' => get_string('cannotfindthepathtothecli', 'tool_task'),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        if (!$task->can_run()) {
            return [
                'data' => get_string('nopermissions', 'error'),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Validate is task is already running.
        if (TasksHelper::is_task_running($task)) {
            return [
                'data' => 'Task already running',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Run task.
        ob_start();
        \core\task\manager::run_from_cli($task);
        $output = ob_get_clean();

        $encryptionservice = new encryption_service();

        return [
            'data' => $encryptionservice->encrypt($output),
            'status' => apilib::STATUS_SUCCESS,
        ];
    }

    /**
     * Return data for run_migration() method.
     *
     * @return external_single_structure
     */
    public static function run_migration_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Response message.'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Parameters for delete_adhoc_task() method.
     *
     * @return external_function_parameters
     */
    public static function delete_adhoc_task_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Delete plugin adhoc task.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function delete_adhoc_task($data) {

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
            self::reset_datatype_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'id' => PARAM_INT,
        ]);

        if (TasksHelper::delete_adhoc_task($params['id'])) {
            return [
                'data' => get_string('taskdeleted', ParamsHelper::PLUGIN),
                'status' => apilib::STATUS_SUCCESS,
            ];
        }

        return [
            'data' => get_string('tasknotdeleted', ParamsHelper::PLUGIN),
            'status' => apilib::STATUS_ERROR,
        ];
    }

    /**
     * Return data for delete_adhoc_task() method.
     *
     * @return external_single_structure
     */
    public static function delete_adhoc_task_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Response message.'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

    /**
     * Parameters for save_task() method.
     *
     * @return external_function_parameters
     */
    public static function save_task_parameters() {
        return new external_function_parameters([
            'data'   => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Save plugin task configuration.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function save_task($data) {

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
            self::reset_datatype_parameters(), [
                'data' => $data,
            ]
        );

        // Validate parameters.
        $params = apilib::validate_parameters($params['data'], [
            'taskname' => PARAM_TEXT,
            'disabled' => PARAM_INT,
            'minute' => PARAM_TEXT,
            'hour' => PARAM_TEXT,
            'day' => PARAM_TEXT,
            'month' => PARAM_TEXT,
            'dayofweek' => PARAM_TEXT,
        ]);

        $classname = 'local_intellidata\task\\' . $params['taskname'];

        try {
            if (TasksHelper::save_scheduled_task($classname, $params)) {
                return [
                    'data' => get_string('scheduledtaskupdated', ParamsHelper::PLUGIN),
                    'status' => apilib::STATUS_SUCCESS,
                ];
            }
        } catch (Exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        return [
            'data' => get_string('scheduledtasknotupdated', ParamsHelper::PLUGIN),
            'status' => apilib::STATUS_ERROR,
        ];
    }

    /**
     * Return data for delete_adhoc_task() method.
     *
     * @return external_single_structure
     */
    public static function save_task_returns() {
        return new external_single_structure(
            [
                'data' => new external_value(PARAM_TEXT, 'Response message.'),
                'status' => new external_value(PARAM_TEXT, 'Response status'),
            ]
        );
    }

}
