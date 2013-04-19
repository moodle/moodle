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
 * Unit tests for the lib/pluginlib.php library
 *
 * Execute the core_plugin group to run all tests in this file:
 *
 *  $ phpunit --group core_plugin
 *
 * @package   core
 * @category  phpunit
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/pluginlib.php');


/**
 * Tests of the basic API of the plugin manager
 *
 * @group core_plugin
 */
class plugin_manager_test extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_plugin_manager_instance() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertTrue($pluginman instanceof testable_plugin_manager);
    }

    public function test_get_plugins_of_type() {
        $pluginman = testable_plugin_manager::instance();
        $mods = $pluginman->get_plugins_of_type('mod');
        $this->assertEquals('array', gettype($mods));
        $this->assertEquals(5, count($mods));
        $this->assertTrue($mods['foo'] instanceof testable_plugininfo_mod);
        $this->assertTrue($mods['bar'] instanceof testable_plugininfo_mod);
        $this->assertTrue($mods['baz'] instanceof testable_plugininfo_mod);
        $this->assertTrue($mods['qux'] instanceof testable_plugininfo_mod);
        $this->assertTrue($mods['new'] instanceof testable_plugininfo_mod);
        $foolishes = $pluginman->get_plugins_of_type('foolish');
        $this->assertEquals(2, count($foolishes));
        $this->assertTrue($foolishes['frog'] instanceof testable_pluginfo_foolish);
        $this->assertTrue($foolishes['hippo'] instanceof testable_pluginfo_foolish);
        $bazmegs = $pluginman->get_plugins_of_type('bazmeg');
        $this->assertEquals(1, count($bazmegs));
        $this->assertTrue($bazmegs['one'] instanceof testable_pluginfo_bazmeg);
        $quxcats = $pluginman->get_plugins_of_type('quxcat');
        $this->assertEquals(1, count($quxcats));
        $this->assertTrue($quxcats['one'] instanceof testable_pluginfo_quxcat);
        $unknown = $pluginman->get_plugins_of_type('muhehe');
        $this->assertSame(array(), $unknown);
    }

    public function test_get_plugins() {
        $pluginman = testable_plugin_manager::instance();
        $plugins = $pluginman->get_plugins();
        $this->assertEquals('array', gettype($plugins));
        $this->assertTrue(isset($plugins['mod']['foo']));
        $this->assertTrue(isset($plugins['mod']['bar']));
        $this->assertTrue(isset($plugins['mod']['baz']));
        $this->assertTrue(isset($plugins['mod']['new']));
        $this->assertTrue(isset($plugins['foolish']['frog']));
        $this->assertTrue(isset($plugins['foolish']['hippo']));
        $this->assertTrue($plugins['mod']['foo'] instanceof testable_plugininfo_mod);
        $this->assertTrue($plugins['mod']['bar'] instanceof testable_plugininfo_mod);
        $this->assertTrue($plugins['mod']['baz'] instanceof testable_plugininfo_mod);
        $this->assertTrue($plugins['mod']['new'] instanceof testable_plugininfo_mod);
        $this->assertTrue($plugins['foolish']['frog'] instanceof testable_pluginfo_foolish);
        $this->assertTrue($plugins['foolish']['hippo'] instanceof testable_pluginfo_foolish);
        $this->assertTrue($plugins['bazmeg']['one'] instanceof testable_pluginfo_bazmeg);
        $this->assertTrue($plugins['quxcat']['one'] instanceof testable_pluginfo_quxcat);
    }

    public function test_get_subplugins_of_plugin() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertSame(array(), $pluginman->get_subplugins_of_plugin('mod_missing'));
        $this->assertSame(array(), $pluginman->get_subplugins_of_plugin('mod_bar'));
        $foosubs = $pluginman->get_subplugins_of_plugin('mod_foo');
        $this->assertEquals('array', gettype($foosubs));
        $this->assertEquals(2, count($foosubs));
        $this->assertTrue($foosubs['foolish_frog'] instanceof testable_pluginfo_foolish);
        $this->assertTrue($foosubs['foolish_hippo'] instanceof testable_pluginfo_foolish);
        $bazsubs = $pluginman->get_subplugins_of_plugin('mod_baz');
        $this->assertEquals('array', gettype($bazsubs));
        $this->assertEquals(1, count($bazsubs));
        $this->assertTrue($bazsubs['bazmeg_one'] instanceof testable_pluginfo_bazmeg);
        $quxsubs = $pluginman->get_subplugins_of_plugin('mod_qux');
        $this->assertEquals('array', gettype($quxsubs));
        $this->assertEquals(1, count($quxsubs));
        $this->assertTrue($quxsubs['quxcat_one'] instanceof testable_pluginfo_quxcat);
    }

    public function test_get_subplugins() {
        $pluginman = testable_plugin_manager::instance();
        $subplugins = $pluginman->get_subplugins();
        $this->assertTrue(isset($subplugins['mod_foo']['foolish']));
        $this->assertTrue(isset($subplugins['mod_baz']['bazmeg']));
        $this->assertTrue(isset($subplugins['mod_qux']['quxcat']));
    }

    public function test_get_parent_of_subplugin() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertEquals('mod_foo', $pluginman->get_parent_of_subplugin('foolish'));
        $this->assertEquals('mod_baz', $pluginman->get_parent_of_subplugin('bazmeg'));
        $this->assertEquals('mod_qux', $pluginman->get_parent_of_subplugin('quxcat'));
        $this->assertSame(false, $pluginman->get_parent_of_subplugin('mod'));
        $this->assertSame(false, $pluginman->get_parent_of_subplugin('unknown'));
        $plugins = $pluginman->get_plugins();
        $this->assertFalse($plugins['mod']['foo']->is_subplugin());
        $this->assertSame(false, $plugins['mod']['foo']->get_parent_plugin());
        $this->assertTrue($plugins['foolish']['frog']->is_subplugin());
        $this->assertEquals('mod_foo', $plugins['foolish']['frog']->get_parent_plugin());
    }

    public function test_plugin_name() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertEquals('Foo', $pluginman->plugin_name('mod_foo'));
        $this->assertEquals('Bar', $pluginman->plugin_name('mod_bar'));
        $this->assertEquals('Frog', $pluginman->plugin_name('foolish_frog'));
        $this->assertEquals('Hippo', $pluginman->plugin_name('foolish_hippo'));
        $this->assertEquals('One', $pluginman->plugin_name('bazmeg_one'));
        $this->assertEquals('One', $pluginman->plugin_name('quxcat_one'));
    }

    public function test_get_plugin_info() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertTrue($pluginman->get_plugin_info('mod_foo') instanceof testable_plugininfo_mod);
        $this->assertTrue($pluginman->get_plugin_info('foolish_frog') instanceof testable_pluginfo_foolish);
    }

    public function test_other_plugins_that_require() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertEquals(array('foolish_frog'), $pluginman->other_plugins_that_require('mod_foo'));
        $this->assertEquals(2, count($pluginman->other_plugins_that_require('foolish_frog')));
        $this->assertTrue(in_array('foolish_hippo', $pluginman->other_plugins_that_require('foolish_frog')));
        $this->assertTrue(in_array('mod_foo', $pluginman->other_plugins_that_require('foolish_frog')));
        $this->assertEquals(array(), $pluginman->other_plugins_that_require('foolish_hippo'));
        $this->assertEquals(array('mod_foo'), $pluginman->other_plugins_that_require('mod_bar'));
        $this->assertEquals(array('mod_foo'), $pluginman->other_plugins_that_require('mod_missing'));
        $this->assertEquals(array('quxcat_one'), $pluginman->other_plugins_that_require('bazmeg_one'));
    }

    public function test_are_dependencies_satisfied() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertTrue($pluginman->are_dependencies_satisfied(array()));
        $this->assertTrue($pluginman->are_dependencies_satisfied(array(
            'mod_bar' => 2012030500,
        )));
        $this->assertTrue($pluginman->are_dependencies_satisfied(array(
            'mod_bar' => ANY_VERSION,
        )));
        $this->assertFalse($pluginman->are_dependencies_satisfied(array(
            'mod_bar' => 2099010000,
        )));
        $this->assertFalse($pluginman->are_dependencies_satisfied(array(
            'mod_bar' => 2012030500,
            'mod_missing' => ANY_VERSION,
        )));
    }

    public function test_all_plugins_ok() {
        $pluginman = testable_plugin_manager::instance();
        $failedplugins = array();
        $this->assertFalse($pluginman->all_plugins_ok(2013010100, $failedplugins));
        $this->assertTrue(in_array('mod_foo', $failedplugins)); // Requires mod_missing
        $this->assertFalse(in_array('mod_bar', $failedplugins));
        $this->assertFalse(in_array('foolish_frog', $failedplugins));
        $this->assertFalse(in_array('foolish_hippo', $failedplugins));

        $failedplugins = array();
        $this->assertFalse($pluginman->all_plugins_ok(2012010100, $failedplugins));
        $this->assertTrue(in_array('mod_foo', $failedplugins)); // Requires mod_missing
        $this->assertFalse(in_array('mod_bar', $failedplugins));
        $this->assertTrue(in_array('foolish_frog', $failedplugins)); // Requires Moodle 2013010100
        $this->assertFalse(in_array('foolish_hippo', $failedplugins));

        $failedplugins = array();
        $this->assertFalse($pluginman->all_plugins_ok(2011010100, $failedplugins));
        $this->assertTrue(in_array('mod_foo', $failedplugins)); // Requires mod_missing and Moodle 2012010100
        $this->assertTrue(in_array('mod_bar', $failedplugins)); // Requires Moodle 2012010100
        $this->assertTrue(in_array('foolish_frog', $failedplugins)); // Requires Moodle 2013010100
        $this->assertTrue(in_array('foolish_hippo', $failedplugins)); // Requires Moodle 2012010100
    }

    public function test_some_plugins_updatable() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertTrue($pluginman->some_plugins_updatable()); // We have available update for mod_foo.
    }

    public function test_is_standard() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertTrue($pluginman->get_plugin_info('mod_bar')->is_standard());
        $this->assertFalse($pluginman->get_plugin_info('mod_foo')->is_standard());
        $this->assertFalse($pluginman->get_plugin_info('foolish_frog')->is_standard());
    }

    public function test_get_status() {
        $pluginman = testable_plugin_manager::instance();
        $plugins = $pluginman->get_plugins();
        $this->assertEquals(plugin_manager::PLUGIN_STATUS_UPGRADE, $plugins['mod']['foo']->get_status());
        $this->assertEquals(plugin_manager::PLUGIN_STATUS_NEW, $plugins['mod']['new']->get_status());
        $this->assertEquals(plugin_manager::PLUGIN_STATUS_NEW, $plugins['bazmeg']['one']->get_status());
        $this->assertEquals(plugin_manager::PLUGIN_STATUS_UPTODATE, $plugins['quxcat']['one']->get_status());
    }

    public function test_available_update() {
        $pluginman = testable_plugin_manager::instance();
        $plugins = $pluginman->get_plugins();
        $this->assertNull($plugins['mod']['bar']->available_updates());
        $this->assertEquals('array', gettype($plugins['mod']['foo']->available_updates()));
        foreach ($plugins['mod']['foo']->available_updates() as $availableupdate) {
            $this->assertInstanceOf('available_update_info', $availableupdate);
        }
    }

    public function test_can_uninstall_plugin() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertFalse($pluginman->can_uninstall_plugin('mod_missing'));
        $this->assertTrue($pluginman->can_uninstall_plugin('mod_foo')); // Because mod_foo is required by foolish_frog only
                                                                        // and foolish_frog is required by mod_foo and foolish_hippo only.
        $this->assertFalse($pluginman->can_uninstall_plugin('mod_bar')); // Because mod_bar is required by mod_foo.
        $this->assertFalse($pluginman->can_uninstall_plugin('mod_qux')); // Because even if no plugin (not even subplugins) declare
                                                                         // dependency on it, but its subplugin can't be uninstalled.
        $this->assertFalse($pluginman->can_uninstall_plugin('mod_baz')); // Because it's subplugin bazmeg_one is required by quxcat_one.
        $this->assertFalse($pluginman->can_uninstall_plugin('mod_new')); // Because it is not installed.
        $this->assertFalse($pluginman->can_uninstall_plugin('quxcat_one')); // Because of testable_pluginfo_quxcat::is_uninstall_allowed().
        $this->assertFalse($pluginman->can_uninstall_plugin('foolish_frog')); // Because foolish_hippo requires it.
    }

    public function test_get_uninstall_url() {
        $pluginman = testable_plugin_manager::instance();
        foreach ($pluginman->get_plugins() as $plugintype => $plugininfos) {
            foreach ($plugininfos as $plugininfo) {
                $this->assertTrue($plugininfo->get_uninstall_url() instanceof moodle_url);
            }
        }
    }
}


