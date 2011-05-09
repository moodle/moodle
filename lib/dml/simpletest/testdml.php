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
 * @package    core
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class dml_test extends UnitTestCase {
    private $tables = array();
    /** @var moodle_database */
    private $tdb;
    private $data;
    public  static $includecoverage = array('lib/dml');
    public  static $excludecoverage = array('lib/dml/simpletest');

    protected $olddebug;
    protected $olddisplay;

    function setUp() {
        global $DB, $UNITTEST;

        if (isset($UNITTEST->func_test_db)) {
            $this->tdb = $UNITTEST->func_test_db;
        } else {
            $this->tdb = $DB;
        }
    }

    function tearDown() {
        $dbman = $this->tdb->get_manager();

        foreach ($this->tables as $tablename) {
            if ($dbman->table_exists($tablename)) {
                $table = new xmldb_table($tablename);
                $dbman->drop_table($table);
            }
        }
        $this->tables = array();
    }

    /**
     * Get a xmldb_table object for testing, deleting any existing table
     * of the same name, for example if one was left over from a previous test
     * run that crashed.
     *
     * @param database_manager $dbman the database_manager to use.
     * @param string $suffix table name suffix, use if you need more test tables
     * @return xmldb_table the table object.
     */
    private function get_test_table($suffix = '') {
        $dbman = $this->tdb->get_manager();

        $tablename = "unit_table";
        if ($suffix !== '') {
            $tablename .= $suffix;
        }

        $table = new xmldb_table($tablename);
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $table->setComment("This is a test'n drop table. You can drop it safely");
        $this->tables[$tablename] = $tablename;
        return new xmldb_table($tablename);
    }

    protected function enable_debugging() {
        global $CFG;

        $this->olddebug   = $CFG->debug;       // Save current debug settings
        $this->olddisplay = $CFG->debugdisplay;
        $CFG->debug = DEBUG_DEVELOPER;
        $CFG->debugdisplay = true;
        ob_start(); // hide debug warning

    }

    protected function get_debugging() {
        global $CFG;

        $debuginfo = ob_get_contents();
        ob_end_clean();
        $CFG->debug = $this->olddebug;         // Restore original debug settings
        $CFG->debugdisplay = $this->olddisplay;

        return $debuginfo;
    }

    // NOTE: please keep order of test methods here matching the order of moodle_database class methods

    function test_diagnose() {
        $DB = $this->tdb;
        $result = $DB->diagnose();
        $this->assertNull($result, 'Database self diagnostics failed %s');
    }

    function test_get_server_info() {
        $DB = $this->tdb;
        $result = $DB->get_server_info();
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('description', $result));
        $this->assertTrue(array_key_exists('version', $result));
    }

    public function test_get_in_or_equal() {
        $DB = $this->tdb;

        // SQL_PARAMS_QM - IN or =

        // Correct usage of multiple values
        $in_values = array('value1', 'value2', '3', 4, null, false, true);
        list($usql, $params) = $DB->get_in_or_equal($in_values);
        $this->assertEqual('IN ('.implode(',',array_fill(0, count($in_values), '?')).')', $usql);
        $this->assertEqual(count($in_values), count($params));
        foreach ($params as $key => $value) {
            $this->assertIdentical($in_values[$key], $value);
        }

        // Correct usage of single value (in an array)
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values);
        $this->assertEqual("= ?", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_values[0], $params[0]);

        // Correct usage of single value
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values);
        $this->assertEqual("= ?", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_value, $params[0]);

        // SQL_PARAMS_QM - NOT IN or <>

        // Correct usage of multiple values
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, null, false);
        $this->assertEqual("NOT IN (?,?,?,?)", $usql);
        $this->assertEqual(4, count($params));
        foreach ($params as $key => $value) {
            $this->assertEqual($in_values[$key], $value);
        }

        // Correct usage of single value (in array()
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, null, false);
        $this->assertEqual("<> ?", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_values[0], $params[0]);

        // Correct usage of single value
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_QM, null, false);
        $this->assertEqual("<> ?", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_value, $params[0]);

        // SQL_PARAMS_NAMED - IN or =

        // Correct usage of multiple values
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', true);
        $this->assertEqual(4, count($params));
        reset($in_values);
        $ps = array();
        foreach ($params as $key => $value) {
            $this->assertEqual(current($in_values), $value);
            next($in_values);
            $ps[] = ':'.$key;
        }
        $this->assertEqual("IN (".implode(',', $ps).")", $usql);

        // Correct usage of single values (in array)
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', true);
        $this->assertEqual(1, count($params));
        $value = reset($params);
        $key = key($params);
        $this->assertEqual("= :$key", $usql);
        $this->assertEqual($in_value, $value);

        // Correct usage of single value
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', true);
        $this->assertEqual(1, count($params));
        $value = reset($params);
        $key = key($params);
        $this->assertEqual("= :$key", $usql);
        $this->assertEqual($in_value, $value);

        // SQL_PARAMS_NAMED - NOT IN or <>

        // Correct usage of multiple values
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false);
        $this->assertEqual(4, count($params));
        reset($in_values);
        $ps = array();
        foreach ($params as $key => $value) {
            $this->assertEqual(current($in_values), $value);
            next($in_values);
            $ps[] = ':'.$key;
        }
        $this->assertEqual("NOT IN (".implode(',', $ps).")", $usql);

        // Correct usage of single values (in array)
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false);
        $this->assertEqual(1, count($params));
        $value = reset($params);
        $key = key($params);
        $this->assertEqual("<> :$key", $usql);
        $this->assertEqual($in_value, $value);

        // Correct usage of single value
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param', false);
        $this->assertEqual(1, count($params));
        $value = reset($params);
        $key = key($params);
        $this->assertEqual("<> :$key", $usql);
        $this->assertEqual($in_value, $value);

        // make sure the param names are unique
        list($usql1, $params1) = $DB->get_in_or_equal(array(1,2,3), SQL_PARAMS_NAMED, 'param');
        list($usql2, $params2) = $DB->get_in_or_equal(array(1,2,3), SQL_PARAMS_NAMED, 'param');
        $params1 = array_keys($params1);
        $params2 = array_keys($params2);
        $common = array_intersect($params1, $params2);
        $this->assertEqual(count($common), 0);
    }

    public function test_fix_table_names() {
        $DB = new moodle_database_for_testing();
        $prefix = $DB->get_prefix();

        // Simple placeholder
        $placeholder = "{user_123}";
        $this->assertIdentical($prefix."user_123", $DB->public_fix_table_names($placeholder));

        // wrong table name
        $placeholder = "{user-a}";
        $this->assertIdentical($placeholder, $DB->public_fix_table_names($placeholder));

        // wrong table name
        $placeholder = "{123user}";
        $this->assertIdentical($placeholder, $DB->public_fix_table_names($placeholder));

        // Full SQL
        $sql = "SELECT * FROM {user}, {funny_table_name}, {mdl_stupid_table} WHERE {user}.id = {funny_table_name}.userid";
        $expected = "SELECT * FROM {$prefix}user, {$prefix}funny_table_name, {$prefix}mdl_stupid_table WHERE {$prefix}user.id = {$prefix}funny_table_name.userid";
        $this->assertIdentical($expected, $DB->public_fix_table_names($sql));
    }

    function test_fix_sql_params() {
        $DB = $this->tdb;

        $table = $this->get_test_table();
        $tablename = $table->getName();

        // Correct table placeholder substitution
        $sql = "SELECT * FROM {{$tablename}}";
        $sqlarray = $DB->fix_sql_params($sql);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}".$tablename, $sqlarray[0]);

        // Conversions of all param types
        $sql = array();
        $sql[SQL_PARAMS_NAMED]  = "SELECT * FROM {$DB->get_prefix()}testtable WHERE name = :param1, course = :param2";
        $sql[SQL_PARAMS_QM]     = "SELECT * FROM {$DB->get_prefix()}testtable WHERE name = ?, course = ?";
        $sql[SQL_PARAMS_DOLLAR] = "SELECT * FROM {$DB->get_prefix()}testtable WHERE name = \$1, course = \$2";

        $params = array();
        $params[SQL_PARAMS_NAMED]  = array('param1'=>'first record', 'param2'=>1);
        $params[SQL_PARAMS_QM]     = array('first record', 1);
        $params[SQL_PARAMS_DOLLAR] = array('first record', 1);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_NAMED], $params[SQL_PARAMS_NAMED]);
        $this->assertIdentical($rsql, $sql[$rtype]);
        $this->assertIdentical($rparams, $params[$rtype]);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_QM], $params[SQL_PARAMS_QM]);
        $this->assertIdentical($rsql, $sql[$rtype]);
        $this->assertIdentical($rparams, $params[$rtype]);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_DOLLAR], $params[SQL_PARAMS_DOLLAR]);
        $this->assertIdentical($rsql, $sql[$rtype]);
        $this->assertIdentical($rparams, $params[$rtype]);


        // Malformed table placeholder
        $sql = "SELECT * FROM [testtable]";
        $sqlarray = $DB->fix_sql_params($sql);
        $this->assertIdentical($sql, $sqlarray[0]);


        // Mixed param types (colon and dollar)
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :param1, course = \$1";
        $params = array('param1' => 'record1', 'param2' => 3);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Mixed param types (question and dollar)
        $sql = "SELECT * FROM {{$tablename}} WHERE name = ?, course = \$1";
        $params = array('param1' => 'record2', 'param2' => 5);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Too few params in sql
        $sql = "SELECT * FROM {{$tablename}} WHERE name = ?, course = ?, id = ?";
        $params = array('record2', 3);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Too many params in array: no error, just use what is necessary
        $params[] = 1;
        $params[] = time();
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(is_array($sqlarray));
            $this->assertEqual(count($sqlarray[1]), 3);
        } catch (Exception $e) {
            $this->fail("Unexpected ".get_class($e)." exception");
        }

        // Named params missing from array
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :name, course = :course";
        $params = array('wrongname' => 'record1', 'course' => 1);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Duplicate named param in query - this is a very important feature!!
        // it helps with debugging of sloppy code
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :name, course = :name";
        $params = array('name' => 'record2', 'course' => 3);
        try {
            $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Extra named param is ignored
        $sql = "SELECT * FROM {{$tablename}} WHERE name = :name, course = :course";
        $params = array('name' => 'record1', 'course' => 1, 'extrastuff'=>'haha');
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(is_array($sqlarray));
            $this->assertEqual(count($sqlarray[1]), 2);
        } catch (Exception $e) {
            $this->fail("Unexpected ".get_class($e)." exception");
        }

        // Booleans in NAMED params are casting to 1/0 int
        $sql = "SELECT * FROM {{$tablename}} WHERE course = ? OR course = ?";
        $params = array(true, false);
        list($sql, $params) = $DB->fix_sql_params($sql, $params);
        $this->assertTrue(reset($params) === 1);
        $this->assertTrue(next($params) === 0);

        // Booleans in QM params are casting to 1/0 int
        $sql = "SELECT * FROM {{$tablename}} WHERE course = :course1 OR course = :course2";
        $params = array('course1' => true, 'course2' => false);
        list($sql, $params) = $DB->fix_sql_params($sql, $params);
        $this->assertTrue(reset($params) === 1);
        $this->assertTrue(next($params) === 0);

        // Booleans in DOLLAR params are casting to 1/0 int
        $sql = "SELECT * FROM {{$tablename}} WHERE course = \$1 OR course = \$2";
        $params = array(true, false);
        list($sql, $params) = $DB->fix_sql_params($sql, $params);
        $this->assertTrue(reset($params) === 1);
        $this->assertTrue(next($params) === 0);

        // No data types are touched except bool
        $sql = "SELECT * FROM {{$tablename}} WHERE name IN (?,?,?,?,?,?)";
        $inparams = array('abc', 'ABC', NULL, '1', 1, 1.4);
        list($sql, $params) = $DB->fix_sql_params($sql, $inparams);
        $this->assertIdentical(array_values($params), array_values($inparams));
    }

    public function test_get_tables() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        // Need to test with multiple DBs
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $original_count = count($DB->get_tables());

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
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

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('course-id', XMLDB_INDEX_UNIQUE, array('course', 'id'));
        $dbman->create_table($table);

        $indices = $DB->get_indexes($tablename);
        $this->assertTrue(is_array($indices));
        $this->assertEqual(count($indices), 2);
        // we do not care about index names for now
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
        $this->assertEqual(1, count($single['columns']));
        $this->assertEqual(2, count($composed['columns']));
        $this->assertEqual('course', $single['columns'][0]);
        $this->assertEqual('course', $composed['columns'][0]);
        $this->assertEqual('id', $composed['columns'][1]);
    }

    public function test_get_columns() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, 'lala');
        $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('enumfield', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'test2');
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $columns = $DB->get_columns($tablename);
        $this->assertTrue(is_array($columns));

        $fields = $table->getFields();
        $this->assertEqual(count($columns), count($fields));

        $field = $columns['id'];
        $this->assertEqual('R', $field->meta_type);
        $this->assertTrue($field->auto_increment);
        $this->assertTrue($field->unique);

        $field = $columns['course'];
        $this->assertEqual('I', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertTrue($field->has_default);
        $this->assertEqual(0, $field->default_value);
        $this->assertTrue($field->not_null);

        $field = $columns['name'];
        $this->assertEqual('C', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertTrue($field->has_default);
        $this->assertIdentical('lala', $field->default_value);
        $this->assertFalse($field->not_null);

        $field = $columns['description'];
        $this->assertEqual('X', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertFalse($field->has_default);
        $this->assertIdentical(null, $field->default_value);
        $this->assertFalse($field->not_null);

        $field = $columns['enumfield'];
        $this->assertEqual('C', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertIdentical('test2', $field->default_value);
        $this->assertTrue($field->not_null);

        $field = $columns['onenum'];
        $this->assertEqual('N', $field->meta_type);
        $this->assertFalse($field->auto_increment);
        $this->assertTrue($field->has_default);
        $this->assertEqual(200.0, $field->default_value);
        $this->assertFalse($field->not_null);

        for ($i = 0; $i < count($columns); $i++) {
            if ($i == 0) {
                $next_column = reset($columns);
                $next_field  = reset($fields);
            } else {
                $next_column = next($columns);
                $next_field  = next($fields);
            }

            $this->assertEqual($next_column->name, $next_field->name);
        }
    }

    public function test_get_manager() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $this->assertTrue($dbman instanceof database_manager);
    }

    public function test_setup_is_unicodedb() {
        $DB = $this->tdb;
        $this->assertTrue($DB->setup_is_unicodedb());
    }

    public function test_set_debug() { //tests get_debug() too
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
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
        $table1->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table1->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table1->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table1->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table1->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table1);

        $table2 = $this->get_test_table('2');
        $tablename2 = $table2->getName();
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);

        $DB->insert_record($tablename1, array('course' => 3, 'name' => 'aaa'));
        $DB->insert_record($tablename1, array('course' => 1, 'name' => 'bbb'));
        $DB->insert_record($tablename1, array('course' => 7, 'name' => 'ccc'));
        $DB->insert_record($tablename1, array('course' => 3, 'name' => 'ddd'));

        // select results are ignored
        $sql = "SELECT * FROM {{$tablename1}} WHERE course = :course";
        $this->assertTrue($DB->execute($sql, array('course'=>3)));

        // throw exception on error
        $sql = "XXUPDATE SET XSSD";
        try {
            $DB->execute($sql);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof dml_write_exception);
        }

        // update records
        $sql = "UPDATE {{$tablename1}}
                   SET course = 6
                 WHERE course = ?";
        $this->assertTrue($DB->execute($sql, array('3')));
        $this->assertEqual($DB->count_records($tablename1, array('course' => 6)), 2);

        // insert from one into second table
        $sql = "INSERT INTO {{$tablename2}} (course)

                SELECT course
                  FROM {{$tablename1}}";
        $this->assertTrue($DB->execute($sql));
        $this->assertEqual($DB->count_records($tablename2), 4);
    }

    public function test_get_recordset() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $data = array(array('id' => 1, 'course' => 3, 'name' => 'record1', 'onetext'=>'abc'),
                      array('id' => 2, 'course' => 3, 'name' => 'record2', 'onetext'=>'abcd'),
                      array('id' => 3, 'course' => 5, 'name' => 'record3', 'onetext'=>'abcde'));

        foreach ($data as $record) {
            $DB->insert_record($tablename, $record);
        }

        // standard recordset iteration
        $rs = $DB->get_recordset($tablename);
        $this->assertTrue($rs instanceof moodle_recordset);
        reset($data);
        foreach($rs as $record) {
            $data_record = current($data);
            foreach ($record as $k => $v) {
                $this->assertEqual($data_record[$k], $v);
            }
            next($data);
        }
        $rs->close();

        // iterator style usage
        $rs = $DB->get_recordset($tablename);
        $this->assertTrue($rs instanceof moodle_recordset);
        reset($data);
        while ($rs->valid()) {
            $record = $rs->current();
            $data_record = current($data);
            foreach ($record as $k => $v) {
                $this->assertEqual($data_record[$k], $v);
            }
            next($data);
            $rs->next();
        }
        $rs->close();

        // make sure rewind is ignored
        $rs = $DB->get_recordset($tablename);
        $this->assertTrue($rs instanceof moodle_recordset);
        reset($data);
        $i = 0;
        foreach($rs as $record) {
            $i++;
            $rs->rewind();
            if ($i > 10) {
                $this->fail('revind not ignored in recordsets');
                break;
            }
            $data_record = current($data);
            foreach ($record as $k => $v) {
                $this->assertEqual($data_record[$k], $v);
            }
            next($data);
        }
        $rs->close();

        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext' => '1');
        try {
            $rs = $DB->get_recordset($tablename, $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }

        // notes:
        //  * limits are tested in test_get_recordset_sql()
        //  * where_clause() is used internally and is tested in test_get_records()
    }

    public function test_get_recordset_iterator_keys() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $data = array(array('id'=> 1, 'course' => 3, 'name' => 'record1'),
                      array('id'=> 2, 'course' => 3, 'name' => 'record2'),
                      array('id'=> 3, 'course' => 5, 'name' => 'record3'));
        foreach ($data as $record) {
            $DB->insert_record($tablename, $record);
        }

        // Test repeated numeric keys are returned ok
        $rs = $DB->get_recordset($tablename, NULL, NULL, 'course, name, id');

        reset($data);
        $count = 0;
        foreach($rs as $key => $record) {
            $data_record = current($data);
            $this->assertEqual($data_record['course'], $key);
            next($data);
            $count++;
        }
        $rs->close();
        $this->assertEqual($count, 3);

        // Test string keys are returned ok
        $rs = $DB->get_recordset($tablename, NULL, NULL, 'name, course, id');

        reset($data);
        $count = 0;
        foreach($rs as $key => $record) {
            $data_record = current($data);
            $this->assertEqual($data_record['name'], $key);
            next($data);
            $count++;
        }
        $rs->close();
        $this->assertEqual($count, 3);

        // Test numeric not starting in 1 keys are returned ok
        $rs = $DB->get_recordset($tablename, NULL, 'id DESC', 'id, course, name');

        $data = array_reverse($data);
        reset($data);
        $count = 0;
        foreach($rs as $key => $record) {
            $data_record = current($data);
            $this->assertEqual($data_record['id'], $key);
            next($data);
            $count++;
        }
        $rs->close();
        $this->assertEqual($count, 3);
    }

    public function test_get_recordset_list() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $rs = $DB->get_recordset_list($tablename, 'course', array(3, 2));

        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEqual(3, $counter);
        $rs->close();

        $rs = $DB->get_recordset_list($tablename, 'course',array()); /// Must return 0 rows without conditions. MDL-17645

        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $rs->close();
        $this->assertEqual(0, $counter);

        // notes:
        //  * limits are tested in test_get_recordset_sql()
        //  * where_clause() is used internally and is tested in test_get_records()
    }

    public function test_get_recordset_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
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
        $this->assertEqual(4, $counter);

        $this->assertTrue($rs = $DB->get_recordset_select($tablename, 'course = 3'));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $rs->close();
        $this->assertEqual(2, $counter);

        // notes:
        //  * limits are tested in test_get_recordset_sql()
    }

    public function test_get_recordset_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
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
        $this->assertEqual(2, $counter);

        // limits - only need to test this case, the rest have been tested by test_get_records_sql()
        // only limitfrom = skips that number of records
        $rs = $DB->get_recordset_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 2, 0);
        $records = array();
        foreach($rs as $key => $record) {
            $records[$key] = $record;
        }
        $rs->close();
        $this->assertEqual(5, count($records));
        $this->assertEqual($inskey3, reset($records)->id);
        $this->assertEqual($inskey7, end($records)->id);

        // note: fetching nulls, empties, LOBs already tested by test_insert_record() no needed here
    }

    public function test_get_records() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        // All records
        $records = $DB->get_records($tablename);
        $this->assertEqual(4, count($records));
        $this->assertEqual(3, $records[1]->course);
        $this->assertEqual(3, $records[2]->course);
        $this->assertEqual(5, $records[3]->course);
        $this->assertEqual(2, $records[4]->course);

        // Records matching certain conditions
        $records = $DB->get_records($tablename, array('course' => 3));
        $this->assertEqual(2, count($records));
        $this->assertEqual(3, $records[1]->course);
        $this->assertEqual(3, $records[2]->course);

        // All records sorted by course
        $records = $DB->get_records($tablename, null, 'course');
        $this->assertEqual(4, count($records));
        $current_record = reset($records);
        $this->assertEqual(4, $current_record->id);
        $current_record = next($records);
        $this->assertEqual(1, $current_record->id);
        $current_record = next($records);
        $this->assertEqual(2, $current_record->id);
        $current_record = next($records);
        $this->assertEqual(3, $current_record->id);

        // All records, but get only one field
        $records = $DB->get_records($tablename, null, '', 'id');
        $this->assertFalse(isset($records[1]->course));
        $this->assertTrue(isset($records[1]->id));
        $this->assertEqual(4, count($records));

        // Booleans into params
        $records = $DB->get_records($tablename, array('course' => true));
        $this->assertEqual(0, count($records));
        $records = $DB->get_records($tablename, array('course' => false));
        $this->assertEqual(0, count($records));

        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext' => '1');
        try {
            $records = $DB->get_records($tablename, $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }

        // note: delegate limits testing to test_get_records_sql()
    }

    public function test_get_records_list() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $records = $DB->get_records_list($tablename, 'course', array(3, 2));
        $this->assertTrue(is_array($records));
        $this->assertEqual(3, count($records));
        $this->assertEqual(1, reset($records)->id);
        $this->assertEqual(2, next($records)->id);
        $this->assertEqual(4, next($records)->id);

        $this->assertIdentical(array(), $records = $DB->get_records_list($tablename, 'course', array())); /// Must return 0 rows without conditions. MDL-17645
        $this->assertEqual(0, count($records));

        // note: delegate limits testing to test_get_records_sql()
    }

    public function test_get_records_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
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
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table2->add_field('nametext', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);

        $DB->insert_record($tablename2, array('course'=>3, 'nametext'=>'badabing'));
        $DB->insert_record($tablename2, array('course'=>4, 'nametext'=>'badabang'));
        $DB->insert_record($tablename2, array('course'=>5, 'nametext'=>'badabung'));
        $DB->insert_record($tablename2, array('course'=>6, 'nametext'=>'badabong'));

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE course = ?", array(3));
        $this->assertEqual(2, count($records));
        $this->assertEqual($inskey1, reset($records)->id);
        $this->assertEqual($inskey4, next($records)->id);

        // Awful test, requires debug enabled and sent to browser. Let's do that and restore after test
        $this->enable_debugging();
        $records = $DB->get_records_sql("SELECT course AS id, course AS course FROM {{$tablename}}", null);
        $this->assertFalse($this->get_debugging() === '');
        $this->assertEqual(6, count($records));

        // negative limits = no limits
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, -1, -1);
        $this->assertEqual(7, count($records));

        // zero limits = no limits
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 0, 0);
        $this->assertEqual(7, count($records));

        // only limitfrom = skips that number of records
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 2, 0);
        $this->assertEqual(5, count($records));
        $this->assertEqual($inskey3, reset($records)->id);
        $this->assertEqual($inskey7, end($records)->id);

        // only limitnum = fetches that number of records
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 0, 3);
        $this->assertEqual(3, count($records));
        $this->assertEqual($inskey1, reset($records)->id);
        $this->assertEqual($inskey3, end($records)->id);

        // both limitfrom and limitnum = skips limitfrom records and fetches limitnum ones
        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} ORDER BY id", null, 3, 2);
        $this->assertEqual(2, count($records));
        $this->assertEqual($inskey4, reset($records)->id);
        $this->assertEqual($inskey5, end($records)->id);

        // both limitfrom and limitnum in query having subqueris
        // note the subquery skips records with course = 0 and 3
        $sql = "SELECT * FROM {{$tablename}}
                 WHERE course NOT IN (
                     SELECT course FROM {{$tablename}}
                      WHERE course IN (0, 3))
                ORDER BY course";
        $records = $DB->get_records_sql($sql, null, 0, 2); // Skip 0, get 2
        $this->assertEqual(2, count($records));
        $this->assertEqual($inskey6, reset($records)->id);
        $this->assertEqual($inskey5, end($records)->id);
        $records = $DB->get_records_sql($sql, null, 2, 2); // Skip 2, get 2
        $this->assertEqual(2, count($records));
        $this->assertEqual($inskey3, reset($records)->id);
        $this->assertEqual($inskey2, end($records)->id);

        // test 2 tables with aliases and limits with order bys
        $sql = "SELECT t1.id, t1.course AS cid, t2.nametext
                  FROM {{$tablename}} t1, {{$tablename2}} t2
                 WHERE t2.course=t1.course
              ORDER BY t1.course, ". $DB->sql_compare_text('t2.nametext');
        $records = $DB->get_records_sql($sql, null, 2, 2); // Skip courses 3 and 6, get 4 and 5
        $this->assertEqual(2, count($records));
        $this->assertEqual('5', end($records)->cid);
        $this->assertEqual('4', reset($records)->cid);

        // test 2 tables with aliases and limits with the highest INT limit works
        $records = $DB->get_records_sql($sql, null, 2, PHP_INT_MAX); // Skip course {3,6}, get {4,5}
        $this->assertEqual(2, count($records));
        $this->assertEqual('5', end($records)->cid);
        $this->assertEqual('4', reset($records)->cid);

        // test 2 tables with aliases and limits with order bys (limit which is highest INT number)
        $records = $DB->get_records_sql($sql, null, PHP_INT_MAX, 2); // Skip all courses
        $this->assertEqual(0, count($records));

        // test 2 tables with aliases and limits with order bys (limit which s highest INT number)
        $records = $DB->get_records_sql($sql, null, PHP_INT_MAX, PHP_INT_MAX); // Skip all courses
        $this->assertEqual(0, count($records));

        // TODO: Test limits in queries having DISTINCT clauses

        // note: fetching nulls, empties, LOBs already tested by test_update_record() no needed here
    }

    public function test_get_records_menu() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $records = $DB->get_records_menu($tablename, array('course' => 3));
        $this->assertTrue(is_array($records));
        $this->assertEqual(2, count($records));
        $this->assertFalse(empty($records[1]));
        $this->assertFalse(empty($records[2]));
        $this->assertEqual(3, $records[1]);
        $this->assertEqual(3, $records[2]);

        // note: delegate limits testing to test_get_records_sql()
    }

    public function test_get_records_select_menu() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));

        $records = $DB->get_records_select_menu($tablename, "course > ?", array(2));
        $this->assertTrue(is_array($records));

        $this->assertEqual(3, count($records));
        $this->assertFalse(empty($records[1]));
        $this->assertTrue(empty($records[2]));
        $this->assertFalse(empty($records[3]));
        $this->assertFalse(empty($records[4]));
        $this->assertEqual(3, $records[1]);
        $this->assertEqual(3, $records[3]);
        $this->assertEqual(5, $records[4]);

        // note: delegate limits testing to test_get_records_sql()
    }

    public function test_get_records_sql_menu() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));

        $records = $DB->get_records_sql_menu("SELECT * FROM {{$tablename}} WHERE course > ?", array(2));
        $this->assertTrue(is_array($records));

        $this->assertEqual(3, count($records));
        $this->assertFalse(empty($records[1]));
        $this->assertTrue(empty($records[2]));
        $this->assertFalse(empty($records[3]));
        $this->assertFalse(empty($records[4]));
        $this->assertEqual(3, $records[1]);
        $this->assertEqual(3, $records[3]);
        $this->assertEqual(5, $records[4]);

        // note: delegate limits testing to test_get_records_sql()
    }

    public function test_get_record() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        $record = $DB->get_record($tablename, array('id' => 2));
        $this->assertTrue($record instanceof stdClass);

        $this->assertEqual(2, $record->course);
        $this->assertEqual(2, $record->id);
    }


    public function test_get_record_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        $record = $DB->get_record_select($tablename, "id = ?", array(2));
        $this->assertTrue($record instanceof stdClass);

        $this->assertEqual(2, $record->course);

        // note: delegates limit testing to test_get_records_sql()
    }

    public function test_get_record_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        // standard use
        $record = $DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(2));
        $this->assertTrue($record instanceof stdClass);
        $this->assertEqual(2, $record->course);
        $this->assertEqual(2, $record->id);

        // backwards compatibility with $ignoremultiple
        $this->assertFalse(IGNORE_MISSING);
        $this->assertTrue(IGNORE_MULTIPLE);

        // record not found - ignore
        $this->assertFalse($DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(666), IGNORE_MISSING));
        $this->assertFalse($DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(666), IGNORE_MULTIPLE));

        // record not found error
        try {
            $DB->get_record_sql("SELECT * FROM {{$tablename}} WHERE id = ?", array(666), MUST_EXIST);
            $this->fail("Exception expected");
        } catch (dml_missing_record_exception $e) {
            $this->assertTrue(true);
        }

        $this->enable_debugging();
        $this->assertTrue($DB->get_record_sql("SELECT * FROM {{$tablename}}", array(), IGNORE_MISSING));
        $this->assertFalse($this->get_debugging() === '');

        // multiple matches ignored
        $this->assertTrue($DB->get_record_sql("SELECT * FROM {{$tablename}}", array(), IGNORE_MULTIPLE));

        // multiple found error
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

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $id1 = $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertEqual(3, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEqual(3, $DB->get_field($tablename, 'course', array('course' => 3)));

        $this->assertIdentical(false, $DB->get_field($tablename, 'course', array('course' => 11), IGNORE_MISSING));
        try {
            $DB->get_field($tablename, 'course', array('course' => 4), MUST_EXIST);
            $this->assertFail('Exception expected due to missing record');
        } catch (dml_exception $ex) {
            $this->assertTrue(true);
        }

        $this->enable_debugging();
        $this->assertEqual(5, $DB->get_field($tablename, 'course', array('course' => 5), IGNORE_MULTIPLE));
        $this->assertIdentical($this->get_debugging(), '');

        $this->enable_debugging();
        $this->assertEqual(5, $DB->get_field($tablename, 'course', array('course' => 5), IGNORE_MISSING));
        $this->assertFalse($this->get_debugging() === '');

        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext' => '1');
        try {
            $DB->get_field($tablename, 'course', $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }
    }

    public function test_get_field_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));

        $this->assertEqual(3, $DB->get_field_select($tablename, 'course', "id = ?", array(1)));
    }

    public function test_get_field_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));

        $this->assertEqual(3, $DB->get_field_sql("SELECT course FROM {{$tablename}} WHERE id = ?", array(1)));
    }

    public function test_get_fieldset_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 6));

        $fieldset = $DB->get_fieldset_select($tablename, 'course', "course > ?", array(1));
        $this->assertTrue(is_array($fieldset));

        $this->assertEqual(3, count($fieldset));
        $this->assertEqual(3, $fieldset[0]);
        $this->assertEqual(2, $fieldset[1]);
        $this->assertEqual(6, $fieldset[2]);
    }

    public function test_get_fieldset_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 6));

        $fieldset = $DB->get_fieldset_sql("SELECT * FROM {{$tablename}} WHERE course > ?", array(1));
        $this->assertTrue(is_array($fieldset));

        $this->assertEqual(3, count($fieldset));
        $this->assertEqual(2, $fieldset[0]);
        $this->assertEqual(3, $fieldset[1]);
        $this->assertEqual(4, $fieldset[2]);
    }

    public function test_insert_record_raw() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $record = (object)array('course' => 1, 'onechar' => 'xx');
        $before = clone($record);
        $result = $DB->insert_record_raw($tablename, $record);
        $this->assertIdentical(1, $result);
        $this->assertIdentical($record, $before);

        $record = $DB->get_record($tablename, array('course' => 1));
        $this->assertTrue($record instanceof stdClass);
        $this->assertIdentical('xx', $record->onechar);

        $result = $DB->insert_record_raw($tablename, array('course' => 2, 'onechar' => 'yy'), false);
        $this->assertIdentical(true, $result);

        // note: bulk not implemented yet
        $DB->insert_record_raw($tablename, array('course' => 3, 'onechar' => 'zz'), true, true);
        $record = $DB->get_record($tablename, array('course' => 3));
        $this->assertTrue($record instanceof stdClass);
        $this->assertIdentical('zz', $record->onechar);

        // custom sequence (id) - returnid is ignored
        $result = $DB->insert_record_raw($tablename, array('id' => 10, 'course' => 3, 'onechar' => 'bb'), true, false, true);
        $this->assertIdentical(true, $result);
        $record = $DB->get_record($tablename, array('id' => 10));
        $this->assertTrue($record instanceof stdClass);
        $this->assertIdentical('bb', $record->onechar);

        // custom sequence - missing id error
        try {
            $DB->insert_record_raw($tablename, array('course' => 3, 'onechar' => 'bb'), true, false, true);
            $this->assertFail('Exception expected due to missing record');
        } catch (coding_exception $ex) {
            $this->assertTrue(true);
        }

        // wrong column error
        try {
            $DB->insert_record_raw($tablename, array('xxxxx' => 3, 'onechar' => 'bb'));
            $this->assertFail('Exception expected due to invalid column');
        } catch (dml_write_exception $ex) {
            $this->assertTrue(true);
        }
    }

    public function test_insert_record() {
        // All the information in this test is fetched from DB by get_recordset() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 100);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertIdentical(1, $DB->insert_record($tablename, array('course' => 1), true));
        $record = $DB->get_record($tablename, array('course' => 1));
        $this->assertEqual(1, $record->id);
        $this->assertEqual(100, $record->oneint); // Just check column defaults have been applied
        $this->assertEqual(200, $record->onenum);
        $this->assertIdentical('onestring', $record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // without returning id, bulk not implemented
        $result = $this->assertIdentical(true, $DB->insert_record($tablename, array('course' => 99), false, true));
        $record = $DB->get_record($tablename, array('course' => 99));
        $this->assertEqual(2, $record->id);
        $this->assertEqual(99, $record->course);

        // Check nulls are set properly for all types
        $record = new stdClass();
        $record->oneint = null;
        $record->onenum = null;
        $record->onechar = null;
        $record->onetext = null;
        $record->onebinary = null;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEqual(0, $record->course);
        $this->assertNull($record->oneint);
        $this->assertNull($record->onenum);
        $this->assertNull($record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check zeros are set properly for all types
        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = 0;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEqual(0, $record->oneint);
        $this->assertEqual(0, $record->onenum);

        // Check booleans are set properly for all types
        $record = new stdClass();
        $record->oneint = true; // trues
        $record->onenum = true;
        $record->onechar = true;
        $record->onetext = true;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEqual(1, $record->oneint);
        $this->assertEqual(1, $record->onenum);
        $this->assertEqual(1, $record->onechar);
        $this->assertEqual(1, $record->onetext);

        $record = new stdClass();
        $record->oneint = false; // falses
        $record->onenum = false;
        $record->onechar = false;
        $record->onetext = false;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEqual(0, $record->oneint);
        $this->assertEqual(0, $record->onenum);
        $this->assertEqual(0, $record->onechar);
        $this->assertEqual(0, $record->onetext);

        // Check string data causes exception in numeric types
        $record = new stdClass();
        $record->oneint = 'onestring';
        $record->onenum = 0;
        try {
            $DB->insert_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }
        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = 'onestring';
        try {
           $DB->insert_record($tablename, $record);
           $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Check empty string data is stored as 0 in numeric datatypes
        $record = new stdClass();
        $record->oneint = ''; // empty string
        $record->onenum = 0;
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertTrue(is_numeric($record->oneint) && $record->oneint == 0);

        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = ''; // empty string
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertTrue(is_numeric($record->onenum) && $record->onenum == 0);

        // Check empty strings are set properly in string types
        $record = new stdClass();
        $record->oneint = 0;
        $record->onenum = 0;
        $record->onechar = '';
        $record->onetext = '';
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertTrue($record->onechar === '');
        $this->assertTrue($record->onetext === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types
        $record = new stdClass();
        $record->oneint = ((210.10 + 39.92) - 150.02);
        $record->onenum = ((210.10 + 39.92) - 150.02);
        $recid = $DB->insert_record($tablename, $record);
        $record = $DB->get_record($tablename, array('id' => $recid));
        $this->assertEqual(100, $record->oneint);
        $this->assertEqual(100, $record->onenum);

        // Check various quotes/backslashes combinations in string types
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
            $this->assertEqual($teststring, $record->onechar);
            $this->assertEqual($teststring, $record->onetext);
        }

        // Check LOBs in text/binary columns
        $clob = file_get_contents(dirname(__FILE__).'/fixtures/clob.txt');
        $blob = file_get_contents(dirname(__FILE__).'/fixtures/randombinary');
        $record = new stdClass();
        $record->onetext = $clob;
        $record->onebinary = $blob;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual($clob, $record->onetext, 'Test CLOB insert (full contents output disabled)');
        $this->assertEqual($blob, $record->onebinary, 'Test BLOB insert (full contents output disabled)');

        // And "small" LOBs too, just in case
        $newclob = substr($clob, 0, 500);
        $newblob = substr($blob, 0, 250);
        $record = new stdClass();
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual($newclob, $record->onetext, 'Test "small" CLOB insert (full contents output disabled)');
        $this->assertEqual($newblob, $record->onebinary, 'Test "small" BLOB insert (full contents output disabled)');
        $this->assertEqual(false, $rs->key()); // Ensure recordset key() method to be working ok after closing

        // And "diagnostic" LOBs too, just in case
        $newclob = '\'"\\;/ěščřžýáíé';
        $newblob = '\'"\\;/ěščřžýáíé';
        $record = new stdClass();
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertIdentical($newclob, $record->onetext);
        $this->assertIdentical($newblob, $record->onebinary);
        $this->assertEqual(false, $rs->key()); // Ensure recordset key() method to be working ok after closing

        // test data is not modified
        $record = new stdClass();
        $record->id     = -1; // has to be ignored
        $record->course = 3;
        $record->lalala = 'lalal'; // unused
        $before = clone($record);
        $DB->insert_record($tablename, $record);
        $this->assertEqual($record, $before);

        // make sure the id is always increasing and never reuses the same id
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
        $this->assertEqual(1.0, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e20));
        $this->assertEqual(1e20, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e-4));
        $this->assertEqual(1e-4, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e-5));
        $this->assertEqual(1e-5, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e-300));
        $this->assertEqual(1e-300, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onechar' => 1e300));
        $this->assertEqual(1e300, $DB->get_field($tablename, 'onechar', array('id' => $id)));

        // Test saving a float in a TEXT column, and reading it back.
        $id = $DB->insert_record($tablename, array('onetext' => 1.0));
        $this->assertEqual(1.0, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e20));
        $this->assertEqual(1e20, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e-4));
        $this->assertEqual(1e-4, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e-5));
        $this->assertEqual(1e-5, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e-300));
        $this->assertEqual(1e-300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $id = $DB->insert_record($tablename, array('onetext' => 1e300));
        $this->assertEqual(1e300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
    }

    public function test_import_record() {
        // All the information in this test is fetched from DB by get_recordset() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 100);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertIdentical(1, $DB->insert_record($tablename, array('course' => 1), true));
        $record = $DB->get_record($tablename, array('course' => 1));
        $this->assertEqual(1, $record->id);
        $this->assertEqual(100, $record->oneint); // Just check column defaults have been applied
        $this->assertEqual(200, $record->onenum);
        $this->assertIdentical('onestring', $record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // ignore extra columns
        $record = (object)array('id'=>13, 'course'=>2, 'xxxx'=>788778);
        $before = clone($record);
        $this->assertIdentical(true, $DB->import_record($tablename, $record));
        $this->assertIdentical($record, $before);
        $records = $DB->get_records($tablename);
        $this->assertEqual(2, $records[13]->course);

        // Check nulls are set properly for all types
        $record = new stdClass();
        $record->id = 20;
        $record->oneint = null;
        $record->onenum = null;
        $record->onechar = null;
        $record->onetext = null;
        $record->onebinary = null;
        $this->assertTrue($DB->import_record($tablename, $record));
        $record = $DB->get_record($tablename, array('id' => 20));
        $this->assertEqual(0, $record->course);
        $this->assertNull($record->oneint);
        $this->assertNull($record->onenum);
        $this->assertNull($record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check zeros are set properly for all types
        $record = new stdClass();
        $record->id = 23;
        $record->oneint = 0;
        $record->onenum = 0;
        $this->assertTrue($DB->import_record($tablename, $record));
        $record = $DB->get_record($tablename, array('id' => 23));
        $this->assertEqual(0, $record->oneint);
        $this->assertEqual(0, $record->onenum);

        // Check string data causes exception in numeric types
        $record = new stdClass();
        $record->id = 32;
        $record->oneint = 'onestring';
        $record->onenum = 0;
        try {
            $DB->import_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }
        $record = new stdClass();
        $record->id = 35;
        $record->oneint = 0;
        $record->onenum = 'onestring';
        try {
           $DB->import_record($tablename, $record);
           $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Check empty strings are set properly in string types
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

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types
        $record = new stdClass();
        $record->id = 47;
        $record->oneint = ((210.10 + 39.92) - 150.02);
        $record->onenum = ((210.10 + 39.92) - 150.02);
        $this->assertTrue($DB->import_record($tablename, $record));
        $record = $DB->get_record($tablename, array('id' => 47));
        $this->assertEqual(100, $record->oneint);
        $this->assertEqual(100, $record->onenum);

        // Check various quotes/backslashes combinations in string types
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
            $this->assertEqual($teststring, $record->onechar);
            $this->assertEqual($teststring, $record->onetext);
            $i = $i + 3;
        }

        // Check LOBs in text/binary columns
        $clob = file_get_contents(dirname(__FILE__).'/fixtures/clob.txt');
        $record = new stdClass();
        $record->id = 70;
        $record->onetext = $clob;
        $record->onebinary = '';
        $this->assertTrue($DB->import_record($tablename, $record));
        $rs = $DB->get_recordset($tablename, array('id' => 70));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual($clob, $record->onetext, 'Test CLOB insert (full contents output disabled)');

        $blob = file_get_contents(dirname(__FILE__).'/fixtures/randombinary');
        $record = new stdClass();
        $record->id = 71;
        $record->onetext = '';
        $record->onebinary = $blob;
        $this->assertTrue($DB->import_record($tablename, $record));
        $rs = $DB->get_recordset($tablename, array('id' => 71));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual($blob, $record->onebinary, 'Test BLOB insert (full contents output disabled)');

        // And "small" LOBs too, just in case
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
        $this->assertEqual($newclob, $record->onetext, 'Test "small" CLOB insert (full contents output disabled)');
        $this->assertEqual($newblob, $record->onebinary, 'Test "small" BLOB insert (full contents output disabled)');
        $this->assertEqual(false, $rs->key()); // Ensure recordset key() method to be working ok after closing
    }

    public function test_update_record_raw() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 3));

        $record = $DB->get_record($tablename, array('course' => 1));
        $record->course = 2;
        $this->assertTrue($DB->update_record_raw($tablename, $record));
        $this->assertEqual(0, $DB->count_records($tablename, array('course' => 1)));
        $this->assertEqual(1, $DB->count_records($tablename, array('course' => 2)));
        $this->assertEqual(1, $DB->count_records($tablename, array('course' => 3)));

        $record = $DB->get_record($tablename, array('course' => 1));
        $record->xxxxx = 2;
        try {
           $DB->update_record_raw($tablename, $record);
           $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof coding_exception);
        }

        $record = $DB->get_record($tablename, array('course' => 3));
        unset($record->id);
        try {
           $DB->update_record_raw($tablename, $record);
           $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof coding_exception);
        }
    }

    public function test_update_record() {

        // All the information in this test is fetched from DB by get_record() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, 100);
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
        $this->assertTrue($record = $DB->get_record($tablename, array('course' => 2)));
        $this->assertEqual(100, $record->oneint); // Just check column defaults have been applied
        $this->assertEqual(200, $record->onenum);
        $this->assertEqual('onestring', $record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check nulls are set properly for all types
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

        // Check zeros are set properly for all types
        $record->oneint = 0;
        $record->onenum = 0;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEqual(0, $record->oneint);
        $this->assertEqual(0, $record->onenum);

        // Check booleans are set properly for all types
        $record->oneint = true; // trues
        $record->onenum = true;
        $record->onechar = true;
        $record->onetext = true;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEqual(1, $record->oneint);
        $this->assertEqual(1, $record->onenum);
        $this->assertEqual(1, $record->onechar);
        $this->assertEqual(1, $record->onetext);

        $record->oneint = false; // falses
        $record->onenum = false;
        $record->onechar = false;
        $record->onetext = false;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEqual(0, $record->oneint);
        $this->assertEqual(0, $record->onenum);
        $this->assertEqual(0, $record->onechar);
        $this->assertEqual(0, $record->onetext);

        // Check string data causes exception in numeric types
        $record->oneint = 'onestring';
        $record->onenum = 0;
        try {
            $DB->update_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }
        $record->oneint = 0;
        $record->onenum = 'onestring';
        try {
            $DB->update_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Check empty string data is stored as 0 in numeric datatypes
        $record->oneint = ''; // empty string
        $record->onenum = 0;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertTrue(is_numeric($record->oneint) && $record->oneint == 0);

        $record->oneint = 0;
        $record->onenum = ''; // empty string
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertTrue(is_numeric($record->onenum) && $record->onenum == 0);

        // Check empty strings are set properly in string types
        $record->oneint = 0;
        $record->onenum = 0;
        $record->onechar = '';
        $record->onetext = '';
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertTrue($record->onechar === '');
        $this->assertTrue($record->onetext === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types
        $record->oneint = ((210.10 + 39.92) - 150.02);
        $record->onenum = ((210.10 + 39.92) - 150.02);
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEqual(100, $record->oneint);
        $this->assertEqual(100, $record->onenum);

        // Check various quotes/backslashes combinations in string types
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
            $this->assertEqual($teststring, $record->onechar);
            $this->assertEqual($teststring, $record->onetext);
        }

        // Check LOBs in text/binary columns
        $clob = file_get_contents(dirname(__FILE__).'/fixtures/clob.txt');
        $blob = file_get_contents(dirname(__FILE__).'/fixtures/randombinary');
        $record->onetext = $clob;
        $record->onebinary = $blob;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEqual($clob, $record->onetext, 'Test CLOB update (full contents output disabled)');
        $this->assertEqual($blob, $record->onebinary, 'Test BLOB update (full contents output disabled)');

        // And "small" LOBs too, just in case
        $newclob = substr($clob, 0, 500);
        $newblob = substr($blob, 0, 250);
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $DB->update_record($tablename, $record);
        $record = $DB->get_record($tablename, array('course' => 2));
        $this->assertEqual($newclob, $record->onetext, 'Test "small" CLOB update (full contents output disabled)');
        $this->assertEqual($newblob, $record->onebinary, 'Test "small" BLOB update (full contents output disabled)');

        // Test saving a float in a CHAR column, and reading it back.
        $id = $DB->insert_record($tablename, array('onechar' => 'X'));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1.0));
        $this->assertEqual(1.0, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e20));
        $this->assertEqual(1e20, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e-4));
        $this->assertEqual(1e-4, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e-5));
        $this->assertEqual(1e-5, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e-300));
        $this->assertEqual(1e-300, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onechar' => 1e300));
        $this->assertEqual(1e300, $DB->get_field($tablename, 'onechar', array('id' => $id)));

        // Test saving a float in a TEXT column, and reading it back.
        $id = $DB->insert_record($tablename, array('onetext' => 'X'));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1.0));
        $this->assertEqual(1.0, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e20));
        $this->assertEqual(1e20, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e-4));
        $this->assertEqual(1e-4, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e-5));
        $this->assertEqual(1e-5, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e-300));
        $this->assertEqual(1e-300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->update_record($tablename, array('id' => $id, 'onetext' => 1e300));
        $this->assertEqual(1e300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
    }

    public function test_set_field() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null);
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // simple set_field
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $this->assertTrue($DB->set_field($tablename, 'course', 2, array('id' => $id1)));
        $this->assertEqual(2, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEqual(1, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEqual(3, $DB->get_field($tablename, 'course', array('id' => $id3)));
        $DB->delete_records($tablename, array());

        // multiple fields affected
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $DB->set_field($tablename, 'course', '5', array('course' => 1));
        $this->assertEqual(5, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEqual(5, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEqual(3, $DB->get_field($tablename, 'course', array('id' => $id3)));
        $DB->delete_records($tablename, array());

        // no field affected
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $DB->set_field($tablename, 'course', '5', array('course' => 0));
        $this->assertEqual(1, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEqual(1, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEqual(3, $DB->get_field($tablename, 'course', array('id' => $id3)));
        $DB->delete_records($tablename, array());

        // all fields - no condition
        $id1 = $DB->insert_record($tablename, array('course' => 1));
        $id2 = $DB->insert_record($tablename, array('course' => 1));
        $id3 = $DB->insert_record($tablename, array('course' => 3));
        $DB->set_field($tablename, 'course', 5, array());
        $this->assertEqual(5, $DB->get_field($tablename, 'course', array('id' => $id1)));
        $this->assertEqual(5, $DB->get_field($tablename, 'course', array('id' => $id2)));
        $this->assertEqual(5, $DB->get_field($tablename, 'course', array('id' => $id3)));

        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext' => '1');
        try {
            $DB->set_field($tablename, 'onechar', 'frog', $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }

        // Test saving a float in a CHAR column, and reading it back.
        $id = $DB->insert_record($tablename, array('onechar' => 'X'));
        $DB->set_field($tablename, 'onechar', 1.0, array('id' => $id));
        $this->assertEqual(1.0, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e20, array('id' => $id));
        $this->assertEqual(1e20, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e-4, array('id' => $id));
        $this->assertEqual(1e-4, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e-5, array('id' => $id));
        $this->assertEqual(1e-5, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e-300, array('id' => $id));
        $this->assertEqual(1e-300, $DB->get_field($tablename, 'onechar', array('id' => $id)));
        $DB->set_field($tablename, 'onechar', 1e300, array('id' => $id));
        $this->assertEqual(1e300, $DB->get_field($tablename, 'onechar', array('id' => $id)));

        // Test saving a float in a TEXT column, and reading it back.
        $id = $DB->insert_record($tablename, array('onetext' => 'X'));
        $DB->set_field($tablename, 'onetext', 1.0, array('id' => $id));
        $this->assertEqual(1.0, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e20, array('id' => $id));
        $this->assertEqual(1e20, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e-4, array('id' => $id));
        $this->assertEqual(1e-4, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e-5, array('id' => $id));
        $this->assertEqual(1e-5, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e-300, array('id' => $id));
        $this->assertEqual(1e-300, $DB->get_field($tablename, 'onetext', array('id' => $id)));
        $DB->set_field($tablename, 'onetext', 1e300, array('id' => $id));
        $this->assertEqual(1e300, $DB->get_field($tablename, 'onetext', array('id' => $id)));

        // Note: All the nulls, booleans, empties, quoted and backslashes tests
        // go to set_field_select() because set_field() is just one wrapper over it
    }

    public function test_set_field_select() {

        // All the information in this test is fetched from DB by get_field() so we
        // have such method properly tested against nulls, empties and friends...

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null);
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_field('onebinary', XMLDB_TYPE_BINARY, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));

        $this->assertTrue($DB->set_field_select($tablename, 'course', 2, 'id = ?', array(1)));
        $this->assertEqual(2, $DB->get_field($tablename, 'course', array('id' => 1)));

        // Check nulls are set properly for all types
        $DB->set_field_select($tablename, 'oneint', null, 'id = ?', array(1)); // trues
        $DB->set_field_select($tablename, 'onenum', null, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onechar', null, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', null, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onebinary', null, 'id = ?', array(1));
        $this->assertNull($DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onenum', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onechar', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onetext', array('id' => 1)));
        $this->assertNull($DB->get_field($tablename, 'onebinary', array('id' => 1)));

        // Check zeros are set properly for all types
        $DB->set_field_select($tablename, 'oneint', 0, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onenum', 0, 'id = ?', array(1));
        $this->assertEqual(0, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEqual(0, $DB->get_field($tablename, 'onenum', array('id' => 1)));

        // Check booleans are set properly for all types
        $DB->set_field_select($tablename, 'oneint', true, 'id = ?', array(1)); // trues
        $DB->set_field_select($tablename, 'onenum', true, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onechar', true, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', true, 'id = ?', array(1));
        $this->assertEqual(1, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEqual(1, $DB->get_field($tablename, 'onenum', array('id' => 1)));
        $this->assertEqual(1, $DB->get_field($tablename, 'onechar', array('id' => 1)));
        $this->assertEqual(1, $DB->get_field($tablename, 'onetext', array('id' => 1)));

        $DB->set_field_select($tablename, 'oneint', false, 'id = ?', array(1)); // falses
        $DB->set_field_select($tablename, 'onenum', false, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onechar', false, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', false, 'id = ?', array(1));
        $this->assertEqual(0, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEqual(0, $DB->get_field($tablename, 'onenum', array('id' => 1)));
        $this->assertEqual(0, $DB->get_field($tablename, 'onechar', array('id' => 1)));
        $this->assertEqual(0, $DB->get_field($tablename, 'onetext', array('id' => 1)));

        // Check string data causes exception in numeric types
        try {
            $DB->set_field_select($tablename, 'oneint', 'onestring', 'id = ?', array(1));
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }
        try {
            $DB->set_field_select($tablename, 'onenum', 'onestring', 'id = ?', array(1));
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Check empty string data is stored as 0 in numeric datatypes
        $DB->set_field_select($tablename, 'oneint', '', 'id = ?', array(1));
        $field = $DB->get_field($tablename, 'oneint', array('id' => 1));
        $this->assertTrue(is_numeric($field) && $field == 0);

        $DB->set_field_select($tablename, 'onenum', '', 'id = ?', array(1));
        $field = $DB->get_field($tablename, 'onenum', array('id' => 1));
        $this->assertTrue(is_numeric($field) && $field == 0);

        // Check empty strings are set properly in string types
        $DB->set_field_select($tablename, 'onechar', '', 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onetext', '', 'id = ?', array(1));
        $this->assertTrue($DB->get_field($tablename, 'onechar', array('id' => 1)) === '');
        $this->assertTrue($DB->get_field($tablename, 'onetext', array('id' => 1)) === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types
        $DB->set_field_select($tablename, 'oneint', ((210.10 + 39.92) - 150.02), 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onenum', ((210.10 + 39.92) - 150.02), 'id = ?', array(1));
        $this->assertEqual(100, $DB->get_field($tablename, 'oneint', array('id' => 1)));
        $this->assertEqual(100, $DB->get_field($tablename, 'onenum', array('id' => 1)));

        // Check various quotes/backslashes combinations in string types
        $teststrings = array(
            'backslashes and quotes alone (even): "" \'\' \\\\',
            'backslashes and quotes alone (odd): """ \'\'\' \\\\\\',
            'backslashes and quotes sequences (even): \\"\\" \\\'\\\'',
            'backslashes and quotes sequences (odd): \\"\\"\\" \\\'\\\'\\\'');
        foreach ($teststrings as $teststring) {
            $DB->set_field_select($tablename, 'onechar', $teststring, 'id = ?', array(1));
            $DB->set_field_select($tablename, 'onetext', $teststring, 'id = ?', array(1));
            $this->assertEqual($teststring, $DB->get_field($tablename, 'onechar', array('id' => 1)));
            $this->assertEqual($teststring, $DB->get_field($tablename, 'onetext', array('id' => 1)));
        }

        // Check LOBs in text/binary columns
        $clob = file_get_contents(dirname(__FILE__).'/fixtures/clob.txt');
        $blob = file_get_contents(dirname(__FILE__).'/fixtures/randombinary');
        $DB->set_field_select($tablename, 'onetext', $clob, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onebinary', $blob, 'id = ?', array(1));
        $this->assertEqual($clob, $DB->get_field($tablename, 'onetext', array('id' => 1)), 'Test CLOB set_field (full contents output disabled)');
        $this->assertEqual($blob, $DB->get_field($tablename, 'onebinary', array('id' => 1)), 'Test BLOB set_field (full contents output disabled)');

        // And "small" LOBs too, just in case
        $newclob = substr($clob, 0, 500);
        $newblob = substr($blob, 0, 250);
        $DB->set_field_select($tablename, 'onetext', $newclob, 'id = ?', array(1));
        $DB->set_field_select($tablename, 'onebinary', $newblob, 'id = ?', array(1));
        $this->assertEqual($newclob, $DB->get_field($tablename, 'onetext', array('id' => 1)), 'Test "small" CLOB set_field (full contents output disabled)');
        $this->assertEqual($newblob, $DB->get_field($tablename, 'onebinary', array('id' => 1)), 'Test "small" BLOB set_field (full contents output disabled)');

        // This is the failure from MDL-24863. This was giving an error on MSSQL,
        // which converts the '1' to an integer, which cannot then be compared with
        // onetext cast to a varchar. This should be fixed and working now.
        $newchar = 'frog';
        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
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

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEqual(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertEqual(3, $DB->count_records($tablename));

        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext' => '1');
        try {
            $DB->count_records($tablename, $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }
    }

    public function test_count_records_select() {
        $DB = $this->tdb;

        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEqual(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertEqual(2, $DB->count_records_select($tablename, 'course > ?', array(3)));
    }

    public function test_count_records_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEqual(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertEqual(2, $DB->count_records_sql("SELECT COUNT(*) FROM {{$tablename}} WHERE course > ?", array(3)));
    }

    public function test_record_exists() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEqual(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists($tablename, array('course' => 3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists($tablename, array('course' => 3)));


        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext' => '1');
        try {
            $DB->record_exists($tablename, $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }
    }

    public function test_record_exists_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEqual(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists_select($tablename, "course = ?", array(3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists_select($tablename, "course = ?", array(3)));
    }

    public function test_record_exists_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertEqual(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists_sql("SELECT * FROM {{$tablename}} WHERE course = ?", array(3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists_sql("SELECT * FROM {{$tablename}} WHERE course = ?", array(3)));
    }

    public function test_recordset_locks_delete() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        //Setup
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 6));

        // Test against db write locking while on an open recordset
        $rs = $DB->get_recordset($tablename, array(), null, 'course', 2, 2); // get courses = {3,4}
        foreach ($rs as $record) {
            $cid = $record->course;
            $DB->delete_records($tablename, array('course' => $cid));
            $this->assertFalse($DB->record_exists($tablename, array('course' => $cid)));
        }
        $rs->close();

        $this->assertEqual(4, $DB->count_records($tablename, array()));
    }

    public function test_recordset_locks_update() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        //Setup
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 6));

        // Test against db write locking while on an open recordset
        $rs = $DB->get_recordset($tablename, array(), null, 'course', 2, 2); // get courses = {3,4}
        foreach ($rs as $record) {
            $cid = $record->course;
            $DB->set_field($tablename, 'course', 10, array('course' => $cid));
            $this->assertFalse($DB->record_exists($tablename, array('course' => $cid)));
        }
        $rs->close();

        $this->assertEqual(2, $DB->count_records($tablename, array('course' => 10)));
    }

    public function test_delete_records() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 2));

        // Delete all records
        $this->assertTrue($DB->delete_records($tablename));
        $this->assertEqual(0, $DB->count_records($tablename));

        // Delete subset of records
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($DB->delete_records($tablename, array('course' => 2)));
        $this->assertEqual(1, $DB->count_records($tablename));

        // delete all
        $this->assertTrue($DB->delete_records($tablename, array()));
        $this->assertEqual(0, $DB->count_records($tablename));

        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext'=>'1');
        try {
            $DB->delete_records($tablename, $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }

        // test for exception throwing on text conditions being compared. (MDL-24863, unwanted auto conversion of param to int)
        $conditions = array('onetext' => 1);
        try {
            $DB->delete_records($tablename, $conditions);
            $this->fail('An Exception is missing, expected due to equating of text fields');
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
            $this->assertEqual($e->errorcode, 'textconditionsnotallowed');
        }
    }

    public function test_delete_records_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($DB->delete_records_select($tablename, 'course = ?', array(2)));
        $this->assertEqual(1, $DB->count_records($tablename));
    }

    public function test_delete_records_list() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->delete_records_list($tablename, 'course', array(2, 3)));
        $this->assertEqual(1, $DB->count_records($tablename));

        $this->assertTrue($DB->delete_records_list($tablename, 'course', array())); /// Must delete 0 rows without conditions. MDL-17645
        $this->assertEqual(1, $DB->count_records($tablename));
    }

    function test_sql_null_from_clause() {
        $DB = $this->tdb;
        $sql = "SELECT 1 AS id ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 1);
    }

    function test_sql_bitand() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('col1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('col2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('col1' => 3, 'col2' => 10));

        $sql = "SELECT ".$DB->sql_bitand(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 2);

        $sql = "SELECT id, ".$DB->sql_bitand('col1', 'col2')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql);
        $this->assertEqual(count($result), 1);
        $this->assertEqual(reset($result)->res, 2);

        $sql = "SELECT id, ".$DB->sql_bitand('col1', '?')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql, array(10));
        $this->assertEqual(count($result), 1);
        $this->assertEqual(reset($result)->res, 2);
    }

    function test_sql_bitnot() {
        $DB = $this->tdb;

        $not = $DB->sql_bitnot(2);
        $notlimited = $DB->sql_bitand($not, 7); // might be positive or negative number which can not fit into PHP INT!

        $sql = "SELECT $notlimited AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 5);
    }

    function test_sql_bitor() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('col1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('col2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('col1' => 3, 'col2' => 10));

        $sql = "SELECT ".$DB->sql_bitor(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 11);

        $sql = "SELECT id, ".$DB->sql_bitor('col1', 'col2')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql);
        $this->assertEqual(count($result), 1);
        $this->assertEqual(reset($result)->res, 11);

        $sql = "SELECT id, ".$DB->sql_bitor('col1', '?')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql, array(10));
        $this->assertEqual(count($result), 1);
        $this->assertEqual(reset($result)->res, 11);
    }

    function test_sql_bitxor() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('col1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('col2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('col1' => 3, 'col2' => 10));

        $sql = "SELECT ".$DB->sql_bitxor(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 9);

        $sql = "SELECT id, ".$DB->sql_bitxor('col1', 'col2')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql);
        $this->assertEqual(count($result), 1);
        $this->assertEqual(reset($result)->res, 9);

        $sql = "SELECT id, ".$DB->sql_bitxor('col1', '?')." AS res FROM {{$tablename}}";
        $result = $DB->get_records_sql($sql, array(10));
        $this->assertEqual(count($result), 1);
        $this->assertEqual(reset($result)->res, 9);
    }

    function test_sql_modulo() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_modulo(10, 7)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 3);
    }

    function test_sql_ceil() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_ceil(665.666)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 666);
    }

    function test_cast_char2int() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table1 = $this->get_test_table("1");
        $tablename1 = $table1->getName();

        $table1->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table1->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table1->add_field('nametext', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table1->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table1);

        $DB->insert_record($tablename1, array('name'=>'0100', 'nametext'=>'0200'));
        $DB->insert_record($tablename1, array('name'=>'10',   'nametext'=>'20'));

        $table2 = $this->get_test_table("2");
        $tablename2 = $table2->getName();
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('res', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table2->add_field('restext', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);

        $DB->insert_record($tablename2, array('res'=>100, 'restext'=>200));

        // casting varchar field
        $sql = "SELECT *
                  FROM {".$tablename1."} t1
                  JOIN {".$tablename2."} t2 ON ".$DB->sql_cast_char2int("t1.name")." = t2.res ";
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 1);
        // also test them in order clauses
        $sql = "SELECT * FROM {{$tablename1}} ORDER BY ".$DB->sql_cast_char2int('name');
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 2);
        $this->assertEqual(reset($records)->name, '10');
        $this->assertEqual(next($records)->name, '0100');

        // casting text field
        $sql = "SELECT *
                  FROM {".$tablename1."} t1
                  JOIN {".$tablename2."} t2 ON ".$DB->sql_cast_char2int("t1.nametext", true)." = t2.restext ";
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 1);
        // also test them in order clauses
        $sql = "SELECT * FROM {{$tablename1}} ORDER BY ".$DB->sql_cast_char2int('nametext', true);
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 2);
        $this->assertEqual(reset($records)->nametext, '20');
        $this->assertEqual(next($records)->nametext, '0200');
    }

    function test_cast_char2real() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('nametext', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('res', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'10.10', 'nametext'=>'10.10', 'res'=>5.1));
        $DB->insert_record($tablename, array('name'=>'91.10', 'nametext'=>'91.10', 'res'=>666));
        $DB->insert_record($tablename, array('name'=>'011.10','nametext'=>'011.10','res'=>10.1));

        // casting varchar field
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_cast_char2real('name')." > res";
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 2);
        // also test them in order clauses
        $sql = "SELECT * FROM {{$tablename}} ORDER BY ".$DB->sql_cast_char2real('name');
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 3);
        $this->assertEqual(reset($records)->name, '10.10');
        $this->assertEqual(next($records)->name, '011.10');
        $this->assertEqual(next($records)->name, '91.10');

        // casting text field
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_cast_char2real('nametext', true)." > res";
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 2);
        // also test them in order clauses
        $sql = "SELECT * FROM {{$tablename}} ORDER BY ".$DB->sql_cast_char2real('nametext', true);
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 3);
        $this->assertEqual(reset($records)->nametext, '10.10');
        $this->assertEqual(next($records)->nametext, '011.10');
        $this->assertEqual(next($records)->nametext, '91.10');
    }

    function sql_compare_text() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'abcd',   'description'=>'abcd'));
        $DB->insert_record($tablename, array('name'=>'abcdef', 'description'=>'bbcdef'));
        $DB->insert_record($tablename, array('name'=>'aaaabb', 'description'=>'aaaacccccccccccccccccc'));

        $sql = "SELECT * FROM {{$tablename}} WHERE name = ".$DB->sql_compare_text('description');
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {{$tablename}} WHERE name = ".$DB->sql_compare_text('description', 4);
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 2);
    }

    function test_unique_index_collation_trouble() {
        // note: this is a work in progress, we should probably move this to ddl test

        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('name', XMLDB_INDEX_UNIQUE, array('name'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'aaa'));

        try {
            $DB->insert_record($tablename, array('name'=>'AAA'));
        } catch (Exception $e) {
            //TODO: ignore case insensitive uniqueness problems for now
            //$this->fail("Unique index is case sensitive - this may cause problems in some tables");
        }

        try {
            $DB->insert_record($tablename, array('name'=>'aäa'));
            $DB->insert_record($tablename, array('name'=>'aáa'));
            $this->assertTrue(true);
        } catch (Exception $e) {
            $family = $DB->get_dbfamily();
            if ($family === 'mysql' or $family === 'mssql') {
                $this->fail("Unique index is accent insensitive, this may cause problems for non-ascii languages. This is usually caused by accent insensitive default collation.");
            } else {
                // this should not happen, PostgreSQL and Oracle do not support accent insensitive uniqueness.
                $this->fail("Unique index is accent insensitive, this may cause problems for non-ascii languages.");
            }
            throw($e);
        }
    }

    function test_sql_binary_equal() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'aaa'));
        $DB->insert_record($tablename, array('name'=>'aáa'));
        $DB->insert_record($tablename, array('name'=>'aäa'));
        $DB->insert_record($tablename, array('name'=>'bbb'));
        $DB->insert_record($tablename, array('name'=>'BBB'));

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE name = ?", array('aaa'));
        $this->assertEqual(count($records), 1, 'SQL operator "=" is expected to be accent sensitive');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE name = ?", array('bbb'));
        $this->assertEqual(count($records), 1, 'SQL operator "=" is expected to be case sensitive');
    }

    function test_sql_like() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
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
        $this->assertEqual(count($records), 2);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true);
        $records = $DB->get_records_sql($sql, array("%dup%"));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?'); // defaults
        $records = $DB->get_records_sql($sql, array("%dup%"));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true);
        $records = $DB->get_records_sql($sql, array("ouc\\_"));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true, false, '|');
        $records = $DB->get_records_sql($sql, array($DB->sql_like_escape("ouc%", '|')));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true);
        $records = $DB->get_records_sql($sql, array('aui'));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, true, true); // NOT LIKE
        $records = $DB->get_records_sql($sql, array("%o%"));
        $this->assertEqual(count($records), 3);

        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', false, true, true); // NOT ILIKE
        $records = $DB->get_records_sql($sql, array("%D%"));
        $this->assertEqual(count($records), 6);

        // TODO: we do not require accent insensitivness yet, just make sure it does not throw errors
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', true, false);
        $records = $DB->get_records_sql($sql, array('aui'));
        //$this->assertEqual(count($records), 2, 'Accent insensitive LIKE searches may not be supported in all databases, this is not a problem.');
        $sql = "SELECT * FROM {{$tablename}} WHERE ".$DB->sql_like('name', '?', false, false);
        $records = $DB->get_records_sql($sql, array('aui'));
        //$this->assertEqual(count($records), 3, 'Accent insensitive LIKE searches may not be supported in all databases, this is not a problem.');
    }

    function test_sql_ilike() {
        // note: this is deprecated, just make sure it does not throw error
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'SuperDuperRecord'));
        $DB->insert_record($tablename, array('name'=>'NoDupor'));
        $DB->insert_record($tablename, array('name'=>'ouch'));

        // make sure it prints debug message
        $this->enable_debugging();
        $sql = "SELECT * FROM {{$tablename}} WHERE name ".$DB->sql_ilike()." ?";
        $params = array("%dup_r%");
        $this->assertFalse($this->get_debugging() === '');

        // following must not throw exception, we ignore result
        $DB->get_records_sql($sql, $params);
    }

    function test_sql_concat() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        /// Testing all sort of values
        $sql = "SELECT ".$DB->sql_concat("?", "?", "?")." AS fullname ". $DB->sql_null_from_clause();
        // string, some unicode chars
        $params = array('name', 'áéíóú', 'name3');
        $this->assertEqual('nameáéíóúname3', $DB->get_field_sql($sql, $params));
        // string, spaces and numbers
        $params = array('name', '  ', 12345);
        $this->assertEqual('name  12345', $DB->get_field_sql($sql, $params));
        // float, empty and strings
        $params = array(123.45, '', 'test');
        $this->assertEqual('123.45test', $DB->get_field_sql($sql, $params));
        // only integers
        $params = array(12, 34, 56);
        $this->assertEqual('123456', $DB->get_field_sql($sql, $params));
        // float, null and strings
        $params = array(123.45, null, 'test');
        $this->assertNull($DB->get_field_sql($sql, $params), 'ANSI behaviour: Concatenating NULL must return NULL - But in Oracle :-(. [%s]'); // Concatenate NULL with anything result = NULL

        /// Testing fieldnames + values and also integer fieldnames
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('description'=>'áéíóú'));
        $DB->insert_record($tablename, array('description'=>'dxxx'));
        $DB->insert_record($tablename, array('description'=>'bcde'));

        // fieldnames and values mixed
        $sql = 'SELECT id, ' . $DB->sql_concat('description', "'harcoded'", '?', '?') . ' AS result FROM {' . $tablename . '}';
        $records = $DB->get_records_sql($sql, array(123.45, 'test'));
        $this->assertEqual(count($records), 3);
        $this->assertEqual($records[1]->result, 'áéíóúharcoded123.45test');
        // integer fieldnames and values
        $sql = 'SELECT id, ' . $DB->sql_concat('id', "'harcoded'", '?', '?') . ' AS result FROM {' . $tablename . '}';
        $records = $DB->get_records_sql($sql, array(123.45, 'test'));
        $this->assertEqual(count($records), 3);
        $this->assertEqual($records[1]->result, '1harcoded123.45test');
        // all integer fieldnames
        $sql = 'SELECT id, ' . $DB->sql_concat('id', 'id', 'id') . ' AS result FROM {' . $tablename . '}';
        $records = $DB->get_records_sql($sql, array());
        $this->assertEqual(count($records), 3);
        $this->assertEqual($records[1]->result, '111');

    }

    function test_concat_join() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_concat_join("' '", array("?", "?", "?"))." AS fullname ".$DB->sql_null_from_clause();
        $params = array("name", "name2", "name3");
        $result = $DB->get_field_sql($sql, $params);
        $this->assertEqual("name name2 name3", $result);
    }

    function test_sql_fullname() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_fullname(':first', ':last')." AS fullname ".$DB->sql_null_from_clause();
        $params = array('first'=>'Firstname', 'last'=>'Surname');
        $this->assertEqual("Firstname Surname", $DB->get_field_sql($sql, $params));
    }

    function sql_sql_order_by_text() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('description'=>'abcd'));
        $DB->insert_record($tablename, array('description'=>'dxxx'));
        $DB->insert_record($tablename, array('description'=>'bcde'));

        $sql = "SELECT * FROM {{$tablename}} ORDER BY ".$DB->sql_order_by_text('description');
        $records = $DB->get_records_sql($sql);
        $first = array_shift($records);
        $this->assertEqual(1, $first->id);
        $second = array_shift($records);
        $this->assertEqual(3, $second->id);
        $last = array_shift($records);
        $this->assertEqual(2, $last->id);
    }

    function test_sql_substring() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $string = 'abcdefghij';

        $DB->insert_record($tablename, array('name'=>$string));

        $sql = "SELECT id, ".$DB->sql_substr("name", 5)." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql);
        $this->assertEqual(substr($string, 5-1), $record->name);

        $sql = "SELECT id, ".$DB->sql_substr("name", 5, 2)." AS name FROM {{$tablename}}";
        $record = $DB->get_record_sql($sql);
        $this->assertEqual(substr($string, 5-1, 2), $record->name);

        try {
            // silence php warning ;-)
            @$DB->sql_substr("name");
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof coding_exception);
        }
    }

    function test_sql_length() {
        $DB = $this->tdb;
        $this->assertEqual($DB->get_field_sql(
                "SELECT ".$DB->sql_length("'aeiou'").$DB->sql_null_from_clause()), 5);
        $this->assertEqual($DB->get_field_sql(
                "SELECT ".$DB->sql_length("'áéíóú'").$DB->sql_null_from_clause()), 5);
    }

    function test_sql_position() {
        $DB = $this->tdb;
        $this->assertEqual($DB->get_field_sql(
                "SELECT ".$DB->sql_position("'ood'", "'Moodle'").$DB->sql_null_from_clause()), 2);
        $this->assertEqual($DB->get_field_sql(
                "SELECT ".$DB->sql_position("'Oracle'", "'Moodle'").$DB->sql_null_from_clause()), 0);
    }

    function test_sql_empty() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('namenotnull', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'default value');
        $table->add_field('namenotnullnodeflt', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'', 'namenotnull'=>''));
        $DB->insert_record($tablename, array('name'=>null));
        $DB->insert_record($tablename, array('name'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>0));

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE name = '".$DB->sql_empty()."'");
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->name, '');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE namenotnull = '".$DB->sql_empty()."'");
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->namenotnull, '');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE namenotnullnodeflt = '".$DB->sql_empty()."'");
        $this->assertEqual(count($records), 4);
        $record = reset($records);
        $this->assertEqual($record->namenotnullnodeflt, '');
    }

    function test_sql_isempty() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
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
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->name, '');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isempty($tablename, 'namenull', true, false));
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->namenull, '');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isempty($tablename, 'description', false, true));
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->description, '');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isempty($tablename, 'descriptionnull', true, true));
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->descriptionnull, '');
    }

    function test_sql_isnotempty() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
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
        $this->assertEqual(count($records), 3);
        $record = reset($records);
        $this->assertEqual($record->name, '??');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isnotempty($tablename, 'namenull', true, false));
        $this->assertEqual(count($records), 2); // nulls aren't comparable (so they aren't "not empty"). SQL expected behaviour
        $record = reset($records);
        $this->assertEqual($record->namenull, 'la'); // so 'la' is the first non-empty 'namenull' record

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isnotempty($tablename, 'description', false, true));
        $this->assertEqual(count($records), 3);
        $record = reset($records);
        $this->assertEqual($record->description, '??');

        $records = $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE ".$DB->sql_isnotempty($tablename, 'descriptionnull', true, true));
        $this->assertEqual(count($records), 2); // nulls aren't comparable (so they aren't "not empty"). SQL expected behaviour
        $record = reset($records);
        $this->assertEqual($record->descriptionnull, 'lalala'); // so 'lalala' is the first non-empty 'descriptionnull' record
    }

    function test_sql_regex() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('name'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>'holaaa'));
        $DB->insert_record($tablename, array('name'=>'aouch'));

        $sql = "SELECT * FROM {{$tablename}} WHERE name ".$DB->sql_regex()." ?";
        $params = array('a$');
        if ($DB->sql_regex_supported()) {
            $records = $DB->get_records_sql($sql, $params);
            $this->assertEqual(count($records), 2);
        } else {
            $this->assertTrue(true, 'Regexp operations not supported. Test skipped');
        }

        $sql = "SELECT * FROM {{$tablename}} WHERE name ".$DB->sql_regex(false)." ?";
        $params = array('.a');
        if ($DB->sql_regex_supported()) {
            $records = $DB->get_records_sql($sql, $params);
            $this->assertEqual(count($records), 1);
        } else {
            $this->assertTrue(true, 'Regexp operations not supported. Test skipped');
        }

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

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => 3, 'content' => 'hello', 'name'=>'xyz'));
        $DB->insert_record($tablename, array('course' => 3, 'content' => 'world', 'name'=>'abc'));
        $DB->insert_record($tablename, array('course' => 5, 'content' => 'hello', 'name'=>'def'));
        $DB->insert_record($tablename, array('course' => 2, 'content' => 'universe', 'name'=>'abc'));

        // test limits in queries with DISTINCT/ALL clauses and multiple whitespace. MDL-25268
        $sql = "SELECT   DISTINCT   course
                  FROM {{$tablename}}
                 ORDER BY course";
        // only limitfrom
        $records = $DB->get_records_sql($sql, null, 1);
        $this->assertEqual(2, count($records));
        $this->assertEqual(3, reset($records)->course);
        $this->assertEqual(5, next($records)->course);
        // only limitnum
        $records = $DB->get_records_sql($sql, null, 0, 2);
        $this->assertEqual(2, count($records));
        $this->assertEqual(2, reset($records)->course);
        $this->assertEqual(3, next($records)->course);
        // both limitfrom and limitnum
        $records = $DB->get_records_sql($sql, null, 2, 2);
        $this->assertEqual(1, count($records));
        $this->assertEqual(5, reset($records)->course);

        // we have sql like this in moodle, this syntax breaks on older versions of sqlite for example..
        $sql = "SELECT a.id AS id, a.course AS course
                  FROM {{$tablename}} a
                  JOIN (SELECT * FROM {{$tablename}}) b ON a.id = b.id
                 WHERE a.course = ?";

        $records = $DB->get_records_sql($sql, array(3));
        $this->assertEqual(2, count($records));
        $this->assertEqual(1, reset($records)->id);
        $this->assertEqual(2, next($records)->id);

        // do NOT try embedding sql_xxxx() helper functions in conditions array of count_records(), they don't break params/binding!
        $count = $DB->count_records_select($tablename, "course = :course AND ".$DB->sql_compare_text('content')." = :content", array('course' => 3, 'content' => 'hello'));
        $this->assertEqual(1, $count);

        // test int x string comparison
        $sql = "SELECT *
                  FROM {{$tablename}} c
                 WHERE name = ?";
        $this->assertEqual(count($DB->get_records_sql($sql, array(10))), 0);
        $this->assertEqual(count($DB->get_records_sql($sql, array("10"))), 0);
        $DB->insert_record($tablename, array('course' => 7, 'content' => 'xx', 'name'=>'1'));
        $DB->insert_record($tablename, array('course' => 7, 'content' => 'yy', 'name'=>'2'));
        $this->assertEqual(count($DB->get_records_sql($sql, array(1))), 1);
        $this->assertEqual(count($DB->get_records_sql($sql, array("1"))), 1);
        $this->assertEqual(count($DB->get_records_sql($sql, array(10))), 0);
        $this->assertEqual(count($DB->get_records_sql($sql, array("10"))), 0);
        $DB->insert_record($tablename, array('course' => 7, 'content' => 'xx', 'name'=>'1abc'));
        $this->assertEqual(count($DB->get_records_sql($sql, array(1))), 1);
        $this->assertEqual(count($DB->get_records_sql($sql, array("1"))), 1);
    }

    function test_onelevel_commit() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $this->assertEqual(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, $data);
        $this->assertEqual(1, $DB->count_records($tablename));
        $transaction->allow_commit();
        $this->assertEqual(1, $DB->count_records($tablename));
    }

    function test_onelevel_rollback() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // this might in fact encourage ppl to migrate from myisam to innodb

        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $this->assertEqual(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, $data);
        $this->assertEqual(1, $DB->count_records($tablename));
        try {
            $transaction->rollback(new Exception('test'));
            $this->fail('transaction rollback must rethrow exception');
        } catch (Exception $e) {
        }
        $this->assertEqual(0, $DB->count_records($tablename));
    }

    function test_nested_transactions() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // two level commit
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
        $this->assertEqual(2, $DB->count_records($tablename));

        $DB->delete_records($tablename);

        // rollback from top level
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
            $this->assertEqual(get_class($e), 'Exception');
        }
        $this->assertEqual(0, $DB->count_records($tablename));

        $DB->delete_records($tablename);

        // rollback from nested level
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
            $this->assertEqual(get_class($e), 'Exception');
        }
        $this->assertEqual(2, $DB->count_records($tablename)); // not rolled back yet
        try {
            $transaction1->allow_commit();
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'dml_transaction_exception');
        }
        $this->assertEqual(2, $DB->count_records($tablename)); // not rolled back yet
        // the forced rollback is done from the default_exception handler and similar places,
        // let's do it manually here
        $this->assertTrue($DB->is_transaction_started());
        $DB->force_transaction_rollback();
        $this->assertFalse($DB->is_transaction_started());
        $this->assertEqual(0, $DB->count_records($tablename)); // finally rolled back

        $DB->delete_records($tablename);
    }

    function test_transactions_forbidden() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->transactions_forbidden();
        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>1);
        $DB->insert_record($tablename, $data);
        try {
            $DB->transactions_forbidden();
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'dml_transaction_exception');
        }
        // the previous test does not force rollback
        $transaction->allow_commit();
        $this->assertFalse($DB->is_transaction_started());
        $this->assertEqual(1, $DB->count_records($tablename));
    }

    function test_wrong_transactions() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);


        // wrong order of nested commits
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>4);
        $DB->insert_record($tablename, $data);
        try {
            $transaction1->allow_commit();
            $this->fail('wrong order of commits must throw exception');
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'dml_transaction_exception');
        }
        try {
            $transaction2->allow_commit();
            $this->fail('first wrong commit forces rollback');
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'dml_transaction_exception');
        }
        // this is done in default exception handler usually
        $this->assertTrue($DB->is_transaction_started());
        $this->assertEqual(2, $DB->count_records($tablename)); // not rolled back yet
        $DB->force_transaction_rollback();
        $this->assertEqual(0, $DB->count_records($tablename));
        $DB->delete_records($tablename);


        // wrong order of nested rollbacks
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>4);
        $DB->insert_record($tablename, $data);
        try {
            // this first rollback should prevent all other rollbacks
            $transaction1->rollback(new Exception('test'));
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'Exception');
        }
        try {
            $transaction2->rollback(new Exception('test'));
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'Exception');
        }
        try {
            $transaction1->rollback(new Exception('test'));
        } catch (Exception $e) {
            // the rollback was used already once, no way to use it again
            $this->assertEqual(get_class($e), 'dml_transaction_exception');
        }
        // this is done in default exception handler usually
        $this->assertTrue($DB->is_transaction_started());
        $DB->force_transaction_rollback();
        $DB->delete_records($tablename);


        // unknown transaction object
        $transaction1 = $DB->start_delegated_transaction();
        $data = (object)array('course'=>3);
        $DB->insert_record($tablename, $data);
        $transaction2 = new moodle_transaction($DB);
        try {
            $transaction2->allow_commit();
            $this->fail('foreign transaction must fail');
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'dml_transaction_exception');
        }
        try {
            $transaction1->allow_commit();
            $this->fail('first wrong commit forces rollback');
        } catch (Exception $e) {
            $this->assertEqual(get_class($e), 'dml_transaction_exception');
        }
        $DB->force_transaction_rollback();
        $DB->delete_records($tablename);
    }

    function test_concurent_transactions() {
        // Notes about this test:
        // 1- MySQL needs to use one engine with transactions support (InnoDB).
        // 2- MSSQL needs to have enabled versioning for read committed
        //    transactions (ALTER DATABASE xxx SET READ_COMMITTED_SNAPSHOT ON)
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $transaction = $DB->start_delegated_transaction();
        $data = (object)array('course'=>1);
        $this->assertEqual(0, $DB->count_records($tablename));
        $DB->insert_record($tablename, $data);
        $this->assertEqual(1, $DB->count_records($tablename));

        //open second connection
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = array();
        }
        $DB2 = moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $DB2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        // second instance should not see pending inserts
        $this->assertEqual(0, $DB2->count_records($tablename));
        $data = (object)array('course'=>2);
        $DB2->insert_record($tablename, $data);
        $this->assertEqual(1, $DB2->count_records($tablename));

        // first should see the changes done from second
        $this->assertEqual(2, $DB->count_records($tablename));

        // now commit and we should see it finally in second connections
        $transaction->allow_commit();
        $this->assertEqual(2, $DB2->count_records($tablename));

        $DB2->dispose();
    }

    public function test_bound_param_types() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertTrue($DB->insert_record($tablename, array('name' => '1', 'content'=>'xx')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 2, 'content'=>'yy')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'somestring', 'content'=>'zz')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'aa', 'content'=>'1')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'bb', 'content'=>2)));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'cc', 'content'=>'sometext')));


        // Conditions in CHAR columns
        $this->assertTrue($DB->record_exists($tablename, array('name'=>1)));
        $this->assertTrue($DB->record_exists($tablename, array('name'=>'1')));
        $this->assertFalse($DB->record_exists($tablename, array('name'=>111)));
        $this->assertTrue($DB->get_record($tablename, array('name'=>1)));
        $this->assertTrue($DB->get_record($tablename, array('name'=>'1')));
        $this->assertFalse($DB->get_record($tablename, array('name'=>111)));
        $sqlqm = "SELECT *
                    FROM {{$tablename}}
                   WHERE name = ?";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, array(1)));
        $this->assertEqual(1, count($records));
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, array('1')));
        $this->assertEqual(1, count($records));
        $records = $DB->get_records_sql($sqlqm, array(222));
        $this->assertEqual(0, count($records));
        $sqlnamed = "SELECT *
                       FROM {{$tablename}}
                      WHERE name = :name";
        $this->assertTrue($records = $DB->get_records_sql($sqlnamed, array('name' => 2)));
        $this->assertEqual(1, count($records));
        $this->assertTrue($records = $DB->get_records_sql($sqlnamed, array('name' => '2')));
        $this->assertEqual(1, count($records));

        // Conditions in TEXT columns always must be performed with the sql_compare_text
        // helper function on both sides of the condition
        $sqlqm = "SELECT *
                    FROM {{$tablename}}
                   WHERE " . $DB->sql_compare_text('content') . " =  " . $DB->sql_compare_text('?');
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, array('1')));
        $this->assertEqual(1, count($records));
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, array(1)));
        $this->assertEqual(1, count($records));
        $sqlnamed = "SELECT *
                       FROM {{$tablename}}
                      WHERE " . $DB->sql_compare_text('content') . " =  " . $DB->sql_compare_text(':content');
        $this->assertTrue($records = $DB->get_records_sql($sqlnamed, array('content' => 2)));
        $this->assertEqual(1, count($records));
        $this->assertTrue($records = $DB->get_records_sql($sqlnamed, array('content' => '2')));
        $this->assertEqual(1, count($records));
    }

    public function test_bound_param_reserved() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $DB->insert_record($tablename, array('course' => '1'));

        // make sure reserved words do not cause fatal problems in query parameters

        $DB->execute("UPDATE {{$tablename}} SET course = 1 WHERE ID = :select", array('select'=>1));
        $DB->get_records_sql("SELECT * FROM {{$tablename}} WHERE course = :select", array('select'=>1));
        $rs = $DB->get_recordset_sql("SELECT * FROM {{$tablename}} WHERE course = :select", array('select'=>1));
        $rs->close();
        $DB->get_fieldset_sql("SELECT id FROM {{$tablename}} WHERE course = :select", array('select'=>1));
        $DB->set_field_select($tablename, 'course', '1', "id = :select", array('select'=>1));
        $DB->delete_records_select($tablename, "id = :select", array('select'=>1));
    }

    public function test_limits_and_offsets() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        if (false) $DB = new moodle_database ();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertTrue($DB->insert_record($tablename, array('name' => 'a', 'content'=>'one')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'b', 'content'=>'two')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'c', 'content'=>'three')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'd', 'content'=>'four')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'e', 'content'=>'five')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'f', 'content'=>'six')));

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 4));
        $this->assertEqual(2, count($records));
        $this->assertEqual('e', reset($records)->name);
        $this->assertEqual('f', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertFalse($records = $DB->get_records_sql($sqlqm, null, 8));

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 0, 4));
        $this->assertEqual(4, count($records));
        $this->assertEqual('a', reset($records)->name);
        $this->assertEqual('d', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 0, 8));
        $this->assertEqual(6, count($records));
        $this->assertEqual('a', reset($records)->name);
        $this->assertEqual('f', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 1, 4));
        $this->assertEqual(4, count($records));
        $this->assertEqual('b', reset($records)->name);
        $this->assertEqual('e', end($records)->name);

        $sqlqm = "SELECT *
                    FROM {{$tablename}}";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 4, 4));
        $this->assertEqual(2, count($records));
        $this->assertEqual('e', reset($records)->name);
        $this->assertEqual('f', end($records)->name);

        $sqlqm = "SELECT t.*, t.name AS test
                    FROM {{$tablename}} t
                    ORDER BY t.id ASC";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 4, 4));
        $this->assertEqual(2, count($records));
        $this->assertEqual('e', reset($records)->name);
        $this->assertEqual('f', end($records)->name);

        $sqlqm = "SELECT DISTINCT t.name, t.name AS test
                    FROM {{$tablename}} t
                    ORDER BY t.name DESC";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 4, 4));
        $this->assertEqual(2, count($records));
        $this->assertEqual('b', reset($records)->name);
        $this->assertEqual('a', end($records)->name);

        $sqlqm = "SELECT 1
                    FROM {{$tablename}} t
                    WHERE t.name = 'a'";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 0, 1));
        $this->assertEqual(1, count($records));

        $sqlqm = "SELECT 'constant'
                    FROM {{$tablename}} t
                    WHERE t.name = 'a'";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 0, 8));
        $this->assertEqual(1, count($records));

        $this->assertTrue($DB->insert_record($tablename, array('name' => 'a', 'content'=>'one')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'b', 'content'=>'two')));
        $this->assertTrue($DB->insert_record($tablename, array('name' => 'c', 'content'=>'three')));

        $sqlqm = "SELECT t.name, COUNT(DISTINCT t2.id) AS count, 'Test' AS teststring
                    FROM {{$tablename}} t
                    LEFT JOIN (
                        SELECT t.id, t.name
                        FROM {{$tablename}} t
                    ) t2 ON t2.name = t.name
                    GROUP BY t.name
                    ORDER BY t.name ASC";
        $this->assertTrue($records = $DB->get_records_sql($sqlqm));
        $this->assertEqual(6, count($records));         // a,b,c,d,e,f
        $this->assertEqual(2, reset($records)->count);  // a has 2 records now
        $this->assertEqual(1, end($records)->count);    // f has 1 record still

        $this->assertTrue($records = $DB->get_records_sql($sqlqm, null, 0, 2));
        $this->assertEqual(2, count($records));
        $this->assertEqual(2, reset($records)->count);
        $this->assertEqual(2, end($records)->count);
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

    public function driver_installed(){}
    public function get_dbfamily(){}
    protected function get_dbtype(){}
    protected function get_dblibrary(){}
    public function get_name(){}
    public function get_configuration_help(){}
    public function get_configuration_hints(){}
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null){}
    public function get_server_info(){}
    protected function allowed_param_types(){}
    public function get_last_error(){}
    public function get_tables($usecache=true){}
    public function get_indexes($table){}
    public function get_columns($table, $usecache=true){}
    protected function normalise_value($column, $value){}
    public function set_debug($state){}
    public function get_debug(){}
    public function set_logging($state){}
    public function change_database_structure($sql){}
    public function execute($sql, array $params=null){}
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0){}
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0){}
    public function get_fieldset_sql($sql, array $params=null){}
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false){}
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false){}
    public function import_record($table, $dataobject){}
    public function update_record_raw($table, $params, $bulk=false){}
    public function update_record($table, $dataobject, $bulk=false){}
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null){}
    public function delete_records_select($table, $select, array $params=null){}
    public function sql_concat(){}
    public function sql_concat_join($separator="' '", $elements=array()){}
    public function sql_substr($expr, $start, $length=false){}
    public function begin_transaction() {}
    public function commit_transaction() {}
    public function rollback_transaction() {}
}
