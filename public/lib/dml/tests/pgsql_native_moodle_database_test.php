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
 * Test specific features of the Postgres dml.
 *
 * @package core
 * @category test
 * @copyright 2020 Ruslan Kabalin
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use stdClass, ReflectionClass;
use moodle_database, pgsql_native_moodle_database;
use xmldb_table;
use moodle_exception;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;

/**
 * Test specific features of the Postgres dml.
 *
 * @package core
 * @category test
 * @copyright 2020 Ruslan Kabalin
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  \pgsql_native_moodle_database
 */
final class pgsql_native_moodle_database_test extends \advanced_testcase {

    /**
     * Setup before class.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir.'/dml/pgsql_native_moodle_database.php');
        parent::setUpBeforeClass();
    }

    /**
     * Set up.
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
        // Skip tests if not using Postgres.
        if (!($DB instanceof pgsql_native_moodle_database)) {
            $this->markTestSkipped('Postgres-only test');
        }
    }

    /**
     * Get a xmldb_table object for testing, deleting any existing table
     * of the same name, for example if one was left over from a previous test
     * run that crashed.
     *
     * @param string $suffix table name suffix, use if you need more test tables
     * @return xmldb_table the table object.
     */
    private function get_test_table($suffix = ''): xmldb_table {
        $tablename = "test_table";
        if ($suffix !== '') {
            $tablename .= $suffix;
        }

        $table = new xmldb_table($tablename);
        $table->setComment("This is a test'n drop table. You can drop it safely");
        return $table;
    }

    /**
     * Find out the current index used for unique SQL_PARAMS_NAMED.
     *
     * @return int
     */
    private function get_current_index(): int {
        global $DB;
        $reflector = new ReflectionClass($DB);
        $property = $reflector->getProperty('inorequaluniqueindex');
        return (int) $property->getValue($DB);
    }

    public function test_get_in_or_equal_below_limit(): void {
        global $DB;
        // Just less than 65535 values, expect fallback to parent method.
        $invalues = range(1, 65533);
        list($usql, $params) = $DB->get_in_or_equal($invalues);
        $this->assertSame('IN ('.implode(',', array_fill(0, count($invalues), '?')).')', $usql);
        $this->assertEquals(count($invalues), count($params));
        foreach ($params as $key => $value) {
            $this->assertSame($invalues[$key], $value);
        }
    }

    public function test_get_in_or_equal_single_array_value(): void {
        global $DB;
        // Single value (in an array), expect fallback to parent method.
        $invalues = array('value1');
        list($usql, $params) = $DB->get_in_or_equal($invalues);
        $this->assertEquals("= ?", $usql);
        $this->assertCount(1, $params);
        $this->assertEquals($invalues[0], $params[0]);
    }

    public function test_get_in_or_equal_single_scalar_value(): void {
        global $DB;
        // Single value (scalar), expect fallback to parent method.
        $invalue = 'value1';
        list($usql, $params) = $DB->get_in_or_equal($invalue);
        $this->assertEquals("= ?", $usql);
        $this->assertCount(1, $params);
        $this->assertEquals($invalue, $params[0]);
    }

    public function test_get_in_or_equal_multiple_int_value(): void {
        global $DB;
        // 65535 values, int.
        $invalues = range(1, 65535);
        list($usql, $params) = $DB->get_in_or_equal($invalues);
        $this->assertSame('IN (VALUES ('.implode('),(', array_fill(0, count($invalues), '?::bigint')).'))', $usql);
        $this->assertEquals($params, $invalues);
    }

    public function test_get_in_or_equal_multiple_int_value_not_equal(): void {
        global $DB;
        // 65535 values, not equal, int.
        $invalues = range(1, 65535);
        list($usql, $params) = $DB->get_in_or_equal($invalues, SQL_PARAMS_QM, 'param', false);
        $this->assertSame('NOT IN (VALUES ('.implode('),(', array_fill(0, count($invalues), '?::bigint')).'))', $usql);
        $this->assertEquals($params, $invalues);
    }

    public function test_get_in_or_equal_named_int_value_default_name(): void {
        global $DB;
        // 65535 values, int, SQL_PARAMS_NAMED.
        $index = $this->get_current_index();
        $invalues = range(1, 65535);
        list($usql, $params) = $DB->get_in_or_equal($invalues, SQL_PARAMS_NAMED);
        $regex = '/^'.
            preg_quote('IN (VALUES (:param'.$index.'::bigint),(:param'.++$index.'::bigint),(:param'.++$index.'::bigint)').'/';
        $this->assertMatchesRegularExpression($regex, $usql);
        foreach ($params as $value) {
            $this->assertEquals(current($invalues), $value);
            next($invalues);
        }
    }