/**
 * Tests of the basic API of the available update checker
 *
 * @group core_plugin
 */
class available_update_checker_test extends advanced_testcase {

    public function test_core_available_update() {
        $provider = testable_available_update_checker::instance();
        $this->assertTrue($provider instanceof available_update_checker);

        $provider->fake_current_environment(2012060102.00, '2.3.2 (Build: 20121012)', '2.3', array());
        $updates = $provider->get_update_info('core');
        $this->assertEquals(count($updates), 2);

        $provider->fake_current_environment(2012060103.00, '2.3.3 (Build: 20121212)', '2.3', array());
        $updates = $provider->get_update_info('core');
        $this->assertEquals(count($updates), 1);

        $provider->fake_current_environment(2012060103.00, '2.3.3 (Build: 20121212)', '2.3', array());
        $updates = $provider->get_update_info('core', array('minmaturity' => MATURITY_STABLE));
        $this->assertNull($updates);
    }

    /**
     * If there are no fetched data yet, the first cron should fetch them
     */
    public function test_cron_initial_fetch() {
        $provider = testable_available_update_checker::instance();
        $provider->fakerecentfetch = null;
        $provider->fakecurrenttimestamp = -1;
        $this->setExpectedException('testable_available_update_checker_cron_executed');
        $provider->cron();
    }

