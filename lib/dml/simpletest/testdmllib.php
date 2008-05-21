<?php
/**
 * Unit tests for dmllib
 * @package dmllib
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/simpletestlib/web_tester.php');
require_once($CFG->libdir . '/dmllib.php');
require_once($CFG->libdir . '/dml/mysql_adodb_moodle_database.php');

class dmllib_test extends UnitTestCase {
    private $tables = array();
    private $dbmanager;
    private $db;

    function setUp() {
        global $CFG;

        $this->db = new mysqli_adodb_moodle_database();
        $this->db->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->dbpersist, $CFG->prefix);
        $this->dbmanager = $this->db->get_manager();

        $table = new xmldb_table("testtable");
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
        $table->addIndexInfo('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

        $table->setComment("This is a test'n drop table. You can drop it safely");

        $this->dbmanager->create_table($table);
        $this->tables[] = $table;

        // insert records
        $datafile = $CFG->libdir . '/dml/simpletest/fixtures/testdata.xml';
        $xml = simplexml_load_file($datafile);

        foreach ($xml->record as $record) {
            $this->db->insert_record('testtable', $record);
        }
    }

    function tearDown() {
        foreach ($this->tables as $key => $table) {
            if ($this->dbmanager->table_exists($table)) {
                $this->dbmanager->drop_table($table, true, false);
            }
        }
        unset($this->tables);

        setup_DB();
    }

    function test_insert_record() {

    }

    function test_get_record_select() {
        $record = $this->db->get_record_select('testtable', 'id = 1');
    }

    function test_fix_sql_params() {
        // Malformed table placeholder
        $sql = "SELECT * FROM [testtable]";
        $sqlarray = $this->db->fix_sql_params($sql);
        $this->assertEqual($sql, $sqlarray[0]);

        // Correct table placeholder substitution
        $sql = "SELECT * FROM {testtable}";
        $sqlarray = $this->db->fix_sql_params($sql);
        $this->assertEqual("SELECT * FROM {$this->db->get_prefix()}testtable", $sqlarray[0]);

        // Malformed param placeholders
        $sql = "SELECT * FROM {testtable} WHERE name = ?param1";
        $params = array('param1' => 'first record');
        $sqlarray = $this->db->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$this->db->get_prefix()}testtable WHERE name = ?param1", $sqlarray[0]);

        // Mixed param types (colon and dollar)
        $sql = "SELECT * FROM {testtable} WHERE name = :param1, rsstype = \$1";
        $params = array('param1' => 'first record', 'param2' => 1);
        try {
            $sqlarray = $this->db->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $this->assertEqual('error() call: ERROR: Mixed types of sql query parameters!!', $e->getMessage());
        }

        // Mixed param types (question and dollar)
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = \$1";
        $params = array('param1' => 'first record', 'param2' => 1);
        $exception_caught = false;
        try {
            $sqlarray = $this->db->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Too many params in sql
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = ?, course = ?";
        $params = array('first record', 1);
        $exception_caught = false;
        try {
            $sqlarray = $this->db->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Too many params in array: no error
        $params[] = 1;
        $params[] = time();
        $exception_caught = false;
        $sqlarray = null;

        try {
            $sqlarray = $this->db->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertFalse($exception_caught);
        $this->assertTrue($sqlarray[0]);

        // Named params missing from array
        $sql = "SELECT * FROM {testtable} WHERE name = :name, rsstype = :rsstype";
        $params = array('wrongname' => 'first record', 'rsstype' => 1);
        $exception_caught = false;
        try {
            $sqlarray = $this->db->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Duplicate named param in query
        $sql = "SELECT * FROM {testtable} WHERE name = :name, rsstype = :name";
        $params = array('name' => 'first record', 'rsstype' => 1);
        $exception_caught = false;
        try {
            $sqlarray = $this->db->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Unsupported Bound params
        $sql = "SELECT * FROM {testtable} WHERE name = $1, rsstype = $2";
        $params = array('first record', 1);
        $exception_caught = false;
        try {
            $sqlarray = $this->db->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Correct named param placeholders
        $sql = "SELECT * FROM {testtable} WHERE name = :name, rsstype = :rsstype";
        $params = array('name' => 'first record', 'rsstype' => 1);
        $sqlarray = $this->db->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$this->db->get_prefix()}testtable WHERE name = ?, rsstype = ?", $sqlarray[0]);
        $this->assertEqual(2, count($sqlarray[1]));

        // Correct ? params
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = ?";
        $params = array('first record', 1);
        $sqlarray = $this->db->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$this->db->get_prefix()}testtable WHERE name = ?, rsstype = ?", $sqlarray[0]);
        $this->assertEqual(2, count($sqlarray[1]));

    }

}

?>
