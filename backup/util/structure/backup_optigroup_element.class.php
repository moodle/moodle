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
 * Instantiable class representing one optigroup element for conditional branching
 *
 * Objects of this class are internally nested elements, so they support having both
 * final elements and children (more nested elements) and are able to have one source
 * and all the stuff supported by nested elements. Their main differences are:
 *
 * - Support for conditional execution, using simple equality checks with outer values.
 * - Don't have representation in the hierarchy, so:
 *     - Their level is the level of the parent of their enclosing optigroup.
 *     - Act as one "path bridge" when looking for parent path values
 *     - They don't support attributes
 *
 * Their main use is to allow conditional branching, basically for optional submodules
 * like question types, assignment subtypes... where different subtrees of information
 * must be exported. It's correct to assume that each submodule will define its own
 * optigroup_element for backup purposes.
 */
class backup_optigroup_element extends backup_nested_element {

    private $conditionparam;     // Unprocessed param representing on path to look for value
    private $procconditionparam; // Processed base_element param to look for value
    private $conditionvalue;     // Value to compare the the param value with

    /**
     * Constructor - instantiates one backup_optigroup_element
     *
     * @param string $name of the element
     * @param array $final_elements this element will handle (optional, defaults to null)
     * @param string $condition_param param (path) we are using as source for comparing (optional, defaults to null)
     * @param string $condition_value   value we are comparing to (optional, defaults to null)
     */
    public function __construct($name, $final_elements = null, $conditionparam = null, $conditionvalue = null) {
        parent::__construct($name, null, $final_elements);
        $this->set_condition($conditionparam, $conditionvalue);
    }

// Public API starts here

    /**
     * Sets the condition for this optigroup
     */
    public function set_condition($conditionparam, $conditionvalue) {
        // We only resolve the condition if the parent of the element (optigroup) already has parent
        // else, we'll resolve it once the optigroup parent is defined
        if ($this->get_parent() && $this->get_parent()->get_parent() && $conditionparam !== null) {
            $this->procconditionparam = $this->find_element($conditionparam);
        }
        $this->conditionparam = $conditionparam;
        $this->conditionvalue = $conditionvalue;
    }

    public function get_condition_param() {
        return $this->conditionparam;
    }

    public function get_condition_value() {
        return $this->conditionvalue;
    }

    /**
     * Evaluate the condition, returning if matches (true) or no (false)
     */
    public function condition_matches() {
        $match = false; // By default no match
        $param = $this->procconditionparam;
        if ($param instanceof base_atom && $param->is_set()) {
            $match = ($param->get_value() == $this->conditionvalue); // blame $DB for not having === !
        } else {
            $match = ($param == $this->conditionvalue);
        }
        return $match;
    }

    /**
     * Return the level of this element, that will be, the level of the parent (doesn't consume level)
     * (note this os only a "cosmetic" effect (to_string) as fact as the real responsible for this
     * is the corresponding structure_processor for the final output.
     */
    public function get_level() {
        return $this->get_parent() == null ? 1 : $this->get_parent()->get_level();
    }

    /**
     * process one optigroup_element
     *
     * Note that this ONLY processes the final elements in order to get all them
     * before processing any nested element. Pending nested elements are processed
     * by the optigroup caller.
     */
    public function process($processor) {
        if (!$processor instanceof base_processor) { // No correct processor, throw exception
            throw new base_element_struct_exception('incorrect_processor');
        }

        $iterator = $this->get_iterator($processor); // Get the iterator over backup-able data

        $itcounter = 0; // To check that the iterator only has 1 ocurrence
        foreach ($iterator as $key => $values) { // Process each "ocurrrence" of the nested element (recordset or array)

            // Fill the values of the attributes and final elements with the $values from the iterator
            $this->fill_values($values);

            // Delegate the process of each final_element
            foreach ($this->get_final_elements() as $final_element) {
                $final_element->process($processor);
            }

            // Everything processed, clean values before next iteration
            $this->clean_values();

            // Increment counters for this element
            $this->counter++;
            $itcounter++;

            // optigroup_element, check we only have 1 element always
            if ($itcounter > 1) {
                throw new base_element_struct_exception('optigroup_element_only_one_ocurrence', $this->get_name());
            }
        }
        // Close the iterator (DB recordset / array iterator)
        $iterator->close();
    }

// Forbidden API starts here

    /**
     * Adding optigroups is forbidden
     */
    public function add_add_optigroup($optigroup) {
        throw new base_element_struct_exception('optigroup_element_not_optigroup');
    }

    /**
     * Adding attributes is forbidden
     */
    public function add_attributes($attributes) {
        throw new base_element_struct_exception('optigroup_element_not_attributes');
    }

    /**
     * Instantiating attributes is forbidden
     */
    protected function get_new_attribute($name) {
        throw new base_element_struct_exception('optigroup_element_not_attributes');
    }

// Protected API starts here

    /**
     * Returns one instace of the @final_element class to work with
     * when final_elements are added simply by name
     */
    protected function get_new_final_element($name) {
        return new backup_final_element($name);
    }

    /**
     * Set the parent of the optigroup_element and, at the same time,
     * process the condition param
     */
    protected function set_parent($element) {
        parent::set_parent($element);
        // Force condition param calculation
        $this->set_condition($this->conditionparam, $this->conditionvalue);
    }

}
