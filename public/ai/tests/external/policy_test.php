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

namespace core_ai\external;

/**
 * Test policy external api calls.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\external\set_policy_status
 * @covers     \core_ai\external\get_policy_status
 */
final class policy_test extends \advanced_testcase {
    /**
     * Test get policy failure.
     */
    public function test_get_policy_failure(): void {
        $this->resetAfterTest();

        // Create a test user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user->id);

        $_POST['sesskey'] = sesskey();
        $params = [
            'userid' => $user->id,
        ];

        $result = \core_external\external_api::call_external_function(
            'core_ai_get_policy_status',
            $params,
        );

        $this->assertFalse($result['error']);
        $this->assertFalse($result['data']['status']);
    }

    /**
     * Test get policy success.
     */
    public function test_get_policy_success(): void {
        $this->resetAfterTest();
        global $DB;

        // Create a test user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user->id);

        // Get system context.
        $context = \core\context\system::instance();

        // Manually add record to the database.
        $record = new \stdClass();
        $record->userid = $user->id;
        $record->contextid = $context->id;
        $record->timeaccepted = time();

        $DB->insert_record('ai_policy_register', $record);

        $_POST['sesskey'] = sesskey();
        $params = [
            'userid' => $user->id,
        ];

        $result = \core_external\external_api::call_external_function(
            'core_ai_get_policy_status',
            $params
        );

        $this->assertFalse($result['error']);
        $this->assertTrue($result['data']['status']);
    }

    /**
     * Test set policy.
     */
    public function test_set_policy(): void {
        $this->resetAfterTest();

        // Create a test user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user->id);

        // Get system context.
        $context = \context_system::instance();

        $_POST['sesskey'] = sesskey();
        $params = [
            'contextid' => $context->id,
        ];

        $result = \core_external\external_api::call_external_function(
            'core_ai_set_policy_status',
            $params
        );

        $this->assertFalse($result['error']);
        $this->assertTrue($result['data']['success']);
    }

    /**
     * Test load_many_for_cache.
     */
    public function test_user_policy_caching(): void {
        global $DB;
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Manually add records to the database.
        $record1 = new \stdClass();
        $record1->userid = $user1->id;
        $record1->contextid = 1;
        $record1->timeaccepted = time();

        $record2 = new \stdClass();
        $record2->userid = $user2->id;
        $record2->contextid = 1;
        $record2->timeaccepted = time();

        $DB->insert_records('ai_policy_register', [
            $record1,
            $record2,
        ]);

        $policycache = \cache::make('core', 'ai_policy');
        $this->assertNotEmpty($policycache->get_many([$user1->id, $user2->id]));
    }

}
