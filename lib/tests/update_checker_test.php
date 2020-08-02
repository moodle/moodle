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
 * Unit tests for the update checker.
 *
 * @package   core_plugin
 * @category  test
 * @copyright 2012, 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\update\testable_checker;
use core\update\testable_checker_cron_executed;

global $CFG;
require_once(__DIR__.'/fixtures/testable_update_checker.php');

/**
 * Tests of the basic API of the available update checker.
 */
class core_update_checker_testcase extends advanced_testcase {

    public function test_core_available_update() {
        $provider = testable_checker::instance();
        $this->assertInstanceOf('\core\update\checker', $provider);

        $provider->fake_current_environment(2012060102.00, '2.3.2 (Build: 20121012)', '2.3', array());
        $updates = $provider->get_update_info('core');
        $this->assertCount(2, $updates);

        $provider->fake_current_environment(2012060103.00, '2.3.3 (Build: 20121212)', '2.3', array());
        $updates = $provider->get_update_info('core');
        $this->assertCount(1, $updates);

        $provider->fake_current_environment(2012060103.00, '2.3.3 (Build: 20121212)', '2.3', array());
        $updates = $provider->get_update_info('core', array('minmaturity' => MATURITY_STABLE));
        $this->assertNull($updates);
    }

    /**
     * If there are no fetched data yet, the first cron should fetch them.
     *
     * @expectedException \core\update\testable_checker_cron_executed
     */
    public function test_cron_initial_fetch() {
        $provider = testable_checker::instance();
        $provider->fakerecentfetch = null;
        $provider->fakecurrenttimestamp = -1;
        $provider->cron();
    }

    /**
     * If there is a fresh fetch available, no cron execution is expected.
     */
    public function test_cron_has_fresh_fetch() {
        $provider = testable_checker::instance();
        $provider->fakerecentfetch = time() - 23 * HOURSECS; // Fetched 23 hours ago.
        $provider->fakecurrenttimestamp = -1;
        $provider->cron();
        $this->assertTrue(true); // We should get here with no exception thrown.
    }

    /**
     * If there is an outdated fetch, the cron execution is expected.
     *
     * @expectedException \core\update\testable_checker_cron_executed
     */
    public function test_cron_has_outdated_fetch() {
        $provider = testable_checker::instance();
        $provider->fakerecentfetch = time() - 49 * HOURSECS; // Fetched 49 hours ago.
        $provider->fakecurrenttimestamp = -1;
        $provider->cron();
    }

    /**
     * The first cron after 01:42 AM today should fetch the data.
     *
     * @see testable_checker::cron_execution_offset()
     */
    public function test_cron_offset_execution_not_yet() {
        $provider = testable_checker::instance();
        $provider->fakecurrenttimestamp = mktime(1, 40, 02); // 01:40:02 AM today
        $provider->fakerecentfetch = $provider->fakecurrenttimestamp - 24 * HOURSECS;
        $provider->cron();
        $this->assertTrue(true); // We should get here with no exception thrown.
    }

    /**
     * The first cron after 01:42 AM today should fetch the data and then
     * it is supposed to wait next 24 hours.
     *
     * @see testable_checker::cron_execution_offset()
     */
    public function test_cron_offset_execution() {
        $provider = testable_checker::instance();

        // The cron at 01:45 should fetch the data.
        $provider->fakecurrenttimestamp = mktime(1, 45, 02); // 01:45:02 AM today
        $provider->fakerecentfetch = $provider->fakecurrenttimestamp - 24 * HOURSECS - 1;
        $executed = false;
        try {
            $provider->cron();
        } catch (testable_checker_cron_executed $e) {
            $executed = true;
        }
        $this->assertTrue($executed, 'Cron should be executed at 01:45:02 but it was not.');

        // Another cron at 06:45 should still consider data as fresh enough.
        $provider->fakerecentfetch = $provider->fakecurrenttimestamp;
        $provider->fakecurrenttimestamp = mktime(6, 45, 03); // 06:45:03 AM
        $executed = false;
        try {
            $provider->cron();
        } catch (testable_checker_cron_executed $e) {
            $executed = true;
        }
        $this->assertFalse($executed, 'Cron should not be executed at 06:45:03 but it was.');

        // The next scheduled execution should happen the next day.
        $provider->fakecurrenttimestamp = $provider->fakerecentfetch + 24 * HOURSECS + 1;
        $executed = false;
        try {
            $provider->cron();
        } catch (testable_checker_cron_executed $e) {
            $executed = true;
        }
        $this->assertTrue($executed, 'Cron should be executed the next night but it was not.');
    }

    public function test_compare_responses_both_empty() {
        $provider = testable_checker::instance();
        $old = array();
        $new = array();
        $cmp = $provider->compare_responses($old, $new);
        $this->assertIsArray($cmp);
        $this->assertEmpty($cmp);
    }