    /**
     * If there is a fresh fetch available, no cron execution is expected
     */
    public function test_cron_has_fresh_fetch() {
        $provider = testable_available_update_checker::instance();
        $provider->fakerecentfetch = time() - 23 * HOURSECS; // fetched 23 hours ago
        $provider->fakecurrenttimestamp = -1;
        $provider->cron();
        $this->assertTrue(true); // we should get here with no exception thrown
    }

    /**
     * If there is an outdated fetch, the cron execution is expected
     */
    public function test_cron_has_outdated_fetch() {
        $provider = testable_available_update_checker::instance();
        $provider->fakerecentfetch = time() - 49 * HOURSECS; // fetched 49 hours ago
        $provider->fakecurrenttimestamp = -1;
        $this->setExpectedException('testable_available_update_checker_cron_executed');
        $provider->cron();
    }

    /**
     * The first cron after 01:42 AM today should fetch the data
     *
     * @see testable_available_update_checker::cron_execution_offset()
     */
    public function test_cron_offset_execution_not_yet() {
        $provider = testable_available_update_checker::instance();
        $provider->fakecurrenttimestamp = mktime(1, 40, 02); // 01:40:02 AM today
        $provider->fakerecentfetch = $provider->fakecurrenttimestamp - 24 * HOURSECS;
        $provider->cron();
        $this->assertTrue(true); // we should get here with no exception thrown
    }

