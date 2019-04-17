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
 * Standard log store tests.
 *
 * @package    logstore_standard
 * @copyright  2014 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/event.php');
require_once(__DIR__ . '/fixtures/restore_hack.php');

class logstore_standard_store_testcase extends advanced_testcase {
    /**
     * @var bool Determine if we disabled the GC, so it can be re-enabled in tearDown.
     */
    private $wedisabledgc = false;

    /**
     * Tests log writing.
     *
     * @param bool $jsonformat True to test with JSON format
     * @dataProvider test_log_writing_provider
     * @throws moodle_exception
     */
    public function test_log_writing(bool $jsonformat) {
        global $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.

        // Apply JSON format system setting.
        set_config('jsonformat', $jsonformat ? 1 : 0, 'logstore_standard');

        $this->setAdminUser();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $module1 = $this->getDataGenerator()->create_module('resource', array('course' => $course1));
        $course2 = $this->getDataGenerator()->create_course();
        $module2 = $this->getDataGenerator()->create_module('resource', array('course' => $course2));

        // Test all plugins are disabled by this command.
        set_config('enabled_stores', '', 'tool_log');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $this->assertCount(0, $stores);

        // Enable logging plugin.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');
        $manager = get_log_manager(true);

        $stores = $manager->get_readers();
        $this->assertCount(1, $stores);
        $this->assertEquals(array('logstore_standard'), array_keys($stores));
        /** @var \logstore_standard\log\store $store */
        $store = $stores['logstore_standard'];
        $this->assertInstanceOf('logstore_standard\log\store', $store);
        $this->assertInstanceOf('tool_log\log\writer', $store);
        $this->assertTrue($store->is_logging());

        $logs = $DB->get_records('logstore_standard_log', array(), 'id ASC');
        $this->assertCount(0, $logs);

        $this->setCurrentTimeStart();

        $this->setUser(0);
        $event1 = \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)));
        $event1->trigger();

        $logs = $DB->get_records('logstore_standard_log', array(), 'id ASC');
        $this->assertCount(1, $logs);

        $log1 = reset($logs);
        unset($log1->id);
        if ($jsonformat) {
            $log1->other = json_decode($log1->other, true);
        } else {
            $log1->other = unserialize($log1->other);
        }
        $log1 = (array)$log1;
        $data = $event1->get_data();
        $data['origin'] = 'cli';
        $data['ip'] = null;
        $data['realuserid'] = null;
        $this->assertEquals($data, $log1);

        $this->setAdminUser();
        \core\session\manager::loginas($user1->id, context_system::instance());
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));

        logstore_standard_restore::hack_executing(1);
        $event2 = \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module2->cmid), 'other' => array('sample' => 6, 'xx' => 9)));
        $event2->trigger();
        logstore_standard_restore::hack_executing(0);

        \core\session\manager::init_empty_session();
        $this->assertFalse(\core\session\manager::is_loggedinas());

        $logs = $DB->get_records('logstore_standard_log', array(), 'id ASC');
        $this->assertCount(3, $logs);
        array_shift($logs);
        $log2 = array_shift($logs);
        $this->assertSame('\core\event\user_loggedinas', $log2->eventname);
        $this->assertSame('cli', $log2->origin);

        $log3 = array_shift($logs);
        unset($log3->id);
        if ($jsonformat) {
            $log3->other = json_decode($log3->other, true);
        } else {
            $log3->other = unserialize($log3->other);
        }
        $log3 = (array)$log3;
        $data = $event2->get_data();
        $data['origin'] = 'restore';
        $data['ip'] = null;
        $data['realuserid'] = 2;
        $this->assertEquals($data, $log3);

        // Test table exists.
        $tablename = $store->get_internal_log_table_name();
        $this->assertTrue($DB->get_manager()->table_exists($tablename));

        // Test reading.
        $this->assertSame(3, $store->get_events_select_count('', array()));
        $events = $store->get_events_select('', array(), 'timecreated ASC', 0, 0); // Is actually sorted by "timecreated ASC, id ASC".
        $this->assertCount(3, $events);
        $resev1 = array_shift($events);
        array_shift($events);
        $resev2 = array_shift($events);
        $this->assertEquals($event1->get_data(), $resev1->get_data());
        $this->assertEquals($event2->get_data(), $resev2->get_data());

        // Test buffering.
        set_config('buffersize', 3, 'logstore_standard');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        /** @var \logstore_standard\log\store $store */
        $store = $stores['logstore_standard'];
        $DB->delete_records('logstore_standard_log');

        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));
        $store->flush();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(5, $DB->count_records('logstore_standard_log'));
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(5, $DB->count_records('logstore_standard_log'));
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(5, $DB->count_records('logstore_standard_log'));
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(8, $DB->count_records('logstore_standard_log'));

        // Test guest logging setting.
        set_config('logguests', 0, 'logstore_standard');
        set_config('buffersize', 0, 'logstore_standard');
        get_log_manager(true);
        $DB->delete_records('logstore_standard_log');
        get_log_manager(true);

        $this->setUser(null);
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));

        $this->setGuestUser();
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));

        $this->setUser($user1);
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(1, $DB->count_records('logstore_standard_log'));

        $this->setUser($user2);
        \logstore_standard\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * Returns different JSON format settings so the test can be run with JSON format either on or
     * off.
     *
     * @return [bool] Array of true/false
     */
    public static function test_log_writing_provider(): array {
        return [
            [false],
            [true]
        ];
    }

    /**
     * Test logmanager::get_supported_reports returns all reports that require this store.
     */
    public function test_get_supported_reports() {
        $logmanager = get_log_manager();
        $allreports = \core_component::get_plugin_list('report');

        $supportedreports = array(
            'report_log' => '/report/log',
            'report_loglive' => '/report/loglive',
            'report_outline' => '/report/outline',
            'report_participation' => '/report/participation',
            'report_stats' => '/report/stats'
        );

        // Make sure all supported reports are installed.
        $expectedreports = array_keys(array_intersect_key($allreports, $supportedreports));
        $reports = $logmanager->get_supported_reports('logstore_standard');
        $reports = array_keys($reports);
        foreach ($expectedreports as $expectedreport) {
            $this->assertContains($expectedreport, $reports);
        }
    }

    /**
     * Verify that gc disabling works
     */
    public function test_gc_enabled_as_expected() {
        if (!gc_enabled()) {
            $this->markTestSkipped('Garbage collector (gc) is globally disabled.');
        }

        $this->disable_gc();
        $this->assertTrue($this->wedisabledgc);
        $this->assertFalse(gc_enabled());
    }

    /**
     * Test sql_reader::get_events_select_iterator.
     * @return void
     */
    public function test_events_traversable() {
        global $DB;

        $this->disable_gc();

        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->setAdminUser();

        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $store = $stores['logstore_standard'];

        $events = $store->get_events_select_iterator('', array(), '', 0, 0);
        $this->assertFalse($events->valid());

        // Here it should be already closed, but we should be allowed to
        // over-close it without exception.
        $events->close();

        $user = $this->getDataGenerator()->create_user();
        for ($i = 0; $i < 1000; $i++) {
            \core\event\user_created::create_from_userid($user->id)->trigger();
        }
        $store->flush();

        // Check some various sizes get the right number of elements.
        $this->assertEquals(1, iterator_count($store->get_events_select_iterator('', array(), '', 0, 1)));
        $this->assertEquals(2, iterator_count($store->get_events_select_iterator('', array(), '', 0, 2)));

        $iterator = $store->get_events_select_iterator('', array(), '', 0, 500);
        $this->assertInstanceOf('\core\event\base', $iterator->current());
        $this->assertEquals(500, iterator_count($iterator));
        $iterator->close();

        // Look for non-linear memory usage for the iterator version.
        $mem = memory_get_usage();
        $events = $store->get_events_select('', array(), '', 0, 0);
        $arraymemusage = memory_get_usage() - $mem;

        $mem = memory_get_usage();
        $eventsit = $store->get_events_select_iterator('', array(), '', 0, 0);
        $eventsit->close();
        $itmemusage = memory_get_usage() - $mem;

        $this->assertInstanceOf('\Traversable', $eventsit);

        $this->assertLessThan($arraymemusage / 10, $itmemusage);
        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * Test that the standard log cleanup works correctly.
     */
    public function test_cleanup_task() {
        global $DB;

        $this->resetAfterTest();

        // Create some records spread over various days; test multiple iterations in cleanup.
        $ctx = context_course::instance(1);
        $record = (object) array(
            'edulevel' => 0,
            'contextid' => $ctx->id,
            'contextlevel' => $ctx->contextlevel,
            'contextinstanceid' => $ctx->instanceid,
            'userid' => 1,
            'timecreated' => time(),
        );
        $DB->insert_record('logstore_standard_log', $record);
        $record->timecreated -= 3600 * 24 * 30;
        $DB->insert_record('logstore_standard_log', $record);
        $record->timecreated -= 3600 * 24 * 30;
        $DB->insert_record('logstore_standard_log', $record);
        $record->timecreated -= 3600 * 24 * 30;
        $DB->insert_record('logstore_standard_log', $record);
        $this->assertEquals(4, $DB->count_records('logstore_standard_log'));

        // Remove all logs before "today".
        set_config('loglifetime', 1, 'logstore_standard');

        $this->expectOutputString(" Deleted old log records from standard store.\n");
        $clean = new \logstore_standard\task\cleanup_task();
        $clean->execute();

        $this->assertEquals(1, $DB->count_records('logstore_standard_log'));
    }

    /**
     * Tests the decode_other function can cope with both JSON and PHP serialized format.
     *
     * @param mixed $value Value to encode and decode
     * @dataProvider test_decode_other_provider
     */
    public function test_decode_other($value) {
        $this->assertEquals($value, \logstore_standard\log\store::decode_other(serialize($value)));
        $this->assertEquals($value, \logstore_standard\log\store::decode_other(json_encode($value)));
    }

    public function test_decode_other_with_wrongly_encoded_contents() {
        $this->assertSame(null, \logstore_standard\log\store::decode_other(null));
    }

    /**
     * List of possible values for 'other' field.
     *
     * I took these types from our logs based on the different first character of PHP serialized
     * data - my query found only these types. The normal case is an array.
     *
     * @return array Array of parameters
     */
    public function test_decode_other_provider(): array {
        return [
            [['info' => 'd2819896', 'logurl' => 'discuss.php?d=2819896']],
            [null],
            ['just a string'],
            [32768]
        ];
    }

    /**
     * Disable the garbage collector if it's enabled to ensure we don't adjust memory statistics.
     */
    private function disable_gc() {
        if (gc_enabled()) {
            $this->wedisabledgc = true;
            gc_disable();
        }
    }

    /**
     * Reset any garbage collector changes to the previous state at the end of the test.
     */
    public function tearDown() {
        if ($this->wedisabledgc) {
            gc_enable();
        }
        $this->wedisabledgc = false;
    }
}
