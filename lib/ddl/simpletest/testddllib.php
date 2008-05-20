<?php
/**
 * Unit tests for (some of) ddl lib.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/simpletestlib/web_tester.php');
require_once($CFG->libdir . '/ddllib.php');

class ddllib_test extends UnitTestCase {
    private $tables = array();
    private $db;
    private $dbmanager;

    public function setUp() {
        global $CFG;

        $this->db = new mysql_adodb_moodle_database();
        $this->db->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->dbpersist, $CFG->prefix);
        $this->dbmanager = $this->db->get_manager();

        $table = new XMLDBTable("testtable");
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
                array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('intro', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('logo', XMLDB_TYPE_BINARY, 'big', null, XMLDB_NOTNULL, null, null, null);
        $table->addFieldInfo('assessed', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('assesstimestart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('assesstimefinish', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('scale', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('maxbytes', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('forcesubscribe', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('trackingtype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1');
        $table->addFieldInfo('rsstype', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('rssarticles', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('grade', XMLDB_TYPE_NUMBER, '20,0', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('percent', XMLDB_TYPE_NUMBER, '5,2', null, null, null, null, null, null);
        $table->addFieldInfo('warnafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('blockafter', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('blockperiod', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('type-name', XMLDB_KEY_UNIQUE, array('type', 'name'));
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->addIndexInfo('rsstype', XMLDB_INDEX_UNIQUE, array('rsstype'));

        $table->setComment("This is a test'n drop table. You can drop it safely");
        $this->dbmanager->create_table($table);
        $this->tables[] = $table;

        // Second, smaller table
        $table = new XMLDBTable ('anothertest');
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');
        $table->addFieldInfo('secondname', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('intro', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('avatar', XMLDB_TYPE_BINARY, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('grade', XMLDB_TYPE_NUMBER, '20,10', null, null, null, null, null);
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

        $this->dbmanager->create_table($table);
        $this->tables[] = $table;
    }

    public function tearDown() {
        foreach ($this->tables as $key => $table) {
            if ($this->dbmanager->table_exists($table)) {
                $this->dbmanager->drop_table($table, true, false);
            }
        }
        unset($this->tables);

        setup_DB();
    }

    public function testCreateTable() {
        $table = new XMLDBTable("other_test_table");
        $field = new XMLDBField('id');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, true);
        $table->addField($field);
        $key = new XMLDBKey('PRIMARY');
        $key->setAttributes(XMLDB_KEY_PRIMARY, array('id'));
        $table->addKey($key);

        $this->assertTrue($this->dbmanager->create_table($table));
        $this->assertTrue($this->dbmanager->table_exists("other_test_table"));
        $this->dbmanager->drop_table($table);
    }

    public function testDropTable() {
        $table = $this->tables[0];
        $this->assertTrue($this->dbmanager->drop_table($table, true, false));
        $this->assertFalse($this->dbmanager->table_exists("testtable"));
    }

    public function testAddEnumField() {
        $table = $this->tables[0];
        /// Create a new field with complex specs (enums are good candidates)
        $field = new XMLDBField('type2');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
            array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');
        $this->assertTrue($this->dbmanager->add_field($table, $field));
        $this->assertTrue($this->dbmanager->field_exists($table, 'type2'));

        $this->dbmanager->drop_field($table, $field);
    }

    public function testAddNumericField() {
        $table = $this->tables[0];
        /// Create a new field with complex specs (enums are good candidates)
        $field = new XMLDBField('onenumber');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0, 'type');
        $this->assertTrue($this->dbmanager->add_field($table, $field));
        $this->assertTrue($this->dbmanager->field_exists($table, 'onenumber'));

        $this->dbmanager->drop_field($table, $field);
    }

    public function testDropField() {
        $table = $this->tables[0];
        $field = $table->getField('type');
        $name = $field->getName();

        $this->assertTrue($this->dbmanager->drop_field($table, $field));
        $this->assertFalse($this->dbmanager->field_exists($table, $name));
    }

    public function testChangeFieldType() {
        $table = $this->tables[1];
        $field = new XMLDBField('course');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, '0');
        $this->assertTrue($this->dbmanager->change_field_type($this->tables[1], $field));

        $field = new XMLDBField('course');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $this->assertTrue($this->dbmanager->change_field_type($this->tables[1], $field));

        $field = new XMLDBField('grade');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, "test'n drop");
        $this->assertTrue($this->dbmanager->change_field_type($this->tables[1], $field));

        $field = new XMLDBField('grade');
        $field->setAttributes(XMLDB_TYPE_FLOAT, '20,10', XMLDB_UNSIGNED, null, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_type($this->tables[1], $field));

        $field = new XMLDBField('grade');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'test');
        $this->assertTrue($this->dbmanager->change_field_type($this->tables[1], $field));

        $field = new XMLDBField('grade');
        $field->setAttributes(XMLDB_TYPE_NUMBER, '20,10', XMLDB_UNSIGNED, null, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_type($this->tables[1], $field));
    }

    public function testChangeFieldPrecision() {
        $table = $this->tables[1];
        $field = new XMLDBField('intro');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_precision($this->tables[1], $field));

        $field = new XMLDBField('secondname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_precision($this->tables[1], $field));

        $field = new XMLDBField('grade');
        $field->setAttributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_precision($this->tables[1], $field));

        $field = new XMLDBField('course');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $this->assertTrue($this->dbmanager->change_field_precision($this->tables[1], $field));
    }

    public function testChangeFieldSign() {
        $table = $this->tables[1];
        $field = new XMLDBField('grade');
        $field->setAttributes(XMLDB_TYPE_NUMBER, '10,2', XMLDB_UNSIGNED, null, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_unsigned($this->tables[1], $field));

        $field = new XMLDBField('grade');
        $field->setAttributes(XMLDB_TYPE_NUMBER, '10,2', null, null, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_unsigned($this->tables[1], $field));
    }

    public function testChangeFieldNullability() {
        $table = $this->tables[1];
        $field = new XMLDBField('name');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'Moodle');
        $this->assertTrue($this->dbmanager->change_field_notnull($this->tables[1], $field));

        $field = new XMLDBField('name');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');
        $this->assertTrue($this->dbmanager->change_field_notnull($this->tables[1], $field));
    }

    public function testChangeFieldDefault() {
        $table = $this->tables[1];
        $field = new XMLDBField('name');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_default($this->tables[1], $field));

        $field = new XMLDBField('name');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, 'Moodle');
        $this->assertTrue($this->dbmanager->change_field_default($this->tables[1], $field));

        $field = new XMLDBField('secondname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, 'Moodle2');
        $this->assertTrue($this->dbmanager->change_field_default($this->tables[1], $field));

        $field = new XMLDBField('secondname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $this->assertTrue($this->dbmanager->change_field_default($this->tables[1], $field));
    }

    public function testAddUniqueIndex() {
        $table = $this->tables[1];
        $index = new XMLDBIndex('secondname');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('name', 'secondname', 'grade'));
        $this->assertTrue($this->dbmanager->add_index($this->tables[1], $index));
    }

    public function testAddNonUniqueIndex() {
        $table = $this->tables[1];
        $index = new XMLDBIndex('secondname');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $this->assertTrue($this->dbmanager->add_index($this->tables[1], $index));
    }

    public function testFindIndexName() {
        $table = $this->tables[1];
        $index = new XMLDBIndex('secondname');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $this->dbmanager->add_index($this->tables[1], $index);

        // TODO DBM Systems name their indices differently. Maybe just test for non-false (or simply true)
        $this->assertEqual($this->dbmanager->find_index_name($this->tables[1], $index), 'mdl_anot_counam_ix');

        $nonexistentindex = new XMLDBIndex('nonexistentindex');
        $nonexistentindex->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('name'));
        $this->assertFalse($this->dbmanager->find_index_name($this->tables[1], $nonexistentindex));
    }

    public function testDropIndex() {
        $table = $this->tables[1];
        $index = new XMLDBIndex('secondname');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'name'));
        $this->dbmanager->add_index($this->tables[1], $index);

        $this->assertTrue($this->dbmanager->drop_index($this->tables[1], $index));
        $this->assertFalse($this->dbmanager->find_index_name($this->tables[1], $index));
    }

    public function testAddUniqueKey() {
        $table = $this->tables[1];
        $key = new XMLDBKey('id-course-grade');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('id', 'course', 'grade'));
        $this->assertTrue($this->dbmanager->add_key($this->tables[1], $key));
    }

    public function testAddForeignUniqueKey() {
        $table = $this->tables[1];
        $key = new XMLDBKey('course');
        $key->setAttributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'anothertest', array('id'));
        $this->assertTrue($this->dbmanager->add_key($this->tables[1], $key));
    }

    public function testDropKey() {
        $table = $this->tables[1];
        $key = new XMLDBKey('course');
        $key->setAttributes(XMLDB_KEY_FOREIGN_UNIQUE, array('course'), 'anothertest', array('id'));
        $this->dbmanager->add_key($this->tables[1], $key);

        $this->assertTrue($this->dbmanager->drop_key($this->tables[1], $key));
    }

    public function testAddForeignKey() {
        $table = $this->tables[1];
        $key = new XMLDBKey('course');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('course'), 'anothertest', array('id'));
        $this->assertTrue($this->dbmanager->add_key($this->tables[1], $key));
    }

    public function testDropForeignKey() {
        $table = $this->tables[1];
        $key = new XMLDBKey('course');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('course'), 'anothertest', array('id'));
        $this->dbmanager->add_key($this->tables[1], $key);

        $this->assertTrue($this->dbmanager->drop_key($this->tables[1], $key));
    }

    public function testChangeFieldEnum() {
        $table = $this->tables[0];
        // Removing an enum value
        $field = new XMLDBField('type');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
                array('news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');

        $this->assertTrue($this->dbmanager->change_field_enum($table, $field));

        // Adding an enum value
        $field = new XMLDBField('type');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
                array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');
        $this->assertTrue($this->dbmanager->change_field_enum($table, $field));
    }

    public function testRenameIndex() {
        $table = $this->tables[0];
        $index = new XMLDBIndex('course');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('course'));

        $this->assertTrue($this->dbmanager->rename_index($table, $index, 'newindexname'));
    }

    public function testRenameKey() {
        $table = $this->tables[0];
        $key = new XMLDBKey('course');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('course'));

        $this->assertTrue($this->dbmanager->rename_key($table, $key, 'newkeyname'));

    }

    public function testRenameField() {
        $table = $this->tables[0];
        $field = new XMLDBField('type');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, XMLDB_ENUM,
                array('single', 'news', 'general', 'social', 'eachuser', 'teacher', 'qanda'), 'general', 'course');

        $this->assertTrue($this->dbmanager->rename_field($table, $field, 'newfieldname'));
    }

    public function testRenameTable() {
        $table = $this->tables[0];
        $rand = round(rand() * 100);
        $this->assertFalse($this->dbmanager->table_exists('newtablename'. $rand));
        $this->assertTrue($this->dbmanager->rename_table($table, 'newtablename'. $rand));
    }

    public function testTableExists() {
        $table = $this->tables[0];
        // Test giving a string
        $this->assertFalse($this->dbmanager->table_exists('nonexistenttable'));
        $this->assertTrue($this->dbmanager->table_exists('testtable'));

        // Test giving a table object
        $nonexistenttable = new XMLDBTable('nonexistenttable');
        $this->assertFalse($this->dbmanager->table_exists($nonexistenttable));
        $this->assertTrue($this->dbmanager->table_exists($table));
    }

    public function testFieldExists() {
        $table = $this->tables[0];
        // String params
        // Give a nonexistent table as first param
        $this->assertFalse($this->dbmanager->field_exists('nonexistenttable', 'id'));

        // Give a nonexistent field as second param
        $this->assertFalse($this->dbmanager->field_exists('testtable', 'nonexistentfield'));

        // Correct string params
        $this->assertTrue($this->dbmanager->field_exists('testtable', 'id'));

        // Object params
        $realfield = $table->getField('id');

        // Give a nonexistent table as first param
        $nonexistenttable = new XMLDBTable('nonexistenttable');
        $this->assertFalse($this->dbmanager->field_exists($nonexistenttable, $realfield));

        // Give a nonexistent field as second param
        $nonexistentfield = new XMLDBField('nonexistentfield');
        $this->assertFalse($this->dbmanager->field_exists($table, $nonexistentfield));

        // Correct string params
        $this->assertTrue($this->dbmanager->field_exists($table, $realfield));
    }

    public function testIndexExists() {
        // Skipping: this is just a test of find_index_name
    }

    public function testFindCheckConstraintName() {
        $table = $this->tables[0];
        $field = $table->getField('type');
        $this->assertEqual($this->dbmanager->find_check_constraint_name($table, $field), 'type');
    }

    public function testCheckConstraintExists() {
        $table = $this->tables[0];
        $field = $table->getField('type');
        $this->assertTrue($this->dbmanager->check_constraint_exists($table, $field), 'type');
    }

    public function testFindKeyName() {
        $table = $this->tables[0];
        $key = $table->getKey('primary');
        $invalid_key = 'invalid_key';

        $this->assertFalse($this->dbmanager->find_key_name($table, $invalid_key));

        // With Mysql, the return value is actually "mdl_test_id_pk"
        $this->assertTrue($this->dbmanager->find_key_name($table, $key));
    }

    public function testFindSequenceName() {
        // give invalid table param
        $table = 'invalid_table';
        $this->assertFalse($this->dbmanager->find_sequence_name($table));

        // give nonexistent table param
        $table = new XMLDBTable("nonexistenttable");
        $this->assertFalse($this->dbmanager->find_sequence_name($table));

        // Give existing and valid table param
        $table = $this->tables[0];
        $this->assertEqual(false, $this->dbmanager->find_sequence_name($table));

    }

    public function testDeleteTablesFromXmldbFile() {

    }

    public function testInstallFromXmldbFile() {

    }

    public function testCreateTempTable() {

    }
}

?>
