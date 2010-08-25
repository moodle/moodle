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
 * @package    moodlecore
 * @subpackage DML
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class dml_test extends UnitTestCase {
    private $tables = array();
    private $tdb;
    private $data;
    public  static $includecoverage = array('lib/dml');
    public  static $excludecoverage = array('lib/dml/simpletest');

    function setUp() {
        global $CFG, $DB, $UNITTEST;

        if (isset($UNITTEST->func_test_db)) {
            $this->tdb = $UNITTEST->func_test_db;
        } else {
            $this->tdb = $DB;
        }

    }

    function tearDown() {
        $dbman = $this->tdb->get_manager();

        foreach ($this->tables as $table) {
            if ($dbman->table_exists($table)) {
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
     * @param string $tablename the name of the table to create.
     * @return xmldb_table the table object.
     */
    private function get_test_table($tablename="") {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        if ($tablename == '') {
            $tablename = "unit_table";
        }

        $table = new xmldb_table($tablename);
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        return new xmldb_table($tablename);
    }

    function test_fix_sql_params() {
        $DB = $this->tdb;

        $table = $this->get_test_table();
        $tablename = $table->getName();

        // Correct table placeholder substitution
        $sql = "SELECT * FROM {".$tablename."}";
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
        $this->assertEqual($rsql, $sql[$rtype]);
        $this->assertEqual($rparams, $params[$rtype]);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_QM], $params[SQL_PARAMS_QM]);
        $this->assertEqual($rsql, $sql[$rtype]);
        $this->assertEqual($rparams, $params[$rtype]);

        list($rsql, $rparams, $rtype) = $DB->fix_sql_params($sql[SQL_PARAMS_DOLLAR], $params[SQL_PARAMS_DOLLAR]);
        $this->assertEqual($rsql, $sql[$rtype]);
        $this->assertEqual($rparams, $params[$rtype]);


        // Malformed table placeholder
        $sql = "SELECT * FROM [testtable]";
        $sqlarray = $DB->fix_sql_params($sql);
        $this->assertEqual($sql, $sqlarray[0]);


        // Mixed param types (colon and dollar)
        $sql = "SELECT * FROM {".$tablename."} WHERE name = :param1, course = \$1";
        $params = array('param1' => 'record1', 'param2' => 3);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Mixed param types (question and dollar)
        $sql = "SELECT * FROM {".$tablename."} WHERE name = ?, course = \$1";
        $params = array('param1' => 'record2', 'param2' => 5);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Too many params in sql
        $sql = "SELECT * FROM {".$tablename."} WHERE name = ?, course = ?, id = ?";
        $params = array('record2', 3);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Too many params in array: no error
        $params[] = 1;
        $params[] = time();
        $sqlarray = null;

        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->pass();
        } catch (Exception $e) {
            $this->fail("Unexpected ".get_class($e)." exception");
        }
        $this->assertTrue($sqlarray[0]);

        // Named params missing from array
        $sql = "SELECT * FROM {".$tablename."} WHERE name = :name, course = :course";
        $params = array('wrongname' => 'record1', 'course' => 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Duplicate named param in query
        $sql = "SELECT * FROM {".$tablename."} WHERE name = :name, course = :name";
        $params = array('name' => 'record2', 'course' => 3);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

    }

    public function testGetTables() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        // Need to test with multiple DBs
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $original_count = count($DB->get_tables());

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $this->assertTrue(count($DB->get_tables()) == $original_count + 1);
    }

    public function testDefaults() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('enumfield', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'test2');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $columns = $DB->get_columns($tablename);

        $enumfield = $columns['enumfield'];
        $this->assertEqual('test2', $enumfield->default_value);
        $this->assertEqual('C', $enumfield->meta_type);

    }

    public function testGetIndexes() {
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
        $this->tables[$tablename] = $table;

        $this->assertTrue($indices = $DB->get_indexes($tablename));
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

    public function testGetColumns() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $this->assertTrue($columns = $DB->get_columns($tablename));
        $fields = $this->tables[$tablename]->getFields();
        $this->assertEqual(count($columns), count($fields));

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

    public function testExecute() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $sql = "SELECT * FROM {".$tablename."}";

        $this->assertTrue($DB->execute($sql));

        $params = array('course' => 1, 'name' => 'test');

        $sql = "INSERT INTO {".$tablename."} (".implode(',', array_keys($params)).")
                       VALUES (".implode(',', array_fill(0, count($params), '?')).")";


        $this->assertTrue($DB->execute($sql, $params));

        $record = $DB->get_record($tablename, array('id' => 1));

        foreach ($params as $field => $value) {
            $this->assertEqual($value, $record->$field, "Field $field in DB ({$record->$field}) is not equal to field $field in sql ($value)");
        }
    }

    public function test_get_in_or_equal() {
        $DB = $this->tdb;

        // SQL_PARAMS_QM - IN or =

        // Correct usage of multiple values
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values);
        $this->assertEqual("IN (?,?,?,?)", $usql);
        $this->assertEqual(4, count($params));
        foreach ($params as $key => $value) {
            $this->assertEqual($in_values[$key], $value);
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
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param01', true);
        $this->assertEqual("IN (:param01,:param02,:param03,:param04)", $usql);
        $this->assertEqual(4, count($params));
        reset($in_values);
        foreach ($params as $key => $value) {
            $this->assertEqual(current($in_values), $value);
            next($in_values);
        }

        // Correct usage of single values (in array)
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param01', true);
        $this->assertEqual("= :param01", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_values[0], $params['param01']);

        // Correct usage of single value
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param01', true);
        $this->assertEqual("= :param01", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_value, $params['param01']);

        // SQL_PARAMS_NAMED - NOT IN or <>

        // Correct usage of multiple values
        $in_values = array('value1', 'value2', 'value3', 'value4');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param01', false);
        $this->assertEqual("NOT IN (:param01,:param02,:param03,:param04)", $usql);
        $this->assertEqual(4, count($params));
        reset($in_values);
        foreach ($params as $key => $value) {
            $this->assertEqual(current($in_values), $value);
            next($in_values);
        }

        // Correct usage of single values (in array)
        $in_values = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param01', false);
        $this->assertEqual("<> :param01", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_values[0], $params['param01']);

        // Correct usage of single value
        $in_value = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($in_values, SQL_PARAMS_NAMED, 'param01', false);
        $this->assertEqual("<> :param01", $usql);
        $this->assertEqual(1, count($params));
        $this->assertEqual($in_value, $params['param01']);

    }

    public function test_fix_table_names() {
        $DB = new moodle_database_for_testing();
        $prefix = $DB->get_prefix();

        // Simple placeholder
        $placeholder = "{user}";
        $this->assertEqual($prefix."user", $DB->public_fix_table_names($placeholder));

        // Full SQL
        $sql = "SELECT * FROM {user}, {funny_table_name}, {mdl_stupid_table} WHERE {user}.id = {funny_table_name}.userid";
        $expected = "SELECT * FROM {$prefix}user, {$prefix}funny_table_name, {$prefix}mdl_stupid_table WHERE {$prefix}user.id = {$prefix}funny_table_name.userid";
        $this->assertEqual($expected, $DB->public_fix_table_names($sql));


    }

    public function test_get_recordset() {
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
        $this->tables[$tablename] = $table;

        $data = array(array('id' => 1, 'course' => 3, 'name' => 'record1'),
                      array('id' => 2, 'course' => 3, 'name' => 'record2'),
                      array('id' => 3, 'course' => 5, 'name' => 'record3'));

        foreach ($data as $record) {
            $DB->insert_record($tablename, $record);
        }

        $rs = $DB->get_recordset($tablename);
        $this->assertTrue($rs);

        reset($data);
        foreach($rs as $record) {
            $data_record = current($data);
            foreach ($record as $k => $v) {
                $this->assertEqual($data_record[$k], $v);
            }
            next($data);
        }
        $rs->close();

        // note: delegate limits testing to test_get_recordset_sql()
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
        $this->tables[$tablename] = $table;

        $data = array(array('id'=> 1, 'course' => 3, 'name' => 'record1'),
                      array('id'=> 2, 'course' => 3, 'name' => 'record2'),
                      array('id'=> 3, 'course' => 5, 'name' => 'record3'));
        foreach ($data as $record) {
            $DB->insert_record($tablename, $record);
        }

    /// Test repeated numeric keys are returned ok
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

    /// Test record returned are ok
        $this->assertEqual($count, 3);

    /// Test string keys are returned ok
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

    /// Test record returned are ok
        $this->assertEqual($count, 3);

    /// Test numeric not starting in 1 keys are returned ok
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

    /// Test record returned are ok
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $rs = $DB->get_recordset_list($tablename, 'course', array(3, 2));

        $this->assertTrue($rs);

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

        // note: delegate limits testing to test_get_recordset_sql()
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
        $this->tables[$tablename] = $table;

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

        // note: delegate limits testing to test_get_recordset_sql()
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
        $this->tables[$tablename] = $table;

        $inskey1 = $DB->insert_record($tablename, array('course' => 3));
        $inskey2 = $DB->insert_record($tablename, array('course' => 5));
        $inskey3 = $DB->insert_record($tablename, array('course' => 4));
        $inskey4 = $DB->insert_record($tablename, array('course' => 3));
        $inskey5 = $DB->insert_record($tablename, array('course' => 2));
        $inskey6 = $DB->insert_record($tablename, array('course' => 1));
        $inskey7 = $DB->insert_record($tablename, array('course' => 0));

        $this->assertTrue($rs = $DB->get_recordset_sql("SELECT * FROM {".$tablename."} WHERE course = ?", array(3)));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $rs->close();
        $this->assertEqual(2, $counter);

        // limits - only need to test this case, the rest have been tested by test_get_records_sql()
        // only limitfrom = skips that number of records
        $rs = $DB->get_recordset_sql("SELECT * FROM {".$tablename."} ORDER BY id", null, 2, 0);
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
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

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
        $this->assertTrue(empty($records[1]->course));
        $this->assertFalse(empty($records[1]->id));
        $this->assertEqual(4, count($records));

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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($records = $DB->get_records_list($tablename, 'course', array(3, 2)));
        $this->assertEqual(3, count($records));
        $this->assertEqual(1, reset($records)->id);
        $this->assertEqual(2, next($records)->id);
        $this->assertEqual(4, next($records)->id);

        $this->assertIdentical(array(), $records = $DB->get_records_list($tablename, 'course', array())); /// Must return 0 rows without conditions. MDL-17645
        $this->assertEqual(0, count($records));

        // note: delegate limits testing to test_get_records_sql()
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($record = $DB->get_record_select($tablename, "id = ?", array(2)));

        $this->assertEqual(2, $record->course);

        // note: delegates limit testing to test_get_records_sql()
    }

    public function test_get_records_sql() {
        global $CFG;
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $inskey1 = $DB->insert_record($tablename, array('course' => 3));
        $inskey2 = $DB->insert_record($tablename, array('course' => 5));
        $inskey3 = $DB->insert_record($tablename, array('course' => 4));
        $inskey4 = $DB->insert_record($tablename, array('course' => 3));
        $inskey5 = $DB->insert_record($tablename, array('course' => 2));
        $inskey6 = $DB->insert_record($tablename, array('course' => 1));
        $inskey7 = $DB->insert_record($tablename, array('course' => 0));

        $this->assertTrue($records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE course = ?", array(3)));
        $this->assertEqual(2, count($records));
        $this->assertEqual($inskey1, reset($records)->id);
        $this->assertEqual($inskey4, next($records)->id);

        // Awful test, requires debug enabled and sent to browser. Let's do that and restore after test
        $olddebug   = $CFG->debug;       // Save current debug settings
        $olddisplay = $CFG->debugdisplay;
        $CFG->debug = DEBUG_DEVELOPER;
        $CFG->debugdisplay = true;
        ob_start(); // hide debug warning
        $records = $DB->get_records_sql("SELECT course AS id, course AS course FROM {".$tablename."}", null);
        ob_end_clean();
        $debuginfo = ob_get_contents();
        $CFG->debug = $olddebug;         // Restore original debug settings
        $CFG->debugdisplay = $olddisplay;

        $this->assertEqual(6, count($records));
        $this->assertFalse($debuginfo === '');

        // negative limits = no limits
        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} ORDER BY id", null, -1, -1);
        $this->assertEqual(7, count($records));

        // zero limits = no limits
        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} ORDER BY id", null, 0, 0);
        $this->assertEqual(7, count($records));

        // only limitfrom = skips that number of records
        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} ORDER BY id", null, 2, 0);
        $this->assertEqual(5, count($records));
        $this->assertEqual($inskey3, reset($records)->id);
        $this->assertEqual($inskey7, end($records)->id);

        // only limitnum = fetches that number of records
        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} ORDER BY id", null, 0, 3);
        $this->assertEqual(3, count($records));
        $this->assertEqual($inskey1, reset($records)->id);
        $this->assertEqual($inskey3, end($records)->id);

        // both limitfrom and limitnum = skips limitfrom records and fetches limitnum ones
        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} ORDER BY id", null, 3, 2);
        $this->assertEqual(2, count($records));
        $this->assertEqual($inskey4, reset($records)->id);
        $this->assertEqual($inskey5, end($records)->id);

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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($records = $DB->get_records_menu($tablename, array('course' => 3)));
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertTrue($records = $DB->get_records_select_menu($tablename, "course > ?", array(2)));

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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertTrue($records = $DB->get_records_sql_menu("SELECT * FROM {".$tablename."} WHERE course > ?", array(2)));

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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($record = $DB->get_record($tablename, array('id' => 2)));

        $this->assertEqual(2, $record->course);
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));

        $this->assertTrue($record = $DB->get_record_sql("SELECT * FROM {".$tablename."} WHERE id = ?", array(2)));

        $this->assertEqual(2, $record->course);

        // backwards compatibility with $ignoremultiple
        $this->assertFalse(IGNORE_MISSING);
        $this->assertTrue(IGNORE_MULTIPLE);

        // record not found
        $this->assertFalse($record = $DB->get_record_sql("SELECT * FROM {".$tablename."} WHERE id = ?", array(666), IGNORE_MISSING));
        $this->assertFalse($record = $DB->get_record_sql("SELECT * FROM {".$tablename."} WHERE id = ?", array(666), IGNORE_MULTIPLE));
        try {
            $DB->get_record_sql("SELECT * FROM {".$tablename."} WHERE id = ?", array(666), MUST_EXIST);
            $this->fail("Exception expected");
        } catch (dml_missing_record_exception $e) {
            $this->assertTrue(true);
        }

        // multiple matches
        ob_start(); // hide debug warning
        $this->assertTrue($record = $DB->get_record_sql("SELECT * FROM {".$tablename."}", array(), IGNORE_MISSING));
        ob_end_clean();
        $this->assertTrue($record = $DB->get_record_sql("SELECT * FROM {".$tablename."}", array(), IGNORE_MULTIPLE));
        try {
            $DB->get_record_sql("SELECT * FROM {".$tablename."}", array(), MUST_EXIST);
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
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($course = $DB->get_field($tablename, 'course', array('id' => 1)));
        $this->assertEqual(3, $course);

        // Add one get_field() example where the field being get is also one condition
        // to check we haven't problem with any type of param (specially NAMED ones)
        $DB->get_field($tablename, 'course', array('course' => 3));
        $this->assertEqual(3, $DB->get_field($tablename, 'course', array('course' => 3)));
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($course = $DB->get_field_select($tablename, 'course', "id = ?", array(1)));
        $this->assertEqual(3, $course);

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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($course = $DB->get_field_sql("SELECT course FROM {".$tablename."} WHERE id = ?", array(1)));
        $this->assertEqual(3, $course);

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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 6));

        $this->assertTrue($fieldset = $DB->get_fieldset_select($tablename, 'course', "course > ?", array(1)));

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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 1));
        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 2));
        $DB->insert_record($tablename, array('course' => 6));

        $this->assertTrue($fieldset = $DB->get_fieldset_sql("SELECT * FROM {".$tablename."} WHERE course > ?", array(1)));

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
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $this->assertTrue($DB->insert_record_raw($tablename, array('course' => 1)));
        $this->assertTrue($record = $DB->get_record($tablename, array('course' => 1)));
        $this->assertEqual(1, $record->course);
    }

    public function test_insert_record() {

        // All the information in this test is fetched from DB by get_recordset() so we
        // have such method properly tested against nulls, empties and friends...

        global $CFG;

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
        $this->tables[$tablename] = $table;

        $this->assertTrue($DB->insert_record($tablename, array('course' => 1), false)); // Without returning id
        $rs = $DB->get_recordset($tablename, array('course' => 1));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual(1, $record->id);
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
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertNull($record->oneint);
        $this->assertNull($record->onenum);
        $this->assertNull($record->onechar);
        $this->assertNull($record->onetext);
        $this->assertNull($record->onebinary);

        // Check zeros are set properly for all types
        $record->oneint = 0;
        $record->onenum = 0;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual(0, $record->oneint);
        $this->assertEqual(0, $record->onenum);

        // Check booleans are set properly for all types
        $record->oneint = true; // trues
        $record->onenum = true;
        $record->onechar = true;
        $record->onetext = true;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual(1, $record->oneint);
        $this->assertEqual(1, $record->onenum);
        $this->assertEqual(1, $record->onechar);
        $this->assertEqual(1, $record->onetext);

        $record->oneint = false; // falses
        $record->onenum = false;
        $record->onechar = false;
        $record->onetext = false;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual(0, $record->oneint);
        $this->assertEqual(0, $record->onenum);
        $this->assertEqual(0, $record->onechar);
        $this->assertEqual(0, $record->onetext);

        // Check string data causes exception in numeric types
        $record->oneint = 'onestring';
        $record->onenum = 0;
        try {
            $DB->insert_record($tablename, $record);
            $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }
        $record->oneint = 0;
        $record->onenum = 'onestring';
        try {
           $DB->insert_record($tablename, $record);
           $this->fail("Expecting an exception, none occurred");
        } catch (exception $e) {
            $this->assertTrue($e instanceof dml_exception);
        }

        // Check empty string data is stored as 0 in numeric datatypes
        $record->oneint = ''; // empty string
        $record->onenum = 0;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertTrue(is_numeric($record->oneint) && $record->oneint == 0);

        $record->oneint = 0;
        $record->onenum = ''; // empty string
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertTrue(is_numeric($record->onenum) && $record->onenum == 0);

        // Check empty strings are set properly in string types
        $record->oneint = 0;
        $record->onenum = 0;
        $record->onechar = '';
        $record->onetext = '';
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertTrue($record->onechar === '');
        $this->assertTrue($record->onetext === '');

        // Check operation ((210.10 + 39.92) - 150.02) against numeric types
        $record->oneint = ((210.10 + 39.92) - 150.02);
        $record->onenum = ((210.10 + 39.92) - 150.02);
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
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
            $recid = $DB->insert_record($tablename, $record);
            $rs = $DB->get_recordset($tablename, array('id' => $recid));
            $record = $rs->current();
            $rs->close();
            $this->assertEqual($teststring, $record->onechar);
            $this->assertEqual($teststring, $record->onetext);
        }

        // Check LOBs in text/binary columns
        $clob = file_get_contents($CFG->libdir.'/dml/simpletest/fixtures/clob.txt');
        $blob = file_get_contents($CFG->libdir.'/dml/simpletest/fixtures/randombinary');
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
        $record->onetext = $newclob;
        $record->onebinary = $newblob;
        $recid = $DB->insert_record($tablename, $record);
        $rs = $DB->get_recordset($tablename, array('id' => $recid));
        $record = $rs->current();
        $rs->close();
        $this->assertEqual($newclob, $record->onetext, 'Test "small" CLOB insert (full contents output disabled)');
        $this->assertEqual($newblob, $record->onebinary, 'Test "small" BLOB insert (full contents output disabled)');
        $this->assertEqual(false, $rs->key()); // Ensure recordset key() method to be working ok after closing

        // test data is not modified
        $rec = new object();
        $rec->id     = -1; // has to be ignored
        $rec->course = 3;
        $rec->lalala = 'lalal'; // unused
        $before = clone($rec);
        $DB->insert_record($tablename, $record);
        $this->assertEqual($rec, $before);
    }

    public function test_import_record() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $record = (object)array('id'=>666, 'course'=>10);
        $this->assertTrue($DB->import_record($tablename, $record));
        $records = $DB->get_records($tablename);
        $this->assertEqual(1, count($records));
        $this->assertEqual(10, $records[666]->course);

        $record = (object)array('id'=>13, 'course'=>2);
        $this->assertTrue($DB->import_record($tablename, $record));
        $records = $DB->get_records($tablename);
        $this->assertEqual(2, $records[13]->course);
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 1));
        $record = $DB->get_record($tablename, array('course' => 1));
        $record->course = 2;
        $this->assertTrue($DB->update_record_raw($tablename, $record));
        $this->assertFalse($record = $DB->get_record($tablename, array('course' => 1)));
        $this->assertTrue($record = $DB->get_record($tablename, array('course' => 2)));
    }

    public function test_update_record() {

        // All the information in this test is fetched from DB by get_record() so we
        // have such method properly tested against nulls, empties and friends...

        global $CFG;

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
        $this->tables[$tablename] = $table;

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
        $clob = file_get_contents($CFG->libdir.'/dml/simpletest/fixtures/clob.txt');
        $blob = file_get_contents($CFG->libdir.'/dml/simpletest/fixtures/randombinary');
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
    }

    public function test_set_field() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 1));

        $this->assertTrue($DB->set_field($tablename, 'course', 2, array('id' => 1)));
        $this->assertEqual(2, $DB->get_field($tablename, 'course', array('id' => 1)));

        // Add one set_field() example where the field being set is also one condition
        // to check we haven't problem with any type of param (specially NAMED ones)
        $DB->set_field($tablename, 'course', '1', array('course' => 2));
        $this->assertEqual(1, $DB->get_field($tablename, 'course', array('id' => 1)));

        // Note: All the nulls, booleans, empties, quoted and backslashes tests
        // go to set_field_select() because set_field() is just one wrapper over it
    }

    public function test_set_field_select() {

        // All the information in this test is fetched from DB by get_field() so we
        // have such method properly tested against nulls, empties and friends...

        global $CFG;

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
        $this->tables[$tablename] = $table;

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
        $clob = file_get_contents($CFG->libdir.'/dml/simpletest/fixtures/clob.txt');
        $blob = file_get_contents($CFG->libdir.'/dml/simpletest/fixtures/randombinary');
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
    }

    public function test_count_records() {
        $DB = $this->tdb;

        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $this->assertEqual(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertEqual(3, $DB->count_records($tablename));
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
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;

        $this->assertEqual(0, $DB->count_records($tablename));

        $DB->insert_record($tablename, array('course' => 3));
        $DB->insert_record($tablename, array('course' => 4));
        $DB->insert_record($tablename, array('course' => 5));

        $this->assertEqual(2, $DB->count_records_sql("SELECT COUNT(*) FROM {".$tablename."} WHERE course > ?", array(3)));
    }

    public function test_record_exists() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $this->assertEqual(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists($tablename, array('course' => 3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists($tablename, array('course' => 3)));

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
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;

        $this->assertEqual(0, $DB->count_records($tablename));

        $this->assertFalse($DB->record_exists_sql("SELECT * FROM {".$tablename."} WHERE course = ?", array(3)));
        $DB->insert_record($tablename, array('course' => 3));

        $this->assertTrue($DB->record_exists_sql("SELECT * FROM {".$tablename."} WHERE course = ?", array(3)));
    }

    public function test_delete_records() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;

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
        $sql = "SELECT ".$DB->sql_bitand(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 2);
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
        $sql = "SELECT ".$DB->sql_bitor(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 11);
    }

    function test_sql_bitxor() {
        $DB = $this->tdb;
        $sql = "SELECT ".$DB->sql_bitxor(10, 3)." AS res ".$DB->sql_null_from_clause();
        $this->assertEqual($DB->get_field_sql($sql), 9);
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

        $table1 = $this->get_test_table("testtable1");
        $tablename1 = $table1->getName();

        $table1->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table1->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table1->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table1);
        $this->tables[$tablename1] = $table1;

        $DB->insert_record($tablename1, array('name'=>'100'));

        $table2 = $this->get_test_table("testtable2");
        $tablename2 = $table2->getName();
        $table2->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table2->add_field('res', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table2);
        $this->tables[$table2->getName()] = $table2;

        $DB->insert_record($tablename2, array('res'=>100));

        try {
            $sql = "SELECT * FROM {".$tablename1."} t1, {".$tablename2."} t2 WHERE ".$DB->sql_cast_char2int("t1.name")." = t2.res ";
            $records = $DB->get_records_sql($sql);
            $this->assertEqual(count($records), 1);
        } catch (dml_exception $e) {
            $this->fail("No exception expected");
        }
    }

    function test_cast_char2real() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('res', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'10.10', 'res'=>5.1));
        $DB->insert_record($tablename, array('name'=>'1.10', 'res'=>666));
        $DB->insert_record($tablename, array('name'=>'11.10', 'res'=>0.1));

        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_cast_char2real('name')." > res";
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 2);
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'abcd',   'description'=>'abcd'));
        $DB->insert_record($tablename, array('name'=>'abcdef', 'description'=>'bbcdef'));
        $DB->insert_record($tablename, array('name'=>'aaaabb', 'description'=>'aaaacccccccccccccccccc'));

        $sql = "SELECT * FROM {".$tablename."} WHERE name = ".$DB->sql_compare_text('description');
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {".$tablename."} WHERE name = ".$DB->sql_compare_text('description', 4);
        $records = $DB->get_records_sql($sql);
        $this->assertEqual(count($records), 2);
    }

    function test_unique_binary() {
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
        $this->tables[$tablename] = $table;

        try {
            $DB->insert_record($tablename, array('name'=>'aaa'));
            $DB->insert_record($tablename, array('name'=>'aäa'));
            $DB->insert_record($tablename, array('name'=>'aáa'));
            $DB->insert_record($tablename, array('name'=>'AAA'));
            $DB->insert_record($tablename, array('name'=>'aÄa'));
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->fail("Collation uniqueness problem detected - default collation is expected to be case sensitive and accent sensitive.");
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
        $table->add_field('descr', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'aaa', 'descr'=>'Aaa'));
        $DB->insert_record($tablename, array('name'=>'aáa', 'descr'=>'ááá'));
        $DB->insert_record($tablename, array('name'=>'aäa', 'descr'=>'aäa'));
        $DB->insert_record($tablename, array('name'=>'AAA', 'descr'=>'AAA'));

        // get_records() is supposed to use binary comparison
        $records = $DB->get_records($tablename, array('name'=>"aaa"));
        $this->assertEqual(count($records), 1, 'SQL operator = is expected to be case and accent sensitive');
        $records = $DB->get_records($tablename, array('name'=>"aäa"));
        $this->assertEqual(count($records), 1, 'SQL operator = is expected to be case and accent sensitive');

        $bool = $DB->record_exists($tablename, array('name'=>"aaa"));
        $this->assertTrue($bool, 'SQL operator = is expected to be case and accent sensitive');
        $bool = $DB->record_exists($tablename, array('name'=>"AaA"));
        $this->assertFalse($bool, 'SQL operator = is expected to be case and accent sensitive');
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'SuperDuperRecord'));
        $DB->insert_record($tablename, array('name'=>'Nodupor'));
        $DB->insert_record($tablename, array('name'=>'ouch'));
        $DB->insert_record($tablename, array('name'=>'ouc_'));
        $DB->insert_record($tablename, array('name'=>'ouc%'));
        $DB->insert_record($tablename, array('name'=>'aui'));
        $DB->insert_record($tablename, array('name'=>'aüi'));
        $DB->insert_record($tablename, array('name'=>'aÜi'));

        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?', false);
        $records = $DB->get_records_sql($sql, array("%dup_r%"));
        $this->assertEqual(count($records), 2);

        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?', true);
        $records = $DB->get_records_sql($sql, array("%dup%"));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?'); // defaults
        $records = $DB->get_records_sql($sql, array("%dup%"));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?', true);
        $records = $DB->get_records_sql($sql, array("ouc\\_"));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?', true, true, '|');
        $records = $DB->get_records_sql($sql, array($DB->sql_like_escape("ouc%", '|')));
        $this->assertEqual(count($records), 1);

        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?', true, true);
        $records = $DB->get_records_sql($sql, array('aui'));
        $this->assertEqual(count($records), 1);

        // we do not require accent insensitivness yet, just make sure it does not throw errors
        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?', true, false);
        $records = $DB->get_records_sql($sql, array('aui'));
        $this->assertEqual(count($records), 2, 'Accent insensitive LIKE searches may not be supported in all databases, this is not really a problem now.');
        $sql = "SELECT * FROM {".$tablename."} WHERE ".$DB->sql_like('name', '?', false, false);
        $records = $DB->get_records_sql($sql, array('aui'));
        $this->assertEqual(count($records), 3, 'Accent insensitive LIKE searches may not be supported in all databases, this is not really a problem now.');
    }

    function test_sql_ilike() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'SuperDuperRecord'));
        $DB->insert_record($tablename, array('name'=>'NoDupor'));
        $DB->insert_record($tablename, array('name'=>'ouch'));

        $sql = "SELECT * FROM {".$tablename."} WHERE name ".$DB->sql_ilike()." ?";
        $params = array("%dup_r%");
        $records = $DB->get_records_sql($sql, $params);
        $this->assertEqual(count($records), 2, 'DB->sql_ilike() is deprecated, ignore this problem.');
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
        // float, null and strings
        $params = array(123.45, null, 'test');
        $this->assertNull($DB->get_field_sql($sql, $params), 'ANSI behaviour: Concatenating NULL must return NULL - But in Oracle :-(. [%s]'); // Concatenate NULL with anything result = NULL

        /// Testing fieldnames + values
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('description'=>'áéíóú'));
        $DB->insert_record($tablename, array('description'=>'dxxx'));
        $DB->insert_record($tablename, array('description'=>'bcde'));

        $sql = 'SELECT id, ' . $DB->sql_concat('description', "'harcoded'", '?', '?') . ' AS result FROM {' . $tablename . '}';
        $records = $DB->get_records_sql($sql, array(123.45, 'test'));
        $this->assertEqual(count($records), 3);
        $this->assertEqual($records[1]->result, 'áéíóúharcoded123.45test');
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('description'=>'abcd'));
        $DB->insert_record($tablename, array('description'=>'dxxx'));
        $DB->insert_record($tablename, array('description'=>'bcde'));

        $sql = "SELECT * FROM {".$tablename."} ORDER BY ".$DB->sql_order_by_text('description');
        $records = $DB->get_records_sql($sql);
        $first = array_unshift($records);
        $this->assertEqual(1, $first->id);
        $second = array_unshift($records);
        $this->assertEqual(3, $second->id);
        $last = array_unshift($records);
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
        $this->tables[$tablename] = $table;

        $string = 'abcdefghij';

        $DB->insert_record($tablename, array('name'=>$string));

        $sql = "SELECT id, ".$DB->sql_substr("name", 5)." AS name FROM {".$tablename."}";
        $record = $DB->get_record_sql($sql);
        $this->assertEqual(substr($string, 5-1), $record->name);

        $sql = "SELECT id, ".$DB->sql_substr("name", 5, 2)." AS name FROM {".$tablename."}";
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'', 'namenotnull'=>''));
        $DB->insert_record($tablename, array('name'=>null));
        $DB->insert_record($tablename, array('name'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>0));

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE name = '".$DB->sql_empty()."'");
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->name, '');

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE namenotnull = '".$DB->sql_empty()."'");
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->namenotnull, '');

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE namenotnullnodeflt = '".$DB->sql_empty()."'");
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'',   'namenull'=>'',   'description'=>'',   'descriptionnull'=>''));
        $DB->insert_record($tablename, array('name'=>'??', 'namenull'=>null, 'description'=>'??', 'descriptionnull'=>null));
        $DB->insert_record($tablename, array('name'=>'la', 'namenull'=>'la', 'description'=>'la', 'descriptionnull'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>0,    'namenull'=>0,    'description'=>0,    'descriptionnull'=>0));

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isempty($tablename, 'name', false, false));
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->name, '');

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isempty($tablename, 'namenull', true, false));
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->namenull, '');

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isempty($tablename, 'description', false, true));
        $this->assertEqual(count($records), 1);
        $record = reset($records);
        $this->assertEqual($record->description, '');

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isempty($tablename, 'descriptionnull', true, true));
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'',   'namenull'=>'',   'description'=>'',   'descriptionnull'=>''));
        $DB->insert_record($tablename, array('name'=>'??', 'namenull'=>null, 'description'=>'??', 'descriptionnull'=>null));
        $DB->insert_record($tablename, array('name'=>'la', 'namenull'=>'la', 'description'=>'la', 'descriptionnull'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>0,    'namenull'=>0,    'description'=>0,    'descriptionnull'=>0));

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isnotempty($tablename, 'name', false, false));
        $this->assertEqual(count($records), 3);
        $record = reset($records);
        $this->assertEqual($record->name, '??');

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isnotempty($tablename, 'namenull', true, false));
        $this->assertEqual(count($records), 2); // nulls aren't comparable (so they aren't "not empty"). SQL expected behaviour
        $record = reset($records);
        $this->assertEqual($record->namenull, 'la'); // so 'la' is the first non-empty 'namenull' record

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isnotempty($tablename, 'description', false, true));
        $this->assertEqual(count($records), 3);
        $record = reset($records);
        $this->assertEqual($record->description, '??');

        $records = $DB->get_records_sql("SELECT * FROM {".$tablename."} WHERE ".$DB->sql_isnotempty($tablename, 'descriptionnull', true, true));
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
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('name'=>'lalala'));
        $DB->insert_record($tablename, array('name'=>'holaaa'));
        $DB->insert_record($tablename, array('name'=>'aouch'));

        $sql = "SELECT * FROM {".$tablename."} WHERE name ".$DB->sql_regex()." ?";
        $params = array('a$');
        if ($DB->sql_regex_supported()) {
            $records = $DB->get_records_sql($sql, $params);
            $this->assertEqual(count($records), 2);
        } else {
            $this->assertTrue(true, 'Regexp operations not supported. Test skipped');
        }

        $sql = "SELECT * FROM {".$tablename."} WHERE name ".$DB->sql_regex(false)." ?";
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
        global $CFG;
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $DB->insert_record($tablename, array('course' => 3, 'content' => 'hello'));
        $DB->insert_record($tablename, array('course' => 3, 'content' => 'world'));
        $DB->insert_record($tablename, array('course' => 5, 'content' => 'hello'));
        $DB->insert_record($tablename, array('course' => 2, 'content' => 'universe'));

        // we have sql like this in moodle, this syntax breaks on older versions of sqlite for example..
        $sql = 'SELECT a.id AS id, a.course AS course
                FROM {'.$tablename.'} a
                JOIN (SELECT * FROM {'.$tablename.'}) b
                ON a.id = b.id
                WHERE a.course = ?';

        $this->assertTrue($records = $DB->get_records_sql($sql, array(3)));
        $this->assertEqual(2, count($records));
        $this->assertEqual(1, reset($records)->id);
        $this->assertEqual(2, next($records)->id);

        // try embeding sql_xxxx() helper functions, to check they don't break params/binding
        $count = $DB->count_records($tablename, array('course' => 3, $DB->sql_compare_text('content') => 'hello'));
        $this->assertEqual(1, $count);
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
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;

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
        $this->tables[$tablename] = $table;


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
            // this first rollback should prevent all otehr rollbacks
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
        $this->tables[$tablename] = $table;

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
