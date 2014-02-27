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
 * Legacy log store tests.
 *
 * @package    logstore_legacy
 * @copyright  2014 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/event.php');

class logstore_legacy_store_testcase extends advanced_testcase {
    public function test_log_writing() {
        global $DB;
        $this->resetAfterTest();

        $this->setAdminUser();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $module1 = $this->getDataGenerator()->create_module('resource', array('course' => $course1));
        $course2 = $this->getDataGenerator()->create_course();
        $module2 = $this->getDataGenerator()->create_module('resource', array('course' => $course2));

        // Enable legacy logging plugin.
        set_config('enabled_stores', 'logstore_legacy', 'tool_log');
        set_config('loglegacy', 1, 'logstore_legacy');
        $manager = get_log_manager(true);

        $stores = $manager->get_readers();
        $this->assertCount(1, $stores);
        $this->assertEquals(array('logstore_legacy'), array_keys($stores));
        $store = $stores['logstore_legacy'];
        $this->assertInstanceOf('logstore_legacy\log\store', $store);
        $this->assertInstanceOf('core\log\sql_select_reader', $store);
        $this->assertTrue($store->is_logging());

        $logs = $DB->get_records('log', array(), 'id ASC');
        $this->assertCount(0, $logs);

        $this->setCurrentTimeStart();

        $this->setUser(0);
        $event1 = \logstore_legacy\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)));
        $event1->trigger();

        $this->setUser($user1);
        $event2 = \logstore_legacy\event\unittest_executed::create(
            array('context' => context_course::instance($course2->id), 'other' => array('sample' => 6, 'xx' => 11)));
        $event2->trigger();

        $this->setUser($user2);
        add_to_log($course1->id, 'xxxx', 'yyyy', '', '7', 0, 0);
        //$this->assertDebuggingCalled();

        add_to_log($course2->id, 'aaa', 'bbb', 'info.php', '666', $module2->cmid, $user1->id);
        //$this->assertDebuggingCalled();

        $logs = $DB->get_records('log', array(), 'id ASC');
        $this->assertCount(4, $logs);

        $log = array_shift($logs);
        $this->assertNotEmpty($log->id);
        $this->assertTimeCurrent($log->time);
        $this->assertEquals(0, $log->userid);
        $this->assertSame('0.0.0.0', $log->ip);
        $this->assertEquals($course1->id, $log->course);
        $this->assertSame('core_unittest', $log->module);
        $this->assertEquals($module1->cmid, $log->cmid);
        $this->assertSame('view', $log->action);
        $this->assertSame('unittest.php?id=5', $log->url);
        $this->assertSame('bbb', $log->info);

        $oldlogid = $log->id;
        $log = array_shift($logs);
        $this->assertGreaterThan($oldlogid, $log->id);
        $this->assertNotEmpty($log->id);
        $this->assertTimeCurrent($log->time);
        $this->assertEquals($user1->id, $log->userid);
        $this->assertSame('0.0.0.0', $log->ip);
        $this->assertEquals($course2->id, $log->course);
        $this->assertSame('core_unittest', $log->module);
        $this->assertEquals(0, $log->cmid);
        $this->assertSame('view', $log->action);
        $this->assertSame('unittest.php?id=6', $log->url);
        $this->assertSame('bbb', $log->info);

        $oldlogid = $log->id;
        $log = array_shift($logs);
        $this->assertGreaterThan($oldlogid, $log->id);
        $this->assertNotEmpty($log->id);
        $this->assertTimeCurrent($log->time);
        $this->assertEquals($user2->id, $log->userid);
        $this->assertSame('0.0.0.0', $log->ip);
        $this->assertEquals($course1->id, $log->course);
        $this->assertSame('xxxx', $log->module);
        $this->assertEquals(0, $log->cmid);
        $this->assertSame('yyyy', $log->action);
        $this->assertSame('', $log->url);
        $this->assertSame('7', $log->info);

        $oldlogid = $log->id;
        $log = array_shift($logs);
        $this->assertGreaterThan($oldlogid, $log->id);
        $this->assertNotEmpty($log->id);
        $this->assertTimeCurrent($log->time);
        $this->assertEquals($user1->id, $log->userid);
        $this->assertSame('0.0.0.0', $log->ip);
        $this->assertEquals($course2->id, $log->course);
        $this->assertSame('aaa', $log->module);
        $this->assertEquals($module2->cmid, $log->cmid);
        $this->assertSame('bbb', $log->action);
        $this->assertSame('info.php', $log->url);
        $this->assertSame('666', $log->info);

        // Test if disabling works.
        set_config('enabled_stores', 'logstore_legacy', 'tool_log');
        set_config('loglegacy', 0, 'logstore_legacy');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $store = $stores['logstore_legacy'];
        $this->assertFalse($store->is_logging());

        \logstore_legacy\event\unittest_executed::create(
            array('context' => \context_system::instance(), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        add_to_log($course1->id, 'xxxx', 'yyyy', '', '7', 0, 0);
        //$this->assertDebuggingCalled();
        $this->assertEquals(4, $DB->count_records('log'));

        // Another way to disable legacy completely.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('loglegacy', 1, 'logstore_legacy');
        get_log_manager(true);

        \logstore_legacy\event\unittest_executed::create(
            array('context' => \context_system::instance(), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        add_to_log($course1->id, 'xxxx', 'yyyy', '', '7', 0, 0);
        //$this->assertDebuggingCalled();
        $this->assertEquals(4, $DB->count_records('log'));
        // Set everything back.
        set_config('enabled_stores', '', 'tool_log');
        set_config('loglegacy', 0, 'logstore_legacy');
        get_log_manager(true);
    }
}
