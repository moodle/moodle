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
 * Backup steps.
 *
 * @package    mod_choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete url structure for backup, with file and id annotations
 */
class backup_choicegroup_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines structure of activity backup
     *
     * @return backup_nested_element
     * @throws base_element_struct_exception
     */
    protected function define_structure() {

        // Define each element separated.
        $choicegroup = new backup_nested_element('choicegroup', ['id'], [
            'name', 'intro', 'introformat', 'publish',
            'multipleenrollmentspossible',
            'showresults', 'display', 'allowupdate', 'showunanswered',
            'limitanswers', 'timeopen', 'timeclose', 'timemodified',
            'completionsubmit', 'sortgroupsby', 'onlyactive', ]);

        $options = new backup_nested_element('options');

        $option = new backup_nested_element('option', ['id'], [
            'groupid', 'maxanswers', 'timemodified', ]);

        // Build the tree.
        $choicegroup->add_child($options);
        $options->add_child($option);

        // Define sources.
        $choicegroup->set_source_table('choicegroup', ['id' => backup::VAR_ACTIVITYID]);

        $option->set_source_sql('
            SELECT *
              FROM {choicegroup_options}
             WHERE choicegroupid = ?',
            [backup::VAR_PARENTID]);

        // Define file annotations.
        $choicegroup->annotate_files('mod_choicegroup', 'intro', null); // This file area hasn't itemid.

        // Return the root element (choicegroup), wrapped into standard activity structure.
        return $this->prepare_activity_structure($choicegroup);
    }
}
