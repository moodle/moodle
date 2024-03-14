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
 * Test case for sqlsrv dml support.
 *
 * @package    core
 * @category   test
 * @copyright  2017 John Okely
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use sqlsrv_native_moodle_database;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/lib/dml/sqlsrv_native_moodle_database.php');

/**
 * Test case for sqlsrv dml support.
 *
 * @package    core
 * @category   test
 * @copyright  2017 John Okely
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sqlsrv_native_moodle_database_test extends \advanced_testcase {

    public function setUp(): void {
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

        $reflector = new \ReflectionObject($sqlsrv);

        $method = $reflector->getMethod('add_no_lock_to_temp_tables');

        $temptablesproperty = $reflector->getProperty('temptables');
        $temptables = new temptables_tester();

        $temptablesproperty->setValue($sqlsrv, $temptables);

        $result = $method->invoke($sqlsrv, $input);

        $temptablesproperty->setValue($sqlsrv, null);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_has_query_order_by
     *
     * @return array data for test_has_query_order_by
     */
    public function has_query_order_by_provider() {
        // Fixtures taken from https://docs.moodle.org/en/ad-hoc_contributed_reports.

        return [
            'User with language => FALSE' => [
                'sql' => <<<EOT
SELECT username, lang
  FROM prefix_user
EOT
                ,
                'expectedmainquery' => <<<EOT
SELECT username, lang
  FROM prefix_user
EOT
                ,
                'expectedresult' => false
            ],
            'List Users with extra info (email) in current course => FALSE' => [
                'sql' => <<<EOT
SELECT u.firstname, u.lastname, u.email
  FROM prefix_role_assignments AS ra
  JOIN prefix_context AS context ON context.id = ra.contextid AND context.contextlevel = 50
  JOIN prefix_course AS c ON c.id = context.instanceid AND c.id = %%COURSEID%%
  JOIN prefix_user AS u ON u.id = ra.userid
EOT
                ,
                'expectedmainquery' => <<<EOT
SELECT u.firstname, u.lastname, u.email
  FROM prefix_role_assignments AS ra
  JOIN prefix_context AS context ON context.id = ra.contextid AND context.contextlevel = 50
  JOIN prefix_course AS c ON c.id = context.instanceid AND c.id = %%COURSEID%%
  JOIN prefix_user AS u ON u.id = ra.userid
EOT
                ,
                'expectedresult' => false
            ],
            'ROW_NUMBER() OVER (ORDER BY ...) => FALSE (https://github.com/jleyva/moodle-block_configurablereports/issues/120)' => [
                'sql' => <<<EOT
SELECT COUNT(*) AS 'Users who have logged in today'
  FROM (
         SELECT ROW_NUMBER() OVER(ORDER BY lastaccess DESC) AS Row
           FROM mdl_user
          WHERE lastaccess > DATEDIFF(s, '1970-01-01 02:00:00', (SELECT Convert(DateTime, DATEDIFF(DAY, 0, GETDATE()))))
       ) AS Logins
EOT
                ,
                'expectedmainquery' => <<<EOT
SELECT COUNT() AS 'Users who have logged in today'
  FROM () AS Logins
EOT
                ,
                'expectedresult' => false
            ],
            'CONTRIB-7725 workaround) => TRUE' => [
                'sql' => <<<EOT
SELECT COUNT(*) AS 'Users who have logged in today'
  FROM (
         SELECT ROW_NUMBER() OVER(ORDER BY lastaccess DESC) AS Row
           FROM mdl_user
          WHERE lastaccess > DATEDIFF(s, '1970-01-01 02:00:00', (SELECT Convert(DateTime, DATEDIFF(DAY, 0, GETDATE()))))
       ) AS Logins ORDER BY 1
EOT
                ,
                'expectedmainquery' => <<<EOT
SELECT COUNT() AS 'Users who have logged in today'
  FROM () AS Logins ORDER BY 1
EOT
                ,
                'expectedresult' => true
            ],
            'Enrolment count in each Course => TRUE' => [
                'sql' => <<<EOT
  SELECT c.fullname, COUNT(ue.id) AS Enroled
    FROM prefix_course AS c
    JOIN prefix_enrol AS en ON en.courseid = c.id
    JOIN prefix_user_enrolments AS ue ON ue.enrolid = en.id
GROUP BY c.id
ORDER BY c.fullname
EOT
                ,
                'expectedmainquery' => <<<EOT
  SELECT c.fullname, COUNT() AS Enroled
    FROM prefix_course AS c
    JOIN prefix_enrol AS en ON en.courseid = c.id
    JOIN prefix_user_enrolments AS ue ON ue.enrolid = en.id
GROUP BY c.id
ORDER BY c.fullname
EOT
                ,
                'expectedresult' => true
            ],
        ];
    }

    /**
     * Test has_query_order_by
     *
     * @dataProvider has_query_order_by_provider
     * @param string $sql the query
     * @param string $expectedmainquery the expected main query
     * @param bool $expectedresult the expected result
     */
    public function test_has_query_order_by(string $sql, string $expectedmainquery, bool $expectedresult) {
        $mainquery = preg_replace('/\(((?>[^()]+)|(?R))*\)/', '()', $sql);
        $this->assertSame($expectedmainquery, $mainquery);

        // The has_query_order_by static method is protected. Use Reflection to call the method.
        $method = new \ReflectionMethod('sqlsrv_native_moodle_database', 'has_query_order_by');
        $result = $method->invoke(null, $sql);
        $this->assertSame($expectedresult, $result);
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
