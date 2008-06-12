<?php
/**
 * Unit tests for (some of) ddl lib.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/ddllib.php');

class ddllib_test extends UnitTestCase {
    private $tables = array();
    private $db;

    public function setUp() {
        global $CFG, $DB, $FUNCT_TEST_DB;

        if (isset($FUNCT_TEST_DB)) {
            $this->db = $FUNCT_TEST_DB;
        } else {
            $this->db = $DB;
        }

        $dbmanager = $this->db->get_manager();

        $table = new xmldb_table('test_table0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
                          array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('logo', XMLDB_TYPE_BINARY, 'big', null, XMLDB_NOTNULL, null, null, null);
        $table->add_field('assessed', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('assesstimestart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('assesstimefinish', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('scale', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('maxbytes', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('forcesubscribe', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('trackingtype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1');
        $table->add_field('rsstype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('rssarticles', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '20,0', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->add_field('percent', XMLDB_TYPE_NUMBER, '5,2', null, null, null, null, null, null);
        $table->add_field('warnafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('blockafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('blockperiod', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('type-name', XMLDB_KEY_UNIQUE, array('type', 'name'));
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->add_index('rsstype', XMLDB_INDEX_UNIQUE, array('rsstype'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        // Second, smaller table
        $table = new xmldb_table ('test_table1');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');
        $table->add_field('secondname', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('avatar', XMLDB_TYPE_BINARY, 'medium', null, null, null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '20,10', null, null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->tables[$table->getName()] = $table;

        // make sure no tables are present!
        $this->tearDown();
    }

    public function tearDown() {
        $dbmanager = $this->db->get_manager();

        // drop custom test tables
        for ($i=0; $i<3; $i++) {
            $table = new xmldb_table('test_table_cust'.$i);
            if ($dbmanager->table_exists($table)) {
                $dbmanager->drop_table($table, true, false);
            }
        }

        // drop default tables
        foreach ($this->tables as $table) {
            if ($dbmanager->table_exists($table)) {
                $dbmanager->drop_table($table, true, false);
            }
        }
    }

    private function create_deftable($tablename) {
        $dbmanager = $this->db->get_manager();

        if (!isset($this->tables[$tablename])) {
            return null;
        }

        $table = $this->tables[$tablename];

        if ($dbmanager->table_exists($table)) {
            $dbmanager->drop_table($table, true, false);
        }
        $dbmanager->create_table($table, true, false);

        return $table;
    }

    public function testTableExists() {
        $DB = $this->db; // do not use global $DB!
        $dbmanager = $this->db->get_manager();

        // first make sure it returns false if table does not exist
        $table = $this->tables['test_table0'];
        ob_start(); // hide debug warning
        $this->assertFalse($DB->get_records('test_table0'));
        ob_end_clean();
        $this->assertFalse($dbmanager->table_exists('test_table0'));
        $this->assertFalse($dbmanager->table_exists($table));

        // create table and test again
        $this->assertTrue($dbmanager->create_table($table, true, false));
        $this->assertTrue($DB->get_records('test_table0') !== false);
        $this->assertTrue($dbmanager->table_exists('test_table0'));
        $this->assertTrue($dbmanager->table_exists($table));

        // Test giving a string
        $this->assertFalse($dbmanager->table_exists('nonexistenttable'));
        $this->assertTrue($dbmanager->table_exists('test_table0'));
    }

    public function testCreateTable() {
        $DB = $this->db; // do not use global $DB!
        $dbmanager = $this->db->get_manager();

        // Give a wrong table param (expect a debugging message)
        $table = 'string';
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->create_table($table));
        ob_end_clean();

        // create table and do basic column tests
        $table = $this->tables['test_table1'];

        $this->assertTrue($dbmanager->create_table($table));
        $this->assertTrue($dbmanager->table_exists($table));

        $columns = $DB->get_columns('test_table1');

        $this->assertEqual($columns['id']->meta_type, 'R');
        $this->assertEqual($columns['course']->meta_type, 'I');
        $this->assertEqual($columns['name']->meta_type, 'C');
        $this->assertEqual($columns['secondname']->meta_type, 'C');
        $this->assertEqual($columns['intro']->meta_type, 'X');
        $this->assertEqual($columns['avatar']->meta_type, 'B');
        $this->assertEqual($columns['grade']->meta_type, 'N');

    }

    public function testDropTable() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');

        $this->assertTrue($dbmanager->drop_table($table, true, false));
        $this->assertFalse($dbmanager->table_exists('test_table0'));

        // Try dropping non-existent table
        $table = new xmldb_table('nonexistenttable');
        ob_start(); // hide debug warning
        $this->assertTrue($dbmanager->drop_table($table, true, false));
        ob_end_clean();

        // Give a wrong table param
        $table = 'string';
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->drop_table($table, true, false));
        ob_end_clean();
    }


    public function testAddEnumField() {
        $DB = $this->db; // do not use global $DB!
        $dbmanager = $this->db->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $this->assertTrue($dbmanager->create_table($table, true, false));

        $enums = array('single', 'news', 'general');

        /// Create a new field with complex specs (enums are good candidates)
        $field = new xmldb_field('type1');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM, $enums, 'general', 'course');
        $this->assertTrue($dbmanager->add_field($table, $field));
        $this->assertTrue($dbmanager->field_exists($table, 'type1'));

        $field = new xmldb_field('type2');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, null, null, XMLDB_ENUM, $enums, 'general', 'course');
        $this->assertTrue($dbmanager->add_field($table, $field));
        $this->assertTrue($dbmanager->field_exists($table, 'type2'));

        /// try inserting a good record
        $record = new object();
        $record->course = 666;
        $record->type1 = 'news';
        $record->type2 = NULL;
        $this->assertTrue($DB->insert_record('test_table_cust0', $record, false));

        /// try inserting a bad record
        $record = new object();
        $record->course = 666;
        $record->type1 = 'xxxxxxxx';
        $record->type2 = 'news';
        ob_start(); // hide debug warning
        $this->assertFalse($DB->insert_record('test_table_cust0', $record));
        ob_end_clean();

        /// try inserting a bad record
        $record = new object();
        $record->course = 666;
        $record->type1 = 'news';
        $record->type2 = 'xxxx';
        ob_start(); // hide debug warning
        $this->assertFalse($DB->insert_record('test_table_cust0', $record));
        ob_end_clean();

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['type1']->meta_type, 'C');

        // enums field is optional
        $result = $columns['type1']->enums;
        if (!is_null($result)) {
            $this->assertEqual($result, $enums);
        }

        /// cleanup
        $dbmanager->drop_field($table, $field);
        $dbmanager->drop_table($table);
    }

    public function testAddNumericField() {
        $DB = $this->db; // do not use global $DB!
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        /// Create a new field with complex specs (enums are good candidates)
        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0, 'type');
        $this->assertTrue($dbmanager->add_field($table, $field));
        $this->assertTrue($dbmanager->field_exists($table, 'onenumber'));

        $columns = $DB->get_columns('test_table0');
        $this->assertEqual($columns['onenumber']->meta_type, 'I');

        $dbmanager->drop_field($table, $field);
    }

    public function testDropField() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = $table->getField('type');

        $this->assertTrue($dbmanager->field_exists($table, $field));
        $this->assertTrue($dbmanager->field_exists($table, 'type'));

        $this->assertTrue($dbmanager->drop_field($table, $field));

        $this->assertFalse($dbmanager->field_exists($table, 'type'));
    }

    public function testChangeFieldType() {
        $DB = $this->db; // do not use global $DB!
        $dbmanager = $this->db->get_manager();

        $table = new xmldb_table('test_table_cust0');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('onenumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbmanager->create_table($table, true, false);

        $record = new object();
        $recorf->course = 2;
        $DB->insert_record('test_table_cust0', $record);

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, '0');
        $this->assertTrue($dbmanager->change_field_type($table, $field));

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'C');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $this->assertTrue($dbmanager->change_field_type($table, $field));

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'I');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, "test'n drop");
        $this->assertTrue($dbmanager->change_field_type($table, $field));

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'C');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_FLOAT, '20,10', XMLDB_UNSIGNED, null, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_type($table, $field));

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'N');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'test');
        $this->assertTrue($dbmanager->change_field_type($table, $field));

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'C');

        $field = new xmldb_field('onenumber');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_type($table, $field));

        $columns = $DB->get_columns('test_table_cust0');
        $this->assertEqual($columns['onenumber']->meta_type, 'N');
    }

    public function testChangeFieldPrecision() {
        $dbmanager = $this->db->get_manager();
// TODO: verify the precision is changed in db

        $table = $this->create_deftable('test_table1');
        $field = new xmldb_field('intro');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_precision($table, $field));

        $field = new xmldb_field('secondname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_precision($table, $field));

        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_precision($table, $field));

        $field = new xmldb_field('course');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $this->assertTrue($dbmanager->change_field_precision($table, $field));
    }

    public function testChangeFieldSign() {
        $dbmanager = $this->db->get_manager();
// TODO: verify the signed is changed in db

        $table = $this->create_deftable('test_table1');
        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', XMLDB_UNSIGNED, null, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_unsigned($table, $field));

        $field = new xmldb_field('grade');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_unsigned($table, $field));
    }

    public function testChangeFieldNullability() {
        $dbmanager = $this->db->get_manager();
// TODO: verify the type is nullability in db

        $table = $this->create_deftable('test_table1');
        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'Moodle');
        $this->assertTrue($dbmanager->change_field_notnull($table, $field));

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');
        $this->assertTrue($dbmanager->change_field_notnull($table, $field));
    }

    public function testChangeFieldDefault() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_default($table, $field));

        $field = new xmldb_field('name');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');
        $this->assertTrue($dbmanager->change_field_default($table, $field));

        $field = new xmldb_field('secondname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, 'Moodle2');
        $this->assertTrue($dbmanager->change_field_default($table, $field));

        $field = new xmldb_field('secondname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $this->assertTrue($dbmanager->change_field_default($table, $field));
    }

    public function testAddUniqueIndex() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('name', 'secondname', 'grade'));
        $this->assertTrue($dbmanager->add_index($table, $index));
    }

    public function testAddNonUniqueIndex() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $this->assertTrue($dbmanager->add_index($table, $index));
    }

    public function testFindIndexName() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $dbmanager->add_index($table, $index);

        // TODO DBM Systems name their indices differently. Maybe just test for non-false (or simply true)
        // $this->assertEqual($dbmanager->find_index_name($table, $index), 'mdl_anot_counam_ix');

        $nonexistentindex = new xmldb_index('nonexistentindex');
        $nonexistentindex->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('name'));
        $this->assertFalse($dbmanager->find_index_name($table, $nonexistentindex));
    }

    public function testDropIndex() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $index = new xmldb_index('secondname');
        $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $dbmanager->add_index($table, $index);

        $this->assertTrue($dbmanager->drop_index($table, $index));
        $this->assertFalse($dbmanager->find_index_name($table, $index));
    }

    public function testAddUniqueKey() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $key = new xmldb_key('id-course-grade');
        $key->set_attributes(XMLDB_KEY_UNIQUE, array('id', 'course', 'grade'));
        $this->assertTrue($dbmanager->add_key($table, $key));
    }

    public function testAddForeignUniqueKey() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'test_table0', array('id'));
        $this->assertTrue($dbmanager->add_key($table, $key));
    }

    public function testDropKey() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'test_table0', array('id'));
        $dbmanager->add_key($table, $key);

        $this->assertTrue($dbmanager->drop_key($table, $key));
    }

    public function testAddForeignKey() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('course'), 'test_table0', array('id'));
        $this->assertTrue($dbmanager->add_key($table, $key));
    }

    public function testDropForeignKey() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table1');
        $this->create_deftable('test_table0');

        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('course'), 'test_table0', array('id'));
        $dbmanager->add_key($table, $key);

        $this->assertTrue($dbmanager->drop_key($table, $key));
    }

    public function testChangeFieldEnum() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        // Removing an enum value
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null);

        $this->assertTrue($dbmanager->change_field_enum($table, $field));

        // Adding an enum value
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
                array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');
        $this->assertTrue($dbmanager->change_field_enum($table, $field));
    }

    public function testRenameField() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
                array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');

        $this->assertTrue($dbmanager->rename_field($table, $field, 'newfieldname'));
    }

    public function testRenameTable() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');

        $this->assertFalse($dbmanager->table_exists('test_table_cust0'));
        $this->assertTrue($dbmanager->rename_table($table, 'test_table_cust0'));

        $table->setName('test_table_cust0');
        $dbmanager->drop_table($table);
    }

    public function testFieldExists() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        // String params
        // Give a nonexistent table as first param
        $this->assertFalse($dbmanager->field_exists('nonexistenttable', 'id'));

        // Give a nonexistent field as second param
        $this->assertFalse($dbmanager->field_exists('test_table0', 'nonexistentfield'));

        // Correct string params
        $this->assertTrue($dbmanager->field_exists('test_table0', 'id'));

        // Object params
        $realfield = $table->getField('id');

        // Give a nonexistent table as first param
        $nonexistenttable = new xmldb_table('nonexistenttable');
        $this->assertFalse($dbmanager->field_exists($nonexistenttable, $realfield));

        // Give a nonexistent field as second param
        $nonexistentfield = new xmldb_field('nonexistentfield');
        $this->assertFalse($dbmanager->field_exists($table, $nonexistentfield));

        // Correct string params
        $this->assertTrue($dbmanager->field_exists($table, $realfield));
    }

    public function testIndexExists() {
        $dbmanager = $this->db->get_manager();

        // Skipping: this is just a test of find_index_name
    }

    public function testFindCheckConstraintName() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = $table->getField('type');
        $result = $dbmanager->find_check_constraint_name($table, $field);
        $this->assertTrue(!empty($result));
    }

    public function testCheckConstraintExists() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        $field = $table->getField('type');
        $this->assertTrue($dbmanager->check_constraint_exists($table, $field), 'type');
    }

    public function testFindKeyName() {
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        $key = $table->getKey('primary');
        $invalid_key = 'invalid_key';

        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->find_key_name($table, $invalid_key));
        ob_end_clean();

        // With Mysql, the return value is actually "mdl_test_id_pk"
        $result = $dbmanager->find_key_name($table, $key);
        $this->assertTrue(!empty($result));
    }

    public function testFindSequenceName() {
        $dbmanager = $this->db->get_manager();

        // give invalid table param
        $table = 'invalid_table';
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->find_sequence_name($table));
        ob_end_clean();

        // give nonexistent table param
        $table = new xmldb_table("nonexistenttable");
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->find_sequence_name($table));
        ob_end_clean();

        // Give existing and valid table param
        $table = $this->create_deftable('test_table0');
//TODO: this returns stuff depending on db internals
        // $this->assertEqual(false, $dbmanager->find_sequence_name($table));

    }

    public function testDeleteTablesFromXmldbFile() {
        global $CFG;
        $dbmanager = $this->db->get_manager();

        unset($CFG->xmldbreconstructprevnext); // remove this unhack ;-)

        $this->create_deftable('test_table1');

        $this->assertTrue($dbmanager->table_exists('test_table1'));

        // feed nonexistent file
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->delete_tables_from_xmldb_file('fpsoiudfposui', false));
        ob_end_clean();

        // Real file but invalid xml file
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->delete_tables_from_xmldb_file($CFG->libdir . '/ddl/simpletest/fixtures/invalid.xml', false));
        ob_end_clean();

        // Check that the table has not been deleted from DB
        $this->assertTrue($dbmanager->table_exists('test_table1'));

        // Real and valid xml file
        $this->assertTrue($dbmanager->delete_tables_from_xmldb_file($CFG->libdir . '/ddl/simpletest/fixtures/xmldb_table.xml', false));

        // Check that the table has been deleted from DB
        $this->assertFalse($dbmanager->table_exists('test_table1'));
    }

    public function testInstallFromXmldbFile() {
        global $CFG;
        $dbmanager = $this->db->get_manager();

        // feed nonexistent file
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->install_from_xmldb_file('fpsoiudfposui', false));
        ob_end_clean();

        // Real but invalid xml file
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->install_from_xmldb_file($CFG->libdir . '/ddl/simpletest/fixtures/invalid.xml', false));
        ob_end_clean();

        // Check that the table has not yet been created in DB
        $this->assertFalse($dbmanager->table_exists('test_table1'));

        // Real and valid xml file
        $this->assertTrue($dbmanager->install_from_xmldb_file($CFG->libdir . '/ddl/simpletest/fixtures/xmldb_table.xml', false));
        $this->assertTrue($dbmanager->table_exists('test_table1'));
    }

    public function testCreateTempTable() {
        $dbmanager = $this->db->get_manager();

        // Feed incorrect table param
        ob_start(); // hide debug warning
        $this->assertFalse($dbmanager->create_temp_table('test_table1'));
        ob_end_clean();

        $table = $this->tables['test_table1'];

        // New table
        $this->assertTrue($dbmanager->create_temp_table($table));
        $this->assertTrue($dbmanager->table_exists('test_table1', true));

        // Delete
        $this->assertTrue($dbmanager->drop_temp_table($table));
        $this->assertFalse($dbmanager->table_exists('test_table1', true));
    }


 // Following methods are not supported == Do not test
/*
    public function testRenameIndex() {
        // unsupported!
        $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        $index = new xmldb_index('course');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('course'));

        $this->assertTrue($dbmanager->rename_index($table, $index, 'newindexname'));
    }

    public function testRenameKey() {
        //unsupported
         $dbmanager = $this->db->get_manager();

        $table = $this->create_deftable('test_table0');
        $key = new xmldb_key('course');
        $key->set_attributes(XMLDB_KEY_UNIQUE, array('course'));

        $this->assertTrue($dbmanager->rename_key($table, $key, 'newkeyname'));
    }
*/

}
?>
