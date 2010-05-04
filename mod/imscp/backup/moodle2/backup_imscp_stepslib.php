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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_imscp_activity_task
 */

/**
 * Define the complete imscp structure for backup, with file and id annotations
 */
class backup_imscp_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $imscp = new backup_nested_element('imscp', array('id'), array(
            'name', 'intro', 'introformat', 'revision',
            'keepold', 'structure', 'timemodified'));

        // Build the tree
        // (love this)

        // Define sources
        $imscp->set_source_table('imscp', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $imscp->annotate_files(array('imscp_intro'), null); // This file area hasn't itemid
        /**
         * Don't annotate contents for now. It breaks backup as far as it's using itemid for storing
         * revisions. Each element only can have one files anotation and itemid must be null or id for all them
         * To be able to backup this properly we must sort of of these:
         *  * take out those revisions from imscp
         *  * if want to implement them properly, create imscp_revisions and associate contents there
         *  * change backup so each file_area can have its own itemid (no good idea form my structured mind)
         *
         * TODO: To decide MDL-22315 comments about this.
         */
        // $imscp->annotate_files(array('imscp_content'), 'revision'); // This file area uses 'revision' as itemid

        // Return the root element (imscp), wrapped into standard activity structure
        return $this->prepare_activity_structure($imscp);
    }
}
