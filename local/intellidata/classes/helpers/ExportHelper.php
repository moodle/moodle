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
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

use local_intellidata\repositories\export_log_repository;
use local_intellidata\repositories\tracking\tracking_repository;
use local_intellidata\services\database_service;
use local_intellidata\services\encryption_service;
use local_intellidata\services\export_service;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class ExportHelper {

    /**
     * Process export files.
     *
     * @param export_service $exportservice
     * @param array $params
     * @param bool $forceexport
     *
     * @return array
     */
    public static function process_export(export_service $exportservice, $params = []) {
        if (TrackingHelper::eventstracking_enabled() || !empty($params['forceexport'])) {
            // Export data to files.
            self::process_data_export($exportservice, $params);
        }

        // Export files to moodledata.
        return self::process_files_export($exportservice);
    }

    /**
     * Process export data.
     *
     * @param export_service $exportservice
     * @param array $params
     * @return array
     */
    public static function process_data_export(export_service $exportservice, $params = []) {

        $services = [
            'encryptionservice' => new encryption_service(),
            'exportservice' => $exportservice,
            'exportlogrepository' => new export_log_repository(),
        ];

        // Export static tables.
        $databaseservice = new database_service(true, $services);
        $databaseservice->export_tables($params);
    }

    /**
     * Process export files.
     *
     * @param export_service $exportservice
     * @return array
     */
    public static function process_files_export(export_service $exportservice, $params = []) {

        if (TrackingHelper::new_tracking_enabled()) {
            (new tracking_repository())->export_records();

            $params['except_rewritable'] = true;
        }

        // Export files to Moodledata.
        $exportservice->save_files($params);

        // Export migration files to Moodledata.
        $exportservice->set_migration_mode();
        $exportservice->save_files($params);

        // Set last export date.
        SettingsHelper::set_lastexportdate();

        return $exportservice->get_files();
    }

    /**
     * Process export file.
     *
     * @param export_service $exportservice
     * @return array
     */
    public static function process_file_export(export_service $exportservice, $params) {

        // Export files to Moodledata.
        $exportservice->save_files($params);

        return $exportservice->get_files($params);
    }

    /**
     * Return next table to export.
     *
     * @param $tables
     * @return int|string
     */
    public static function get_export_table($tables) {
        $exportdatatype = SettingsHelper::get_setting('exportdatatype');
        return !empty($exportdatatype) ? $exportdatatype : self::get_first_tablename($tables);
    }

    /**
     * Return next table to process.
     *
     * @param $tables
     * @param string $processingtable
     * @return int|string
     */
    public static function get_next_table($tables, $processingtable = '') {

        $nexttable = self::get_first_tablename($tables);
        $currenttable = false;

        foreach ($tables as $key => $datatype) {
            if ($currenttable) {
                $nexttable = $key;
                break;
            }
            if ($key == $processingtable) {
                $currenttable = true;
            }
        }

        return $nexttable;
    }

    /**
     * Get first table name from tables list.
     *
     * @param $tables
     * @return mixed|string
     */
    public static function get_first_tablename($tables) {
        $firsttable = reset($tables);
        return (!empty($firsttable['name'])) ? $firsttable['name'] : '';
    }

    /**
     * Set next datatype to export.
     *
     * @param $migrationdatatype
     * @param int $migrationstart
     */
    public static function set_next_export_params($exportdatatype = '', $exportstartstart = 0) {
        SettingsHelper::set_setting('exportdatatype', $exportdatatype);
        SettingsHelper::set_setting('exportstart', $exportstartstart);
    }

    /**
     * Reset export details.
     *
     * @return void
     */
    public static function reset_export_details() {
        self::set_next_export_params();
    }
}
