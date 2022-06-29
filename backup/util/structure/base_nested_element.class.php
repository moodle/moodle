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
 * Abstract class representing one nestable element (non final) piece of information
 */
abstract class base_nested_element extends base_final_element {

    /** @var array final elements of the element (maps to XML final elements of the tag) */
    private $final_elements;

    /** @var array children base_elements of this element (describes structure of the XML file) */
    private $children;

    /** @var base_optigroup optional group of this element (branches to be processed conditionally) */
    private $optigroup;

    /** @var array elements already used by the base_element, to avoid circular references */
    private $used;

    /**
     * Constructor - instantiates one base_nested_element, specifying its basic info.
     *
     * @param string $name name of the element
     * @param array  $attributes attributes this element will handle (optional, defaults to null)
     * @param array  $final_elements this element will handle (optional, defaults to null)
     */
    public function __construct($name, $attributes = null, $final_elements = null) {
        parent::__construct($name, $attributes);
        $this->final_elements = array();
        if (!empty($final_elements)) {
            $this->add_final_elements($final_elements);
        }
        $this->children = array();
        $this->optigroup = null;
        $this->used[] = $name;
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // Before reseting anything, call destroy recursively
        foreach ($this->children as $child) {
            $child->destroy();
        }
        foreach ($this->final_elements as $element) {
            $element->destroy();
        }
        if ($this->optigroup) {
            $this->optigroup->destroy();
        }
        // Everything has been destroyed recursively, now we can reset safely
        $this->children = array();
        $this->final_elements = array();
        $this->optigroup = null;
        // Delegate to parent to destroy other bits
        parent::destroy();
    }

    protected function get_used() {
        return $this->used;
    }

    protected function set_used($used) {
        $this->used = $used;
    }

    protected function add_used($element) {
        $this->used = array_merge($this->used, $element->get_used());
    }

    protected function check_and_set_used($element) {
        // First of all, check the element being added doesn't conflict with own final elements
        if (array_key_exists($element->get_name(), $this->final_elements)) {
            throw new base_element_struct_exception('baseelementchildnameconflict', $element->get_name());
        }
        $grandparent = $this->get_grandoptigroupelement_or_grandparent();
        if ($existing = array_intersect($grandparent->get_used(), $element->get_used())) { // Check the element isn't being used already
            throw new base_element_struct_exception('baseelementexisting', implode($existing));
        }
        $grandparent->add_used($element);
        // If the parent is one optigroup, add the element useds to it too
        if ($grandparent->get_parent() instanceof base_optigroup) {
            $grandparent->get_parent()->add_used($element);
        }

    }

/// Public API starts here

    public function get_final_elements() {
        return $this->final_elements;
    }

    public function get_final_element($name) {
        if (array_key_exists($name, $this->final_elements)) {
            return $this->final_elements[$name];
        } else {
            return null;
        }
    }

    public function get_children() {
        return $this->children;
    }

    public function get_child($name) {
        if (array_key_exists($name, $this->children)) {
            return $this->children[$name];
        } else {
            return null;
        }
    }

    public function get_optigroup() {
        return $this->optigroup;
    }

    public function add_final_elements($final_elements) {
        if ($final_elements instanceof base_final_element || is_string($final_elements)) { // Accept 1 final_element, object or string
            $final_elements = array($final_elements);
        }
        if (is_array($final_elements)) {
            foreach ($final_elements as $final_element) {
                if (is_string($final_element)) { // Accept string final_elements
                    $final_element = $this->get_new_final_element($final_element);
                }
                if (!($final_element instanceof base_final_element)) {
                    throw new base_element_struct_exception('baseelementnofinalelement', get_class($final_element));
                }
                if (array_key_exists($final_element->get_name(), $this->final_elements)) {
                    throw new base_element_struct_exception('baseelementexists', $final_element->get_name());
                }
                $this->final_elements[$final_element->get_name()] = $final_element;
                $final_element->set_parent($this);
            }
        } else {
            throw new base_element_struct_exception('baseelementincorrect');
        }
    }

    public function add_child($element) {
        if (!is_object($element) || !($element instanceof base_nested_element)) { // parameter must be a base_nested_element
            if (!is_object($element) || !($found = get_class($element))) {
                $found = 'non object';
            }
            throw new base_element_struct_exception('nestedelementincorrect', $found);
        }
        $this->check_and_set_used($element);
        $this->children[$element->get_name()] = $element;
        $element->set_parent($this);
    }

    public function add_optigroup($optigroup) {
        if (!($optigroup instanceof base_optigroup)) { // parameter must be a base_optigroup
            if (!$found = get_class($optigroup)) {
                $found = 'non object';
            }
            throw new base_element_struct_exception('optigroupincorrect', $found);
        }
        if ($this->optigroup !== null) {
            throw new base_element_struct_exception('optigroupalreadyset', $found);
        }
        $this->check_and_set_used($optigroup);
        $this->optigroup = $optigroup;
        $optigroup->set_parent($this);
    }

    public function get_value() {
        throw new base_element_struct_exception('nestedelementnotvalue');
    }

    public function set_value($value) {
        throw new base_element_struct_exception('nestedelementnotvalue');
    }

    public function clean_value() {
        throw new base_element_struct_exception('nestedelementnotvalue');
    }

    public function clean_values() {
        parent::clean_values();
        if (!empty($this->final_elements)) {
            foreach ($this->final_elements as $final_element) {
                $final_element->clean_values();
            }
        }
        if (!empty($this->children)) {
            foreach ($this->children as $child) {
                $child->clean_values();
            }
        }
        if (!empty($this->optigroup)) {
            $this->optigroup->clean_values();
        }
    }

    public function to_string($showvalue = false) {
        $output = parent::to_string($showvalue);
        if (!empty($this->final_elements)) {
            foreach ($this->final_elements as $final_element) {
                $output .= PHP_EOL . $final_element->to_string($showvalue);
            }
        }
        if (!empty($this->children)) {
            foreach ($this->children as $child) {
                $output .= PHP_EOL . $child->to_string($showvalue);
            }
        }
        if (!empty($this->optigroup)) {
            $output .= PHP_EOL . $this->optigroup->to_string($showvalue);
        }
        return $output;
    }

// Implementable API

    /**
     * Returns one instace of the @final_element class to work with
     * when final_elements are added simply by name
     */
    abstract protected function get_new_final_element($name);
}

/**
 * base_element exception to control all the errors while building the nested tree
 *
 * This exception will be thrown each time the base_element class detects some
 * inconsistency related with the building of the nested tree representing one base part
 * (invalid objects, circular references, double parents...)
 */
class base_element_struct_exception extends base_atom_exception {

    /**
     * Constructor - instantiates one base_element_struct_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