    /**
     * The first cron after 01:42 AM today should fetch the data and then
     * it is supposed to wait next 24 hours.
     *
     * @see testable_available_update_checker::cron_execution_offset()
     */
    public function test_cron_offset_execution() {
        $provider = testable_available_update_checker::instance();

        // the cron at 01:45 should fetch the data
        $provider->fakecurrenttimestamp = mktime(1, 45, 02); // 01:45:02 AM today
        $provider->fakerecentfetch = $provider->fakecurrenttimestamp - 24 * HOURSECS - 1;
        $executed = false;
        try {
            $provider->cron();
        } catch (testable_available_update_checker_cron_executed $e) {
            $executed = true;
        }
        $this->assertTrue($executed, 'Cron should be executed at 01:45:02 but it was not.');

        // another cron at 06:45 should still consider data as fresh enough
        $provider->fakerecentfetch = $provider->fakecurrenttimestamp;
        $provider->fakecurrenttimestamp = mktime(6, 45, 03); // 06:45:03 AM
        $executed = false;
        try {
            $provider->cron();
        } catch (testable_available_update_checker_cron_executed $e) {
            $executed = true;
        }
        $this->assertFalse($executed, 'Cron should not be executed at 06:45:03 but it was.');

        // the next scheduled execution should happen the next day
        $provider->fakecurrenttimestamp = $provider->fakerecentfetch + 24 * HOURSECS + 1;
        $executed = false;
        try {
            $provider->cron();
        } catch (testable_available_update_checker_cron_executed $e) {
            $executed = true;
        }
        $this->assertTrue($executed, 'Cron should be executed the next night but it was not.');
    }

