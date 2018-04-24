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
 */
class contextlist_test extends advanced_testcase {

    /**
     * Ensure that valid SQL results in the relevant contexts being added.
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
}
