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

namespace cachestore_apcu;

use core_cache\store;
use core_cache\definition;
use cachestore_apcu;

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/apcu/lib.php');

/**
 * APC unit test class.
 *
 * @package    cachestore_apcu
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends \cachestore_tests {
    /**
     * Returns the apcu class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_apcu';
    }

    public function setUp(): void {
        if (!cachestore_apcu::are_requirements_met()) {
            $this->markTestSkipped('Could not test cachestore_apcu. Requirements are not met.');
        }
        parent::setUp();
    }

    /**
     * Test that the Moodle APCu store doesn't cross paths with other code using APCu as well.
     */
    public function test_cross_application_interaction(): void {
        $definition = definition::load_adhoc(store::MODE_APPLICATION, 'cachestore_apcu', 'phpunit_test');
        $instance = new cachestore_apcu('Test', cachestore_apcu::unit_test_configuration());
        $instance->initialise($definition);

        // Test purge with custom data.
        $this->assertTrue($instance->set('test', 'monster'));
        $this->assertSame('monster', $instance->get('test'));
        $this->assertTrue(apcu_store('test', 'pirate', 180));
        $this->assertSame('monster', $instance->get('test'));
        $this->assertTrue(apcu_exists('test'));
        $this->assertSame('pirate', apcu_fetch('test'));
        // Purge and check that our data is gone but the the custom data is still there.
        $this->assertTrue($instance->purge());
        $this->assertFalse($instance->get('test'));
        $this->assertTrue(apcu_exists('test'));
        $this->assertSame('pirate', apcu_fetch('test'));
    }

    public function test_different_caches_have_different_prefixes(): void {
        $definition = definition::load_adhoc(store::MODE_APPLICATION, 'cachestore_apcu', 'phpunit_test');
        $instance = new cachestore_apcu('Test', cachestore_apcu::unit_test_configuration());
        $instance->initialise($definition);

        $definition2 = definition::load_adhoc(store::MODE_APPLICATION, 'cachestore_apcu', 'phpunit_test2');
        $instance2 = new cachestore_apcu('Test', cachestore_apcu::unit_test_configuration());
        $instance2->initialise($definition2);

        $instance->set('test1', 1);
        $this->assertFalse($instance2->get('test1'));
        $instance2->purge();
        $this->assertSame(1, $instance->get('test1'));
    }
}
