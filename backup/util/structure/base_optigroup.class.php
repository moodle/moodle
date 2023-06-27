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
 * @package    moodlecore
 * @subpackage backup-structure
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * TODO: Finish phpdocs
 */

/**
 * Abstract class representing one optigroup for conditional branching
 */
abstract class base_optigroup extends base_nested_element {

    /** @var boolean flag indicating if multiple branches can be processed (true) or no (false) */
    private $multiple;

    /**
     * Constructor - instantiates one base_optigroup, specifying its basic info
     *
     * @param string $name name of the element
     * @param array $elements base_optigroup_elements of this group
     * @param bool $multiple to decide if the group allows multiple branches processing or no
     */
    public function __construct($name, $elements = null, $multiple = false) {
        parent::__construct($name);
        $this->multiple = $multiple;
        if (!empty($elements)) {
            $this->add_children($elements);
        }
    }

// Public API starts here

    /**
     * Return the level of this element, that will be, the level of the parent (doesn't consume level)
     * (note this os only a "cosmetic" effect (to_string) as fact as the real responsible for this
     * is the corresponding structure_processor for the final output.
     */
    public function get_level() {
        return $this->get_parent() == null ? 1 : $this->get_parent()->get_level();
    }

    public function to_string($showvalue = false) {
        $indent = str_repeat('    ', $this->get_level()); // Indent output based in level (4cc)
        $output = $indent . '!' . $this->get_name() . ' (level: ' . $this->get_level() . ')';
        $children = $this->get_children();
        if (!empty($children)) {
            foreach ($this->get_children() as $child) {
                $output .= PHP_EOL . $child->to_string($showvalue);
            }
        }
        return $output;
    }

// Forbidden API starts here

    /**
     * Adding attributes is forbidden
     */
    public function add_attributes($attributes) {
        throw new base_element_struct_exception('optigroup_not_attributes');
    }

    /**
     * Instantiating attributes is forbidden
     */
    protected function get_new_attribute($name) {
        throw new base_element_struct_exception('optigroup_not_attributes');
    }

    /**
     * Adding final elements is forbidden
     */
    public function add_final_elements($attributes) {
        throw new base_element_struct_exception('optigroup_not_final_elements');
    }

    /**
     * Instantiating final elements is forbidden
     */
    protected function get_new_final_element($name) {
        throw new base_element_struct_exception('optigroup_not_final_elements');
    }

// Protected API starts here

    protected function add_children($elements) {
        if ($elements instanceof base_nested_element) { // Accept 1 element, object
            $elements = array($elements);
        }
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $this->add_child($element);
            }
        } else {
            throw new base_optigroup_exception('optigroup_elements_incorrect');
        }
    }

    /**
     * Set the parent of the optigroup and, at the same time, process all the
     * condition params in all the childs
     */
    protected function set_parent($element) {
        parent::set_parent($element);
        // Force condition param calculation in all children
        foreach ($this->get_children() as $child) {
            $child->set_condition($child->get_condition_param(), $child->get_condition_value());
        }
    }

    /**
     * Recalculate all the used elements in the optigroup, observing
     * restrictions and passing the new used to outer level
     */
    protected function add_used($element) {
        $newused = array();
        // Iterate over all the element useds, filling $newused and
        // observing the multiple setting
        foreach ($element->get_used() as $used) {
            if (!in_array($used, $this->get_used())) { // it's a new one, add to $newused array
                $newused[] = $used;
                $this->set_used(array_merge($this->get_used(), array($used))); // add to the optigroup used array
            } else { // it's an existing one, exception on multiple optigroups
                if ($this->multiple) {
                    throw new base_optigroup_exception('multiple_optigroup_duplicate_element', $used);
                }
            }
        }
        // Finally, inform about newused to the next grand(parent/optigroupelement)
        if ($newused && $this->get_parent()) {
            $element->set_used($newused); // Only about the newused
            $grandparent = $this->get_grandoptigroupelement_or_grandparent();
            $grandparent->check_and_set_used($element);
        }
    }

    protected function is_multiple() {
        return $this->multiple;
    }
}

/**
 * base_optigroup_exception to control all the errors while building the optigroups
 *
 * This exception will be thrown each time the base_optigroup class detects some
 * inconsistency related with the building of the group
 */
class base_optigroup_exception extends base_atom_exception {

    /**
     * Constructor - instantiates one base_optigroup_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
