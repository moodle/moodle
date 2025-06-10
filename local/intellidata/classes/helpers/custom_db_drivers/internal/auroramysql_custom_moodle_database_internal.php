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
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../../../lib/dml/auroramysql_native_moodle_database.php');

/**
 * Custom mysqli class representing moodle database interface.
 *
 * @package    local_intellidata
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class auroramysql_custom_moodle_database_internal extends \auroramysql_native_moodle_database {

    /**
     * Get a number of records as a moodle_recordset using a SQL statement.
     *
     * Since this method is a little less readable, use of it should be restricted to
     * code where it's possible there might be large datasets being returned.  For known
     * small datasets use get_records_sql - it leads to simpler code.
     *
     * The return type is like:
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset instance
     * @throws dml_exception A DML specific exception is thrown for any errors.
     * @see function get_recordset.
     *
     */
    public function get_recordset_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {
        list($limitfrom, $limitnum) = $this->normalise_limit_from_num($limitfrom, $limitnum);

        if ($limitfrom || $limitnum) {
            if ($limitnum < 1) {
                $limitnum = "18446744073709551615";
            }
            $sql .= " LIMIT $limitfrom, $limitnum";
        }

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        // No MYSQLI_USE_RESULT here, it would block write ops on affected tables.
        $result = $this->mysqli->query($rawsql, MYSQLI_USE_RESULT);
        $this->query_end($result);

        return $this->create_recordset($result);
    }
}
