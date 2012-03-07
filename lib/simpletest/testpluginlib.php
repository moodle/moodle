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
 * @package     core
 * @category    test
 * @copyright   2012 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (empty($CFG->unittestprefix)) {
    die('You must define $CFG->unittestprefix to run these unit tests.');
}

require_once($CFG->libdir.'/pluginlib.php');

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
                'buz' => plugininfo_default_factory::make('mod', $CFG->dirroot.'/buz', 'buz',
                    $CFG->dirroot.'/mod/buz', 'testable_plugininfo_mod'),
            )
        );

        $checker = testable_available_update_checker::instance();
        $this->pluginsinfo['mod']['foo']->check_available_update($checker);
        $this->pluginsinfo['mod']['bar']->check_available_update($checker);
        $this->pluginsinfo['mod']['buz']->check_available_update($checker);

        return $this->pluginsinfo;
    }
}


/**
 * Modified version of {@link available_update_checker} suitable for testing
 */
class testable_available_update_checker extends available_update_checker {

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

    /**
     * Do not load config in this testable subclass
     */
    protected function load_config() {
    }

    /**
     * Do not fetch anything in this testable subclass
     */
    public function fetch() {
    }

    /**
     * Here we simulate read access to the fetched remote statuses
     */
    public function get_update_info($component) {
        if ($component === 'mod_foo') {
            // no update available
            return (object)array(
                'version' => 2012030500,
            );

        } else if ($component === 'mod_bar') {
            // there is an update available
            return (object)array(
                'version' => 2012030501,
            );

        } else {
            // nothing known to us
            return null;
        }
    }

    /**
     * Makes the method public so we can test it
     */
    public function merge_components_info(stdClass $old, stdClass $new, $timegenerated=null) {
        return parent::merge_components_info($old, $new, $timegenerated);
    }
}


/**
 * Tests of the basic API of the plugin manager
 */
class plugin_manager_test extends UnitTestCase {

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
        $this->assertEqual($modfoo->get_status(), plugin_manager::PLUGIN_STATUS_UPGRADE);
    }

    public function test_available_update() {
        $pluginman = testable_plugin_manager::instance();
        $plugins = $pluginman->get_plugins();
        $this->assertFalse($plugins['mod']['foo']->available_update());
        $this->assertNull($plugins['mod']['buz']->available_update());
        $this->assertIsA($plugins['mod']['bar']->available_update(), 'stdClass');
        $this->assertEqual($plugins['mod']['bar']->available_update()->version, 2012030501);
    }
}


/**
 * Tests of the basic API of the available update checker
 */
class available_update_checker_test extends UnitTestCase {

    public function test_core_available_update() {
        $provider = testable_available_update_checker::instance();
        $this->assertTrue($provider instanceof available_update_checker);
    }

    public function test_merge_components_info() {
        $old = (object)array(
            '2.2' => (object)array(
                'core' => (object)array(
                    'version' => 2011120501.11,
                    'release' => '2.2.1+ (Build: 20120301)',
                    'maturity' => MATURITY_STABLE,
                ),
                'mod_foo' => (object)array(
                    'version' => 2011010100,
                ),
                'mod_bar' => (object)array(
                    'version' => 2011020200,
                )
            )
        );
        $new = (object)array(
            '2.2' => (object)array(
                'core' => (object)array(
                    'version' => 2011120501.12,
                    'release' => '2.2.1+ (Build: 20120302)',
                    'maturity' => MATURITY_STABLE,
                ),
                'mod_bar' => (object)array(
                    'version' => 2011020201,
                ),
            ),
            '2.3' => (object)array(
                'core' => (object)array(
                    'version' => 2012030100.00,
                    'release' => '2.3dev (Build: 20120301)',
                    'maturity' => MATURITY_ALPHA,
                ),
                'mod_foo' => (object)array(
                    'version' => 2012010200,
                )
            )
        );
        $checker = testable_available_update_checker::instance();
        $now = time();
        $merged = $checker->merge_components_info($old, $new, $now);
        $this->assertEqual($merged->{2.2}->core->version, 2011120501.12); // from $new
        $this->assertEqual($merged->{2.2}->mod_bar->version, 2011020201); // from $new
        $this->assertEqual($merged->{2.2}->mod_foo->version, 2011010100); // from $old
        $this->assertEqual($merged->{2.3}->core->version, 2012030100.00); // from $new
        $this->assertFalse(isset($merged->{2.3}->mod_bar));
    }
}
