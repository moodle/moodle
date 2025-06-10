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

namespace local_intellidata\repositories;

use local_intellidata\persistent\datatypeconfig;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class optional_tables_repository extends base_tables_repository {

    /**
     * Exclude tables from the list of tables to be processed.
     *
     * @param $dbtables
     * @return mixed
     */
    public static function exclude_tables($dbtables) {
        $tablestodelete = self::get_excluded_tables($dbtables);

        if (count($tablestodelete)) {
            foreach ($tablestodelete as $key) {
                unset($dbtables[$key]);
            }
        }

        return $dbtables;
    }

    /**
     * Retrieve tables to exclude from the list of tables to be processed.
     *
     * @param $dbtables
     * @return array
     */
    public static function get_excluded_tables($dbtables) {
        $tablestodelete = [];

        foreach (self::get_defined_tables() as $tablename => $datatype) {
            self::validate_table($dbtables, $tablename, $tablestodelete);
        }

        return $tablestodelete;
    }

    /**
     * Validate single table to be excluded or not.
     *
     * @param $dbtables
     * @param $table
     * @param $keystodelete
     */
    private static function validate_table($dbtables, $table, &$keystodelete) {
        if (($key = array_search($table, $dbtables)) !== false) {
            $keystodelete[$key] = $table;
        }
    }

    /**
     * Tables list from config table to exclude from the list of tables to be processed.
     *
     * @return string[]
     */
    protected static function get_defined_tables() {
        return config_repository::get_optional_datatypes(datatypeconfig::STATUS_DISABLED);
    }
}
