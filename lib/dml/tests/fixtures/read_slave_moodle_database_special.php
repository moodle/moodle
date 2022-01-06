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
 * Database driver test class for testing moodle_read_slave_trait
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/read_slave_moodle_database.php');
require_once(__DIR__.'/read_slave_moodle_recordset_special.php');

/**
 * Database driver mock test class that uses read_slave_moodle_recordset_special
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_slave_moodle_database_special extends read_slave_moodle_database {
    /**
     * Returns empty array
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return string $handle handle property
     */
    public function get_records_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {
        $dbhandle = parent::get_records_sql($sql, $params);
        return [];
    }

    /**
     * Returns fake recordset
     * @param string $sql
     * @param array $params
     * @param int $limitfrom
     * @param int $limitnum
     * @return bool true
     */
    public function get_recordset_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {
        $dbhandle = parent::get_recordset_sql($sql, $params);
        return new read_slave_moodle_recordset_special();
    }

    /**
     * Count the records in a table where all the given conditions met.
     *
     * @param string $table The table to query.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return int The count of records returned from the specified criteria.
     */
    public function count_records($table, array $conditions = null) {
        return 1;
    }
}
