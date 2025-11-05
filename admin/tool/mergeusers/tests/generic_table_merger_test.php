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

namespace tool_mergeusers;

use advanced_testcase;
use ddl_exception;
use dml_exception;
use moodle_exception;
use tool_mergeusers\local\merger\generic_table_merger;
use xmldb_table;

/**
 * Generic table merger test.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generic_table_merger_test extends advanced_testcase {
    public const USER_TO_KEEP = 1;
    public const USER_TO_REMOVE = 2;
    public const TABLE_NAME_FOR_TESTING = 'generic_table_merger_test';

    private array $datafortablemerger;
    private generic_table_merger $merger;

    public function setUp(): void {
        global $DB;
        parent::setUp();
        $this->resetAfterTest(true);

        $dbman = $DB->get_manager();
        $table = new xmldb_table('generic_table_merger_test');
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, false, true, true);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, 10, false, true);
        $table->add_field('compoundindexfield', XMLDB_TYPE_INTEGER, 10, false, true);
        $table->add_key('id', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_index('compoundindex', XMLDB_INDEX_UNIQUE, ['userid', 'compoundindexfield']);
        $dbman->create_table($table);

        $this->datafortablemerger = [
            'toid' => self::USER_TO_KEEP,
            'fromid' => self::USER_TO_REMOVE,
            'userFields' => ['userid'],
            'tableName' => 'generic_table_merger_test',
            'compoundIndex' => [
                'userfield' => ['userid'],
                'otherfields' => ['compoundindexfield'],
            ],
        ];

        $this->merger = new generic_table_merger();
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_generic_tablemerger
     * @dataProvider without_conflicting_records_provider
     * @throws dml_exception
     * @throws moodle_exception
     * @throws ddl_exception
     */
    public function test_merge_without_conflicting_records(?object $record, bool $withlog): void {
        global $DB;

        // Prepare scenario.
        $logs = [];
        $errormessages = [];
        if (!is_null($record)) {
            $DB->insert_record('generic_table_merger_test', $record);
        }

        // Do the job.
        $this->merger->merge($this->datafortablemerger, $logs, $errormessages);

        // Check result.
        if ($withlog) {
            $this->assertCount(1, $logs);
            $this->assertLogsContain($logs, ['generic_table_merger_test', 'UPDATE']);
        } else {
            $this->assertEmpty($logs);
        }
        $this->assertEmpty($errormessages);
    }

    /**
     * Filters Checks that $stringstomatchperline are all matched against every single log line in $logs.
     *
     * Asserts as true whenever there is just a single match.
     *
     * @param array $logs
     * @param array $stringstomatchperline
     * @return void
     */
    private function assertlogscontain(array $logs, array $stringstomatchperline): void {
        $matchinglogs = array_filter(
            $logs,
            function ($log) use ($stringstomatchperline) {
                $match = true;
                foreach ($stringstomatchperline as $stringtomatch) {
                    $match = $match && strstr($log, $stringtomatch);
                }
                return $match;
            },
        );
        $this->assertCount(1, $matchinglogs);
    }

    public static function without_conflicting_records_provider(): array {
        return [
            'no records for users' => [null, false],
            'record for user to keep' => [
                (object)[
                    'id' => 1,
                    'userid' => self::USER_TO_KEEP,
                    'compoundindexfield' => 1,
                ],
                false,
            ],
            'record for user to remove' => [
                (object)[
                    'id' => 1,
                    'userid' => self::USER_TO_REMOVE,
                    'compoundindexfield' => 1,
                ],
                true,
            ],
        ];
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_generic_tablemerger
     * @dataProvider with_conflicting_records_provider
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_merge_with_conflicting_records(bool $informedindex): void {
        global $DB;

        // Prepare scenario.
        $logs = [];
        $errormessages = [];
        $idforusertokeep = 1;
        $idforusertoremove = 2;
        if (!$informedindex) {
            unset($this->datafortablemerger['compoundIndex']);
        }
        $DB->insert_record(
            'generic_table_merger_test',
            (object)[
                'id' => $idforusertokeep,
                'userid' => self::USER_TO_KEEP,
                'compoundindexfield' => 1,
            ],
        );
        $DB->insert_record(
            'generic_table_merger_test',
            (object)[
                'id' => $idforusertoremove,
                'userid' => self::USER_TO_REMOVE,
                'compoundindexfield' => 1,
            ],
        );
        // Ensure preconditions are met.
        $this->assertEquals(2, $DB->count_records('generic_table_merger_test'));

        // Do the job.
        $this->merger->merge($this->datafortablemerger, $logs, $errormessages);

        // Check result.
        $this->assertCount(1, $logs);
        $this->assertLogsContain($logs, ['generic_table_merger_test', 'DELETE']);
        $this->assertEmpty($errormessages);
    }

    public static function with_conflicting_records_provider(): array {
        return [
            'with informed compound index' => [true],
            'without informed compound index' => [false],
        ];
    }
}
