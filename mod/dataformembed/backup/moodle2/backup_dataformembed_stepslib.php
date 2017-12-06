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
 * @package    mod_dataformembed
 * @copyright  2012 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete dataformembed structure for backup, with file and id annotations.
 */
class backup_dataformembed_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $dataformembed = new backup_nested_element('dataformembed', array('id'), array(
            'name', 'intro', 'introformat', 'timemodified', 'dataform', 'view', 'filter', 'embed', 'style'));

        // Build the tree.
        // (None).

        // Define sources.
        $dataformembed->set_source_table('dataformembed', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations.
        // (None).

        // Define file annotations.
        $dataformembed->annotate_files('mod_dataformembed', 'intro', null); // This file area hasn't itemid.

        // Return the root element (dataformembed), wrapped into standard activity structure.
        return $this->prepare_activity_structure($dataformembed);
    }
}
