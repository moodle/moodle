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
 * APC unit tests.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_XCACHE', true);
 *
 * @package    cachestore_apc
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/apc/lib.php');

/**
 * APC unit test class.
 *
 * @package    cachestore_apc
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_apc_test extends cachestore_tests {
    /**
     * Returns the apc class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_apc';
    }

    /**
     * Test purging the apc cache store.
     */
    public function test_purge() {
        if (!cachestore_apc::are_requirements_met() || !defined('TEST_CACHESTORE_APC')) {
            $this->markTestSkipped('Could not test cachestore_apc. Requirements are not met.');
        }

        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_apc', 'phpunit_test');
        $instance = cachestore_apc::initialise_unit_test_instance($definition);

        // Test a simple purge return.
        $this->assertTrue($instance->purge());

        // Test purge works.
        $this->assertTrue($instance->set('test', 'monster'));
        $this->assertSame('monster', $instance->get('test'));
        $this->assertTrue($instance->purge());
        $this->assertFalse($instance->get('test'));
    }

    /**
     * Test that the Moodle APC store doesn't cross paths with other code using APC as well.
     */
    public function test_cross_application_interaction() {
        if (!cachestore_apc::are_requirements_met() || !defined('TEST_CACHESTORE_APC')) {
            $this->markTestSkipped('Could not test cachestore_apc. Requirements are not met.');
        }

        $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cachestore_apc', 'phpunit_test');
        $instance = cachestore_apc::initialise_unit_test_instance($definition);

        // Test purge with custom data.
        $this->assertTrue($instance->set('test', 'monster'));
        $this->assertSame('monster', $instance->get('test'));
        $this->assertTrue(apc_store('test', 'pirate', 180));
        $this->assertSame('monster', $instance->get('test'));
        $this->assertTrue(apc_exists('test'));
        $this->assertSame('pirate', apc_fetch('test'));
        // Purge and check that our data is gone but the the custom data is still there.
        $this->assertTrue($instance->purge());
        $this->assertFalse($instance->get('test'));
        $this->assertTrue(apc_exists('test'));
        $this->assertSame('pirate', apc_fetch('test'));
    }
}
