<?php
/**
 * Unit tests for (some of) ../datalib.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/simpletestlib/web_tester.php');
require_once($CFG->libdir . '/dmllib.php');

class datalib_test extends prefix_changing_test_case {
    var $table = 'table';
    var $data = array(
            array('id',   'textfield', 'numberfield'),
            array(  1,    'frog',     101),
            array(  2,    'toad',     102),
            array(  3, 'tadpole',     103),
            array(  4, 'tadpole',     104),
        );
    var $objects = array();

    function setUp() {
        global $CFG, $db;
        parent::setUp();
        wipe_tables($CFG->prefix, $db);
        load_test_table($CFG->prefix . $this->table, $this->data, $db);
        $keys = reset($this->data);
        foreach ($this->data as $datum) {
            if ($datum != $keys) {
               $this->objects[$datum[0]] = (object) array_combine($keys, $datum);
            }
        }
    }

    function tearDown() {
        global $CFG, $db;
        remove_test_table($CFG->prefix . $this->table, $db);
        parent::tearDown();
    }

    function test_where_clause() {
        $this->assertEqual(where_clause('f1', 'v1'), "WHERE f1 = 'v1'");
        $this->assertEqual(where_clause('f1', 'v1', 'f2', 2), "WHERE f1 = 'v1' AND f2 = '2'");
        $this->assertEqual(where_clause('f1', 'v1', 'f2', 1.75, 'f3', 'v3'), "WHERE f1 = 'v1' AND f2 = '1.75' AND f3 = 'v3'");
    }
    
    function test_record_exists() {
        $this->assertTrue(record_exists($this->table, 'numberfield', 101, 'id', 1));
        $this->assertFalse(record_exists($this->table, 'numberfield', 102, 'id', 1));
    }

    function test_record_exists_select() {
        $this->assertTrue(record_exists_select($this->table, 'numberfield = 101 AND id = 1'));
        $this->assertFalse(record_exists_select($this->table, 'numberfield = 102 AND id = 1'));
    }

    function test_record_exists_sql() {
        global $CFG;
        $this->assertTrue(record_exists_sql("SELECT * FROM {$CFG->prefix}$this->table WHERE numberfield = 101 AND id = 1"));
        $this->assertFalse(record_exists_sql("SELECT * FROM {$CFG->prefix}$this->table WHERE numberfield = 102 AND id = 1"));
    }


    function test_get_record() {
        // Get particular records.
        $this->assert(new CheckSpecifiedFieldsExpectation($this->objects[1]), get_record($this->table, 'id', 1), 'id = 1');
        $this->assert(new CheckSpecifiedFieldsExpectation($this->objects[3]), get_record($this->table, 'textfield', 'tadpole', 'numberfield', 103), 'textfield = tadpole AND numberfield = 103');

        // Abiguous get attempt, should return one, and print a warning in debug mode.
        global $CFG;
        $old_debug = $CFG->debug;
        $CFG->debug = 0;

        ob_start();
        $record = get_record($this->table, 'textfield', 'tadpole');
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertEqual('', $result, '%s (No error ouside debug mode).');

        $CFG->debug = E_ALL;
        ob_start();
        $record = get_record($this->table, 'textfield', 'tadpole');
        $result = ob_get_contents();
        ob_end_clean();
        $this->assert(new TextExpectation('Error:'), $result, 'Error in debug mode.');

        $CFG->debug = $old_debug;

        // Return only specified fields
        $expected = new stdClass;
        $expected->id = 3;
        $expected->textfield = 'tadpole';
        $result = get_record($this->table, 'id', '3', '', '', '', '', 'id,textfield');
        $this->assert(new CheckSpecifiedFieldsExpectation($expected), $result);
        $this->assertFalse(isset($result->numberfield));
        $expected = new stdClass;
        $expected->textfield = 'tadpole';
        $expected->numberfield = 103;
        $result = get_record($this->table, 'id', '3', '', '', '', '', 'textfield,numberfield');
        $this->assert(new CheckSpecifiedFieldsExpectation($expected), $result);
        $this->assertFalse(isset($result->id));

        // Attempting to get a non-existant records should return false.
        $this->assertFalse(get_record($this->table, 'textfield', 'not there'), 'attempt to get non-existant record');
    }

    function test_get_record_sql() {
        global $CFG;
        // Get particular records.
        $this->assert(new CheckSpecifiedFieldsExpectation($this->objects[1]), get_record_sql("SELECT * FROM {$CFG->prefix}" . $this->table . " WHERE id = '1'", 'id = 1'));

        // Abiguous get attempt, should return one, and print a warning in debug mode, unless $expectmultiple is used.
        $old_debug = $CFG->debug;
        $CFG->debug = 0;

        ob_start();
        $record = get_record_sql("SELECT * FROM {$CFG->prefix}" . $this->table . " WHERE textfield = 'tadpole'");
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertEqual('', $result, '%s (No error ouside debug mode).');

        $CFG->debug = E_ALL;
        ob_start();
        $record = get_record_sql("SELECT * FROM {$CFG->prefix}" . $this->table . " WHERE textfield = 'tadpole'");
        $result = ob_get_contents();
        ob_end_clean();
        $this->assert(new TextExpectation('Error:'), $result, 'Error in debug mode.');

        ob_start();
        $record = get_record_sql("SELECT * FROM {$CFG->prefix}" . $this->table . " WHERE textfield = 'tadpole'", true);
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertEqual('', $result, '%s (No error ouside debug mode).');

        $CFG->debug = $old_debug;

        // Attempting to get a non-existant records should return false.
        $this->assertFalse(get_record_sql("SELECT * FROM {$CFG->prefix}" . $this->table . " WHERE textfield = 'not there'"), 'attempt to get non-existant record');
    }

    function test_get_record_select() {
        // Get particular records.
        $this->assert(new CheckSpecifiedFieldsExpectation($this->objects[2]), get_record_select($this->table, 'id > 1 AND id < 3'), 'id > 1 AND id < 3');

        // Abiguous get attempt, should return one, and print a warning in debug mode.
        global $CFG;
        $old_debug = $CFG->debug;
        $CFG->debug = 0;

        ob_start();
        $record = get_record_select($this->table, "textfield = 'tadpole'");
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertEqual('', $result, '%s (No error ouside debug mode).');

        $CFG->debug = E_ALL;
        ob_start();
        $record = get_record_select($this->table, "textfield = 'tadpole'");
        $result = ob_get_contents();
        ob_end_clean();
        $this->assert(new TextExpectation('Error:'), $result, 'Error in debug mode.');

        $CFG->debug = $old_debug;

        // Return only specified fields
        $expected = new stdClass;
        $expected->id = 1;
        $expected->textfield = 'frog';
        $result = get_record_select($this->table, "textfield = 'frog'", 'id,textfield');
        $this->assert(new CheckSpecifiedFieldsExpectation($expected), $result);
        $this->assertFalse(isset($result->numberfield));

        // Attempting to get a non-existant records should return false.
        $this->assertFalse(get_record_select($this->table, 'id > 666'), 'attempt to get non-existant record');
    }

    function test_get_field() {
        $this->assertEqual(get_field($this->table, 'numberfield', 'id', 1), 101);
        $this->assertEqual(get_field($this->table, 'textfield', 'numberfield', 102), 'toad');
        $this->assertEqual(get_field($this->table, 'numberfield', 'textfield', 'tadpole', 'id', 4), 104);
        $this->assertEqual(get_field($this->table, 'numberfield + id', 'textfield', 'tadpole', 'id', 4), 108);
    }

    function test_get_field_select() {
        $this->assertEqual(get_field_select($this->table, 'numberfield',  'id = 1'), 101);
    }

    function test_get_field_sql() {
        global $CFG;
        $this->assertEqual(get_field_sql("SELECT numberfield FROM {$CFG->prefix}$this->table WHERE id = 1"), 101);
    }

    function test_set_field() {
        set_field($this->table, 'numberfield', 12345, 'id', 1);
        $this->assertEqual(get_field($this->table, 'numberfield', 'id', 1), 12345);

        set_field($this->table, 'textfield', 'newvalue', 'numberfield', 102);
        $this->assertEqual(get_field($this->table, 'textfield', 'numberfield', 102), 'newvalue');

        set_field($this->table, 'numberfield', -1, 'textfield', 'tadpole', 'id', 4);
        $this->assertEqual(get_field($this->table, 'numberfield', 'textfield', 'tadpole', 'id', 4), -1);
    }

    function test_delete_records() {
        delete_records($this->table, 'id', 666);
        $this->assertEqual(count_records($this->table), 4);
        delete_records($this->table, 'id', 1);
        $this->assertEqual(count_records($this->table), 3);
        delete_records($this->table, 'textfield', 'tadpole');
        $this->assertEqual(count_records($this->table), 1);
    }

    function test_delete_records2() {
        delete_records($this->table, 'textfield', 'tadpole', 'id', 4);
        $this->assertEqual(count_records($this->table), 3);
        delete_records($this->table);
        $this->assertEqual(count_records($this->table), 0);
    }

    function test_delete_records_select() {
        delete_records_select($this->table, "textfield LIKE 't%'");
        $this->assertEqual(count_records($this->table), 1);
        delete_records_select($this->table, "'1' = '1'");
        $this->assertEqual(count_records($this->table), 0);
    }

//function insert_record($table, $dataobject, $returnid=true, $primarykey='id', $feedback=true) {
    function test_insert_record() {
        // Simple insert with $returnid
        $obj = new stdClass;
        $obj->textfield = 'new entry';
        $obj->numberfield = 123;
        $this->assertEqual(insert_record($this->table, $obj), 5);
        $obj->id = 5;
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple insert with returnid (%s)'), get_record($this->table, 'id', 5));
        
        // Simple insert without $returnid
        $obj = new stdClass;
        $obj->textfield = 'newer entry';
        $obj->numberfield = 321;
        $this->assertEqual(insert_record($this->table, $obj, false), true);
        $obj->id = 6;
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple insert without returnid (%s)'), get_record($this->table, 'id', 6));
        
        // Insert with missing columns - should get defaults.
        $obj = new stdClass;
        $obj->textfield = 'partial entry';
        $this->assertEqual(insert_record($this->table, $obj), 7);
        $obj->id = 7;
        $obj->numberfield = 0xDefa;
        $got = get_record($this->table, 'id', 7);
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Insert with missing columns - should get defaults (%s)'), get_record($this->table, 'id', 7));
        
        // Insert with extra columns - should be ingnored.
        $obj = new stdClass;
        $obj->textfield = 'entry with extra';
        $obj->numberfield = 747;
        $obj->unused = 666;
        $this->assertEqual(insert_record($this->table, $obj), 8);
        $obj->id = 8;
        unset($obj->unused);
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Insert with extra columns - should be ingnored (%s)'), get_record($this->table, 'id', 8));
        
        // Insert into nonexistant table - should fail.
        $obj = new stdClass;
        $obj->textfield = 'new entry';
        $obj->numberfield = 123;
        $this->assertFalse(insert_record('nonexistant_table', $obj), 'Insert into nonexistant table');
        
        // Insert bad data - error should be printed.
        $obj = new stdClass;
        $obj->textfield = 'new entry';
        $obj->numberfield = 'not a number';
        ob_start();
        $this->assertFalse(insert_record($this->table, $obj), 'Insert bad data - should fail.');
        $result = ob_get_contents();
        ob_end_clean();
        $this->assert(new TextExpectation('ERROR:'), $result, 'Insert bad data - error should have been printed. This is known not to work on MySQL.');
    }
}

?>
