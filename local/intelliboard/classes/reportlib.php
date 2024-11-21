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
 * @package    local_intelliboard
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_intelliboard_report extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function run_report_parameters() {
        return new external_function_parameters(
            array(
                'report' => new external_single_structure(
                    array(
                        'appid' => new external_value(PARAM_INT, 'External app ID'),
                        'debug' => new external_value(PARAM_INT, 'Debug Mode'),
                        'sortdir' => new external_value(PARAM_ALPHA, 'Sorting dir ASC/DESC', VALUE_OPTIONAL, ''),
                        'sortcol' => new external_value(PARAM_INT, 'Sorting column', VALUE_OPTIONAL, 1),
                        'filterval' => new external_value(PARAM_TEXT, 'Filter column', VALUE_OPTIONAL, ''),
                        'filtercol' => new external_value(PARAM_TEXT, 'Filter column', VALUE_OPTIONAL, ''),
                        'timestart' => new external_value(PARAM_INT, 'Report filter date[start]', VALUE_OPTIONAL, 0),
                        'timefinish' => new external_value(PARAM_INT, 'Report filter date[finish]', VALUE_OPTIONAL, 0),
                        'courses' => new external_value(PARAM_SEQUENCE, 'Course IDs SEQUENCE', VALUE_OPTIONAL, 0),
                        'vendors' => new external_value(PARAM_SEQUENCE, 'Vendors IDs SEQUENCE', VALUE_OPTIONAL, 0),
                        'start' => new external_value(PARAM_INT, 'Report pagination start'),
                        'length' => new external_value(PARAM_INT, 'Report pagination length'),
                        'vendor_user_id' => new external_value(PARAM_INT, 'Vendor User ID', VALUE_OPTIONAL, 0),
                    )
                )
            )
        );
    }

    /**
     * Create one report
     *
     * @param array $report.
     * @return array An array of arrays
     * @since Moodle 2.5
     */
    public static function run_report($report) {
        global $CFG, $DB;

        if (isset($CFG->intelliboardsql) and $CFG->intelliboardsql == false) {
            throw new moodle_exception('invalidaccess', 'error');
        }

        $params = self::validate_parameters(self::run_report_parameters(), array('report' => $report));

        $transaction = $DB->start_delegated_transaction();

        self::validate_context(context_system::instance());

        $params = (object) $params['report'];

        $data = [];

        if ($report = $DB->get_record('local_intelliboard_reports', ['status' => 1, 'appid' => $params->appid])) {
            if ($report->sqlcode) {
                $query = str_replace("prefix_", "mdl_", base64_decode($report->sqlcode));

                $filters = [];
                if (strrpos($query, ':sorting') !== false) {
                    $params->sortdir = isset($params->sortdir)? $params->sortdir : false;
                    $params->sortcol = isset($params->sortcol)? $params->sortcol : 0;

                    $params->sortcol = $params->sortcol + 1;
                    if ($params->sortdir and $params->sortcol) {
                        $sorting = " ORDER BY {$params->sortcol} {$params->sortdir}";
                        $query = str_replace(":sorting", $sorting, $query);
                    } else {
                        $query = str_replace(":sorting", "", $query);
                    }
                }

                if ($datefilter = strpos($query, ':datefilter[')) {
                    $start =  $datefilter+12;
                    $end =  strpos($query, ']', $datefilter) - $start;
                    $col = substr($query, $start, $end);
                    $val = ":datefilter[$col]";

                    $params->timestart = isset($params->timestart)? $params->timestart : false;
                    $params->timefinish = isset($params->timefinish)? $params->timefinish : false;

                    if ($params->timestart and $params->timefinish and $col) {
                        $filters['timestart'] = $params->timestart;
                        $filters['timefinish'] = $params->timefinish;
                        $like = " AND $col BETWEEN :timestart AND :timefinish ";
                        $query = str_replace($val, $like, $query);
                    } else {
                        $query = str_replace($val, "", $query);
                    }
                }
                if ($coursefilter = strpos($query, ':coursefilter[')) {
                    $start =  $coursefilter+14;
                    $end =  strpos($query, ']', $coursefilter) - $start;
                    $col = substr($query, $start, $end);
                    $val = ":coursefilter[$col]";

                    $params->courses = isset($params->courses)? $params->courses : false;

                    if ($params->courses and $col) {
                        list($sql, $coursefilter) = $DB->get_in_or_equal(explode(",", $params->courses), SQL_PARAMS_NAMED, 'courses', true);
                        $filters = array_merge($filters, $coursefilter);
                        $like = " AND $col $sql ";
                        $query = str_replace($val, $like, $query);
                    } else {
                        $query = str_replace($val, "", $query);
                    }
                }
                if ($intellicartvendorfilter = strpos($query, ':intellicartvendorfilter[')) {
                    $start =  $intellicartvendorfilter+25;
                    $end =  strpos($query, ']', $intellicartvendorfilter) - $start;
                    $col = substr($query, $start, $end);
                    $val = ":intellicartvendorfilter[$col]";

                    if ($params->vendors and $col) {
                        list($innersql, $intellicartvendorfilter) = $DB->get_in_or_equal(explode(",", $params->vendors), SQL_PARAMS_NAMED, 'vndid', true);

                        $sql = "SELECT liu.userid
                                   FROM {local_intellicart_users} liu
                                   JOIN {local_intellicart_vendors} vnd ON vnd.id = liu.instanceid AND vnd.id $innersql
                                  WHERE liu.role = 'user' AND liu.type = 'vendor'
                               GROUP BY liu.userid";

                        $filters = array_merge($filters, $intellicartvendorfilter);
                        $like = " AND $col IN($sql) ";
                        $query = str_replace($val, $like, $query);
                    } else {
                        $query = str_replace($val, "", $query);
                    }
                }

                if (strrpos($query, ':filter') !== false) {
                    $query = str_replace(":filter", "", $query);

                    $params->filterval = isset($params->filterval) ? str_replace("&apos;", '_', $params->filterval) : false;
                    $params->filtercol = isset($params->filtercol) ? $params->filtercol : false;

                    if ($params->filterval and $params->filtercol) {
                        $key = 'build_by_sql_search';
                        $query = "SELECT t.*
                                    FROM ({$query}) t
                                   WHERE t." . $DB->sql_like('`'.$params->filtercol.'`', ":" . $key, false, false);
                        $filters[$key] = "%".$params->filterval."%";
                    }
                }

                if ($intellicartvendorfilter = strpos($query, ':managervendors')) {
                    if (!empty($params->vendor_user_id)) {
                        $managersvendors = $DB->get_records_sql(
                            "SELECT DISTINCT instanceid
                               FROM {local_intellicart_users}
                              WHERE type = 'vendor' AND userid = :userid AND role = 'manager'",
                            ['userid' => $params->vendor_user_id]
                        );

                        if (!$managersvendors) {
                            $vendorsids = '-1';
                        } else {
                            $vendorsids = implode(',', array_keys($managersvendors));
                        }
                        $query = str_replace(':managervendors', " IN ({$vendorsids})", $query);
                    } else {
                        $query = str_replace(':managervendors', ' > 0', $query);
                    }
                }

                if ($params->debug === 1) {
                    $CFG->debug = (E_ALL | E_STRICT);
                    $CFG->debugdisplay = 1;
                }
                if ($params->debug === 2) {
                    $data = [$report->sqlcode, $query, $filters];
                } elseif(isset($params->start) and $params->length != 0 and $params->length != -1){
                    $data = $DB->get_records_sql($query, $filters, $params->start, $params->length);
                } else {
                    $data = $DB->get_records_sql($query, $filters);
                }
            }
        }

        $transaction->allow_commit();

        return ['jsondata' => json_encode($data)];
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function run_report_returns() {
       return new external_single_structure(
            array(
                'jsondata' => new external_value(PARAM_RAW, 'Report data'),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function save_report_parameters() {
        return new external_function_parameters(
            array(
                'report' => new external_single_structure(
                    array(
                        'appid' => new external_value(PARAM_INT, 'External app ID'),
                        'name' => new external_value(PARAM_TEXT, 'Report name'),
                        'sqlcode' => new external_value(PARAM_TEXT, 'SQL code of custom report')
                    )
                )
            )
        );
    }

    /**
     * Create one report
     *
     * @param array $report.
     * @return array An array of arrays
     * @since Moodle 2.5
     */
    public static function save_report($report) {
        global $CFG, $DB;

        if (isset($CFG->intelliboardsql) and $CFG->intelliboardsql == false) {
            throw new moodle_exception('invalidaccess', 'error');
        }

        $params = self::validate_parameters(self::save_report_parameters(), array('report' => $report));

        $transaction = $DB->start_delegated_transaction();

        self::validate_context(context_system::instance());

        $report = (object) $params['report'];

        //Deactivate report every time
        $report->status = 0;

        if ($data = $DB->get_record('local_intelliboard_reports', ['appid' => $report->appid])) {
            $report->id = $data->id;
            $DB->update_record('local_intelliboard_reports', $report);
        } else {
            $report->timecreated = time();
            $DB->insert_record('local_intelliboard_reports', $report);
        }

        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function save_report_returns() {
       return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_report_parameters() {
        return new external_function_parameters(
            array (
                'appid' => new external_value(PARAM_INT, 'External app ID')
            )
        );
    }

    /**
     *
     * @param array $report
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_report($params) {
        global $CFG, $DB;

        if (isset($CFG->intelliboardsql) and $CFG->intelliboardsql == false) {
            throw new moodle_exception('invalidaccess', 'error');
        }

        $params = self::validate_parameters(self::delete_report_parameters(), array('appid' => $params));

        $transaction = $DB->start_delegated_transaction();

        self::validate_context(context_system::instance());

        $DB->delete_records('local_intelliboard_reports', ['appid' => $params['appid']]);

        $transaction->allow_commit();

        return null;
    }

    public static function delete_report_returns() {
        return null;
    }
}