    public function test_get_in_or_equal_named_int_value_specified_name(): void {
        global $DB;
        // 65535 values, int, SQL_PARAMS_NAMED, define param name.
        $index = $this->get_current_index();
        $invalues = range(1, 65535);
        list($usql, $params) = $DB->get_in_or_equal($invalues, SQL_PARAMS_NAMED, 'ppp');
        // We are in same DBI instance, expect uniqie param indexes.
        $regex = '/^'.
            preg_quote('IN (VALUES (:ppp'.$index.'::bigint),(:ppp'.++$index.'::bigint),(:ppp'.++$index.'::bigint)').'/';
        $this->assertMatchesRegularExpression($regex, $usql);
        foreach ($params as $value) {
            $this->assertEquals(current($invalues), $value);
            next($invalues);
        }
    }

    public function test_get_in_or_equal_named_scalar_value_specified_name(): void {
        global $DB;
        // 65535 values, string.
        $invalues = array_fill(1, 65535, 'abc');
        list($usql, $params) = $DB->get_in_or_equal($invalues);
        $this->assertMatchesRegularExpression('/^' . preg_quote('IN (VALUES (?::text),(?::text),(?::text)') . '/', $usql);
        foreach ($params as $value) {
            $this->assertEquals(current($invalues), $value);
            next($invalues);
        }
    }

    public function test_get_in_or_equal_query_use(): void {
        global $DB;
        $this->resetAfterTest();
        $dbman = $DB->get_manager();
        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

        $rec1 = ['course' => 3, 'content' => 'hello', 'name' => 'xyz'];
        $DB->insert_record($tablename, $rec1);
        $rec2 = ['course' => 3, 'content' => 'world', 'name' => 'abc'];
        $DB->insert_record($tablename, $rec2);
        $rec3 = ['course' => 5, 'content' => 'hello', 'name' => 'xyz'];
        $DB->insert_record($tablename, $rec3);
        $rec4 = ['course' => 6, 'content' => 'universe'];
        $DB->insert_record($tablename, $rec4);

        $currentcount = $DB->count_records($tablename);

        // Getting all 4.
        $values = range(1, 65535);
        list($insql, $inparams) = $DB->get_in_or_equal($values);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE id $insql
              ORDER BY id ASC";
        $this->assertCount($currentcount, $DB->get_records_sql($sql, $inparams));

        // Getting 'hello' records (text).
        $values = array_fill(1, 65535, 'hello');
        list($insql, $inparams) = $DB->get_in_or_equal($values);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE content $insql
              ORDER BY id ASC";
        $result = $DB->get_records_sql($sql, $inparams);
        $this->assertCount(2, $result);
        $this->assertEquals([1, 3], array_keys($result));

        // Getting NOT 'hello' records (text).
        $values = array_fill(1, 65535, 'hello');
        list($insql, $inparams) = $DB->get_in_or_equal($values, SQL_PARAMS_QM, 'param', false);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE content $insql
              ORDER BY id ASC";
        $result = $DB->get_records_sql($sql, $inparams);
        $this->assertCount(2, $result);
        $this->assertEquals([2, 4], array_keys($result));

        // Getting 'xyz' records (char and NULL mix).
        $values = array_fill(1, 65535, 'xyz');
        list($insql, $inparams) = $DB->get_in_or_equal($values);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE name $insql
              ORDER BY id ASC";
        $result = $DB->get_records_sql($sql, $inparams);
        $this->assertCount(2, $result);
        $this->assertEquals([1, 3], array_keys($result));

        // Getting NOT 'xyz' records (char and NULL mix).
        $values = array_fill(1, 65535, 'xyz');
        list($insql, $inparams) = $DB->get_in_or_equal($values, SQL_PARAMS_QM, 'param', false);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE name $insql
              ORDER BY id ASC";
        $result = $DB->get_records_sql($sql, $inparams);
        // NULL will not be in result.
        $this->assertCount(1, $result);
        $this->assertEquals([2], array_keys($result));

        // Getting numbeic records.
        $values = array_fill(1, 65535, 3);
        list($insql, $inparams) = $DB->get_in_or_equal($values);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE course $insql
              ORDER BY id ASC";
        $result = $DB->get_records_sql($sql, $inparams);
        $this->assertCount(2, $result);
        $this->assertEquals([1, 2], array_keys($result));

        // Getting numbeic records with NOT condition.
        $values = array_fill(1, 65535, 3);
        list($insql, $inparams) = $DB->get_in_or_equal($values, SQL_PARAMS_QM, 'param', false);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE course $insql
              ORDER BY id ASC";
        $result = $DB->get_records_sql($sql, $inparams);
        $this->assertCount(2, $result);
        $this->assertEquals([3, 4], array_keys($result));
    }

