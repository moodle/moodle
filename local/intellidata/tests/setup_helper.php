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
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata;

use local_intellidata\helpers\SettingsHelper;

/**
 * Test setup test case helper.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class setup_helper {

    /**
     * Enable intellidata plugin.
     */
    public static function enable_plugin() {
        SettingsHelper::set_setting('enabled', 1);
        SettingsHelper::set_setting('enabledtracking', 1);
    }

    /**
     * Setup configuration for testing.
     */
    public static function setup_tests_config() {

        // Enable plugin.
        self::enable_plugin();

        // Enable DB Storage for testing.
        self::enable_db_storage();

        // Enable json format for testing.
        self::setup_json_exportformat();

        // Enable Data cleaning.
        self::enable_datacleaning();
    }

    /**
     * Enable Custom DB Driver.
     */
    public static function enable_custom_driver() {
        SettingsHelper::set_setting('enablecustomdbdriver', 1);
        SettingsHelper::set_setting('newtracking', 1);
    }

    /**
     * Enable data cleaning.
     */
    public static function enable_datacleaning() {
        SettingsHelper::set_setting('enabledatacleaning', 1);
    }

    /**
     * Enable db storage.
     */
    public static function enable_db_storage() {
        SettingsHelper::set_setting('trackingstorage', 1);
    }

    /**
     * Enable files storage.
     */
    public static function enable_file_storage() {
        SettingsHelper::set_setting('trackingstorage', 0);
    }

    /**
     * Set json export format.
     */
    public static function setup_json_exportformat() {
        SettingsHelper::set_setting('exportdataformat', 'json');
    }

    /**
     * Set csv export format.
     */
    public static function setup_csv_exportformat() {
        SettingsHelper::set_setting('exportdataformat', 'csv');
    }

    /**
     * Disable eventstracking.
     */
    public static function disable_eventstracking() {
        SettingsHelper::set_setting('eventstracking', 0);
    }

    /**
     * Enable eventstracking.
     */
    public static function enable_eventstracking() {
        SettingsHelper::set_setting('eventstracking', 1);
    }

    /**
     * Disable exportfilesduringmigration.
     */
    public static function disable_exportfilesduringmigration() {
        SettingsHelper::set_setting('exportfilesduringmigration', 0);
    }

    /**
     * Enable exportfilesduringmigration.
     */
    public static function enable_exportfilesduringmigration() {
        SettingsHelper::set_setting('exportfilesduringmigration', 1);
    }

    /**
     * Disable exportfilesduringmigration.
     */
    public static function set_migration_limit($value = 0) {
        SettingsHelper::set_setting('migrationrecordslimit', $value);
    }
}
