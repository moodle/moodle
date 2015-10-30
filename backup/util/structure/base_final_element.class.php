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
 * Abstract class representing one final element atom (name/value/parent) piece of information
 */
abstract class base_final_element extends base_atom {

    /** @var array base_attributes of the element (maps to XML attributes of the tag) */
        private $attributes;

    /** @var base_nested_element parent of this element (describes structure of the XML file) */
        private $parent;

    /**
     * Constructor - instantiates one base_final_element, specifying its basic info.
     *
     * @param string $name name of the element
     * @param array  $attributes attributes this element will handle (optional, defaults to null)
     */
    public function __construct($name, $attributes = null) {
        parent::__construct($name);
        $this->attributes = array();
        if (!empty($attributes)) {
            $this->add_attributes($attributes);
        }
        $this->parent = null;
    }

    /**
     * Destroy all circular references. It helps PHP 5.2 a lot!
     */
    public function destroy() {
        // No need to destroy anything recursively here, direct reset
        $this->attributes = array();
        $this->parent = null;
    }

    protected function set_parent($element) {
        if ($this->parent) {
            $info = new stdClass();
            $info->currparent= $this->parent->get_name();
            $info->newparent = $element->get_name();
            $info->element   = $this->get_name();
            throw new base_element_parent_exception('baseelementhasparent', $info);
        }
        $this->parent = $element;
    }

    protected function get_grandparent() {
        $parent = $this->parent;
        if ($parent instanceof base_nested_element) {
            return $parent->get_grandparent();
        } else {
            return $this;
        }
    }

    protected function get_grandoptigroupelement_or_grandparent() {
        $parent = $this->parent;
        if ($parent instanceof base_optigroup) {
            return $this; // Have found one parent optigroup, so I (first child of optigroup) am
        } else if ($parent instanceof base_nested_element) {
            return $parent->get_grandoptigroupelement_or_grandparent(); // Continue searching
        } else {
            return $this;
        }
    }

    protected function find_element_by_path($path) {
        $patharr = explode('/', trim($path, '/')); // Split the path trimming slashes
        if (substr($path, 0, 1) == '/') { // Absolute path, go to grandparent and process
            if (!$this->get_grandparent() instanceof base_nested_element) {
                throw new base_element_struct_exception('baseelementincorrectgrandparent', $patharr[0]);
            } else if ($this->get_grandparent()->get_name() !== $patharr[0]) {
                throw new base_element_struct_exception('baseelementincorrectgrandparent', $patharr[0]);
            } else {
                $newpath = implode('/', array_slice($patharr, 1)); // Take out 1st element
                return $this->get_grandparent()->find_element_by_path($newpath); // Process as relative in grandparent
            }
        } else {
            if ($patharr[0] == '..') { // Go to parent
                if (!$this->get_parent() instanceof base_nested_element) {
                    throw new base_element_struct_exception('baseelementincorrectparent', $patharr[0]);
                } else {
                    $newpath = implode('/', array_slice($patharr, 1)); // Take out 1st element
                    return $this->get_parent()->find_element_by_path($newpath); // Process as relative in parent
                }
            } else if (count($patharr) > 1) { // Go to next child
                if (!$this->get_child($patharr[0]) instanceof base_nested_element) {
                    throw new base_element_struct_exception('baseelementincorrectchild', $patharr[0]);
                } else {
                    $newpath = implode('/', array_slice($patharr, 1)); // Take out 1st element
                    return $this->get_child($patharr[0])->find_element_by_path($newpath); // Process as relative in parent
                }
            } else { // Return final element or attribute
                if ($this->get_final_element($patharr[0]) instanceof base_final_element) {
                    return $this->get_final_element($patharr[0]);
                } else if ($this->get_attribute($patharr[0]) instanceof base_attribute) {
                    return $this->get_attribute($patharr[0]);
                } else {
                    throw new base_element_struct_exception('baseelementincorrectfinalorattribute', $patharr[0]);
                }
            }
        }
    }

    protected function find_first_parent_by_name($name) {
        if ($parent = $this->get_parent()) { // If element has parent
            $element   = $parent->get_final_element($name); // Look for name into parent finals
            $attribute = $parent->get_attribute($name);     // Look for name into parent attrs
            if ($element instanceof base_final_element) {
                return $element;

            } else if ($attribute instanceof base_attribute) {
                return $attribute;

            } else { // Not found, go up 1 level and continue searching
                return $parent->find_first_parent_by_name($name);
            }
        } else { // No more parents available, return the original backup::VAR_PARENTID, exception
            throw new base_element_struct_exception('cannotfindparentidforelement', $name);
        }
    }


/// Public API starts here

    public function get_attributes() {
        return $this->attributes;
    }

    public function get_attribute($name) {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        } else {
            return null;
        }
    }

    public function get_parent() {
        return $this->parent;
    }

    public function get_level() {
        return $this->parent == null ? 1 : $this->parent->get_level() + 1;
    }

    public function add_attributes($attributes) {
        if ($attributes instanceof base_attribute || is_string($attributes)) { // Accept 1 attribute, object or string
            $attributes = array($attributes);
        }
        if (is_array($attributes)) {
            foreach ($attributes as $attribute) {
                if (is_string($attribute)) { // Accept string attributes
                    $attribute = $this->get_new_attribute($attribute);
                }
                if (!($attribute instanceof base_attribute)) {
                    throw new base_element_attribute_exception('baseelementnoattribute', get_class($attribute));
                }
                if (array_key_exists($attribute->get_name(), $this->attributes)) {
                    throw new base_element_attribute_exception('baseelementattributeexists', $attribute->get_name());
                }
                $this->attributes[$attribute->get_name()] = $attribute;
            }
        } else {
            throw new base_element_attribute_exception('baseelementattributeincorrect');
        }
    }

    public function clean_values() {
        parent::clean_value();
        if (!empty($this->attributes)) {
            foreach ($this->attributes as $attribute) {
                $attribute->clean_value();
            }
        }
    }

    public function to_string($showvalue = false) {
        // Decide the correct prefix
        $prefix = '#'; // default
        if ($this->parent instanceof base_optigroup) {
            $prefix = '?';
        } else if ($this instanceof base_nested_element) {
            $prefix = '';
        }
        $indent = str_repeat('    ', $this->get_level()); // Indent output based in level (4cc)
        $output = $indent . $prefix . $this->get_name() . ' (level: ' . $this->get_level() . ')';
        if ($showvalue) {
            $value = $this->is_set() ? $this->get_value() : 'not set';
            $output .= ' => ' . $value;
        }
        if (!empty($this->attributes)) {
            foreach ($this->attributes as $attribute) {
                $output .= PHP_EOL . $indent . '    ' . $attribute->to_string($showvalue);
            }
        }
        return $output;
    }

// Implementable API

    /**
     * Returns one instace of the @base_attribute class to work with
     * when attributes are added simply by name
     */
    abstract protected function get_new_attribute($name);
}

/**
 * base_element exception to control all the errors related with parents handling
 */
class base_element_parent_exception extends base_atom_exception {

    /**
     * Constructor - instantiates one base_element_parent_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

/**
 * base_element exception to control all the errors related with attributes handling
 */
class base_element_attribute_exception extends base_atom_exception {

    /**
     * Constructor - instantiates one base_element_attribute_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
