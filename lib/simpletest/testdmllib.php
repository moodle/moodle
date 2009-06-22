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

class dmllib_test extends prefix_changing_test_case {
    var $table = 'table';
    var $data = array(
            array('id',   'textfield', 'numberfield'),
            array(  1,    'frog',     101),
            array(  2,    'toad',     102),
            array(  3, 'tadpole',     103),
            array(  4, 'tadpole',     104),
            array(  5, 'nothing',     NULL),
        );
    var $objects = array();

    function setUp() {
        global $CFG, $db;
        parent::setUp();
        load_test_table($CFG->prefix . $this->table, $this->data, $db);
        $keys = reset($this->data);
        foreach ($this->data as $row=>$datum) {
            if ($row == 0) {
                continue;
            }
            $this->objects[$datum[0]] = (object) array_combine($keys, $datum);
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
        $this->assertEqual(where_clause('f1', NULL), "WHERE f1 IS NULL");
    }

    function test_record_exists() {
        $this->assertTrue(record_exists($this->table, 'numberfield', 101, 'id', 1));
        $this->assertFalse(record_exists($this->table, 'numberfield', 102, 'id', 1));
        $this->assertTrue(record_exists($this->table, 'numberfield', NULL));
    }

    function test_record_exists_select() {
        $this->assertTrue(record_exists_select($this->table, 'numberfield = 101 AND id = 1'));
        $this->assertFalse(record_exists_select($this->table, 'numberfield = 102 AND id = 1'));
        $this->assertTrue(record_exists_select($this->table, 'numberfield IS NULL'));
    }

    function test_record_exists_sql() {
        global $CFG;
        $this->assertTrue(record_exists_sql("SELECT * FROM {$CFG->prefix}$this->table WHERE numberfield = 101 AND id = 1"));
        $this->assertFalse(record_exists_sql("SELECT * FROM {$CFG->prefix}$this->table WHERE numberfield = 102 AND id = 1"));
        $this->assertTrue(record_exists_sql("SELECT * FROM {$CFG->prefix}$this->table WHERE numberfield IS NULL"));
    }


    function test_get_record() {
        // Get particular records.
        $this->assert(new CheckSpecifiedFieldsExpectation($this->objects[1]), get_record($this->table, 'id', 1));
        $this->assert(new CheckSpecifiedFieldsExpectation($this->objects[3]), get_record($this->table, 'textfield', 'tadpole', 'numberfield', 103));
        $this->assert(new CheckSpecifiedFieldsExpectation($this->objects[5]), get_record($this->table, 'numberfield', null));

        // Abiguous get attempt, should return one, and print a warning in debug mode.
        global $CFG;
        $old_debug = $CFG->debug;
        $CFG->debug = 0;

        ob_start();
        $record = get_record($this->table, 'textfield', 'tadpole');
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertEqual('', $result, '%s (No error ouside debug mode).');

        $CFG->debug = DEBUG_DEVELOPER;
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

        $CFG->debug = DEBUG_DEVELOPER;
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

        $CFG->debug = DEBUG_DEVELOPER;
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
        $this->assertNull(get_field($this->table, 'numberfield', 'id', 5));
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

        set_field($this->table, 'textfield', null, 'id', 5);
        $this->assertNull(get_field($this->table, 'textfield', 'id', 5));
    }

    function test_delete_records() {
        delete_records($this->table, 'id', 666);
        $this->assertEqual(count_records($this->table), 5);
        delete_records($this->table, 'id', 1);
        $this->assertEqual(count_records($this->table), 4);
        delete_records($this->table, 'textfield', 'tadpole');
        $this->assertEqual(count_records($this->table), 2);
        delete_records($this->table, 'numberfield', NULL);
        $this->assertEqual(count_records($this->table), 1);
    }

    function test_delete_records2() {
        delete_records($this->table, 'textfield', 'tadpole', 'id', 4);
        $this->assertEqual(count_records($this->table), 4);
        delete_records($this->table);
        $this->assertEqual(count_records($this->table), 0);
    }

    function test_delete_records_select() {
        delete_records_select($this->table, "textfield LIKE 't%'");
        $this->assertEqual(count_records($this->table), 2);
        delete_records_select($this->table, "'1' = '1'");
        $this->assertEqual(count_records($this->table), 0);
    }

    function test_update_record() {
        global $CFG;

        // Simple update
        $obj = new stdClass;
        $obj->id = 1;
        $obj->textfield = 'changed entry';
        $obj->numberfield = 123;
        $this->assertTrue(update_record($this->table, $obj));
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple update (%s)'), get_record($this->table, 'id', $obj->id));

        // Simple incomplete update
        $obj = new stdClass;
        $obj->id = 2;
        $obj->numberfield = 123;
        $this->assertTrue(update_record($this->table, $obj));
        $obj->textfield = 'toad';
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple update (%s)'), get_record($this->table, 'id', $obj->id));

        // Simple incomplete update
        $obj = new stdClass;
        $obj->id = 3;
        $obj->numberfield = 123;
        $obj->textfield = null;
        $this->assertTrue(update_record($this->table, $obj));
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple update (%s)'), get_record($this->table, 'id', $obj->id));

    }

//function insert_record($table, $dataobject, $returnid=true, $primarykey='id', $feedback=true) {
    function test_insert_record() {
        global $CFG;

        // Simple insert with $returnid
        $obj = new stdClass;
        $obj->textfield = 'new entry';
        $obj->numberfield = 123;
        $this->assertEqual(insert_record($this->table, $obj), 6);
        $obj->id = 6;
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple insert with returnid (%s)'), get_record($this->table, 'id', $obj->id));

        // Simple insert without $returnid
        $obj = new stdClass;
        $obj->textfield = 'newer entry';
        $obj->numberfield = 321;
        $this->assertEqual(insert_record($this->table, $obj, false), true);
        $obj->id = 7;
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple insert without returnid (%s)'), get_record($this->table, 'id', $obj->id));

        // Insert with missing columns - should get defaults.
        $obj = new stdClass;
        $obj->textfield = 'partial entry';
        $this->assertEqual(insert_record($this->table, $obj), 8);
        $obj->id = 8;
        $obj->numberfield = 0xDefa;
        $got = get_record($this->table, 'id', 8);
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Insert with missing columns - should get defaults (%s)'), get_record($this->table, 'id', $obj->id));

        // Insert with extra columns - should be ingnored.
        $obj = new stdClass;
        $obj->textfield = 'entry with extra';
        $obj->numberfield = 747;
        $obj->unused = 666;
        $this->assertEqual(insert_record($this->table, $obj), 9);
        $obj->id = 9;
        unset($obj->unused);
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Insert with extra columns - should be ingnored (%s)'), get_record($this->table, 'id', $obj->id));

        // Simple insert with $returnid and NULL values
        $obj = new stdClass;
        $obj->textfield = null;
        $obj->numberfield = null;
        $this->assertEqual(insert_record($this->table, $obj), 10);
        $obj->id = 10;
        $new = get_record($this->table, 'id', $obj->id);
        $this->assert(new CheckSpecifiedFieldsExpectation($obj, 'Simple insert with returnid (%s)'), $new);
        $this->assertNull($new->textfield);
        $this->assertNull($new->numberfield);

        // Insert into nonexistant table - should fail.
        $obj = new stdClass;
        $obj->textfield = 'new entry';
        $obj->numberfield = 123;
        $this->assertFalse(insert_record('nonexistant_table', $obj), 'Insert into nonexistant table');

    }

//test insert / retrieval of information containing backslashes, single and double quotes combinations
    function test_backslashes_and_quotes() {

        $teststrings = array(
                'backslashes and quotes alone (even): "" \'\' \\\\',
                'backslashes and quotes alone (odd): """ \'\'\' \\\\\\',
                'backslashes and quotes seqences (even): \\"\\" \\\'\\\'',
                'backslashes and quotes seqences (odd): \\"\\"\\" \\\'\\\'\\\'');
        foreach ($teststrings as $teststrig) {
            // insert the test string
            $obj = new stdClass;
            $obj->textfield = 'insert ' . $teststrig;
            $recid = insert_record($this->table, addslashes_recursive($obj));
            // retrieve it with get_record and test
            $rec = get_record($this->table, 'id', $recid);
            $this->assertEqual($rec->textfield, $obj->textfield);
            // update it
            $rec->textfield = 'update ' . $teststrig;
            update_record($this->table, addslashes_recursive($rec));
            // retrieve it with get field and test
            $textfield_db = get_field($this->table, 'textfield', 'id', $recid);
            $this->assertEqual($textfield_db, $rec->textfield);
        }


    }
}

?>
