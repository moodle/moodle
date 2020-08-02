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
 * Data provider tests.
 *
 * @package    logstore_legacy
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use logstore_legacy\privacy\provider;
use logstore_legacy\event\unittest_executed;

require_once(__DIR__ . '/fixtures/event.php');

/**
 * Data provider testcase class.
 *
 * @package    logstore_legacy
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logstore_legacy_privacy_testcase extends provider_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    public function test_get_contexts_for_userid() {
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $cm1 = $this->getDataGenerator()->create_module('url', ['course' => $c1]);
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $cm1ctx = context_module::instance($cm1->cmid);

        $this->enable_logging();
        $manager = get_log_manager(true);

        // User 1 is the author.
        $this->setUser($u1);
        $this->assert_contextlist_equals($this->get_contextlist_for_user($u1), []);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 1]]);
        $e->trigger();
        $this->assert_contextlist_equals($this->get_contextlist_for_user($u1), [$cm1ctx]);

        // User 2 is the author.
        $this->setUser($u2);
        $this->assert_contextlist_equals($this->get_contextlist_for_user($u2), []);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 2]]);
        $e->trigger();
        $this->assert_contextlist_equals($this->get_contextlist_for_user($u2), [$cm1ctx]);

        // User 3 is the author.
        $this->setUser($u3);
        $this->assert_contextlist_equals($this->get_contextlist_for_user($u3), []);
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 3]]);
        $e->trigger();
        $this->assert_contextlist_equals($this->get_contextlist_for_user($u3), [$sysctx]);
    }

    /**
     * Test returning user IDs for a given context.
     */
    public function test_add_userids_for_context() {
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('url', ['course' => $course]);
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($course->id);
        $cm1ctx = context_module::instance($module->cmid);

        $userctx = context_user::instance($u1->id);

        $this->enable_logging();
        $manager = get_log_manager(true);

        $this->setUser($u1);
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 1]]);
        $e->trigger();
        $this->setUser($u2);
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 2]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 3]]);
        $e->trigger();
        $this->setUser($u3);
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 4]]);
        $e->trigger();
        $this->setUser($u1);
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 5]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 6]]);
        $e->trigger();
        $this->setUser($u2);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 7]]);
        $e->trigger();
        $this->setUser($u3);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 8]]);
        $e->trigger();

        // Start with system and check that each of the contexts returns what we expected.
        $userlist = new \core_privacy\local\request\userlist($sysctx, 'logstore_legacy');
        provider::add_userids_for_context($userlist);
        $systemuserids = $userlist->get_userids();
        $this->assertCount(2, $systemuserids);
        $this->assertNotFalse(array_search($u1->id, $systemuserids));
        $this->assertNotFalse(array_search($u2->id, $systemuserids));
        // Check the course context.
        $userlist = new \core_privacy\local\request\userlist($c1ctx, 'logstore_legacy');
        provider::add_userids_for_context($userlist);
        $courseuserids = $userlist->get_userids();
        $this->assertCount(2, $courseuserids);
        $this->assertNotFalse(array_search($u1->id, $courseuserids));
        $this->assertNotFalse(array_search($u3->id, $courseuserids));
        // Check the module context.
        $userlist = new \core_privacy\local\request\userlist($cm1ctx, 'logstore_legacy');
        provider::add_userids_for_context($userlist);
        $moduleuserids = $userlist->get_userids();
        $this->assertCount(3, $moduleuserids);
        $this->assertNotFalse(array_search($u1->id, $moduleuserids));
        $this->assertNotFalse(array_search($u2->id, $moduleuserids));
        $this->assertNotFalse(array_search($u3->id, $moduleuserids));
    }

    public function test_delete_data_for_user() {
        global $DB;

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $cm1 = $this->getDataGenerator()->create_module('url', ['course' => $c1]);
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);
        $cm1ctx = context_module::instance($cm1->cmid);

        $this->enable_logging();
        $manager = get_log_manager(true);

        // User 1 is the author.
        $this->setUser($u1);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 1]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 2]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 3]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 4]]);
        $e->trigger();

        // User 2 is the author.
        $this->setUser($u2);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 5]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 6]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 7]]);
        $e->trigger();

        // Assert what we have.
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => 0]));
        $this->assertEquals(4, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete other context.
        provider::delete_data_for_user(new approved_contextlist($u1, 'logstore_legacy', [$c2ctx->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => 0]));
        $this->assertEquals(4, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete system.
        provider::delete_data_for_user(new approved_contextlist($u1, 'logstore_legacy', [$sysctx->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => 0]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete course.
        provider::delete_data_for_user(new approved_contextlist($u1, 'logstore_legacy', [$c1ctx->id]));
        $this->assertTrue($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => 0]));
        $this->assertEquals(2, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete course.
        provider::delete_data_for_user(new approved_contextlist($u1, 'logstore_legacy', [$cm1ctx->id]));
        $this->assertFalse($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['userid' => $u1->id, 'cmid' => 0, 'course' => 0]));
        $this->assertEquals(0, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u2->id]));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $cm1 = $this->getDataGenerator()->create_module('url', ['course' => $c1]);
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);
        $cm1ctx = context_module::instance($cm1->cmid);

        $this->enable_logging();
        $manager = get_log_manager(true);

        // User 1 is the author.
        $this->setUser($u1);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 1]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 2]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 3]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 4]]);
        $e->trigger();

        // User 2 is the author.
        $this->setUser($u2);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 5]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 6]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 7]]);
        $e->trigger();

        // Assert what we have.
        $this->assertTrue($DB->record_exists('log', ['cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['cmid' => 0, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['cmid' => 0, 'course' => 0]));
        $this->assertEquals(4, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete other context.
        provider::delete_data_for_all_users_in_context($c2ctx);
        $this->assertTrue($DB->record_exists('log', ['cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['cmid' => 0, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['cmid' => 0, 'course' => 0]));
        $this->assertEquals(4, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete system.
        provider::delete_data_for_all_users_in_context($sysctx);
        $this->assertTrue($DB->record_exists('log', ['cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertTrue($DB->record_exists('log', ['cmid' => 0, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['cmid' => 0, 'course' => 0]));
        $this->assertEquals(3, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(2, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete course.
        provider::delete_data_for_all_users_in_context($c1ctx);
        $this->assertTrue($DB->record_exists('log', ['cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['cmid' => 0, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['cmid' => 0, 'course' => 0]));
        $this->assertEquals(2, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('log', ['userid' => $u2->id]));

        // Delete course.
        provider::delete_data_for_all_users_in_context($cm1ctx);
        $this->assertFalse($DB->record_exists('log', ['cmid' => $cm1->cmid, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['cmid' => 0, 'course' => $c1->id]));
        $this->assertFalse($DB->record_exists('log', ['cmid' => 0, 'course' => 0]));
        $this->assertEquals(0, $DB->count_records('log', ['userid' => $u1->id]));
        $this->assertEquals(0, $DB->count_records('log', ['userid' => $u2->id]));
    }

    /**
     * Test the deletion of data for a list of users in a context.
     */
    public function test_delete_data_for_userlist() {
        global $DB;

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('url', ['course' => $course]);
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($course->id);
        $cm1ctx = context_module::instance($module->cmid);

        $userctx = context_user::instance($u1->id);

        $this->enable_logging();
        $manager = get_log_manager(true);

        $this->setUser($u1);
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 1]]);
        $e->trigger();
        $this->setUser($u2);
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 2]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 3]]);
        $e->trigger();
        $this->setUser($u3);
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 4]]);
        $e->trigger();
        $this->setUser($u1);
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 5]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 6]]);
        $e->trigger();
        $this->setUser($u2);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 7]]);
        $e->trigger();
        $this->setUser($u3);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 8]]);
        $e->trigger();

        // System context deleting one user.
        $this->assertEquals(3, $DB->count_records('log', ['cmid' => 0, 'course' => 0]));
        $userlist = new \core_privacy\local\request\approved_userlist($sysctx, 'logstore_legacy', [$u2->id]);
        provider::delete_data_for_userlist($userlist);
        $this->assertEquals(1, $DB->count_records('log', ['cmid' => 0, 'course' => 0]));

        // Course context deleting one user.
        $this->assertEquals(2, $DB->count_records('log', ['cmid' => 0, 'course' => $course->id]));
        $userlist = new \core_privacy\local\request\approved_userlist($c1ctx, 'logstore_legacy', [$u1->id]);
        provider::delete_data_for_userlist($userlist);
        $this->assertEquals(1, $DB->count_records('log', ['cmid' => 0, 'course' => $course->id]));

        // Module context deleting two users.
        $this->assertEquals(3, $DB->count_records('log', ['cmid' => $module->cmid, 'course' => $course->id]));
        $userlist = new \core_privacy\local\request\approved_userlist($cm1ctx, 'logstore_legacy', [$u1->id, $u3->id]);
        provider::delete_data_for_userlist($userlist);
        $this->assertEquals(1, $DB->count_records('log', ['cmid' => $module->cmid, 'course' => $course->id]));
    }

    public function test_export_data_for_user() {
        global $DB;

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $cm1 = $this->getDataGenerator()->create_module('url', ['course' => $c1]);
        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);
        $cm1ctx = context_module::instance($cm1->cmid);

        $this->enable_logging();
        $manager = get_log_manager(true);
        $path = [get_string('privacy:path:logs', 'tool_log'), get_string('pluginname', 'logstore_legacy')];

        // User 1 is the author.
        $this->setUser($u1);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 1]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 2]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 3]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 4]]);
        $e->trigger();

        // User 2 is the author.
        $this->setUser($u2);
        $e = unittest_executed::create(['context' => $cm1ctx, 'other' => ['sample' => 5]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $c1ctx, 'other' => ['sample' => 6]]);
        $e->trigger();
        $e = unittest_executed::create(['context' => $sysctx, 'other' => ['sample' => 7]]);
        $e->trigger();

        // Test export.
        provider::export_user_data(new approved_contextlist($u1, 'logstore_legacy', [$cm1ctx->id]));
        $data = writer::with_context($c1ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($cm1ctx)->get_data($path);
        $this->assertCount(2, $data->logs);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'logstore_legacy', [$c1ctx->id]));
        $data = writer::with_context($cm1ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($c1ctx)->get_data($path);
        $this->assertCount(1, $data->logs);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'logstore_legacy', [$sysctx->id]));
        $data = writer::with_context($sysctx)->get_data($path);
        $this->assertCount(1, $data->logs);
    }

    /**
     * Assert the content of a context list.
     *
     * @param contextlist $contextlist The collection.
     * @param array $expected List of expected contexts or IDs.
     * @return void
     */
    protected function assert_contextlist_equals($contextlist, array $expected) {
        $expectedids = array_map(function($context) {
            if (is_object($context)) {
                return $context->id;
            }
            return $context;
        }, $expected);
        $contextids = array_map('intval', $contextlist->get_contextids());
        sort($contextids);
        sort($expectedids);
        $this->assertEquals($expectedids, $contextids);
    }

    /**
     * Enable logging.
     *
     * @return void
     */
    protected function enable_logging() {
        set_config('enabled_stores', 'logstore_legacy', 'tool_log');
        set_config('loglegacy', 1, 'logstore_legacy');
        get_log_manager(true);
    }

    /**
     * Get the contextlist for a user.
     *
     * @param object $user The user.
     * @return contextlist
     */
    protected function get_contextlist_for_user($user) {
        $contextlist = new contextlist();
        provider::add_contexts_for_userid($contextlist, $user->id);
        return $contextlist;
    }
}
