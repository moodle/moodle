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
 */
class plugin_manager_test extends advanced_testcase {

    public function test_plugin_manager_instance() {
        $pluginman = testable_plugin_manager::instance();
        $this->assertTrue($pluginman instanceof testable_plugin_manager);
    }

    public function test_get_plugins() {
        $pluginman = testable_plugin_manager::instance();
        $plugins = $pluginman->get_plugins();
        $this->assertTrue(isset($plugins['mod']['foo']));
        $this->assertTrue($plugins['mod']['foo'] instanceof testable_plugininfo_mod);
    }

    public function test_get_status() {
        $pluginman = testable_plugin_manager::instance();
        $plugins = $pluginman->get_plugins();
        $modfoo = $plugins['mod']['foo'];
        $this->assertEquals($modfoo->get_status(), plugin_manager::PLUGIN_STATUS_UPGRADE);
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
}


/**
 * Tests of the basic API of the available update checker
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
 * Modified {@link plugininfo_mod} suitable for testing purposes
 */
class testable_plugininfo_mod extends plugininfo_mod {

    public function init_display_name() {
        $this->displayname = ucfirst($this->name);
    }

    public function load_disk_version() {
        $this->versiondisk = 2012030500;
    }

    protected function load_version_php() {
        return (object)array(
            'version' => 2012030500,
            'requires' => 2012010100,
            'component' => $this->type.'_'.$this->name);
    }

    public function load_db_version() {
        $this->versiondb = 2012022900;
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
        global $CFG;

        $this->pluginsinfo = array(
            'mod' => array(
                'foo' => plugininfo_default_factory::make('mod', $CFG->dirroot.'/mod', 'foo',
                    $CFG->dirroot.'/mod/foo', 'testable_plugininfo_mod'),
                'bar' => plugininfo_default_factory::make('mod', $CFG->dirroot.'/bar', 'bar',
                    $CFG->dirroot.'/mod/bar', 'testable_plugininfo_mod'),
            )
        );

        $checker = testable_available_update_checker::instance();
        $this->pluginsinfo['mod']['foo']->check_available_updates($checker);
        $this->pluginsinfo['mod']['bar']->check_available_updates($checker);

        return $this->pluginsinfo;
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
