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
 * Unit Tests for the approved contextlist Class
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\contextlist;

/**
 * Tests for the \core_privacy API's approved contextlist functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\request\contextlist
 */
class contextlist_test extends advanced_testcase {

    /**
     * Ensure that valid SQL results in the relevant contexts being added.
     *
     * @covers ::add_from_sql
     * @covers ::<!public>
     */
    public function test_add_from_sql() {
        global $DB;

        $sql = "SELECT c.id FROM {context} c";
        $params = [];
        $allcontexts = $DB->get_records_sql($sql, $params);

        $uit = new contextlist();
        $uit->add_from_sql($sql, $params);

        $this->assertCount(count($allcontexts), $uit);
    }

    /**
     * Ensure that valid system context id is added.
     *
     * @covers ::add_system_context
     * @covers ::<!public>
     */
    public function test_add_system_context() {
        $cl = new contextlist();
        $cl->add_system_context();

        $this->assertCount(1, $cl);

        foreach ($cl->get_contexts() as $context) {
            $this->assertEquals(SYSCONTEXTID, $context->id);
        }
    }

    /**
     * Ensure that a valid user context id is added.
     *
     * @covers ::add_user_context
     * @covers ::<!public>
     */
    public function test_add_user_context() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_user();

        $cl = new contextlist();
        $cl->add_user_context($user->id);

        $this->assertCount(1, $cl);

        foreach ($cl->get_contexts() as $context) {
            $this->assertEquals(\context_user::instance($user->id)->id, $context->id);
        }
    }

    /**
     * Ensure that valid user contexts are added.
     *
     * @covers ::add_user_contexts
     * @covers ::<!public>
     */
    public function test_add_user_contexts() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->create_user();

        $cl = new contextlist();
        $cl->add_user_contexts([$user1->id, $user2->id]);

        $this->assertCount(2, $cl);

        $contexts = $cl->get_contextids();
        $this->assertContains(\context_user::instance($user1->id)->id, $contexts);
        $this->assertContains(\context_user::instance($user2->id)->id, $contexts);
    }

    /**
     * Test {@link \core_privacy\local\request\contextlist::test_guess_id_field_from_sql()} implementation.
     *
     * @dataProvider data_guess_id_field_from_sql
     * @param string $sql Input SQL we try to extract the context id field name from.
     * @param string $expected Expected detected value.
     * @covers ::guess_id_field_from_sql
     * @covers ::<!public>
     */
    public function test_guess_id_field_from_sql($sql, $expected) {

        $rc = new \ReflectionClass(contextlist::class);
        $rcm = $rc->getMethod('guess_id_field_from_sql');
        $rcm->setAccessible(true);
        $actual = $rcm->invoke(new contextlist(), $sql);

        $this->assertEquals($expected, $actual, 'Unable to guess context id field in: '.$sql);
    }

    /**
     * Provides data sets for {@link self::test_guess_id_field_from_sql()}.
     *
     * @return array
     */
    public function data_guess_id_field_from_sql() {
        return [
            'easy' => [
                'SELECT contextid FROM {foo}',
                'contextid',
            ],
            'with_distinct' => [
                'SELECT DISTINCT contextid FROM {foo}',
                'contextid',
            ],
            'with_dot' => [
                'SELECT cx.id FROM {foo} JOIN {context} cx ON blahblahblah',
                'id',
            ],
            'letter_case_does_not_matter' => [
                'Select ctxid From {foo} Where bar = ?',
                'ctxid',
            ],
            'alias' => [
                'SELECT foo.contextid AS ctx FROM {bar} JOIN {foo} ON bar.id = foo.barid',
                'ctx',
            ],
            'tabs' => [
                "SELECT\tctxid\t\tFROM foo f",
                'ctxid',
            ],
            'whitespace' => [
                "SELECT
                    ctxid\t
                   \tFROM foo f",
                'ctxid',
            ],
            'just_number' => [
                '1',
                '1',
            ],
            'select_number' => [
                'SELECT 2',
                '2',
            ],
            'select_number_with_semicolon' => [
                'SELECT 3;',
                '3',
            ],
            'select_number_from_table' => [
                'SELECT 4 FROM users',
                '4',
            ],
            'select_with_complex_subqueries' => [
                'SELECT id FROM table WHERE id IN (
                     SELECT x FROM xtable
                     UNION
                     SELECT y FROM (
                         SELECT y FROM ytable
                         JOIN ztable ON (z = y)))',
                'id'
            ],
            'invalid_union_with_first_being_column_name' => [
                'SELECT id FROM table UNION SELECT 1 FROM table',
                ''
            ],
            'invalid_union_with_first_being_numeric' => [
                'SELECT 1 FROM table UNION SELECT id FROM table',
                ''
            ],
            'invalid_union_without_from' => [
                'SELECT 1 UNION SELECT id FROM table',
                ''
            ],
            'invalid_1' => [
                'SELECT 1+1',
                '',
            ],
            'invalid_2' => [
                'muhehe',
                '',
            ],
        ];
    }
}
