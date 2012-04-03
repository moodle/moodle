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
 * @package   core_backup
 * @category  phpunit
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
require_once(__DIR__.'/fixtures/structure_fixtures.php');


/**
 * Unit test case the base_optigroup class. Note: highly imbricated with nested/final base elements
 */
class backup_base_optigroup_testcase extends basic_testcase {

    /**
     * Correct creation tests (s)
     */
    function test_creation() {
        $instance = new mock_base_optigroup('optigroup', null, true);
        $this->assertInstanceOf('base_optigroup', $instance);
        $this->assertEquals($instance->get_name(), 'optigroup');
        $this->assertNull($instance->get_parent());
        $this->assertEquals($instance->get_children(), array());
        $this->assertEquals($instance->get_level(), 1);
        $this->assertTrue($instance->is_multiple());

        // Get to_string() results (with values)
        $child1 = new mock_base_nested_element('child1', null, new mock_base_final_element('four'));
        $child2 = new mock_base_nested_element('child2', null, new mock_base_final_element('five'));
        $instance->add_child($child1);
        $instance->add_child($child2);
        $children = $instance->get_children();
        $final_elements = $children['child1']->get_final_elements();
        $final_elements['four']->set_value('final4value');
        $final_elements['four']->add_attributes('attr4');
        $grandchild = new mock_base_nested_element('grandchild', new mock_base_attribute('attr5'));
        $child2->add_child($grandchild);
        $attrs = $grandchild->get_attributes();
        $attrs['attr5']->set_value('attr5value');
        $tostring = $instance->to_string(true);
        $this->assertTrue(strpos($tostring, '!optigroup (level: 1)') !== false);
        $this->assertTrue(strpos($tostring, '?child2 (level: 2) =>') !== false);
        $this->assertTrue(strpos($tostring, ' => ') !== false);
        $this->assertTrue(strpos($tostring, '#four (level: 3) => final4value') !== false);
        $this->assertTrue(strpos($tostring, '@attr5 => attr5value') !== false);
        $this->assertTrue(strpos($tostring, '#five (level: 3) => not set') !== false);
    }

    /**
     * Incorrect creation tests (attributes and final elements)
     */
    function itest_wrong_creation() {

        // Create instance with invalid name
        try {
            $instance = new mock_base_nested_element('');
            $this->fail("Expecting base_atom_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_atom_struct_exception);
        }

        // Create instance with incorrect (object) final element
        try {
            $obj = new stdClass;
            $obj->name = 'test_attr';
            $instance = new mock_base_nested_element('TEST', null, $obj);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Create instance with array containing incorrect (object) final element
        try {
            $obj = new stdClass;
            $obj->name = 'test_attr';
            $instance = new mock_base_nested_element('TEST', null, array($obj));
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Create instance with array containing duplicate final elements
        try {
            $instance = new mock_base_nested_element('TEST', null, array('VAL1', 'VAL2', 'VAL1'));
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Try to get value of base_nested_element
        $instance = new mock_base_nested_element('TEST');
        try {
            $instance->get_value();
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Try to set value of base_nested_element
        $instance = new mock_base_nested_element('TEST');
        try {
            $instance->set_value('some_value');
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Try to clean one value of base_nested_element
        $instance = new mock_base_nested_element('TEST');
        try {
            $instance->clean_value('some_value');
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }
    }
}
