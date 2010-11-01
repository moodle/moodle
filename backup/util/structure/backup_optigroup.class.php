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
class backup_optigroup extends base_optigroup implements processable {

    public function add_child($element) {
        if (!($element instanceof backup_optigroup_element)) { // parameter must be backup_optigroup_element
            if (is_object($element)) {
	            $found = get_class($element);
            } else {
                $found = 'non object';
            }
            throw new base_optigroup_exception('optigroup_element_incorrect', $found);
        }
        parent::add_child($element);
    }

    public function process($processor) {
        if (!$processor instanceof base_processor) { // No correct processor, throw exception
            throw new base_element_struct_exception('incorrect_processor');
        }
        // Iterate over all the children backup_optigroup_elements, delegating the process
        // an knowing it only handles final elements, so we'll delegate process of nested
        // elements below. Tricky but we need to priorize finals BEFORE nested always.
        foreach ($this->get_children() as $child) {
            if ($child->condition_matches()) { // Only if the optigroup_element condition matches
                $child->process($processor);
                if (!$this->is_multiple()) {
                    break; // one match found and this optigroup is not multiple => break loop
                }
            }
        }
        // Now iterate again, but looking for nested elements what will go AFTER all the finals
        // that have been processed above
        foreach ($this->get_children() as $child) {
            if ($child->condition_matches()) { // Only if the optigroup_element condition matches
                foreach ($child->get_children() as $nested_element) {
                     $nested_element->process($processor);
                }
                if (!$this->is_multiple()) {
                    break; // one match found and this optigroup is not multiple => break loop
                }
            }
        }
    }
}
