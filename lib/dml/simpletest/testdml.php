<?php
/**
 * Unit tests for dml
 * @package dml
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class dml_test extends UnitTestCase {
    private $tables = array();
    private $tdb;
    private $data;

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
                $dbman->drop_table($table, true, false);
            }
        }
        $this->tables = array();
    }

    function test_fix_sql_params() {
        $DB = $this->tdb;

        // Malformed table placeholder
        $sql = "SELECT * FROM [testtable]";
        $sqlarray = $DB->fix_sql_params($sql);
        $this->assertEqual($sql, $sqlarray[0]);

        // Correct table placeholder substitution
        $sql = "SELECT * FROM {testtable}";
        $sqlarray = $DB->fix_sql_params($sql);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}testtable", $sqlarray[0]);

        // Malformed param placeholders
        $sql = "SELECT * FROM {testtable} WHERE name = ?param1";
        $params = array('param1' => 'record2');
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}testtable WHERE name = ?param1", $sqlarray[0]);

        // Mixed param types (colon and dollar)
        $sql = "SELECT * FROM {testtable} WHERE name = :param1, course = \$1";
        $params = array('param1' => 'record1', 'param2' => 3);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Mixed param types (question and dollar)
        $sql = "SELECT * FROM {testtable} WHERE name = ?, course = \$1";
        $params = array('param1' => 'record2', 'param2' => 5);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Too many params in sql
        $sql = "SELECT * FROM {testtable} WHERE name = ?, course = ?, id = ?";
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
            $this->fail("Unexpected " . get_class($e) . " exception");
        }
        $this->assertTrue($sqlarray[0]);

        // Named params missing from array
        $sql = "SELECT * FROM {testtable} WHERE name = :name, course = :course";
        $params = array('wrongname' => 'record1', 'course' => 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Duplicate named param in query
        $sql = "SELECT * FROM {testtable} WHERE name = :name, course = :name";
        $params = array('name' => 'record2', 'course' => 3);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Unsupported Bound params
        $sql = "SELECT * FROM {testtable} WHERE name = $1, course = $2";
        $params = array('first record', 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->fail("Expecting an exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Correct named param placeholders
        $sql = "SELECT * FROM {testtable} WHERE name = :name, course = :course";
        $params = array('name' => 'first record', 'course' => 1);
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}testtable WHERE name = ?, course = ?", $sqlarray[0]);
        $this->assertEqual(2, count($sqlarray[1]));

        // Correct ? params
        $sql = "SELECT * FROM {testtable} WHERE name = ?, course = ?";
        $params = array('first record', 1);
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}testtable WHERE name = ?, course = ?", $sqlarray[0]);
        $this->assertEqual(2, count($sqlarray[1]));

    }

    public function testGetTables() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $original_count = count($DB->get_tables());

        // Need to test with multiple DBs
        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;
        $this->assertTrue(count($DB->get_tables()) == $original_count + 1);
    }

    public function testGetIndexes() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $this->assertTrue($indices = $DB->get_indexes('testtable'));
        $this->assertTrue(count($indices) == 1);

        $xmldb_indexes = $table->getIndexes();
        $this->assertEqual(count($indices), count($xmldb_indexes));

        for ($i = 0; $i < count($indices); $i++) {
            if ($i == 0) {
                $next_index = reset($indices);
                $next_xmldb_index  = reset($xmldb_indexes);
            } else {
                $next_index = next($indices);
                $next_xmldb_index  = next($xmldb_indexes);
            }

            $this->assertEqual($next_index['columns'][0], $next_xmldb_index->name);
        }
    }

    public function testGetColumns() {
        $DB = $this->tdb;
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $this->assertTrue($columns = $DB->get_columns('testtable'));
        $fields = $this->tables['testtable']->getFields();
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

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $sql = "SELECT * FROM {testtable}";

        $this->assertTrue($DB->execute($sql));

        $params = array('course' => 1, 'name' => 'test');

        $sql = "INSERT INTO {testtable} (".implode(',', array_keys($params)).")
                       VALUES (".implode(',', array_fill(0, count($params), '?')).")";


        $this->assertTrue($DB->execute($sql, $params));

        $record = $DB->get_record('testtable', array('id' => 1));

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
        $this->assertEqual($prefix . "user", $DB->public_fix_table_names($placeholder));

        // Full SQL
        $sql = "SELECT * FROM {user}, {funny_table_name}, {mdl_stupid_table} WHERE {user}.id = {funny_table_name}.userid";
        $expected = "SELECT * FROM {$prefix}user, {$prefix}funny_table_name, {$prefix}mdl_stupid_table WHERE {$prefix}user.id = {$prefix}funny_table_name.userid";
        $this->assertEqual($expected, $DB->public_fix_table_names($sql));


    }

    public function test_get_recordset() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $data = array(array('id' => 1, 'course' => 3, 'name' => 'record1'),
                      array('id' => 2, 'course' => 3, 'name' => 'record2'),
                      array('id' => 3, 'course' => 5, 'name' => 'record3'));
        foreach ($data as $record) {
            $DB->insert_record('testtable', $record);
        }

        $rs = $DB->get_recordset('testtable');
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
    }

    public function test_get_recordset_list() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 5));
        $DB->insert_record('testtable', array('course' => 2));

        $rs = $DB->get_recordset_list('testtable', 'course', array(3, 2));

        $this->assertTrue($rs);

        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEqual(3, $counter);
    }

    public function test_get_recordset_select() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 5));
        $DB->insert_record('testtable', array('course' => 2));

        $this->assertTrue($rs = $DB->get_recordset_select('testtable', ''));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEqual(4, $counter);

        $this->assertTrue($rs = $DB->get_recordset_select('testtable', 'course = 3'));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEqual(2, $counter);
    }

    public function test_get_recordset_sql() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 5));
        $DB->insert_record('testtable', array('course' => 2));

        $this->assertTrue($rs = $DB->get_recordset_sql('SELECT * FROM {testtable} WHERE course = ?', array(3)));
        $counter = 0;
        foreach ($rs as $record) {
            $counter++;
        }
        $this->assertEqual(2, $counter);
    }

    public function test_get_records() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 5));
        $DB->insert_record('testtable', array('course' => 2));

        // All records
        $records = $DB->get_records('testtable');
        $this->assertEqual(4, count($records));
        $this->assertEqual(3, $records[1]->course);
        $this->assertEqual(3, $records[2]->course);
        $this->assertEqual(5, $records[3]->course);
        $this->assertEqual(2, $records[4]->course);

        // Records matching certain conditions
        $records = $DB->get_records('testtable', array('course' => 3));
        $this->assertEqual(2, count($records));
        $this->assertEqual(3, $records[1]->course);
        $this->assertEqual(3, $records[2]->course);

        // All records sorted by course
        $records = $DB->get_records('testtable', null, 'course');
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
        $records = $DB->get_records('testtable', null, '', 'id');
        $this->assertTrue(empty($records[1]->course));
        $this->assertFalse(empty($records[1]->id));
        $this->assertEqual(4, count($records));
    }

    public function test_get_records_list() {

    }

    public function test_get_records_sql() {

    }

    public function test_get_records_menu() {

    }

    public function test_get_records_select_menu() {

    }

    public function test_get_records_sql_menu() {

    }

    public function test_get_record() {

    }

    public function test_get_record_select() {

    }

    public function test_get_record_sql() {

    }

    public function test_get_field() {

    }

    public function test_get_field_select() {

    }

    public function test_get_field_sql() {

    }

    public function test_get_field_select() {

    }

    public function test_get_fieldset_sql() {

    }

    public function test_insert_record_raw() {

    }

    public function test_insert_record() {

    }

    public function test_update_record_raw() {

    }

    public function test_update_record() {

    }

    public function test_set_field() {

    }

    public function test_set_field_select() {

    }

    public function test_count_records() {
        $DB = $this->tdb;

        $dbman = $DB->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $this->assertEqual(0, $DB->count_records('testtable'));

        $DB->insert_record('testtable', array('course' => 3));
        $DB->insert_record('testtable', array('course' => 4));
        $DB->insert_record('testtable', array('course' => 5));

        $this->assertEqual(3, $DB->count_records('testtable'));
    }

    public function test_count_records_select() {

    }

    public function test_count_records_sql() {

    }

    public function test_record_exists() {

    }

    public function test_record_exists_select() {

    }

    public function test_record_exists_sql() {

    }

    public function test_delete_records() {

    }

    public function test_delete_records_select() {

    }

    public function test_where_clause() {

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
    public function get_name(){}
    public function get_configuration_hints(){}
    public function export_dbconfig(){}
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, array $dboptions=null){}
    public function get_server_info(){}
    protected function allowed_param_types(){}
    public function get_last_error(){}
    public function get_tables(){}
    public function get_indexes($table){}
    public function get_columns($table, $usecache=true){}
    public function set_debug($state){}
    public function get_debug(){}
    public function set_logging($state){}
    public function change_database_structure($sql){}
    public function execute($sql, array $params=null){}
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0){}
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0){}
    public function get_fieldset_sql($sql, array $params=null){}
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false){}
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false){}
    public function update_record_raw($table, $params, $bulk=false){}
    public function update_record($table, $dataobject, $bulk=false){}
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null){}
    public function delete_records_select($table, $select, array $params=null){}
    public function sql_concat(){}
    public function sql_concat_join($separator="' '", $elements=array()){}
    public function sql_substr(){}
}
