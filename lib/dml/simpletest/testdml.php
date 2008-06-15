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

    function setUp() {
        global $CFG, $DB, $UNITTEST;

        if (isset($UNITTEST->func_test_db)) {
            $this->tdb = $UNITTEST->func_test_db;
        } else {
            $this->tdb = $DB;
        }

        $dbman = $this->tdb->get_manager();

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

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table, true, false);
        }
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

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
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

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
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Mixed param types (question and dollar)
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = \$1";
        $params = array('param1' => 'first record', 'param2' => 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Too many params in sql
        $sql = "SELECT * FROM {testtable} WHERE name = ?, rsstype = ?, course = ?";
        $params = array('first record', 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Too many params in array: no error
        $params[] = 1;
        $params[] = time();
        $sqlarray = null;

        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
        $this->assertTrue($sqlarray[0]);

        // Named params missing from array
        $sql = "SELECT * FROM {testtable} WHERE name = :name, rsstype = :rsstype";
        $params = array('wrongname' => 'first record', 'rsstype' => 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Duplicate named param in query
        $sql = "SELECT * FROM {testtable} WHERE name = :name, rsstype = :name";
        $params = array('name' => 'first record', 'rsstype' => 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

        // Unsupported Bound params
        $sql = "SELECT * FROM {testtable} WHERE name = $1, rsstype = $2";
        $params = array('first record', 1);
        try {
            $sqlarray = $DB->fix_sql_params($sql, $params);
            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof moodle_exception);
        }

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

    public function testGetTables() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        // Need to test with multiple DBs
        $this->assertTrue($DB->get_tables() > 2);
    }

    public function testGetIndexes() {
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $this->assertTrue($indices = $DB->get_indexes('testtable'));
        $this->assertTrue(count($indices) == 1);

        $xmldb_indexes = $this->tables['testtable']->getIndexes();
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
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

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
        $DB = $this->tdb; // do not use global $DB!
        $dbman = $this->tdb->get_manager();

        $sql = "SELECT * FROM {testtable}";
        $this->assertTrue($DB->execute($sql));

        $sql = "INSERT INTO {testtable}
                        SET course = :course,
                            type = :type,
                            name = :name,
                            intro = :intro,
                            assessed = :assessed,
                            assesstimestart = :assesstimestart,
                            assesstimefinish = :assesstimefinish,
                            scale = :scale,
                            maxbytes = :maxbytes,
                            forcesubscribe = :forcesubscribe,
                            trackingtype = :trackingtype,
                            rsstype = :rsstype,
                            rssarticles = :rssarticles,
                            timemodified = :timemodified,
                            warnafter = :warnafter,
                            blockafter = :blockafter,
                            blockperiod = :blockperiod";
        $values = array('course' => 1,
                        'type' => 'news',
                        'name' => 'test',
                        'intro' => 'Simple news forum',
                        'assessed' => time(),
                        'assesstimestart' => time(),
                        'assesstimefinish' => time() + 579343,
                        'scale' => 1,
                        'maxbytes' => 512,
                        'forcesubscribe' => 1,
                        'trackingtype' => 1,
                        'rssarticles' => 1,
                        'rsstype' => 1,
                        'timemodified' => time(),
                        'warnafter' => time() + 579343,
                        'blockafter' => time() + 600000,
                        'blockperiod' => 5533);
        $this->assertTrue($DB->execute($sql, $values));

        $record = $DB->get_record('testtable', array('blockperiod' => 5533));

        foreach ($values as $field => $value) {
            $this->assertEqual($value, $record->$field, "Field $field in DB ({$record->$field}) is not equal to field $field in sql ($value)");
        }
    }

}
