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
 * Tests for report library functions.
 *
 * @package    report_loglive
 * @copyright  2014 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace report_loglive;

defined('MOODLE_INTERNAL') || die();

/**
 * Class report_loglive_lib_testcase
 *
 * @package    report_loglive
 * @copyright  2014 onwards Ankit agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class lib_test extends \advanced_testcase {

    /**
     * Test report_log_supports_logstore.
     */
    public function test_report_participation_supports_logstore(): void {
        $logmanager = get_log_manager();
        $allstores = \core_component::get_plugin_list_with_class('logstore', 'log\store');

        $supportedstores = array(
            'logstore_standard' => '\logstore_standard\log\store'
        );

        // Make sure all supported stores are installed.
        $expectedstores = array_keys(array_intersect($allstores, $supportedstores));
        $stores = $logmanager->get_supported_logstores('report_loglive');
        $stores = array_keys($stores);
        foreach ($expectedstores as $expectedstore) {
            $this->assertContains($expectedstore, $stores);
        }
    }

    /**
     * Test the latest record timestamp of the report data set.
     *
     * @covers ::get_until()
     */
    public function test_report_get_until(): void {
        global $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $now = time();

        // Configure log store.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        $manager = get_log_manager();
        $stores = $manager->get_readers();
        $store = $stores['logstore_standard'];
        $DB->delete_records('logstore_standard_log');

        // Build the report.
        $url = new \moodle_url("/report/loglive/index.php");
        $renderable = new \report_loglive_renderable('logstore_standard', 0, $url);
        $table = $renderable->get_table();
        $table->query_db(100);
        $until = $table->get_until();

        // There is no record in the log table at this stage so until date is supposed to be equal to CUTOFF date.
        $this->assertLessThanOrEqual(time() - \report_loglive_renderable::CUTOFF, $until);

        // Create a user, store the event and re-build the report.
        $this->getDataGenerator()->create_user();
        $store->flush();
        $table->query_db(100);
        $until = $table->get_until();

        // Assert that until date reflects user creation event date (now).
        $this->assertGreaterThanOrEqual($now, $until);
    }
}