    public function test_compare_responses_both_empty() {
        $provider = testable_available_update_checker::instance();
        $old = array();
        $new = array();
        $cmp = $provider->compare_responses($old, $new);
        $this->assertEquals('array', gettype($cmp));
        $this->assertTrue(empty($cmp));
    }

    public function test_compare_responses_old_empty() {
        $provider = testable_available_update_checker::instance();
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
        $this->assertEquals('array', gettype($cmp));
        $this->assertFalse(empty($cmp));
        $this->assertTrue(isset($cmp['core'][0]['version']));
        $this->assertEquals($cmp['core'][0]['version'], 2012060103);
    }

    public function test_compare_responses_no_change() {
        $provider = testable_available_update_checker::instance();
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
        $this->assertEquals('array', gettype($cmp));
        $this->assertTrue(empty($cmp));
    }

    public function test_compare_responses_new_and_missing_update() {
        $provider = testable_available_update_checker::instance();
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
        $this->assertEquals('array', gettype($cmp));
        $this->assertFalse(empty($cmp));
        $this->assertEquals(count($cmp), 1);
        $this->assertEquals(count($cmp['core']), 1);
        $this->assertEquals($cmp['core'][0]['version'], 2012120100);
    }

    public function test_compare_responses_modified_update() {
        $provider = testable_available_update_checker::instance();
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
        $this->assertEquals('array', gettype($cmp));
        $this->assertFalse(empty($cmp));
        $this->assertEquals(count($cmp), 1);
        $this->assertEquals(count($cmp['mod_foo']), 1);
        $this->assertEquals($cmp['mod_foo'][0]['version'], 2011010102);
    }

    public function test_compare_responses_invalid_format() {
        $provider = testable_available_update_checker::instance();
        $broken = array(
            'status' => 'ERROR' // no 'updates' key here
        );
        $this->setExpectedException('available_update_checker_exception');
        $cmp = $provider->compare_responses($broken, $broken);
    }

    public function test_is_same_release_explicit() {
        $provider = testable_available_update_checker::instance();
        $this->assertTrue($provider->is_same_release('2.3dev (Build: 20120323)', '2.3dev (Build: 20120323)'));
        $this->assertTrue($provider->is_same_release('2.3dev (Build: 20120323)', '2.3dev (Build: 20120330)'));
        $this->assertFalse($provider->is_same_release('2.3dev (Build: 20120529)', '2.3 (Build: 20120601)'));
        $this->assertFalse($provider->is_same_release('2.3dev', '2.3 dev'));
        $this->assertFalse($provider->is_same_release('2.3.1', '2.3'));
        $this->assertFalse($provider->is_same_release('2.3.1', '2.3.2'));
        $this->assertTrue($provider->is_same_release('2.3.2+', '2.3.2')); // yes, really
        $this->assertTrue($provider->is_same_release('2.3.2 (Build: 123456)', '2.3.2+ (Build: 123457)'));
        $this->assertFalse($provider->is_same_release('3.0 Community Edition', '3.0 Enterprise Edition'));
        $this->assertTrue($provider->is_same_release('3.0 Community Edition', '3.0 Community Edition (Build: 20290101)'));
    }

