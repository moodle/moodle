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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

use local_intellidata\api\apilib;
use local_intellidata\helpers\DBHelper;
use local_intellidata\repositories\reports_repository;
use local_intellidata\services\encryption_service;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class local_intellidata_sqlreportlib extends external_api {

    /**
     * Parameters for run_report() method.
     *
     * @return external_function_parameters
     */
    public static function run_report_parameters() {
        return new external_function_parameters([
            'data' => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Run sql report in Moodle.
     *
     * @param $data
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function run_report($data) {
        global $DB, $CFG;

        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(self::run_report_parameters(), ['data' => $data]);

        // Validate if credentials not empty.
        $encryptionservice = new encryption_service();

        if (!$encryptionservice->validate_credentials()) {
            return [
                'data' => 'emptycredentials',
                'status' => apilib::STATUS_ERROR,
            ];
        }

        // Validate parameters.
        $params = (object) apilib::validate_parameters($params['data'], [
            'external_identifier' => PARAM_TEXT,
            'debug' => PARAM_INT,
            'sortdir' => PARAM_ALPHA,
            'sortcol' => PARAM_INT,
            'search_value' => PARAM_TEXT,
            'search_column' => PARAM_TEXT,
            'timestart' => PARAM_INT,
            'timefinish' => PARAM_INT,
            'courses' => PARAM_SEQUENCE,
            'start' => PARAM_INT,
            'length' => PARAM_INT,
        ]);

        $report = reports_repository::get_by_external_identifier($params->external_identifier);
        $data = [];

        if ($report) {
            $query = $report->sqlcode;
            $sqlparams = [];

            // Prepare sorting.
            if (strrpos($query, ':sorting') !== false) {
                $params->sortcol = $params->sortcol + 1;

                if ($params->sortdir && $params->sortcol) {
                    $sorting = " ORDER BY {$params->sortcol} {$params->sortdir}";
                    $query = str_replace(":sorting", $sorting, $query);
                } else {
                    $query = str_replace(":sorting", "", $query);
                }
            }

            // Filter data with datefilter.
            if (strpos($query, ':datefilter[') !== false) {
                $filterstart = strpos($query, ':datefilter[');
                $columnstart = $filterstart + 12;
                $columnend = strpos($query, ']', $filterstart) - $columnstart;
                $columnname = substr($query, $columnstart, $columnend);
                $val = ":datefilter[{$columnname}]";

                $params->timestart = isset($params->timestart) ? $params->timestart : false;
                $params->timefinish = isset($params->timefinish) ? $params->timefinish : false;

                if ($params->timestart && $params->timefinish && $columnname) {
                    $sqlparams['timestart'] = $params->timestart;
                    $sqlparams['timefinish'] = $params->timefinish;
                    $like = " AND {$columnname} BETWEEN :timestart AND :timefinish ";
                    $query = str_replace($val, $like, $query);
                } else {
                    $query = str_replace($val, "", $query);
                }
            }

            // Filter data with course filter.
            if ($coursefilter = strpos($query, ':coursefilter[')) {
                $filterstart = $coursefilter + 14;
                $filterend = strpos($query, ']', $coursefilter) - $filterstart;
                $columnname = substr($query, $filterstart, $filterend);
                $val = ":coursefilter[{$columnname}]";

                $params->courses = isset($params->courses) ? $params->courses : false;

                if ($params->courses && $columnname) {
                    list($sql, $coursefilter) = $DB->get_in_or_equal(explode(",", $params->courses), SQL_PARAMS_NAMED, 'courses');
                    $sqlparams = array_merge($sqlparams, $coursefilter);
                    $like = " AND {$columnname} {$sql} ";
                    $query = str_replace($val, $like, $query);
                } else {
                    $query = str_replace($val, "", $query);
                }
            }

            // Filter data with additional filters.
            if (strrpos($query, ':filter') !== false) {
                $query = str_replace(":filter", "", $query);

                $params->search_value = isset($params->search_value) ? addslashes($params->search_value) : false;
                $params->search_column = isset($params->search_column) ? $params->search_column : false;

                // Enclose the column name in backticks for MySQL Type.
                if (DBHelper::is_mysql_type() && $params->search_column) {
                    $params->search_column = "`" . $params->search_column . "`";
                }

                if (($params->search_value || $params->search_value === '0') && $params->search_column) {
                    $key = 'build_by_sql_search';
                    $query = "SELECT t.*
                                    FROM ({$query}) t
                                   WHERE t." . $DB->sql_like($params->search_column,
                                       ':' . $key, false, false
                                    );
                    $sqlparams[$key] = '%' . $params->search_value . '%';
                }
            }

            if ($params->debug === 1) {
                $CFG->debug = (E_ALL | E_STRICT);
                $CFG->debugdisplay = 1;
            }
            if ($params->debug === 2) {
                $data = [$report->sqlcode, $query, $sqlparams];
            } else if (isset($params->start) && $params->length != 0 && $params->length != -1) {
                $data = $DB->get_records_sql($query, $sqlparams, $params->start, $params->length);
            } else {
                $data = $DB->get_records_sql($query, $sqlparams);
            }
        }

        return ['status' => apilib::STATUS_SUCCESS, 'data' => json_encode($data)];
    }

    /**
     * Run report returns.
     *
     * @return external_single_structure
     */
    public static function run_report_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Response status'),
            'data' => new external_value(PARAM_RAW, 'Report data'),
        ]);
    }

    /**
     * Save report parameters.
     *
     * @return external_function_parameters
     */
    public static function save_report_parameters() {
        return new external_function_parameters([
            'data' => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Save report.
     *
     * @param $data
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function save_report($data) {
        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(self::save_report_parameters(), ['data' => $data]);

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
            'external_identifier' => PARAM_TEXT,
            'service' => PARAM_URL,
            'name' => PARAM_TEXT,
            'sqlcode' => PARAM_TEXT,
        ]);
        $report = (object) $params;
        // Deactivate report every time.
        $report->status = 0;
        $data = (array) reports_repository::update_or_create($report);

        return [
            'status' => apilib::STATUS_SUCCESS,
            'data' => $data,
        ];
    }

    /**
     * Save report returns.
     *
     * @return external_single_structure
     */
    public static function save_report_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Response status'),
            'data' => new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'Report ID'),
                    'status' => new external_value(PARAM_INT, 'Report status'),
                    'name' => new external_value(PARAM_TEXT, 'Report name'),
                    'sqlcode' => new external_value(PARAM_TEXT, 'Report SQL'),
                    'external_identifier' => new external_value(PARAM_TEXT, 'External identifier'),
                    'service' => new external_value(PARAM_URL, 'Service'),
                    'timecreated' => new external_value(PARAM_INT, 'Report time created'),
                ]
            ),
        ]);
    }

    /**
     * Delete report parameters.
     *
     * @return external_function_parameters
     */
    public static function delete_report_parameters() {
        return new external_function_parameters([
            'data' => new external_value(PARAM_RAW, 'Request params'),
        ]);
    }

    /**
     * Delete report.
     *
     * @param $data
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function delete_report($data) {
        try {
            apilib::check_auth();
        } catch (\moodle_exception $e) {
            return [
                'data' => $e->getMessage(),
                'status' => apilib::STATUS_ERROR,
            ];
        }

        $context = context_system::instance();
        self::validate_context($context);

        $params = self::validate_parameters(self::save_report_parameters(), ['data' => $data]);

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
            'external_identifier' => PARAM_TEXT,
        ]);

        reports_repository::delete_by_external_identifier($params['external_identifier']);

        return [
            'status' => apilib::STATUS_SUCCESS,
            'data' => '',
        ];
    }

    /**
     * Delete report returns.
     *
     * @return external_single_structure
     */
    public static function delete_report_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Response status'),
            'data' => new external_value(PARAM_TEXT, ''),
        ]);
    }
}
