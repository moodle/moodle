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
 * External database log store tests.
 *
 * @package    logstore_database
 * @copyright  2014 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/event.php');
require_once(__DIR__ . '/fixtures/store.php');

class logstore_database_store_testcase extends advanced_testcase {
    public function test_log_writing() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.

        $dbman = $DB->get_manager();
        $this->assertTrue($dbman->table_exists('logstore_standard_log'));
        $DB->delete_records('logstore_standard_log');

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

        // Fake the settings, we will abuse the standard plugin table here...
        $parts = explode('_', get_class($DB));
        set_config('dbdriver', $parts[1] . '/' . $parts[0], 'logstore_database');
        set_config('dbhost', $CFG->dbhost, 'logstore_database');
        set_config('dbuser', $CFG->dbuser, 'logstore_database');
        set_config('dbpass', $CFG->dbpass, 'logstore_database');
        set_config('dbname', $CFG->dbname, 'logstore_database');
        set_config('dbtable', $CFG->prefix . 'logstore_standard_log', 'logstore_database');
        if (!empty($CFG->dboptions['dbpersist'])) {
            set_config('dbpersist', 1, 'logstore_database');
        } else {
            set_config('dbpersist', 0, 'logstore_database');
        }
        if (!empty($CFG->dboptions['dbsocket'])) {
            set_config('dbsocket', $CFG->dboptions['dbsocket'], 'logstore_database');
        } else {
            set_config('dbsocket', '', 'logstore_database');
        }
        if (!empty($CFG->dboptions['dbport'])) {
            set_config('dbport', $CFG->dboptions['dbport'], 'logstore_database');
        } else {
            set_config('dbport', '', 'logstore_database');
        }
        if (!empty($CFG->dboptions['dbschema'])) {
            set_config('dbschema', $CFG->dboptions['dbschema'], 'logstore_database');
        } else {
            set_config('dbschema', '', 'logstore_database');
        }
        if (!empty($CFG->dboptions['dbcollation'])) {
            set_config('dbcollation', $CFG->dboptions['dbcollation'], 'logstore_database');
        } else {
            set_config('dbcollation', '', 'logstore_database');
        }

        // Enable logging plugin.
        set_config('enabled_stores', 'logstore_database', 'tool_log');
        set_config('buffersize', 0, 'logstore_database');
        set_config('logguests', 1, 'logstore_database');
        $manager = get_log_manager(true);

        $stores = $manager->get_readers();
        $this->assertCount(1, $stores);
        $this->assertEquals(array('logstore_database'), array_keys($stores));
        $store = $stores['logstore_database'];
        $this->assertInstanceOf('logstore_database\log\store', $store);
        $this->assertInstanceOf('tool_log\log\writer', $store);
        $this->assertTrue($store->is_logging());

        $logs = $DB->get_records('logstore_standard_log', array(), 'id ASC');
        $this->assertCount(0, $logs);

        $this->setCurrentTimeStart();

