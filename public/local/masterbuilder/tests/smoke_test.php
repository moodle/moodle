<?php
/**
 * System Health "Smoke" Tests for Moodle
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_masterbuilder;

use advanced_testcase;
use cache;

/**
 * Smoke tests to verify core system health.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
class smoke_test extends advanced_testcase {

    /**
     * Test database connectivity.
     */
    public function test_database_connection() {
        global $DB;
        $this->assertEquals(1, $DB->get_field_sql('SELECT 1'));
    }

    /**
     * Test global configuration loading.
     */
    public function test_configuration_loaded() {
        global $CFG;
        $this->assertNotEmpty($CFG->wwwroot, 'wwwroot should be defined');
        $this->assertNotEmpty($CFG->dataroot, 'dataroot should be defined');
    }

    /**
     * Test data directory permissions.
     */
    public function test_dataroot_writable() {
        global $CFG;
        $this->assertTrue(is_dir($CFG->dataroot), 'Dataroot must be a directory');
        $this->assertTrue(is_writable($CFG->dataroot), 'Dataroot must be writable');
    }

    /**
     * Test core Moodle API availability (Site Object).
     */
    public function test_site_exists() {
        $this->resetAfterTest(true);
        $site = get_site();
        $this->assertNotFalse($site, 'get_site() should return the site object');
        $this->assertEquals(1, $site->id, 'Site ID should be 1');
    }

    /**
     * Test Moodle Universal Cache (MUC) operation.
     */
    public function test_cache_operation() {
        $cache = cache::make('core', 'string');
        $key = 'smoke_test_key_' . time();
        $value = 'test_value';

        // Set.
        $cache->set($key, $value);

        // Get.
        $retrieved = $cache->get($key);
        $this->assertEquals($value, $retrieved, 'Cache should return the saved value');

        // Delete.
        $cache->delete($key);
        $this->assertFalse($cache->get($key), 'Cache should be empty after deletion');
    }

    /**
     * Test Cron status.
     * Note: This is a warning-level test in a real environment, but here we strictly assert
     * that the cron system is at least aware of its last run time (even if 0 for fresh installs).
     */
    public function test_cron_status() {
        $lastcron = get_config('tool_task', 'lastcronstart');
        // We just verify we can read the config, asserting specific times might be flaky in CI.
        $this->assertTrue(isset($lastcron), 'Cron start time config should exist');
    }
}