    public function test_is_same_release_implicit() {
        $provider = testable_available_update_checker::instance();
        $provider->fake_current_environment(2012060102.00, '2.3.2 (Build: 20121012)', '2.3', array());
        $this->assertTrue($provider->is_same_release('2.3.2'));
        $this->assertTrue($provider->is_same_release('2.3.2+'));
        $this->assertTrue($provider->is_same_release('2.3.2+ (Build: 20121013)'));
        $this->assertFalse($provider->is_same_release('2.4dev (Build: 20121012)'));
    }
}


/**
 * Base class for testable plugininfo classes.
 */
class testable_plugininfo_base extends plugininfo_base {

    protected function get_plugin_manager() {
        return testable_plugin_manager::instance();
    }
}


/**
 * Modified {@link plugininfo_mod} suitable for testing purposes
 */
class testable_plugininfo_mod extends plugininfo_mod {

    public function init_display_name() {
        $this->displayname = ucfirst($this->name);
    }

    public function is_standard() {
        if ($this->component === 'mod_foo') {
            return false;
        } else {
            return true;
        }
    }

    public function load_db_version() {
        if ($this->component !== 'mod_new') {
            $this->versiondb = 2012022900;
        }
    }

    public function is_uninstall_allowed() {
        return true; // Allow uninstall for standard plugins too.
    }

    protected function get_plugin_manager() {
        return testable_plugin_manager::instance();
    }
}


/**
 * Testable class representing subplugins of testable mod_foo
 */
class testable_pluginfo_foolish extends testable_plugininfo_base {

    public function init_display_name() {
        $this->displayname = ucfirst($this->name);
    }

    public function is_standard() {
        return false;
    }

    public function load_db_version() {
        $this->versiondb = 2012022900;
    }
}


/**
 * Testable class representing subplugins of testable mod_baz
 */
class testable_pluginfo_bazmeg extends testable_plugininfo_base {

    public function init_display_name() {
        $this->displayname = ucfirst($this->name);
    }

    public function is_standard() {
        return false;
    }

    public function load_db_version() {
        $this->versiondb = null;
    }
}


/**
 * Testable class representing subplugins of testable mod_qux
 */
class testable_pluginfo_quxcat extends testable_plugininfo_base {

    public function init_display_name() {
        $this->displayname = ucfirst($this->name);
    }

    public function is_standard() {
        return false;
    }

    public function load_db_version() {
        $this->versiondb = 2013041103;
    }

    public function is_uninstall_allowed() {
        return false;
    }
}


/**
 * Modified {@link plugin_manager} suitable for testing purposes
 */
class testable_plugin_manager extends plugin_manager {

