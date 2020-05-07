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
 * Test SQL code generator class
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../ddl/sql_generator.php');

/**
 * Test SQL code generator class
 *
 * @package    core
 * @category   ddl
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_sql_generator extends sql_generator {
    // @codingStandardsIgnoreStart
    /**
     * Reset a sequence to the id field of a table.
     *
     * @param xmldb_table|string $table name of table or the table object.
     * @return array of sql statements
     */
    public function getResetSequenceSQL($table) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to create temporary table (inside one array).
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @return array of sql statements
     */
    public function getCreateTempTableSQL($xmldbtable) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one XMLDB Type, length and decimals, returns the DB proper SQL type.
     *
     * @param int $xmldbtype The xmldb_type defined constant. XMLDB_TYPE_INTEGER and other XMLDB_TYPE_* constants.
     * @param int $xmldblength The length of that data type.
     * @param int $xmldbdecimals The decimal places of precision of the data type.
     * @return string The DB defined data type.
     */
    public function getTypeSQL($xmldbtype, $xmldblength = null, $xmldbdecimals = null) {
    // @codingStandardsIgnoreEnd
        return '';
    }

    // @codingStandardsIgnoreStart
    /**
     * Returns the code (array of statements) needed to add one comment to the table.
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @return array Array of SQL statements to add one comment to the table.
     */
    function getCommentSQL ($xmldbtable) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to add its default
     * (usually invoked from getModifyDefaultSQL()
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @param xmldb_field $xmldbfield The xmldb_field object instance.
     * @return array Array of SQL statements to create a field's default.
     */
    public function getCreateDefaultSQL($xmldbtable, $xmldbfield) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop its default
     * (usually invoked from getModifyDefaultSQL()
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @param xmldb_field $xmldbfield The xmldb_field object instance.
     * @return array Array of SQL statements to create a field's default.
     */
    public function getDropDefaultSQL($xmldbtable, $xmldbfield) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Returns an array of reserved words (lowercase) for this DB
     * @return array An array of database specific reserved words
     */
    public static function getReservedWords() {
    // @codingStandardsIgnoreEnd
        return [];
    }

}
