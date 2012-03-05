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
            )
        );

        return $this->pluginsinfo;
    }
}


/**
 * Test cases for the pluginlib API
 *
 * These are basic tests to document the basic API of the plugin manager.
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
}
