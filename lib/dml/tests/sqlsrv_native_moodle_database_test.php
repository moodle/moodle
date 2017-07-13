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
 * Test sqlsrv dml support.
 *
 * @package    core
 * @category   dml
 * @copyright  2017 John Okely
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/lib/dml/sqlsrv_native_moodle_database.php');

/**
 * Test case for sqlsrv dml support.
 *
 * @package    core
 * @category   dml
 * @copyright  2017 John Okely
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sqlsrv_native_moodle_database_testcase extends advanced_testcase {

    public function setUp() {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Dataprovider for test_add_no_lock_to_temp_tables
     * @return array Data for test_add_no_lock_to_temp_tables
     */
    public function add_no_lock_to_temp_tables_provider() {
        return [
            "Basic temp table, nothing following" => [
                'input' => 'SELECT * FROM {table_temp}',
                'expected' => 'SELECT * FROM {table_temp} WITH (NOLOCK)'
            ],
            "Basic temp table, with capitalised alias" => [
                'input' => 'SELECT * FROM {table_temp} MYTABLE',
                'expected' => 'SELECT * FROM {table_temp} MYTABLE WITH (NOLOCK)'
            ],
            "Temp table with alias, and another non-temp table" => [
                'input' => 'SELECT * FROM {table_temp} x WHERE y in (SELECT y from {table2})',
                'expected' => 'SELECT * FROM {table_temp} x WITH (NOLOCK) WHERE y in (SELECT y from {table2})'
            ],
            "Temp table with reserve word following, no alias" => [
                'input' => 'SELECT DISTINCT * FROM {table_temp} WHERE y in (SELECT y from {table2} nottemp)',
                'expected' => 'SELECT DISTINCT * FROM {table_temp} WITH (NOLOCK) WHERE y in (SELECT y from {table2} nottemp)'
            ],
            "Temp table with reserve word, lower case" => [
                'input' => 'SELECT DISTINCT * FROM {table_temp} where y in (SELECT y from {table2} nottemp)',
                'expected' => 'SELECT DISTINCT * FROM {table_temp} WITH (NOLOCK) where y in (SELECT y from {table2} nottemp)'
            ],
            "Another reserve word test" => [
                'input' => 'SELECT DISTINCT * FROM {table_temp} PIVOT y in (SELECT y from {table2} nottemp)',
                'expected' => 'SELECT DISTINCT * FROM {table_temp} WITH (NOLOCK) PIVOT y in (SELECT y from {table2} nottemp)'
            ],
            "Another reserve word test should fail" => [
                'input' => 'SELECT DISTINCT * FROM {table_temp} PIVOT y in (SELECT y from {table2} nottemp)',
                'expected' => 'SELECT DISTINCT * FROM {table_temp} WITH (NOLOCK) PIVOT y in (SELECT y from {table2} nottemp)'
            ],
            "Temp table with an alias starting with a keyword" => [
                'input' => 'SELECT * FROM {table_temp} asx',
                'expected' => 'SELECT * FROM {table_temp} asx WITH (NOLOCK)'
            ],
            "Keep alias with underscore" => [
                'input' => 'SELECT * FROM {table_temp} alias_for_table',
                'expected' => 'SELECT * FROM {table_temp} alias_for_table WITH (NOLOCK)'
            ],
            "Alias with number" => [
                'input' => 'SELECT * FROM {table_temp} a5 WHERE y',
                'expected' => 'SELECT * FROM {table_temp} a5 WITH (NOLOCK) WHERE y'
            ],
            "Alias with number and underscore" => [
                'input' => 'SELECT * FROM {table_temp} a_5 WHERE y',
                'expected' => 'SELECT * FROM {table_temp} a_5 WITH (NOLOCK) WHERE y'
            ],
            "Temp table in subquery" => [
                'input' => 'select * FROM (SELECT DISTINCT * FROM {table_temp})',
                'expected' => 'select * FROM (SELECT DISTINCT * FROM {table_temp} WITH (NOLOCK))'
            ],
            "Temp table in subquery, with following commands" => [
                'input' => 'select * FROM (SELECT DISTINCT * FROM {table_temp} ) WHERE y',
                'expected' => 'select * FROM (SELECT DISTINCT * FROM {table_temp} WITH (NOLOCK) ) WHERE y'
            ],
            "Temp table in subquery, with alias" => [
                'input' => 'select * FROM (SELECT DISTINCT * FROM {table_temp} x) WHERE y',
                'expected' => 'select * FROM (SELECT DISTINCT * FROM {table_temp} x WITH (NOLOCK)) WHERE y'
            ],
        ];
    }

    /**
     * Test add_no_lock_to_temp_tables
     *
     * @param string $input The input SQL query
     * @param string $expected The expected resultant query
     * @dataProvider add_no_lock_to_temp_tables_provider
     */
    public function test_add_no_lock_to_temp_tables($input, $expected) {
        $sqlsrv = new sqlsrv_native_moodle_database();

        $reflector = new ReflectionObject($sqlsrv);

        $method = $reflector->getMethod('add_no_lock_to_temp_tables');
        $method->setAccessible(true);

        $temptablesproperty = $reflector->getProperty('temptables');
        $temptablesproperty->setAccessible(true);
        $temptables = new temptables_tester();

        $temptablesproperty->setValue($sqlsrv, $temptables);

        $result = $method->invoke($sqlsrv, $input);

        $temptablesproperty->setValue($sqlsrv, null);
        $this->assertEquals($expected, $result);
    }
}

/**
 * Test class for testing temptables
 *
 * @copyright  2017 John Okely
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class temptables_tester {
    /**
     * Returns if one table, based in the information present in the store, is a temp table
     *
     * For easy testing, anything with the word 'temp' in it is considered temporary.
     *
     * @param string $tablename name without prefix of the table we are asking about
     * @return bool true if the table is a temp table (based in the store info), false if not
     */
    public function is_temptable($tablename) {
        if (strpos($tablename, 'temp') === false) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Dispose the temptables
     *
     * @return void
     */
    public function dispose() {
    }
}
