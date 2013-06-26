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
 * DDL layer tests
 *
 * @package    core_ddl
 * @category   phpunit
 * @copyright  2008 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class ddl_testcase extends database_driver_testcase {
    private $tables = array();
    private $records= array();

    protected function setUp() {
        parent::setUp();
        $dbman = $this->tdb->get_manager(); // loads DDL libs

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
     * @param string $tablename
     * @return int count of records
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

        try {
            $result = $DB->get_records('test_table0');
        } catch (dml_exception $e) {
            $result = false;
        }
        $this->resetDebugging();

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

        try {
            $result = $DB->get_records('test_table0');
        } catch (dml_exception $e) {
            $result = false;
        }
        $this->resetDebugging();

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
        $this->assertEquals($columns['id']->meta_type, 'R');
        $this->assertEquals($columns['course']->meta_type, 'I');
        $this->assertEquals($columns['name']->meta_type, 'C');
        $this->assertEquals($columns['secondname']->meta_type, 'C');
        $this->assertEquals($columns['thirdname']->meta_type, 'C');
        $this->assertEquals($columns['intro']->meta_type, 'X');
        $this->assertEquals($columns['avatar']->meta_type, 'B');
        $this->assertEquals($columns['grade']->meta_type, 'N');
        $this->assertEquals($columns['percentfloat']->meta_type, 'N');
        $this->assertEquals($columns['userid']->meta_type, 'I');
        // some defaults
        $this->assertTrue($columns['course']->has_default);
        $this->assertEquals($columns['course']->default_value, 0);
        $this->assertTrue($columns['name']->has_default);
        $this->assertEquals($columns['name']->default_value, 'Moodle');
        $this->assertTrue($columns['secondname']->has_default);
        $this->assertEquals($columns['secondname']->default_value, '');
        $this->assertTrue($columns['thirdname']->has_default);
        $this->assertEquals($columns['thirdname']->default_value, '');
        $this->assertTrue($columns['percentfloat']->has_default);
        $this->assertEquals($columns['percentfloat']->default_value, 99.9);
        $this->assertTrue($columns['userid']->has_default);
        $this->assertEquals($columns['userid']->default_value, 0);

        // basic get_indexes() test
        $indexes = $DB->get_indexes('test_table1');
        $courseindex = reset($indexes);
        $this->assertEquals($courseindex['unique'], 1);
        $this->assertEquals($courseindex['columns'][0], 'course');

        // check sequence returns 1 for first insert
        $rec = (object)array(
            'course'     => 10,
            'secondname' => 'not important',
            'intro'      => 'not important');
        $this->assertSame($DB->insert_record('test_table1', $rec), 1);

        // check defined defaults are working ok
        $dbrec = $DB->get_record('test_table1', array('id' => 1));
        $this->assertEquals($dbrec->name, 'Moodle');
        $this->assertEquals($dbrec->thirdname, '');

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

        // long table name names - the largest allowed
        $table = new xmldb_table('test_table0123456789_____xyz');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        $dbman->create_table($table);
        $this->assertTrue($dbman->table_exists($table));
        $dbman->drop_table($table);

        // table name is too long
        $table = new xmldb_table('test_table0123456789_____xyz9');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // invalid table name
        $table = new xmldb_table('test_tableCD');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }


        // weird column names - the largest allowed
        $table = new xmldb_table('test_table3');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('abcdef____0123456789_______xyz', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        $dbman->create_table($table);
        $this->assertTrue($dbman->table_exists($table));
        $dbman->drop_table($table);

        // Too long field name - max 30
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('abcdeabcdeabcdeabcdeabcdeabcdez', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid field name
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('abCD', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid integer length
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '21', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid integer default
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 'x');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid decimal length
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('num', XMLDB_TYPE_NUMBER, '21,10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid decimal decimals
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('num', XMLDB_TYPE_NUMBER, '10,11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid decimal default
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('num', XMLDB_TYPE_NUMBER, '10,5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 'x');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid float length
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('num', XMLDB_TYPE_FLOAT, '21,10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid float decimals
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('num', XMLDB_TYPE_FLOAT, '10,11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
        }

        // Invalid float default
        $table = new xmldb_table('test_table4');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('num', XMLDB_TYPE_FLOAT, '10,5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 'x');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        try {
            $dbman->create_table($table);
            $this->fail('Exception expected');
        } catch (Exception $e) {
            $this->assertSame(get_class($e), 'coding_exception');
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

        // columns cache must be empty
        $columns = $DB->get_columns('test_table0');
        $this->assertEmpty($columns);

        $indexes = $DB->get_indexes('test_table0');
        $this->assertEmpty($indexes);
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
        $this->assertSame($DB->insert_record('test_table_cust1', $rec), $insertedrows + 1);
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

        // add one not null field without specifying default value (throws ddl_exception)
        $field = new xmldb_field('onefield');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        try {
            $dbman->add_field($table, $field);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_exception);
        }

        // add one existing field (throws ddl_exception)
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

        // add one integer field and check it
        $field = new xmldb_field('oneinteger');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 2);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'oneinteger'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['oneinteger']->name         ,'oneinteger');
        $this->assertEquals($columns['oneinteger']->not_null     , true);
        // max_length and scale cannot be checked under all DBs at all for integer fields
        $this->assertEquals($columns['oneinteger']->primary_key  , false);
        $this->assertEquals($columns['oneinteger']->binary       , false);
        $this->assertEquals($columns['oneinteger']->has_default  , true);
        $this->assertEquals($columns['oneinteger']->default_value, 2);
        $this->assertEquals($columns['oneinteger']->meta_type    ,'I');
        $this->assertEquals($DB->get_field('test_table1', 'oneinteger', array(), IGNORE_MULTIPLE), 2); //check default has been applied

        // add one numeric field and check it
        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '6,3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 2.55);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onenumber'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['onenumber']->name         ,'onenumber');
        $this->assertEquals($columns['onenumber']->max_length   , 6);
        $this->assertEquals($columns['onenumber']->scale        , 3);
        $this->assertEquals($columns['onenumber']->not_null     , true);
        $this->assertEquals($columns['onenumber']->primary_key  , false);
        $this->assertEquals($columns['onenumber']->binary       , false);
        $this->assertEquals($columns['onenumber']->has_default  , true);
        $this->assertEquals($columns['onenumber']->default_value, 2.550);
        $this->assertEquals($columns['onenumber']->meta_type    ,'N');
        $this->assertEquals($DB->get_field('test_table1', 'onenumber', array(), IGNORE_MULTIPLE), 2.550); //check default has been applied

        // add one float field and check it (not official type - must work as number)
        $field = new xmldb_field('onefloat');
        $field->set_attributes(XMLDB_TYPE_FLOAT, '6,3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 3.550);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onefloat'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['onefloat']->name         ,'onefloat');
        $this->assertEquals($columns['onefloat']->not_null     , true);
        // max_length and scale cannot be checked under all DBs at all for float fields
        $this->assertEquals($columns['onefloat']->primary_key  , false);
        $this->assertEquals($columns['onefloat']->binary       , false);
        $this->assertEquals($columns['onefloat']->has_default  , true);
        $this->assertEquals($columns['onefloat']->default_value, 3.550);
        $this->assertEquals($columns['onefloat']->meta_type    ,'N');
        // Just rounding DB information to 7 decimal digits. Fair enough to test 3.550 and avoids one nasty bug
        // in MSSQL core returning wrong floats (http://social.msdn.microsoft.com/Forums/en-US/sqldataaccess/thread/5e08de63-16bb-4f24-b645-0cf8fc669de3)
        // In any case, floats aren't officially supported by Moodle, with number/decimal type being the correct ones, so
        // this isn't a real problem at all.
        $this->assertEquals(round($DB->get_field('test_table1', 'onefloat', array(), IGNORE_MULTIPLE), 7), 3.550); //check default has been applied

        // add one char field and check it
        $field = new xmldb_field('onechar');
        $field->set_attributes(XMLDB_TYPE_CHAR, '25', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 'Nice dflt!');
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onechar'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['onechar']->name         ,'onechar');
        $this->assertEquals($columns['onechar']->max_length   , 25);
        $this->assertEquals($columns['onechar']->scale        , null);
        $this->assertEquals($columns['onechar']->not_null     , true);
        $this->assertEquals($columns['onechar']->primary_key  , false);
        $this->assertEquals($columns['onechar']->binary       , false);
        $this->assertEquals($columns['onechar']->has_default  , true);
        $this->assertEquals($columns['onechar']->default_value,'Nice dflt!');
        $this->assertEquals($columns['onechar']->meta_type    ,'C');
        $this->assertEquals($DB->get_field('test_table1', 'onechar', array(), IGNORE_MULTIPLE), 'Nice dflt!'); //check default has been applied

        // add one big text field and check it
        $field = new xmldb_field('onetext');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big');
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onetext'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['onetext']->name         ,'onetext');
        $this->assertEquals($columns['onetext']->max_length   , -1); // -1 means unknown or big
        $this->assertEquals($columns['onetext']->scale        , null);
        $this->assertEquals($columns['onetext']->not_null     , false);
        $this->assertEquals($columns['onetext']->primary_key  , false);
        $this->assertEquals($columns['onetext']->binary       , false);
        $this->assertEquals($columns['onetext']->has_default  , false);
        $this->assertEquals($columns['onetext']->default_value, null);
        $this->assertEquals($columns['onetext']->meta_type    ,'X');

        // add one medium text field and check it
        $field = new xmldb_field('mediumtext');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium');
        $dbman->add_field($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertTrue(($columns['mediumtext']->max_length == -1) or ($columns['mediumtext']->max_length >= 16777215)); // -1 means unknown or big

        // add one small text field and check it
        $field = new xmldb_field('smalltext');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small');
        $dbman->add_field($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertTrue(($columns['smalltext']->max_length == -1) or ($columns['smalltext']->max_length >= 65535)); // -1 means unknown or big

        // add one binary field and check it
        $field = new xmldb_field('onebinary');
        $field->set_attributes(XMLDB_TYPE_BINARY);
        $dbman->add_field($table, $field);
        $this->assertTrue($dbman->field_exists($table, 'onebinary'));
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['onebinary']->name         ,'onebinary');
        $this->assertEquals($columns['onebinary']->max_length   , -1);
        $this->assertEquals($columns['onebinary']->scale        , null);
        $this->assertEquals($columns['onebinary']->not_null     , false);
        $this->assertEquals($columns['onebinary']->primary_key  , false);
        $this->assertEquals($columns['onebinary']->binary       , true);
        $this->assertEquals($columns['onebinary']->has_default  , false);
        $this->assertEquals($columns['onebinary']->default_value, null);
        $this->assertEquals($columns['onebinary']->meta_type    ,'B');

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
        $this->assertEquals($columns['onenumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change column from integer to varchar. Must work because column has no dependencies
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'test');
        $dbman->change_field_type($table, $field);
        // column is char 30 not null default 'test' now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'C');
        //TODO: check the rest of attributes

        // change column back from char to integer
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '8', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '5');
        $dbman->change_field_type($table, $field);
        // column is integer 8 not null default 5 now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change column once more from integer to char
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, "test'n drop");
        $dbman->change_field_type($table, $field);
        // column is char 30 not null default "test'n drop" now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'C');
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
        $this->assertEquals($columns['anothernumber']->meta_type, 'C');
        //TODO: check the rest of attributes
        $DB->delete_records('test_table_cust0', array('id' => $rectodrop)); // Delete the string record

        // change the column from varchar to float
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_FLOAT, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is float 20,10 null default null
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'N'); // floats are seen as number
        //TODO: check the rest of attributes

        // change the column back from float to varchar
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'test');
        $dbman->change_field_type($table, $field);
        // column is char 20 not null default "test" now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'C');
        //TODO: check the rest of attributes

        // change the column from varchar to number
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is number 20,10 null default null now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'N');
        //TODO: check the rest of attributes

        // change the column from number to integer
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is integer 2 null default null now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change the column from integer to text
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_type($table, $field);
        // column is char text not null default null
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'X');

        // change the column back from text to number
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_type($table, $field);
        // column is number 20,10 null default null now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'N');
        //TODO: check the rest of attributes

        // change the column from number to text
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_type($table, $field);
        // column is char text not null default "test" now
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'X');
        //TODO: check the rest of attributes

        // change the column back from text to integer
        $field = new xmldb_field('anothernumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 10);
        $dbman->change_field_type($table, $field);
        // column is integer 10 not null default 10
        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEquals($columns['anothernumber']->meta_type, 'I');
        //TODO: check the rest of attributes

        // check original value has survived to all the type changes
        $this->assertnotEmpty($rec = $DB->get_record('test_table_cust0', array('id' => $recoriginal)));
        $this->assertEquals($rec->anothernumber, 4);

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
        $this->assertEquals($columns['intro']->meta_type, 'X');
        //TODO: check the rest of attributes

        // change char field from 30 to 20
        $field = new xmldb_field('secondname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_precision($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['secondname']->meta_type, 'C');
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
        $this->assertEquals($columns['secondname']->meta_type, 'C');
        //TODO: check the rest of attributes

        // change number field from 20,10 to 10,2
        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null);
        $dbman->change_field_precision($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['grade']->meta_type, 'N');
        //TODO: check the rest of attributes

        // change integer field from 10 to 2
        $field = new xmldb_field('userid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $dbman->change_field_precision($table, $field);
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['userid']->meta_type, 'I');
        //TODO: check the rest of attributes

        // change the column from integer (2) to integer (6) (forces change of type in some DBs)
        $field = new xmldb_field('userid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, null, null, null);
        $dbman->change_field_precision($table, $field);
        // column is integer 6 null default null now
        $columns = $DB->get_columns('test_table1');
        $this->assertEquals($columns['userid']->meta_type, 'I');
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
        $this->assertEquals($columns['userid']->meta_type, 'I');
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
        $this->assertEquals($columns['course']->meta_type, 'I');
        //TODO: check the rest of attributes
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

        try {
            $result = $DB->insert_record('test_table_cust0', $record, false);
        } catch (dml_exception $e) {
            $result = false;
        }
        $this->resetDebugging();
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

        try {
            $result = $DB->insert_record('test_table_cust0', $record, false);
        } catch (dml_exception $e) {
            $result = false;
        }
        $this->resetDebugging();
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
        $this->assertEquals($record->name, 'Moodle2');


        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 666);
        $dbman->change_field_default($table, $field);

        $record = new stdClass();
        $record->name = 'something';
        $id = $DB->insert_record('test_table_cust0', $record);

        $record = $DB->get_record('test_table_cust0', array('id'=>$id));
        $this->assertEquals($record->onenumber, '666');

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

        try {
            $result = $DB->insert_record('test_table_cust0', $record, false);
        } catch (dml_exception $e) {
            $result = false;
        }
        $this->resetDebugging();
        $this->assertFalse($result);

        $dbman->drop_table($table);
    }

    public function testAddNonUniqueIndex() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $dbman->add_index($table, $index);
        $this->assertTrue($dbman->index_exists($table, $index));

        try {
            $dbman->add_index($table, $index);
            $this->fail('Exception expected for duplicate indexes');
        } catch (Exception $e) {
            $this->assertInstanceOf('ddl_exception', $e);
        }

        $index = new xmldb_index('third');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course'));
        try {
            $dbman->add_index($table, $index);
            $this->fail('Exception expected for duplicate indexes');
        } catch (Exception $e) {
            $this->assertInstanceOf('ddl_exception', $e);
        }

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('onenumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('onenumber', XMLDB_KEY_FOREIGN, array('onenumber'));

        try {
            $table->add_index('onenumber', XMLDB_INDEX_NOTUNIQUE, array('onenumber'));
            $this->fail('Coding exception expected');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('onenumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'Moodle');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('onenumber', XMLDB_INDEX_NOTUNIQUE, array('onenumber'));

        try {
            $table->add_key('onenumber', XMLDB_KEY_FOREIGN, array('onenumber'));
            $this->fail('Coding exception expected');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

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
        $this->assertNotEmpty($dbman->find_index_name($table, $index));
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

        // No easy way to test it, this just makes sure no errors are encountered.
        $this->assertTrue(true);
    }

    public function testAddForeignUniqueKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);

        // No easy way to test it, this just makes sure no errors are encountered.
        $this->assertTrue(true);
    }

    public function testDropKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);

        $dbman->drop_key($table, $key);

        // No easy way to test it, this just makes sure no errors are encountered.
        $this->assertTrue(true);
    }

    public function testAddForeignKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);

        // No easy way to test it, this just makes sure no errors are encountered.
        $this->assertTrue(true);
    }

    public function testDropForeignKey() {
        $dbman = $this->tdb->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('course'), 'test_table0', array('id'));
        $dbman->add_key($table, $key);

        $dbman->drop_key($table, $key);

        // No easy way to test it, this just makes sure no errors are encountered.
        $this->assertTrue(true);
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
            $dbman->delete_tables_from_xmldb_file('fpsoiudfposui');
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->resetDebugging();
            $this->assertTrue($e instanceof moodle_exception);
        }

        try {
            $dbman->delete_tables_from_xmldb_file(__DIR__ . '/fixtures/invalid.xml');
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->resetDebugging();
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Check that the table has not been deleted from DB
        $this->assertTrue($dbman->table_exists('test_table1'));

        // Real and valid xml file
        //TODO: drop UNSINGED completely in Moodle 2.4
        $dbman->delete_tables_from_xmldb_file(__DIR__ . '/fixtures/xmldb_table.xml');

        // Check that the table has been deleted from DB
        $this->assertFalse($dbman->table_exists('test_table1'));
    }

    public function testInstallFromXmldbFile() {
        global $CFG;
        $dbman = $this->tdb->get_manager();

        // feed nonexistent file
        try {
            $dbman->install_from_xmldb_file('fpsoiudfposui');
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->resetDebugging();
            $this->assertTrue($e instanceof moodle_exception);
        }

        try {
            $dbman->install_from_xmldb_file(__DIR__ . '/fixtures/invalid.xml');
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->resetDebugging();
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Check that the table has not yet been created in DB
        $this->assertFalse($dbman->table_exists('test_table1'));

        // Real and valid xml file
        $dbman->install_from_xmldb_file(__DIR__ . '/fixtures/xmldb_table.xml');
        $this->assertTrue($dbman->table_exists('test_table1'));
    }

    public function test_temp_tables() {
        global $CFG;

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
        $this->assertEquals(count($columns), 11);
        $this->assertTrue($columns['name'] instanceof database_column_info);
        $this->assertEquals($columns['name']->max_length, 30);
        $this->assertTrue($columns['name']->has_default);
        $this->assertEquals($columns['name']->default_value, 'Moodle');

        // Insert some records
        $inserted = $this->fill_deftable('test_table1');
        $records = $DB->get_records('test_table1');
        $this->assertEquals(count($records), $inserted);
        $this->assertEquals($records[1]->course, $this->records['test_table1'][0]->course);
        $this->assertEquals($records[1]->secondname, $this->records['test_table1'][0]->secondname);
        $this->assertEquals($records[2]->intro, $this->records['test_table1'][1]->intro);

        // Collect statistics about the data in the temp table.
        $DB->update_temp_table_stats();

        // Drop table1.
        $dbman->drop_table($table1);
        $this->assertFalse($dbman->table_exists('test_table1'));

        // Try to drop non-existing temp table, must throw exception
        $noetable = $this->tables['test_table1'];
        try {
            $dbman->drop_table($noetable);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ddl_table_missing_exception);
        }

        // Collect statistics about the data in the temp table with less tables.
        $DB->update_temp_table_stats();

        // Fill/modify/delete a few table0 records.

        // Drop table0
        $dbman->drop_table($table0);
        $this->assertFalse($dbman->table_exists('test_table0'));

        // Create another temp table1
        $table1 = $this->tables['test_table1'];
        $dbman->create_temp_table($table1);
        $this->assertTrue($dbman->table_exists('test_table1'));

        // Make sure it can be dropped using deprecated drop_temp_table()
        $dbman->drop_temp_table($table1);
        $this->assertFalse($dbman->table_exists('test_table1'));
        $this->assertDebuggingCalled();
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

        $dbman2->drop_table($table); // Drop temp table before closing DB2
        $this->assertFalse($dbman2->table_exists('test_table1'));
        $DB2->dispose(); // Close DB2

        $this->assertTrue($dbman->table_exists('test_table1')); // Check table continues existing for DB
        $dbman->drop_table($table); // Drop temp table
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
        $DB->delete_records('testtable'); // This delete performs one TRUNCATE

        $dbman->reset_sequence($table); // using xmldb object
        $this->assertEquals(1, $DB->insert_record('testtable', (object)array('course'=>13)));

        $record = (object)array('id'=>666, 'course'=>10);
        $DB->import_record('testtable', $record);
        $DB->delete_records('testtable', array()); // This delete performs one DELETE

        $dbman->reset_sequence($table); // using xmldb object
        $this->assertEquals(1, $DB->insert_record('testtable', (object)array('course'=>13)));

        $DB->import_record('testtable', $record);
        $dbman->reset_sequence($tablename); // using string
        $this->assertEquals(667, $DB->insert_record('testtable', (object)array('course'=>13)));

        $dbman->drop_table($table);
    }

    public function test_reserved_words() {
        $reserved = sql_generator::getAllReservedWords();
        $this->assertTrue(count($reserved) > 1);
    }

    public function test_index_hints() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null);
        $table->add_field('path', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, array('name'), array('xxxx,yyyy'));
        $table->add_index('path', XMLDB_INDEX_NOTUNIQUE, array('path'), array('varchar_pattern_ops'));

        // Drop if exists
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);
        $tablename = $table->getName();
        $this->tables[$tablename] = $table;

        $table = new xmldb_table('testtable');
        $index = new xmldb_index('name', XMLDB_INDEX_NOTUNIQUE, array('name'), array('xxxx,yyyy'));
        $this->assertTrue($dbman->index_exists($table, $index));

        $table = new xmldb_table('testtable');
        $index = new xmldb_index('path', XMLDB_INDEX_NOTUNIQUE, array('path'), array('varchar_pattern_ops'));
        $this->assertTrue($dbman->index_exists($table, $index));

        // Try unique indexes too.
        $dbman->drop_table($this->tables[$tablename]);

        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('path', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('path', XMLDB_INDEX_UNIQUE, array('path'), array('varchar_pattern_ops'));
        $dbman->create_table($table);
        $this->tables[$tablename] = $table;

        $table = new xmldb_table('testtable');
        $index = new xmldb_index('path', XMLDB_INDEX_UNIQUE, array('path'), array('varchar_pattern_ops'));
        $this->assertTrue($dbman->index_exists($table, $index));
    }

    public function test_index_max_bytes() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $maxstr = '';
        for($i=0; $i<255; $i++) {
            $maxstr .= '言'; // random long string that should fix exactly the limit for one char column
        }

        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, 255, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, array('name'));

        // Drop if exists
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);
        $tablename = $table->getName();
        $this->tables[$tablename] = $table;

        $rec = new stdClass();
        $rec->name = $maxstr;

        $id = $DB->insert_record($tablename, $rec);
        $this->assertTrue(!empty($id));

        $rec = $DB->get_record($tablename, array('id'=>$id));
        $this->assertSame($rec->name, $maxstr);

        $dbman->drop_table($table);


        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, 255+1, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, array('name'));

        try {
            $dbman->create_table($table);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof coding_exception);
        }
    }

    public function test_index_composed_max_bytes() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $maxstr = '';
        for($i=0; $i<200; $i++) {
            $maxstr .= '言';
        }
        $reststr = '';
        for($i=0; $i<133; $i++) {
            $reststr .= '言';
        }

        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name1', XMLDB_TYPE_CHAR, 200, null, XMLDB_NOTNULL, null);
        $table->add_field('name2', XMLDB_TYPE_CHAR, 133, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('name1-name2', XMLDB_INDEX_NOTUNIQUE, array('name1','name2'));

        // Drop if exists
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);
        $tablename = $table->getName();
        $this->tables[$tablename] = $table;

        $rec = new stdClass();
        $rec->name1 = $maxstr;
        $rec->name2 = $reststr;

        $id = $DB->insert_record($tablename, $rec);
        $this->assertTrue(!empty($id));

        $rec = $DB->get_record($tablename, array('id'=>$id));
        $this->assertSame($rec->name1, $maxstr);
        $this->assertSame($rec->name2, $reststr);


        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name1', XMLDB_TYPE_CHAR, 201, null, XMLDB_NOTNULL, null);
        $table->add_field('name2', XMLDB_TYPE_CHAR, 133, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('name1-name2', XMLDB_INDEX_NOTUNIQUE, array('name1','name2'));

        // Drop if exists
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        try {
            $dbman->create_table($table);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof coding_exception);
        }
    }

    public function test_char_size_limit() {
        $DB = $this->tdb;
        $dbman = $DB->get_manager();

        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, xmldb_field::CHAR_MAX_LENGTH, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Drop if exists
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);
        $tablename = $table->getName();
        $this->tables[$tablename] = $table;

        // this has to work in all DBs
        $maxstr = '';
        for($i=0; $i<xmldb_field::CHAR_MAX_LENGTH; $i++) {
            $maxstr .= 'a'; // ascii only
        }

        $rec = new stdClass();
        $rec->name = $maxstr;

        $id = $DB->insert_record($tablename, $rec);
        $this->assertTrue(!empty($id));

        $rec = $DB->get_record($tablename, array('id'=>$id));
        $this->assertSame($rec->name, $maxstr);


        // Following test is supposed to fail in oracle
        $maxstr = '';
        for($i=0; $i<xmldb_field::CHAR_MAX_LENGTH; $i++) {
            $maxstr .= '言'; // random long string that should fix exactly the limit for one char column
        }

        $rec = new stdClass();
        $rec->name = $maxstr;

        try {
            $id = $DB->insert_record($tablename, $rec);
            $this->assertTrue(!empty($id));

            $rec = $DB->get_record($tablename, array('id'=>$id));
            $this->assertSame($rec->name, $maxstr);
        } catch (dml_exception $e) {
            if ($DB->get_dbfamily() === 'oracle') {
                $this->fail('Oracle does not support text fields larger than 4000 bytes, this is not a big problem for mostly ascii based languages');
            } else {
                throw $e;
            }
        }


        $table = new xmldb_table('testtable');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, xmldb_field::CHAR_MAX_LENGTH+1, null, XMLDB_NOTNULL, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Drop if exists
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $tablename = $table->getName();
        $this->tables[$tablename] = $table;

        try {
            $dbman->create_table($table);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof coding_exception);
        }
    }

    public function test_object_name() {
        $gen = $this->tdb->get_manager()->generator;

        // This will form short object name and max length should not be exceeded.
        $table = 'tablename';
        $fields = 'id';
        $suffix = 'pk';
        for ($i=0; $i<12; $i++) {
            $this->assertLessThanOrEqual($gen->names_max_length,
                    strlen($gen->getNameForObject($table, $fields, $suffix)),
                    'Generated object name is too long. $i = '.$i);
        }

        // This will form too long object name always and it must be trimmed to exactly 30 chars.
        $table = 'aaaa_bbbb_cccc_dddd_eeee_ffff_gggg';
        $fields = 'aaaaa,bbbbb,ccccc,ddddd';
        $suffix = 'idx';
        for ($i=0; $i<12; $i++) {
            $this->assertEquals($gen->names_max_length,
                    strlen($gen->getNameForObject($table, $fields, $suffix)),
                    'Generated object name is too long. $i = '.$i);
        }

        // Same test without suffix.
        $table = 'bbbb_cccc_dddd_eeee_ffff_gggg_hhhh';
        $fields = 'aaaaa,bbbbb,ccccc,ddddd';
        $suffix = '';
        for ($i=0; $i<12; $i++) {
            $this->assertEquals($gen->names_max_length,
                    strlen($gen->getNameForObject($table, $fields, $suffix)),
                    'Generated object name is too long. $i = '.$i);
        }

        // This must only trim name when counter is 10 or more.
        $table = 'cccc_dddd_eeee_ffff_gggg_hhhh_iiii';
        $fields = 'id';
        $suffix = 'idx';
        // Since we don't know how long prefix is, loop to generate tablename that gives exactly maxlengh-1 length.
        // Skip this test if prefix is too long.
        while (strlen($table) && strlen($gen->prefix.preg_replace('/_/','',$table).'_id_'.$suffix) >= $gen->names_max_length) {
            $table = rtrim(substr($table, 0, strlen($table) - 1), '_');
        }
        if (strlen($table)) {
            $this->assertEquals($gen->names_max_length - 1,
                        strlen($gen->getNameForObject($table, $fields, $suffix)));
            for ($i=0; $i<12; $i++) {
                $this->assertEquals($gen->names_max_length,
                        strlen($gen->getNameForObject($table, $fields, $suffix)),
                        'Generated object name is too long. $i = '.$i);
            }
        }
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
