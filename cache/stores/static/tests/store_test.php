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

namespace cachestore_static;

use core_cache\definition;
use core_cache\store;
use cachestore_static;

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
global $CFG;
require_once($CFG->dirroot.'/cache/tests/fixtures/stores.php');
require_once($CFG->dirroot.'/cache/stores/static/lib.php');

/**
 * Static unit test class.
 *
 * @package    cachestore_static
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store_test extends \cachestore_tests {
    /**
     * Returns the static class name
     * @return string
     */
    protected function get_class_name() {
        return 'cachestore_static';
    }

    /**
     * Test the maxsize option.
     */
    public function test_maxsize(): void {
        $defid = 'phpunit/testmaxsize';
        $config = \cache_config_testing::instance();
        $config->phpunit_add_definition($defid, array(
            'mode' => store::MODE_REQUEST,
            'component' => 'phpunit',
            'area' => 'testmaxsize',
            'maxsize' => 3
        ));
        $definition = definition::load($defid, $config->get_definition_by_id($defid));
        $instance = cachestore_static::initialise_test_instance($definition);

        $this->assertTrue($instance->set('key1', 'value1'));
        $this->assertTrue($instance->set('key2', 'value2'));
        $this->assertTrue($instance->set('key3', 'value3'));

        $this->assertTrue($instance->has('key1'));
        $this->assertTrue($instance->has('key2'));
        $this->assertTrue($instance->has('key3'));

        $this->assertTrue($instance->set('key4', 'value4'));
        $this->assertTrue($instance->set('key5', 'value5'));

        $this->assertFalse($instance->has('key1'));
        $this->assertFalse($instance->has('key2'));
        $this->assertTrue($instance->has('key3'));
        $this->assertTrue($instance->has('key4'));
        $this->assertTrue($instance->has('key5'));

        $this->assertFalse($instance->get('key1'));
        $this->assertFalse($instance->get('key2'));
        $this->assertEquals('value3', $instance->get('key3'));
        $this->assertEquals('value4', $instance->get('key4'));
        $this->assertEquals('value5', $instance->get('key5'));

        // Test adding one more.
        $this->assertTrue($instance->set('key6', 'value6'));
        $this->assertFalse($instance->get('key3'));

        // Test reducing and then adding to make sure we don't lost one.
        $this->assertTrue($instance->delete('key6'));
        $this->assertTrue($instance->set('key7', 'value7'));
        $this->assertEquals('value4', $instance->get('key4'));

        // Set the same key three times to make sure it doesn't count overrides.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($instance->set('key8', 'value8'));
        }

        $this->assertEquals('value7', $instance->get('key7'), 'Overrides are incorrectly incrementing size');

        // Test adding many.
        $this->assertEquals(3, $instance->set_many(array(
            array('key' => 'keyA', 'value' => 'valueA'),
            array('key' => 'keyB', 'value' => 'valueB'),
            array('key' => 'keyC', 'value' => 'valueC')
        )));
        $this->assertEquals(array(
            'key4' => false,
            'key5' => false,
            'key6' => false,
            'key7' => false,
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 'valueC'
        ), $instance->get_many(array(
            'key4', 'key5', 'key6', 'key7', 'keyA', 'keyB', 'keyC'
        )));
    }

    /**
     * Simple test to verify igbinary availability and check basic serialization is working ok.
     */
    public function test_igbinary_serializer(): void {
        // Skip if igbinary is not available.
        if (!extension_loaded('igbinary')) {
            $this->markTestSkipped('Cannot test igbinary serializer. Extension missing');
        }
        // Prepare the static instance.
        $defid = 'phpunit/igbinary';
        $config = \cache_config_testing::instance();
        $config->phpunit_add_definition($defid, array(
            'mode' => store::MODE_REQUEST,
            'component' => 'phpunit',
            'area' => 'testigbinary'
        ));
        $definition = definition::load($defid, $config->get_definition_by_id($defid));
        $instance = cachestore_static::initialise_test_instance($definition);
        // Prepare an object.
        $obj = new \stdClass();
        $obj->someint = 9;
        $obj->somestring = '99';
        $obj->somearray = [9 => 999, '99' => '9999'];
        // Serialize and set.
        $objser = igbinary_serialize($obj);
        $instance->set('testigbinary', $objser);
        // Get and unserialize.
        $res = $instance->get('testigbinary');
        $resunser = igbinary_unserialize($res);
        // Check expectations.
        $this->assertSame($objser, $res);     // Ok from cache (ig-serialized, 100% same string).
        $this->assertEquals($obj, $resunser); // Ok ig-unserialized (equal
        $this->assertNotSame($obj, $resunser);// but different objects, obviously).
    }
}