    public function test_compare_responses_old_empty() {
        $provider = testable_checker::instance();
        $old = array();
        $new = array(
            'updates' => array(
                'core' => array(
                    array(
                        'version' => 2012060103
                    )
                )
            )
        );
        $cmp = $provider->compare_responses($old, $new);
        $this->assertIsArray($cmp);
        $this->assertNotEmpty($cmp);
        $this->assertTrue(isset($cmp['core'][0]['version']));
        $this->assertEquals(2012060103, $cmp['core'][0]['version']);
    }

    public function test_compare_responses_no_change() {
        $provider = testable_checker::instance();
        $old = $new = array(
            'updates' => array(
                'core' => array(
                    array(
                        'version' => 2012060104
                    ),
                    array(
                        'version' => 2012120100
                    )
                ),
                'mod_foo' => array(
                    array(
                        'version' => 2011010101
                    )
                )
            )
        );
        $cmp = $provider->compare_responses($old, $new);
        $this->assertIsArray($cmp);
        $this->assertEmpty($cmp);
    }

    public function test_compare_responses_new_and_missing_update() {
        $provider = testable_checker::instance();
        $old = array(
            'updates' => array(
                'core' => array(
                    array(
                        'version' => 2012060104
                    )
                ),
                'mod_foo' => array(
                    array(
                        'version' => 2011010101
                    )
                )
            )
        );
        $new = array(
            'updates' => array(
                'core' => array(
                    array(
                        'version' => 2012060104
                    ),
                    array(
                        'version' => 2012120100
                    )
                )
            )
        );
        $cmp = $provider->compare_responses($old, $new);
        $this->assertIsArray($cmp);
        $this->assertNotEmpty($cmp);
        $this->assertCount(1, $cmp);
        $this->assertCount(1, $cmp['core']);
        $this->assertEquals(2012120100, $cmp['core'][0]['version']);
    }

    public function test_compare_responses_modified_update() {
        $provider = testable_checker::instance();
        $old = array(
            'updates' => array(
                'mod_foo' => array(
                    array(
                        'version' => 2011010101
                    )
                )
            )
        );
        $new = array(
            'updates' => array(
                'mod_foo' => array(
                    array(
                        'version' => 2011010102
                    )
                )
            )
        );
        $cmp = $provider->compare_responses($old, $new);
        $this->assertIsArray($cmp);
        $this->assertNotEmpty($cmp);
        $this->assertCount(1, $cmp);
        $this->assertCount(1, $cmp['mod_foo']);
        $this->assertEquals(2011010102, $cmp['mod_foo'][0]['version']);
    }

    /**
     * @expectedException \core\update\checker_exception
     */
    public function test_compare_responses_invalid_format() {
        $provider = testable_checker::instance();
        $broken = array(
            'status' => 'ERROR' // No 'updates' key here.
        );
        $cmp = $provider->compare_responses($broken, $broken);
    }

    public function test_is_same_release_explicit() {
        $provider = testable_checker::instance();
        $this->assertTrue($provider->is_same_release('2.3dev (Build: 20120323)', '2.3dev (Build: 20120323)'));
        $this->assertTrue($provider->is_same_release('2.3dev (Build: 20120323)', '2.3dev (Build: 20120330)'));
        $this->assertFalse($provider->is_same_release('2.3dev (Build: 20120529)', '2.3 (Build: 20120601)'));
        $this->assertFalse($provider->is_same_release('2.3dev', '2.3 dev'));
        $this->assertFalse($provider->is_same_release('2.3.1', '2.3'));
        $this->assertFalse($provider->is_same_release('2.3.1', '2.3.2'));
        $this->assertTrue($provider->is_same_release('2.3.2+', '2.3.2')); // Yes, really!
        $this->assertTrue($provider->is_same_release('2.3.2 (Build: 123456)', '2.3.2+ (Build: 123457)'));
        $this->assertFalse($provider->is_same_release('3.0 Community Edition', '3.0 Enterprise Edition'));
        $this->assertTrue($provider->is_same_release('3.0 Community Edition', '3.0 Community Edition (Build: 20290101)'));
    }

    public function test_is_same_release_implicit() {
        $provider = testable_checker::instance();
        $provider->fake_current_environment(2012060102.00, '2.3.2 (Build: 20121012)', '2.3', array());
        $this->assertTrue($provider->is_same_release('2.3.2'));
        $this->assertTrue($provider->is_same_release('2.3.2+'));
        $this->assertTrue($provider->is_same_release('2.3.2+ (Build: 20121013)'));
        $this->assertFalse($provider->is_same_release('2.4dev (Build: 20121012)'));
    }
}
