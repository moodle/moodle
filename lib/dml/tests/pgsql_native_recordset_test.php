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
 * Test specific features of the Postgres dml support relating to recordsets.
 *
 * @package core
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/lib/dml/pgsql_native_moodle_database.php');

/**
 * Test specific features of the Postgres dml support relating to recordsets.
 *
 * @package core
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pgsql_native_recordset_test extends basic_testcase {

    /** @var pgsql_native_moodle_database Special database connection */
    protected $specialdb;

    /**
     * Creates a second db connection and a temp table with values in for testing.
     */
    protected function setUp(): void {
        global $DB;

        parent::setUp();

        // Skip tests if not using Postgres.
        if (!($DB instanceof pgsql_native_moodle_database)) {
            $this->markTestSkipped('Postgres-only test');
        }
    }

    /**
     * Initialises database connection with given fetch buffer size
     * @param int $fetchbuffersize Size of fetch buffer
     */
    protected function init_db($fetchbuffersize) {
        global $CFG, $DB;

        // To make testing easier, create a database with the same dboptions as the real one,
        // but a low number for the cursor size.
        $this->specialdb = \moodle_database::get_driver_instance('pgsql', 'native', true);
        $dboptions = $CFG->dboptions;
        $dboptions['fetchbuffersize'] = $fetchbuffersize;
        $this->specialdb->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname,
                $DB->get_prefix(), $dboptions);

        // Create a temp table.
        $dbman = $this->specialdb->get_manager();
        $table = new xmldb_table('silly_test_table');
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('msg', XMLDB_TYPE_CHAR, 255);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbman->create_temp_table($table);

        // Add some records to the table.
        for ($index = 1; $index <= 7; $index++) {
            $this->specialdb->insert_record('silly_test_table', ['msg' => 'record' . $index]);
        }
    }

    /**
     * Gets rid of the second db connection.
     */
    protected function tearDown(): void {
        if ($this->specialdb) {
            $table = new xmldb_table('silly_test_table');
            $this->specialdb->get_manager()->drop_table($table);
            $this->specialdb->dispose();
            $this->specialdb = null;
        }
        parent::tearDown();
    }

    /**
     * Tests that get_recordset_sql works when using cursors, which it does when no limit is
     * specified.
     */
    public function test_recordset_cursors(): void {
        $this->init_db(3);

        // Query the table and check the actual queries using debug mode, also check the count.
        $this->specialdb->set_debug(true);
        $before = $this->specialdb->perf_get_queries();
        ob_start();
        $rs = $this->specialdb->get_recordset_sql('SELECT * FROM {silly_test_table} ORDER BY id');
        $index = 0;
        foreach ($rs as $rec) {
            $index++;
            $this->assertEquals('record' . $index, $rec->msg);
        }
        $this->assertEquals(7, $index);
        $rs->close();
        $debugging = ob_get_contents();
        ob_end_clean();

        // Expect 4 fetches - first three, next three, last one (with 2).
        $this->assert_query_regexps([
                '~SELECT \* FROM~',
                '~FETCH 3 FROM crs1~',
                '~FETCH 3 FROM crs1~',
                '~FETCH 3 FROM crs1~',
                '~CLOSE crs1~'], $debugging);

        // There should have been 7 queries tracked for perf log.
        $this->assertEquals(5, $this->specialdb->perf_get_queries() - $before);

        // Try a second time - this time we'll request exactly 3 items so that it has to query
        // twice (as it can't tell if the first batch is the last).
        $before = $this->specialdb->perf_get_queries();
        ob_start();
        $rs = $this->specialdb->get_recordset_sql(
                'SELECT * FROM {silly_test_table} WHERE id <= ? ORDER BY id', [3]);
        $index = 0;
        foreach ($rs as $rec) {
            $index++;
            $this->assertEquals('record' . $index, $rec->msg);
        }
        $this->assertEquals(3, $index);
        $rs->close();
        $debugging = ob_get_contents();
        ob_end_clean();

        $this->specialdb->set_debug(false);

        // Expect 2 fetches - first three, then next one (empty).
        $this->assert_query_regexps([
                '~SELECT \* FROM~',
                '~FETCH 3 FROM crs2~',
                '~FETCH 3 FROM crs2~',
                '~CLOSE crs2~'], $debugging);

        // There should have been 4 queries tracked for perf log.
        $this->assertEquals(4, $this->specialdb->perf_get_queries() - $before);
    }

    /**
     * Tests that get_recordset_sql works when using cursors and when there are two overlapping
     * recordsets being used.
     */
    public function test_recordset_cursors_overlapping(): void {
        $this->init_db(3);

        $rs1 = $this->specialdb->get_recordset('silly_test_table', null, 'id');
        $rs2 = $this->specialdb->get_recordset('silly_test_table', null, 'id DESC');

        // Read first 3 from first recordset.
        $read = [];
        $read[] = $rs1->current()->id;
        $rs1->next();
        $read[] = $rs1->current()->id;
        $rs1->next();
        $read[] = $rs1->current()->id;
        $rs1->next();
        $this->assertEquals([1, 2, 3], $read);

        // Read 5 from second recordset.
        $read = [];
        $read[] = $rs2->current()->id;
        $rs2->next();
        $read[] = $rs2->current()->id;
        $rs2->next();
        $read[] = $rs2->current()->id;
        $rs2->next();
        $read[] = $rs2->current()->id;
        $rs2->next();
        $read[] = $rs2->current()->id;
        $rs2->next();
        $this->assertEquals([7, 6, 5, 4, 3], $read);

        // Now read remainder of first recordset and close it.
        $read = [];
        $read[] = $rs1->current()->id;
        $rs1->next();
        $read[] = $rs1->current()->id;
        $rs1->next();
        $read[] = $rs1->current()->id;
        $rs1->next();
        $read[] = $rs1->current()->id;
        $rs1->next();
        $this->assertFalse($rs1->valid());
        $this->assertEquals([4, 5, 6, 7], $read);
        $rs1->close();

        // And remainder of second.
        $read = [];
        $read[] = $rs2->current()->id;
        $rs2->next();
        $read[] = $rs2->current()->id;
        $rs2->next();
        $this->assertFalse($rs2->valid());
        $this->assertEquals([2, 1], $read);
        $rs2->close();
    }

    /**
     * Tests that get_recordset_sql works when using cursors and transactions inside.
     */
    public function test_recordset_cursors_transaction_inside(): void {
        $this->init_db(3);

        // Transaction inside the recordset processing.
        $rs = $this->specialdb->get_recordset('silly_test_table', null, 'id');
        $read = [];
        foreach ($rs as $rec) {
            $read[] = $rec->id;
            $transaction = $this->specialdb->start_delegated_transaction();
            $transaction->allow_commit();
        }
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $read);
        $rs->close();
    }

    /**
     * Tests that get_recordset_sql works when using cursors and a transaction outside.
     */
    public function test_recordset_cursors_transaction_outside(): void {
        $this->init_db(3);

        // Transaction outside the recordset processing.
        $transaction = $this->specialdb->start_delegated_transaction();
        $rs = $this->specialdb->get_recordset('silly_test_table', null, 'id');
        $read = [];
        foreach ($rs as $rec) {
            $read[] = $rec->id;
        }
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $read);
        $rs->close();
        $transaction->allow_commit();
    }

    /**
     * Tests that get_recordset_sql works when using cursors and a transaction overlapping.
     */
    public function test_recordset_cursors_transaction_overlapping_before(): void {
        $this->init_db(3);

        // Transaction outside the recordset processing.
        $transaction = $this->specialdb->start_delegated_transaction();
        $rs = $this->specialdb->get_recordset('silly_test_table', null, 'id');
        $transaction->allow_commit();
        $read = [];
        foreach ($rs as $rec) {
            $read[] = $rec->id;
        }
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $read);
        $rs->close();
    }

    /**
     * Tests that get_recordset_sql works when using cursors and a transaction overlapping.
     */
    public function test_recordset_cursors_transaction_overlapping_after(): void {
        $this->init_db(3);

        // Transaction outside the recordset processing.
        $rs = $this->specialdb->get_recordset('silly_test_table', null, 'id');
        $transaction = $this->specialdb->start_delegated_transaction();
        $read = [];
        foreach ($rs as $rec) {
            $read[] = $rec->id;
        }
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $read);
        $rs->close();
        $transaction->allow_commit();
    }

    /**
     * Tests that get_recordset_sql works when using cursors and a transaction that 'fails' and gets
     * rolled back.
     */
    public function test_recordset_cursors_transaction_rollback(): void {
        $this->init_db(3);

        try {
            $rs = $this->specialdb->get_recordset('silly_test_table', null, 'id');
            $transaction = $this->specialdb->start_delegated_transaction();
            $this->specialdb->delete_records('silly_test_table', ['id' => 5]);
            $transaction->rollback(new dml_transaction_exception('rollback please'));
            $this->fail('should not get here');
        } catch (dml_transaction_exception $e) {
            $this->assertStringContainsString('rollback please', $e->getMessage());
        } finally {

            // Rollback should not kill our recordset.
            $read = [];
            foreach ($rs as $rec) {
                $read[] = $rec->id;
            }
            $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $read);

            // This would happen in real code (that isn't within the same function) anyway because
            // it would go out of scope.
            $rs->close();
        }

        // OK, transaction aborted, now get the recordset again and check nothing was deleted.
        $rs = $this->specialdb->get_recordset('silly_test_table', null, 'id');
        $read = [];
        foreach ($rs as $rec) {
            $read[] = $rec->id;
        }
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $read);
        $rs->close();
    }

    /**
     * Tests that get_recordset_sql works when not using cursors, because a limit is specified.
     */
    public function test_recordset_no_cursors_limit(): void {
        $this->init_db(3);

        $this->specialdb->set_debug(true);
        $before = $this->specialdb->perf_get_queries();
        ob_start();
        $rs = $this->specialdb->get_recordset_sql(
                'SELECT * FROM {silly_test_table} ORDER BY id', [], 0, 100);
        $index = 0;
        foreach ($rs as $rec) {
            $index++;
            $this->assertEquals('record' . $index, $rec->msg);
        }
        $this->assertEquals(7, $index);
        $rs->close();
        $this->specialdb->set_debug(false);
        $debugging = ob_get_contents();
        ob_end_clean();

        // Expect direct request without using cursors.
        $this->assert_query_regexps(['~SELECT \* FROM~'], $debugging);

        // There should have been 1 query tracked for perf log.
        $this->assertEquals(1, $this->specialdb->perf_get_queries() - $before);
    }

    /**
     * Tests that get_recordset_sql works when not using cursors, because the config setting turns
     * them off.
     */
    public function test_recordset_no_cursors_config(): void {
        $this->init_db(0);

        $this->specialdb->set_debug(true);
        $before = $this->specialdb->perf_get_queries();
        ob_start();
        $rs = $this->specialdb->get_recordset_sql('SELECT * FROM {silly_test_table} ORDER BY id');
        $index = 0;
        foreach ($rs as $rec) {
            $index++;
            $this->assertEquals('record' . $index, $rec->msg);
        }
        $this->assertEquals(7, $index);
        $rs->close();
        $this->specialdb->set_debug(false);
        $debugging = ob_get_contents();
        ob_end_clean();

        // Expect direct request without using cursors.
        $this->assert_query_regexps(['~SELECT \* FROM~'], $debugging);

        // There should have been 1 query tracked for perf log.
        $this->assertEquals(1, $this->specialdb->perf_get_queries() - $before);
    }

    /**
     * Asserts that database debugging output matches the expected list of SQL queries, specified
     * as an array of regular expressions.
     *
     * @param string[] $expected Expected regular expressions
     * @param string $debugging Debugging text from the database
     */
    protected function assert_query_regexps(array $expected, $debugging) {
        $lines = explode("\n", $debugging);
        $index = 0;
        $params = false;
        foreach ($lines as $line) {
            if ($params) {
                if ($line === ')]') {
                    $params = false;
                }
                continue;
            }
            // Skip irrelevant lines.
            if (preg_match('~^---~', $line)) {
                continue;
            }
            if (preg_match('~^Query took~', $line)) {
                continue;
            }
            if (trim($line) === '') {
                continue;
            }
            // Skip param lines.
            if ($line === '[array (') {
                $params = true;
                continue;
            }
            if (!array_key_exists($index, $expected)) {
                $this->fail('More queries than expected');
            }
            $this->assertMatchesRegularExpression($expected[$index++], $line);
        }
        if (array_key_exists($index, $expected)) {
            $this->fail('Fewer queries than expected');
        }
    }

}
