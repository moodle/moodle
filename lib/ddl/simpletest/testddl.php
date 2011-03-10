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
    private $records= array();
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
        $table->add_field('logo', XMLDB_TYPE_BINARY, 'big', null, null, null);
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
        $table->add_key('course', XMLDB_KEY_UNIQUE, array('course'));
        $table->add_index('type-name', XMLDB_INDEX_UNIQUE, array('type', 'name'));
        $table->add_index('rsstype', XMLDB_INDEX_NOTUNIQUE, array('rsstype'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        // Define 2 initial records for this table
        $this->records[$table->getName()] = array(
                (object)array(
                        'course' => '1',
                        'type'   => 'general',
                        'name'   => 'record',
                        'intro'  => 'first record'),
                (object)array(
                        'course' => '2',
                        'type'   => 'social',
                        'name'   => 'record',
                        'intro'  => 'second record'));

        // Second, smaller table
        $table = new xmldb_table ('test_table1');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, null, null, 'Moodle');
        $table->add_field('secondname', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_field('thirdname', XMLDB_TYPE_CHAR, '30', null, null, null, ''); // nullable column with empty default
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('avatar', XMLDB_TYPE_BINARY, 'medium', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '20,10', null, null, null);
        $table->add_field('gradefloat', XMLDB_TYPE_FLOAT, '20,0', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('percentfloat', XMLDB_TYPE_FLOAT, '5,2', null, null, null, 99.9);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('course', XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'test_table0', array('course'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        // Define 2 initial records for this table
        $this->records[$table->getName()] = array(
                (object)array(
                        'course' => '1',
                        'secondname'   => 'first record', // > 10 cc, please don't modify. Some tests below depend of this
                        'intro'  => 'first record'),
                (object)array(
                        'course'       => '2',
                        'secondname'   => 'second record', // > 10 cc, please don't modify. Some tests below depend of this
                        'intro'  => 'second record'));

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
     * Fill the given test table with some records, as far as
     * DDL behaviour must be tested both with real data and
     * with empty tables
     */
    private function fill_deftable($tablename) {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        if (!isset($this->records[$tablename])) {
            return null;
        }

        if ($dbman->table_exists($tablename)) {
            foreach ($this->records[$tablename] as $row) {
                $DB->insert_record($tablename, $row);
            }
        } else {
            return null;
        }

        return count($this->records[$tablename]);
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
        $this->assertEqual($columns['thirdname']->meta_type, 'C');
        $this->assertEqual($columns['intro']->meta_type, 'X');
        $this->assertEqual($columns['avatar']->meta_type, 'B');
        $this->assertEqual($columns['grade']->meta_type, 'N');
        $this->assertEqual($columns['percentfloat']->meta_type, 'N');
        $this->assertEqual($columns['userid']->meta_type, 'I');
        // some defaults
        $this->assertTrue($columns['course']->has_default);
        $this->assertEqual($columns['course']->default_value, 0);
        $this->assertTrue($columns['name']->has_default);
        $this->assertEqual($columns['name']->default_value, 'Moodle');
        $this->assertTrue($columns['secondname']->has_default);
        $this->assertEqual($columns['secondname']->default_value, '');
        $this->assertTrue($columns['thirdname']->has_default);
        $this->assertEqual($columns['thirdname']->default_value, '');
        $this->assertTrue($columns['percentfloat']->has_default);
        $this->assertEqual($columns['percentfloat']->default_value, 99.9);
        $this->assertTrue($columns['userid']->has_default);
        $this->assertEqual($columns['userid']->default_value, 0);

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

        // check defined defaults are working ok
        $dbrec = $DB->get_record('test_table1', array('id' => 1));
        $this->assertEqual($dbrec->name, 'Moodle');
        $this->assertEqual($dbrec->thirdname, '');

        // check exceptions if multiple R columns
        $table = new xmldb_table ('test_table2');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('rid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('primaryx', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_exception);
        }

        // check exceptions missing primary key on R column
        $table = new xmldb_table ('test_table2');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_exception);
        }

    }

    /**
     * Test behaviour of drop_table()
     */
    public function test_drop_table() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // initially table doesn't exist
        $this->assertFalse($dbman->table_exists('test_table0'));

        // create table with contents
        $table = $this->create_deftable('test_table0');
        $this->assertTrue($dbman->table_exists('test_table0'));

        // fill the table with some records before dropping it
        $this->fill_deftable('test_table0');

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

        // fill the table with some records before renaming it
        $insertedrows = $this->fill_deftable('test_table1');

        $this->assertFalse($dbman->table_exists('test_table_cust1'));
        $dbman->rename_table($table, 'test_table_cust1');
        $this->assertTrue($dbman->table_exists('test_table_cust1'));

        // check sequence returns $insertedrows + 1 for this insert (after rename)
        $rec = (object)array(
                'course'     => 20,
                'secondname' => 'not important',
                'intro'      => 'not important');
        $this->assertIdentical($DB->insert_record('test_table_cust1', $rec), $insertedrows + 1);
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

    /**
     * Test behaviour of add_field()
     */
    public function test_add_field() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');

        // fill the table with some records before adding fields
        $this->fill_deftable('test_table1');

        /// add one not null field without specifying default value (throws ddl_exception)
        $field = new xmldb_field('onefield');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        try {
            $dbman->add_field($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_exception);
        }

        /// add one existing field (throws ddl_exception)
        $field = new xmldb_field('course');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 2);
        try {
            $dbman->add_field($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_exception);
        }

        // TODO: add one field with invalid type, must throw exception
        // TODO: add one text field with default, must throw exception
        // TODO: add one binary field with default, must throw exception

        /// add one integer field and check it
        $field = new xmldb_field('oneinteger');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 2);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'oneinteger'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['oneinteger']->name         ,'oneinteger');
        $this->assertEqual($columns['oneinteger']->not_null     , true);
        // max_length and scale cannot be checked under all DBs at all for integer fields
        $this->assertEqual($columns['oneinteger']->primary_key  , false);
        $this->assertEqual($columns['oneinteger']->binary       , false);
        $this->assertEqual($columns['oneinteger']->has_default  , true);
        $this->assertEqual($columns['oneinteger']->default_value, 2);
        $this->assertEqual($columns['oneinteger']->meta_type    ,'I');
        $this->assertEqual($DB->get_field('test_table1', 'oneinteger', array(), IGNORE_MULTIPLE), 2); //check default has been applied

        /// add one numeric field and check it
        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '6,3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 2.55);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onenumber'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['onenumber']->name         ,'onenumber');
        $this->assertEqual($columns['onenumber']->max_length   , 6);
        $this->assertEqual($columns['onenumber']->scale        , 3);
        $this->assertEqual($columns['onenumber']->not_null     , true);
        $this->assertEqual($columns['onenumber']->primary_key  , false);
        $this->assertEqual($columns['onenumber']->binary       , false);
        $this->assertEqual($columns['onenumber']->has_default  , true);
        $this->assertEqual($columns['onenumber']->default_value, 2.550);
        $this->assertEqual($columns['onenumber']->meta_type    ,'N');
        $this->assertEqual($DB->get_field('test_table1', 'onenumber', array(), IGNORE_MULTIPLE), 2.550); //check default has been applied

        /// add one float field and check it (not official type - must work as number)
        $field = new xmldb_field('onefloat');
        $field->set_attributes(XMLDB_TYPE_FLOAT, '6,3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 3.550);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onefloat'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['onefloat']->name         ,'onefloat');
        $this->assertEqual($columns['onefloat']->not_null     , true);
        // max_length and scale cannot be checked under all DBs at all for float fields
        $this->assertEqual($columns['onefloat']->primary_key  , false);
        $this->assertEqual($columns['onefloat']->binary       , false);
        $this->assertEqual($columns['onefloat']->has_default  , true);
        $this->assertEqual($columns['onefloat']->default_value, 3.550);
        $this->assertEqual($columns['onefloat']->meta_type    ,'N');
        // Just rounding DB information to 7 decimal digits. Fair enough to test 3.550 and avoids one nasty bug
        // in MSSQL core returning wrong floats (http://social.msdn.microsoft.com/Forums/en-US/sqldataaccess/thread/5e08de63-16bb-4f24-b645-0cf8fc669de3)
        // In any case, floats aren't officially supported by Moodle, with number/decimal type being the correct ones, so
        // this isn't a real problem at all.
        $this->assertEqual(round($DB->get_field('test_table1', 'onefloat', array(), IGNORE_MULTIPLE), 7), 3.550); //check default has been applied

        /// add one char field and check it
        $field = new xmldb_field('onechar');
        $field->set_attributes(XMLDB_TYPE_CHAR, '25', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 'Nice dflt!');
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onechar'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['onechar']->name         ,'onechar');
        $this->assertEqual($columns['onechar']->max_length   , 25);
        $this->assertEqual($columns['onechar']->scale        , null);
        $this->assertEqual($columns['onechar']->not_null     , true);
        $this->assertEqual($columns['onechar']->primary_key  , false);
        $this->assertEqual($columns['onechar']->binary       , false);
        $this->assertEqual($columns['onechar']->has_default  , true);
        $this->assertEqual($columns['onechar']->default_value,'Nice dflt!');
        $this->assertEqual($columns['onechar']->meta_type    ,'C');
        $this->assertEqual($DB->get_field('test_table1', 'onechar', array(), IGNORE_MULTIPLE), 'Nice dflt!'); //check default has been applied

        /// add one text field and check it
        $field = new xmldb_field('onetext');
        $field->set_attributes(XMLDB_TYPE_TEXT);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onetext'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['onetext']->name         ,'onetext');
        $this->assertEqual($columns['onetext']->max_length   , -1);
        $this->assertEqual($columns['onetext']->scale        , null);
        $this->assertEqual($columns['onetext']->not_null     , false);
        $this->assertEqual($columns['onetext']->primary_key  , false);
        $this->assertEqual($columns['onetext']->binary       , false);
        $this->assertEqual($columns['onetext']->has_default  , false);
        $this->assertEqual($columns['onetext']->default_value, null);
        $this->assertEqual($columns['onetext']->meta_type    ,'X');

        /// add one binary field and check it
        $field = new xmldb_field('onebinary');
        $field->set_attributes(XMLDB_TYPE_BINARY);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onebinary'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['onebinary']->name         ,'onebinary');
        $this->assertEqual($columns['onebinary']->max_length   , -1);
        $this->assertEqual($columns['onebinary']->scale        , null);
        $this->assertEqual($columns['onebinary']->not_null     , false);
        $this->assertEqual($columns['onebinary']->primary_key  , false);
        $this->assertEqual($columns['onebinary']->binary       , true);
        $this->assertEqual($columns['onebinary']->has_default  , false);
        $this->assertEqual($columns['onebinary']->default_value, null);
        $this->assertEqual($columns['onebinary']->meta_type    ,'B');

        // TODO: check datetime type. Although unused should be fully supported.
    }

    /**
     * Test behaviour of drop_field()
     */
    public function test_drop_field() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');

        // fill the table with some records before dropping fields
        $this->fill_deftable('test_table0');

        // drop field with simple xmldb_field having indexes, must return exception
        $field = new xmldb_field('type'); // Field has indexes and default clause
        $this->assertTrue($dbman->field_exists($table, 'type'));
        try {
            $dbman->drop_field($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_dependency_exception);
        }
        $this->assertTrue($dbman->field_exists($table, 'type')); // continues existing, drop aborted

        // drop field with complete xmldb_field object and related indexes, must return exception
        $field = $table->getField('course'); // Field has indexes and default clause
        $this->assertTrue($dbman->field_exists($table, $field));
        try {
            $dbman->drop_field($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_dependency_exception);
        }
        $this->assertTrue($dbman->field_exists($table, $field)); // continues existing, drop aborted

        // drop one non-existing field, must return exception
        $field = new xmldb_field('nonexistingfield');
        $this->assertFalse($dbman->field_exists($table, $field));
        try {
            $dbman->drop_field($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_field_missing_exception);
        }

        // drop field with simple xmldb_field, not having related indexes
        $field = new xmldb_field('forcesubscribe'); // Field has default clause
        $this->assertTrue($dbman->field_exists($table, 'forcesubscribe'));
        $dbman->drop_field($table, $field);
        $this->assertFalse($dbman->field_exists($table, 'forcesubscribe'));


        // drop field with complete xmldb_field object, not having related indexes
        $field = new xmldb_field('trackingtype'); // Field has default clause
        $this->assertTrue($dbman->field_exists($table, $field));
        $dbman->drop_field($table, $field);
        $this->assertFalse($dbman->field_exists($table, $field));
    }

    /**
     * Test behaviour of change_field_type()
     */
    public function test_change_field_type() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // create table with indexed field and not indexed field to
        // perform tests in both fields, both having defaults
        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('onenumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_field('anothernumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '4');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('onenumber', XMLDB_INDEX_NOTUNIQUE, array('onenumber'));
        $dbman->create_table($table);

        $record = new stdClass();
        $record->onenumber = 2;
        $record->anothernumber = 4;
        $recoriginal = $DB->insert_record('test_table_cust0', $record);

        // change column from integer to varchar. Must return exception because of dependent index
        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'test');
        try {
            $dbman->change_field_type($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_dependency_exception);
        }
        // column continues being integer 10 not null default 2
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change column from integer to varchar. Must work because column has no dependencies
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'test');
        $dbman->change_field_type($table, $field);
        // column is char 30 not null default 'test' now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'C');
        //TODO: check the rest of attributes

        // change column back from char to integer
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '8', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '5');
        $dbman->change_field_type($table, $field);
        // column is integer 8 not null default 5 now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change column once more from integer to char
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, "test'n drop");
        $dbman->change_field_type($table, $field);
        // column is char 30 not null default "test'n drop" now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'C');
        //TODO: check the rest of attributes

        // insert one string value and try to convert to integer. Must throw exception
        $record = new stdClass();
        $record->onenumber = 7;
        $record->anothernumber = 'string value';
        $rectodrop = $DB->insert_record('test_table_cust0', $record);
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '5');
        try {
            $dbman->change_field_type($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_change_structure_exception);
        }
        // column continues being char 30 not null default "test'n drop" now
        $this->assertEqual($columns['anothernumber']->meta_type, 'C');
        //TODO: check the rest of attributes
        $DB->delete_records('test_table_cust0', array('id' => $rectodrop)); // Delete the string record

        // change the column from varchar to float
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_FLOAT, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is float 20,10 null default null
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'N'); // floats are seen as number
        //TODO: check the rest of attributes

        // change the column back from float to varchar
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'test');
        $dbman->change_field_type($table, $field);
        // column is char 20 not null default "test" now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'C');
        //TODO: check the rest of attributes

        // change the column from varchar to number
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is number 20,10 null default null now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'N');
        //TODO: check the rest of attributes

        // change the column from number to integer
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is integer 2 null default null now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change the column from integer to text
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_type($table, $field);
        // column is char text not null default null
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'X');

        // change the column back from text to number
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is number 20,10 null default null now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'N');
        //TODO: check the rest of attributes

        // change the column from number to text
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_type($table, $field);
        // column is char text not null default "test" now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'X');
        //TODO: check the rest of attributes

        // change the column back from text to integer
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 10);
        $dbman->change_field_type($table, $field);
        // column is integer 10 not null default 10
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['anothernumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // check original value has survived to all the type changes
        $this->assertTrue($rec = $DB->get_record('test_table_cust0', array('id' => $recoriginal)));
        $this->assertEqual($rec->anothernumber, 4);

        $dbman->drop_table($table);
        $this->assertFalse($dbman->table_exists($table));
    }

    /**
     * Test behaviour of test_change_field_precision()
     */
    public function test_change_field_precision() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');

        // fill the table with some records before dropping fields
        $this->fill_deftable('test_table1');

        // change text field from medium to big
        $field = new xmldb_field('intro');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_precision($table, $field);
        $columns = $DB->get_columns('test_table1');
        // cannot check the text type, only the metatype
        $this->assertEqual($columns['intro']->meta_type, 'X');
        //TODO: check the rest of attributes

        // change char field from 30 to 20
        $field = new xmldb_field('secondname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_precision($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['secondname']->meta_type, 'C');
        //TODO: check the rest of attributes

        // change char field from 20 to 10, having contents > 10cc. Throw exception
        $field = new xmldb_field('secondname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        try {
            $dbman->change_field_precision($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_change_structure_exception);
        }
        // No changes in field specs at all
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['secondname']->meta_type, 'C');
        //TODO: check the rest of attributes

        // change number field from 20,10 to 10,2
        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null);
        $dbman->change_field_precision($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['grade']->meta_type, 'N');
        //TODO: check the rest of attributes

        // change integer field from 10 to 2
        $field = new xmldb_field('userid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $dbman->change_field_precision($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['userid']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change the column from integer (2) to integer (6) (forces change of type in some DBs)
        $field = new xmldb_field('userid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_precision($table, $field);
        // column is integer 6 null default null now
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['userid']->meta_type, 'I');
        //TODO: check the rest of attributes

        // insert one record with 6-digit field
        $record = new stdClass();
        $record->course = 10;
        $record->secondname  = 'third record';
        $record->intro  = 'third record';
        $record->userid = 123456;
        $DB->insert_record('test_table1', $record);
        // change integer field from 6 to 2, contents are bigger. must throw exception
        $field = new xmldb_field('userid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        try {
            $dbman->change_field_precision($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_change_structure_exception);
        }
        // No changes in field specs at all
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['userid']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change integer field from 10 to 3, in field used by index. must throw exception.
        $field = new xmldb_field('course');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        try {
            $dbman->change_field_precision($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_dependency_exception);
        }
        // No changes in field specs at all
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual($columns['course']->meta_type, 'I');
        //TODO: check the rest of attributes
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

        $record = new stdClass();
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
        $table->add_field('onenumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle2');
        $dbman->change_field_default($table, $field);

        $record = new stdClass();
        $record->onenumber = 666;
        $id = $DB->insert_record('test_table_cust0', $record);

        $record = $DB->get_record('test_table_cust0', array('id'=>$id));
        $this->assertEqual($record->name, 'Moodle2');


        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 666);
        $dbman->change_field_default($table, $field);

        $record = new stdClass();
        $record->name = 'something';
        $id = $DB->insert_record('test_table_cust0', $record);

        $record = $DB->get_record('test_table_cust0', array('id'=>$id));
        $this->assertEqual($record->onenumber, '666');

        $dbman->drop_table($table);
    }

    public function testAddUniqueIndex() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('onenumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $record = new stdClass();
        $record->onenumber = 666;
        $record->name = 'something';
        $DB->insert_record('test_table_cust0', $record, false);

        $index = new xmldb_index('onenumber-name');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('onenumber', 'name'));
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
        $DB = $this->tdb; // do not use global $DB!

        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $dbman->add_index($table, $index);

        $dbman->drop_index($table, $index);
        $this->assertFalse($dbman->find_index_name($table, $index));

        // Test we are able to drop indexes having hyphens. MDL-22804
        // Create index with hyphens (by hand)
        $indexname = 'test-index-with-hyphens';
        switch ($DB->get_dbfamily()) {
            case 'mysql':
                $indexname = '`' . $indexname . '`';
                break;
            default:
                $indexname = '"' . $indexname . '"';
        }
        $stmt = "CREATE INDEX {$indexname} ON {$DB->get_prefix()}test_table1 (course, name)";
        $DB->change_database_structure($stmt);
        $this->assertTrue($dbman->find_index_name($table, $index));
        // Index created, let's drop it using db manager stuff
        $index = new xmldb_index('indexname', XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
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

    /**
     * Test behaviour of drop_enum_from_field() and related functions (find_check_constraint_name
     * and check_constraint_exists). Needed to be able to drop existing "enum" fields in the upgrade
     * from 1.9 to 2.0, will be completely deleted for Moodle 2.1
     *
     * Because we already have dropped support for creation of enum fields in 2.0, we are going to
     * create them here "manually" (hardcoded DB-dependent SQL). Just to be able to test the
     * find and drop functions properly.
     *
     * TODO: Drop this tests completely from Moodle 2.1
     */
    public function test_drop_enum_from_field() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // Create normal table, no enums.
        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'general');
        $table->addField($field);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $this->assertTrue($dbman->table_exists($table));
        $this->assertTrue($dbman->field_exists($table, $field));

        // Check table hasn't enums at all
        $this->assertFalse($dbman->check_constraint_exists($table, $field));
        $this->assertFalse($dbman->find_check_constraint_name($table, $field));
        ob_start();
        $this->assertFalse($dbman->drop_enum_from_field($table, $field)); // This just outputs debug warning if field hasn't enums
        ob_end_clean();

        // Insert some info
        $record = new stdClass();
        $record->course = 666;
        $record->type = 'qanda';
        $this->assertTrue($DB->insert_record('test_table_cust0', $record, false));

        // Hackery starts here, depending of the db family we are testing... execute
        // the needed SQL statements to get the "type" field defined as enum
        $stmt = '';
        switch ($DB->get_dbfamily()) {
            case 'mysql': // It's ENUM field for mysql
                $stmt = "ALTER TABLE {$DB->get_prefix()}test_table_cust0 MODIFY type ENUM ('general', 'qanda', 'moodle') NOT NULL DEFAULT 'general'";
                break;
            default: // It's check constraint for "normal" DBs
                $stmt = "ALTER TABLE {$DB->get_prefix()}test_table_cust0 ADD CONSTRAINT ttcu0_ck CHECK (type IN ('general', 'qanda', 'moodle'))";
        }
        $DB->change_database_structure($stmt);

        // Check table has enums now
        $this->assertTrue($dbman->check_constraint_exists($table, $field));
        $this->assertTrue($dbman->find_check_constraint_name($table, $field));

        // Removing an enum value
        $dbman->drop_enum_from_field($table, $field);

        // Chech table hasn't enum anymore
        $this->assertFalse($dbman->check_constraint_exists($table, $field));
        $this->assertFalse($dbman->find_check_constraint_name($table, $field));

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

    public function testFindKeyName() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table0');
        $key = $table->getKey('primary');

        // With Mysql, the return value is actually "mdl_test_id_pk"
        $result = $dbman->find_key_name($table, $key);
        $this->assertTrue(!empty($result));
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
        $devhack = false;
        if (!empty($CFG->xmldbdisablenextprevchecking)) {
            $CFG->xmldbdisablenextprevchecking = false;
            $devhack = true;
        }
        try {
            ob_start(); // hide debug warning
            $dbman->delete_tables_from_xmldb_file($CFG->libdir . '/ddl/simpletest/fixtures/invalid.xml');
            $this->assertTrue(false);
            ob_end_clean();
        } catch (Exception $e) {
            ob_end_clean();
            $this->assertTrue($e instanceof moodle_exception);
        }
        if ($devhack) {
            $CFG->xmldbdisablenextprevchecking = true;
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
        $devhack = false;
        if (!empty($CFG->xmldbdisablenextprevchecking)) {
            $CFG->xmldbdisablenextprevchecking = false;
            $devhack = true;
        }
        try {
            ob_start(); // hide debug warning
            $dbman->install_from_xmldb_file($CFG->libdir.'/ddl/simpletest/fixtures/invalid.xml');
            ob_end_clean();
            $this->assertTrue(false);
        } catch (Exception $e) {
            ob_end_clean();
            $this->assertTrue($e instanceof moodle_exception);
        }
        if ($devhack) {
            $CFG->xmldbdisablenextprevchecking = true;
        }

        // Check that the table has not yet been created in DB
        $this->assertFalse($dbman->table_exists('test_table1'));

        // Real and valid xml file
        $dbman->install_from_xmldb_file($CFG->libdir.'/ddl/simpletest/fixtures/xmldb_table.xml');
        $this->assertTrue($dbman->table_exists('test_table1'));
    }

    public function test_temp_tables() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // Create temp table0
        $table0 = $this->tables['test_table0'];
        $dbman->create_temp_table($table0);
        $this->assertTrue($dbman->table_exists('test_table0'));

        // Try to create temp table with same name, must throw exception
        $dupetable = $this->tables['test_table0'];
        try {
            $dbman->create_temp_table($dupetable);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_exception);
        }

        // Try to create table with same name, must throw exception
        $dupetable = $this->tables['test_table0'];
        try {
            $dbman->create_table($dupetable);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_exception);
        }

        // Create another temp table1
        $table1 = $this->tables['test_table1'];
        $dbman->create_temp_table($table1);
        $this->assertTrue($dbman->table_exists('test_table1'));

        // Get columns and perform some basic tests
        $columns = $DB->get_columns('test_table1');
        $this->assertEqual(count($columns), 11);
        $this->assertTrue($columns['name'] instanceof database_column_info);
        $this->assertEqual($columns['name']->max_length, 30);
        $this->assertTrue($columns['name']->has_default);
        $this->assertEqual($columns['name']->default_value, 'Moodle');

        // Insert some records
        $inserted = $this->fill_deftable('test_table1');
        $records = $DB->get_records('test_table1');
        $this->assertEqual(count($records), $inserted);
        $this->assertEqual($records[1]->course, $this->records['test_table1'][0]->course);
        $this->assertEqual($records[1]->secondname, $this->records['test_table1'][0]->secondname);
        $this->assertEqual($records[2]->intro, $this->records['test_table1'][1]->intro);

        // Drop table1
        $dbman->drop_temp_table($table1);
        $this->assertFalse($dbman->table_exists('test_table1'));

        // Try to drop non-existing temp table, must throw exception
        $noetable = $this->tables['test_table1'];
        try {
            $dbman->drop_temp_table($noetable);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_table_missing_exception);
        }

        // Fill/modify/delete a few table0 records
        // TODO: that's

        // Drop table0
        $dbman->drop_temp_table($table0);
        $this->assertFalse($dbman->table_exists('test_table0'));

        // Have dropped all these temp tables here, to avoid conflicts with other (normal tables) tests!
    }

    public function test_concurrent_temp_tables() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // Define 2 records
        $record1 = (object)array(
                        'course'     =>  1,
                        'secondname' => '11 important',
                        'intro'      => '111 important');
        $record2 = (object)array(
                        'course'     =>  2,
                        'secondname' => '22 important',
                        'intro'      => '222 important');

        // Create temp table1 and insert 1 record (in DB)
        $table = $this->tables['test_table1'];
        $dbman->create_temp_table($table);
        $this->assertTrue($dbman->table_exists('test_table1'));
        $inserted = $DB->insert_record('test_table1', $record1);

        // Switch to new connection
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = array();
        }
        $DB2 = moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $DB2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);
        $dbman2 = $DB2->get_manager();
        $this->assertFalse($dbman2->table_exists('test_table1')); // Temp table not exists in DB2

        // Create temp table1 and insert 1 record (in DB2)
        $table = $this->tables['test_table1'];
        $dbman2->create_temp_table($table);
        $this->assertTrue($dbman2->table_exists('test_table1'));
        $inserted = $DB2->insert_record('test_table1', $record2);

        $dbman2->drop_temp_table($table); // Drop temp table before closing DB2
        $this->assertFalse($dbman2->table_exists('test_table1'));
        $DB2->dispose(); // Close DB2

        $this->assertTrue($dbman->table_exists('test_table1')); // Check table continues existing for DB
        $dbman->drop_temp_table($table); // Drop temp table
        $this->assertFalse($dbman->table_exists('test_table1'));
    }

    public function test_reset_sequence() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Drop if exists
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);
        $tablename = $table->getName();
        $this->tables[$tablename] = $table;

        $record = (object)array('id'=>666, 'course'=>10);
        $DB->import_record('testtable', $record);
        $DB->delete_records('testtable');

        $dbman->reset_sequence($table); // using xmldb object
        $this->assertEqual(1, $DB->insert_record('testtable', (object)array('course'=>13)));

        $DB->import_record('testtable', $record);
        $dbman->reset_sequence($tablename); // using string
        $this->assertEqual(667, $DB->insert_record('testtable', (object)array('course'=>13)));

        $dbman->drop_table($table);
    }

    public function test_reserved_words() {
        $reserved = sql_generator::getAllReservedWords();
        $this->assertTrue(count($reserved) > 1);
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