    /**
     * Factory method for this class
     *
     * @return plugin_manager the singleton instance
     */
    public static function instance() {
        global $CFG;

        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    /**
     * A version of {@link plugin_manager::get_plugins()} that prepares some faked
     * testable instances.
     *
     * @param bool $disablecache ignored in this class
     * @return array
     */
    public function get_plugins($disablecache=false) {

        $dirroot = dirname(__FILE__).'/fixtures/mockplugins';

        $this->pluginsinfo = array(
            'mod' => array(
                'foo' => plugininfo_default_factory::make('mod', $dirroot.'/mod', 'foo',
                    $dirroot.'/mod/foo', 'testable_plugininfo_mod'),
                'bar' => plugininfo_default_factory::make('mod', $dirroot.'/bar', 'bar',
                    $dirroot.'/mod/bar', 'testable_plugininfo_mod'),
                'baz' => plugininfo_default_factory::make('mod', $dirroot.'/baz', 'baz',
                    $dirroot.'/mod/baz', 'testable_plugininfo_mod'),
                'qux' => plugininfo_default_factory::make('mod', $dirroot.'/qux', 'qux',
                    $dirroot.'/mod/qux', 'testable_plugininfo_mod'),
                'new' => plugininfo_default_factory::make('mod', $dirroot.'/new', 'new',
                    $dirroot.'/mod/new', 'testable_plugininfo_mod'),
            ),
            'foolish' => array(
                'frog' => plugininfo_default_factory::make('foolish', $dirroot.'/mod/foo/lish', 'frog',
                    $dirroot.'/mod/foo/lish/frog', 'testable_pluginfo_foolish'),
                'hippo' => plugininfo_default_factory::make('foolish', $dirroot.'/mod/foo/lish', 'hippo',
                    $dirroot.'/mod/foo/lish/hippo', 'testable_pluginfo_foolish'),
            ),
            'bazmeg' => array(
                'one' => plugininfo_default_factory::make('bazmeg', $dirroot.'/mod/baz/meg', 'one',
                    $dirroot.'/mod/baz/meg/one', 'testable_pluginfo_bazmeg'),
            ),
            'quxcat' => array(
                'one' => plugininfo_default_factory::make('quxcat', $dirroot.'/mod/qux/cat', 'one',
                    $dirroot.'/mod/qux/cat/one', 'testable_pluginfo_quxcat'),
            ),
        );

        $checker = testable_available_update_checker::instance();
        $this->pluginsinfo['mod']['foo']->check_available_updates($checker);
        $this->pluginsinfo['mod']['bar']->check_available_updates($checker);
        $this->pluginsinfo['mod']['baz']->check_available_updates($checker);
        $this->pluginsinfo['mod']['new']->check_available_updates($checker);
        $this->pluginsinfo['bazmeg']['one']->check_available_updates($checker);
        $this->pluginsinfo['quxcat']['one']->check_available_updates($checker);

        return $this->pluginsinfo;
    }

    /**
     * Testable version of {@link plugin_manager::get_subplugins()} that works with
     * the simulated environment.
     *
     * In this case, the mod_foo fake module provides subplugins of type 'foolish',
     * mod_baz provides subplugins of type 'bazmeg' and mod_qux has 'quxcat'.
     *
     * @param bool $disablecache ignored in this class
     * @return array
     */
    public function get_subplugins($disablecache=false) {

        $this->subpluginsinfo = array(
            'mod_foo' => array(
                'foolish' => (object)array(
                    'type' => 'foolish',
                    'typerootdir' => 'mod/foo/lish',
                ),
            ),
            'mod_baz' => array(
                'bazmeg' => (object)array(
                    'type' => 'bazmeg',
                    'typerootdir' => 'mod/baz/meg',
                ),
            ),
            'mod_qux' => array(
                'quxcat' => (object)array(
                    'type' => 'quxcat',
                    'typerootdir' => 'mod/qux/cat',
                ),
            ),
        );

        return $this->subpluginsinfo;
    }

    /**
     * Adds support for mock plugin types.
     */
    protected function normalize_component($component) {

        // List of mock plugin types used in these unit tests.
        $faketypes = array('foolish', 'bazmeg', 'quxcat');

        foreach ($faketypes as $faketype) {
            if (strpos($component, $faketype.'_') === 0) {
                return explode('_', $component, 2);
            }
        }

        return parent::normalize_component($component);
    }

    public function plugintype_name($type) {
        return ucfirst($type);
    }

    public function plugintype_name_plural($type) {
        return ucfirst($type).'s'; // Simple, isn't it? ;-)
    }

    public function plugin_external_source($component) {
        if ($component === 'foolish_frog') {
            return true;
        }
        return false;
    }
}


/**
 * Modified version of {@link available_update_checker} suitable for testing
 */
class testable_available_update_checker extends available_update_checker {

    /** @var replaces the default DB table storage for the fetched response */
    protected $fakeresponsestorage;
    /** @var int stores the fake recentfetch value */
    public $fakerecentfetch = -1;
    /** @var int stores the fake value of time() */
    public $fakecurrenttimestamp = -1;

