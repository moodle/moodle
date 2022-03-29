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

namespace tool_monitor;

/**
 * Unit tests for subscription manager api.
 *
 * @package    tool_monitor
 * @category   test
 * @copyright  2014 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscription_manager_test extends \advanced_testcase {

    /**
     * Test count_rule_subscriptions method.
     */
    public function test_count_rule_subscriptions() {

        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Create few rules.
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule1 = $monitorgenerator->create_rule();
        $rule2 = $monitorgenerator->create_rule();
        $subs = \tool_monitor\subscription_manager::count_rule_subscriptions($rule1->id);

        // No subscriptions at this point.
        $this->assertEquals(0, $subs);

        // Subscribe user 1 to rule 1.
        $record = new \stdClass;
        $record->ruleid = $rule1->id;
        $record->userid = $user1->id;
        $monitorgenerator->create_subscription($record);

        // Subscribe user 2 to rule 1.
        $record->userid = $user2->id;
        $monitorgenerator->create_subscription($record);

        // Subscribe user 2 to rule 2.
        $record->ruleid = $rule2->id;
        $monitorgenerator->create_subscription($record);

        // Should have 2 subscriptions for rule 1 and 1 subscription for rule 2
        $subs1 = \tool_monitor\subscription_manager::count_rule_subscriptions($rule1->id);
        $subs2 = \tool_monitor\subscription_manager::count_rule_subscriptions($rule2->id);
        $this->assertEquals(2, $subs1);
        $this->assertEquals(1, $subs2);
    }
}
