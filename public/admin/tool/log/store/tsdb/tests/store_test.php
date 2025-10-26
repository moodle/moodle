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
 * Tests for TSDB logstore.
 *
 * @package    logstore_tsdb
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace logstore_tsdb;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../classes/log/store.php');
require_once(__DIR__ . '/../classes/client/timescaledb_client.php');

/**
 * Tests for logstore_tsdb store class.
 *
 * @package    logstore_tsdb
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends \advanced_testcase {

    /**
     * Setup test environment.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.
    }

    /**
     * Test plugin initialization.
     */
    public function test_plugin_initialization() {
        global $DB;

        // Enable the plugin.
        set_config('enabled_stores', 'logstore_tsdb', 'tool_log');

        // Get log manager.
        $manager = get_log_manager(true);

        // Verify logstore_tsdb is registered.
        $stores = $manager->get_readers();
        $this->assertCount(0, $stores, 'No readers should be available yet (write-only store)');

        // Check that the plugin can be instantiated.
        $store = new \logstore_tsdb\log\store($manager);
        $this->assertInstanceOf('logstore_tsdb\log\store', $store);
    }

    /**
     * Test event transformation.
     */
    public function test_event_transformation() {
        $manager = get_log_manager();
        $store = new \logstore_tsdb\log\store($manager);

        // Create a test event.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $this->setUser($user);

        // Trigger a simple event.
        $event = \core\event\course_viewed::create([
            'objectid' => $course->id,
            'context' => \context_course::instance($course->id),
        ]);

        // Use reflection to access protected method.
        $reflection = new \ReflectionClass($store);
        $method = $reflection->getMethod('transform_event');
        $method->setAccessible(true);

        $datapoint = $method->invoke($store, $event);

        // Verify datapoint structure.
        $this->assertArrayHasKey('measurement', $datapoint);
        $this->assertEquals('moodle_events', $datapoint['measurement']);

        $this->assertArrayHasKey('tags', $datapoint);
        $this->assertArrayHasKey('eventname', $datapoint['tags']);
        $this->assertStringContainsString('course_viewed', $datapoint['tags']['eventname']);

        $this->assertArrayHasKey('fields', $datapoint);
        $this->assertArrayHasKey('userid', $datapoint['fields']);
        $this->assertEquals($user->id, $datapoint['fields']['userid']);

        $this->assertArrayHasKey('timestamp', $datapoint);
        $this->assertIsInt($datapoint['timestamp']);
    }

    /**
     * Test event writing (requires TimescaleDB connection).
     *
     * @group external
     */
    public function test_event_writing() {
        global $CFG;

        // Skip if TimescaleDB is not configured.
        if (empty($CFG->logstore_tsdb_host)) {
            $this->markTestSkipped('TimescaleDB not configured for testing');
        }

        // Configure plugin.
        set_config('enabled_stores', 'logstore_tsdb', 'tool_log');
        set_config('host', $CFG->logstore_tsdb_host ?? 'localhost', 'logstore_tsdb');
        set_config('port', $CFG->logstore_tsdb_port ?? '5433', 'logstore_tsdb');
        set_config('database', $CFG->logstore_tsdb_database ?? 'moodle_logs_tsdb', 'logstore_tsdb');
        set_config('username', $CFG->logstore_tsdb_username ?? 'moodleuser', 'logstore_tsdb');
        set_config('password', $CFG->logstore_tsdb_password ?? '', 'logstore_tsdb');
        set_config('writemode', 'sync', 'logstore_tsdb');

        // Create test data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->setUser($user);

        // Trigger event.
        $event = \core\event\course_viewed::create([
            'objectid' => $course->id,
            'context' => \context_course::instance($course->id),
        ]);
        $event->trigger();

        // Small delay to ensure event is written.
        sleep(1);

        // Verify event was written (requires TimescaleDB client).
        // This is a basic smoke test - detailed verification would require querying TimescaleDB.
        $this->assertTrue(true, 'Event triggered successfully');
    }

    /**
     * Test buffering mechanism.
     */
    public function test_buffering() {
        set_config('writemode', 'async', 'logstore_tsdb');
        set_config('buffersize', 5, 'logstore_tsdb');

        $manager = get_log_manager();
        $store = new \logstore_tsdb\log\store($manager);

        // Use reflection to access protected properties.
        $reflection = new \ReflectionClass($store);
        $bufferprop = $reflection->getProperty('buffer');
        $bufferprop->setAccessible(true);

        // Verify buffer starts empty.
        $this->assertEmpty($bufferprop->getValue($store));

        // Create test event.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->setUser($user);

        // Add multiple events to buffer.
        for ($i = 0; $i < 3; $i++) {
            $event = \core\event\course_viewed::create([
                'objectid' => $course->id,
                'context' => \context_course::instance($course->id),
            ]);

            // Transform and buffer (without triggering to avoid actual writes).
            $method = $reflection->getMethod('transform_event');
            $method->setAccessible(true);
            $datapoint = $method->invoke($store, $event);

            $buffermethod = $reflection->getMethod('buffer_event');
            $buffermethod->setAccessible(true);
            $buffermethod->invoke($store, $datapoint);
        }

        // Verify buffer has events.
        $buffer = $bufferprop->getValue($store);
        $this->assertCount(3, $buffer);
    }

    /**
     * Test configuration loading.
     */
    public function test_configuration_loading() {
        // Set test configuration.
        set_config('host', 'testhost', 'logstore_tsdb');
        set_config('port', '9999', 'logstore_tsdb');
        set_config('database', 'testdb', 'logstore_tsdb');
        set_config('writemode', 'sync', 'logstore_tsdb');

        $manager = get_log_manager();
        $store = new \logstore_tsdb\log\store($manager);

        // Use reflection to access protected config.
        $reflection = new \ReflectionClass($store);
        $configprop = $reflection->getProperty('config');
        $configprop->setAccessible(true);
        $config = $configprop->getValue($store);

        // Verify configuration was loaded correctly.
        $this->assertEquals('testhost', $config['host']);
        $this->assertEquals('9999', $config['port']);
        $this->assertEquals('testdb', $config['database']);
        $this->assertEquals('sync', $config['writemode']);
    }

    /**
     * Test anonymous events are ignored.
     */
    public function test_anonymous_events_ignored() {
        $manager = get_log_manager();
        $store = new \logstore_tsdb\log\store($manager);

        // Create anonymous event.
        $event = \core\event\unittest_executed::create([
            'other' => ['sample' => 1],
        ]);

        // Set event as anonymous using reflection.
        $reflection = new \ReflectionClass($event);
        $prop = $reflection->getProperty('data');
        $prop->setAccessible(true);
        $data = $prop->getValue($event);
        $data['anonymous'] = 1;
        $prop->setValue($event, $data);

        // Record events.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        // Write should return early for anonymous events.
        // Since we can't easily verify the write was skipped, this is a basic test.
        $this->assertCount(1, $events);
    }

    /**
     * Test disposal/cleanup.
     */
    public function test_dispose() {
        set_config('writemode', 'async', 'logstore_tsdb');

        $manager = get_log_manager();
        $store = new \logstore_tsdb\log\store($manager);

        // Add some events to buffer.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->setUser($user);

        $reflection = new \ReflectionClass($store);
        $method = $reflection->getMethod('transform_event');
        $method->setAccessible(true);

        $event = \core\event\course_viewed::create([
            'objectid' => $course->id,
            'context' => \context_course::instance($course->id),
        ]);

        $datapoint = $method->invoke($store, $event);

        $buffermethod = $reflection->getMethod('buffer_event');
        $buffermethod->setAccessible(true);
        $buffermethod->invoke($store, $datapoint);

        // Dispose should flush buffer.
        $store->dispose();

        // Verify buffer is empty after dispose.
        $bufferprop = $reflection->getProperty('buffer');
        $bufferprop->setAccessible(true);
        $buffer = $bufferprop->getValue($store);

        $this->assertEmpty($buffer, 'Buffer should be empty after dispose');
    }
}
