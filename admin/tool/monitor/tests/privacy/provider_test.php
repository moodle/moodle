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
 * Privacy test for the event monitor
 *
 * @package    tool_monitor
 * @category   test
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_monitor\privacy;

defined('MOODLE_INTERNAL') || die();

use tool_monitor\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\tests\provider_testcase;

/**
 * Privacy test for the event monitor
 *
 * @package    tool_monitor
 * @category   test
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    /**
     * Set up method.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        // Enable monitor.
        set_config('enablemonitor', 1, 'tool_monitor');
    }

    /**
     * Assign a capability to $USER
     * The function creates a student $USER if $USER->id is empty
     *
     * @param string $capability capability name
     * @param int $contextid
     * @param int $roleid
     * @return int the role id - mainly returned for creation, so calling function can reuse it
     */
    public static function assign_user_capability($capability, $contextid, $roleid = null) {
        global $USER;

        // Create a new student $USER if $USER doesn't exist.
        if (empty($USER->id)) {
            $user  = self::getDataGenerator()->create_user();
            self::setUser($user);
        }

        if (empty($roleid)) {
            $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        }

        assign_capability($capability, CAP_ALLOW, $roleid, $contextid);

        role_assign($roleid, $USER->id, $contextid);

        accesslib_clear_all_caches_for_unit_testing();

        return $roleid;
    }

    /**
     * Test that a collection with data is returned when calling this function.
     */
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('tool_monitor');
        $collection = provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Check that a user context is returned if there is any user data for this user.
     */
    public function test_get_contexts_for_userid(): void {
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $usercontext2 = \context_user::instance($user2->id);
        $this->assertEmpty(provider::get_contexts_for_userid($user->id));
        $this->assertEmpty(provider::get_contexts_for_userid($user2->id));

        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Create a rule with this user.
        $this->setUser($user);
        $rule = $monitorgenerator->create_rule();
        $contextlist = provider::get_contexts_for_userid($user->id);

        // Check that we only get back one context.
        $this->assertCount(1, $contextlist);

        // Check that a context is returned for just creating a rule.
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);

        $this->setUser($user2);

        $record = new \stdClass();
        $record->courseid = 0;
        $record->userid = $user2->id;
        $record->ruleid = $rule->id;

        $subscription = $monitorgenerator->create_subscription($record);
        $contextlist = provider::get_contexts_for_userid($user2->id);

        // Check that we only get back one context.
        $this->assertCount(1, $contextlist);

        // Check that a context is returned for just subscribing to a rule.
        $this->assertEquals($usercontext2->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Check that the correct userlist is returned if there is any user data for this context.
     */
    public function test_get_users_in_context(): void {
        $component = 'tool_monitor';
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $usercontext2 = \context_user::instance($user2->id);

        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertEmpty($userlist);

        $userlist = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist);
        $this->assertEmpty($userlist);

        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Create a rule with user.
        $this->setUser($user);
        $rule = $monitorgenerator->create_rule();
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);

        // Check that we only get back user.
        $userids = $userlist->get_userids();
        $this->assertCount(1, $userlist);
        $this->assertEquals($user->id, $userids[0]);

        // Create a subscription with user2.
        $this->setUser($user2);

        $record = new \stdClass();
        $record->courseid = 0;
        $record->userid = $user2->id;
        $record->ruleid = $rule->id;

        $subscription = $monitorgenerator->create_subscription($record);
        $userlist = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist);

        // Check that user2 is returned for just subscribing to a rule.
        $userids = $userlist->get_userids();
        $this->assertCount(1, $userlist);
        $this->assertEquals($user2->id, $userids[0]);
    }

    /**
     * Test that user data is exported correctly.
     */
    public function test_export_user_data(): void {
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $this->setUser($user);
        $rulerecord = (object)['name' => 'privacy rule'];
        $rule = $monitorgenerator->create_rule($rulerecord);

        $secondrulerecord = (object)['name' => 'privacy rule2'];
        $rule2 = $monitorgenerator->create_rule($secondrulerecord);

        $subscription = (object)['ruleid' => $rule->id, 'userid' => $user->id];
        $subscription = $monitorgenerator->create_subscription($subscription);

        $writer = \core_privacy\local\request\writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());

        $approvedlist = new approved_contextlist($user, 'tool_monitor', [$usercontext->id]);
        provider::export_user_data($approvedlist);

        // Check that the rules created by this user are exported.
        $this->assertEquals($rulerecord->name, $writer->get_data([get_string('privacy:createdrules', 'tool_monitor'),
                $rulerecord->name . '_' . $rule->id])->name);
        $this->assertEquals($secondrulerecord->name, $writer->get_data([get_string('privacy:createdrules', 'tool_monitor'),
                $secondrulerecord->name . '_' . $rule2->id])->name);

        // Check that the subscriptions for this user are also exported.
        $this->assertEquals($rulerecord->name, $writer->get_data([get_string('privacy:subscriptions', 'tool_monitor'),
                $rulerecord->name . '_' . $subscription->id, 'Site' , 'All events'])->name);
    }

    /**
     * Test deleting all user data for a specific context.
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $usercontext2 = \context_user::instance($user2->id);
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $this->setUser($user);
        // Need to give user one the ability to manage rules.
        $this->assign_user_capability('tool/monitor:managerules', \context_system::instance());

        $rulerecord = (object)['name' => 'privacy rule'];
        $rule = $monitorgenerator->create_rule($rulerecord);

        $secondrulerecord = (object)['name' => 'privacy rule2'];
        $rule2 = $monitorgenerator->create_rule($secondrulerecord);

        $subscription = (object)['ruleid' => $rule->id, 'userid' => $user->id];
        $subscription = $monitorgenerator->create_subscription($subscription);

        // Have user 2 subscribe to the second rule created by user 1.
        $subscription2 = (object)['ruleid' => $rule2->id, 'userid' => $user2->id];
        $subscription2 = $monitorgenerator->create_subscription($subscription2);

        $this->setUser($user2);
        $thirdrulerecord = (object)['name' => 'privacy rule for second user'];
        $rule3 = $monitorgenerator->create_rule($thirdrulerecord);

        $subscription3 = (object)['ruleid' => $rule3->id, 'userid' => $user2->id];
        $subscription3 = $monitorgenerator->create_subscription($subscription3);

        // Try a different context first.
        provider::delete_data_for_all_users_in_context(\context_system::instance());

        // Get all of the monitor rules.
        $dbrules = $DB->get_records('tool_monitor_rules');

        // All of the rules should still be present.
        $this->assertCount(3, $dbrules);
        $this->assertEquals($user->id, $dbrules[$rule->id]->userid);
        $this->assertEquals($user->id, $dbrules[$rule2->id]->userid);
        $this->assertEquals($user2->id, $dbrules[$rule3->id]->userid);

        // Delete everything for the first user context.
        provider::delete_data_for_all_users_in_context($usercontext);

        // Get all of the monitor rules.
        $dbrules = $DB->get_records('tool_monitor_rules');

        // Only the rules for user 1 that does not have any more subscriptions should be deleted (the first rule).
        $this->assertCount(2, $dbrules);
        $this->assertEquals($user->id, $dbrules[$rule2->id]->userid);
        $this->assertEquals($user2->id, $dbrules[$rule3->id]->userid);

        // Get all of the monitor subscriptions.
        $dbsubs = $DB->get_records('tool_monitor_subscriptions');
        // There should be two subscriptions left, both for user 2.
        $this->assertCount(2, $dbsubs);
        $this->assertEquals($user2->id, $dbsubs[$subscription2->id]->userid);
        $this->assertEquals($user2->id, $dbsubs[$subscription3->id]->userid);
    }

    /**
     * This should work identical to the above test.
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $usercontext2 = \context_user::instance($user2->id);
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $this->setUser($user);
        // Need to give user one the ability to manage rules.
        $this->assign_user_capability('tool/monitor:managerules', \context_system::instance());

        $rulerecord = (object)['name' => 'privacy rule'];
        $rule = $monitorgenerator->create_rule($rulerecord);

        $secondrulerecord = (object)['name' => 'privacy rule2'];
        $rule2 = $monitorgenerator->create_rule($secondrulerecord);

        $subscription = (object)['ruleid' => $rule->id, 'userid' => $user->id];
        $subscription = $monitorgenerator->create_subscription($subscription);

        // Have user 2 subscribe to the second rule created by user 1.
        $subscription2 = (object)['ruleid' => $rule2->id, 'userid' => $user2->id];
        $subscription2 = $monitorgenerator->create_subscription($subscription2);

        $this->setUser($user2);
        $thirdrulerecord = (object)['name' => 'privacy rule for second user'];
        $rule3 = $monitorgenerator->create_rule($thirdrulerecord);

        $subscription3 = (object)['ruleid' => $rule3->id, 'userid' => $user2->id];
        $subscription3 = $monitorgenerator->create_subscription($subscription3);

        $approvedlist = new approved_contextlist($user, 'tool_monitor', [$usercontext->id]);

        // Delete everything for the first user.
        provider::delete_data_for_user($approvedlist);

        // Get all of the monitor rules.
        $dbrules = $DB->get_records('tool_monitor_rules');

        // Only the rules for user 1 that does not have any more subscriptions should be deleted (the first rule).
        $this->assertCount(2, $dbrules);
        $this->assertEquals($user->id, $dbrules[$rule2->id]->userid);
        $this->assertEquals($user2->id, $dbrules[$rule3->id]->userid);

        // Get all of the monitor subscriptions.
        $dbsubs = $DB->get_records('tool_monitor_subscriptions');
        // There should be two subscriptions left, both for user 2.
        $this->assertCount(2, $dbsubs);
        $this->assertEquals($user2->id, $dbsubs[$subscription2->id]->userid);
        $this->assertEquals($user2->id, $dbsubs[$subscription3->id]->userid);
    }

    /**
     * Test deleting user data for an approved userlist in a context.
     */
    public function test_delete_data_for_users(): void {
        global $DB;

        $component = 'tool_monitor';
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $usercontext2 = \context_user::instance($user2->id);
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $this->setUser($user);
        // Need to give user one the ability to manage rules.
        $this->assign_user_capability('tool/monitor:managerules', \context_system::instance());

        $rulerecord = (object)['name' => 'privacy rule'];
        $rule = $monitorgenerator->create_rule($rulerecord);

        $secondrulerecord = (object)['name' => 'privacy rule2'];
        $rule2 = $monitorgenerator->create_rule($secondrulerecord);

        $subscription = (object)['ruleid' => $rule->id, 'userid' => $user->id];
        $subscription = $monitorgenerator->create_subscription($subscription);

        // Have user 2 subscribe to the second rule created by user 1.
        $subscription2 = (object)['ruleid' => $rule2->id, 'userid' => $user2->id];
        $subscription2 = $monitorgenerator->create_subscription($subscription2);

        $this->setUser($user2);
        $thirdrulerecord = (object)['name' => 'privacy rule for second user'];
        $rule3 = $monitorgenerator->create_rule($thirdrulerecord);

        $subscription3 = (object)['ruleid' => $rule3->id, 'userid' => $user2->id];
        $subscription3 = $monitorgenerator->create_subscription($subscription3);

        // Get all of the monitor rules, ensure all exist.
        $dbrules = $DB->get_records('tool_monitor_rules');
        $this->assertCount(3, $dbrules);

        // Delete for user2 in first user's context, should have no effect.
        $approveduserids = [$user2->id];
        $approvedlist = new approved_userlist($usercontext, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $dbrules = $DB->get_records('tool_monitor_rules');
        $this->assertCount(3, $dbrules);

        // Delete for user in usercontext.
        $approveduserids = [$user->id];
        $approvedlist = new approved_userlist($usercontext, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // Only the rules for user 1 that does not have any more subscriptions should be deleted (the first rule).
        $dbrules = $DB->get_records('tool_monitor_rules');
        $this->assertCount(2, $dbrules);
        $this->assertEquals($user->id, $dbrules[$rule2->id]->userid);
        $this->assertEquals($user2->id, $dbrules[$rule3->id]->userid);

        // There should be two subscriptions left, both for user 2.
        $dbsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertCount(2, $dbsubs);
        $this->assertEquals($user2->id, $dbsubs[$subscription2->id]->userid);
        $this->assertEquals($user2->id, $dbsubs[$subscription3->id]->userid);

        // Delete for user2 in context 2.
        $approveduserids = [$user2->id];
        $approvedlist = new approved_userlist($usercontext2, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // There should be no subscriptions left.
        $dbsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertEmpty($dbsubs);
    }
}