    /**
     * Factory method for this class
     *
     * @return testable_available_update_checker the singleton instance
     */
    public static function instance() {
        global $CFG;

        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    protected function validate_response($response) {
    }

    protected function store_response($response) {
        $this->fakeresponsestorage = $response;
    }

    protected function restore_response($forcereload = false) {
        $this->recentfetch = time();
        $this->recentresponse = $this->decode_response($this->get_fake_response());
    }

    public function compare_responses(array $old, array $new) {
        return parent::compare_responses($old, $new);
    }

    public function is_same_release($remote, $local=null) {
        return parent::is_same_release($remote, $local);
    }

    protected function load_current_environment($forcereload=false) {
    }

    public function fake_current_environment($version, $release, $branch, array $plugins) {
        $this->currentversion = $version;
        $this->currentrelease = $release;
        $this->currentbranch = $branch;
        $this->currentplugins = $plugins;
    }

    public function get_last_timefetched() {
        if ($this->fakerecentfetch == -1) {
            return parent::get_last_timefetched();
        } else {
            return $this->fakerecentfetch;
        }
    }

    private function get_fake_response() {
        $fakeresponse = array(
            'status' => 'OK',
            'provider' => 'http://download.moodle.org/api/1.0/updates.php',
            'apiver' => '1.0',
            'timegenerated' => time(),
            'forversion' => '2012010100.00',
            'forbranch' => '2.3',
            'ticket' => sha1('No, I am not going to mention the word "frog" here. Oh crap. I just did.'),
            'updates' => array(
                'core' => array(
                    array(
                        'version' => 2012060103.00,
                        'release' => '2.3.3 (Build: 20121201)',
                        'maturity' => 200,
                        'url' => 'http://download.moodle.org/',
                        'download' => 'http://download.moodle.org/download.php/MOODLE_23_STABLE/moodle-2.3.3-latest.zip',
                    ),
                    array(
                        'version' => 2012120100.00,
                        'release' => '2.4dev (Build: 20121201)',
                        'maturity' => 50,
                        'url' => 'http://download.moodle.org/',
                        'download' => 'http://download.moodle.org/download.php/MOODLE_24_STABLE/moodle-2.4.0-latest.zip',
                    ),
                ),
                'mod_foo' => array(
                    array(
                        'version' => 2012030501,
                        'requires' => 2012010100,
                        'maturity' => 200,
                        'release' => '1.1',
                        'url' => 'http://moodle.org/plugins/blahblahblah/',
                        'download' => 'http://moodle.org/plugins/download.php/blahblahblah',
                    ),
                    array(
                        'version' => 2012030502,
                        'requires' => 2012010100,
                        'maturity' => 100,
                        'release' => '1.2 beta',
                        'url' => 'http://moodle.org/plugins/',
                    ),
                ),
            ),
        );

        return json_encode($fakeresponse);
    }

    protected function cron_current_timestamp() {
        if ($this->fakecurrenttimestamp == -1) {
            return parent::cron_current_timestamp();
        } else {
            return $this->fakecurrenttimestamp;
        }
    }

    protected function cron_mtrace($msg, $eol = PHP_EOL) {
    }

    protected function cron_autocheck_enabled() {
        return true;
    }

    protected function cron_execution_offset() {
        // autofetch should run by the first cron after 01:42 AM
        return 42 * MINSECS;
    }

    protected function cron_execute() {
        throw new testable_available_update_checker_cron_executed('Cron executed!');
    }
}


/**
 * Exception used to detect {@link available_update_checker::cron_execute()} calls
 */
class testable_available_update_checker_cron_executed extends Exception {

}


/**
 * Modified {@link available_update_deployer} suitable for testing purposes
 */
class testable_available_update_deployer extends available_update_deployer {

}


/**
 * Test cases for {@link available_update_deployer} class
 *
 * @group core_plugin
 */
class available_update_deployer_test extends advanced_testcase {

    public function test_magic_setters() {
        $deployer = testable_available_update_deployer::instance();
        $value = new moodle_url('/');
        $deployer->set_returnurl($value);
        $this->assertSame($deployer->get_returnurl(), $value);
    }

    public function test_prepare_authorization() {
        global $CFG;

        $deployer = testable_available_update_deployer::instance();
        list($passfile, $password) = $deployer->prepare_authorization();
        $filename = $CFG->phpunit_dataroot.'/mdeploy/auth/'.$passfile;
        $this->assertFileExists($filename);
        $stored = file($filename, FILE_IGNORE_NEW_LINES);
        $this->assertEquals(count($stored), 2);
        $this->assertGreaterThan(23, strlen($stored[0]));
        $this->assertSame($stored[0], $password);
        $this->assertTrue(time() - (int)$stored[1] < 60);
    }
}
