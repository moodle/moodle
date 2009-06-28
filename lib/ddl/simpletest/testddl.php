<?php
/**
 * Unit tests for (some of) ddl lib.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/adminlib.php');

class ddl_test extends UnitTestCase {
    private $tables = array();
    private $tdb;
    public  static $includecoverage = array('lib/ddl');
    public  static $excludecoverage = array('lib/ddl/simpletest');

    public function setUp() {
        global $CFG, $DB, $UNITTEST;

        if (isset($UNITTEST->func_test_db)) {
            $this->tdb = $UNITTEST->func_test_db;
        } else {
            $this->tdb = $DB;
        }

        unset($CFG->xmldbreconstructprevnext); // remove this unhack ;-)

        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table('test_table0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'general');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('logo', XMLDB_TYPE_BINARY, 'big', null, XMLDB_NOTNULL, null);
        $table->add_field('assessed', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('assesstimestart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('assesstimefinish', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('scale', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('maxbytes', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('forcesubscribe', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('trackingtype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('rsstype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('rssarticles', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '20,0', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('percent', XMLDB_TYPE_NUMBER, '5,2', null, null, null, 66.6);
        $table->add_field('warnafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('blockafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('blockperiod', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('type-name', XMLDB_KEY_UNIQUE, array('type', 'name'));
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('rsstype', XMLDB_INDEX_UNIQUE, array('rsstype'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        // Second, smaller table
        $table = new xmldb_table ('test_table1');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, null, null, 'Moodle');
        $table->add_field('secondname', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('avatar', XMLDB_TYPE_BINARY, 'medium', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '20,10', null, null, null);
        $table->add_field('gradefloat', XMLDB_TYPE_FLOAT, '20,0', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('percentfloat', XMLDB_TYPE_FLOAT, '5,2', null, null, null, 99.9);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('course', XMLDB_INDEX_UNIQUE, array('course'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        // make sure no tables are present!
        $this->tearDown();
    }

    public function tearDown() {
        $dbman = $this->tdb->get_manager();

        // drop custom test tables
        for ($i=0; $i<3; $i++) {
            $table = new xmldb_table('test_table_cust'.$i);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }

        // drop default tables
        foreach ($this->tables as $table) {
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }
    }

    private function create_deftable($tablename) {
        $dbman = $this->tdb->get_manager();

        if (!isset($this->tables[$tablename])) {
            return null;
        }

        $table = $this->tables[$tablename];

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);

        return $table;
    }

    /**
     * Test behaviour of table_exists()
     */
    public function test_table_exists() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // first make sure it returns false if table does not exist
        $table = $this->tables['test_table0'];

        ob_start(); // hide debug warning
        try {
            $result = $DB->get_records('test_table0');
        } catch (dml_read_exception $e) {
            $result = false;
        }
        ob_end_clean();

        $this->assertFalse($result);

        $this->assertFalse($dbman->table_exists('test_table0')); // by name
        $this->assertFalse($dbman->table_exists($table));        // by xmldb_table

        // create table and test again
        $dbman->create_table($table);

        $this->assertTrue($DB->get_records('test_table0') === array());
        $this->assertTrue($dbman->table_exists('test_table0')); // by name
        $this->assertTrue($dbman->table_exists($table));        // by xmldb_table

        // drop table and test again
        $dbman->drop_table($table);

        ob_start(); // hide debug warning
        try {
            $result = $DB->get_records('test_table0');
        } catch (dml_read_exception $e) {
            $result = false;
        }
        ob_end_clean();

        $this->assertFalse($result);

        $this->assertFalse($dbman->table_exists('test_table0')); // by name
        $this->assertFalse($dbman->table_exists($table));        // by xmldb_table
    }

    /**
     * Test behaviour of create_table()
     */
    public function test_create_table() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // create table
        $table = $this->tables['test_table1'];

        $dbman->create_table($table);
        $this->assertTrue($dbman->table_exists($table));

        // basic get_tables() test
        $tables = $DB->get_tables();
        $this->assertTrue(array_key_exists('test_table1', $tables));

        // basic get_columns() tests
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['id']->meta_type, 'R');
        $this->assertEqual($columns['course']->meta_type, 'I');
        $this->assertEqual($columns['name']->meta_type, 'C');
        $this->assertEqual($columns['secondname']->meta_type, 'C');
        $this->assertEqual($columns['intro']->meta_type, 'X');
        $this->assertEqual($columns['avatar']->meta_type, 'B');
        $this->assertEqual($columns['grade']->meta_type, 'N');
        $this->assertEqual($columns['percentfloat']->meta_type, 'N');

        // basic get_indexes() test
        $indexes = $DB->get_indexes('test_table1');
        $courseindex = reset($indexes);
        $this->assertEqual($courseindex['unique'], 1);
        $this->assertEqual($courseindex['columns'][0], 'course');

        // check sequence returns 1 for first insert
        $rec = (object)array(
                'course'     => 10,
                'secondname' => 'not important',
                'intro'      => 'not important');
        $this->assertIdentical($DB->insert_record('test_table1', $rec), 1);

    }

    /**
     * Test behaviour of drop_table()
     */
    public function test_drop_table() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // initially table doesn't exist
        $this->assertFalse($dbman->table_exists('test_table0'));

        // create table
        $table = $this->create_deftable('test_table0');
        $this->assertTrue($dbman->table_exists('test_table0'));

        // drop by xmldb_table object
        $dbman->drop_table($table);
        $this->assertFalse($dbman->table_exists('test_table0'));

        // basic get_tables() test
        $tables = $DB->get_tables();
        $this->assertFalse(array_key_exists('test_table0', $tables));

        try { // columns cache must be empty, so sentence throw exception
            $columns = $DB->get_columns('test_table0');
        } catch (dml_read_exception $e) {
            $columns = false;
        }
        $this->assertFalse($columns);

        try { /// throw exception
            $indexes = $DB->get_indexes('test_table0');
        } catch (dml_read_exception $e) {
            $indexes = false;
        }
        $this->assertFalse($indexes);
    }

    /**
     * Test behaviour of rename_table()
     */
    public function test_rename_table() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');

        // check sequence returns 1 for first insert
        $rec = (object)array(
                'course'     => 10,
                'secondname' => 'not important',
                'intro'      => 'not important');
        $this->assertIdentical($DB->insert_record('test_table1', $rec), 1);

        $this->assertFalse($dbman->table_exists('test_table_cust1'));
        $dbman->rename_table($table, 'test_table_cust1');
        $this->assertTrue($dbman->table_exists('test_table_cust1'));

        // check sequence returns 2 for second insert (after rename)
        $rec = (object)array(
                'course'     => 20,
                'secondname' => 'not important',
                'intro'      => 'not important');
        $this->assertIdentical($DB->insert_record('test_table_cust1', $rec), 2);
    }

    /**
     * Test behaviour of field_exists()
     */
    public function test_field_exists() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');

        // String params
        // Give a nonexistent table as first param (throw exception)
        try {
            $dbman->field_exists('nonexistenttable', 'id');
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Give a nonexistent field as second param (return false)
        $this->assertFalse($dbman->field_exists('test_table0', 'nonexistentfield'));

        // Correct string params
        $this->assertTrue($dbman->field_exists('test_table0', 'id'));

        // Object params
        $realfield = $table->getField('id');

        // Give a nonexistent table as first param (throw exception)
        $nonexistenttable = new xmldb_table('nonexistenttable');
        try {
            $dbman->field_exists($nonexistenttable, $realfield);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Give a nonexistent field as second param (return false)
        $nonexistentfield = new xmldb_field('nonexistentfield');
        $this->assertFalse($dbman->field_exists($table, $nonexistentfield));

        // Correct object params
        $this->assertTrue($dbman->field_exists($table, $realfield));

        // Mix string and object params
        // Correct ones
        $this->assertTrue($dbman->field_exists($table, 'id'));
        $this->assertTrue($dbman->field_exists('test_table0', $realfield));
        // Non existing tables (throw exception)
        try {
            $this->assertFalse($dbman->field_exists($nonexistenttable, 'id'));
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }
        try {
            $this->assertFalse($dbman->field_exists('nonexistenttable', $realfield));
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }
        // Non existing fields (return false)
        $this->assertFalse($dbman->field_exists($table, 'nonexistentfield'));
        $this->assertFalse($dbman->field_exists('test_table0', $nonexistentfield));
    }

    public function testAddNumericField() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        /// Create a new field with complex specs (enums are good candidates)
        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'type');
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onenumber'));

        $columns = $DB->get_columns('test_table0');
        $this->assertEqual($columns['onenumber']->meta_type, 'I');

        $dbman->drop_field($table, $field);
    }

    public function testDropField() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = $table->getField('type');

        $this->assertTrue($dbman->field_exists($table, $field));
        $this->assertTrue($dbman->field_exists($table, 'type'));

        $dbman->drop_field($table, $field);

        $this->assertFalse($dbman->field_exists($table, 'type'));
    }

    public function testChangeFieldType() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('onenumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $record = new object();
        $record->onenumber = 2;
        $DB->insert_record('test_table_cust0', $record);

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, '0');
        $dbman->change_field_type($table, $field);

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'C');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $dbman->change_field_type($table, $field);

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'I');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, "test'n drop");
        $dbman->change_field_type($table, $field);

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'C');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_FLOAT, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'N');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'test');
        $dbman->change_field_type($table, $field);

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'C');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'N');

        $dbman->drop_table($table);
    }

    public function testChangeFieldPrecision() {
        $dbman = $this->tdb->get_manager();
// TODO: verify the precision is changed in db

        $table = $this->create_deftable('test_table1');
        $field = new xmldb_field('intro');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('secondname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null);
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('course');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $dbman->change_field_precision($table, $field);

    }

    public function testChangeFieldSign() {
        $dbman = $this->tdb->get_manager();
// TODO: verify the signed is changed in db

        $table = $this->create_deftable('test_table1');
        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_unsigned($table, $field);

        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null);
        $dbman->change_field_unsigned($table, $field);
    }

    public function testChangeFieldNullability() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $record = new object();
        $record->name = NULL;

        ob_start(); // hide debug warning
        try {
            $result = $DB->insert_record('test_table_cust0', $record, false);
        } catch (dml_write_exception $e) {
            $result = false;
        }
        ob_end_clean();
        $this->assertFalse($result);

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $dbman->change_field_notnull($table, $field);

        $this->assertTrue($DB->insert_record('test_table_cust0', $record, false));

    // TODO: add some tests with existing data in table
        $DB->delete_records('test_table_cust0');

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_notnull($table, $field);

        ob_start(); // hide debug warning
        try {
            $result = $DB->insert_record('test_table_cust0', $record, false);
        } catch (dml_write_exception $e) {
            $result = false;
        }
        ob_end_clean();
        $this->assertFalse($result);

        $dbman->drop_table($table);
    }

    public function testChangeFieldDefault() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('number', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle2');
        $dbman->change_field_default($table, $field);

        $record = new object();
        $record->number = 666;
        $id = $DB->insert_record('test_table_cust0', $record);

        $record = $DB->get_record('test_table_cust0', array('id'=>$id));
        $this->assertEqual($record->name, 'Moodle2');


        $field = new xmldb_field('number');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 666);
        $dbman->change_field_default($table, $field);

        $record = new object();
        $record->name = 'something';
        $id = $DB->insert_record('test_table_cust0', $record);

        $record = $DB->get_record('test_table_cust0', array('id'=>$id));
        $this->assertEqual($record->number, '666');

        $dbman->drop_table($table);
    }

    public function testAddUniqueIndex() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('number', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $record = new object();
        $record->number = 666;
        $record->name = 'something';
        $DB->insert_record('test_table_cust0', $record, false);

        $index = new xmldb_index('number-name');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('number', 'name'));
        $dbman->add_index($table, $index);

        ob_start(); // hide debug warning
        try {
            $result = $DB->insert_record('test_table_cust0', $record, false);
        } catch (dml_write_exception $e) {
            $result = false;;
        }
        ob_end_clean();
        $this->assertFalse($result);

        $dbman->drop_table($table);
    }

    public function testAddNonUniqueIndex() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $dbman->add_index($table, $index);
    }

    public function testFindIndexName() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $dbman->add_index($table, $index);

        //DBM Systems name their indices differently - do not test the actual index name
        $result = $dbman->find_index_name($table, $index);
        $this->assertTrue(!empty($result));

        $nonexistentindex = new xmldb_index('nonexistentindex');
        $nonexistentindex->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('name'));
        $this->assertFalse($dbman->find_index_name($table, $nonexistentindex));
    }

    public function testDropIndex() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $dbman->add_index($table, $index);

        $dbman->drop_index($table, $index);
        $this->assertFalse($dbman->find_index_name($table, $index));
    }

    public function testAddUniqueKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $key = new xmldb_key('id-course-grade');
        $key->set_attributes(XMLDB_KEY_UNIQUE, array('id', 'course', 'grade'));
        $dbman->add_key($table, $key);
    }

    public function testAddForeignUniqueKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);
    }

    public function testDropKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);

        $dbman->drop_key($table, $key);
    }

    public function testAddForeignKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);
    }

    public function testDropForeignKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);

        $dbman->drop_key($table, $key);
    }

    public function testChangeFieldEnum() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'general');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        // Removing an enum value
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null);
        $dbman->drop_enum_from_field($table, $field);

        $record = new object();
        $record->course = 666;
        $record->type = 'qanda';
        $this->assertTrue($DB->insert_record('test_table_cust0', $record, false));

        // Adding an enum value
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'general', 'course');
        $dbman->drop_enum_from_field($table, $field);

        $record = new object();
        $record->course = 666;
        $record->type = 'nothing';

        ob_start(); // hide debug warning
        try {
            $result = $DB->insert_record('test_table_cust0', $record, false);
        } catch (dml_write_exception $e) {
            $result = false;
        }
        ob_end_clean();
        $this->assertFalse($result);

        $dbman->drop_table($table);
    }

    public function testRenameField() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'general', 'course');

        $dbman->rename_field($table, $field, 'newfieldname');

        $columns = $DB->get_columns('test_table0');

        $this->assertFalse(array_key_exists('type', $columns));
        $this->assertTrue(array_key_exists('newfieldname', $columns));
    }


    public function testIndexExists() {
        // Skipping: this is just a test of find_index_name
    }

    public function testFindCheckConstraintName() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = $table->getField('type');
        $result = $dbman->find_check_constraint_name($table, $field);
        $this->assertTrue(!empty($result));
    }

    public function testCheckConstraintExists() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = $table->getField('type');
        $this->assertTrue($dbman->check_constraint_exists($table, $field), 'type');
    }

    public function testFindKeyName() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $key = $table->getKey('primary');

        // With Mysql, the return value is actually "mdl_test_id_pk"
        $result = $dbman->find_key_name($table, $key);
        $this->assertTrue(!empty($result));
    }

    public function testFindSequenceName() {
        $dbman = $this->tdb->get_manager();

        // give nonexistent table param
        $table = new xmldb_table("nonexistenttable");
        try {
            $dbman->find_sequence_name($table);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Give existing and valid table param
        $table = $this->create_deftable('test_table0');
//TODO: this returns stuff depending on db internals
        // $this->assertEqual(false, $dbman->find_sequence_name($table));

    }

    public function testDeleteTablesFromXmldbFile() {
        global $CFG;
        $dbman = $this->tdb->get_manager();

        $this->create_deftable('test_table1');

        $this->assertTrue($dbman->table_exists('test_table1'));

        // feed nonexistent file
        try {
            ob_start(); // hide debug warning
            $dbman->delete_tables_from_xmldb_file('fpsoiudfposui');
            ob_end_clean();
            $this->assertTrue(false);
        } catch (Exception $e) {
            ob_end_clean();
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Real file but invalid xml file
        try {
            ob_start(); // hide debug warning
            $dbman->delete_tables_from_xmldb_file($CFG->libdir . '/ddl/simpletest/fixtures/invalid.xml');
            $this->assertTrue(false);
            ob_end_clean();
        } catch (Exception $e) {
            ob_end_clean();
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Check that the table has not been deleted from DB
        $this->assertTrue($dbman->table_exists('test_table1'));

        // Real and valid xml file
        $dbman->delete_tables_from_xmldb_file($CFG->libdir . '/ddl/simpletest/fixtures/xmldb_table.xml');

        // Check that the table has been deleted from DB
        $this->assertFalse($dbman->table_exists('test_table1'));
    }

    public function testInstallFromXmldbFile() {
        global $CFG;
        $dbman = $this->tdb->get_manager();

        // feed nonexistent file
        try {
            ob_start(); // hide debug warning
            $dbman->install_from_xmldb_file('fpsoiudfposui');
            ob_end_clean();
            $this->assertTrue(false);
        } catch (Exception $e) {
            ob_end_clean();
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Real but invalid xml file
        try {
            ob_start(); // hide debug warning
            $dbman->install_from_xmldb_file($CFG->libdir.'/ddl/simpletest/fixtures/invalid.xml');
            ob_end_clean();
            $this->assertTrue(false);
        } catch (Exception $e) {
            ob_end_clean();
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Check that the table has not yet been created in DB
        $this->assertFalse($dbman->table_exists('test_table1'));

        // Real and valid xml file
        $dbman->install_from_xmldb_file($CFG->libdir.'/ddl/simpletest/fixtures/xmldb_table.xml');
        $this->assertTrue($dbman->table_exists('test_table1'));
    }

    public function testCreateTempTable() {
        $dbman = $this->tdb->get_manager();

        $table = $this->tables['test_table1'];

        // New table
        $dbman->create_temp_table($table);
        $this->assertTrue($dbman->table_exists('test_table1', true));

        // Delete
        $dbman->drop_temp_table($table);
        $this->assertFalse($dbman->table_exists('test_table1', true));
    }


    public function test_reset_sequence() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $record = (object)array('id'=>666, 'course'=>10);
        $DB->import_record('testtable', $record);
        $DB->delete_records('testtable');

        $this->assertTrue($dbman->reset_sequence('testtable'));
        $this->assertEqual(1, $DB->insert_record('testtable', (object)array('course'=>13)));

        $DB->import_record('testtable', $record);
        $this->assertTrue($dbman->reset_sequence('testtable'));
        $this->assertEqual(667, $DB->insert_record('testtable', (object)array('course'=>13)));

        $dbman->drop_table($table);
    }


 // Following methods are not supported == Do not test
/*
    public function testRenameIndex() {
        // unsupported!
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $index = new xmldb_index('course');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('course'));

        $this->assertTrue($dbman->rename_index($table, $index, 'newindexname'));
    }

    public function testRenameKey() {
        //unsupported
         $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_UNIQUE, array('course'));

        $this->assertTrue($dbman->rename_key($table, $key, 'newkeyname'));
    }
*/

}
?>