    public function test_get_in_or_equal_big_table_query(): void {
        global $DB;
        $this->resetAfterTest();
        $dbman = $DB->get_manager();

        $table = $this->get_test_table();
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('oneint', XMLDB_TYPE_INTEGER, '10', null, null, null, 100);
        $table->add_field('onenum', XMLDB_TYPE_NUMBER, '10,2', null, null, null, 200);
        $table->add_field('onechar', XMLDB_TYPE_CHAR, '100', null, null, null, 'onestring');
        $table->add_field('onetext', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbman->create_table($table);

        $record = new stdClass();
        $record->course = 1;
        $record->oneint = null;
        $record->onenum = 1.0;
        $record->onechar = 'a';
        $record->onetext = 'aaa';

        $records = [];
        for ($i = 1; $i <= 65535; $i++) {
            $rec = clone($record);
            $rec->oneint = $i;
            $records[$i] = $rec;
        }
        // Populate table with 65535 records.
        $DB->insert_records($tablename, $records);
        // And one more record.
        $record->oneint = -1;
        $DB->insert_record($tablename, $record);

        // Check we can fetch all.
        $values = range(1, 65535);
        list($insql, $inparams) = $DB->get_in_or_equal($values);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE oneint $insql
              ORDER BY id ASC";
        $stored = $DB->get_records_sql($sql, $inparams);

        // Check we got correct set of records.
        $this->assertCount(65535, $stored);
        $oneint = array_column($stored, 'oneint');
        $this->assertEquals($values, $oneint);

        // Check we can fetch all, SQL_PARAMS_NAMED.
        $values = range(1, 65535);
        list($insql, $inparams) = $DB->get_in_or_equal($values, SQL_PARAMS_NAMED);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE oneint $insql
              ORDER BY id ASC";
        $stored = $DB->get_records_sql($sql, $inparams);

        // Check we got correct set of records.
        $this->assertCount(65535, $stored);
        $oneint = array_column($stored, 'oneint');
        $this->assertEquals($values, $oneint);

        // Check we can fetch one using NOT IN.
        list($insql, $inparams) = $DB->get_in_or_equal($values, SQL_PARAMS_QM, 'param', false);
        $sql = "SELECT *
                  FROM {{$tablename}}
                 WHERE oneint $insql
              ORDER BY id ASC";
        $stored = $DB->get_records_sql($sql, $inparams);

        // Check we got correct set of records.
        $this->assertCount(1, $stored);
        $oneint = array_column($stored, 'oneint');
        $this->assertEquals([-1], $oneint);
    }

    /**
     * SSL connection helper.
     *
     * @param mixed $ssl
     * @return resource|PgSql\Connection
     * @throws moodle_exception
     */
    public function new_connection($ssl) {
        global $DB;

        // Open new connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = [];
        }

        $cfg->dboptions['ssl'] = $ssl;

        // Get a separate disposable db connection handle with guaranteed 'readonly' config.
        $db2 = moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $db2->raw_connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        $reflector = new ReflectionClass($db2);
        $rp = $reflector->getProperty('pgsql');
        return $rp->getValue($db2);
    }

    /**
     * Test SSL connection.
     */
    #[WithoutErrorHandler]
    public function test_ssl_connection(): void {
        $pgconnerr = 'pg_connect(): Unable to connect to PostgreSQL server:';

        try {
            $pgsql = $this->new_connection('require');
            // Either connect ...
            $this->assertNotNull($pgsql);
        } catch (moodle_exception $e) {
            // ... or fail with SSL not supported.
            $this->assertStringContainsString($pgconnerr, $e->debuginfo);
            $this->assertStringContainsString('server does not support SSL', $e->debuginfo);
            $this->markTestSkipped('Postgres server does not support SSL. Unable to complete the test.');
            return;
        }

        try {
            $pgsql = $this->new_connection('verify-full');
            // Either connect ...
            $this->assertNotNull($pgsql);
        } catch (moodle_exception $e) {
            // ... or fail with invalid cert.
            $this->assertStringContainsString($pgconnerr, $e->debuginfo);
            $this->assertStringContainsString('change sslmode to disable server certificate verification', $e->debuginfo);
        }

        $this->expectException(moodle_exception::class);
        $this->new_connection('invalid-mode');
    }
}
