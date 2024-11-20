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
 * Backup steps for mod_jupyter are defined here.
 *
 * @package     mod_jupyter
 * @category    backup
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_jupyter_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        // Replace with the attributes and final elements that the element will handle.
        $attributes = ['id'];
        $finalelements = [
            'name', 'timecreated', 'timemodified', 'intro', 'introformat', 'autograded', 'notebook_filename'
        ];
        $root = new backup_nested_element('jupyter', $attributes, $finalelements);

        // Define the source tables for the elements.
        $root->set_source_table('jupyter', ['id' => backup::VAR_ACTIVITYID]);

        // Define file annotations.
        $root->annotate_files('mod_jupyter', 'intro', null);    // This file area has no itemid.
        $root->annotate_files('mod_jupyter', 'package', null);  // This file area has no itemid.

        return $this->prepare_activity_structure($root);

    }
}
