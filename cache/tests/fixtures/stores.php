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
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() {
        $class = $this->get_class_name();
        if (!class_exists($class) || !$class::are_requirements_met()) {
            $this->markTestSkipped('Could not test '.$class.'. Requirements are not met.');
        }
        parent::setUp();
    }
    /**
     * Run the unit tests for the store.
     */
    public function test_test_instance() {
        $class = $this->get_class_name();

        $modes = $class::get_supported_modes();
        if ($modes & cache_store::MODE_APPLICATION) {
            $definition = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, $class, 'phpunit_test');
            $instance = new $class($class.'_test', $class::unit_test_configuration());

            if (!$instance->is_ready()) {
                $this->markTestSkipped('Could not test '.$class.'. No test instance configured for application caches.');
            } else {
                $instance->initialise($definition);
                $this->run_tests($instance);
            }
        }
        if ($modes & cache_store::MODE_SESSION) {
            $definition = cache_definition::load_adhoc(cache_store::MODE_SESSION, $class, 'phpunit_test');
            $instance = new $class($class.'_test', $class::unit_test_configuration());

            if (!$instance->is_ready()) {
                $this->markTestSkipped('Could not test '.$class.'. No test instance configured for session caches.');
            } else {
                $instance->initialise($definition);
                $this->run_tests($instance);
            }
        }
        if ($modes & cache_store::MODE_REQUEST) {
            $definition = cache_definition::load_adhoc(cache_store::MODE_REQUEST, $class, 'phpunit_test');
            $instance = new $class($class.'_test', $class::unit_test_configuration());

            if (!$instance->is_ready()) {
                $this->markTestSkipped('Could not test '.$class.'. No test instance configured for request caches.');
            } else {
                $instance->initialise($definition);
                $this->run_tests($instance);
            }
        }
    }

    /**
     * Test the store for basic functionality.
     */
    public function run_tests(cache_store $instance) {
        $object = new stdClass;
        $object->data = 1;

        // Test set with a string.
        $this->assertTrue($instance->set('test1', 'test1'));
        $this->assertTrue($instance->set('test2', 'test2'));
        $this->assertTrue($instance->set('test3', '3'));
        $this->assertTrue($instance->set('other3', '3'));

        // Test get with a string.
        $this->assertSame('test1', $instance->get('test1'));
        $this->assertSame('test2', $instance->get('test2'));
        $this->assertSame('3', $instance->get('test3'));

        // Test find and find with prefix if this class implements the searchable interface.
        if ($instance->is_searchable()) {
            // Extra settings here ignore the return order of the array.
            $this->assertEquals(['test3', 'test1', 'test2', 'other3'], $instance->find_all(), '', 0, 1, true);

            // Extra settings here ignore the return order of the array.
            $this->assertEquals(['test2', 'test1', 'test3'], $instance->find_by_prefix('test'), '', 0, 1, true);
            $this->assertEquals(['test2'], $instance->find_by_prefix('test2'));
            $this->assertEquals(['other3'], $instance->find_by_prefix('other'));
            $this->assertEquals([], $instance->find_by_prefix('nothere'));
        }

        // Test set with an int.
        $this->assertTrue($instance->set('test1', 1));
        $this->assertTrue($instance->set('test2', 2));

        // Test get with an int.
        $this->assertSame(1, $instance->get('test1'));
        $this->assertInternalType('int', $instance->get('test1'));
        $this->assertSame(2, $instance->get('test2'));
        $this->assertInternalType('int', $instance->get('test2'));

        // Test set with a bool.
        $this->assertTrue($instance->set('test1', true));

        // Test get with an bool.
        $this->assertSame(true, $instance->get('test1'));
        $this->assertInternalType('boolean', $instance->get('test1'));

        // Test with an object.
        $this->assertTrue($instance->set('obj', $object));
        if ($instance::get_supported_features() & cache_store::DEREFERENCES_OBJECTS) {
            $this->assertNotSame($object, $instance->get('obj'), 'Objects must be dereferenced when returned.');
        }
        $this->assertEquals($object, $instance->get('obj'));

        // Test delete.
        $this->assertTrue($instance->delete('test1'));
        $this->assertTrue($instance->delete('test3'));
        $this->assertFalse($instance->delete('test3'));
        $this->assertFalse($instance->get('test1'));
        $this->assertSame(2, $instance->get('test2'));
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
        $this->assertSame(5, $outcome);
        $this->assertSame('many1', $instance->get('many1'));
        $this->assertSame('many5', $instance->get('many5'));
        $this->assertFalse($instance->get('many6'));

        // Test get_many.
        $result = $instance->get_many(array('many1', 'many3', 'many5', 'many6'));
        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);
        $this->assertSame(array(
            'many1' => 'many1',
            'many3' => 'many3',
            'many5' => 'many5',
            'many6' => false,
        ), $result);

        // Test delete_many.
        $this->assertSame(3, $instance->delete_many(array('many2', 'many3', 'many4')));
        $this->assertSame(2, $instance->delete_many(array('many1', 'many5', 'many6')));
    }
}