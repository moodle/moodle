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
 * Cache store test fixtures.
 *
 * @package    core
 * @category   cache
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * An abstract class to make writing unit tests for cache stores very easy.
 *
 * @package    core
 * @category   cache
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class cachestore_tests extends advanced_testcase {

    /**
     * Returns the class name for the store.
     *
     * @return string
     */
    abstract protected function get_class_name();

    /**
     * Run the unit tests for the store.
     */
    public function test_test_instance() {
        $class = $this->get_class_name();
        if (!class_exists($class) || !method_exists($class, 'initialise_test_instance') || !$class::are_requirements_met()) {
            $this->markTestSkipped('Could not test '.$class.'. Requirements are not met.');
        }

        $modes = $class::get_supported_modes();
        if ($modes & cache_store::MODE_APPLICATION) {
            $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, $class, 'phpunit_test');
            $instance = $class::initialise_test_instance($definition);
            if (!$instance) {
                $this->markTestSkipped('Could not test '.$class.'. No test instance configured for application caches.');
            } else {
                $this->run_tests($instance);
            }
        }
        if ($modes & cache_store::MODE_SESSION) {
            $definition = cache_definition::load_adhoc(cache_store::MODE_SESSION, $class, 'phpunit_test');
            $instance = $class::initialise_test_instance($definition);
            if (!$instance) {
                $this->markTestSkipped('Could not test '.$class.'. No test instance configured for session caches.');
            } else {
                $this->run_tests($instance);
            }
        }
        if ($modes & cache_store::MODE_REQUEST) {
            $definition = cache_definition::load_adhoc(cache_store::MODE_REQUEST, $class, 'phpunit_test');
            $instance = $class::initialise_test_instance($definition);
            if (!$instance) {
                $this->markTestSkipped('Could not test '.$class.'. No test instance configured for request caches.');
            } else {
                $this->run_tests($instance);
            }
        }
    }

    /**
     * Test the store for basic functionality.
     */
    public function run_tests(cache_store $instance) {

        // Test set.
        $this->assertTrue($instance->set('test1', 'test1'));
        $this->assertTrue($instance->set('test2', 'test2'));

        // Test get.
        $this->assertEquals('test1', $instance->get('test1'));
        $this->assertEquals('test2', $instance->get('test2'));

        // Test delete.
        $this->assertTrue($instance->delete('test1'));
        $this->assertFalse($instance->delete('test3'));
        $this->assertFalse($instance->get('test1'));
        $this->assertEquals('test2', $instance->get('test2'));
        $this->assertTrue($instance->set('test1', 'test1'));

        // Test purge.
        $this->assertTrue($instance->purge());
        $this->assertFalse($instance->get('test1'));
        $this->assertFalse($instance->get('test2'));

        // Test set_many.
        $outcome = $instance->set_many(array(
            array('key' => 'many1', 'value' => 'many1'),
            array('key' => 'many2', 'value' => 'many2'),
            array('key' => 'many3', 'value' => 'many3'),
            array('key' => 'many4', 'value' => 'many4'),
            array('key' => 'many5', 'value' => 'many5')
        ));
        $this->assertEquals(5, $outcome);
        $this->assertEquals('many1', $instance->get('many1'));
        $this->assertEquals('many5', $instance->get('many5'));
        $this->assertFalse($instance->get('many6'));

        // Test get_many.
        $result = $instance->get_many(array('many1', 'many3', 'many5', 'many6'));
        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);
        $this->assertEquals(array(
            'many1' => 'many1',
            'many3' => 'many3',
            'many5' => 'many5',
            'many6' => false,
        ), $result);

        // Test delete_many.
        $this->assertEquals(3, $instance->delete_many(array('many2', 'many3', 'many4')));
        $this->assertEquals(2, $instance->delete_many(array('many1', 'many5', 'many6')));
    }
}