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
 * @package    tool_log
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_log\privacy;

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use tool_log\privacy\provider;

require_once($CFG->dirroot . '/admin/tool/log/store/standard/tests/fixtures/event.php');

/**
 * Data provider testcase class.
 *
 * We're not testing the full functionality, just that the provider passes the requests
 * down to at least one of its subplugin. Each subplugin should have tests to cover the
 * different provider methods in depth.
 *
 * @package    tool_log
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.
    }

    public function test_get_contexts_for_userid() {
        $admin = \core_user::get_user(2);
        $u1 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $c1ctx = \context_course::instance($c1->id);

        $this->enable_logging();
        $manager = get_log_manager(true);

        $this->setUser($u1);
        $this->assertEmpty(provider::get_contexts_for_userid($u1->id)->get_contextids());
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx]);
        $e->trigger();
        $this->assertEquals($c1ctx->id, provider::get_contexts_for_userid($u1->id)->get_contextids()[0]);
    }

    public function test_delete_data_for_user() {
        global $DB;
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $c1ctx = \context_course::instance($c1->id);

        $this->enable_logging();
        $manager = get_log_manager(true);

        // User 1 is the author.
        $this->setUser($u1);
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx]);
        $e->trigger();
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx]);
        $e->trigger();

        // User 2 is the author.
        $this->setUser($u2);
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx]);
        $e->trigger();

        // Confirm data present.
        $this->assertTrue($DB->record_exists('logstore_standard_log', ['userid' => $u1->id, 'contextid' => $c1ctx->id]));
        $this->assertEquals(2, $DB->count_records('logstore_standard_log', ['userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('logstore_standard_log', ['userid' => $u2->id]));

        // Delete all the things!
        provider::delete_data_for_user(new approved_contextlist($u1, 'logstore_standard', [$c1ctx->id]));
        $this->assertFalse($DB->record_exists('logstore_standard_log', ['userid' => $u1->id, 'contextid' => $c1ctx->id]));
        $this->assertEquals(0, $DB->count_records('logstore_standard_log', ['userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('logstore_standard_log', ['userid' => $u2->id]));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $c1ctx = \context_course::instance($c1->id);

        $this->enable_logging();
        $manager = get_log_manager(true);

        // User 1 is the author.
        $this->setUser($u1);
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx]);
        $e->trigger();
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx]);
        $e->trigger();

        // User 2 is the author.
        $this->setUser($u2);
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx]);
        $e->trigger();

        // Confirm data present.
        $this->assertTrue($DB->record_exists('logstore_standard_log', ['contextid' => $c1ctx->id]));
        $this->assertEquals(2, $DB->count_records('logstore_standard_log', ['userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('logstore_standard_log', ['userid' => $u2->id]));

        // Delete all the things!
        provider::delete_data_for_all_users_in_context($c1ctx);
        $this->assertFalse($DB->record_exists('logstore_standard_log', ['contextid' => $c1ctx->id]));
        $this->assertEquals(0, $DB->count_records('logstore_standard_log', ['userid' => $u1->id]));
        $this->assertEquals(0, $DB->count_records('logstore_standard_log', ['userid' => $u2->id]));
    }

    public function test_export_data_for_user() {
        $admin = \core_user::get_user(2);
        $u1 = $this->getDataGenerator()->create_user();
        $c1 = $this->getDataGenerator()->create_course();
        $c1ctx = \context_course::instance($c1->id);

        $path = [get_string('privacy:path:logs', 'tool_log'), get_string('pluginname', 'logstore_standard')];
        $this->enable_logging();
        $manager = get_log_manager(true);

        // User 1 is the author.
        $this->setUser($u1);
        $e = \logstore_standard\event\unittest_executed::create(['context' => $c1ctx, 'other' => ['i' => 123]]);
        $e->trigger();

        // Confirm data present for u1.
        provider::export_user_data(new approved_contextlist($u1, 'tool_log', [$c1ctx->id]));
        $data = writer::with_context($c1ctx)->get_data($path);
        $this->assertCount(1, $data->logs);
        $this->assertEquals(transform::yesno(true), $data->logs[0]['author_of_the_action_was_you']);
        $this->assertSame(123, $data->logs[0]['other']['i']);
    }

    /**
     * Enable logging.
     *
     * @return void
     */
    protected function enable_logging() {
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');
    }
}