        $this->setUser(0);
        $event1 = \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)));
        $event1->trigger();

        $logs = $DB->get_records('logstore_standard_log', array(), 'id ASC');
        $this->assertCount(1, $logs);

        $log1 = reset($logs);
        unset($log1->id);
        $log1->other = unserialize($log1->other);
        $log1 = (array)$log1;
        $data = $event1->get_data();
        $data['origin'] = 'cli';
        $data['ip'] = null;
        $data['realuserid'] = null;
        $this->assertEquals($data, $log1);

        $this->setAdminUser();
        \core\session\manager::loginas($user1->id, context_system::instance());
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));

        $event2 = \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module2->cmid), 'other' => array('sample' => 6, 'xx' => 9)));
        $event2->trigger();

        $_SESSION['SESSION'] = new \stdClass();
        $this->setUser(0);
        $this->assertFalse(\core\session\manager::is_loggedinas());

        $logs = $DB->get_records('logstore_standard_log', array(), 'id ASC');
        $this->assertCount(3, $logs);
        array_shift($logs);
        $log2 = array_shift($logs);
        $this->assertSame('\core\event\user_loggedinas', $log2->eventname);

        $log3 = array_shift($logs);
        unset($log3->id);
        $log3->other = unserialize($log3->other);
        $log3 = (array)$log3;
        $data = $event2->get_data();
        $data['origin'] = 'cli';
        $data['ip'] = null;
        $data['realuserid'] = 2;
        $this->assertEquals($data, $log3);

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
        set_config('buffersize', 3, 'logstore_database');
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        /** @var \logstore_database\log\store $store */
        $store = $stores['logstore_database'];
        $DB->delete_records('logstore_standard_log');

        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));
        $store->flush();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(5, $DB->count_records('logstore_standard_log'));
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(5, $DB->count_records('logstore_standard_log'));
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(5, $DB->count_records('logstore_standard_log'));
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(8, $DB->count_records('logstore_standard_log'));

        // Test guest logging setting.
        set_config('logguests', 0, 'logstore_database');
        set_config('buffersize', 0, 'logstore_database');
        get_log_manager(true);
        $DB->delete_records('logstore_standard_log');
        get_log_manager(true);

        $this->setUser(null);
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));

        $this->setGuestUser();
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));

        $this->setUser($user1);
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(1, $DB->count_records('logstore_standard_log'));

        $this->setUser($user2);
        \logstore_database\event\unittest_executed::create(
            array('context' => context_module::instance($module1->cmid), 'other' => array('sample' => 5, 'xx' => 10)))->trigger();
        $this->assertEquals(2, $DB->count_records('logstore_standard_log'));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * Test method is_event_ignored.
     */
    public function test_is_event_ignored() {
        $this->resetAfterTest();

        // Test guest filtering.
        set_config('logguests', 0, 'logstore_database');
        $this->setGuestUser();
        $event = \logstore_database\event\unittest_executed::create(
                array('context' => context_system::instance(), 'other' => array('sample' => 5, 'xx' => 10)));
        $logmanager = get_log_manager();
        $store = new \logstore_database\test\store($logmanager);
        $this->assertTrue($store->is_event_ignored($event));

        set_config('logguests', 1, 'logstore_database');
        $store = new \logstore_database\test\store($logmanager); // Reload.
        $this->assertFalse($store->is_event_ignored($event));

        // Test action/level filtering.
        set_config('includelevels', '', 'logstore_database');
        set_config('includeactions', '', 'logstore_database');
        $store = new \logstore_database\test\store($logmanager); // Reload.
        $this->assertTrue($store->is_event_ignored($event));

        set_config('includelevels', '0,1', 'logstore_database');
        $store = new \logstore_database\test\store($logmanager); // Reload.
        $this->assertTrue($store->is_event_ignored($event));

        set_config('includelevels', '0,1,2', 'logstore_database');
        $store = new \logstore_database\test\store($logmanager); // Reload.
        $this->assertFalse($store->is_event_ignored($event));

        set_config('includelevels', '', 'logstore_database');
        set_config('includeactions', 'c,r,d', 'logstore_database');
        $store = new \logstore_database\test\store($logmanager); // Reload.
        $this->assertTrue($store->is_event_ignored($event));

        set_config('includeactions', 'c,r,u,d', 'logstore_database');
        $store = new \logstore_database\test\store($logmanager); // Reload.
        $this->assertFalse($store->is_event_ignored($event));
    }

    /**
     * Test logmanager::get_supported_reports returns all reports that require this store.
     */
    public function test_get_supported_reports() {
        $logmanager = get_log_manager();
        $allreports = \core_component::get_plugin_list('report');

        $supportedreports = array(
            'report_log' => '/report/log',
            'report_loglive' => '/report/loglive'
        );

        // Make sure all supported reports are installed.
        $expectedreports = array_keys(array_intersect_key($allreports, $supportedreports));
        $reports = $logmanager->get_supported_reports('logstore_database');
        $reports = array_keys($reports);
        foreach ($expectedreports as $expectedreport) {
            $this->assertContains($expectedreport, $reports);
        }
    }
}
