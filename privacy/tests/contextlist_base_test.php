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
 * Unit Tests for the abstract contextlist Class
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\contextlist_base;

/**
 * Tests for the \core_privacy API's contextlist base functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\request\contextlist_base
 */
class contextlist_base_test extends advanced_testcase {
    /**
     * Ensure that get_contextids returns the list of unique contextids.
     *
     * @dataProvider    get_contextids_provider
     * @param   array   $input List of context IDs
     * @param   array   $expected list of contextids
     * @param   int     $count Expected count
     * @covers ::get_contextids
     * @covers ::<!public>
     */
    public function test_get_contextids($input, $expected, $count) {
        $uit = new test_contextlist_base();
        $uit->set_contextids($input);

        $result = $uit->get_contextids();
        $this->assertCount($count, $result);

        // Note: Array order is not guaranteed and should not matter.
        foreach ($expected as $contextid) {
            $this->assertNotFalse(array_search($contextid, $result));
        }
    }

    /**
     * Provider for the list of contextids.
     *
     * @return array
     */
    public function get_contextids_provider() {
        return [
            'basic' => [
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
                5,
            ],
            'duplicates' => [
                [1, 1, 2, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
                5,
            ],
            'Mixed order with duplicates' => [
                [5, 4, 2, 5, 4, 1, 3, 4, 1, 5, 5, 5, 2, 4, 1, 2],
                [1, 2, 3, 4, 5],
                5,
            ],
        ];
    }

    /**
     * Ensure that get_contexts returns the correct list of contexts.
     *
     * @covers ::get_contexts
     * @covers ::<!public>
     */
    public function test_get_contexts() {
        global $DB;

        $contexts = [];
        $contexts[] = \context_system::instance();
        $contexts[] = \context_user::instance(\core_user::get_user_by_username('admin')->id);

        $ids = [];
        foreach ($contexts as $context) {
            $ids[] = $context->id;
        }

        $uit = new test_contextlist_base();
        $uit->set_contextids($ids);

        $result = $uit->get_contexts();
        $this->assertCount(count($contexts), $result);
        foreach ($contexts as $context) {
            $this->assertNotFalse(array_search($context, $result));
        }
    }

    /**
     * Ensure that the contextlist_base is countable.
     *
     * @dataProvider    get_contextids_provider
     * @param   array   $input List of context IDs
     * @param   array   $expected list of contextids
     * @param   int     $count Expected count
     * @covers ::count
     * @covers ::<!public>
     */
    public function test_countable($input, $expected, $count) {
        $uit = new test_contextlist_base();
        $uit->set_contextids($input);

        $this->assertCount($count, $uit);
    }

    /**
     * Ensure that the contextlist_base iterates over the set of contexts.
     *
     * @covers ::current
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     * @covers ::<!public>
     */
    public function test_context_iteration() {
        global $DB;

        $allcontexts = $DB->get_records('context');
        $contexts = [];
        foreach ($allcontexts as $context) {
            $contexts[] = \context::instance_by_id($context->id);
        }

        $uit = new test_contextlist_base();
        $uit->set_contextids(array_keys($allcontexts));

        foreach ($uit as $key => $context) {
            $this->assertNotFalse(array_search($context, $contexts));
        }
    }

    /**
     * Test that deleting a context results in current returning nothing.
     *
     * @covers ::current
     * @covers ::<!public>
     */
    public function test_current_context_one_context() {
        global $DB;

        $this->resetAfterTest();

        $data = (object) [
            'contextlevel' => CONTEXT_BLOCK,
            'instanceid' => 45,
            'path' => '1/5/67/107',
            'depth' => 4
        ];

        $contextid = $DB->insert_record('context', $data);

        $contextbase = new test_contextlist_base();
        $contextbase->set_contextids([$contextid]);
        $this->assertCount(1, $contextbase);
        $currentcontext = $contextbase->current();
        $this->assertEquals($contextid, $currentcontext->id);
        $DB->delete_records('context', ['id' => $contextid]);
        context_helper::reset_caches();
        $this->assertEmpty($contextbase->current());
    }

    /**
     * Test that deleting a context results in the next record being returned.
     *
     * @covers ::current
     * @covers ::<!public>
     */
    public function test_current_context_two_contexts() {
        global $DB;

        $this->resetAfterTest();

        $data = (object) [
            'contextlevel' => CONTEXT_BLOCK,
            'instanceid' => 45,
            'path' => '1/5/67/107',
            'depth' => 4
        ];

        $contextid1 = $DB->insert_record('context', $data);

        $data = (object) [
            'contextlevel' => CONTEXT_BLOCK,
            'instanceid' => 47,
            'path' => '1/5/54/213',
            'depth' => 4
        ];

        $contextid2 = $DB->insert_record('context', $data);

        $contextbase = new test_contextlist_base();
        $contextbase->set_contextids([$contextid1, $contextid2]);
        $this->assertCount(2, $contextbase);
        $DB->delete_records('context', ['id' => $contextid1]);
        context_helper::reset_caches();
        // Current should return context 2.
        $this->assertEquals($contextid2, $contextbase->current()->id);
    }

    /**
     * Test that if there are no non-deleted contexts that nothing is returned.
     *
     * @covers ::get_contexts
     * @covers ::<!public>
     */
    public function test_get_contexts_all_deleted() {
        global $DB;

        $this->resetAfterTest();

        $data = (object) [
            'contextlevel' => CONTEXT_BLOCK,
            'instanceid' => 45,
            'path' => '1/5/67/107',
            'depth' => 4
        ];

        $contextid = $DB->insert_record('context', $data);

        $contextbase = new test_contextlist_base();
        $contextbase->set_contextids([$contextid]);
        $this->assertCount(1, $contextbase);
        $DB->delete_records('context', ['id' => $contextid]);
        context_helper::reset_caches();
        $this->assertEmpty($contextbase->get_contexts());
    }

    /**
     * Test that get_contexts() returns only active contexts.
     *
     * @covers ::get_contexts
     * @covers ::<!public>
     */
    public function test_get_contexts_one_deleted() {
        global $DB;

        $this->resetAfterTest();

        $data = (object) [
            'contextlevel' => CONTEXT_BLOCK,
            'instanceid' => 45,
            'path' => '1/5/67/107',
            'depth' => 4
        ];

        $contextid1 = $DB->insert_record('context', $data);

        $data = (object) [
            'contextlevel' => CONTEXT_BLOCK,
            'instanceid' => 47,
            'path' => '1/5/54/213',
            'depth' => 4
        ];

        $contextid2 = $DB->insert_record('context', $data);

        $contextbase = new test_contextlist_base();
        $contextbase->set_contextids([$contextid1, $contextid2]);
        $this->assertCount(2, $contextbase);
        $DB->delete_records('context', ['id' => $contextid1]);
        context_helper::reset_caches();
        $contexts = $contextbase->get_contexts();
        $this->assertCount(1, $contexts);
        $context = array_shift($contexts);
        $this->assertEquals($contextid2, $context->id);
    }
}

/**
 * A test class extending the contextlist_base allowing setting of the
 * contextids.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_contextlist_base extends contextlist_base {
    /**
     * Set the contextids for the test class.
     *
     * @param   int[]   $contexids  The list of contextids to use.
     */
    public function set_contextids(array $contextids) {
        parent::set_contextids($contextids);
    }
}
