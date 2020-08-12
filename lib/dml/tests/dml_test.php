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
 * DML layer tests.
 *
 * @package    core_dml
 * @category   phpunit
 * @copyright  2008 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class core_dml_testcase extends database_driver_testcase {

    protected function setUp() {
        parent::setUp();
        $dbman = $this->tdb->get_manager(); // Loads DDL libs.
    }

    /**
     * Get a xmldb_table object for testing, deleting any existing table
     * of the same name, for example if one was left over from a previous test
     * run that crashed.
     *
     * @param string $suffix table name suffix, use if you need more test tables
     * @return xmldb_table the table object.
     */
    private function get_test_table($suffix = '') {
        $tablename = "test_table";
        if ($suffix !== '') {
            $tablename .= $suffix;
        }

        $table = new xmldb_table($tablename);
        $table->setComment("This is a test'n drop table. You can drop it safely");
        return $table;
    }

    /**
     * Convert a unix string to a OS (dir separator) dependent string.
     *
     * @param string $source the original srting, using unix dir separators and newlines.
     * @return string the resulting string, using current OS dir separators newlines.
     */
    private function unix_to_os_dirsep(string $source): string {
        if (DIRECTORY_SEPARATOR !== '/') {
            return str_replace('/', DIRECTORY_SEPARATOR, $source);
        }
        return $source; // No changes, so far.
    }

    public function test_diagnose() {
        $DB = $this->tdb;
        $result = $DB->diagnose();
        $this->assertNull($result, 'Database self diagnostics failed %s');
    }

    public function test_get_server_info() {
        $DB = $this->tdb;
        $result = $DB->get_server_info();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('version', $result);
    }

    public function test_get_in_or_equal() {
        $DB = $this->tdb;

        // SQL_PARAMS_QM - IN or =.

        // Correct usage of multiple values.
        $in_values = array('value1', 'value2', '3', 4, null, false, true);
        list($usql, $params) = $DB->get_in_or_equal($in_values);
        $this->assertSame('IN ('.implode(',', array_fill(0, count($in_values), '?')).')', $usql);
        $this->assertEquals(count($in_values), count($params));
        foreach ($params as $key => $value) {
            $this->assertSame($in_values[$key], $value);
        }

        // Correct usage of single value (in an array).
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values);
        $this->assertEquals("= ?", $usql);
        $this->assertCount(1, $params);
        $this->assertEquals($in_values[0], $params[0]);

        // Correct usage of single value.
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values);
        $this->assertEquals("= ?", $usql);
        $this->assertCount(1, $params);
        $this->assertEquals($in_value, $params[0]);

        // SQL_PARAMS_QM - NOT IN or <>.

        // Correct usage of multiple values.
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, null, false);
        $this->assertEquals("NOT IN (?,?,?,?)", $usql);
        $this->assertCount(4, $params);
        foreach ($params as $key => $value) {
            $this->assertEquals($in_values[$key], $value);
        }

        // Correct usage of single value (in array().
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, null, false);
        $this->assertEquals("<> ?", $usql);
        $this->assertCount(1, $params);
        $this->assertEquals($in_values[0], $params[0]);

        // Correct usage of single value.
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, null, false);
        $this->assertEquals("<> ?", $usql);
        $this->assertCount(1, $params);
        $this->assertEquals($in_value, $params[0]);

        // SQL_PARAMS_NAMED - IN or =.

        // Correct usage of multiple values.
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', true);
        $this->assertCount(4, $params);
        reset($in_values);
        $ps = array();
        foreach ($params as $key => $value) {
            $this->assertEquals(current($in_values), $value);
            next($in_values);
            $ps[] = ':'.$key;
        }
        $this->assertEquals("IN (".implode(',', $ps).")", $usql);

        // Correct usage of single values (in array).
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', true);
        $this->assertCount(1, $params);
        $value = reset($params);
        $key = key($params);
        $this->assertEquals("= :$key", $usql);
        $this->assertEquals($in_value, $value);

        // Correct usage of single value.
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', true);
        $this->assertCount(1, $params);
        $value = reset($params);
        $key = key($params);
        $this->assertEquals("= :$key", $usql);
        $this->assertEquals($in_value, $value);

        // SQL_PARAMS_NAMED - NOT IN or <>.

        // Correct usage of multiple values.
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false);
        $this->assertCount(4, $params);
        reset($in_values);
        $ps = array();
        foreach ($params as $key => $value) {
            $this->assertEquals(current($in_values), $value);
            next($in_values);
            $ps[] = ':'.$key;
        }
        $this->assertEquals("NOT IN (".implode(',', $ps).")", $usql);

        // Correct usage of single values (in array).
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false);
        $this->assertCount(1, $params);
        $value = reset($params);
        $key = key($params);
        $this->assertEquals("<> :$key", $usql);
        $this->assertEquals($in_value, $value);

        // Correct usage of single value.
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false);
        $this->assertCount(1, $params);
        $value = reset($params);
        $key = key($params);
        $this->assertEquals("<> :$key", $usql);
        $this->assertEquals($in_value, $value);

        // Make sure the param names are unique.
        list($usql1, $params1) = $DB->get_in_or_equal(array(1, 2, 3), SQL_PARAMS_NAMED, 'param');
        list($usql2, $params2) = $DB->get_in_or_equal(array(1, 2, 3), SQL_PARAMS_NAMED, 'param');
        $params1 = array_keys($params1);
        $params2 = array_keys($params2);
        $common = array_intersect($params1, $params2);
        $this->assertCount(0, $common);

        // Some incorrect tests.

        // Incorrect usage passing not-allowed params type.
        $in_values = array(1, 2, 3);
        try {
            list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_DOLLAR, 'param', false);
            $this->fail('An Exception is missing, expected due to not supported SQL_PARAMS_DOLLAR');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('typenotimplement', $e->errorcode);
        }

        // Incorrect usage passing empty array.
        $in_values = array();
        try {
            list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false);
            $this->fail('An Exception is missing, expected due to empty array of items');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        // Test using $onemptyitems.

        // Correct usage passing empty array and $onemptyitems = null (equal = true, QM).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, 'param', true, null);
        $this->assertSame(' IS NULL', $usql);
        $this->assertSame(array(), $params);

        // Correct usage passing empty array and $onemptyitems = null (equal = false, NAMED).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false, null);
        $this->assertSame(' IS NOT NULL', $usql);
        $this->assertSame(array(), $params);

        // Correct usage passing empty array and $onemptyitems = true (equal = true, QM).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, 'param', true, true);
        $this->assertSame('= ?', $usql);
        $this->assertSame(array(true), $params);

        // Correct usage passing empty array and $onemptyitems = true (equal = false, NAMED).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false, true);
        $this->assertCount(1, $params);
        $value = reset($params);
        $key = key($params);
        $this->assertSame('<> :'.$key, $usql);
        $this->assertSame($value, true);

        // Correct usage passing empty array and $onemptyitems = -1 (equal = true, QM).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, 'param', true, -1);
        $this->assertSame('= ?', $usql);
        $this->assertSame(array(-1), $params);

        // Correct usage passing empty array and $onemptyitems = -1 (equal = false, NAMED).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false, -1);
        $this->assertCount(1, $params);
        $value = reset($params);
        $key = key($params);
        $this->assertSame('<> :'.$key, $usql);
        $this->assertSame($value, -1);

        // Correct usage passing empty array and $onemptyitems = 'onevalue' (equal = true, QM).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, 'param', true, 'onevalue');
        $this->assertSame('= ?', $usql);
        $this->assertSame(array('onevalue'), $params);

        // Correct usage passing empty array and $onemptyitems = 'onevalue' (equal = false, NAMED).
        $in_values = array();
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false, 'onevalue');
        $this->assertCount(1, $params);
        $value = reset($params);
        $key = key($params);
        $this->assertSame('<> :'.$key, $usql);
        $this->assertSame($value, 'onevalue');
    }

    public function test_fix_table_names() {
        $DB = new moodle_database_for_testing();
        $prefix = $DB->get_prefix();

        // Simple placeholder.
        $placeholder = "{user_123}";
        $this->assertSame($prefix."user_123", $DB->public_fix_table_names($placeholder));

        // Wrong table name.
        $placeholder = "{user-a}";
        $this->assertSame($placeholder, $DB->public_fix_table_names($placeholder));

        // Wrong table name.
        $placeholder = "{123user}";
        $this->assertSame($placeholder, $DB->public_fix_table_names($placeholder));

        // Full SQL.
        $sql = "SELECT * FROM {user}, {funny_table_name}, {mdl_stupid_table} WHERE {user}.id = {funny_table_name}.userid";
        $expected = "SELECT * FROM {$prefix}user, {$prefix}funny_table_name, {$prefix}mdl_stupid_table WHERE {$prefix}user.id = {$prefix}funny_table_name.userid";
        $this->assertSame($expected, $DB->public_fix_table_names($sql));
    }

    public function test_fix_sql_params() {
        $DB = $this->tdb;
        $prefix = $DB->get_prefix();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        // Correct table placeholder substitution.
        $sql = "SELECT * FROM {{$tablename}}";
        $sqlarray = $DB->fix_sql_params($sql);
        $this->assertEquals("SELECT * FROM {$prefix}".$tablename, $sqlarray[0]);

        // Conversions of all param types.
        $sql = array();
        $sql[SQL_PARAMS_NAMED]  = "SELECT * FROM {$prefix}testtable WHERE name = :param1, course = :param2";
        $sql[SQL_PARAMS_QM]     = "SELECT * FROM {$prefix}testtable WHERE name = ?, course = ?";
        $sql[SQL_PARAMS_DOLLAR] = "SELECT * FROM {$prefix}testtable WHERE name = \$1, course = \$2";

        $params = array();
        $params[SQL_PARAMS_NAMED]  = array('param1'=>'first record', 'param2'=>1);
        $params[SQL_PARAMS_QM]     = array('first record', 1);
        $params[SQL_PARAMS_DOLLAR] = array('first record', 1);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_NAMED], $params[SQL_PARAMS_NAMED]);
        $this->assertSame($rsql, $sql[$rtype]);
        $this->assertSame($rparams, $params[$rtype]);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_QM], $params[SQL_PARAMS_QM]);
        $this->assertSame($rsql, $sql[$rtype]);
        $this->assertSame($rparams, $params[$rtype]);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_DOLLAR], $params[SQL_PARAMS_DOLLAR]);
        $this->assertSame($rsql, $sql[$rtype]);
        $this->assertSame($rparams, $params[$rtype]);

        // Malformed table placeholder.
        $sql = "SELECT * FROM [testtable]";
        $sqlarray = $DB->fix_sql_params($sql);
        $this->assertSame($sql, $sqlarray[0]);

        // Mixed param types (colon and dollar).
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :param1, course = \$1";
        $params = array('param1' => 'record1', 'param2' => 3);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Mixed param types (question and dollar).
        $sql = "SELECT * FROM {{$tablename}} WHERE name = ?, course = \$1";
        $params = array('param1' => 'record2', 'param2' => 5);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Too few params in sql.
        $sql = "SELECT * FROM {{$tablename}} WHERE name = ?, course = ?, id = ?";
        $params = array('record2', 3);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Too many params in array: no error, just use what is necessary.
        $params[] = 1;
        $params[] = time();
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertInternalType('array', $sqlarray);
        $this->assertCount(3, $sqlarray[1]);

        // Named params missing from array.
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :name, course = :course";
        $params = array('wrongname' => 'record1', 'course' => 1);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Duplicate named param in query - this is a very important feature!!
        // it helps with debugging of sloppy code.
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :name, course = :name";
        $params = array('name' => 'record2', 'course' => 3);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Extra named param is ignored.
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :name, course = :course";
        $params = array('name' => 'record1', 'course' => 1, 'extrastuff'=>'haha');
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertInternalType('array', $sqlarray);
        $this->assertCount(2, $sqlarray[1]);

        // Params exceeding 30 chars length.
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :long_placeholder_with_more_than_30";
        $params = array('long_placeholder_with_more_than_30' => 'record1');
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        // Booleans in NAMED params are casting to 1/0 int.
        $sql = "SELECT * FROM {{$tablename}} WHERE course = ? OR course = ?";
        $params = array(true, false);
        list($sql, $params) = $DB->fix_sql_params($sql, $params);
        $this->assertTrue(reset($params) === 1);
        $this->assertTrue(next($params) === 0);

        // Booleans in QM params are casting to 1/0 int.
        $sql = "SELECT * FROM {{$tablename}} WHERE course = :course1 OR course = :course2";
        $params = array('course1' => true, 'course2' => false);
        list($sql, $params) = $DB->fix_sql_params($sql, $params);
        $this->assertTrue(reset($params) === 1);
        $this->assertTrue(next($params) === 0);

        // Booleans in DOLLAR params are casting to 1/0 int.
        $sql = "SELECT * FROM {{$tablename}} WHERE course = \$1 OR course = \$2";
        $params = array(true, false);
        list($sql, $params) = $DB->fix_sql_params($sql, $params);
        $this->assertTrue(reset($params) === 1);
        $this->assertTrue(next($params) === 0);

        // No data types are touched except bool.
        $sql = "SELECT * FROM {{$tablename}} WHERE name IN (?,?,?,?,?,?)";
        $inparams = array('abc', 'ABC', null, '1', 1, 1.4);
        list($sql, $params) = $DB->fix_sql_params($sql, $inparams);
        $this->assertSame(array_values($params), array_values($inparams));
    }

    /**
     * Test the database debugging as SQL comment.
     */
    public function test_add_sql_debugging() {
        global $CFG;
        $DB = $this->tdb;

        require_once($CFG->dirroot . '/lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php');
        $fixture = new test_dml_sql_debugging_fixture($this);

        $sql = "SELECT * FROM {users}";

        $out = $fixture->four($sql);

        $CFG->debugsqltrace = 0;
        $this->assertEquals("SELECT * FROM {users}", $out);

        $CFG->debugsqltrace = 1;
        $out = $fixture->four($sql);
        $expected = <<<EOD
SELECT * FROM {users}
-- line 65 of /lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php: call to ReflectionMethod->invoke()
EOD;
        $this->assertEquals($this->unix_to_os_dirsep($expected), $out);

        $CFG->debugsqltrace = 2;
        $out = $fixture->four($sql);
        $expected = <<<EOD
SELECT * FROM {users}
-- line 65 of /lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php: call to ReflectionMethod->invoke()
-- line 74 of /lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php: call to test_dml_sql_debugging_fixture->one()
EOD;
        $this->assertEquals($this->unix_to_os_dirsep($expected), $out);

        $CFG->debugsqltrace = 5;
        $out = $fixture->four($sql);
        $expected = <<<EOD
SELECT * FROM {users}
-- line 65 of /lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php: call to ReflectionMethod->invoke()
-- line 74 of /lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php: call to test_dml_sql_debugging_fixture->one()
-- line 83 of /lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php: call to test_dml_sql_debugging_fixture->two()
-- line 92 of /lib/dml/tests/fixtures/test_dml_sql_debugging_fixture.php: call to test_dml_sql_debugging_fixture->three()
-- line 489 of /lib/dml/tests/dml_test.php: call to test_dml_sql_debugging_fixture->four()
EOD;
        $this->assertEquals($this->unix_to_os_dirsep($expected), $out);

        $CFG->debugsqltrace = 0;
    }

    public function test_strtok() {
        // Strtok was previously used by bound emulation, make sure it is not used any more.
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, 'lala');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $str = 'a?b?c?d';
        $this->assertSame(strtok($str, '?'), 'a');

        $DB->get_records($tablename, array('id'=>1));

        $this->assertSame(strtok('?'), 'b');
    }

    public function test_tweak_param_names() {
        // Note the tweak_param_names() method is only available in the oracle driver,
        // hence we look for expected results indirectly, by testing various DML methods.
        // with some "extreme" conditions causing the tweak to happen.
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // Add some columns with 28 chars in the name.
        $table->add_field('long_int_columnname_with_28c', XMLDB_TYPE_INTEGER, '10');
        $table->add_field('long_dec_columnname_with_28c', XMLDB_TYPE_NUMBER, '10,2');
        $table->add_field('long_str_columnname_with_28c', XMLDB_TYPE_CHAR, '100');
        // Add some columns with 30 chars in the name.
        $table->add_field('long_int_columnname_with_30cxx', XMLDB_TYPE_INTEGER, '10');
        $table->add_field('long_dec_columnname_with_30cxx', XMLDB_TYPE_NUMBER, '10,2');
        $table->add_field('long_str_columnname_with_30cxx', XMLDB_TYPE_CHAR, '100');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_table($table);

        $this->assertTrue($dbman->table_exists($tablename));

        // Test insert record.
        $rec1 = new stdClass();
        $rec1->long_int_columnname_with_28c = 28;
        $rec1->long_dec_columnname_with_28c = 28.28;
        $rec1->long_str_columnname_with_28c = '28';
        $rec1->long_int_columnname_with_30cxx = 30;
        $rec1->long_dec_columnname_with_30cxx = 30.30;
        $rec1->long_str_columnname_with_30cxx = '30';

        // Insert_record().
        $rec1->id = $DB->insert_record($tablename, $rec1);
        $this->assertEquals($rec1, $DB->get_record($tablename, array('id' => $rec1->id)));

        // Update_record().
        $DB->update_record($tablename, $rec1);
        $this->assertEquals($rec1, $DB->get_record($tablename, array('id' => $rec1->id)));

        // Set_field().
        $rec1->long_int_columnname_with_28c = 280;
        $DB->set_field($tablename, 'long_int_columnname_with_28c', $rec1->long_int_columnname_with_28c,
            array('id' => $rec1->id, 'long_int_columnname_with_28c' => 28));
        $rec1->long_dec_columnname_with_28c = 280.28;
        $DB->set_field($tablename, 'long_dec_columnname_with_28c', $rec1->long_dec_columnname_with_28c,
            array('id' => $rec1->id, 'long_dec_columnname_with_28c' => 28.28));
        $rec1->long_str_columnname_with_28c = '280';
        $DB->set_field($tablename, 'long_str_columnname_with_28c', $rec1->long_str_columnname_with_28c,
            array('id' => $rec1->id, 'long_str_columnname_with_28c' => '28'));
        $rec1->long_int_columnname_with_30cxx = 300;
        $DB->set_field($tablename, 'long_int_columnname_with_30cxx', $rec1->long_int_columnname_with_30cxx,
            array('id' => $rec1->id, 'long_int_columnname_with_30cxx' => 30));
        $rec1->long_dec_columnname_with_30cxx = 300.30;
        $DB->set_field($tablename, 'long_dec_columnname_with_30cxx', $rec1->long_dec_columnname_with_30cxx,
            array('id' => $rec1->id, 'long_dec_columnname_with_30cxx' => 30.30));
        $rec1->long_str_columnname_with_30cxx = '300';
        $DB->set_field($tablename, 'long_str_columnname_with_30cxx', $rec1->long_str_columnname_with_30cxx,
            array('id' => $rec1->id, 'long_str_columnname_with_30cxx' => '30'));
        $this->assertEquals($rec1, $DB->get_record($tablename, array('id' => $rec1->id)));

        // Delete_records().
        $rec2 = $DB->get_record($tablename, array('id' => $rec1->id));
        $rec2->id = $DB->insert_record($tablename, $rec2);
        $this->assertEquals(2, $DB->count_records($tablename));
        $DB->delete_records($tablename, (array) $rec2);
        $this->assertEquals(1, $DB->count_records($tablename));

        // Get_recordset().
        $rs = $DB->get_recordset($tablename, (array) $rec1);
        $iterations = 0;
        foreach ($rs as $rec2) {
            $iterations++;
        }
        $rs->close();
        $this->assertEquals(1, $iterations);
        $this->assertEquals($rec1, $rec2);

        // Get_records().
        $recs = $DB->get_records($tablename, (array) $rec1);
        $this->assertCount(1, $recs);
        $this->assertEquals($rec1, reset($recs));

        // Get_fieldset_select().
        $select = 'id = :id AND
                   long_int_columnname_with_28c = :long_int_columnname_with_28c AND
                   long_dec_columnname_with_28c = :long_dec_columnname_with_28c AND
                   long_str_columnname_with_28c = :long_str_columnname_with_28c AND
                   long_int_columnname_with_30cxx = :long_int_columnname_with_30cxx AND
                   long_dec_columnname_with_30cxx = :long_dec_columnname_with_30cxx AND
                   long_str_columnname_with_30cxx = :long_str_columnname_with_30cxx';
        $fields = $DB->get_fieldset_select($tablename, 'long_int_columnname_with_28c', $select, (array)$rec1);
        $this->assertCount(1, $fields);
        $this->assertEquals($rec1->long_int_columnname_with_28c, reset($fields));
        $fields = $DB->get_fieldset_select($tablename, 'long_dec_columnname_with_28c', $select, (array)$rec1);
        $this->assertEquals($rec1->long_dec_columnname_with_28c, reset($fields));
        $fields = $DB->get_fieldset_select($tablename, 'long_str_columnname_with_28c', $select, (array)$rec1);
        $this->assertEquals($rec1->long_str_columnname_with_28c, reset($fields));
        $fields = $DB->get_fieldset_select($tablename, 'long_int_columnname_with_30cxx', $select, (array)$rec1);
        $this->assertEquals($rec1->long_int_columnname_with_30cxx, reset($fields));
        $fields = $DB->get_fieldset_select($tablename, 'long_dec_columnname_with_30cxx', $select, (array)$rec1);
        $this->assertEquals($rec1->long_dec_columnname_with_30cxx, reset($fields));
        $fields = $DB->get_fieldset_select($tablename, 'long_str_columnname_with_30cxx', $select, (array)$rec1);
        $this->assertEquals($rec1->long_str_columnname_with_30cxx, reset($fields));

        // Overlapping placeholders (progressive str_replace).
        $overlapselect = 'id = :p AND
                   long_int_columnname_with_28c = :param1 AND
                   long_dec_columnname_with_28c = :param2 AND
                   long_str_columnname_with_28c = :param_with_29_characters_long AND
                   long_int_columnname_with_30cxx = :param_with_30_characters_long_ AND
                   long_dec_columnname_with_30cxx = :param_ AND
                   long_str_columnname_with_30cxx = :param__';
        $overlapparams = array(
            'p' => $rec1->id,
            'param1' => $rec1->long_int_columnname_with_28c,
            'param2' => $rec1->long_dec_columnname_with_28c,
            'param_with_29_characters_long' => $rec1->long_str_columnname_with_28c,
            'param_with_30_characters_long_' => $rec1->long_int_columnname_with_30cxx,
            'param_' => $rec1->long_dec_columnname_with_30cxx,
            'param__' => $rec1->long_str_columnname_with_30cxx);
        $recs = $DB->get_records_select($tablename, $overlapselect, $overlapparams);
        $this->assertCount(1, $recs);
        $this->assertEquals($rec1, reset($recs));

        // Execute().
        $DB->execute("DELETE FROM {{$tablename}} WHERE $select", (array)$rec1);
        $this->assertEquals(0, $DB->count_records($tablename));
    }

    public function test_get_tables() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        // Need to test with multiple DBs.
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $original_count = count($DB->get_tables());

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_table($table);
        $this->assertTrue(count($DB->get_tables()) == $original_count + 1);

        $dbman->drop_table($table);
        $this->assertTrue(count($DB->get_tables()) == $original_count);
    }

    public function test_get_indexes() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('course-id', XMLDB_INDEX_UNIQUE, array('course', 'id'));
        $dbman->create_table($table);

        $indices = $DB->get_indexes($tablename);
        $this->assertInternalType('array', $indices);
        $this->assertCount(2, $indices);
        // We do not care about index names for now.
        $first = array_shift($indices);
        $second = array_shift($indices);
        if (count($first['columns']) == 2) {
            $composed = $first;
            $single   = $second;
        } else {
            $composed = $second;
            $single   = $first;
        }
        $this->assertFalse($single['unique']);
        $this->assertTrue($composed['unique']);
        $this->assertCount(1, $single['columns']);
        $this->assertCount(2, $composed['columns']);
        $this->assertSame('course', $single['columns'][0]);
        $this->assertSame('course', $composed['columns'][0]);
        $this->assertSame('id', $composed['columns'][1]);
    }

    public function test_get_columns() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, 'lala');
        $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('enumfield', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'test2');
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onefloat', XMLDB_TYPE_FLOAT, '10,2', null, null, null, 300);
        $table->add_field('anotherfloat', XMLDB_TYPE_FLOAT, null, null, null, null, 400);
        $table->add_field('negativedfltint', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1');
        $table->add_field('negativedfltnumber', XMLDB_TYPE_NUMBER, '10', null, XMLDB_NOTNULL, null, '-2');
        $table->add_field('negativedfltfloat', XMLDB_TYPE_FLOAT, '10', null, XMLDB_NOTNULL, null, '-3');
        $table->add_field('someint1', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('someint2', XMLDB_TYPE_INTEGER, '2', null, null, null, '0');
        $table->add_field('someint3', XMLDB_TYPE_INTEGER, '3', null, null, null, '0');
        $table->add_field('someint4', XMLDB_TYPE_INTEGER, '4', null, null, null, '0');
        $table->add_field('someint5', XMLDB_TYPE_INTEGER, '5', null, null, null, '0');
        $table->add_field('someint6', XMLDB_TYPE_INTEGER, '6', null, null, null, '0');
        $table->add_field('someint7', XMLDB_TYPE_INTEGER, '7', null, null, null, '0');
        $table->add_field('someint8', XMLDB_TYPE_INTEGER, '8', null, null, null, '0');
        $table->add_field('someint9', XMLDB_TYPE_INTEGER, '9', null, null, null, '0');
        $table->add_field('someint10', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('someint18', XMLDB_TYPE_INTEGER, '18', null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $columns = $DB->get_columns($tablename);
        $this->assertInternalType('array', $columns);

        $fields = $table->getFields();
        $this->assertCount(count($columns), $fields);

        $field = $columns['id'];
        $this->assertSame('R', $field->meta_type);
        $this->assertTrue($field->auto_increment);
        $this->assertTrue($field->unique);

        $field = $columns['course'];
        $this->assertSame('I', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertTrue($field->has_default);
        $this->assertEquals(0, $field->default_value);
        $this->assertTrue($field->not_null);

        for ($i=1; $i<=10; $i++) {
            $field = $columns['someint'.$i];
            $this->assertSame('I', $field->meta_type);
            $this->assertGreaterThanOrEqual($i, $field->max_length);
        }
        $field = $columns['someint18'];
        $this->assertSame('I', $field->meta_type);
        $this->assertGreaterThanOrEqual(18, $field->max_length);

        $field = $columns['name'];
        $this->assertSame('C', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertEquals(255, $field->max_length);
        $this->assertTrue($field->has_default);
        $this->assertSame('lala', $field->default_value);
        $this->assertFalse($field->not_null);

        $field = $columns['description'];
        $this->assertSame('X', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertFalse($field->has_default);
        $this->assertNull($field->default_value);
        $this->assertFalse($field->not_null);

        $field = $columns['enumfield'];
        $this->assertSame('C', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertSame('test2', $field->default_value);
        $this->assertTrue($field->not_null);

        $field = $columns['onenum'];
        $this->assertSame('N', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertEquals(10, $field->max_length);
        $this->assertEquals(2, $field->scale);
        $this->assertTrue($field->has_default);
        $this->assertEquals(200.0, $field->default_value);
        $this->assertFalse($field->not_null);

        $field = $columns['onefloat'];
        $this->assertSame('N', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertTrue($field->has_default);
        $this->assertEquals(300.0, $field->default_value);
        $this->assertFalse($field->not_null);

        $field = $columns['anotherfloat'];
        $this->assertSame('N', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertTrue($field->has_default);
        $this->assertEquals(400.0, $field->default_value);
        $this->assertFalse($field->not_null);

        // Test negative defaults in numerical columns.
        $field = $columns['negativedfltint'];
        $this->assertTrue($field->has_default);
        $this->assertEquals(-1, $field->default_value);

        $field = $columns['negativedfltnumber'];
        $this->assertTrue($field->has_default);
        $this->assertEquals(-2, $field->default_value);

        $field = $columns['negativedfltfloat'];
        $this->assertTrue($field->has_default);
        $this->assertEquals(-3, $field->default_value);

        for ($i = 0; $i < count($columns); $i++) {
            if ($i == 0) {
                $next_column = reset($columns);
                $next_field  = reset($fields);
            } else {
                $next_column = next($columns);
                $next_field  = next($fields);
            }

            $this->assertEquals($next_column->name, $next_field->getName());
        }

        // Test get_columns for non-existing table returns empty array. MDL-30147.
        $columns = $DB->get_columns('xxxx');
        $this->assertEquals(array(), $columns);

        // Create something similar to "context_temp" with id column without sequence.
        $dbman->drop_table($table);
        $table = $this->get_test_table();
        $tablename = $table->getName();
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $columns = $DB->get_columns($tablename);
        $this->assertFalse($columns['id']->auto_increment);
    }

    public function test_get_manager() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $this->assertInstanceOf('database_manager', $dbman);
    }

    public function test_setup_is_unicodedb() {
        $DB = $this->tdb;
        $this->assertTrue($DB->setup_is_unicodedb());
    }

    public function test_set_debug() { // Tests get_debug() too.
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $sql = "SELECT * FROM {{$tablename}}";

        $prevdebug = $DB->get_debug();

        ob_start();
        $DB->set_debug(true);
        $this->assertTrue($DB->get_debug());
        $DB->execute($sql);
        $DB->set_debug(false);
        $this->assertFalse($DB->get_debug());
        $debuginfo = ob_get_contents();
        ob_end_clean();
        $this->assertFalse($debuginfo === '');

        ob_start();
        $DB->execute($sql);
        $debuginfo = ob_get_contents();
        ob_end_clean();
        $this->assertTrue($debuginfo === '');

        $DB->set_debug($prevdebug);
    }

    public function test_execute() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table1 = $this->get_test_table('1');
        $tablename1 = $table1->getName();
        $table1->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table1->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table1->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table1->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table1->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table1);

        $table2 = $this->get_test_table('2');
        $tablename2 = $table2->getName();
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table2->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);

        $DB->insert_record($tablename1, array('course' => 3, 'name' => 'aaa'));
        $DB->insert_record($tablename1, array('course' => 1, 'name' => 'bbb'));
        $DB->insert_record($tablename1, array('course' => 7, 'name' => 'ccc'));
        $DB->insert_record($tablename1, array('course' => 3, 'name' => 'ddd'));

        // Select results are ignored.
        $sql = "SELECT * FROM {{$tablename1}} WHERE course = :course";
        $this->assertTrue($DB->execute($sql, array('course'=>3)));

        // Throw exception on error.
        $sql = "XXUPDATE SET XSSD";
        try {
            $DB->execute($sql);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Update records.
        $sql = "UPDATE {{$tablename1}}
                   SET course = 6
                 WHERE course = ?";
        $this->assertTrue($DB->execute($sql, array('3')));
        $this->assertEquals(2, $DB->count_records($tablename1, array('course' => 6)));

        // Update records with subquery condition.
        // Confirm that the option not using table aliases is cross-db.
        $sql = "UPDATE {{$tablename1}}
                   SET course = 0
                 WHERE NOT EXISTS (
                           SELECT course
                             FROM {{$tablename2}} tbl2
                            WHERE tbl2.course = {{$tablename1}}.course
                              AND 1 = 0)"; // Really we don't update anything, but verify the syntax is allowed.
        $this->assertTrue($DB->execute($sql));

        // Insert from one into second table.
        $sql = "INSERT INTO {{$tablename2}} (course)

                SELECT course
                  FROM {{$tablename1}}";
        $this->assertTrue($DB->execute($sql));
        $this->assertEquals(4, $DB->count_records($tablename2));

        // Insert a TEXT with raw SQL, binding TEXT params.
        $course = 9999;
        $onetext = file_get_contents(__DIR__ . '/fixtures/clob.txt');
        $sql = "INSERT INTO {{$tablename2}} (course, onetext)
                VALUES (:course, :onetext)";
        $DB->execute($sql, array('course' => $course, 'onetext' => $onetext));
        $records = $DB->get_records($tablename2, array('course' => $course));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame($onetext, $record->onetext);

        // Update a TEXT with raw SQL, binding TEXT params.
        $newcourse = 10000;
        $newonetext = file_get_contents(__DIR__ . '/fixtures/clob.txt') . '- updated';
        $sql = "UPDATE {{$tablename2}} SET course = :newcourse, onetext = :newonetext
                WHERE course = :oldcourse";
        $DB->execute($sql, array('oldcourse' => $course, 'newcourse' => $newcourse, 'newonetext' => $newonetext));
        $records = $DB->get_records($tablename2, array('course' => $course));
        $this->assertCount(0, $records);
        $records = $DB->get_records($tablename2, array('course' => $newcourse));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame($newonetext, $record->onetext);
    }

    public function test_get_recordset() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $data = array(array('course' => 3, 'name' => 'record1', 'onetext'=>'abc'),
            array('course' => 3, 'name' => 'record2', 'onetext'=>'abcd'),
            array('course' => 5, 'name' => 'record3', 'onetext'=>'abcde'));

        foreach ($data as $key => $record) {
            $data[$key]['id'] = $DB->insert_record($tablename, $record);
        }

        // Standard recordset iteration.
        $rs = $DB->get_recordset($tablename);
        $this->assertInstanceOf('moodle_recordset', $rs);
        reset($data);
        foreach ($rs as $record) {
            $data_record = current($data);
            foreach ($record as $k => $v) {
                $this->assertEquals($data_record[$k], $v);
            }
            next($data);
        }
        $rs->close();

        // Iterator style usage.
        $rs = $DB->get_recordset($tablename);
        $this->assertInstanceOf('moodle_recordset', $rs);
        reset($data);
        while ($rs->valid()) {
            $record = $rs->current();
            $data_record = current($data);
            foreach ($record as $k => $v) {
                $this->assertEquals($data_record[$k], $v);
            }
            next($data);
            $rs->next();
        }
        $rs->close();

        // Make sure rewind is ignored.
        $rs = $DB->get_recordset($tablename);
        $this->assertInstanceOf('moodle_recordset', $rs);
        reset($data);
        $i = 0;
        foreach ($rs as $record) {
            $i++;
            $rs->rewind();
            if ($i > 10) {
                $this->fail('revind not ignored in recordsets');
                break;
            }
            $data_record = current($data);
            foreach ($record as $k => $v) {
                $this->assertEquals($data_record[$k], $v);
            }
            next($data);
        }
        $rs->close();

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext' => '1');
        try {
            $rs = $DB->get_recordset($tablename, $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }

        // Test nested iteration.
        $rs1 = $DB->get_recordset($tablename);
        $i = 0;
        foreach ($rs1 as $record1) {
            $rs2 = $DB->get_recordset($tablename);
            $i++;
            $j = 0;
            foreach ($rs2 as $record2) {
                $j++;
            }
            $rs2->close();
            $this->assertCount($j, $data);
        }
        $rs1->close();
        $this->assertCount($i, $data);

        // Notes:
        //  * limits are tested in test_get_recordset_sql()
        //  * where_clause() is used internally and is tested in test_get_records()
    }

    public function test_get_recordset_static() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));

        $rs = $DB->get_recordset($tablename, array(), 'id');

        $DB->set_field($tablename, 'course', 666, array('course'=>1));
        $DB->delete_records($tablename, array('course'=>2));

        $i = 0;
        foreach ($rs as $record) {
            $i++;
            $this->assertEquals($i, $record->course);
        }
        $rs->close();
        $this->assertEquals(4, $i);

        // Now repeat with limits because it may use different code.
        $DB->delete_records($tablename, array());

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));

        $rs = $DB->get_recordset($tablename, array(), 'id', '*', 0, 3);

        $DB->set_field($tablename, 'course', 666, array('course'=>1));
        $DB->delete_records($tablename, array('course'=>2));

        $i = 0;
        foreach ($rs as $record) {
            $i++;
            $this->assertEquals($i, $record->course);
        }
        $rs->close();
        $this->assertEquals(3, $i);
    }

    public function test_get_recordset_iterator_keys() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $data = array(array('course' => 3, 'name' => 'record1'),
            array('course' => 3, 'name' => 'record2'),
            array('course' => 5, 'name' => 'record3'));
        foreach ($data as $key => $record) {
            $data[$key]['id'] = $DB->insert_record($tablename, $record);
        }

        // Test repeated numeric keys are returned ok.
        $rs = $DB->get_recordset($tablename, null, null, 'course, name, id');

        reset($data);
        $count = 0;
        foreach ($rs as $key => $record) {
            $data_record = current($data);
            $this->assertEquals($data_record['course'], $key);
            next($data);
            $count++;
        }
        $rs->close();
        $this->assertEquals(3, $count);

        // Test string keys are returned ok.
        $rs = $DB->get_recordset($tablename, null, null, 'name, course, id');

        reset($data);
        $count = 0;
        foreach ($rs as $key => $record) {
            $data_record = current($data);
            $this->assertEquals($data_record['name'], $key);
            next($data);
            $count++;
        }
        $rs->close();
        $this->assertEquals(3, $count);

        // Test numeric not starting in 1 keys are returned ok.
        $rs = $DB->get_recordset($tablename, null, 'id DESC', 'id, course, name');

        $data = array_reverse($data);
        reset($data);
        $count = 0;
        foreach ($rs as $key => $record) {
            $data_record = current($data);
            $this->assertEquals($data_record['id'], $key);
            next($data);
            $count++;
        }
        $rs->close();
        $this->assertEquals(3, $count);
    }

    public function test_get_recordset_list() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => null));
        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 0));

        $rs = $DB->get_recordset_list($tablename, 'course', array(3, 2));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEquals(3, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course', array(3));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEquals(2, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course', array(null));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEquals(1, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course', array(6, null));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEquals(1, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course', array(null, 5, 5, 5));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEquals(2, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course', array(true));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEquals(1, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course', array(false));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEquals(1, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course', array()); // Must return 0 rows without conditions. MDL-17645.

        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $rs->close();
        $this->assertEquals(0, $counter);

        // Notes:
        //  * limits are tested in test_get_recordset_sql()
        //  * where_clause() is used internally and is tested in test_get_records()
    }

    public function test_get_recordset_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $rs = $DB->get_recordset_select($tablename, '');
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $rs->close();
        $this->assertEquals(4, $counter);

        $this->assertNotEmpty($rs = $DB->get_recordset_select($tablename, 'course = 3'));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $rs->close();
        $this->assertEquals(2, $counter);

        // Notes:
        //  * limits are tested in test_get_recordset_sql()
    }

    public function test_get_recordset_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $inskey1 = $DB->insert_record($tablename, array('course' => 3));
        $inskey2 = $DB->insert_record($tablename, array('course' => 5));
        $inskey3 = $DB->insert_record($tablename, array('course' => 4));
        $inskey4 = $DB->insert_record($tablename, array('course' => 3));
        $inskey5 = $DB->insert_record($tablename, array('course' => 2));
        $inskey6 = $DB->insert_record($tablename, array('course' => 1));
        $inskey7 = $DB->insert_record($tablename, array('course' => 0));

        $rs = $DB->get_recordset_sql("SELECT * FROM {{$tablename}} WHERE course = ?", array(3));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $rs->close();
        $this->assertEquals(2, $counter);

        // Limits - only need to test this case, the rest have been tested by test_get_records_sql()
        // only limitfrom = skips that number of records.
        $rs = $DB->get_recordset_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 2, 0);
        $records = array();
        foreach ($rs as $key => $record) {
            $records[$key] = $record;
        }
        $rs->close();
        $this->assertCount(5, $records);
        $this->assertEquals($inskey3, reset($records)->id);
        $this->assertEquals($inskey7, end($records)->id);

        // Note: fetching nulls, empties, LOBs already tested by test_insert_record() no needed here.
    }

    public function test_export_table_recordset() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $ids = array();
        $ids[] = $DB->insert_record($tablename, array('course' => 3));
        $ids[] = $DB->insert_record($tablename, array('course' => 5));
        $ids[] = $DB->insert_record($tablename, array('course' => 4));
        $ids[] = $DB->insert_record($tablename, array('course' => 3));
        $ids[] = $DB->insert_record($tablename, array('course' => 2));
        $ids[] = $DB->insert_record($tablename, array('course' => 1));
        $ids[] = $DB->insert_record($tablename, array('course' => 0));

        $rs = $DB->export_table_recordset($tablename);
        $rids = array();
        foreach ($rs as $record) {
            $rids[] = $record->id;
        }
        $rs->close();
        $this->assertEquals($ids, $rids, '', 0, 0, true);
    }

    public function test_get_records() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        // All records.
        $records = $DB->get_records($tablename);
        $this->assertCount(4, $records);
        $this->assertEquals(3, $records[1]->course);
        $this->assertEquals(3, $records[2]->course);
        $this->assertEquals(5, $records[3]->course);
        $this->assertEquals(2, $records[4]->course);

        // Records matching certain conditions.
        $records = $DB->get_records($tablename, array('course' => 3));
        $this->assertCount(2, $records);
        $this->assertEquals(3, $records[1]->course);
        $this->assertEquals(3, $records[2]->course);

        // All records sorted by course.
        $records = $DB->get_records($tablename, null, 'course');
        $this->assertCount(4, $records);
        $current_record = reset($records);
        $this->assertEquals(4, $current_record->id);
        $current_record = next($records);
        $this->assertEquals(1, $current_record->id);
        $current_record = next($records);
        $this->assertEquals(2, $current_record->id);
        $current_record = next($records);
        $this->assertEquals(3, $current_record->id);

        // All records, but get only one field.
        $records = $DB->get_records($tablename, null, '', 'id');
        $this->assertFalse(isset($records[1]->course));
        $this->assertTrue(isset($records[1]->id));
        $this->assertCount(4, $records);

        // Booleans into params.
        $records = $DB->get_records($tablename, array('course' => true));
        $this->assertCount(0, $records);
        $records = $DB->get_records($tablename, array('course' => false));
        $this->assertCount(0, $records);

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext' => '1');
        try {
            $records = $DB->get_records($tablename, $conditions);
            if (debugging()) {
                // Only in debug mode - hopefully all devs test code in debug mode...
                $this->fail('An Exception is missing, expected due to equating of text fields');
            }
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }

        // Test get_records passing non-existing table.
        // with params.
        try {
            $records = $DB->get_records('xxxx', array('id' => 0));
            $this->fail('An Exception is missing, expected due to query against non-existing table');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            if (debugging()) {
                // Information for developers only, normal users get general error message.
                $this->assertSame('ddltablenotexist', $e->errorcode);
            }
        }

        try {
            $records = $DB->get_records('xxxx', array('id' => '1'));
            $this->fail('An Exception is missing, expected due to query against non-existing table');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            if (debugging()) {
                // Information for developers only, normal users get general error message.
                $this->assertSame('ddltablenotexist', $e->errorcode);
            }
        }

        // Test get_records passing non-existing column.
        try {
            $records = $DB->get_records($tablename, array('xxxx' => 0));
            $this->fail('An Exception is missing, expected due to query against non-existing column');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            if (debugging()) {
                // Information for developers only, normal users get general error message.
                $this->assertSame('ddlfieldnotexist', $e->errorcode);
            }
        }

        // Note: delegate limits testing to test_get_records_sql().
    }

    public function test_get_records_list() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $records = $DB->get_records_list($tablename, 'course', array(3, 2));
        $this->assertInternalType('array', $records);
        $this->assertCount(3, $records);
        $this->assertEquals(1, reset($records)->id);
        $this->assertEquals(2, next($records)->id);
        $this->assertEquals(4, next($records)->id);

        $this->assertSame(array(), $records = $DB->get_records_list($tablename, 'course', array())); // Must return 0 rows without conditions. MDL-17645.
        $this->assertCount(0, $records);

        // Note: delegate limits testing to test_get_records_sql().
    }

    public function test_get_records_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $inskey1 = $DB->insert_record($tablename, array('course' => 3));
        $inskey2 = $DB->insert_record($tablename, array('course' => 5));
        $inskey3 = $DB->insert_record($tablename, array('course' => 4));
        $inskey4 = $DB->insert_record($tablename, array('course' => 3));
        $inskey5 = $DB->insert_record($tablename, array('course' => 2));
        $inskey6 = $DB->insert_record($tablename, array('course' => 1));
        $inskey7 = $DB->insert_record($tablename, array('course' => 0));

        $table2 = $this->get_test_table("2");
        $tablename2 = $table2->getName();
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table2->add_field('nametext', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);

        $DB->insert_record($tablename2, array('course'=>3, 'nametext'=>'badabing'));
        $DB->insert_record($tablename2, array('course'=>4, 'nametext'=>'badabang'));
        $DB->insert_record($tablename2, array('course'=>5, 'nametext'=>'badabung'));
        $DB->insert_record($tablename2, array('course'=>6, 'nametext'=>'badabong'));

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE course = ?", array(3));
        $this->assertCount(2, $records);
        $this->assertEquals($inskey1, reset($records)->id);
        $this->assertEquals($inskey4, next($records)->id);

        // Awful test, requires debug enabled and sent to browser. Let's do that and restore after test.
        $records = $DB->get_records_sql("SELECT course AS id, course AS course FROM {{$tablename}}", null);
        $this->assertDebuggingCalled();
        $this->assertCount(6, $records);
        set_debugging(DEBUG_MINIMAL);
        $records = $DB->get_records_sql("SELECT course AS id, course AS course FROM {{$tablename}}", null);
        $this->assertDebuggingNotCalled();
        $this->assertCount(6, $records);
        set_debugging(DEBUG_DEVELOPER);

        // Negative limits = no limits.
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, -1, -1);
        $this->assertCount(7, $records);

        // Zero limits = no limits.
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 0, 0);
        $this->assertCount(7, $records);

        // Only limitfrom = skips that number of records.
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 2, 0);
        $this->assertCount(5, $records);
        $this->assertEquals($inskey3, reset($records)->id);
        $this->assertEquals($inskey7, end($records)->id);

        // Only limitnum = fetches that number of records.
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 0, 3);
        $this->assertCount(3, $records);
        $this->assertEquals($inskey1, reset($records)->id);
        $this->assertEquals($inskey3, end($records)->id);

        // Both limitfrom and limitnum = skips limitfrom records and fetches limitnum ones.
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 3, 2);
        $this->assertCount(2, $records);
        $this->assertEquals($inskey4, reset($records)->id);
        $this->assertEquals($inskey5, end($records)->id);

        // Both limitfrom and limitnum in query having subqueris.
        // Note the subquery skips records with course = 0 and 3.
        $sql = "SELECT * FROM {{$tablename}}
                 WHERE course NOT IN (
                     SELECT course FROM {{$tablename}}
                      WHERE course IN (0, 3))
                ORDER BY course";
        $records = $DB->get_records_sql($sql, null, 0, 2); // Skip 0, get 2.
        $this->assertCount(2, $records);
        $this->assertEquals($inskey6, reset($records)->id);
        $this->assertEquals($inskey5, end($records)->id);
        $records = $DB->get_records_sql($sql, null, 2, 2); // Skip 2, get 2.
        $this->assertCount(2, $records);
        $this->assertEquals($inskey3, reset($records)->id);
        $this->assertEquals($inskey2, end($records)->id);

        // Test 2 tables with aliases and limits with order bys.
        $sql = "SELECT t1.id, t1.course AS cid, t2.nametext
                  FROM {{$tablename}} t1, {{$tablename2}} t2
                 WHERE t2.course=t1.course
              ORDER BY t1.course, ". $DB->sql_compare_text('t2.nametext');
        $records = $DB->get_records_sql($sql, null, 2, 2); // Skip courses 3 and 6, get 4 and 5.
        $this->assertCount(2, $records);
        $this->assertSame('5', end($records)->cid);
        $this->assertSame('4', reset($records)->cid);

        // Test 2 tables with aliases and limits with the highest INT limit works.
        $records = $DB->get_records_sql($sql, null, 2, PHP_INT_MAX); // Skip course {3,6}, get {4,5}.
        $this->assertCount(2, $records);
        $this->assertSame('5', end($records)->cid);
        $this->assertSame('4', reset($records)->cid);

        // Test 2 tables with aliases and limits with order bys (limit which is highest INT number).
        $records = $DB->get_records_sql($sql, null, PHP_INT_MAX, 2); // Skip all courses.
        $this->assertCount(0, $records);

        // Test 2 tables with aliases and limits with order bys (limit which s highest INT number).
        $records = $DB->get_records_sql($sql, null, PHP_INT_MAX, PHP_INT_MAX); // Skip all courses.
        $this->assertCount(0, $records);

        // TODO: Test limits in queries having DISTINCT clauses.

        // Note: fetching nulls, empties, LOBs already tested by test_update_record() no needed here.
    }

    public function test_get_records_menu() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $records = $DB->get_records_menu($tablename, array('course' => 3));
        $this->assertInternalType('array', $records);
        $this->assertCount(2, $records);
        $this->assertNotEmpty($records[1]);
        $this->assertNotEmpty($records[2]);
        $this->assertEquals(3, $records[1]);
        $this->assertEquals(3, $records[2]);

        // Note: delegate limits testing to test_get_records_sql().
    }

    public function test_get_records_select_menu() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));

        $records = $DB->get_records_select_menu($tablename, "course > ?", array(2));
        $this->assertInternalType('array', $records);

        $this->assertCount(3, $records);
        $this->assertArrayHasKey(1, $records);
        $this->assertArrayNotHasKey(2, $records);
        $this->assertArrayHasKey(3, $records);
        $this->assertArrayHasKey(4, $records);
        $this->assertSame('3', $records[1]);
        $this->assertSame('3', $records[3]);
        $this->assertSame('5', $records[4]);

        // Note: delegate limits testing to test_get_records_sql().
    }

    public function test_get_records_sql_menu() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));

        $records = $DB->get_records_sql_menu("SELECT * FROM {{$tablename}} WHERE course > ?", array(2));
        $this->assertInternalType('array', $records);

        $this->assertCount(3, $records);
        $this->assertArrayHasKey(1, $records);
        $this->assertArrayNotHasKey(2, $records);
        $this->assertArrayHasKey(3, $records);
        $this->assertArrayHasKey(4, $records);
        $this->assertSame('3', $records[1]);
        $this->assertSame('3', $records[3]);
        $this->assertSame('5', $records[4]);

        // Note: delegate limits testing to test_get_records_sql().
    }

    public function test_get_record() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        $record = $DB->get_record($tablename, array('id' => 2));
        $this->assertInstanceOf('stdClass', $record);

        $this->assertEquals(2, $record->course);
        $this->assertEquals(2, $record->id);
    }


    public function test_get_record_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        $record = $DB->get_record_select($tablename, "id = ?", array(2));
        $this->assertInstanceOf('stdClass', $record);

        $this->assertEquals(2, $record->course);

        // Note: delegates limit testing to test_get_records_sql().
    }

    public function test_get_record_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        // Standard use.
        $record = $DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(2));
        $this->assertInstanceOf('stdClass', $record);
        $this->assertEquals(2, $record->course);
        $this->assertEquals(2, $record->id);

        // Backwards compatibility with $ignoremultiple.
        $this->assertFalse((bool)IGNORE_MISSING);
        $this->assertTrue((bool)IGNORE_MULTIPLE);

        // Record not found - ignore.
        $this->assertFalse($DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(666), IGNORE_MISSING));
        $this->assertFalse($DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(666), IGNORE_MULTIPLE));

        // Record not found error.
        try {
            $DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(666), MUST_EXIST);
            $this->fail("Exception expected");
        } catch (dml_missing_record_exception $e) {
            $this->assertTrue(true);
        }

        $this->assertNotEmpty($DB->get_record_sql("SELECT * FROM {{$tablename}}", array(), IGNORE_MISSING));
        $this->assertDebuggingCalled();
        set_debugging(DEBUG_MINIMAL);
        $this->assertNotEmpty($DB->get_record_sql("SELECT * FROM {{$tablename}}", array(), IGNORE_MISSING));
        $this->assertDebuggingNotCalled();
        set_debugging(DEBUG_DEVELOPER);

        // Multiple matches ignored.
        $this->assertNotEmpty($DB->get_record_sql("SELECT * FROM {{$tablename}}", array(), IGNORE_MULTIPLE));

        // Multiple found error.
        try {
            $DB->get_record_sql("SELECT * FROM {{$tablename}}", array(), MUST_EXIST);
            $this->fail("Exception expected");
        } catch (dml_multiple_records_exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_get_field() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $id1 = $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertEquals(3, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEquals(3, $DB->get_field($tablename, 'course', array('course' => 3)));

        $this->assertFalse($DB->get_field($tablename, 'course', array('course' => 11), IGNORE_MISSING));
        try {
            $DB->get_field($tablename, 'course', array('course' => 4), MUST_EXIST);
            $this->fail('Exception expected due to missing record');
        } catch (dml_exception $ex) {
            $this->assertTrue(true);
        }

        $this->assertEquals(5, $DB->get_field($tablename, 'course', array('course' => 5), IGNORE_MULTIPLE));
        $this->assertDebuggingNotCalled();

        $this->assertEquals(5, $DB->get_field($tablename, 'course', array('course' => 5), IGNORE_MISSING));
        $this->assertDebuggingCalled();

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext' => '1');
        try {
            $DB->get_field($tablename, 'course', $conditions);
            if (debugging()) {
                // Only in debug mode - hopefully all devs test code in debug mode...
                $this->fail('An Exception is missing, expected due to equating of text fields');
            }
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }
    }

    public function test_get_field_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));

        $this->assertEquals(3, $DB->get_field_select($tablename, 'course', "id = ?", array(1)));
    }

    public function test_get_field_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));

        $this->assertEquals(3, $DB->get_field_sql("SELECT course FROM {{$tablename}} WHERE id = ?", array(1)));
    }

    public function test_get_fieldset_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 6));

        $fieldset = $DB->get_fieldset_select($tablename, 'course', "course > ?", array(1));
        $this->assertInternalType('array', $fieldset);

        $this->assertCount(3, $fieldset);
        $this->assertEquals(3, $fieldset[0]);
        $this->assertEquals(2, $fieldset[1]);
        $this->assertEquals(6, $fieldset[2]);
    }

    public function test_get_fieldset_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $binarydata = '\\'.chr(241);

        $DB->insert_record($tablename, array('course' => 1, 'onebinary' => $binarydata));
        $DB->insert_record($tablename, array('course' => 3, 'onebinary' => $binarydata));
        $DB->insert_record($tablename, array('course' => 2, 'onebinary' => $binarydata));
        $DB->insert_record($tablename, array('course' => 6, 'onebinary' => $binarydata));

        $fieldset = $DB->get_fieldset_sql("SELECT * FROM {{$tablename}} WHERE course > ?", array(1));
        $this->assertInternalType('array', $fieldset);

        $this->assertCount(3, $fieldset);
        $this->assertEquals(2, $fieldset[0]);
        $this->assertEquals(3, $fieldset[1]);
        $this->assertEquals(4, $fieldset[2]);

        $fieldset = $DB->get_fieldset_sql("SELECT onebinary FROM {{$tablename}} WHERE course > ?", array(1));
        $this->assertInternalType('array', $fieldset);

        $this->assertCount(3, $fieldset);
        $this->assertEquals($binarydata, $fieldset[0]);
        $this->assertEquals($binarydata, $fieldset[1]);
        $this->assertEquals($binarydata, $fieldset[2]);
    }

    public function test_insert_record_raw() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $record = (object)array('course' => 1, 'onechar' => 'xx');
        $before = clone($record);
        $result = $DB->insert_record_raw($tablename, $record);
        $this->assertSame(1, $result);
        $this->assertEquals($record, $before);

        $record = $DB->get_record($tablename, array('course' => 1));
        $this->assertInstanceOf('stdClass', $record);
        $this->assertSame('xx', $record->onechar);

        $result = $DB->insert_record_raw($tablename, array('course' => 2, 'onechar' => 'yy'), false);
        $this->assertTrue($result);

        // Note: bulk not implemented yet.
        $DB->insert_record_raw($tablename, array('course' => 3, 'onechar' => 'zz'), true, true);
        $record = $DB->get_record($tablename, array('course' => 3));
        $this->assertInstanceOf('stdClass', $record);
        $this->assertSame('zz', $record->onechar);

        // Custom sequence (id) - returnid is ignored.
        $result = $DB->insert_record_raw($tablename, array('id' => 10, 'course' => 3, 'onechar' => 'bb'), true, false, true);
        $this->assertTrue($result);
        $record = $DB->get_record($tablename, array('id' => 10));
        $this->assertInstanceOf('stdClass', $record);
        $this->assertSame('bb', $record->onechar);

        // Custom sequence - missing id error.
        try {
            $DB->insert_record_raw($tablename, array('course' => 3, 'onechar' => 'bb'), true, false, true);
            $this->fail('Exception expected due to missing record');
        } catch (coding_exception $ex) {
            $this->assertTrue(true);
        }

        // Wrong column error.
        try {
            $DB->insert_record_raw($tablename, array('xxxxx' => 3, 'onechar' => 'bb'));
            $this->fail('Exception expected due to invalid column');
        } catch (dml_exception $ex) {
            $this->assertTrue(true);
        }

        // Create something similar to "context_temp" with id column without sequence.
        $dbman->drop_table($table);
        $table = $this->get_test_table();
        $tablename = $table->getName();
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $record = (object)array('id'=>5, 'course' => 1);
        $DB->insert_record_raw($tablename, $record, false, false, true);
        $record = $DB->get_record($tablename, array());
        $this->assertEquals(5, $record->id);
    }

    public function test_insert_record() {
        // All the information in this test is fetched from DB by get_recordset() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', null, null, null, 100);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertSame(1, $DB->insert_record($tablename, array('course' => 1), true));
        $record = $DB->get_record($tablename, array('course' => 1));
        $this->assertEquals(1, $record->id);
        $this->assertEquals(100, $record->oneint); // Just check column defaults have been applied.
        $this->assertEquals(200, $record->onenum);
        $this->assertSame('onestring', $record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Without returning id, bulk not implemented.
        $result = $this->assertTrue($DB->insert_record($tablename, array('course' => 99), false, true));
        $record = $DB->get_record($tablename, array('course' => 99));
        $this->assertEquals(2, $record->id);
        $this->assertEquals(99, $record->course);

        // Check nulls are set properly for all types.
        $record = new stdClass();
        $record->oneint = null;
        $record->onenum = null;
        $record->onechar = null;
        $record->onetext = null;
        $record->onebinary = null;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEquals(0, $record->course);
        $this->assertNull($record->oneint);
        $this->assertNull($record->onenum);
        $this->assertNull($record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check zeros are set properly for all types.
        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = 0;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEquals(0, $record->oneint);
        $this->assertEquals(0, $record->onenum);

        // Check booleans are set properly for all types.
        $record = new stdClass();
        $record->oneint = true; // Trues.
        $record->onenum = true;
        $record->onechar = true;
        $record->onetext = true;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEquals(1, $record->oneint);
        $this->assertEquals(1, $record->onenum);
        $this->assertEquals(1, $record->onechar);
        $this->assertEquals(1, $record->onetext);

        $record = new stdClass();
        $record->oneint = false; // Falses.
        $record->onenum = false;
        $record->onechar = false;
        $record->onetext = false;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEquals(0, $record->oneint);
        $this->assertEquals(0, $record->onenum);
        $this->assertEquals(0, $record->onechar);
        $this->assertEquals(0, $record->onetext);

        // Check string data causes exception in numeric types.
        $record = new stdClass();
        $record->oneint = 'onestring';
        $record->onenum = 0;
        try {
            $DB->insert_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }
        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = 'onestring';
        try {
            $DB->insert_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Check empty string data is stored as 0 in numeric datatypes.
        $record = new stdClass();
        $record->oneint = ''; // Empty string.
        $record->onenum = 0;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertTrue(is_numeric($record->oneint) && $record->oneint == 0);

        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = ''; // Empty string.
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertTrue(is_numeric($record->onenum) && $record->onenum == 0);

        // Check empty strings are set properly in string types.
        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = 0;
        $record->onechar = '';
        $record->onetext = '';
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertTrue($record->onechar === '');
        $this->assertTrue($record->onetext === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types.
        $record = new stdClass();
        $record->oneint = ((210.10 + 39.92) - 150.02);
        $record->onenum = ((210.10 + 39.92) - 150.02);
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEquals(100, $record->oneint);
        $this->assertEquals(100, $record->onenum);

        // Check various quotes/backslashes combinations in string types.
        $teststrings = array(
            'backslashes and quotes alone (even): "" \'\' \\\\',
            'backslashes and quotes alone (odd): """ \'\'\' \\\\\\',
            'backslashes and quotes sequences (even): \\"\\" \\\'\\\'',
            'backslashes and quotes sequences (odd): \\"\\"\\" \\\'\\\'\\\'');
        foreach ($teststrings as $teststring) {
            $record = new stdClass();
            $record->onechar = $teststring;
            $record->onetext = $teststring;
            $recid = $DB->insert_record($tablename, $record);
            $record = $DB->get_record($tablename, array('id' => $recid));
            $this->assertEquals($teststring, $record->onechar);
            $this->assertEquals($teststring, $record->onetext);
        }

        // Check LOBs in text/binary columns.
        $clob = file_get_contents(__DIR__ . '/fixtures/clob.txt');
        $blob = file_get_contents(__DIR__ . '/fixtures/randombinary');
        $record = new stdClass();
        $record->onetext = $clob;
        $record->onebinary = $blob;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEquals($clob, $record->onetext, 'Test CLOB insert (full contents output disabled)');
        $this->assertEquals($blob, $record->onebinary, 'Test BLOB insert (full contents output disabled)');

        // And "small" LOBs too, just in case.
        $newclob = substr($clob, 0, 500);
        $newblob = substr($blob, 0, 250);
        $record = new stdClass();
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEquals($newclob, $record->onetext, 'Test "small" CLOB insert (full contents output disabled)');
        $this->assertEquals($newblob, $record->onebinary, 'Test "small" BLOB insert (full contents output disabled)');
        $this->assertEquals(false, $rs->key()); // Ensure recordset key() method to be working ok after closing.

        // And "diagnostic" LOBs too, just in case.
        $newclob = '\'"\\;/ěščřžýáíé';
        $newblob = '\'"\\;/ěščřžýáíé';
        $record = new stdClass();
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertSame($newclob, $record->onetext);
        $this->assertSame($newblob, $record->onebinary);
        $this->assertEquals(false, $rs->key()); // Ensure recordset key() method to be working ok after closing.

        // Test data is not modified.
        $record = new stdClass();
        $record->id     = -1; // Has to be ignored.
        $record->course = 3;
        $record->lalala = 'lalal'; // Unused.
        $before = clone($record);
        $DB->insert_record($tablename, $record);
        $this->assertEquals($record, $before);

        // Make sure the id is always increasing and never reuses the same id.
        $id1 = $DB->insert_record($tablename, array('course' => 3));
        $id2 = $DB->insert_record($tablename, array('course' => 3));
        $this->assertTrue($id1 < $id2);
        $DB->delete_records($tablename, array('id'=>$id2));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $this->assertTrue($id2 < $id3);
        $DB->delete_records($tablename, array());
        $id4 = $DB->insert_record($tablename, array('course' => 3));
        $this->assertTrue($id3 < $id4);

        // Test saving a float in a CHAR column, and reading it back.
        $id = $DB->insert_record($tablename, array('onechar' => 1.0));
        $this->assertEquals(1.0, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e20));
        $this->assertEquals(1e20, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e-4));
        $this->assertEquals(1e-4, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e-5));
        $this->assertEquals(1e-5, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e-300));
        $this->assertEquals(1e-300, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e300));
        $this->assertEquals(1e300, $DB->get_field($tablename, 'onechar', array('id' => $id)));

        // Test saving a float in a TEXT column, and reading it back.
        $id = $DB->insert_record($tablename, array('onetext' => 1.0));
        $this->assertEquals(1.0, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e20));
        $this->assertEquals(1e20, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e-4));
        $this->assertEquals(1e-4, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e-5));
        $this->assertEquals(1e-5, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e-300));
        $this->assertEquals(1e-300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e300));
        $this->assertEquals(1e300, $DB->get_field($tablename, 'onetext', array('id' => $id)));

        // Test that inserting data violating one unique key leads to error.
        // Empty the table completely.
        $this->assertTrue($DB->delete_records($tablename));

        // Add one unique constraint (index).
        $key = new xmldb_key('testuk', XMLDB_KEY_UNIQUE, array('course', 'oneint'));
        $dbman->add_key($table, $key);

        // Let's insert one record violating the constraint multiple times.
        $record = (object)array('course' => 1, 'oneint' => 1);
        $this->assertTrue($DB->insert_record($tablename, $record, false)); // Insert 1st. No problem expected.

        // Re-insert same record, not returning id. dml_exception expected.
        try {
            $DB->insert_record($tablename, $record, false);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Re-insert same record, returning id. dml_exception expected.
        try {
            $DB->insert_record($tablename, $record, true);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Try to insert a record into a non-existent table. dml_exception expected.
        try {
            $DB->insert_record('nonexistenttable', $record, true);
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }
    }

    public function test_insert_records() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', null, null, null, 100);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertCount(0, $DB->get_records($tablename));

        $record = new stdClass();
        $record->id = '1';
        $record->course = '1';
        $record->oneint = null;
        $record->onenum = 1.0;
        $record->onechar = 'a';
        $record->onetext = 'aaa';

        $expected = array();
        $records = array();
        for ($i = 1; $i <= 2000; $i++) { // This may take a while, it should be higher than defaults in DML drivers.
            $rec = clone($record);
            $rec->id = (string)$i;
            $rec->oneint = (string)$i;
            $expected[$i] = $rec;
            $rec = clone($rec);
            unset($rec->id);
            $records[$i] = $rec;
        }

        $DB->insert_records($tablename, $records);
        $stored = $DB->get_records($tablename, array(), 'id ASC');
        $this->assertEquals($expected, $stored);

        // Test there can be some extra properties including id.
        $count = $DB->count_records($tablename);
        $rec1 = (array)$record;
        $rec1['xxx'] = 1;
        $rec2 = (array)$record;
        $rec2['xxx'] = 2;

        $records = array($rec1, $rec2);
        $DB->insert_records($tablename, $records);
        $this->assertEquals($count + 2, $DB->count_records($tablename));

        // Test not all properties are necessary.
        $rec1 = (array)$record;
        unset($rec1['course']);
        $rec2 = (array)$record;
        unset($rec2['course']);

        $records = array($rec1, $rec2);
        $DB->insert_records($tablename, $records);

        // Make sure no changes in data object structure are tolerated.
        $rec1 = (array)$record;
        unset($rec1['id']);
        $rec2 = (array)$record;
        unset($rec2['id']);

        $records = array($rec1, $rec2);
        $DB->insert_records($tablename, $records);

        $rec2['xx'] = '1';
        $records = array($rec1, $rec2);
        try {
            $DB->insert_records($tablename, $records);
            $this->fail('coding_exception expected when insert_records receives different object data structures');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        unset($rec2['xx']);
        unset($rec2['course']);
        $rec2['course'] = '1';
        $records = array($rec1, $rec2);
        try {
            $DB->insert_records($tablename, $records);
            $this->fail('coding_exception expected when insert_records receives different object data structures');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $records = 1;
        try {
            $DB->insert_records($tablename, $records);
            $this->fail('coding_exception expected when insert_records receives non-traversable data');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $records = array(1);
        try {
            $DB->insert_records($tablename, $records);
            $this->fail('coding_exception expected when insert_records receives non-objet record');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_insert_record_with_nullable_unique_index() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('notnull1', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('nullable1', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('nullable2', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('notnull1-nullable1-nullable2', XMLDB_INDEX_UNIQUE,
                array('notnull1', 'nullable1', 'nullable2'));
        $dbman->create_table($table);

        // Insert one record. Should be OK (no exception).
        $DB->insert_record($tablename, (object) ['notnull1' => 1, 'nullable1' => 1, 'nullable2' => 1]);

        $this->assertEquals(1, $DB->count_records($table->getName()));
        $this->assertEquals(1, $DB->count_records($table->getName(), ['nullable1' => 1]));

        // Inserting a duplicate should fail.
        try {
            $DB->insert_record($tablename, (object) ['notnull1' => 1, 'nullable1' => 1, 'nullable2' => 1]);
            $this->fail('dml_write_exception expected when a record violates a unique index');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_write_exception', $e);
        }

        $this->assertEquals(1, $DB->count_records($table->getName()));
        $this->assertEquals(1, $DB->count_records($table->getName(), ['nullable1' => 1]));

        // Inserting a record with nulls in the nullable columns should work.
        $DB->insert_record($tablename, (object) ['notnull1' => 1, 'nullable1' => null, 'nullable2' => null]);

        $this->assertEquals(2, $DB->count_records($table->getName()));
        $this->assertEquals(1, $DB->count_records($table->getName(), ['nullable1' => 1]));
        $this->assertEquals(1, $DB->count_records($table->getName(), ['nullable1' => null]));

        // And it should be possible to insert a duplicate.
        $DB->insert_record($tablename, (object) ['notnull1' => 1, 'nullable1' => null, 'nullable2' => null]);

        $this->assertEquals(3, $DB->count_records($table->getName()));
        $this->assertEquals(1, $DB->count_records($table->getName(), ['nullable1' => 1]));
        $this->assertEquals(2, $DB->count_records($table->getName(), ['nullable1' => null]));

        // Same, but with only one of the nullable columns being null.
        $DB->insert_record($tablename, (object) ['notnull1' => 1, 'nullable1' => 1, 'nullable2' => null]);

        $this->assertEquals(4, $DB->count_records($table->getName()));
        $this->assertEquals(2, $DB->count_records($table->getName(), ['nullable1' => 1]));
        $this->assertEquals(2, $DB->count_records($table->getName(), ['nullable1' => null]));

        $DB->insert_record($tablename, (object) ['notnull1' => 1, 'nullable1' => 1, 'nullable2' => null]);

        $this->assertEquals(5, $DB->count_records($table->getName()));
        $this->assertEquals(3, $DB->count_records($table->getName(), ['nullable1' => 1]));
        $this->assertEquals(2, $DB->count_records($table->getName(), ['nullable1' => null]));

    }

    public function test_import_record() {
        // All the information in this test is fetched from DB by get_recordset() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', null, null, null, 100);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertSame(1, $DB->insert_record($tablename, array('course' => 1), true));
        $record = $DB->get_record($tablename, array('course' => 1));
        $this->assertEquals(1, $record->id);
        $this->assertEquals(100, $record->oneint); // Just check column defaults have been applied.
        $this->assertEquals(200, $record->onenum);
        $this->assertSame('onestring', $record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Ignore extra columns.
        $record = (object)array('id'=>13, 'course'=>2, 'xxxx'=>788778);
        $before = clone($record);
        $this->assertTrue($DB->import_record($tablename, $record));
        $this->assertEquals($record, $before);
        $records = $DB->get_records($tablename);
        $this->assertEquals(2, $records[13]->course);

        // Check nulls are set properly for all types.
        $record = new stdClass();
        $record->id = 20;
        $record->oneint = null;
        $record->onenum = null;
        $record->onechar = null;
        $record->onetext = null;
        $record->onebinary = null;
        $this->assertTrue($DB->import_record($tablename, $record));
        $record = $DB->get_record($tablename, array('id' => 20));
        $this->assertEquals(0, $record->course);
        $this->assertNull($record->oneint);
        $this->assertNull($record->onenum);
        $this->assertNull($record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check zeros are set properly for all types.
        $record = new stdClass();
        $record->id = 23;
        $record->oneint = 0;
        $record->onenum = 0;
        $this->assertTrue($DB->import_record($tablename, $record));
        $record = $DB->get_record($tablename, array('id' => 23));
        $this->assertEquals(0, $record->oneint);
        $this->assertEquals(0, $record->onenum);

        // Check string data causes exception in numeric types.
        $record = new stdClass();
        $record->id = 32;
        $record->oneint = 'onestring';
        $record->onenum = 0;
        try {
            $DB->import_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }
        $record = new stdClass();
        $record->id = 35;
        $record->oneint = 0;
        $record->onenum = 'onestring';
        try {
            $DB->import_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Check empty strings are set properly in string types.
        $record = new stdClass();
        $record->id = 44;
        $record->oneint = 0;
        $record->onenum = 0;
        $record->onechar = '';
        $record->onetext = '';
        $this->assertTrue($DB->import_record($tablename, $record));
        $record = $DB->get_record($tablename, array('id' => 44));
        $this->assertTrue($record->onechar === '');
        $this->assertTrue($record->onetext === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types.
        $record = new stdClass();
        $record->id = 47;
        $record->oneint = ((210.10 + 39.92) - 150.02);
        $record->onenum = ((210.10 + 39.92) - 150.02);
        $this->assertTrue($DB->import_record($tablename, $record));
        $record = $DB->get_record($tablename, array('id' => 47));
        $this->assertEquals(100, $record->oneint);
        $this->assertEquals(100, $record->onenum);

        // Check various quotes/backslashes combinations in string types.
        $i = 50;
        $teststrings = array(
            'backslashes and quotes alone (even): "" \'\' \\\\',
            'backslashes and quotes alone (odd): """ \'\'\' \\\\\\',
            'backslashes and quotes sequences (even): \\"\\" \\\'\\\'',
            'backslashes and quotes sequences (odd): \\"\\"\\" \\\'\\\'\\\'');
        foreach ($teststrings as $teststring) {
            $record = new stdClass();
            $record->id = $i;
            $record->onechar = $teststring;
            $record->onetext = $teststring;
            $this->assertTrue($DB->import_record($tablename, $record));
            $record = $DB->get_record($tablename, array('id' => $i));
            $this->assertEquals($teststring, $record->onechar);
            $this->assertEquals($teststring, $record->onetext);
            $i = $i + 3;
        }

        // Check LOBs in text/binary columns.
        $clob = file_get_contents(__DIR__ . '/fixtures/clob.txt');
        $record = new stdClass();
        $record->id = 70;
        $record->onetext = $clob;
        $record->onebinary = '';
        $this->assertTrue($DB->import_record($tablename, $record));
        $rs = $DB->get_recordset($tablename, array('id' => 70));
        $record = $rs->current();
        $rs->close();
        $this->assertEquals($clob, $record->onetext, 'Test CLOB insert (full contents output disabled)');

        $blob = file_get_contents(__DIR__ . '/fixtures/randombinary');
        $record = new stdClass();
        $record->id = 71;
        $record->onetext = '';
        $record->onebinary = $blob;
        $this->assertTrue($DB->import_record($tablename, $record));
        $rs = $DB->get_recordset($tablename, array('id' => 71));
        $record = $rs->current();
        $rs->close();
        $this->assertEquals($blob, $record->onebinary, 'Test BLOB insert (full contents output disabled)');

        // And "small" LOBs too, just in case.
        $newclob = substr($clob, 0, 500);
        $newblob = substr($blob, 0, 250);
        $record = new stdClass();
        $record->id = 73;
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $this->assertTrue($DB->import_record($tablename, $record));
        $rs = $DB->get_recordset($tablename, array('id' => 73));
        $record = $rs->current();
        $rs->close();
        $this->assertEquals($newclob, $record->onetext, 'Test "small" CLOB insert (full contents output disabled)');
        $this->assertEquals($newblob, $record->onebinary, 'Test "small" BLOB insert (full contents output disabled)');
        $this->assertEquals(false, $rs->key()); // Ensure recordset key() method to be working ok after closing.
    }

    public function test_update_record_raw() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 3));

        $record = $DB->get_record($tablename, array('course' => 1));
        $record->course = 2;
        $this->assertTrue($DB->update_record_raw($tablename, $record));
        $this->assertEquals(0, $DB->count_records($tablename, array('course' => 1)));
        $this->assertEquals(1, $DB->count_records($tablename, array('course' => 2)));
        $this->assertEquals(1, $DB->count_records($tablename, array('course' => 3)));

        $record = $DB->get_record($tablename, array('course' => 3));
        $record->xxxxx = 2;
        try {
            $DB->update_record_raw($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }

        $record = $DB->get_record($tablename, array('course' => 3));
        unset($record->id);
        try {
            $DB->update_record_raw($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_update_record() {

        // All the information in this test is fetched from DB by get_record() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', null, null, null, 100);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $record = $DB->get_record($tablename, array('course' => 1));
        $record->course = 2;

        $this->assertTrue($DB->update_record($tablename, $record));
        $this->assertFalse($record = $DB->get_record($tablename, array('course' => 1)));
        $this->assertNotEmpty($record = $DB->get_record($tablename, array('course' => 2)));
        $this->assertEquals(100, $record->oneint); // Just check column defaults have been applied.
        $this->assertEquals(200, $record->onenum);
        $this->assertSame('onestring', $record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check nulls are set properly for all types.
        $record->oneint = null;
        $record->onenum = null;
        $record->onechar = null;
        $record->onetext = null;
        $record->onebinary = null;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertNull($record->oneint);
        $this->assertNull($record->onenum);
        $this->assertNull($record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check zeros are set properly for all types.
        $record->oneint = 0;
        $record->onenum = 0;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEquals(0, $record->oneint);
        $this->assertEquals(0, $record->onenum);

        // Check booleans are set properly for all types.
        $record->oneint = true; // Trues.
        $record->onenum = true;
        $record->onechar = true;
        $record->onetext = true;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEquals(1, $record->oneint);
        $this->assertEquals(1, $record->onenum);
        $this->assertEquals(1, $record->onechar);
        $this->assertEquals(1, $record->onetext);

        $record->oneint = false; // Falses.
        $record->onenum = false;
        $record->onechar = false;
        $record->onetext = false;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEquals(0, $record->oneint);
        $this->assertEquals(0, $record->onenum);
        $this->assertEquals(0, $record->onechar);
        $this->assertEquals(0, $record->onetext);

        // Check string data causes exception in numeric types.
        $record->oneint = 'onestring';
        $record->onenum = 0;
        try {
            $DB->update_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }
        $record->oneint = 0;
        $record->onenum = 'onestring';
        try {
            $DB->update_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Check empty string data is stored as 0 in numeric datatypes.
        $record->oneint = ''; // Empty string.
        $record->onenum = 0;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertTrue(is_numeric($record->oneint) && $record->oneint == 0);

        $record->oneint = 0;
        $record->onenum = ''; // Empty string.
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertTrue(is_numeric($record->onenum) && $record->onenum == 0);

        // Check empty strings are set properly in string types.
        $record->oneint = 0;
        $record->onenum = 0;
        $record->onechar = '';
        $record->onetext = '';
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertTrue($record->onechar === '');
        $this->assertTrue($record->onetext === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types.
        $record->oneint = ((210.10 + 39.92) - 150.02);
        $record->onenum = ((210.10 + 39.92) - 150.02);
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEquals(100, $record->oneint);
        $this->assertEquals(100, $record->onenum);

        // Check various quotes/backslashes combinations in string types.
        $teststrings = array(
            'backslashes and quotes alone (even): "" \'\' \\\\',
            'backslashes and quotes alone (odd): """ \'\'\' \\\\\\',
            'backslashes and quotes sequences (even): \\"\\" \\\'\\\'',
            'backslashes and quotes sequences (odd): \\"\\"\\" \\\'\\\'\\\'');
        foreach ($teststrings as $teststring) {
            $record->onechar = $teststring;
            $record->onetext = $teststring;
            $DB->update_record($tablename, $record);
            $record = $DB->get_record($tablename, array('course' => 2));
            $this->assertEquals($teststring, $record->onechar);
            $this->assertEquals($teststring, $record->onetext);
        }

        // Check LOBs in text/binary columns.
        $clob = file_get_contents(__DIR__ . '/fixtures/clob.txt');
        $blob = file_get_contents(__DIR__ . '/fixtures/randombinary');
        $record->onetext = $clob;
        $record->onebinary = $blob;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEquals($clob, $record->onetext, 'Test CLOB update (full contents output disabled)');
        $this->assertEquals($blob, $record->onebinary, 'Test BLOB update (full contents output disabled)');

        // And "small" LOBs too, just in case.
        $newclob = substr($clob, 0, 500);
        $newblob = substr($blob, 0, 250);
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEquals($newclob, $record->onetext, 'Test "small" CLOB update (full contents output disabled)');
        $this->assertEquals($newblob, $record->onebinary, 'Test "small" BLOB update (full contents output disabled)');

        // Test saving a float in a CHAR column, and reading it back.
        $id = $DB->insert_record($tablename, array('onechar' => 'X'));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1.0));
        $this->assertEquals(1.0, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e20));
        $this->assertEquals(1e20, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e-4));
        $this->assertEquals(1e-4, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e-5));
        $this->assertEquals(1e-5, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e-300));
        $this->assertEquals(1e-300, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e300));
        $this->assertEquals(1e300, $DB->get_field($tablename, 'onechar', array('id' => $id)));

        // Test saving a float in a TEXT column, and reading it back.
        $id = $DB->insert_record($tablename, array('onetext' => 'X'));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1.0));
        $this->assertEquals(1.0, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e20));
        $this->assertEquals(1e20, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e-4));
        $this->assertEquals(1e-4, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e-5));
        $this->assertEquals(1e-5, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e-300));
        $this->assertEquals(1e-300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e300));
        $this->assertEquals(1e300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
    }

    public function test_set_field() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null);
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // Simple set_field.
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $this->assertTrue($DB->set_field($tablename, 'course', 2, array('id' => $id1)));
        $this->assertEquals(2, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEquals(1, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEquals(3, $DB->get_field($tablename, 'course', array('id' => $id3)));
        $DB->delete_records($tablename, array());

        // Multiple fields affected.
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $DB->set_field($tablename, 'course', '5', array('course' => 1));
        $this->assertEquals(5, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEquals(5, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEquals(3, $DB->get_field($tablename, 'course', array('id' => $id3)));
        $DB->delete_records($tablename, array());

        // No field affected.
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $DB->set_field($tablename, 'course', '5', array('course' => 0));
        $this->assertEquals(1, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEquals(1, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEquals(3, $DB->get_field($tablename, 'course', array('id' => $id3)));
        $DB->delete_records($tablename, array());

        // All fields - no condition.
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $DB->set_field($tablename, 'course', 5, array());
        $this->assertEquals(5, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEquals(5, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEquals(5, $DB->get_field($tablename, 'course', array('id' => $id3)));

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext' => '1');
        try {
            $DB->set_field($tablename, 'onechar', 'frog', $conditions);
            if (debugging()) {
                // Only in debug mode - hopefully all devs test code in debug mode...
                $this->fail('An Exception is missing, expected due to equating of text fields');
            }
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }

        // Test saving a float in a CHAR column, and reading it back.
        $id = $DB->insert_record($tablename, array('onechar' => 'X'));
        $DB->set_field($tablename, 'onechar', 1.0, array('id' => $id));
        $this->assertEquals(1.0, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e20, array('id' => $id));
        $this->assertEquals(1e20, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e-4, array('id' => $id));
        $this->assertEquals(1e-4, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e-5, array('id' => $id));
        $this->assertEquals(1e-5, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e-300, array('id' => $id));
        $this->assertEquals(1e-300, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e300, array('id' => $id));
        $this->assertEquals(1e300, $DB->get_field($tablename, 'onechar', array('id' => $id)));

        // Test saving a float in a TEXT column, and reading it back.
        $id = $DB->insert_record($tablename, array('onetext' => 'X'));
        $DB->set_field($tablename, 'onetext', 1.0, array('id' => $id));
        $this->assertEquals(1.0, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e20, array('id' => $id));
        $this->assertEquals(1e20, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e-4, array('id' => $id));
        $this->assertEquals(1e-4, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e-5, array('id' => $id));
        $this->assertEquals(1e-5, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e-300, array('id' => $id));
        $this->assertEquals(1e-300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e300, array('id' => $id));
        $this->assertEquals(1e300, $DB->get_field($tablename, 'onetext', array('id' => $id)));

        // Note: All the nulls, booleans, empties, quoted and backslashes tests
        // go to set_field_select() because set_field() is just one wrapper over it.
    }

    public function test_set_field_select() {

        // All the information in this test is fetched from DB by get_field() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', null, null, null);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null);
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));

        $this->assertTrue($DB->set_field_select($tablename, 'course', 2, 'id = ?', array(1)));
        $this->assertEquals(2, $DB->get_field($tablename, 'course', array('id' => 1)));

        // Check nulls are set properly for all types.
        $DB->set_field_select($tablename, 'oneint', null, 'id = ?', array(1)); // Trues.
        $DB->set_field_select($tablename, 'onenum', null, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onechar', null, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', null, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onebinary', null, 'id = ?', array(1));
        $this->assertNull($DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onenum', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onechar', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onetext', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onebinary', array('id' => 1)));

        // Check zeros are set properly for all types.
        $DB->set_field_select($tablename, 'oneint', 0, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onenum', 0, 'id = ?', array(1));
        $this->assertEquals(0, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEquals(0, $DB->get_field($tablename, 'onenum', array('id' => 1)));

        // Check booleans are set properly for all types.
        $DB->set_field_select($tablename, 'oneint', true, 'id = ?', array(1)); // Trues.
        $DB->set_field_select($tablename, 'onenum', true, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onechar', true, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', true, 'id = ?', array(1));
        $this->assertEquals(1, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEquals(1, $DB->get_field($tablename, 'onenum', array('id' => 1)));
        $this->assertEquals(1, $DB->get_field($tablename, 'onechar', array('id' => 1)));
        $this->assertEquals(1, $DB->get_field($tablename, 'onetext', array('id' => 1)));

        $DB->set_field_select($tablename, 'oneint', false, 'id = ?', array(1)); // Falses.
        $DB->set_field_select($tablename, 'onenum', false, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onechar', false, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', false, 'id = ?', array(1));
        $this->assertEquals(0, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEquals(0, $DB->get_field($tablename, 'onenum', array('id' => 1)));
        $this->assertEquals(0, $DB->get_field($tablename, 'onechar', array('id' => 1)));
        $this->assertEquals(0, $DB->get_field($tablename, 'onetext', array('id' => 1)));

        // Check string data causes exception in numeric types.
        try {
            $DB->set_field_select($tablename, 'oneint', 'onestring', 'id = ?', array(1));
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }
        try {
            $DB->set_field_select($tablename, 'onenum', 'onestring', 'id = ?', array(1));
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
        }

        // Check empty string data is stored as 0 in numeric datatypes.
        $DB->set_field_select($tablename, 'oneint', '', 'id = ?', array(1));
        $field = $DB->get_field($tablename, 'oneint', array('id' => 1));
        $this->assertTrue(is_numeric($field) && $field == 0);

        $DB->set_field_select($tablename, 'onenum', '', 'id = ?', array(1));
        $field = $DB->get_field($tablename, 'onenum', array('id' => 1));
        $this->assertTrue(is_numeric($field) && $field == 0);

        // Check empty strings are set properly in string types.
        $DB->set_field_select($tablename, 'onechar', '', 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', '', 'id = ?', array(1));
        $this->assertTrue($DB->get_field($tablename, 'onechar', array('id' => 1)) === '');
        $this->assertTrue($DB->get_field($tablename, 'onetext', array('id' => 1)) === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types.
        $DB->set_field_select($tablename, 'oneint', ((210.10 + 39.92) - 150.02), 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onenum', ((210.10 + 39.92) - 150.02), 'id = ?', array(1));
        $this->assertEquals(100, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEquals(100, $DB->get_field($tablename, 'onenum', array('id' => 1)));

        // Check various quotes/backslashes combinations in string types.
        $teststrings = array(
            'backslashes and quotes alone (even): "" \'\' \\\\',
            'backslashes and quotes alone (odd): """ \'\'\' \\\\\\',
            'backslashes and quotes sequences (even): \\"\\" \\\'\\\'',
            'backslashes and quotes sequences (odd): \\"\\"\\" \\\'\\\'\\\'');
        foreach ($teststrings as $teststring) {
            $DB->set_field_select($tablename, 'onechar', $teststring, 'id = ?', array(1));
            $DB->set_field_select($tablename, 'onetext', $teststring, 'id = ?', array(1));
            $this->assertEquals($teststring, $DB->get_field($tablename, 'onechar', array('id' => 1)));
            $this->assertEquals($teststring, $DB->get_field($tablename, 'onetext', array('id' => 1)));
        }

        // Check LOBs in text/binary columns.
        $clob = file_get_contents(__DIR__ . '/fixtures/clob.txt');
        $blob = file_get_contents(__DIR__ . '/fixtures/randombinary');
        $DB->set_field_select($tablename, 'onetext', $clob, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onebinary', $blob, 'id = ?', array(1));
        $this->assertEquals($clob, $DB->get_field($tablename, 'onetext', array('id' => 1)), 'Test CLOB set_field (full contents output disabled)');
        $this->assertEquals($blob, $DB->get_field($tablename, 'onebinary', array('id' => 1)), 'Test BLOB set_field (full contents output disabled)');

        // Empty data in binary columns works.
        $DB->set_field_select($tablename, 'onebinary', '', 'id = ?', array(1));
        $this->assertEquals('', $DB->get_field($tablename, 'onebinary', array('id' => 1)), 'Blobs need to accept empty values.');

        // And "small" LOBs too, just in case.
        $newclob = substr($clob, 0, 500);
        $newblob = substr($blob, 0, 250);
        $DB->set_field_select($tablename, 'onetext', $newclob, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onebinary', $newblob, 'id = ?', array(1));
        $this->assertEquals($newclob, $DB->get_field($tablename, 'onetext', array('id' => 1)), 'Test "small" CLOB set_field (full contents output disabled)');
        $this->assertEquals($newblob, $DB->get_field($tablename, 'onebinary', array('id' => 1)), 'Test "small" BLOB set_field (full contents output disabled)');

        // This is the failure from MDL-24863. This was giving an error on MSSQL,
        // which converts the '1' to an integer, which cannot then be compared with
        // onetext cast to a varchar. This should be fixed and working now.
        $newchar = 'frog';
        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $params = array('onetext' => '1');
        try {
            $DB->set_field_select($tablename, 'onechar', $newchar, $DB->sql_compare_text('onetext') . ' = ?', $params);
            $this->assertTrue(true, 'No exceptions thrown with numerical text param comparison for text field.');
        } catch (dml_exception $e) {
            $this->assertFalse(true, 'We have an unexpected exception.');
            throw $e;
        }
    }

    public function test_count_records() {
        $DB = $this->tdb;

        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertSame(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertSame(3, $DB->count_records($tablename));

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext' => '1');
        try {
            $DB->count_records($tablename, $conditions);
            if (debugging()) {
                // Only in debug mode - hopefully all devs test code in debug mode...
                $this->fail('An Exception is missing, expected due to equating of text fields');
            }
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }
    }

    public function test_count_records_select() {
        $DB = $this->tdb;

        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertSame(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertSame(2, $DB->count_records_select($tablename, 'course > ?', array(3)));
    }

    public function test_count_records_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertSame(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3, 'onechar' => 'a'));
        $DB->insert_record($tablename, array('course' => 4, 'onechar' => 'b'));
        $DB->insert_record($tablename, array('course' => 5, 'onechar' => 'c'));

        $this->assertSame(2, $DB->count_records_sql("SELECT COUNT(*) FROM {{$tablename}} WHERE course > ?", array(3)));

        // Test invalid use.
        try {
            $DB->count_records_sql("SELECT onechar FROM {{$tablename}} WHERE course = ?", array(3));
            $this->fail('Exception expected when non-number field used in count_records_sql');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $DB->count_records_sql("SELECT course FROM {{$tablename}} WHERE 1 = 2");
            $this->fail('Exception expected when non-number field used in count_records_sql');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_record_exists() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEquals(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists($tablename, array('course' => 3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists($tablename, array('course' => 3)));

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext' => '1');
        try {
            $DB->record_exists($tablename, $conditions);
            if (debugging()) {
                // Only in debug mode - hopefully all devs test code in debug mode...
                $this->fail('An Exception is missing, expected due to equating of text fields');
            }
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }
    }

    public function test_record_exists_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEquals(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists_select($tablename, "course = ?", array(3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists_select($tablename, "course = ?", array(3)));
    }

    public function test_record_exists_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEquals(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists_sql("SELECT * FROM {{$tablename}} WHERE course = ?", array(3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists_sql("SELECT * FROM {{$tablename}} WHERE course = ?", array(3)));
    }

    public function test_recordset_locks_delete() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        // Setup.
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 6));

        // Test against db write locking while on an open recordset.
        $rs = $DB->get_recordset($tablename, array(), null, 'course', 2, 2); // Get courses = {3,4}.
        foreach ($rs as $record) {
            $cid = $record->course;
            $DB->delete_records($tablename, array('course' => $cid));
            $this->assertFalse($DB->record_exists($tablename, array('course' => $cid)));
        }
        $rs->close();

        $this->assertEquals(4, $DB->count_records($tablename, array()));
    }

    public function test_recordset_locks_update() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        // Setup.
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 6));

        // Test against db write locking while on an open recordset.
        $rs = $DB->get_recordset($tablename, array(), null, 'course', 2, 2); // Get courses = {3,4}.
        foreach ($rs as $record) {
            $cid = $record->course;
            $DB->set_field($tablename, 'course', 10, array('course' => $cid));
            $this->assertFalse($DB->record_exists($tablename, array('course' => $cid)));
        }
        $rs->close();

        $this->assertEquals(2, $DB->count_records($tablename, array('course' => 10)));
    }

    public function test_delete_records() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 2));

        // Delete all records.
        $this->assertTrue($DB->delete_records($tablename));
        $this->assertEquals(0, $DB->count_records($tablename));

        // Delete subset of records.
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($DB->delete_records($tablename, array('course' => 2)));
        $this->assertEquals(1, $DB->count_records($tablename));

        // Delete all.
        $this->assertTrue($DB->delete_records($tablename, array()));
        $this->assertEquals(0, $DB->count_records($tablename));

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext'=>'1');
        try {
            $DB->delete_records($tablename, $conditions);
            if (debugging()) {
                // Only in debug mode - hopefully all devs test code in debug mode...
                $this->fail('An Exception is missing, expected due to equating of text fields');
            }
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }

        // Test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int).
        $conditions = array('onetext' => 1);
        try {
            $DB->delete_records($tablename, $conditions);
            if (debugging()) {
                // Only in debug mode - hopefully all devs test code in debug mode...
                $this->fail('An Exception is missing, expected due to equating of text fields');
            }
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_exception', $e);
            $this->assertSame('textconditionsnotallowed', $e->errorcode);
        }
    }

    public function test_delete_records_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($DB->delete_records_select($tablename, 'course = ?', array(2)));
        $this->assertEquals(1, $DB->count_records($tablename));
    }

    public function test_delete_records_list() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->delete_records_list($tablename, 'course', array(2, 3)));
        $this->assertEquals(1, $DB->count_records($tablename));

        $this->assertTrue($DB->delete_records_list($tablename, 'course', array())); // Must delete 0 rows without conditions. MDL-17645.
        $this->assertEquals(1, $DB->count_records($tablename));
    }

    public function test_object_params() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $o = new stdClass(); // Objects without __toString - never worked.
        try {
            $DB->fix_sql_params("SELECT {{$tablename}} WHERE course = ? ", array($o));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        // Objects with __toString() forbidden everywhere since 2.3.
        $o = new dml_test_object_one();
        try {
            $DB->fix_sql_params("SELECT {{$tablename}} WHERE course = ? ", array($o));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $DB->execute("SELECT {{$tablename}} WHERE course = ? ", array($o));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $DB->get_recordset_sql("SELECT {{$tablename}} WHERE course = ? ", array($o));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $DB->get_records_sql("SELECT {{$tablename}} WHERE course = ? ", array($o));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $record = new stdClass();
            $record->course = $o;
            $DB->insert_record_raw($tablename, $record);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $record = new stdClass();
            $record->course = $o;
            $DB->insert_record($tablename, $record);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $record = new stdClass();
            $record->course = $o;
            $DB->import_record($tablename, $record);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $record = new stdClass();
            $record->id = 1;
            $record->course = $o;
            $DB->update_record_raw($tablename, $record);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $record = new stdClass();
            $record->id = 1;
            $record->course = $o;
            $DB->update_record($tablename, $record);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $DB->set_field_select($tablename, 'course', 1, "course = ? ", array($o));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $DB->delete_records_select($tablename, "course = ? ", array($o));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_sql_null_from_clause() {
        $DB = $this->tdb;
        $sql = "SELECT 1 AS id ".$DB->sql_null_from_clause();
        $this->assertEquals(1, $DB->get_field_sql($sql));
    }

    public function test_sql_bitand() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('col1', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('col2', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('col1' => 3, 'col2' => 10));

        $sql = "SELECT ".$DB->sql_bitand(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEquals(2, $DB->get_field_sql($sql));

        $sql = "SELECT id, ".$DB->sql_bitand('col1', 'col2')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql);
        $this->assertCount(1, $result);
        $this->assertEquals(2, reset($result)->res);

        $sql = "SELECT id, ".$DB->sql_bitand('col1', '?')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql, array(10));
        $this->assertCount(1, $result);
        $this->assertEquals(2, reset($result)->res);
    }

    public function test_sql_bitnot() {
        $DB = $this->tdb;

        $not = $DB->sql_bitnot(2);
        $notlimited = $DB->sql_bitand($not, 7); // Might be positive or negative number which can not fit into PHP INT!

        $sql = "SELECT $notlimited AS res ".$DB->sql_null_from_clause();
        $this->assertEquals(5, $DB->get_field_sql($sql));
    }

    public function test_sql_bitor() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('col1', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('col2', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('col1' => 3, 'col2' => 10));

        $sql = "SELECT ".$DB->sql_bitor(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEquals(11, $DB->get_field_sql($sql));

        $sql = "SELECT id, ".$DB->sql_bitor('col1', 'col2')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql);
        $this->assertCount(1, $result);
        $this->assertEquals(11, reset($result)->res);

        $sql = "SELECT id, ".$DB->sql_bitor('col1', '?')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql, array(10));
        $this->assertCount(1, $result);
        $this->assertEquals(11, reset($result)->res);
    }

    public function test_sql_bitxor() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('col1', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('col2', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('col1' => 3, 'col2' => 10));

        $sql = "SELECT ".$DB->sql_bitxor(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEquals(9, $DB->get_field_sql($sql));

        $sql = "SELECT id, ".$DB->sql_bitxor('col1', 'col2')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql);
        $this->assertCount(1, $result);
        $this->assertEquals(9, reset($result)->res);

        $sql = "SELECT id, ".$DB->sql_bitxor('col1', '?')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql, array(10));
        $this->assertCount(1, $result);
        $this->assertEquals(9, reset($result)->res);
    }

    public function test_sql_modulo() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_modulo(10, 7)." AS res ".$DB->sql_null_from_clause();
        $this->assertEquals(3, $DB->get_field_sql($sql));
    }

    public function test_sql_ceil() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_ceil(665.666)." AS res ".$DB->sql_null_from_clause();
        $this->assertEquals(666, $DB->get_field_sql($sql));
    }

    public function test_cast_char2int() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table1 = $this->get_test_table("1");
        $tablename1 = $table1->getName();

        $table1->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table1->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table1->add_field('nametext', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table1->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table1);

        $DB->insert_record($tablename1, array('name'=>'0100', 'nametext'=>'0200'));
        $DB->insert_record($tablename1, array('name'=>'10',   'nametext'=>'20'));

        $table2 = $this->get_test_table("2");
        $tablename2 = $table2->getName();
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('res', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table2->add_field('restext', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);

        $DB->insert_record($tablename2, array('res'=>100, 'restext'=>200));

        // Casting varchar field.
        $sql = "SELECT *
                  FROM {".$tablename1."} t1
                  JOIN {".$tablename2."} t2 ON ".$DB->sql_cast_char2int("t1.name")." = t2.res ";
        $records = $DB->get_records_sql($sql);
        $this->assertCount(1, $records);
        // Also test them in order clauses.
        $sql = "SELECT * FROM {{$tablename1}} ORDER BY ".$DB->sql_cast_char2int('name');
        $records = $DB->get_records_sql($sql);
        $this->assertCount(2, $records);
        $this->assertSame('10', reset($records)->name);
        $this->assertSame('0100', next($records)->name);

        // Casting text field.
        $sql = "SELECT *
                  FROM {".$tablename1."} t1
                  JOIN {".$tablename2."} t2 ON ".$DB->sql_cast_char2int("t1.nametext", true)." = t2.restext ";
        $records = $DB->get_records_sql($sql);
        $this->assertCount(1, $records);
        // Also test them in order clauses.
        $sql = "SELECT * FROM {{$tablename1}} ORDER BY ".$DB->sql_cast_char2int('nametext', true);
        $records = $DB->get_records_sql($sql);
        $this->assertCount(2, $records);
        $this->assertSame('20', reset($records)->nametext);
        $this->assertSame('0200', next($records)->nametext);
    }

    public function test_cast_char2real() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('nametext', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('res', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'10.10', 'nametext'=>'10.10', 'res'=>5.1));
        $DB->insert_record($tablename, array('name'=>'91.10', 'nametext'=>'91.10', 'res'=>666));
        $DB->insert_record($tablename, array('name'=>'011.13333333', 'nametext'=>'011.13333333', 'res'=>10.1));

        // Casting varchar field.
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_cast_char2real('name')." > res";
        $records = $DB->get_records_sql($sql);
        $this->assertCount(2, $records);
        // Also test them in order clauses.
        $sql = "SELECT * FROM {{$tablename}} ORDER BY ".$DB->sql_cast_char2real('name');
        $records = $DB->get_records_sql($sql);
        $this->assertCount(3, $records);
        $this->assertSame('10.10', reset($records)->name);
        $this->assertSame('011.13333333', next($records)->name);
        $this->assertSame('91.10', next($records)->name);
        // And verify we can operate with them without too much problem with at least 6 decimals scale accuracy.
        $sql = "SELECT AVG(" . $DB->sql_cast_char2real('name') . ") FROM {{$tablename}}";
        $this->assertEquals(37.44444443333333, (float)$DB->get_field_sql($sql), '', 1.0E-6);

        // Casting text field.
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_cast_char2real('nametext', true)." > res";
        $records = $DB->get_records_sql($sql);
        $this->assertCount(2, $records);
        // Also test them in order clauses.
        $sql = "SELECT * FROM {{$tablename}} ORDER BY ".$DB->sql_cast_char2real('nametext', true);
        $records = $DB->get_records_sql($sql);
        $this->assertCount(3, $records);
        $this->assertSame('10.10', reset($records)->nametext);
        $this->assertSame('011.13333333', next($records)->nametext);
        $this->assertSame('91.10', next($records)->nametext);
        // And verify we can operate with them without too much problem with at least 6 decimals scale accuracy.
        $sql = "SELECT AVG(" . $DB->sql_cast_char2real('nametext', true) . ") FROM {{$tablename}}";
        $this->assertEquals(37.44444443333333, (float)$DB->get_field_sql($sql), '', 1.0E-6);

        // Check it works with values passed as param.
        $sql = "SELECT name FROM {{$tablename}} WHERE FLOOR(res - " . $DB->sql_cast_char2real(':param') . ") = 0";
        $this->assertEquals('011.13333333', $DB->get_field_sql($sql, array('param' => '10.09999')));

        // And also, although not recommended, with directly passed values.
        $sql = "SELECT name FROM {{$tablename}} WHERE FLOOR(res - " . $DB->sql_cast_char2real('10.09999') . ") = 0";
        $this->assertEquals('011.13333333', $DB->get_field_sql($sql));
    }

    public function test_sql_compare_text() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'abcd',   'description'=>'abcd'));
        $DB->insert_record($tablename, array('name'=>'abcdef', 'description'=>'bbcdef'));
        $DB->insert_record($tablename, array('name'=>'aaaa', 'description'=>'aaaacccccccccccccccccc'));
        $DB->insert_record($tablename, array('name'=>'xxxx',   'description'=>'123456789a123456789b123456789c123456789d'));

        // Only some supported databases truncate TEXT fields for comparisons, currently MSSQL and Oracle.
        $dbtruncatestextfields = ($DB->get_dbfamily() == 'mssql' || $DB->get_dbfamily() == 'oracle');

        if ($dbtruncatestextfields) {
            // Ensure truncation behaves as expected.

            $sql = "SELECT " . $DB->sql_compare_text('description') . " AS field FROM {{$tablename}} WHERE name = ?";
            $description = $DB->get_field_sql($sql, array('xxxx'));

            // Should truncate to 32 chars (the default).
            $this->assertEquals('123456789a123456789b123456789c12', $description);

            $sql = "SELECT " . $DB->sql_compare_text('description', 35) . " AS field FROM {{$tablename}} WHERE name = ?";
            $description = $DB->get_field_sql($sql, array('xxxx'));

            // Should truncate to the specified number of chars.
            $this->assertEquals('123456789a123456789b123456789c12345', $description);
        }

        // Ensure text field comparison is successful.
        $sql = "SELECT * FROM {{$tablename}} WHERE name = ".$DB->sql_compare_text('description');
        $records = $DB->get_records_sql($sql);
        $this->assertCount(1, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE name = ".$DB->sql_compare_text('description', 4);
        $records = $DB->get_records_sql($sql);
        if ($dbtruncatestextfields) {
            // Should truncate description to 4 characters before comparing.
            $this->assertCount(2, $records);
        } else {
            // Should leave untruncated, so one less match.
            $this->assertCount(1, $records);
        }

        // Now test the function with really big content and params.
        $clob = file_get_contents(__DIR__ . '/fixtures/clob.txt');
        $DB->insert_record($tablename, array('name' => 'zzzz', 'description' => $clob));
        $sql = "SELECT * FROM {{$tablename}}
                 WHERE " . $DB->sql_compare_text('description') . " = " . $DB->sql_compare_text(':clob');
        $records = $DB->get_records_sql($sql, array('clob' => $clob));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame($clob, $record->description);
    }

    public function test_unique_index_collation_trouble() {
        // Note: this is a work in progress, we should probably move this to ddl test.

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('name', XMLDB_INDEX_UNIQUE, array('name'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'aaa'));

        try {
            $DB->insert_record($tablename, array('name'=>'AAA'));
        } catch (moodle_exception $e) {
            // TODO: ignore case insensitive uniqueness problems for now.
            // $this->fail("Unique index is case sensitive - this may cause problems in some tables");
        }

        try {
            $DB->insert_record($tablename, array('name'=>'aäa'));
            $DB->insert_record($tablename, array('name'=>'aáa'));
            $this->assertTrue(true);
        } catch (moodle_exception $e) {
            $family = $DB->get_dbfamily();
            if ($family === 'mysql' or $family === 'mssql') {
                $this->fail("Unique index is accent insensitive, this may cause problems for non-ascii languages. This is usually caused by accent insensitive default collation.");
            } else {
                // This should not happen, PostgreSQL and Oracle do not support accent insensitive uniqueness.
                $this->fail("Unique index is accent insensitive, this may cause problems for non-ascii languages.");
            }
            throw($e);
        }
    }

    public function test_sql_equal() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('name2', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name' => 'one', 'name2' => 'one'));
        $DB->insert_record($tablename, array('name' => 'ONE', 'name2' => 'ONE'));
        $DB->insert_record($tablename, array('name' => 'two', 'name2' => 'TWO'));
        $DB->insert_record($tablename, array('name' => 'öne', 'name2' => 'one'));
        $DB->insert_record($tablename, array('name' => 'öne', 'name2' => 'ÖNE'));

        // Case sensitive and accent sensitive (equal and not equal).
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', '?', true, true, false);
        $records = $DB->get_records_sql($sql, array('one'));
        $this->assertCount(1, $records);
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', ':name', true, true, true);
        $records = $DB->get_records_sql($sql, array('name' => 'one'));
        $this->assertCount(4, $records);
        // And with column comparison instead of params.
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', 'name2', true, true, false);
        $records = $DB->get_records_sql($sql);
        $this->assertCount(2, $records);

        // Case insensitive and accent sensitive (equal and not equal).
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', '?', false, true, false);
        $records = $DB->get_records_sql($sql, array('one'));
        $this->assertCount(2, $records);
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', ':name', false, true, true);
        $records = $DB->get_records_sql($sql, array('name' => 'one'));
        $this->assertCount(3, $records);
        // And with column comparison instead of params.
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', 'name2', false, true, false);
        $records = $DB->get_records_sql($sql);
        $this->assertCount(4, $records);

        // TODO: Accent insensitive is not cross-db, only some drivers support it, so just verify the queries work.
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', '?', true, false);
        $records = $DB->get_records_sql($sql, array('one'));
        $this->assertGreaterThanOrEqual(1, count($records)); // At very least, there is 1 record with CS/AI "one".
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', '?', false, false);
        $records = $DB->get_records_sql($sql, array('one'));
        $this->assertGreaterThanOrEqual(2, count($records)); // At very least, there are 2 records with CI/AI "one".
        // And with column comparison instead of params.
        $sql = "SELECT * FROM {{$tablename}} WHERE " . $DB->sql_equal('name', 'name2', false, false);
        $records = $DB->get_records_sql($sql);
        $this->assertGreaterThanOrEqual(4, count($records)); // At very least, there are 4 records with CI/AI names matching.
    }

    public function test_sql_like() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'SuperDuperRecord'));
        $DB->insert_record($tablename, array('name'=>'Nodupor'));
        $DB->insert_record($tablename, array('name'=>'ouch'));
        $DB->insert_record($tablename, array('name'=>'ouc_'));
        $DB->insert_record($tablename, array('name'=>'ouc%'));
        $DB->insert_record($tablename, array('name'=>'aui'));
        $DB->insert_record($tablename, array('name'=>'aüi'));
        $DB->insert_record($tablename, array('name'=>'aÜi'));

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', false);
        $records = $DB->get_records_sql($sql, array("%dup_r%"));
        $this->assertCount(2, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true);
        $records = $DB->get_records_sql($sql, array("%dup%"));
        $this->assertCount(1, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?'); // Defaults.
        $records = $DB->get_records_sql($sql, array("%dup%"));
        $this->assertCount(1, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true);
        $records = $DB->get_records_sql($sql, array("ouc\\_"));
        $this->assertCount(1, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true, false, '|');
        $records = $DB->get_records_sql($sql, array($DB->sql_like_escape("ouc%", '|')));
        $this->assertCount(1, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true);
        $records = $DB->get_records_sql($sql, array('aui'));
        $this->assertCount(1, $records);

        // Test LIKE under unusual collations.
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', false, false);
        $records = $DB->get_records_sql($sql, array("%dup_r%"));
        $this->assertCount(2, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true, true); // NOT LIKE.
        $records = $DB->get_records_sql($sql, array("%o%"));
        $this->assertCount(3, $records);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', false, true, true); // NOT ILIKE.
        $records = $DB->get_records_sql($sql, array("%D%"));
        $this->assertCount(6, $records);

        // Verify usual escaping characters work fine.
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true, false, '\\');
        $records = $DB->get_records_sql($sql, array("ouc\\_"));
        $this->assertCount(1, $records);
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true, false, '|');
        $records = $DB->get_records_sql($sql, array("ouc|%"));
        $this->assertCount(1, $records);

        // TODO: we do not require accent insensitivness yet, just make sure it does not throw errors.
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, false);
        $records = $DB->get_records_sql($sql, array('aui'));
        // $this->assertEquals(2, count($records), 'Accent insensitive LIKE searches may not be supported in all databases, this is not a problem.');
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', false, false);
        $records = $DB->get_records_sql($sql, array('aui'));
        // $this->assertEquals(3, count($records), 'Accent insensitive LIKE searches may not be supported in all databases, this is not a problem.');
    }

    public function test_coalesce() {
        $DB = $this->tdb;

        // Testing not-null occurrences, return 1st.
        $sql = "SELECT COALESCE('returnthis', 'orthis', 'orwhynotthis') AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array()));
        $sql = "SELECT COALESCE(:paramvalue, 'orthis', 'orwhynotthis') AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array('paramvalue' => 'returnthis')));

        // Testing null occurrences, return 2nd.
        $sql = "SELECT COALESCE(null, 'returnthis', 'orthis') AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array()));
        $sql = "SELECT COALESCE(:paramvalue, 'returnthis', 'orthis') AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array('paramvalue' => null)));
        $sql = "SELECT COALESCE(null, :paramvalue, 'orthis') AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array('paramvalue' => 'returnthis')));

        // Testing null occurrences, return 3rd.
        $sql = "SELECT COALESCE(null, null, 'returnthis') AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array()));
        $sql = "SELECT COALESCE(null, :paramvalue, 'returnthis') AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array('paramvalue' => null)));
        $sql = "SELECT COALESCE(null, null, :paramvalue) AS test" . $DB->sql_null_from_clause();
        $this->assertSame('returnthis', $DB->get_field_sql($sql, array('paramvalue' => 'returnthis')));

        // Testing all null occurrences, return null.
        // Note: under mssql, if all elements are nulls, at least one must be a "typed" null, hence
        // we cannot test this in a cross-db way easily, so next 2 tests are using
        // different queries depending of the DB family.
        $customnull = $DB->get_dbfamily() == 'mssql' ? 'CAST(null AS varchar)' : 'null';
        $sql = "SELECT COALESCE(null, null, " . $customnull . ") AS test" . $DB->sql_null_from_clause();
        $this->assertNull($DB->get_field_sql($sql, array()));
        $sql = "SELECT COALESCE(null, :paramvalue, " . $customnull . ") AS test" . $DB->sql_null_from_clause();
        $this->assertNull($DB->get_field_sql($sql, array('paramvalue' => null)));

        // Check there are not problems with whitespace strings.
        $sql = "SELECT COALESCE(null, :paramvalue, null) AS test" . $DB->sql_null_from_clause();
        $this->assertSame('', $DB->get_field_sql($sql, array('paramvalue' => '')));
    }

    public function test_sql_concat() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        // Testing all sort of values.
        $sql = "SELECT ".$DB->sql_concat("?", "?", "?")." AS fullname ". $DB->sql_null_from_clause();
        // String, some unicode chars.
        $params = array('name', 'áéíóú', 'name3');
        $this->assertSame('nameáéíóúname3', $DB->get_field_sql($sql, $params));
        // String, spaces and numbers.
        $params = array('name', '  ', 12345);
        $this->assertSame('name  12345', $DB->get_field_sql($sql, $params));
        // Float, empty and strings.
        $params = array(123.45, '', 'test');
        $this->assertSame('123.45test', $DB->get_field_sql($sql, $params));
        // Only integers.
        $params = array(12, 34, 56);
        $this->assertSame('123456', $DB->get_field_sql($sql, $params));
        // Float, null and strings.
        $params = array(123.45, null, 'test');
        $this->assertNull($DB->get_field_sql($sql, $params)); // Concatenate null with anything result = null.

        // Testing fieldnames + values and also integer fieldnames.
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('description'=>'áéíóú'));
        $DB->insert_record($tablename, array('description'=>'dxxx'));
        $DB->insert_record($tablename, array('description'=>'bcde'));

        // Fieldnames and values mixed.
        $sql = 'SELECT id, ' . $DB->sql_concat('description', "'harcoded'", '?', '?') . ' AS result FROM {' . $tablename . '}';
        $records = $DB->get_records_sql($sql, array(123.45, 'test'));
        $this->assertCount(3, $records);
        $this->assertSame('áéíóúharcoded123.45test', $records[1]->result);
        // Integer fieldnames and values.
        $sql = 'SELECT id, ' . $DB->sql_concat('id', "'harcoded'", '?', '?') . ' AS result FROM {' . $tablename . '}';
        $records = $DB->get_records_sql($sql, array(123.45, 'test'));
        $this->assertCount(3, $records);
        $this->assertSame('1harcoded123.45test', $records[1]->result);
        // All integer fieldnames.
        $sql = 'SELECT id, ' . $DB->sql_concat('id', 'id', 'id') . ' AS result FROM {' . $tablename . '}';
        $records = $DB->get_records_sql($sql, array());
        $this->assertCount(3, $records);
        $this->assertSame('111', $records[1]->result);

    }

    public function sql_concat_join_provider() {
        return array(
            // All strings.
            array(
                "' '",
                array("'name'", "'name2'", "'name3'"),
                array(),
                'name name2 name3',
            ),
            // All strings using placeholders
            array(
                "' '",
                array("?", "?", "?"),
                array('name', 'name2', 'name3'),
                'name name2 name3',
            ),
            // All integers.
            array(
                "' '",
                array(1, 2, 3),
                array(),
                '1 2 3',
            ),
            // All integers using placeholders
            array(
                "' '",
                array("?", "?", "?"),
                array(1, 2, 3),
                '1 2 3',
            ),
            // Mix of strings and integers.
            array(
                "' '",
                array(1, "'2'", 3),
                array(),
                '1 2 3',
            ),
            // Mix of strings and integers using placeholders.
            array(
                "' '",
                array(1, '2', 3),
                array(),
                '1 2 3',
            ),
        );
    }

    /**
     * @dataProvider sql_concat_join_provider
     * @param string $concat The string to use when concatanating.
     * @param array $fields The fields to concatanate
     * @param array $params Any parameters to provide to the query
     * @param @string $expected The expected result
     */
    public function test_concat_join($concat, $fields, $params, $expected) {
        $DB = $this->tdb;
        $sql = "SELECT " . $DB->sql_concat_join($concat, $fields) . " AS result" . $DB->sql_null_from_clause();
        $result = $DB->get_field_sql($sql, $params);
        $this->assertEquals($expected, $result);
    }

    public function test_sql_fullname() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_fullname(':first', ':last')." AS fullname ".$DB->sql_null_from_clause();
        $params = array('first'=>'Firstname', 'last'=>'Surname');
        $this->assertEquals("Firstname Surname", $DB->get_field_sql($sql, $params));
    }

    public function test_sql_order_by_text() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('description'=>'abcd'));
        $DB->insert_record($tablename, array('description'=>'dxxx'));
        $DB->insert_record($tablename, array('description'=>'bcde'));

        $sql = "SELECT * FROM {{$tablename}} ORDER BY ".$DB->sql_order_by_text('description');
        $records = $DB->get_records_sql($sql);
        $first = array_shift($records);
        $this->assertEquals(1, $first->id);
        $second = array_shift($records);
        $this->assertEquals(3, $second->id);
        $last = array_shift($records);
        $this->assertEquals(2, $last->id);
    }

    public function test_sql_substring() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $string = 'abcdefghij';

        $DB->insert_record($tablename, array('name'=>$string));

        $sql = "SELECT id, ".$DB->sql_substr("name", 5)." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql);
        $this->assertEquals(substr($string, 5-1), $record->name);

        $sql = "SELECT id, ".$DB->sql_substr("name", 5, 2)." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql);
        $this->assertEquals(substr($string, 5-1, 2), $record->name);

        try {
            // Silence php warning.
            @$DB->sql_substr("name");
            $this->fail("Expecting an exception, none occurred");
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        } catch (Error $error) {
            // PHP 7.1 throws Error even earlier.
            $this->assertRegExp('/Too few arguments to function/', $error->getMessage());
        }

        // Cover the function using placeholders in all positions.
        $start = 4;
        $length = 2;
        // 1st param (target).
        $sql = "SELECT id, ".$DB->sql_substr(":param1", $start)." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql, array('param1' => $string));
        $this->assertEquals(substr($string, $start - 1), $record->name); // PHP's substr is 0-based.
        // 2nd param (start).
        $sql = "SELECT id, ".$DB->sql_substr("name", ":param1")." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql, array('param1' => $start));
        $this->assertEquals(substr($string, $start - 1), $record->name); // PHP's substr is 0-based.
        // 3rd param (length).
        $sql = "SELECT id, ".$DB->sql_substr("name", $start, ":param1")." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql, array('param1' => $length));
        $this->assertEquals(substr($string, $start - 1,  $length), $record->name); // PHP's substr is 0-based.
        // All together.
        $sql = "SELECT id, ".$DB->sql_substr(":param1", ":param2", ":param3")." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql, array('param1' => $string, 'param2' => $start, 'param3' => $length));
        $this->assertEquals(substr($string, $start - 1,  $length), $record->name); // PHP's substr is 0-based.

        // Try also with some expression passed.
        $sql = "SELECT id, ".$DB->sql_substr("name", "(:param1 + 1) - 1")." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql, array('param1' => $start));
        $this->assertEquals(substr($string, $start - 1), $record->name); // PHP's substr is 0-based.
    }

    public function test_sql_length() {
        $DB = $this->tdb;
        $this->assertEquals($DB->get_field_sql(
            "SELECT ".$DB->sql_length("'aeiou'").$DB->sql_null_from_clause()), 5);
        $this->assertEquals($DB->get_field_sql(
            "SELECT ".$DB->sql_length("'áéíóú'").$DB->sql_null_from_clause()), 5);
    }

    public function test_sql_position() {
        $DB = $this->tdb;
        $this->assertEquals($DB->get_field_sql(
            "SELECT ".$DB->sql_position("'ood'", "'Moodle'").$DB->sql_null_from_clause()), 2);
        $this->assertEquals($DB->get_field_sql(
            "SELECT ".$DB->sql_position("'Oracle'", "'Moodle'").$DB->sql_null_from_clause()), 0);
    }

    public function test_sql_empty() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $this->assertSame('', $DB->sql_empty()); // Since 2.5 the hack is applied automatically to all bound params.
        $this->assertDebuggingCalled();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('namenotnull', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'default value');
        $table->add_field('namenotnullnodeflt', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'', 'namenotnull'=>''));
        $DB->insert_record($tablename, array('name'=>null));
        $DB->insert_record($tablename, array('name'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>0));

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE name = ?", array(''));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame('', $record->name);

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE namenotnull = ?", array(''));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame('', $record->namenotnull);

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE namenotnullnodeflt = ?", array(''));
        $this->assertCount(4, $records);
        $record = reset($records);
        $this->assertSame('', $record->namenotnullnodeflt);
    }

    public function test_sql_isempty() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('namenull', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $table->add_field('descriptionnull', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'',   'namenull'=>'',   'description'=>'',   'descriptionnull'=>''));
        $DB->insert_record($tablename, array('name'=>'??', 'namenull'=>null, 'description'=>'??', 'descriptionnull'=>null));
        $DB->insert_record($tablename, array('name'=>'la', 'namenull'=>'la', 'description'=>'la', 'descriptionnull'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>0,    'namenull'=>0,    'description'=>0,    'descriptionnull'=>0));

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isempty($tablename, 'name', false, false));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame('', $record->name);

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isempty($tablename, 'namenull', true, false));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame('', $record->namenull);

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isempty($tablename, 'description', false, true));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame('', $record->description);

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isempty($tablename, 'descriptionnull', true, true));
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->assertSame('', $record->descriptionnull);
    }

    public function test_sql_isnotempty() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('namenull', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $table->add_field('descriptionnull', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'',   'namenull'=>'',   'description'=>'',   'descriptionnull'=>''));
        $DB->insert_record($tablename, array('name'=>'??', 'namenull'=>null, 'description'=>'??', 'descriptionnull'=>null));
        $DB->insert_record($tablename, array('name'=>'la', 'namenull'=>'la', 'description'=>'la', 'descriptionnull'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>0,    'namenull'=>0,    'description'=>0,    'descriptionnull'=>0));

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isnotempty($tablename, 'name', false, false));
        $this->assertCount(3, $records);
        $record = reset($records);
        $this->assertSame('??', $record->name);

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isnotempty($tablename, 'namenull', true, false));
        $this->assertCount(2, $records); // Nulls aren't comparable (so they aren't "not empty"). SQL expected behaviour.
        $record = reset($records);
        $this->assertSame('la', $record->namenull); // So 'la' is the first non-empty 'namenull' record.

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isnotempty($tablename, 'description', false, true));
        $this->assertCount(3, $records);
        $record = reset($records);
        $this->assertSame('??', $record->description);

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isnotempty($tablename, 'descriptionnull', true, true));
        $this->assertCount(2, $records); // Nulls aren't comparable (so they aren't "not empty"). SQL expected behaviour.
        $record = reset($records);
        $this->assertSame('lalala', $record->descriptionnull); // So 'lalala' is the first non-empty 'descriptionnull' record.
    }

    public function test_sql_regex() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();
        if (!$DB->sql_regex_supported()) {
            $this->markTestSkipped($DB->get_name().' does not support regular expressions');
        }

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'LALALA'));
        $DB->insert_record($tablename, array('name'=>'holaaa'));
        $DB->insert_record($tablename, array('name'=>'aouch'));

        // Regex /a$/i (case-insensitive).
        $sql = "SELECT * FROM {{$tablename}} WHERE name ".$DB->sql_regex()." ?";
        $params = array('a$');
        $records = $DB->get_records_sql($sql, $params);
        $this->assertCount(2, $records);

        // Regex ! (not) /.a/i (case insensitive).
        $sql = "SELECT * FROM {{$tablename}} WHERE name ".$DB->sql_regex(false)." ?";
        $params = array('.a');
        $records = $DB->get_records_sql($sql, $params);
        $this->assertCount(1, $records);

        // Regex /a$/ (case-sensitive).
        $sql = "SELECT * FROM {{$tablename}} WHERE name ".$DB->sql_regex(true, true)." ?";
        $params = array('a$');
        $records = $DB->get_records_sql($sql, $params);
        $this->assertCount(1, $records);

        // Regex ! (not) /.a/ (case sensitive).
        $sql = "SELECT * FROM {{$tablename}} WHERE name ".$DB->sql_regex(false, true)." ?";
        $params = array('.a');
        $records = $DB->get_records_sql($sql, $params);
        $this->assertCount(2, $records);

    }

    /**
     * Test some complicated variations of set_field_select.
     */
    public function test_set_field_select_complicated() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3, 'content' => 'hello', 'name'=>'xyz'));
        $DB->insert_record($tablename, array('course' => 3, 'content' => 'world', 'name'=>'abc'));
        $DB->insert_record($tablename, array('course' => 5, 'content' => 'hello', 'name'=>'def'));
        $DB->insert_record($tablename, array('course' => 2, 'content' => 'universe', 'name'=>'abc'));
        // This SQL is a tricky case because we are selecting from the same table we are updating.
        $sql = 'id IN (SELECT outerq.id from (SELECT innerq.id from {' . $tablename . '} innerq WHERE course = 3) outerq)';
        $DB->set_field_select($tablename, 'name', 'ghi', $sql);

        $this->assertSame(2, $DB->count_records_select($tablename, 'name = ?', array('ghi')));

    }

    /**
     * Test some more complex SQL syntax which moodle uses and depends on to work
     * useful to determine if new database libraries can be supported.
     */
    public function test_get_records_sql_complicated() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3, 'content' => 'hello', 'name'=>'xyz'));
        $DB->insert_record($tablename, array('course' => 3, 'content' => 'world', 'name'=>'abc'));
        $DB->insert_record($tablename, array('course' => 5, 'content' => 'hello', 'name'=>'def'));
        $DB->insert_record($tablename, array('course' => 2, 'content' => 'universe', 'name'=>'abc'));

        // Test grouping by expressions in the query. MDL-26819. Note that there are 4 ways:
        // - By column position (GROUP by 1) - Not supported by mssql & oracle
        // - By column name (GROUP by course) - Supported by all, but leading to wrong results
        // - By column alias (GROUP by casecol) - Not supported by mssql & oracle
        // - By complete expression (GROUP BY CASE ...) - 100% cross-db, this test checks it
        $sql = "SELECT (CASE WHEN course = 3 THEN 1 ELSE 0 END) AS casecol,
                       COUNT(1) AS countrecs,
                       MAX(name) AS maxname
                  FROM {{$tablename}}
              GROUP BY CASE WHEN course = 3 THEN 1 ELSE 0 END
              ORDER BY casecol DESC";
        $result = array(
            1 => (object)array('casecol' => 1, 'countrecs' => 2, 'maxname' => 'xyz'),
            0 => (object)array('casecol' => 0, 'countrecs' => 2, 'maxname' => 'def'));
        $records = $DB->get_records_sql($sql, null);
        $this->assertEquals($result, $records);

        // Another grouping by CASE expression just to ensure it works ok for multiple WHEN.
        $sql = "SELECT CASE name
                            WHEN 'xyz' THEN 'last'
                            WHEN 'def' THEN 'mid'
                            WHEN 'abc' THEN 'first'
                       END AS casecol,
                       COUNT(1) AS countrecs,
                       MAX(name) AS maxname
                  FROM {{$tablename}}
              GROUP BY CASE name
                           WHEN 'xyz' THEN 'last'
                           WHEN 'def' THEN 'mid'
                           WHEN 'abc' THEN 'first'
                       END
              ORDER BY casecol DESC";
        $result = array(
            'mid'  => (object)array('casecol' => 'mid', 'countrecs' => 1, 'maxname' => 'def'),
            'last' => (object)array('casecol' => 'last', 'countrecs' => 1, 'maxname' => 'xyz'),
            'first'=> (object)array('casecol' => 'first', 'countrecs' => 2, 'maxname' => 'abc'));
        $records = $DB->get_records_sql($sql, null);
        $this->assertEquals($result, $records);

        // Test CASE expressions in the ORDER BY clause - used by MDL-34657.
        $sql = "SELECT id, course, name
                  FROM {{$tablename}}
              ORDER BY CASE WHEN (course = 5 OR name  = 'xyz') THEN 0 ELSE 1 END, name, course";
        // First, records matching the course = 5 OR name = 'xyz', then the rest. Each.
        // group ordered by name and course.
        $result = array(
            3 => (object)array('id' => 3, 'course' => 5, 'name' => 'def'),
            1 => (object)array('id' => 1, 'course' => 3, 'name' => 'xyz'),
            4 => (object)array('id' => 4, 'course' => 2, 'name' => 'abc'),
            2 => (object)array('id' => 2, 'course' => 3, 'name' => 'abc'));
        $records = $DB->get_records_sql($sql, null);
        $this->assertEquals($result, $records);
        // Verify also array keys, order is important in this test.
        $this->assertEquals(array_keys($result), array_keys($records));

        // Test limits in queries with DISTINCT/ALL clauses and multiple whitespace. MDL-25268.
        $sql = "SELECT   DISTINCT   course
                  FROM {{$tablename}}
                 ORDER BY course";
        // Only limitfrom.
        $records = $DB->get_records_sql($sql, null, 1);
        $this->assertCount(2, $records);
        $this->assertEquals(3, reset($records)->course);
        $this->assertEquals(5, next($records)->course);
        // Only limitnum.
        $records = $DB->get_records_sql($sql, null, 0, 2);
        $this->assertCount(2, $records);
        $this->assertEquals(2, reset($records)->course);
        $this->assertEquals(3, next($records)->course);
        // Both limitfrom and limitnum.
        $records = $DB->get_records_sql($sql, null, 2, 2);
        $this->assertCount(1, $records);
        $this->assertEquals(5, reset($records)->course);

        // We have sql like this in moodle, this syntax breaks on older versions of sqlite for example..
        $sql = "SELECT a.id AS id, a.course AS course
                  FROM {{$tablename}} a
                  JOIN (SELECT * FROM {{$tablename}}) b ON a.id = b.id
                 WHERE a.course = ?";

        $records = $DB->get_records_sql($sql, array(3));
        $this->assertCount(2, $records);
        $this->assertEquals(1, reset($records)->id);
        $this->assertEquals(2, next($records)->id);

        // Do NOT try embedding sql_xxxx() helper functions in conditions array of count_records(), they don't break params/binding!
        $count = $DB->count_records_select($tablename, "course = :course AND ".$DB->sql_compare_text('content')." = :content", array('course' => 3, 'content' => 'hello'));
        $this->assertEquals(1, $count);

        // Test int x string comparison.
        $sql = "SELECT *
                  FROM {{$tablename}} c
                 WHERE name = ?";
        $this->assertCount(0, $DB->get_records_sql($sql, array(10)));
        $this->assertCount(0, $DB->get_records_sql($sql, array("10")));
        $DB->insert_record($tablename, array('course' => 7, 'content' => 'xx', 'name'=>'1'));
        $DB->insert_record($tablename, array('course' => 7, 'content' => 'yy', 'name'=>'2'));
        $this->assertCount(1, $DB->get_records_sql($sql, array(1)));
        $this->assertCount(1, $DB->get_records_sql($sql, array("1")));
        $this->assertCount(0, $DB->get_records_sql($sql, array(10)));
        $this->assertCount(0, $DB->get_records_sql($sql, array("10")));
        $DB->insert_record($tablename, array('course' => 7, 'content' => 'xx', 'name'=>'1abc'));
        $this->assertCount(1, $DB->get_records_sql($sql, array(1)));
        $this->assertCount(1, $DB->get_records_sql($sql, array("1")));

        // Test get_in_or_equal() with a big number of elements. Note that ideally
        // we should be detecting and warning about any use over, say, 200 elements
        // And recommend to change code to use subqueries and/or chunks instead.
        $currentcount = $DB->count_records($tablename);
        $numelements = 10000; // Verify that we can handle 10000 elements (crazy!)
        $values = range(1, $numelements);

        list($insql, $inparams) = $DB->get_in_or_equal($values, SQL_PARAMS_QM); // With QM params.
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE id $insql";
        $results = $DB->get_records_sql($sql, $inparams);
        $this->assertCount($currentcount, $results);

        list($insql, $inparams) = $DB->get_in_or_equal($values, SQL_PARAMS_NAMED); // With NAMED params.
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE id $insql";
        $results = $DB->get_records_sql($sql, $inparams);
        $this->assertCount($currentcount, $results);
    }

    public function test_replace_all_text() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        if (!$DB->replace_all_text_supported()) {
            $this->markTestSkipped($DB->get_name().' does not support replacing of texts');
        }

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '20', null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'big', null, null);
        // Add a CHAR field named using a word reserved for all the supported DB servers.
        $table->add_field('where', XMLDB_TYPE_CHAR, '20', null, null, null, 'localhost');
        // Add a TEXT field named using a word reserved for all the supported DB servers.
        $table->add_field('from', XMLDB_TYPE_TEXT, 'big', null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $fromfield = $dbman->generator->getEncQuoted('from');
        $DB->execute("INSERT INTO {".$tablename."} (name,intro,$fromfield) VALUES (NULL,NULL,'localhost')");
        $DB->execute("INSERT INTO {".$tablename."} (name,intro,$fromfield) VALUES ('','','localhost')");
        $DB->execute("INSERT INTO {".$tablename."} (name,intro,$fromfield) VALUES ('xxyy','vvzz','localhost')");
        $DB->execute("INSERT INTO {".$tablename."} (name,intro,$fromfield) VALUES ('aa bb aa bb','cc dd cc aa','localhost')");
        $DB->execute("INSERT INTO {".$tablename."} (name,intro,$fromfield) VALUES ('kkllll','kkllll','localhost')");

        $expected = $DB->get_records($tablename, array(), 'id ASC');
        $idx = 1;
        $id1 = $id2 = $id3 = $id4 = $id5 = 0;
        foreach (array_keys($expected) as $identifier) {
            ${"id$idx"} = (string)$identifier;
            $idx++;
        }

        $columns = $DB->get_columns($tablename);

        // Replace should work even with columns named using a reserved word.
        $this->assertEquals('C', $columns['where']->meta_type);
        $this->assertEquals('localhost', $expected[$id1]->where);
        $this->assertEquals('localhost', $expected[$id2]->where);
        $this->assertEquals('localhost', $expected[$id3]->where);
        $this->assertEquals('localhost', $expected[$id4]->where);
        $this->assertEquals('localhost', $expected[$id5]->where);
        $DB->replace_all_text($tablename, $columns['where'], 'localhost', '::1');
        $result = $DB->get_records($tablename, array(), 'id ASC');
        $expected[$id1]->where = '::1';
        $expected[$id2]->where = '::1';
        $expected[$id3]->where = '::1';
        $expected[$id4]->where = '::1';
        $expected[$id5]->where = '::1';
        $this->assertEquals($expected, $result);
        $this->assertEquals('X', $columns['from']->meta_type);
        $DB->replace_all_text($tablename, $columns['from'], 'localhost', '127.0.0.1');
        $result = $DB->get_records($tablename, array(), 'id ASC');
        $expected[$id1]->from = '127.0.0.1';
        $expected[$id2]->from = '127.0.0.1';
        $expected[$id3]->from = '127.0.0.1';
        $expected[$id4]->from = '127.0.0.1';
        $expected[$id5]->from = '127.0.0.1';
        $this->assertEquals($expected, $result);

        $DB->replace_all_text($tablename, $columns['name'], 'aa', 'o');
        $result = $DB->get_records($tablename, array(), 'id ASC');
        $expected[$id4]->name = 'o bb o bb';
        $this->assertEquals($expected, $result);

        $DB->replace_all_text($tablename, $columns['intro'], 'aa', 'o');
        $result = $DB->get_records($tablename, array(), 'id ASC');
        $expected[$id4]->intro = 'cc dd cc o';
        $this->assertEquals($expected, $result);

        $DB->replace_all_text($tablename, $columns['name'], '_', '*');
        $DB->replace_all_text($tablename, $columns['name'], '?', '*');
        $DB->replace_all_text($tablename, $columns['name'], '%', '*');
        $DB->replace_all_text($tablename, $columns['intro'], '_', '*');
        $DB->replace_all_text($tablename, $columns['intro'], '?', '*');
        $DB->replace_all_text($tablename, $columns['intro'], '%', '*');
        $result = $DB->get_records($tablename, array(), 'id ASC');
        $this->assertEquals($expected, $result);

        $long = '1234567890123456789';
        $DB->replace_all_text($tablename, $columns['name'], 'kk', $long);
        $result = $DB->get_records($tablename, array(), 'id ASC');
        $expected[$id5]->name = core_text::substr($long.'llll', 0, 20);
        $this->assertEquals($expected, $result);

        $DB->replace_all_text($tablename, $columns['intro'], 'kk', $long);
        $result = $DB->get_records($tablename, array(), 'id ASC');
        $expected[$id5]->intro = $long.'llll';
        $this->assertEquals($expected, $result);
    }

    public function test_onelevel_commit() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, $data);
        $this->assertEquals(1, $DB->count_records($tablename));
        $transaction->allow_commit();
        $this->assertEquals(1, $DB->count_records($tablename));
    }

    public function test_transaction_ignore_error_trouble() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('course', XMLDB_INDEX_UNIQUE, array('course'));
        $dbman->create_table($table);

        // Test error on SQL_QUERY_INSERT.
        $transaction = $DB->start_delegated_transaction();
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, (object)array('course'=>1));
        $this->assertEquals(1, $DB->count_records($tablename));
        try {
            $DB->insert_record($tablename, (object)array('course'=>1));
        } catch (Exception $e) {
            // This must be ignored and it must not roll back the whole transaction.
        }
        $DB->insert_record($tablename, (object)array('course'=>2));
        $this->assertEquals(2, $DB->count_records($tablename));
        $transaction->allow_commit();
        $this->assertEquals(2, $DB->count_records($tablename));
        $this->assertFalse($DB->is_transaction_started());

        // Test error on SQL_QUERY_SELECT.
        $DB->delete_records($tablename);
        $transaction = $DB->start_delegated_transaction();
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, (object)array('course'=>1));
        $this->assertEquals(1, $DB->count_records($tablename));
        try {
            $DB->get_records_sql('s e l e c t');
        } catch (moodle_exception $e) {
            // This must be ignored and it must not roll back the whole transaction.
        }
        $DB->insert_record($tablename, (object)array('course'=>2));
        $this->assertEquals(2, $DB->count_records($tablename));
        $transaction->allow_commit();
        $this->assertEquals(2, $DB->count_records($tablename));
        $this->assertFalse($DB->is_transaction_started());

        // Test error on structure SQL_QUERY_UPDATE.
        $DB->delete_records($tablename);
        $transaction = $DB->start_delegated_transaction();
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, (object)array('course'=>1));
        $this->assertEquals(1, $DB->count_records($tablename));
        try {
            $DB->execute('xxxx');
        } catch (moodle_exception $e) {
            // This must be ignored and it must not roll back the whole transaction.
        }
        $DB->insert_record($tablename, (object)array('course'=>2));
        $this->assertEquals(2, $DB->count_records($tablename));
        $transaction->allow_commit();
        $this->assertEquals(2, $DB->count_records($tablename));
        $this->assertFalse($DB->is_transaction_started());

        // Test error on structure SQL_QUERY_STRUCTURE.
        $DB->delete_records($tablename);
        $transaction = $DB->start_delegated_transaction();
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, (object)array('course'=>1));
        $this->assertEquals(1, $DB->count_records($tablename));
        try {
            $DB->change_database_structure('xxxx');
        } catch (moodle_exception $e) {
            // This must be ignored and it must not roll back the whole transaction.
        }
        $DB->insert_record($tablename, (object)array('course'=>2));
        $this->assertEquals(2, $DB->count_records($tablename));
        $transaction->allow_commit();
        $this->assertEquals(2, $DB->count_records($tablename));
        $this->assertFalse($DB->is_transaction_started());

        // NOTE: SQL_QUERY_STRUCTURE is intentionally not tested here because it should never fail.
    }

    public function test_onelevel_rollback() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // This might in fact encourage ppl to migrate from myisam to innodb.

        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, $data);
        $this->assertEquals(1, $DB->count_records($tablename));
        try {
            $transaction->rollback(new Exception('test'));
            $this->fail('transaction rollback must rethrow exception');
        } catch (Exception $e) {
            // Ignored.
        }
        $this->assertEquals(0, $DB->count_records($tablename));
    }

    public function test_nested_transactions() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // Two level commit.
        $this->assertFalse($DB->is_transaction_started());
        $transaction1 = $DB->start_delegated_transaction();
        $this->assertTrue($DB->is_transaction_started());
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>4);
        $DB->insert_record($tablename, $data);
        $transaction2->allow_commit();
        $this->assertTrue($DB->is_transaction_started());
        $transaction1->allow_commit();
        $this->assertFalse($DB->is_transaction_started());
        $this->assertEquals(2, $DB->count_records($tablename));

        $DB->delete_records($tablename);

        // Rollback from top level.
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>4);
        $DB->insert_record($tablename, $data);
        $transaction2->allow_commit();
        try {
            $transaction1->rollback(new Exception('test'));
            $this->fail('transaction rollback must rethrow exception');
        } catch (Exception $e) {
            $this->assertEquals(get_class($e), 'Exception');
        }
        $this->assertEquals(0, $DB->count_records($tablename));

        $DB->delete_records($tablename);

        // Rollback from nested level.
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>4);
        $DB->insert_record($tablename, $data);
        try {
            $transaction2->rollback(new Exception('test'));
            $this->fail('transaction rollback must rethrow exception');
        } catch (Exception $e) {
            $this->assertEquals(get_class($e), 'Exception');
        }
        $this->assertEquals(2, $DB->count_records($tablename)); // Not rolled back yet.
        try {
            $transaction1->allow_commit();
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_transaction_exception', $e);
        }
        $this->assertEquals(2, $DB->count_records($tablename)); // Not rolled back yet.
        // The forced rollback is done from the default_exception handler and similar places,
        // let's do it manually here.
        $this->assertTrue($DB->is_transaction_started());
        $DB->force_transaction_rollback();
        $this->assertFalse($DB->is_transaction_started());
        $this->assertEquals(0, $DB->count_records($tablename)); // Finally rolled back.

        $DB->delete_records($tablename);

        // Test interactions of recordset and transactions - this causes problems in SQL Server.
        $table2 = $this->get_test_table('2');
        $tablename2 = $table2->getName();

        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);

        $DB->insert_record($tablename, array('course'=>1));
        $DB->insert_record($tablename, array('course'=>2));
        $DB->insert_record($tablename, array('course'=>3));

        $DB->insert_record($tablename2, array('course'=>5));
        $DB->insert_record($tablename2, array('course'=>6));
        $DB->insert_record($tablename2, array('course'=>7));
        $DB->insert_record($tablename2, array('course'=>8));

        $rs1 = $DB->get_recordset($tablename);
        $i = 0;
        foreach ($rs1 as $record1) {
            $i++;
            $rs2 = $DB->get_recordset($tablename2);
            $j = 0;
            foreach ($rs2 as $record2) {
                $t = $DB->start_delegated_transaction();
                $DB->set_field($tablename, 'course', $record1->course+1, array('id'=>$record1->id));
                $DB->set_field($tablename2, 'course', $record2->course+1, array('id'=>$record2->id));
                $t->allow_commit();
                $j++;
            }
            $rs2->close();
            $this->assertEquals(4, $j);
        }
        $rs1->close();
        $this->assertEquals(3, $i);

        // Test nested recordsets isolation without transaction.
        $DB->delete_records($tablename);
        $DB->insert_record($tablename, array('course'=>1));
        $DB->insert_record($tablename, array('course'=>2));
        $DB->insert_record($tablename, array('course'=>3));

        $DB->delete_records($tablename2);
        $DB->insert_record($tablename2, array('course'=>5));
        $DB->insert_record($tablename2, array('course'=>6));
        $DB->insert_record($tablename2, array('course'=>7));
        $DB->insert_record($tablename2, array('course'=>8));

        $rs1 = $DB->get_recordset($tablename);
        $i = 0;
        foreach ($rs1 as $record1) {
            $i++;
            $rs2 = $DB->get_recordset($tablename2);
            $j = 0;
            foreach ($rs2 as $record2) {
                $DB->set_field($tablename, 'course', $record1->course+1, array('id'=>$record1->id));
                $DB->set_field($tablename2, 'course', $record2->course+1, array('id'=>$record2->id));
                $j++;
            }
            $rs2->close();
            $this->assertEquals(4, $j);
        }
        $rs1->close();
        $this->assertEquals(3, $i);
    }

    public function test_transactions_forbidden() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->transactions_forbidden();
        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>1);
        $DB->insert_record($tablename, $data);
        try {
            $DB->transactions_forbidden();
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_transaction_exception', $e);
        }
        // The previous test does not force rollback.
        $transaction->allow_commit();
        $this->assertFalse($DB->is_transaction_started());
        $this->assertEquals(1, $DB->count_records($tablename));
    }

    public function test_wrong_transactions() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // Wrong order of nested commits.
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>4);
        $DB->insert_record($tablename, $data);
        try {
            $transaction1->allow_commit();
            $this->fail('wrong order of commits must throw exception');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_transaction_exception', $e);
        }
        try {
            $transaction2->allow_commit();
            $this->fail('first wrong commit forces rollback');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_transaction_exception', $e);
        }
        // This is done in default exception handler usually.
        $this->assertTrue($DB->is_transaction_started());
        $this->assertEquals(2, $DB->count_records($tablename)); // Not rolled back yet.
        $DB->force_transaction_rollback();
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->delete_records($tablename);

        // Wrong order of nested rollbacks.
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>4);
        $DB->insert_record($tablename, $data);
        try {
            // This first rollback should prevent all other rollbacks.
            $transaction1->rollback(new Exception('test'));
        } catch (Exception $e) {
            $this->assertEquals(get_class($e), 'Exception');
        }
        try {
            $transaction2->rollback(new Exception('test'));
        } catch (Exception $e) {
            $this->assertEquals(get_class($e), 'Exception');
        }
        try {
            $transaction1->rollback(new Exception('test'));
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_transaction_exception', $e);
        }
        // This is done in default exception handler usually.
        $this->assertTrue($DB->is_transaction_started());
        $DB->force_transaction_rollback();
        $DB->delete_records($tablename);

        // Unknown transaction object.
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = new moodle_transaction($DB);
        try {
            $transaction2->allow_commit();
            $this->fail('foreign transaction must fail');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_transaction_exception', $e);
        }
        try {
            $transaction1->allow_commit();
            $this->fail('first wrong commit forces rollback');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_transaction_exception', $e);
        }
        $DB->force_transaction_rollback();
        $DB->delete_records($tablename);
    }

    public function test_concurent_transactions() {
        // Notes about this test:
        // 1- MySQL needs to use one engine with transactions support (InnoDB).
        // 2- MSSQL needs to have enabled versioning for read committed
        //    transactions (ALTER DATABASE xxx SET READ_COMMITTED_SNAPSHOT ON)
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>1);
        $this->assertEquals(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, $data);
        $this->assertEquals(1, $DB->count_records($tablename));

        // Open second connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = array();
        }
        // If we have a readonly slave situation, we need to either observe
        // the latency, or if the latency is not specified we need to take
        // the slave out because the table may not have propagated yet.
        if (isset($cfg->dboptions['readonly'])) {
            if (isset($cfg->dboptions['readonly']['latency'])) {
                usleep(intval(1000000 * $cfg->dboptions['readonly']['latency']));
            } else {
                unset($cfg->dboptions['readonly']);
            }
        }
        $DB2 = moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $DB2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        // Second instance should not see pending inserts.
        $this->assertEquals(0, $DB2->count_records($tablename));
        $data = (object)array('course'=>2);
        $DB2->insert_record($tablename, $data);
        $this->assertEquals(1, $DB2->count_records($tablename));

        // First should see the changes done from second.
        $this->assertEquals(2, $DB->count_records($tablename));

        // Now commit and we should see it finally in second connections.
        $transaction->allow_commit();
        $this->assertEquals(2, $DB2->count_records($tablename));

        // Let's try delete all is also working on (this checks MDL-29198).
        // Initially both connections see all the records in the table (2).
        $this->assertEquals(2, $DB->count_records($tablename));
        $this->assertEquals(2, $DB2->count_records($tablename));
        $transaction = $DB->start_delegated_transaction();

        // Delete all from within transaction.
        $DB->delete_records($tablename);

        // Transactional $DB, sees 0 records now.
        $this->assertEquals(0, $DB->count_records($tablename));

        // Others ($DB2) get no changes yet.
        $this->assertEquals(2, $DB2->count_records($tablename));

        // Now commit and we should see changes.
        $transaction->allow_commit();
        $this->assertEquals(0, $DB2->count_records($tablename));

        $DB2->dispose();
    }

    public function test_session_locks() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        // Open second connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = array();
        }
        $DB2 = moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $DB2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        // Testing that acquiring a lock effectively locks.
        // Get a session lock on connection1.
        $rowid = rand(100, 200);
        $timeout = 1;
        $DB->get_session_lock($rowid, $timeout);

        // Try to get the same session lock on connection2.
        try {
            $DB2->get_session_lock($rowid, $timeout);
            $DB2->release_session_lock($rowid); // Should not be executed, but here for safety.
            $this->fail('An Exception is missing, expected due to session lock acquired.');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_sessionwait_exception', $e);
            $DB->release_session_lock($rowid); // Release lock on connection1.
        }

        // Testing that releasing a lock effectively frees.
        // Get a session lock on connection1.
        $rowid = rand(100, 200);
        $timeout = 1;
        $DB->get_session_lock($rowid, $timeout);
        // Release the lock on connection1.
        $DB->release_session_lock($rowid);

        // Get the just released lock on connection2.
        $DB2->get_session_lock($rowid, $timeout);
        // Release the lock on connection2.
        $DB2->release_session_lock($rowid);

        $DB2->dispose();
    }

    public function test_bound_param_types() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => '1', 'content'=>'xx')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 2, 'content'=>'yy')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'somestring', 'content'=>'zz')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'aa', 'content'=>'1')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'bb', 'content'=>2)));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'cc', 'content'=>'sometext')));

        // Conditions in CHAR columns.
        $this->assertTrue($DB->record_exists($tablename, array('name'=>1)));
        $this->assertTrue($DB->record_exists($tablename, array('name'=>'1')));
        $this->assertFalse($DB->record_exists($tablename, array('name'=>111)));
        $this->assertNotEmpty($DB->get_record($tablename, array('name'=>1)));
        $this->assertNotEmpty($DB->get_record($tablename, array('name'=>'1')));
        $this->assertEmpty($DB->get_record($tablename, array('name'=>111)));
        $sqlqm = "SELECT *
                    FROM {{$tablename}}
                   WHERE name = ?";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, array(1)));
        $this->assertCount(1, $records);
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, array('1')));
        $this->assertCount(1, $records);
        $records = $DB->get_records_sql($sqlqm, array(222));
        $this->assertCount(0, $records);
        $sqlnamed = "SELECT *
                       FROM {{$tablename}}
                      WHERE name = :name";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlnamed, array('name' => 2)));
        $this->assertCount(1, $records);
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlnamed, array('name' => '2')));
        $this->assertCount(1, $records);

        // Conditions in TEXT columns always must be performed with the sql_compare_text
        // helper function on both sides of the condition.
        $sqlqm = "SELECT *
                    FROM {{$tablename}}
                   WHERE " . $DB->sql_compare_text('content') . " =  " . $DB->sql_compare_text('?');
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, array('1')));
        $this->assertCount(1, $records);
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, array(1)));
        $this->assertCount(1, $records);
        $sqlnamed = "SELECT *
                       FROM {{$tablename}}
                      WHERE " . $DB->sql_compare_text('content') . " =  " . $DB->sql_compare_text(':content');
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlnamed, array('content' => 2)));
        $this->assertCount(1, $records);
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlnamed, array('content' => '2')));
        $this->assertCount(1, $records);
    }

    public function test_bound_param_reserved() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => '1'));

        // Make sure reserved words do not cause fatal problems in query parameters.

        $DB->execute("UPDATE {{$tablename}} SET course = 1 WHERE id = :select", array('select'=>1));
        $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE course = :select", array('select'=>1));
        $rs = $DB->get_recordset_sql("SELECT * FROM {{$tablename}} WHERE course = :select", array('select'=>1));
        $rs->close();
        $DB->get_fieldset_sql("SELECT id FROM {{$tablename}} WHERE course = :select", array('select'=>1));
        $DB->set_field_select($tablename, 'course', '1', "id = :select", array('select'=>1));
        $DB->delete_records_select($tablename, "id = :select", array('select'=>1));

        // If we get here test passed ok.
        $this->assertTrue(true);
    }

    public function test_limits_and_offsets() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'a', 'content'=>'one')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'b', 'content'=>'two')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'c', 'content'=>'three')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'd', 'content'=>'four')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'e', 'content'=>'five')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'f', 'content'=>'six')));

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 4));
        $this->assertCount(2, $records);
        $this->assertSame('e', reset($records)->name);
        $this->assertSame('f', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertEmpty($records = $DB->get_records_sql($sqlqm, null, 8));

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 0, 4));
        $this->assertCount(4, $records);
        $this->assertSame('a', reset($records)->name);
        $this->assertSame('d', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 0, 8));
        $this->assertCount(6, $records);
        $this->assertSame('a', reset($records)->name);
        $this->assertSame('f', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 1, 4));
        $this->assertCount(4, $records);
        $this->assertSame('b', reset($records)->name);
        $this->assertSame('e', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 4, 4));
        $this->assertCount(2, $records);
        $this->assertSame('e', reset($records)->name);
        $this->assertSame('f', end($records)->name);

        $sqlqm = "SELECT t.*, t.name AS test
                    FROM {{$tablename}} t
                    ORDER BY t.id ASC";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 4, 4));
        $this->assertCount(2, $records);
        $this->assertSame('e', reset($records)->name);
        $this->assertSame('f', end($records)->name);

        $sqlqm = "SELECT DISTINCT t.name, t.name AS test
                    FROM {{$tablename}} t
                    ORDER BY t.name DESC";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 4, 4));
        $this->assertCount(2, $records);
        $this->assertSame('b', reset($records)->name);
        $this->assertSame('a', end($records)->name);

        $sqlqm = "SELECT 1
                    FROM {{$tablename}} t
                    WHERE t.name = 'a'";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 0, 1));
        $this->assertCount(1, $records);

        $sqlqm = "SELECT 'constant'
                    FROM {{$tablename}} t
                    WHERE t.name = 'a'";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 0, 8));
        $this->assertCount(1, $records);

        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'a', 'content'=>'one')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'b', 'content'=>'two')));
        $this->assertNotEmpty($DB->insert_record($tablename, array('name' => 'c', 'content'=>'three')));

        $sqlqm = "SELECT t.name, COUNT(DISTINCT t2.id) AS count, 'Test' AS teststring
                    FROM {{$tablename}} t
                    LEFT JOIN (
                        SELECT t.id, t.name
                        FROM {{$tablename}} t
                    ) t2 ON t2.name = t.name
                    GROUP BY t.name
                    ORDER BY t.name ASC";
        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm));
        $this->assertCount(6, $records);         // a,b,c,d,e,f.
        $this->assertEquals(2, reset($records)->count);  // a has 2 records now.
        $this->assertEquals(1, end($records)->count);    // f has 1 record still.

        $this->assertNotEmpty($records = $DB->get_records_sql($sqlqm, null, 0, 2));
        $this->assertCount(2, $records);
        $this->assertEquals(2, reset($records)->count);
        $this->assertEquals(2, end($records)->count);
    }

    /**
     * Test debugging messages about invalid limit number values.
     */
    public function test_invalid_limits_debugging() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        // Setup test data.
        $table = $this->get_test_table();
        $tablename = $table->getName();
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $DB->insert_record($tablename, array('course' => '1'));

        // Verify that get_records_sql throws debug notices with invalid limit params.
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 'invalid');
        $this->assertDebuggingCalled("Non-numeric limitfrom parameter detected: 'invalid', did you pass the correct arguments?");

        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 1, 'invalid');
        $this->assertDebuggingCalled("Non-numeric limitnum parameter detected: 'invalid', did you pass the correct arguments?");

        // Verify that get_recordset_sql throws debug notices with invalid limit params.
        $rs = $DB->get_recordset_sql("SELECT * FROM {{$tablename}}", null, 'invalid');
        $this->assertDebuggingCalled("Non-numeric limitfrom parameter detected: 'invalid', did you pass the correct arguments?");
        $rs->close();

        $rs = $DB->get_recordset_sql("SELECT * FROM {{$tablename}}", null, 1, 'invalid');
        $this->assertDebuggingCalled("Non-numeric limitnum parameter detected: 'invalid', did you pass the correct arguments?");
        $rs->close();

        // Verify that some edge cases do no create debugging messages.
        // String form of integer values.
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, '1');
        $this->assertDebuggingNotCalled();
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 1, '2');
        $this->assertDebuggingNotCalled();
        // Empty strings.
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, '');
        $this->assertDebuggingNotCalled();
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 1, '');
        $this->assertDebuggingNotCalled();
        // Null values.
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, null);
        $this->assertDebuggingNotCalled();
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 1, null);
        $this->assertDebuggingNotCalled();

        // Verify that empty arrays DO create debugging mesages.
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, array());
        $this->assertDebuggingCalled("Non-numeric limitfrom parameter detected: array (\n), did you pass the correct arguments?");
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 1, array());
        $this->assertDebuggingCalled("Non-numeric limitnum parameter detected: array (\n), did you pass the correct arguments?");

        // Verify Negative number handling:
        // -1 is explicitly treated as 0 for historical reasons.
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, -1);
        $this->assertDebuggingNotCalled();
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 1, -1);
        $this->assertDebuggingNotCalled();
        // Any other negative values should throw debugging messages.
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, -2);
        $this->assertDebuggingCalled("Negative limitfrom parameter detected: -2, did you pass the correct arguments?");
        $DB->get_records_sql("SELECT * FROM {{$tablename}}", null, 1, -2);
        $this->assertDebuggingCalled("Negative limitnum parameter detected: -2, did you pass the correct arguments?");
    }

    public function test_queries_counter() {

        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        // Test database.
        $table = $this->get_test_table();
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('fieldvalue', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_table($table);
        $tablename = $table->getName();

        // Initial counters values.
        $initreads = $DB->perf_get_reads();
        $initwrites = $DB->perf_get_writes();
        $previousqueriestime = $DB->perf_get_queries_time();

        // Selects counts as reads.

        // The get_records_sql() method generates only 1 db query.
        $whatever = $DB->get_records_sql("SELECT * FROM {{$tablename}}");
        $this->assertEquals($initreads + 1, $DB->perf_get_reads());

        // The get_records() method generates 2 queries the first time is called
        // as it is fetching the table structure.
        $whatever = $DB->get_records($tablename, array('id' => '1'));
        $this->assertEquals($initreads + 3, $DB->perf_get_reads());
        $this->assertEquals($initwrites, $DB->perf_get_writes());

        // The elapsed time is counted.
        $lastqueriestime = $DB->perf_get_queries_time();
        $this->assertGreaterThanOrEqual($previousqueriestime, $lastqueriestime);
        $previousqueriestime = $lastqueriestime;

        // Only 1 now, it already fetched the table columns.
        $whatever = $DB->get_records($tablename);
        $this->assertEquals($initreads + 4, $DB->perf_get_reads());

        // And only 1 more from now.
        $whatever = $DB->get_records($tablename);
        $this->assertEquals($initreads + 5, $DB->perf_get_reads());

        // Inserts counts as writes.

        $rec1 = new stdClass();
        $rec1->fieldvalue = 11;
        $rec1->id = $DB->insert_record($tablename, $rec1);
        $this->assertEquals($initwrites + 1, $DB->perf_get_writes());
        $this->assertEquals($initreads + 5, $DB->perf_get_reads());

        // The elapsed time is counted.
        $lastqueriestime = $DB->perf_get_queries_time();
        $this->assertGreaterThanOrEqual($previousqueriestime, $lastqueriestime);
        $previousqueriestime = $lastqueriestime;

        $rec2 = new stdClass();
        $rec2->fieldvalue = 22;
        $rec2->id = $DB->insert_record($tablename, $rec2);
        $this->assertEquals($initwrites + 2, $DB->perf_get_writes());

        // Updates counts as writes.

        $rec1->fieldvalue = 111;
        $DB->update_record($tablename, $rec1);
        $this->assertEquals($initwrites + 3, $DB->perf_get_writes());
        $this->assertEquals($initreads + 5, $DB->perf_get_reads());

        // The elapsed time is counted.
        $lastqueriestime = $DB->perf_get_queries_time();
        $this->assertGreaterThanOrEqual($previousqueriestime, $lastqueriestime);
        $previousqueriestime = $lastqueriestime;

        // Sum of them.
        $totaldbqueries = $DB->perf_get_reads() + $DB->perf_get_writes();
        $this->assertEquals($totaldbqueries, $DB->perf_get_queries());
    }

    public function test_sql_intersect() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $tables = array();
        for ($i = 0; $i < 3; $i++) {
            $table = $this->get_test_table('i'.$i);
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('ival', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $dbman->create_table($table);
            $tables[$i] = $table;
        }
        $DB->insert_record($tables[0]->getName(), array('ival' => 1, 'name' => 'One'), false);
        $DB->insert_record($tables[0]->getName(), array('ival' => 2, 'name' => 'Two'), false);
        $DB->insert_record($tables[0]->getName(), array('ival' => 3, 'name' => 'Three'), false);
        $DB->insert_record($tables[0]->getName(), array('ival' => 4, 'name' => 'Four'), false);

        $DB->insert_record($tables[1]->getName(), array('ival' => 1, 'name' => 'One'), false);
        $DB->insert_record($tables[1]->getName(), array('ival' => 2, 'name' => 'Two'), false);
        $DB->insert_record($tables[1]->getName(), array('ival' => 3, 'name' => 'Three'), false);

        $DB->insert_record($tables[2]->getName(), array('ival' => 1, 'name' => 'One'), false);
        $DB->insert_record($tables[2]->getName(), array('ival' => 2, 'name' => 'Two'), false);
        $DB->insert_record($tables[2]->getName(), array('ival' => 5, 'name' => 'Five'), false);

        // Intersection on the int column.
        $params = array('excludename' => 'Two');
        $sql1 = 'SELECT ival FROM {'.$tables[0]->getName().'}';
        $sql2 = 'SELECT ival FROM {'.$tables[1]->getName().'} WHERE name <> :excludename';
        $sql3 = 'SELECT ival FROM {'.$tables[2]->getName().'}';

        $sql = $DB->sql_intersect(array($sql1), 'ival') . ' ORDER BY ival';
        $this->assertEquals(array(1, 2, 3, 4), $DB->get_fieldset_sql($sql, $params));

        $sql = $DB->sql_intersect(array($sql1, $sql2), 'ival') . ' ORDER BY ival';
        $this->assertEquals(array(1, 3), $DB->get_fieldset_sql($sql, $params));

        $sql = $DB->sql_intersect(array($sql1, $sql2, $sql3), 'ival') . ' ORDER BY ival';
        $this->assertEquals(array(1),
            $DB->get_fieldset_sql($sql, $params));

        // Intersection on the char column.
        $params = array('excludeival' => 2);
        $sql1 = 'SELECT name FROM {'.$tables[0]->getName().'}';
        $sql2 = 'SELECT name FROM {'.$tables[1]->getName().'} WHERE ival <> :excludeival';
        $sql3 = 'SELECT name FROM {'.$tables[2]->getName().'}';

        $sql = $DB->sql_intersect(array($sql1), 'name') . ' ORDER BY name';
        $this->assertEquals(array('Four', 'One', 'Three', 'Two'), $DB->get_fieldset_sql($sql, $params));

        $sql = $DB->sql_intersect(array($sql1, $sql2), 'name') . ' ORDER BY name';
        $this->assertEquals(array('One', 'Three'), $DB->get_fieldset_sql($sql, $params));

        $sql = $DB->sql_intersect(array($sql1, $sql2, $sql3), 'name') . ' ORDER BY name';
        $this->assertEquals(array('One'), $DB->get_fieldset_sql($sql, $params));

        // Intersection on the several columns.
        $params = array('excludename' => 'Two');
        $sql1 = 'SELECT ival, name FROM {'.$tables[0]->getName().'}';
        $sql2 = 'SELECT ival, name FROM {'.$tables[1]->getName().'} WHERE name <> :excludename';
        $sql3 = 'SELECT ival, name FROM {'.$tables[2]->getName().'}';

        $sql = $DB->sql_intersect(array($sql1), 'ival, name') . ' ORDER BY ival';
        $this->assertEquals(array(1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four'),
            $DB->get_records_sql_menu($sql, $params));

        $sql = $DB->sql_intersect(array($sql1, $sql2), 'ival, name') . ' ORDER BY ival';
        $this->assertEquals(array(1 => 'One', 3 => 'Three'),
            $DB->get_records_sql_menu($sql, $params));

        $sql = $DB->sql_intersect(array($sql1, $sql2, $sql3), 'ival, name') . ' ORDER BY ival';
        $this->assertEquals(array(1 => 'One'),
            $DB->get_records_sql_menu($sql, $params));

        // Drop temporary tables.
        foreach ($tables as $table) {
            $dbman->drop_table($table);
        }
    }

    /**
     * Test that the database has full utf8 support (4 bytes).
     */
    public function test_four_byte_character_insertion() {
        $DB = $this->tdb;

        if ($DB->get_dbfamily() === 'mysql' && strpos($DB->get_dbcollation(), 'utf8_') === 0) {
            $this->markTestSkipped($DB->get_name() .
                    ' does not support 4 byte characters with only a utf8 collation.
                    Please change to utf8mb4 for full utf8 support.');
        }

        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $data = array(
            'name' => 'Name with a four byte character 𠮟る',
            'content' => 'Content with a four byte emoji 📝 memo.'
        );

        $insertid = $DB->insert_record($tablename, $data);
        $result = $DB->get_record($tablename, array('id' => $insertid));
        $this->assertEquals($data['name'], $result->name);
        $this->assertEquals($data['content'], $result->content);

        $dbman->drop_table($table);
    }
}

/**
 * This class is not a proper subclass of moodle_database. It is
 * intended to be used only in unit tests, in order to gain access to the
 * protected methods of moodle_database, and unit test them.
 */
class moodle_database_for_testing extends moodle_database {
    protected $prefix = 'mdl_';

    public function public_fix_table_names($sql) {
        return $this->fix_table_names($sql);
    }

    public function driver_installed() {}
    public function get_dbfamily() {}
    protected function get_dbtype() {}
    protected function get_dblibrary() {}
    public function get_name() {}
    public function get_configuration_help() {}
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {}
    public function get_server_info() {}
    protected function allowed_param_types() {}
    public function get_last_error() {}
    public function get_tables($usecache=true) {}
    public function get_indexes($table) {}
    protected function fetch_columns(string $table): array {
        return [];
    }
    protected function normalise_value($column, $value) {}
    public function set_debug($state) {}
    public function get_debug() {}
    public function change_database_structure($sql, $tablenames = null) {}
    public function execute($sql, array $params=null) {}
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {}
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {}
    public function get_fieldset_sql($sql, array $params=null) {}
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false) {}
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {}
    public function import_record($table, $dataobject) {}
    public function update_record_raw($table, $params, $bulk=false) {}
    public function update_record($table, $dataobject, $bulk=false) {}
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null) {}
    public function delete_records_select($table, $select, array $params=null) {}
    public function sql_concat() {}
    public function sql_concat_join($separator="' '", $elements=array()) {}
    public function sql_substr($expr, $start, $length=false) {}
    public function begin_transaction() {}
    public function commit_transaction() {}
    public function rollback_transaction() {}
}


/**
 * Dumb test class with toString() returning 1.
 */
class dml_test_object_one {
    public function __toString() {
        return 1;
    }
}
