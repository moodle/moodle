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
 * Web service mod_brprojects external functions and service definitions.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.

defined('MOODLE_INTERNAL') || die;

$functions = [
    'local_intellidata_validate_credentials' => [
        'classname'     => 'local_intellidata_exportlib',
        'methodname'    => 'validate_credentials',
        'classpath'     => 'local/intellidata/classes/api/exportlib.php',
        'description'   => 'Validate plugin credentials',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => false,
    ],
    'local_intellidata_export_data' => [
        'classname'     => 'local_intellidata_exportlib',
        'methodname'    => 'export_data',
        'classpath'     => 'local/intellidata/classes/api/exportlib.php',
        'description'   => 'Export Data',
        'type'          => 'write',
        'ajax'          => true,
        'loginrequired' => false,
    ],
    'local_intellidata_get_data_files' => [
        'classname'     => 'local_intellidata_exportlib',
        'methodname'    => 'get_data_files',
        'classpath'     => 'local/intellidata/classes/api/exportlib.php',
        'description'   => 'Get Data Files',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => false,
    ],
    'local_intelldata_save_tracking' => [
        'classname'     => 'local_intellidata_trackinglib',
        'methodname'    => 'save_tracking',
        'classpath'     => 'local/intellidata/classes/api/trackinglib.php',
        'description'   => 'Save Tracking',
        'type'          => 'write',
        'ajax'          => true,
    ],
    'local_intellidata_get_live_data' => [
        'classname'     => 'local_intellidata_exportlib',
        'methodname'    => 'get_live_data',
        'classpath'     => 'local/intellidata/classes/api/exportlib.php',
        'description'   => 'Get some data in real time',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => false,
    ],
    'local_intellidata_run_report' => [
        'classname'   => 'local_intellidata_sqlreportlib',
        'methodname'  => 'run_report',
        'classpath'   => 'local/intellidata/classes/api/sqlreportlib.php',
        'description' => 'Run Supernova SQL report',
        'type'        => 'read',
    ],
    'local_intellidata_save_report' => [
        'classname'   => 'local_intellidata_sqlreportlib',
        'methodname'  => 'save_report',
        'classpath'   => 'local/intellidata/classes/api/sqlreportlib.php',
        'description' => 'Save Supernova SQL report',
        'type'        => 'write',
    ],
    'local_intellidata_set_lti_role' => [
        'classname'   => 'local_intellidata_datalib',
        'methodname'  => 'set_lti_role',
        'classpath'   => 'local/intellidata/classes/api/datalib.php',
        'description' => 'Accepts roles and ids of users that need to be assigned to LTI role',
        'type'        => 'write',
    ],
    'local_intellidata_delete_report' => [
        'classname'   => 'local_intellidata_sqlreportlib',
        'methodname'  => 'delete_report',
        'classpath'   => 'local/intellidata/classes/api/sqlreportlib.php',
        'description' => 'Delete Supernova SQL report',
        'type'        => 'write',
    ],
    'local_intellidata_get_dbschema_custom' => [
        'classname'   => 'local_intellidata_dbschemalib',
        'methodname'  => 'get_dbschema_custom',
        'classpath'   => 'local/intellidata/classes/api/dbschemalib.php',
        'description' => 'Get Moodle DB Schema for Custom Tables',
        'type'        => 'read',
    ],
    'local_intellidata_get_dbschema_unified' => [
        'classname'   => 'local_intellidata_dbschemalib',
        'methodname'  => 'get_dbschema_unified',
        'classpath'   => 'local/intellidata/classes/api/dbschemalib.php',
        'description' => 'Get Moodle DB Schema for Unified Tables',
        'type'        => 'read',
    ],
    'local_intellidata_get_dbschema_logs' => [
        'classname'   => 'local_intellidata_dbschemalib',
        'methodname'  => 'get_dbschema_logs',
        'classpath'   => 'local/intellidata/classes/api/dbschemalib.php',
        'description' => 'Get Moodle DB Schema for Logs Tables',
        'type'        => 'read',
    ],
    'local_intellidata_set_datatype' => [
        'classname'     => 'local_intellidata_exportlib',
        'methodname'    => 'set_datatype',
        'classpath'     => 'local/intellidata/classes/api/exportlib.php',
        'description'   => 'Set new or update existing datatype in Moodle.',
        'type'          => 'write',
    ],
    'local_intellidata_get_tasks_logs' => [
        'classname'     => 'local_intellidata_logslib',
        'methodname'    => 'get_tasks_logs',
        'classpath'     => 'local/intellidata/classes/api/logslib.php',
        'description'   => 'Get plugin tasks logs from Moodle.',
        'type'          => 'read',
    ],
    'local_intellidata_get_export_logs' => [
        'classname'     => 'local_intellidata_logslib',
        'methodname'    => 'get_export_logs',
        'classpath'     => 'local/intellidata/classes/api/logslib.php',
        'description'   => 'Get export logs statistics from plugin.',
        'type'          => 'read',
    ],
    'local_intellidata_enable_processing' => [
        'classname'     => 'local_intellidata_exportlib',
        'methodname'    => 'enable_processing',
        'classpath'     => 'local/intellidata/classes/api/exportlib.php',
        'description'   => 'Enable data processing',
        'type'          => 'write',
        'ajax'          => true,
        'loginrequired' => false,
    ],
    'local_intellidata_get_plugin_config' => [
        'classname'     => 'local_intellidata_configlib',
        'methodname'    => 'get_plugin_config',
        'classpath'     => 'local/intellidata/classes/api/configlib.php',
        'description'   => 'Get plugin config',
        'type'          => 'read',
        'loginrequired' => false,
    ],
    'local_intellidata_set_plugin_config' => [
        'classname'     => 'local_intellidata_configlib',
        'methodname'    => 'set_plugin_config',
        'classpath'     => 'local/intellidata/classes/api/configlib.php',
        'description'   => 'Set plugin config',
        'type'          => 'write',
        'loginrequired' => false,
    ],
    'local_intellidata_get_data' => [
        'classname'   => 'local_intellidata_datalib',
        'methodname'  => 'get_data',
        'classpath'   => 'local/intellidata/classes/api/datalib.php',
        'description' => 'Run SQL and retrieve data from LMS',
        'type'        => 'read',
    ],
    'local_intellidata_get_bbcollsessions_data' => [
        'classname'     => 'local_intellidata_exportlib',
        'methodname'    => 'get_bbcollsessions',
        'classpath'     => 'local/intellidata/classes/api/exportlib.php',
        'description'   => 'Get get Blackboard Collaborate Sessions relations to Course',
        'type'          => 'read',
        'ajax'          => true,
        'loginrequired' => false,
    ],
    'local_intellidata_get_adhoc_tasks' => [
        'classname'     => 'local_intellidata_logslib',
        'methodname'    => 'get_adhoc_tasks',
        'classpath'     => 'local/intellidata/classes/api/logslib.php',
        'description'   => 'Get plugin scheduled adhoc tasks list from Moodle.',
        'type'          => 'read',
    ],
    'local_intellidata_reset_migration' => [
        'classname'     => 'local_intellidata_configlib',
        'methodname'    => 'reset_migration',
        'classpath'     => 'local/intellidata/classes/api/configlib.php',
        'description'   => 'Reset full migration in Plugin.',
        'type'          => 'write',
    ],
    'local_intellidata_reset_datatype' => [
        'classname'     => 'local_intellidata_configlib',
        'methodname'    => 'reset_datatype',
        'classpath'     => 'local/intellidata/classes/api/configlib.php',
        'description'   => 'Reset specific datatype in Plugin.',
        'type'          => 'write',
    ],
    'local_intellidata_run_migration' => [
        'classname'     => 'local_intellidata_configlib',
        'methodname'    => 'run_migration',
        'classpath'     => 'local/intellidata/classes/api/configlib.php',
        'description'   => 'Schedule adhoc migration task.',
        'type'          => 'write',
    ],
    'local_intellidata_get_running_tasks' => [
        'classname'     => 'local_intellidata_logslib',
        'methodname'    => 'get_running_tasks',
        'classpath'     => 'local/intellidata/classes/api/logslib.php',
        'description'   => 'Get plugin running tasks list from Moodle.',
        'type'          => 'read',
    ],
    'local_intellidata_calculate_migration_progress' => [
        'classname'     => 'local_intellidata_logslib',
        'methodname'    => 'calculate_migration_progress',
        'classpath'     => 'local/intellidata/classes/api/logslib.php',
        'description'   => 'Trigger migration calculation adhoc task.',
        'type'          => 'write',
    ],
    'local_intellidata_delete_adhoc_task' => [
        'classname'     => 'local_intellidata_configlib',
        'methodname'    => 'delete_adhoc_task',
        'classpath'     => 'local/intellidata/classes/api/configlib.php',
        'description'   => 'Deletes plugin adhoc task.',
        'type'          => 'write',
    ],
    'local_intellidata_save_task' => [
        'classname'     => 'local_intellidata_configlib',
        'methodname'    => 'save_task',
        'classpath'     => 'local/intellidata/classes/api/configlib.php',
        'description'   => 'Save plugin task configuration.',
        'type'          => 'write',
    ],
];

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = [
    'IntelliData Service' => [
        'functions' => [
            'local_intellidata_validate_credentials',
            'local_intellidata_get_data_files',
            'local_intellidata_get_live_data',
            'local_intellidata_export_data',
            'local_intelldata_save_tracking',
            'local_intellidata_run_report',
            'local_intellidata_save_report',
            'local_intellidata_set_lti_role',
            'local_intellidata_delete_report',
            'local_intellidata_get_dbschema_custom',
            'local_intellidata_get_dbschema_unified',
            'local_intellidata_get_dbschema_logs',
            'local_intellidata_set_datatype',
            'local_intellidata_get_tasks_logs',
            'local_intellidata_get_export_logs',
            'local_intellidata_enable_processing',
            'local_intellidata_get_plugin_config',
            'local_intellidata_set_plugin_config',
            'local_intellidata_get_data',
            'local_intellidata_get_bbcollsessions_data',
            'local_intellidata_get_adhoc_tasks',
            'local_intellidata_reset_migration',
            'local_intellidata_reset_datatype',
            'local_intellidata_run_migration',
            'local_intellidata_get_running_tasks',
            'local_intellidata_calculate_migration_progress',
            'local_intellidata_delete_adhoc_task',
            'local_intellidata_save_task',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'downloadfiles' => 1,
    ],
];
