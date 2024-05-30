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

namespace core_backup;

use base_atom_struct_exception;
use base_element_parent_exception;
use base_element_struct_exception;
use mock_base_attribute;
use mock_base_final_element;
use mock_base_nested_element;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
require_once(__DIR__.'/fixtures/structure_fixtures.php');


/**
 * Unit test case the base_nested_element class.
 *
 * Note: highly imbricated with base_final_element class
 *
 * @package   core_backup
 * @category  test
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class basenestedelement_test extends \basic_testcase {

    /**
     * Correct creation tests (attributes and final elements)
     */
    public function test_creation(): void {
        // Create instance with name, attributes and values and check all them
        $instance = new mock_base_nested_element('NAME', array('ATTR1', 'ATTR2'), array('VAL1', 'VAL2', 'VAL3'));
        $this->assertInstanceOf('base_nested_element', $instance);
        $this->assertEquals($instance->get_name(), 'NAME');
        $attrs = $instance->get_attributes();
        $this->assertTrue(is_array($attrs));
        $this->assertEquals(count($attrs), 2);
        $this->assertInstanceOf('base_attribute', $attrs['ATTR1']);
        $this->assertEquals($attrs['ATTR1']->get_name(), 'ATTR1');
        $this->assertNull($attrs['ATTR1']->get_value());
        $this->assertEquals($attrs['ATTR2']->get_name(), 'ATTR2');
        $this->assertNull($attrs['ATTR2']->get_value());
        $finals = $instance->get_final_elements();
        $this->assertTrue(is_array($finals));
        $this->assertEquals(count($finals), 3);
        $this->assertInstanceOf('base_final_element', $finals['VAL1']);
        $this->assertEquals($finals['VAL1']->get_name(), 'VAL1');
        $this->assertNull($finals['VAL1']->get_value());
        $this->assertEquals($finals['VAL1']->get_level(), 2);
        $this->assertInstanceOf('base_nested_element', $finals['VAL1']->get_parent());
        $this->assertEquals($finals['VAL2']->get_name(), 'VAL2');
        $this->assertNull($finals['VAL2']->get_value());
        $this->assertEquals($finals['VAL2']->get_level(), 2);
        $this->assertInstanceOf('base_nested_element', $finals['VAL1']->get_parent());
        $this->assertEquals($finals['VAL3']->get_name(), 'VAL3');
        $this->assertNull($finals['VAL3']->get_value());
        $this->assertEquals($finals['VAL3']->get_level(), 2);
        $this->assertInstanceOf('base_nested_element', $finals['VAL1']->get_parent());
        $this->assertNull($instance->get_parent());
        $this->assertEquals($instance->get_children(), array());
        $this->assertEquals($instance->get_level(), 1);

        // Create instance with name only
        $instance = new mock_base_nested_element('NAME');
        $this->assertInstanceOf('base_nested_element', $instance);
        $this->assertEquals($instance->get_name(), 'NAME');
        $this->assertEquals($instance->get_attributes(), array());
        $this->assertEquals($instance->get_final_elements(), array());
        $this->assertNull($instance->get_parent());
        $this->assertEquals($instance->get_children(), array());
        $this->assertEquals($instance->get_level(), 1);

        // Add some attributes
        $instance->add_attributes(array('ATTR1', 'ATTR2'));
        $attrs = $instance->get_attributes();
        $this->assertTrue(is_array($attrs));
        $this->assertEquals(count($attrs), 2);
        $this->assertEquals($attrs['ATTR1']->get_name(), 'ATTR1');
        $this->assertNull($attrs['ATTR1']->get_value());
        $this->assertEquals($attrs['ATTR2']->get_name(), 'ATTR2');
        $this->assertNull($attrs['ATTR2']->get_value());

        // And some more atributes
        $instance->add_attributes(array('ATTR3', 'ATTR4'));
        $attrs = $instance->get_attributes();
        $this->assertTrue(is_array($attrs));
        $this->assertEquals(count($attrs), 4);
        $this->assertEquals($attrs['ATTR1']->get_name(), 'ATTR1');
        $this->assertNull($attrs['ATTR1']->get_value());
        $this->assertEquals($attrs['ATTR2']->get_name(), 'ATTR2');
        $this->assertNull($attrs['ATTR2']->get_value());
        $this->assertEquals($attrs['ATTR3']->get_name(), 'ATTR3');
        $this->assertNull($attrs['ATTR3']->get_value());
        $this->assertEquals($attrs['ATTR4']->get_name(), 'ATTR4');
        $this->assertNull($attrs['ATTR4']->get_value());

        // Add some final elements
        $instance->add_final_elements(array('VAL1', 'VAL2', 'VAL3'));
        $finals = $instance->get_final_elements();
        $this->assertTrue(is_array($finals));
        $this->assertEquals(count($finals), 3);
        $this->assertEquals($finals['VAL1']->get_name(), 'VAL1');
        $this->assertNull($finals['VAL1']->get_value());
        $this->assertEquals($finals['VAL2']->get_name(), 'VAL2');
        $this->assertNull($finals['VAL2']->get_value());
        $this->assertEquals($finals['VAL3']->get_name(), 'VAL3');
        $this->assertNull($finals['VAL3']->get_value());

        // Add some more final elements
        $instance->add_final_elements('VAL4');
        $finals = $instance->get_final_elements();
        $this->assertTrue(is_array($finals));
        $this->assertEquals(count($finals), 4);
        $this->assertEquals($finals['VAL1']->get_name(), 'VAL1');
        $this->assertNull($finals['VAL1']->get_value());
        $this->assertEquals($finals['VAL2']->get_name(), 'VAL2');
        $this->assertNull($finals['VAL2']->get_value());
        $this->assertEquals($finals['VAL3']->get_name(), 'VAL3');
        $this->assertNull($finals['VAL3']->get_value());
        $this->assertEquals($finals['VAL4']->get_name(), 'VAL4');
        $this->assertNull($finals['VAL4']->get_value());

        // Get to_string() results (with values)
        $instance = new mock_base_nested_element('PARENT', array('ATTR1', 'ATTR2'), array('FINAL1', 'FINAL2', 'FINAL3'));
        $child1 = new mock_base_nested_element('CHILD1', null, new mock_base_final_element('FINAL4'));
        $child2 = new mock_base_nested_element('CHILD2', null, new mock_base_final_element('FINAL5'));
        $instance->add_child($child1);
        $instance->add_child($child2);
        $children = $instance->get_children();
        $final_elements = $children['CHILD1']->get_final_elements();
        $final_elements['FINAL4']->set_value('final4value');
        $final_elements['FINAL4']->add_attributes('ATTR4');
        $grandchild = new mock_base_nested_element('GRANDCHILD', new mock_base_attribute('ATTR5'));
        $child2->add_child($grandchild);
        $attrs = $grandchild->get_attributes();
        $attrs['ATTR5']->set_value('attr5value');
        $tostring = $instance->to_string(true);
        $this->assertTrue(strpos($tostring, 'PARENT (level: 1)') !== false);
        $this->assertTrue(strpos($tostring, ' => ') !== false);
        $this->assertTrue(strpos($tostring, '#FINAL4 (level: 3) => final4value') !== false);
        $this->assertTrue(strpos($tostring, '@ATTR5 => attr5value') !== false);
        $this->assertTrue(strpos($tostring, '#FINAL5 (level: 3) => not set') !== false);

        // Clean values
        $instance = new mock_base_nested_element('PARENT', array('ATTR1', 'ATTR2'), array('FINAL1', 'FINAL2', 'FINAL3'));
        $child1 = new mock_base_nested_element('CHILD1', null, new mock_base_final_element('FINAL4'));
        $child2 = new mock_base_nested_element('CHILD2', null, new mock_base_final_element('FINAL4'));
        $instance->add_child($child1);
        $instance->add_child($child2);
        $children = $instance->get_children();
        $final_elements = $children['CHILD1']->get_final_elements();
        $final_elements['FINAL4']->set_value('final4value');
        $final_elements['FINAL4']->add_attributes('ATTR4');
        $grandchild = new mock_base_nested_element('GRANDCHILD', new mock_base_attribute('ATTR4'));
        $child2->add_child($grandchild);
        $attrs = $grandchild->get_attributes();
        $attrs['ATTR4']->set_value('attr4value');
        $this->assertEquals($final_elements['FINAL4']->get_value(), 'final4value');
        $this->assertEquals($attrs['ATTR4']->get_value(), 'attr4value');
        $instance->clean_values();
        $this->assertNull($final_elements['FINAL4']->get_value());
        $this->assertNull($attrs['ATTR4']->get_value());
    }

    /**
     * Incorrect creation tests (attributes and final elements)
     */
    function test_wrong_creation(): void {

        // Create instance with invalid name
        try {
            $instance = new mock_base_nested_element('');
            $this->fail("Expecting base_atom_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_atom_struct_exception);
        }

        // Create instance with incorrect (object) final element
        try {
            $obj = new \stdClass;
            $obj->name = 'test_attr';
            $instance = new mock_base_nested_element('TEST', null, $obj);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Create instance with array containing incorrect (object) final element
        try {
            $obj = new \stdClass;
            $obj->name = 'test_attr';
            $instance = new mock_base_nested_element('TEST', null, array($obj));
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Create instance with array containing duplicate final elements
        try {
            $instance = new mock_base_nested_element('TEST', null, array('VAL1', 'VAL2', 'VAL1'));
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Try to get value of base_nested_element
        $instance = new mock_base_nested_element('TEST');
        try {
            $instance->get_value();
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Try to set value of base_nested_element
        $instance = new mock_base_nested_element('TEST');
        try {
            $instance->set_value('some_value');
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Try to clean one value of base_nested_element
        $instance = new mock_base_nested_element('TEST');
        try {
            $instance->clean_value('some_value');
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }
    }

    /**
     * Correct tree tests (children stuff)
     */
    function test_tree(): void {

        // Create parent and child instances, tree-ing them
        $parent = new mock_base_nested_element('PARENT');
        $child = new mock_base_nested_element('CHILD');
        $parent->add_child($child);
        $this->assertEquals($parent->get_children(), array('CHILD' => $child));
        $this->assertEquals($child->get_parent(), $parent);
        $check_children = $parent->get_children();
        $check_child = $check_children['CHILD'];
        $check_parent = $check_child->get_parent();
        $this->assertEquals($check_child->get_name(), 'CHILD');
        $this->assertEquals($check_parent->get_name(), 'PARENT');
        $this->assertEquals($check_child->get_level(), 2);
        $this->assertEquals($check_parent->get_level(), 1);
        $this->assertEquals($check_parent->get_children(), array('CHILD' => $child));
        $this->assertEquals($check_child->get_parent(), $parent);

        // Add parent to grandparent
        $grandparent = new mock_base_nested_element('GRANDPARENT');
        $grandparent->add_child($parent);
        $this->assertEquals($grandparent->get_children(), array('PARENT' => $parent));
        $this->assertEquals($parent->get_parent(), $grandparent);
        $this->assertEquals($parent->get_children(), array('CHILD' => $child));
        $this->assertEquals($child->get_parent(), $parent);
        $this->assertEquals($child->get_level(), 3);
        $this->assertEquals($parent->get_level(), 2);
        $this->assertEquals($grandparent->get_level(), 1);

        // Add grandchild to child
        $grandchild = new mock_base_nested_element('GRANDCHILD');
        $child->add_child($grandchild);
        $this->assertEquals($child->get_children(), array('GRANDCHILD' => $grandchild));
        $this->assertEquals($grandchild->get_parent(), $child);
        $this->assertEquals($grandchild->get_level(), 4);
        $this->assertEquals($child->get_level(), 3);
        $this->assertEquals($parent->get_level(), 2);
        $this->assertEquals($grandparent->get_level(), 1);

        // Add another child to parent
        $child2 = new mock_base_nested_element('CHILD2');
        $parent->add_child($child2);
        $this->assertEquals($parent->get_children(), array('CHILD' => $child, 'CHILD2' => $child2));
        $this->assertEquals($child2->get_parent(), $parent);
        $this->assertEquals($grandchild->get_level(), 4);
        $this->assertEquals($child->get_level(), 3);
        $this->assertEquals($child2->get_level(), 3);
        $this->assertEquals($parent->get_level(), 2);
        $this->assertEquals($grandparent->get_level(), 1);
    }

    /**
     * Incorrect tree tests (children stuff)
     */
    function test_wrong_tree(): void {

        // Add null object child
        $parent = new mock_base_nested_element('PARENT');
        $child = null;
        try {
            $parent->add_child($child);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Add non base_element object child
        $parent = new mock_base_nested_element('PARENT');
        $child = new \stdClass();
        try {
            $parent->add_child($child);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Add existing element (being parent)
        $parent = new mock_base_nested_element('PARENT');
        $child = new mock_base_nested_element('PARENT');
        try {
            $parent->add_child($child);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Add existing element (being grandparent)
        $grandparent = new mock_base_nested_element('GRANDPARENT');
        $parent = new mock_base_nested_element('PARENT');
        $child = new mock_base_nested_element('GRANDPARENT');
        $grandparent->add_child($parent);
        try {
            $parent->add_child($child);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Add existing element (being grandchild)
        $grandparent = new mock_base_nested_element('GRANDPARENT');
        $parent = new mock_base_nested_element('PARENT');
        $child = new mock_base_nested_element('GRANDPARENT');
        $parent->add_child($child);
        try {
            $grandparent->add_child($parent);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Add existing element (being cousin)
        $grandparent = new mock_base_nested_element('GRANDPARENT');
        $parent1 = new mock_base_nested_element('PARENT1');
        $parent2 = new mock_base_nested_element('PARENT2');
        $child1 = new mock_base_nested_element('CHILD1');
        $child2 = new mock_base_nested_element('CHILD1');
        $grandparent->add_child($parent1);
        $parent1->add_child($child1);
        $parent2->add_child($child2);
        try {
            $grandparent->add_child($parent2);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
        }

        // Add element to two parents
        $parent1 = new mock_base_nested_element('PARENT1');
        $parent2 = new mock_base_nested_element('PARENT2');
        $child = new mock_base_nested_element('CHILD');
        $parent1->add_child($child);
        try {
            $parent2->add_child($child);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_parent_exception);
        }

        // Add child element already used by own final elements
        $nested = new mock_base_nested_element('PARENT1', null, array('FINAL1', 'FINAL2'));
        $child = new mock_base_nested_element('FINAL2', null, array('FINAL3', 'FINAL4'));
        try {
            $nested->add_child($child);
            $this->fail("Expecting base_element_struct_exception exception, none occurred");
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'baseelementchildnameconflict');
            $this->assertEquals($e->a, 'FINAL2');
        }
    }
}
