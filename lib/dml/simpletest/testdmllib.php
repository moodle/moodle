<?php
/**
 * Unit tests for dmllib
 * @package dmllib
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class dmllib_test extends UnitTestCase {
    private $tables = array();
    private $db;

    function setUp() {
        global $CFG, $DB, $EXT_TEST_DB;

        if (isset($EXT_TEST_DB)) {
            $this->db = $EXT_TEST_DB;
        } else {
            $this->db = $DB;
        }

        $dbmanager = $this->db->get_manager();

        $table = new xmldb_table("testtable");
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
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
        $table->setComment("This is a test'n drop table. You can drop it safely");

        if ($dbmanager->table_exists($table)) {
            $dbmanager->drop_table($table, true, false);
        }
        $dbmanager->create_table($table);
        $this->tables[$table->getName()] = $table;

    }

    function tearDown() {
        $dbmanager = $this->db->get_manager();

        foreach ($this->tables as $table) {
            if ($dbmanager->table_exists($table)) {
                $dbmanager->drop_table($table, true, false);
            }
        }
        $this->tables = array();
    }

    function test_fix_sql_params() {
        $DB = $this->db; // do not use global $DB!

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
        $params = array('param1' => 'first record');
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}testtable WHERE name = ?param1", $sqlarray[0]);

        // Mixed param types (colon and dollar)
        $sql = "SELECT * FROM {testtable} WHERE name = :param1, rsstype = \$1";
        $params = array('param1' => 'first record', 'param2' => 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $this->assertEqual('error() call: ERROR: Mixed types of sql query parameters!!', $e->getMessage());
        }

        // Mixed param types (question and dollar)
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = \$1";
        $params = array('param1' => 'first record', 'param2' => 1);
        $exception_caught = false;
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Too many params in sql
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = ?, course = ?";
        $params = array('first record', 1);
        $exception_caught = false;
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
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
            $sqlarray = $DB->fix_sql_params($sql, $params);
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
            $sqlarray = $DB->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Duplicate named param in query
        $sql = "SELECT * FROM {testtable} WHERE name = :name, rsstype = :name";
        $params = array('name' => 'first record', 'rsstype' => 1);
        $exception_caught = false;
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Unsupported Bound params
        $sql = "SELECT * FROM {testtable} WHERE name = $1, rsstype = $2";
        $params = array('first record', 1);
        $exception_caught = false;
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
        } catch (Exception $e) {
            $exception_caught = true;
        }
        $this->assertTrue($exception_caught);

        // Correct named param placeholders
        $sql = "SELECT * FROM {testtable} WHERE name = :name, rsstype = :rsstype";
        $params = array('name' => 'first record', 'rsstype' => 1);
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}testtable WHERE name = ?, rsstype = ?", $sqlarray[0]);
        $this->assertEqual(2, count($sqlarray[1]));

        // Correct ? params
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = ?";
        $params = array('first record', 1);
        $sqlarray = $DB->fix_sql_params($sql, $params);
        $this->assertEqual("SELECT * FROM {$DB->get_prefix()}testtable WHERE name = ?, rsstype = ?", $sqlarray[0]);
        $this->assertEqual(2, count($sqlarray[1]));

    }

}

?>
